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

<form method="post" id="form_akun">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Formulir</span>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="id" class="control-label text-danger">ID</label>
                                        <input type="text" class="form-control" id="kodeAkun" name="kodeAkun" placeholder="Otomatis" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="nama_akun" class="control-label text-danger">Nama Akun</label>
                                        <input type="text" class="form-control" id="nama_akun" name="nama_akun" placeholder="Masukkan Akun" onkeyup="ubah_nama(this.value, 'nama_akun')">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="kode_klasifikasi" class="control-label text-danger">Klasifikasi</label>
                                        <select name="kode_klasifikasi" id="kode_klasifikasi" class="form-control select2_klasifikasi_akun" data-placeholder="~ Pilih Klasifikasi"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="header">Header <sup id="for_header" class="text-danger">**</sup></label>
                                        <div class="row">
                                            <div class="col-md-1">
                                                <input type="checkbox" name="header" id="header" class="form-control float-left" checked onclick="cek_header()">
                                            </div>
                                            <div class="col-md-11">
                                                <select name="sub_akun" id="sub_akun" class="form-control select2_global" data-placeholder="~ Pilih Akun Header" disabled>
                                                    <option value="">~ Pilih Akun Header</option>
                                                    <?php foreach ($akun as $a) : ?>
                                                        <option value="<?= $a->kode_akun ?>"><?= $a->nama_akun ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="float-right">
                        <button type="button" class="btn btn-info" onclick="reseting()" id="btnReset"><i class="fa-solid fa-arrows-rotate"></i>&nbsp;&nbsp;Reset</button>
                        <?php if ($created == 1) : ?>
                            <button type="button" class="btn btn-success" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Daftar Akun</span>
                    <div class="float-right">
                        <button type="button" class="btn btn-info" onclick="send_data_mail('Master Akun')"><i class="fa-solid fa-paper-plane"></i>&nbsp;&nbsp;Kirim Email</button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-circle-down"></i>&nbsp;&nbsp;Unduh
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="preview('akun')"><i class="fa-solid fa-fw fa-tv"></i>&nbsp;&nbsp;Preview</a></li>
                                <li><a class="dropdown-item" href="#" onclick="print('akun')"><i class="fa-regular fa-fw fa-file-pdf"></i>&nbsp;&nbsp;Pdf</a></li>
                                <li><a class="dropdown-item" href="#" onclick="excel('akun')"><i class="fa-regular fa-fw fa-file-excel"></i>&nbsp;&nbsp;Excel</a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="reloadTable()"><i class="fa-solid fa-rotate-right"></i>&nbsp;&nbsp;Refresh</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table shadow-sm table-hover table-bordered" id="tableAkun" width="100%" style="border-radius: 10px;">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="5%" style="border-radius: 10px 0px 0px 0px;">#</th>
                                            <th width="20%">ID</th>
                                            <th width="30%">Nama</th>
                                            <th width="20%">Klasifikasi</th>
                                            <th width="10%">Header</th>
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
    var table = $('#tableAkun');
    var kodeAkun = $('#kodeAkun');
    var nama_akun = $('#nama_akun');
    var kode_klasifikasi = $('#kode_klasifikasi');
    var sub_akun = $('#sub_akun');
    const form = $('#form_akun');
    const btnSimpan = $('#btnSimpan');
    const for_header = $('#for_header');

    // btnSimpan.attr('disabled', false);
    for_header.hide();

    function cek_header() {
        $('#sub_akun').val('').change();

        if (document.getElementById('header').checked == true) {
            $('#sub_akun').attr('disabled', true);
            for_header.hide();
        } else {
            $('#sub_akun').attr('disabled', false);
            for_header.show();
        }
    }

    // fungsi simpan
    function save() {
        btnSimpan.attr('disabled', true);

        if (nama_akun.val() == '' || nama_akun.val() == null) { // jika nama_akun null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Nama Akun", "Form sudah diisi?", "question");
        }

        if (kode_klasifikasi.val() == '' || kode_klasifikasi.val() == null) { // jika kode_klasifikasi null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Klasifikasi Akun", "Form sudah dipilih?", "question");
        }

        if (document.getElementById('header').checked == false) {
            if (sub_akun.val() == '' || sub_akun.val() == null) { // jika sub_akun null/ kosong
                btnSimpan.attr('disabled', false);

                return Swal.fire("Header", "Form sudah dipilih?", "question");
            }
        }

        if (kodeAkun.val() == '' || kodeAkun.val() == null) { // jika kode_akun null/ kosong
            // isi param = 1
            var param = 1;
        } else { // selain itu
            // isi param = 2
            var param = 2;
        }

        // jalankan proses cek akun
        if (param == 1) {
            $.ajax({
                url: siteUrl + 'Master/cekAkun',
                type: 'POST',
                dataType: 'JSON',
                data: form.serialize(),
                success: function(result) { // jika fungsi berjalan dengan baik
                    if (result.status == 1) { // jika mendapatkan respon 1
                        // jalankan fungsi proses berdasarkan param
                        proses(param);
                    } else { // selain itu

                        Swal.fire("Nama Akun", "Sudah ada!, silahkan isi nama_akun lain ", "info");
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
            url: siteUrl + 'Master/akun_proses/' + param,
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) { // jika fungsi berjalan dengan baik
                btnSimpan.attr('disabled', false);

                if (result.status == 1) { // jika mendapatkan respon 1

                    Swal.fire("Akun", "Berhasil " + message, "success").then(() => {
                        reseting();
                        reloadTable();
                    });
                } else { // selain itu

                    Swal.fire("Akun", "Gagal " + message + ", silahkan dicoba kembali", "info");
                }
            },
            error: function(result) { // jika fungsi error
                btnSimpan.attr('disabled', false);

                error_proccess();
            }
        });
    }

    //fungsi ubah berdasarkan lemparan kode
    function ubah(kode_akun) {
        // Reset form before making the AJAX request
        reseting();

        $.ajax({
            url: siteUrl + 'Master/getInfoAkun/' + kode_akun,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if (result) { // If result exists, fill the form with the result data
                    kodeAkun.val(kode_akun);
                    nama_akun.val(result.nama_akun);

                    // Clear previous options
                    kode_klasifikasi.empty();
                    // Add new option
                    kode_klasifikasi.append(`<option value="${(result.kode_klasifikasi == null ? '' : result.kode_klasifikasi)}">${(result.nama_klasifikasi == null ? '~ Pilih Klasifikasi' : result.nama_klasifikasi)}</option>`);

                    // Set checkbox status
                    document.getElementById('header').checked = (result.header == '2' || result.header == 2);

                    sub_akun.attr('disabled', false);

                    sub_akun.empty();

                    sub_akun_x(result.sub_akun);

                } else { // If no result, reset the form
                    reseting();
                }
            },
            error: function(xhr, status, error) {
                // Optionally, reset the form or show an error message
                reseting();
            }
        });
    }

    function sub_akun_x(sa) {
        if (!sa || sa == null) {
            return
        }

        $.ajax({
            url: siteUrl + 'Master/subAkun/',
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                $.each(result, function(index, value) {
                    if (sa == value.kode_akun) {
                        var sel = 'selected';
                    } else {
                        var sel = '';
                    }

                    sub_akun.append(`<option value="${value.kode_akun}" ${sel}>${value.nama_akun}</option>`);
                });
            },
            error: function(result) {
                error_proccess();
            }
        });
    }

    // fungsi reset form
    function reseting() {
        kodeAkun.val('');
        nama_akun.val('');
        kode_klasifikasi.html('<option value="">~ Pilih Klasifikasi</option>');
        document.getElementById('header').checked = true;
        sub_akun.val('').change();
        for_header.hide();
    }

    // fungsi hapus berdasarkan kode_akun
    function hapus(kode_akun) {
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
                    url: siteUrl + 'Master/delAkun/' + kode_akun,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik
                        btnSimpan.attr('disabled', false);

                        if (result.status == 1) { // jika mendapatkan hasil 1

                            Swal.fire("Akun", "Berhasil di hapus!", "success").then(() => {
                                reloadTable();
                            });
                        } else { // selain itu

                            Swal.fire("Akun", "Gagal di hapus!, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error
                        btnSimpan.attr('disabled', false);

                        error_proccess();
                    }
                });
            }
        });
    }

    function showGuide() {
        // clean text
        $('#modal_mgLabel').text(``);
        $('#modal-isi').text(``);

        $('#modal_mg').modal('show'); // show modal

        // isi text
        $('#modal_mgLabel').append(`Manual Guide Master Akun`);
        $('#modal-isi').append(`
            <ol>
                <li style="font-weight: bold;">Tambah Data</li>
                <p>
                    <ul>
                        <li>Isi Form Nama Akun<br><span style='color: red;'>Teks berawarna merah</span> mengartikan wajib terisi</li>
                        <li>Pilih Form Klasifikasi</li>
                        <li>Atur apakah menjadi Header atau Sub-header</li>
                        <li>Klik tombol Proses</li>
                    </ul>
                </p>
                <li style="font-weight: bold;">Ubah Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Ubah pada list data yang ingin di ubah</li>
                        <li>Ubah Form Nama Akun atau Klasifikasi atau pengaturan Header/Sub-header<br><span style='color: red;'>Teks berawarna merah</span> mengartikan wajib terisi</li>
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