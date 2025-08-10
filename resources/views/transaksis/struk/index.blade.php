<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran PAMSIMAS</title>
    <style>
        /* @font-face {
            font-family: 'Tahoma';
            src: url("fonts/Tahoma Regular font.ttf") format('truetype');
        } */
        body {
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            font-style: normal;
            font-size: 19px;
            margin: 0;
            padding: 0;
            width: 100%;
            box-sizing: border-box;
        }
        body, html{
            padding: 0%;
            margin: 0%;
        }
        .struk {
            width: 100%;
            max-width: 100%;
            margin: 0;
            /* padding: 5px; */
            border: none;
        }
        .struk h1, .struk h2, .struk h3 {
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .kop, .footer {
            text-align: center;
        }
        .header, .content, .footer {
            margin-bottom: 10px;
        }
        .content {
            text-align: left;
            padding-left: 10px;
            padding-right: 10px;
        }
        .content table {
            width: 100%;
            border-collapse: collapse;
        }
        .content table td {
            padding: 2px 0;
        }
        .footer {
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="struk">
        <div class="kop">
            <img src="{{ public_path('images/logolandscape.png') }}" alt="Logo-bumdes" style="max-width: 100%;">
        </div>
        <br>
        <div class="header" style="margin-bottom: 40px;">
            <h3>STRUK PEMBAYARAN PAMSIMAS</h3>       
        </div>
        <div class="" style="padding-right: 10px; padding-left: 10px">
            <p style="line-height: 0.8em;">No. Ref : {{ $data['tagihanKode']}}</p>
            <p style="line-height: 0.8em; margin-bottom: 30px">Kasir : {{ $data['pembayaranKasirName'] ?? 'Aplikasi' }}</p>
        </div>
        
        <div class="content">
            <table>
                <tr>
                    <td>Tgl. Bayar</td>
                    <td>: {{ $data['date'] }}</td>
                </tr>
                <tr>
                    <td>No. Meter</td>
                    <td>: {{ $data['pelangganKode'] }}</td>
                </tr>
                <tr>
                    <td>Nama Pelanggan</td>
                    <td>: {{ $data['pelangganNama'] }}</td>
                </tr>
                <tr>
                    <td>Stand Meter</td>
                    <td>: {{ $data['tagihanMeteranAwal']}} - {{$data['tagihanMeteranAkhir'] }}</td>
                </tr>
                <tr>
                    <td>Tagihan bln.</td>
                    <td>: {{ $data['nama_bulan'] }} &nbsp; {{ $data['tagihanTahun'] }}</td>
                </tr>
                <tr>
                    <td>Jml. Tagihan</td>
                    <td>: Rp. {{ $data['formattedTagihanTotal'] }},-</td>
                </tr>
                <tr>
                    <td>Abonemen</td>
                    <td>: Rp. {{ $data['formattedTotalDenda'] }},-</td>
                </tr>
                <tr>
                    <td><strong>TOTAL</strong></td>
                    <td><strong>: Rp. {{ $data['formattedTotal'] }},-</strong></td>
                </tr>
            </table>
        </div>
        <div class="footer" style="padding-right: 10px; padding-left: 10px">
            <p>Simpanlah Struk Ini Sebagai</p>
            <p>Bukti Pembayaran Anda</p>
            <hr style="border: 1px dashed #000;">
            <p><strong>TERIMA KASIH</strong></p>
        </div>
        <p style="padding-left: 10px; padding-right: 10px">Dicetak pada tanggal : {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
