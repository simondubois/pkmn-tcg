<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('set_id')->index();
            $table->string('tcgdex_id')->unique();
            $table->string('name');
            $table->string('category')->index();
            $table->string('rarity')->index();
            $table->string('set_position');
            $table->string('scan_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
