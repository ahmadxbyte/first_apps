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
                <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Daftar Databases</span>
            </div>
            <div class="card-footer">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <button type="button" class="btn btn-danger" onclick="getUrl('Backdoor')"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                    </div>
                    <div class="col-md-6">
                        <div class="float-right">
                            <button class="btn btn-primary" onclick="backup()"><i class="fa-solid fa-cloud-arrow-down"></i> Backup Database</button>
                        </div>
                        <!-- masih error saat upload table m_menu -->
                        <!-- <div class="float-right">
                            <form id="form_db" method="post">
                                <div class="input-group mb-3">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="myfile" name="myfile">
                                        <label class="custom-file-label" for="inputGroupFile02" aria-describedby="inputGroupFileAddon02">Cari File SQL</label>
                                    </div>
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="inputGroupFileAddon02" type="button" onclick="restore_db()">Upload</span>
                                    </div>
                                </div>
                            </form>
                        </div> -->
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
                                        <th>Nama Database</th>
                                        <th>Tgl Backup</th>
                                        <th style="width: 15%; border-radius: 0px 10px 0px 0px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    foreach ($backup_db as $bdb) : ?>
                                        <tr>
                                            <td class="text-right"><?= $no ?></td>
                                            <td><?= $bdb->nama ?></td>
                                            <td><?= date('d/m/Y ~ H:i:s', strtotime($bdb->tgl_backup)) ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-info" type="button" onclick="download_db('<?= $bdb->nama; ?>')"><i class="fa-solid fa-download"></i></button>
                                                <button class="btn btn-danger" type="button" onclick="delete_db('<?= $bdb->id; ?>')"><i class="fa-solid fa-trash-can-arrow-up"></i></button>
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
            [5, 15, 20, -1],
            [5, 15, 20, "Semua"]
        ],
        "columnDefs": [{
            "targets": [-1],
            "orderable": false,
        }, ],
    });

    function backup() {
        $.ajax({
            url: siteUrl + 'Backdoor/backup_db',
            type: 'POST',
            dataType: 'JSON',
            success: function(result) { // jika fungsi berjalan dengan baik

                if (result.status == 1) { // jika mendapatkan hasil 1
                    Swal.fire("Database", "Berhasil dibackup", "success").then(() => {
                        getUrl('Backdoor/data_db');
                    });
                } else { // selain itu

                    Swal.fire("Database", "Gagal dibackup" + ", silahkan dicoba kembali", "info");
                }
            },
            error: function(result) { // jika fungsi error

                error_proccess();
            }
        })
    }

    function download_db(nama) {
        Swal.fire({
            title: "Kamu yakin?",
            text: "Download Database " + nama + "!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, download!",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin

                // jalankan fungsi
                location.href = siteUrl + "/database/" + nama;
            }
        });
    }

    function delete_db(id) {
        $.ajax({
            url: siteUrl + 'Backdoor/del_db/' + id,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) { // jika fungsi berjalan dengan baik

                if (result.status == 1) { // jika mendapatkan hasil 1
                    Swal.fire("Database", "Berhasil dihapus", "success").then(() => {
                        getUrl('Backdoor/data_db');
                    });
                } else { // selain itu

                    Swal.fire("Database", "Gagal dihapus" + ", silahkan dicoba kembali", "info");
                }
            },
            error: function(result) { // jika fungsi error

                error_proccess();
            }
        })
    }

    function restore_db() {
        var form = $('#form_db')[0];
        var data = new FormData(form);

        $.ajax({
            url: siteUrl + 'Backdoor/restore_db/',
            type: "POST",
            enctype: 'multipart/form-data',
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function(result) { // jika fungsi berjalan dengan baik

                if (result.status == 1) { // jika mendapatkan respon 1

                    Swal.fire("Restore Database", "Berhasil dilakukan", "success").then(() => {
                        getUrl('Backdoor/data_db');
                    });
                } else { // selain itu

                    Swal.fire("Restore Database", "Gagal dilakukan" + ", silahkan dicoba kembali", "info");
                }
            },
            error: function(result) { // jika fungsi error

                error_proccess();
            }
        });
    }
</script>