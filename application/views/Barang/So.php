<?php
$created    = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->created;

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

echo _lock_so();
?>

<form id="form_schedule_so" method="post">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Jadwal SO</span>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row text-center">
                        <div class="col-md-6 col-12">
                            <span class="h5">PERIODE DARI</span>
                        </div>
                        <div class="col-md-6 col-12">
                            <span class="h5">PERIODE SAMPAI</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label>Tanggal Mulai <span class="text-danger">**</span></label>
                                    <input type="hidden" id="id_so" name="id_so" value="<?= ((!empty($cek_jadwal)) ? $cek_jadwal->id : null) ?>">
                                    <input type="date" name="tgl_dari_so" id="tgl_dari_so" value="<?= ((!empty($cek_jadwal)) ? date('Y-m-d', strtotime($cek_jadwal->tgl_dari)) : date('Y-m-d')) ?>" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label>Jam Mulai <span class="text-danger">**</span></label>
                                    <input type="time" name="jam_dari_so" id="jam_dari_so" value="<?= ((!empty($cek_jadwal)) ? date('H:i:s', strtotime($cek_jadwal->jam_dari)) : date('H:i:s', strtotime('23:59:59'))) ?>" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label>Tanggal Selesai <span class="text-danger">**</span></label>
                                    <input type="date" name="tgl_sampai_so" id="tgl_sampai_so" value="<?= ((!empty($cek_jadwal)) ? date('Y-m-d', strtotime($cek_jadwal->tgl_sampai)) : date('Y-m-d', strtotime('+1 Day'))) ?>" class="form-control">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label>Jam Selesai <span class="text-danger">**</span></label>
                                    <input type="time" name="jam_sampai_so" id="jam_sampai_so" value="<?= ((!empty($cek_jadwal)) ? date('H:i:s', strtotime($cek_jadwal->jam_sampai)) : date('H:i:s', strtotime('23:59:59'))) ?>" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <?php if ($created == 1) : ?>
                                <button type="button" class="btn btn-danger float-right" id="btnSchedule" onclick="buat_schedule()"><i class="fa fa-fw fa-server"></i> Jalankan Proses SO</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    const form_lock = $('#form_schedule_so')
    var id_so = $('#id_so')
    var tgl_dari_so = $('#tgl_dari_so')
    var jam_dari_so = $('#jam_dari_so')
    var tgl_sampai_so = $('#tgl_sampai_so')
    var jam_sampai_so = $('#jam_sampai_so')

    function buat_schedule() {
        if (tgl_dari_so.val() == '' || jam_dari_so.val() == '' || tgl_sampai_so.val() == '' || jam_sampai_so.val() == '' || tgl_dari_so.val() == null || jam_dari_so.val() == null || tgl_sampai_so.val() == null || jam_sampai_so.val() == null) {
            return Swal.fire("Jadwal SO", "Form harus lengkap!, silahkan dicoba kembali", "info");
        }

        $.ajax({
            url: siteUrl + 'Transaksi/schedule_so',
            type: 'POST',
            data: form_lock.serialize(),
            dataType: 'JSON',
            success: function(result) { // jika fungsi berjalan dengan baik

                if (result.status == 1) { // jika mendapatkan hasil 1
                    Swal.fire("Jadwal Stok", "Berhasil di kunci!", "success").then(() => {
                        reloadTable();
                    });
                } else { // selain itu

                    Swal.fire("Jadwal Stok", "Gagal di kunci!, silahkan dicoba kembali", "info");
                }
            },
            error: function(result) { // jika fungsi error

                error_proccess();
            }
        })
    }
</script>

