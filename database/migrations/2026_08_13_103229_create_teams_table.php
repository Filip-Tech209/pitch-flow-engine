<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('teams', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('home_venue_id')->nullable()->constrained('venues')->onDelete('set null');
            $table->string('name');
            $table->string('short_name', 10);
            $table->string('logo_path')->nullable();
            $table->string('coach_name')->nullable();
            $table->string('primary_color', 7)->default('#0F2D5B')->nullable();
            $table->string('secondary_color', 7)->default('#2563EB')->nullable();
            $table->enum('default_formation', ['4-4-2', '4-3-3', '3-5-2', '4-2-3-1', '5-3-2'])->default('4-3-3');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('teams');
    }
};