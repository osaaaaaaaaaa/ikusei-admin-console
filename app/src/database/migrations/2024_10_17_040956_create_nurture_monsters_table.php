<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nurture_monsters', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();    // ユーザーID
            $table->unsignedInteger('monster_id');          // モンスターID
            $table->unsignedInteger('parent1_id');          // 親1モンスターID
            $table->unsignedInteger('parent2_id');          // 親2モンスターID
            $table->string('name');                         // 名前
            $table->integer('exp');                         // 所持経験値
            $table->integer('level');                       // レベル
            $table->integer('stomach_vol');                 // 満腹度
            $table->tinyInteger('state')->index();          // 育成状態 [1:卵 2:育成中 3:育成完了 4:死亡]
            $table->timestamps();

            $table->index('user_id','state');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nurture_monsters');
    }
};
