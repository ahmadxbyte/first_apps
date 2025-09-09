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
<form method="post" id="form_coa">
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
                                        <input type="text" class="form-control" id="idCoa" name="idCoa" placeholder="Masukan Id" value="<?= (!empty($coa) ? $coa->id : '') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="coa_name" class="control-label text-danger">Group COA</label>
                                        <select name="coa_group" id="coa_group" class="form-control select2_group_coa" data_placeholder="~ Pilih Group Coa"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="coa_name" class="control-label text-danger">Nama</label>
                                <input type="text" class="form-control" id="coa_name" name="coa_name" placeholder="Masukan Nama" onkeyup="ubah_nama(this.value, 'coa_name')" value="<?= (!empty($coa) ? $coa->coa_name : '') ?>">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="coa_name" class="control-label">Parent</label>
                                        <select name="parent_id" id="parent_id" class="form-control select2_master_coa" data-placeholder="~ Pilih Master Coa"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="is_header" class="control-label">Header</label>
                                        <select name="is_header" id="is_header" class="form-control select2_global">
                                            <option value="0">Bukan</option>
                                            <option value="1">Ya</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="is_active" class="control-label">Aktif</label>
                                        <select name="is_active" id="is_active" class="form-control select2_global">
                                            <option value="1">Ya</option>
                                            <option value="0">Bukan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="normal_balance" class="control-label text-danger">Balance</label>
                                        <select name="normal_balance" id="normal_balance" class="form-control select2_global">
                                            <option value="">~ Pilih Balance</option>
                                            <option value="Debit">Debit</option>
                                            <option value="Credit">Credit</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="remark" class="control-label">Catatan</label>
                            <textarea name="remark" id="remark" class="form-control" rows="3"><?= (!empty($coa) ? $coa->remark : '') ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-danger" onclick="getUrl('Master/coa')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                            <button type="button" class="btn btn-success float-right ml-2" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                            <?php if (!empty($coa)) : ?>
                                <button type="button" class="btn btn-info float-right" onclick="getUrl('Master/form_coa/0')" id="btnBaru"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
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
    const form = $('#form_coa');
    const btnSimpan = $('#btnSimpan');
    var idCoa = $('#idCoa');
    var coa_name = $('#coa_name');
    var coa_group = $('#coa_group');
    var normal_balance = $('#normal_balance');

    btnSimpan.attr('disabled', false);

    // fungsi simpan
    function save() {
        btnSimpan.attr('disabled', true);

        if (idCoa.val() == '' || idCoa.val() == null) { // jika idCoa null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Nama", "Form sudah diisi?", "question");
        }

        if (coa_name.val() == '' || coa_name.val() == null) { // jika coa_name null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Nama", "Form sudah diisi?", "question");
        }

        if (coa_group.val() == '' || coa_group.val() == null) { // jika coa_group null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Group COA", "Form sudah diisi?", "question");
        }

        if (normal_balance.val() == '' || normal_balance.val() == null) { // jika normal_balance null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Normal Balance", "Form sudah diisi?", "question");
        }

        // jalankan proses cek coa
        proses();
    }

    // fungsi proses dengan param
    function proses() {
        // jalankan proses dengan param insert/update
        $.ajax({
            url: siteUrl + 'Master/coa_proses',
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) { // jika fungsi berjalan dengan baik
                btnSimpan.attr('disabled', false);

                if (result.status == 1) { // jika mendapatkan respon 1

                    Swal.fire("Master COA", "Berhasil diproses", "success").then(() => {
                        getUrl('Master/coa');
                    });
                } else { // selain itu

                    Swal.fire("Master COA", "Gagal diproses" + ", silahkan dicoba kembali", "info");
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
        idCoa.val('');
        coa_group.val('');
        coa_name.val('');
        normal_balance.val('').change();
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