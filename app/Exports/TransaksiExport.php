<?php

namespace App\Exports;

use App\Models\Tagihan;
use App\Models\Bulan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransaksiExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Tagihan::with(['pelanggan', 'pembayaranInfo'])
            ->where('tagihanStatus', 'Lunas')
            ->orderBy('tagihanKode', 'asc');

        foreach ($this->filters as $field => $value) {
            if (!empty($value)) {
                if ($field === 'tagihanDariTahun') {
                    $query->where('tagihanTahun', '>=', $value);
                } elseif ($field === 'tagihanDariBulan') {
                    $query->where('tagihanBulan', '>=', $value);
                } elseif ($field === 'tagihanSampaiTahun') {
                    $query->where('tagihanTahun', '<=', $value);
                } elseif ($field === 'tagihanSampaiBulan') {
                    $query->where('tagihanBulan', '<=', $value);
                } elseif ($field === 'pelangganDesa') {
                    $query->whereHas('pelanggan', function ($q) use ($value) {
                        $q->where('pelangganDesa', $value);
                    });
                } elseif ($field === 'pelangganRt') {
                    $query->whereHas('pelanggan', function ($q) use ($value) {
                        $q->where('pelangganRt', $value);
                    });
                } elseif ($field === 'pelangganRw') {
                    $query->whereHas('pelanggan', function ($q) use ($value) {
                        $q->where('pelangganRw', $value);
                    });
                } else {
                    $query->where($field, 'like', '%' . $value . '%');
                }
            }
        }

        $data = $query->get();

        $data->transform(function ($item) {
            $pelanggan = $item->pelanggan;
            $item->pelangganNama = $pelanggan->pelangganNama;
            $item->pelangganDesa = $pelanggan->pelangganDesa;
            $item->pelangganRTRW = $pelanggan->pelangganRt . ' / ' . $pelanggan->pelangganRw;
            $item->tagihanTotal = ($item->tagihanMAkhir - $item->tagihanMAwal) * $item->tagihanInfoTarif;
            $item->tagihanJumlahTotal = $item->tagihanTotal + $item->tagihanInfoAbonemen;
            $item->terverifikasi = $item->tagihanDibayarPadaWaktu;
            $item->metodePembayaran = $item->pembayaranInfo->pembayaranMetode;
            $item->kasir = User::find($item->pembayaranInfo->pembayaranKasirId)->name ?? 'Aplikasi';
            $item->formattedTagihanJumlahTotal = 'Rp. ' . number_format($item->tagihanJumlahTotal, 0, ',', '.');

            $item->tagihanTerbit = Bulan::where('bulanId', $item->tagihanBulan)->first()->bulanNama . ' - ' . $item->tagihanTahun;
            return $item;
        });

        // Hitung total semua tagihan lunas
        $totalTagihan = $data->sum('tagihanJumlahTotal');

        // Tambahkan baris terakhir untuk total semua tagihan
        $data->push((object)[
            'pelangganNama' => 'TOTAL SEMUA TAGIHAN LUNAS',
            'pelangganDesa' => '',
            'pelangganRTRW' => '',
            'tagihanTerbit' => '',
            'formattedTagihanJumlahTotal' => 'Rp. ' . number_format($totalTagihan, 0, ',', '.'),
            'terverifikasi' => '',
            'metodePembayaran' => '',
            'kasir' => '',
        ]);

        return $data;
    }

    public function headings(): array
    {
        return [
            "Nama Pelanggan",
            "Desa",
            "RT / RW",
            "Tagihan Terbit",
            "Total Tagihan",
            "Terverifikasi",
            "Metode Pembayaran",
            "Kasir",
        ];
    }

    public function map($item): array
    {
        return [
            $item->pelangganNama,
            $item->pelangganDesa,
            $item->pelangganRTRW,
            $item->tagihanTerbit,
            $item->formattedTagihanJumlahTotal,
            $item->terverifikasi,
            $item->metodePembayaran,
            $item->kasir,
        ];
    }
}
