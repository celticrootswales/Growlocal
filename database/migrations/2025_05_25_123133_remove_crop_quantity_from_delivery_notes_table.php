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
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->dropColumn(['crop', 'quantity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->string('crop');
            $table->integer('quantity');
        });
    }
};
