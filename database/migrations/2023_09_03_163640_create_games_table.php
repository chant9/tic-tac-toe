<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('player1_symbol', 1);
            $table->integer('player_move');
            $table->string('player1_name', 500);
            $table->string('player2_name', 500)->nullable();
            $table->json('moves')->nullable();
            $table->integer('winner')->nullable();
            $table->json('winning_squares')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
