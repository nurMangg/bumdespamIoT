<?php

namespace App\Imports;

use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Roles;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class DataImport implements ToModel, WithStartRow, SkipsOnFailure, WithHeadingRow
{
    use SkipsFailures;

    public function startRow(): int
    {
        return 2;
    }


    public function model(array $row)
    {
        $row = array_change_key_case($row, CASE_UPPER);

        // Coba cari pelanggan berdasarkan nama dan nomor telepon
        $existingPelanggan = Pelanggan::where('pelangganKode', $row['NO'])
            ->first();

        if (!$existingPelanggan) {
            // Buat user baru
            $user = User::create([
                'name' => $row['NAMA'],
                'username' => strtolower($this->generateUniqueCode()),
                'password' => Hash::make('password'),
                'userRoleId' => Roles::where('roleName', 'pelanggan')->first()->roleId
            ]);

            // Buat pelanggan baru
            $existingPelanggan = Pelanggan::create([
                'pelangganKode' => $this->generateUniqueCode(),
                'pelangganNama' => $row['NAMA'],
                'pelangganPhone' => $row['TELEPON'],
                'pelangganDesa' => $row['DESA'],
                'pelangganRt' => $row['RT'],
                'pelangganRw' => $row['RW'],
                'pelangganGolonganId' => $row['GOLONGAN_ID'],
                'pelangganUserId' => $user->id
            ]);
        }

        // Buat tagihan baru
        $tagihan = Tagihan::create([
            'tagihanKode' => $this->generateUniqueCodeTagihan(),
            'tagihanPelangganId' => $existingPelanggan->pelangganId,
            'tagihanBulan' => $row['BULAN'],
            'tagihanTahun' => $row['TAHUN'],
            'tagihanInfoTarif' => $existingPelanggan->golongan->golonganTarif,
            'tagihanInfoAbonemen' => $row['ABONEMEN'],
            'tagihanMAwal' => $row['M_AWAL'],
            'tagihanMAkhir' => $row['M_AKHIR'],
            'tagihanUserId' => Auth::user()->id,
            'tagihanTanggal' => date('Y-m-d'),
            'tagihanStatus' => "Belum Lunas",
        ]);

        return Pembayaran::create([
            'pembayaranTagihanId' => $tagihan->tagihanId,
            'pembayaranJumlah' => (($row['M_AKHIR'] - $row['M_AWAL']) * $tagihan->tagihanInfoTarif),
            'pembayaranStatus' => 'Belum Lunas'
        ]);
    }


    public function generateUniqueCode(): string
    {
        $pelangganCount = Pelanggan::count();

        $pelangganPart = str_pad($pelangganCount + 1, 4, '0', STR_PAD_LEFT);

        return "PAM{$pelangganPart}";
    }

    public function generateUniqueCodeTagihan(): string
    {
        $date = date('ym');
        $tagihanCount = Tagihan::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();

        $tagihanPart = str_pad($tagihanCount + 1, 4, '0', STR_PAD_LEFT);

        return "TPAM-{$date}{$tagihanPart}";
    }
}
