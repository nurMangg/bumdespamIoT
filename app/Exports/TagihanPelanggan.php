<?php

namespace App\Exports;

use App\Models\Pelanggan;
use App\Models\Golongan;
use App\Models\SettingWeb;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class PelangganExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Pelanggan::query();

        foreach ($this->filters as $field => $value) {
            if (!empty($value)) {
                $query->where($field, 'like', '%' . $value . '%');
            }
        }

        $data = $query->orderBy('pelangganKode', 'asc')->get();

        // Konversi ID Golongan menjadi Nama Golongan
        $data->transform(function ($item) {
            $item->pelangganGolonganId = Golongan::find($item->pelangganGolonganId)->golonganNama;
            return $item;
        });

        // Tambahkan baris terakhir dengan tanggal cetak
        $data->push((object)[
            'pelangganKode' => '',
            'pelangganNama' => 'Dicetak pada ' . now()->format('d-m-Y H:i:s'),
            'pelangganAlamat' => '',
            'pelangganGolonganId' => '',
        ]);

        return $data;
    }

    public function headings(): array
    {
        return [
            "Kode Pelanggan",
            "Nama Pelanggan",
            "Alamat",
            "Golongan",
        ];
    }

    public function map($item): array
    {
        return [
            $item->pelangganKode,
            $item->pelangganNama,
            $item->pelangganAlamat,
            $item->pelangganGolonganId,
        ];
    }
}
