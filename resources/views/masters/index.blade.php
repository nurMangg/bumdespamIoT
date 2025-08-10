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
                @if (request()->is('master/pelanggan') || request()->is('master/pelanggan/*'))
                <div class="col-md-12">
                    <div class="alert alert-primary alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                      <h5><i class="icon fas fa-exclamation-triangle"></i> Informasi!</h5>
                      <ul>
                        <li>Agar Notifikasi muncul di Whatsapp pelanggan, pastikan data pelanggan mencantumkan nomor telepon yang terdaftar</li>
                        <li>Untuk penulisan nomor telepon <b>WAJIB</b> diawali dengan 62 tidak boleh menggunakan 0</li>
                        <li>Untuk mencetak kartu nama klik tautan disini <a href="{{ route('pelanggan.cetakKartu')}}" target="_blank" class="btn btn-success" style="text-decoration: none">Cetak Kartu</a></li>
                      </ul>
                    </div>
                  </div>
                @endif
                <div class="col-md-6">
                </div>
                <div class="col-md-6">
                    <div class="float-sm-right">
                        <button id="create" class="btn" style="background-color: #608BC1; color: #ffffff">
                            Tambah {{ $title ?? env('APP_NAME') }}
                        </button>
                    </div>
                    
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

<x-form.modal :form="$form" :title="$title ?? env('APP_NAME')" />
<x-kartu.kartupelanggan />

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

        $('#create').click(function () {
            $('#saveBtn').val("create-action");
            $('#id').val('');
            $('#addForm').trigger("reset");
            $('#modelHeading').html("Tambah Baru {{ $title ?? env('APP_NAME') }}");
            $('#ajaxModel').modal('show');
        });

        $('body').on('click', '.edit', function () {
            var id = $(this).data('id');
            $.get('{{ route($route . '.index') }}/' + id + '/edit', function (data) {
                $('#modelHeading').html("Edit {{ $title ?? env('APP_NAME') }}");
                $('#saveBtn').val("edit-action");
                $('#ajaxModel').modal('show');
                $('#id').val(id);
                @foreach ($form as $field)
                    @if ($field['type'] === 'checkbox')
                        $('input[name="{{ $field['field'] }}[]"]').each(function() {
                            $(this).prop('checked', data.{{ $field['field'] }}.includes($(this).val()));
                        });
                    @else
                        $('#{{ $field['field'] }}').val(data.{{ $field['field'] }});
                    @endif
                @endforeach
            })
        });

        $('body').on('click', '.view', function () {
            var id = $(this).data('id');
            window.open("{{ route('pelanggan.viewKartu', ['pelanggan' => ':id']) }}".replace(':id', id), '_blank');

        });


        $('#saveBtn').click(function (e) {
            e.preventDefault();
            $('#saveBtn').html('Mengirim..');

            // Reset error messages
            @foreach ($form as $field)
                @if ($field['required'] ?? false)
                    $('#{{ $field['field'] }}Error').text('');
                @endif
            @endforeach

            // Validate non-empty fields
            var isValid = true;
            @foreach ($form as $field)
                @if ($field['required'] ?? false)
                    @if ($field['type'] === 'checkbox')
                        if (!$('input[name="{{ $field['field'] }}[]"]:checked').length) {
                            $('#{{ $field['field'] }}Error').text('This field is required.');
                            isValid = false;
                        }
                    @else
                        if (!$('#{{ $field['field'] }}').val()) {
                            $('#{{ $field['field'] }}Error').text('This field is required.');
                            isValid = false;
                        }
                    @endif
                @endif
            @endforeach

            if (!isValid) {
                $('#saveBtn').html('Simpan Data');
                return;
            }

            var actionType = $(this).val();
            console.log(actionType);
            var url = actionType === "create-action" ? "{{ route($route . '.store') }}" :
                "{{ route($route . '.index') }}/" + $('#id').val();

            // Tentukan jenis permintaan (POST atau PUT)
            var requestType = actionType === "create-action" ? "POST" : "PUT";

            $.ajax({
                data: $('#addForm').serialize(),
                url: url,
                type: requestType,
                dataType: 'json',
                success: function (data) {
                    $('#addForm').trigger("reset");
                    $('#ajaxModel').modal('hide');
                    $('#laravel_datatable').DataTable().ajax.reload();
                    $('#saveBtn').html('Simpan Data');

                    var message = actionType === "create-action" ? "Data Berhasil ditambahkan!" : "Data berhasil diperbarui!";
                    toastr.success(message);
                },
                error: function (xhr) {
                    $('#saveBtn').html('Simpan Data');

                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        @foreach ($form as $field)
                            @if ($field['required'] ?? false)
                                if (errors.{{ $field['field'] }}) {
                                    $('#{{ $field['field'] }}Error').text(errors.{{ $field['field'] }});
                                }
                            @endif
                        @endforeach
                        toastr.error('Error');
                    } else {
                      toastr.error('Terjadi kesalahan saat menyimpan data.');
                    }
                }
            });
        });

        $('body').on('click', '.delete', function () {
            var id = $(this).data('id');
            var url = "{{ route($route . '.index') }}/" + id;
            Swal.fire({
                title: 'Anda yakin?',
                text: "Apakah Anda yakin ingin menghapus data ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: url,
                        success: function (data) {
                            $('#laravel_datatable').DataTable().ajax.reload();
                            toastr.success('Data berhasil dihapus!');
                        },
                        error: function (data) {
                            console.log('Error:', data);
                            toastr.error('Terjadi kesalahan saat menghapus data.');
                        }
                    });
                }
            });
        });
            
    })
</script>
    
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
@endpush
@endsection
