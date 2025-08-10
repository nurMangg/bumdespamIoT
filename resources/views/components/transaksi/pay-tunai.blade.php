<div class="modal modal-blur fade" id="ajaxModel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modelHeading">Masukkan Pembayaran Tunai</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addForm" name="addForm" class="form-horizontal">
                    <div class="container">
                            <input type="hidden" name="id" id="id">
                    
                            <!-- Total Tagihan -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="totalTagihanTunai">Total Tagihan</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group flex-fill">
                                        <span class="input-group-text"><i class="fas fa-receipt"></i></span>
                                        <input type="text" class="form-control" id="totalTagihanTunai" name="totalTagihanTunai" readonly placeholder="Total Tagihan">
                                    </div>
                                </div>
                               
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="uangBayarTunai">Uang Bayar</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group flex-fill">
                                        <span class="input-group-text"><i class="fas fa-money-bill-wave"></i></span>
                                        <input type="text" class="form-control" id="uangBayarTunai" name="uangBayarTunai" placeholder="Uang Bayar">
                                    </div>
                                </div>
                               
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <label for="uangKembaliTunai">Uang Kembali</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group flex-fill">
                                        <span class="input-group-text"><i class="fas fa-money-bill-wave"></i></span>
                                        <input type="text" class="form-control" id="uangKembaliTunai" name="uangKembaliTunai" readonly placeholder="Uang Kembali">
                                    </div>
                                </div>
                               
                            </div>
                    
                            
                    </div>
                    
                    
                    
                    <div class="col-sm-12 mt-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-blue" id="saveBtn" value="create">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
