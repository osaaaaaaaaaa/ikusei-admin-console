<?php

namespace Database\Seeders;

use App\Models\NurtureMonster;
use Illuminate\Database\Seeder;

class NurtureMonsterTableSeeder extends Seeder
{
    public function run(): void
    {
        NurtureMonster::create([
            'user_id' => 1,
            'monster_id' => 1,
            'parent1_id' => 2,
            'parent2_id' => 3,
            'name' => 'a',
            'exp' => 1,
            'level' => 1,
            'stomach_vol' => 100,
            'state' => 2
        ]);
        NurtureMonster::create([
            'user_id' => 2,
            'monster_id' => 2,
            'parent1_id' => 3,
            'parent2_id' => 4,
            'name' => 'b',
            'exp' => 2,
            'level' => 2,
            'stomach_vol' => 100,
            'state' => 2
        ]);
        NurtureMonster::create([
            'user_id' => 3,
            'monster_id' => 3,
            'parent1_id' => 4,
            'parent2_id' => 5,
            'name' => 'c',
            'exp' => 3,
            'level' => 3,
            'stomach_vol' => 100,
            'state' => 2
        ]);
        NurtureMonster::create([
            'user_id' => 4,
            'monster_id' => 4,
            'parent1_id' => 5,
            'parent2_id' => 6,
            'name' => 'd',
            'exp' => 4,
            'level' => 4,
            'stomach_vol' => 100,
            'state' => 2
        ]);
    }
}
