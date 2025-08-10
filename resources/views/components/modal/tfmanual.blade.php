<div class="modal modal-blur fade" id="uploadModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modelHeading">Form Upload Bukti Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="TFManualForm" name="TFManualForm" class="form-horizontal">
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-warning alert-dismissible">
                              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                              <h5><i class="icon fas fa-exclamation-triangle"></i> Warning!</h5>
                              <ul>
                                <li>Pastikan jumlah transfer sesuai dengan tagihan agar tidak terjadi kesalahan verifikasi.</li>
                                <li>Gunakan nomor rekening/e-wallet tujuan yang benar sesuai dengan yang diberikan pada sistem.</li>
                                <li>Simpan bukti transfer sebelum keluar dari aplikasi bank/e-wallet.</li>
                                <li>Format gambar harus sesuai (JPG, PNG, JPEG) dan tidak melebihi ukuran 2MB.</li>
                                <li>Unggah bukti pembayaran dengan kualitas yang jelas, tidak buram atau terpotong.</li>
                                <li>Proses verifikasi membutuhkan waktu hingga 1x24 jam, harap menunggu konfirmasi.</li>
                                <li>Kesalahan dalam transfer bukan tanggung jawab admin, pastikan data yang dimasukkan benar.</li>
                                <li>Jika ada kendala, segera hubungi customer support sebelum melakukan tindakan lebih lanjut.</li>
                              </ul>
                            </div>
                          </div>
                          <div class="col-12">
                            <div class="alert alert-success alert-dismissible">
                              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                              <h5><i class="icon fas fa-exclamation-triangle"></i> Rekening Pembayaran!</h5>
                              <ol>
                                <li>BNI: 1234567890 a/n Nama Lengkap</li>
                                <li>BCA: 0987654321 a/n Nama Lengkap</li>
                                <li>Mandiri: 1122334455 a/n Nama Lengkap</li>
                              </ol>
                            </div>
                          </div>
                        <input type="hidden" name="tagihanIdTFManual" id="tagihanIdTFManual">
                        <div class="form-group col-md-12">
                            <label for="uploadFile" class="control-label">
                                Upload Bukti Pembayaran
                                <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control" id="uploadFile" name="uploadFile" required>
                            <span class="text-danger" id="uploadFileError"></span>
                        </div>
                    </div>
                    <div class="col-sm-12 mt-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-blue" id="btnUploadFile" value="create">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
