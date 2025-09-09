<?php
$created    = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->created;
if ($web->ct_theme == 1) {
    $style = 'style="background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);"';
    $style2 = 'style="backdrop-filter: blur(10px);"';
    $style3 = 'style="background: transparent;"';
    $style_modal = 'style="background-color: rgba(255, 255, 255, 0.4); -webkit-backdrop-filter: blur(4px); backdrop-filter: blur(4px);"';
} else if ($web->ct_theme == 2) {
    $style = 'style="background: rgba(30, 30, 30, 0.8); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); color: white !important;"';
    $style2 = 'style="backdrop-filter: blur(10px);"';
    $style3 = 'style="background: transparent;"';
    $style_modal = 'style="background-color: rgba(30, 30, 30, 0.9); -webkit-backdrop-filter: blur(30px); backdrop-filter: blur(5px); color: white !important;"';
} else {
    $style = 'style="background-color: white;"';
    $style2 = '';
    $style3 = '';
    $style_modal = '';
}
?>

<div class="row">
    <div class="col-lg-6 col-6">
        <div class="small-box" <?= $style ?>>
            <div class="inner">
                <h3>Rp. <span id="hutang-counter">0</span></h3>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const targetValue = '<?= (!empty($hutang) && !empty($hutang->hutang)) ? $hutang->hutang : 0 ?>';
                        const duration = 2000; // Animation duration in milliseconds
                        const counterElement = document.getElementById('hutang-counter');
                        let startValue = 0;
                        const increment = targetValue / (duration / 10);

                        const counterInterval = setInterval(() => {
                            startValue += increment;
                            if (startValue >= targetValue) {
                                startValue = targetValue;
                                clearInterval(counterInterval);
                            }
                            counterElement.textContent = new Intl.NumberFormat().format(Math.floor(startValue));
                        }, 10);
                    });
                </script>
                <p>Hutang Belum Dibayar</p>
            </div>
            <div class="icon">
                <i class="fa-solid fa-scale-unbalanced"></i>
            </div>
            <a type="button" class="small-box-footer text-left p-3">Jumlah: <?= number_format(!empty($hutang_num) ? $hutang_num : 0); ?></a>
        </div>
    </div>
    <div class="col-lg-6 col-6">
        <div class="small-box" <?= $style ?>>
            <div class="inner">
                <h3>Rp. <span id="piutang-counter">0</span></h3>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const targetValue = <?= (!empty($piutang) && !empty($piutang->piutang)) ? $piutang->piutang : 0 ?>;
                        const duration = 2000; // Animation duration in milliseconds
                        const counterElement = document.getElementById('piutang-counter');
                        let startValue = 0;
                        const increment = targetValue / (duration / 10);

                        const counterInterval = setInterval(() => {
                            startValue += increment;
                            if (startValue >= targetValue) {
                                startValue = targetValue;
                                clearInterval(counterInterval);
                            }
                            counterElement.textContent = new Intl.NumberFormat().format(Math.floor(startValue));
                        }, 10);
                    });
                </script>
                <p>Piutang Belum Dibayar</p>
            </div>
            <div class="icon">
                <i class="fa-solid fa-scale-unbalanced-flip"></i>
            </div>
            <a type="button" class="small-box-footer text-left p-3">Jumlah: <?= number_format(!empty($piutang_num) ? $piutang_num : 0); ?></a>
        </div>
    </div>
</div>

<form method="post" id="form_piutang">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Daftar Hutang & Piutang</span>
                    <div class="float-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-circle-down"></i>&nbsp;&nbsp;Unduh
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="preview('barang_in')"><i class="fa-solid fa-fw fa-tv"></i>&nbsp;&nbsp;Preview</a></li>
                                <li><a class="dropdown-item" href="#" onclick="print('barang_in')"><i class="fa-regular fa-fw fa-file-pdf"></i>&nbsp;&nbsp;Pdf</a></li>
                                <li><a class="dropdown-item" href="#" onclick="excel('barang_in')"><i class="fa-regular fa-fw fa-file-excel"></i>&nbsp;&nbsp;Excel</a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="reloadTable()"><i class="fa-solid fa-rotate-right"></i>&nbsp;&nbsp;Refresh</button>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-5 col-5 mb-3">
                            <input type="date" name="dari" id="dari" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-5 col-5 mb-3">
                            <input type="date" name="sampai" id="sampai" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-2 col-2 mb-3">
                            <button type="button" style="width: 100%;" class="btn btn-info" onclick="filter($('#kode_gudang').val())"><i class="fa-solid fa-sort"></i>&nbsp;&nbsp;Filter</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table shadow-sm table-hover table-bordered" id="tablePiuttang" width="100%" style="border-radius: 10px;">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="5%" style="border-radius: 10px 0px 0px 0px;">#</th>
                                            <th width="15%">Invoice</th>
                                            <th width="15%">Tgl/Jam Bayar</th>
                                            <th width="15%">Pemasok</th>
                                            <th width="10%">Jenis</th>
                                            <th width="15%">Total</th>
                                            <th width="10%">Status</th>
                                            <th width="5%" style="border-radius: 0px 10px 0px 0px;">Aksi</th>
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
    var table = $('#tablePiuttang');

    function bayarin(inv, reff, jn) {
        Swal.fire({
            title: "Kamu yakin?",
            html: "Bayar Piutang <b>#" + inv + "</b><br>Dari referensi <b style='color: red;'>" + reff + "</>!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, bayar!",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin

                // jalankan fungsi
                $.ajax({
                    url: siteUrl + 'Accounting/piutang_bayar/?inv=' + inv + '&jn=' + jn,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik

                        if (result.status == 1) { // jika mendapatkan hasil 1
                            Swal.fire("Piutang " + reff, "Berhasil dibayarkan!", "success").then(() => {
                                location.reload();
                            });
                        } else { // selain itu

                            Swal.fire("Piutang " + reff, "Gagal dibayarkan!, silahkan dicoba kembali", "info");
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