<form method="post" id="form_report">
    <div class="row">
        <div class="col-md-12">
            <span class="font-weight-bold h4"><ion-icon name="bookmark-outline" style="color: red;"></ion-icon> Parameter</span>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-6 offset-3 col-12">
            <div class="row mb-3">
                <label for="laporan" class="control-label col-md-3 m-auto">Laporan</label>
                <div class="col-md-9">
                    <select name="laporan" id="laporan" class="form-control select2_global" data-placeholder="~ Pilih Laporan">
                        <option value="">~ Pilih Laporan</option>
                        <optgroup label="Jenis Laporan">
                            <option value="1">1) Penjualan</option>
                            <option value="2">2) Retur Penjualan</option>
                            <option value="3">3) Riwayat Stok Penjualan</option>
                        </optgroup>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label for="periode" class="control-label col-md-3 m-auto">Periode</label>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="date" name="dari" id="dari" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6">
                            <input type="date" name="sampai" id="sampai" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <label for="kode_gudang" class="control-label col-md-3 m-auto">Gudang</label>
                <div class="col-md-9">
                    <select name="kode_gudang" id="kode_gudang" class="form-control select2_gudang_int" data-placeholder="~ Pilih Gudang">
                        <option value="">~ Pilih Gudang</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 offset-3 col-12 text-center">
            <button class="btn btn-primary btn-sm" type="button" onclick="cetak(0)"><ion-icon name="desktop-outline"></ion-icon> LAYAR</button>
            <button class="btn btn-warning btn-sm" type="button" onclick="cetak(1)"><ion-icon name="document-text-outline"></ion-icon> PDF</button>
            <button class="btn btn-success btn-sm" type="button" onclick="cetak(2)"><ion-icon name="grid-outline"></ion-icon> EXCEL</button>
        </div>
    </div>
</form>

<script>
    const form = $('#form_report');

    var laporan = $('#laporan');
    var dari = $('#dari');
    var sampai = $('#sampai');
    var kode_gudang = $('#kode_gudang');

    // fungsi cetak
    function cetak(param) {
        if (kode_gudang.val() == '' || kode_gudang.val() == null) {
            return Swal.fire("Gudang", "Form sudah diisi?", "question");
        }

        var parameterString = `/${param}?laporan=${laporan.val()}&dari=${dari.val()}&sampai=${sampai.val()}&kode_gudang=${kode_gudang.val()}`;
        window.open(`${siteUrl}Transaksi/report_print_out${parameterString}`, '_blank');
    }
</script>