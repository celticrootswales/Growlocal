<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('crop_commitments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('crop_plan_id');
            $table->unsignedBigInteger('grower_id');
            $table->decimal('quantity_committed', 8, 2);
            $table->timestamps();

            $table->foreign('crop_plan_id')->references('id')->on('crop_plans')->onDelete('cascade');
            $table->foreign('grower_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['crop_plan_id', 'grower_id']); // Prevent duplicate commitments per plan/grower
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_commitments');
    }
};