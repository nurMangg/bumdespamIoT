<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Card</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }
        .card {
            width: 600px; /* Lebar kartu diperbesar */
            height: 300px; /* Tinggi kartu diperbesar */
            background: linear-gradient(90deg, #005f87, #003f5c);
            border-radius: 20px;
            display: flex;
            color: #fff;
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        .card .left {
            padding: 30px; /* Padding diperbesar */
            width: 70%;
            display: flex;
            flex-direction: column;
            justify-content: space-evenly;
        }
        .card .left .company {
            background-color: #2fb54a;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
            font-size: 20px; 
            font-weight: bold;
        }
        .card .left .info {
            font-size: 24px;
            margin-top: 15px;
        }
        .card .left .info span {
            display: block;
            margin-top: 8px;
            font-size: 18px; 
        }
        .card .right {
            width: 30%;
            background-color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px; 
            color: #333;
        }
        .card .right .qr {
            width: 160px; 
            height: 160px; 
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 15px;
        }
        .card .right .scan-text {
            font-size: 16px; 
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="card">
        <!-- Left section -->
        <div class="left">
            <div class="company">PAMSIMAS</div>
            <div class="info">
                <strong>{{ $data->pelangganNama }}</strong><br>
                <span>{{ $data->pelangganKode }}</span><br>
                <span>{{ $data->pelangganAlamat }}</span><br>
            </div>
        </div>
        <!-- Right section -->
        <div class="right">
            <canvas id="qr"></canvas>
            <div class="scan-text">SCAN ME</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pelangganKode = "{{ $data->pelangganKode }}"; // Data pelanggan kode
            QRCode.toCanvas(document.getElementById('qr'), pelangganKode, {
                width: 200,
                height: 200
            }, function (error) {
                if (error) {
                    console.error(error);
                } else {
                    console.log('Success! QR Code generated.');
                }
            });
        });
    </script>
</body>
</html>
