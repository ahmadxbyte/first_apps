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
                    <div class="form-group">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="invoice" class="control-label">Invoice</label>
                                        <input type="text" class="form-control" placeholder="Otomatis" id="invoice" name="invoice" value="<?= (!empty($data_mutasi) ? $data_mutasi->invoice : '') ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="invoice_po" class="control-label">Invoice Pengajuan</label>
                                        <select name="invoice_po" id="invoice_po" class="form-control select2_global" data-placeholder="~ Pilih Pengajuan Mutasi" onchange="getPengajuanMutasi(this.value)">
                                            <option value="">~ Pilih Pengajuan Mutasi</option>
                                            <?php foreach ($data_pm as $dpm) : ?>
                                                <option value="<?= $dpm->invoice ?>" <?= (!empty($data_mutasi) ? (($data_mutasi->invoice_po == $dpm->invoice) ? 'selected' : '') : '') ?>><?= $dpm->invoice . ' | Tgl/Jam: ' . date('d-m-Y', strtotime($dpm->tgl_po)) . '/' . date('H:i:s', strtotime($dpm->jam_po)) . ' | Jenis: ' . (($dpm->jenis_po > 0) ? 'Mutasi Cabang' : 'Mutasi Gudang') ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6 col-6">
                                        <label for="tgl" class="control-label">Tgl Penerimaan Mutasi</label>
                                        <input type="date" title="Tgl Penerimaan" class="form-control" placeholder="Tgl Penerimaan" id="tgl" name="tgl" value="<?= (!empty($data_mutasi) ? date('Y-m-d', strtotime($data_mutasi->tgl)) : date('Y-m-d')) ?>" readonly>
                                    </div>
                                    <div class="col-md-6 col-6">
                                        <label for="jam" class="control-label">Jam Penerimaan Mutasi</label>
                                        <input type="time" title="Jam Penerimaan" class="form-control" placeholder="Jam Penerimaan" id="jam" name="jam" value="<?= (!empty($data_mutasi) ? date('H:i:s', strtotime($data_mutasi->jam)) : date('H:i:s')) ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <input type="hidden" name="cek_pajak" id="cek_pajak" value="<?= $pajak ?>">
                            <div class="col-md-6">
                                <label for="kode_supplier" class="control-label">Jenis Pengajuan Mutasi <sup class="text-danger">**</sup></label>
                                <input type="hidden" id="jenis" name="jenis" class="form-control" value="<?= (!empty($data_mutasi) ? $data_mutasi->jenis : '') ?>">
                                <input type="text" id="jenisx" name="jenisx" class="form-control" value="<?= (!empty($data_mutasi) ? (($data_mutasi->jenis == 0) ? 'Mutasi Gudang' : 'Mutasi Cabang') : '') ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <?php
                                if (!empty($data_mutasi)) {
                                    $dari = $data_mutasi->dari;
                                    $menuju = $data_mutasi->menuju;

                                    if ($data_mutasi->jenis == 0) {
                                        $darix = $this->M_global->getData('m_gudang', ['kode_gudang' => $data_mutasi->dari])->nama;
                                        $menujux = $this->M_global->getData('m_gudang', ['kode_gudang' => $data_mutasi->menuju])->nama;
                                    } else {
                                        $darix = $this->M_global->getData('cabang', ['kode_cabang' => $data_mutasi->dari])->cabang;
                                        $menujux = $this->M_global->getData('cabang', ['kode_cabang' => $data_mutasi->menuju])->cabang;
                                    }
                                } else {
                                    $dari = '';
                                    $menuju = '';

                                    $darix = '';
                                    $menujux = '';
                                }
                                ?>
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <label for="kode_supplier" class="control-label">Dari <sup class="text-danger">**</sup></label>
                                        <input type="hidden" id="dari" name="dari" class="form-control" value="<?= $dari ?>">
                                        <input type="text" id="darix" name="darix" class="form-control" value="<?= $darix ?>" readonly>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <label for="kode_gudang" class="control-label">Menuju <sup class="text-danger">**</sup></label>
                                        <input type="hidden" id="menuju" name="menuju" class="form-control" value="<?= $menuju ?>">
                                        <input type="text" id="menujux" name="menujux" class="form-control" value="<?= $menujux ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Detail Barang</span>
                    <div class="float-right">
                        <span class="text-danger font-weight-bold">Pajak Aktif: <?= $pajak ?>%</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <input type="hidden" name="jumlahBarisBarang" id="jumlahBarisBarang" value="<?= (!empty($mutasi_detail) ? count($mutasi_detail) : '0') ?>">
                                <table class="table shadow-sm table-hover table-bordered" id="tableDetailBarangIn" width="100%" style="border-radius: 10px;">
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
                                    <tbody id="bodyBarangIn">
                                        <?php if (!empty($mutasi_detail)) : ?>
                                            <?php $no = 1;
                                            foreach ($mutasi_detail as $bd) :
                                                $barang = $this->M_global->getData('barang', ['kode_barang' => $bd->kode_barang]);

                                                $satuan = [];
                                                foreach ([$barang->kode_satuan, $barang->kode_satuan2, $barang->kode_satuan3] as $satuanCode) {
                                                    $satuanDetail = $this->M_global->getData('m_satuan', ['kode_satuan' => $satuanCode]);
                                                    if ($satuanDetail) {
                                                        $satuan[] = [
                                                            'kode_satuan' => $satuanCode,
                                                            'keterangan' => $satuanDetail->keterangan,
                                                        ];
                                                    }
                                                }
                                            ?>
                                                <tr id="rowBarangIn<?= $no ?>">
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-danger" type="button" id="btnHapus<?= $no ?>" onclick="hapusBarang('<?= $no ?>')"><i class="fa-solid fa-delete-left"></i></button>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" id="kode_barang_po_in<?= $no ?>" name="kode_barang_po_in[]" value="<?= $bd->kode_barang ?>">
                                                        <span><?= $bd->kode_barang ?> ~ <?= $this->M_global->getData('barang', ['kode_barang' => $bd->kode_barang])->nama ?></span>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="kode_satuan[]" id="kode_satuan<?= $no ?>" value="<?= $bd->kode_satuan; ?>">
                                                        <span><?= $this->M_global->getData('m_satuan', ['kode_satuan' => $bd->kode_satuan])->keterangan ?></span>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" name="harga_in[]" id="harga_in<?= $no ?>" value="<?= $bd->harga; ?>">
                                                        Rp. <span class="float-right"><?= number_format($bd->harga) ?></span>
                                                    </td>
                                                    <td>
                                                        <input type="text" id="qty_in<?= $no ?>" name="qty_in[]" value="<?= number_format($bd->qty) ?>" class="form-control text-right" onchange="hitung_st('<?= $no ?>'); formatRp(this.value, 'qty_in<?= $no ?>')">
                                                    </td>
                                                    <td>
                                                        <input type="text" id="discpr_in<?= $no ?>" name="discpr_in[]" value="<?= number_format($bd->discpr) ?>" class="form-control text-right" onchange="hitung_dpr(<?= $no ?>); formatRp(this.value, 'discpr_in<?= $no ?>')">
                                                    </td>
                                                    <td>
                                                        <input type="text" id="discrp_in<?= $no ?>" name="discrp_in[]" value="<?= number_format($bd->discrp) ?>" class="form-control text-right" onchange="hitung_drp(<?= $no ?>); formatRp(this.value, 'discrp_in<?= $no ?>')">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="checkbox" id="pajak_in<?= $no ?>" name="pajak_in[]" class="form-control" onclick="hitung_st('<?= $no ?>')" <?= (((int)$bd->pajak > 0) ? 'checked' : '') ?>>
                                                        <input type="hidden" id="pajakrp_in<?= $no ?>" name="pajakrp_in[]" value="<?= number_format($bd->pajakrp) ?>">
                                                    </td>
                                                    <td>
                                                        <input type="hidden" id="jumlah_in<?= $no ?>" name="jumlah_in[]" value="<?= number_format($bd->jumlah) ?>" class="form-control text-right" readonly>
                                                        Rp. <span class="float-right" id="jumlah2_in<?= $no ?>"><?= number_format($bd->jumlah) ?></span>
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
                                            <input type="text" name="subtotal" id="subtotal" class="form-control text-right" value="<?= ((!empty($data_mutasi)) ? number_format($data_mutasi->subtotal) : '0') ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <label for="diskon" class="control-label col-md-4 col-12 my-auto">Diskon <span class="float-right">Rp</span></label>
                                        <div class="col-md-8 col-12">
                                            <input type="text" name="diskon" id="diskon" class="form-control text-right" value="<?= ((!empty($data_mutasi)) ? number_format($data_mutasi->diskon) : '0') ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <label for="pajak" class="control-label col-md-4 col-12 my-auto">Pajak <span class="float-right">Rp</span></label>
                                        <div class="col-md-8 col-12">
                                            <input type="text" name="pajak" id="pajak" class="form-control text-right" value="<?= ((!empty($data_mutasi)) ? number_format($data_mutasi->pajak) : '0') ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="total" class="control-label col-md-4 col-12 my-auto">Total <span class="float-right">Rp</span></label>
                                        <div class="col-md-8 col-12">
                                            <input type="text" name="total" id="total" class="form-control text-right" value="<?= ((!empty($data_mutasi)) ? number_format($data_mutasi->total) : '0') ?>" readonly>
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
                            <button type="button" class="btn btn-danger" onclick="getUrl('Transaksi/pengajuan_mutasi')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                            <button type="button" class="btn btn-success float-right ml-2" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                            <?php if (!empty($data_mutasi)) : ?>
                                <button type="button" class="btn btn-info float-right" onclick="getUrl('Transaksi/form_mutasi/0')" id="btnBaru"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
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
    var kode_barang = $('#kode_barang');
    const form = $('#form_mutasi');
    const btnCari = $('#btnCari');
    const btnSimpan = $('#btnSimpan');

    // header
    var invoice = $('#invoice');
    var tgl_po = $('#tgl_po');
    var jam_po = $('#jam_po');
    var kode_supplier = $('#kode_supplier');
    var kode_gudang = $('#kode_gudang');
    var surat_jalan = $('#surat_jalan');
    var no_faktur = $('#no_faktur');
    var cek_pajak = $('#cek_pajak');

    // detail
    var kode_satuan = $('#kode_satuan');
    var tableBarangIn = $('#tableDetailBarangIn');
    var bodyBarangIn = $('#bodyBarangIn');
    var rowBarangIn = $('#rowBarangIn');
    var jumlahBarisBarang = $('#jumlahBarisBarang');

    $('#tableSederhanaObat').DataTable({
        "destroy": true,
        "processing": true,
        "responsive": true,
        "serverSide": false,
        "scrollCollapse": false,
        "paging": false,
        "oLanguage": {
            "sEmptyTable": "<div class='text-center'>Data Kosong</div>",
            "sInfoEmpty": "",
            "sInfoFiltered": "",
            "sSearch": "",
            "sSearchPlaceholder": "Cari data...",
            "sInfo": " Jumlah _TOTAL_ Data (_START_ - _END_)",
            "sLengthMenu": "_MENU_ Baris",
            "sZeroRecords": "<div class='text-center'>Data Kosong</div>",
            "oPaginate": {
                "sPrevious": "Sebelumnya",
                "sNext": "Berikutnya"
            }
        },
        "aLengthMenu": [
            [5, 15, 20, -1],
            [5, 15, 20, "Semua"]
        ],
        "columnDefs": [{
            "targets": [-1],
            "orderable": false,
        }, ],
    });


    // onload
    if (invoice.val() == '' || invoice.val() == null) {
        btnSimpan.attr('disabled', true);
    } else {
        hitung_t();
    }

    // fungsi tampil modal list barang
    function showBarang() {
        $('#modal_barang').modal('show');
    }

    // fungsi tutup modal list barang
    function tutupModal() {
        $('#modal_barang').modal('hide');
    }

    // fungsi ubah satuan untuk ubah harga
    function ubahSatuan(param, id) {
        var kode_barang_po_in = $('#kode_barang_po_in' + id).val();
        var kode_satuan = $('#kode_satuan' + id).val();

        if (!param || param === null) {
            error_proccess();
            return; // Add return to stop further execution
        }

        $.ajax({
            url: siteUrl + 'Transaksi/getSatuan/' + param + '/' + kode_barang_po_in,
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) {
                var qty_satuan = Number(result.qty_satuan);
                var hna_master = Number(result.hna);
                var qty = Number($('#qty_in' + id).val().replaceAll(',', ''));

                if (isNaN(qty)) qty = 0; // Ensure qty is valid

                var newHarga = hna_master * qty_satuan;
                $('#harga_in' + id).val(formatRpNoId(newHarga));

                var discpr = Number($('#discpr_in' + id).val().replaceAll(',', ''));
                var newDiskon = (discpr > 0) ? (newHarga * qty) * (discpr / 100) : ($('#discrp_in' + id).val()).replaceAll(',', '');

                $('#discrp_in' + id).val(formatRpNoId(newDiskon));
                hitung_st(id);
            },
            error: function(result) {
                error_proccess();
            }
        });
    }

    var cek_param = "<?= $this->input->get('invoice') ?>";

    if (cek_param !== '' && cek_param !== '0') {
        $('#invoice_po').val(cek_param).change();
        // alert(cek_param)
    }


    // get data pengajuan mutasi
    function getPengajuanMutasi(param) {

        bodyBarangIn.empty();
        hitung_t();
        $('#dari').val('').change();
        $('#menuju').val('').change();

        if (param == '' || param == null) {
            return Swal.fire("Invoice Pengajuan Mutasi", "Form sudah dipilih?", "info");
        }

        $.ajax({
            url: '<?= site_url() ?>Transaksi/getDataMPO/' + param,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if (result[0]['status'] == 1) {
                    $('#jenis').val(result[0]['header'].jenis_po);
                    if (result[0]['header'].jenis_po == 0) {
                        $('#jenisx').val('Mutasi Gudang');
                    } else {
                        $('#jenisx').val('Mutasi Cabang');
                    }
                    $('#dari').val(result[0]['header'].dari);
                    $('#darix').val(result[0]['header'].dari_nama);
                    $('#menuju').val(result[0]['header'].menuju);
                    $('#menujux').val(result[0]['header'].menuju_nama);

                    jumlahBarisBarang.val(result[1].length);

                    var x = 1;

                    $.each(result[1], function(index, value) {
                        if (value.pajak > 0) {
                            var cek_pajak = 'checked';
                        } else {
                            var cek_pajak = '';
                        }

                        bodyBarangIn.append(`<tr id="rowBarangIn${x}">
                            <td class="text-center">
                                <button class="btn btn-sm btn-danger" type="button" id="btnHapus${x}" onclick="hapusBarang('${x}')">
                                    <i class="fa-solid fa-delete-left"></i>
                                </button>
                            </td>
                            <td>
                                <input type="hidden" id="kode_barang_po_in${x}" name="kode_barang_po_in[]" value="${value.kode_barang}">
                                <span>${value.kode_barang} ~ ${value.nama_barang}</span>
                            </td>
                            <td>
                                <input type="hidden" id="kode_satuan${x}" name="kode_satuan[]" value="${value.kode_satuan}">
                                <span>${value.nama_satuan}</span>
                            </td>
                            <td>
                                <input type="hidden" id="harga_in${x}" name="harga_in[]" value="${formatRpNoId(Number(value.harga))}" class="form-control text-right" onchange="hitung_st('${x}'); formatRp(this.value, 'harga_in${x}');" readonly>
                                Rp. <span class="float-right">${formatRpNoId(Number(value.harga))}</span>
                            </td>
                            <td>
                                <input type="text" id="qty_in${x}" name="qty_in[]" value="${formatRpNoId(Number(value.qty))}" class="form-control text-right" onchange="hitung_qty('${x}'); formatRp(this.value, 'qty_in${x}')">
                            </td>
                            <td>
                                <input type="text" id="discpr_in${x}" name="discpr_in[]" value="${formatRpNoId(Number(value.discpr))}" class="form-control text-right" onchange="hitung_dpr(${x}); formatRp(this.value, 'discpr_in${x}')">
                            </td>
                            <td>
                                <input type="text" id="discrp_in${x}" name="discrp_in[]" value="${formatRpNoId(Number(value.discrp))}" class="form-control text-right" onchange="hitung_drp(${x}); formatRp(this.value, 'discrp_in${x}')">
                            </td>
                            <td class="text-center">
                                <input type="checkbox" id="pajak_in${x}" name="pajak_in[]" class="form-control" onclick="hitung_st('${x}')" ${cek_pajak}>
                                <input type="hidden" id="pajakrp_in${x}" name="pajakrp_in[]" value="${formatRpNoId(Number(value.pajakrp))}">
                            </td>
                            <td>
                                <input type="hidden" id="jumlah_in${x}" name="jumlah_in[]" value="${formatRpNoId(Number(value.jumlah))}" class="form-control text-right" readonly>
                                Rp. <span class="float-right" id="jumlah2_in${x}">${formatRpNoId(Number(value.jumlah))}</span>
                            </td>
                        </tr>`);
                        hitung_st(x);

                        x++;
                    });
                } else {
                    Swal.fire("Mutasi", "Tidak ditemukan!", "info");
                }
            },
            error: function(error) {
                error_proccess();
            }
        });
    }

    // fungsi ubah qty row
    function hitung_qty(x) {
        if (Number($('#discpr_in' + x).val().replaceAll(',', '')) > 0) {
            hitung_dpr(x);
        } else {
            hitung_drp(x);
        }
    }

    // fungsi hapus baris barang detail
    function hapusBarang(x) {
        var awal = Number(jumlahBarisBarang.val());
        if (awal > 0) { // Ensure there are rows to delete
            jumlahBarisBarang.val(awal - 1);
            $('#rowBarangIn' + x).remove();
            hitung_t();
        }
    }

    // perhitungan diskon % row
    function hitung_dpr(x) {
        var harga = ($('#harga_in' + x).val()).replaceAll(',', '');
        var qty = ($('#qty_in' + x).val()).replaceAll(',', '');
        var discpr = ($('#discpr_in' + x).val()).replaceAll(',', '');

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
        $('#discpr_in' + x).val(formatRpNoId(a));
        $('#discrp_in' + x).val(formatRpNoId(discrp));

        // jalankan fungsi
        hitung_st(x);
    }

    // perhitungan diskon rp row
    function hitung_drp(x) {
        var harga = ($('#harga_in' + x).val()).replaceAll(',', '');
        var qty = ($('#qty_in' + x).val()).replaceAll(',', '');
        var discrp = ($('#discrp_in' + x).val()).replaceAll(',', '');

        // buat rumus jumlah
        var st_awal = (harga * qty) - discrp;

        // tampilkan hasil ke dalam format koma
        $('#discrp_in' + x).val(formatRpNoId(discrp));
        $('#discpr_in' + x).val('0');
        $('#jumlah_in' + x).val(formatRpNoId(st_awal));
        $('#jumlah2_in' + x).text(formatRpNoId(st_awal));

        // jalankan fungsi
        hitung_st(x);
    }

    // perhitungan row
    function hitung_st(x) {
        var harga = ($('#harga_in' + x).val()).replaceAll(',', '');
        var qty = ($('#qty_in' + x).val()).replaceAll(',', '');
        var discrp = ($('#discrp_in' + x).val()).replaceAll(',', '');
        var cek_pajak = ($('#cek_pajak').val()).replaceAll(',', '');

        if (Number(cek_pajak) > 0) {
            var pajak = (Number(cek_pajak) / 100);
        } else {
            var pajak = 0;
        }

        // buat rumus jumlah
        var st_awal = (harga * qty) - discrp;

        if (document.getElementById('pajak_in' + x).checked == true) { // jika pajak checked true
            // buat rumus pajak
            var pajakrp = formatRpNoId(st_awal * pajak);
        } else { // selain itu
            // pajak dibuat 0
            var pajakrp = '0';
        }

        // tampilkan hasil ke dalam format koma
        $('#pajakrp_in' + x).val(pajakrp);
        $('#jumlah_in' + x).val(formatRpNoId(st_awal));
        $('#jumlah2_in' + x).text(formatRpNoId(st_awal));

        // jalankan rumus
        hitung_t();
    }

    // perhitungan total;
    function hitung_t() {
        var tableBarang = document.getElementById('tableDetailBarangIn'); // ambil id table detail
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

        var tableBarang = document.getElementById('tableDetailBarangIn'); // ambil id table detail
        var rowCount = tableBarang.rows.length; // hitung jumlah rownya

        if (rowCount < 1) { // jika jumlah baris detail kurang dari 1
            btnSimpan.attr('disabled', false);
            return Swal.fire("Detail Barang Mutasi", "Form sudah diisi?", "question");
        }

        if (!$('#dari').val()) {
            btnSimpan.attr('disabled', false);
            return Swal.fire("Dari", "Form sudah dipilih?", "question");
        }

        if (!$('#menuju').val()) {
            btnSimpan.attr('disabled', false);
            return Swal.fire("Menuju", "Form sudah dipilih?", "question");
        }

        var param = invoice.val() ? 2 : 1; // Set param based on invoice value

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
            url: siteUrl + 'Transaksi/mutasi_proses/' + param,
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) { // jika fungsi berjalan dengan baik
                btnSimpan.attr('disabled', false);

                if (result.status == 1) { // jika mendapatkan respon 1

                    Swal.fire("Mutasi", "Berhasil " + message, "success").then(() => {
                        getUrl('Transaksi/penerimaan_mutasi');
                    });
                } else { // selain itu

                    Swal.fire("Mutasi", "Gagal " + message + ", silahkan dicoba kembali", "info");
                }
            },
            error: function(result) { // jika fungsi error
                btnSimpan.attr('disabled', false);

                error_proccess();
            }
        });
    }

    // fungsi reset
    function reseting() {
        $('#dari').val('');
        $('#darix').val('');
        $('#menuju').val('');
        $('#menujux').val('');
        $('#jenis').val('');
        $('#jenisx').val('');
        $('#invoice_po').val('').change();
        bodyBarangIn.empty();
        hitung_t();
    }
</script>