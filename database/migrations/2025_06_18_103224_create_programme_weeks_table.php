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
        Schema::create('programme_weeks', function (Blueprint $table) {
            $table->id();
            $table->string('term'); // e.g. Autumn, Food & Fun
            $table->date('date');   // single date representing the week
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programme_weeks');
    }
};
