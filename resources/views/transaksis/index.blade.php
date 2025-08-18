@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="#">{{ $breadcrumb ?? env('APP_NAME') }}</a></li>
                <li class="breadcrumb-item active">{{ $title ?? env('APP_NAME') }}</li>
              </ol>
          </div><!-- /.col -->
          <div class="col-sm-6">

          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container mb-3">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Transaksi</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
              </button>
              <button type="button" class="btn btn-tool" onclick="getInfoAllTransaksi()" title="Refresh">
                <i class="fas fa-sync-alt"></i>
              </button>
          </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="row">
              @if (Auth::user()->userRoleId !== App\Models\Roles::where('roleName', 'pelanggan')->first()->roleId)
              <div class="col-md-12">
                <div class="alert alert-primary alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <h5><i class="icon fas fa-exclamation-triangle"></i> Informasi!</h5>
                  <ul>
                    <li>Data yang disini merupakan data tagihan pembayaran semuanya, baik yang menggunakan <b>Tunai, Transfer Otomatis</b> maupun <b>Transfer Manual</b></li>

                  </ul>
                </div>
              </div>

              @endif
              <div class="col-12 col-sm-6 col-md-6">
                <div class="info-box">
                  <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-money-bill-wave"></i></span>

                  <div class="info-box-content">
                    <span class="info-box-text">Total Tagihan Belum Lunas</span>
                    <span class="info-box-number" id="totalTagihanBelumLunasSemua">
                      <small></small>
                    </span>
                  </div>
                  <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
              </div>
              <!-- /.col -->
              <div class="col-12 col-sm-6 col-md-6">
                <div class="info-box mb-3">
                  <span class="info-box-icon bg-success elevation-1"><i class="fas fa-money-bill-wave"></i></span>

                  <div class="info-box-content">
                    <span class="info-box-text" data-toggle="tooltip" data-placement="top" title="Total tagihan yang belum dibayar">Total Tagihan Lunas</span>
                    <span class="info-box-number" id="totalTagihanLunasSemua"></span>
                  </div>
                  <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
              </div>
              <!-- /.col -->

              <!-- fix for small devices only -->
              <div class="clearfix hidden-md-up"></div>

              <div class="col-12 col-sm-6 col-md-6">
                <div class="info-box mb-3">
                  <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-money-bill-wave"></i></span>

                  <div class="info-box-content">
                    <span class="info-box-text">Tagihan Belum Lunas Bulan Ini</span>
                    <span class="info-box-number" id="totalTagihanBelumLunasBulanIni"></span>
                  </div>
                  <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
              </div>
              <!-- /.col -->
              <div class="col-12 col-sm-6 col-md-6">
                <div class="info-box mb-3">
                  <span class="info-box-icon bg-success elevation-1"><i class="fas fa-money-bill-wave"></i></span>

                  <div class="info-box-content">
                    <span class="info-box-text">Tagihan Lunas Bulan Ini</span>
                    <span class="info-box-number" id="totalTagihanLunasBulanIni"></span>
                  </div>
                  <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
              </div>
              <!-- /.col -->

            </div>
          </div>
          <!-- /.card-body -->
        </div>
      </div>
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Daftar {{ $title ?? env('APP_NAME') }}</h3>

              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <!-- Filter Section -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="filter_bulan">Filter Bulan:</label>
                                    <select class="form-control" id="filter_bulan" name="filter_bulan">
                                        <option value="all">Semua Bulan</option>
                                        @foreach($bulanList as $bulan)
                                            <option value="{{ $bulan->bulanId }}" {{ $bulan->bulanId == $currentMonth ? 'selected' : '' }}>
                                                {{ $bulan->bulanNama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filter_tahun">Filter Tahun:</label>
                                    <select class="form-control" id="filter_tahun" name="filter_tahun">
                                        <option value="all">Semua Tahun</option>
                                        @foreach($tahunList as $tahun)
                                            <option value="{{ $tahun }}" {{ $tahun == $currentYear ? 'selected' : '' }}>
                                                {{ $tahun }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filter_status">Filter Status:</label>
                                    <select class="form-control" id="filter_status" name="filter_status">
                                        <option value="all">Semua Status</option>
                                        <option value="Lunas">Lunas</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Belum Lunas">Belum Lunas</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label><br>
                                    <button type="button" class="btn btn-primary" id="btn-filter">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="btn-reset">
                                        <i class="fas fa-undo"></i> Reset
                                    </button>
                                </div>
                            </div>
                <table class="table" id="laravel_datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            @foreach ($grid as $item)
                                <th>{{ $item['label'] }}</th>
                            @endforeach
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->

</div>

{{-- <x-form.modal :form="$form" :title="$title ?? env('APP_NAME')" /> --}}

<script type="text/javascript">
    $(document).ready(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        var dataTable = $('#laravel_datatable').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,      // Optimize rendering
            deferLoading: 0,        // Defer initial loading
            pageLength: 10,         // Reduce initial load
            lengthMenu: [10, 25, 50, 100], // Page size options
            stateSave: true,
            ajax: {
                url: '{{ route($route . '.index') }}',
                type: 'GET',
                dataType: 'json',
                data: function(d) {
                    // Add filter parameters
                    d.filter_bulan = $('#filter_bulan').val();
                    d.filter_tahun = $('#filter_tahun').val();
                    d.filter_status = $('#filter_status').val();
                    d.cache_bust = new Date().getTime(); // Prevent caching issues
                },
                error: function (xhr, status, error) {
                    console.log('AJAX Error: ' + status + error);
                    // Show user-friendly error message
                    $('#laravel_datatable_processing').hide();
                    //showError('Gagal memuat data. Silakan refresh halaman.');
                }
            },
            columns: [
              {
                data: 'id',
                name: 'id',
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
              },
              @foreach ($grid as $field)
                  {data: '{{ $field['field'] }}', name: '{{ $field['field'] }}'},

              @endforeach

              {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            order: [[0, 'desc']],
            responsive: true,
            scrollX: true,
        });

        getInfoAllTransaksi();

        Promise.all([
            loadTableData(),
            getInfoAllTransaksi()
        ]).then(() => {
            console.log('All data loaded successfully');
        }).catch((error) => {
            console.error('Error loading data:', error);
        });

         $('#filter_bulan, #filter_tahun, #filter_status').on('change', function() {
            dataTable.ajax.reload();

        });

        $('#btn-filter').on('click', function() {
            dataTable.ajax.reload();
        });

        // Reset button click handler
        $('#btn-reset').on('click', function() {
            $('#filter_bulan').val('{{ $currentMonth }}');
            $('#filter_tahun').val('{{ $currentYear }}');
            $('#filter_status').val('all');
            dataTable.ajax.reload();

        });


        $('body').on('click', '.bayar', function () {
            var id = $(this).data('id');
            window.location.href = '{{ route('aksi-transaksi' . '.index') }}/' + id;
        });

         window.refreshData = function() {
            dataTable.ajax.reload(null, false);
            getInfoAllTransaksi();
        };

    })

     function loadTableData() {
        return new Promise((resolve, reject) => {
            $('#laravel_datatable').DataTable().ajax.reload(function() {
                resolve();
            }, false);
        });
    }


    function getInfoAllTransaksi() {
        $.ajax({
            url: '{{ route('transaksi.getInfoAllTransaksi') }}',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
              console.log(response);
                $('#totalTagihanBelumLunasSemua').html('Rp. ' + response.totalSemuaTagihanBelumLunas.toLocaleString('id-ID') + ' / ' + response.jumlahTagihanBelumLunas + ' Tagihan');
                $('#totalTagihanLunasSemua').html('Rp. ' + response.totalSemuaTagihanLunas.toLocaleString('id-ID') + ' / ' + response.jumlahTagihanLunas + ' Tagihan');
                $('#totalTagihanBelumLunasBulanIni').html('Rp. ' + response.totalTagihanBelumLunasBulanIni.toLocaleString('id-ID') + ' / ' + response.jumlahTagihanBelumLunasBulanIni + ' Tagihan');
                $('#totalTagihanLunasBulanIni').html('Rp. ' + response.totalTagihanLunasBulanIni.toLocaleString('id-ID') + ' / ' + response.jumlahTagihanLunasBulanIni + ' Tagihan');
            },
            error: function(xhr) {
                console.error('AJAX Error: ' + xhr.status + xhr.statusText);
            }
        });

        $('#laravel_datatable').DataTable().ajax.reload(null, false);

    }
</script>

@endsection
