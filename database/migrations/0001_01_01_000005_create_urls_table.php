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
        Schema::create('urls', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('url');
            $table->string('type')->nullable();
            $table->unsignedBigInteger('urlable_id');
            $table->string('urlable_type');
            $table->timestamps();

            $table->index(['urlable_id', 'urlable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('urls');
    }
};
