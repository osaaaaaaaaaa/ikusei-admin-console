<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;

    // $guardedには更新しないカラムを指定する
    protected $guarded = [
        'id',
    ];

    // 所持アイテムのリレーション
    public function items()
    {
        // 中間テーブルに関する複数行を取得
        return $this->belongsToMany(
        // 第二モデル , 第三テーブル(中間テーブル) , 第一モデルと関係のある中間テーブルカラム , 第二モデルと関係のある中間テーブルカラム
            Item::class, 'user_items', 'user_id', 'item_id')
            ->withPivot('quantity');  // 中間テーブルのカラムを取得
    }

    // アイテムログのリレーション
    public function item_logs()
    {
        return $this->belongsToMany(
            Item::class, 'item_logs', 'user_id', 'item_id')
            ->withPivot('quantity', 'use_flag');
    }
}
