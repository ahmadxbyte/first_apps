<?php
$member = $this->M_global->getData('member', ['kode_member' => $pendaftaran->kode_member]);

$cek_session = $this->session->userdata('kode_user');
$cek_sess_dokter = $this->M_global->getData('dokter', ['kode_dokter' => $cek_session]);

$cek_jual = $this->M_global->getData('barang_out_header', ['no_trx' => $no_trx]);
if ($cek_jual) {
    $btn_diss = 'disabled';
    $readonly = 'readonly';
} else {
    $btn_diss = '';
    $readonly = '';
}

if ($pendaftaran->status_trx == 1) {
    $btn_sv = '';
} else {
    $btn_sv = '<button type="button" class="btn btn-success float-right ml-2" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>';
}

$kode_memberx = $pendaftaran->kode_member;

$last_notrx = $this->db->query('SELECT * FROM pendaftaran WHERE kode_member = ? ORDER BY id DESC LIMIT 1', [$kode_memberx])->row();

if ($last_notrx) {
    $riwayat = $this->db->query('SELECT * FROM emr_dok WHERE kode_member = ? AND no_trx <> ? ORDER BY id DESC', [$kode_memberx, $last_notrx->no_trx])->result();

    if (!empty($riwayat)) {
        $p_kel = [];
        $alr = [];
        foreach ($riwayat as $rwt) {
            if (!empty($rwt->penyakit_keluarga)) {
                $p_kel[] = $rwt->penyakit_keluarga;
            }
            if (!empty($rwt->alergi)) {
                $alr[] = $rwt->alergi;
            }
        }
    } else {
        $p_kel = '';
        $alr = '';
    }
} else {
    $p_kel = '';
    $alr = '';
}

if (is_array($p_kel) && !empty($p_kel)) {
    $p_kel = implode(", ", $p_kel);
} else {
    $p_kel = empty($p_kel) ? '' : $p_kel;
}

if (is_array($alr) && !empty($alr)) {
    $alr = implode(", ", $alr);
} else {
    $alr = empty($alr) ? '' : $alr;
}

$paketan = $this->db->query('SELECT tp.*, mt.keterangan FROM tarif_paket_pasien tp JOIN m_tindakan mt USING(kode_tindakan) WHERE tp.no_trx = "' . $no_trx . '"')->result();

if ($web->ct_theme == 1) {
    $style = 'style="background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);"';
    $style2 = 'style="backdrop-filter: blur(10px);"';
    $style3 = 'style="background: transparent;"';
    $style_modal = 'style="background-color: rgba(255, 255, 255, 0.4); -webkit-backdrop-filter: blur(10px); backdrop-filter: blur(4px);"';
    $style_fixed = 'style="background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); position: fixed; width: 20%"';
} else if ($web->ct_theme == 2) {
    $style = 'style="background: rgba(30, 30, 30, 0.8); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);"';
    $style2 = 'style="backdrop-filter: blur(10px);"';
    $style3 = 'style="background: transparent;"';
    $style_modal = 'style="background-color: rgba(30, 30, 30, 0.9); -webkit-backdrop-filter: blur(30px); backdrop-filter: blur(5px);"';
    $style_fixed = 'style="background: rgba(30, 30, 30, 0.8); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(30px); position: fixed; width: 20%"';
} else {
    $style = '';
    $style2 = '';
    $style3 = '';
    $style_modal = '';
    $style_fixed = 'style="background-color: white; position: fixed; width: 20%"';
}
?>

<style>
    /* CSS untuk .main-sidebar */
    .main-sidebar2 {
        position: relative;
        top: 10;
        left: 5 !important;
        width: 100%;
        height: 100vh;
        /* max-height: 80vh; */
        max-height: calc(100vh - 170px);
        z-index: 1030;
        overflow-y: auto;
        border-radius: 5px !important;
        display: flex;
        flex-direction: column;
    }

    .main-sidebar2 .card-body {
        flex: 1 1 auto;
        overflow-y: auto;
    }

    @media (max-width: 991.98px) {
        .main-sidebar2 {
            width: 100%;
        }
    }
</style>

<?php foreach ($hist_member as $hm) : ?>
    <div id="popup<?= $hm->id ?>">
        <div class="card shadow card-lg" <?= $style ?>>
            <div class="card-header card-draggable<?= $hm->id ?>">
                <span class="h4">
                    Riwayat Pasien - Perawat
                    <i type="button" class="fa fa-times float-right" onclick="close_popup('<?= $hm->id ?>')"></i>
                </span>
            </div>
            <div id="body_hispx<?= $hm->id ?>" style="overflow-y: scroll; overflow-x: hidden; height: calc(100vh - 200px); width: 100%; scrollbar-width: thin; scrollbar-color: rgba(0,0,0,0.2) transparent;"></div>
        </div>
    </div>

    <div id="popup2<?= $hm->id ?>">
        <div class="card shadow card-lg" <?= $style ?>>
            <div class="card-header card-draggable2<?= $hm->id ?>">
                <span class="h4">
                    Riwayat Pasien - Dokter
                    <i type="button" class="fa fa-times float-right" onclick="close_popup2('<?= $hm->id ?>')"></i>
                </span>
            </div>
            <div id="body_hispx2<?= $hm->id ?>" style="overflow-y: scroll; overflow-x: hidden; height: calc(100vh - 200px); width: 100%; scrollbar-width: thin; scrollbar-color: rgba(0,0,0,0.2) transparent;"></div>
        </div>
    </div>
<?php endforeach; ?>

