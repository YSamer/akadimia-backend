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
            $table->unsignedSmallInteger('hifz_page')->nullable()->default(null);
            $table->unsignedTinyInteger('tilawah_juz')->nullable()->default(null);
            $table->unsignedTinyInteger('sama_hizb')->nullable()->default(null);
            $table->unsignedSmallInteger('weekly_tahder_from')->nullable()->default(null);
            $table->unsignedSmallInteger('tajweed_dars')->nullable()->default(null);
            $table->unsignedSmallInteger('tafseer_dars')->nullable()->default(null);
            $table->unsignedSmallInteger('sard_shikh_from')->nullable()->default(null);
            $table->unsignedSmallInteger('sard_rafiq_from')->nullable()->default(null);
            $table->unsignedSmallInteger('hifz_tohfa_from')->nullable()->default(null);

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
