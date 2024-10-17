<?php

namespace Database\Seeders;

use App\Models\SupplyLog;
use Illuminate\Database\Seeder;

class SupplyLogTableSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            SupplyLog::create([
                'user_id' => $i,
                'get_vol' => rand(100, 500),
            ]);
        }
    }
}
