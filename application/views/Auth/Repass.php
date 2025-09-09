<!-- view atur ulang -->
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

<div class="register-box" data-aos="fade-down">
    <div class="card card-outline card-primary" <?= $style; ?>>
        <div class="card-header text-center">
            <a type="button" class="h1"><b><img src="<?= base_url('assets/img/web/') . $web->loading ?>" style="width: 80px;"> <?= $nama_apps ?></b></a>
            <!-- <br>
            <div class="h5"><?= $web_version_all->nama ?></div> -->
        </div>
        <div class="card-body">
            <p class="login-box-msg">Atur Ulang Sandi</p>
            <form id="form_repass" method="post">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <input type="email" class="form-control" placeholder="Email" id="email" name="email" onchange="cekEmail(this.value)">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="input-group">
                            <input type="password" class="form-control" placeholder="Sandi Baru" id="password" name="password">
                            <div class="input-group-append" onclick="pass()">
                                <div class="input-group-text">
                                    <i class="fa-solid fa-fw fa-lock text-success" id="lock_pass"></i>
                                    <i class="fa-solid fa-lock-open text-danger" id="open_pass"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" placeholder="Kode Verifikasi" id="kode" name="kode">
                    </div>
                    <div class="col-md-6">
                        <button type="button" style="min-width: 100%;" class="btn btn-danger w-100" onclick="cekCode(2)">Dapatkan Kode</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary w-100" onclick="aturSandi()">Atur Ulang Sandi</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer">
            <a type="button" class="text-center font-weight-bold" onclick="getUrl('Auth')">Form Masuk</a>
        </div>
        <div class="card-footer card-outline card-danger text-center">
            <a href="<?= $web->ig ?>" target="_blank" type="button" style="margin: 2px;" class="btn btn-danger"><i class="fa-brands fa-instagram"></i></a>
            <a href="<?= $web->git ?>" target="_blank" type="button" style="margin: 2px;" class="btn btn-dark"><i class="fa-brands fa-github"></i></a>
            <a href="https://wa.me/<?= $web->nohp ?>" target="_blank" type="button" style="margin: 2px;" class="btn btn-success"><i class="fa-brands fa-whatsapp"></i></a>
            <a href="mailto:<?= $web->email ?>" target="_blank" type="button" style="margin: 2px;" class="btn btn-info"><i class="fa-solid fa-envelope-open-text"></i></a>
        </div>
    </div>
</div>

<script>
    // variable
    var email = $("#email");
    const form = $('#form_repass');

    // fungsi cekCode
    function cekCode(param) {
        // jalankan fungsi getCode
        getCode(param, email.val());
    }

    // fungsi atur ulang sandi
    function aturSandi() {
        // tampilkan loading

        // jalankan fungsi
        $.ajax({
            url: siteUrl + 'Auth/atur_sandi',
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) { // jika fungsi berjalan
                if (result.status == 1) { // jika mendapatkan hasil status 1
                    // sembunyikan loading

                    Swal.fire({
                        title: "Sandi",
                        text: "Berhasil diatur ulang!, silahkan masuk",
                        icon: "success"
                    }).then((value) => {
                        // ketika notifikasi di klik ok, maka arahkan ke Auth
                        getUrl('Auth');
                    });
                } else {
                    // sembunyikan loading

                    Swal.fire({
                        title: "Sandi",
                        text: "Gagal diatur ulang!, silahkan coba lagi",
                        icon: "info"
                    })
                    return;
                }
            },
            error: function(result) { // jika fungsi gagal berjalan
                // sembunyikan loading

                // tampilkan notifikasi error
                error_proccess()
            }
        });
    }
</script>