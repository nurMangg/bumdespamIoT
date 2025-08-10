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
            
        </div>
      <div class="container">
        <div class="row">
          @if (Auth::user()->userRoleId != App\Models\Roles::where('roleName', 'pelanggan')->first()->roleId)
          <div class="col-md-12">
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <h5><i class="icon fas fa-exclamation-triangle"></i> Peringatan!</h5>
              <ul>
                <li>Jangan Gunakan <b>Export to PDF</b> Jika tidak menggunakan <b>Filter Data</b>, menghindari error yang muncul</li>
                <li><b>Export to PDF</b> Tidak mendukung data banyak!.</li>
                <li>Jika ingin meng-Export semua data, gunakan <b>Export to Excel</b></li>
            
              </ul>
            </div>
          </div>
          <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                  <h3 class="card-title">{{ __('Filter Data') }}</h3>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" onclick="location.reload()" title="Refresh">
                      <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  
                  <form>
                    <div class="row">
                    @foreach ($form as $field)
                    <div class="form-group mb-3 col-md-{{ $field['width'] ?? 12 }}">
                      <label for="{{ $field['field'] }}" class="control-label">
                          {{ $field['label'] }}
                      </label>
                      @if ($field['type'] === 'checkbox')
                      {{-- <div class="row ms-3 mt-2"> --}}
                          @foreach ($field['options'] as $value => $label)
                          
                              <div class="form-check">
                                  <input class="form-check-input" type="checkbox" id="{{ $field['field'] }}-{{ $value }}" name="{{ $field['field'] }}[]" value="{{ $value }}" {{ in_array($value, old($field['field'], [])) ? 'checked' : '' }}>
                                  <label class="form-check-label" for="{{ $field['field'] }}-{{ $value }}">
                                      {{ $label }}
                                  </label>
                              </div>
                          
                          @endforeach
                      {{-- </div> --}}
                      @else
                        <select class="form-control" id="{{ $field['field'] }}" name="{{ $field['field'] }}" {{ $field['required'] ?? false ? 'required' : '' }}>
                          <option value="" selected>{{ $field['placeholder'] }}</option>
                          @foreach ($field['options'] as $value => $label)
                              <option value="{{ $value }}">{{ $label }}</option>
                          @endforeach
                        </select>
                      @endif
                      
                    </div>

                    @endforeach
                  </div>
                    <div class="col-md-12 text-right">
                      <button type="submit" id="excelButton" class="btn btn-success">Export to Excel</button>
                      <button type="submit" id="pdfButton" class="btn btn-danger">Export to PDF</button>
                      <button type="submit" id="filterBtn" class="btn btn-primary">Preview Filter</button>
                    </div>
                  </form>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
        </div>
          @endif
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">{{ $title ?? env('APP_NAME') }}</h3>
                
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table" id="laravel_datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            @foreach ($grid as $item)
                                <th>{{ $item['label'] }}</th>
                            @endforeach
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

        $('#filterBtn').click(function(e) {
            e.preventDefault();
            $('#laravel_datatable').DataTable().ajax.reload();
        });


        $('#laravel_datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route($route . '.index') }}',
                type: 'GET',
                dataType: 'json',
                data: function (d) {
                    d.filter = {};
                    @foreach ($form as $field)
                      @if ($field['type'] === 'checkbox')
                          d.filter.{{ $field['field'] }} = $('input[name="{{ $field['field'] }}[]"]:checked').map(function() {
                              return this.value;
                          }).get();
                      @else
                          d.filter.{{ $field['field'] }} = $('#{{ $field['field'] }}').val();
                      @endif
                    @endforeach
                },
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
              @foreach ($grid as $field)
                  {data: '{{ $field['field'] }}', name: '{{ $field['field'] }}'},
              @endforeach
              
            ],
            order: [[0, 'desc']],
            responsive: true,
            scrollX: true,
        });

        $('#pdfButton').click(function (event) {
            // Mencegah form submission default
            event.preventDefault();

            // Menyembunyikan tombol PDF
            $('#pdfButton').text('Exporting...');

            // Kirim request AJAX
            $.ajax({
                url: '{{ route($route . '.exportPdf') }}',
                type: 'POST',
                data: {
                    filter: (function() {
                        var filterData = {};
                        @foreach ($form as $field)
                            @if ($field['type'] === 'checkbox') 
                                filterData['{{ $field['field'] }}'] = $('input[name="{{ $field['field'] }}[]"]:checked').map(function() {
                                    return this.value;
                                }).get();
                            
                            @else 
                                filterData['{{ $field['field'] }}'] = $('#{{ $field['field'] }}').val();
                            @endif
                        @endforeach
                        return filterData;
                    })()
                },
                success: function (response) {
                    $('#pdfButton').text('Export to PDF');

                    if (response.status === 'success') {
                        // Redirect ke URL file PDF untuk diunduh
                        window.open(response.url, '_blank');
                    }
                },
                error: function (xhr, status, error) {
                    $('#pdfButton').text('Export to PDF');

                    alert('Terjadi kesalahan: ' + error);
                }
            });
        });


        $('#excelButton').click(function (event) {
    event.preventDefault();

    // Ubah teks tombol untuk menunjukkan proses
    $('#excelButton').text('Exporting...');

    // Ambil data filter
    var filterData = {};
    @foreach ($form as $field)
        @if ($field['type'] === 'checkbox') 
            filterData['{{ $field['field'] }}'] = $('input[name="{{ $field['field'] }}[]"]:checked').map(function() {
                return this.value;
            }).get();
        @else 
            filterData['{{ $field['field'] }}'] = $('#{{ $field['field'] }}').val();
        @endif
    @endforeach

    // Konversi filter menjadi query string
    var queryString = $.param({ filter: filterData });

    // Redirect langsung untuk mengunduh file
    window.location.href = '{{ route($route . '.exportExcel') }}?' + queryString;

    // Kembalikan teks tombol setelah beberapa detik
    setTimeout(function() {
        $('#excelButton').text('Export to Excel');
    }, 3000);
});

    })
</script>
    
@endsection
