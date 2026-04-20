<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crowdfunding_products', function (Blueprint $table) {
            $table->unsignedInteger('finish_schedule_version')->default(0)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('crowdfunding_products', function (Blueprint $table) {
            $table->dropColumn('finish_schedule_version');
        });
    }
};
