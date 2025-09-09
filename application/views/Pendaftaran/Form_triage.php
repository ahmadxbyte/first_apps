<?php
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

<form id="form_triage">
    <div class="row">
        <div class="col-md-3">
            <div class="card card-outline card-primary main-sidebar2" <?= $style_fixed ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4 text-primary"><i class="fa-solid fa-bookmark text-primary"></i> Data Pasien</span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="nama" class="form-label col-md-12 text-danger">Nama/No RM</label>
                            <input type="text" class="form-control" id="nama" name="nama" value="" placeholder="Disarankan No RM">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="jkel" class="control-label">Jenis Kelamin</label>
                            <select name="jkel" id="jkel" class="form-control select2_global" data-placeholder="~ Pilih Gender">
                                <option value="">~ Pilih Gender</option>
                                <option value="P">Laki-laki</option>
                                <option value="W">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="tgl_lahir" class="control-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" name="tgl_lahir" id="tgl_lahir" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="kontak" class="control-label">Kontak</label>
                            <input type="text" class="form-control" name="kontak" id="kontak" placeholder="08xx xxxx xxxx">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="alamat" class="control-label">Alamat</label>
                            <textarea name="alamat" id="alamat" class="form-control" rows="1"></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="" class="control-label">Masuk UGD</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="date" name="tgl_masuk" id="tgl_masuk" class="form-control" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="jam_masuk" id="jam_masuk" class="form-control" value="<?= date('H:i:s') ?>" min="<?= date('H:i:s') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="cara_masuk" class="control-label">Cara Masuk</label>
                            <select name="cara_masuk" id="cara_masuk" class="form-control select2_cara_masuk" data-placeholder="~ Cara Masuk">
                                <option value="CM00000001">Datang Sendiri</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4 text-primary"><i class="fa-solid fa-bookmark text-primary"></i> Form Skrining</span>
                </div>
                <div class="card-footer">
                    <button type="button" id="btn_assesment" class="btn btn-primary" onclick="sel_tab_emr(1)">Assesment</button>
                    <button type="button" id="btn_pemeriksaan" class="btn" onclick="sel_tab_emr(2)">Pemeriksaan</button>
                    <button type="button" id="btn_psiko" class="btn" onclick="sel_tab_emr(3)">Psikologi & Spiritual</button>
                    <button type="button" id="btn_htt" class="btn" onclick="sel_tab_emr(4)">Head to Toe</button>
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
                                            <option value="0" selected>Tidak</option>
                                            <option value="1">Ya</option>
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
                                            <option value="0" selected>Tidak</option>
                                            <option value="1">Ya</option>
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
                                            <option value="0" selected>Tidak</option>
                                            <option value="1">Ya</option>
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
                                <textarea name="keterangan_assesment" id="keterangan_assesment" class="form-control" rows="3" placeholder="Keterangan Lain"></textarea>
                            </div>
                        </div>
                    </div>
                    <div id="pemeriksaan_emr">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="row">
                                    <label for="label_triage" class="form-label col-md-12 text-danger">Label Triage</label>
                                    <input type="hidden" id="label_triage" name="label_triage" value="0">
                                    <div class="col-md-12">
                                        <table>
                                            <tr>
                                                <td style="width: 5%;">
                                                    <input type="checkbox" style="width: 25px;" name="lt_0" id="lt_0" onclick="selLt(0)" class="form-control checkbox-hijau" checked>
                                                    <style>
                                                        /* Checkbox custom hijau */
                                                        .checkbox-hijau[type="checkbox"] {
                                                            accent-color: #28a745;
                                                            /* Bootstrap green */
                                                        }

                                                        /* Untuk browser yang tidak support accent-color */
                                                        .checkbox-hijau[type="checkbox"]:checked {
                                                            box-shadow: 0 0 0 2px #28a745;
                                                            border-color: #28a745;
                                                            background-color: #28a745;
                                                        }
                                                    </style>
                                                </td>
                                                <td style="width: 20%;">
                                                    <label for="" class="m-auto">&nbsp;&nbsp;Hijau</label>
                                                </td>
                                                <td style="width: 5%;">
                                                    <input type="checkbox" style="width: 25px;" name="lt_1" id="lt_1" onclick="selLt(1)" class="form-control checkbox-kuning">
                                                    <style>
                                                        /* Checkbox custom kuning */
                                                        .checkbox-kuning[type="checkbox"] {
                                                            accent-color: #d5e031;
                                                            /* Bootstrap green */
                                                        }

                                                        /* Untuk browser yang tidak support accent-color */
                                                        .checkbox-kuning[type="checkbox"]:checked {
                                                            box-shadow: 0 0 0 2px #d5e031;
                                                            border-color: #d5e031;
                                                            background-color: #d5e031;
                                                        }
                                                    </style>
                                                </td>
                                                <td style="width: 20%;">
                                                    <label for="" class="m-auto">&nbsp;&nbsp;Kuning</label>
                                                </td>
                                                <td style="width: 5%;">
                                                    <input type="checkbox" style="width: 25px;" name="lt_2" id="lt_2" onclick="selLt(2)" class="form-control checkbox-merah">
                                                    <style>
                                                        /* Checkbox custom merah */
                                                        .checkbox-merah[type="checkbox"] {
                                                            accent-color: #bb1414;
                                                            /* Bootstrap green */
                                                        }

                                                        /* Untuk browser yang tidak support accent-color */
                                                        .checkbox-merah[type="checkbox"]:checked {
                                                            box-shadow: 0 0 0 2px #bb1414;
                                                            border-color: #bb1414;
                                                            background-color: #bb1414;
                                                        }
                                                    </style>
                                                </td>
                                                <td style="width: 20%;">
                                                    <label for="" class="m-auto">&nbsp;&nbsp;Merah</label>
                                                </td>
                                                <td style="width: 5%;">
                                                    <input type="checkbox" style="width: 25px;" name="lt_3" id="lt_3" onclick="selLt(3)" class="form-control checkbox-hitam">
                                                    <style>
                                                        /* Checkbox custom hitam */
                                                        .checkbox-hitam[type="checkbox"] {
                                                            accent-color: #2c2c2c;
                                                            /* Bootstrap green */
                                                        }

                                                        /* Untuk browser yang tidak support accent-color */
                                                        .checkbox-hitam[type="checkbox"]:checked {
                                                            box-shadow: 0 0 0 2px #2c2c2c;
                                                            border-color: #2c2c2c;
                                                            background-color: #2c2c2c;
                                                        }
                                                    </style>
                                                </td>
                                                <td style="width: 20%;">
                                                    <label for="" class="m-auto">&nbsp;&nbsp;Hitam</label>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label for="petugas_triage" class="form-label col-md-12">Petugas Triage</label>
                                    <div class="col-md-12">
                                        <input type="text" name="petugas_triage" id="petugas_triage" class="form-control" value="<?= $this->M_global->getData('user', ['email' => $this->session->userdata('email')])->nama ?>" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="row">
                                    <label for="anamnesa_per" class="form-label col-md-12 text-danger">Anamnesa</label>
                                    <div class="col-md-12">
                                        <textarea name="anamnesa_per" id="anamnesa_per" class="form-control" rows="3" placeholder="Anamnesa..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label for="diagnosa_per" class="form-label col-md-12 text-danger">Diagnosa</label>
                                    <div class="col-md-12">
                                        <textarea name="diagnosa_per" id="diagnosa_per" class="form-control" rows="3" placeholder="Diagnosa..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="row">
                                    <label for="penyakit_keluarga_his" class="form-label col-md-12">Penyakit Keluarga</label>
                                    <div class="col-md-12">
                                        <textarea name="penyakit_keluarga" id="penyakit_keluarga" class="form-control" rows="3" placeholder="Penyakit Baru..."></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label for="alergi_his" class="form-label col-md-12">Alergi</label>
                                    <div class="col-md-12">
                                        <textarea name="alergi" id="alergi" class="form-control" rows="3" placeholder="Alergi Baru..."></textarea>
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
                                            <input type="text" id="tekanan_darah" name="tekanan_darah" class="form-control" placeholder="xxx" value="">
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
                                            <input type="text" id="nadi" name="nadi" class="form-control" placeholder="xxx" value="">
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
                                            <input type="text" id="suhu" name="suhu" class="form-control" placeholder="xxx" value="">
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
                                            <input type="text" id="bb" name="bb" class="form-control" placeholder="xxx" value="">
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
                                            <input type="text" id="tb" name="tb" class="form-control" placeholder="xxx" value="">
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
                                            <input type="text" id="pernapasan" name="pernapasan" class="form-control" placeholder="xxx" value="">
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
                                            <input type="text" id="saturasi" name="saturasi" class="form-control" placeholder="xxx" value="">
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
                                            <option value="0">Gizi Buruk</option>
                                            <option value="1">Gizi Kurang</option>
                                            <option value="2">Gizi Cukup</option>
                                            <option value="3">Gizi Lebih</option>
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
                                            <option value="0">Tidak</option>
                                            <option value="1">Ya</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label for="hpht" class="form-label col-md-12">HPHT</label>
                                    <div class="col-md-12">
                                        <input type="date" name="hpht" id="hpht" class="form-control" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <textarea name="keterangan_hamil" id="keterangan_hamil" class="form-control" rows="3" placeholder="Keterangan Hamil..."></textarea>
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
                                            <input type="hidden" id="scale" name="scale" class="form-control" value="0">
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
                                    <input type="hidden" id="bicara" name="bicara" class="form-control" value="1">
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
                                                <textarea name="gangguan_bcr" id="gangguan_bcr" class="form-control" rows="1" placeholder="Keterangan Gangguan Bicara..."></textarea>
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
                                    <input type="hidden" id="emosi" name="emosi" class="form-control" value="1">
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
                                    <input type="hidden" id="spiritual" name="spiritual" class="form-control" value="1">
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
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-success float-right ml-2" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                            <button type="button" class="btn btn-info float-right" onclick="reseting()" id="btnReset"><i class="fa-solid fa-arrows-rotate"></i>&nbsp;&nbsp;Reset</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


