<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('matches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('season_id')->constrained('seasons')->onDelete('cascade');
            $table->foreignUuid('home_team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignUuid('away_team_id')->constrained('teams')->onDelete('cascade');
            $table->foreignUuid('venue_id')->nullable()->constrained('venues')->onDelete('set null');
            $table->dateTime('kickoff_time');
            $table->enum('status', [
                'SCHEDULED', 'LIVE_1ST_HALF', 'HALF_TIME', 
                'LIVE_2ND_HALF', 'FULL_TIME', 'POSTPONED', 'ABANDONED'
            ])->default('SCHEDULED');
            $table->integer('home_score')->default(0);
            $table->integer('away_score')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('matches');
    }
};