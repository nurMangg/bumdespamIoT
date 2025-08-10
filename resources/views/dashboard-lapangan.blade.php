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
                            <h2 class="text-center font-weight-bold">Selamat Datang di Layanan Lapangan PDAM</h2>
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
                                            <button class="btn btn-primary" type="button" id="scanBarcode">
                                                <i class="fas fa-barcode"></i>
                                            </button>
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-search"></i> Cari
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card shadow w-100 mt-4" style="max-width: 1000px; margin: 0 auto;">
                            <div class="card-header">
                                <h3 class="card-title">Data Input Meter Air Hari Ini</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="tagihanTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Periode</th>
                                                <th>Kode Pelanggan</th>
                                                <th>Nama</th>
                                                <th>Alamat</th>
                                                <th>Meter Awal</th>
                                                <th>Meter Akhir</th>
                                                <th>Pemakaian</th>
                                                <th>Waktu Input</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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

    {{-- QR Code Scanner Modal --}}
    <div class="modal modal-blur fade" id="ajaxScanner" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div id="qr-reader" style="width: 100%;"></div>
                <div id="qr-reader-results"></div>
            </div>
        </div>
    </div>

    {{-- Meter Reading Modal --}}
    <div class="modal fade" id="meterReadingModal" tabindex="-1" role="dialog" aria-labelledby="meterReadingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="meterReadingModalLabel">Input Meter Air</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="meterReadingForm">
                        <input type="hidden" id="id" name="id">
                        <input type="hidden" id="pelangganId" name="pelangganId">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kode Pelanggan</label>
                                    <input type="text" class="form-control" id="pelangganKode" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Pelanggan</label>
                                    <input type="text" class="form-control" id="pelangganNama" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <input type="text" class="form-control" id="pelangganAlamat" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Golongan</label>
                                    <input type="text" class="form-control" id="pelangganGolongan" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tagihan Terakhir</label>
                                    <input type="text" class="form-control" id="tagihanTerakhir" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tagihan Meter Terakhir</label>
                                    <input type="text" class="form-control" id="tagihanMeterTerakhir" readonly>
                                </div>
                            </div>
                        </div>

                        <h5>Input Tagihan Baru</h5>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Periode Baru</label>
                                    <input type="text" class="form-control" id="periodePembacaan" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Stand Meter Lalu</label>
                                    <input type="text" class="form-control" id="tagihanMeterAwal" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tagihanMeterAkhir">Stand Meter Sekarang <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="tagihanMeterAkhir" name="tagihanMeterAkhir" required>
                                    <small class="text-muted">Masukkan angka meter saat ini</small>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="col-md-12">
                        <div class="alert alert-primary alert-dismissible">
                          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                          <h5><i class="icon fas fa-info-circle"></i> Informasi!</h5>
                          <ul>
                            <li>Input Tagihan Baru akan diproses setelah Anda mengisi data pelanggan dan informasi pemakaian.</li>
                            <li>Cek kembali, Pastikan data yang Anda masukkan sudah benar.</li>
                          </ul>
                        </div>
                      </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="submitMeterReading">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>

    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert2/sweetalert2.all.min.js')}}"></script>
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#tagihanTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('input-tagihan.listTagihan') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'tagihanPeriode', name: 'tagihanPeriode'},
                    {data: 'pelangganKode', name: 'pelangganKode'},
                    {data: 'pelangganNama', name: 'pelangganNama'},
                    {data: 'alamat', name: 'alamat'},
                    {data: 'tagihanMAwal', name: 'tagihanMAwal'},
                    {data: 'tagihanMAkhir', name: 'tagihanMAkhir'},
                    {data: 'pemakaian', name: 'pemakaian'},
                    {data: 'created_at', name: 'created_at'}
                ],
                order: [[8, 'desc']]
            });

            // Refresh table after successful submission
            $('#submitMeterReading').click(function() {
                table.ajax.reload();
            });
        });
    </script>
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
            let html5QrcodeScanner;

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

            // Fungsi untuk memulai scanner
            function startScanner() {
                html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", { fps: 10, qrbox: 300 });
                html5QrcodeScanner.render(onScanSuccess, onScanFailure);
            }

            // Fungsi untuk menghentikan scanner
            function stopScanner() {
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.clear().then(() => {
                        console.log("Scanner stopped.");
                    }).catch(error => {
                        console.error("Failed to stop scanner:", error);
                    });
                }
            }

            // Callback saat QR berhasil discan
            function onScanSuccess(decodedText, decodedResult) {
                // Set nilai ke Select2
                var newOption = new Option(decodedText, decodedText, true, true);
                $('#searchPelanggan').append(newOption).trigger('change');

                // Tutup modal scanner
                $('#ajaxScanner').modal('hide');

                // Hentikan scanner
                stopScanner();

                // Trigger form submit
                $('#formCekTagihan').submit();
            }

            // Callback saat scan QR gagal
            function onScanFailure(error) {
                console.warn(`QR Code scan failed. Error: ${error}`);
            }

            // Event listener untuk tombol scan barcode
            $('#scanBarcode').click(function() {
                $('#ajaxScanner').modal('show');
            });

            // Event listener untuk modal
            $('#ajaxScanner').on('shown.bs.modal', function () {
                startScanner();
            });

            $('#ajaxScanner').on('hidden.bs.modal', function () {
                stopScanner();
            });

            // Form submit handler
            $('#formCekTagihan').on('submit', function(e) {
                e.preventDefault();
                var kodePelanggan = $('#searchPelanggan').val();

                if (!kodePelanggan) {
                    toastr.error('Silakan pilih pelanggan terlebih dahulu');
                    return;
                }

                // Fetch customer data and show modal
                $.ajax({
                    url: '{{ route('input-tagihan.index') }}/' + kodePelanggan,
                    method: 'GET',
                    success: function(response) {
                        // Fill modal with customer data
                        $('#id').val(response.data.pelangganId);
                        $('#pelangganId').val(response.data.pelangganId);
                        $('#pelangganKode').val(response.data.pelangganKode);
                        $('#pelangganNama').val(response.data.pelangganNama);
                        $('#pelangganAlamat').val(response.data.pelangganDesa + ' RT ' + response.data.pelangganRt + ' RW ' + response.data.pelangganRw);
                        $('#pelangganGolongan').val(response.data.pelangganGolonganId);
                        $('#tagihanTerakhir').val(response.data.tagihanTerakhir);
                        $('#tagihanMeterTerakhir').val(response.data.tagihanBulanLalu);
                        $('#periodePembacaan').val(response.data.tagihanBulanBaru + ' ' + response.data.tagihanTahunBaru);
                        $('#tagihanMeterAwal').val(response.data.tagihanMeterAwal);

                        // Reset input fields
                        $('#tagihanMeterAkhir').val('');

                        // Show the modal
                        $('#meterReadingModal').modal('show');
                    },
                    error: function(xhr) {
                        toastr.error('Gagal mengambil data pelanggan');
                    }
                });
            });

            // Submit meter reading
            $('#submitMeterReading').click(function() {
                var form = $('#meterReadingForm')[0];

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                $.ajax({
                    url: '{{ route('input-tagihan.store') }}',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        meterAkhir: $('#tagihanMeterAkhir').val(),
                        catatanTagihan: $('#tagihanKeterangan').val(),
                        id: $('#id').val(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        setTimeout(function() {
                            $('#meterReadingModal').modal('hide');
                            toastr.success('Data berhasil disimpan');
                            form.reset();
                            $('#tagihanTable').DataTable().ajax.reload();
                            $('#searchPelanggan').val(null).trigger('change');
                        }, 1000);
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON?.message || 'Gagal menyimpan data');
                    }
                });
            });
        });
    </script>
</body>

</html>
