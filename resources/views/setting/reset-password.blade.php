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
            <div class="row">
                <div class="col-md-6">
                </div>
                <div class="col-md-6">
                    
                </div>
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
                                <th>Action</th>
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

        $('body').on('click', '.reset-password', function () {
            var id = $(this).data('id');
            var url = "{{ route($route . '.resetPassword', ['id' => ':id']) }}".replace(':id', id);
            Swal.fire({
                title: 'Reset Password',
                text: "Apakah Anda yakin ingin mereset password pengguna ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, reset!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: url,
                        success: function (data) {
                            toastr.success('Password berhasil direset!');
                        },
                        error: function (data) {
                            console.log('Error:', data);
                            toastr.error('Terjadi kesalahan saat mereset password.');
                        }
                    });
                }
            });
        });

            
    })
</script>
    
@endsection
