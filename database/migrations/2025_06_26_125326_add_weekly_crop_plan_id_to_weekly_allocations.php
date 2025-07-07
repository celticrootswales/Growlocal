<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weekly_allocations', function (Blueprint $table) {
            $table->unsignedBigInteger('weekly_crop_plan_id')->nullable()->after('commitment_id');
            $table->foreign('weekly_crop_plan_id')->references('id')->on('weekly_crop_plans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('weekly_allocations', function (Blueprint $table) {
            $table->dropForeign(['weekly_crop_plan_id']);
            $table->dropColumn('weekly_crop_plan_id');
        });
    }
};