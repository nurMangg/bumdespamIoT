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
                error: function (xhr, status, error) {
                    console.error('AJAX Error: ' + status + error);
                }
            },
            columns: [
              {
                data: 'id',
                name: 'no',
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
              },
              @foreach ($grid as $field)
                  {data: '{{ $field['field'] }}', name: '{{ $field['field'] }}'},
              @endforeach
              
            ],
            responsive: true,
            scrollX: true,
        });

    })
</script>
    
@endsection
