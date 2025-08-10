<?php

namespace App\Exports;

use App\Models\Tagihan;
use App\Models\User;
use App\Models\Bulan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransaksiKasirExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Tagihan::with('pelanggan')
            ->where('tagihanStatus', 'Lunas')
            ->orderBy('tagihanKode', 'asc');

        foreach ($this->filters as $field => $value) {
            if (!empty($value)) {
                if ($field === 'pelangganKasir') {
                    $query->whereHas('pembayaranInfo', function ($q) use ($value) {
                        $q->where('pembayaranKasirId', $value);
                    });
                } elseif ($field === 'hariIni') {
                    $query->whereDate('tagihanDibayarPadaWaktu', date('Y-m-d'));
                } else {
                    $query->where($field, 'like', '%' . $value . '%');
                }
            }
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Kode Tagihan',
            'Nama Pelanggan',
            'Desa',
            'RT/RW',
            'Total Tagihan',
            'Jumlah Total',
            'Terverifikasi',
            'Metode Pembayaran',
            'Kasir',
            'Periode Tagihan',
        ];
    }

    public function map($item): array
    {
        return [
            $item->tagihanKode,
            $item->pelanggan->pelangganNama ?? '-',
            $item->pelanggan->pelangganDesa ?? '-',
            ($item->pelanggan->pelangganRt ?? '-') . ' / ' . ($item->pelanggan->pelangganRw ?? '-'),
            ($item->tagihanMAkhir - $item->tagihanMAwal) * $item->tagihanInfoTarif,
            ($item->tagihanMAkhir - $item->tagihanMAwal) * $item->tagihanInfoTarif + $item->tagihanInfoAbonemen,
            $item->tagihanDibayarPadaWaktu ?? '-',
            $item->pembayaranInfo->pembayaranMetode ?? '-',
            User::find($item->pembayaranInfo->pembayaranKasirId)->name ?? 'Aplikasi',
            Bulan::where('bulanId', $item->tagihanBulan)->first()->bulanNama . ' - ' . $item->tagihanTahun,
        ];
    }
}
