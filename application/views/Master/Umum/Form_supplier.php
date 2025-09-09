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
<form method="post" id="form_supplier">
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
                                        <input type="text" class="form-control" id="kodeSupplier" name="kodeSupplier" placeholder="Otomatis" readonly value="<?= (!empty($supplier) ? $supplier->kode_supplier : '') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="nama" class="control-label text-danger">Nama</label>
                                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan Nama" onkeyup="ubah_nama(this.value, 'nama')" value="<?= (!empty($supplier) ? $supplier->nama : '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="nohp" class="control-label text-danger">No. Hp</label>
                                        <input type="text" class="form-control" id="nohp" name="nohp" placeholder="Masukan No. Hp" value="<?= (!empty($supplier) ? $supplier->nohp : '') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="email" class="control-label text-danger">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan Email" onchange="cekEmail('email')" value="<?= (!empty($supplier) ? $supplier->email : '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="fax" class="control-label text-danger">Fax</label>
                                        <input type="number" class="form-control" id="fax" name="fax" placeholder="Masukan Fax" value="<?= (!empty($supplier) ? $supplier->fax : '') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="alamat" class="control-label text-danger">Alamat</label>
                                        <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Masukkan Alamat" onkeyup="ubah_nama(this.value, 'alamat')" value="<?= (!empty($supplier) ? $supplier->alamat : '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-danger" onclick="getUrl('Master/supplier')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                            <button type="button" class="btn btn-success float-right ml-2" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                            <?php if (!empty($supplier)) : ?>
                                <button type="button" class="btn btn-info float-right" onclick="getUrl('Master/form_supplier/0')" id="btnBaru"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
                            <?php else : ?>
                                <button type="button" class="btn btn-info float-right" onclick="reset()" id="btnReset"><i class="fa-solid fa-arrows-rotate"></i>&nbsp;&nbsp;Reset</button>
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
    const form = $('#form_supplier');
    const btnSimpan = $('#btnSimpan');
    var kodeSupplier = $('#kodeSupplier');
    var nama = $('#nama');
    var nohp = $('#nohp');
    var email = $('#email');
    var fax = $('#fax');
    var alamat = $('#alamat');

    btnSimpan.attr('disabled', false);

    // fungsi simpan
    function save() {
        btnSimpan.attr('disabled', true);

        if (nama.val() == '' || nama.val() == null) { // jika nama null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Nama", "Form sudah diisi?", "question");
        }

        if (nohp.val() == '' || nohp.val() == null) { // jika nohp null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("No. Hp", "Form sudah diisi?", "question");
        }

        if (email.val() == '' || email.val() == null) { // jika email null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Email", "Form sudah diisi?", "question");
        }

        if (fax.val() == '' || fax.val() == null) { // jika fax null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Fax", "Form sudah diisi?", "question");
        }

        if (alamat.val() == '' || alamat.val() == null) { // jika alamat null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Alamat", "Form sudah diisi?", "question");
        }

        if (kodeSupplier.val() == '' || kodeSupplier.val() == null) { // jika kode_supplier null/ kosong
            // isi param = 1
            var param = 1;
        } else { // selain itu
            // isi param = 2
            var param = 2;
        }

        // jalankan proses cek supplier
        if (param == 1) {
            $.ajax({
                url: siteUrl + 'Master/cekSup',
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
            url: siteUrl + 'Master/supplier_proses/' + param,
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) { // jika fungsi berjalan dengan baik
                btnSimpan.attr('disabled', false);

                if (result.status == 1) { // jika mendapatkan respon 1

                    Swal.fire("Supplier", "Berhasil " + message, "success").then(() => {
                        getUrl('Master/supplier');
                    });
                } else { // selain itu

                    Swal.fire("Supplier", "Gagal " + message + ", silahkan dicoba kembali", "info");
                }
            },
            error: function(result) { // jika fungsi error
                btnSimpan.attr('disabled', false);

                error_proccess();
            }
        });
    }

    // fungsi reset form
    function reset() {
        if (kodeSupplier.val() == '' || kodeSupplier.val() == null) { // jika kode_suppliernya tidak ada isi/ null
            // kosongkan
            kodeSupplier.val('');
        }

        nama.val('');
        email.val('');
        nohp.val('');
        fax.val('');
        alamat.val('');
    }

    function showGuide() {
        // clean text
        $('#modal_mgLabel').text(``);
        $('#modal-isi').text(``);

        $('#modal_mg').modal('show'); // show modal

        // isi text
        $('#modal_mgLabel').append(`Manual Guide Master Pemasok`);
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