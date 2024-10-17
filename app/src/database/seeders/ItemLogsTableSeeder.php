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
            'quantity' => 1,
            'use_flag' => true
        ]);
        ItemLog::create([
            'user_id' => 2,
            'item_id' => 2,
            'quantity' => 2,
            'use_flag' => false
        ]);
        ItemLog::create([
            'user_id' => 3,
            'item_id' => 3,
            'quantity' => 3,
            'use_flag' => true
        ]);
        ItemLog::create([
            'user_id' => 4,
            'item_id' => 4,
            'quantity' => 4,
            'use_flag' => false
        ]);
    }
}
