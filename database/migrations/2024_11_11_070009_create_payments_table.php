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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            // amount
            $table->integer('amount')->default(0);
            $table->string('image');
            $table->boolean('is_confirm')->default(false);
            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('confirmed_by')->references('id')->on('admins')->onDelete('set null');
        });
        // Schema::table('batch_applies', function ($table) {
        //     $table->unsignedBigInteger('payment_id')->nullable();

        //     $table->foreign('payment_id')->references('id')->on('payments')->onDelete(action: 'cascade');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
