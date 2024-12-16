<?php

use App\Enums\SectionType;
use App\Enums\WeekDays;
use App\Enums\WirdType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('group_wird_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->enum('section_type', array_column(SectionType::cases(), 'value')); // ->change to update
            $table->enum('wird_type', array_column(WirdType::cases(), 'value'));
            $table->unsignedBigInteger('under_wird')->nullable(); //تابع لورد آخر
            $table->integer('grade')->default(1); // الدرجات
            $table->integer('sanction')->default(1); // العقوبات
            // Repeated
            $table->boolean('is_changed')->default(false);
            $table->integer('from')->nullable();
            $table->integer('to')->nullable();
            $table->integer('start_from')->nullable();
            $table->integer('end_to')->nullable();
            $table->integer('change_value')->nullable();
            $table->unsignedBigInteger('repeated_from_list')->nullable();
            $table->set('days', array_column(WeekDays::cases(), 'value'))->nullable();
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('repeated_from_list')->references('id')->on('lists')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_wird_configs');
    }
};
