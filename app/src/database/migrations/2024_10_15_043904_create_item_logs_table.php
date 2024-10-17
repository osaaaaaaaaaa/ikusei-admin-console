<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('item_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('item_id');
            $table->integer('quantity');        // 数量
            $table->boolean('use_flag');        // 操作フラグ [true:消費 false:取得]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_logs');
    }
};
