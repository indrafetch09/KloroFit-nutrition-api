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
            
            // Total Food Consumed (Breakfast + Lunch + Dinner + Snack)
            $table->decimal('total_calories_consumed', 8, 2)->default(0);
            $table->decimal('total_carbs', 8, 2)->default(0);
            $table->decimal('total_protein', 8, 2)->default(0);
            $table->decimal('total_fat', 8, 2)->default(0);
            
            // Total Activity Calories Burned
            $table->decimal('total_calories_burned', 8, 2)->default(0);
            
            // Net Result (Consumed - Burned)
            $table->decimal('net_calories', 8, 2)->default(0);
            
            $table->timestamps();
            
            // One summary per user per day
            $table->unique(['user_id', 'date']);
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('daily_summaries');
    }
};
