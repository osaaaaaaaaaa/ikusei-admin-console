<?php

namespace Database\Seeders;

use App\Models\UserItem;
use Illuminate\Database\Seeder;

class UserItemTableSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 0; $i < 5; $i++) {
            for ($j = 0; $j < 3; $j++) {
                UserItem::create([
                    'user_id' => $i+1,
                    'item_id' => $j+1,
                    'quantity' => rand(1, 10),
                ]);
            }
        }
    }
}
