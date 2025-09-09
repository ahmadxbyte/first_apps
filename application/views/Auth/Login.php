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

<div class="login-box" data-aos="fade-up">
    <div class="card card-outline card-primary" <?= $style; ?>>
        <div class="card-header text-center">
            <a type="button" class="h1"><b><img src="<?= base_url('assets/img/web/') . $web->loading ?>" style="width: 80px;"> <?= $nama_apps ?></b></a>
            <!-- <br>
            <div class="h5"><?= $web_version_all->nama ?></div> -->
        </div>
        <div class="card-body">
            <p class="login-box-msg">Selamat Datang</p>
            <form id="form_login" method="post">
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Email/RM" id="email" name="email" onchange="cekEmailLog(this.value); cekUserRole(this.value); cekUserCabang(this.value)">
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Sandi" id="password" name="password">
                    <div class="input-group-append" onclick="pass()">
                        <div class="input-group-text">
                            <i class="fa-solid fa-fw fa-lock text-success" id="lock_pass"></i>
                            <i class="fa-solid fa-lock-open text-danger" id="open_pass"></i>
                        </div>
                    </div>
                </div>
                <div>
                    <input type="hidden" name="kode_role" id="kode_role" value="">
                    <div class="row" id="forshift">
                        <div class="col-md-12 mb-3">
                            <select name="shift" id="shift" class="form-control select2_global" data-placeholder="~ Pilih Shift" style="width: 100%;">
                                <option value="">~ Pilih Shift</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3" id="forcabang">
                            <select name="cabang" id="cabang" class="form-control select2_cabang" data-placeholder="~ Pilih Cabang">
                                <option value="">~ Pilih Cabang</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary btn-block" onclick="login()">Masuk</button>
                    </div>
                    <!-- <div class="col-6">
                        <button type="button" class="btn btn-danger btn-block" onclick="getUrl('Auth/regist')">Daftar</button>
                    </div> -->
                </div>
            </form>
        </div>
        <div class="card-footer">
            <p class="mb-1">
                <a type="button" onclick="getUrl('Auth/repass')" class="text-danger font-weight-bold">LUPA SANDI?</a>
            </p>
        </div>
        <div class="card-footer card-outline card-danger text-center">
            <a href="<?= $web->ig ?>" target="_blank" type="button" style="margin: 2px;" class="btn btn-danger"><i class="fa-brands fa-instagram"></i></a>
            <a href="<?= $web->git ?>" target="_blank" type="button" style="margin: 2px;" class="btn btn-dark"><i class="fa-brands fa-github"></i></a>
            <a href="https://wa.me/<?= $web->nohp ?>" target="_blank" type="button" style="margin: 2px;" class="btn btn-success"><i class="fa-brands fa-whatsapp"></i></a>
            <a href="mailto:<?= $web->email ?>" target="_blank" type="button" style="margin: 2px;" class="btn btn-info"><i class="fa-solid fa-envelope-open-text"></i></a>
        </div>
    </div>
</div>

<!-- <a class="floating text-decoration-none" href="https://wa.me/<?= $web->nohp ?>" title="Whatsapp" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Whatsapp" style="text-decoration: none;"><i class="fa-brands fa-2x fa-whatsapp" style="margin-top: 10px;"></i></a> -->

<script>
    const form = $("#form_login");
    var email = $("#email");
    var password = $("#password");
    var shift = $("#shift");
    var forshift = $("#forshift");
    var kode_role = $("#kode_role");
    var cabang = $("#cabang");

    forshift.hide();

    // fungsi cek role
    function cekUserRole(x) {
        if (x == '' || x == null) {
            Swal.fire("Email", "Form sudah diisi?", "question");
            return;
        }

        $.ajax({
            url: siteUrl + 'Auth/cekRole?email=' + x,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                document.getElementById("shift").style.width = "100%";

                if (result.status == 1) {
                    forshift.hide();
                    kode_role.val(result.kode_role);
                } else {
                    forshift.show();
                    kode_role.val(result.kode_role);
                }
            },
            error: function(result) {


                error_proccess()
            }
        });
    }

    // fungsi cek cabang
    function cekUserCabang(x) {
        if (x == '' || x == null) {
            Swal.fire("Email", "Form sudah diisi?", "question");
            return;
        }

        if (validateEmail(x)) {
            // jalankan select2 berdasarkan x
            initailizeSelect2_cabang(x);
        } else {
            initailizeSelect2_cabang_member(x);
        }
    }

    function login() {
        if (email.val() == "" || email.val() == null) {
            Swal.fire("Email", "Form sudah diisi?", "question");
            return;
        }

        // if (validateEmail(email.val()) == false) {
        //     Swal.fire("Email", "Format sudah valid?", "question");
        //     return;
        // }

        if (password.val() == "" || password.val() == null) {
            Swal.fire("Sandi", "Form sudah diisi?", "question");
            return;
        }

        if (kode_role.val() != 'R0005') {
            if (shift.val() == "" || shift.val() == null) {
                Swal.fire("Shift", "Sudah dipilih?", "question");
                return;
            }
        }

        if (cabang.val() == "" || cabang.val() == null) {
            Swal.fire("Cabang", "Form sudah diisi?", "question");
            return;
        }

        $.ajax({
            url: siteUrl + 'Auth/login_proses',
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) {
                if (result.status == 1) {
                    if (result.kode_role == 'R0005') {
                        getUrl('App');
                    } else {
                        getUrl('Home');
                    }
                } else if (result.status == 2) {
                    Swal.fire("Email", "Tidak ditemukan!, silahkan daftar terlebih dahulu", "info");
                } else if (result.status == 3) {
                    Swal.fire("Akun", "Password yang dimasukan salah!, silahkan coba lagi", "info");
                } else {
                    Swal.fire("Akun", "Dinonaktifkan!, silahkan hubungi admin untuk diaktifkan", "info");
                }
            },
            error: function(result) {
                error_proccess()
            }
        });
    }
</script>