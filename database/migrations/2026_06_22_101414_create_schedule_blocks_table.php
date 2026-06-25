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
        Schema::create('schedule_blocks', function (Blueprint $table) {
            $table->id();

            // Voor eenmalige blokkades: specifieke datum.
            // Voor herhalende blokkades: null, en day_of_week wordt gebruikt.
            $table->date('date')->nullable();

            // Voor herhalende blokkades: dag van de week (0=zondag...6=zaterdag).
            // Voor eenmalige blokkades: null, date wordt gebruikt.
            $table->unsignedTinyInteger('day_of_week')->nullable();

            $table->time('start_time');
            $table->time('end_time');

            $table->boolean('is_recurring')->default(false);

            $table->string('reason')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_blocks');
    }
};