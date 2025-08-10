<?php

namespace App\Imports;

use App\Models\Pelanggan;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class PelangganImport implements 
    ToModel, 
    WithStartRow, 
    SkipsOnFailure, 
    WithHeadingRow,
    WithChunkReading,
    WithBatchInserts
{
    use SkipsFailures;

    private int $lastCode;
    private int $increment = 1;
    private $roleId;

    public function __construct()
    {
        // Ambil kode terakhir dari pelanggan
        $this->lastCode = $this->getLastCode();

        // Ambil role ID pelanggan sekali saja
        $role = Roles::where('roleName', 'pelanggan')->first();
        $this->roleId = $role ? $role->roleId : null;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function batchSize(): int
    {
        return 100;
    }

    private function getLastCode(): int
    {
        $last = Pelanggan::orderBy('pelangganKode', 'desc')->first();

        if ($last && preg_match('/PAM(\d+)/', $last->pelangganKode, $matches)) {
            return (int) $matches[1];
        }

        return 0;
    }

    private function generateNextCode(): string
    {
        do {
            $newNumber = $this->lastCode + $this->increment;
            $this->increment++;
    
            $newCode = 'PAM' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        } while (User::where('username', strtolower($newCode))->exists());
    
        return $newCode;
    }


    public function model(array $row)
    {
        $row = array_change_key_case($row, CASE_UPPER);

        if (!$this->roleId || !isset($row['NAMA'])) {
            return null; // Role tidak ditemukan atau nama kosong
        }

        $kode = $this->generateNextCode();
        Log::info('Membuat pelanggan dengan kode: ' . $kode);

        $user = User::create([
            'name' => $row['NAMA'],
            'username' => strtolower($kode), // atau bisa pakai custom username logic
            'password' => Hash::make('password'),
            'userRoleId' => $this->roleId,
        ]);

        return new Pelanggan([
            'pelangganKode' => $kode,
            'pelangganNama' => $row['NAMA'],
            'pelangganPhone' => $row['TELEPON'] ?? null,
            'pelangganDesa' => $row['DESA'] ?? null,
            'pelangganRt' => $row['RT'] ?? null,
            'pelangganRw' => $row['RW'] ?? null,
            'pelangganGolonganId' => $row['GOLONGAN_ID'] ?? null,
            'pelangganUserId' => $user->id
        ]);
    }
}
