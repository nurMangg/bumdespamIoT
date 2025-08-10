<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Tagihan</title>
    <!-- Tambahkan CDN Bootstrap dan Datatables -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Tambahkan CDN untuk Datatables Responsive -->
    <link href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap5.min.css" rel="stylesheet">

    <style>
        /* Background gradient */
        body {
            background: linear-gradient(135deg, #6e8efb, #a777e3);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        /* Card styling */
        .card {
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            background: #ffffff;
        }

        /* Table styling */
        table.dataTable tbody tr:hover {
            background-color: #f5f5f5;
        }

        /* Form styling */
        .form-control {
            border-radius: 10px;
        }

        /* Button styling */
        .btn-primary {
            background-color: #6e8efb;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background-color: #5b75e0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center text-white mb-4">Cek Tagihan</h1>

        <!-- Form Input Kode Pelanggan -->
        <div class="card p-4 mb-4">
            <div class="card-body">
                <form id="formCekTagihan">
                    <div class="mb-3">
                        <label for="kodePelanggan" class="form-label">Kode Pelanggan</label>
                        <input type="text" class="form-control" id="kodePelanggan" placeholder="Masukkan kode pelanggan" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">🔍 Cek Tagihan</button>
                </form>
            </div>
        </div>

        <!-- Tabel Data Tagihan -->
        <div class="card p-4">
            <div class="card-body">
                <table id="tagihanTable" class="table table-striped table-hover table-responsive">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Kode Pelanggan</th>
                            <th>Nama Pelanggan</th>
                            <th>Bulan</th>
                            <th>Tahun</th>
                            <th>Meter Awal</th>
                            <th>Meter Akhir</th>
                            <th>Tarif</th>
                            <th>Abonemen</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data akan dimuat melalui AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tambahkan CDN jQuery, Bootstrap, dan Datatables -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <!-- Tambahkan CDN untuk Datatables Responsive -->
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function () {
            var table = $('#tagihanTable').DataTable({
                responsive: true, // Aktifkan mode responsive
                columnDefs: [
                    {
                        targets: [3, 4], // Kolom ID dan Bulan/Tahun
                        responsivePriority: 1, // Membuat kolom ini prioritas untuk disembunyikan di perangkat kecil
                    },
                    {
                        targets: '_all', // Kolom lainnya
                        responsivePriority: 2, // Kolom ini akan diprioritaskan untuk tetap ditampilkan
                    }
                ]
            });

            // Event Submit Form
            $('#formCekTagihan').on('submit', function (e) {
                e.preventDefault(); // Hindari refresh halaman
                var kodePelanggan = $('#kodePelanggan').val();

                // Panggil AJAX untuk mendapatkan data tagihan
                $.ajax({
                    url: '/tagihan/kode-pelanggan', // Sesuaikan dengan route API
                    method: 'GET',
                    data: { kodePelanggan: kodePelanggan },
                    success: function (response) {
                        // Periksa apakah respons memiliki struktur yang diharapkan
                        if (response && Array.isArray(response.data)) {
                            // Kosongkan tabel sebelum menambahkan data baru
                            table.clear();

                            // Tambahkan data ke tabel
                            response.data.forEach((item, index) => {
                                table.row.add([
                                    index + 1,
                                    item.pelangganKode,
                                    item.pelangganNama,
                                    item.bulanNama,
                                    item.tagihanTahun,
                                    item.tagihanMAwal,
                                    item.tagihanMAkhir,
                                    'Rp. ' + item.tagihanInfoTarif.toLocaleString('id-ID'),
                                    'Rp. ' + item.tagihanInfoAbonemen.toLocaleString('id-ID'),
                                    'Rp. ' + item.tagihanTotal.toLocaleString('id-ID'),
                                    `<span class="badge ${item.tagihanStatus === 'Lunas' ? 'bg-success' : 'bg-danger'}">${item.tagihanStatus}</span>`,
                                ]);
                            });

                            // Perbarui tampilan tabel
                            table.columns.adjust().draw();
                        } else {
                            alert('Data tagihan tidak ditemukan atau respons tidak valid.');
                        }
                    },
                    error: function (xhr, status, error) {
                        // Tampilkan error di konsol untuk debugging
                        console.error('Error:', status, error);
                        console.error('Response:', xhr.responseText);

                        alert('Gagal mendapatkan data tagihan. Silakan coba lagi.');
                    }
                });
            });
        });
    </script>
</body>
</html>
