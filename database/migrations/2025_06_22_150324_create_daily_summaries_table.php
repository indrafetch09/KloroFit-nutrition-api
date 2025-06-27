<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');

            // Total nutrisi harian
            $table->decimal('total_calories', 8, 2)->default(0);
            $table->decimal('total_protein', 8, 2)->default(0);
            $table->decimal('total_fat', 8, 2)->default(0);
            $table->decimal('total_carbs', 8, 2)->default(0);

            // Per meal type
            $table->decimal('breakfast_calories', 8, 2)->default(0);
            $table->decimal('lunch_calories', 8, 2)->default(0);
            $table->decimal('dinner_calories', 8, 2)->default(0);
            $table->decimal('snack_calories', 8, 2)->default(0);

            // Kalori yang dibakar dari aktivitas
            $table->decimal('activity_calories_burned', 8, 2)->default(0);

            $table->timestamps();
            $table->unique(['user_id', 'date']);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('daily_summaries');
    }
};
