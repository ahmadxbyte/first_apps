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

$pembayaran = $this->M_global->getDataResult('pembayaran', [
    'kode_user'         => $this->session->userdata('kode_user'),
    'tgl_pembayaran'    => date('Y-m-d'),
    'kode_cabang'       => $this->session->userdata('kode_cabang')
]);

// Get already closed payments
$closed_payments = $this->db->select('token_pembayaran')
    ->from('closing_detail')
    ->get()
    ->result();

// Filter out closed payments
$pembayaran = array_filter($pembayaran, function ($payment) use ($closed_payments) {
    foreach ($closed_payments as $closed) {
        if ($payment->token_pembayaran === $closed->token_pembayaran) {
            return false;
        }
    }
    return true;
});
?>

<div class="row">
    <div class="col-md-12">
        <form method="post" id="form_closing">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Form Closingan Kasir</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="user_closing" class="control-label">User Closing</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" title="Shift" id="basic-addon1"><?= $this->session->userdata('shift') ?></span>
                                                </div>
                                                <input type="text" class="form-control text-center bg-primary" name="user_closing" id="user_closing" value="<?= $this->session->userdata('kode_user') ?>" readonly title="Kode User">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control text-center" name="email_closing" id="email_closing" value="<?= $this->session->userdata('nama') ?>" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="" class="control-label">Waktu Closing</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text font-weight-bold" title="Status" id="basic-addon1"><?= ($pembayaran) ? '<span class="text-success">CLOSE</span>' : '<span class="text-primary">OPEN</span>' ?></span>
                                                </div>
                                                <input type="text" name="jam_closing" id="jam_closing" class="form-control text-center bg-danger" readonly title="Jam">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="tgl_closing" id="tgl_closing" class="form-control text-center" value="<?= date('Y-m-d') ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="" class="control-label">Detail Pendapatan</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table shadow-sm table-hover table-bordered" id="tablePendaftaran" width="100%" style="border-radius: 10px; overflow: hidden;">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th width="50%" style="border-top-left-radius: 10px;">CASH</th>
                                                            <th width="50%" style="border-top-right-radius: 10px;">CARD</th>
                                                        </tr>
                                                    </thead>
                                                    <?php
                                                    $cash = 0;
                                                    $carding = 0;
                                                    if ($pembayaran) {
                                                        foreach ($pembayaran as $p) {
                                                            $cash += $p->cash;
                                                            $carding += $p->card;
                                                        }
                                                    } else {
                                                        $cash = 0;
                                                        $carding = 0;
                                                    }
                                                    ?>
                                                    <tbody>
                                                        <tr>
                                                            <td style="border-bottom-left-radius: 10px;">Rp. <span class="float-right"><?= number_format($cash) ?></span></td>
                                                            <td style="border-bottom-right-radius: 10px;">Rp. <span class="float-right"><?= number_format($carding) ?></span></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <input type="hidden" name="tunai" id="tunai" value="<?= $cash ?>">
                                                <input type="hidden" name="nontunai" id="nontunai" value="<?= $carding ?>">
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
                        <button type="button" class="btn btn-success" <?= ($pembayaran) ? 'disabled' : 'onclick="proses()"' ?>><i class="fa fa-solid fa-server"></i> <?= ($pembayaran) ? 'SUDAH CLOSE' : 'PROSES' ?></button>
                    </div>
                </div>
            </div>
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Daftar Closingan Kasir</span>
                    <div class="float-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-circle-down"></i>&nbsp;&nbsp;Unduh
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="preview('promo')"><i class="fa-solid fa-fw fa-tv"></i>&nbsp;&nbsp;Preview</a></li>
                                <li><a class="dropdown-item" href="#" onclick="print('promo')"><i class="fa-regular fa-fw fa-file-pdf"></i>&nbsp;&nbsp;Pdf</a></li>
                                <li><a class="dropdown-item" href="#" onclick="excel('promo')"><i class="fa-regular fa-fw fa-file-excel"></i>&nbsp;&nbsp;Excel</a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="reloadTable()"><i class="fa-solid fa-rotate-right"></i>&nbsp;&nbsp;Refresh</button>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-5 col-5">
                            <input type="date" name="dari" id="dari" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-5 col-5">
                            <input type="date" name="sampai" id="sampai" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-2 col-2">
                            <button type="button" style="width: 100%;" class="btn btn-info" onclick="filter('')"><i class="fa-solid fa-sort"></i>&nbsp;&nbsp;Filter</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table shadow-sm table-hover table-bordered" id="tableClosing" width="100%" style="border-radius: 10px;">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="5%" style="border-radius: 10px 0px 0px 0px;">#</th>
                                            <th width="20%">No Closing</th>
                                            <th width="20%">Waktu</th>
                                            <th width="20%">Nama</th>
                                            <th width="15%">Tunai</th>
                                            <th width="15%">Non-tunai</th>
                                            <th width="5%">Cetak</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    var table = $('#tableClosing');
    display_ct2();

    function display_ct2() {
        var x = new Date();

        // Mendapatkan jam, menit, dan detik
        var hours = x.getHours();
        var minutes = x.getMinutes();
        var seconds = x.getSeconds();

        // Menampilkan waktu pada elemen dengan id 'time'
        document.getElementById('jam_closing').value = hours + ":" + minutes + ":" + seconds;
        setTimeout(display_ct2, 1000); // Memperbarui setiap detik
    }

    function proses() {
        Swal.fire({
            title: "Kamu yakin?",
            text: "Ingin Closing Kasir Sekarang!",
            icon: "info",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, closing!",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin

                // jalankan fungsi
                $.ajax({
                    url: '<?= site_url() ?>Marketing/closing_proses/',
                    type: 'POST',
                    dataType: 'JSON',
                    data: $('#form_closing').serialize(),
                    success: function(result) { // jika fungsi berjalan dengan baik
                        if (result.status == 1) { // jika mendapatkan hasil 1
                            Swal.fire("Closing Kasir", "Berhasil di proses!", "success").then(() => {
                                location.href = '<?= site_url() ?>Marketing/closing_kasir'
                            });
                        } else { // selain itu
                            Swal.fire("Closing Kasir", "Gagal di proses!, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error
                        error_proccess();
                    }
                });
            }
        });
    }
</script>