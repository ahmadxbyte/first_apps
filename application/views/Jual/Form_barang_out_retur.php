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

<form method="post" id="form_barang_out_retur">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Formulir</span>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="">Invoice Retur</label>
                                <input type="text" class="form-control" placeholder="Invoice (Otomatis)" id="invoice" name="invoice" value="<?= (!empty($data_barang_out_retur) ? $data_barang_out_retur->invoice : '') ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="">Tgl/Jam Retur Penjualan</label>
                                <div class="row">
                                    <div class="col-md-6 col-6">
                                        <input type="date" title="Tgl Retur" class="form-control" placeholder="Tgl Retur" id="tgl_retur" name="tgl_retur" value="<?= (!empty($data_barang_out_retur) ? date('Y-m-d', strtotime($data_barang_out_retur->tgl_retur)) : date('Y-m-d')) ?>" readonly>
                                    </div>
                                    <div class="col-md-6 col-6">
                                        <input type="time" title="Jam Retur" class="form-control" placeholder="Jam Retur" id="jam_retur" name="jam_retur" value="<?= (!empty($data_barang_out_retur) ? date('H:i:s', strtotime($data_barang_out_retur->jam_retur)) : date('H:i:s')) ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="">Penjualan <sup class="text-danger">**</sup></label>
                                <select name="invoice_jual" id="invoice_jual" class="form-control select2_jual_for_retur" data-placeholder="~ Pilih Invoice Penjualan" onchange="getPenjualan(this.value)">
                                    <?php
                                    if (!empty($data_barang_out_retur)) :
                                        $penjualan = $this->M_global->getData('barang_out_header', ['invoice' => $data_barang_out_retur->invoice_jual]);
                                        echo '<option value="' . $data_barang_out_retur->invoice_jual . '">' . $data_barang_out_retur->invoice_jual . ' ~ Tgl/Jam: ' . date('d-m-Y', strtotime($penjualan->tgl_jual)) . '/' . date('H:i:s', strtotime($penjualan->jam_jual)) . ' | Total: Rp.' . number_format($penjualan->total) . '</option>';
                                    endif;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="">Gudang</label>
                                <input type="hidden" name="kode_gudang" id="kode_gudang" value="<?= (!empty($data_barang_out_retur) ? $data_barang_out_retur->kode_gudang : '') ?>">
                                <input type="text" title="Gudang" class="form-control" placeholder="Gudang" id="gudang" name="gudang" value="<?= (!empty($data_barang_out_retur) ? $this->M_global->getData('m_gudang', ['kode_gudang' => $data_barang_out_retur->kode_gudang])->nama : '') ?>" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="">Alasan Di Retur <sup class="text-danger">**</sup></label>
                                <textarea name="alasan" id="alasan" class="form-control" rows="3"><?= (!empty($data_barang_out_retur) ? $data_barang_out_retur->alasan : '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Detail Barang Jual</span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <input type="hidden" name="jumlahBarisBarang" id="jumlahBarisBarang" value="<?= (!empty($barang_detail) ? count($barang_detail) : '0') ?>">
                                <table class="table shadow-sm table-hover table-bordered" id="tableDetailBarangOut" width="100%" style="border-radius: 10px;">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="5%" style="border-radius: 10px 0px 0px 0px;">Hapus</th>
                                            <th>Barang</th>
                                            <th width="12%">Satuan</th>
                                            <th width="14%">Harga</th>
                                            <th width="10%">Qty</th>
                                            <th width="10%">Disc (%)</th>
                                            <th width="14%">Disc (Rp)</th>
                                            <th width="5%">Pajak</th>
                                            <th width="10%" style="border-radius: 0px 10px 0px 0px;">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyBarangOutRetur">
                                        <?php if (!empty($barang_detail)) : ?>
                                            <?php $no = 1;
                                            foreach ($barang_detail as $bd) :
                                                $satuan = $this->M_global->getData('m_satuan', ['kode_satuan' => $bd->kode_satuan]);
                                            ?>
                                                <tr id="rowBarangOut<?= $no ?>">
                                                    <td class="text-center"><button class="btn btn-sm btn-danger" type="button" id="btnHapus<?= $no ?>" onclick="hapusBarang('<?= $no ?>')"><i class="fa-solid fa-delete-left"></i></button></td>
                                                    <td>
                                                        <input type="hidden" id="kode_barang_out<?= $no ?>" name="kode_barang_out[]" value="<?= $bd->kode_barang ?>">
                                                        <span><?= $bd->kode_barang ?> ~ <?= $this->M_global->getData('barang', ['kode_barang' => $bd->kode_barang])->nama ?></span>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" id="kode_satuan<?= $no ?>" name="kode_satuan[]" value="<?= $bd->kode_satuan ?>">
                                                        <input type="text" class="form-control" id="kode_satuanx<?= $no ?>" name="kode_satuanx[]" value="<?= $satuan->keterangan ?>" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" id="harga_out<?= $no ?>" name="harga_out[]" value="<?= number_format($bd->harga) ?>" class="form-control text-right" onchange="hitung_st('<?= $no ?>'); formatRp(this.value, 'harga_out<?= $no ?>')" readonly>
                                                        Rp. <span class="float-right"><?= number_format($bd->harga) ?></span>
                                                    </td>
                                                    <td>
                                                        <input type="text" id="qty_out<?= $no ?>" name="qty_out[]" value="<?= number_format($bd->qty) ?>" class="form-control text-right" onchange="hitung_st('<?= $no ?>'); formatRp(this.value, 'qty_out<?= $no ?>')">
                                                    </td>
                                                    <td>
                                                        <input type="text" id="discpr_out<?= $no ?>" name="discpr_out[]" value="<?= number_format($bd->discpr) ?>" class="form-control text-right" onchange="hitung_dpr(<?= $no ?>); formatRp(this.value, 'discpr_out<?= $no ?>')">
                                                    </td>
                                                    <td>
                                                        <input type="text" id="discrp_out<?= $no ?>" name="discrp_out[]" value="<?= number_format($bd->discrp) ?>" class="form-control text-right" onchange="hitung_drp(<?= $no ?>); formatRp(this.value, 'discrp_out<?= $no ?>')">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" id="pajak_out<?= $no ?>" name="pajak_out[]" class="form-control" onclick="hitung_st('<?= $no ?>')" <?= (((int)$bd->pajak > 0) ? 'checked' : '') ?>>
                                                        <input type="hidden" id="pajakrp_out<?= $no ?>" name="pajakrp_out[]" value="<?= number_format($bd->pajakrp) ?>">
                                                    </td>
                                                    <td>
                                                        <input type="hidden" id="jumlah_out<?= $no ?>" name="jumlah_out[]" value="<?= number_format($bd->jumlah) ?>" class="form-control text-right" readonly>
                                                        Rp. <span class="float-right" id="jumlah2_out<?= $no ?>"><?= number_format($bd->jumlah) ?></span>
                                                    </td>
                                                </tr>
                                            <?php $no++;
                                            endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7 col-12"></div>
                        <div class="col-md-5 col-12">
                            <div class="card">
                                <div class="card-footer">
                                    <div class="row mb-1">
                                        <label for="subtotal" class="control-label col-md-4 col-12 my-auto">Subtotal <span class="float-right">Rp</span></label>
                                        <div class="col-md-8 col-12">
                                            <input type="text" name="subtotal" id="subtotal" class="form-control text-right" value="<?= ((!empty($data_barang_out_retur)) ? number_format($data_barang_out_retur->subtotal) : '0') ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <label for="diskon" class="control-label col-md-4 col-12 my-auto">Diskon <span class="float-right">Rp</span></label>
                                        <div class="col-md-8 col-12">
                                            <input type="text" name="diskon" id="diskon" class="form-control text-right" value="<?= ((!empty($data_barang_out_retur)) ? number_format($data_barang_out_retur->diskon) : '0') ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <label for="pajak" class="control-label col-md-4 col-12 my-auto">Pajak <span class="float-right">Rp</span></label>
                                        <div class="col-md-8 col-12">
                                            <input type="text" name="pajak" id="pajak" class="form-control text-right" value="<?= ((!empty($data_barang_out_retur)) ? number_format($data_barang_out_retur->pajak) : '0') ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="total" class="control-label col-md-4 col-12 my-auto">Total <span class="float-right">Rp</span></label>
                                        <div class="col-md-8 col-12">
                                            <input type="text" name="total" id="total" class="form-control text-right" value="<?= ((!empty($data_barang_out_retur)) ? number_format($data_barang_out_retur->total) : '0') ?>" readonly>
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
                            <button type="button" class="btn btn-danger" onclick="getUrl('Transaksi/barang_out_retur')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                            <button type="button" class="btn btn-success float-right ml-2" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                            <?php if (!empty($data_barang_out_retur)) : ?>
                                <button type="button" class="btn btn-info float-right" onclick="getUrl('Transaksi/form_barang_out_retur/0')" id="btnBaru"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
                            <?php else : ?>
                                <button type="button" class="btn btn-info float-right" onclick="reset()" id="btnReset"><i class="fa-solid fa-arrows-rotate"></i>&nbsp;&nbsp;Reset</button>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    var kode_barang = $('#kode_barang');
    const form = $('#form_barang_out_retur');
    const btnCari = $('#btnCari');
    const btnSimpan = $('#btnSimpan');

    // header
    var invoice = $('#invoice');
    var invoice_jual = $('#invoice_jual');
    var tgl_retur = $('#tgl_retur');
    var jam_retur = $('#jam_retur');
    var kode_gudang = $('#kode_gudang');
    var gudang = $('#gudang');
    var alasan = $('#alasan');

    // detail
    var tableBarangIn = $('#tableDetailBarangOut');
    var bodyBarangOutRetur = $('#bodyBarangOutRetur');
    var rowBarangOut = $('#rowBarangOut');
    var jumlahBarisBarang = $('#jumlahBarisBarang');

    // onload
    if (invoice.val() == '' || invoice.val() == null) { // jika invoice kosong/ null
        // disabled id
        btnSimpan.attr('disabled', true);
    } else { // selain itu
        // jalankan fungsi hitung_t()
        hitung_t();
    }

    // fungsi hitung max retur
    function cek_qty(x) {
        var inv_jual = invoice_jual.val();
        var kode = $('#kode_barang_out' + x).val();
        var qty_now = ($('#qty_out' + x).val()).replaceAll(',', '');

        // jalankan fungsi
        $.ajax({
            url: siteUrl + 'Transaksi/getQtyJual/' + inv_jual + '/' + kode,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) { // jika fungsi berjalan
                if (result.status == 0) { // jika mendapatkan feedback status 0
                    // munculkan notifikasi
                    return Swal.fire("Qty Penjualan", "Tidak ditemukan!, coba lagi", "info");
                }

                // result qty jual
                var qty_jual = parseFloat(result.qty);

                if (qty_now > qty_jual) { // jika qty input lebih besar dari qty jual
                    // isi inputakn qty dengan qty jual
                    $('#qty_out' + x).val(formatRpNoId(qty_jual));

                    // munculkan notifikasi
                    Swal.fire("Qty Penjualan", "Qty retur melebihi qty jual!<br>Qty jual barang " + kode + ": " + formatRpNoId(result.qty), "info");
                } else { // selain itu
                    $('#qty_out' + x).val(formatRpNoId(qty_now));
                }

                hitung_st(x)
            },
            error: function(result) { // jika fungsi error

                // jalankan notifikasi error
                error_proccess();
            }
        });
    }

    // fungsi ambil data penjualan
    function getPenjualan(x) {
        if (x == '' || x == null) { // jika invoice_jual kosong/ null
            $('#bodyBarangOutRetur').html('');
            kode_gudang.val('');
            gudang.val('');

            $('#jumlahBarisBarang').val(1);

            hitung_t();

            // munculkan notifikasi
            return Swal.fire("Invoice Penjualan", "Form sudah dipilih!", "question");
        }

        // jalankan fungsi
        $('#bodyBarangOutRetur').html('');

        $('#jumlahBarisBarang').val(1);

        $.ajax({
            url: siteUrl + 'Transaksi/getBarangOut/' + x,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) { // jika fungsi berjalan
                if (result[0].status == 1) {
                    kode_gudang.val(result[0]['header'].kode_gudang);
                    gudang.val(result[0]['header'].nama_gudang);

                    jumlahBarisBarang.val(result[1].length);

                    var no = 1;
                    $.each(result[1], function(index, value) {
                        if (value.pajak > 0) {
                            var cek_pajak = 'checked';
                        } else {
                            var cek_pajak = '';
                        }

                        getSatuan(value.satuan_default, no);

                        bodyBarangOutRetur.append(`<tr id="rowBarangOut${no}">
                            <td class="text-center">
                                <button class="btn btn-sm btn-danger" type="button" id="btnHapus${no}" onclick="hapusBarang('${no}')">
                                    <i class="fa-solid fa-delete-left"></i>
                                </button>
                            </td>
                            <td>
                                <input type="hidden" id="kode_barang_out${no}" name="kode_barang_out[]" value="${value.kode_barang}">
                                <span>${value.kode_barang} ~ ${value.nama}</span>
                            </td>
                            <td>
                                <input type="hidden" id="kode_satuan${no}" name="kode_satuan[]" value="">
                                <span id="kode_satuanx${no}" name="kode_satuanx[]"></span>
                            </td>
                            <td>
                                <input type="hidden" id="harga_out${no}" name="harga_out[]" value="${formatRpNoId(Number(value.harga))}" class="form-control text-right" onchange="hitung_st('${no}'); formatRp(this.value, 'harga_out${no}')" readonly>
                                Rp. <span class="float-right">${formatRpNoId(Number(value.harga))}</span>
                            </td>
                            <td>
                                <input type="text" id="qty_out${no}" name="qty_out[]" value="${formatRpNoId(value.qty_po)}" class="form-control text-right" onchange="cek_qty('${no}'); formatRp(this.value, 'qty_out${no}')">
                            </td>
                            <td>
                                <input type="text" id="discpr_out${no}" name="discpr_out[]" value="${formatRpNoId(value.discpr)}" class="form-control text-right" onchange="hitung_dpr(${no}); formatRp(this.value, 'discpr_out${no}')">
                            </td>
                            <td>
                                <input type="text" id="discrp_out${no}" name="discrp_out[]" value="${formatRpNoId(value.discrp)}" class="form-control text-right" onchange="hitung_drp(${no}); formatRp(this.value, 'discrp_out${no}')">
                            </td>
                            <td class="text-center">
                                <input type="checkbox" id="pajak_out${no}" name="pajak_out[]" class="form-control" onclick="hitung_st('${no}')" ${cek_pajak}>
                                <input type="hidden" id="pajakrp_out${no}" name="pajakrp_out[]" value="${formatRpNoId(value.pajakrp)}">
                            </td>
                            <td>
                                <input type="hidden" id="jumlah_out${no}" name="jumlah_out[]" value="${formatRpNoId(Number(value.jumlah))}" class="form-control text-right" readonly>
                                Rp. <span class="float-right" id="jumlah2_out${no}">${formatRpNoId(Number(value.jumlah))}</span>
                            </td>
                        </tr>`);

                        hitung_st(no);

                        no++;
                    });

                    // jalankan fungsi
                } else {
                    $('#bodyBarangOutRetur').html('');

                    $('#jumlahBarisBarang').val(1);

                    hitung_t();
                }

            },
            error: function(result) {
                error_proccess();
            }
        });
    }

    function getSatuan(ks, x) {
        if (ks == '' || ks == null) {
            return Swal.fire("Satuan", "Tidak terdefinisi, silahkan dicoba kembali", "info");
        }

        $.ajax({
            url: siteUrl + 'Transaksi/getSatuanBarangIn/' + ks,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if (result.status == 1) {
                    $('#kode_satuan' + x).val(result.kode_satuan);
                    $('#kode_satuanx' + x).text(result.keterangan);
                } else {
                    return Swal.fire("Satuan", "Tidak terdefinisi, silahkan dicoba kembali", "info");
                }
            },
            error: function(result) {
                error_proccess();
            }
        });
    }

    // fungsi hapus baris barang detail
    function hapusBarang(x) {
        // hapus baris barang detail dengan id tr table
        $('#rowBarangOut' + x).remove();
        // jalankan fungsi
        hitung_t();

    }

    // perhitungan diskon % row
    function hitung_dpr(x) {
        var harga = ($('#harga_out' + x).val()).replaceAll(',', '');
        var qty = ($('#qty_out' + x).val()).replaceAll(',', '');
        var discpr = ($('#discpr_out' + x).val()).replaceAll(',', '');

        if (Number(discpr) > 100) { // jika disc pr > 100
            // munculkan notifikasi
            Swal.fire("Diskon (%)", "Maksimal 100%!", "info");

            // identifikasi x = 100
            var a = 100;
        } else { // selain itu
            // identifikasi x = discpr
            var a = discpr;
        }

        // buat rumus diskon rp
        var discrp = (harga * qty) * (a / 100);

        // tampilkan hasil ke dalam format koma
        $('#discpr_out' + x).val(formatRpNoId(a));
        $('#discrp_out' + x).val(formatRpNoId(discrp));

        // jalankan fungsi
        hitung_st(x);
    }

    // perhitungan diskon rp row
    function hitung_drp(x) {
        var harga = ($('#harga_out' + x).val()).replaceAll(',', '');
        var qty = ($('#qty_out' + x).val()).replaceAll(',', '');
        var discrp = ($('#discrp_out' + x).val()).replaceAll(',', '');

        // buat rumus jumlah
        var st_awal = (harga * qty) - discrp;

        // tampilkan hasil ke dalam format koma
        $('#discrp_out' + x).val(formatRpNoId(discrp));
        $('#discpr_out' + x).val('0');
        $('#jumlah_out' + x).val(formatRpNoId(st_awal));
        $('#jumlah2_out' + x).text(formatRpNoId(st_awal));

        // jalankan fungsi
        hitung_st(x);
    }

    // fungsi ubah qty row
    function hitung_qty(x) {
        if (Number($('#discpr_out' + x).val().replaceAll(',', '')) > 0) {
            hitung_dpr(x);
        } else {
            hitung_drp(x);
        }
    }

    // perhitungan row
    function hitung_st(x) {
        var harga = ($('#harga_out' + x).val()).replaceAll(',', '');
        var qty = ($('#qty_out' + x).val()).replaceAll(',', '');
        var discrp = ($('#discrp_out' + x).val()).replaceAll(',', '');

        // buat rumus jumlah
        var st_awal = (harga * qty) - discrp;

        if (document.getElementById('pajak_out' + x).checked == true) { // jika pajak checked true
            // buat rumus pajak
            var pajakrp = formatRpNoId(st_awal * (Number(<?= $pajak ?>) / 100));
        } else { // selain itu
            // pajak dibuat 0
            var pajakrp = '0';
        }

        // tampilkan hasil ke dalam format koma
        $('#pajakrp_out' + x).val(pajakrp);
        $('#jumlah_out' + x).val(formatRpNoId(st_awal));
        $('#jumlah2_out' + x).text(formatRpNoId(st_awal));

        // jalankan rumus
        hitung_t();
    }

    // perhitungan total;
    function hitung_t() {
        var tableBarang = document.getElementById('tableDetailBarangOut'); // ambil id table detail
        var rowCount = tableBarang.rows.length; // hitung jumlah rownya

        // buat variable untuk di sum
        var tjumlah = 0;
        var tdiskon = 0;
        var tppn = 0;

        // lakukan loop
        for (var i = 1; i < rowCount; i++) {
            var row = tableBarang.rows[i];

            // ambil data berdasarkan loop
            var harga1 = Number((row.cells[3].children[0].value).replace(/[^0-9\.]+/g, ""));
            var qty1 = Number((row.cells[4].children[0].value).replace(/[^0-9\.]+/g, ""));
            var discrp1 = Number((row.cells[6].children[0].value).replace(/[^0-9\.]+/g, ""));
            var pajak1 = Number((row.cells[7].children[1].value).replace(/[^0-9\.]+/g, ""));
            var jumlah1 = Number((row.cells[8].children[0].value).replace(/[^0-9\.]+/g, ""));

            // lakukan rumus sum
            tjumlah += jumlah1 + discrp1;
            tdiskon += discrp1;
            tppn += pajak1;
        }

        // buat rumus total
        var ttotal = tjumlah + tppn;

        // tampilkan hasil ke dalam format koma
        $('#subtotal').val(formatRpNoId(tjumlah));
        $('#diskon').val(formatRpNoId(tdiskon));
        $('#pajak').val(formatRpNoId(tppn));
        $('#total').val(formatRpNoId(ttotal));

        // jalankan fungsi
        cekButtonSave();
    }

    // fungsi cek tombol simpan
    function cekButtonSave() {
        if (($('#total').val()).replaceAll(',', '') < 1 || $('#total').val() == '0') {
            btnSimpan.attr('disabled', true);
        } else {
            btnSimpan.attr('disabled', false);
        }
    }

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
            num = num.substring(0, num.length - (4 * i + 3)) + ',' +
                num.substring(num.length - (4 * i + 3));
        }

        return (((sign) ? '' : '-') + '' + num);
    }

    // fungsi simpan
    function save() {
        btnSimpan.attr('disabled', true);

        var tableBarang = document.getElementById('tableDetailBarangOut'); // ambil id table detail
        var rowCount = tableBarang.rows.length; // hitung jumlah rownya

        if (rowCount < 1) { // jika jumlah baris detail kurang dari 1
            btnSimpan.attr('disabled', false);

            return Swal.fire("Detail Barang Retur Penjualan", "Form sudah diisi?", "question");
        }

        if (tgl_retur.val() == '' || tgl_retur.val() == null) { // jika tgl_retur null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Tgl Jual", "Form sudah diisi?", "question");
        }

        if (jam_retur.val() == '' || jam_retur.val() == null) { // jika jam_retur null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Jam Jual", "Form sudah diisi?", "question");
        }

        if (kode_gudang.val() == '' || kode_gudang.val() == null) { // jika kode_gudang null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Gudang", "Form sudah dipilih?", "question");
        }

        if (alasan.val() == '' || alasan.val() == null) { // jika alasan null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Alasan", "Form sudah diisi?", "question");
        }

        if (invoice.val() == '' || invoice.val() == null) { // jika invoice null/ kosong
            // isi param = 1
            var param = 1;
        } else { // selain itu
            // isi param = 2
            var param = 2;
        }

        // jalankan proses cek barang
        proses(param);
    }

    // fungsi proses dengan param
    function proses(param) {

        if (param == 1) { // jika param 1 berarti insert/tambah
            var message = 'dibuat!';
        } else { // selain itu berarti update/ubah
            var message = 'diperbarui!';
        }

        // jalankan proses dengan param insert/update
        $.ajax({
            url: siteUrl + 'Transaksi/barang_out_retur_proses/' + param,
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) { // jika fungsi berjalan dengan baik
                btnSimpan.attr('disabled', false);

                if (result.status == 1) { // jika mendapatkan respon 1

                    Swal.fire("Retur Penjualan", "Berhasil " + message, "success").then(() => {
                        getUrl('Transaksi/barang_out_retur');
                    });
                } else { // selain itu

                    Swal.fire("Retur Penjualan", "Gagal " + message + ", silahkan dicoba kembali", "info");
                }
            },
            error: function(result) { // jika fungsi error
                btnSimpan.attr('disabled', false);

                error_proccess();
            }
        });
    }

    // fungsi reset
    function reset() {
        kode_gudang.val('');
        gudang.val('');
        bodyBarangOutRetur.html('');
    }
</script>