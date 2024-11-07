<?php

namespace App\Http\Controllers;

use App\Http\Resources\MonsterResource;
use App\Http\Resources\NutureMonsterResource;
use App\Models\ExerciseLog;
use App\Models\MealLog;
use App\Models\NGWord;
use App\Models\NurtureMonster;
use App\Models\User;
use App\Models\UserInfo;
use Exception;
use App\Models\Monster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MonsterController extends Controller
{
    // 全モンスター情報の取得
    public function index()
    {
        $monsterList = Monster::all();

        return response()->json(MonsterResource::collection($monsterList));
    }

    // 育成中・完了したモンスターIDの取得
    public function nurtured(Request $request)
    {
        $monsterList = NurtureMonster::select('monster_id')
            ->where('user_id', $request->user()->id)->whereIn('state', [2, 3])->get()->toArray();

        $idList = array_column($monsterList, 'monster_id');
        $idList = array_unique($idList);
        $idList = array_values($idList);

        return response()->json($idList);
    }

    // 育成中のモンスター情報を取得
    public function nurturing(Request $request)
    {
        $monsterList = NurtureMonster::where('user_id', $request->user()->id)
            ->whereIn('state', [1, 2])->get();

        return response()->json(NutureMonsterResource::collection($monsterList));
    }

    // 自分の育成済モンスター&育成モンスターID以外の新着30件取得
    public function new30(Request $request)
    {
        // ユーザの育成中・済のモンスターIDを取得
        $monsterList = NurtureMonster::where('user_id', $request->user()->id)
            ->where('state', [2, 3])->get()->toArray();
        $idList = array_column($monsterList, 'monster_id');

        // ユーザーの育成済モンスターを弾き、新着順に取得
        $new30List = NurtureMonster::
        select('nurture_monsters.monster_id as monster_id', 'nurture_monsters.name as monster_name',
            'users.name as user_name', 'nurture_monsters.level as level')
            ->whereNotIn('monster_id', $idList)
            ->whereIn('nurture_monsters.state', [2, 3])
            ->orderBy('nurture_monsters.updated_at', 'desc')
            ->join('users', 'users.id', '=', 'nurture_monsters.user_id')
            ->get();

        return response()->json($new30List);
    }

    // 初回育成モンスター登録
    public function initStore(Request $request)
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'name' => ['string', 'max:128']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // 名前変更の場合、NGワードチェック
        if (isset($request->name)) {    // 名前
            $replaceName = str_replace(["　", " "], "", $request->name);  // 全角・半角スペース削除
            $ngWords = NGWord::pluck('word')->toArray();
            foreach ($ngWords as $ngWord) {
                if (stripos($replaceName, $ngWord) !== false) {
                    // NGワードが含まれている場合の処理
                    return response()->json(['error' => "使用できないワードが含まれています：" . $ngWord], 400);
                }
            }
        }

        $monsterInfo = Monster::find(1);

        $nurtureInfo = NurtureMonster::create([
            'user_id' => $request->user()->id,
            'monster_id' => 1,
            'name' => $monsterInfo->name,
            'state' => 1,
        ]);

        $nurtureInfo = NurtureMonster::find($nurtureInfo->id);

        return response()->json(NutureMonsterResource::make($nurtureInfo));
    }

    // 育成モンスター情報更新
    public function update(Request $request)
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer'],
            'monster_id' => ['integer'],
            'name' => ['string', 'max:128'],
            'level' => ['integer'],
            'exp' => ['integer'],
            'stomach_vol' => ['integer'],
            'state' => ['integer']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // 存在するかチェック
        User::findOrFail($request->user()->id);
        $monsterInfo = NurtureMonster::findOrFail($request->id);

        try {
            // トランザクション処理
            DB::transaction(function () use ($request, $monsterInfo) {
                // 渡ってきたデータごとに上書き処理
                if (isset($request->monster_id)) {  // モンスターID
                    $monsterInfo->monster_id = $request->monster_id;
                }
                if (isset($request->name)) {        // 名前
                    $monsterInfo->name = $request->name;
                }
                if (isset($request->level)) {       // レベル
                    $monsterInfo->level = $request->level;
                }
                if (isset($request->exp)) {         // 経験値
                    $monsterInfo->exp = $request->exp;
                }
                if (isset($request->stomach_vol)) { // 満腹度
                    $monsterInfo->stomach_vol = $request->stomach_vol;
                }
                if (isset($request->state)) {       // 育成状態
                    $monsterInfo->state = $request->state;
                }

                // 更新処理
                $monsterInfo->save();
            });
            return response()->json();
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 運動処理
    public function exercise(Request $request)
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'nurture_id' => ['required', 'integer'],
            'used_vol' => ['required', 'integer'],
            'exp' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // 存在するかチェック
        $monsterInfo = NurtureMonster::findOrFail($request->nurture_id);

        try {
            // トランザクション処理
            $response = DB::transaction(function () use ($request, $monsterInfo) {
                // ログ書き出し
                ExerciseLog::create([
                    'user_id' => $request->user()->id,
                    'nurture_id' => $request->nurture_id,
                    'use_vol' => $monsterInfo->stomach_vol - $request->used_vol,
                    'get_exp' => $request->exp - $monsterInfo->exp
                ]);

                // 食料残量更新
                $monsterInfo->stomach_vol = $request->used_vol;

                // レベル・経験値の更新
                $result = $this->CalcExercise($request->nurture_id, $monsterInfo->level, $request->exp);
                $monsterInfo->level = $result[0];
                $monsterInfo->exp = $result[1];

                // 保存
                $monsterInfo->save();
                $monsterInfo->save();

                return $result;
            });
            return response()->json(['level' => $response[0], 'exp' => $response[1]]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 食事処理
    public function meal(Request $request)
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'nurture_id' => ['required', 'integer'],
            'stomach_vol' => ['required', 'integer'],
            'used_vol' => ['required', 'integer'],
            'exp' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // 存在するかチェック
        $userInfo = UserInfo::where('user_id', $request->user()->id)->first();
        $monsterInfo = NurtureMonster::findOrFail($request->nurture_id);

        try {
            // トランザクション処理
            $response = DB::transaction(function () use ($request, $monsterInfo, $userInfo) {
                // ログ書き出し
                MealLog::create([
                    'user_id' => $request->user()->id,
                    'nurture_id' => $request->nurture_id,
                    'use_vol' => $userInfo->food_vol - $request->used_vol,
                    'get_exp' => $request->exp - $monsterInfo->exp,
                ]);

                // 食料残量更新
                $userInfo->food_vol = $request->used_vol;

                // レベル・経験値の更新
                $result = $this->CalcExercise($request->nurture_id, $monsterInfo->level, $request->exp);
                $monsterInfo->stomach_vol = $request->stomach_vol;
                $monsterInfo->level = $result[0];
                $monsterInfo->exp = $result[1];

                // 保存
                $userInfo->save();
                $monsterInfo->save();

                return $result;
            });
            return response()->json(['level' => $response[0], 'exp' => $response[1]]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 経験値計算処理 戻り値(レベル、余り経験値)
    private function CalcExercise(int $id, int $level, int $exp)
    {
        // 現在のレベル・経験値を取得
        $nowLv = $level;
        $nowExp = $exp;

        while (1) {
            // 次のレベルまでに必要な経験値を取得
            $minExp = pow($nowLv, 3);
            $nextBorder = pow(($nowLv + 1), 3);
            $needExp = $nextBorder - $minExp;

            if ($nowExp < $needExp) {
                // これ以上レベルが上がらない時は情報更新後にリターン
                $nurtureInfo = NurtureMonster::find($id)->first();
                $nurtureInfo->level = $level;
                $nurtureInfo->exp = $nowExp;
                $nurtureInfo->save();
                return [$nowLv, $nowExp];
            } else {
                $nowExp = $nowExp - $needExp;
                $nowLv++;
            }
        }
    }

    // ミラクル配合
    public function miracle(Request $request)
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'nurture_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // 現育成モンスターの情報を取得
        $nowMonster = NurtureMonster::findOrFail($request->nurture_id);

        try {
            // トランザクション処理
            $response = DB::transaction(function () use ($request, $nowMonster) {

                // ユーザの育成中・済のモンスターIDを取得
                $userMonsters = NurtureMonster::where('user_id', $request->user()->id)
                    ->where('state', [2, 3])->get()->toArray();
                $idList = array_column($userMonsters, 'monster_id');

                // 未所持モンスターIDの抽選
                $monsterList = Monster::select('id')->whereNotIn('id', $idList)->get()->toArray();
                $lotteryID = array_rand(array_column($monsterList, 'id'));

                // 抽選されたモンスターの情報を取得
                $randMonster = Monster::find($lotteryID);

                // 親2をランダムに選出
                $idList = Monster::select('id')->get()->toArray();
                $randomID = array_rand(array_column($idList, 'id'));

                // 新モンスターを登録
                $nurtureMonster = NurtureMonster::create([
                    'user_id' => $request->user()->id,
                    'monster_id' => $lotteryID,
                    'parent1_id' => $nowMonster->monster_id,
                    'parent2_id' => $randomID,
                    'name' => $randMonster->name,
                    'state' => 1
                ]);

                $nurtureMonster = NurtureMonster::find($nurtureMonster->id);

                // 現育成モンスターを育成完了済みに変更
                $nowMonster->state = 3;
                $nowMonster->save();

                return $nurtureMonster;
            });

            // モンスター情報を返却
            return response()->json(NutureMonsterResource::make($response));
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
