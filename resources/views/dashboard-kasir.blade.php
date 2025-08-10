@php
    use App\Models\SettingWeb;
    $settingWeb = SettingWeb::first();
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $settingWeb->settingWebNama ? $settingWeb->settingWebNama . ' | PDAM' : env('APP_NAME') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon"
        href="{{ $settingWeb->settingWebLogo ? asset($settingWeb->settingWebLogo) : asset('images/favicon.svg') }}"
        type="image/x-icon">
    <meta name="theme-color" content="#6777ef" />
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">
    <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700">
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free-6.7.1-web/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Tambahkan CDN untuk Datatables Responsive -->
    <link href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('plugins/sweetalert2/sweetalert2.min.css')}}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <style>
        body {
            background: url('https://png.pngtree.com/thumb_back/fh260/background/20230607/pngtree-beautiful-landscape-hd-wallpaper-image_2914564.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .main-header, .main-footer {
            background-color: rgba(255, 255, 255, 0.1); /* Transparan */
            backdrop-filter: blur(10px); /* Efek blur */
            border: none;
        }
        .main-footer {
            color: #ffffff; /* Warna teks footer */
        }

        .overlay-bg {
            background-color: rgba(255, 255, 255, 0.75); /* Transparan */
            min-height: 80vh;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3); /* Bayangan lebih halus */
        }


        .btn-blue {
            background-color: #608BC1;
            color: white;
        }

        .btn-blue:hover {
            background-color: white;
            color: #608BC1;
            border: #777 solid 1px;
        }

        .btn-out-blue {
            background-color: white;
            color: #608BC1;
            border: #777 solid 1px;
        }

        .btn-out-blue:hover {
            background-color: #608BC1;
            color: white;
        }

        .btn-blue, .btn-out-blue {
            transition: background-color 0.3s, color 0.3s; /* Transisi halus */
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.5); /* Efek hover */
            border-radius: 5px; /* Sudut membulat */
        }

        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
            border-radius: 4px;
        }
        
        .select2-container--bootstrap-5 .select2-selection--single {
            background-color: #fff;
            border: 1px solid #ced4da;
        }
        
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            padding: 5px 12px;
            color: #212529;
        }
        
        .select2-container--bootstrap-5 .select2-dropdown {
            border-color: #80bdff;
            border-radius: 4px;
        }
        
        .select2-container--bootstrap-5 .select2-search__field:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
            background-color: #608BC1;
            color: #fff;
        }
    </style>

    @stack('script-header')
</head>

