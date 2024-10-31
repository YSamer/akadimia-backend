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
        Schema::create('wirds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->onDelete('cascade'); // إشارة إلى المجموعة
            $table->date('date'); // تاريخ الورود اليومي
            $table->enum('type', config('wird.types'));
            $table->integer('amount')->comment('المقدار'); // كمية الورد اليومية المطلوبة
            $table->decimal('score', 5, 2)->nullable()->comment('درجة الورد'); // عمود درجة الأداء، يمكن أن يكون نسبة مئوية مثل 100.00
            $table->timestamps();
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
