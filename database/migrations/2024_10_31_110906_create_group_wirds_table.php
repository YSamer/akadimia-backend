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
            $table->unsignedSmallInteger('hifz_page')->nullable()->default(1);
            $table->unsignedTinyInteger('tilawah_juz')->nullable()->default(1);
            $table->unsignedTinyInteger('sama_hizb')->nullable()->default(1);
            $table->unsignedSmallInteger('weekly_tahder_from')->nullable()->default(1);
            $table->unsignedSmallInteger('tajweed_dars')->nullable()->default(null);
            $table->unsignedSmallInteger('tafseer_dars')->nullable()->default(null);

            $table->timestamps();
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
