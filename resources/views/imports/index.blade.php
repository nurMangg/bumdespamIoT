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
                      <div class="mb-5">
                        <h5>Contoh Format Data</h5>
                        <img src="{{ asset($image ?? '' ) }}" alt="" width="100%">
                        <small style="color:red">*</small> Pastikan format data sesuai dengan contoh
                        <br>
                        <small style="color:red">*</small> Pastikan header data sesuai dengan contoh
                      </div>
                      
                      <form id="importData" name="importData" enctype="multipart/form-data">
                        <div class="row">
                        @foreach ($form as $field)
                        <div class="mb-3 col-md-{{ $field['width'] ?? 12 }}">
                          <label for="{{ $field['field'] }}" class="control-label">
                              {{ $field['label'] }}
                          </label>
          
                          <input type="file" class="form-control" id="{{ $field['field'] }}" name="{{ $field['field'] }}" {{ $field['required'] ?? false ? 'required' : '' }} {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                          <span class="text-danger" id="{{ $field['field'] }}Error"></span>
                        </div>

                        @endforeach
                      </div>
                        <div class="col-md-12 text-right">
                          <button type="submit" id="importBtn" class="btn btn-primary">{{ $title ?? env('APP_NAME') }}</button>
                        </div>
                      </form>
                    </div>
                    <!-- /.card-body -->
                  </div>
                  <!-- /.card -->
            </div>
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

        $('#importBtn').click(function (e) {
            e.preventDefault();
            var formData = new FormData($('#importData')[0]);
            $('#importBtn').html('Mengirim..');

            @foreach ($form as $field)
                @if ($field['required'] ?? false)
                    $('#{{ $field['field'] }}Error').text('');
                @endif
            @endforeach

            $.ajax({
                type: 'POST',
                url: "{{ route($route . '.store') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    $('#importData').trigger("reset");
                    $('#importBtn').html('{{ $title ?? env('APP_NAME') }}');
                    toastr.success('Data berhasil diimport!');
                },
                error: function (xhr) {
                  $('#importBtn').html('{{ $title ?? env('APP_NAME') }}');

                  if (xhr.status === 422) { 
                      var errors = xhr.responseJSON.errors;
                      @foreach ($form as $field)
                          @if ($field['required'] ?? false)
                              if (errors.{{ $field['field'] }}) {
                                  $('#{{ $field['field'] }}Error').text(errors.{{ $field['field'] }});
                              }
                          @endif
                      @endforeach
                  } else {
                    toastr.error('Terjadi kesalahan saat menyimpan data.');
                  }
                }
            });
        });

    })
</script>
    
@endsection
