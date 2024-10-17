<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('item_id');
            $table->integer('quantity');        // 所持数
            $table->timestamps();

            // ユニーク制約設定
            $table->unique('user_id','item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_items');
    }
};
