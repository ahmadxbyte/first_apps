<style>
    /* :root {
        --fc-border-color: #e9ecef;
        --fc-daygrid-event-dot-width: 5px;
        --fc-button-primary: #007bff;
    } */
</style>

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

<form method="post" id="form_pendaftaran">
    <input type="hidden" name="ulang" id="ulang" value="<?= $ulang ?>">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Formulir</span>

                    <input type="hidden" name="no_anjungan" id="no_anjungan" value="<?= $no_anjungan ?>">
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <div class="row mb-3">
                                    <div class="col-md-12 mb-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="no_trx" class="control-label">No. Pendaftaran</label>
                                                <input type="hidden" name="no_triage" id="no_triage">
                                                <input type="text" class="form-control" placeholder="Otomatis" id="no_trx" name="no_trx" value="<?= (($ulang == 1) ? '' : (!empty($data_pendaftaran) ? $data_pendaftaran->no_trx : '')) ?>" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="no_antrian" class="control-label">No. Antrian</label>
                                                <input type="text" class="form-control" placeholder="Otomatis" id="no_antrian" name="no_antrian" value="<?= (($ulang == 1) ? '' : (!empty($data_pendaftaran) ? $data_pendaftaran->no_antrian : '')) ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="cara_masuk" class="control-label text-danger">Cara Masuk</label>
                                        <div class="row">
                                            <div class="col-md-8 col-6 mb-2">
                                                <select name="cara_masuk" id="cara_masuk" class="form-control select2_cara_masuk" data-placeholder="~ Cari Member">
                                                    <option value="CM00000001">Datang Sendiri</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4 col-6 mb-2">
                                                <button type="button" class="btn btn-primary w-100" title="Tambah Cara Masuk" onclick="modal_cm()"><i class="fa fa-database"></i>&nbsp;&nbsp;Data</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="kode_member" class="mt-3 control-label text-danger">Member</label>
                                        <div class="row">
                                            <div class="col-md-6 col-6">
                                                <select name="kode_member" id="kode_member" class="form-control select2_member" data-placeholder="~ Cari Member" onchange="getRiwayat(this.value); activeTindakan()">
                                                    <?php
                                                    if (!empty($data_pendaftaran)) :
                                                        $member = $this->M_global->getData('member', ['kode_member' => $data_pendaftaran->kode_member]);
                                                        echo '<option value="' . $member->kode_member . '">' . $member->kode_member . ' ~ ' . $member->nama . '</option>';
                                                    endif;
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6 col-6">
                                                <div class="row">
                                                    <div class="col-md-6 col-6">
                                                        <button type="button" class="btn btn-warning w-100" onclick="updateMember()" id="btnUMember"><i class="fa-regular fa-pen-to-square"></i>&nbsp;&nbsp;Update</button>
                                                    </div>
                                                    <div class="col-md-6 col-6">
                                                        <a type="button" href="<?= site_url('Health/form_daftar/0') ?>" class="btn btn-success w-100" id="btnNMember"><i class="fa-solid fa-user-plus"></i>&nbsp;&nbsp;Baru</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="" class="control-label text-danger">Tipe Rawat</label>
                                                <input type="hidden" id="tipe_daftar" name="tipe_daftar" value="1">
                                                <div class="row">
                                                    <div class="col-md-6 col-6">
                                                        <div class="row">
                                                            <div class="col-md-4 m-auto">
                                                                <input type="checkbox" name="rajal" id="rajal" class="form-control" onclick="changeType(1)">
                                                            </div>
                                                            <div class="col-md-8 m-auto">
                                                                <span for="">Jalan</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-6">
                                                        <div class="row">
                                                            <div class="col-md-4 m-auto">
                                                                <input type="checkbox" name="ranap" id="ranap" class="form-control" onclick="changeType(2)" disabled>
                                                            </div>
                                                            <div class="col-md-8 m-auto">
                                                                <span for="">Inap</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="" class="control-label">Tgl/Jam Daftar</label>
                                                <div class="row">
                                                    <div class="col-md-6 col-6">
                                                        <input type="date" class="form-control" id="tgl_masuk" name="tgl_masuk" value="<?= (($ulang == 1) ? ((!empty($daftar_ulang)) ? date('Y-m-d', strtotime($daftar_ulang->tgl_ulang)) : date('Y-m-d')) : ((!empty($data_pendaftaran) ? date('Y-m-d', strtotime($data_pendaftaran->tgl_daftar)) : date('Y-m-d')))) ?>" readonly>
                                                    </div>
                                                    <div class="col-md-6 col-6">
                                                        <input type="text" class="form-control" id="jam_masuk" name="jam_masuk" value="<?= (($ulang == 1) ? date('H:i:s') : ((!empty($data_pendaftaran) ? date('H:i:s', strtotime($data_pendaftaran->jam_daftar)) : date('H:i:s')))) ?>" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="kode_jenis_bayar" class="control-label text-danger">Jenis Bayar</label>
                                        <select name="kode_jenis_bayar" id="kode_jenis_bayar" class="form-control select2_jenis_bayar" data-placeholder="~ Pilih Jenis Bayar" onchange="activeTindakan()">
                                            <?php
                                            if (!empty($data_pendaftaran)) :
                                                $jenis_bayar = $this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $data_pendaftaran->kode_jenis_bayar]);
                                                echo '<option value="' . $jenis_bayar->kode_jenis_bayar . '">' . $jenis_bayar->keterangan . '</option>';
                                            else :
                                                echo '<option value="JB00000001">Perorangan</option>';
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="kelas" class="control-label text-danger">Kelas</label>
                                        <select name="kelas" id="kelas" class="form-control select2_kelas" data-placeholder="~ Pilih Kelas" onchange="activeTindakan()">
                                            <option value="Umum">Umum</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="kode_poli" class="control-label text-danger">Poli</label>
                                        <select name="kode_poli" id="kode_poli" class="form-control select2_poli" data-placeholder="~ Pilih Poli" onchange="getDokter(this.value); activeTindakan()">
                                            <?php
                                            if (!empty($data_pendaftaran)) :
                                                $poli = $this->M_global->getData('m_poli', ['kode_poli' => $data_pendaftaran->kode_poli]);
                                                echo '<option value="' . $poli->kode_poli . '">' . $poli->keterangan . '</option>';
                                            endif;
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="kode_dokter" class="control-label text-danger">Dokter Poli</label>
                                        <div class="row">
                                            <div class="col-md-9 col-9">
                                                <select name="kode_dokter" id="kode_dokter" class="form-control select2_dokter_poli" data-placeholder="~ Pilih Dokter">
                                                    <?php
                                                    if (!empty($data_pendaftaran)) :
                                                        $dokter = $this->M_global->getData('dokter', ['kode_dokter' => $data_pendaftaran->kode_dokter]);
                                                        echo '<option value="' . $dokter->kode_dokter . '">Dr. ' . $dokter->nama . '</option>';
                                                    endif;
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3 col-3">
                                                <button type="button" class="btn btn-info w-100" title="Jadwal Dokter" onclick="jadwal_dokter()"><i class="fa fa-info-circle"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 for_ranap">
                                        <label for="" class="control-label text-danger">Ruangan/Bed</label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <select name="kode_ruang" id="kode_ruang" class="form-control select2_ruang" data-placeholder="~ Pilih Ruang" onchange="getBed(this.value)">
                                                    <?php
                                                    if (!empty($data_pendaftaran)) :
                                                        $ruang = $this->M_global->getData('m_ruang', ['kode_ruang' => $data_pendaftaran->kode_ruang]);
                                                        echo '<option value="' . $ruang->kode_ruang . '">' . $ruang->keterangan . '</option>';
                                                    endif;
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <select name="kode_bed" id="kode_bed" class="form-control select2_bed" data-placeholder="~ Pilih Bed">
                                                    <?php
                                                    if (!empty($data_pendaftaran)) :
                                                        $bed = $this->M_global->getData('bed', ['kode_bed' => $data_pendaftaran->kode_bed]);
                                                        echo '<option value="' . $bed->kode_bed . '">' . $bed->nama_bed . '</option>';
                                                    endif;
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <label for="">Tarif Paket</label>
                            <div class="table-responsive">
                                <input type="hidden" name="jumPaket" id="jumPaket" value="<?= ((!empty($pasien_paket)) ? count($pasien_paket) : 0) ?>">
                                <table class="table shadow-sm table-striped table-bordered" id="tableTarifPaket" width="100%" style="border-raidus: 10px;">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="5%">Hapus</th>
                                            <th width="55%">Tindakan</th>
                                            <th width="30%">Harga</th>
                                            <th width="10%">Kunjungan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyTarifPaket">
                                        <?php if (!empty($pasien_paket)) : ?>
                                            <?php $no = 1;
                                            foreach ($pasien_paket as $pp) :
                                                $paket = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $pp->kode_tindakan]);
                                            ?>
                                                <tr id="rowPaket<?= $no ?>">
                                                    <td>
                                                        <button type="button" class="btn btn-danger" onclick="hapusTindakan('<?= $no ?>')">
                                                            <i class="fa-solid fa-delete-left"></i>
                                                        </button>
                                                    </td>
                                                    <td>
                                                        <select name="kode_multiprice[]" id="kode_multiprice<?= $no ?>" class="form-control select2_paket_tindakan" data-placeholder="~ Pilih Tindakan" onchange="getKunjungan(this.value, <?= $no ?>)">
                                                            <option value="<?= $pp->kode_multiprice ?>"><?= $paket->keterangan; ?></option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="harga[]" id="harga<?= $no ?>" class="form-control text-right" readonly value="<?= number_format($pp->harga) ?>">
                                                    </td>
                                                    <td>
                                                        <input type="text" name="kunjungan[]" id="kunjungan<?= $no ?>" class="form-control text-center" readonly value="<?= $pp->kunjungan ?>">
                                                    </td>
                                                </tr>
                                            <?php $no++;
                                            endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="tambahTarifPaket()" id="btnTambahPaket" <?= ((!empty($pasien_paket) ? (((count($pasien_paket) > 0) ? '' : 'disabled')) : 'disabled')) ?>><i class="fa-solid fa-folder-plus"></i> Tambah Tarif Paket</button>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-danger" onclick="getUrl('Health/pendaftaran')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                                </div>
                                <div class="col-md-6">
                                    <div class="float-right">
                                        <?php if (!empty($data_pendaftaran)) : ?>
                                            <button type="button" class="btn btn-info" onclick="getUrl('Health/form_pendaftaran/0')" id="btnBaru"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
                                        <?php else : ?>
                                            <button type="button" class="btn btn-info" onclick="reseting()" id="btnReset"><i class="fa-solid fa-arrows-rotate"></i>&nbsp;&nbsp;Reset</button>
                                        <?php endif ?>
                                        <button type="button" class="btn btn-success" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Data Member</span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary" id="btn_keluarga" onclick="tab(1)">Keluarga</button>
                            <button type="button" class="btn" id="btn_riwayat" onclick="tab(2)">Riawat</button>
                        </div>
                    </div>
                    <div id="keluarga">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table shadow-sm table-striped table-hover table-bordered">
                                        <thead>
                                            <tr class="text-center">
                                                <th style="width: 10%;">Keterangan</th>
                                                <th style="width: 45%;">Suami</th>
                                                <th style="width: 45%;">Istri</th>
                                            </tr>
                                            <tr>
                                                <td style="width: 10%;">Nama</td>
                                                <td style="width: 45%;">
                                                    <input type="text" name="suami" id="suami" class="form-control" readonly>
                                                </td>
                                                <td style="width: 45%;">
                                                    <input type="text" name="istri" id="istri" class="form-control" readonly>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 10%;">No Hp</td>
                                                <td style="width: 45%;">
                                                    <input type="text" name="nohp_suami" id="nohp_suami" class="form-control" readonly>
                                                </td>
                                                <td style="width: 45%;">
                                                    <input type="text" name="nohp_istri" id="nohp_istri" class="form-control" readonly>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 10%;">Alamat</td>
                                                <td style="width: 45%;">
                                                    <textarea name="alamat_suami" id="alamat_suami" class="form-control" readonly rows="3"></textarea>
                                                </td>
                                                <td style="width: 45%;">
                                                    <textarea name="alamat_istri" id="alamat_istri" class="form-control" readonly rows="3"></textarea>
                                                </td>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="riwayat">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table shadow-sm table-hover table-bordered" id="tableRiwayat" width="100%" style="border-radius: 10px;">
                                        <thead>
                                            <tr class="text-center">
                                                <th width="5%" style="border-radius: 10px 0px 0px 0px;">#</th>
                                                <th>Cabang</th>
                                                <th>No. Transaksi</th>
                                                <th>Tgl/Jam Daftar</th>
                                                <th>Tgl/Jam Keluar</th>
                                                <th>Poli</th>
                                                <th style="border-radius: 0px 10px 0px 0px;">Dokter</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodyRiwayat">
                                            <?php if (!empty($data_pendaftaran)) : ?>
                                                <?php $no = 1;
                                                foreach ($riwayat as $r) : ?>
                                                    <tr>
                                                        <td style="text-align: right;"><?= $no ?></td>
                                                        <td>
                                                            <?= $this->M_global->getData('cabang', ['kode_cabang' => $r->kode_cabang])->cabang ?>
                                                            <?php
                                                            if ($r->status_trx == 0) {
                                                                $cek_status = 'success';
                                                                $message_status = 'Proses';
                                                                $btndis = 'style="color: black;"';
                                                            } else if ($r->status_trx == 2) {
                                                                $cek_status = 'warning';
                                                                $message_status = 'Batal';
                                                                $btndis = 'style="color: black;"';
                                                            } else {
                                                                $cek_status = 'danger';
                                                                $message_status = 'Selesai';
                                                                $btndis = 'onclick="getHisPas(' . "'" . $r->no_trx . "'" . ')" style="color: blue;"';
                                                            }
                                                            ?>
                                                            <span class="float-right badge badge-<?= $cek_status ?>"><?= $message_status ?></span>
                                                        </td>
                                                        <td>
                                                            <a type="button" <?= $btndis ?>><?= $r->no_trx ?></a>
                                                        </td>
                                                        <td><?= date('Y-m-d', strtotime($r->tgl_daftar)) . ' ~ ' . date('H:i:s', strtotime($r->jam_daftar)) ?></td>
                                                        <td><?= '<span class="text-center">' . (($r->status_trx < 1) ? '-' : date('d/m/Y', strtotime($r->tgl_keluar)) . ' ~ ' . date('H:i:s', strtotime($r->jam_keluar))) . '</>' ?></td>
                                                        <td><?= $this->M_global->getData('m_poli', ['kode_poli' => $r->kode_poli])->keterangan ?></td>
                                                        <td>Dr. <?= $this->M_global->getData('dokter', ['kode_dokter' => $r->kode_dokter])->nama ?></td>
                                                    </tr>
                                                <?php $no++;
                                                endforeach; ?>
                                            <?php else : ?>
                                                <tr>
                                                    <td style="border-radius: 0px 0px 10px 10px;" colspan="8" class="text-center">Belum Ada Riwayat</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="my_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" <?= $style_modal ?>>
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Cara Masuk</h5>
                </div>
                <div class="modal-body">
                    <label for="cara_bayar">Form Cara Masuk</label>
                    <div class="row mb-3">
                        <div class="col-md-2 col-2">
                            <button type="button" class="btn btn-info w-100" onclick="reseting_cm()" title="Reset"><i class="fa fa-refresh"></i></button>
                        </div>
                        <div class="col-md-8 col-8">
                            <input type="hidden" name="kode_cara_masuk" id="kode_cara_masuk" class="form-control" readonly placeholder="Otomatis">
                            <input type="text" name="cara_masuk_m" id="cara_masuk_m" class="form-control" placeholder="Cara Masuk" onkeyup="ubah_nama(this.value, 'cara_masuk_m')">
                        </div>
                        <div class="col-md-2 col-2">
                            <button type="button" class="btn btn-primary w-100" onclick="proses_cm()" title="Proses"><i class="fa fa-server"></i></button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table shadow-sm table-hover table-bordered" id="tableCaraMasuk" width="100%" style="border-radius: 10px;">
                                <thead>
                                    <tr class="text-center">
                                        <th width="5%" style="border-radius: 10px 0px 0px 0px;">#</th>
                                        <th>Keterangan</th>
                                        <th width="25%" style="border-radius: 0px 10px 0px 0px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    foreach ($m_cara_masuk as $mcm) : ?>
                                        <tr>
                                            <td style="width: 5%;"><?= $no; ?></td>
                                            <td style="width: 70%;"><?= $mcm->keterangan ?></td>
                                            <td style="width: 25%; text-align: center;">
                                                <button type="button" class="btn btn-warning mb-2" title="Ubah" onclick="ubah_cm('<?= $mcm->kode_masuk ?>', '<?= $mcm->keterangan ?>')" <?= ($mcm->kode_masuk == 'CM00000001') ? 'disabled' : '' ?>><i class="fa fa-pen"></i></button>
                                                <button type="button" class="btn btn-danger mb-2" title="Hapus" onclick="hapus_cm('<?= $mcm->kode_masuk ?>', '<?= $mcm->keterangan ?>')" <?= ($mcm->kode_masuk == 'CM00000001') ? 'disabled' : '' ?>><i class="fa fa-ban"></i></button>
                                            </td>
                                        </tr>
                                    <?php $no++;
                                    endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- full calendar -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>

