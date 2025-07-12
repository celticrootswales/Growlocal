<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('grower_crop_commitments', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false);
        });
    }

    public function down()
    {
        Schema::table('grower_crop_commitments', function (Blueprint $table) {
            $table->dropColumn('is_locked');
        });
    }
};
