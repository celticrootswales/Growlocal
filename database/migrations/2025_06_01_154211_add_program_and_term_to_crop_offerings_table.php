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
        Schema::table('crop_offerings', function (Blueprint $table) {
            $table->string('program')->nullable(); // e.g., "Food and Fun"
            $table->string('term')->nullable();     // e.g., "Autumn Term"
        });
    }

    public function down()
    {
        Schema::table('crop_offerings', function (Blueprint $table) {
            $table->dropColumn(['program', 'term']);
        });
    }
};
