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
        Schema::create('duitkuPayment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('merchant_code_id'); // Kode Merchant
            $table->string('reference')->unique(); // Kode Referensi dari Duitku
            $table->unsignedBigInteger('payment_pembayaranId');
            $table->foreign('payment_pembayaranId')->references('pembayaranId')->on('pembayarans')->onDelete('cascade');
            $table->string('payment_url')->nullable(); // URL Pembayaran Duitku
            $table->timestamp('payment_success')->nullable();
            $table->string('status_code'); // Status transaksi
            $table->string('status_message'); // Pesan status transaksi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key
        Schema::table('duitkuPayment', function (Blueprint $table) {
            $table->dropForeign('duitkupayment_payment_pembayaranid_foreign');
        });
        Schema::dropIfExists('duitkuPayment');
    }
};
