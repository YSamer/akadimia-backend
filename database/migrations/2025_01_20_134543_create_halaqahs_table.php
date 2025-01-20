<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('halaqahs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['halaqah', 'sard'])->default('halaqah');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->morphs('target');
            $table->integer('duration_hours')->default(0);
            $table->integer('duration_minutes')->default(0);
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('halaqahs');
    }
};
