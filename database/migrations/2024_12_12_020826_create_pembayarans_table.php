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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->bigIncrements('pembayaranId');
            $table->unsignedBigInteger('pembayaranTagihanId');
            $table->foreign('pembayaranTagihanId')->references('tagihanId')->on('tagihans')->onDelete('cascade');
            $table->string('pembayaranMetode')->nullable();
            $table->double('pembayaranJumlah');
            $table->double('pembayaranUang')->nullable();
            $table->double('pembayaranKembali')->nullable();
            $table->enum('pembayaranStatus', ['Belum Lunas', 'Lunas'])->default('Belum Lunas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
