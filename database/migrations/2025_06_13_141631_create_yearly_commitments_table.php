<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYearlyCommitmentsTable extends Migration
{
    public function up()
    {
        Schema::create('yearly_commitments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grower_id');
            $table->unsignedBigInteger('crop_offering_id');
            $table->decimal('committed_quantity', 10, 2);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();

            $table->foreign('grower_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('crop_offering_id')->references('id')->on('crop_offerings')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('yearly_commitments');
    }
}