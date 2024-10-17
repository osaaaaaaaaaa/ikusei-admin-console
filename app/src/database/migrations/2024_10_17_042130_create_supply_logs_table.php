<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('supply_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->integer('get_vol');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_logs');
    }
};
