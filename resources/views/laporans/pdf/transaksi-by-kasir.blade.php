<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>{{ $title ?? env('APP_NAME') }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            padding: 10px;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 5px;
        }
        .header img {
            height: 130px;
            width: 130px;
            margin-right: 20px;
        }
        .header h3,
        .header h4 {
            margin: 0;
        }
        .header h3 {
            font-size: 1.2em;
            font-weight: bold;
        }
        .header h4 {
            font-size: 1em;
            font-weight: normal;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
            box-shadow: 0 2px 3px rgba(0,0,0,0.1);
        }
        th, td {
            border: none;
            padding: 12px;
            text-align: left;
            background-color: #fff;
            border-bottom: 1px solid #ddd;
            font-size: 12px;
        }
        th {
            background-color: #608BC1;
            color: #fff;
            font-weight: bold;
            
        }
        tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }
        tfoot {
            font-weight: bold;
            background-color: #f4f4f9;
        }
        .footer {
            text-align: left;
            margin-top: 30px;
            font-size: 0.9em;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo">
        <div>
            <h3>Pagar Sejahtera | PDAM BUMDES</h3>
            <h4>{{ $title ?? env('APP_NAME') }}</h4><br>
            <table style="border-collapse: collapse; border: none;">
                <tr>
                    <td style="border: none;" width="100">Tanggal</td>
                    <td style="border: none;">: {{ $filterTanggal ?? 'Semua Tanggal' }}</td>
                </tr>
                <tr>
                    <td style="border: none;" width="100">Kasir</td>
                    <td style="border: none;">: {{ $filterKasir ?? 'Semua Kasir' }}</td>
                </tr>
            </table>
            {{-- <h4>Tanggal: {{ $filterTanggal ?? 'Semua Tanggal' }}</h4>
            <h4>Kasir&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $filterPelanggan ?? 'Semua Kasir' }}</h4> --}}
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                @foreach ($grid as $item)
                    <th>{{ $item['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    @foreach ($grid as $itemgrid)
                        @if ($itemgrid['field'] == 'pelangganBergabung')
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d F Y H:i') }}</td>
                        @else
                            <td>
                                @if (isset($item->{$itemgrid['field']}))
                                    {{ $item->{$itemgrid['field']} }}
                                @else
                                    -
                                @endif
                            </td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <table>
        <tr>
            <td colspan="2"><b>Jumlah Data</b></td>
        </tr>
        <tr>
            <td>Total Tagihan Lunas</td>
            <td>Rp. {{ number_format($dataJumlah['totalSemuaTagihanLunas'], 0, ',', '.') }}</td>
        </tr>
    </table>
    <div class="footer">
        Dicetak pada : {{ \Carbon\Carbon::now()->format('d F Y H:i') }}
    </div>
</body>
</html>

