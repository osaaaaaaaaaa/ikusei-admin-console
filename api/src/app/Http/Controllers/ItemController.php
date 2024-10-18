<?php

namespace App\Http\Controllers;

use App\Http\Resources\ItemListResource;
use App\Http\Resources\UserItemResource;
use App\Models\Item;
use App\Models\UserItem;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    // 全アイテム取得処理
    public function all()
    {
        // 所持品の取得
        $itemList = Item::all();

        return response()->json(ItemListResource::collection($itemList));
    }

    // プレイユーザーの所持品取得
    public function show(Request $request)
    {
        $userItem = UserItem::where('user_id', $request->user()->id)->get();

        return response()->json(UserItemResource::collection($userItem));
    }

    // アイテム入手・更新処理
    public function update(Request $request)
    {
        return response()->json();
    }
}
