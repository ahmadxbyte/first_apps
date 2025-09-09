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

if ($param == 'emr') {
    $pendaftaran = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);
    $emr_dok = $this->M_global->getData('emr_dok', ['no_trx' => $no_trx]);
} else {
    $pendaftaran = null;
    $emr_dok = null;
}
?>

<form method="post" id="form_barang_out">
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
                                <label for="">Invoice Penjualan</label>
                                <input type="text" class="form-control" placeholder="Otomatis" id="invoice" name="invoice" value="<?= (!empty($data_barang_out) ? $data_barang_out->invoice : '') ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="">Tgl/Jam Penjualan</label>
                                <div class="row">
                                    <div class="col-md-6 col-6">
                                        <input type="date" title="Tgl Jual" class="form-control" placeholder="Tgl Jual" id="tgl_jual" name="tgl_jual" value="<?= (!empty($data_barang_out) ? date('Y-m-d', strtotime($data_barang_out->tgl_jual)) : date('Y-m-d')) ?>" readonly>
                                    </div>
                                    <div class="col-md-6 col-6">
                                        <input type="time" title="Jam Jual" class="form-control" placeholder="Jam Jual" id="jam_jual" name="jam_jual" value="<?= (!empty($data_barang_out) ? date('H:i:s', strtotime($data_barang_out->jam_jual)) : date('H:i:s')) ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="">Poli</label>
                                <div class="row">
                                    <div class="col-md-2 col-2">
                                        <input type="checkbox" name="cek_pendaftaran" id="cek_pendaftaran" class="form-control" onclick="cekPendaftaran('<?= (($param == 'emr') ? $no_trx : ((!empty($data_barang_out)) ? $data_barang_out->no_trx : '')) ?>')">
                                    </div>
                                    <div class="col-md-10 col-10">
                                        <select name="kode_poli" id="kode_poli" class="form-control select2_poli" data-placeholder="~ Pilih Poli" onchange="getPendaftaran(this.value)">
                                            <?php
                                            if (!empty($data_barang_out)) :
                                                $poli = $this->M_global->getData('m_poli', ['kode_poli' => $data_barang_out->kode_poli])->keterangan;
                                                echo '<option value="' . $data_barang_out->kode_poli . '">' . $data_barang_out->kode_poli . ' ~ ' . $poli . '</option>';
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="">Member Terdaftar</label>
                                <select name="kode_pendaftaran" id="kode_pendaftaran" class="form-control select2_pendaftaran" data-placeholder="~ Pilih Member Terdaftar" <?= (!empty($data_barang_out) ? 'onchange="getInfoPendaftaran(this.value)"' : 'onchange="cekJual(this.value)"') ?>>
                                    <?php
                                    if (!empty($data_barang_out)) :
                                        $pendaftaran = $this->M_global->getData('pendaftaran', ['no_trx' => $data_barang_out->no_trx]);
                                        echo '<option value="' . $data_barang_out->no_trx . '">' . $data_barang_out->no_trx . ' ~ Kode Member: ' . $pendaftaran->kode_member . ' | Nama Member: ' . $this->M_global->getData('member', ['kode_member' => $data_barang_out->kode_member])->nama . '</option>';
                                    endif;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="">Dokter Poli</label>
                                <select name="kode_dokter" id="kode_dokter" class="form-control select2_dokter_poli" data-placeholder="~ Pilih Dokter Poli">
                                    <?php
                                    if (!empty($data_barang_out)) :
                                        $dokter = $this->M_global->getData('dokter', ['kode_dokter' => $data_barang_out->kode_dokter])->nama;
                                        echo '<option value="' . $data_barang_out->kode_dokter . '">' . $data_barang_out->kode_dokter . ' ~ ' . $dokter . '</option>';
                                    endif;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="">Member</label>
                                <select name="kode_member" id="kode_member" class="form-control select2_member" data-placeholder="~ Pilih Member" onchange="cekMember(this.value)">
                                    <?php
                                    if (!empty($data_barang_out)) :
                                        $member = $this->M_global->getData('member', ['kode_member' => $data_barang_out->kode_member])->nama;
                                        echo '<option value="' . $data_barang_out->kode_member . '">' . $data_barang_out->kode_member . ' ~ ' . $member . '</option>';
                                    endif;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="">Gudang</label>
                                <select name="kode_gudang" id="kode_gudang" class="form-control select2_gudang_int" data-placeholder="~ Pilih Gudang" onchange="cekGudang(this.value)">
                                    <?php
                                    if (!empty($data_barang_out)) :
                                        $gudang = $this->M_global->getData('m_gudang', ['kode_gudang' => $data_barang_out->kode_gudang])->nama;
                                        echo '<option value="' . $data_barang_out->kode_gudang . '">' . $gudang . '</option>';
                                    else :
                                        echo '<option value="' . $gutama->kode_gudang . '" selected>' . $gutama->nama . '</option>';
                                    endif;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="">Alamat Member</label>
                                <textarea name="alamat" id="alamat" class="form-control" rows="3" readonly><?= (!empty($data_barang_out) ? $data_barang_out->alamat : '') ?></textarea>
                            </div>
                        </div>
                        <?php if ($param == 'emr') : ?>
                            <div class="row mb-3">
                                <label for="" class="text-danger font-weight-bold">Racikan dari dokter</label>
                                <textarea name="eracikan" id="eracikan" class="form-control" rows="3" readonly><?= (!empty($emr_dok) ? $emr_dok->eracikan : '') ?></textarea>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Detail Barang Jual</span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <input type="hidden" name="jumlahBarisBarang" id="jumlahBarisBarang" value="<?= (!empty($barang_detail) ? count($barang_detail) : (($param == 'emr') ? count($emr_per_barang) : '0')) ?>">
                                <table class="table shadow-sm table-hover table-bordered" id="tableDetailBarangOut" width="100%" style="border-radius: 10px;">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="5%" style="border-radius: 10px 0px 0px 0px;">Hapus</th>
                                            <th width="20%">Barang</th>
                                            <th width="10%">Satuan</th>
                                            <th width="10%">Harga</th>
                                            <th width="10%">Qty</th>
                                            <th width="10%">Disc (%)</th>
                                            <th width="10%">Disc (Rp)</th>
                                            <th width="5%">Pajak</th>
                                            <th width="10%">Jumlah</th>
                                            <th width="10%" style="border-radius: 0px 10px 0px 0px;">Signa</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyBarangIn">
                                        <?php if (!empty($barang_detail)) : ?>
                                            <?php $no = 1;
                                            foreach ($barang_detail as $bd) :
                                                $barang = $this->M_global->getData('barang', ['kode_barang' => $bd->kode_barang]);
                                                if (!$barang) {
                                                    continue;  // Skip if no barang is found
                                                }

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
                                                        <input type="hidden" id="kode_barang_out<?= $no ?>" name="kode_barang_out[]" value="<?= $bd->kode_barang ?>">
                                                        <span><?= $bd->kode_barang ?> ~ <?= $barang->nama ?></span>
                                                    </td>
                                                    <td>
                                                        <select name="kode_satuan[]" id="kode_satuan<?= $no ?>" class="form-control select2_global" data-placeholder="~ Pilih Satuan" onchange="ubahSatuan(this.value, <?= $no ?>)">
                                                            <option value="">~ Pilih Satuan</option>
                                                            <?php if (!empty($satuan)): ?>
                                                                <?php foreach ($satuan as $s): ?>
                                                                    <option value="<?= $s['kode_satuan'] ?>" <?= (($bd->kode_satuan == $s['kode_satuan']) ? 'selected' : '') ?>><?= $s['keterangan'] ?></option>
                                                                <?php endforeach; ?>
                                                            <?php else: ?>
                                                                <option value="">No Satuan Available</option>
                                                            <?php endif; ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" id="harga_out<?= $no ?>" name="harga_out[]" value="<?= number_format($bd->harga) ?>" class="form-control text-right" onchange="hitung_st('<?= $no ?>'); formatRp(this.value, 'harga_out<?= $no ?>'); cekHarga(this.value, <?= $no ?>)">
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
                                                    <td class="text-right">
                                                        <input type="hidden" id="jumlah_out<?= $no ?>" name="jumlah_out[]" value="<?= number_format($bd->jumlah) ?>" class="form-control text-right" readonly>
                                                        <span id="jumlah2_out<?= $no ?>"><?= number_format($bd->jumlah) ?></span>
                                                    </td>
                                                    <td>
                                                        <input type="text" id="signa_out<?= $no ?>" name="signa_out[]" value="<?= $bd->signa ?>" class="form-control" placeholder="Jumlah X Hari">
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
                            <div class="row">
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
                        </div>
                        <div class="col-md-5 col-12">
                            <div class="card">
                                <div class="card-footer">
                                    <div class="row mb-1">
                                        <label for="subtotal" class="control-label col-md-4 col-12 my-auto">Subtotal <span class="float-right">Rp</span></label>
                                        <div class="col-md-8 col-12">
                                            <input type="text" name="subtotal" id="subtotal" class="form-control text-right" value="<?= ((!empty($data_barang_out)) ? number_format($data_barang_out->subtotal) : '0') ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <label for="diskon" class="control-label col-md-4 col-12 my-auto">Diskon <span class="float-right">Rp</span></label>
                                        <div class="col-md-8 col-12">
                                            <input type="text" name="diskon" id="diskon" class="form-control text-right" value="<?= ((!empty($data_barang_out)) ? number_format($data_barang_out->diskon) : '0') ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <label for="pajak" class="control-label col-md-4 col-12 my-auto">Pajak <span class="float-right">Rp</span></label>
                                        <div class="col-md-8 col-12">
                                            <input type="text" name="pajak" id="pajak" class="form-control text-right" value="<?= ((!empty($data_barang_out)) ? number_format($data_barang_out->pajak) : '0') ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="total" class="control-label col-md-4 col-12 my-auto">Total <span class="float-right">Rp</span></label>
                                        <div class="col-md-8 col-12">
                                            <input type="text" name="total" id="total" class="form-control text-right" value="<?= ((!empty($data_barang_out)) ? number_format($data_barang_out->total) : '0') ?>" readonly>
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
                            <button type="button" class="btn btn-danger" onclick="getUrl('Transaksi/barang_out')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                            <button type="button" class="btn btn-success float-right ml-2" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                            <?php if (!empty($data_barang_out)) : ?>
                                <button type="button" class="btn btn-info float-right" onclick="getUrl('Transaksi/form_barang_out/0')" id="btnBaru"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Baru</button>
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
                                <table class="table shadow-sm table-striped table-hover table-bordered" id="tableSederhanaObat" style="width: 100%; border-radius: 10px;">
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
                                                    <?= $lb->kode_barang . ' ~ ' . $lb->nama . ' ~ Harga Jual: Rp. ' . number_format($lb->harga_jual) . ' ~ Stok: ' . number_format($lb->stok) . ' ' . $this->M_global->getData('m_satuan', ['kode_satuan' => $lb->kode_satuan])->keterangan ?>
                                                    <input type="hidden" name="selobat[]" id="selobat<?= $nolb ?>" value="<?= $lb->kode_barang ?>">
                                                </td>
                                                <td width="5%" class="text-center">
                                                    <input type="hidden" class="form-control" name="select_barang[]" id="select_barang<?= $nolb ?>" value="0">
                                                    <input type="checkbox" class="form-control" name="select_barangx[]" id="select_barangx<?= $nolb ?>" onclick="selbar('<?= $nolb ?>')">
                                                    <!-- <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tooltip on bottom" title="Pilih" onclick="selectBarang('<?= $lb->kode_barang ?>')"><ion-icon name="checkmark-circle-outline"></ion-icon></button> -->
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
                        <button type="button" class="btn btn-primary float-right" onclick="selbarfunc()"><ion-icon name="file-tray-full-outline"></ion-icon> Pilih Obat</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var kode_barang = $('#kode_barang');
    const form = $('#form_barang_out');
    const btnCari = $('#btnCari');
    const btnSimpan = $('#btnSimpan');

    // header
    var invoice = $('#invoice');
    var tgl_jual = $('#tgl_jual');
    var jam_jual = $('#jam_jual');
    var kode_poli = $('#kode_poli');
    var kode_member = $('#kode_member');
    var kode_gudang = $('#kode_gudang');
    var kode_pendaftaran = $('#kode_pendaftaran');
    var kode_dokter = $('#kode_dokter');
    var alamat = $('#alamat');

    // detail
    var tableBarangIn = $('#tableDetailBarangOut');
    var bodyBarangIn = $('#bodyBarangIn');
    var rowBarangOut = $('#rowBarangOut');
    var jumlahBarisBarang = $('#jumlahBarisBarang');

    <?php if (!empty($emr_dok)) : ?>
        <?php if ($emr_dok->eracikan != '') : ?>
            Swal.fire("INFORMASI", "Terdapat racikan dari Dokter, mohon diisikan kedalam obat!", "info");
        <?php endif ?>
    <?php endif ?>

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
        var tableBarang = $('#tableSederhanaObat').DataTable(); // Inisialisasi DataTable dengan benar
        var rowCount = tableBarang.rows().count(); // Mendapatkan jumlah baris data
        var tableBarangIn = document.getElementById('tableDetailBarangOut'); // Ambil table detail
        var no = tableBarangIn.rows.length; // Hitung jumlah row pada table detail

        tableBarang.search('').draw(); // Hapus pencarian pada DataTable

        // Loop melalui setiap row pada tableBarang
        for (var i = 1; i <= rowCount; i++) {
            // Cek apakah barang yang dipilih adalah '1'
            if ($('#select_barang' + i).val() == 1) {
                $('#select_barang' + i).val(0); // Set nilai select_barang menjadi 0
                document.getElementById('select_barangx' + i).checked = false; // Uncheck checkbox
                var obat = $('#selobat' + i).val(); // Ambil nilai obat yang dipilih
                $('#modal_barang').modal('hide'); // Sembunyikan modal
                tampilList2(obat, no); // Tampilkan list obat dengan no yang sesuai
                no += 1; // Increment nomor baris
                jumlahBarisBarang.val(no); // Update nilai jumlahBarisBarang
            }
        }
    }


    // fungsi tampilList2
    function tampilList2(brg, i) {
        var gudang = kode_gudang.val();
        // jalankan fungsi
        $.ajax({
            url: siteUrl + 'Transaksi/getBarangGudang/' + brg + '/' + gudang,
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
                    var x = i;

                    // masukan ke body table barang in detail
                    bodyBarangIn.append(`<tr id="rowBarangOut${x}">
                        <td class="text-center">
                            <button class="btn btn-sm btn-danger" type="button" id="btnHapus${x}" onclick="hapusBarang('${x}')"><i class="fa-solid fa-delete-left"></i></button>
                        </td>
                        <td>
                            <input type="hidden" id="kode_barang_out${x}" name="kode_barang_out[]" value="${result[0].kode_barang}">
                            <span>${result[0].kode_barang} ~ ${result[0].nama}</span>
                        </td>
                        <td>
                            <select name="kode_satuan[]" id="kode_satuan${x}" class="form-control select2_global" data-placeholder="~ Pilih Satuan" onchange="ubahSatuan(this.value, ${x})"></select>
                        </td>
                        <td>
                            <input type="text" id="harga_out${x}" name="harga_out[]" value="${formatRpNoId(Number(result[0].harga_jual))}" class="form-control text-right" onchange="hitung_st('${x}'); formatRp(this.value, 'harga_out${x}'); cekHarga(this.value, ${x})">
                        </td>
                        <td>
                            <input type="text" id="qty_out${x}" name="qty_out[]" value="1" class="form-control text-right" onchange="hitung_st('${x}'); formatRp(this.value, 'qty_out${x}')">
                        </td>
                        <td>
                            <input type="text" id="discpr_out${x}" name="discpr_out[]" value="0" class="form-control text-right" onchange="hitung_dpr(${x}); formatRp(this.value, 'discpr_out${x}')">
                        </td>
                        <td>
                            <input type="text" id="discrp_out${x}" name="discrp_out[]" value="0" class="form-control text-right" onchange="hitung_drp(${x}); formatRp(this.value, 'discrp_out${x}')">
                        </td>
                        <td class="text-center">
                            <input type="checkbox" id="pajak_out${x}" name="pajak_out[]" class="form-control" onclick="hitung_st('${x}')">
                            <input type="hidden" id="pajakrp_out${x}" name="pajakrp_out[]" value="0">
                        </td>
                        <td class="text-right">
                            <input type="hidden" id="jumlah_out${x}" name="jumlah_out[]" value="${formatRpNoId(Number(result[0].harga_jual))}" class="form-control text-right" readonly>
                            <span id="jumlah2_out${x}">${formatRpNoId(Number(result[0].harga_jual))}</span>
                        </td>
                        <td>
                            <input type="text" id="signa_out${x}" name="signa_out[]" value="" class="form-control" placeholder="Jumlah X Hari">
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

    // onload
    load_first()

    function load_first() {
        if (invoice.val() == '' || invoice.val() == null) { // jika invoice kosong/ null
            // disabled id
            kode_barang.attr('disabled', false);
            btnCari.attr('disabled', false);
            btnSimpan.attr('disabled', true);

            // isi kode_poli menjadi default Kulit
            kode_poli.html(`<option value="">~ Pilih Poli</option>`);
            kode_poli.attr(`disabled`, true);
            kode_pendaftaran.html(`<option value="">~ Pilih Member Terdaftar</option>`);
            kode_pendaftaran.attr(`disabled`, true);
            kode_member.html(`<option value="U00001">U00001 ~ UMUM</option>`);
        } else { // selain itu
            // jalankan fungsi hitung_t()
            hitung_t();
        }
    }

    <?php if ($param == 'emr') : ?>
        // cekPendaftaran('<?= $no_trx ?>');
        document.getElementById('cek_pendaftaran').checked = true;

        <?php
        $poli = $this->M_global->getData('m_poli', ['kode_poli' => $pendaftaran->kode_poli]);
        ?>
        kode_poli.attr('disabled', false);
        kode_poli.html(`<option value="<?= $pendaftaran->kode_poli ?>"><?= $poli->keterangan ?></option>`);

        <?php
        $member = $this->M_global->getData('member', ['kode_member' => $pendaftaran->kode_member]);
        ?>
        kode_pendaftaran.attr('disabled', false);
        kode_pendaftaran.html(`<option value="<?= $pendaftaran->no_trx ?>"><?= $pendaftaran->no_trx . ' ~ Kode Member: ' . $pendaftaran->kode_member . ' | Nama Member: ' . $member->nama ?></option>`);

        <?php
        $dokter = $this->M_global->getData('dokter', ['kode_dokter' => $pendaftaran->kode_dokter]);
        ?>
        kode_dokter.attr('disabled', false);
        kode_dokter.html(`<option value="<?= $pendaftaran->kode_dokter ?>"><?= $pendaftaran->kode_dokter . ' ~ Dr. ' . $dokter->nama ?></option>`);

        <?php
        $member = $this->M_global->getData('member', ['kode_member' => $pendaftaran->kode_member]);
        ?>
        kode_member.attr('disabled', false);
        kode_member.html(`<option value="<?= $pendaftaran->kode_member ?>"><?= $pendaftaran->kode_member . ' ~ ' . $member->nama ?></option>`);

        <?php
        $prov           = $this->M_global->getData('m_provinsi', ['kode_provinsi' => $member->provinsi])->provinsi;
        $kab            = $this->M_global->getData('kabupaten', ['kode_kabupaten' => $member->kabupaten])->kabupaten;
        $kec            = $this->M_global->getData('kecamatan', ['kode_kecamatan' => $member->kecamatan])->kecamatan;

        $alamat         = 'Prov. ' . $prov . ', ' . $kab . ', Kec. ' . $kec . ', Ds. ' . $member->desa . ', (POS: ' . $member->kodepos . '), RT.' . $member->rt . '/RW.' . $member->rw;
        ?>
        alamat.val(`<?= $alamat ?>`);

        $.ajax({
            url: `<?= site_url('Transaksi/getBarangEmr/') . $no_trx ?>`,
            type: `POST`,
            dataType: `JSON`,
            success: function(result) {
                var x = 1;
                $.each(result, function(index, value) {
                    bodyBarangIn.append(`<tr id="rowBarangOut${x}">
                        <td class="text-center">
                            <button class="btn btn-sm btn-danger" type="button" id="btnHapus${x}" onclick="hapusBarang('${x}')"><i class="fa-solid fa-delete-left"></i></button>
                        </td>
                        <td>
                            <input type="hidden" id="kode_barang_out${x}" name="kode_barang_out[]" value="${value.kode_barang}">
                            <span>${value.kode_barang} ~ ${value.barang}</span>
                        </td>
                        <td>
                            <input type="hidden" id="kode_satuan${x}" name="kode_satuan[]" value="${value.kode_satuan}">
                            <span>${value.satuan}</span>
                        </td>
                        <td class="text-right">
                            <input type="hidden" id="harga_out${x}" name="harga_out[]" value="${value.harga_jual}">
                            <span>${formatRpNoId(Number(value.harga_jual))}</span>
                        </td>
                        <td>
                            <input type="text" id="qty_out${x}" name="qty_out[]" value="${value.qty}" class="form-control text-right" onchange="hitung_st('${x}'); formatRp(this.value, 'qty_out${x}')">
                        </td>
                        <td>
                            <input type="text" id="discpr_out${x}" name="discpr_out[]" value="0" class="form-control text-right" onchange="hitung_dpr(${x}); formatRp(this.value, 'discpr_out${x}')">
                        </td>
                        <td>
                            <input type="text" id="discrp_out${x}" name="discrp_out[]" value="0" class="form-control text-right" onchange="hitung_drp(${x}); formatRp(this.value, 'discrp_out${x}')">
                        </td>
                        <td class="text-center">
                            <input type="checkbox" id="pajak_out${x}" name="pajak_out[]" class="form-control" onclick="hitung_st('${x}')">
                            <input type="hidden" id="pajakrp_out${x}" name="pajakrp_out[]" value="0">
                        </td>
                        <td class="text-right">
                            <input type="hidden" id="jumlah_out${x}" name="jumlah_out[]" value="${formatRpNoId(Number(value.harga_jual * value.qty))}" class="form-control text-right" readonly>
                            <span id="jumlah2_out${x}">${formatRpNoId(Number(value.harga_jual * value.qty))}</span>
                        </td>
                        <td>
                            <input type="text" id="signa_out${x}" name="signa_out[]" value="${value.signa}" class="form-control" placeholder="Jumlah X Hari">
                        </td>
                    </tr>`);
                });

                // jalankan fungsi
                hitung_st(x);

                x++;
            },
            error: function(error) {
                error_proccess();
            }
        });
    <?php else : ?>
        cekPendaftaran('<?= ((!empty($data_barang_out)) ? $data_barang_out->no_trx : '') ?>');
    <?php endif; ?>

    // cek pendaftaran
    function cekPendaftaran(notrx) {
        if (notrx) {
            document.getElementById('cek_pendaftaran').checked = true;
            kode_poli.attr('disabled', false);
        } else {
            if (document.getElementById('cek_pendaftaran').checked == true) { // jika checked
                // id kode_poli di nondisabled
                kode_poli.attr('disabled', false);

                $('#kode_member').html(`<option value="">~ Pilih Member</option>`);
            } else { // selain itu
                // id (kode_poli, kode_pendaftaran) disabled, set ke default kosong
                kode_poli.attr('disabled', true);
                kode_poli.html(`<option value="">~ Pilih Poli</option>`);
                kode_pendaftaran.attr('disabled', true);
                kode_pendaftaran.html(`<option value="">~ Pilih Member Terdaftar</option>`);

                $('#kode_member').html(`<option value="U00001">U00001 ~ UMUM</option>`);
            }
        }
    }

    // fungsi cek gudang
    function cekGudang(gudang) {
        if (gudang == '' || gudang == null) { // jika gudang kosong/ null

            // disabled id yang di perluhkan
            kode_barang.attr('disabled', true);
            btnCari.attr('disabled', true);
            // munculkan notifikasi
            Swal.fire("Gudang", "Form sudah dipilih!", "question");
        } else { // selain itu
            // nondisabled id yang diperluhkan
            kode_barang.attr('disabled', false);
            btnCari.attr('disabled', false);
        }
    }

    // fungsi cek no_trx in penjualan
    function cekJual(param) {
        if (param == '' || param == null) { // jika param kosong/ null
            // jalankan fungsi reset
            reset();

            // munculkan notifikasi
            return Swal.fire("Member Terdaftar", "Form sudah dipilih?", "question");
        } else { // selain itu
            // jalankan fungsi
            $.ajax({
                url: siteUrl + 'Transaksi/cekJual/' + param,
                type: 'POST',
                dataType: 'JSON',
                success: function(result) { // jika fungsi berjalan
                    if (result.status == 1) {
                        getInfoPendaftaran(param)
                    } else {
                        // jalankan fungsi reset
                        reset();

                        // munculkan notifikasi
                        return Swal.fire("Member Terdaftar", "Sudah di masukan ke penjualan!", "info");
                    }
                },
                error: function(result) { // jika fungsi error

                    // jalankan notifikasi error
                    error_proccess();
                }
            });
        }
    }

    // fungsi ambil info pendaftaran
    function getInfoPendaftaran(param) {
        if (param == '' || param == null) { // jika param kosong/ null
            // jalankan fungsi reset
            reset();

            // munculkan notifikasi
            return Swal.fire("Member Terdaftar", "Form sudah dipilih?", "question");
        }

        // jalankan fungsi
        $.ajax({
            url: siteUrl + 'Transaksi/getInfoPendaftaran/' + param,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) { // jika fungsi berjalan
                if (result.status == 0) { // jika mendapatkan status 0
                    // jalankan reset
                    reset();

                    // munculkan notifikasi
                    Swal.fire("Member Terdaftar", "Tidak ditemukan!, silahkan coba lagi", "info");
                } else { // selain itu
                    // jalankan fungsi getAlamat by kode_member
                    getAlamat(result.kode_member);

                    // tampilkan isi ke view
                    kode_member.html(`<option value="${result.kode_member}">${result.kode_member} ~ ${result.nama_member}</option>`)
                    kode_dokter.html(`<option value="${result.kode_dokter}">${result.kode_dokter} ~ ${result.nama_dokter}</option>`)
                }
            },
            error: function(result) { // jika fungsi error

                // jalankan notifikasi error
                error_proccess();
            }
        });
    }

    // fungsi cek status member 
    function cekMember(param) {
        if (param == '' || param == null) { // jika kode_member kosong/ null
            // kosongkan kode_member dan alamat
            kode_member.html(`<option value="">~ Pilih Member</option>`);
            alamat.val('');
        } else { // selain itu
            // jalankan fungsi
            $.ajax({
                url: siteUrl + 'Transaksi/cekMember/' + param,
                type: 'POST',
                dataType: 'JSON',
                success: function(result) { // jika fungsi berjalan
                    if (result.status == 0) {
                        // jalankan fungsi getAlamat by kode_member
                        getAlamat(param);
                    } else {
                        // jalankan reset
                        reset();

                        // munculkan notifikasi
                        Swal.fire("Member", "Sudah didaftarkan!, dengan No. Transaksi <br><b>" + result.no_trx + "</b>", "info");
                    }
                },
                error: function(result) { // jika fungsi error

                    // jalankan notifikasi error
                    error_proccess();
                }
            });
        }
    }

    // fungsi ambil alamat by kode_member
    function getAlamat(param) {

        if (param == '' || param == null || param == 'U00001') {
            // kosongkan alamat
            alamat.val('');
        } else {
            // jalankan fungsi
            $.ajax({
                url: siteUrl + 'Transaksi/getAddressMember/' + param,
                type: 'POST',
                dataType: 'JSON',
                success: function(result) { // jika berjalan dengan baik
                    if (result.status == 0) { // jika mendapatkan status 0
                        // kosongkan alamat
                        alamat.val('');

                        // munculkan notifikasi
                        Swal.fire("Alamat", "Tidak ditemukan!, silahkan coba lagi", "info");
                    } else { // selain itu

                        // masukan result ke view alamat
                        alamat.val(result.alamat);
                    }
                },
                error: function(result) { // jika fungsi error

                    // jalankan notifikasi error
                    error_proccess();
                }
            });
        }
    }

    // fungsi get pendaftaran
    function getPendaftaran(poli) {
        if (poli == '' || poli == null) { // jika poli kosong/ null

            // kosongkan select2 inisial
            initailizeSelect2_pendaftaran('');
            initailizeSelect2_dokter_poli('');

            // disabled id yang diperlukan
            kode_dokter.attr('disabled', true);
            kode_pendaftaran.attr('disabled', true);
        } else { // selain itu

            // kirim param poli ke select2 inisial
            initailizeSelect2_dokter_poli(poli);
            initailizeSelect2_pendaftaran(poli);

            // nondisabled id yang diperlukan
            kode_dokter.attr('disabled', false);
            kode_pendaftaran.attr('disabled', false);
        }
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

        var gudang = kode_gudang.val();

        if (gudang == '' || gudang == null) {
            return Swal.fire("Gudang", "Form sudah dipilih?", "question");
        }

        // jalankan fungsi
        $.ajax({
            url: siteUrl + 'Transaksi/getBarangGudang/' + brg + '/' + gudang,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) { // jika fungsi berjalan
                // reset inputan pencarian barang
                kode_barang.val('');

                if (result.status == 0) { // jika mendapatkan status 0
                    // munculkan notifikasi
                    Swal.fire("Barang", "Tidak ditemukan!", "info");
                } else { // selain itu
                    if (result.akhir < 1) {
                        // munculkan notifikasi
                        return Swal.fire("Qty Stok Barang", "Kurang dari 1!", "info");
                    }

                    // tambahkan jumlah row
                    var x = jum + 1;
                    jumlahBarisBarang.val(x);

                    // masukan ke body table barang in detail
                    bodyBarangIn.append(`<tr id="rowBarangOut${x}">
                        <td class="text-center">
                            <button class="btn btn-sm btn-danger" type="button" id="btnHapus${x}" onclick="hapusBarang('${x}')"><i class="fa-solid fa-delete-left"></i></button>
                        </td>
                        <td>
                            <input type="hidden" id="kode_barang_out${x}" name="kode_barang_out[]" value="${result[0].kode_barang}">
                            <span>${result[0].kode_barang} ~ ${result[0].nama}</span>
                        </td>
                        <td>
                            <select name="kode_satuan[]" id="kode_satuan${x}" class="form-control select2_global" data-placeholder="~ Pilih Satuan" onchange="ubahSatuan(this.value, ${x})"></select>
                        </td>
                        <td>
                            <input type="text" id="harga_out${x}" name="harga_out[]" value="${formatRpNoId(Number(result[0].harga_jual))}" class="form-control text-right" onchange="hitung_st('${x}'); formatRp(this.value, 'harga_out${x}'); cekHarga(this.value, ${x})">
                        </td>
                        <td>
                            <input type="text" id="qty_out${x}" name="qty_out[]" value="1" class="form-control text-right" onchange="hitung_st('${x}'); formatRp(this.value, 'qty_out${x}')">
                        </td>
                        <td>
                            <input type="text" id="discpr_out${x}" name="discpr_out[]" value="0" class="form-control text-right" onchange="hitung_dpr(${x}); formatRp(this.value, 'discpr_out${x}')">
                        </td>
                        <td>
                            <input type="text" id="discrp_out${x}" name="discrp_out[]" value="0" class="form-control text-right" onchange="hitung_drp(${x}); formatRp(this.value, 'discrp_out${x}')">
                        </td>
                        <td class="text-center">
                            <input type="checkbox" id="pajak_out${x}" name="pajak_out[]" class="form-control" onclick="hitung_st('${x}')">
                            <input type="hidden" id="pajakrp_out${x}" name="pajakrp_out[]" value="0">
                        </td>
                        <td class="text-right">
                            <input type="hidden" id="jumlah_out${x}" name="jumlah_out[]" value="${formatRpNoId(Number(result[0].harga_jual))}" class="form-control text-right" readonly>
                            <span id="jumlah2_out${x}">${formatRpNoId(Number(result[0].harga_jual))}</span>
                        </td>
                        <td>
                            <input type="text" id="signa_out${x}" name="signa_out[]" value="" class="form-control" placeholder="Jumlah X Hari">
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

    // fungsi ubah satuan untuk ubah harga
    function ubahSatuan(param, id) {
        var kode_barang_out = $('#kode_barang_out' + id).val();
        var kode_satuan = $('#kode_satuan' + id).val();

        if (!param || param === null) {
            error_proccess();
            return; // Add return to stop further execution
        }

        $.ajax({
            url: siteUrl + 'Transaksi/getSatuan/' + param + '/' + kode_barang_out,
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) {
                var qty_satuan = Number(result.qty_satuan);
                var hna_master = Number(result.harga_jual);
                var qty = Number($('#qty_out' + id).val().replaceAll(',', ''));

                if (isNaN(qty)) qty = 0; // Ensure qty is valid

                var newHarga = hna_master * qty_satuan;
                $('#harga_out' + id).val(formatRpNoId(newHarga));

                var discpr = Number($('#discpr_out' + id).val().replaceAll(',', ''));
                var newDiskon = (discpr > 0) ? (newHarga * qty) * (discpr / 100) : ($('#discrp_out' + id).val()).replaceAll(',', '');

                $('#discrp_out' + id).val(formatRpNoId(newDiskon));
                hitung_st(id);
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
        var ttotal = tjumlah - tdiskon + tppn;

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

        var tableBarang = document.getElementById('tableDetailBarangOut'); // ambil id table detail
        var rowCount = tableBarang.rows.length; // hitung jumlah rownya

        if (rowCount < 1) { // jika jumlah baris detail kurang dari 1
            btnSimpan.attr('disabled', false);

            return Swal.fire("Detail Barang Penjualan", "Form sudah diisi?", "question");
        }

        if (tgl_jual.val() == '' || tgl_jual.val() == null) { // jika tgl_jual null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Tgl Jual", "Form sudah diisi?", "question");
        }

        if (jam_jual.val() == '' || jam_jual.val() == null) { // jika jam_jual null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Jam Jual", "Form sudah diisi?", "question");
        }

        // if (kode_dokter.val() == '' || kode_dokter.val() == null) { // jika kode_dokter null/ kosong
        //     btnSimpan.attr('disabled', false);

        //     return Swal.fire("Dokter", "Form sudah dipilih?", "question");
        // }

        if (kode_gudang.val() == '' || kode_gudang.val() == null) { // jika kode_gudang null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Gudang", "Form sudah dipilih?", "question");
        }

        if (kode_member.val() == '' || kode_member.val() == null) { // jika kode_member null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Member", "Form sudah diisi?", "question");
        }

        if (kode_member.val() != 'U00001') {
            if (alamat.val() == '' || alamat.val() == null) { // jika alamat null/ kosong
                btnSimpan.attr('disabled', false);

                return Swal.fire("Alamat", "Form sudah diisi?", "question");
            }
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
            url: siteUrl + 'Transaksi/barang_out_proses/' + param,
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) { // jika fungsi berjalan dengan baik
                btnSimpan.attr('disabled', false);

                if (result.status == 1) { // jika mendapatkan respon 1

                    Swal.fire("Penjualan", "Berhasil " + message, "success").then(() => {
                        // question_cetak(result.invoice);
                        getUrl('Transaksi/barang_out');
                    });
                } else { // selain itu

                    Swal.fire("Penjualan", "Gagal " + message + ", silahkan dicoba kembali", "info");
                }
            },
            error: function(result) { // jika fungsi error
                btnSimpan.attr('disabled', false);

                error_proccess();
            }
        });
    }

    function question_cetak(x) {
        Swal.fire({
            title: "Cetak Bukti?",
            text: 'Cetak bukti pendaftaran!',
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Cetak",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin
                window.open(siteUrl + 'Transaksi/print_barang_out/' + x, '_blank');
                getUrl('Transaksi/barang_out');
            } else {
                getUrl('Transaksi/barang_out');
            }
        });
    }

    // fungsi reset
    function reset() {
        kode_pendaftaran.html(`<option value="">~ Pilih Member Terdaftar</option>`);
        kode_dokter.html(`<option value="">~ Pilih Dokter Poli</option>`);
        kode_member.html(`<option value="">~ Pilih Member</option>`);
        kode_gudang.html(`<option value="">~ Pilih Gudang</option>`);
        alamat.val('');
        cekGudang('');
    }
</script>