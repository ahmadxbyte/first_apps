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

<div class="row mb-3">
    <div class="col-lg-6 col-6">
        <div class="small-box" <?= $style ?>>
            <div class="inner">
                <h3>Rp. <?= number_format(isset($saldo_utama) ? $saldo_utama : 0) ?></h3>
                <p>Saldo Kas/Bank Utama</p>
            </div>
            <div class="icon">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <a type="button" href="#" class="small-box-footer">&nbsp;</a>
        </div>
    </div>
    <div class="col-lg-6 col-6">
        <div class="small-box" <?= $style ?>>
            <div class="inner">
                <h3>Rp. <?= number_format(isset($saldo_second) ? $saldo_second : 0) ?></h3>
                <p>Saldo Kas/Bank Second</p>
            </div>
            <div class="icon">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <a type="button" href="#" class="small-box-footer">&nbsp;</a>
        </div>
    </div>
</div>

<form method="post" id="form_piutang">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Daftar Mutasi Kas & Bank</span>
                    <div class="float-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-circle-down"></i>&nbsp;&nbsp;Unduh
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="preview('mutasi_kas')"><i class="fa-solid fa-fw fa-tv"></i>&nbsp;&nbsp;Preview</a></li>
                                <li><a class="dropdown-item" href="#" onclick="print('mutasi_kas')"><i class="fa-regular fa-fw fa-file-pdf"></i>&nbsp;&nbsp;Pdf</a></li>
                                <li><a class="dropdown-item" href="#" onclick="excel('mutasi_kas')"><i class="fa-regular fa-fw fa-file-excel"></i>&nbsp;&nbsp;Excel</a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="reloadTable()"><i class="fa-solid fa-rotate-right"></i>&nbsp;&nbsp;Refresh</button>
                        <?php if ($created == 1) : ?>
                            <button type="button" class="btn btn-success" onclick="getUrl('Accounting/form_mutasi_kas/0')"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
                        <?php endif; ?>
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
                                <table class="table shadow-sm table-hover table-bordered" id="tableMutasi" width="100%" style="border-radius: 10px;">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="5%" style="border-radius: 10px 0px 0px 0px;">#</th>
                                            <th width="15%">Invoice</th>
                                            <th width="15%">Tgl/Jam Mutasi</th>
                                            <th width="15%">Dari Kas</th>
                                            <th width="10%">Menuju Kas</th>
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
    var table = $('#tableMutasi')

    function ubah(param) {
        getUrl('Accounting/form_mutasi_kas/' + param)
    }

    // fungsi hapus berdasarkan invoice
    function hapus(param) {
        // ajukan pertanyaaan
        Swal.fire({
            title: "Kamu yakin?",
            text: "Data yang dihapus tidak bisa dikembalikan!",
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
                    url: siteUrl + 'Accounting/delMutasiKas/' + param,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik

                        if (result.status == 1) { // jika mendapatkan hasil 1
                            Swal.fire("Mutasi Kas & Bank", "Berhasil di hapus!", "success").then(() => {
                                getUrl('Accounting/mutasi_kas');
                            });
                        } else { // selain itu

                            Swal.fire("Mutasi Kas & Bank", "Gagal di hapus!, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error

                        error_proccess();
                    }
                });
            }
        });
    }

    // fungsi acc/unacc
    function valided(invoice, param) {
        if (param == 0) {
            var pesan = "Mutasi Kas & Bank ini akan di re-acc!";
            var pesan2 = "di re-acc!";
        } else {
            var pesan = "Mutasi Kas & Bank ini akan diacc!";
            var pesan2 = "diacc!";
        }
        // ajukan pertanyaaan
        Swal.fire({
            title: "Kamu yakin?",
            text: pesan,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, " + pesan2,
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin

                // jalankan fungsi
                $.ajax({
                    url: siteUrl + 'Accounting/acc_mutasi/' + invoice + '/' + param,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik

                        if (result.status == 1) { // jika mendapatkan hasil 1
                            Swal.fire("Mutasi Kas & Bank", "Berhasil " + pesan2, "success").then(() => {
                                getUrl('Accounting/mutasi_kas');
                            });
                        } else { // selain itu

                            Swal.fire("Mutasi Kas & Bank", "Gagal " + pesan2 + ", silahkan dicoba kembali", "info");
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