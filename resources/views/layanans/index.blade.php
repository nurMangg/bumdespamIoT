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
                <button type="button" class="btn btn-tool" onclick="getInfoAllTagihan()" title="Refresh">
                  <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <div class="row">
                <div class="col-12 col-sm-6 col-md-4">
                  <div class="info-box">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-file-invoice"></i></span>
      
                    <div class="info-box-content">
                      <span class="info-box-text" id="BulanLalu">Jumlah Tagihan Bulan Lalu</span>
                      <span class="info-box-number" id="jumlahTagihanBulanLalu">
                        <small></small>
                      </span>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
                  <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-12 col-sm-6 col-md-4">
                  <div class="info-box mb-3">
                    <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-file-invoice"></i></span>
      
                    <div class="info-box-content">
                      <span class="info-box-text" id="BulanIni">Jumlah Tagihan Bulan Ini</span>
                      <span class="info-box-number" id="jumlahTagihanBulanIni"></span>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
                  <!-- /.info-box -->
                </div>
                <!-- /.col -->
                <div class="col-12 col-sm-6 col-md-4">
                  <div class="info-box mb-3">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-file-invoice"></i></span>
      
                    <div class="info-box-content">
                      <span class="info-box-text" id="BulanDepan">Jumlah Tagihan Bulan Depan</span>
                      <span class="info-box-number" id="jumlahTagihanBulanDepan"></span>
                    </div>
                    <!-- /.info-box-content -->
                  </div>
                  <!-- /.info-box -->
                </div>
                <!-- /.col -->
      
                <!-- fix for small devices only -->
                <div class="clearfix hidden-md-up"></div>
        
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
                <table class="table" id="laravel_datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            @foreach ($form as $item)
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


        $('#laravel_datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route($route . '.index') }}',
                type: 'GET',
                dataType: 'json',
                error: function (xhr, status, error) {
                    console.error('AJAX Error: ' + status + error);
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
              @foreach ($form as $field)
                  {data: '{{ $field['field'] }}', name: '{{ $field['field'] }}'},
              @endforeach
              
              {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            order: [[0, 'desc']],
            responsive: true,
            scrollX: true,
        });

        $('body').on('click', '.edit', function () {
            var id = $(this).data('id');
            window.location.href = '{{ route('aksi-tagihan' . '.index') }}/' + id;
        });

        getInfoAllTagihan();

    })

    function getInfoAllTagihan() {
        $.ajax({
            url: '{{ route('tagihan.getInfoTagihan') }}',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
              console.log(response);
                $('#BulanLalu').html('Jumlah Tagihan Bulan ' + response.bulanLalu);
                $('#BulanIni').html('Jumlah Tagihan Bulan ' + response.bulanIni);
                $('#BulanDepan').html('Jumlah Tagihan Bulan ' + response.bulanDepan);

                $('#jumlahTagihanBulanLalu').html(response.jumlahInputTagihanBulanLalu + ' / ' + response.jumlahInputTagihan + ' Tagihan');
                $('#jumlahTagihanBulanIni').html(response.jumlahInputTagihanBulanIni + ' / ' + response.jumlahInputTagihan + ' Tagihan');
                $('#jumlahTagihanBulanDepan').html(response.jumlahInputTagihanBulanDepan + ' / ' + response.jumlahInputTagihan + ' Tagihan');

            },
            error: function(xhr) {
                console.error('AJAX Error: ' + xhr.status + xhr.statusText);
            }
        });

        $('#laravel_datatable').DataTable().ajax.reload(null, false);

    }    
</script>
    
@endsection
