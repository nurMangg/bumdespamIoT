<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Menu::insert([
            ['menuId' => 1, 'menuName' => 'Dashboard', 'menuRoute' => 'dashboard', 'menuParentId' => null, 'menuOrder' => 1, 'menuIcon' => 'fas fa-tachometer-alt', 'created_at' => '2024-12-15 13:27:32', 'updated_at' => '2024-12-15 13:27:32'],
            ['menuId' => 2, 'menuName' => 'Layanan', 'menuRoute' => 'layanan', 'menuParentId' => null, 'menuOrder' => 2, 'menuIcon' => 'fas fa-edit', 'created_at' => '2024-12-15 13:28:34', 'updated_at' => '2024-12-15 13:28:34'],
            ['menuId' => 3, 'menuName' => 'Tagihan', 'menuRoute' => 'tagihan.index', 'menuParentId' => 2, 'menuOrder' => 1, 'menuIcon' => 'far fa-circle', 'created_at' => '2024-12-15 13:29:38', 'updated_at' => '2024-12-15 13:29:38'],
            ['menuId' => 4, 'menuName' => 'Transaksi', 'menuRoute' => 'transaksi.index', 'menuParentId' => 2, 'menuOrder' => 2, 'menuIcon' => 'far fa-circle', 'created_at' => '2024-12-15 13:31:59', 'updated_at' => '2024-12-15 13:31:59'],
            ['menuId' => 5, 'menuName' => 'Laporan', 'menuRoute' => 'laporan', 'menuParentId' => null, 'menuOrder' => 3, 'menuIcon' => 'fas fa-file-pdf', 'created_at' => '2024-12-15 13:34:29', 'updated_at' => '2024-12-15 13:34:29'],
            ['menuId' => 6, 'menuName' => 'Laporan Pelanggan', 'menuRoute' => 'laporan-pelanggan.index', 'menuParentId' => 5, 'menuOrder' => 1, 'menuIcon' => 'far fa-circle', 'created_at' => '2024-12-15 13:48:24', 'updated_at' => '2024-12-15 13:48:24'],
            ['menuId' => 7, 'menuName' => 'Laporan Tagihan', 'menuRoute' => 'laporan-tagihan.index', 'menuParentId' => 5, 'menuOrder' => 2, 'menuIcon' => 'far fa-circle', 'created_at' => '2024-12-15 13:50:42', 'updated_at' => '2024-12-15 13:50:42'],
            ['menuId' => 8, 'menuName' => 'Master Data', 'menuRoute' => 'master', 'menuParentId' => null, 'menuOrder' => 4, 'menuIcon' => 'fas fa-edit', 'created_at' => '2024-12-15 13:54:12', 'updated_at' => '2024-12-15 13:54:12'],
            ['menuId' => 9, 'menuName' => 'Golongan Tarif', 'menuRoute' => 'golongan-tarif.index', 'menuParentId' => 8, 'menuOrder' => 1, 'menuIcon' => 'far fa-circle', 'created_at' => '2024-12-15 13:55:28', 'updated_at' => '2024-12-15 13:55:28'],
            ['menuId' => 10, 'menuName' => 'Pelanggan', 'menuRoute' => 'pelanggan.index', 'menuParentId' => 8, 'menuOrder' => 2, 'menuIcon' => 'far fa-circle', 'created_at' => '2024-12-15 13:55:56', 'updated_at' => '2024-12-15 13:55:56'],
            ['menuId' => 11, 'menuName' => 'Data Tahun', 'menuRoute' => 'tahun.index', 'menuParentId' => 8, 'menuOrder' => 3, 'menuIcon' => 'far fa-circle', 'created_at' => '2024-12-15 13:56:22', 'updated_at' => '2024-12-15 13:56:22'],
            ['menuId' => 12, 'menuName' => 'Import Data', 'menuRoute' => 'import', 'menuParentId' => null, 'menuOrder' => 5, 'menuIcon' => 'fas fa-file-import', 'created_at' => '2024-12-15 13:57:00', 'updated_at' => '2024-12-15 13:57:00'],
            ['menuId' => 13, 'menuName' => 'Import Pelanggan', 'menuRoute' => 'import-pelanggan.index', 'menuParentId' => 12, 'menuOrder' => 1, 'menuIcon' => 'far fa-circle', 'created_at' => '2024-12-15 13:57:48', 'updated_at' => '2024-12-15 13:57:48'],
            ['menuId' => 14, 'menuName' => 'Setting', 'menuRoute' => 'setting', 'menuParentId' => null, 'menuOrder' => 6, 'menuIcon' => 'fas fa-cog', 'created_at' => '2024-12-15 13:58:13', 'updated_at' => '2024-12-15 13:58:13'],
            ['menuId' => 15, 'menuName' => 'Pengguna Aplikasi', 'menuRoute' => 'pengguna-aplikasi.index', 'menuParentId' => 14, 'menuOrder' => 1, 'menuIcon' => 'far fa-circle', 'created_at' => '2024-12-15 13:58:58', 'updated_at' => '2024-12-15 13:58:58'],
            ['menuId' => 16, 'menuName' => 'Menu Aplikasi', 'menuRoute' => 'menu-aplikasi.index', 'menuParentId' => 14, 'menuOrder' => 2, 'menuIcon' => 'far fa-circle', 'created_at' => '2024-12-15 13:59:26', 'updated_at' => '2024-12-15 13:59:26'],
            ['menuId' => 17, 'menuName' => 'Role Aplikasi', 'menuRoute' => 'role-aplikasi.index', 'menuParentId' => 14, 'menuOrder' => 3, 'menuIcon' => 'far fa-circle', 'created_at' => '2024-12-15 14:11:17', 'updated_at' => '2024-12-15 14:11:17'],
            ['menuId' => 18, 'menuName' => 'Input Tagihan', 'menuRoute' => 'input-tagihan', 'menuParentId' => null, 'menuOrder' => 1, 'menuIcon' => 'fas fa-file-import', 'created_at' => '2025-01-09 15:20:42', 'updated_at' => '2025-01-09 15:45:11'],
            ['menuId' => 19, 'menuName' => 'Input Tagihan', 'menuRoute' => 'input-tagihan.index', 'menuParentId' => 18, 'menuOrder' => 1, 'menuIcon' => 'far fa-circle', 'created_at' => '2025-01-09 15:22:44', 'updated_at' => '2025-01-09 15:22:44'],
            ['menuId' => 20, 'menuName' => 'Setting Web', 'menuRoute' => 'setting-web.index', 'menuParentId' => 14, 'menuOrder' => 1, 'menuIcon' => 'far fa-circle', 'created_at' => '2025-01-24 03:29:26', 'updated_at' => '2025-01-24 03:29:26'],
            ['menuId' => 21, 'menuName' => 'Riwayat Website', 'menuRoute' => 'riwayat-website.index', 'menuParentId' => 14, 'menuOrder' => 5, 'menuIcon' => 'far fa-circle', 'created_at' => '2025-01-24 05:58:32', 'updated_at' => '2025-01-24 05:58:32'],
            ['menuId' => 22, 'menuName' => 'Reset Password', 'menuRoute' => 'reset-password.index', 'menuParentId' => 14, 'menuOrder' => 3, 'menuIcon' => 'far fa-circle', 'created_at' => '2025-01-24 06:38:09', 'updated_at' => '2025-01-24 06:38:09'],
        ]);

    }
}
