<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('monster_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('monster_id');  // モンスターID
            $table->unsignedInteger('parent1_id');  // 親1のモンスターID
            $table->unsignedInteger('parent2_id');  // 親2のモンスターID
            $table->string('name');                 // 名前
            $table->integer('exp');                 // 経験値
            $table->integer('stomach_vol');         // 満腹度
            $table->tinyInteger('state');           // 育成状況 [1:卵 2:育成中 3:育成完了 4:死亡]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monster_infos');
    }
};
