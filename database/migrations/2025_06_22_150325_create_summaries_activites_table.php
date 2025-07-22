<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('summaries_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('calories_burned');
            $table->integer('activity_count')->default(0);
            $table->integer('duration_minutes')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('summaries_activities');
    }
};
