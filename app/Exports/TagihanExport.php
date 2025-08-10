<?php

namespace App\Exports;

use App\Models\Tagihan;
use App\Models\Bulan;
use Illuminate\Support\Facades\Auth;
use App\Models\Pelanggan;
use App\Models\Roles;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class TagihanExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
{
    if (Auth::user()->userRoleId != Roles::where('roleName', 'pelanggan')->first()->roleId) {
        $query = Tagihan::with('pelanggan')->orderBy('tagihanKode', 'asc');
    } else {
        $pelanggan = Pelanggan::where('pelangganUserId', Auth::user()->id)->first();
        $query = Tagihan::with('pelanggan')->where('tagihanPelangganId', $pelanggan->pelangganId)
            ->orderBy('tagihanKode', 'asc');
    }

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

    // Ambil data tagihan
    $data = $query->get();

    // Tambahkan baris terakhir dengan tanggal cetak
    $data->push((object)[
        'pelanggan' => (object)['pelangganKode' => '', 'pelangganNama' => '', 'pelangganPhone' => '', 'pelangganDesa' => '', 'pelangganRt' => '', 'pelangganRw' => ''],
        'tagihanBulan' => '',
        'tagihanTahun' => '',
        'tagihanMAwal' => '',
        'tagihanMAkhir' => '',
        'tagihanInfoTarif' => '',
        'tagihanInfoAbonemen' => '',
        'tagihanStatus' => 'Dicetak pada: ' . Carbon::now()->format('d F Y H:i'),
    ]);

    return $data;
}

    public function headings(): array
    {
        return [
            "Kode",
            "Nama Pelanggan",
            "Telepon",
            "Desa",
            "RT / RW",
            "Tagihan Terbit",
            "Meter Awal",
            "Meter Akhir",
            "Penggunaan",
            "Total Tagihan",
            "Jumlah Total",
            "Status",
        ];
    }

    public function map($item): array
    {
        if ($item->tagihanStatus === 'Dicetak pada: ' . Carbon::now()->format('d F Y H:i')) {
            return ['', '', '', '', '', '', '', '', '', $item->tagihanStatus];
        }
    
        return [
            $item->pelanggan->pelangganKode,
            $item->pelanggan->pelangganNama,
            $item->pelanggan->pelangganPhone,
            $item->pelanggan->pelangganDesa,
            $item->pelanggan->pelangganRt . ' / ' . $item->pelanggan->pelangganRw,
            (Bulan::find($item->tagihanBulan)?->bulanNama ?? '') . ' - ' . $item->tagihanTahun,
            $item->tagihanMAwal,
            $item->tagihanMAkhir,
            $item->tagihanMAkhir - $item->tagihanMAwal,
            'Rp. ' . number_format(($item->tagihanMAkhir - $item->tagihanMAwal) * $item->tagihanInfoTarif, 0, ',', '.'),
            'Rp. ' . number_format(($item->tagihanMAkhir - $item->tagihanMAwal) * $item->tagihanInfoTarif + $item->tagihanInfoAbonemen, 0, ',', '.'),
            $item->tagihanStatus,
        ];
    }
    
}
