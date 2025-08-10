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

    <!-- Modal cek content -->    
    

    <!-- Main content -->
    <section class="content">
        <div class="container mb-3">
            <div class="row">
                <div class="col-md-6">
                </div>
                <div class="col-md-6">
                    <div class="float-sm-right">
                        <button id="create" class="btn btn-blue" data-bs-toggle="modal" data-bs-target="#ajaxModel">
                            Cari Pelanggan
                        </button>
                    </div>
                    
                </div>
            </div>
        </div>
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <div class="card">
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
                <x-form.nomodalbutton :name="'addform1'" :form="$form" />
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <div class="card">
                <div class="card-header">
                  <h3 class="card-title">
                      Informasi Pemakaian
                  </h3>
                  <div class="card-tools">
                      <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                        <i class="fas fa-minus"></i>
                      </button>
                  </div>
                  
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                  <x-form.nomodalbutton :name="'addform2'" :form="$form3" />
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
          </div>
          <!-- /.col -->

          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Input Tagihan Baru</h3>
                
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <x-form.nomodal :form="$form2" :name="'addform3'" :title="$title ?? env('APP_NAME')" :color="'btn-out-blue'" />
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
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
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->

</div>

<div class="modal modal-blur fade" id="ajaxModel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modelHeading">Judul Modal</h5>
                <button type="button" class="btn" onclick="$('#ajaxModel').modal('hide')">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
                <form id="cariTagihanForm" class="form-horizontal">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="kodePelangganInput" placeholder="Masukkan Kode Pelanggan" required>
                        <span class="text-danger" id="kodePelangganInputError"></span>
                    </div>
                    <div class="text-end d-flex justify-content-end gap-3 mt-3">
                        <button type="button" class="btn btn-primary mr-2" id="cariPelangganBarcode">Scan Barcode</button>
                        <button type="button" class="btn btn-primary cariPelanggan" id="cariPelanggan">Cari Pelanggan</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<x-scan.qrcode :form=$form :form2=$form2 :form3=$form3 />

<script type="text/javascript">
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#create').click(function () {
            $('#saveBtn').val("create-action");
            $('#id').val('');
            $('#addform1').trigger("reset");
            $('#kodePelangganInput').val('');
            $('#addform2').trigger("reset");
            $('#addform3').trigger("reset");

            $('#modelHeading').html("Cari Pelanggan");
            $('#ajaxModel').modal('show');
        });

        $('#cariPelangganBarcode').click(function () {
                    $('#ajaxModel').modal('hide');
                    $('#ajaxScanner').modal('show');
                });

        $('body').on('click', '.cariPelanggan', function () {
            var id = $('#kodePelangganInput').val();
            $.get('{{ route($route . '.index') }}/' + id , function (data) {
                if (data.status === 'success') {
                    $('#ajaxModel').modal('hide');
                    // $('#id').val(id);
                    $('#id').val(data.data.pelangganId);

                    
                    @foreach ($form as $field)
                        @if ($field['type'] === 'checkbox')
                            $('input[name="{{ $field['field'] }}[]"]').each(function() {
                                $(this).prop('checked', data.data.{{ $field['field'] }}.includes($(this).val()));
                            });
                        @else
                            $('#{{ $field['field'] }}').val(data.data.{{ $field['field'] }});
                        @endif
                    @endforeach
                    @foreach ($form2 as $field)
                        @if ($field['type'] === 'checkbox')
                            $('input[name="{{ $field['field'] }}[]"]').each(function() {
                                $(this).prop('checked', data.data.{{ $field['field'] }}.includes($(this).val()));
                            });
                        @else
                            $('#{{ $field['field'] }}').val(data.data.{{ $field['field'] }});
                        @endif
                    @endforeach
                    @foreach ($form3 as $field)
                        @if ($field['type'] === 'checkbox')
                            $('input[name="{{ $field['field'] }}[]"]').each(function() {
                                $(this).prop('checked', data.data.{{ $field['field'] }}.includes($(this).val()));
                            });
                        @else
                            $('#{{ $field['field'] }}').val(data.data.{{ $field['field'] }});
                        @endif
                    @endforeach
                    toastr.success('Data berhasil diambil!');
                } else {

                    toastr.error('Data Tidak Ditemukan');
                }
            })
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
                data: {
                    meterAkhir: $('#tagihanMeterAkhir').val(),
                    catatanTagihan: $('#tagihanKeterangan').val(),
                    id: $('#id').val(),
                },
                url: url,
                type: requestType,
                dataType: 'json',
                success: function (data) {
                    $('#addform1').trigger("reset");
                    $('#addform2').trigger("reset");
                    $('#addform3').trigger("reset");
                    $('#id').val('');
                    $('#ajaxModel').modal('hide');
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
                        $('#addform1').trigger("reset");
                    $('#addform2').trigger("reset");
                    $('#addform3').trigger("reset");
                    $('#id').val('');
                    $('#ajaxModel').modal('hide');
                    $('#saveBtn').html('Simpan Data');
                    } else {
                      toastr.error('Terjadi kesalahan saat menyimpan data.');
                      $('#addform1').trigger("reset");
                    $('#addform2').trigger("reset");
                    $('#addform3').trigger("reset");
                    $('#id').val('');
                    $('#ajaxModel').modal('hide');
                    $('#saveBtn').html('Simpan Data');
                    }
                }
            });
        });
            


    })
</script>
    
@endsection
