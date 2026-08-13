<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('match_lineups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('match_id')->constrained('matches')->onDelete('cascade');
            $table->foreignUuid('team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignUuid('player_id')->constrained('players')->onDelete('cascade');
            $table->integer('jersey_number');
            $table->enum('position', ['GK', 'DF', 'MF', 'FW']);
            $table->boolean('is_starter')->default(true);
            $table->boolean('is_captain')->default(false);
            $table->timestamps();

            $table->unique(['match_id', 'player_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('match_lineups');
    }
};