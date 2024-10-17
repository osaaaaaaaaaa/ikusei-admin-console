<?php

namespace Database\Seeders;

use App\Models\MealLog;
use Illuminate\Database\Seeder;

class MealLogTableSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            MealLog::create([
               'user_id' => $i,
               'nurture_id' => $i,
                'use_vol' => rand(1, 50),
                'get_exp' => rand(10, 200),
            ]);
        }
    }
}
