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

<form method="post" id="form_akun">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <button type="button" class="btn" id="btnProfile" onclick="seltab(1)">Profile</button>
                    <button type="button" class="btn" id="btnRiwayat" onclick="seltab(2)">Riwayat</button>
                </div>
                <div class="card-body">
                    <div id="profile">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4 col-4">
                                        <div class="card shadow">
                                            <div class="card-body p-1">
                                                <img id="preview_img" class="rounded mx-auto d-block" style="border: 2px solid grey; width: 100%;" src="<?= base_url('assets/user/') . $data_user->foto; ?>" alt="User profile picture">
                                            </div>
                                            <div class="card-footer p-0">
                                                <button type="button" class="btn btn-primary" disabled style="width: 100%; border-radius: 0px;">Foto Profil</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-8">
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="filefoto" aria-describedby="inputGroupFileAddon01" name="filefoto" onchange="readURL(this)">
                                                <label class="custom-file-label" id="label-gambar" for="inputGroupFile01">Cari Gambar</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Nama Lengkap" id="nama" name="nama" value="<?= $data_user->nama ?>">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <ion-icon name="person-outline"></ion-icon>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group mb-3">
                                    <input type="email" class="form-control" placeholder="Email" id="email" name="email" onchange="cekEmail(this.value)" value="<?= $data_user->email ?>">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <ion-icon name="mail-outline"></ion-icon>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group mb-3">
                                    <input type="password" class="form-control" placeholder="Sandi" id="secondpass" name="secondpass" value="<?= $data_user->secondpass ?>">
                                    <div class="input-group-append" onclick="secondpassshow()">
                                        <div class="input-group-text">
                                            <i class="fa-solid fa-fw fa-lock text-success" id="lock_pass"></i>
                                            <i class="fa-solid fa-lock-open text-danger" id="open_pass"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group mb-3">
                                    <select name="jkel" id="jkel" class="form-control select2_global" data-placeholder="~ Pilih Gender">
                                        <option value="">~ Pilih Gender</option>
                                        <option value="P" <?= ($data_user->jkel == 'P') ? 'selected' : '' ?>>Laki-laki</option>
                                        <option value="W" <?= ($data_user->jkel == 'W') ? 'selected' : '' ?>>Perempuan</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" id="btnSimpan" class="btn btn-success btn-sm float-right" onclick="simpan('<?= $data_user->kode_member ?>')"><ion-icon name="reload-outline"></ion-icon> Perbarui</button>
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
                                                foreach ($data_pendaftaran as $r) : ?>
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
</form>

<script>
    var nama = $("#nama");
    var email = $("#email");
    var secondpass = $("#secondpass");
    var jkel = $("#jkel");
    var btnSimpan = $('#btnSimpan');
    var profile = $('#profile');
    var riwayat = $('#riwayat');
    var btnProfile = $('#btnProfile');
    var btnRiwayat = $('#btnRiwayat');

    seltab(1)

    function seltab(param) {
        if (param == 1) {
            profile.show();
            riwayat.hide();

            btnProfile.addClass('btn-primary');
            btnRiwayat.removeClass('btn-primary');
        } else {
            profile.hide();
            riwayat.show();

            btnProfile.removeClass('btn-primary');
            btnRiwayat.addClass('btn-primary');
        }
    }

    // preview image
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#div_preview_foto').css("display", "block");
                $('#preview_img').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            $('#div_preview_foto').css("display", "none");
            $('#preview_img').attr('src', '');
        }
    }

    // fungsi keluar sistem
    function exit() {
        Swal.fire({
            title: "Kamu yakin?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, keluar!"
        }).then((result) => {
            if (result.isConfirmed) { // jika di konfirmasi "Ya"
                // arahkan ke fungsi logout di controller Auth
                getUrl('Auth/logout')
            }
        });
    }

    // fungsi tampil/sembunyi secondpass
    function secondpassshow() {
        if (document.getElementById("secondpass").type == "password") { // jika icon secondpass gembok di klik
            // ubah tipe secondpass menjadi text
            document.getElementById("secondpass").type = "text";

            // tampilkan icon buka
            $("#open_pass").show();

            // sembunyikan icon gembok
            $("#lock_pass").hide();
        } else { // selain itu
            // ubah tipe secondpass menjadi passwword
            document.getElementById("secondpass").type = "password";
            // sembunyikan icon buka
            $("#open_pass").hide();

            // tampilkan icon gembok
            $("#lock_pass").show();
        }
    }

    // fungsi simpan
    function simpan(kode_member) {
        btnSimpan.attr('disabled', true);

        if (nama.val() == '' || nama.val() == null) {
            btnSimpan.attr('disabled', false);

            Swal.fire("Nama", "Form sudah diisi?", "question");
            return;
        }

        if (email.val() == '' || email.val() == null) {
            btnSimpan.attr('disabled', false);

            Swal.fire("Email Website", "Form sudah diisi?", "question");
            return;
        }

        if (secondpass.val() == '' || secondpass.val() == null) {
            btnSimpan.attr('disabled', false);

            Swal.fire("Sandi", "Form sudah diisi?", "question");
            return;
        }

        if (jkel.val() == '' || jkel.val() == null) {
            btnSimpan.attr('disabled', false);

            Swal.fire("Gender", "Form sudah diisi?", "question");
            return;
        }

        proses(kode_member);
    }

    function proses(kode_member) {
        // jalankan proses
        var form = $('#form_akun')[0];
        var data = new FormData(form);

        $.ajax({
            url: siteUrl + 'Profile/updateAkunMember/' + kode_member,
            type: "POST",
            enctype: 'multipart/form-data',
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function(result) {
                btnSimpan.attr('disabled', false);

                if (result.status == 1) {

                    Swal.fire("Profile Akun", "Berhasil di perbarui!", "success").then(() => {
                        getUrl('Profile/profile_member');
                    });
                } else {

                    Swal.fire("Profile Akun", "Gagal di perbarui!, silahkan dicoba kembali", "info");
                }
            },
            error: function(result) {
                btnSimpan.attr('disabled', false);

                error_proccess();
            }
        });
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
</script>