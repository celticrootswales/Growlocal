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
        Schema::create('distributor_crop_needs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distributor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('crop_offering_id')->constrained()->onDelete('cascade');
            $table->integer('desired_quantity');
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['distributor_id', 'crop_offering_id']); // Each distributor can only set need once per crop
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_crop_needs');
    }
};
