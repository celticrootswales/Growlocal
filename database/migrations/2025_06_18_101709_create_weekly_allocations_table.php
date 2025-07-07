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
        Schema::create('weekly_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grower_crop_commitment_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->date('planned_date');
            $table->decimal('quantity', 10, 2);
            $table->timestamps();

            $table->unique(['grower_crop_commitment_id', 'planned_date'],
                           'allocation_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_allocations');
    }
};
