<?php

namespace Database\Seeders;

use App\Models\ExerciseLog;
use Illuminate\Database\Seeder;

class ExerciseLogTableSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            ExerciseLog::create([
                'user_id' => $i,
                'nurture_id' => $i,
                'use_vol' => rand(10, 50),
                'get_exp' => rand(20, 1000),
            ]);
        }
    }
}