<form method="post" id="form_so">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Stock Opname</span>
                    <div class="float-right">
                        <button type="button" class="btn btn-primary" onclick="reloadTable()"><i class="fa-solid fa-rotate-right"></i>&nbsp;&nbsp;Refresh</button>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-5 col-5">
                            <input type="date" name="dari" id="dari" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-5 col-5">
                            <input type="date" name="sampai" id="sampai" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-2 col-2">
                            <button type="button" style="width: 100%;" class="btn btn-info" onclick="filter('')"><i class="fa-solid fa-sort"></i>&nbsp;&nbsp;Filter</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table shadow-sm table-hover table-bordered" id="tablePenyesuaianStok" width="100%" style="border-radius: 10px;">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="5%" style="border-radius: 10px 0px 0px 0px;">#</th>
                                            <th width="20%">Tgl/Jam Mulai</th>
                                            <th width="20%">Tgl/Jam Selesai</th>
                                            <th width="25%">User</th>
                                            <th width="10%" style="border-radius: 0px 10px 0px 0px;">Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    // variable
    var table = $('#tablePenyesuaianStok');

    //fungsi ubah berdasarkan lemparan kode
    function ubah(x) {
        // jalankan fungsi
        $.ajax({
            url: '<?= site_url() ?>Transaksi/getDataSo/' + x,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                id_so.val(x);
                tgl_dari_so.val(result.tgl_dari);
                jam_dari_so.val(result.jam_dari);
                tgl_sampai_so.val(result.tgl_sampai);
                jam_sampai_so.val(result.jam_sampai);
            },
            error: function(error) {
                error_proccess();
            }
        })
    }

    // fungsi hapus berdasarkan invoice
    function hapus(x) {
        // ajukan pertanyaaan
        Swal.fire({
            title: "Kamu yakin?",
            text: "Data yang dihapus tidak bisa dikembalikan!",
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
                    url: siteUrl + 'Transaksi/delJadwalSo/' + x,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik

                        if (result.status == 1) { // jika mendapatkan hasil 1
                            Swal.fire("Jadwal SO", "Berhasil di hapus!", "success").then(() => {
                                getUrl('Transaksi/so');
                            });
                        } else { // selain itu

                            Swal.fire("Jadwal SO", "Gagal di hapus!, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error

                        error_proccess();
                    }
                });
            }
        });
    }

    // fungsi acc/unacc
    function valided(invoice, param) {
        if (param == 0) {
            var pesan = "Penyesuaian Stok ini akan di re-acc!";
            var pesan2 = "di re-acc!";
        } else {
            var pesan = "Penyesuaian Stok ini akan diacc!";
            var pesan2 = "diacc!";
        }
        // ajukan pertanyaaan
        Swal.fire({
            title: "Kamu yakin?",
            text: pesan,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, " + pesan2,
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin

                // jalankan fungsi
                $.ajax({
                    url: siteUrl + 'Transaksi/accpenyesuaian_stok/' + invoice + '/' + param,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik

                        if (result.status == 1) { // jika mendapatkan hasil 1
                            Swal.fire("Penyesuaian Stok", "Berhasil " + pesan2, "success").then(() => {
                                reloadTable();
                            });
                        } else { // selain itu

                            Swal.fire("Penyesuaian Stok", "Gagal " + pesan2 + ", silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error

                        error_proccess();
                    }
                });
            }
        });
    }

    // fungsi cetak
    function cetak(x, y) {
        printsingle('Transaksi/single_print_ps/' + x + '/' + y);
    }

    // fungsi kirim email
    function email(x) {
        Swal.fire({
            title: "Masukan Email",
            input: "text",
            inputAttributes: {
                autocapitalize: "off"
            },
            showCancelButton: true,
            confirmButtonText: "Kirim",
            cancelButtonText: "Tutup",
            showLoaderOnConfirm: true,
            preConfirm: async (email) => {
                try {
                    const githubUrl = `${siteUrl}Transaksi/email/${x}?email=${email}`;
                    const response = await fetch(githubUrl);
                    if (!response.ok) {
                        return Swal.showValidationMessage(`${JSON.stringify(await response.json())}`);
                    }
                    return response.json();
                } catch (error) {
                    Swal.showValidationMessage(`Request failed: ${error}`);
                }
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value.status == 1) {
                    Swal.fire("Laporan Penyesuaian Stok", "Berhasil dikirim via Email!, silahkan cek email", "success");
                } else {
                    Swal.fire("Laporan Penyesuaian Stok", "Gagal dikirim via Email!, silahkan cek email", "info");
                }
            }
        });
    }
</script>