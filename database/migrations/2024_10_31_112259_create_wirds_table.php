<?php

use App\Enums\SectionType;
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
        Schema::create('wirds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('group_wird_config_id')->nullable();
            $table->date('date');
            $table->string('title')->nullable();
            $table->integer('start_from')->nullable();
            $table->integer('end_to')->nullable();
            // Attachment
            $table->string('file_path')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();

            $table->foreign('group_wird_config_id')->references('id')->on('group_wird_configs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wirds');
    }
};