<body class="layout-top-nav">
    <div class="wrapper">

        {{-- Preloader --}}
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake"
                src="{{ $settingWeb->settingWebLogo ? asset($settingWeb->settingWebLogo) : asset('images/favicon.svg') }}"
                alt="Logo" height="160" width="160">
        </div>

        {{-- Navbar --}}
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <a href="/" class="logo d-flex align-items-center">
                    <img src="{{ $settingWeb->settingWebLogo ? asset($settingWeb->settingWebLogo) : asset('images/favicon.svg') }}" alt="Logo" class="brand-image img-circle elevation-3 mr-2" style="opacity: .8; width: 40px; height: 40px;">
                    <span class="brand-text font-weight-light">{{ $settingWeb->settingWebNama ?? env('APP_NAME') }}</span>
                </a>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                            <i class="fas fa-expand-arrows-alt" style="color: white;"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" href="#">
                            <i class="fas fa-user-circle" style="color: white;"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <div class="dropdown-header text-center">
                                <strong>{{ Auth::user()->name ?? 'Unknown' }}</strong>
                                <p class="text-muted text-sm">{{ Auth::user()->role->roleName ?? 'Unknown' }}</p>
                            </div>
                            <div class="dropdown-divider"></div>
                            <form action="/logout" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
        </nav>

        {{-- Main Content --}}
        <div class="content-wrapper" style="margin-left: 0 !important; background: transparent;">
            <div class="content-header">
                <div class="container d-flex justify-content-center align-items-center flex-column">
                    <div class="overlay-bg w-100" style="max-width: 1200px;">
                        <div class="mb-5">
                            <h2 class="text-center font-weight-bold">Selamat Datang di Layanan Kasir PDAM</h2>
                            <marquee behavior="scroll" direction="left" class="text-primary">
                                Fokus pada kemudahan dan kecepatan dalam membayar tagihan PDAM Anda, dengan layanan yang lebih cepat dan lebih mudah.
                            </marquee>
                        </div>

                        <div class="card shadow w-100" style="max-width: 1000px; margin: 0 auto;">
                            <div class="card-body">
                                <form id="formCekTagihan">
                                    <div class="input-group mb-3">
                                        <select class="form-control" id="searchPelanggan" name="kodePelanggan">
                                            <option value="">Cari berdasarkan kode/nama pelanggan...</option>
                                        </select>
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-search"></i> Cari
                                            </button>
                                        </div>
                                    </div>
                                </form> 
                            </div>
                        </div>
                            
                        <div class="card shadow w-100" style="max-width: 1000px; margin: 10px auto;">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Informasi Pelanggan
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                                
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="tagihanTable" class="table table-striped table-hover table-responsive">
                                    <thead class="table">
                                        <tr>
                                            <th>ID</th>
                                            <th>Kode Pelanggan</th>
                                            <th>Nama Pelanggan</th>
                                            <th>Periode</th>
                                            <th>Meter Awal</th>
                                            <th>Meter Akhir</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data akan dimuat melalui AJAX -->
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                                
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <footer class="main-footer">
            <strong>&copy; 2025 <a href="https://withmangg.my.id" style="color: #ffffff">withMangg</a>.</strong> All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 1.1.0
            </div>
        </footer>
    </div>

    {{-- Scripts --}}
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.all.min.js')}}"></script>
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <!-- Tambahkan CDN untuk Datatables Responsive -->
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        if ("serviceWorker" in navigator) {
            navigator.serviceWorker.register("/sw.js").then(
                registration => console.log("Service worker registered:", registration),
                err => console.error("Service worker failed:", err)
            );
        }
    </script>
    <script>
        $(document).ready(function () {
            // Initialize Select2
            $('#searchPelanggan').select2({
                theme: 'bootstrap-5',
                placeholder: 'Cari berdasarkan kode/nama pelanggan...',
                allowClear: true,
                minimumInputLength: 3,
                ajax: {
                    url: '{{ route('pelanggan.search') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            search: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        
                        return {
                            results: data.data.map(function(item) {
                                return {
                                    id: item.pelangganKode,
                                    text: item.pelangganKode + ' - ' + item.pelangganNama
                                };
                            }),
                            pagination: {
                                more: data.current_page < data.last_page
                            }
                        };
                    },
                    cache: true
                }
            });

            var table = $('#tagihanTable').DataTable({
                responsive: true, // Aktifkan mode responsive
                columnDefs: [
                    {
                        targets: [3,6], // Kolom ID dan Bulan/Tahun
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
                e.preventDefault(); 
                var kodePelanggan = $('#searchPelanggan').val();

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
                                    item.bulanNama + ' - ' + item.tagihanTahun,
                                    item.tagihanMAwal,
                                    item.tagihanMAkhir,
                                    'Rp. ' + item.tagihanTotal.toLocaleString('id-ID'),
                                    `<span class="badge ${item.tagihanStatus === 'Lunas' ? 'bg-success' : 'bg-danger'}">${item.tagihanStatus}</span>`,
                                    `${item.tagihanStatus === 'Lunas' ? '<span class="badge bg-success">Lunas</span>' : `<a href="/tagihan/${item.tagihanEncrypted}/detail" class="btn btn-sm btn-primary">Bayar</a>`}`,
                                    
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
