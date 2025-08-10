<div class="modal modal-blur fade" id="ajaxModel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modelHeading">Konfirmasi Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addForm" name="addForm" class="form-horizontal">
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
                          
                        <input type="hidden" name="id" id="id">
                        <div class="row">
                            <table style="border-collapse: collapse; ">
                                <tr>
                                    <td style="">Kode Tagihan</td>
                                    <td style="" id="kodeTagihan"></td>
                                </tr>
                                <tr>
                                    <td style="" >Nama Pelanggan</td>
                                    <td style="" id="namaPelanggan"></td>
                                </tr>
                                <tr>
                                    <td style="" >Tagihan Terbit</td>
                                    <td style="" id="tagihanTerbit"></td>
                                </tr>
                                <tr>
                                    <td style="" >Total Tagihan</td>
                                    <td style="" id="totalTagihan"></td>
                                </tr>
                                <tr>
                                    <td style="" >Tanggal Pembayaran</td>
                                    <td style="" id="tanggalPembayaran"></td>
                                </tr>
                                <tr>
                                    <td style="" >Bukti Pembayaran</td>
                                </tr>
                                <tr>
                                    <td style="" id=""></td>

                                </tr>
                            </table>
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
