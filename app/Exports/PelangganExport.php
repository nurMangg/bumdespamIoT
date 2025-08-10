<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
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
        $query = DB::table('mspelanggan')
            ->select(
                'mspelanggan.pelangganKode',
                'mspelanggan.pelangganNama',
                'mspelanggan.pelangganPhone',
                'mspelanggan.pelangganDesa',
                'mspelanggan.pelangganRt',
                'mspelanggan.pelangganRw',
                'msgolongan.golonganNama as pelangganGolongan'
            )
            ->leftJoin('msgolongan', 'mspelanggan.pelangganGolonganId', '=', 'msgolongan.golonganId')
            ->orderBy('mspelanggan.pelangganKode', 'asc');

        // Terapkan filter seperti TagihanExport
        foreach ($this->filters as $field => $value) {
            if (!empty($value)) {
                if ($field === 'pelangganDesa') {
                    $query->where('pelanggan.pelangganDesa', $value);
                } elseif ($field === 'pelangganRt') {
                    $query->where('pelanggan.pelangganRt', $value);
                } elseif ($field === 'pelangganRw') {
                    $query->where('pelanggan.pelangganRw', $value);
                } else {
                    $query->where($field, 'like', '%' . $value . '%');
                }
            }
        }

        $data = $query->get();

        // Tambahkan baris terakhir dengan tanggal cetak
        $data->push((object)[
            'pelangganKode' => '',
            'pelangganNama' => 'Dicetak pada ' . Carbon::now()->format('d-m-Y H:i:s'),
            'pelangganPhone' => '',
            'pelangganDesa' => '',
            'pelangganRt' => '',
            'pelangganRw' => '',
            'pelangganGolongan' => '',
        ]);

        return $data;
    }

    public function headings(): array
    {
        return [
            "Kode Pelanggan",
            "Nama Pelanggan",
            "Telepon",
            "Desa",
            "RT / RW",
            "Golongan",
        ];
    }

    public function map($item): array
    {
        return [
            $item->pelangganKode,
            $item->pelangganNama,
            $item->pelangganPhone,
            $item->pelangganDesa,
            $item->pelangganRt . ' / ' . $item->pelangganRw,
            $item->pelangganGolongan,
        ];
    }
}
