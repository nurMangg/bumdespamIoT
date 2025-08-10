<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi</title>
</head>
<body>
    <h2>{{ $title }}</h2>
    <p>Kasir: {{ $filterKasir }}</p>
    <p>Tanggal: {{ $filterTanggal }}</p>

    <table border="1">
        <thead>
            <tr>
                <th>Kode Tagihan</th>
                <th>Nama Pelanggan</th>
                <th>Desa</th>
                <th>RT/RW</th>
                <th>Tagihan Terbit</th>
                <th>Metode Pembayaran</th>
                <th>Kasir</th>
                <th>Total Tagihan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $item->tagihanKode }}</td>
                    <td>{{ $item->pelangganNama }}</td>
                    <td>{{ $item->pelangganDesa }}</td>
                    <td>{{ $item->pelangganRTRW }}</td>
                    <td>{{ $item->tagihanTerbit }}</td>
                    <td>{{ $item->metodePembayaran }}</td>
                    <td>{{ $item->kasir }}</td>
                    <td>{{ $item->formattedTagihanJumlahTotal }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7"><strong>Total Semua Tagihan Lunas</strong></td>
                <td><strong>{{ 'Rp. ' . number_format($dataJumlah['totalSemuaTagihanLunas'], 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
