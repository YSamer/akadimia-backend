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
        Schema::create('wird_dones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreignId('wird_id')->constrained()->onDelete('cascade'); // إشارة إلى الورد اليومي
            $table->boolean('is_completed')->default(false); // حالة الإتمام
            $table->decimal('score', 5, 2)->nullable()->comment('درجة الأداء'); // عمود درجة الأداء، يمكن أن يكون نسبة مئوية مثل 100.00
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wird_dones');
    }
};
