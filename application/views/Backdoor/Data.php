<?php
// Pastikan session sudah dimulai sebelum akses $_SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Tambahan: Deteksi dark mode dari browser/user
$is_darkmode = (
    (isset($_COOKIE['darkmode']) && $_COOKIE['darkmode'] == '1') ||
    (isset($_SESSION['darkmode']) && $_SESSION['darkmode'] == '1') ||
    (isset($_GET['darkmode']) && $_GET['darkmode'] == '1')
);

if ($is_darkmode) {
    $style = 'style="height: 20vh; background-color: #222; color: #fff !important;"';
    $style2 = 'style="height: 20vh; background-color: #222; color: #fff !important;"';
    $style3 = 'style="height: 20vh; background-color: #222; color: #fff !important;"';
    $style_modal = 'style="height: 20vh; background-color: #222; color: #fff !important;"';
} else if (isset($web) && isset($web->ct_theme) && $web->ct_theme == 1) {
    $style = 'style="height: 20vh; background-color: rgba(255, 255, 255, 0.6); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);"';
    $style2 = 'style="height: 20vh; backdrop-filter: blur(10px);"';
    $style3 = 'style="height: 20vh; background-color: transparent;"';
    $style_modal = 'style="height: 20vh; background-color: rgba(255, 255, 255, 0.4); -webkit-backdrop-filter: blur(10px); backdrop-filter: blur(4px);"';
} else if (isset($web) && isset($web->ct_theme) && $web->ct_theme == 2) {
    $style = 'style="height: 20vh; background-color: rgba(30, 30, 30, 0.8); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); color: white !important;"';
    $style2 = 'style="height: 20vh; backdrop-filter: blur(10px);"';
    $style3 = 'style="height: 20vh; background-color: transparent;"';
    $style_modal = 'style="height: 20vh; background-color: rgba(30, 30, 30, 0.9); -webkit-backdrop-filter: blur(30px); backdrop-filter: blur(5px); color: white !important;"';
} else {
    $style = 'style="background-color: white; height: 20vh;"';
    $style2 = 'style="height: 20vh;"';
    $style3 = 'style="height: 20vh;"';
    $style_modal = 'style="height: 20vh;"';
}
?>

<div class="row mb-1">
    <div class="col-lg-4 col-4" type="button" onclick="empty_trx()">
        <div class="small-box" <?= $style ?>>
            <div class="inner">
                <h4>Empty Transaksi</h4>
            </div>
            <div class="icon">
                <i class="fa-solid fa-recycle"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-4" type="button" onclick="getUrl('Backdoor/data_db')">
        <div class="small-box" <?= $style ?>>
            <div class="inner">
                <h4>Backup & Download Database</h4>
            </div>
            <div class="icon">
                <i class="fa-solid fa-server"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-4" type="button" onclick="empty_all()">
        <div class="small-box" <?= $style ?>>
            <div class="inner">
                <h4>Empty Database</h4>
            </div>
            <div class="icon">
                <i class="fa-solid fa-database"></i>
            </div>
        </div>
    </div>
</div>

<div class="row mb-1">
    <div class="col-lg-4 col-4" type="button" onclick="getUrl('Backdoor/user_akses')">
        <div class="small-box" <?= $style ?>>
            <div class="inner">
                <h4>Akses User</h4>
            </div>
            <div class="icon">
                <i class="fa-solid fa-users-gear"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-4" type="button" onclick="getUrl('Backdoor/menu_akses')">
        <div class="small-box" <?= $style ?>>
            <div class="inner">
                <h4>Akses Menu</h4>
            </div>
            <div class="icon">
                <i class="fa-solid fa-user-gear"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-4" type="button" onclick="getUrl('Backdoor/cabang_akses')">
        <div class="small-box" <?= $style ?>>
            <div class="inner">
                <h4>Akses Cabang</h4>
            </div>
            <div class="icon">
                <i class="fa-solid fa-building"></i>
            </div>
        </div>
    </div>
</div>

