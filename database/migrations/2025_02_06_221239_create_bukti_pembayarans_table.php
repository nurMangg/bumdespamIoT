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
        Schema::create('buktiPembayaran', function (Blueprint $table) {
            $table->bigIncrements('buktiPembayaranId');
            $table->unsignedBigInteger('buktiPembayaranPembayaranId');
            $table->foreign('buktiPembayaranPembayaranId')->references('pembayaranId')->on('pembayarans')->onDelete('cascade');
            $table->longText('buktiPembayaranFoto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key
        Schema::table('buktiPembayaran', function (Blueprint $table) {
            $table->dropForeign('buktipembayaran_buktipembayaranpembayaranid_foreign');
        });
        Schema::dropIfExists('buktiPembayaran');
    }
};
