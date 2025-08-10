<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Cards</title>
    <style>
        /* Style untuk halaman A4 */
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
        }
        .page {
            width: 210mm;
            height: 297mm;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            align-content: space-around;
            padding: 10mm;
            box-sizing: border-box;
        }
        .card {
            width: 90mm; /* Lebar kartu */
            height: 50mm; /* Tinggi kartu */
            background: linear-gradient(90deg, #005f87, #003f5c);
            border-radius: 10px;
            display: flex;
            color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            margin: 5mm;
        }
        .card .left {
            padding: 10mm;
            width: 60%;
            display: flex;
            flex-direction: column;
            justify-content: space-evenly;
        }
        .card .left .company {
            background-color: #2fb54a;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
        }
        .card .left .info {
            font-size: 16px;
            margin-top: 10px;
        }
        .card .left .info span {
            display: block;
            margin-top: 5px;
            font-size: 12px;
        }
        .card .right {
            width: 40%;
            background-color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 10px;
            color: #333;
        }
        .card .right .qr {
            width: 40mm;
            height: 40mm;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 5mm;
        }
        .card .right .scan-text {
            font-size: 12px;
            font-weight: bold;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
        }
        .col-md-6 {
            width: calc(100% / 2);
        }
        
    </style>
</head>
<body>
    @foreach ($data->chunk(8) as $chunk)
        <div class="page">
            <div class="row">
                @foreach ($chunk as $item)
                    <div class="col-md-6">
                        <div class="card">
                            <!-- Left section -->
                            <div class="left">
                                <div class="company">PAMSIMAS</div>
                                <div class="info">
                                    <strong>{{ $item->pelangganNama }}</strong><br>
                                    <span>{{ $item->pelangganKode }}</span><br>
                                    <span>{{ $item->pelangganAlamat }}</span><br>
                                </div>
                            </div>
                            <!-- Right section -->
                            <div class="right">
                                <div class="qr">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($item->pelangganKode) }}" alt="QR Code">
                                </div>
                                <div class="scan-text">SCAN ME</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</body>
</html>

