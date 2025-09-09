<!-- view registrasi akun baru -->

<div class="register-box" data-aos="fade-down">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <a type="button" class="h1"><b><?= $nama_apps ?></b> <?= $web_version_all->nama ?></a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Form Daftar</p>

            <form id="form_regist" method="post">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Nama Lengkap" id="nama" name="nama">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <ion-icon name="person-outline"></ion-icon>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="email" class="form-control" placeholder="Email" id="email" name="email" onchange="cekEmail('email')">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <ion-icon name="mail-outline"></ion-icon>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="number" class="form-control" placeholder="No. Hp" id="nohp" name="nohp">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <ion-icon name="call-outline"></ion-icon>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Sandi" id="password" name="password">
                    <div class="input-group-append" onclick="pass()">
                        <div class="input-group-text">
                            <ion-icon name="lock-closed-outline" id="lock_pass"></ion-icon>
                            <ion-icon name="lock-open-outline" id="open_pass"></ion-icon>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <select name="jkel" id="jkel" class="form-control select2_global" data-placeholder="~ Pilih Gender">
                        <option value="">~ Pilih Gender</option>
                        <option value="P">Laki-laki</option>
                        <option value="W">Perempuan</option>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <div class="row">
                        <div class="col-md-6 col-6">
                            <input type="text" class="form-control" placeholder="Kode Verifikasi" id="kode" name="kode">
                        </div>
                        <div class="col-md-6 col-6">
                            <button type="button" class="btn btn-danger btn-block" onclick="cekCode(1)">Dapatkan Kode</button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary btn-block" onclick="regist()">Daftarkan</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-footer">
            <a type="button" class="text-center" onclick="getUrl('Auth')">Sudah Punya Akun!</a>
        </div>
    </div>
</div>

<script>
    // variable
    const form = $("#form_regist");
    var nama = $("#nama");
    var email = $("#email");
    var nohp = $("#nohp");
    var password = $("#password");
    var jkel = $("#jkel");
    var kode = $("#kode");

    // cek email berdsasarkan email
    function cekEmail(forid) {
        if (validateEmail($('#' + forid).val()) == false) {
            Swal.fire("Email", "Format sudah valid?", "question");
            return;
        }
    }

    // fungsi daftarkan akun
    function regist() {
        // tampilkan loading

        if (nama.val() == "" || nama.val() == null) { // jika nama null/ kosong
            // sembunyikan loading

            return Swal.fire("Nama Lengkap", "Form sudah diisi?", "question");
        }

        if (email.val() == "" || email.val() == null) { // jika email null/ kosong
            // sembunyikan loading

            return Swal.fire("Email", "Form sudah diisi?", "question");
        }

        if (nohp.val() == "" || nohp.val() == null) { // jika nohp null/ kosong
            // sembunyikan loading

            return Swal.fire("No. Hp", "Form sudah diisi?", "question");
        }

        if (password.val() == "" || password.val() == null) { // jika password null/ kosong
            // sembunyikan loading

            return Swal.fire("Sandi", "Form sudah diisi?", "question");
        }

        if (jkel.val() == "" || jkel.val() == null) { // jika jkel null/ kosong
            // sembunyikan loading

            return Swal.fire("Gender", "Form sudah diisi?", "question");
        }

        if (kode.val() == "" || kode.val() == null) { // jika kode null/ kosong
            // sembunyikan loading

            return Swal.fire("Kode Verifikasi", "Form sudah diisi?", "question");
        }

        // jalankan fungsi
        $.ajax({
            url: siteUrl + 'Auth/regist_proses',
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) { // jika fungsi berjalan
                // sembunyikan loading


                if (result.status == 1) { // jika mendapatkan hasil status 1
                    Swal.fire({
                        title: "Akun",
                        text: "Berhasil didaftarkan!, silahkan masuk",
                        icon: "success"
                    }).then((value) => {
                        // ketika di klik ok, arahkan ke Auth
                        getUrl('Auth');
                    });
                } else if (result.status == 2) { // jika mendaparkan hasil status 2
                    Swal.fire({
                        title: "Kode Verifikasi",
                        text: "Tidak sesuai!, silahkan masukan ulang kode",
                        icon: "info"
                    })
                } else if (result.status == 3) { // jika mendapatkan hasil status 3
                    Swal.fire({
                        title: "Email",
                        text: "Sudah digunakan!, silahkan masukan ulang email",
                        icon: "info"
                    })
                } else { // selain itu
                    Swal.fire({
                        title: "Akun",
                        text: "Gagal didaftarkan!, silahkan coba lagi",
                        icon: "info"
                    })
                }
            },
            error: function(result) { // jika fungsi gagal berjalan
                // sembunyikan loading

                // tampilkan notifikasi error
                error_proccess()
            }
        });

    }

    // fungsi cekCode
    function cekCode(param) {
        // jalankan fungsi getCode
        getCode(param, email.val());
    }
</script>