<div class="form-container">
    <form id="form_emr_perawat">
        <input type="hidden" name="no_trx" id="no_trx" value="<?= $no_trx ?>">
        <input type="hidden" name="kode_member" id="kode_member" value="<?= $pendaftaran->kode_member ?>">
        <div class="row">
            <div class="col-md-3">
                <div class="card card-outline card-primary main-sidebar2" <?= $style_fixed ?>>
                    <div class="card-header">
                        <span class="font-weight-bold h4 text-primary">Riwayat Pasien</span>
                        <hr>
                        <select name="filter_dokter" id="filter_dokter" class="form-control select2_dokter_all" data-placeholder="~ Pilih Dokter" onchange="history_px()">
                            <?php if ($cek_sess_dokter) : ?>
                                <?php if (!empty($kode_dokter)) : ?>
                                    <option value="<?= $kode_dokter ?>">Dr. <?= $this->M_global->getData('dokter', ['kode_dokter' => $kode_dokter])->nama ?></option>
                                <?php else : ?>
                                    <option value="<?= ((!empty($pendaftaran)) ? $pendaftaran->kode_dokter : '') ?>"><?= ((!empty($pendaftaran)) ? 'Dr. ' . $this->M_global->getData('dokter', ['kode_dokter' => $pendaftaran->kode_dokter])->nama : '') ?></option>
                                <?php endif ?>
                            <?php endif ?>
                        </select>
                    </div>
                    <div class="card-body" style="height: calc(100vh - 400px); overflow: hidden;">
                        <div id="body_history" style="height: 100%; overflow-y: auto; overflow-x: hidden; scrollbar-width: thin; scrollbar-color: rgba(0,0,0,0.2) transparent;"></div>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-danger w-100" onclick="getUrl('Emr')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card card-outline card-primary" <?= $style ?>>
                    <div class=" card-header">
                        <span class="font-weight-bold h4 text-primary"><i class="fa-solid fa-bookmark text-primary"></i> EMR Perawat</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-3">
                                    <label for="" class="form-label col-md-12">Nomor RM</label>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" id="kode_member2" name="kode_member2" value="<?= (($pendaftaran) ? (($member) ? $member->kode_member : '') : '') ?>" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="" class="form-label col-md-12">Nama</label>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" id="name_member" name="name_member" value="<?= (($pendaftaran) ? (($member) ? $member->nama : '') : '') ?>" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="" class="form-label col-md-12">Tempat / Tgl Lahir</label>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <input type="text" class="form-control" id="tmp_lahir" name="tmp_lahir" value="<?= (($pendaftaran) ? (($member) ? $member->tmp_lahir : '') : '') ?>" readonly>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir" value="<?= (($pendaftaran) ? (($member) ? $member->tgl_lahir : '') : '')  ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="" class="form-label col-md-12">Gender / Umur</label>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="hidden" class="form-control" id="jkel" name="jkel" value="<?= (($pendaftaran) ? (($member) ? $member->jkel : '') : '') ?>" readonly>
                                                <input type="text" class="form-control" id="jkel1" name="jkel1" value="<?= (($pendaftaran) ? (($member) ? (($member->jkel == 'P') ? 'Pria' : 'Wanita') : '') : '') ?>" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" id="umur" name="umur" value="<?= (($pendaftaran) ? (($member) ? hitung_umur($member->tgl_lahir) : '0 Tahun') : '0 Tahun') ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="" class="form-label col-md-12">Alamat</label>
                                    <div class="col-md-12">
                                        <?php
                                        $prov           = $this->M_global->getData('m_provinsi', ['kode_provinsi' => $member->provinsi])->provinsi;
                                        $kab            = $this->M_global->getData('kabupaten', ['kode_kabupaten' => $member->kabupaten])->kabupaten;
                                        $kec            = $this->M_global->getData('kecamatan', ['kode_kecamatan' => $member->kecamatan])->kecamatan;

                                        $alamat         = 'Prov. ' . $prov . ', ' . $kab . ', Kec. ' . $kec . ', Ds. ' . $member->desa . ', (POS: ' . $member->kodepos . '), RT.' . $member->rt . '/RW.' . $member->rw;
                                        ?>
                                        <textarea name="alamat" id="alamat" class="form-control" readonly rows="5"><?= $alamat ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row mb-3">
                                    <label for="" class="form-label col-md-12">Poli</label>
                                    <div class="col-md-12">
                                        <input type="hidden" class="form-control" id="kode_poli" name="kode_poli" value="<?= ($pendaftaran) ? $pendaftaran->kode_poli : '' ?>" readonly>
                                        <input type="text" class="form-control" id="kode_poli1" name="kode_poli1" value="<?= ($pendaftaran) ? $this->M_global->getData('m_poli', ['kode_poli' => $pendaftaran->kode_poli])->keterangan : '' ?>" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="" class="form-label col-md-12">Dokter</label>
                                    <div class="col-md-12">
                                        <input type="hidden" class="form-control" id="kode_dokter" name="kode_dokter" value="<?= ($pendaftaran) ? $pendaftaran->kode_dokter : '' ?>" readonly>
                                        <input type="text" class="form-control" id="kode_dokter1" name="kode_dokter1" value="Dr. <?= ($pendaftaran) ? $this->M_global->getData('dokter', ['kode_dokter' => $pendaftaran->kode_dokter])->nama : '' ?>" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="" class="form-label col-md-12">Perawat</label>
                                    <?php
                                    if (!empty($emr_per)) {
                                        $kode_per = $emr_per->kode_user;
                                    } else {
                                        $kode_per = $this->session->userdata('kode_user');
                                    }
                                    ?>
                                    <div class="col-md-12">
                                        <input type="hidden" class="form-control" id="kode_perawat" name="kode_perawat" value="<?= $kode_per ?>" readonly>
                                        <input type="text" class="form-control" id="kode_dokter1" name="kode_dokter1" value="<?= $this->M_global->getData('user', ['kode_user' => $kode_per])->nama ?>" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="" class="form-label col-md-12">Kunjungan</label>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" id="episode" name="episode" value="<?= ($pendaftaran) ? count($this->M_global->getDataResult('pendaftaran', ['kode_member' => $pendaftaran->kode_member])) : '0' ?>" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="" class="form-label col-md-12">Ruang / Bed</label>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <input type="text" class="form-control" id="kode_ruang" name="kode_ruang" value="<?= ($pendaftaran) ? $this->M_global->getData('m_ruang', ['kode_ruang' => $pendaftaran->kode_ruang])->keterangan : '' ?>" readonly>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <input type="text" class="form-control" id="kode_bed" name="kode_bed" value="<?= ($pendaftaran) ? (($pendaftaran->kode_bed != '') ? $this->M_global->getData('bed', ['kode_bed' => $pendaftaran->kode_bed])->nama_bed : '-') : '-' ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <label for="" class="form-label col-md-12">Penjamin / Kelas</label>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-8 col-12">
                                                <input type="text" class="form-control" id="kode_jenis_bayar" name="kode_jenis_bayar" value="<?= ($pendaftaran) ? $this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $pendaftaran->kode_jenis_bayar])->keterangan : '' ?>" readonly>
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <input type="text" class="form-control" id="kode_kelas" name="kode_kelas" value="<?= ($pendaftaran) ? (($pendaftaran->kelas != '') ? $this->M_global->getData('m_kelas', ['kode_kelas' => $pendaftaran->kelas])->keterangan : '-') : '-' ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer card-outline card-primary">
                        <span class="font-weight-bold h4 text-primary"><i class="fa-solid fa-bookmark text-primary"></i> Tindakan Paket</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table shadow-sm table-hover table-bordered" id="tablePaket" width="100%" style="border-radius: 10px;">
                                        <thead>
                                            <tr class="text-center">
                                                <th width="5%" style="border-radius: 10px 0px 0px 0px;">#</th>
                                                <th width="65%">Paket</th>
                                                <th width="15%">Kunjungan</th>
                                                <th width="15%" style="border-radius: 0px 10px 0px 0px;">Harga</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($paketan)) : ?>
                                                <?php $nopt = 1;
                                                foreach ($paketan as $pt) : ?>
                                                    <tr>
                                                        <td class="text-right"><?= $nopt ?></td>
                                                        <td><?= $pt->keterangan ?></td>
                                                        <td class="text-center"><?= number_format($pt->kunjungan) ?></td>
                                                        <td class="text-right"><?= number_format($pt->harga) ?></td>
                                                    </tr>
                                                <?php $nopt++;
                                                endforeach ?>
                                            <?php else : ?>
                                                <tr class="text-center">
                                                    <td colspan="4" class="text-center">Tidak Ada Paketan</td>
                                                </tr>
                                            <?php endif ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer card-outline card-primary">
                        <button type="button" id="btn_assesment" class="btn btn-primary" onclick="sel_tab_emr(1)">Assesment</button>
                        <button type="button" id="btn_pemeriksaan" class="btn" onclick="sel_tab_emr(2)">Pemeriksaan</button>
                        <button type="button" id="btn_psiko" class="btn" onclick="sel_tab_emr(3)">Psikologi & Spiritual</button>
                        <button type="button" id="btn_htt" class="btn" onclick="sel_tab_emr(4)">Head to Toe</button>
                        <button type="button" id="btn_order" class="btn" onclick="sel_tab_emr(5)">E-Order</button>
                        <button type="button" id="plafon" class="btn btn-success float-right">Total Plafon: Rp. <span id="jml_plafon">0</span></button>
                    </div>
                    <div class="card-body">
                        <div id="assesment_emr">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="sempoyongan" class="form-label col-md-12">Sempoyongan</label>
                                        <div class="col-md-12">
                                            <select name="sempoyongan" id="sempoyongan" class="form-control select2_global" data-placeholder="~ Pilih Cara Berjalan" onchange="cek_resiko()">
                                                <option value="">~ Pilih Cara Berjalan</option>
                                                <option value="0" <?= (!empty($emr_per) ? (($emr_per->sempoyongan == 0) ? 'selected' : '') : 'selected') ?>>Tidak</option>
                                                <option value="1" <?= (!empty($emr_per) ? (($emr_per->sempoyongan == 1) ? 'selected' : '') : '') ?>>Ya</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="berjalan_dgn_alat" class="form-label col-md-12">Berjalan dgn Alat</label>
                                        <div class="col-md-12">
                                            <select name="berjalan_dgn_alat" id="berjalan_dgn_alat" class="form-control select2_global" data-placeholder="~ Pilih Cara Berjalan" onchange="cek_resiko()">
                                                <option value="">~ Pilih Cara Berjalan</option>
                                                <option value="0" <?= (!empty($emr_per) ? (($emr_per->berjalan_dgn_alat == 0) ? 'selected' : '') : 'selected') ?>>Tidak</option>
                                                <option value="1" <?= (!empty($emr_per) ? (($emr_per->berjalan_dgn_alat == 1) ? 'selected' : '') : '') ?>>Ya</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="penompang" class="form-label col-md-12">Penompang duduk</label>
                                        <div class="col-md-12">
                                            <select name="penompang" id="penompang" class="form-control select2_global" data-placeholder="~ Pilih Penompang" onchange="cek_resiko()">
                                                <option value="">~ Pilih Cara Berjalan</option>
                                                <option value="0" <?= (!empty($emr_per) ? (($emr_per->penompang == 0) ? 'selected' : '') : 'selected') ?>>Tidak</option>
                                                <option value="1" <?= (!empty($emr_per) ? (($emr_per->penompang == 1) ? 'selected' : '') : '') ?>>Ya</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6"></div>
                            </div>
                            <hr>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="hasil" class="form-label col-md-12">Hasil</label>
                                        <div class="col-md-12">
                                            <input type="text" name="hasil" id="hasil" class="form-control" readonly value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="nilai" class="form-label col-md-12">Nilai</label>
                                        <div class="col-md-12">
                                            <input type="text" name="nilai" id="nilai" class="form-control" readonly value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <textarea name="keterangan_assesmnet" id="keterangan_assesmnet" class="form-control" rows="3" placeholder="Keterangan Lain"><?= (!empty($emr_per) ? $emr_per->keterangan_assesment : '') ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div id="pemeriksaan_emr">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="anamnesa_per" class="form-label col-md-12 text-danger">Anamnesa</label>
                                        <div class="col-md-12">
                                            <textarea name="anamnesa_per" id="anamnesa_per" class="form-control" rows="3" placeholder="Anamnesa Perawat..."><?= ((!empty($emr_per)) ? $emr_per->anamnesa_per : '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="diagnosa_per" class="form-label col-md-12 text-danger">Diagnosa</label>
                                        <div class="col-md-12">
                                            <textarea name="diagnosa_per" id="diagnosa_per" class="form-control" rows="3" placeholder="Diagnosa Perawat..."><?= ((!empty($emr_per)) ? $emr_per->diagnosa_per : '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="penyakit_keluarga_his" class="form-label col-md-12">Penyakit Keluarga</label>
                                        <div class="col-md-12">
                                            <input type="text" id="penyakit_keluarga_his" name="penyakit_keluarga_his" class="form-control mb-3" readonly value="<?= $p_kel ?>">
                                            <textarea name="penyakit_keluarga" id="penyakit_keluarga" class="form-control" rows="3" placeholder="Penyakit Baru..."><?= ((!empty($emr_per)) ? $emr_per->penyakit_keluarga : '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="alergi_his" class="form-label col-md-12">Alergi</label>
                                        <div class="col-md-12">
                                            <input type="text" id="alergi_his" name="alergi_his" class="form-control mb-3" readonly value="<?= $alr ?>">
                                            <textarea name="alergi" id="alergi" class="form-control" rows="3" placeholder="Alergi Baru..."><?= ((!empty($emr_per)) ? $emr_per->alergi : '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="tekanan_darah" class="form-label col-md-12">Tekanan Darah</label>
                                        <div class="col-md-12">
                                            <div class="input-group mb-3">
                                                <input type="text" id="tekanan_darah" name="tekanan_darah" class="form-control" placeholder="xxx" value="<?= ((!empty($emr_per)) ? $emr_per->tekanan_darah : '') ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" style="width: 75px;" id="basic-addon2">mmHg</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="nadi" class="form-label col-md-12">Nadi</label>
                                        <div class="col-md-12">
                                            <div class="input-group mb-3">
                                                <input type="text" id="nadi" name="nadi" class="form-control" placeholder="xxx" value="<?= ((!empty($emr_per)) ? $emr_per->nadi : '') ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" style="width: 75px;" id="basic-addon2">x/mnt</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="suhu" class="form-label col-md-12">Suhu</label>
                                        <div class="col-md-12">
                                            <div class="input-group mb-3">
                                                <input type="text" id="suhu" name="suhu" class="form-control" placeholder="xxx" value="<?= ((!empty($emr_per)) ? $emr_per->suhu : '') ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" style="width: 75px;" id="basic-addon2">°c</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="bb" class="form-label col-md-12">Berat Badan</label>
                                        <div class="col-md-12">
                                            <div class="input-group mb-3">
                                                <input type="text" id="bb" name="bb" class="form-control" placeholder="xxx" value="<?= ((!empty($emr_per)) ? $emr_per->bb : '') ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" style="width: 75px;" id="basic-addon2">Kg</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="tb" class="form-label col-md-12">Tinggi Badan</label>
                                        <div class="col-md-12">
                                            <div class="input-group mb-3">
                                                <input type="text" id="tb" name="tb" class="form-control" placeholder="xxx" value="<?= ((!empty($emr_per)) ? $emr_per->tb : '') ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" style="width: 75px;" id="basic-addon2">Cm</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="pernapasan" class="form-label col-md-12">Pernapasan</label>
                                        <div class="col-md-12">
                                            <div class="input-group mb-3">
                                                <input type="text" id="pernapasan" name="pernapasan" class="form-control" placeholder="xxx" value="<?= ((!empty($emr_per)) ? $emr_per->pernapasan : '') ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" style="width: 75px;" id="basic-addon2">x/mnt</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="saturasi" class="form-label col-md-12">Saturasi O2</label>
                                        <div class="col-md-12">
                                            <div class="input-group mb-3">
                                                <input type="text" id="saturasi" name="saturasi" class="form-control" placeholder="xxx" value="<?= ((!empty($emr_per)) ? $emr_per->saturasi : '') ?>">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" style="width: 75px;" id="basic-addon2">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="gizi" class="form-label col-md-12">Status Gizi</label>
                                        <div class="col-md-12">
                                            <select name="gizi" id="gizi" class="form-control select2_global" data-placeholder="~ Pilih Status Gizi">
                                                <option value="">~ Pilih Status Gizi</option>
                                                <option value="0" <?= (!empty($emr_per) ? (($emr_per->gizi == 0) ? 'selected' : '') : '') ?>>Gizi Buruk</option>
                                                <option value="1" <?= (!empty($emr_per) ? (($emr_per->gizi == 1) ? 'selected' : '') : '') ?>>Gizi Kurang</option>
                                                <option value="2" <?= (!empty($emr_per) ? (($emr_per->gizi == 2) ? 'selected' : '') : 'selected') ?>>Gizi Cukup</option>
                                                <option value="3" <?= (!empty($emr_per) ? (($emr_per->gizi == 3) ? 'selected' : '') : '') ?>>Gizi Lebih</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="hamil" class="form-label col-md-12">Status Hamil</label>
                                        <div class="col-md-12">
                                            <select name="hamil" id="hamil" class="form-control select2_global" data-placeholder="~ Pilih Status">
                                                <option value="">~ Pilih Status</option>
                                                <option value="0" <?= (!empty($emr_per) ? (($emr_per->hamil == 0) ? 'selected' : '') : 'selected') ?>>Tidak</option>
                                                <option value="1" <?= (!empty($emr_per) ? (($emr_per->hamil == 1) ? 'selected' : '') : '') ?>>Ya</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="hpht" class="form-label col-md-12">HPHT</label>
                                        <div class="col-md-12">
                                            <input type="date" name="hpht" id="hpht" class="form-control" value="<?= (!empty($emr_per) ? (($emr_per->hpht != null) ? date('Y-m-d', strtotime($emr_per->hpht)) : '') : '') ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <textarea name="keterangan_hamil" id="keterangan_hamil" class="form-control" rows="3" placeholder="Keterangan Hamil..."><?= (!empty($emr_per) ? $emr_per->keterangan_hamil : '') ?></textarea>
                                </div>
                            </div>
                            <hr>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <img src="<?= base_url() ?>assets/img/emr/scale.jpg" width="100%">
                                </div>
                                <div class="col-md-6">
                                    <div class="card-body">
                                        <span class="h5 font-weight-bold">Skala Nyeri</span>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <input type="hidden" id="scale" name="scale" class="form-control" value="<?= ((!empty($emr_per)) ? $emr_per->scale : '1') ?>">
                                                <table>
                                                    <tr>
                                                        <td style="width: 20%;">
                                                            <input type="checkbox" id="scale1" class="form-control" onclick="cek_scale('1')">
                                                        </td>
                                                        <td style="width: 80%;">
                                                            <label for="">&nbsp; (Skala Nyeri 0-1) Tidak Ada Rasa Sakit</label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 20%;">
                                                            <input type="checkbox" id="scale2" class="form-control" onclick="cek_scale('2')">
                                                        </td>
                                                        <td style="width: 80%;">
                                                            <label for="">&nbsp; (Skala Nyeri 2-3) Nyeri Ringan</label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 20%;">
                                                            <input type="checkbox" id="scale3" class="form-control" onclick="cek_scale('3')">
                                                        </td>
                                                        <td style="width: 80%;">
                                                            <label for="">&nbsp; (Skala Nyeri 4-5) Nyeri Sedang</label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 20%;">
                                                            <input type="checkbox" id="scale4" class="form-control" onclick="cek_scale('4')">
                                                        </td>
                                                        <td style="width: 80%;">
                                                            <label for="">&nbsp; (Skala Nyeri 6-7) Nyeri Parah</label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 20%;">
                                                            <input type="checkbox" id="scale5" class="form-control" onclick="cek_scale('5')">
                                                        </td>
                                                        <td style="width: 80%;">
                                                            <label for="">&nbsp; (Skala Nyeri 8-9) Nyeri Sangat Parah</label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 20%;">
                                                            <input type="checkbox" id="scale6" class="form-control" onclick="cek_scale('6')">
                                                        </td>
                                                        <td style="width: 80%;">
                                                            <label for="">&nbsp; (Skala Nyeri 10 >) Nyeri Sangat Buruk</label>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="psiko_emr">
                            <div class="row mb-3">
                                <div class="col-md-2 my-auto">
                                    <label for="" class="form-label">Cara Bicara</label>
                                </div>
                                <div class="col-md-10">
                                    <div class="table-responsive">
                                        <input type="hidden" id="bicara" name="bicara" class="form-control" value="<?= ((!empty($emr_per)) ? $emr_per->bicara : '1') ?>">
                                        <table cellpadding="10px" width="100%">
                                            <tr>
                                                <td style="width: 5%;">
                                                    <input type="radio" id="bicara1" class="form-control" onclick="cek_bcr(1)">
                                                </td>
                                                <td style="width: 25%;">
                                                    <span for="">&nbsp;&nbsp;&nbsp; Bicara Normal</span>
                                                </td>
                                                <td style="width: 5%;">
                                                    <input type="radio" id="bicara2" class="form-control" onclick="cek_bcr(2)">
                                                </td>
                                                <td style="width: 25%;">
                                                    <span for="">&nbsp;&nbsp;&nbsp; Bicara Terganggu</span>
                                                </td>
                                                <td style="width: 40%;">
                                                    <textarea name="gangguan_bcr" id="gangguan_bcr" class="form-control" rows="1" placeholder="Keterangan Gangguan Bicara..."><?= ((!empty($emr_per)) ? $emr_per->gangguan : '') ?></textarea>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-2 my-auto">
                                    <label for="" class="form-label">Psikologi</label>
                                </div>
                                <div class="col-md-10">
                                    <div class="table-responsive">
                                        <input type="hidden" id="emosi" name="emosi" class="form-control" value="<?= ((!empty($emr_per)) ? $emr_per->emosi : '1') ?>">
                                        <table cellpadding="10px" width="100%">
                                            <tr>
                                                <td style="width: 5%;">
                                                    <input type="radio" id="emosi1" class="form-control" onclick="cek_emosi(1)">
                                                </td>
                                                <td style="width: 25%;">
                                                    <span for="">&nbsp;&nbsp;&nbsp; Tenang</span>
                                                </td>
                                                <td style="width: 5%;">
                                                    <input type="radio" id="emosi2" class="form-control" onclick="cek_emosi(2)">
                                                </td>
                                                <td style="width: 25%;">
                                                    <span for="">&nbsp;&nbsp;&nbsp; Gelisah</span>
                                                </td>
                                                <td style="width: 5%;">
                                                    <input type="radio" id="emosi3" class="form-control" onclick="cek_emosi(3)">
                                                </td>
                                                <td style="width: 25%;">
                                                    <span for="">&nbsp;&nbsp;&nbsp; Emosional</span>
                                                </td>
                                                <td style="width: 15%;"></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-2 my-auto">
                                    <label for="" class="form-label">Spiritual</label>
                                </div>
                                <div class="col-md-10">
                                    <div class="table-responsive">
                                        <input type="hidden" id="spiritual" name="spiritual" class="form-control" value="<?= ((!empty($emr_per)) ? $emr_per->spiritual : '1') ?>">
                                        <table cellpadding="10px" width="100%">
                                            <tr>
                                                <td style="width: 5%;">
                                                    <input type="radio" id="spiritual1" class="form-control" onclick="cek_spiritual(1)">
                                                </td>
                                                <td style="width: 25%;">
                                                    <span for="">&nbsp;&nbsp;&nbsp; Berdiri</span>
                                                </td>
                                                <td style="width: 5%;">
                                                    <input type="radio" id="spiritual2" class="form-control" onclick="cek_spiritual(2)">
                                                </td>
                                                <td style="width: 25%;">
                                                    <span for="">&nbsp;&nbsp;&nbsp; Duduk</span>
                                                </td>
                                                <td style="width: 5%;">
                                                    <input type="radio" id="spiritual3" class="form-control" onclick="cek_spiritual(3)">
                                                </td>
                                                <td style="width: 25%;">
                                                    <span for="">&nbsp;&nbsp;&nbsp; Berbaring</span>
                                                </td>
                                                <td style="width: 15%;"></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="htt">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table shadow-sm table-striped table-hover table-bordered" id="table_fisik">
                                            <thead>
                                                <tr class="text-center">
                                                    <th style="width: 30%;">Bagian Tubuh</th>
                                                    <th style="width: 70%;">Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody id="body_fisik">
                                                <?php if (!empty($emr_dok_fisik)) : ?>
                                                    <?php $nof = 1;
                                                    foreach ($emr_dok_fisik as $edf) : ?>
                                                        <tr id="row_fisik<?= $nof ?>">
                                                            <td>
                                                                <input type="text" name="fisik[]" id="fisik<?= $nof ?>" readonly class="form-control" readonly value="<?= $edf->fisik ?>">
                                                            </td>
                                                            <td>
                                                                <textarea name="desc_fisik[]" id="desc_fisik<?= $nof ?>" class="form-control"><?= $edf->desc_fisik ?></textarea>
                                                            </td>
                                                        </tr>
                                                    <?php $nof++;
                                                    endforeach ?>
                                                <?php else : ?>
                                                    <tr id="row_fisik1">
                                                        <td>
                                                            <input type="text" name="fisik[]" id="fisik1" class="form-control" readonly value="Kepala">
                                                        </td>
                                                        <td>
                                                            <textarea name="desc_fisik[]" id="desc_fisik1" class="form-control"></textarea>
                                                        </td>
                                                    </tr>
                                                    <tr id="row_fisik2">
                                                        <td>
                                                            <input type="text" name="fisik[]" id="fisik2" class="form-control" readonly value="Mata">
                                                        </td>
                                                        <td>
                                                            <textarea name="desc_fisik[]" id="desc_fisik2" class="form-control"></textarea>
                                                        </td>
                                                    </tr>
                                                    <tr id="row_fisik3">
                                                        <td>
                                                            <input type="text" name="fisik[]" id="fisik3" class="form-control" readonly value="Telinga">
                                                        </td>
                                                        <td>
                                                            <textarea name="desc_fisik[]" id="desc_fisik3" class="form-control"></textarea>
                                                        </td>
                                                    </tr>
                                                    <tr id="row_fisik4">
                                                        <td>
                                                            <input type="text" name="fisik[]" id="fisik4" class="form-control" readonly value="Hidung">
                                                        </td>
                                                        <td>
                                                            <textarea name="desc_fisik[]" id="desc_fisik4" class="form-control"></textarea>
                                                        </td>
                                                    </tr>
                                                    <tr id="row_fisik5">
                                                        <td>
                                                            <input type="text" name="fisik[]" id="fisik5" class="form-control" readonly value="Rambut">
                                                        </td>
                                                        <td>
                                                            <textarea name="desc_fisik[]" id="desc_fisik5" class="form-control"></textarea>
                                                        </td>
                                                    </tr>
                                                    <tr id="row_fisik6">
                                                        <td>
                                                            <input type="text" name="fisik[]" id="fisik6" class="form-control" readonly value="Bibir">
                                                        </td>
                                                        <td>
                                                            <textarea name="desc_fisik[]" id="desc_fisik6" class="form-control"></textarea>
                                                        </td>
                                                    </tr>
                                                    <tr id="row_fisik7">
                                                        <td>
                                                            <input type="text" name="fisik[]" id="fisik7" class="form-control" readonly value="Leher">
                                                        </td>
                                                        <td>
                                                            <textarea name="desc_fisik[]" id="desc_fisik7" class="form-control"></textarea>
                                                        </td>
                                                    </tr>
                                                    <tr id="row_fisik8">
                                                        <td>
                                                            <input type="text" name="fisik[]" id="fisik8" class="form-control" readonly value="Dada">
                                                        </td>
                                                        <td>
                                                            <textarea name="desc_fisik[]" id="desc_fisik8" class="form-control"></textarea>
                                                        </td>
                                                    </tr>
                                                    <tr id="row_fisik9">
                                                        <td>
                                                            <input type="text" name="fisik[]" id="fisik9" class="form-control" readonly value="Payudara">
                                                        </td>
                                                        <td>
                                                            <textarea name="desc_fisik[]" id="desc_fisik9" class="form-control"></textarea>
                                                        </td>
                                                    </tr>
                                                    <tr id="row_fisik10">
                                                        <td>
                                                            <input type="text" name="fisik[]" id="fisik10" class="form-control" readonly value="Punggung">
                                                        </td>
                                                        <td>
                                                            <textarea name="desc_fisik[]" id="desc_fisik10" class="form-control"></textarea>
                                                        </td>
                                                    </tr>
                                                    <tr id="row_fisik11">
                                                        <td>
                                                            <input type="text" name="fisik[]" id="fisik11" class="form-control" readonly value="Perut">
                                                        </td>
                                                        <td>
                                                            <textarea name="desc_fisik[]" id="desc_fisik11" class="form-control"></textarea>
                                                        </td>
                                                    </tr>
                                                    <tr id="row_fisik12">
                                                        <td>
                                                            <input type="text" name="fisik[]" id="fisik12" class="form-control" readonly value="Lengan Atas">
                                                        </td>
                                                        <td>
                                                            <textarea name="desc_fisik[]" id="desc_fisik12" class="form-control"></textarea>
                                                        </td>
                                                    </tr>
                                                    <tr id="row_fisik13">
                                                        <td>
                                                            <input type="text" name="fisik[]" id="fisik13" class="form-control" readonly value="Lengan Bawah">
                                                        </td>
                                                        <td>
                                                            <textarea name="desc_fisik[]" id="desc_fisik13" class="form-control"></textarea>
                                                        </td>
                                                    </tr>
                                                    <tr id="row_fisik14">
                                                        <td>
                                                            <input type="text" name="fisik[]" id="fisik14" class="form-control" readonly value="Tungkai Atas">
                                                        </td>
                                                        <td>
                                                            <textarea name="desc_fisik[]" id="desc_fisik14" class="form-control"></textarea>
                                                        </td>
                                                    </tr>
                                                    <tr id="row_fisik15">
                                                        <td>
                                                            <input type="text" name="fisik[]" id="fisik15" class="form-control" readonly value="Tungkai Bawah">
                                                        </td>
                                                        <td>
                                                            <textarea name="desc_fisik[]" id="desc_fisik15" class="form-control"></textarea>
                                                        </td>
                                                    </tr>
                                                <?php endif ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="order_emr">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <button class="btn w-100 mb-1" type="button" onclick="sel_tab(0)" id="btn_etarif">Tindakan</button>
                                            <button class="btn w-100 mb-1" type="button" onclick="sel_tab(1)" id="btn_eresep">Resep</button>
                                            <button class="btn w-100 mb-1" type="button" onclick="sel_tab(2)" id="btn_elab">Laboratorium</button>
                                            <button class="btn w-100 mb-1" type="button" onclick="sel_tab(3)" id="btn_erad">Radiologi</button>
                                        </div>
                                        <div class="col-md-10">
                                            <div class="card w-100 h-100">
                                                <div class="card-header">
                                                    <span class="h4" id="title_tab" style="color: black !important;">Tindakan</span>
                                                    <span id="pl_tindakan" class="float-right font-weight-bold text-danger">Rp. <span id="plafon_tindakan">0</span></span>
                                                    <span id="pl_lab" class="float-right font-weight-bold text-danger">Rp. <span id="plafon_lab">0</span></span>
                                                    <span id="pl_rad" class="float-right font-weight-bold text-danger">Rp. <span id="plafon_rad">0</span></span>
                                                </div>
                                                <div class="card-body">
                                                    <div id="tab_etarif" style="color: black !important;">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="table-responsive">
                                                                    <table class="table shadow-sm table-hover table-bordered" id="table_etarif" width="100%" style="border-radius: 10px;">
                                                                        <thead>
                                                                            <tr class="text-center">
                                                                                <th width="5%" style="border-radius: 10px 0px 0px 0px;">Hapus</th>
                                                                                <th width="45%">Tindakan</th>
                                                                                <th width="20%">Harga</th>
                                                                                <th width="10%">Qty</th>
                                                                                <th width="20%">Total</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="body_etarif">
                                                                            <?php if (!empty($etarif)) : ?>
                                                                                <?php $no_etarif = 1;
                                                                                foreach ($etarif as $et) : ?>
                                                                                    <tr id="row_etarif<?= $no_etarif ?>">
                                                                                        <td class="text-center">
                                                                                            <button class="btn btn-sm btn-danger" type="button" id="btnHapusT<?= $no_etarif ?>" onclick="hapusTarif('<?= $no_etarif ?>')"><i class="fa-solid fa-delete-left"></i></button>
                                                                                        </td>
                                                                                        <td>
                                                                                            <select name="kode_tarif[]" id="kode_tarif<?= $no_etarif ?>" class="form-control select2_tindakan_single" data-placeholder="~ Pilih Tindakan" onchange="get_harga_tindakan('<?= $et->kode_multiprice ?>', '<?= $no_etarif ?>', 'tindakan')">
                                                                                                <?php
                                                                                                $tarif = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $et->kode_tarif]);
                                                                                                $mprice = $this->M_global->getData('multiprice_tindakan', ['kode_multiprice' => $et->kode_multiprice]);
                                                                                                ?>
                                                                                                <option value="<?= $et->kode_multiprice ?>"><?= $tarif->keterangan ?></option>
                                                                                            </select>
                                                                                        </td>
                                                                                        <td class="text-right">
                                                                                            <span id="harga_tarif<?= $no_etarif ?>" name="harga_tarif[]" class="text-right"><?= number_format($mprice->klinik + $mprice->dokter + $mprice->pelayanan + $mprice->poli) ?></span>
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" id="qty_tarif<?= $no_etarif ?>" name="qty_tarif[]" value="<?= number_format($et->qty) ?>" min="1" class="form-control text-right" onkeyup="hp_tindakan(); formatRp(this.value, 'qty_tarif<?= $no_etarif ?>')">
                                                                                        </td>
                                                                                        <td class="text-right">
                                                                                            <span id="total_tarif<?= $no_etarif ?>" name="total_tarif[]" class="text-right"><?= number_format($et->qty * ($mprice->klinik + $mprice->dokter + $mprice->pelayanan + $mprice->poli)) ?></span>
                                                                                        </td>
                                                                                    </tr>
                                                                                <?php $no_etarif++;
                                                                                endforeach ?>
                                                                            <?php else : ?>
                                                                                <tr id="row_etarif1">
                                                                                    <td class="text-center">
                                                                                        <button class="btn btn-sm btn-danger" type="button" id="btnHapusT1" onclick="hapusTarif('1')"><i class="fa-solid fa-delete-left"></i></button>
                                                                                    </td>
                                                                                    <td>
                                                                                        <select name="kode_tarif[]" id="kode_tarif1" class="form-control select2_tindakan_single" data-placeholder="~ Pilih Tindakan" onchange="get_harga_tindakan(this.value, '1', 'tindakan')">
                                                                                            <option value="">~ Pilih Tindakan</option>
                                                                                        </select>
                                                                                    </td>
                                                                                    <td class="text-right">
                                                                                        <span id="harga_tarif1" name="harga_tarif[]" class="text-right">0</span>
                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="text" id="qty_tarif1" name="qty_tarif[]" value="1" min="1" class="form-control text-right" onkeyup="hp_tindakan(); formatRp(this.value, 'qty_tarif1')">
                                                                                    </td>
                                                                                    <td class="text-right">
                                                                                        <span id="total_tarif1" name="total_tarif[]" class="text-right">0</span>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php endif; ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <button type="button" class="btn btn-primary" onclick="addTarif()" id="btnCari" <?= $btn_diss ?>><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
                                                                        <button type="button" class="btn btn-danger float-right" onclick="emptyTarif()" id="btnEmpty" <?= $btn_diss ?>><i class="fa-solid fa-trash"></i>&nbsp;&nbsp;Hapus Semua</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="tab_eresep" style="color: black !important;">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="row mb-3">
                                                                    <div class="col-md-12">
                                                                        <span class="h4">Resep</span>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="table-responsive">
                                                                            <table class="table shadow-sm table-hover table-bordered" id="table_eresep" width="100%" style="border-radius: 10px;">
                                                                                <thead>
                                                                                    <tr class="text-center">
                                                                                        <th width="5%" style="border-radius: 10px 0px 0px 0px;">Hapus</th>
                                                                                        <th width="30%">Barang</th>
                                                                                        <th width="15%">Satuan</th>
                                                                                        <th width="15%">Qty</th>
                                                                                        <th width="35%" style="border-radius: 0px 10px 0px 0px;">Signa</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody id="body_eresep">
                                                                                    <?php if (!empty($eresep)) : ?>
                                                                                        <?php $no_eresep = 1;
                                                                                        foreach ($eresep as $er) : ?>
                                                                                            <tr id="row_eresep<?= $no_eresep ?>">
                                                                                                <td width="5%" class="text-center">
                                                                                                    <button class="btn btn-sm btn-danger" type="button" id="btnHapus<?= $no_eresep ?>" onclick="hapusBarang('<?= $no_eresep ?>')" <?= $btn_diss ?>><i class="fa-solid fa-delete-left"></i></button>
                                                                                                </td>
                                                                                                <td width="30%">
                                                                                                    <select name="kode_barang[]" id="kode_barang<?= $no_eresep ?>" class="form-control select2_barang_stok" data-placeholder="~ Pilih Barang" onchange="getSatuan(this.value, '<?= $no_eresep ?>')">
                                                                                                        <?php
                                                                                                        $barang = $this->M_global->getData('barang', ['kode_barang' => $er->kode_barang]);
                                                                                                        ?>
                                                                                                        <option value="<?= $er->kode_barang ?>"><?= $barang->nama ?></option>
                                                                                                    </select>
                                                                                                </td>
                                                                                                <td width="15%">
                                                                                                    <select name="kode_satuan[]" id="kode_satuan<?= $no_eresep ?>" class="form-control select2_global" data-placeholder="~ Pilih Satuan">
                                                                                                        <?php
                                                                                                        $barang = $this->M_global->getData('barang', ['kode_barang' => $er->kode_barang]);

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
                                                                                                        <?php foreach ($satuan as $s) : ?>
                                                                                                            <option value="<?= $s['kode_satuan'] ?>" <?= (($er->kode_satuan == $s['kode_satuan']) ? 'selected' : '') ?>><?= $s['keterangan'] ?></option>
                                                                                                        <?php endforeach; ?>
                                                                                                    </select>
                                                                                                </td>
                                                                                                <td width="15%">
                                                                                                    <input type="text" id="qty<?= $no_eresep ?>" name="qty[]" value="<?= $er->qty ?>" min="<?= $no_eresep ?>" class="form-control text-right" onchange="hitung_st('<?= $no_eresep ?>'); formatRp(this.value, 'qty<?= $no_eresep ?>')" <?= $readonly ?>>
                                                                                                </td>
                                                                                                <td width="35%">
                                                                                                    <textarea name="signa[]" id="signa<?= $no_eresep ?>" class="form-control" <?= $readonly ?>><?= $er->signa ?></textarea>
                                                                                                </td>
                                                                                            </tr>
                                                                                        <?php $no_eresep++;
                                                                                        endforeach; ?>
                                                                                    <?php else : ?>
                                                                                        <tr id="row_eresep1">
                                                                                            <td class="text-center">
                                                                                                <button class="btn btn-sm btn-danger" type="button" id="btnHapus1" onclick="hapusBarang('1')"><i class="fa-solid fa-delete-left"></i></button>
                                                                                            </td>
                                                                                            <td>
                                                                                                <select name="kode_barang[]" id="kode_barang1" class="form-control select2_barang_stok" data-placeholder="~ Pilih Barang" onchange="getSatuan(this.value, '1')">
                                                                                                    <option value="">~ Pilih Barang</option>
                                                                                                </select>
                                                                                            </td>
                                                                                            <td>
                                                                                                <select name="kode_satuan[]" id="kode_satuan1" class="form-control select2_global" data-placeholder="~ Pilih Satuan">
                                                                                                    <option value="">~ Pilih Satuan</option>
                                                                                                    <?php foreach ($satuan as $s) : ?>
                                                                                                        <option value="<?= $s['kode_satuan'] ?>" <?= (($er->kode_satuan == $s['kode_satuan']) ? 'selected' : '') ?>><?= $s['keterangan'] ?></option>
                                                                                                    <?php endforeach; ?>
                                                                                                </select>
                                                                                            </td>
                                                                                            <td>
                                                                                                <input type="text" id="qty1" name="qty[]" value="1" min="1" class="form-control text-right" onchange="hitung_st('1'); formatRp(this.value, 'qty1')">
                                                                                            </td>
                                                                                            <td>
                                                                                                <textarea name="signa[]" id="signa1" class="form-control"></textarea>
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
                                                                        <button type="button" class="btn btn-primary" onclick="addBarang()" id="btnCari" <?= $btn_diss ?>><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
                                                                        <button type="button" class="btn btn-danger float-right" onclick="emptyBarang()" id="btnEmpty" <?= $btn_diss ?>><i class="fa-solid fa-trash"></i>&nbsp;&nbsp;Hapus Semua</button>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <div class="col-md-12">
                                                                    <div class="row mb-3">
                                                                        <div class="col-md-12">
                                                                            <span class="h4">Racikan</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row mb-3">
                                                                        <div class="col-md-12">
                                                                            <textarea name="eracikan" id="eracikan" class="form-control" rows="5" <?= $readonly ?>><?= ((!empty($emr_per)) ? $emr_per->eracikan : '') ?></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="tab_elab" style="color: black !important;">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="table-responsive">
                                                                    <table class="table shadow-sm table-hover table-bordered" id="table_elab" width="100%" style="border-radius: 10px;">
                                                                        <thead>
                                                                            <tr class="text-center">
                                                                                <th width="5%" style="border-radius: 10px 0px 0px 0px;">Hapus</th>
                                                                                <th width="45%">Tindakan</th>
                                                                                <th width="20%">Harga</th>
                                                                                <th width="10%">Qty</th>
                                                                                <th width="20%">Total</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="body_elab">
                                                                            <?php if (!empty($elab)) : ?>
                                                                                <?php $no_elab = 1;
                                                                                foreach ($elab as $et) : ?>
                                                                                    <tr id="row_elab<?= $no_elab ?>">
                                                                                        <td class="text-center">
                                                                                            <button class="btn btn-sm btn-danger" type="button" id="btnHapusL<?= $no_elab ?>" onclick="hapusLab('<?= $no_elab ?>')"><i class="fa-solid fa-delete-left"></i></button>
                                                                                        </td>
                                                                                        <td>
                                                                                            <select name="kode_lab[]" id="kode_lab<?= $no_elab ?>" class="form-control select2_tindakan_single_lab" data-placeholder="~ Pilih Tindakan" onchange="get_harga_tindakan(this.value, '<?= $no_elab ?>', 'lab')">
                                                                                                <?php
                                                                                                $lab = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $et->kode_tarif]);
                                                                                                $mpricelab = $this->M_global->getData('multiprice_tindakan', ['kode_multiprice' => $et->kode_multiprice]);
                                                                                                ?>
                                                                                                <option value="<?= $et->kode_multiprice ?>"><?= $lab->keterangan ?></option>
                                                                                            </select>
                                                                                        </td>
                                                                                        <td class="text-right">
                                                                                            <span id="harga_lab<?= $no_elab ?>" name="harga_lab[]"><?= number_format($mpricelab->klinik + $mpricelab->dokter + $mpricelab->pelayanan + $mpricelab->poli) ?></span>
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" id="qty_lab<?= $no_elab ?>" name="qty_lab[]" value="<?= number_format($et->qty) ?>" min="1" class="form-control text-right" onkeyup="hp_lab(); formatRp(this.value, 'qty_lab<?= $no_elab ?>')">
                                                                                        </td>
                                                                                        <td class="text-right">
                                                                                            <span id="total_lab<?= $no_elab ?>" name="total_lab[]"><?= number_format($et->qty * ($mpricelab->klinik + $mpricelab->dokter + $mpricelab->pelayanan + $mpricelab->poli)) ?></span>
                                                                                        </td>
                                                                                    </tr>
                                                                                <?php $no_elab++;
                                                                                endforeach ?>
                                                                            <?php else : ?>
                                                                                <tr id="row_elab1">
                                                                                    <td class="text-center">
                                                                                        <button class="btn btn-sm btn-danger" type="button" id="btnHapusL1" onclick="hapusLab('1')"><i class="fa-solid fa-delete-left"></i></button>
                                                                                    </td>
                                                                                    <td>
                                                                                        <select name="kode_lab[]" id="kode_lab1" class="form-control select2_tindakan_single_lab" data-placeholder="~ Pilih Tindakan" onchange="get_harga_tindakan(this.value, '1', 'lab')">
                                                                                            <option value="">~ Pilih Tindakan</option>
                                                                                        </select>
                                                                                    </td>
                                                                                    <td class="text-right">
                                                                                        <span id="harga_lab1" name="harga_lab[]">0</span>
                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="text" id="qty_lab1" name="qty_lab[]" value="1" min="1" class="form-control text-right" onkeyup="hp_lab(); formatRp(this.value, 'qty_lab1')">
                                                                                    </td>
                                                                                    <td class="text-right">
                                                                                        <span id="total_lab1" name="total_lab[]">0</span>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php endif; ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <button type="button" class="btn btn-primary" onclick="addLab()" id="btnCari" <?= $btn_diss ?>><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
                                                                        <button type="button" class="btn btn-danger float-right" onclick="emptyLab()" id="btnEmpty" <?= $btn_diss ?>><i class="fa-solid fa-trash"></i>&nbsp;&nbsp;Hapus Semua</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="tab_erad" style="color: black !important;">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="table-responsive">
                                                                    <table class="table shadow-sm table-hover table-bordered" id="table_erad" width="100%" style="border-radius: 10px;">
                                                                        <thead>
                                                                            <tr class="text-center">
                                                                                <th width="5%" style="border-radius: 10px 0px 0px 0px;">Hapus</th>
                                                                                <th width="45%">Tindakan</th>
                                                                                <th width="20%">Harga</th>
                                                                                <th width="10%">Qty</th>
                                                                                <th width="20%">Total</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="body_erad">
                                                                            <?php if (!empty($erad)) : ?>
                                                                                <?php $no_erad = 1;
                                                                                foreach ($erad as $et) : ?>
                                                                                    <tr id="row_erad<?= $no_erad ?>">
                                                                                        <td class="text-center">
                                                                                            <button class="btn btn-sm btn-danger" type="button" id="btnHapusR<?= $no_erad ?>" onclick="hapusRad('<?= $no_erad ?>')"><i class="fa-solid fa-delete-left"></i></button>
                                                                                        </td>
                                                                                        <td>
                                                                                            <select name="kode_rad[]" id="kode_rad<?= $no_erad ?>" class="form-control select2_tindakan_single_rad" data-placeholder="~ Pilih Tindakan" onchange="get_harga_tindakan(this.value, '<?= $no_erad ?>', 'rad')">
                                                                                                <?php
                                                                                                $rad = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $et->kode_tarif]);
                                                                                                $mpricerad = $this->M_global->getData('multiprice_tindakan', ['kode_multiprice' => $et->kode_multiprice]);
                                                                                                ?>
                                                                                                <option value="<?= $et->kode_multiprice ?>"><?= $rad->keterangan ?></option>
                                                                                            </select>
                                                                                        </td>
                                                                                        <td class="text-right">
                                                                                            <span id="harga_rad<?= $no_erad ?>" name="harga_rad[]"><?= number_format($mpricerad->klinik + $mpricerad->dokter + $mpricerad->pelayanan + $mpricerad->poli) ?></span>
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" id="qty_rad<?= $no_erad ?>" name="qty_rad[]" value="<?= number_format($et->qty) ?>" min="1" class="form-control text-right" onkeyup="hp_rad(); formatRp(this.value, 'qty_rad<?= $no_erad ?>')">
                                                                                        </td>
                                                                                        <td class="text-right">
                                                                                            <span id="total_rad<?= $no_erad ?>" name="total_rad[]"><?= number_format($et->qty * ($mpricerad->klinik + $mpricerad->dokter + $mpricerad->pelayanan + $mpricerad->poli)) ?></span>
                                                                                        </td>
                                                                                    </tr>
                                                                                <?php $no_erad++;
                                                                                endforeach ?>
                                                                            <?php else : ?>
                                                                                <tr id="row_erad1">
                                                                                    <td class="text-center">
                                                                                        <button class="btn btn-sm btn-danger" type="button" id="btnHapusR1" onclick="hapusRad('1')"><i class="fa-solid fa-delete-left"></i></button>
                                                                                    </td>
                                                                                    <td>
                                                                                        <select name="kode_rad[]" id="kode_rad1" class="form-control select2_tindakan_single_rad" data-placeholder="~ Pilih Tindakan" onchange="get_harga_tindakan(this.value, '1', 'rad')">
                                                                                            <option value="">~ Pilih Tindakan</option>
                                                                                        </select>
                                                                                    </td>
                                                                                    <td class="text-right">
                                                                                        <span id="harga_rad1" name="harga_rad[]">0</span>
                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="text" id="qty_rad1" name="qty_rad[]" value="1" min="1" class="form-control text-right" onkeyup="hp_rad(); formatRp(this.value, 'qty_rad1')">
                                                                                    </td>
                                                                                    <td class="text-right">
                                                                                        <span id="total_rad1" name="total_rad[]">0</span>
                                                                                    </td>
                                                                                </tr>
                                                                            <?php endif; ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <button type="button" class="btn btn-primary" onclick="addRad()" id="btnCari" <?= $btn_diss ?>><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
                                                                        <button type="button" class="btn btn-danger float-right" onclick="emptyRad()" id="btnEmpty" <?= $btn_diss ?>><i class="fa-solid fa-trash"></i>&nbsp;&nbsp;Hapus Semua</button>
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
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12">
                                <?= $btn_sv ?>
                                <button type="button" class="btn btn-info float-right" onclick="reseting()" id="btnReset"><i class="fa-solid fa-arrows-rotate"></i>&nbsp;&nbsp;Reset</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script>
    const form = $('#form_emr_perawat')
    var no_trx = $("#no_trx");
    var kode_member = $("#kode_member");
    var diagnosa_per = $("#diagnosa_per");
    var anamnesa_per = $("#anamnesa_per");
    var filter_dokter = $("#filter_dokter");
    const btn_assesment = $('#btn_assesment');
    const btn_pemeriksaan = $('#btn_pemeriksaan');
    const btn_psiko = $('#btn_psiko');
    const btn_htt = $('#btn_htt');
    const btn_order = $('#btn_order');
    const assesment_emr = $('#assesment_emr');
    const pemeriksaan_emr = $('#pemeriksaan_emr');
    const psiko_emr = $('#psiko_emr');
    const htt = $('#htt');
    const order_emr = $('#order_emr');
    const btn_etarif = $('#btn_etarif');
    const btn_eresep = $('#btn_eresep');
    const btn_elab = $('#btn_elab');
    const btn_erad = $('#btn_erad');
    const tab_etarif = $('#tab_etarif');
    const tab_eresep = $('#tab_eresep');
    const tab_elab = $('#tab_elab');
    const tab_erad = $('#tab_erad');
    let title_tab = $('#title_tab');
    var pl_tindakan = $('#pl_tindakan');
    var pl_lab = $('#pl_lab');
    var pl_rad = $('#pl_rad');
    var sempoyongan = $('#sempoyongan');
    var berjalan_dgn_alat = $('#berjalan_dgn_alat');
    var penompang = $('#penompang');
    var hasil = $('#hasil');
    var nilai = $('#nilai');
    var scale = $('#scale');
    var bicara = $('#bicara');
    var gangguan_bcr = $('#gangguan_bcr');
    var emosi = $('#emosi');
    var spiritual = $('#spiritual');

    var jml_plafon = $('#jml_plafon');
    var plafon_tindakan = $('#plafon_tindakan');
    var plafon_lab = $('#plafon_lab');
    var plafon_rad = $('#plafon_rad');

    $(document).ready(function() {
        history_px();
        cek_scale('<?= ((!empty($emr_per)) ? $emr_per->scale : '1') ?>');
        sel_tab(0);
        cek_bcr(<?= ((!empty($emr_per)) ? $emr_per->bicara : '1') ?>);
        cek_emosi(<?= ((!empty($emr_per)) ? $emr_per->emosi : '1') ?>);
        cek_spiritual(<?= ((!empty($emr_per)) ? $emr_per->spiritual : '1') ?>);
        sel_tab_emr(1);
        cek_resiko();
        initailizeSelect2_tindakan_single('<?= (!empty($pendaftaran) ? $pendaftaran->kode_jenis_bayar : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kelas : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kode_poli : "") ?>');
        hp_tindakan();
        initailizeSelect2_tindakan_single_lab('<?= (!empty($pendaftaran) ? $pendaftaran->kode_jenis_bayar : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kelas : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kode_poli : "") ?>');
        hp_lab();
        initailizeSelect2_tindakan_single_rad('<?= (!empty($pendaftaran) ? $pendaftaran->kode_jenis_bayar : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kelas : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kode_poli : "") ?>');
        hp_rad();
        hitung_jml_plafon();
    });

    function history_px() {
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("body_history").innerHTML = this.responseText;
            }
        };
        var param = `/${no_trx.val()}?kode_member=${kode_member.val()}&kode_dokter=${filter_dokter.val()}`;
        xhttp.open("GET", "<?= base_url('Emr/histori_px'); ?>" + param, true);
        xhttp.send();
    }

    function sel_tab_emr(param) {
        if (param == 1) {
            btn_assesment.addClass('btn-primary');
            btn_pemeriksaan.removeClass('btn-primary');
            btn_psiko.removeClass('btn-primary');
            btn_htt.removeClass('btn-primary');
            btn_order.removeClass('btn-primary');

            assesment_emr.show(200);
            pemeriksaan_emr.hide(200);
            psiko_emr.hide(200);
            htt.hide(200);
            order_emr.hide(200);
        } else if (param == 2) {
            btn_assesment.removeClass('btn-primary');
            btn_pemeriksaan.addClass('btn-primary');
            btn_psiko.removeClass('btn-primary');
            btn_htt.removeClass('btn-primary');
            btn_order.removeClass('btn-primary');

            assesment_emr.hide(200);
            pemeriksaan_emr.show(200);
            psiko_emr.hide(200);
            htt.hide(200);
            order_emr.hide(200);
        } else if (param == 3) {
            btn_assesment.removeClass('btn-primary');
            btn_pemeriksaan.removeClass('btn-primary');
            btn_psiko.addClass('btn-primary');
            btn_htt.removeClass('btn-primary');
            btn_order.removeClass('btn-primary');

            assesment_emr.hide(200);
            pemeriksaan_emr.hide(200);
            psiko_emr.show(200);
            htt.hide(200);
            order_emr.hide(200);
        } else if (param == 4) {
            btn_assesment.removeClass('btn-primary');
            btn_pemeriksaan.removeClass('btn-primary');
            btn_psiko.removeClass('btn-primary');
            btn_htt.addClass('btn-primary');
            btn_order.removeClass('btn-primary');

            assesment_emr.hide(200);
            pemeriksaan_emr.hide(200);
            psiko_emr.hide(200);
            htt.show(200);
            order_emr.hide(200);
        } else {
            btn_assesment.removeClass('btn-primary');
            btn_pemeriksaan.removeClass('btn-primary');
            btn_psiko.removeClass('btn-primary');
            btn_htt.removeClass('btn-primary');
            btn_order.addClass('btn-primary');

            assesment_emr.hide(200);
            pemeriksaan_emr.hide(200);
            psiko_emr.hide(200);
            htt.hide(200);
            order_emr.show(200);
        }
    }

    function cek_resiko() {
        var a1 = sempoyongan.val();
        var a2 = berjalan_dgn_alat.val();
        var b = penompang.val();

        a1 = (a1 === '' || a1 == null) ? 0 : parseInt(a1);
        a2 = (a2 === '' || a2 == null) ? 0 : parseInt(a2);
        b = (b === '' || b == null) ? 0 : parseInt(b);

        var a = (a1 === 1 || a2 === 1) ? 1 : 0;

        if (a === 0 && b === 0) {
            hasil.val('Tidak Beresiko');
            nilai.val('Tidak Ditemukan A & B');
        } else if (a === 1 && b === 1) {
            hasil.val('Beresiko Tinggi');
            nilai.val('Ditemukan A & B');
        } else {
            hasil.val('Beresiko Sedang');
            nilai.val('Ditemukan Salah Satu Antara A & B');
        }
    }

    function cek_scale(param) {
        scale.val(param);
        if (param == 1) {
            document.getElementById('scale1').checked = true;
            document.getElementById('scale2').checked = false;
            document.getElementById('scale3').checked = false;
            document.getElementById('scale4').checked = false;
            document.getElementById('scale5').checked = false;
            document.getElementById('scale6').checked = false;
        } else if (param == 2) {
            document.getElementById('scale1').checked = false;
            document.getElementById('scale2').checked = true;
            document.getElementById('scale3').checked = false;
            document.getElementById('scale4').checked = false;
            document.getElementById('scale5').checked = false;
            document.getElementById('scale6').checked = false;
        } else if (param == 3) {
            document.getElementById('scale1').checked = false;
            document.getElementById('scale2').checked = false;
            document.getElementById('scale3').checked = true;
            document.getElementById('scale4').checked = false;
            document.getElementById('scale5').checked = false;
            document.getElementById('scale6').checked = false;
        } else if (param == 4) {
            document.getElementById('scale1').checked = false;
            document.getElementById('scale2').checked = false;
            document.getElementById('scale3').checked = false;
            document.getElementById('scale4').checked = true;
            document.getElementById('scale5').checked = false;
            document.getElementById('scale6').checked = false;
        } else if (param == 5) {
            document.getElementById('scale1').checked = false;
            document.getElementById('scale2').checked = false;
            document.getElementById('scale3').checked = false;
            document.getElementById('scale4').checked = false;
            document.getElementById('scale5').checked = true;
            document.getElementById('scale6').checked = false;
        } else {
            document.getElementById('scale1').checked = false;
            document.getElementById('scale2').checked = false;
            document.getElementById('scale3').checked = false;
            document.getElementById('scale4').checked = false;
            document.getElementById('scale5').checked = false;
            document.getElementById('scale6').checked = true;
        }
    }

    function cek_bcr(param) {
        bicara.val(param);
        if (param == 1) {
            document.getElementById('bicara1').checked = true;
            document.getElementById('bicara2').checked = false;
            gangguan_bcr.hide();
        } else {
            document.getElementById('bicara1').checked = false;
            document.getElementById('bicara2').checked = true;
            gangguan_bcr.show();
        }
    }

    function cek_emosi(param) {
        emosi.val(param);
        if (param == 1) {
            document.getElementById('emosi1').checked = true;
            document.getElementById('emosi2').checked = false;
            document.getElementById('emosi3').checked = false;
        } else if (param == 2) {
            document.getElementById('emosi1').checked = false;
            document.getElementById('emosi2').checked = true;
            document.getElementById('emosi3').checked = false;
        } else {
            document.getElementById('emosi1').checked = false;
            document.getElementById('emosi2').checked = false;
            document.getElementById('emosi3').checked = true;
        }
    }

    function cek_spiritual(param) {
        spiritual.val(param);
        if (param == 1) {
            document.getElementById('spiritual1').checked = true;
            document.getElementById('spiritual2').checked = false;
            document.getElementById('spiritual3').checked = false;
        } else if (param == 2) {
            document.getElementById('spiritual1').checked = false;
            document.getElementById('spiritual2').checked = true;
            document.getElementById('spiritual3').checked = false;
        } else {
            document.getElementById('spiritual1').checked = false;
            document.getElementById('spiritual2').checked = false;
            document.getElementById('spiritual3').checked = true;
        }
    }

    function sel_tab(param) {
        if (param == 0) {
            btn_etarif.addClass('btn-primary');
            btn_etarif.addClass('actived');

            btn_eresep.removeClass('btn-primary');
            btn_eresep.removeClass('actived');

            btn_elab.removeClass('btn-primary');
            btn_elab.removeClass('actived');

            btn_erad.removeClass('btn-primary');
            btn_erad.removeClass('actived');

            tab_etarif.show(200);
            tab_eresep.hide(200);
            tab_elab.hide(200);
            tab_erad.hide(200);

            pl_tindakan.show();
            pl_lab.hide();
            pl_rad.hide();

            title_tab.text('Tindakan');
        } else if (param == 1) {
            btn_eresep.addClass('btn-primary');
            btn_eresep.addClass('actived');

            btn_etarif.removeClass('btn-primary');
            btn_etarif.removeClass('actived');

            btn_elab.removeClass('btn-primary');
            btn_elab.removeClass('actived');

            btn_erad.removeClass('btn-primary');
            btn_erad.removeClass('actived');

            tab_eresep.show(200);
            tab_etarif.hide(200);
            tab_elab.hide(200);
            tab_erad.hide(200);

            pl_tindakan.hide();
            pl_lab.hide();
            pl_rad.hide();

            title_tab.text('Resep / Racik');
        } else if (param == 2) {
            btn_elab.addClass('btn-primary');
            btn_elab.addClass('actived');

            btn_eresep.removeClass('btn-primary');
            btn_eresep.removeClass('actived');

            btn_etarif.removeClass('btn-primary');
            btn_etarif.removeClass('actived');

            btn_erad.removeClass('btn-primary');
            btn_erad.removeClass('actived');

            tab_elab.show(200);
            tab_eresep.hide(200);
            tab_etarif.hide(200);
            tab_erad.hide(200);

            pl_tindakan.hide();
            pl_lab.show();
            pl_rad.hide();

            title_tab.text('Laboratorium');
        } else {
            btn_erad.addClass('btn-primary');
            btn_erad.addClass('actived');

            btn_eresep.removeClass('btn-primary');
            btn_eresep.removeClass('actived');

            btn_elab.removeClass('btn-primary');
            btn_elab.removeClass('actived');

            btn_etarif.removeClass('btn-primary');
            btn_etarif.removeClass('actived');

            tab_erad.show(200);
            tab_eresep.hide(200);
            tab_elab.hide(200);
            tab_etarif.hide(200);

            pl_tindakan.hide();
            pl_lab.hide();
            pl_rad.show();

            title_tab.text('Radiologi');
        }
    }

    function hitung_jml_plafon() {
        var htin = $('#plafon_tindakan').text();
        var hlab = $('#plafon_lab').text();
        var hrad = $('#plafon_rad').text();

        var jml_pl_all = Number(htin.replace(',', '')) + Number(hlab.replace(',', '')) + Number(hrad.replace(',', ''))

        jml_plafon.text(formatRpNoId(jml_pl_all))
    }

    function getSatuan(param, no) {
        if (!param) {
            return Swal.fire("Barang", "Form sudah dipilih?", "question");
        }

        $('#kode_satuan' + no).empty();

        $.ajax({
            url: siteUrl + 'Emr/getSatuan/' + param,
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) { // jika fungsi berjalan dengan baik
                $.each(result, function(index, value) {
                    $('#kode_satuan' + no).append('<option value="' + value.kode_satuan + '">' + value.nama_satuan + '</option>')
                });
            },
            error: function(result) { // jika fungsi error
                error_proccess();
            }
        });
    }

    function addTarif() {
        var tableBarangIn = document.getElementById('table_etarif'); // ambil id table detail
        var jum = tableBarangIn.rows.length; // hitung jumlah rownya
        var x = Number(jum) + 1;
        var tbody = $('#body_etarif')

        tbody.append(`<tr id="row_etarif${x}">
            <td class="text-center">
                <button class="btn btn-sm btn-danger" type="button" id="btnHapusT${x}" onclick="hapusTarif('${x}')"><i class="fa-solid fa-delete-left"></i></button>
            </td>
            <td>
                <select name="kode_tarif[]" id="kode_tarif${x}" class="form-control select2_tindakan_single" data-placeholder="~ Pilih Tindakan" onchange="get_harga_tindakan(this.value, '${x}', 'tindakan')">
                    <option value="">~ Pilih Tindakan</option>
                </select>
            </td>
            <td class="text-right">
                <span id="harga_tarif${x}" name="harga_tarif[]" class="text-right">0</span>
            </td>
            <td>
                <input type="text" id="qty_tarif${x}" name="qty_tarif[]" value="1" min="1" class="form-control text-right" onkeyup="hp_tindakan(); formatRp(this.value, 'qty_tarif${x}')">
            </td>
            <td class="text-right">
                <span id="total_tarif${x}" name="total_tarif[]" class="text-right">0</span>
            </td>
        </tr>`);

        initailizeSelect2_tindakan_single('<?= (!empty($pendaftaran) ? $pendaftaran->kode_jenis_bayar : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kelas : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kode_poli : "") ?>');
    }

    function emptyTarif() {
        var tbody = $('#body_etarif');

        tbody.empty();
        addTarif();
    }

    function get_harga_tindakan(kd, i, param) {
        if (kd) {
            $.ajax({
                url: siteUrl + 'Emr/getHargaTindakan/' + kd,
                type: "POST",
                data: form.serialize(),
                dataType: "JSON",
                success: function(result) { // jika fungsi berjalan dengan baik
                    if (param == 'tindakan') {
                        $('#harga_tarif' + i).text(formatRpNoId(result.harga));
                        hp_tindakan();
                    } else if (param == 'lab') {
                        $('#harga_lab' + i).text(formatRpNoId(result.harga));
                        hp_lab();
                    } else {
                        $('#harga_rad' + i).text(formatRpNoId(result.harga));
                        hp_rad();
                    }
                },
            })
        }
    }

    function hp_tindakan() {
        var tableTindakan = document.getElementById('table_etarif'); // ambil id table detail
        var rowCount = tableTindakan.rows.length; // hitung jumlah rownya

        // buat variable untuk di sum
        var tjumlah = 0;

        // lakukan loop
        for (var i = 1; i < rowCount; i++) {
            var row = tableTindakan.rows[i];

            // ambil data berdasarkan loop
            var harga1 = Number((row.cells[2].textContent).replace(/[^0-9\.]+/g, ""));
            var qty1 = Number((row.cells[3].children[0].value).replace(/[^0-9\.]+/g, ""));

            row.cells[4].children[0].textContent = formatRpNoId(harga1 * qty1);

            // lakukan rumus sum
            tjumlah += (harga1 * qty1)

        }

        // tampilkan hasil ke dalam format koma
        $('#plafon_tindakan').text(formatRpNoId(tjumlah));

        hitung_jml_plafon();
    }

    function hapusTarif(no) {
        $('#row_etarif' + no).remove();
        hp_tindakan();
    }

    function addBarang() {
        var tableBarangIn = document.getElementById('table_eresep'); // ambil id table detail
        var jum = tableBarangIn.rows.length; // hitung jumlah rownya
        var x = Number(jum) + 1;
        var tbody = $('#body_eresep')

        tbody.append(`<tr id="row_eresep${x}">
            <td width="5%" class="text-center">
                <button class="btn btn-sm btn-danger" type="button" id="btnHapus${x}" onclick="hapusBarang('${x}')"><i class="fa-solid fa-delete-left"></i></button>
            </td>
            <td width="30%">
                <select name="kode_barang[]" id="kode_barang${x}" class="form-control select2_barang_stok" data-placeholder="~ Pilih Barang" onchange="getSatuan(this.value, '${x}')">
                    <option value="">~ Pilih Barang</option>
                </select>
            </td>
            <td width="15%">
                <select name="kode_satuan[]" id="kode_satuan${x}" class="form-control select2_global" data-placeholder="~ Pilih Satuan">
                    <option value="">~ Pilih Satuan</option>
                </select>
            </td>
            <td width="15%">
                <input type="text" id="qty${x}" name="qty[]" value="1" min="1" class="form-control text-right" onchange="hitung_st('${x}'); formatRp(this.value, 'qty${x}')">
            </td>
            <td width="35%">
                <textarea name="signa[]" id="signa${x}" class="form-control"></textarea>
            </td>
        </tr>`);

        initailizeSelect2_barang_stok();

        $(".select2_global").select2({
            placeholder: $(this).data('placeholder'),
            width: '100%',
            allowClear: true,
        });
    }

    function emptyBarang() {
        var tbody = $('#body_eresep');

        tbody.empty();
        addBarang();
    }

    function hapusBarang(no) {
        $('#row_eresep' + no).remove();
    }

    function addLab() {
        var tableBarangIn = document.getElementById('table_elab'); // ambil id table detail
        var jum = tableBarangIn.rows.length; // hitung jumlah rownya
        var x = Number(jum) + 1;
        var tbody = $('#body_elab')

        tbody.append(`<tr id="row_elab${x}">
            <td class="text-center">
                <button class="btn btn-sm btn-danger" type="button" id="btnHapusL${x}" onclick="hapusLab('${x}')"><i class="fa-solid fa-delete-left"></i></button>
            </td>
            <td>
                <select name="kode_lab[]" id="kode_lab${x}" class="form-control select2_tindakan_single_lab" data-placeholder="~ Pilih Tindakan" onchange="get_harga_tindakan(this.value, '${x}', 'lab')">
                    <option value="">~ Pilih Tindakan</option>
                </select>
            </td>
            <td class="text-right">
                <span id="harga_lab${x}" name="harga_lab[]">0</span>
            </td>
            <td>
                <input type="text" id="qty_lab${x}" name="qty_lab[]" value="1" min="1" class="form-control text-right" onkeyup="hp_lab(); formatRp(this.value, 'qty_lab${x}')">
            </td>
            <td class="text-right">
                <span id="total_lab${x}" name="total_lab[]">0</span>
            </td>
        </tr>`);

        initailizeSelect2_tindakan_single_lab('<?= (!empty($pendaftaran) ? $pendaftaran->kode_jenis_bayar : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kelas : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kode_poli : "") ?>');
    }

    function hp_lab() {
        var tableLab = document.getElementById('table_elab'); // ambil id table detail
        var rowCount = tableLab.rows.length; // hitung jumlah rownya

        // buat variable untuk di sum
        var tjumlah = 0;

        // lakukan loop
        for (var i = 1; i < rowCount; i++) {
            var row = tableLab.rows[i];

            // ambil data berdasarkan loop
            var harga1 = Number((row.cells[2].textContent).replace(/[^0-9\.]+/g, ""));
            var qty1 = Number((row.cells[3].children[0].value).replace(/[^0-9\.]+/g, ""));

            row.cells[4].children[0].textContent = formatRpNoId(harga1 * qty1);

            // lakukan rumus sum
            tjumlah += (harga1 * qty1)

        }

        // tampilkan hasil ke dalam format koma
        $('#plafon_lab').text(formatRpNoId(tjumlah));

        hitung_jml_plafon();
    }

    function emptyLab() {
        var tbody = $('#body_elab');

        tbody.empty();
        addLab();
    }

    function hapusLab(no) {
        $('#row_elab' + no).remove();
        hp_lab()
    }

    function addRad() {
        var tableBarangIn = document.getElementById('table_erad'); // ambil id table detail
        var jum = tableBarangIn.rows.length; // hitung jumlah rownya
        var x = Number(jum) + 1;
        var tbody = $('#body_erad')

        tbody.append(`<tr id="row_erad${x}">
            <td class="text-center">
                <button class="btn btn-sm btn-danger" type="button" id="btnHapusR${x}" onclick="hapusRad('${x}')"><i class="fa-solid fa-delete-left"></i></button>
            </td>
            <td>
                <select name="kode_rad[]" id="kode_rad${x}" class="form-control select2_tindakan_single_rad" data-placeholder="~ Pilih Tindakan" onchange="get_harga_tindakan(this.value, '${x}', 'rad')">
                    <option value="">~ Pilih Tindakan</option>
                </select>
            </td>
            <td class="text-right">
                <span id="harga_rad${x}" name="harga_rad[]">0</span>
            </td>
            <td>
                <input type="text" id="qty_rad${x}" name="qty_rad[]" value="1" min="1" class="form-control text-right" onkeyup="hp_rad(); formatRp(this.value, 'qty_rad${x}')">
            </td>
            <td class="text-right">
                <span id="total_rad${x}" name="total_rad[]">0</span>
            </td>
        </tr>`);

        initailizeSelect2_tindakan_single_rad('<?= (!empty($pendaftaran) ? $pendaftaran->kode_jenis_bayar : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kelas : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kode_poli : "") ?>');
    }

    function hp_rad() {
        var tableRad = document.getElementById('table_erad'); // ambil id table detail
        var rowCount = tableRad.rows.length; // hitung jumlah rownya

        // buat variable untuk di sum
        var tjumlah = 0;

        // lakukan loop
        for (var i = 1; i < rowCount; i++) {
            var row = tableRad.rows[i];

            // ambil data berdasarkan loop
            var harga1 = Number((row.cells[2].textContent).replace(/[^0-9\.]+/g, ""));
            var qty1 = Number((row.cells[3].children[0].value).replace(/[^0-9\.]+/g, ""));

            row.cells[4].children[0].textContent = formatRpNoId(harga1 * qty1);

            // lakukan rumus sum
            tjumlah += (harga1 * qty1)

        }

        // tampilkan hasil ke dalam format koma
        $('#plafon_rad').text(formatRpNoId(tjumlah));

        hitung_jml_plafon();
    }

    function emptyRad() {
        var tbody = $('#body_erad');

        tbody.empty();
        addRad();
    }

    function hapusRad(no) {
        $('#row_erad' + no).remove();
        hp_rad();
    }

    function reseting() {
        window.location.reload();
    }

    <?php foreach ($hist_member as $hm) : ?>
        const popup<?= $hm->id ?> = document.getElementById('popup<?= $hm->id ?>');
        const header<?= $hm->id ?> = document.querySelector('.card-draggable<?= $hm->id ?>');
        let offsetX<?= $hm->id ?>, offsetY<?= $hm->id ?>, isDragging<?= $hm->id ?> = false;

        header<?= $hm->id ?>.addEventListener('mousedown', (e) => {
            isDragging<?= $hm->id ?> = true;
            offsetX<?= $hm->id ?> = e.clientX - popup<?= $hm->id ?>.offsetLeft;
            offsetY<?= $hm->id ?> = e.clientY - popup<?= $hm->id ?>.offsetTop;
        });

        document.addEventListener('mousemove', (e) => {
            if (!isDragging<?= $hm->id ?>) return;
            popup<?= $hm->id ?>.style.left = e.clientX - offsetX<?= $hm->id ?> + 'px';
            popup<?= $hm->id ?>.style.top = e.clientY - offsetY<?= $hm->id ?> + 'px';
        });

        document.addEventListener('mouseup', () => {
            isDragging<?= $hm->id ?> = false;
        });
    <?php endforeach ?>

    function show_his(param, nohis, km, uid) {
        $('#body_hispx' + uid).text('');

        if (!param) {
            return Swal.fire("Invoice", "Form sudah dipilih?", "question");
        }

        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("body_hispx" + uid).innerHTML = this.responseText;
            }
        };
        var param = `/${param}/${nohis}/${km}`;
        xhttp.open("GET", "<?= base_url('Emr/his_px'); ?>" + param, true);
        xhttp.send();

        document.getElementById('popup' + uid).style.display = 'block';
    }

    // dokter
    <?php foreach ($hist_member as $hm) : ?>
        const popup2<?= $hm->id ?> = document.getElementById('popup2<?= $hm->id ?>');
        const header2<?= $hm->id ?> = document.querySelector('.card-draggable2<?= $hm->id ?>');
        let offsetX2<?= $hm->id ?>, offsetY2<?= $hm->id ?>, isDragging2<?= $hm->id ?> = false;

        header2<?= $hm->id ?>.addEventListener('mousedown', (e) => {
            isDragging2<?= $hm->id ?> = true;
            offsetX2<?= $hm->id ?> = e.clientX - popup2<?= $hm->id ?>.offsetLeft;
            offsetY2<?= $hm->id ?> = e.clientY - popup2<?= $hm->id ?>.offsetTop;
        });

        document.addEventListener('mousemove', (e) => {
            if (!isDragging2<?= $hm->id ?>) return;
            popup2<?= $hm->id ?>.style.left = e.clientX - offsetX2<?= $hm->id ?> + 'px';
            popup2<?= $hm->id ?>.style.top = e.clientY - offsetY2<?= $hm->id ?> + 'px';
        });

        document.addEventListener('mouseup', () => {
            isDragging2<?= $hm->id ?> = false;
        });
    <?php endforeach ?>

    function show_his2(param, nohis, km, uid) {
        $('#body_hispx2' + uid).text('');

        if (!param) {
            return Swal.fire("Invoice", "Form sudah dipilih?", "question");
        }

        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("body_hispx2" + uid).innerHTML = this.responseText;
            }
        };
        var param = `/${param}/${nohis}/${km}`;
        xhttp.open("GET", "<?= base_url('Emr/his_px2'); ?>" + param, true);
        xhttp.send();

        document.getElementById('popup2' + uid).style.display = 'block';
    }

    function copyTextAssesment(sempoyongan_emr, berjalan_dgn_alat_emr, penompang_emr, keterangan_assesment_emr) {
        const element = document.getElementById(sempoyongan_emr);
        const element1 = document.getElementById(berjalan_dgn_alat_emr);
        const element2 = document.getElementById(penompang_emr);
        const element3 = document.getElementById(keterangan_assesment_emr);
        const text = 'Sempoyongan: ' + element.textContent + ', Berjalan dengan alat: ' + element1.textContent + ', Penompang: ' + element2.textContent + ', Keterangan lain: ' + element3.textContent;
        navigator.clipboard.writeText(text)
            .then(() => {
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Teks Berhasil Disalin",
                    showConfirmButton: false,
                    timer: 500
                });
            })
            .catch(err => {
                Swal.fire({
                    position: "center",
                    icon: "warning",
                    title: "Teks Gagal Disalin",
                    showConfirmButton: false,
                    timer: 500
                });
            });
    }

    function implementAssesment(param1, x1, param2, x2, param3, x3, param4, x4) {
        $('#' + x1).val(param1).change();
        $('#' + x2).val(param2).change();
        $('#' + x3).val(param3).change();
        $('#' + x4).val(param4);
    }

    function copyTextPemeriksaan(anamnesa_per_emr, diagnosa_per_emr, tekanan_darah_emr, nadi_emr, suhu_emr, bb_emr, tb_emr, pernapasan_emr, saturasi_emr, gizi_emr, hamil_emr, hpht_emr, keterangan_hamil_emr, scale_emr) {
        const element = document.getElementById(anamnesa_per_emr);
        const element1 = document.getElementById(diagnosa_per_emr);
        const element2 = document.getElementById(tekanan_darah_emr);
        const element3 = document.getElementById(nadi_emr);
        const element4 = document.getElementById(suhu_emr);
        const element5 = document.getElementById(bb_emr);
        const element6 = document.getElementById(tb_emr);
        const element7 = document.getElementById(pernapasan_emr);
        const element8 = document.getElementById(saturasi_emr);
        const element9 = document.getElementById(gizi_emr);
        const element10 = document.getElementById(hamil_emr);
        const element11 = document.getElementById(hpht_emr);
        const element12 = document.getElementById(keterangan_hamil_emr);
        const element13 = document.getElementById(scale_emr);
        const text = 'Anamnesa: ' + element.textContent + ', Diagnosa: ' + element1.textContent + ', Tekanan Darah: ' + element2.textContent + ', Nadi: ' + element3.textContent + ', Suhu: ' + element4.textContent + ', Berat Badan: ' + element5.textContent + ', Tinggi Badan: ' + element6.textContent + ', Pernapasan: ' + element7.textContent + ', Saturasi: ' + element8.textContent + ', Gizi: ' + element9.textContent + ', Hamil: ' + element10.textContent + ', HPHT: ' + element11.textContent + ', Keterangan Hamil: ' + element12.textContent + ', Skala Nyeri: ' + element13.textContent;
        navigator.clipboard.writeText(text)
            .then(() => {
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Teks Berhasil Disalin",
                    showConfirmButton: false,
                    timer: 500
                });
            })
            .catch(err => {
                Swal.fire({
                    position: "center",
                    icon: "warning",
                    title: "Teks Gagal Disalin",
                    showConfirmButton: false,
                    timer: 500
                });
            });
    }

    function implementPemeriksaan(x1, x2, x3, x4, x5, x6, x7, x8, x9, x10, x11, x12, x13, x14) {
        $('#anamnesa_per').val(x1);
        $('#diagnosa_per').val(x2);
        $('#tekanan_darah').val(x3);
        $('#nadi').val(x4);
        $('#suhu').val(x5);
        $('#bb').val(x6);
        $('#tb').val(x7);
        $('#pernapasan').val(x8);
        $('#saturasi').val(x9);
        $('#gizi').val(x10).change();
        $('#hamil').val(x11).change();
        $('#hpht').val(x12);
        $('#keterangan_hamil').val(x13);
        $('#scale').val(x14);
        document.getElementById('scale' + x14).checked = true;
        cek_scale(x14);
        console.log(x14)
    }

    function copyTextPsiko(x1, x2, x3, x4) {
        const text = 'Bicara: ' + ((x1 == 1) ? 'Normal' : 'Terganggu') + ', Psikologi: ' + ((x2 == 1) ? 'Tenang' : ((x2 == 2) ? 'Gelisah' : 'Emosional')) + ', Spiritual: ' + ((x3 == 1) ? 'Berdiri' : ((x3 == 2) ? 'Duduk' : 'Berbaring')) + ', Gangguan Bicara: ' + x3;
        navigator.clipboard.writeText(text)
            .then(() => {
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Teks Berhasil Disalin",
                    showConfirmButton: false,
                    timer: 500
                });
            })
            .catch(err => {
                Swal.fire({
                    position: "center",
                    icon: "warning",
                    title: "Teks Gagal Disalin",
                    showConfirmButton: false,
                    timer: 500
                });
            });
    }

    function implementPsiko(x1, x2, x3, x4) {
        $('#gangguan').val(x4);
        $('#bicara').val(x1);
        document.getElementById('bicara' + x1).checed = true;
        cek_bcr(x1);
        $('#emosi').val(x2);
        document.getElementById('emosi' + x2).checed = true;
        cek_emosi(x2);
        $('#spiritual').val(x3);
        document.getElementById('spiritual' + x3).checed = true;
        cek_spiritual(x3);
    }

    function copyTextHead(param) {
        const text = param;
        navigator.clipboard.writeText(text)
            .then(() => {
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Teks Berhasil Disalin",
                    showConfirmButton: false,
                    timer: 500
                });
            })
            .catch(err => {
                Swal.fire({
                    position: "center",
                    icon: "warning",
                    title: "Teks Gagal Disalin",
                    showConfirmButton: false,
                    timer: 500
                });
            });
    }

    function implementHead(param) {
        $.ajax({
            url: `${siteUrl}Emr/emr_dok_fisik/${param}`,
            type: `POST`,
            dataType: `JSON`,
            success: function(result) {
                if (result.length > 0) {
                    $('#body_fisik').empty();

                    var nohead = 1;
                    $.each(result, function(index, value) {
                        $('#body_fisik').append(`<tr id="row_fisik${nohead}">
                            <td>
                                <input type="text" name="fisik[]" id="fisik${nohead}" class="form-control" value="${value.fisik}" readonly>
                            </td>
                            <td>
                                <textarea name="desc_fisik[]" id="desc_fisik${nohead}" class="form-control">${value.desc_fisik}</textarea>
                            </td>
                        </tr>`);

                        nohead++;
                    });
                }
            },
            error: function(error) {
                error_proccess();
            }
        });
    }

    function copyTextOrder(x1) {
        const text = x1;
        navigator.clipboard.writeText(text)
            .then(() => {
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Teks Berhasil Disalin",
                    showConfirmButton: false,
                    timer: 500
                });
            })
            .catch(err => {
                Swal.fire({
                    position: "center",
                    icon: "warning",
                    title: "Teks Gagal Disalin",
                    showConfirmButton: false,
                    timer: 500
                });
            });
    }

    function implementOrder(param) {
        //etarif
        $.ajax({
            url: `${siteUrl}Emr/emr_tarif/${param}`,
            type: `POST`,
            dataType: `JSON`,
            success: function(result) {
                console.log(result)
                if (result.length > 0) {
                    $('#body_etarif').empty();

                    var notar = 1;

                    $.each(result, function(index, value) {
                        $('#body_etarif').append(`<tr id="row_etarif${notar}">
                            <td class="text-center">
                                <button class="btn btn-sm btn-danger" type="button" id="btnHapusT${notar}" onclick="hapusTarif('${notar}')"><i class="fa-solid fa-delete-left"></i></button>
                            </td>
                            <td>
                                <select name="kode_tarif[]" id="kode_tarif${notar}" class="form-control select2_tindakan_single" data-placeholder="~ Pilih Tindakan" onchange="get_harga_tindakan(this.value, '${notar}', 'tindakan')">
                                    <option value="${value.kode_multiprice}">${value.nama}</option>
                                </select>
                            </td>
                            <td class="text-right">
                                <span id="harga_tarif${notar}" name="harga_tarif[]" class="text-right">${formatRpNoId(value.harga)}</span>
                            </td>
                            <td>
                                <input type="text" id="qty_tarif${notar}" name="qty_tarif[]" value="${value.qty}" min="1" class="form-control text-right" onkeyup="hp_tindakan(); formatRp(this.value, 'qty_tarif${notar}')">
                            </td>
                            <td class="text-right">
                                <span id="total_tarif${notar}" name="total_tarif[]" class="text-right">${formatRpNoId(value.qty * value.harga)}</span>
                            </td>
                        </tr>`);

                        initailizeSelect2_tindakan_single('<?= (!empty($pendaftaran) ? $pendaftaran->kode_jenis_bayar : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kelas : "") ?>');

                        notar++;
                    });

                    return;
                }
            },
            error: function(error) {
                error_proccess();
            }
        })

        //eresep
        $.ajax({
            url: `${siteUrl}Emr/emr_per_barang/${param}`,
            type: `POST`,
            dataType: `JSON`,
            success: function(result) {
                if (result.length > 0) {
                    $('#body_eresep').empty();

                    var norsp = 1;

                    $.each(result, function(index, value) {
                        $('#body_eresep').append(`<tr id="row_eresep${norsp}">
                            <td class="text-center">
                                <button class="btn btn-sm btn-danger" type="button" id="btnHapus${norsp}" onclick="hapusBarang('${norsp}')">
                                    <i class="fa-solid fa-delete-left"></i>
                                </button>
                            </td>
                            <td>
                                <select name="kode_barang[]" id="kode_barang${norsp}" class="form-control select2_barang_stok" data-placeholder="~ Pilih Barang" onchange="getSatuan(this.value, '${norsp}')">
                                    <option value="${value.kode_barang}">${value.nama}</option>
                                </select>
                            </td>
                            <td>
                                <select name="kode_satuan[]" id="kode_satuan${norsp}" class="form-control select2_global" data-placeholder="~ Pilih Satuan">
                                    <option value="">~ Pilih Satuan</option>
                                    <?php if (!empty($satuan)) : ?>
                                    <?php foreach ($satuan as $s) : ?>
                                        <option value="<?= $s['kode_satuan'] ?>" ${value.kode_satuan == <?= $s['kode_satuan'] ?> ? 'selected' : '' }><?= $s['keterangan'] ?></option>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="qty${norsp}" name="qty[]" value="${value.qty}" min="1" class="form-control text-right" onchange="hitung_st('${norsp}'); formatRp(this.value, 'qty${norsp}')">
                            </td>
                            <td>
                                <textarea name="signa[]" id="signa${norsp}" class="form-control">${value.signa}</textarea>
                            </td>
                        </tr>`)

                        initailizeSelect2_barang_stok();

                        $(".select2_global").select2({
                            placeholder: $(this).data('placeholder'),
                            width: '100%',
                            allowClear: true,
                        });

                        norsp++;
                    });

                    return;
                }
            },
            error: function(error) {
                error_proccess();
            }
        })

        //elab
        $.ajax({
            url: `${siteUrl}Emr/emr_lab/${param}`,
            type: `POST`,
            dataType: `JSON`,
            success: function(result) {
                console.log(result)
                if (result.length > 0) {
                    $('#body_elab').empty();

                    var notar = 1;

                    $.each(result, function(index, value) {
                        $('#body_elab').append(`<tr id="row_elab${notar}">
                            <td class="text-center">
                                <button class="btn btn-sm btn-danger" type="button" id="btnHapusT${notar}" onclick="hapusTarif('${notar}')"><i class="fa-solid fa-delete-left"></i></button>
                            </td>
                            <td>
                                <select name="kode_lab[]" id="kode_lab${notar}" class="form-control select2_tindakan_single" data-placeholder="~ Pilih Tindakan" onchange="get_harga_tindakan(this.value, '${notar}', 'lab')">
                                    <option value="${value.kode_multiprice}">${value.nama}</option>
                                </select>
                            </td>
                            <td class="text-right">
                                <span id="harga_lab${notar}" name="harga_lab[]" class="text-right">${formatRpNoId(value.harga)}</span>
                            </td>
                            <td>
                                <input type="text" id="qty_lab${notar}" name="qty_lab[]" value="${value.qty}" min="1" class="form-control text-right" onkeyup="hp_tindakan(); formatRp(this.value, 'qty_lab${notar}')">
                            </td>
                            <td class="text-right">
                                <span id="total_lab${notar}" name="total_lab[]" class="text-right">${formatRpNoId(value.qty * value.harga)}</span>
                            </td>
                        </tr>`);

                        initailizeSelect2_tindakan_single_lab('<?= (!empty($pendaftaran) ? $pendaftaran->kode_jenis_bayar : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kelas : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kode_poli : "") ?>');

                        notar++;
                    });

                    return;
                }
            },
            error: function(error) {
                error_proccess();
            }
        })

        //erad
        $.ajax({
            url: `${siteUrl}Emr/emr_rad/${param}`,
            type: `POST`,
            dataType: `JSON`,
            success: function(result) {
                console.log(result)
                if (result.length > 0) {
                    $('#body_erad').empty();

                    var notar = 1;

                    $.each(result, function(index, value) {
                        $('#body_erad').append(`<tr id="row_erad${notar}">
                            <td class="text-center">
                                <button class="btn btn-sm btn-danger" type="button" id="btnHapusT${notar}" onclick="hapusTarif('${notar}')"><i class="fa-solid fa-delete-left"></i></button>
                            </td>
                            <td>
                                <select name="kode_rad[]" id="kode_rad${notar}" class="form-control select2_tindakan_single" data-placeholder="~ Pilih Tindakan" onchange="get_harga_tindakan(this.value, '${notar}', 'rad')">
                                    <option value="${value.kode_multiprice}">${value.nama}</option>
                                </select>
                            </td>
                            <td class="text-right">
                                <span id="harga_rad${notar}" name="harga_rad[]" class="text-right">${formatRpNoId(value.harga)}</span>
                            </td>
                            <td>
                                <input type="text" id="qty_rad${notar}" name="qty_rad[]" value="${value.qty}" min="1" class="form-control text-right" onkeyup="hp_tindakan(); formatRp(this.value, 'qty_rad${notar}')">
                            </td>
                            <td class="text-right">
                                <span id="total_rad${notar}" name="total_rad[]" class="text-right">${formatRpNoId(value.qty * value.harga)}</span>
                            </td>
                        </tr>`);

                        initailizeSelect2_tindakan_single_rad('<?= (!empty($pendaftaran) ? $pendaftaran->kode_jenis_bayar : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kelas : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kode_poli : "") ?>');

                        notar++;
                    });

                    return;
                }
            },
            error: function(error) {
                error_proccess();
            }
        })
    }
</script>

<script>
    function save() {
        if (diagnosa_per.val() == '' || diagnosa_per.val() == null) {
            return Swal.fire("Diagnosa Perawat", "Form sudah diisi?", "question");
        }

        if (anamnesa_per.val() == '' || anamnesa_per.val() == null) {
            return Swal.fire("Anamnesa Perawat", "Form sudah diisi?", "question");
        }

        $.ajax({
            url: `${siteUrl}Emr/proses_per`,
            type: `POST`,
            dataType: `JSON`,
            data: form.serialize(),
            success: function(result) {
                if (result.status == 1) { // jika mendapatkan respon 1

                    Swal.fire("EMR Perawat", "Berhasil diproses", "success").then(() => {
                        location.href = siteUrl + 'Emr';
                    });
                } else { // selain itu

                    Swal.fire("EMR Perawat", "Gagal diproses, silahkan dicoba kembali", "info");
                }
            },
            error: function(error) {
                error_proccess();
            }
        });
    }
</script>