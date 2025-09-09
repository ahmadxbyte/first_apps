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

<form method="post" id="form_gudang">
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
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="id" class="control-label text-danger">ID</label>
                                        <input type="text" class="form-control" id="kodeGudang" name="kodeGudang" placeholder="Otomatis" readonly value="<?= (!empty($gudang) ? $gudang->kode_gudang : '') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="nama" class="control-label text-danger">Nama</label>
                                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan Nama" onkeyup="ubah_nama(this.value, 'nama')" value="<?= (!empty($gudang) ? $gudang->nama : '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="bagian" class="control-label text-danger">Bagian</label>
                                        <select name="bagian" id="bagian" class="form-control select2_global" data-placeholder="~ Pilih">
                                            <option value="">~ Pilih</option>
                                            <option value="Internal" <?= (!empty($gudang) ? ($gudang->bagian == 'Internal') ? 'selected' : '' : '') ?>>Internal</option>
                                            <option value="Logistik" <?= (!empty($gudang) ? ($gudang->bagian == 'Logistik') ? 'selected' : '' : '') ?>>Logistik</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="aktif" class="control-label text-danger">Status</label>
                                        <select name="aktif" id="aktif" class="form-control select2_global" data-placeholder="~ Pilih Status">
                                            <option value="">~ Pilih Status</option>
                                            <option value="0" <?= (!empty($gudang) ? (($gudang->aktif == 0) ? 'selected' : '') : '') ?>>Non-aktif</option>
                                            <option value="1" <?= (!empty($gudang) ? (($gudang->aktif == 1) ? 'selected' : '') : '') ?>>Aktif</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="keterangan" class="control-label text-danger">Keterangan</label>
                                        <textarea name="keterangan" id="keterangan" class="form-control" placeholder="Masukkan Keterangan" onkeyup="ubah_nama(this.value, 'keterangan')"><?= (!empty($gudang) ? $gudang->keterangan : '') ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-danger" onclick="getUrl('Master/gudang')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                            <button type="button" class="btn btn-success float-right ml-2" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                            <?php if (!empty($gudang)) : ?>
                                <button type="button" class="btn btn-info float-right" onclick="getUrl('Master/form_gudang/0')" id="btnBaru"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
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
    const form = $('#form_gudang');
    const btnSimpan = $('#btnSimpan');
    var kodeGudang = $('#kodeGudang');
    var nama = $('#nama');
    var bagian = $('#bagian');
    var aktif = $('#aktif');
    var keterangan = $('#keterangan');

    btnSimpan.attr('disabled', false);

    // fungsi simpan
    function save() {
        btnSimpan.attr('disabled', true);

        if (nama.val() == '' || nama.val() == null) { // jika nama null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Nama", "Form sudah diisi?", "question");
        }

        if (bagian.val() == '' || bagian.val() == null) { // jika bagian null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("No. Hp", "Form sudah diisi?", "question");
        }

        if (aktif.val() == '' || aktif.val() == null) { // jika aktif null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Status", "Form sudah diisi?", "question");
        }

        if (keterangan.val() == '' || keterangan.val() == null) { // jika keterangan null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Fax", "Form sudah diisi?", "question");
        }

        if (kodeGudang.val() == '' || kodeGudang.val() == null) { // jika kode_gudang null/ kosong
            // isi param = 1
            var param = 1;
        } else { // selain itu
            // isi param = 2
            var param = 2;
        }

        // jalankan proses cek gudang
        if (param == 1) {
            $.ajax({
                url: siteUrl + 'Master/cekGud',
                type: 'POST',
                dataType: 'JSON',
                data: form.serialize(),
                success: function(result) { // jika fungsi berjalan dengan baik
                    if (result.status == 1) { // jika mendapatkan respon 1
                        // jalankan fungsi proses berdasarkan param
                        proses(param);
                    } else { // selain itu

                        Swal.fire("Nama", "Sudah ada!, silahkan isi nama lain ", "info");
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
            url: siteUrl + 'Master/gudang_proses/' + param,
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) { // jika fungsi berjalan dengan baik
                btnSimpan.attr('disabled', false);

                if (result.status == 1) { // jika mendapatkan respon 1

                    Swal.fire("Gudang", "Berhasil " + message, "success").then(() => {
                        getUrl('Master/gudang');
                    });
                } else { // selain itu

                    Swal.fire("Gudang", "Gagal " + message + ", silahkan dicoba kembali", "info");
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
        if (kodeGudang.val() == '' || kodeGudang.val() == null) { // jika kode_gudangnya tidak ada isi/ null
            // kosongkan
            kodeGudang.val('');
        }

        nama.val('');
        pajak.val('').change();
        bagian.val('').change();
        keterangan.val('');
    }

    function showGuide() {
        // clean text
        $('#modal_mgLabel').text(``);
        $('#modal-isi').text(``);

        $('#modal_mg').modal('show'); // show modal

        // isi text
        $('#modal_mgLabel').append(`Manual Guide Master Gudang`);
        $('#modal-isi').append(`
            <ol>
                <li style="font-weight: bold;">Atur Default</li>
                <p>
                    <ul>
                        <li>Ceklis pada kolom utama yang akan di jadikan Default</li>
                        <li>Saat Muncul Pop up, klik "Ya, Atur Default"</li>
                    </ul>
                </p>
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