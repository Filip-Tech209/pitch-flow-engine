<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('match_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('match_id')->constrained('matches')->onDelete('cascade');
            $table->foreignUuid('team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignUuid('player_id')->constrained('players')->onDelete('cascade');
            $table->foreignUuid('related_player_id')->nullable()->constrained('players')->onDelete('set null'); // e.g. Assister or Subbed player
            $table->integer('minute');
            $table->integer('extra_minute')->nullable();
            $table->enum('event_type', ['GOAL', 'PENALTY_GOAL', 'OWN_GOAL', 'PENALTY_MISS', 'YELLOW_CARD', 'RED_CARD', 'SUBSTITUTION', 'VAR_DECISION']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('match_events');
    }
};