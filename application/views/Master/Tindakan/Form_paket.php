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

<form method="post" id="form_tindakan">
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
                                        <label for="id" class="control-label text-danger">Kode Tindakan</label>
                                        <input type="text" class="form-control" id="kodeTindakan" name="kodeTindakan" placeholder="Otomatis" value="<?= (!empty($tindakan) ? $tindakan->kode_tindakan : '') ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="keterangan" class="control-label text-danger">Tindakan</label>
                                        <input type="text" class="form-control" id="keterangan" name="keterangan" placeholder="Masukkan Tindakan" onkeyup="ubah_nama(this.value, 'keterangan')" value="<?= (!empty($tindakan) ? $tindakan->keterangan : '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="kategori" class="control-label text-danger">Kategori Tindakan</label>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <select name="kategori" id="kategori" class="form-control select2_kategori_tarif" data-placeholder="~ Pilih Kategori">
                                                    <?php if (!empty($tindakan)) :
                                                        $kategori = $this->M_global->getData('kategori_tarif', ['kode_kategori' => $tindakan->kode_kategori]); ?>
                                                        <option value="<?= $tindakan->kode_kategori; ?>"><?= $kategori->keterangan ?></option>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="button" class="btn btn-primary" data-toggle="modal" id="btnTambahModal" data-target="#modal_kategori" style="width: 100%;">
                                                    <i class="fa-solid fa-circle-plus"></i> Tambah
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="jenis">Jenis Tindakan</label>
                                        <input type="hidden" class="form-control" id="jenis" name="jenis" placeholder="Jenis Tindakan" value="1">
                                        <input type="text" class="form-control" id="jenisx" name="jenisx" placeholder="Jenis Tindakan" value="Paket" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Detail Paket</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table shadow-sm table-striped table-bordered" id="tableBhp" width="100%" style="border-radius: 10px;">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th width="5%" style="border-radius: 10px 0px 0px 0px;">Hapus</th>
                                                        <th width="95%">Tindakan</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="bodyBhp">
                                                    <?php if (!empty($detail_tindakan)) : ?>
                                                        <?php
                                                        $nobhp = 1;
                                                        foreach ($detail_tindakan as $sbhp) :
                                                            $m_tindakan = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $sbhp->kode_tindakan]);
                                                        ?>
                                                            <tr id="rowBhp<?= htmlspecialchars($nobhp) ?>">
                                                                <td class="text-center">
                                                                    <button class="btn btn-sm btn-danger" type="button" id="btnHapus<?= htmlspecialchars($nobhp) ?>" onclick="hapusBarang('<?= htmlspecialchars($nobhp) ?>')">
                                                                        <i class="fa-solid fa-delete-left"></i>
                                                                    </button>
                                                                </td>
                                                                <td>
                                                                    <select name="kode_detail_tindakan[]" id="kode_detail_tindakan<?= htmlspecialchars($nobhp) ?>" class="form-control select2_tindakan_single_master" data-placeholder="~ Pilih Tindakan">
                                                                        <option value="<?= htmlspecialchars($sbhp->kode_tindakan) ?>"><?= htmlspecialchars($sbhp->kode_tindakan . ' | ' . $m_tindakan->keterangan) ?></option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                        <?php
                                                            $nobhp++;
                                                        endforeach;
                                                        ?>
                                                    <?php else : ?>
                                                        <tr id="rowBhp1">
                                                            <td class="text-center">
                                                                <button class="btn btn-sm btn-danger" type="button" id="btnHapus1" onclick="hapusBarang('1')"><i class="fa-solid fa-delete-left"></i></button>
                                                            </td>
                                                            <td>
                                                                <select name="kode_detail_tindakan[]" id="kode_detail_tindakan1" class="form-control select2_tindakan_single_master" data-placeholder="~ Pilih Barang"></select>
                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="hidden" class="form-control" id="jumBhp" value="<?= (!empty($detail_tindakan) ? count($detail_tindakan) : '1') ?>">
                                        <button type="button" class="btn btn-primary" onclick="tambah_bhp()"><i class="fa-solid fa-folder-plus"></i> Tambah Tindakan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-danger" onclick="getUrl('Master/tindakan_paket')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                            <button type="button" class="btn btn-success float-right ml-2" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                            <?php if (!empty($tindakan)) : ?>
                                <button type="button" class="btn btn-info float-right" onclick="getUrl('Master/form_tindakan_paket/0')" id="btnBaru"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
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

