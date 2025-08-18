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
        // Add indexes to tagihans table for better datatable performance
        Schema::table('tagihans', function (Blueprint $table) {
            // Index for status filtering (most common filter)
            $table->index('tagihanStatus', 'idx_tagihan_status');

            // Index for date sorting and filtering
            $table->index('tagihanDibayarPadaWaktu', 'idx_tagihan_dibayar_pada_waktu');

            // Index for year filtering
            $table->index('tagihanTahun', 'idx_tagihan_tahun');

            // Index for month filtering
            $table->index('tagihanBulan', 'idx_tagihan_bulan');

            // Index for pelanggan relationship
            $table->index('tagihanPelangganId', 'idx_tagihan_pelanggan_id');

            // Composite index for year + month filtering (common combination)
            $table->index(['tagihanTahun', 'tagihanBulan'], 'idx_tagihan_tahun_bulan');

            // Composite index for status + date sorting (most common query pattern)
            $table->index(['tagihanStatus', 'tagihanDibayarPadaWaktu'], 'idx_tagihan_status_dibayar');

            // Index for ID sorting (default ordering)
            $table->index('tagihanId', 'idx_tagihan_id');
        });

        // Add indexes to mspelanggan table for better filtering performance
        Schema::table('mspelanggan', function (Blueprint $table) {
            // Index for desa filtering
            $table->index('pelangganDesa', 'idx_pelanggan_desa');

            // Index for RT filtering
            $table->index('pelangganRt', 'idx_pelanggan_rt');

            // Index for RW filtering
            $table->index('pelangganRw', 'idx_pelanggan_rw');

            // Index for user relationship
            $table->index('pelangganUserId', 'idx_pelanggan_user_id');

            // Composite index for RT + RW filtering (common combination)
            $table->index(['pelangganRt', 'pelangganRw'], 'idx_pelanggan_rt_rw');

            // Composite index for desa + RT + RW filtering (address grouping)
            $table->index(['pelangganDesa', 'pelangganRt', 'pelangganRw'], 'idx_pelanggan_desa_rt_rw');
        });

        // Add indexes to pembayarans table for better relationship performance
        Schema::table('pembayarans', function (Blueprint $table) {
            // Index for tagihan relationship
            $table->index('pembayaranTagihanId', 'idx_pembayaran_tagihan_id');

            // Index for kasir filtering
            $table->index('pembayaranKasirId', 'idx_pembayaran_kasir_id');

            // Index for status filtering
            $table->index('pembayaranStatus', 'idx_pembayaran_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from tagihans table
        Schema::table('tagihans', function (Blueprint $table) {
            $table->dropIndex('idx_tagihan_status');
            $table->dropIndex('idx_tagihan_dibayar_pada_waktu');
            $table->dropIndex('idx_tagihan_tahun');
            $table->dropIndex('idx_tagihan_bulan');
            $table->dropIndex('idx_tagihan_pelanggan_id');
            $table->dropIndex('idx_tagihan_tahun_bulan');
            $table->dropIndex('idx_tagihan_status_dibayar');
            $table->dropIndex('idx_tagihan_id');
        });

        // Remove indexes from mspelanggan table
        Schema::table('mspelanggan', function (Blueprint $table) {
            $table->dropIndex('idx_pelanggan_desa');
            $table->dropIndex('idx_pelanggan_rt');
            $table->dropIndex('idx_pelanggan_rw');
            $table->dropIndex('idx_pelanggan_user_id');
            $table->dropIndex('idx_pelanggan_rt_rw');
            $table->dropIndex('idx_pelanggan_desa_rt_rw');
        });

        // Remove indexes from pembayarans table
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropIndex('idx_pembayaran_tagihan_id');
            $table->dropIndex('idx_pembayaran_kasir_id');
            $table->dropIndex('idx_pembayaran_status');
        });
    }
};
