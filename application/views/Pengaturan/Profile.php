<style>
    .dataTables_filter {
        display: none;
    }

    [data-toggle="tab"].active {
        background-color: #007bff !important;
        color: white !important;
    }

    .tab-pane {
        background-color: transparent !important;
        -webkit-backdrop-filter: blur(5px) !important;
        backdrop-filter: blur(5px) !important;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin: 10px 0;
    }

    /* Ensure backdrop-filter works across browsers */
    @supports (-webkit-backdrop-filter: none) or (backdrop-filter: none) {
        .tab-pane {
            -webkit-backdrop-filter: blur(5px);
            backdrop-filter: blur(5px);
        }
    }

    /* Fallback for browsers that don't support backdrop-filter */
    @supports not ((-webkit-backdrop-filter: none) or (backdrop-filter: none)) {
        .tab-pane {
            background-color: transparent !important;
        }
    }
</style>

<?php
if ($data_user->on_off == 1) {
    $status = 'Aktif';
} else {
    $status = '';
}
if ($data_user->on_off == 1) {
    $on_off = 'border-success';
} else {
    $on_off = '';
}

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

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <div class="card card-primary card-outline" <?= $style ?>>
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img id="preview_img" class="profile-user-img img-fluid img-circle <?= $on_off; ?>" src="<?= base_url('assets/user/') . $data_user->foto; ?>" alt="User profile picture">
                        </div>
                        <h3 class="profile-username text-center"><?= strtoupper($data_user->nama); ?></h3>
                        <p class="text-muted text-center"><?= $this->M_global->getData('m_role', ['kode_role' => $data_user->kode_role])->keterangan; ?></p>
                    </div>
                </div>

                <div class="card card-outline card-primary" <?= $style ?>>
                    <div class="card-header">
                        <h3 class="card-title">Informasi Pribadi</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <strong><i class="fa-regular fa-calendar-days mr-1"></i> Berjabung Sejak</strong>
                        <p class="text-muted"><?= date("d M Y", strtotime($data_user->joined)); ?></p>
                        <hr>
                        <strong><i class="fa-solid fa-at mr-1"></i> Email</strong>
                        <p class="text-muted"><?= '@' . str_replace('@gmail.com', '', $data_user->email); ?></p>
                        <hr>
                        <strong><i class="fa-solid fa-phone mr-1"></i> Nomor Hp/Telp</strong>
                        <p class="text-muted"><?= (!$data_user->nohp) ? '-' : str_replace('08', '+62-8', $data_user->nohp); ?></p>
                        <hr>
                        <strong><i class="fa-solid fa-power-off mr-1"></i> Status Akun</strong>
                        <p class="text-muted"><?= $status; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-10">
                <div class="card card-outline card-primary" <?= $style ?>>
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Aktifitas</a></li>
                            <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Pengaturan Akun</a></li>
                            <li class="nav-item"><a class="nav-link" href="#password" data-toggle="tab">Ubah Password</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="active tab-pane" id="activity">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive">
                                            <div class="h4 text-primary font-weight-bold">Masuk & Keluar Sistem</div>
                                            <table class="table shadow-sm table-bordered" width="100%" <?= ($web->ct_theme == 2) ? ' style="border-radius: 10px; color: white !important;"' : ' style="border-radius: 10px;"' ?>>
                                                <thead>
                                                    <tr class="text-center">
                                                        <th style="border-radius: 10px 0px 0px 0px;">Tanggal Masuk</th>
                                                        <th>Jam Masuk</th>
                                                        <th>Tanggal Keluar</th>
                                                        <th style="border-radius: 0px 10px 0px 0px;">Jam Keluar</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $hari = date("D", strtotime($in_out->tgl_masuk));
                                                    $hari2 = date("D", strtotime($in_out->tgl_keluar));

                                                    switch ($hari) {
                                                        case 'Sun':
                                                            $hari_masuk = "Minggu";
                                                            break;

                                                        case 'Mon':
                                                            $hari_masuk = "Senin";
                                                            break;

                                                        case 'Tue':
                                                            $hari_masuk = "Selasa";
                                                            break;

                                                        case 'Wed':
                                                            $hari_masuk = "Rabu";
                                                            break;

                                                        case 'Thu':
                                                            $hari_masuk = "Kamis";
                                                            break;

                                                        case 'Fri':
                                                            $hari_masuk = "Jumat";
                                                            break;

                                                        case 'Sat':
                                                            $hari_masuk = "Sabtu";
                                                            break;

                                                        default:
                                                            $hari_masuk = "Tidak di ketahui";
                                                            break;
                                                    }

                                                    switch ($hari2) {
                                                        case 'Sun':
                                                            $hari_keluar = "Minggu";
                                                            break;

                                                        case 'Mon':
                                                            $hari_keluar = "Senin";
                                                            break;

                                                        case 'Tue':
                                                            $hari_keluar = "Selasa";
                                                            break;

                                                        case 'Wed':
                                                            $hari_keluar = "Rabu";
                                                            break;

                                                        case 'Thu':
                                                            $hari_keluar = "Kamis";
                                                            break;

                                                        case 'Fri':
                                                            $hari_keluar = "Jumat";
                                                            break;

                                                        case 'Sat':
                                                            $hari_keluar = "Sabtu";
                                                            break;

                                                        default:
                                                            $hari_keluar = "Tidak di ketahui";
                                                            break;
                                                    }

                                                    $bulan = array(
                                                        '01' => 'Januari',
                                                        '02' => 'Februari',
                                                        '03' => 'Maret',
                                                        '04' => 'April',
                                                        '05' => 'Mei',
                                                        '06' => 'Juni',
                                                        '07' => 'Juli',
                                                        '08' => 'Agustus',
                                                        '09' => 'September',
                                                        '10' => 'Oktober',
                                                        '11' => 'November',
                                                        '12' => 'Desember',
                                                    );
                                                    ?>
                                                    <tr>
                                                        <td><?= $hari_masuk . ", " . date('d', strtotime($in_out->tgl_masuk)) . " " . $bulan[date('m', strtotime($in_out->tgl_masuk))] . " " . date('Y', strtotime($in_out->tgl_masuk)); ?></td>
                                                        <td><?= date("H:i:s", strtotime($in_out->jam_masuk)); ?></td>
                                                        <td><?= $hari_keluar . ", " . date('d', strtotime($in_out->tgl_keluar)) . " " . $bulan[date('m', strtotime($in_out->tgl_keluar))] . " " . date('Y', strtotime($in_out->tgl_keluar)); ?></td>
                                                        <td><?= date("H:i:s", strtotime($in_out->jam_keluar)); ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-sm-6" style="margin-bottom: 5px;">
                                                <div class="h4 text-primary font-weight-bold">Aktifitas Ketika di dalam Sistem</div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="float-right">
                                                    <div class="row">
                                                        <?php if ($this->session->userdata('kode_role') == 'R0001') : ?>
                                                            <div class="col-md-8">
                                                                <select name="kode_user" id="kode_user" class="form-control select2_user" data-placeholder="~ Pilih User" onchange="lihat_aktifitas()">
                                                                    <option value="<?= $this->session->userdata('kode_user') ?>">Status: <?= $this->M_global->getData('m_role', ['kode_role' => $this->session->userdata('kode_role')])->keterangan ?> ~ Nama: <?= $this->M_global->getData('user', ['kode_user' => $this->session->userdata('kode_user')])->nama ?></option>
                                                                </select>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="col-md-<?= (($this->session->userdata('kode_role') == 'R0001') ? '4' : '12') ?>">
                                                            <input type="date" class="form-control" name="tgl" id="tgl" value="<?= date('Y-m-d'); ?>" onchange="lihat_aktifitas()">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="cekaktif_user">
                                            <?php if ($aktifitas) : ?>
                                                <br>
                                                <span class="badge bg-info" type="button" onclick="lihat_aktifitas()"><i class="fa-solid fa-arrows-rotate"></i> Refresh</span>
                                                <span class="badge bg-warning" type="button" onclick="download_au($('#tgl').val())"><i class="fa-solid fa-arrows-rotate"></i> Cetak</span>
                                                <span class="badge bg-danger float-right">Banyaknya aktifitas : <?= $jum_aktif; ?></span>
                                                <br>
                                                <br>
                                                <div class="table-responsive">
                                                    <table class="table table-striped w-100" <?= ($web->ct_theme == 2) ? ' style="border-radius: 10px; color: white !important;"' : ' style="border-radius: 10px;"' ?>>
                                                        <?php foreach ($aktifitas as $au) { ?>
                                                            <tr>
                                                                <td style="width: 12%;" class="text-left align-middle"><span class="badge bg-success"><?= date("d m Y", strtotime($au->waktu)); ?></span></td>
                                                                <td style="width: 18%;" class="text-left align-middle"><?= $au->menu; ?></td>
                                                                <td style="width: 40%;" class="text-left align-middle">
                                                                    <?= $au->kegiatan . (($this->session->userdata('kode_role') == 'R0001') ? '<hr>Sesudah: <br><a href="#" onclick="copyActivity(' . "'" . $au->id_activity . "', '0'" . ')">' . $au->detail_kegiatan . '</a><br>Sebelumnya: <br><a href="#" onclick="copyActivity(' . "'" . $au->id_activity . "', '1'" . ')">' . $au->detail_sebelum . '</a>' : ''); ?>
                                                                </td>
                                                                <td style="width: 10%;" class="text-left align-middle"><?= $au->kode_cabang; ?></td>
                                                                <td style="width: 10%;" class="text-left align-middle">Shif: <?= $au->shift; ?></td>
                                                                <td style="width: 10%;" class="text-right align-middle">Jam : <?= date("H:i", strtotime($au->waktu)); ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    </table>
                                                    <style>
                                                        /* Remove horizontal scroll for table-responsive in this context */
                                                        #cekaktif_user .table-responsive {
                                                            overflow-x: unset !important;
                                                        }

                                                        /* Ensure table fills container */
                                                        #cekaktif_user table.table {
                                                            width: 100% !important;
                                                            min-width: 100% !important;
                                                            table-layout: fixed;
                                                        }

                                                        #cekaktif_user td {
                                                            white-space: normal !important;
                                                            word-break: break-word;
                                                        }
                                                    </style>
                                                </div>
                                            <?php else : ?>
                                                <br>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <span class="badge bg-info" type="button" onclick="lihat_aktifitas()"><i class="fa-solid fa-arrows-rotate"></i> Refresh</span>
                                                        <span class="badge bg-warning" type="button" onclick="download_au($('#tgl').val())"><i class="fa-solid fa-arrows-rotate"></i> Cetak</span>
                                                        <span class="badge bg-danger float-right">Banyaknya aktifitas : 0</span>
                                                        <br>
                                                        <br>
                                                        <div class="table-responsive">
                                                            <table width="100%" class="table shadow-sm table-striped" <?= ($web->ct_theme == 2) ? ' style="border-radius: 10px; color: white !important;"' : ' style="border-radius: 10px;"' ?>>
                                                                <tr>
                                                                    <td>
                                                                        <span class="text-center font-weight-bold">Tidak ada aktifitas</span>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="settings" <?= ($web->ct_theme == 2) ? ' style="border-radius: 10px; color: white !important;"' : ' style="border-radius: 10px;"' ?>>
                                <form class="form-horizontal" id="form-profile" method="POST">
                                    <div class="form-group row">
                                        <label for="inputName" class="col-sm-2 col-form-label">Profile</label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="filefoto" aria-describedby="inputGroupFileAddon01" name="filefoto">
                                                    <label class="custom-file-label" for="inputGroupFile01">Cari Gambar</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputName" class="col-sm-2 col-form-label">Email</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="email" name="email" placeholder="Email" value="<?= $data_user->email; ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputName" class="col-sm-2 col-form-label">Tingkatan</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="id_role" name="id_role" placeholder="Tingkatan" value="<?= $this->M_global->getData('m_role', ['kode_role' => $data_user->kode_role])->keterangan; ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputName2" class="col-sm-2 col-form-label">Nama</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama" value="<?= $data_user->nama; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputExperience" class="col-sm-2 col-form-label">Nomor Hp</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="nohp" name="nohp" placeholder="Nomor Hp" value="<?= $data_user->nohp; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputExperience" class="col-sm-2 col-form-label">Gender</label>
                                        <div class="col-sm-10">
                                            <select name="jkel" id="jkel" class="form-control select2_global" data-placeholder="~ Pilih Gender">
                                                <option value="~ Pilih Gender"></option>
                                                <option value="P" <?= ($data_user->jkel == 'P') ? 'selected' : '' ?>>Pria</option>
                                                <option value="W" <?= ($data_user->jkel == 'W') ? 'selected' : '' ?>>Wanita</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="offset-sm-2 col-sm-10">
                                            <button type="button" class="btn btn-danger float-right" onclick="simpan_profile('<?= $data_user->kode_user; ?>')"><i class="fa-solid fa-hard-drive mr-1"></i> Perbarui Data Diri</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane" id="password" <?= ($web->ct_theme == 2) ? ' style="border-radius: 10px; color: white !important;"' : ' style="border-radius: 10px;"' ?>>
                                <form method="post" id="form-password">
                                    <div class="form-group row">
                                        <label for="inputName" class="col-sm-2 col-form-label">Password Baru</label>
                                        <div class="input-group col-sm-10">
                                            <input type="password" class="form-control" placeholder="Password Baru" id="password1" name="password1">
                                            <div class="input-group-append" onclick="pass1()">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-fw fa-lock text-success" id="lock_pass1"></i>
                                                    <i class="fa-solid fa-lock-open text-danger" id="open_pass1"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputName" class="col-sm-2 col-form-label">Ulangi</label>
                                        <div class="input-group col-sm-10">
                                            <input type="password" class="form-control" placeholder="Ulangi Password" id="password2" name="password2">
                                            <div class="input-group-append" onclick="pass2()">
                                                <div class="input-group-text">
                                                    <i class="fa-solid fa-fw fa-lock text-success" id="lock_pass2"></i>
                                                    <i class="fa-solid fa-lock-open text-danger" id="open_pass2"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <button class="btn btn-success float-right" type="button" id="btnpassword" onclick="c_pas()"><i class="fa-solid fa-key mr-1"></i> Simpan Password Baru</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    let input = document.querySelector('#password1');
    let input2 = document.querySelector('#password2');

    function simpan_profile(id) {
        Swal.fire({
            title: "Kamu yakin?",
            text: "Data profile akan diupdate!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, update!",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) {
                var form = $('#form-profile')[0];
                var data = new FormData(form);
                $.ajax({
                    url: "<?= site_url('Profile/updateAkun/'); ?>" + id,
                    type: "POST",
                    enctype: 'multipart/form-data',
                    data: data,
                    dataType: "JSON",
                    processData: false,
                    contentType: false,
                    cache: false,
                    timeout: 600000,
                    success: function(result) {
                        if (result.status == 1) { // jika mendapatkan respon 1

                            Swal.fire("Profile", "Berhasil diupdate", "success").then(() => {
                                getUrl('Profile');
                            });
                        } else { // selain itu

                            Swal.fire("Profile", "Gagal diupdate" + ", silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error
                        error_proccess();
                    }
                });
            }
        })
    }

    function copyActivity(param, ket) {
        $.ajax({
            url: '<?= site_url('Profile/getDataActivity/') ?>' + param + '/' + ket,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if (result.status == 1) {
                    navigator.clipboard.writeText(result.hasil)
                        .then(() => {
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Teks JSON Sebelum Berhasil Disalin",
                                showConfirmButton: false,
                                timer: 500
                            });
                        })
                        .catch(err => {
                            Swal.fire({
                                position: "center",
                                icon: "warning",
                                title: "Teks JSON Sebelum Gagal Disalin",
                                showConfirmButton: false,
                                timer: 500
                            });
                        });
                } else {
                    Swal.fire({
                        position: "center",
                        icon: "warning",
                        title: "Teks JSON Sebelum Gagal Disalin",
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

    // when photo has been change
    $("#filefoto").change(function() {
        readURL(this);
    });

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

    // aktifitas
    function lihat_aktifitas() {
        var tgl = $('#tgl').val();
        var kode_user = $('#kode_user').val();
        var params = tgl + "/" + kode_user;

        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("cekaktif_user").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "<?= base_url(); ?>Profile/aktifitas_user/" + params, true);
        xhttp.send();
    }

    // update password
    function c_pas() {
        if (input.value != input2.value) {
            return Swal.fire("Password", "Tidak sama, coba lagi!", "info");
        }

        $.ajax({
            url: '<?= site_url('Profile/update_pass') ?>',
            type: 'POST',
            dataType: 'JSON',
            data: $("#form-password").serialize(),
            success: function(result) {
                if (result.status == 1) { // jika mendapatkan respon 1
                    input.value = '';
                    input2.value = '';

                    Swal.fire("Password", "Berhasil diupdate", "success").then(() => {
                        getUrl('Profile');
                    });
                } else { // selain itu

                    Swal.fire("Password", "Gagal diupdate" + ", silahkan dicoba kembali", "warning");
                }
            },
            error: function(result) {
                error_proccess();
            }
        })
    }

    // print
    function download_au(param) {
        var param = `?tgl=${param}`
        window.open(`${siteUrl}Report/activity_user/1${param}`, '_blank');
    }

    $("#open_pass1").hide();
    $("#open_pass2").hide();

    // fungsi tampil/sembunyi password
    function pass1() {
        if (document.getElementById("password1").type == "password") { // jika icon password gembok di klik
            // ubah tipe password menjadi text
            document.getElementById("password1").type = "text";

            // tampilkan icon buka
            $("#open_pass1").show();

            // sembunyikan icon gembok
            $("#lock_pass1").hide();
        } else { // selain itu
            // ubah tipe password menjadi passwword
            document.getElementById("password1").type = "password";
            // sembunyikan icon buka
            $("#open_pass1").hide();

            // tampilkan icon gembok
            $("#lock_pass1").show();
        }
    }

    // fungsi tampil/sembunyi password
    function pass2() {
        if (document.getElementById("password2").type == "password") { // jika icon password gembok di klik
            // ubah tipe password menjadi text
            document.getElementById("password2").type = "text";

            // tampilkan icon buka
            $("#open_pass2").show();

            // sembunyikan icon gembok
            $("#lock_pass2").hide();
        } else { // selain itu
            // ubah tipe password menjadi passwword
            document.getElementById("password2").type = "password";
            // sembunyikan icon buka
            $("#open_pass2").hide();

            // tampilkan icon gembok
            $("#lock_pass2").show();
        }
    }
</script>