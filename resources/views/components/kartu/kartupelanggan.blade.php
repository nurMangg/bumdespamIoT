<style>
    .card1 {
        width: 400px;
        height: 250px;
        background: linear-gradient(90deg, #005f87, #003f5c);
        border-radius: 15px;
        display: flex;
        color: #fff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        overflow: hidden;
    }
    .card1 .left {
        padding: 20px;
        width: 70%;
        display: flex;
        flex-direction: column;
        justify-content: space-evenly;
    }
    .card1 .left .company {
        background-color: #2fb54a;
        color: #fff;
        padding: 5px 10px;
        border-radius: 5px;
        display: inline-block;
        font-size: 14px;
        font-weight: bold;
    }
    .card1 .left .info {
        font-size: 18px;
        margin-top: 5px;
    }
    .card1 .left .info span {
        display: block;
        margin-top: 2px;
        font-size: 14px;
    }
    .card1 .right {
        width: 30%;
        background-color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 10px;
        color: #333;
    }
    .card1 .right .qr {
        width: 80px;
        height: 80px;
        background-color: #eee;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 10px;
        margin-bottom: 10px;
    }
    .card1 .right .scan-text {
        font-size: 12px;
        font-weight: bold;
    }
</style>

<div class="modal modal-blur fade" id="ajaxModelKartu" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="card1">
                <!-- Left section -->
                <div class="left">
                    <div class="company">BBM BUILDERS</div>
                    <div class="info">
                        <strong id="namaKartu"></strong><br>
                        <span id="kodeKartu"></span><br>
                        <span id="alamatKartu"></span>
                    </div>
                </div>
                <!-- Right section -->
                <div class="right">
                    <div class="qr">
                        
                    </div>
                    <div class="scan-text">SCAN ME</div>
                </div>
            </div>
        </div>
    </div>
</div>
