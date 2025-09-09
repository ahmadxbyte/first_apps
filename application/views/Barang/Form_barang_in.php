<?php
$gutama = $this->M_global->getData('m_gudang', ['utama' => 1]);

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

<form method="post" id="form_barang_in">
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
                                        <input type="text" class="form-control" placeholder="Otomatis" id="invoice" name="invoice" value="<?= (!empty($data_barang_in) ? $data_barang_in->invoice : '') ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="invoice" class="control-label">Pre Order (PO)</label>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <input type="checkbox" class="form-control" id="cek_po" name="cek_po" onclick="cekPo('cek_po')" <?= (!empty($data_barang_in) ? (($data_barang_in->invoice_po == '') ? '' : 'checked') : '') ?>>
                                            </div>
                                            <div class="col-md-10">
                                                <select name="invoice_po" id="invoice_po" class="form-control select2_global" data-placeholder="~ Pilih No PO" <?= (!empty($data_barang_in) ? (($data_barang_in->invoice_po == '') ? 'disabled' : '') : 'disabled') ?> onchange="getPo(this.value)">
                                                    <option value="">~ Pilih No PO</option>
                                                    <?php if (!empty($data_barang_in)) : ?>
                                                        <?php foreach ($barang_po_in_x as $bpox) : ?>
                                                            <option value="<?= $bpox->invoice ?>" <?= ($bpox->invoice == $data_barang_in->invoice_po) ? 'selected' : '' ?>><?= $bpox->invoice . ' | Tgl/jam: ' . date('D, m-Y', strtotime($bpox->tgl_po)) . '/' . date('H:i:s', strtotime($bpox->jam_po)) ?></option>
                                                        <?php endforeach; ?>
                                                    <?php else : ?>
                                                        <?php foreach ($barang_po_in as $bpo) : ?>
                                                            <option value="<?= $bpo->invoice ?>"><?= $bpo->invoice . ' | Tgl/jam: ' . date('D, m-Y', strtotime($bpo->tgl_po)) . '/' . date('H:i:s', strtotime($bpo->jam_po)) ?></option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6 col-6">
                                        <label for="tgl_beli" class="control-label">Tgl Beli</label>
                                        <input type="date" title="Tgl Beli" class="form-control" placeholder="Tgl Beli" id="tgl_beli" name="tgl_beli" value="<?= (!empty($data_barang_in) ? date('Y-m-d', strtotime($data_barang_in->tgl_beli)) : date('Y-m-d')) ?>" readonly>
                                    </div>
                                    <div class="col-md-6 col-6">
                                        <label for="jam_beli" class="control-label">Jam Beli</label>
                                        <input type="time" title="Jam Beli" class="form-control" placeholder="Jam Beli" id="jam_beli" name="jam_beli" value="<?= (!empty($data_barang_in) ? date('H:i:s', strtotime($data_barang_in->jam_beli)) : date('H:i:s')) ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="kode_supplier" class="control-label">Pemasok <sup class="text-danger">**</sup></label>
                                <select name="kode_supplier" id="kode_supplier" class="form-control select2_supplier" data-placeholder="~ Pilih Pemasok">
                                    <?php
                                    if (!empty($data_barang_in)) :
                                        $supplier = $this->M_global->getData('m_supplier', ['kode_supplier' => $data_barang_in->kode_supplier])->nama;
                                        echo '<option value="' . $data_barang_in->kode_supplier . '">' . $supplier . '</option>';
                                    endif;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="kode_gudang" class="control-label">Gudang <sup class="text-danger">**</sup></label>
                                <select name="kode_gudang" id="kode_gudang" class="form-control select2_gudang_int" data-placeholder="~ Pilih Gudang">
                                    <?php
                                    if (!empty($data_barang_in)) :
                                        $gudang = $this->M_global->getData('m_gudang', ['kode_gudang' => $data_barang_in->kode_gudang])->nama;
                                        echo '<option value="' . $data_barang_in->kode_gudang . '">' . $gudang . '</option>';
                                    else :
                                        echo '<option value="' . $gutama->kode_gudang . '" selected>' . $gutama->nama . '</option>';
                                    endif;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="surat_jalan" class="control-label">Surat Jalan</label>
                                        <input type="text" class="form-control" placeholder="Otomatis" id="surat_jalan" name="surat_jalan" value="<?= (!empty($data_barang_in) ? $data_barang_in->surat_jalan : '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="no_faktur" class="control-label">No. Faktur</label>
                                        <input type="text" class="form-control" placeholder="Otomatis" id="no_faktur" name="no_faktur" value="<?= (!empty($data_barang_in) ? $data_barang_in->no_faktur : '') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="tempo" class="control-label">Jatuh Tempo <sup class="text-danger">**</sup></label>
                                        <input type="text" class="form-control text-right" name="tempo" id="tempo" value="<?= (!empty($data_barang_in) ? $data_barang_in->tempo : 0) ?>" placeholder="XX Hari">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="kirim_via" class="control-label">Dikirim Via <sup class="text-danger">**</sup></label>
                                        <select name="kirim_via" id="kirim_via" class="form-control select2_global" data-placeholder="~ Pilih Pengiriman">
                                            <option value="">~ Pilih Pengiriman</option>
                                            <option value="WA" <?= (!empty($data_barang_in) ? (($data_barang_in->kirim_via == 'WA') ? 'selected' : '') : '') ?>>WA</option>
                                            <option value="EMAIL" <?= (!empty($data_barang_in) ? (($data_barang_in->kirim_via == 'EMAIL') ? 'selected' : '') : '') ?>>EMAIL</option>
                                            <option value="SMS" <?= (!empty($data_barang_in) ? (($data_barang_in->kirim_via == 'SMS') ? 'selected' : '') : '') ?>>SMS</option>
                                            <option value="TELPON" <?= (!empty($data_barang_in) ? (($data_barang_in->kirim_via == 'TELPON') ? 'selected' : '') : '') ?>>TELPON</option>
                                        </select>
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
                    <div class="row mb-3 alert-po">
                        <div class="col-md-12">
                            <div class="alert alert-primary" role="alert" style="font-size: 11px;">
                                <div class="row">
                                    <div class="col-md-2 my-auto text-center">
                                        <i class="fa-solid fa-2x fa-triangle-exclamation"></i>
                                    </div>
                                    <div class="col-md-8 text-center">
                                        <span class="font-weight-bold"><u>PERINGATAN</u></span>
                                        <br>
                                        Jika menggunakan PO, jangan ubah satuannnya, cukup ubah qty atau hapus barangnya
                                    </div>
                                    <div class="col-md-2 my-auto text-center">
                                        <i class="fa-solid fa-2x fa-triangle-exclamation"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <input type="hidden" name="jumlahBarisBarang" id="jumlahBarisBarang" value="<?= (!empty($barang_detail) ? count($barang_detail) : '0') ?>">
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
                                        <?php if (!empty($barang_detail)) : ?>
                                            <?php $no = 1;
                                            foreach ($barang_detail as $bd) :
                                                $barang = $this->M_global->getData('barang', ['kode_barang' => $bd->kode_barang]);

                                                $satuan = [];
                                                foreach ([$barang->kode_satuan, $barang->kode_satuan2, $barang->kode_satuan3] as $satuanCode) {
                                                    $satuanDetail = $this->M_global->getData('m_satuan', ['kode_satuan' => $satuanCode]);
                                                    if ($satuanDetail) {
                                                        $satuan[] = [
                                                            'kode_satuan' => $satuanCode,
                                                            'keterangan'  => $satuanDetail->keterangan,
                                                        ];
                                                    } else {
                                                        $satuan[] = '';
                                                    }
                                                }
                                            ?>
                                                <tr id="rowBarangIn<?= $no ?>">
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-danger" type="button" id="btnHapus<?= $no ?>" onclick="hapusBarang('<?= $no ?>')"><i class="fa-solid fa-delete-left"></i></button>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" id="kode_barang_in<?= $no ?>" name="kode_barang_in[]" value="<?= $bd->kode_barang ?>">
                                                        <span><?= $bd->kode_barang ?> ~ <?= $this->M_global->getData('barang', ['kode_barang' => $bd->kode_barang])->nama ?></span>
                                                    </td>
                                                    <td>
                                                        <select name="kode_satuan[]" id="kode_satuan<?= $no ?>" class="form-control select2_global" data-placeholder="~ Pilih Satuan" onchange="ubahSatuan(this.value, <?= $no ?>)">
                                                            <option value="">~ Pilih Satuan</option>
                                                            <?php foreach ($satuan as $s) : ?>
                                                                <?php if (is_array($s)) : ?>
                                                                    <option value="<?= $s['kode_satuan'] ?>" <?= (($bd->kode_satuan == $s['kode_satuan']) ? 'selected' : '') ?>><?= $s['keterangan'] ?></option>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" id="harga_in<?= $no ?>" name="harga_in[]" value="<?= number_format($bd->harga) ?>" class="form-control text-right" onchange="hitung_st('<?= $no ?>'); formatRp(this.value, 'harga_in<?= $no ?>'); cekHarga(this.value, <?= $no ?>)">
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
                                                    <td class="text-right">
                                                        <input type="hidden" id="jumlah_in<?= $no ?>" name="jumlah_in[]" value="<?= number_format($bd->jumlah) ?>" class="form-control text-right" readonly>
                                                        <span id="jumlah2_in<?= $no ?>"><?= number_format($bd->jumlah) ?></span>
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
                        <div class="col-md-7 col-12">
                            <?php if (!empty($data_barang_in)) : ?>
                                <?php if ($data_barang_in->invoice_po == '') : ?>
                                    <div class="row search_barang">
                                        <div class="col-md-8 col-6">
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control" placeholder="Masukan Kode/Nama Barang" id="kode_barang" name="kode_barang">
                                                <div class="input-group-append" onclick="showBarang()">
                                                    <div class="input-group-text">
                                                        <i class="fa-solid fa-magnifying-glass-plus"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-6">
                                            <button type="button" class="btn btn-primary" onclick="searchBarang()" id="btnCari"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah Barang</button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php else : ?>
                                <div class="row search_barang">
                                    <div class="col-md-8 col-6">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" placeholder="Masukan Kode/Nama Barang" id="kode_barang" name="kode_barang">
                                            <div class="input-group-append" onclick="showBarang()">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <button type="button" class="btn btn-primary" onclick="searchBarang()" id="btnCari"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah Barang</button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-5 col-12">
                            <div class="card">
                                <div class="card-footer">
                                    <div class="row mb-1">
                                        <label for="subtotal" class="control-label col-md-4 col-12 my-auto">Subtotal <span class="float-right">Rp</span></label>
                                        <div class="col-md-8 col-12">
                                            <input type="text" name="subtotal" id="subtotal" class="form-control text-right" value="<?= ((!empty($data_barang_in)) ? number_format($data_barang_in->subtotal) : '0') ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <label for="diskon" class="control-label col-md-4 col-12 my-auto">Diskon <span class="float-right">Rp</span></label>
                                        <div class="col-md-8 col-12">
                                            <input type="text" name="diskon" id="diskon" class="form-control text-right" value="<?= ((!empty($data_barang_in)) ? number_format($data_barang_in->diskon) : '0') ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <label for="pajak" class="control-label col-md-4 col-12 my-auto">Pajak <span class="float-right">Rp</span></label>
                                        <div class="col-md-8 col-12">
                                            <input type="text" name="pajak" id="pajak" class="form-control text-right" value="<?= ((!empty($data_barang_in)) ? number_format($data_barang_in->pajak) : '0') ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="total" class="control-label col-md-4 col-12 my-auto">Total <span class="float-right">Rp</span></label>
                                        <div class="col-md-8 col-12">
                                            <input type="text" name="total" id="total" class="form-control text-right" value="<?= ((!empty($data_barang_in)) ? number_format($data_barang_in->total) : '0') ?>" readonly>
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
                            <button type="button" class="btn btn-danger" onclick="getUrl('Transaksi/barang_in')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                            <button type="button" class="btn btn-success float-right ml-2" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                            <?php if (!empty($data_barang_in)) : ?>
                                <button type="button" class="btn btn-info float-right" onclick="getUrl('Transaksi/form_barang_in/0')" id="btnBaru"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Baru</button>
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

