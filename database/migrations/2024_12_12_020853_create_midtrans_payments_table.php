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
        Schema::create('midtrans_payments', function (Blueprint $table) {
            $table->increments('midtransPaymentId');
            $table->unsignedBigInteger('midtransPaymentPembayaranId');
            $table->foreign('midtransPaymentPembayaranId')->references('pembayaranId')->on('pembayarans')->onDelete('cascade');
            $table->string('midtransPaymentOrderId');
            $table->string('midtransPaymentTransactionId');
            $table->string('midtransPaymentStatus');
            $table->text('midtransPaymentCatatan')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('midtrans_payments');
    }
};
