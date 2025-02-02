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
        Schema::create('user_wirds_dones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->boolean('tilawah_juz_done')->default(false);
            $table->boolean('sama_hizb_done')->default(false);
            $table->boolean('weekly_tahder_done')->default(2);
            $table->boolean('hifz_night_tahder_done')->default(2);
            $table->boolean('hifz_before_tahder_done')->default(2);
            $table->boolean('hifz_tafser_done')->default(2);
            $table->boolean('hifz_tadabor_done')->default(2);
            $table->text('hifz_waqfa_text')->nullable();
            $table->boolean('hifz_dabt_tilawah_done')->default(2);
            $table->boolean('salat_hifz_done')->default(2);
            $table->unsignedTinyInteger('halaqah_grade')->nullable();
            $table->unsignedTinyInteger('sard_shikh_grade')->nullable();
            $table->unsignedTinyInteger('sard_rafiq_grade')->nullable();
            $table->boolean('tafseer_dars_done')->default(5);
            $table->boolean('tajweed_dars_done')->default(5);
            $table->text('fwaed_text')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_wirds_dones');
    }
};
