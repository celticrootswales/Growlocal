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
        Schema::table('grower_crop_commitments', function (Blueprint $table) {
            $table->unsignedBigInteger('crop_offering_id')->nullable()->after('grower_id');
            
            // Optional: Add foreign key if you want strict relation
            $table->foreign('crop_offering_id')->references('id')->on('crop_offerings')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('grower_crop_commitments', function (Blueprint $table) {
            $table->dropForeign(['crop_offering_id']);
            $table->dropColumn('crop_offering_id');
        });
    }
};
