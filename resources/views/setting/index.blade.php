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
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Update Setting Web</h3>
                
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
                <form id="addForm" name="addForm" class="form-horizontal">
                    <div class="row">
                    <input type="hidden" name="id" id="id">
                    @foreach ($form as $field)
                        <div class="form-group col-md-{{ $field['width'] ?? 12 }}">
                            <label for="{{ $field['field'] }}" class="mb-0 control-label ">
                                {{ $field['label'] }}
                                @if ($field['required'] ?? false)
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            @if ($field['type'] === 'file')
                                <input type="file" class="form-control" id="{{ $field['field'] }}" name="{{ $field['field'] }}" value="{{ old($field['field'], $data->{$field['field']} ?? '') }}" {{ $field['required'] ?? false ? 'required' : '' }} {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                            @elseif ($field['type'] === 'password')
                                <input type="password" class="form-control" id="{{ $field['field'] }}" name="{{ $field['field'] }}" placeholder="{{ $field['placeholder'] ?? '' }}" value="{{ old($field['field'], $data->{$field['field']} ?? '') }}" {{ $field['required'] ?? false ? 'required' : '' }} {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                            @elseif ($field['type'] === 'email')
                                <input type="email" class="form-control" id="{{ $field['field'] }}" name="{{ $field['field'] }}" placeholder="{{ $field['placeholder'] ?? '' }}" value="{{ old($field['field'], $data->{$field['field']} ?? '') }}" {{ $field['required'] ?? false ? 'required' : '' }} {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                            @else
                                <input type="text" class="form-control" id="{{ $field['field'] }}" name="{{ $field['field'] }}" placeholder="{{ $field['placeholder'] ?? '' }}" value="{{ old($field['field'], $data->{$field['field']} ?? '') }}" {{ $field['required'] ?? false ? 'required' : '' }} {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                            @endif
                            <span class="text-danger" id="{{ $field['field'] }}Error"></span>
                        </div>
                    @endforeach
                    
                </div>
                    <div class="col-sm-12 mt-3 d-flex justify-content-end">
                        <button type="submit" class="btn {{ $color ?? 'btn-blue'}}" id="saveBtn" value="create">Simpan Data</button>
                    </div>
                </form>
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


        $('#saveBtn').click(function (e) {
            e.preventDefault();
            $('#saveBtn').html('Mengirim..');

            // Reset error messages
            @foreach ($form as $field)
                $('#{{ $field["field"] }}Error').text('');
            @endforeach

            // Ajax request
            $.ajax({
                data: new FormData($('#addForm')[0]),
                url: "{{ route($route . '.store') }}",
                type: "POST",
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (response) {
                    $('#ajaxModel').modal('hide');
                    $('#saveBtn').html('Simpan Data');

                    toastr.success(response.success || "Data berhasil disimpan!");
                },
                error: function (xhr) {
                    $('#saveBtn').html('Simpan Data');

                    if (xhr.status === 422) {
                        // Tampilkan error validasi
                        var errors = xhr.responseJSON.errors;
                        for (const field in errors) {
                            $(`#${field}Error`).text(errors[field][0]);
                        }

                        toastr.error("Terdapat error validasi. Silakan periksa form.");
                    } else {
                        // Error selain validasi
                        toastr.error(xhr.responseJSON.error || "Terjadi kesalahan sistem.");
                    }
                }
            });
        });

            


    })
</script>
    
@endsection
