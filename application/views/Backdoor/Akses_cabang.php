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
echo _lock_so();
?>

<form method="post" id="form_barang_in">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Daftar Cabang</span>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <button type="button" class="btn btn-danger" onclick="getUrl('Backdoor')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="float-right">
                                <button type="button" class="btn btn-primary" onclick="reloadTable()"><i class="fa-solid fa-rotate-right"></i>&nbsp;&nbsp;Refresh</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table shadow-sm table-hover table-bordered" id="tableAksesCabang" width="100%" style="border-radius: 10px;">
                                    <thead>
                                        <tr class="text-center">
                                            <th rowspan="2" width="5%" style="border-radius: 10px 0px 0px 0px;">#</th>
                                            <th rowspan="2" width="25%">User</th>
                                            <th colspan="<?= count($cabang) ?>" width="70%">Akses</th>
                                        </tr>
                                        <tr class="text-center">
                                            <?php foreach ($cabang as $c) : ?>
                                                <th><?= $c->inisial_cabang ?></td>
                                                <?php endforeach ?>
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
    var table = $('#tableAksesCabang');

    // change role
    function changeAkses(email, kcabang, no, nor, ncabang, cabang, email) {
        // console.log(email + ' - ' + kcabang + ' - ' + no + ' - ' + nor + ' - ' + ncabang + ' - ' + cabang + ' - ' + email);
        Swal.fire({
            title: "Kamu yakin?",
            html: "User <b>" + email + "</b> untuk akses <b style='color: red;'>" + cabang + "</>!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, ubah!",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin

                // jalankan fungsi
                $.ajax({
                    url: siteUrl + 'Backdoor/changeCabang/?email=' + email + '&kcabang=' + kcabang,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik

                        if (result.status == 1) { // jika mendapatkan hasil 1
                            Swal.fire("User " + ncabang, "Berhasil diubah aksesnya!", "success").then(() => {
                                reloadTable();
                            });
                        } else { // selain itu

                            Swal.fire("User " + ncabang, "Gagal diubah aksesnya!, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error

                        error_proccess();
                    }
                });
            } else if (result.dismiss == 'cancel') {
                document.getElementById('krole' + no + '_' + nor).checked = false
            } else {
                document.getElementById('krole' + no + '_' + nor).checked = false
            }
        });
    }
</script>