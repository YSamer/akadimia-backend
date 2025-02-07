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
        Schema::create('group_wirds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->date('date');
            $table->unsignedSmallInteger('hifz_page')->nullable();
            $table->unsignedTinyInteger('tilawah_juz')->nullable();
            $table->unsignedTinyInteger('sama_hizb')->nullable();
            $table->unsignedSmallInteger('weekly_tahder_from')->nullable();
            $table->unsignedSmallInteger('tajweed_dars')->nullable();
            $table->unsignedSmallInteger('tafseer_dars')->nullable();
            $table->json('sard_shikh')->nullable();
            $table->json('sard_rafiq')->nullable();
            $table->unsignedSmallInteger('hifz_tohfa_from')->nullable();

            $table->timestamps();
            $table->unique(['group_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_wirds');
    }
};
