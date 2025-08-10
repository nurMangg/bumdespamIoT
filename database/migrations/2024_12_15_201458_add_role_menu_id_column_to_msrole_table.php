<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('msrole', function (Blueprint $table) {
            $table->mediumText('roleMenuId')->nullable()->after('roleId');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('msrole', function (Blueprint $table) {
            $table->dropColumn('roleMenuId');
        });
    }
};
