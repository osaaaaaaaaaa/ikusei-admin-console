<?php

namespace App\Http\Controllers;

use App\Models\NGWord;
use App\Models\SupplyLog;
use App\Models\User;
use App\Models\UserInfo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // ユーザー情報登録
    public function store(Request $request)
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            // トランザクション処理
            $userInfo = DB::transaction(function () use ($request) {
                // 登録処理
                $user = User::create([
                    'name' => $request->name,
                ]);
                UserInfo::create([
                    'user_id' => $user->id,
                ]);

                // APIトークンを発行する
                $token = $user->createToken($request->name)->plainTextToken;

                return [$user, $token];
            });

            return response()->json(['user_info' => $userInfo]);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ユーザー情報更新
    public function update(Request $request)
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'name' => ['string', 'max:128'],
            'food_vol' => ['integer'],
            'facility_lv' => ['integer'],
            'reroll_num' => ['integer'],
            'money' => ['integer']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // 存在チェック
        $user = User::findOrFail($request->user()->id);
        $userInfo = UserInfo::where('user_id', $request->user()->id)->first();

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

        try {
            // トランザクション処理
            DB::transaction(function () use ($request, $user, $userInfo) {
                // 渡ってきたデータごとに上書き処理
                if (isset($request->name)) {        // 名前
                    $user->name = $request->name;
                }
                if (isset($request->food_vol)) {    // 食料残量
                    $userInfo->food_vol = $request->food_vol;

                    SupplyLog::create([
                        'user_id' => $request->user()->id,
                        'get_vol' => $request->food_vol,
                    ]);
                }
                if (isset($request->facility_lv)) { // 施設レベル
                    $userInfo->facility_lv = $request->facility_lv;
                }
                if (isset($request->reroll_num)) {  // リロール回数
                    $userInfo->reroll_num = $request->reroll_num;
                }
                if (isset($request->money)) {       // 所持金
                    $userInfo->money = $request->money;
                }

                // 更新処理
                $user->save();
                $userInfo->save();
            });
            return response()->json();
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ユーザー情報取得
    public function show(Request $request)
    {
        // 存在チェック
        $user = User::findOrFail($request->user()->id);
        $userInfo = UserInfo::where('user_id', $request->user()->id)->first();

        return response()->json([
            'name' => $user->name,
            'food_vol' => $userInfo->food_vol,
            'facility_lv' => $userInfo->facility_lv,
            'reroll_num' => $userInfo->reroll_num,
            'money' => $userInfo->money
        ]);
    }

}
