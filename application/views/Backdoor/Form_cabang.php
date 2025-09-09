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

<form method="post" id="form_cabang">
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
                                    <div class="col-md-12">
                                        <label for="id" class="control-label">ID <span class="text-danger">**</span></label>
                                        <input type="hidden" id="kode_cabang" name="kode_cabang" value="<?= (!empty($cabang) ? $cabang->kode_cabang : '') ?>">
                                        <input type="text" class="form-control" maxlength="3" id="inisial_cabang" name="inisial_cabang" placeholder="3 Huruf" value="<?= (!empty($cabang) ? $cabang->inisial_cabang : '') ?>" onkeyup="upperCase(this.value, 'inisial_cabang')">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="cabang">Nama <span class="text-danger">**</span></label>
                                        <input type="text" class="form-control" id="cabang" name="cabang" placeholder="Masukkan Nama" onkeyup="ubah_nama(this.value, 'cabang')" value="<?= (!empty($cabang) ? $cabang->cabang : '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="kontak" class="control-label">No. Hp <span class="text-danger">**</span></label>
                                        <input type="text" class="form-control" id="kontak" name="kontak" placeholder="Masukan No. Hp" value="<?= (!empty($cabang) ? $cabang->kontak : '') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="email">Email <span class="text-danger">**</span></label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan Email" onchange="cekEmail('email')" value="<?= (!empty($cabang) ? $cabang->email : '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="owner" class="control-label">Owner <span class="text-danger">**</span></label>
                                        <input type="text" class="form-control" id="owner" name="owner" placeholder="Masukan Owner" value="<?= (!empty($cabang) ? $cabang->owner : '') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="">Aktif <span class="text-danger">**</span></label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-3 mt-auto">
                                                        <label for="aktif_dari" class="control-label">Dari:</label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="date" name="aktif_dari" id="aktif_dari" class="form-control" value="<?= (!empty($cabang) ? $cabang->aktif_dari : date('Y-m-d')) ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-3 mt-auto">
                                                        <label for="aktif_sampai" class="control-label">Sampai:</label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="date" name="aktif_sampai" id="aktif_sampai" class="form-control" value="<?= (!empty($cabang) ? $cabang->aktif_sampai : date('Y-m-d')) ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="provinsi">Provinsi <span class="text-danger">**</span></label>
                                <select name="provinsi" id="provinsi" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tooltip on bottom" title="Provinsi" class="form-control select2_provinsi" data-placeholder="~ Pilih Provinsi" onchange="getKabupaten(this.value)">
                                    <?php
                                    if (!empty($cabang)) {
                                        $prov = $this->M_global->getData('m_provinsi', ['kode_provinsi' => $cabang->provinsi]);
                                        echo "<option value='" . $prov->kode_provinsi . "'>" . $prov->provinsi . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="kabupaten">Kabupaten <sup class="text-danger">**</sup></label>
                                <select name="kabupaten" id="kabupaten" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tooltip on bottom" title="Kabupaten" class="form-control select2_kabupaten" data-placeholder="~ Pilih Kabupaten" onchange="getKecamatan(this.value)">
                                    <?php
                                    if (!empty($cabang)) {
                                        $prov = $this->M_global->getData('kabupaten', ['kode_kabupaten' => $cabang->kabupaten]);
                                        echo "<option value='" . $prov->kode_kabupaten . "'>" . $prov->kabupaten . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="kecamatan">Kecamatan <sup class="text-danger">**</sup></label>
                                <select name="kecamatan" id="kecamatan" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tooltip on bottom" title="Kecamatan" class="form-control select2_kecamatan" data-placeholder="~ Pilih Kecamatan">
                                    <?php
                                    if (!empty($cabang)) {
                                        $prov = $this->M_global->getData('kecamatan', ['kode_kecamatan' => $cabang->kecamatan]);
                                        echo "<option value='" . $prov->kode_kecamatan . "'>" . $prov->kecamatan . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="desa">Desa <sup class="text-danger">**</sup></label>
                                <input type="text" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tooltip on bottom" title="Desa" class="form-control" placeholder="Desa" id="desa" name="desa" value="<?= ((!empty($cabang)) ? $cabang->desa : '') ?>" onkeyup="ubah_nama(this.value, 'desa')">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="kode_pos">Kode POS <sup class="text-danger">**</sup></label>
                                <input type="number" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tooltip on bottom" title="Kode Pos" class="form-control" placeholder="Kode Pos" id="kode_pos" name="kode_pos" value="<?= ((!empty($cabang)) ? $cabang->kode_pos : '') ?>" onkeyup="cekLength(this.value, 'kode_pos')">
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="rt">RT <sup class="text-danger">**</sup></label>
                                        <input type="text" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tooltip on bottom" title="RT" class="form-control" placeholder="RT" id="rt" name="rt" value="<?= ((!empty($cabang)) ? $cabang->rt : '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="rw">RW <sup class="text-danger">**</sup></label>
                                        <input type="number" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tooltip on bottom" title="RW" class="form-control" placeholder="RW" id="rw" name="rw" value="<?= ((!empty($cabang)) ? $cabang->rw : '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-danger" onclick="getUrl('Backdoor/for_cabang')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                            <button type="button" class="btn btn-success float-right ml-2" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                            <?php if (!empty($cabang)) : ?>
                                <button type="button" class="btn btn-info float-right" onclick="getUrl('Backdoor/form_cabang/0')" id="btnBaru"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
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
    var table;
    const form = $('#form_cabang');
    const btnSimpan = $('#btnSimpan');
    var kode_cabang = $('#kode_cabang');
    var inisial_cabang = $('#inisial_cabang');
    var cabang = $('#cabang');
    var kontak = $('#kontak');
    var email = $('#email');
    var owner = $('#owner');
    var provinsi = $('#provinsi');
    var kabupaten = $('#kabupaten');
    var kecamatan = $('#kecamatan');
    var desa = $('#desa');
    var kode_pos = $('#kode_pos');
    var rt = $('#rt');
    var rw = $('#rw');
    var aktif_dari = $('#aktif_dari');
    var aktif_sampai = $('#aktif_sampai');

    btnSimpan.attr('disabled', false);

    // fungsi get kabupaten berdasarkan kode provinsi
    function getKabupaten(kode_provinsi) {
        if (kode_provinsi == '' || kode_provinsi == null) { // jika kode provinsi kosong/ null
            // tampilkan notif
            Swal.fire("Provinsi", "Sudah dipilih?", "question");
            // set param jadi kosong
            var param = '';
        } else {
            // set param menjadi kode provinsi
            var param = kode_provinsi;
        }

        // jalankan select2 berdasarkan param
        initailizeSelect2_kabupaten(param);
    }

    // fungsi get kecamatan berdasarkan kode kabupaten
    function getKecamatan(kode_kabupaten) {
        if (kode_kabupaten == '' || kode_kabupaten == null) { // jika kode provinsi kosong/ null
            // tampilkan notif
            Swal.fire("Kabupaten", "Sudah dipilih?", "question");
            // set param jadi kosong
            var param = '';
        } else {
            // set param menjadi kode kabupaten
            var param = kode_kabupaten;
        }
        initailizeSelect2_kecamatan(param);
    }

    // fungsi simpan
    function save() {
        btnSimpan.attr('disabled', true);

        if (inisial_cabang.val() == '' || inisial_cabang.val() == null) { // jika inisial_cabang null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("ID", "Form sudah diisi?", "question");
        }

        if (cabang.val() == '' || cabang.val() == null) { // jika cabang null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Nama", "Form sudah diisi?", "question");
        }

        if (kontak.val() == '' || kontak.val() == null) { // jika kontak null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("No. Hp", "Form sudah diisi?", "question");
        }

        if (email.val() == '' || email.val() == null) { // jika email null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Email", "Form sudah diisi?", "question");
        }

        if (owner.val() == '' || owner.val() == null) { // jika owner null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Owner", "Form sudah diisi?", "question");
        }

        if (provinsi.val() == '' || provinsi.val() == null) { // jika provinsi null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Provinsi", "Form sudah diisi?", "question");
        }

        if (kabupaten.val() == '' || kabupaten.val() == null) { // jika kabupaten null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Kabupaten", "Form sudah diisi?", "question");
        }

        if (kecamatan.val() == '' || kecamatan.val() == null) { // jika kecamatan null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Kecamatan", "Form sudah diisi?", "question");
        }

        if (desa.val() == '' || desa.val() == null) { // jika desa null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Desa", "Form sudah diisi?", "question");
        }

        if (kode_pos.val() == '' || kode_pos.val() == null) { // jika kode_pos null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Kode Pos", "Form sudah diisi?", "question");
        }

        if (rt.val() == '' || rt.val() == null) { // jika rt null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("RT", "Form sudah diisi?", "question");
        }

        if (rw.val() == '' || rw.val() == null) { // jika rw null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("RW", "Form sudah diisi?", "question");
        }

        if (kode_cabang.val() == '' || kode_cabang.val() == null) { // jika kode_cabang null/ kosong
            // isi param = 1
            var param = 1;
        } else { // selain itu
            // isi param = 2
            var param = 2;
        }

        // jalankan proses cek cabang
        if (param == 1) {
            $.ajax({
                url: siteUrl + 'Backdoor/cekCabang',
                type: 'POST',
                dataType: 'JSON',
                data: form.serialize(),
                success: function(result) { // jika fungsi berjalan dengan baik
                    if (result.status == 1) { // jika mendapatkan respon 1
                        // jalankan fungsi proses berdasarkan param
                        proses(param);
                    } else { // selain itu

                        Swal.fire("Nama", "Sudah ada!, silahkan isi nama lain ", "info");
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

    // fungsi proses dengan param
    function proses(param) {

        if (param == 1) { // jika param 1 berarti insert/tambah
            var message = 'dibuat!';
        } else { // selain itu berarti update/ubah
            var message = 'diperbarui!';
        }

        // jalankan proses dengan param insert/update
        $.ajax({
            url: siteUrl + 'Backdoor/cabang_proses/' + param,
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) { // jika fungsi berjalan dengan baik
                btnSimpan.attr('disabled', false);

                if (result.status == 1) { // jika mendapatkan respon 1

                    Swal.fire("Cabang", "Berhasil " + message, "success").then(() => {
                        getUrl('Backdoor/for_cabang');
                    });
                } else { // selain itu

                    Swal.fire("Cabang", "Gagal " + message + ", silahkan dicoba kembali", "info");
                }
            },
            error: function(result) { // jika fungsi error
                btnSimpan.attr('disabled', false);

                error_proccess();
            }
        });
    }

    // fungsi reset form
    function reset() {
        if (inisial_cabang.val() == '' || inisial_cabang.val() == null) { // jika kode_cabangnya tidak ada isi/ null
            // kosongkan
            inisial_cabang.val('');
        }

        cabang.val('');
        kontak.val('');
        email.val('');
        owner.val('');
        provinsi.val('');
        kabupaten.val('');
        kecamatan.val('');
        desa.val('');
        kode_pos.val('');
        rt.val('');
        rw.val('');
        aktif_dari.val('<?= date('Y-m-d') ?>');
        aktif_sampai.val('<?= date('Y-m-d') ?>');
    }

    function showGuide() {
        // clean text
        $('#modal_mgLabel').text(``);
        $('#modal-isi').text(``);

        $('#modal_mg').modal('show'); // show modal

        // isi text
        $('#modal_mgLabel').append(`Manual Guide Backdoor Cabang`);
        $('#modal-isi').append(`
            <ol>
                <li style="font-weight: bold;">Tambah Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Tambah</li>
                        <li>Selanjutnya isikan Form yang tersedia<br>Tanda (<span style="color: red;">**</span>) mengartikan wajib terisi</li>
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