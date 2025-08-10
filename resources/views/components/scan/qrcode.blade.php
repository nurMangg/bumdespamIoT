<div class="modal modal-blur fade" id="ajaxScanner" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            {{-- <div class="modal-header">
                <h5 class="modal-title" id="modelHeading">New {{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
            </div> --}}
            <div id="qr-reader" style="width: 100%;"></div>
            <div id="qr-reader-results"></div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    $(document).ready(function () {
        let html5QrcodeScanner;

        // Fungsi untuk memulai scanner
        function startScanner() {
            html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", { fps: 10, qrbox: 300 });
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        }

        // Fungsi untuk menghentikan scanner
        function stopScanner() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear().then(() => {
                    console.log("Scanner stopped.");
                }).catch(error => {
                    console.error("Failed to stop scanner:", error);
                });
            }
        }

        // Callback saat QR berhasil discan
        function onScanSuccess(decodedText, decodedResult) {
            $.ajax({
                url: "{{ route('input-tagihan.scanqrcode') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    idPelanggan: decodedText
                },
                success: function (data) {
                    $('#cekPendaftaran').trigger("reset");
                    $('#ajaxModel').modal('hide');
                    $('#ajaxScanner').modal('hide');
                    $('#cariPendaftaran').html('Save Changes');
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
                    toastr.success('QR Code Scan Sukses!');

                    // Stop the QR code scanner
                    stopScanner();
                },
                error: function (xhr) {
                    $('#cariPendaftaran').html('Save Changes');
                    $('#ajaxModel').modal('hide');
                    $('#ajaxScanner').modal('hide');

                    if (xhr.status === 422) {
                        toastr.success('QR Code Scan Error!');
                        
                    } else {
                        toastr.success('QR Code Scan Error!');
                       
                    }

                    // Stop the QR code scanner on error
                    stopScanner();
                }
            });
        }

        // Callback saat scan QR gagal
        function onScanFailure(error) {
            console.warn(`QR Code scan failed. Error: ${error}`);
        }

        // Event listener untuk modal
        $('#ajaxScanner').on('shown.bs.modal', function () {
            startScanner(); // Mulai scanner saat modal dibuka
        });

        $('#ajaxScanner').on('hidden.bs.modal', function () {
            stopScanner(); // Hentikan scanner saat modal ditutup
        });
    });
</script>

