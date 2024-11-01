<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nurture_monsters', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index();             // ユーザーID
            $table->unsignedInteger('monster_id');                   // モンスターID
            $table->unsignedInteger('parent1_id')->default(0); // 親1モンスターID
            $table->unsignedInteger('parent2_id')->default(0); // 親2モンスターID
            $table->string('name', 128)->default("");    // 名前
            $table->integer('exp')->default(0);                // 所持経験値
            $table->integer('level')->default(1);              // レベル
            $table->integer('stomach_vol')->default(20);       // 満腹度
            $table->tinyInteger('state')->index();                  // 育成状態 [1:卵 2:育成中 3:育成完了 4:死亡]
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->index(['user_id', 'state']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nurture_monsters');
    }
};
