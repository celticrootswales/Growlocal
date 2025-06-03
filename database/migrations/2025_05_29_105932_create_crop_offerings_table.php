<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('crop_offerings', function (Blueprint $table) {
            $table->id();
            $table->string('crop_name');
            $table->string('icon')->nullable();
            $table->enum('unit', ['kg', 'ea']);
            $table->year('year');
            $table->decimal('default_price', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('crop_offerings');
    }
};