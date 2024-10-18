<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserInfoResource;
use App\Http\Resources\UserResource;
use App\Models\Item;
use App\Models\NGWord;
use App\Models\User;
use App\Models\UserInfo;
use App\Models\UserItem;
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
            'reroll_num' => $userInfo->reroll_num
        ]);
    }

    // ユーザー情報更新
    public function update(Request $request)
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'user_id' => ['int', 'min:1'],
            'name' => ['required', 'string'],
            'title_id' => ['required', 'integer'],
            'stage_id' => ['required', 'integer'],
            'icon_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // 存在チェック
        $user = User::findOrFail($request->user_id);

        // NGワードチェック
        $replaceName = str_replace(["　", " "], "", $request->name);  // 全角・半角スペース削除
        $ngWords = NGWord::pluck('word')->toArray();
        foreach ($ngWords as $ngWord) {
            if (stripos($replaceName, $ngWord) !== false) {
                // NGワードが含まれている場合の処理
                return response()->json(['error' => "使用できないワードが含まれています：" . $ngWord], 400);
            }
        }

        try {
            // トランザクション処理
            DB::transaction(function () use ($request, $user) {
                $user->name = $request->name;
                $user->save();
            });
            return response()->json();
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 所持アイテムリスト取得
    public function showItem(Request $request)
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'user_id' => ['int', 'min:1'],
            'type' => ['int'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // 存在チェック
        $user = User::findOrFail($request->user_id);

        // タイプ指定がない場合は全て取得する
        if (empty($request->type)) {
            $items = $user->items;
        } else {
            $items = $user->items->where('type', '=', $request->type);
        }

        return response()->json(UserItemResource::collection($items));
    }

    // 所持アイテム更新
    public function updateItem(Request $request)
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'int', 'min:1'],
            'item_id' => ['required', 'int', 'min:1'],
            'option_id' => ['required', 'int', 'min:1'],
            'allie_amount' => ['required', 'int'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // 指定したユーザーが存在するかどうか
        User::findOrFail($request->user_id);

        try {
            // トランザクション処理
            DB::transaction(function () use ($request) {

                // 条件値に一致するレコードを検索して返す、存在しなければ新しく生成して返す
                $user_item = UserItem::firstOrCreate(
                    ['user_id' => $request->user_id, 'item_id' => $request->item_id],
                    // 検索する条件値
                    ['amount' => 0]   // 生成するときに代入するカラム
                );

                // 加減算
                $user_item->amount += $request->allie_amount;
                // 個数が0未満になる場合
                if ($user_item->amount < 0) {
                    return response()->json(['error' => '所持数が0以下です'], 400);
                }
                $user_item->save();

                // ログテーブル登録処理
                ItemLogs::create([
                    'user_id' => $request->user_id,
                    'item_id' => $request->item_id,
                    'option_id' => $request->option_id,
                    'allie_count' => $request->allie_amount
                ]);
            });
            return response()->json();
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 受信メールリスト取得
    public function showMail(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $sub_query = DB::raw('(SELECT * FROM users where id = ' . $request->user_id . ' ) AS users');
        $user_mails = Mail::selectRaw('user_mails.id AS id,user_mails.mail_id AS mail_id,title,text,user_mails.is_received AS is_received,user_mails.created_at AS created_at,(30 - DATEDIFF(now(),user_mails.created_at)) AS elapsed_days')
            ->join('user_mails', 'mails.id', '=', 'user_mails.mail_id')
            ->join($sub_query, 'user_mails.user_id', '=', 'users.id')
            ->where('user_mails.user_id', '=', $user->id)
            ->get();
        $response = [];
        foreach ($user_mails as $mail) {
            $frag = MailLogs::where('user_id', '=', $request->user_id)
                ->where("mail_id", "=", $mail->mail_id)->exists();
            // ログに未登録の受信メールの場合
            if (!$frag) {
                // ログテーブル登録処理
                MailLogs::create([
                    'user_id' => $request->user_id,
                    'mail_id' => $mail->mail_id,
                    'action' => 0
                ]);
            }

            // 生成してから30日が経過している場合は削除する
            if ($mail->elapsed_days <= 0) {
                DB::transaction(function () use ($request, $mail) {
                    // ログテーブル登録処理
                    MailLogs::create([
                        'user_id' => $request->user_id,
                        'mail_id' => $mail->mail_id,
                        'action' => 0
                    ]);

                    // 削除処理
                    UserMail::where('id', '=', $mail->id)->delete();
                });
            } else {
                $response[] = $mail;
            }
        }

        return response()->json(UserMailResource::collection($response));
    }

    // 受信メール開封
    public function updateMail(Request $request)
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'int'],
            'user_mail_id' => ['required', 'int']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // 指定したユーザーが存在するかどうか
        User::findOrFail($request->user_id);

        // レコード存在チェック・受け取り済みかどうかチェック
        $userMail = UserMail::findOrFail($request->user_mail_id);
        if (empty($userMail)) {
            abort(404);
        } elseif ($userMail->is_received === 1) {
            abort(404);
        }

        //------------------------
        // 添付アイテムの受け取り処理
        //------------------------
        try {
            // トランザクション処理
            $item_id = DB::transaction(function () use ($request, $userMail) {

                // メールの添付アイテムを取得
                $attachedItems = Attached_Item::where('mail_id', '=', $userMail->mail_id)->get();
                foreach ($attachedItems as $item) {

                    // 条件値に一致するレコードを検索して返す、存在しなければ新しく生成して返す
                    $userItem = UserItem::firstOrCreate(
                        ['user_id' => $request->user_id, 'item_id' => $item->item_id],
                        // 検索する条件値
                        ['amount' => 0]   // 生成するときに代入するカラム
                    );

                    $userItem->amount = ($userItem->amount + $item->amount) >= 0 ? ($userItem->amount + $item->amount) : 0;
                    $userItem->save();
                }

                // 受信メールを開封済みにする
                $userMail->is_received = 1;
                $userMail->save();

                // ログテーブル登録処理
                MailLogs::create([
                    'user_id' => $request->user_id,
                    'mail_id' => $request->user_mail_id,
                    'action' => 1
                ]);

                return $attachedItems;
            });

            if (empty($item_id)) {
                return response()->json();
            } else {
                $items = Item::whereIn('id', $item_id->pluck('item_id'))->get()->toArray();
                for ($i = 0; $i < count($items); $i++) {
                    $items[$i] += ['amount' => $item_id[$i]->amount];
                }
                return response()->json(UserRewardItemResource::collection($items));
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 受信メール削除
    public function destroyMail(Request $request)
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'int'],
            'user_mail_id' => ['required', 'int'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // 対象のユーザーが存在するかどうか
        User::findOrFail($request->user_id);

        try {
            // トランザクション処理
            DB::transaction(function () use ($request) {
                // 削除処理
                UserMail::where('user_id', '=', $request->user_id)->where('id', '=', $request->user_mail_id)->delete();

                // ログテーブル登録処理
                MailLogs::create([
                    'user_id' => $request->user_id,
                    'mail_id' => $request->user_mail_id,
                    'action' => 0
                ]);
            });
            return response()->json();
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
