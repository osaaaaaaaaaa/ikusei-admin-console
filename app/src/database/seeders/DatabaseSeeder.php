<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // マスターデータ(初期データ)挿入
        $this->call(AccountsTableSeeder::class);
        $this->call(ExerciseLogTableSeeder::class);
        $this->call(ItemLogsTableSeeder::class);
        $this->call(ItemTableSeeder::class);
        $this->call(MealLogTableSeeder::class);
        $this->call(MonsterTableSeeder::class);
        $this->call(NGWordTableSeeder::class);
        $this->call(NurtureMonsterTableSeeder::class);
        $this->call(SupplyLogTableSeeder::class);
        $this->call(UserInfoTableSeeder::class);
        $this->call(UserItemTableSeeder::class);
        $this->call(UserTableSeeder::class);
    }
}
