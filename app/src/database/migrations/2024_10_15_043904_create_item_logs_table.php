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
            $table->integer('ope_num');         // 操作数量
            $table->integer('result');          // 操作結果
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_logs');
    }
};
