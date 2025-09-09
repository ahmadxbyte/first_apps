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
?>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box" style="background: rgb(0, 123, 255, 1); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);">
            <div class="inner">
                <h3><span id="pendaftaran_count">0</span></h3>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const targetValue = <?= count($this->M_global->getDataResult('pendaftaran', ['kode_cabang' => $this->session->userdata('cabang'), 'tgl_daftar' => date('Y-m-d')])) ?>;
                        const duration = 2000; // Animation duration in milliseconds
                        const counterElement = document.getElementById('pendaftaran_count');
                        let startValue = 0;
                        const increment = targetValue / (duration / 10);

                        const counterInterval = setInterval(() => {
                            startValue += increment;
                            if (startValue >= targetValue) {
                                startValue = targetValue;
                                clearInterval(counterInterval);
                            }
                            counterElement.textContent = new Intl.NumberFormat().format(Math.floor(startValue));
                        }, 10);
                    });
                </script>
                <p>Terdaftar</p>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-users"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box" style="background: rgb(23, 162, 184, 1); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);">
            <div class="inner">
                <h3><span id="pendaftaran_count_pros">0</span></h3>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const targetValue = <?= count($this->M_global->getDataResult('pendaftaran', ['kode_cabang' => $this->session->userdata('cabang'), 'status_trx' => 0, 'tgl_daftar' => date('Y-m-d')])) ?>;
                        const duration = 2000; // Animation duration in milliseconds
                        const counterElement = document.getElementById('pendaftaran_count_pros');
                        let startValue = 0;
                        const increment = targetValue / (duration / 10);

                        const counterInterval = setInterval(() => {
                            startValue += increment;
                            if (startValue >= targetValue) {
                                startValue = targetValue;
                                clearInterval(counterInterval);
                            }
                            counterElement.textContent = new Intl.NumberFormat().format(Math.floor(startValue));
                        }, 10);
                    });
                </script>
                <p>Proses</p>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-user-clock"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box" style="background: rgb(51, 212, 87, 1); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);">
            <div class="inner">
                <h3><span id="pendaftaran_count_done">0</span></h3>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const targetValue = <?= count($this->M_global->getDataResult('pendaftaran', ['kode_cabang' => $this->session->userdata('cabang'), 'status_trx' => 1, 'tgl_daftar' => date('Y-m-d')])) ?>;
                        const duration = 2000; // Animation duration in milliseconds
                        const counterElement = document.getElementById('pendaftaran_count_done');
                        let startValue = 0;
                        const increment = targetValue / (duration / 10);

                        const counterInterval = setInterval(() => {
                            startValue += increment;
                            if (startValue >= targetValue) {
                                startValue = targetValue;
                                clearInterval(counterInterval);
                            }
                            counterElement.textContent = new Intl.NumberFormat().format(Math.floor(startValue));
                        }, 10);
                    });
                </script>
                <p>Selesai</p>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-user-check"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box" style="background: rgb(200, 35, 51, 1); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);">
            <div class="inner">
                <h3><span id="pendaftaran_count_ccl">0</span></h3>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const targetValue = <?= count($this->M_global->getDataResult('pendaftaran', ['kode_cabang' => $this->session->userdata('cabang'), 'status_trx' => 2, 'tgl_daftar' => date('Y-m-d')])) ?>;
                        const duration = 2000; // Animation duration in milliseconds
                        const counterElement = document.getElementById('pendaftaran_count_ccl');
                        let startValue = 0;
                        const increment = targetValue / (duration / 10);

                        const counterInterval = setInterval(() => {
                            startValue += increment;
                            if (startValue >= targetValue) {
                                startValue = targetValue;
                                clearInterval(counterInterval);
                            }
                            counterElement.textContent = new Intl.NumberFormat().format(Math.floor(startValue));
                        }, 10);
                    });
                </script>
                <p>Batal</p>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-user-times"></i>
            </div>
        </div>
    </div>
</div>

