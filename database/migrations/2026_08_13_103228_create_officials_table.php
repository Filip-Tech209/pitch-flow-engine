<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('officials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('role', ['MAIN_REFEREE', 'ASSISTANT_REFEREE_1', 'ASSISTANT_REFEREE_2', 'FOURTH_OFFICIAL', 'VAR', 'COMMISSIONER'])->default('MAIN_REFEREE');
            $table->string('nationality')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('officials');
    }
};