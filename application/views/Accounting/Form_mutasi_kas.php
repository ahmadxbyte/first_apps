<?php
if ($web->ct_theme == 1) {
    $style = 'style="background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);"';
    $style2 = 'style="backdrop-filter: blur(10px);"';
    $style3 = 'style="background: transparent;"';
    $style_modal = 'style="background-color: rgba(255, 255, 255, 0.4); -webkit-backdrop-filter: blur(10px); backdrop-filter: blur(4px);"';
} else if ($web->ct_theme == 2) {
    $style = 'style="background: rgba(30, 30, 30, 0.8); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); color: white !important;"';
    $style2 = 'style="backdrop-filter: blur(10px);"';
    $style3 = 'style="background: transparent;"';
    $style_modal = 'style="background-color: rgba(30, 30, 30, 0.9); -webkit-backdrop-filter: blur(30px); backdrop-filter: blur(5px); color: white !important;"';
} else {
    $style = '';
    $style2 = '';
    $style3 = '';
    $style_modal = '';
}
?>

<form method="post" id="form_mutasi">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Formulir</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="invoice" class="control-label">Invoice</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" placeholder="Otomatis" id="invoice" name="invoice" value="<?= (!empty($data_mutasi) ? $data_mutasi->invoice : '') ?>" readonly>
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <ion-icon name="id-card-outline"></ion-icon>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="">Tgl/Jam Mutasi</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group mb-3">
                                                <input type="date" class="form-control" placeholder="Tgl Mutasi" id="tgl_mutasi" name="tgl_mutasi" value="<?= (!empty($data_mutasi) ? date('Y-m-d', strtotime($data_mutasi->tgl_mutasi)) : date('Y-m-d')) ?>" readonly>
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <ion-icon name="calendar-number-outline"></ion-icon>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group mb-3">
                                                <input type="time" class="form-control" placeholder="Jam Mutasi" id="jam_mutasi" name="jam_mutasi" value="<?= (!empty($data_mutasi) ? date('H:i:s', strtotime($data_mutasi->jam_mutasi)) : date('H:i:s')) ?>" readonly>
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <ion-icon name="time-outline"></ion-icon>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="invoice" class="control-label">Mutasi Dari <sup class="text-danger">**</sup></label>
                                    <div class="input-group mb-3">
                                        <select name="dari" id="dari" class="form-control select2_kas_bank" data-placeholder="~ Pilih Kas & Bank" onchange="get_saldo(this.value)">
                                            <?php if (!empty($data_mutasi)) : ?>
                                                <?php
                                                $bank = $this->M_global->getData('kas_bank', ['kode_kas_bank' => $data_mutasi->dari]);
                                                if ($bank) {
                                                    $dari = $bank->nama;
                                                } else {
                                                    $dari = '** KAS UTAMA **';
                                                }
                                                ?>
                                                <option value="<?= $data_mutasi->dari ?>"><?= $dari ?></option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="invoice" class="control-label">Saldo Kas Dari</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control text-right" placeholder="0" id="saldo_dari" name="saldo_dari" value="<?= (!empty($data_mutasi) ? number_format($data_mutasi->saldo_dari) : 0) ?>" readonly>
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <ion-icon name="id-card-outline"></ion-icon>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="invoice" class="control-label">Mutasi Menuju <sup class="text-danger">**</sup></label>
                                    <div class="input-group mb-3">
                                        <select name="menuju" id="menuju" class="form-control select2_kas_bank" data-placeholder="~ Pilih Kas & Bank" onchange="cek_kas(this.value)">
                                            <?php if (!empty($data_mutasi)) : ?>
                                                <?php
                                                $bank2 = $this->M_global->getData('kas_bank', ['kode_kas_bank' => $data_mutasi->menuju]);
                                                if ($bank2) {
                                                    $menuju = $bank2->nama;
                                                } else {
                                                    $menuju = '** KAS UTAMA **';
                                                }
                                                ?>
                                                <option value="<?= $data_mutasi->menuju ?>"><?= $menuju ?></option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="invoice" class="control-label">Saldo Kas Menuju <sup class="text-danger">**</sup></label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control text-right" placeholder="0" id="saldo_menuju" name="saldo_menuju" value="<?= (!empty($data_mutasi) ? number_format($data_mutasi->saldo_menuju) : 0) ?>" onchange="cek_saldo_dari(this.value)">
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <ion-icon name="id-card-outline"></ion-icon>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-danger" onclick="getUrl('Accounting/mutasi_kas')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                            <button type="button" class="btn btn-success float-right ml-2" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                            <?php if (!empty($data_mutasi_kas)) : ?>
                                <button type="button" class="btn btn-info float-right" onclick="getUrl('Accounting/form_mutasi_kas/0')" id="btnBaru"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Baru</button>
                            <?php else : ?>
                                <button type="button" class="btn btn-info float-right" onclick="reseting()" id="btnReset"><i class="fa-solid fa-arrows-rotate"></i>&nbsp;&nbsp;Reset</button>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    var invoice = $('#invoice')
    var dari = $('#dari')
    var saldo_dari = $('#saldo_dari')
    var menuju = $('#menuju')
    var saldo_menuju = $('#saldo_menuju')
    const btnSimpan = $('#btnSimpan')
    const form = $('#form_mutasi')

    cek_button()

    function cek_kas(param) {
        if (dari.val() == param) {
            menuju.html('<option value="">~ Pilih Kas & Bank</option>')
            return Swal.fire("Kas", "Sudah digunakan Mutasi Dari, silahkan dipilih yang lain", "info")
        }
    }

    function get_saldo(param) {
        if (param == '' || param == null) {
            saldo_dari.val(0)
            return cek_button()
        }

        $.ajax({
            url: siteUrl + 'Accounting/getSaldo/' + param,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if (result.status == 1) {
                    saldo_dari.val(formatRpNoId(result.saldo))
                } else {
                    saldo_dari.val(formatRpNoId(result.saldo))
                    Swal.fire("Kas " + result.nama, "Tidak ada saldo, silahkan dipilih yang lain", "info")
                }

                cek_button()
            },
            error: function(result) {
                cek_button()

                error_proccess()
            }
        })
    }

    function cek_saldo_dari(sld_menuju) {
        if (Number((saldo_dari.val()).replaceAll(',', '')) < Number((saldo_menuju.val()).replaceAll(',', ''))) {
            saldo_menuju.val(formatRpNoId(0))

            cek_button();
            return Swal.fire("Saldo Menuju", "Tidak boleh lebih besar dari Saldo dari, silahkan dicoba lagi", "info");
        }
        formatRp(sld_menuju, 'saldo_menuju');

    }

    function cek_button() {
        if (((saldo_menuju.val()).replaceAll(',', '') > (saldo_dari.val()).replaceAll(',', '')) || (((saldo_menuju.val()).replaceAll(',', '') == 0) && ((saldo_dari.val()).replaceAll(',', '') == 0))) {
            btnSimpan.attr('disabled', true);
        } else {
            btnSimpan.attr('disabled', false);
        }
    }

    function save() {
        btnSimpan.attr('disabled', true);

        if (invoice.val() == '' || invoice.val() == null) {
            var param = 1;
            var message = 'dibuat';
        } else {
            var param = 2;
            var message = 'diupdate';
        }

        $.ajax({
            url: siteUrl + 'Accounting/mutasi_proses/' + param,
            type: 'POST',
            dataType: 'JSON',
            data: form.serialize(),
            success: function(result) {
                btnSimpan.attr('disabled', false);

                if (result.status == 1) { // jika mendapatkan respon 1

                    Swal.fire("Mutasi Kas & Bank", "Berhasil " + message, "success").then(() => {
                        getUrl('Accounting/mutasi_kas');
                    });
                } else { // selain itu

                    Swal.fire("Mutasi Kas & Bank", "Gagal " + message + ", silahkan dicoba kembali", "info");
                }
            },
            error: function(result) {
                btnSimpan.attr('disabled', false);

                error_proccess()
            }
        })
    }
</script>