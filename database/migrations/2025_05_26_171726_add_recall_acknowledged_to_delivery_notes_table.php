<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->boolean('recall_acknowledged')->default(false);
        });
    }

    public function down()
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->dropColumn('recall_acknowledged');
        });
    }
};
