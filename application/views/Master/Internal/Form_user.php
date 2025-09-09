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

<form method="post" id="form_user">
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
                                <label for="nama" class="control-label text-danger">Nama Lengkap</label>
                                <input type="hidden" class="form-control" placeholder="Nama Lengkap" id="kodeUser" name="kodeUser" value="<?= ((!empty($data_user)) ? $data_user->kode_user : '') ?>">
                                <input type="text" class="form-control" placeholder="Nama Lengkap" id="nama" name="nama" value="<?= ((!empty($data_user)) ? $data_user->nama : '') ?>" onkeyup="ubah_nama(this.value, 'nama')">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="control-label text-danger">Email</label>
                                <input type="email" class="form-control" placeholder="Email" id="email" name="email" onchange="cekEmail('email')" value="<?= ((!empty($data_user)) ? $data_user->email : '') ?>">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="control-label text-danger">Sandi</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" placeholder="Sandi" id="password" name="password" value="<?= ((!empty($data_user)) ? $data_user->secondpass : '') ?>">
                                    <div class="input-group-append" onclick="pass()">
                                        <div class="input-group-text">
                                            <i class="fa-solid fa-lock text-success" id="lock_pass"></i>
                                            <i class="fa-solid fa-lock-open text-danger" id="open_pass"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="jkel" class="control-label text-danger">Gender</label>
                                <select name="jkel" id="jkel" class="form-control select2_global" data-placeholder="~ Pilih Gender">
                                    <option value="">~ Pilih Gender</option>
                                    <option value="P" <?= (!empty($data_user) ? (($data_user->jkel == 'P') ? 'selected' : '') : '') ?>>Laki-laki</option>
                                    <option value="W" <?= (!empty($data_user) ? (($data_user->jkel == 'W') ? 'selected' : '') : '') ?>>Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="kode_role" class="control-label text-danger">Role</label>
                                <select name="kode_role" id="kode_role" class="form-control select2_global" data-placeholder="~ Pilih Role">
                                    <option value="">~ Pilih Role</option>
                                    <?php foreach ($role as $r) : ?>
                                        <option value="<?= $r->kode_role ?>" <?= (!empty($data_user) ? (($data_user->kode_role == $r->kode_role) ? 'selected' : '') : '') ?>><?= $r->keterangan ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="nohp" class="control-label text-danger">No. HP</label>
                                <input type="text" name="nohp" id="nohp" class="form-control text-right" value="<?= ((!empty($data_user)) ? $data_user->nohp : '') ?>" placeholder="08xxx">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-danger" onclick="getUrl('Master/user')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                            <button type="button" class="btn btn-success float-right ml-2" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                            <?php if (!empty($data_user)) : ?>
                                <button type="button" class="btn btn-info float-right" onclick="getUrl('Master/form_user/0')" id="btnBaru"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
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
    const form = $('#form_user');
    const btnSimpan = $('#btnSimpan');
    var kodeUser = $('#kodeUser');
    var nama = $('#nama');
    var email = $('#email');
    var password = $('#password');
    var jkel = $('#jkel');
    var kode_role = $('#kode_role');
    var nohp = $('#nohp');

    btnSimpan.attr('disabled', false);

    // fungsi daftarkan akun
    function save() {

        if (nama.val() == "" || nama.val() == null) { // jika nama null/ kosong
            Swal.fire("Nama Lengkap", "Form sudah diisi?", "question");
            return;
        }

        if (email.val() == "" || email.val() == null) { // jika email null/ kosong
            Swal.fire("Email", "Form sudah diisi?", "question");
            return;
        }

        if (password.val() == "" || password.val() == null) { // jika password null/ kosong
            Swal.fire("Sandi", "Form sudah diisi?", "question");
            return;
        }

        if (jkel.val() == "" || jkel.val() == null) { // jika jkel null/ kosong
            Swal.fire("Gender", "Form sudah diisi?", "question");
            return;
        }

        if (kode_role.val() == "" || kode_role.val() == null) { // jika kode_role null/ kosong
            Swal.fire("Role", "Form sudah diisi?", "question");
            return;
        }

        if (nohp.val() == "" || nohp.val() == null) { // jika nohp null/ kosong
            Swal.fire("No. HP", "Form sudah diisi?", "question");
            return;
        }

        if (kodeUser.val() == "" || kodeUser.val() == null) {
            var param = 1;
        } else {
            var param = 2;
        }

        // jalankan proses cek logistik
        if (param == 1) {
            $.ajax({
                url: siteUrl + 'Master/cekUser',
                type: 'POST',
                dataType: 'JSON',
                data: form.serialize(),
                success: function(result) { // jika fungsi berjalan dengan baik
                    if (result.status == 1) { // jika mendapatkan respon 1
                        // jalankan fungsi proses berdasarkan param
                        proses(param);
                    } else { // selain itu
                        btnSimpan.attr('disabled', false);

                        Swal.fire("Email", "Sudah digunakan!, silahkan gunakan email lain ", "info");
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
            url: siteUrl + 'Master/user_proses/' + param,
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) { // jika fungsi berjalan dengan baik
                btnSimpan.attr('disabled', false);

                if (result.status == 1) { // jika mendapatkan respon 1

                    Swal.fire("Pengguna", "Berhasil " + message, "success").then(() => {
                        getUrl('Master/user');
                    });
                } else { // selain itu

                    Swal.fire("Pengguna", "Gagal " + message + ", silahkan dicoba kembali", "info");
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
        if (kodeUser.val() == '' || kodeUser.val() == null) { // jika kode_usernya tidak ada isi/ null
            // kosongkan
            kodeUser.val('');
        }

        nama.val('');
        email.val('');
        password.val('');
        jkel.val('').change();
        kode_role.val('').change();
    }

    function showGuide() {
        // clean text
        $('#modal_mgLabel').text(``);
        $('#modal-isi').text(``);

        $('#modal_mg').modal('show'); // show modal

        // isi text
        $('#modal_mgLabel').append(`Manual Guide Master Pengguna`);
        $('#modal-isi').append(`
            <ol>
                <li style="font-weight: bold;">Tambah Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Tambah</li>
                        <li>Selanjutnya isikan Form yang tersedia<br><span style='color: red;'>Teks berawarna merah</span> mengartikan wajib terisi mengartikan wajib terisi</li>
                        <li>Klik tombol Proses</li>
                    </ul>
                </p>
                <li style="font-weight: bold;">Ubah Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Ubah pada list data yang ingin di ubah</li>
                        <li>Ubah isi Form yang akan di ubah<br><span style='color: red;'>Teks berawarna merah</span> mengartikan wajib terisi mengartikan wajib terisi</li>
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