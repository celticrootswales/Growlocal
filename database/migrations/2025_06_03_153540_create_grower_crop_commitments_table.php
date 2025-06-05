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
        Schema::create('grower_crop_commitments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grower_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('distributor_crop_need_id')->constrained()->onDelete('cascade');
            $table->integer('committed_quantity');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['grower_id', 'distributor_crop_need_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grower_crop_commitments');
    }
};
