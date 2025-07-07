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
        Schema::table('weekly_crop_plans', function (Blueprint $table) {
            $table->unsignedBigInteger('grower_crop_commitment_id')->after('id')->index();
            $table->foreign('grower_crop_commitment_id')
                  ->references('id')
                  ->on('grower_crop_commitments')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('weekly_crop_plans', function (Blueprint $table) {
            $table->dropForeign(['grower_crop_commitment_id']);
            $table->dropColumn('grower_crop_commitment_id');
        });
    }
};