<div class="modal fade" id="modal_kategori" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" <?= $style_modal ?>>
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Kategori Tindakan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeModalKategori">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" id="form_kategori">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="inisial_kategori" class="control-label text-danger">Inisial</label>
                            <input type="text" class="form-control" id="inisial_kategori" name="inisial_kategori" placeholder="Inisial..." onkeyup="upperCase(this.value, 'inisial_kategori')" maxlength="3" max="3">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="keterangan_kategori" class="control-label text-danger">Keterangan</label>
                            <input type="text" class="form-control" id="keterangan_kategori" name="keterangan_kategori" placeholder="Keterangan..." onkeyup="ubah_nama(this.value, 'keterangan_kategori')">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="button" class="btn btn-success float-right" onclick="proses_kategori()"><i class="fa fa-server"></i> Proses</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const form_tindakan = $('#form_tindakan');
    const bodyBhp = $('#bodyBhp');
    const btnSimpan = $('#btnSimpan');
    var kodeTindakan = $('#kodeTindakan');
    var keterangan = $('#keterangan');
    var kategori = $('#kategori');
    var jenis_bayar = $('#jenis_bayar');
    var closeModalKategori = $('#closeModalKategori');
    var btnModal = $('#btnTambahModal');

    function reseting() {
        form_tindakan[0].reset();
        kodeTindakan.val('');
        keterangan.val('');
        kategori.val('').trigger('change');
        jenis_bayar.val('').trigger('change');
        btnSimpan.prop('disabled', false);
        $('#jumBhp').val(0);
        bodyBhp.empty();
        tambah_bhp();
    }

    function proses_kategori() {
        closeModalKategori.trigger('click')

        // Check if the keterangan_kategori is empty or null
        if ($('#inisial_kategori').val() === '' || $('#inisial_kategori').val() === null) {
            // Show alert and re-show the modal
            Swal.fire({
                title: "Inisial Kategori",
                text: "Form sudah diisi?",
                icon: "question"
            }).then((result) => {
                if (result.isConfirmed) {
                    btnModal.trigger('click');
                }
            });
            return;
        }

        if ($('#keterangan_kategori').val() === '' || $('#keterangan_kategori').val() === null) {
            // Show alert and re-show the modal
            Swal.fire({
                title: "Keterangan Kategori",
                text: "Form sudah diisi?",
                icon: "question"
            }).then((result) => {
                if (result.isConfirmed) {
                    btnModal.trigger('click');
                }
            });
            return;
        }

        if ($('#jenis_bayar').val() === '' || $('#jenis_bayar').val() === null) {
            // Show alert and re-show the modal
            Swal.fire({
                title: "Jenis Bayar",
                text: "Form sudah dipilih?",
                icon: "question"
            });
            return;
        }

        // AJAX request
        $.ajax({
            url: siteUrl + 'Master/add_kategori_tindakan',
            type: 'POST',
            dataType: 'JSON',
            data: $('#form_kategori').serialize(),
            success: function(result) {
                if (result.status == 1) {
                    Swal.fire({
                        title: "Kategori Tarif",
                        text: "Berhasil dibuat, silahkan dipilih!",
                        icon: "success"
                    }).then(() => {
                        $('#inisial_kategori').val('');
                        $('#keterangan_kategori').val('');

                        closeModalKategori.trigger('click')
                    });
                } else {
                    Swal.fire({
                        title: "Kategori Tarif",
                        text: "Gagal dibuat, silahkan coba lagi!",
                        icon: "info"
                    });
                }
            },
            error: function() {
                // Call the error processing function
                error_proccess();

                // Show the modal again if there's an error
                btnModal.trigger('click');
            }
        });
    }

    $(".select2_global").select2({
        placeholder: $(this).data('placeholder'),
        width: '100%',
        allowClear: true,
    });

    function tambah_bhp() {
        var jum = Number($('#jumBhp').val());
        var row = jum + 1;

        $('#jumBhp').val(row);
        bodyBhp.append(`<tr id="rowBhp${row}">
            <td class="text-center">
                <button class="btn btn-sm btn-danger" type="button" id="btnHapus${row}" onclick="hapusBarang('${row}')"><i class="fa-solid fa-delete-left"></i></button>
            </td>
            <td>
                <select name="kode_detail_tindakan[]" id="kode_detail_tindakan${row}" class="form-control select2_tindakan_single_master" data-placeholder="~ Pilih Tindakan"></select>
            </td>
        </tr>`);

        initailizeSelect2_tindakan_single_master();
    }

    // fungsi hapus baris card
    function hapusBarang(row) {
        $('#rowBhp' + row).remove();
    }

    function save() {
        btnSimpan.attr('disabled', true);

        if (kodeTindakan.val() == '' || kodeTindakan.val() == null) {
            var param = 1;
        } else {
            var param = 2;
        }

        if (keterangan.val() == '' || keterangan.val() == null) {
            btnSimpan.attr('disabled', false);

            Swal.fire("Nama Tindakan", "Form sudah diisi?", "question");

            return
        }

        if (kategori.val() == '' || kategori.val() == null) {
            btnSimpan.attr('disabled', false);

            Swal.fire("Kategori Tindakan", "Form sudah dipilih?", "question");

            return
        }

        proses(param);

    }

    function proses(param) {
        if (param == 1) {
            var message = 'buat';
        } else {
            var message = 'perbarui';
        }

        $.ajax({
            url: siteUrl + 'Master/tindakan_paket_proses/' + param,
            type: 'POST',
            dataType: 'JSON',
            data: form_tindakan.serialize(),
            success: function(result) {
                btnSimpan.attr('disabled', false);

                if (result.status == 1) {
                    Swal.fire("Tindakan Paket", "Berhasil di" + message + "!", "success").then(() => {
                        getUrl('Master/tindakan_paket');
                    });
                } else {
                    Swal.fire("Tindakan Paket", "Gagal di" + message + "!, silahkan dicoba lagi", "info");
                }
            },
            error: function(result) {
                btnSimpan.attr('disabled', false);
                error_proccess();
            }
        })
    }

    function showGuide() {
        // clean text
        $('#modal_mgLabel').text(``);
        $('#modal-isi').text(``);

        $('#modal_mg').modal('show'); // show modal

        // isi text
        $('#modal_mgLabel').append(`Manual Guide Master Logistik`);
        $('#modal-isi').append(`
            <ol>
                <li style="font-weight: bold;">Tambah Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Tambah</li>
                        <li>Selanjutnya isikan Form yang tersedia<br>Tanda (<span style="color: red;">**</span>) mengartikan wajib terisi</li>
                        <li>Isi Tab Jasa dan BHP (disesuaikan)</li>
                        <li>Klik tombol Proses</li>
                    </ul>
                </p>
                <li style="font-weight: bold;">Ubah Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Ubah pada list data yang ingin di ubah</li>
                        <li>Ubah isi Form yang akan di ubah<br>Tanda (<span style="color: red;">**</span>) mengartikan wajib terisi</li>
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