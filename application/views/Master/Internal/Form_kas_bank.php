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

<form method="post" id="form_kas_bank">
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
                                <label for="kode_kas_bank" class="control-label text-danger">Kode</label>
                                <input type="text" class="form-control" placeholder="Otomatis" id="kode_kas_bank" name="kode_kas_bank" value="<?= ((!empty($data_kas_bank)) ? $data_kas_bank->kode_kas_bank : '') ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="" class="control-label text-danger">Nama Kas</label>
                                <input type="text" class="form-control" placeholder="Nama Kas" id="nama" name="nama" value="<?= ((!empty($data_kas_bank)) ? $data_kas_bank->nama : '') ?>" onkeyup="ubah_nama(this.value, 'nama')">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tipe" class="control-label text-danger">Tipe</label>
                                <select name="tipe" id="tipe" class="form-control select2_global" data-placeholder="~ Pilih Tipe">
                                    <option value="">~ Pilih Tipe</option>
                                    <option value="1" <?= (!empty($data_kas_bank) ? ($data_kas_bank->tipe == '1') ? 'selected' : '' : '') ?>>Cash</option>
                                    <option value="2" <?= (!empty($data_kas_bank) ? ($data_kas_bank->tipe == '2') ? 'selected' : '' : '') ?>>Bank</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="akun" class="control-label text-danger">Akun</label>
                                <select name="akun" id="akun" class="form-control select2_global" data-placeholder="~ Pilih Akun">
                                    <option value="">~ Pilih Akun</option>
                                    <option value="1" <?= (!empty($data_kas_bank) ? ($data_kas_bank->akun == '1') ? 'selected' : '' : '') ?>>Kas Besar</option>
                                    <option value="2" <?= (!empty($data_kas_bank) ? ($data_kas_bank->akun == '2') ? 'selected' : '' : '') ?>>Kas Kecil</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-danger" onclick="getUrl('Master/kas_bank')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                            <button type="button" class="btn btn-success float-right ml-2" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                            <?php if (!empty($data_kas_bank)) : ?>
                                <button type="button" class="btn btn-info float-right" onclick="getUrl('Master/form_kas_bank/0')" id="btnBaru"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
                            <?php else : ?>
                                <button type="button" class="btn btn-info float-right" onclick="reseting()" id="btnReset"><i class="fa-solid fa-arrows-rotate"></i>&nbsp;&nbsp;Reset</button>
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
    const form = $('#form_kas_bank');
    const btnSimpan = $('#btnSimpan');
    var kode_kas_bank = $('#kode_kas_bank');
    var nama = $('#nama');
    var tipe = $('#tipe');
    var akun = $('#akun');

    btnSimpan.attr('disabled', false);

    // fungsi daftarkan akun
    function save() {
        btnSimpan.attr('disabled', true);

        if (nama.val() == '' || nama.val() == null) { // jika nama kosong/ null
            btnSimpan.attr('disabled', false);
            return Swal.fire("Nama", "Form sudah diisi?", "question");
        }

        if (tipe.val() == '' || tipe.val() == null) { // jika tipe kosong/ null
            btnSimpan.attr('disabled', false);
            return Swal.fire("Tipe", "Form sudah diisi?", "question");
        }

        if (akun.val() == '' || akun.val() == null) { // jika akun kosong/ null
            btnSimpan.attr('disabled', false);
            return Swal.fire("Akun", "Form sudah diisi?", "question");
        }

        if (kode_kas_bank.val() == '' || kode_kas_bank.val() == null) { // jika kode perawat kosong/ null
            // isi param = 1
            var param = 1;
        } else { // selain itu
            // isi param = 2
            var param = 2;
        }

        // jalankan proses cek logistik
        if (param == 1) {
            $.ajax({
                url: siteUrl + 'Master/cekKas_bank',
                type: 'POST',
                dataType: 'JSON',
                data: form.serialize(),
                success: function(result) { // jika fungsi berjalan dengan baik
                    if (result.status == 1) { // jika mendapatkan respon 1
                        // jalankan fungsi proses berdasarkan param
                        proses(param);
                    } else { // selain itu
                        btnSimpan.attr('disabled', false);

                        Swal.fire("Nama", "Sudah digunakan!, silahkan gunakan nama lain ", "info");
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
            url: siteUrl + 'Master/kas_bank_proses/' + param,
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) { // jika fungsi berjalan dengan baik
                btnSimpan.attr('disabled', false);

                if (result.status == 1) { // jika mendapatkan respon 1

                    Swal.fire("Kas & Bank", "Berhasil " + message, "success").then(() => {
                        getUrl('Master/kas_bank');
                    });
                } else { // selain itu

                    Swal.fire("Kas & Bank", "Gagal " + message + ", silahkan dicoba kembali", "info");
                }
            },
            error: function(result) { // jika fungsi error
                btnSimpan.attr('disabled', false);

                error_proccess();
            }
        });
    }

    // fungsi reset form
    function reseting() {
        if (kode_kas_bank.val() == '' || kode_kas_bank.val() == null) { // jika kode_kas_banknya tidak ada isi/ null
            // kosongkan
            kode_kas_bank.val('');
        }

        nama.val('');
        tipe.val('').change();
        akun.val('').change();
    }

    function showGuide() {
        // clean text
        $('#modal_mgLabel').text(``);
        $('#modal-isi').text(``);

        $('#modal_mg').modal('show'); // show modal

        // isi text
        $('#modal_mgLabel').append(`Manual Guide Master Kas & Bank`);
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
            </ol>
        `);
    }
</script>