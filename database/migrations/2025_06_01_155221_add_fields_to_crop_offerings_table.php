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
            if (!Schema::hasColumn('crop_offerings', 'amount_needed')) {
                $table->decimal('amount_needed', 8, 2)->nullable();
            }

            // Only add term if it doesn't already exist
            if (!Schema::hasColumn('crop_offerings', 'term')) {
                $table->string('term')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('crop_offerings', function (Blueprint $table) {
            if (Schema::hasColumn('crop_offerings', 'amount_needed')) {
                $table->dropColumn('amount_needed');
            }

            if (Schema::hasColumn('crop_offerings', 'term')) {
                $table->dropColumn('term');
            }
        });
    }
};
