<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('summaries_foods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');

            // Total nutrisi harian
            $table->decimal('total_calories')->default(0);
            $table->decimal('total_protein')->default(0);
            $table->decimal('total_fat')->default(0);
            $table->decimal('total_carbs')->default(0);

            // Per meal type
            $table->decimal('breakfast_calories')->default(0);
            $table->decimal('lunch_calories')->default(0);
            $table->decimal('dinner_calories')->default(0);
            $table->decimal('snack_calories')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'date']);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('summaries_foods');
    }
};
