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

<form method="post" id="form_pembayaran">
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
                                    <label for="">Tgl/Jam Masuk <sup class="text-danger">**</sup></label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group mb-3">
                                                <input type="hidden" id="token" name="token" value="<?= (!empty($data_pembayaran) ? $data_pembayaran->token : '') ?>">
                                                <input type="date" class="form-control" placeholder="Tgl Masuk" id="tgl_masuk" name="tgl_masuk" value="<?= (!empty($data_pembayaran) ? date('Y-m-d', strtotime($data_pembayaran->tgl_masuk)) : date('Y-m-d')) ?>" readonly>
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <ion-icon name="calendar-number-outline"></ion-icon>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group mb-3">
                                                <input type="time" class="form-control" placeholder="Jam Masuk" id="jam_masuk" name="jam_masuk" value="<?= (!empty($data_pembayaran) ? date('H:i:s', strtotime($data_pembayaran->jam_masuk)) : date('H:i:s')) ?>" readonly>
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <ion-icon name="time-outline"></ion-icon>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="">Jenis Pembayaran <sup class="text-danger">**</sup></label>
                                    <input type="hidden" name="jenis_pembayaran" id="jenis_pembayaran" value="<?= (!empty($data_pembayaran) ? $data_pembayaran->jenis_pembayaran : 0) ?>">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <div class="row">
                                                <div class="col-md-6 col-6">
                                                    <input type="checkbox" id="cek_cash" name="cek_cash" class="form-control" onclick="cek_cc(0)" <?= (!empty($data_pembayaran) ? (($data_pembayaran->jenis_pembayaran == 0) ? 'checked' : '') : '') ?> <?= (($param2) ? 'disabled' : '') ?>>
                                                </div>
                                                <div class="col-md-6 col-6">CASH</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="row">
                                                <div class="col-md-6 col-6">
                                                    <input type="checkbox" id="cek_card" name="cek_card" class="form-control" onclick="cek_cc(1)" <?= (!empty($data_pembayaran) ? (($data_pembayaran->jenis_pembayaran == 1) ? 'checked' : '') : '') ?> <?= (($param2) ? 'disabled' : '') ?>>
                                                </div>
                                                <div class="col-md-6 col-6">CARD</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="row">
                                                <div class="col-md-6 col-6">
                                                    <input type="checkbox" id="cek_cash_card" name="cek_cash_card" class="form-control" onclick="cek_cc(2)" <?= (!empty($data_pembayaran) ? (($data_pembayaran->jenis_pembayaran == 2) ? 'checked' : '') : '') ?> <?= (($param2) ? 'disabled' : '') ?>>
                                                </div>
                                                <div class="col-md-6 col-6">CASH + CARD</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Pembayaran</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="" class="text-danger font-weight-bold">Total Pembayaran</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control text-right text-primary font-weight-bold" placeholder="Pembayaran Total" id="total" name="total" value="<?= (!empty($data_pembayaran) ? number_format($data_pembayaran->total) : '0') ?>" readonly>
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <ion-icon name="wallet-outline"></ion-icon>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="fortableCash">
                                    <label for="">Cash</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control text-right" placeholder="Pembayaran Cash" id="cash" name="cash" value="<?= (!empty($data_pembayaran) ? number_format($data_pembayaran->cash) : '0') ?>" onchange="hitung_bayar()">
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <ion-icon name="cash-outline"></ion-icon>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="fortableCard">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table shadow-sm table-hover table-bordered" id="tableBayarCard" width="100%" style="border-radius: 10px;">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th style="width: 5%;" style="border-radius: 10px 0px 0px 0px;">Hapus</th>
                                                            <th style="width: 15%;">Bank</th>
                                                            <th style="width: 10%;">Tipe</th>
                                                            <th style="width: 20%;">No. Kartu</th>
                                                            <th style="width: 20%;">Approval</th>
                                                            <th style="width: 20%;" style="border-radius: 0px 10px 0px 0px;">Pembayaran</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="bodyBayarCard">
                                                        <?php if (!empty($bayar_detail)) : ?>
                                                            <?php $no = 1;
                                                            foreach ($bayar_detail as $bd) : ?>
                                                                <tr id="rowCard<?= $no ?>">
                                                                    <td>
                                                                        <button type="button" class="btn btn-dark" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tooltip on bottom" title="Hapus" onclick="hapusBaris(<?= $no ?>)"><i class="fa-solid fa-delete-left"></i></button>
                                                                    </td>
                                                                    <td>
                                                                        <select name="kode_bank[]" id="kode_bank<?= $no ?>" class="select2_bank" data-placeholder="~ Pilih Bank">
                                                                            <option value="<?= $bd->kode_bank ?>"><?= $this->M_global->getData('m_bank', ['kode_bank' => $bd->kode_bank])->keterangan; ?></option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <select name="tipe_bank[]" id="tipe_bank<?= $no ?>" class="select2_tipe_bank" data-placeholder="~ Pilih Tipe Bank">
                                                                            <option value="<?= $bd->kode_tipe ?>"><?= $this->M_global->getData('tipe_bank', ['kode_tipe' => $bd->kode_tipe])->keterangan; ?></option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="no_card[]" id="no_card<?= $no ?>" class="form-control" maxlength="16" value="<?= $bd->no_card ?>">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="approval[]" id="approval<?= $no ?>" class="form-control" maxlength="6" value="<?= $bd->approval ?>">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="jumlah_card[]" id="jumlah_card<?= $no ?>" class="form-control text-right" value="<?= number_format($bd->jumlah) ?>" onchange="hitung_card(<?= $no ?>); formatRp(this.value, 'jumlah_card1')">
                                                                    </td>
                                                                </tr>
                                                            <?php $no++;
                                                            endforeach; ?>
                                                        <?php else : ?>
                                                            <tr id="rowCard1">
                                                                <td>
                                                                    <button type="button" class="btn btn-dark" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tooltip on bottom" title="Hapus" onclick="hapusBaris(1)"><i class="fa-solid fa-delete-left"></i></button>
                                                                </td>
                                                                <td>
                                                                    <select name="kode_bank[]" id="kode_bank1" class="select2_bank" data-placeholder="~ Pilih Bank"></select>
                                                                </td>
                                                                <td>
                                                                    <select name="tipe_bank[]" id="tipe_bank1" class="select2_tipe_bank" data-placeholder="~ Pilih Tipe Bank"></select>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="no_card[]" id="no_card1" class="form-control" maxlength="16">
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="approval[]" id="approval1" class="form-control" maxlength="6">
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="jumlah_card[]" id="jumlah_card1" class="form-control text-right" value="0" onchange="hitung_card(1); formatRp(this.value, 'jumlah_card1')">
                                                                </td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="hidden" class="form-control" id="jumCard" value="<?= (!empty($bayar_detail) ? count($bayar_detail) : '1') ?>">
                                            <button type="button" class="btn btn-primary" onclick="tambah_card()"><i class="fa-solid fa-folder-plus"></i> Tambah Card</button>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <div class="row">
                                                <label for="" class="control-label col-md-3 my-auto">Total Card</label>
                                                <div class="col-md-9">
                                                    <input type="text" class="form-control text-right" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tooltip on bottom" title="Total Card" placeholder="Total Card" id="card" name="card" value="<?= (!empty($data_pembayaran) ? number_format($data_pembayaran->card) : '0') ?>" readonly>
                                                </div>
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
                            <button type="button" class="btn btn-danger" onclick="getUrl('Accounting/deposit_kas')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                            <button type="button" class="btn btn-success float-right ml-2" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                            <?php if (!empty($data_pembayaran)) : ?>
                                <button type="button" class="btn btn-info float-right" onclick="getUrl('Accounting/form_deposit_kas/0')" id="btnBaru"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
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
    let token = $('#token')
    let cek_cash = $('#cek_cash')
    let cek_card = $('#cek_card')
    let total = $('#total')
    const fortableCash = $('#fortableCash')
    const fortableCard = $('#fortableCard')
    var bodyCard = $('#bodyBayarCard')
    const btnSimpan = $('#btnSimpan')
    const form = $('#form_pembayaran')

    <?php if (!empty($data_pembayaran)) : ?>
        cek_cc(<?= $data_pembayaran->jenis_pembayaran ?>)
    <?php else : ?>
        cek_cash.attr('checked', true);
        fortableCard.hide();
        cek_button();
    <?php endif ?>

    // fungsi format Rupiah NoId
    function formatRpNoId(num) {
        num = num.toString().replace(/\$|\,/g, '');

        num = Math.ceil(num);

        if (isNaN(num)) num = "0";

        sign = (num == (num = Math.abs(num)));
        num = Math.floor(num * 100 + 0.50000000001);
        cents = num % 100;
        num = Math.floor(num / 100).toString();

        if (cents < 10) cents = "0" + cents;

        for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++) {
            num = num.substring(0, num.length - (4 * i + 3)) + ',' + num.substring(num.length - (4 * i + 3));
        }

        return (((sign) ? '' : '-') + '' + num);
    }

    // fungsi tambah baris card
    function tambah_card() {
        var jum = Number($('#jumCard').val());
        var row = jum + 1;

        $('#jumCard').val(row);
        bodyCard.append(`<tr id="rowCard${row}">
            <td>
                <button type="button" class="btn btn-dark" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tooltip on bottom" title="Hapus" onclick="hapusBaris(${row})"><i class="fa-solid fa-delete-left"></i></button>
            </td>
            <td>
                <select name="kode_bank[]" id="kode_bank${row}" class="select2_bank" data-placeholder="~ Pilih Bank"></select>
            </td>
            <td>
                <select name="tipe_bank[]" id="tipe_bank${row}" class="select2_tipe_bank" data-placeholder="~ Pilih Tipe Bank"></select>
            </td>
            <td>
                <input type="text" name="no_card[]" id="no_card${row}" class="form-control" maxlength="16">
            </td>
            <td>
                <input type="text" name="approval[]" id="approval${row}" class="form-control" maxlength="6">
            </td>
            <td>
                <input type="text" name="jumlah_card[]" id="jumlah_card${row}" class="form-control text-right" value="0" onchange="hitung_card(${row}); formatRp(this.value, 'jumlah_card${row}')">
            </td>
        </tr>`);


        initailizeSelect2_bank();
        initailizeSelect2_tipe_bank();
    }

    // fungsi hapus baris card
    function hapusBaris(row) {
        $('#rowCard' + row).remove();

        hitung_card_all();
    }

    function cek_cc(isi) {
        if (isi == 0) {
            document.getElementById('cek_card').checked = false;
            document.getElementById('cek_cash').checked = true;
            document.getElementById('cek_cash_card').checked = false;

            fortableCash.show(200);
            fortableCard.hide(200);

            $('#jumCard').val(0);
            bodyCard.empty();
            tambah_card();
            hitung_card_all();
        } else if (isi == 1) {
            document.getElementById('cek_card').checked = true;
            document.getElementById('cek_cash').checked = false;
            document.getElementById('cek_cash_card').checked = false;

            fortableCash.hide(200);
            fortableCard.show(200);

            $('#cash').val(0);
            hitung_bayar();
        } else {
            document.getElementById('cek_card').checked = false;
            document.getElementById('cek_cash').checked = false;
            document.getElementById('cek_cash_card').checked = true;

            fortableCash.show(200);
            fortableCard.show(200);
        }

        $('#jenis_pembayaran').val(isi);
    }

    // fungsi hitung row card
    function hitung_card(x) {
        var jumlah = ($('#jumlah_card' + x).val()).replaceAll(',', '');

        hitung_card_all();
    }

    // fungsi hitung seluruh card
    function hitung_card_all() {
        var tableBayarCard = document.getElementById('tableBayarCard'); // ambil id table detail
        var rowCount = tableBayarCard.rows.length; // hitung jumlah rownya

        // buat variable untuk di sum
        var tjumlah = 0;
        for (var i = 1; i < rowCount; i++) {
            var row = tableBayarCard.rows[i];

            var jumlah1 = Number((row.cells[5].children[0].value).replace(/[^0-9\.]+/g, ""));

            // lakukan rumus sum
            tjumlah += jumlah1;
        }

        $('#card').val(formatRpNoId(tjumlah));

        hitung_bayar();
    }

    function hitung_bayar() {
        var cash = parseFloat(($('#cash').val()).replaceAll(',', ''));
        var card = parseFloat(($('#card').val()).replaceAll(',', ''));

        var semua = cash + card;
        $('#cash').val(formatRpNoId(cash));
        $('#total').val(formatRpNoId(semua));

        cek_button();
    }

    function cek_button() {
        if (parseFloat(($('#total').val()).replaceAll(',', '')) > 0) {
            btnSimpan.attr('disabled', false);
        } else {
            btnSimpan.attr('disabled', true);
        }
    }

    function save() {
        if (token.val() == '' || token.val() == null) { // jika token null/ kosong
            // isi param = 1
            var param = 1;
        } else { // selain itu
            // isi param = 2
            var param = 2;
        }

        // jalankan proses cek barang
        proses(param);
    }

    function proses(param) {
        if (param == 1) { // jika param 1 berarti insert/tambah
            var message = 'dibuat!';
        } else { // selain itu berarti update/ubah
            var message = 'diperbarui!';
        }

        // jalankan proses dengan param insert/update
        $.ajax({
            url: siteUrl + 'Accounting/deposit_kas_proses/' + param,
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) { // jika fungsi berjalan dengan baik
                btnSimpan.attr('disabled', false);

                if (result.status == 1) { // jika mendapatkan respon 1

                    Swal.fire("Deposit Kas/Bank", "Berhasil " + message, "success").then(() => {
                        // question_cetak(result.token);
                        getUrl('Accounting/deposit_kas');
                    });
                } else { // selain itu

                    Swal.fire("Deposit Kas/Bank", "Gagal " + message + ", silahkan dicoba kembali", "info");
                }
            },
            error: function(result) { // jika fungsi error
                btnSimpan.attr('disabled', false);

                error_proccess();
            }
        });
    }
</script>