<script>
    const form = $('#form_triage');
    var nama = $('#nama');
    var jkel = $('#jkel');
    var tgl_lahir = $('#tgl_lahir');
    var kontak = $('#kontak');
    var alamat = $('#alamat');
    var kode_member = $('#kode_member');
    const btn_assesment = $('#btn_assesment');
    const btn_pemeriksaan = $('#btn_pemeriksaan');
    const btn_psiko = $('#btn_psiko');
    const btn_htt = $('#btn_htt');
    const assesment_emr = $('#assesment_emr');
    const pemeriksaan_emr = $('#pemeriksaan_emr');
    const psiko_emr = $('#psiko_emr');
    const htt = $('#htt');
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

    $(document).ready(function() {
        cek_scale('<?= ((!empty($emr_per)) ? $emr_per->scale : '1') ?>');
        cek_bcr(<?= ((!empty($emr_per)) ? $emr_per->bicara : '1') ?>);
        cek_emosi(<?= ((!empty($emr_per)) ? $emr_per->emosi : '1') ?>);
        cek_spiritual(<?= ((!empty($emr_per)) ? $emr_per->spiritual : '1') ?>);
        sel_tab_emr(1);
        cek_resiko();
        display_ct2();
    });

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

    function sel_tab_emr(param) {
        if (param == 1) {
            btn_assesment.addClass('btn-primary');
            btn_pemeriksaan.removeClass('btn-primary');
            btn_psiko.removeClass('btn-primary');
            btn_htt.removeClass('btn-primary');

            assesment_emr.show(200);
            pemeriksaan_emr.hide(200);
            psiko_emr.hide(200);
            htt.hide(200);
        } else if (param == 2) {
            btn_assesment.removeClass('btn-primary');
            btn_pemeriksaan.addClass('btn-primary');
            btn_psiko.removeClass('btn-primary');
            btn_htt.removeClass('btn-primary');

            assesment_emr.hide(200);
            pemeriksaan_emr.show(200);
            psiko_emr.hide(200);
            htt.hide(200);
        } else if (param == 3) {
            btn_assesment.removeClass('btn-primary');
            btn_pemeriksaan.removeClass('btn-primary');
            btn_psiko.addClass('btn-primary');
            btn_htt.removeClass('btn-primary');

            assesment_emr.hide(200);
            pemeriksaan_emr.hide(200);
            psiko_emr.show(200);
            htt.hide(200);
        } else {
            btn_assesment.removeClass('btn-primary');
            btn_pemeriksaan.removeClass('btn-primary');
            btn_psiko.removeClass('btn-primary');
            btn_htt.addClass('btn-primary');

            assesment_emr.hide(200);
            pemeriksaan_emr.hide(200);
            psiko_emr.hide(200);
            htt.show(200);
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

    function selLt(param) {
        $('#label_triage').val(param);
        if (param == 0) {
            document.getElementById('lt_0').checked = true;
            document.getElementById('lt_1').checked = false;
            document.getElementById('lt_2').checked = false;
            document.getElementById('lt_3').checked = false;
        } else if (param == 1) {
            document.getElementById('lt_0').checked = false;
            document.getElementById('lt_1').checked = true;
            document.getElementById('lt_2').checked = false;
            document.getElementById('lt_3').checked = false;
        } else if (param == 2) {
            document.getElementById('lt_0').checked = false;
            document.getElementById('lt_1').checked = false;
            document.getElementById('lt_2').checked = true;
            document.getElementById('lt_3').checked = false;
        } else {
            document.getElementById('lt_0').checked = false;
            document.getElementById('lt_1').checked = false;
            document.getElementById('lt_2').checked = false;
            document.getElementById('lt_3').checked = true;
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

    function save() {
        if ($('#nama').val() === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Nama Pasien tidak boleh kosong!'
            });
            nama.focus();

            return false;
        }

        if ($('#anamnesa').val() === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Anamnesa tidak boleh kosong!'
            });
            anamnesa.focus();

            return false;
        }

        if ($('#diagnosa').val() === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Diagnosa tidak boleh kosong!'
            });
            diagnosa.focus();

            return false;
        }

        $.ajax({
            url: '<?= site_url('Health/triage_proses') ?>',
            type: 'POST',
            data: form.serialize(),
            dataType: 'JSON',
            beforeSend: function() {
                $('#btnSimpan').html('<i class="fa fa-spinner fa-spin"></i>&nbsp;&nbsp;Proses');
                $('#btnSimpan').prop('disabled', true);
            },
            success: function(response) {
                if (response.status == 1) {
                    Swal.fire("Triage", "Berhasil diproses!", "success").then(() => {
                        getUrl('Health/triage');
                    });
                } else {
                    Swal.fire("Triage", "Gagal diproses!, silahkan dicoba lagi", "info");
                }
            },
            error: function(result) {
                $('#btnSimpan').html('Proses');
                $('#btnSimpan').attr('disabled', false);

                error_proccess();
            }
        });
    }
</script>