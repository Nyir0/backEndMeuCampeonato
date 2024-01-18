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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->integer('championship');
            $table->string('team_A');
            $table->string('team_B');
            $table->integer('gol_team_A')->nullable();
            $table->integer('gol_team_B')->nullable();
            $table->enum('winner', ['team_A', 'team_B', 'draw'])->default('draw');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