<div class="row mb-1">
    <div class="col-lg-4 col-4" type="button" onclick="getUrl('Backdoor/user_role')">
        <div class="small-box" <?= $style ?>>
            <div class="inner">
                <h4>Akses Role</h4>
            </div>
            <div class="icon">
                <i class="fa-solid fa-user-tie"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-4" type="button" onclick="getUrl('Backdoor/for_role')">
        <div class="small-box" <?= $style ?>>
            <div class="inner">
                <h4>Role</h4>
            </div>
            <div class="icon">
                <i class="fa-solid fa-user-secret"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-4" type="button" onclick="getUrl('Backdoor/for_cabang')">
        <div class="small-box" <?= $style ?>>
            <div class="inner">
                <h4>Cabang</h4>
            </div>
            <div class="icon">
                <i class="fa-solid fa-city"></i>
            </div>
        </div>
    </div>
</div>

<div class="row mb-1">
    <div class="col-lg-4 col-4" type="button" onclick="migrasi_db()">
        <div class="small-box" <?= $style ?>>
            <div class="inner">
                <h4>Migrasi Database</h4>
                <span>Last Backup: <?= $web->last_bak ?></span>
            </div>
            <div class="icon">
                <i class="fa-solid fa-clone"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-4" type="button" onclick="restore_db()">
        <div class="small-box" <?= $style ?>>
            <div class="inner">
                <h4>Restore Database</h4>
                <span>Last Restore: <?= $web->last_res ?></span>
            </div>
            <div class="icon">
                <i class="fa-solid fa-window-restore"></i>
            </div>
        </div>
    </div>
</div>

<script>
    function empty_trx() {
        Swal.fire({
            title: "Kamu yakin?",
            html: "<b style='color: red;'>Semua Log User, Transaksi (PO, Pembelian, dan Penjualan) beserta riwayat pasien dan kasir akan di kosongkan!</b>",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, kosongkan",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin

                // jalankan fungsi
                $.ajax({
                    url: siteUrl + 'Backdoor/trx_empty',
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik

                        if (result.status == 1) { // jika mendapatkan hasil 1
                            Swal.fire("Empty Transaksi", "Berhasil dikosongkan", "success").then(() => {
                                location.href = siteUrl + "Auth/logout";
                            });
                        } else { // selain itu

                            Swal.fire("Empty Transaksi", "Gagal dikosongkan" + ", silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error

                        error_proccess();
                    }
                });
            }
        });
    }

    function empty_all() {
        Swal.fire({
            title: "Kamu yakin?",
            html: "<b style='color: red;'>Semua table termasuk master akan di kosongkan!</b>",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, kosongkan",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin
                $('#loading').modal('show');

                // jalankan fungsi
                $.ajax({
                    url: siteUrl + 'Backdoor/db_empty',
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik

                        if (result.status == 1) { // jika mendapatkan hasil 1
                            $('#loading').modal('hide');

                            Swal.fire("Empty Database", "Berhasil dikosongkan", "success").then(() => {
                                location.href = siteUrl + "Auth/logout";
                            });
                        } else { // selain itu
                            $('#loading').modal('hide');

                            Swal.fire("Empty Database", "Gagal dikosongkan" + ", silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error
                        $('#loading').modal('hdie');

                        error_proccess();
                    }
                });
            }
        });
    }

    function migrasi_db() {
        Swal.fire({
            title: "Kamu yakin?",
            html: "<b style='color: red;'>Database akan di migrasikan!</b>",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, migrate",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin

                // jalankan fungsi
                $.ajax({
                    url: siteUrl + 'Backdoor/migrasi',
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik

                        if (result.status == 1) { // jika mendapatkan hasil 1
                            Swal.fire("Migrasi Database", "Berhasil dilakukan", "success");
                        } else { // selain itu

                            Swal.fire("Migrasi Database", "Gagal dilakukan" + ", silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error

                        error_proccess();
                    }
                });
            }
        });
    }

    function restore_db() {
        Swal.fire({
            title: "Kamu yakin?",
            html: "<b style='color: red;'>Database akan di restore!</b>",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, restore",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin

                // jalankan fungsi
                $.ajax({
                    url: siteUrl + 'Backdoor/restore_db',
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik

                        if (result.status == 1) { // jika mendapatkan hasil 1
                            Swal.fire("Restore Database", "Berhasil dilakukan", "success");
                        } else { // selain itu

                            Swal.fire("Restore Database", "Gagal dilakukan" + ", silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error

                        error_proccess();
                    }
                });
            }
        });
    }
</script>