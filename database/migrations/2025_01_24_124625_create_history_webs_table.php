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
        Schema::create('msriwayat', function (Blueprint $table) {
            $table->bigIncrements('riwayatId');
            $table->string('riwayatTable');
            $table->string('riwayatAksi');
            $table->text('riwayatData');
            $table->integer('riwayatUserId');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('msriwayat');
    }
};
