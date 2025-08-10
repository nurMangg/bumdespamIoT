<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('readMeteran', function (Blueprint $table) {
            $table->id('readMeteranId');
            $table->string('readMeteranDeviceId', 50)->nullable();
            $table->string('readMeteranPelangganKode')->nullable();
            $table->decimal('readMeteranWaterUsage', 10, 2)->default(0);
            $table->timestamp('readMeteranReadingDate')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('readMeteran');
    }
};
