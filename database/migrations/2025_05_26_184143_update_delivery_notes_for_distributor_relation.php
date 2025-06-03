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
            $table->dropColumn('destination');
            $table->unsignedBigInteger('distributor_id')->nullable()->after('user_id');
            $table->foreign('distributor_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->dropForeign(['distributor_id']);
            $table->dropColumn('distributor_id');
            $table->string('destination')->nullable();
        });
    }
};