<!-- modal semua barang -->
<div class="modal fade" id="modal_barang" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" <?= $style_modal ?>>
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"># List Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="tutupModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div style="height: 400px; overflow: auto;">
                            <div class="table-responsive">
                                <table class="table shadow-sm table-hover table-bordered" id="tableSederhanaObat" style="width: 100%; border-radius: 10px;">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="5%" style="border-radius: 10px 0px 0px 0px;">#</th>
                                            <th width="90%">Obat</th>
                                            <th width="5%" style="border-radius: 0px 10px 0px 0px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $nolb = 1;
                                        foreach ($list_barang as $lb) : ?>
                                            <tr>
                                                <td width="5%"><?= $nolb ?></td>
                                                <td width="90%">
                                                    <?= $lb->kode_barang . ' ~ ' . $lb->nama . ' ~ Satuan: ' . $this->M_global->getData('m_satuan', ['kode_satuan' => $lb->kode_satuan])->keterangan . ' ~ Kategori: ' . $this->M_global->getData('m_kategori', ['kode_kategori' => $lb->kode_kategori])->keterangan . ' ~ HNA: Rp. ' . number_format($lb->hna) ?>
                                                    <input type="hidden" name="selobat[]" id="selobat<?= $nolb ?>" value="<?= $lb->kode_barang ?>">
                                                </td>
                                                <td width="5%" class="text-center">
                                                    <input type="hidden" class="form-control" name="select_barang[]" id="select_barang<?= $nolb ?>" value="0">
                                                    <input type="checkbox" class="form-control" name="select_barangx[]" id="select_barangx<?= $nolb ?>" onclick="selbar('<?= $nolb ?>')">
                                                </td>
                                            </tr>
                                        <?php $nolb++;
                                        endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary float-right" onclick="selbarfunc()"><i class="fa-regular fa-circle-check"></i> Pilih Obat</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var kode_barang = $('#kode_barang');
    const form = $('#form_barang_in');
    const btnCari = $('#btnCari');
    const btnSimpan = $('#btnSimpan');
    const alert_po = $('.alert-po');

    // header
    var invoice = $('#invoice');
    var invoice_po = $('#invoice_po');
    var tgl_beli = $('#tgl_beli');
    var jam_beli = $('#jam_beli');
    var kode_supplier = $('#kode_supplier');
    var kode_gudang = $('#kode_gudang');
    var surat_jalan = $('#surat_jalan');
    var no_faktur = $('#no_faktur');
    var kirim_via = $('#kirim_via');

    // detail
    var kode_satuan = $('#kode_satuan');
    var tableBarangIn = $('#tableDetailBarangIn');
    var bodyBarangIn = $('#bodyBarangIn');
    var rowBarangIn = $('#rowBarangIn');
    var jumlahBarisBarang = $('#jumlahBarisBarang');

    alert_po.hide();

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

    // fungsi pencarian by input dan enter
    kode_barang.keypress(function(e) {
        if (e.which == 13) { // jika di enter
            // jalankan fungsi
            return searchBarang();
        }
    });

    // fungsi select barang on check
    function selbar(x) {
        if (document.getElementById('select_barangx' + x).checked == true) {
            $('#select_barang' + x).val(1);
        } else {
            $('#select_barang' + x).val(0);
        }
    }

    // tampilkan fungsi select barang
    function selbarfunc() {
        var tableBarang = $('#tableSederhanaObat').DataTable(); // ambil id table detail
        var rowCount = tableBarang.rows().count(); // hitung jumlah rownya
        var tableBarangIn = document.getElementById('tableDetailBarangIn'); // ambil id table detail
        var no = tableBarangIn.rows.length; // hitung jumlah rownya

        tableBarang.search('').draw(); // Hapus pencarian pada DataTable

        // lakukan loop
        for (var i = 1; i <= rowCount; i++) {
            if ($('#select_barang' + i).val() == 1) {
                $('#select_barang' + i).val(0);
                document.getElementById('select_barangx' + i).checked = false;
                var obat = $('#selobat' + i).val();
                $('#modal_barang').modal('hide');
                tampilList2(obat, i);
                no += 1;
                jumlahBarisBarang.val(no);
            }
        }
    }

    // fungsi tampilList2
    function tampilList2(brg, i) {
        // jalankan fungsi
        $.ajax({
            url: siteUrl + 'Transaksi/getBarang/' + brg,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) { // jika fungsi berjalan
                // reset inputan pencarian barang
                kode_barang.val('');

                if (result.status == 0) { // jika mendapatkan status 0
                    // munculkan notifikasi
                    return Swal.fire("Barang", "Tidak ditemukan!", "info");
                } else { // selain itu
                    // tambahkan jumlah row
                    var tableBarangIn = document.getElementById('tableDetailBarangIn'); // ambil id table detail
                    var jum = tableBarangIn.rows.length; // hitung jumlah rownya
                    var x = Number(jum) + 1;

                    // masukan ke body table barang in detail
                    bodyBarangIn.append(`<tr id="rowBarangIn${x}">
                        <td class="text-center">
                            <button class="btn btn-sm btn-danger" type="button" id="btnHapus${x}" onclick="hapusBarang('${x}')"><i class="fa-solid fa-delete-left"></i></button>
                        </td>
                        <td>
                            <input type="hidden" id="kode_barang_in${x}" name="kode_barang_in[]" value="${result[0].kode_barang}">
                            <span>${result[0].kode_barang} ~ ${result[0].nama}</span>
                        </td>
                        <td>
                            <select name="kode_satuan[]" id="kode_satuan${x}" class="form-control select2_global" data-placeholder="~ Pilih Satuan" onchange="ubahSatuan(this.value, ${x})"></select>
                        </td>
                        <td>
                            <input type="text" id="harga_in${x}" name="harga_in[]" value="${formatRpNoId(result[0].hna)}" class="form-control text-right" onchange="hitung_st('${x}'); formatRp(this.value, 'harga_in${x}'); cekHarga(this.value, ${x})">
                        </td>
                        <td>
                            <input type="text" id="qty_in${x}" name="qty_in[]" value="1" class="form-control text-right" onchange="hitung_st('${x}'); formatRp(this.value, 'qty_in${x}')">
                        </td>
                        <td>
                            <input type="text" id="discpr_in${x}" name="discpr_in[]" value="0" class="form-control text-right" onchange="hitung_dpr(${x}); formatRp(this.value, 'discpr_in${x}')">
                        </td>
                        <td>
                            <input type="text" id="discrp_in${x}" name="discrp_in[]" value="0" class="form-control text-right" onchange="hitung_drp(${x}); formatRp(this.value, 'discrp_in${x}')">
                        </td>
                        <td class="text-center">
                            <input type="checkbox" id="pajak_in${x}" name="pajak_in[]" class="form-control" onclick="hitung_st('${x}')">
                            <input type="hidden" id="pajakrp_in${x}" name="pajakrp_in[]" value="0">
                        </td>
                        <td class="text-right">
                            <input type="hidden" id="jumlah_in${x}" name="jumlah_in[]" value="${formatRpNoId(result[0].hna)}" class="form-control text-right" readonly>
                            <span id="jumlah2_in${x}">${formatRpNoId(result[0].hna)}</span>
                        </td>
                    </tr>`);

                    // each satuan
                    $.each(result[1], function(index, value) {
                        $('#kode_satuan' + x).append(`<option value="${value.kode_satuan}">${value.keterangan}</option>`)
                    });

                    jumlahBarisBarang.val(x);

                    $(".select2_global").select2({
                        placeholder: $(this).data('placeholder'),
                        width: '100%',
                        allowClear: true,
                    });

                    // jalankan fungsi
                    hitung_st(x);
                }
            },
            error: function(result) { // jika fungsi error

                // jalankan notifikasi error
                error_proccess();
            }
        });
    }

    // fungsi pilih barang dari modal
    function selectBarang(x) {
        // ambil angka row terakhir
        var jum = Number(jumlahBarisBarang.val());

        if (x == '' || x == null) { // jika x kosong/ null
        } else { // selain itu

            // jalankan fungsi
            $('#modal_barang').modal('hide');
            tampilList(x, jum);
        }
    }

    // fungsi pencarian barang
    function searchBarang() {
        // ambil angka row terakhir
        var jum = Number(jumlahBarisBarang.val());

        if (kode_barang.val() == '' || kode_barang.val() == null) { // jika kode_barang kosong/ null
        } else { // selain itu

            // jalankan fungsi
            tampilList(kode_barang.val(), jum);
        }
    }

    // fungsi tampilList
    function tampilList(brg, jum) {

        // jalankan fungsi
        $.ajax({
            url: siteUrl + 'Transaksi/getBarang/' + brg,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) { // jika fungsi berjalan
                // reset inputan pencarian barang
                kode_barang.val('');

                if (result.status == 0) { // jika mendapatkan status 0
                    // munculkan notifikasi
                    return Swal.fire("Barang", "Tidak ditemukan!", "info");
                } else { // selain itu
                    // tambahkan jumlah row
                    var x = jum + 1;
                    jumlahBarisBarang.val(x);

                    // masukan ke body table barang in detail
                    bodyBarangIn.append(`<tr id="rowBarangIn${x}">
                            <td class="text-center">
                                <button class="btn btn-sm btn-danger" type="button" id="btnHapus${x}" onclick="hapusBarang('${x}')"><i class="fa-solid fa-delete-left"></i></button>
                            </td>
                            <td>
                                <input type="hidden" id="kode_barang_in${x}" name="kode_barang_in[]" value="${result[0].kode_barang}">
                                <span>${result[0].kode_barang} ~ ${result[0].nama}</span>
                            </td>
                            <td>
                                <select name="kode_satuan[]" id="kode_satuan${x}" class="form-control select2_global" data-placeholder="~ Pilih Satuan" onchange="ubahSatuan(this.value, ${x})"></select>
                            </td>
                            <td>
                                <input type="text" id="harga_in${x}" name="harga_in[]" value="${formatRpNoId(result[0].hna)}" class="form-control text-right" onchange="hitung_st('${x}'); formatRp(this.value, 'harga_in${x}'); cekHarga(this.value, ${x})">
                            </td>
                            <td>
                                <input type="text" id="qty_in${x}" name="qty_in[]" value="1" class="form-control text-right" onchange="hitung_st('${x}'); formatRp(this.value, 'qty_in${x}')">
                            </td>
                            <td>
                                <input type="text" id="discpr_in${x}" name="discpr_in[]" value="0" class="form-control text-right" onchange="hitung_dpr(${x}); formatRp(this.value, 'discpr_in${x}')">
                            </td>
                            <td>
                                <input type="text" id="discrp_in${x}" name="discrp_in[]" value="0" class="form-control text-right" onchange="hitung_drp(${x}); formatRp(this.value, 'discrp_in${x}')">
                            </td>
                            <td class="text-center">
                                <input type="checkbox" id="pajak_in${x}" name="pajak_in[]" class="form-control" onclick="hitung_st('${x}')">
                                <input type="hidden" id="pajakrp_in${x}" name="pajakrp_in[]" value="0">
                            </td>
                            <td class="text-right">
                                <input type="hidden" id="jumlah_in${x}" name="jumlah_in[]" value="${formatRpNoId(result[0].hna)}" class="form-control text-right" readonly>
                                <span id="jumlah2_in${x}">${formatRpNoId(result[0].hna)}</span>
                            </td>
                        </tr>`);

                    // each satuan
                    $.each(result[1], function(index, value) {
                        $('#kode_satuan' + x).append(`<option value="${value.kode_satuan}">${value.keterangan}</option>`)
                    });

                    $(".select2_global").select2({
                        placeholder: $(this).data('placeholder'),
                        width: '100%',
                        allowClear: true,
                    });

                    // jalankan fungsi
                    hitung_st(x);
                }
            },
            error: function(result) { // jika fungsi error

                // jalankan notifikasi error
                error_proccess();
            }
        });
    }

    //fungsi untuk pengecekan menggunakan po atau tidak
    function cekPo(param) {
        $('#jumlahBarisBarang').val(1);

        // kosongkan body table barang in detail
        $('#bodyBarangIn').empty();

        $('#kode_supplier').val('').change();
        $('#kode_gudang').val('<?= $gutama->kode_gudang ?>').change();

        if (document.getElementById(`${param}`).checked == true) {
            invoice_po.attr('disabled', false);
            alert_po.fadeIn(500);
            $('.search_barang').fadeOut(500);
        } else {
            // kosongkan isi nomor po
            invoice_po.val('').change();

            invoice_po.attr('disabled', true);
            alert_po.fadeOut(500);
            $('.search_barang').fadeIn(500);
        }

        hitung_t();
    }

    var cek_param = "<?= $this->input->get('invoice') ?>";

    if (cek_param && cek_param !== '0') {
        // alert(cek_param)
        document.getElementById('cek_po').checked = true;
        cekPo('cek_po');
        $('#invoice_po').val(cek_param).change();
    }

    // Function to fetch PO data based on the PO number
    function getPo(param) {
        if (param == '' || param == null) {
            return Swal.fire("Invoice PO", "Form sudah dipilih?", "info");
        }

        $.ajax({
            url: '<?= site_url() ?>Transaksi/getPengajuan/' + param,
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) {
                if (result[0]['status'] == 1) {
                    $('#kode_supplier').html(`<option value="${result[0]['header'].kode_supplier}">${result[0]['header'].nama_supplier}</option>`);
                    $('#kode_gudang').html(`<option value="${result[0]['header'].kode_gudang}">${result[0]['header'].nama_gudang}</option>`);
                    jumlahBarisBarang.val(result[1].length);

                    let x = 1;
                    $.each(result[1], function(index, value) {
                        const cek_pajak = value.pajak > 0 ? 'checked' : '';

                        bodyBarangIn.append(`
                        <tr id="rowBarangIn${x}">
                            <td class="text-center">
                                <button class="btn btn-sm btn-danger" type="button" id="btnHapus${x}" onclick="hapusBarang('${x}')">
                                    <i class="fa-solid fa-delete-left"></i>
                                </button>
                            </td>
                            <td>
                                <input type="hidden" id="kode_barang_in${x}" name="kode_barang_in[]" value="${value.kode_barang}">
                                <span>${value.kode_barang} ~ ${value.nama}</span>
                            </td>
                            <td>
                                <input type="hidden" id="kode_satuan${x}" name="kode_satuan[]" value="${value.satuan_default}">
                                <span>${value.nama_satuan}</span>
                            </td>
                            <td>
                                <input type="text" id="harga_in${x}" name="harga_in[]" value="${formatRpNoId(value.harga)}" class="form-control text-right" onchange="hitung_st('${x}'); formatRp(this.value, 'harga_in${x}'); cekHarga(this.value, ${x})">
                            </td>
                            <td>
                                <input type="text" id="qty_in${x}" name="qty_in[]" value="${formatRpNoId(value.qty_po)}" class="form-control text-right" onchange="hitung_dpr('${x}'); formatRp(this.value, 'qty_in${x}')">
                            </td>
                            <td>
                                <input type="text" id="discpr_in${x}" name="discpr_in[]" value="${formatRpNoId(value.discpr)}" class="form-control text-right" onchange="hitung_dpr(${x}); formatRp(this.value, 'discpr_in${x}')">
                            </td>
                            <td>
                                <input type="text" id="discrp_in${x}" name="discrp_in[]" value="${formatRpNoId(value.discrp)}" class="form-control text-right" onchange="hitung_drp(${x}); formatRp(this.value, 'discrp_in${x}')">
                            </td>
                            <td class="text-center">
                                <input type="checkbox" id="pajak_in${x}" name="pajak_in[]" class="form-control" onclick="hitung_st('${x}')" ${cek_pajak}>
                                <input type="hidden" id="pajakrp_in${x}" name="pajakrp_in[]" value="${formatRpNoId(value.pajakrp)}">
                            </td>
                            <td>
                                <input type="hidden" id="jumlah_in${x}" name="jumlah_in[]" value="${formatRpNoId(value.jumlah)}" class="form-control text-right" readonly>
                                Rp. <span class="float-right" id="jumlah2_in${x}">${formatRpNoId(value.jumlah)}</span>
                            </td>
                        </tr>
                    `);

                        $(".select2_global").select2({
                            placeholder: $(this).data('placeholder'),
                            width: '100%',
                            allowClear: true,
                        });

                        // Call the function
                        hitung_st(x);
                        x++;
                    });
                } else {
                    $('#bodyBarangIn').html('');
                    $('#jumlahBarisBarang').val(1);
                    hitung_t();
                }
            },
            error: function() {
                error_proccess();
            }
        });
    }


    // fungsi ubah satuan untuk ubah harga
    function ubahSatuan(param, id) {
        var kode_barang_in = $('#kode_barang_in' + id).val();
        var kode_satuan = $('#kode_satuan' + id).val();

        if (!param || param === null) {
            error_proccess();
            return; // Add return to stop further execution
        }

        $.ajax({
            url: siteUrl + 'Transaksi/getSatuan/' + param + '/' + kode_barang_in,
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
        var discrp = ($('#discrp_in' + x).val()).replaceAll(',', '');

        if (discpr < 1 && discrp < 1) {
            return hitung_drp(x);
        }

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

        // buat rumus jumlah
        var st_awal = (harga * qty) - discrp;

        if (document.getElementById('pajak_in' + x).checked == true) { // jika pajak checked true
            // buat rumus pajak
            var pajakrp = formatRpNoId(st_awal * (Number(<?= $pajak ?>) / 100));
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

    // fungsi cek ubah harga
    function cekHarga(num, x) {
        // munculkan notifikasi
        Swal.fire("Harga Barang", "Akan diubah?, harga master akan mangikuti harga terakhir!", "question");

        // format harga
        $('#harga_in' + x).val(formatRpNoId(num));

        // ambil discpr untuk pengecekan
        var discpr = Number(($('#discpr_in' + x).val()).replaceAll(',', ''));
        if (discpr > 0) { // jika discpr lebih dari 0
            // jalankan fungsi
            hitung_dpr(x);
        } else { // selain itu
            // jalankan fungsi
            hitung_drp(x);
        }
    }

    // fungsi simpan
    function save() {
        btnSimpan.attr('disabled', true);

        var tableBarang = document.getElementById('tableDetailBarangIn'); // ambil id table detail
        var rowCount = tableBarang.rows.length; // hitung jumlah rownya

        if (rowCount < 1) { // jika jumlah baris detail kurang dari 1
            btnSimpan.attr('disabled', false);
            return Swal.fire("Detail Barang Pembelian", "Form sudah diisi?", "question");
        }

        // Validate all required fields
        if (!tgl_beli.val()) {
            btnSimpan.attr('disabled', false);
            return Swal.fire("Tgl Beli", "Form sudah diisi?", "question");
        }

        if (!jam_beli.val()) {
            btnSimpan.attr('disabled', false);
            return Swal.fire("Jam Beli", "Form sudah diisi?", "question");
        }

        if (!kode_supplier.val()) {
            btnSimpan.attr('disabled', false);
            return Swal.fire("Pemasok", "Form sudah dipilih?", "question");
        }

        if (!kode_gudang.val()) {
            btnSimpan.attr('disabled', false);
            return Swal.fire("Gudang", "Form sudah dipilih?", "question");
        }

        if (!kirim_via.val()) {
            btnSimpan.attr('disabled', false);
            return Swal.fire("Dikirim Via", "Form sudah dipilih?", "question");
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
            url: siteUrl + 'Transaksi/barang_in_proses/' + param,
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) { // jika fungsi berjalan dengan baik
                btnSimpan.attr('disabled', false);

                if (result.status == 1) { // jika mendapatkan respon 1

                    Swal.fire("Pembelian", "Berhasil " + message, "success").then(() => {
                        getUrl('Transaksi/barang_in');
                    });
                } else { // selain itu

                    Swal.fire("Pembelian", "Gagal " + message + ", silahkan dicoba kembali", "info");
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
        $('#jumlahBarisBarang').val(1);

        // Clear the body of the barang in detail table
        $('#bodyBarangIn').empty();

        // Clear and set default values for supplier and warehouse codes
        $('#kode_supplier').val('').change();
        $('#kode_gudang').val('<?= $gutama->kode_gudang ?>').change();

        // Uncheck the PO checkbox
        document.getElementById('cek_po').checked = false;

        // Clear the PO number input
        invoice_po.val('').change();

        // Disable the PO number input and show the search barang button
        invoice_po.attr('disabled', true);
        $('.search_barang').fadeIn(500);

        // Recalculate total
        hitung_t();
    }
</script>