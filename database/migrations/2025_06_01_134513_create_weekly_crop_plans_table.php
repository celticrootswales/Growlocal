<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('weekly_crop_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grower_crop_commitment_id')->constrained()->onDelete('cascade');
            $table->date('week'); // e.g. 2025-06-01 (Monday of the week)
            $table->integer('expected_quantity');
            $table->timestamps();

            $table->unique(['grower_crop_commitment_id', 'week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_crop_plans');
    }
};