<script>
    // variable
    var table = $('#tableRiwayat');
    var body = $('#bodyRiwayat');
    var bodyPaket = $('#bodyTarifPaket');
    var no_trx = $('#no_trx');
    var kode_member = $('#kode_member');
    var kode_poli = $('#kode_poli');
    var kode_jenis_bayar = $('#kode_jenis_bayar');
    var tipe_daftar = $('#tipe_daftar');
    var kode_dokter = $('#kode_dokter');
    var kode_ruang = $('#kode_ruang');
    var kode_bed = $('#kode_bed');
    var cara_masuk = $('#cara_masuk');
    var kelas = $('#kelas');
    const btnTambahPaket = $('#btnTambahPaket');
    var modal_mg = $('#modal_mg');
    var for_ranap = $('.for_ranap');
    const my_modal = $('#my_modal');
    var kode_cara_masuk = $('#kode_cara_masuk');
    var cara_masuk_m = $('#cara_masuk_m');

    const btn_keluarga = $('#btn_keluarga');
    const btn_riwayat = $('#btn_riwayat');
    const keluarga = $('#keluarga');
    const riwayat = $('#riwayat');

    const form = $('#form_pendaftaran');
    const btnSimpan = $('#btnSimpan');

    $('#btnUMember').attr('disabled', true);
    changeType(1);
    display_ct2();
    tab(1);
    $('#kode_multiprice').attr('disabled', true);

    <?php if ($ulang == 1) : ?>
        getRiwayat('<?= (!empty($daftar_ulang) ? $daftar_ulang->kode_member : '') ?>');
        activeTindakan();

        <?php if (!empty($daftar_ulang)) : ?>
            Swal.fire("Pasien Appointment", "Pastikan jadwal dokter tersedia sebelum mendaftarkan ulang pasien!", "info");
        <?php endif; ?>
    <?php endif ?>

    <?php if (isset($_GET['membering']) && !empty($_GET['membering'])) : ?>
        var membering = '<?= htmlspecialchars($_GET['membering'], ENT_QUOTES, 'UTF-8') ?>';

        $.ajax({
            url: '<?= site_url() ?>Health/getMembering/?key=' + membering,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if (result.status == 0) {
                    Swal.fire("Member", "Member tidak ditemukan!", "error");
                    return;
                }

                $('#no_triage').val(result.no_triage);
                $('#kode_member').append('<option value="' + result.kode_member + '">' + result.kode_member + ' ~ ' + result.nama + '</option>');
                getRiwayat(result.kode_member);
            },
            error: function(error) {
                error_proccess();
            }
        });
    <?php endif; ?>

    function activeTindakan() {
        if ($('#kode_member').val() != '' && $('#kode_member').val() != null && $('#kode_poli').val() != '' && $('#kode_poli').val() != null && $('#kode_jenis_bayar').val() != '' && $('#kode_jenis_bayar').val() != null && $('#kelas').val() != '' && $('#kelas').val() != null) {
            $('#kode_multiprice').attr('disabled', false);
            btnTambahPaket.attr('disabled', false);
        } else {
            $('#kode_multiprice').attr('disabled', true);
            btnTambahPaket.attr('disabled', true);
        }
    }

    function display_ct2() {
        var x = new Date();

        // Mendapatkan jam, menit, dan detik
        var hours = x.getHours();
        var minutes = x.getMinutes();
        var seconds = x.getSeconds();

        // Menampilkan waktu pada elemen dengan id 'time'
        document.getElementById('jam_masuk').value = hours + ":" + minutes + ":" + seconds;
        setTimeout(display_ct2, 1000); // Memperbarui setiap detik
    }

    function tab(param) {
        if (param == 1) {
            btn_keluarga.addClass('btn-primary');
            btn_riwayat.removeClass('btn-primary');

            keluarga.show();
            riwayat.hide();
        } else {
            btn_keluarga.removeClass('btn-primary');
            btn_riwayat.addClass('btn-primary');

            keluarga.hide();
            riwayat.show();
        }
    }

    function modal_cm() {
        my_modal.modal('show');
    }

    function ubah_cm(k_cm, n_cm) {
        kode_cara_masuk.val(k_cm);
        cara_masuk_m.val(n_cm);
    }

    function reseting_cm() {
        kode_cara_masuk.val('');
        cara_masuk_m.val('');
    }

    function hapus_cm(k_cm, n_cm) {
        Swal.fire({
            title: "Kamu yakin?",
            text: "Data *" + n_cm + "* yang dihapus tidak bisa dikembalikan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin

                // jalankan fungsi
                $.ajax({
                    url: siteUrl + 'Health/delCaraMasuk/' + k_cm,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik
                        my_modal.modal('hide');

                        if (result.status == 1) { // jika mendapatkan hasil 1
                            Swal.fire("Cara Masuk", "Berhasil di hapus!", "success").then(() => {
                                location.href = '<?= base_url() ?>Health/form_pendaftaran/0'
                            });
                        } else { // selain itu
                            Swal.fire("Cara Masuk", "Gagal di hapus!, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error

                        error_proccess();
                    }
                });
            }
        });
    }

    function proses_cm() {
        var k_cm2 = $('#kode_cara_masuk').val();

        if (k_cm2 == '' || k_cm2 == null) {
            var inkode = 1;
            var message = 'ditambahkan';
        } else {
            var inkode = 2;
            var message = 'diperbarui';
        }

        $.ajax({
            url: siteUrl + 'Health/proses_cara_masuk/' + inkode,
            type: 'POST',
            dataType: 'JSON',
            data: form.serialize(),
            success: function(result) {
                if (result.status == 1) { // jika mendapatkan hasil 1
                    Swal.fire("Cara Masuk", "Berhasil " + message, "success").then(() => {
                        location.href = '<?= base_url() ?>Health/form_pendaftaran/0'
                    });
                } else { // selain itu
                    Swal.fire("Cara Masuk", "Gagal " + message + ", silahkan dicoba kembali", "info");
                }
            },
            error: function(error) {
                error_proccess();
            }
        });
    }

    function tambahTarifPaket() {
        var jum = Number($('#jumPaket').val());
        var row = jum + 1;

        $('#jumPaket').val(row);

        bodyPaket.append(`<tr id="rowPaket${row}">
            <td>
                <button type="button" class="btn btn-danger" onclick="hapusTindakan(${row})">
                    <i class="fa-solid fa-delete-left"></i>
                </button>
            </td>
            <td>
                <select name="kode_multiprice[]" id="kode_multiprice${row}" class="form-control select2_paket_tindakan" data-placeholder="~ Pilih Tindakan" onchange="getKunjungan(this.value, ${row})"></select>
            </td>
            <td>
                <input type="text" name="harga[]" id="harga${row}" class="form-control text-right" readonly value="0.00">
            </td>
            <td>
                <input type="text" name="kunjungan[]" id="kunjungan${row}" class="form-control text-center" readonly value="1">
            </td>
        </tr>`);

        $('#kode_multiprice').attr('disabled', true);

        activeTindakan();

        initailizeSelect2_paket_tindakan(`${$('#kode_jenis_bayar').val()}`, `${$('#kelas').val()}`, `${$('#kode_poli').val()}`);
    }

    function changeType(param) {
        var rajal = document.getElementById('rajal');
        var ranap = document.getElementById('ranap');

        if (param == 1) {
            rajal.checked = true;
            ranap.checked = false;
            tipe_daftar.val(1)
            for_ranap.hide(200)
        } else {
            changeType(1)

            Swal.fire("Rawat Inap", "Coming Soon", "info");
        }
    }

    function hapusTindakan(i) {
        $('#rowPaket' + i).remove();
    }

    function getKunjungan(kd_mltp, i) {
        if (!kd_mltp || kd_mltp === null) {
            return
        }

        var kd_mbr = $('#kode_member').val();

        $.ajax({
            url: siteUrl + 'Health/getPaket/' + kd_mltp + '/' + kd_mbr,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                console.log(result)
                if (result.status == 1) {
                    $('#kunjungan' + i).val(result.kunjungan);
                    $('#harga' + i).val(formatRpNoId(result.harga));
                }
            },
            error: function(result) {
                error_proccess();
            }
        });
    }

    // fungsi get dokter berdasarkan kode poli
    function getDokter(kode_poli) {
        kode_dokter.empty();

        if (kode_poli == '' || kode_poli == null) { // jika kode poli kosong/ null
            // tampilkan notif
            Swal.fire("Poli", "Sudah dipilih?", "question");
            // set param jadi kosong
            var param = '';
        } else {
            // set param menjadi kode poli
            var param = kode_poli;
        }

        // jalankan select2 berdasarkan param
        initailizeSelect2_dokter_poli(param);
    }

    // fungsi get bed berdasarkan ruang
    function getBed(param) {
        kode_bed.empty();

        if (param == '' || param == null) { // jika kode ruang kosong/ null
            // tampilkan notif
            Swal.fire("Poli", "Sudah dipilih?", "question");
            // set param jadi kosong
            var param = '';
        } else {
            // set param menjadi kode ruang
            var param = param;
        }

        // jalankan select2 berdasarkan param
        initailizeSelect2_bed(param);
    }

    // fungsi save/update
    function save() {
        btnSimpan.attr('disabled', true);

        if (kode_member.val() == '' || kode_member.val() == null) { // jika kode_member kosong/ null
            btnSimpan.attr('disabled', false);

            return Swal.fire("Member", "Sudah dipilih?", "question");
        }

        if (kode_dokter.val() == '' || kode_dokter.val() == null) { // jika kode_dokter kosong/ null
            btnSimpan.attr('disabled', false);

            return Swal.fire("Dokter", "Sudah dipilih?", "question");
        }

        if (kode_jenis_bayar.val() == '' || kode_jenis_bayar.val() == null) { // jika kode_jenis_bayar kosong/ null
            btnSimpan.attr('disabled', false);

            return Swal.fire("Jenis Bayar", "Sudah dipilih?", "question");
        }

        if (tipe_daftar.val() == 1) {
            if ($('#kode_poli').val() == '' || $('#kode_poli').val() == null) { // jika kode_poli kosong/ null
                btnSimpan.attr('disabled', false);

                return Swal.fire("Poli", "Sudah dipilih?", "question");
            }

        } else {
            if (kode_ruang.val() == '' || kode_ruang.val() == null) { // jika kode_ruang kosong/ null
                btnSimpan.attr('disabled', false);

                return Swal.fire("Ruang", "Sudah dipilih?", "question");
            }

            if (kode_bed.val() == '' || kode_bed.val() == null) { // jika kode_bed kosong/ null
                btnSimpan.attr('disabled', false);

                return Swal.fire("Ruang", "Sudah dipilih?", "question");
            }
        }

        if (no_trx.val() == '' || no_trx.val() == null) { // jika kode no_trx kosong/ null
            // isi param = 1
            var param = 1;
        } else { // selain itu
            // isi param = 2
            var param = 2;
        }

        // jalankan proses cek logistik
        if (param == 1) {
            $.ajax({
                url: siteUrl + 'Health/cekStatusMember',
                type: 'POST',
                dataType: 'JSON',
                data: form.serialize(),
                success: function(result) { // jika fungsi berjalan dengan baik
                    if (result.status == 1) { // jika mendapatkan respon 1
                        // jalankan fungsi proses berdasarkan param
                        proses(param);
                    } else { // selain itu
                        btnSimpan.attr('disabled', false);

                        Swal.fire("Member " + result.kode_member, "Sudah terdaftar di cabang <b>" + result.cabang + "</b> pada tanggal <b>" + result.tgl + "</b><br>Silahkan <b>hubungi cabang terkait</b> untuk diselesaikan!", "info");
                    }
                },
                error: function(result) { // jika fungsi error
                    btnSimpan.attr('disabled', false);

                    error_proccess();
                }
            });
        } else {
            proses(param);
        }
    }

    // fungsi proses
    function proses(param) {
        if (param == 1) { // jika param 1 berarti insert/tambah
            var message = 'dibuat!';
        } else { // selain itu berarti update/ubah
            var message = 'diperbarui!';
        }

        // jalankan proses dengan param insert/update
        $.ajax({
            url: siteUrl + 'Health/pendaftaran_proses/' + param,
            type: "POST",
            data: $('#form_pendaftaran').serialize(),
            dataType: "JSON",
            success: function(result) { // jika fungsi berjalan dengan baik
                btnSimpan.attr('disabled', false);

                if (result.status == 2) {
                    Swal.fire("Limit Pendaftaran " + result.limit + " Pasien", "Pasien Dr. " + result.dokter + " sudah penuh, mohon maaf silahkan lakukan diesok hari, Terima kasih!", "info");
                } else if (result.status == 1) { // jika mendapatkan respon 1

                    Swal.fire("Pendaftaran", "Berhasil " + message, "success").then(() => {
                        // querstion(result.no_trx);
                        getUrl('Health/pendaftaran');
                    });
                } else { // selain itu

                    Swal.fire("Pendaftaran", "Gagal " + message + ", silahkan dicoba kembali", "info");
                }
            },
            error: function(result) { // jika fungsi error
                btnSimpan.attr('disabled', false);

                error_proccess();
            }
        });
    }

    function querstion(param) {
        Swal.fire({
            title: "Cetak Berkas?",
            text: "Berkas bukti pendaftaran pasien!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, cetak!",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin
                getUrl('Health/pendaftaran');
                getDetail(param);
            } else {
                getUrl('Health/pendaftaran');
            }
        });
    }

    // fungsi ambil riwayat
    function getRiwayat(kode_member) {
        if (kode_member == '' || kode_member == null) { // jika kode_member kosong/ null
            // kosongkan body
            $('#btnUMember').attr('disabled', true);
            return body.empty();
        }

        $('#btnUMember').attr('disabled', false);
        // kosongkan body
        body.empty();

        // jalankan fungsi
        $.ajax({
            url: '<?= site_url() ?>Health/getRiwayat/' + kode_member,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) { // jika fungsi berjalan
                var no = 1;

                // loop hasil
                $.each(result[0], function(index, value) {
                    if (value.tgl_keluar == null) { // jika tgl keluarnya null
                        // beri nilai minus/ strip
                        var keluar = "-";
                    } else { // selain itu
                        // beri nilai sesuai record db
                        var keluar = value.tgl_keluar + ' ~ ' + value.jam_keluar;
                    }

                    if (value.status_trx == 0) {
                        var cek_color = 'success';
                        var message = 'Proses';

                        var btndis = 'style="color: black;"';
                    } else if (value.status_trx == 2) {
                        var cek_color = 'warning';
                        var message = 'Batal';

                        var btndis = `style="color: black;"`;
                    } else {
                        var cek_color = 'danger';
                        var message = 'Selesai';

                        var btndis = ` onclick="getHisPas('${value.no_trx}')" style="color: blue;"`;
                    }

                    // tampilkan ke bodyRiwayat
                    $('#bodyRiwayat').append(`<tr>
                        <td style="text-align: right;">${no}</td>
                        <td>${value.cabang} <span class="float-right badge badge-${cek_color}">${message}</span></td>
                        <td>
                            <a type="button" ${btndis}>${value.no_trx}</a>
                        </td>
                        <td>${value.tgl_daftar} ~ ${value.jam_daftar}</td>
                        <td>${keluar}</td>
                        <td>${value.nama_poli}</td>
                        <td>Dr. ${value.nama_dokter}</td>
                    </tr>`);
                    no++;
                });

                $('#suami').val(result[1].suami);
                $('#nohp_suami').val(result[1].nohp_suami);
                $('#alamat_suami').val(result[1].alamat_suami);
                $('#istri').val(result[1].istri);
                $('#nohp_istri').val(result[1].nohp_istri);
                $('#alamat_istri').val(result[1].alamat_istri);
            },
            error: function(result) { // jika fungsi error

                // jalankan fungsi error
                error_proccess();
            }
        });
    }

    // fungsi update data member
    function updateMember() {
        var param = kode_member.val();
        if (param == '' || param == null) {
            return Swal.fire("Member", "Sudah dipilih?", "question");
        }

        getUrl('Health/form_daftar/' + param);
    }

    function reseting() {
        $('#no_trx').val('');
        $('#no_antrian').val('');
        $('#kode_member').val('').change();
        $('#kode_poli').val('').change();
        $('#kode_dokter').val('').change();
        $('#kode_ruang').val('').change();
        $('#kode_jenis_bayar').val('').change();
        $('#cara_masuk').html('<option value="CM00000001">Diri Sendiri</option>');
        $('#kelas').html('<option value="Umum">Umum</option>');
        bodyPaket.empty();
        body.empty();
    }

    // fungsi lihat detail
    function getDetail(param) {
        window.open(siteUrl + 'Health/print_pendaftaran/' + param + '/0', '_blank');
    }

    // fungsi lihat detail
    function getHisPas(param) {
        $.ajax({
            url: siteUrl + 'Health/getToken/' + param,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if (result.status == 1) {
                    window.open(siteUrl + 'Kasir/print_kwitansi/' + result.token + '/0', '_blank');
                } else {
                    Swal.fire("History Pasien", "Gagal diambil, silahkan dicoba kembali", "info");
                }
            },
            error: function(result) {
                error_proccess();
            }
        });
    }

    function showGuide() {
        // ubah ukuran modal
        $('.modal-dialog').removeClass('modal-lg')
        $('.modal-dialog').addClass('modal-xl')

        // clean text
        $('#modal_mgLabel').text(``);
        $('#modal-isi').text(``);

        $('#modal_mg').modal('show'); // show modal

        // isi text
        $('#modal_mgLabel').append(`Manual Guide Pendaftaran`);
        $('#modal-isi').append(`
            <ol>
                <li style="font-weight: bold;">Tambah Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Tambah</li>
                        <li>Selanjutnya isikan Form berikut:</li>
                        <p>
                            <ul>
                                <li>Cari Member yang akan didaftarkan<br>(jika ingin update data member, klik tombol Update di kanan form member)</li>
                                <li>Pilih Ruangan/Bed</li>
                                <li>Pilih Poli</li>
                                <li>Pilih Dokter, dan</li>
                                <li>Pilih Tarif Paket jika menggunakan Tarif Paket, jika tidak maka tidak perlu di pilih</li>
                            </ul>
                        </p>
                        <li><span style='color: red;'>Teks berawarna merah</span> mengartikan wajib terisi</li>
                        <li>Klik tombol Proses</li>
                    </ul>
                </p>
                <li style="font-weight: bold;">Ubah Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Ubah pada list data yang ingin di ubah</li>
                        <li>Selanjutnya ubah Form yang ingin diubah, diantaranya:</li>
                        <p>
                            <ul>
                                <li>Form Member yang akan didaftarkan<br>(jika ingin update data member, klik tombol Update di kanan form member)</li>
                                <li>Form Ruangan/Bed</li>
                                <li>Form Poli</li>
                                <li>Form Dokter, dan</li>
                                <li>Form Tarif Paket jika menggunakan Tarif Paket, jika tidak maka tidak perlu di pilih</li>
                            </ul>
                        </p>
                        <li><span style='color: red;'>Teks berawarna merah</span> mengartikan wajib terisi</li>
                        <li>Klik tombol Proses</li>
                    </ul>
                </p>
            </ol>
        `);
    }

    // modal jadwal dokter
    function jadwal_dokter() {
        // ubah ukuran modal
        $('.modal-dialog').removeClass('modal-lg')
        $('.modal-dialog').addClass('modal-xl')

        // clean text
        $('#modal_mgLabel').text(``);
        $('#modal-isi').text(``);

        setTimeout(function() {
            kalendar();
        }, 500); // Add delay to ensure modal is fully shown

        $('#modal_mg').modal('show'); // show modal

        $('#modal_mgLabel').text(`Jadwal Dokter`);
        $('#modal-isi').append("<div id='calendar' style='min-height: 700px;'></div>"); // Adjusted height

    }

    // fungsi kalendar
    function kalendar() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'id', // ubah lokasi ke indonesia
            editable: true,
            headerToolbar: { // menampilkan button yang akan ditampilkan
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: { // merubah text button
                today: 'Hari ini',
                month: 'Bulan',
                week: 'Minggu',
                day: 'Hari'
            },
            customButtons: { // merubah text button
                prev: {
                    text: 'Sebelumnya',
                    click: function() {
                        calendar.prev();
                    }
                },
                next: {
                    text: 'Berikutnya',
                    click: function() {
                        calendar.next();
                    }
                }
            },
            events: { // load data fullcalendar
                url: siteUrl + 'Health/jdokter_list',
                method: 'GET',
                failure: function() {
                    Swal.fire("Jadwal Dokter", "Gagal diload", "error");
                },
                allDay: false
            },
            eventContent: function(arg) { // mengubah tanda koma (,) menjadi <br>
                let title = arg.event.title.split(',').join('<br>');
                return {
                    html: title
                };
            },
            eventDidMount: function(info) {
                // Mengatur warna teks menjadi putih
                info.el.style.color = 'white';

                // Mengatur warna latar belakang berdasarkan status
                switch (info.event.extendedProps.status_dokter) {
                    case '1': // Hadir
                        info.el.style.backgroundColor = '#007bff'; // Biru
                        break;
                    case '2': // Izin
                        info.el.style.backgroundColor = '#ffd000'; // Kuning
                        break;
                    case '3': // Sakit
                        info.el.style.backgroundColor = '#ed1e32'; // Merah
                        break;
                    case '4': // Cuti
                        info.el.style.backgroundColor = '#2aae47'; // Hijau
                        break;
                    default:
                        info.el.style.backgroundColor = '#76818d'; // Warna default jika status tidak dikenali
                }
            },
            eventMouseEnter: function(info) {
                // Fungsi untuk memformat tanggal
                function formatDate(date) {
                    const yyyy = date.getFullYear();
                    let mm = date.getMonth() + 1;
                    let dd = date.getDate();

                    if (dd < 10) dd = '0' + dd;
                    if (mm < 10) mm = '0' + mm;

                    return dd + '-' + mm + '-' + yyyy;
                }

                const start_date = info.event.start || new Date(info.event.startStr); // fallback ke startStr jika start tidak ada
                const formattedStartDate = formatDateWithDay(start_date); // Memformat tanggal mulai

                const end_date = info.event.end || new Date(info.event.endStr); // fallback ke endStr jika end tidak ada
                const formattedEndDate = formatDateWithDay(end_date); // Memformat tanggal selesai

                const formattedStartTime = formatTime(info.event.extendedProps.time_start);
                const formattedEndTime = formatTime(info.event.extendedProps.time_end);

                if (info.event.extendedProps.limit_px == 0) {
                    var limit_px = 'Tidak Terbatas';
                } else {
                    var limit_px = info.event.extendedProps.limit_px + ' Pasien';
                }

                // Buat tooltip dengan informasi dokter, waktu mulai dan selesai, serta catatan
                $(info.el).tooltip({
                    title: 'Nama: ' + info.event.extendedProps.nama_dokter + '<br>Hari: ' + formattedStartDate + ' (' + formattedStartTime + ' - ' + formattedEndTime + ')<br>Limit Pasien: ' + limit_px + '<br>Catatan: ' + info.event.extendedProps.comment,
                    html: true,
                    placement: 'top'
                });

                // Tampilkan tooltip
                $(info.el).tooltip('show');
            },
            eventMouseLeave: function(info) { // saat tidak di hover
                // sembunyikan tooltip
                $(info.el).tooltip('hide');
            }
        });
        calendar.render();
    }
</script>