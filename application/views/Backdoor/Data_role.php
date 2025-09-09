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

<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-primary" <?= $style ?>>
            <div class="card-header">
                <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Daftar Role</span>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6 col-12">
                        <button type="button" class="btn btn-danger" onclick="getUrl('Backdoor')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="float-right">
                            <button type="button" class="btn btn-primary" onclick="getUrl('Backdoor/user_role')"><i class="fa-solid fa-rotate-right"></i>&nbsp;&nbsp;Refresh</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table shadow-sm table-hover table-bordered" width="100%" style="border-radius: 10px;" id="myTables">
                                <thead>
                                    <tr class="text-center">
                                        <th style="width: 5%; border-radius: 10px 0px 0px 0px;">No</th>
                                        <th>Keterangan</th>
                                        <th style="width: 10%;">Tambah</th>
                                        <th style="width: 10%;">Ubah</th>
                                        <th style="width: 10%;">Hapus</th>
                                        <th style="width: 10%;">Konfirmasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    foreach ($role as $r) : ?>
                                        <tr>
                                            <td class="text-right"><?= $no ?></td>
                                            <td><?= $r->keterangan ?></td>
                                            <td class="text-center">
                                                <input type="checkbox" name="tambah_role[]" id="tambah_role<?= $r->id ?>_<?= $no ?>" class="form-control" <?= ($r->created == 1) ? 'checked' : '' ?> onclick="tambah_aksi('<?= $r->keterangan ?>','<?= $r->id ?>', '<?= $no ?>')">
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" name="ubah_role[]" id="ubah_role<?= $r->id ?>_<?= $no ?>" class="form-control" <?= ($r->updated == 1) ? 'checked' : '' ?> onclick="ubah_aksi('<?= $r->keterangan ?>','<?= $r->id ?>', '<?= $no ?>')">
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" name="hapus_role[]" id="hapus_role<?= $r->id ?>_<?= $no ?>" class="form-control" <?= ($r->deleted == 1) ? 'checked' : '' ?> onclick="hapus_aksi('<?= $r->keterangan ?>','<?= $r->id ?>', '<?= $no ?>')">
                                            </td>
                                            <td class="text-center">
                                                <input type="checkbox" name="konfirmasi_role[]" id="konfirmasi_role<?= $r->id ?>_<?= $no ?>" class="form-control" <?= ($r->confirmed == 1) ? 'checked' : '' ?> onclick="konfirmasi_aksi('<?= $r->keterangan ?>','<?= $r->id ?>', '<?= $no ?>')">
                                            </td>
                                        </tr>
                                    <?php $no++;
                                    endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function tambah_aksi(keterangan, kode, no) {
        if (document.getElementById('tambah_role' + kode + '_' + no).checked == true) {
            var message = 'ditambahkan'
        } else {
            var message = 'dihapus'
        }

        Swal.fire({
            title: "Kamu yakin?",
            html: "Role " + keterangan + " akan " + message + " aksi <b style='color: red'>Tambah</b>!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, atur!",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin

                // jalankan fungsi
                $.ajax({
                    url: siteUrl + 'Backdoor/setRole/1/' + kode,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik
                        if (result.status == 1) { // jika mendapatkan hasil 1

                            Swal.fire("Role", "Berhasil diupdate!", "success").then(() => {
                                reloadTable();
                            });
                        } else { // selain itu

                            Swal.fire("Role", "Gagal diupdate!, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error
                        error_proccess();
                    }
                });
            } else if (result.dismiss == 'cancel') {
                if (document.getElementById('tambah_role' + kode + '_' + no).checked == false) {
                    document.getElementById('tambah_role' + kode + '_' + no).checked = true
                } else {
                    document.getElementById('tambah_role' + kode + '_' + no).checked = false
                }
            } else {
                if (document.getElementById('tambah_role' + kode + '_' + no).checked == false) {
                    document.getElementById('tambah_role' + kode + '_' + no).checked = true
                } else {
                    document.getElementById('tambah_role' + kode + '_' + no).checked = false
                }
            }
        });
    }

    function ubah_aksi(keterangan, kode, no) {
        if (document.getElementById('ubah_role' + kode + '_' + no).checked == true) {
            var message = 'ditambahkan'
        } else {
            var message = 'dihapus'
        }

        Swal.fire({
            title: "Kamu yakin?",
            html: "Role " + keterangan + " akan " + message + " aksi <b style='color: red'>Ubah</b>!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, atur!",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin

                // jalankan fungsi
                $.ajax({
                    url: siteUrl + 'Backdoor/setRole/2/' + kode,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik
                        if (result.status == 1) { // jika mendapatkan hasil 1

                            Swal.fire("Role", "Berhasil diupdate!", "success").then(() => {
                                reloadTable();
                            });
                        } else { // selain itu

                            Swal.fire("Role", "Gagal diupdate!, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error
                        error_proccess();
                    }
                });
            } else if (result.dismiss == 'cancel') {
                if (document.getElementById('ubah_role' + kode + '_' + no).checked == false) {
                    document.getElementById('ubah_role' + kode + '_' + no).checked = true
                } else {
                    document.getElementById('ubah_role' + kode + '_' + no).checked = false
                }
            } else {
                if (document.getElementById('ubah_role' + kode + '_' + no).checked == false) {
                    document.getElementById('ubah_role' + kode + '_' + no).checked = true
                } else {
                    document.getElementById('ubah_role' + kode + '_' + no).checked = false
                }
            }
        });
    }

    function hapus_aksi(keterangan, kode, no) {
        if (document.getElementById('hapus_role' + kode + '_' + no).checked == true) {
            var message = 'ditambahkan'
        } else {
            var message = 'dihapus'
        }

        Swal.fire({
            title: "Kamu yakin?",
            html: "Role " + keterangan + " akan " + message + " aksi <b style='color: red'>Hapus</b>!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, atur!",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin

                // jalankan fungsi
                $.ajax({
                    url: siteUrl + 'Backdoor/setRole/3/' + kode,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik
                        if (result.status == 1) { // jika mendapatkan hasil 1

                            Swal.fire("Role", "Berhasil diupdate!", "success").then(() => {
                                reloadTable();
                            });
                        } else { // selain itu

                            Swal.fire("Role", "Gagal diupdate!, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error
                        error_proccess();
                    }
                });
            } else if (result.dismiss == 'cancel') {
                if (document.getElementById('hapus_role' + kode + '_' + no).checked == false) {
                    document.getElementById('hapus_role' + kode + '_' + no).checked = true
                } else {
                    document.getElementById('hapus_role' + kode + '_' + no).checked = false
                }
            } else {
                if (document.getElementById('hapus_role' + kode + '_' + no).checked == false) {
                    document.getElementById('hapus_role' + kode + '_' + no).checked = true
                } else {
                    document.getElementById('hapus_role' + kode + '_' + no).checked = false
                }
            }
        });
    }

    function konfirmasi_aksi(keterangan, kode, no) {
        if (document.getElementById('konfirmasi_role' + kode + '_' + no).checked == true) {
            var message = 'ditambahkan'
        } else {
            var message = 'dihapus'
        }

        Swal.fire({
            title: "Kamu yakin?",
            html: "Role " + keterangan + " akan " + message + " aksi <b style='color: red'>Konfirmasi</b>!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, atur!",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin

                // jalankan fungsi
                $.ajax({
                    url: siteUrl + 'Backdoor/setRole/4/' + kode,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik
                        if (result.status == 1) { // jika mendapatkan hasil 1

                            Swal.fire("Role", "Berhasil diupdate!", "success").then(() => {
                                reloadTable();
                            });
                        } else { // selain itu

                            Swal.fire("Role", "Gagal diupdate!, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error
                        error_proccess();
                    }
                });
            } else if (result.dismiss == 'cancel') {
                if (document.getElementById('konfirmasi_role' + kode + '_' + no).checked == false) {
                    document.getElementById('konfirmasi_role' + kode + '_' + no).checked = true
                } else {
                    document.getElementById('konfirmasi_role' + kode + '_' + no).checked = false
                }
            } else {
                if (document.getElementById('konfirmasi_role' + kode + '_' + no).checked == false) {
                    document.getElementById('konfirmasi_role' + kode + '_' + no).checked = true
                } else {
                    document.getElementById('konfirmasi_role' + kode + '_' + no).checked = false
                }
            }
        });
    }

    $('#myTables').DataTable({
        "destroy": true,
        "processing": false,
        "responsive": true,
        "serverSide": false,
        "scrollCollapse": false,
        "paging": true,
        "ajax": false,
        "oLanguage": {
            "sEmptyTable": "<div class='text-center'>Data Kosong</div>",
            "sInfoEmpty": "",
            "sInfoFiltered": "",
            "sSearch": "",
            "sSearchPlaceholder": "Cari data...",
            "sInfo": " Jumlah _TOTAL_ Data (_START_ - _END_)",
            "sLengthMenu": "_MENU_ Baris",
            "sZeroRecords": "<div class='text-center'>Data Kosong</div>",
            "oPaginate": {
                "sPrevious": "Sebelumnya",
                "sNext": "Berikutnya"
            }
        },
        "aLengthMenu": [
            [10, 30, 50, -1],
            [10, 30, 50, "Semua"]
        ],
        "columnDefs": [{
            "targets": [-1],
            "orderable": false,
        }, ],
    });
</script>