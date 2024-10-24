<?php

namespace App\Http\Controllers;

use App\Http\Resources\ItemListResource;
use App\Http\Resources\UserItemResource;
use App\Models\Item;
use App\Models\ItemLog;
use App\Models\User;
use App\Models\UserItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    // 全アイテム取得処理
    public function index()
    {
        // 所持品の取得
        $itemList = Item::all();

        return response()->json(ItemListResource::collection($itemList));
    }

    // プレイユーザーの所持品取得
    public function show(Request $request)
    {
        // ユーザーの存在確認
        User::findOrFail($request->user()->id);

        $userItem = UserItem::where('user_id', $request->user()->id)->get();

        return response()->json(UserItemResource::collection($userItem));
    }

    // アイテム入手・更新処理
    public function update(Request $request)
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'item_id' => ['required', 'int'],
            'quantity' => ['required', 'int'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // ユーザーの存在確認
        User::findOrFail($request->user()->id);

        // 所持情報を取得
        $userItem = UserItem::where('user_id', $request->user()->id)->
        where('item_id', $request->item_id)->first();

        if ($request->quantity < 0) {
            // 所持数が0以下になる時
            return response()->json([], 400);
        }

        try {
            DB::transaction(function () use ($request, $userItem) {
                // 所持情報の更新・ログ書き出し

                if ($userItem == null) {
                    // 入手処理
                    UserItem::create([
                        'user_id' => $request->user()->id,
                        'item_id' => $request->item_id,
                        'quantity' => $request->quantity,
                    ]);
                    ItemLog::create([
                        'user_id' => $request->user()->id,
                        'item_id' => $request->item_id,
                        'ope_num' => $request->quantity,
                        'result' => $request->quantity,
                    ]);
                } else {
                    // 更新処理
                    ItemLog::create([
                        'user_id' => $request->user()->id,
                        'item_id' => $request->item_id,
                        'ope_num' => $request->quantity - $userItem->quantity,
                        'result' => $request->quantity,
                    ]);

                    $userItem->quantity = $request->quantity;
                    $userItem->save();
                }
            });
            return response()->json();
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
