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
        Schema::create('group_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->string('title')->nullable();
            $table->integer('amount')->nullable();
            $table->integer('from')->nullable();
            $table->integer('to')->nullable();
            $table->string('wird_type');
            $table->string('section_type');
            $table->decimal('score', 9, 2)->nullable();
            $table->enum('day', ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']);
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->index(['group_id', 'day']);
        });
    }

    // $request->validate([
    //     'wird_type' => 'required|in:' . implode(',', config('enums.wird_types')),
    //     'section_type' => 'required|in:' . implode(',', config('enums.section_types')),
    // ]);
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_configs');
    }
};
