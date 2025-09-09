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
    $btn_sv = 'disabled';
    $btn_proses = '';
    $read_sv = 'readonly';
} else {
    $btn_sv = '';
    $btn_proses = '<button type="button" class="btn btn-success float-right ml-2" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>';
    $read_sv = '';
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
    <form id="form_emr_dokter">
        <input type="hidden" name="no_trx" id="no_trx" value="<?= $no_trx ?>">
        <input type="hidden" name="kode_member" id="kode_member" value="<?= $pendaftaran->kode_member ?>">
        <div class="row">
            <div class="col-md-3">
                <div class="card card-outline card-primary main-sidebar2" <?= $style_fixed ?>>
                    <div class="card-header">
                        <span class="font-weight-bold h4 text-primary">Riwayat Pasien</span>
                        <hr>
                        <select name="filter_dokter" id="filter_dokter" class="form-control select2_dokter_all" data-placeholder="~ Pilih Dokter" onchange="history_px()">
                            <?php if (!empty($kode_dokter)) : ?>
                                <option value="<?= $kode_dokter ?>">Dr. <?= $this->M_global->getData('dokter', ['kode_dokter' => $kode_dokter])->nama ?></option>
                            <?php else : ?>
                                <option value="<?= ((!empty($pendaftaran)) ? $pendaftaran->kode_dokter : '') ?>"><?= ((!empty($pendaftaran)) ? 'Dr. ' . $this->M_global->getData('dokter', ['kode_dokter' => $pendaftaran->kode_dokter])->nama : '') ?></option>
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
                        <span class="font-weight-bold h4 text-primary"><i class="fa-solid fa-bookmark text-primary"></i> EMR Dokter</span>
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
                                                <input type="text" class="form-control" id="kode_bed" name="kode_bed" value="<?= ($pendaftaran) ? $pendaftaran->kode_bed : '' ?>" readonly>
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
                        <button type="button" id="btn_soap" class="btn btn-primary" onclick="sel_tab_emr(1)">Pemeriksaan</button>
                        <button type="button" id="btn_cppt" class="btn" onclick="sel_tab_emr(2)">CPPT</button>
                        <button type="button" id="btn_htt" class="btn" onclick="sel_tab_emr(3)">Head to Toe</button>
                        <button type="button" id="btn_order" class="btn" onclick="sel_tab_emr(4)">E-Order</button>
                        <button type="button" id="btn_ralan" class="btn" onclick="sel_tab_emr(5)">Rawat Lanjut</button>
                        <button type="button" id="plafon" class="btn btn-success float-right">Total Plafon: Rp. <span id="jml_plafon">0</span></button>
                    </div>
                    <div class="card-body">
                        <div id="soap_emr">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="tekanan_darah" class="form-label col-md-12">Tekanan Darah</label>
                                        <div class="col-md-12">
                                            <div class="input-group mb-3">
                                                <input type="text" id="tekanan_darah" name="tekanan_darah" class="form-control" placeholder="xxx" value="<?= ((!empty($emr_per)) ? $emr_per->tekanan_darah : '') ?>" readonly>
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
                                                <input type="text" id="nadi" name="nadi" class="form-control" placeholder="xxx" value="<?= ((!empty($emr_per)) ? $emr_per->nadi : '') ?>" readonly>
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
                                                <input type="text" id="suhu" name="suhu" class="form-control" placeholder="xxx" value="<?= ((!empty($emr_per)) ? $emr_per->suhu : '') ?>" readonly>
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
                                                <input type="text" id="bb" name="bb" class="form-control" placeholder="xxx" value="<?= ((!empty($emr_per)) ? $emr_per->bb : '') ?>" readonly>
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
                                                <input type="text" id="tb" name="tb" class="form-control" placeholder="xxx" value="<?= ((!empty($emr_per)) ? $emr_per->tb : '') ?>" readonly>
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
                                                <input type="text" id="pernapasan" name="pernapasan" class="form-control" placeholder="xxx" value="<?= ((!empty($emr_per)) ? $emr_per->pernapasan : '') ?>" readonly>
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
                                                <input type="text" id="saturasi" name="saturasi" class="form-control" placeholder="xxx" value="<?= ((!empty($emr_per)) ? $emr_per->saturasi : '') ?>" readonly>
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
                                            <?php
                                            if (!empty($emr_per)) {
                                                if ($emr_per->gizi == 0) {
                                                    $gizi = 'Gizi Buruk';
                                                } else if ($emr_per->gizi == 1) {
                                                    $gizi = 'Gizi Kurang';
                                                } else if ($emr_per->gizi == 2) {
                                                    $gizi = 'Gizi Cukup';
                                                } else {
                                                    $gizi = 'Gizi Lebih';
                                                }
                                            } else {
                                                $gizi = '';
                                            }
                                            ?>
                                            <input type="text" name="gizi" id="gizi" class="form-control" value="<?= $gizi ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
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
                                        <label for="anamnesa_per" class="form-label col-md-12">Anamnesa Perawat</label>
                                        <div class="col-md-12">
                                            <textarea name="anamnesa_per" id="anamnesa_per" class="form-control" rows="3" readonly><?= ((!empty($emr_per)) ? $emr_per->anamnesa_per : '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="anamnesa_dok" class="form-label col-md-12">Anamnesa Dokter <sup class="text-danger">**</sup></label>
                                        <div class="col-md-12">
                                            <textarea name="anamnesa_dok" id="anamnesa_dok" class="form-control" rows="3"><?= ((!empty($emr_dok)) ? $emr_dok->anamnesa_dok : '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="diagnosa_dok" class="form-label col-md-12">Diagnosa Dokter <sup class="text-danger">**</sup></label>
                                        <div class="col-md-12">
                                            <textarea name="diagnosa_dok" id="diagnosa_dok" class="form-control" rows="3" placeholder="Diagnosa Dokter..."><?= ((!empty($emr_dok)) ? $emr_dok->diagnosa_dok : '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="rencana_dok" class="form-label col-md-12">Anjuran/Saran <sup class="text-danger">**</sup></label>
                                        <div class="col-md-12">
                                            <textarea name="rencana_dok" id="rencana_dok" class="form-control" rows="3" placeholder="Anjuran Dokter..."><?= ((!empty($emr_dok)) ? $emr_dok->rencana_dok : '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <span class="font-weight-bold">ICD 9</span>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" name="input_icd9" id="input_icd9" placeholder="Cari ICD 9..." <?= $read_sv ?>>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-primary w-100" onclick="add_icd('9', $('#input_icd9').val())" <?= $btn_sv ?>><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <table class="table shadow-sm table-striped table-hover table-bordered" id="tableIcd9" style="width: 100%; table-layout: fixed;">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 15%; text-align: center;">Hapus</th>
                                                        <th style="width: 85%; text-align: center;">ICD 9</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="bodyIcd9">
                                                    <?php if (!empty($icd9)) : ?>
                                                        <?php $noicd9 = 1;
                                                        foreach ($icd9 as $icd9_dok) : ?>
                                                            <tr id="row_Icd9<?= $noicd9 ?>">
                                                                <td style="width: 15%; text-align: center;">
                                                                    <button class="btn btn-sm btn-danger" type="button" id="btnHapusIcd9<?= $noicd9 ?>" onclick="hapusIcd9('<?= $noicd9 ?>')" <?= $btn_sv ?>><i class="fa-solid fa-delete-left"></i></button>
                                                                </td>
                                                                <td style="width: 85%;">
                                                                    <input type="hidden" name="icd9[]" id="icd9<?= $noicd9 ?>" class="form-control" value="<?= $icd9_dok->kode_icd ?>">
                                                                    <span><?= $icd9_dok->kode_icd . ', ' . $this->M_global->getData('icd9', ['kode' => $icd9_dok->kode_icd])->keterangan ?></span>
                                                                </td>
                                                            </tr>
                                                        <?php $noicd9++;
                                                        endforeach; ?>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <span class="font-weight-bold">ICD 10</span>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" name="input_icd10" id="input_icd10" placeholder="Cari ICD 10..." <?= $read_sv ?>>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-primary w-100" onclick="add_icd('10', $('#input_icd10').val())" <?= $btn_sv ?>><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <table class="table shadow-sm table-striped table-hover table-bordered" id="tableIcd10" style="width: 100%; table-layout: fixed;">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 15%; text-align: center;">Hapus</th>
                                                        <th style="width: 85%; text-align: center;">ICD 10</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="bodyIcd10">
                                                    <?php if (!empty($icd10)) : ?>
                                                        <?php $noicd10 = 1;
                                                        foreach ($icd10 as $icd10_dok) : ?>
                                                            <tr id="row_Icd10<?= $noicd10 ?>">
                                                                <td style="width: 15%; text-align: center;">
                                                                    <button class="btn btn-sm btn-danger" type="button" id="btnHapusIcd10<?= $noicd10 ?>" onclick="hapusIcd10('<?= $noicd10 ?>')" <?= $btn_sv ?>><i class="fa-solid fa-delete-left"></i></button>
                                                                </td>
                                                                <td style="width: 85%;">
                                                                    <input type="hidden" name="icd10[]" id="icd10<?= $noicd10 ?>" class="form-control" value="<?= $icd10_dok->kode_icd ?>">
                                                                    <span><?= $icd10_dok->kode_icd . ', ' . $this->M_global->getData('icd10', ['kode' => $icd10_dok->kode_icd])->keterangan ?></span>
                                                                </td>
                                                            </tr>
                                                        <?php $noicd10++;
                                                        endforeach; ?>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="cppt_emr">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="ppa" class="form-label col-md-12">Perawat PPA <sup class="text-danger">**</sup></label>
                                        <div class="col-md-12">
                                            <select name="ppa" id="ppa" class="form-control select2_dokter_all" data-placeholder="~ Pilih PPA">
                                                <?php if (!empty($emr_dok_cppt)) : ?>
                                                    <option value="<?= $emr_dok_cppt->ppa ?>">Dr. <?= $this->M_global->getData('dokter', ['kode_dokter' => $emr_dok_cppt->ppa])->nama ?></option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="instruksi" class="form-label col-md-12">Instruksi <sup class="text-danger">**</sup></label>
                                        <div class="col-md-12">
                                            <input type="text" name="instruksi" id="instruksi" class="form-control" placeholder="Instruksi..." value="<?= (!empty($emr_dok_cppt) ? $emr_dok_cppt->instruksi : '') ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12 mb-1">
                                            <div class="row mb-1">
                                                <div class="col-md-2 m-auto">
                                                    <span>Subjective</span>
                                                </div>
                                                <div class="col-md-10">
                                                    <textarea name="soap_s" id="soap_s" class="form-control" rows="3" style="width: 100%;"><?= (!empty($emr_dok_cppt) ? $emr_dok_cppt->soap_s : '') ?></textarea>
                                                </div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-2 m-auto">
                                                    <span>Objective</span>
                                                </div>
                                                <div class="col-md-10">
                                                    <textarea name="soap_o" id="soap_o" class="form-control" rows="3" style="width: 100%;"><?= (!empty($emr_dok_cppt) ? $emr_dok_cppt->soap_o : '') ?></textarea>
                                                </div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-2 m-auto">
                                                    <span>Assessment</span>
                                                </div>
                                                <div class="col-md-10">
                                                    <textarea name="soap_a" id="soap_a" class="form-control" rows="3" style="width: 100%;"><?= (!empty($emr_dok_cppt) ? $emr_dok_cppt->soap_a : '') ?></textarea>
                                                </div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-2 m-auto">
                                                    <span>Plan</span>
                                                </div>
                                                <div class="col-md-10">
                                                    <textarea name="soap_p" id="soap_p" class="form-control" rows="3" style="width: 100%;"><?= (!empty($emr_dok_cppt) ? $emr_dok_cppt->soap_p : '') ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <span class="h5 text-primary font-weight-bold">Riwayat SOAP</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table shadow-sm table-hover table-bordered" style="width: 100%; border-radius: 10px;" id="tableNonSearch">
                                            <thead>
                                                <tr class="text-center">
                                                    <th style="width: 10%; border-radius: 10px 0px 0px 0px;">Aksi</th>
                                                    <th style="width: 35%; border-radius: 10px 0px 0px 0px;">Dokter</th>
                                                    <th style="width: 55%; border-radius: 0px 10px 0px 0px;">SOAP</th>
                                                </tr>
                                            </thead>
                                            <tbody id="body_cppt"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="htt_emr">
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
                                                                <input type="text" name="fisik[]" id="fisik<?= $nof ?>" readonly class="form-control" value="<?= $edf->fisik ?>">
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
                        <div id="ralan_emr">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label for="status_lanjut" class="form-label col-md-12">Status Lanjutan</label>
                                        <div class="col-md-12">
                                            <div class="input-group mb-3">
                                                <select name="status_lanjut" id="status_lanjut" class="form-control select2_global" data-placeholder="Pilih Status" onchange="sel_statlan(this.value)">
                                                    <option value="0" <?= ((!empty($emr_dok)) ? (($emr_dok->status_lanjut == 0) ? 'selected' : '') : '') ?>>Pulang</option>
                                                    <option value="1" <?= ((!empty($emr_dok)) ? (($emr_dok->status_lanjut == 1) ? 'selected' : '') : '') ?>>Kontrol</option>
                                                    <option value="2" <?= ((!empty($emr_dok)) ? (($emr_dok->status_lanjut == 2) ? 'selected' : '') : '') ?>>Rawat Inap</option>
                                                    <option value="3" <?= ((!empty($emr_dok)) ? (($emr_dok->status_lanjut == 3) ? 'selected' : '') : '') ?>>Rujuk</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row" id="for_ranap">
                                        <label for="buatSPRI" class="form-label col-md-12">Alasan Ranap</label>
                                        <div class="col-md-12">
                                            <div class="input-group mb-3">
                                                <input type="text" name="alasan_ranap" id="alasan_ranap" class="form-control" placeholder="Alasan..." value="<?= ((!empty($emr_dok)) ? $emr_dok->alasan_ranap : '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="for_rujuk">
                                        <label for="rujuk_ke" class="form-label col-md-12">Rujuk</label>
                                        <div class="col-md-6">
                                            <div class="input-group mb-3">
                                                <input type="text" name="rujuk_ke" id="rujuk_ke" class="form-control" placeholder="Rujuk kemana..." value="<?= ((!empty($emr_dok)) ? $emr_dok->rujuk_ke : '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group mb-3">
                                                <input type="text" name="rujuk_alasan" id="rujuk_alasan" class="form-control" placeholder="Alasan Rujuk..." value="<?= ((!empty($emr_dok)) ? $emr_dok->rujuk_alasan : '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="for_kontrol">
                                        <label for="tgl_kontrol" class="form-label col-md-12">Kontrol</label>
                                        <div class="col-md-4">
                                            <div class="input-group mb-3">
                                                <input type="date" name="tgl_kontrol" id="tgl_kontrol" class="form-control" value="<?= ((!empty($emr_dok)) ? date('Y-m-d', strtotime($emr_dok->tgl_kontrol)) : (date('Y-m-d', strtotime('+1 day')))) ?>" min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="input-group mb-3">
                                                <select name="poli_kontrol" id="poli_kontrol" class="form-control select2_poli">
                                                    <?php if (!empty($emr_dok)) : ?>
                                                        <option value="<?= $emr_dok->poli_kontrol ?>"><?= $this->M_global->getData('m_poli', ['kode_poli' => $emr_dok->poli_kontrol])->keterangan ?></option>
                                                    <?php endif; ?>
                                                </select>
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
                                <?= $btn_proses ?>
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
    const form = $('#form_emr_dokter')
    var no_trx = $("#no_trx");
    var kode_member = $("#kode_member");
    var diagnosa_per = $("#diagnosa_per");
    var diagnosa_dok = $("#diagnosa_dok");
    var rencana_dok = $("#rencana_dok");
    var anamnesa_per = $("#anamnesa_per");
    var anamnesa_dok = $("#anamnesa_dok");
    var ppa = $("#ppa");
    var instruksi = $("#instruksi");
    var filter_dokter = $("#filter_dokter");
    const btn_soap = $('#btn_soap');
    const btn_cppt = $('#btn_cppt');
    const btn_htt = $('#btn_htt');
    const btn_order = $('#btn_order');
    const btn_ralan = $('#btn_ralan');
    const soap_emr = $('#soap_emr');
    const cppt_emr = $('#cppt_emr');
    const htt_emr = $('#htt_emr');
    const order_emr = $('#order_emr');
    const ralan_emr = $('#ralan_emr');
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

    var jml_plafon = $('#jml_plafon');
    var plafon_tindakan = $('#plafon_tindakan');
    var plafon_lab = $('#plafon_lab');
    var plafon_rad = $('#plafon_rad');

    const for_ranap = $('#for_ranap')
    const for_rujuk = $('#for_rujuk')
    const for_kontrol = $('#for_kontrol')

    $(document).ready(function() {
        history_px();
        body_cppt('<?= $pendaftaran->kode_member ?>');
        sel_tab(0);
        sel_tab_emr(1);
        sel_statlan('<?= ((!empty($emr_dok)) ? $emr_dok->status_lanjut : "0") ?>');
        initailizeSelect2_tindakan_single('<?= (!empty($pendaftaran) ? $pendaftaran->kode_jenis_bayar : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kelas : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kode_poli : "") ?>');
        hp_tindakan();
        initailizeSelect2_tindakan_single_lab('<?= (!empty($pendaftaran) ? $pendaftaran->kode_jenis_bayar : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kelas : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kode_poli : "") ?>');
        hp_lab();
        initailizeSelect2_tindakan_single_rad('<?= (!empty($pendaftaran) ? $pendaftaran->kode_jenis_bayar : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kelas : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kode_poli : "") ?>');
        hp_rad();
        hitung_jml_plafon();
    });

    function sel_statlan(param) {
        if (param == 0) {
            for_ranap.hide(200)
            for_rujuk.hide(200)
            for_kontrol.hide(200)
        } else if (param == 1) {
            for_ranap.hide(200)
            for_rujuk.hide(200)
            for_kontrol.show(200)
        } else if (param == 2) {
            for_ranap.show(200)
            for_rujuk.hide(200)
            for_kontrol.hide(200)
        } else {
            for_ranap.hide(200)
            for_rujuk.show(200)
            for_kontrol.hide(200)
        }
    }

    function body_cppt(param) {
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("body_cppt").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "<?= base_url('Emr/body_cppt/'); ?>" + param, true);
        xhttp.send();
    }

    function verif_cppt(param, verif) {
        if (verif == 1) {
            var message = 'Diverifikasi!';
        } else {
            var message = 'Dibatalkan Verifikasi!';
        }

        $.ajax({
            url: `${siteUrl}Emr/verif_cppt/${param}/${verif}`,
            type: `POST`,
            dataType: `JSON`,
            success: function(result) {
                body_cppt('<?= $pendaftaran->kode_member ?>')

                if (result.status == 1) {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Berhasil " + message,
                        showConfirmButton: false,
                        timer: 500
                    });
                } else {
                    Swal.fire({
                        position: "center",
                        icon: "info",
                        title: "Gagal " + message,
                        showConfirmButton: false,
                        timer: 500
                    });
                }
            },
            error: function(error) {
                error_proccess();
            }
        });
    }

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
            btn_soap.addClass('btn-primary');
            btn_cppt.removeClass('btn-primary');
            btn_htt.removeClass('btn-primary');
            btn_order.removeClass('btn-primary');
            btn_ralan.removeClass('btn-primary');

            soap_emr.show(200);
            cppt_emr.hide(200);
            htt_emr.hide(200);
            order_emr.hide(200);
            ralan_emr.hide(200);
        } else if (param == 2) {
            btn_soap.removeClass('btn-primary');
            btn_cppt.addClass('btn-primary');
            btn_htt.removeClass('btn-primary');
            btn_order.removeClass('btn-primary');
            btn_ralan.removeClass('btn-primary');

            soap_emr.hide(200);
            cppt_emr.show(200);
            htt_emr.hide(200);
            order_emr.hide(200);
            ralan_emr.hide(200);
        } else if (param == 3) {
            btn_soap.removeClass('btn-primary');
            btn_cppt.removeClass('btn-primary');
            btn_htt.addClass('btn-primary');
            btn_order.removeClass('btn-primary');
            btn_ralan.removeClass('btn-primary');

            soap_emr.hide(200);
            cppt_emr.hide(200);
            htt_emr.show(200);
            order_emr.hide(200);
            ralan_emr.hide(200);
        } else if (param == 4) {
            btn_soap.removeClass('btn-primary');
            btn_cppt.removeClass('btn-primary');
            btn_htt.removeClass('btn-primary');
            btn_order.addClass('btn-primary');
            btn_ralan.removeClass('btn-primary');

            soap_emr.hide(200);
            cppt_emr.hide(200);
            htt_emr.hide(200);
            order_emr.show(200);
            ralan_emr.hide(200);
        } else {
            btn_soap.removeClass('btn-primary');
            btn_cppt.removeClass('btn-primary');
            btn_htt.removeClass('btn-primary');
            btn_order.removeClass('btn-primary');
            btn_ralan.addClass('btn-primary');

            soap_emr.hide(200);
            cppt_emr.hide(200);
            htt_emr.hide(200);
            order_emr.hide(200);
            ralan_emr.show(200);
        }
    }

    var input_icd9 = $('#input_icd9');

    input_icd9.keypress(function(e) {
        if (e.which == 13) { // jika di enter
            // jalankan fungsi
            return add_icd(9, input_icd9.val());
        }
    });

    var input_icd10 = $('#input_icd10');

    input_icd10.keypress(function(e) {
        if (e.which == 13) { // jika di enter
            // jalankan fungsi
            return add_icd(10, input_icd10.val());
        }
    });

    function add_icd(param, key) {
        $.ajax({
            url: `${siteUrl}Emr/getIcd/${param}/${key}`,
            type: `POST`,
            dataType: `JSON`,
            success: function(result) {
                if (param == '9') {
                    input_icd9.val('');
                } else {
                    input_icd10.val('');
                }
                const bodyIcd = $('#bodyIcd' + param);

                var tableICD = document.getElementById('tableIcd' + param);
                var x = tableICD.rows.length + 1;

                bodyIcd.append(`<tr id="row_Icd${param}${x}">
                    <td style="width: 15%; text-align: center;">
                        <button class="btn btn-sm btn-danger" type="button" id="btnHapusIcd${param}${x}" onclick="hapusIcd${param}('${x}')"><i class="fa-solid fa-delete-left"></i></button>
                    </td>
                    <td style="width: 85%;">
                        <input type="hidden" name="icd${param}[]" id="icd${param}${x}" value="${result.id}">
                        <span>${result.text}</span>
                    </td>
                </tr>`);

            },
            error: function(error) {
                error_proccess();
            }
        });
    }

    function hapusIcd9(x) {
        $('#row_Icd9' + x).remove();
    }

    function hapusIcd10(x) {
        $('#row_Icd10' + x).remove();
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
                console.table(result)
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

    async function copyTextSoap(anamnesa_dok_emr, diagnosa_dok_emr, anjuran_dok_emr, tekanan_darah_emr, nadi_emr, suhu_emr, bb_emr, tb_emr, pernapasan_emr, saturasi_emr, gizi_emr, param) {
        var anamnesa_dok_emr = document.getElementById(anamnesa_dok_emr);
        var diagnosa_dok_emr = document.getElementById(diagnosa_dok_emr);
        var anjuran_dok_emr = document.getElementById(anjuran_dok_emr);
        var tekanan_darah_emr = document.getElementById(tekanan_darah_emr);
        var nadi_emr = document.getElementById(nadi_emr);
        var suhu_emr = document.getElementById(suhu_emr);
        var bb_emr = document.getElementById(bb_emr);
        var tb_emr = document.getElementById(tb_emr);
        var pernapasan_emr = document.getElementById(pernapasan_emr);
        var saturasi_emr = document.getElementById(saturasi_emr);
        var gizi_emr = document.getElementById(gizi_emr);

        var text = '';

        text += "Tekanan Darah: " + tekanan_darah_emr.textContent + ", Nadi: " + nadi_emr.textContent + ", Suhu: " + suhu_emr.textContent + ", Berat Badan: " + bb_emr.textContent + ", Tinggi Badan: " + tb_emr.textContent + ", Pernapasan: " + pernapasan_emr.textContent + ", Saturasi: " + saturasi_emr.textContent + ", Gizi: " + gizi_emr.textContent + ", Anamnesa: " + anamnesa_dok_emr.textContent + ", Diagnosa: " + diagnosa_dok_emr.textContent + ", Anjuran: " + anjuran_dok_emr.textContent + ', ICD 9: ';

        try {
            const result = await $.ajax({
                url: `${siteUrl}Emr/emr_dok_icd9/${param}`,
                type: 'POST',
                dataType: 'JSON'
            });

            const result2 = await $.ajax({
                url: `${siteUrl}Emr/emr_dok_icd10/${param}`,
                type: 'POST',
                dataType: 'JSON'
            });

            if (result.length > 0) {
                result.forEach(function(value) {
                    text += `${value.kode_icd} - ${value.nama}, `;
                });
            }

            text += ', ICD 10: ';

            if (result2.length > 0) {
                result2.forEach(function(value) {
                    text += `${value.kode_icd} - ${value.nama}, `;
                });
            }
        } catch (error) {
            error_proccess();
            return; // Hentikan eksekusi jika terjadi kesalahan
        }

        try {
            await navigator.clipboard.writeText(text);
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Teks Berhasil Disalin',
                showConfirmButton: false,
                timer: 500
            });
        } catch (err) {
            Swal.fire({
                position: 'center',
                icon: 'warning',
                title: 'Teks Gagal Disalin',
                showConfirmButton: false,
                timer: 500
            });
        }
    }

    function implementSoap(anamnesa_dok_emr, diagnosa_dok_emr, anjuran_dok_emr, param) {
        $('#anamnesa_dok').val(anamnesa_dok_emr);
        $('#diagnosa_dok').val(diagnosa_dok_emr);
        $('#rencana_dok').val(anjuran_dok_emr);

        $.ajax({
            url: `${siteUrl}Emr/emr_dok_icd9/${param}`,
            type: `POST`,
            dataType: `JSON`,
            success: function(result) {
                if (result.length > 0) {
                    $('#bodyIcd9').empty();

                    var noicd9 = 1;
                    $.each(result, function(index, value) {
                        $('#bodyIcd9').append(`<tr id="row_Icd9${noicd9}">
                            <td style="width: 15%; text-align: center;">
                                <button class="btn btn-sm btn-danger" type="button" id="btnHapusIcd9${noicd9}" onclick="hapusIcd9('${noicd9}')"><i class="fa-solid fa-delete-left"></i></button>
                            </td>
                            <td style="width: 85%;">
                                <input type="hidden" name="icd9[]" id="icd9${noicd9}" class="form-control" value="${value.kode_icd}">
                                <span>${value.kode_icd}, ${value.nama}</span>
                            </td>
                        </tr>`)

                        noicd9++;
                    });
                }
            },
            error: function(error) {
                error_proccess();
            }
        });

        $.ajax({
            url: `${siteUrl}Emr/emr_dok_icd10/${param}`,
            type: `POST`,
            dataType: `JSON`,
            success: function(result) {
                if (result.length > 0) {
                    $('#bodyIcd10').empty();

                    var noicd10 = 1;
                    $.each(result, function(index, value) {
                        $('#bodyIcd10').append(`<tr id="row_Icd10${noicd10}">
                            <td style="width: 15%; text-align: center;">
                                <button class="btn btn-sm btn-danger" type="button" id="btnHapusIcd10${noicd10}" onclick="hapusIcd10('${noicd10}')"><i class="fa-solid fa-delete-left"></i></button>
                            </td>
                            <td style="width: 85%;">
                                <input type="hidden" name="icd10[]" id="icd10${noicd10}" class="form-control" value="${value.kode_icd}">
                                <span>${value.kode_icd}, ${value.nama}</span>
                            </td>
                        </tr>`)

                        noicd10++;
                    });
                }
            },
            error: function(error) {
                error_proccess();
            }
        });
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

    function implementOrder(notrx) {
        var tbody = $('#body_eresep');
        var tbody2 = $('#body_etarif');
        tbody.empty();
        tbody2.empty();
        var no = 1;
        var no2 = 1;

        if (!notrx) {
            return;
        }

        tbody.empty();

        $.ajax({
            url: `${siteUrl}Emr/emr_per_barang/${notrx}`,
            type: `POST`,
            dataType: `JSON`,
            success: function(result) {
                $.each(result, function(index, value) {
                    tbody.append(`<tr id="row_eresep${no}">
                        <td width="5%" class="text-center">
                            <button class="btn btn-sm btn-danger" type="button" id="btnHapus${no}" onclick="hapusBarang('${no}')" <?= $btn_diss ?>><i class="fa-solid fa-delete-left"></i></button>
                        </td>
                        <td width="30%">
                            <select name="kode_barang[]" id="kode_barang${no}" class="form-control select2_barang_stok" data-placeholder="~ Pilih Barang" onchange="getSatuan(this.value, '${no}')">
                                <option value="${value.kode_barang}">${value.nama}</option>
                            </select>
                        </td>
                        <td width="15%">
                            <select name="kode_satuan[]" id="kode_satuan${no}" class="form-control select2_global" data-placeholder="~ Pilih Satuan">
                                <option value="${value.kode_satuan}">${value.satuan}</option>
                            </select>
                        </td>
                        <td width="15%">
                            <input type="text" id="qty${no}" name="qty[]" value="${value.qty}" min="1" class="form-control text-right" onchange="hitung_st('${no}'); formatRp(this.value, 'qty${no}')" <?= $readonly ?>>
                        </td>
                        <td width="35%">
                            <textarea name="signa[]" id="signa${no}" class="form-control" <?= $readonly ?>>${value.signa}</textarea>
                        </td>
                    </tr>`);

                    initailizeSelect2_barang_stok();

                    $(".select2_global").select2({
                        placeholder: $(this).data('placeholder'),
                        width: '100%',
                        allowClear: true,
                    });

                    no++
                });
            },
            error: function(error) {
                error_proccess();
            }
        });

        $.ajax({
            url: `${siteUrl}Emr/emr_tarif/${notrx}`,
            type: `POST`,
            dataType: `JSON`,
            success: function(result) {
                $.each(result, function(index, value) {
                    tbody2.append(`<tr id="row_etarif${no2}">
                        <td class="text-center">
                            <button class="btn btn-sm btn-danger" type="button" id="btnHapusT${no2}" onclick="hapusTarif('${no2}')"><i class="fa-solid fa-delete-left"></i></button>
                        </td>
                        <td>
                            <select name="kode_tarif[]" id="kode_tarif${no2}" class="form-control select2_tindakan_single" data-placeholder="~ Pilih Tindakan">
                                <option value="${value.kode_tarif}">${value.nama}</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" id="qty_tarif${no2}" name="qty_tarif[]" value="${value.qty}" min="1" class="form-control text-right" onchange="formatRp(this.value, 'qty_tarif${no2}')">
                        </td>
                    </tr>`);

                    initailizeSelect2_tindakan_single('<?= (!empty($pendaftaran) ? $pendaftaran->kode_jenis_bayar : "") ?>', '<?= (!empty($pendaftaran) ? $pendaftaran->kelas : "") ?>');

                    no2++
                });
            },
            error: function(error) {
                error_proccess();
            }
        });
    }

    function copyTextCppt(soap_s_emr, soap_o_emr, soap_a_emr, soap_p_emr, ppa_emr, instruksi_emr, verifikasi_emr) {
        var soap_s_emr = document.getElementById(soap_s_emr).textContent;
        var soap_o_emr = document.getElementById(soap_o_emr).textContent;
        var soap_a_emr = document.getElementById(soap_a_emr).textContent;
        var soap_p_emr = document.getElementById(soap_p_emr).textContent;
        var ppa_emr = document.getElementById(ppa_emr).textContent;
        var instruksi_emr = document.getElementById(instruksi_emr).textContent;
        var verifikasi_emr = document.getElementById(verifikasi_emr).textContent;
        var text = 'PPA: ' + ppa_emr + ', Instruksi: ' + instruksi_emr + ', Verifikasi: ' + verifikasi_emr + ', SOAP (S): ' + soap_s_emr + ', SOAP (O): ' + soap_o_emr + ', SOAP (A): ' + soap_a_emr + ', SOAP (P): ' + soap_p_emr;
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

    function implementCppt(ppa_emr, ppa_emr2, instruksi_emr, soap_s_emr, soap_o_emr, soap_a_emr, soap_p_emr) {
        $('#ppa').append('<option value="' + ppa_emr + '">' + ppa_emr2 + '</option>');
        $('#instruksi').val(instruksi_emr);
        $('#soap_s').val(soap_s_emr);
        $('#soap_o').val(soap_o_emr);
        $('#soap_a').val(soap_a_emr);
        $('#soap_p').val(soap_p_emr);
    }

    function save() {
        if (diagnosa_dok.val() == '' || diagnosa_dok.val() == null) {
            return Swal.fire("Diagnosa Dokter", "Form sudah diisi?", "question");
        }

        if (rencana_dok.val() == '' || rencana_dok.val() == null) {
            return Swal.fire("Anjuran/Saran Dokter", "Form sudah diisi?", "question");
        }

        if (anamnesa_dok.val() == '' || anamnesa_dok.val() == null) {
            return Swal.fire("Anamnesa Dokter", "Form sudah diisi?", "question");
        }

        if (ppa.val() == '' || ppa.val() == null) {
            return Swal.fire("Perawat PPA", "Form sudah diisi?", "question");
        }

        if (instruksi.val() == '' || instruksi.val() == null) {
            return Swal.fire("Instruksi", "Form sudah diisi?", "question");
        }

        Swal.fire({
            title: "Pastikan Ulang",
            html: "Data yang disimpan sudah sesuai ?<br>Jika <b>YA</b>, data akan dikirim ke masing-masing unit untuk diproses!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, simpan!",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin

                // jalankan fungsi
                $.ajax({
                    url: `${siteUrl}Emr/proses_dok`,
                    type: `POST`,
                    dataType: `JSON`,
                    data: form.serialize(),
                    success: function(result) {
                        if (result.status == 1) { // jika mendapatkan respon 1

                            Swal.fire("EMR Dokter", "Berhasil diproses", "success").then(() => {
                                location.href = siteUrl + 'Emr';
                            });
                        } else { // selain itu

                            Swal.fire("EMR Dokter", "Gagal diproses, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(error) {
                        error_proccess();
                    }
                });
            }
        });
    }
</script>