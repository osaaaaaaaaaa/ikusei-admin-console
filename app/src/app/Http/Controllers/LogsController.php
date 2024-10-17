<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\Debugbar\Facades\Debugbar;

class LogsController extends Controller
{
    // アイテムログ一覧表示
    public function item(Request $request)
    {
        // モデルを取得する
        $user = User::find($request->id);

        // ユーザーが存在するかどうか
        if (!empty($user)) {
            // リレーション
            $logs = $user->item_logs()->paginate(10);
            $logs->appends(['id' => $request->id]);
        }

        return view('logs.item', ['user' => $user, 'logs' => $logs ?? null]);
    }
}