<form method="post" id="form_pendaftaran">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6 col-12 mb-3">
                            <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Pendaftaran Member</span>
                        </div>
                        <div class="col-md-6 col-12 mb-3">
                            <div class="float-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fa-solid fa-circle-down"></i>&nbsp;&nbsp;Unduh
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="preview('pendaftaran')"><i class="fa-solid fa-fw fa-tv"></i>&nbsp;&nbsp;Preview</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="print('pendaftaran')"><i class="fa-regular fa-fw fa-file-pdf"></i>&nbsp;&nbsp;Pdf</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="excel('pendaftaran')"><i class="fa-regular fa-fw fa-file-excel"></i>&nbsp;&nbsp;Excel</a></li>
                                    </ul>
                                </div>
                                <button type="button" class="btn btn-primary" onclick="reloadTable()"><i class="fa-solid fa-rotate-right"></i>&nbsp;&nbsp;Refresh</button>
                                <?php if ($created == 1) : ?>
                                    <a type="button" class="btn btn-success" href="<?= site_url('Health/form_pendaftaran/0?no_anjungan=') . $anjungan ?>"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 col-6 mb-2">
                            <input type="text" id="no_anjungan" name="no_anjungan" value="<?= $anjungan ?>" class="form-control">
                        </div>
                        <div class="col-md-6 col-6 mb-2">
                            <button type="button" class="btn btn-primary" id="btnPanggilAnjungan" <?= (($anjungan ==  '') ? 'disabled' : 'onclick="panggil_anjungan()"') ?>><i class="fa-solid fa-volume-high"></i> Panggil</button>
                            <button type="button" class="btn btn-warning" id="btnLewatiAnjungan" <?= (($anjungan ==  '') ? 'disabled' : 'onclick="lewati_anjungan()"') ?>><i class="fa-solid fa-volume-xmark"></i> Lewati</button>
                        </div>
                        <div class="col-md-4 col-12">
                            <div class="input-group float-right">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1">Komputer <span class="text-danger font-weight-bold">(max: 50)</span></span>
                                </div>
                                <input type="text" id="komputer" name="komputer" value="1" min="1" class="form-control text-center" max="50" onkeyup="cek_limit(this.value)">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6 col-12 mb-2">
                            <select name="kode_poli" id="kode_poli" class="select2_poli" data-placeholder="~ Pilih Poli" onchange="getPoli(this.value)"></select>
                        </div>
                        <div class="col-md-6 col-12 mb-2">
                            <div class="row">
                                <div class="col-md-4 col-4 mb-3">
                                    <input type="date" name="dari" id="dari" class="form-control" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-4 col-4 mb-3">
                                    <input type="date" name="sampai" id="sampai" class="form-control" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-4 col-4 mb-3">
                                    <button type="button" class="btn btn-info" style="width: 100%" onclick="filter($('#kode_poli').val())"><i class="fa-solid fa-sort"></i>&nbsp;&nbsp;Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table shadow-sm table-hover table-bordered" id="tablePendaftaran" width="100%" style="border-radius: 10px;">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="5%" style="border-radius: 10px 0px 0px 0px;">#</th>
                                            <th width="15%">No. Trx</th>
                                            <th width="10%">Member</th>
                                            <th>Tgl/Jam Masuk</th>
                                            <th>Tgl/Jam Keluar</th>
                                            <th>Poli</th>
                                            <th>Dokter</th>
                                            <th>Antri</th>
                                            <th>User</th>
                                            <th width="15%" style="border-radius: 0px 10px 0px 0px;">Aksi</th>
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
    var table = $('#tablePendaftaran');

    //fungsi ubah berdasarkan lemparan kode
    function ubah(kode_pendaftaran) {
        // jalankan fungsi
        getUrl('Health/form_pendaftaran/' + kode_pendaftaran);
    }

    function cek_limit(limit) {
        // Convert input to number and handle non-numeric values
        let numLimit = Number(limit);

        if (isNaN(numLimit) || !limit) {
            $('#komputer').val(1);
        } else if (numLimit > 50) {
            $('#komputer').val(50);
        } else if (numLimit < 1) {
            $('#komputer').val(1);
        } else {
            $('#komputer').val(Math.floor(numLimit));
        }
    }

    // fungsi hapus berdasarkan no_trx
    function hapus(no_trx) {
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
                    url: siteUrl + 'Health/delPendaftaran/' + no_trx,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik

                        if (result.status == 1) { // jika mendapatkan hasil 1
                            Swal.fire("Pendaftaran", "Berhasil di hapus!", "success").then(() => {
                                window.location.reload();
                            });
                        } else { // selain itu

                            Swal.fire("Pendaftaran", "Gagal di hapus!, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error

                        error_proccess();
                    }
                });
            }
        });
    }

    // fungsi group by poli
    function getPoli(x) {
        filter(x);
    }

    // fungsi aktif/non-aktif akun
    function actived(no_trx) {
        var pesan = "Pendaftaran ini akan dibatalkan!";
        var pesan2 = "dibatalkan!";
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
                    url: siteUrl + 'Health/activedpendaftaran/' + no_trx,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik

                        if (result.status == 1) { // jika mendapatkan hasil 1
                            Swal.fire("Pengguna", "Berhasil " + pesan2, "success").then(() => {
                                reloadTable();
                            });
                        } else { // selain itu

                            Swal.fire("Pengguna", "Gagal " + pesan2 + ", silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error

                        error_proccess();
                    }
                });
            }
        });
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
                    const githubUrl = `${siteUrl}Health/email/${x}?email=${email}`;
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
                    Swal.fire("Berkas Pendaftaran", "Berhasil dikirim via Email!, silahkan cek email", "success");
                } else {
                    Swal.fire("Berkas Pendaftaran", "Gagal dikirim via Email!, silahkan cek email", "info");
                }
            }
        });
    }

    function showGuide() {
        // clean text
        $('#modal_mgLabel').text(``);
        $('#modal-isi').text(``);

        $('#modal_mg').modal('show'); // show modal

        // isi text
        $('#modal_mgLabel').append(`Manual Guide Pendaftaran`);
        $('#modal-isi').append(`
            <ol>
                <li style="font-weight: bold;">Tambah Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Tambah</li>
                        <li>Selanjutnya isikan Form yang tersedia<br><span style='color: red;'>Teks berawarna merah</span> mengartikan wajib terisi</li>
                        <li>Klik tombol Proses</li>
                    </ul>
                </p>
                <li style="font-weight: bold;">Ubah Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Ubah pada list data yang ingin di ubah</li>
                        <li>Ubah isi Form yang akan di ubah<br><span style='color: red;'>Teks berawarna merah</span> mengartikan wajib terisi</li>
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
                <li style="font-weight: bold;">Pembatalan Pendaftaran</li>
                <p>
                    <ul>
                        <li>Klik tombol Batal pada list data yang ingin di batalkan</li>
                        <li>Saat Muncul Pop Up, klik "Ya, Batalkan"</li>
                    </ul>
                </p>
            </ol>
        `);
    }

    function panggil_anjungan() {
        // Panggil endpoint untuk update status di database
        $.ajax({
            url: `<?= site_url() ?>Health/panggil/${$('#no_anjungan').val()}/${$('#komputer').val()}`,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if (result.status == 1) {
                    // Tampilkan notifikasi sukses
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Berhasil Dipanggil!",
                        showConfirmButton: false,
                        timer: 1000
                    });
                } else {
                    // Tampilkan notifikasi gagal
                    Swal.fire({
                        position: "center",
                        icon: "info",
                        title: "Gagal Memanggil!",
                        showConfirmButton: false,
                        timer: 1000
                    });
                }
            },
            error: function(error) {
                error_proccess();
            }
        });
    }

    function lewati_anjungan() {
        $.ajax({
            url: `<?= site_url() ?>Health/lewati/${$('#no_anjungan').val()}`,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if (result.status == 1) {
                    // Tampilkan notifikasi sukses
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Berhasil Dilewati!",
                        showConfirmButton: false,
                        timer: 1000
                    });

                    $('#no_anjungan').val(result.next_anjungan);
                } else {
                    // Tampilkan notifikasi gagal
                    Swal.fire({
                        position: "center",
                        icon: "info",
                        title: "Gagal Melewati!",
                        showConfirmButton: false,
                        timer: 1000
                    });
                }
            },
            error: function(error) {
                error_proccess();
            }
        });
    }
</script>