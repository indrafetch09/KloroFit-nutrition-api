<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->enum('type', ['run', 'walk', 'swimming', 'cycling']);
            $table->date('activity_date');
            $table->integer('duration_minutes');
            $table->decimal('distance', 8, 2)->nullable();
            $table->integer('calories_burned');
            $table->timestamp('created_at')->useCurrent();

            //      $table->timestamps(); // Jangan pakai kalau tidak ada `updated_at`
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
