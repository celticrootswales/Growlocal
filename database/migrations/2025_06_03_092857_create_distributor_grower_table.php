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
        Schema::create('distributor_grower', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grower_id');
            $table->unsignedBigInteger('distributor_id');
            $table->timestamps();

            $table->foreign('grower_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('distributor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_grower');
    }
};
