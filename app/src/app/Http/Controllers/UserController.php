<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\FollowingUser;
use App\Models\Item;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Http\Request;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;

class UserController
{
    // ユーザー一覧表示
    public function index(Request $request)
    {
        // 全ユーザー検索
        if (empty($request->id)) {
            $currentPage = $request->page === null ? 1 : $request->page;        // 現在のページ数
            $recordMax = 10;                                                    // １ページに表示する最大件数
            $min = $currentPage > 1 ? ($currentPage - 1) * $recordMax : 0;      // レコードを取得する開始位置

            // ユーザー一覧を取得する(１ページにつき$recordMax件表示する)
            $users = User::offset($min)->limit($recordMax)->get();
            // 最大件数を取得する
            $usersCnt = User::count();
            // 最終的なデータを格納する
            $responseData = [];

            for ($i = 0; $i < count($users); $i++) {
                // 設定しているアチーブメントの称号を取得する
                $title = '';
                if ($users[$i]->title_id > 0) {
                    $item = $users[$i]->gettitle()->selectRaw('name')->first();
                    if (!empty($item->name)) {
                        $title = $item->name;
                    }
                }

                // 合計スコアを取得する
                $total_score = $users[$i]->totalscore()->first() == null ? 0 : $users[$i]->totalscore()->pluck('total_score')->first();

                // データを格納する
                $array = [
                    'id' => $users[$i]['id'],
                    'name' => $users[$i]['name'],
                    'title' => $title,
                    'total_score' => $total_score,
                    'stage_id' => $users[$i]['stage_id']
                ];
                $responseData[$i] = $array;
            }

            // 自前の配列をページャーする
            $view_name = new LengthAwarePaginator($responseData, $usersCnt, $recordMax, $currentPage,
                array('path' => '/users/index'));

            return view('users/index', ['userData' => $view_name, 'requestID' => $request->id]);
        }

        // ユーザーIDを指定して検索
        $user = User::find($request->id);

        if (!empty($user)) {

            $userData = [
                [
                    'id' => $user->id,
                    'name' => $user->name,
                ]
            ];
        }

        return view('users/index', ['userData' => $userData ?? null, 'requestID' => $request->id]);
    }

    // インベントリのアイテム一覧表示
    public function item(Request $request)
    {
        // モデルを取得する
        $user = User::find($request->id);

        // モデルを取得できた場合
        if (!empty($user)) {
            $items = $user->items()->paginate(10);
            $items->appends(['id' => $request->id]);    // ページネーションで遷移したときにパラメータが消えないようにする

            for ($i = 0; $i < count($items); $i++) {
                $strType = '';
                switch ($items[$i]['type']) {
                    case 1:
                        $strType = 'アイコン';
                        break;
                    case 2:
                        $strType = '称号';
                        break;
                    case 3:
                        $strType = 'お助けアイテム';
                        break;
                    case 4:
                        $strType = '救難信号解放';
                        break;
                    case 5:
                        $strType = '救難信号の上限値UP';
                        break;
                    case 6:
                        $strType = 'ポイント';
                        break;
                }
                $items[$i]['type'] = $strType;
            }
        }

        return view('users/item', ['user' => $user, 'items' => $items ?? null]);
    }

    // 受信メール一覧表示
    public function mail(Request $request)
    {
        // モデルを取得する
        $user = User::find($request->id);

        // リレーション
        if (!empty($user)) {
            $mails = $user->mails()->paginate(10);
            $mails->appends(['id' => $request->id]);
        }

        return view('users/mail', ['user' => $user, 'mails' => $mails ?? null]);
    }
}