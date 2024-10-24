<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ItemTableSeeder extends Seeder
{
    public function run(): void
    {
        // テキストファイルのパス
        $filePath = 'txt/Item.txt';

        // Storageファザードを使って読み込む
        $content = Storage::get($filePath);

        // 区切り文字で分割する(改行指定)
        $lines = explode(PHP_EOL, $content);
        array_splice($lines, 0, 1); // 1行目は削除

        foreach ($lines as $line) {
            // 改行と空白を削除
            str_replace([PHP_EOL, " "], "", $line);
            $values = explode(",", $line);

            Item::create([
                'name' => $values[0],
                'text' => $values[1],
                'value' => (int)$values[2],
            ]);
        }
    }
}
