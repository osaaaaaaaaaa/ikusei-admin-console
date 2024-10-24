<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NurtureMonster extends Model
{
    // $guardedには更新しないカラムを指定する
    protected $guarded = [
        'id',
    ];

    // 所持アイテム
    public function userInfo()
    {
        return $this->belongsTo(User::class);
    }
}
