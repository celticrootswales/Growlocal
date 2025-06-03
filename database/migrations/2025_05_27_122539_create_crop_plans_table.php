<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('crop_plans', function (Blueprint $table) {
            $table->id();
            $table->date('week');
            $table->string('crop_name');
            $table->string('unit'); // kg, unit, etc.
            $table->integer('expected_quantity');
            $table->decimal('price_per_unit', 8, 2);
            $table->foreignId('distributor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('grower_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('confirmed_quantity')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crop_plans');
    }
};
