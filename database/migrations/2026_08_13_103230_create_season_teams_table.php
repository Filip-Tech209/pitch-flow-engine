<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('season_teams', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('season_id')->constrained('seasons')->onDelete('cascade');
            $table->foreignUuid('team_id')->constrained('teams')->onDelete('cascade');
            $table->string('group_name', 50)->nullable();
            $table->timestamps();

            $table->unique(['season_id', 'team_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('season_teams');
    }
};