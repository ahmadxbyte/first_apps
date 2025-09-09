<style>
    table {
        border-collapse: separate;
        border: solid #dee2e6 0.5px;
        border-radius: 6px;
    }

    td,
    th {
        border-left: solid #dee2e6 0.5px;
        border-top: solid #dee2e6 0.5px;
        padding: 15px;
    }

    th {
        border-top: none;
        padding: 15px;
    }

    td:first-child,
    th:first-child {
        border-left: none;
        padding: 15px;
    }
</style>

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

<form method="post" id="form_sampah">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Daftar Sampah</span>
                    <div class="float-right">
                        <button type="button" class="btn btn-primary" onclick="getUrl('Sampah')"><i class="fa-solid fa-rotate-right"></i>&nbsp;&nbsp;Refresh</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <span class="text-danger font-weight-bold">Limit Hapus Sampah Permanen: <?= number_format($web->limit_trash_web) ?> Hari setelah penghapusan</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="" id="tableSampah" width="100%">
                                    <thead>
                                        <tr class="text-center">
                                            <th style="width: 5%;"><input type="checkbox" class="form-control" name="check_all" id="check_all" onclick="sel_all()"></th>
                                            <th style="width: 5%;">#</th>
                                            <th>Menu</th>
                                            <th>Keterangan</th>
                                            <th>Waktu</th>
                                            <th>Id/Invoice</th>
                                            <th style="width: 10%;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1;
                                        foreach ($query_master as $qm) : ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="check_one[]" id="check_one<?= $no ?>" class="form-control" onclick="sel_one('<?= $no ?>', '<?= $qm->id ?>')">
                                                    <input type="hidden" name="check_onex[]" id="check_onex<?= $no ?>" value="0" class="form-control">
                                                    <input type="hidden" name="menu[]" id="menu<?= $no ?>" value="<?= $qm->menu ?>" class="form-control">
                                                    <input type="hidden" name="invoice[]" id="invoice<?= $no ?>" value="<?= $qm->id ?>" class="form-control">
                                                    <input type="hidden" name="tabel[]" id="tabel<?= $no ?>" value="<?= $qm->tabel ?>" class="form-control">
                                                </td>
                                                <td class="text-center"><?= $no ?></td>
                                                <td><?= $qm->menu ?></td>
                                                <td><?= $qm->nama ?></td>
                                                <td><?= $qm->tgl . ' ~ ' . $qm->jam ?></td>
                                                <td><?= $qm->id ?></td>
                                                <td class="text-center">
                                                    <button type="button" title="Pulihkan" class="btn btn-success mb-1" onclick="pulihkan_one('<?= $qm->id ?>', '<?= $qm->nama ?>', '<?= $qm->tabel ?>')"><i class="fa fa-trash-restore-alt"></i></button>
                                                    <button type="button" title="Hapus" class="btn btn-dark mb-1" onclick="hapus_one('<?= $qm->id ?>', '<?= $qm->nama ?>', '<?= $qm->tabel ?>')"><i class="fa fa-trash-alt"></i></button>
                                                </td>
                                            </tr>
                                        <?php $no++;
                                        endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div>
                                <button type="button" class="btn btn-success" onclick="sel_pulihkan()"><i class="fa fa-trash-restore-alt"></i> Pulihkan Semua</button>
                                <button type="button" class="btn btn-dark" onclick="sel_hapus()"><i class="fa fa-trash-alt"></i> Hapus Semua</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="h6 float-right">Jumlah Sampah: <span style="color: <?= (count($query_master) < 501) ? 'green' : ((count($query_master) < 1000) ? 'yellow' : 'red') ?>; font-weight: bold;"><?= count($query_master) ?></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function hapus_one(params, keterangan, tabel) {
        const form = $('#form_sampah')

        Swal.fire({
            title: "Kamu yakin?",
            html: "Data <b style='color:red;'>" + keterangan + "</b> akan hapus!",
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
                    url: siteUrl + 'Sampah/deleted_one/' + params + '/' + tabel,
                    data: form.serialize(),
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik
                        if (result.status == 1) { // jika mendapatkan hasil 1

                            Swal.fire("Data Sampah", "Berhasil di hapus!", "success").then(() => {
                                getUrl('Sampah');
                            });
                        } else { // selain itu

                            Swal.fire("Data Sampah", "Gagal di hapus!, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error

                        error_proccess();
                    }
                });
            }
        });
    }

    function pulihkan_one(params, keterangan, tabel) {
        const form = $('#form_sampah')

        Swal.fire({
            title: "Kamu yakin?",
            html: "Data <b style='color:red;'>" + keterangan + "</b> akan dipulihkan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, pulihkan!",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin

                // jalankan fungsi
                $.ajax({
                    url: siteUrl + 'Sampah/restore_one/' + params + '/' + tabel,
                    data: form.serialize(),
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik
                        if (result.status == 1) { // jika mendapatkan hasil 1

                            Swal.fire("Data Sampah", "Berhasil di pulihkan!", "success").then(() => {
                                getUrl('Sampah');
                            });
                        } else { // selain itu

                            Swal.fire("Data Sampah", "Gagal di pulihkan!, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error

                        error_proccess();
                    }
                });
            }
        });
    }

    function sel_hapus() {
        const form = $('#form_sampah')
        var tableBarangIn = document.getElementById('tableSampah'); // ambil id table detail
        var rowCount = tableBarangIn.rows.length; // hitung jumlah rownya
        var no = 0;

        for (var i = 1; i <= rowCount; i++) {
            if ($('#check_onex' + i).val() == 1) {
                no += 1;
            }
        }

        Swal.fire({
            title: "Kamu yakin?",
            html: "<b style='color: red'>" + no + "</b> Data akan dihapus!",
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
                    url: siteUrl + 'Sampah/deleted',
                    data: form.serialize(),
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik
                        if (result.status == 1) { // jika mendapatkan hasil 1

                            Swal.fire("Data Sampah", "Berhasil di hapus!", "success").then(() => {
                                getUrl('Sampah');
                            });
                        } else { // selain itu

                            Swal.fire("Data Sampah", "Gagal di hapus!, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error

                        error_proccess();
                    }
                });
            }
        });
    }

    function sel_pulihkan() {
        const form = $('#form_sampah')
        var tableBarangIn = document.getElementById('tableSampah'); // ambil id table detail
        var rowCount = tableBarangIn.rows.length; // hitung jumlah rownya
        var no = 0;

        for (var i = 1; i <= rowCount; i++) {
            if ($('#check_onex' + i).val() == 1) {
                no += 1;
            }
        }

        Swal.fire({
            title: "Kamu yakin?",
            html: "<b style='color: red'>" + no + "</b> Data akan dipulihkan!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, pulihkan!",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin

                // jalankan fungsi
                $.ajax({
                    url: siteUrl + 'Sampah/restore',
                    data: form.serialize(),
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik
                        if (result.status == 1) { // jika mendapatkan hasil 1

                            Swal.fire("Data Sampah", "Berhasil di pulihkan!", "success").then(() => {
                                getUrl('Sampah');
                            });
                        } else { // selain itu

                            Swal.fire("Data Sampah", "Gagal di pulihkan!, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error

                        error_proccess();
                    }
                });
            }
        });
    }

    function sel_one(param1, param2) {
        document.getElementById('check_all').checked = false
        if (document.getElementById('check_one' + param1).checked == true) {
            $('#check_onex' + param1).val(1)
        } else {
            $('#check_onex' + param1).val(0)
        }
    }

    function sel_all() {
        var no = 1;
        var isChecked = document.getElementById('check_all').checked;

        var queryMaster = JSON.parse('<?= json_encode($query_master) ?>');

        $.each(queryMaster, function(index, value) {
            var checkBoxId = 'check_one' + no;
            var hiddenInputId = '#check_onex' + no;

            document.getElementById(checkBoxId).checked = isChecked ? true : false;
            $(hiddenInputId).val(isChecked ? 1 : 0);

            no++;
        });
    }
</script>