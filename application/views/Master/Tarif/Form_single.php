<form method="post" id="form_tarif">
    <div class="row" data-aos="fade-down"
        data-aos-easing="linear"
        data-aos-duration="1000">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Formulir</span>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="id" class="control-label">Kode Tarif</label>
                                        <div class="input-group mb-3">
                                            <select name="kodeTarif" id="kodeTarif" class="select2_tarif_singlex form-control" data-placeholder="~ Pilih Tarif Single" onchange="getTarif(this.value)">
                                                <?php if (!empty($tarif)) : ?>
                                                    <option value="<?= $tarif->kode_tarif ?>"><?= $tarif->kode_tarif ?></option>
                                                <?php endif ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="jenis_bayar" class="control-label">Jenis Bayar <span class="text-danger">**</span></label>
                                        <div class="input-group mb-3">
                                            <select name="jenis_bayar" id="jenis_bayar" class="select2_jenis_bayar form-control" data-placeholder="~ Pilih Jenis Bayar">
                                                <?php if (!empty($tarif)) :
                                                    $jenis_bayar = $this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $tarif->jenis_bayar]);
                                                    echo '<option value="' . $jenis_bayar->kode_jenis_bayar . '">' . $jenis_bayar->keterangan . '</option>';
                                                else :
                                                    echo '<option value="JB00000001">Perorangan</option>';
                                                endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="kelas" class="control-label">Kelas <span class="text-danger">**</span></label>
                                        <div class="input-group mb-3">
                                            <select name="kelas" id="kelas" class="form-control select2_global" data-placeholder="~ Pilih Kelas">
                                                <option value="">~ Pilih Kelas</option>
                                                <option value="VVIP" <?= ((!empty($tarif)) ? (($tarif->kelas == 'VVIP') ? 'selected' : '') : '') ?>>VVIP</option>
                                                <option value="VIP" <?= ((!empty($tarif)) ? (($tarif->kelas == 'VIP') ? 'selected' : '') : '') ?>>VIP</option>
                                                <option value="1" <?= ((!empty($tarif)) ? (($tarif->kelas == '1') ? 'selected' : '') : '') ?>>1</option>
                                                <option value="2" <?= ((!empty($tarif)) ? (($tarif->kelas == '2') ? 'selected' : '') : '') ?>>2</option>
                                                <option value="3" <?= ((!empty($tarif)) ? (($tarif->kelas == '3') ? 'selected' : '') : '') ?>>3</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <label for="nama">Nama Tarif <span class="text-danger">**</span></label>
                                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan Nama" onkeyup="ubah_nama(this.value, 'nama')" value="<?= (!empty($tarif) ? $tarif->nama : '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="id" class="control-label">Kategori Tarif <sup class="text-danger">**</sup></label>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <select name="kategori" id="kategori" class="form-control select2_kategori_tarif" data-placeholder="~ Pilih Kategori">
                                                    <?php if (!empty($tarif)) :
                                                        $kategori = $this->M_global->getData('kategori_tarif', ['kode_kategori' => $tarif->kategori]); ?>
                                                        <option value="<?= $tarif->kategori; ?>"><?= $kategori->keterangan ?></option>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="button" class="btn btn-primary" data-toggle="modal" id="btnTambahModal" data-target="#modal_kategori" style="width: 100%;">
                                                    <i class="fa-solid fa-circle-plus"></i> Tambah
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="nama">Jenis Tarif</label>
                                        <input type="hidden" class="form-control" id="jenis" name="jenis" placeholder="Jenis Tarif" value="1">
                                        <input type="text" class="form-control" id="jenisx" name="jenisx" placeholder="Jenis Tarif" value="Single" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Detail</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header" style="background-color: #343a40;">
                                    <button class="btn btn-primary" id="btn-tab-jasa" type="button" onclick="tabs(1)">JASA</button>
                                    <button class="btn btn-light" id="btn-tab-bhp" type="button" onclick="tabs(2)">BHP</button>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="for1">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="row mb-3">
                                                    <div class="col-md-12">
                                                        <div class="table-responsive">
                                                            <table class="table table-striped table-bordered" id="tableDetail" width="100%" style="border-radius: 10px;">
                                                                <thead>
                                                                    <tr class="text-center">
                                                                        <th rowspan="2" style="width: 5%;">Hapus</th>
                                                                        <th rowspan="2" style="width: 35%;">Cabang</th>
                                                                        <th colspan="4" style="width: 60%;">Jasa</th>
                                                                    </tr>
                                                                    <tr class="text-center">
                                                                        <th style="width: 15%;">RS</th>
                                                                        <th style="width: 15%;">Dokter</th>
                                                                        <th style="width: 15%;">Pelayanan</th>
                                                                        <th style="width: 15%;">Poli</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="bodyDetail">
                                                                    <?php if (!empty($single_jasa)) : ?>
                                                                        <?php $no = 1;
                                                                        foreach ($single_jasa as $sj) : ?>
                                                                            <tr id="rowJasa<?= $no ?>">
                                                                                <td>
                                                                                    <button type="button" class="btn btn-dark" onclick="hapusBaris(<?= $no ?>)"><i class="fa-solid fa-delete-left"></i></button>
                                                                                </td>
                                                                                <td>
                                                                                    <select name="kode_cabang[]" id="kode_cabang<?= $no ?>" class="select2_all_cabang" data-placeholder="~ Pilih Cabang">
                                                                                        <?php $cabang = $this->M_global->getData('cabang', ['kode_cabang' => $sj->kode_cabang]); ?>
                                                                                        <option value="<?= $sj->kode_cabang ?>"><?= $cabang->cabang ?></option>
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" name="jasa_rs[]" id="jasa_rs<?= $no ?>" class="form-control text-right" value="<?= number_format($sj->jasa_rs) ?>" onkeyup="formatRp(this.value, 'jasa_rs<?= $no ?>');">
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" name="jasa_dokter[]" id="jasa_dokter<?= $no ?>" class="form-control text-right" value="<?= number_format($sj->jasa_dokter) ?>" onkeyup="formatRp(this.value, 'jasa_dokter<?= $no ?>');">
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" name="jasa_pelayanan[]" id="jasa_pelayanan<?= $no ?>" class="form-control text-right" value="<?= number_format($sj->jasa_pelayanan) ?>" onkeyup="formatRp(this.value, 'jasa_pelayanan<?= $no ?>');">
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" name="jasa_poli[]" id="jasa_poli<?= $no ?>" class="form-control text-right" value="<?= number_format($sj->jasa_poli) ?>" onkeyup="formatRp(this.value, 'jasa_poli<?= $no ?>');">
                                                                                </td>
                                                                            </tr>
                                                                        <?php $no++;
                                                                        endforeach; ?>
                                                                    <?php else : ?>
                                                                        <tr id="rowJasa1">
                                                                            <td>
                                                                                <button type="button" class="btn btn-dark" onclick="hapusBaris(1)"><i class="fa-solid fa-delete-left"></i></button>
                                                                            </td>
                                                                            <td>
                                                                                <select name="kode_cabang[]" id="kode_cabang1" class="select2_all_cabang" data-placeholder="~ Pilih Cabang"></select>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" name="jasa_rs[]" id="jasa_rs1" class="form-control text-right" value="0" onkeyup="formatRp(this.value, 'jasa_rs1');">
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" name="jasa_dokter[]" id="jasa_dokter1" class="form-control text-right" value="0" onkeyup="formatRp(this.value, 'jasa_dokter1');">
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" name="jasa_pelayanan[]" id="jasa_pelayanan1" class="form-control text-right" value="0" onkeyup="formatRp(this.value, 'jasa_pelayanan1');">
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" name="jasa_poli[]" id="jasa_poli1" class="form-control text-right" value="0" onkeyup="formatRp(this.value, 'jasa_poli1');">
                                                                            </td>
                                                                        </tr>
                                                                    <?php endif; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <input type="hidden" class="form-control" id="jumJasa" value="<?= (!empty($single_jasa) ? count($single_jasa) : '1') ?>">
                                                        <button type="button" class="btn btn-primary" onclick="tambah_jasa()"><i class="fa-solid fa-folder-plus"></i> Tambah Jasa</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="for2">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="row mb-3">
                                                    <div class="col-md-12">
                                                        <div class="table-responsive">
                                                            <table class="table table-striped table-bordered" id="tableBhp" width="100%" style="border-radius: 10px;">
                                                                <thead>
                                                                    <tr class="text-center">
                                                                        <th width="5%" style="border-radius: 10px 0px 0px 0px;">Hapus</th>
                                                                        <th width="35%">Barang</th>
                                                                        <th width="15%">Satuan</th>
                                                                        <th width="15%">Harga</th>
                                                                        <th width="15%">Qty</th>
                                                                        <th width="15%" style="border-radius: 0px 10px 0px 0px;">Jumlah</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="bodyBhp">
                                                                    <?php if (!empty($single_bhp)) : ?>
                                                                        <?php
                                                                        $nobhp = 1;
                                                                        foreach ($single_bhp as $sbhp) :
                                                                            $barang = $this->M_global->getData('barang', ['kode_barang' => $sbhp->kode_barang]);

                                                                            $satuan = [];
                                                                            foreach ([$barang->kode_satuan, $barang->kode_satuan2, $barang->kode_satuan3] as $satuanCode) {
                                                                                $satuanDetail = $this->M_global->getData('m_satuan', ['kode_satuan' => $satuanCode]);
                                                                                if ($satuanDetail) {
                                                                                    $satuan[] = [
                                                                                        'kode_satuan' => $satuanCode,
                                                                                        'keterangan' => $satuanDetail->keterangan,
                                                                                    ];
                                                                                } else {
                                                                                    $satuan[] = [
                                                                                        'kode_satuan' => '',
                                                                                        'keterangan' => '~ Pilih Satuan'
                                                                                    ];
                                                                                }
                                                                            }
                                                                        ?>
                                                                            <tr id="rowBhp<?= htmlspecialchars($nobhp) ?>">
                                                                                <td class="text-center">
                                                                                    <button class="btn btn-sm btn-danger" type="button" id="btnHapus<?= htmlspecialchars($nobhp) ?>" onclick="hapusBarang('<?= htmlspecialchars($nobhp) ?>')">
                                                                                        <i class="fa-solid fa-delete-left"></i>
                                                                                    </button>
                                                                                </td>
                                                                                <td>
                                                                                    <select name="kode_barang[]" id="kode_barang<?= htmlspecialchars($nobhp) ?>" class="form-control select2_barang" data-placeholder="~ Pilih Barang" onchange="getBarang(this.value, '<?= htmlspecialchars($nobhp) ?>')">
                                                                                        <option value="<?= htmlspecialchars($sbhp->kode_barang) ?>"><?= htmlspecialchars($barang->nama) ?></option>
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <select name="kode_satuan[]" id="kode_satuan<?= htmlspecialchars($nobhp) ?>" class="form-control select2_global" data-placeholder="~ Pilih Satuan" onchange="ubahSatuan(this.value, <?= htmlspecialchars($nobhp) ?>)">
                                                                                        <option value="">~ Pilih Satuan</option>
                                                                                        <?php foreach ($satuan as $s) : ?>
                                                                                            <option value="<?= htmlspecialchars($s['kode_satuan']) ?>" <?= ($sbhp->kode_satuan == $s['kode_satuan'] ? 'selected' : '') ?>>
                                                                                                <?= htmlspecialchars($s['keterangan']) ?>
                                                                                            </option>
                                                                                        <?php endforeach; ?>
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" id="harga<?= htmlspecialchars($nobhp) ?>" name="harga[]" value="<?= htmlspecialchars(number_format($sbhp->harga)) ?>" class="form-control text-right" readonly>
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" id="qty<?= htmlspecialchars($nobhp) ?>" name="qty[]" value="<?= htmlspecialchars(number_format($sbhp->qty)) ?>" class="form-control text-right" onchange="hitung_st('<?= htmlspecialchars($nobhp) ?>'); formatRp(this.value, 'qty<?= htmlspecialchars($nobhp) ?>')">
                                                                                </td>
                                                                                <td class="text-right">
                                                                                    <input type="text" id="jumlah<?= htmlspecialchars($nobhp) ?>" name="jumlah[]" value="<?= htmlspecialchars(number_format($sbhp->jumlah)) ?>" class="form-control text-right" readonly>
                                                                                </td>
                                                                            </tr>
                                                                        <?php
                                                                            $nobhp++;
                                                                        endforeach;
                                                                        ?>
                                                                    <?php else : ?>
                                                                        <tr id="rowBhp1">
                                                                            <td class="text-center">
                                                                                <button class="btn btn-sm btn-danger" type="button" id="btnHapus1" onclick="hapusBarang('1')"><i class="fa-solid fa-delete-left"></i></button>
                                                                            </td>
                                                                            <td>
                                                                                <select name="kode_barang[]" id="kode_barang1" class="form-control select2_barang" data-placeholder="~ Pilih Barang" onchange="getBarang(this.value, '1')"></select>
                                                                            </td>
                                                                            <td>
                                                                                <select name="kode_satuan[]" id="kode_satuan1" class="form-control select2_global" data-placeholder="~ Pilih Satuan" onchange="ubahSatuan(this.value, 1)">
                                                                                    <option value="">~ Pilih Satuan</option>
                                                                                </select>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" id="harga1" name="harga[]" value="0" class="form-control text-right" readonly>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" id="qty1" name="qty[]" value="1" class="form-control text-right" onchange="hitung_st('1'); formatRp(this.value, 'qty1')">
                                                                            </td>
                                                                            <td class="text-right">
                                                                                <input type="text" id="jumlah1" name="jumlah[]" value="0" class="form-control text-right" readonly>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endif; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <input type="hidden" class="form-control" id="jumBhp" value="<?= (!empty($single_bhp) ? count($single_bhp) : '1') ?>">
                                                        <button type="button" class="btn btn-primary" onclick="tambah_bhp()"><i class="fa-solid fa-folder-plus"></i> Tambah BHP</button>
                                                    </div>
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
                            <button type="button" class="btn btn-danger" onclick="getUrl('Master/tin_single')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                            <button type="button" class="btn btn-success float-right ml-2" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                            <?php if (!empty($tarif)) : ?>
                                <button type="button" class="btn btn-info float-right" onclick="getUrl('Master/form_tin_single/0')" id="btnBaru"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
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

<div class="modal fade" id="modal_kategori" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Kategori Tarif</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeModalKategori">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" id="form_kategori">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="inisial_kategori">Inisial <sup class="text-danger">**</sup></label>
                            <input type="text" class="form-control" id="inisial_kategori" name="inisial_kategori" placeholder="Inisial..." onkeyup="upperCase(this.value, 'inisial_kategori')" maxlength="3" max="3">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="keterangan_kategori">Keterangan <sup class="text-danger">**</sup></label>
                            <input type="text" class="form-control" id="keterangan_kategori" name="keterangan_kategori" placeholder="Keterangan..." onkeyup="ubah_nama(this.value, 'keterangan_kategori')">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-success float-right" onclick="proses_kategori()"><i class="fa fa-server"></i> Proses</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    const bodyDetail = $('#bodyDetail');
    const bodyBhp = $('#bodyBhp');
    const form_tarif = $('#form_tarif');
    const btnSimpan = $('#btnSimpan');
    var kodeTarif = $('#kodeTarif');
    var nama = $('#nama');
    var kategori = $('#kategori');
    var jenis_bayar = $('#jenis_bayar');
    const for1 = $('#for1');
    const for2 = $('#for2');
    const btn_tab_jasa = $('#btn-tab-jasa');
    const btn_tab_bhp = $('#btn-tab-bhp');
    var closeModalKategori = $('#closeModalKategori');
    var btnModal = $('#btnTambahModal');

    tabs(1);

    function tabs(param) {
        if (param == 1) {
            btn_tab_jasa.removeClass('btn-light');
            btn_tab_jasa.addClass('btn-primary');
            btn_tab_bhp.removeClass('btn-primary');
            btn_tab_bhp.addClass('btn-light');

            for1.show(200);
            for2.hide(200);
        } else {
            btn_tab_bhp.removeClass('btn-light');
            btn_tab_bhp.addClass('btn-primary');
            btn_tab_jasa.removeClass('btn-primary');
            btn_tab_jasa.addClass('btn-light');

            for1.hide(200);
            for2.show(200);
        }
    }

    function getTarif(kode) {
        $.ajax({
            url: siteUrl + 'Master/getInfoTarif/' + kode,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if (result.status == 0) {
                    error_proccess();
                } else {
                    nama.val(result.nama);
                }
            },
            error: function() {
                error_proccess();
            }
        });
    }

    function proses_kategori() {
        closeModalKategori.trigger('click')

        // Check if the keterangan_kategori is empty or null
        if ($('#inisial_kategori').val() === '' || $('#inisial_kategori').val() === null) {
            // Show alert and re-show the modal
            Swal.fire({
                title: "Inisial Kategori",
                text: "Form sudah diisi?",
                icon: "question"
            }).then((result) => {
                if (result.isConfirmed) {
                    btnModal.trigger('click');
                }
            });
            return;
        }

        if ($('#keterangan_kategori').val() === '' || $('#keterangan_kategori').val() === null) {
            // Show alert and re-show the modal
            Swal.fire({
                title: "Keterangan Kategori",
                text: "Form sudah diisi?",
                icon: "question"
            }).then((result) => {
                if (result.isConfirmed) {
                    btnModal.trigger('click');
                }
            });
            return;
        }

        if ($('#jenis_bayar').val() === '' || $('#jenis_bayar').val() === null) {
            // Show alert and re-show the modal
            Swal.fire({
                title: "Jenis Bayar",
                text: "Form sudah dipilih?",
                icon: "question"
            });
            return;
        }

        // AJAX request
        $.ajax({
            url: siteUrl + 'Master/add_kategori_tarif',
            type: 'POST',
            dataType: 'JSON',
            data: $('#form_kategori').serialize(),
            success: function(result) {
                if (result.status == 1) {
                    Swal.fire({
                        title: "Kategori Tarif",
                        text: "Berhasil dibuat, silahkan dipilih!",
                        icon: "success"
                    }).then(() => {
                        $('#inisial_kategori').val('');
                        $('#keterangan_kategori').val('');

                        closeModalKategori.trigger('click')
                    });
                } else {
                    Swal.fire({
                        title: "Kategori Tarif",
                        text: "Gagal dibuat, silahkan coba lagi!",
                        icon: "info"
                    });
                }
            },
            error: function() {
                // Call the error processing function
                error_proccess();

                // Show the modal again if there's an error
                btnModal.trigger('click');
            }
        });
    }


    function tambah_jasa() {
        var jum = Number($('#jumJasa').val());
        var row = jum + 1;

        $('#jumJasa').val(row);
        bodyDetail.append(`<tr id="rowJasa${row}">
            <td>
                <button type="button" class="btn btn-dark" onclick="hapusBaris(${row})"><i class="fa-solid fa-delete-left"></i></button>
            </td>
            <td>
                <select name="kode_cabang[]" id="kode_cabang${row}" class="select2_all_cabang" data-placeholder="~ Pilih Cabang"></select>
            </td>
            <td>
                <input type="text" name="jasa_rs[]" id="jasa_rs${row}" class="form-control text-right" value="0" onkeyup="formatRp(this.value, 'jasa_rs${row}');">
            </td>
            <td>
                <input type="text" name="jasa_dokter[]" id="jasa_dokter${row}" class="form-control text-right" value="0" onkeyup="formatRp(this.value, 'jasa_dokter${row}');">
            </td>
            <td>
                <input type="text" name="jasa_pelayanan[]" id="jasa_pelayanan${row}" class="form-control text-right" value="0" onkeyup="formatRp(this.value, 'jasa_pelayanan${row}');">
            </td>
            <td>
                <input type="text" name="jasa_poli[]" id="jasa_poli${row}" class="form-control text-right" value="0" onkeyup="formatRp(this.value, 'jasa_poli${row}');">
            </td>
        </tr>`);

        initailizeSelect2_all_cabang();
    }

    // fungsi hapus baris card
    function hapusBaris(row) {
        $('#rowJasa' + row).remove();
    }

    $(".select2_global").select2({
        placeholder: $(this).data('placeholder'),
        width: '100%',
        allowClear: true,
    });

    function tambah_bhp() {
        var jum = Number($('#jumBhp').val());
        var row = jum + 1;

        $('#jumBhp').val(row);
        bodyBhp.append(`<tr id="rowBhp${row}">
            <td class="text-center">
                <button class="btn btn-sm btn-danger" type="button" id="btnHapus${row}" onclick="hapusBarang('${row}')"><i class="fa-solid fa-delete-left"></i></button>
            </td>
            <td>
                <select name="kode_barang[]" id="kode_barang${row}" class="form-control select2_barang" data-placeholder="~ Pilih Barang" onchange="getBarang(this.value, '${row}')"></select>
            </td>
            <td>
                <select name="kode_satuan[]" id="kode_satuan${row}" class="form-control select2_global" data-placeholder="~ Pilih Satuan" onchange="ubahSatuan(this.value, ${row})">
                    <option value="">~ Pilih Satuan</option>
                </select>
            </td>
            <td>
                <input type="text" id="harga${row}" name="harga[]" value="0" class="form-control text-right" readonly>
            </td>
            <td>
                <input type="text" id="qty${row}" name="qty[]" value="1" class="form-control text-right" onchange="hitung_st('${row}'); formatRp(this.value, 'qty${row}')">
            </td>
            <td class="text-right">
                <input type="text" id="jumlah${row}" name="jumlah[]" value="0" class="form-control text-right" readonly>
            </td>
        </tr>`);

        initailizeSelect2_barang();

        $(".select2_global").select2({
            placeholder: $(this).data('placeholder'),
            width: '100%',
            allowClear: true,
        });
    }

    // fungsi hapus baris card
    function hapusBarang(row) {
        $('#rowBhp' + row).remove();
    }

    function reseting() {
        kodeTarif.val('');
        nama.val('');
        kategori.val('').change();
        jenis_bayar.val('').change();
        $('#jumJasa').val(0);
        bodyDetail.empty();
        tambah_jasa();
        $('#jumBhp').val(0);
        bodyBhp.empty();
        tambah_bhp();
    }

    // fungsi getBarang
    function getBarang(brg, i) {
        var qty = ($('#qty' + i).val()).replaceAll(',', '');
        $('#kode_satuan' + i).empty();
        $('#harga' + i).val(0);
        $('#jumlah' + i).val(0);
        // jalankan fungsi
        $.ajax({
            url: siteUrl + 'Transaksi/getBarang/' + brg,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) { // jika fungsi berjalan
                $('#harga' + i).val(formatRpNoId(result[0].hna));
                $('#jumlah' + i).val(formatRpNoId(result[0].hna * qty));

                // each satuan
                $.each(result[1], function(index, value) {
                    $('#kode_satuan' + i).append(`<option value="${value.kode_satuan}">${value.keterangan}</option>`)
                });
            },
            error: function(result) { // jika fungsi error

                // jalankan notifikasi error
                error_proccess();
            }
        });
    }

    // fungsi ubah satuan untuk ubah harga
    function ubahSatuan(param, id) {
        var kode_barang = $('#kode_barang' + id).val();
        var kode_satuan = $('#kode_satuan' + id).val();

        if (!param || param === null) {
            error_proccess();
            return; // Add return to stop further execution
        }

        $.ajax({
            url: siteUrl + 'Transaksi/getSatuan/' + param + '/' + kode_barang,
            type: "POST",
            data: form_tarif.serialize(),
            dataType: "JSON",
            success: function(result) {
                var qty_satuan = Number(result.qty_satuan);
                var hna_master = Number(result.hna);
                var qty = Number($('#qty' + id).val().replaceAll(',', ''));

                if (isNaN(qty)) qty = 0; // Ensure qty is valid

                var newHarga = hna_master * qty_satuan;
                $('#harga' + id).val(formatRpNoId(newHarga));

                hitung_st(id);
            },
            error: function(result) {
                error_proccess();
            }
        });
    }

    // perhitungan row
    function hitung_st(x) {
        var harga = ($('#harga' + x).val()).replaceAll(',', '');
        var qty = ($('#qty' + x).val()).replaceAll(',', '');

        // buat rumus jumlah
        var st_awal = (harga * qty);

        // tampilkan hasil ke dalam format koma
        $('#jumlah' + x).val(formatRpNoId(st_awal));
    }

    function save() {
        btnSimpan.attr('disabled', true);

        // if (kodeTarif.val() == '' || kodeTarif.val() == null) {
        //     var param = 1;
        // } else {
        //     var param = 2;
        // }

        if (nama.val() == '' || nama.val() == null) {
            btnSimpan.attr('disabled', false);

            Swal.fire("Nama Tarif", "Form sudah diisi?", "question");

            return
        }

        if (kategori.val() == '' || kategori.val() == null) {
            btnSimpan.attr('disabled', false);

            Swal.fire("Kategori Tarif", "Form sudah dipilih?", "question");

            return
        }

        $.ajax({
            url: siteUrl + 'Master/cekTarifSingle',
            type: 'POST',
            dataType: 'JSON',
            data: form_tarif.serialize(),
            success: function(result) {
                if (result.status == 1) {
                    param = 1;
                } else {
                    param = 2;
                }

                proses(param);
            },
            error: function(result) {
                btnSimpan.attr('disabled', false);
                error_proccess();
            }
        });

    }

    function proses() {
        if (param == 1) {
            var message = 'buat';
        } else {
            var message = 'perbarui';
        }

        $.ajax({
            url: siteUrl + 'Master/tin_single_proses/' + param,
            type: 'POST',
            dataType: 'JSON',
            data: form_tarif.serialize(),
            success: function(result) {
                btnSimpan.attr('disabled', false);

                if (result.status == 1) {
                    Swal.fire("Tarif Single", "Berhasil di" + message + "!", "success").then(() => {
                        getUrl('Master/tin_single');
                    });
                } else {
                    Swal.fire("Tarif Single", "Gagal di" + message + "!, silahkan dicoba lagi", "info");
                }
            },
            error: function(result) {
                btnSimpan.attr('disabled', false);
                error_proccess();
            }
        })
    }

    function showGuide() {
        // clean text
        $('#modal_mgLabel').text(``);
        $('#modal-isi').text(``);

        $('#modal_mg').modal('show'); // show modal

        // isi text
        $('#modal_mgLabel').append(`Manual Guide Master Logistik`);
        $('#modal-isi').append(`
            <ol>
                <li style="font-weight: bold;">Tambah Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Tambah</li>
                        <li>Selanjutnya isikan Form yang tersedia<br>Tanda (<span style="color: red;">**</span>) mengartikan wajib terisi</li>
                        <li>Isi Tab Jasa dan BHP (disesuaikan)</li>
                        <li>Klik tombol Proses</li>
                    </ul>
                </p>
                <li style="font-weight: bold;">Ubah Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Ubah pada list data yang ingin di ubah</li>
                        <li>Ubah isi Form yang akan di ubah<br>Tanda (<span style="color: red;">**</span>) mengartikan wajib terisi</li>
                        <li>Klik tombol Proses</li>
                    </ul>
                </p>
                <li style="font-weight: bold;">Hapus Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Hapus pada list data yang ingin di hapus</li>
                        <li>Saat Muncul Pop Up, klik "Ya, Hapus"</li>
                    </ul>
                </p>
            </ol>
        `);
    }
</script>