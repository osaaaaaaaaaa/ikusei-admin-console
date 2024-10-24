<?php

namespace Database\Seeders;

use App\Models\ItemLog;
use Illuminate\Database\Seeder;

class ItemLogsTableSeeder extends Seeder
{
    public function run(): void
    {
        ItemLog::create([
            'user_id' => 1,
            'item_id' => 1,
            'ope_num' => 1,
            'result' => 1
        ]);
        ItemLog::create([
            'user_id' => 2,
            'item_id' => 2,
            'ope_num' => 2,
            'result' => 2
        ]);
        ItemLog::create([
            'user_id' => 3,
            'item_id' => 3,
            'ope_num' => 3,
            'result' => 3
        ]);
        ItemLog::create([
            'user_id' => 4,
            'item_id' => 4,
            'ope_num' => 4,
            'result' => 4
        ]);
    }
}
