<?php

namespace Database\Seeders;

use App\Models\UserInfo;
use Illuminate\Database\Seeder;

class UserInfoTableSeeder extends Seeder
{
    public function run(): void
    {
        UserInfo::create([
            'user_id' => 1,
            'food_vol' => 150,
            'facility_lv' => 2,
            'reroll_num' => 0
        ]);
        UserInfo::create([
            'user_id' => 2,
            'food_vol' => 300,
            'facility_lv' => 1,
            'reroll_num' => 2
        ]);
        UserInfo::create([
            'user_id' => 3,
            'food_vol' => 450,
            'facility_lv' => 5,
            'reroll_num' => 15
        ]);
        UserInfo::create([
            'user_id' => 4,
            'food_vol' => 600,
            'facility_lv' => 3,
            'reroll_num' => 2
        ]);
    }
}
