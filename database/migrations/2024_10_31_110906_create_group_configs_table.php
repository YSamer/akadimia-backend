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
        $daysTypes = ["hifz", "tafseer", "tajweed", "morajaa", "ajaza"];
        Schema::create('group_configs', function (Blueprint $table) use ($daysTypes) {
            $table->id();
            $table->foreignId('group_id')->unique()->constrained('groups')->cascadeOnDelete();

            $table->unsignedTinyInteger('tilawah_juz_grade')->default(2);
            $table->unsignedTinyInteger('tilawah_juz_sanction')->default(2);

            $table->unsignedTinyInteger('sama_hizb_grade')->default(2);
            $table->unsignedTinyInteger('sama_hizb_sanction')->default(2);

            $table->unsignedTinyInteger('weekly_tahder_grade')->default(2);
            $table->unsignedTinyInteger('weekly_tahder_sanction')->default(2);

            $table->unsignedTinyInteger('hifz_night_tahder_grade')->default(2);
            $table->unsignedTinyInteger('hifz_night_tahder_sanction')->default(2);
            $table->unsignedTinyInteger('hifz_before_tahder_grade')->default(2);
            $table->unsignedTinyInteger('hifz_before_tahder_sanction')->default(2);
            $table->unsignedTinyInteger('hifz_tafser_grade')->default(2);
            $table->unsignedTinyInteger('hifz_tafser_sanction')->default(2);
            $table->unsignedTinyInteger('hifz_tadabor_grade')->default(2);
            $table->unsignedTinyInteger('hifz_tadabor_sanction')->default(2);
            $table->unsignedTinyInteger('hifz_waqfa_grade')->default(2);
            $table->unsignedTinyInteger('hifz_waqfa_sanction')->default(2);
            $table->unsignedTinyInteger('hifz_dabt_tilawah_grade')->default(2);
            $table->unsignedTinyInteger('hifz_dabt_tilawah_sanction')->default(2);
            $table->unsignedTinyInteger('salat_hifz_grade')->default(2);
            $table->unsignedTinyInteger('salat_hifz_sanction')->default(2);
            $table->unsignedTinyInteger('halaqah_grade')->default(2);
            $table->unsignedTinyInteger('halaqah_sanction')->default(2);
            $table->unsignedTinyInteger('sard_shikh_grade')->default(5);
            $table->unsignedTinyInteger('sard_shikh_sanction')->default(5);
            $table->unsignedTinyInteger('sard_rafiq_grade')->default(5);
            $table->unsignedTinyInteger('sard_rafiq_sanction')->default(5);
            $table->unsignedTinyInteger('tafseer_dars_grade')->default(5);
            $table->unsignedTinyInteger('tafseer_dars_sanction')->default(5);
            $table->unsignedTinyInteger('tajweed_dars_grade')->default(5);
            $table->unsignedTinyInteger('tajweed_dars_sanction')->default(5);
            $table->unsignedTinyInteger('fwaed_grade')->default(5);
            $table->unsignedTinyInteger('fwaed_sanction')->default(5);
            $table->enum('saturday', $daysTypes)->default("hifz");
            $table->enum('sunday', $daysTypes)->default("hifz");
            $table->enum('monday', $daysTypes)->default("hifz");
            $table->enum('tuesday', $daysTypes)->default("hifz");
            $table->enum('wednesday', $daysTypes)->default("hifz");
            $table->enum('thursday', $daysTypes)->default("tafseer");
            $table->enum('friday', $daysTypes)->default("tajweed");
            $table->unsignedSmallInteger('sard_shikh')->nullable()->default(null);
            $table->unsignedSmallInteger('sard_rafiq')->nullable()->default(null);
            $table->unsignedSmallInteger('tohfa')->nullable()->default(null);
            $table->unsignedSmallInteger('hifz_start_from')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_configs');
    }
};
