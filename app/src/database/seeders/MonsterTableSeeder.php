<?php

namespace Database\Seeders;

use App\Models\Monster;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class MonsterTableSeeder extends Seeder
{
    public function run(): void
    {
        // テキストファイルのパス
        $filePath = 'txt/Monster.txt';

        // Storageファザードを使って読み込む
        $content = Storage::get($filePath);

        // 区切り文字で分割する(改行指定)
        $lines = explode(PHP_EOL, $content);
        array_splice($lines, 0, 1); // 1行目は削除

        foreach ($lines as $line) {
            // 改行と空白を削除
            str_replace([PHP_EOL, " "], "", $line);
            $values = explode(",", $line);

            Monster::create([
                'name' => $values[0],
                'text' => $values[1],
                'evo_lv' => (int)$values[2],
                'rarity' => $values[3],
            ]);
        }
    }
}
