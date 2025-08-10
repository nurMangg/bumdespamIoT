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
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Filter Data</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="filter_month">Bulan:</label>
                                    <select id="filter_month" class="form-control">
                                        <option value="">Semua Bulan</option>
                                        <option value="1" {{ date('n') == 1 ? 'selected' : '' }}>Januari</option>
                                        <option value="2" {{ date('n') == 2 ? 'selected' : '' }}>Februari</option>
                                        <option value="3" {{ date('n') == 3 ? 'selected' : '' }}>Maret</option>
                                        <option value="4" {{ date('n') == 4 ? 'selected' : '' }}>April</option>
                                        <option value="5" {{ date('n') == 5 ? 'selected' : '' }}>Mei</option>
                                        <option value="6" {{ date('n') == 6 ? 'selected' : '' }}>Juni</option>
                                        <option value="7" {{ date('n') == 7 ? 'selected' : '' }}>Juli</option>
                                        <option value="8" {{ date('n') == 8 ? 'selected' : '' }}>Agustus</option>
                                        <option value="9" {{ date('n') == 9 ? 'selected' : '' }}>September</option>
                                        <option value="10" {{ date('n') == 10 ? 'selected' : '' }}>Oktober</option>
                                        <option value="11" {{ date('n') == 11 ? 'selected' : '' }}>November</option>
                                        <option value="12" {{ date('n') == 12 ? 'selected' : '' }}>Desember</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="filter_year">Tahun:</label>
                                    <select id="filter_year" class="form-control">
                                        <option value="">Semua Tahun</option>
                                        @for($year = date('Y'); $year >= date('Y') - 5; $year--)
                                            <option value="{{ $year }}" {{ date('Y') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
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

                <div class="mb-3">
                    <button type="button" class="btn btn-success" id="bulkUpdateBtn" style="display: none;">
                        <i class="fas fa-plus"></i> Input Tagihan
                    </button>
                    <button type="button" class="btn btn-info" id="selectAllBtn">
                        <i class="fas fa-check-square"></i> Pilih Semua
                    </button>
                    <button type="button" class="btn btn-secondary" id="deselectAllBtn">
                        <i class="fas fa-square"></i> Hapus Pilihan
                    </button>
                    <div class="float-right">
                        <span id="filterStatus" class="badge badge-info" style="display: none;">
                            Filter Aktif: <span id="filterText"></span>
                        </span>
                    </div>
                </div>
                <table class="table" id="laravel_datatable">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
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

    <div class="modal fade" id="ajaxModel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading">Riwayat Log Bluetooth</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="historyTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal & Waktu</th>
                                    <th>Volume (m³)</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

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
                data: function(d) {
                    d.filter_month = $('#filter_month').val();
                    d.filter_year = $('#filter_year').val();
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error: ' + status + error);
                }
            },
            columns: [
              {
                data: 'checkbox',
                name: 'checkbox',
                orderable: false,
                searchable: false
              },
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

        // Filter functionality
        $('#applyFilter').click(function() {
            $('#laravel_datatable').DataTable().ajax.reload();
        });

        $('#clearFilter').click(function() {
            $('#filter_month').val('');
            $('#filter_year').val('');
            $('#laravel_datatable').DataTable().ajax.reload();
            updateFilterStatus();
        });

        // Auto-apply filter when dropdowns change
        $('#filter_month, #filter_year').change(function() {
            $('#laravel_datatable').DataTable().ajax.reload();
            updateFilterStatus();

            // If modal is open, refresh the history table
            if ($('#ajaxModel').hasClass('show')) {
                $('#historyTable').DataTable().ajax.reload();
            }
        });

        function updateFilterStatus() {
            var month = $('#filter_month option:selected').text();
            var year = $('#filter_year').val();
            var filterText = '';

            if (month && month !== 'Semua Bulan' && year) {
                filterText = month + ' ' + year;
            } else if (year) {
                filterText = 'Tahun ' + year;
            } else if (month && month !== 'Semua Bulan') {
                filterText = month;
            }

            if (filterText) {
                $('#filterText').text(filterText);
                $('#filterStatus').show();
            } else {
                $('#filterStatus').hide();
            }
        }

        // Initialize filter status
        updateFilterStatus();


        $('body').on('click', '.edit', function () {
            var id = $(this).data('id');
            $.get('{{ route($route . '.index') }}/' + id + '/edit', function (data) {
                $('#modelHeading').html("Edit {{ $title ?? env('APP_NAME') }}");
                $('#saveBtn').val("edit-action");
                $('#ajaxModel').modal('show');
                $('#id').val(id);
                @foreach ($form as $field)
                    @if ($field['type'] === 'checkbox')
                        $('input[name="{{ $field['field'] }}["]').each(function() {
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

        // Checkbox functionality
        $('#selectAll').change(function() {
            $('.bulk-checkbox').prop('checked', $(this).prop('checked'));
            updateBulkUpdateButton();
        });

        $(document).on('change', '.bulk-checkbox', function() {
            updateBulkUpdateButton();
            updateSelectAllCheckbox();
        });

        $('#selectAllBtn').click(function() {
            $('.bulk-checkbox').prop('checked', true);
            $('#selectAll').prop('checked', true);
            updateBulkUpdateButton();
        });

        $('#deselectAllBtn').click(function() {
            $('.bulk-checkbox').prop('checked', false);
            $('#selectAll').prop('checked', false);
            updateBulkUpdateButton();
        });

        function updateBulkUpdateButton() {
            var checkedCount = $('.bulk-checkbox:checked').length;
            if (checkedCount > 0) {
                $('#bulkUpdateBtn').show().text('Input Tagihan (' + checkedCount + ' item)');
            } else {
                $('#bulkUpdateBtn').hide();
            }
        }

        function updateSelectAllCheckbox() {
            var totalCheckboxes = $('.bulk-checkbox').length;
            var checkedCheckboxes = $('.bulk-checkbox:checked').length;

            if (checkedCheckboxes === 0) {
                $('#selectAll').prop('indeterminate', false).prop('checked', false);
            } else if (checkedCheckboxes === totalCheckboxes) {
                $('#selectAll').prop('indeterminate', false).prop('checked', true);
            } else {
                $('#selectAll').prop('indeterminate', true);
            }
        }

        // Bulk Update functionality
        $('#bulkUpdateBtn').click(function() {
            var selectedIds = [];
            $('.bulk-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                toastr.warning('Pilih setidaknya satu item untuk diupdate');
                return;
            }

            // Show bulk update modal
            Swal.fire({
                title: 'Input Tagihan Massal',
                html: `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Data yang dipilih akan diinputkan ke tabel tagihan secara otomatis.
                        <br><br>
                        <strong>Jumlah data yang dipilih:</strong> ${selectedIds.length} pelanggan
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Pastikan data yang dipilih belum memiliki tagihan untuk bulan yang sama.
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Input Tagihan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#28a745',
                icon: 'question'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route($route . ".bulk-update") }}',
                        type: 'POST',
                        data: {
                            selected_ids: selectedIds,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                $('#laravel_datatable').DataTable().ajax.reload();
                                $('.bulk-checkbox').prop('checked', false);
                                $('#selectAll').prop('checked', false);
                                updateBulkUpdateButton();

                                // Show detailed results if there are errors
                                if (response.errors && response.errors.length > 0) {
                                    Swal.fire({
                                        title: 'Hasil Input Tagihan',
                                        html: `
                                            <div class="text-left">
                                                <p><strong>Berhasil diinput:</strong> ${response.inserted_count} tagihan</p>
                                                <p><strong>Error:</strong></p>
                                                <ul style="text-align: left; max-height: 200px; overflow-y: auto;">
                                                    ${response.errors.map(error => `<li>${error}</li>`).join('')}
                                                </ul>
                                            </div>
                                        `,
                                        icon: 'info',
                                        width: '600px'
                                    });
                                }
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Terjadi kesalahan saat melakukan input tagihan massal');
                        }
                    });
                }
            });
        });

    // Lihat button handler for showing modal and populating form
    $(document).on('click', '.lihat', function () {
        var id = $(this).data('id');

        // Show modal
        $('#ajaxModel').modal('show');

        // Get pelanggan info first
        $.get('{{ route($route . ".index") }}/' + id + '/edit', function (response) {
            if (response.success && response.pelanggan) {
                var filterMonth = $('#filter_month option:selected').text();
                var filterYear = $('#filter_year').val();
                var filterInfo = '';

                if (filterMonth && filterMonth !== 'Semua Bulan' && filterYear) {
                    filterInfo = ' - Filter: ' + filterMonth + ' ' + filterYear;
                } else if (filterYear) {
                    filterInfo = ' - Filter: Tahun ' + filterYear;
                }

                $('#modelHeading').html('Riwayat Log Bluetooth - ' + response.pelanggan.nama + ' (' + response.pelanggan.kode + ')' + filterInfo);
            }
        });

        // Initialize history datatable
        if ($.fn.DataTable.isDataTable('#historyTable')) {
            $('#historyTable').DataTable().destroy();
        }

        $('#historyTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '{{ route($route . ".index") }}/' + id + '/edit',
                type: 'GET',
                dataType: 'json',
                data: function(d) {
                    d.filter_month = $('#filter_month').val();
                    d.filter_year = $('#filter_year').val();
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
                {data: 'datetime', name: 'datetime'},
                {data: 'volume', name: 'volume'}
            ],
            order: [[1, 'desc']],
            responsive: true,
            pageLength: 10,

        });
    });

    })
</script>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
    <style>
        .modal-lg {
            max-width: 800px;
        }
        #historyTable {
            font-size: 14px;
        }
        #historyTable th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .modal-header {
            background-color: #f8f9fa;
            color: black;
        }
        .modal-header .close {
            color: black;
        }
    </style>
@endpush
@endsection
