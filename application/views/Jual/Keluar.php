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

<form method="post" id="form_barang_out">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Daftar Order EMR Dokter & Penjualan</span>
                    <div class="float-right">
                        <button type="button" class="btn btn-primary" onclick="reloadTable3()"><i class="fa-solid fa-rotate-right"></i>&nbsp;&nbsp;Refresh</button>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <span class="h4"><i class="fa-solid fa-bookmark text-primary"></i> Daftar Order EMR Dokter</span>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="row">
                                <div class="col-md-4 col-4 mb-3">
                                    <input type="date" name="dari2" id="dari2" class="form-control" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-4 col-4 mb-3">
                                    <input type="date" name="sampai2" id="sampai2" class="form-control" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-4 col-4 mb-3">
                                    <button type="button" style="width: 100%;" class="btn btn-info" onclick="filterEmr()"><i class="fa-solid fa-sort"></i>&nbsp;&nbsp;Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table shadow-sm table-hover table-bordered" id="tableEmr" width="100%" style="border-radius: 10px;">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="5%" style="border-radius: 10px 0px 0px 0px;">#</th>
                                            <th width="15%">Invoice</th>
                                            <th width="15%">Tgl/Jam Order</th>
                                            <th width="20%">Pembeli</th>
                                            <th width="15%">Dokter</th>
                                            <th width="15%">Perawat</th>
                                            <th width="15%" style="border-radius: 0px 10px 0px 0px;">Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="h4"><i class="fa-solid fa-bookmark text-primary"></i> Daftar Penjualan</span>
                    <div class="float-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-circle-down"></i>&nbsp;&nbsp;Unduh
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="preview('barang_out')"><i class="fa-solid fa-fw fa-tv"></i>&nbsp;&nbsp;Preview</a></li>
                                <li><a class="dropdown-item" href="#" onclick="print('barang_out')"><i class="fa-regular fa-fw fa-file-pdf"></i>&nbsp;&nbsp;Pdf</a></li>
                                <li><a class="dropdown-item" href="#" onclick="excel('barang_out')"><i class="fa-regular fa-fw fa-file-excel"></i>&nbsp;&nbsp;Excel</a></li>
                            </ul>
                        </div>
                        <?php if ($created == 1) : ?>
                            <button type="button" class="btn btn-success" onclick="getUrl('Transaksi/form_barang_out/0')" <?= _lock_button() ?>><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <select name="kode_gudang" id="kode_gudang" class="select2_gudang_int" data-placeholder="~ Pilih Gudang" onchange="getGudang(this.value)"></select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-4 col-4 mb-3">
                                    <input type="date" name="dari" id="dari" class="form-control" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-4 col-4 mb-3">
                                    <input type="date" name="sampai" id="sampai" class="form-control" value="<?= date('Y-m-d') ?>">
                                </div>
                                <div class="col-md-4 col-4 mb-3">
                                    <button type="button" style="width: 100%;" class="btn btn-info" onclick="filter($('#kode_gudang').val())"><i class="fa-solid fa-sort"></i>&nbsp;&nbsp;Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table shadow-sm table-hover table-bordered" id="tableBarangOut" width="100%" style="border-radius: 10px;">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="5%" style="border-radius: 10px 0px 0px 0px;">#</th>
                                            <th width="15%">Invoice</th>
                                            <th width="15%">Tgl/Jam Jual</th>
                                            <th width="10%">Pembeli</th>
                                            <th width="10%">Gudang</th>
                                            <th width="15%">Total</th>
                                            <th width="10%">User</th>
                                            <th width="15%" style="border-radius: 0px 10px 0px 0px;">Aksi</th>
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
    var tableEmr = $('#tableEmr');

    tableEmr.DataTable({
        "destroy": true,
        "processing": true,
        "responsive": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            "url": `<?= site_url() ?>Transaksi/emr_list/1`,
            "type": "POST",
        },
        "scrollCollapse": false,
        "paging": true,
        "language": {
            "emptyTable": "<div class='text-center'>Data Kosong</div>",
            "infoEmpty": "",
            "infoFiltered": "",
            "search": "",
            "searchPlaceholder": "Cari data...",
            "info": " Jumlah _TOTAL_ Data (_START_ - _END_)",
            "lengthMenu": "_MENU_ Baris",
            "zeroRecords": "<div class='text-center'>Data Kosong</div>",
            "paginate": {
                "previous": "Sebelumnya",
                "next": "Berikutnya"
            }
        },
        "lengthMenu": [
            [10, 25, 50, 75, 100, -1],
            [10, 25, 50, 75, 100, "Semua"]
        ],
        "columnDefs": [{
            "targets": [-1],
            "orderable": false,
        }],
    });

    function filterEmr() {
        var dari2 = $('#dari2').val();
        var sampai2 = $('#sampai2').val();

        var parameterString = `2~${dari2}~${sampai2}`;

        tableEmr.DataTable().ajax.url('<?= site_url() ?>Transaksi/emr_list/' + parameterString).load();
    }

    function reloadTable3() {
        reloadTable();
        reloadTable2();
    }

    function reloadTable2() {
        tableEmr.DataTable().ajax.reload(null, false);
    }
</script>

<script>
    // variable
    var table = $('#tableBarangOut');

    //fungsi ubah berdasarkan lemparan kode
    function ubah(invoice, notrx) {
        // jalankan fungsi
        getUrl('Transaksi/form_barang_out/' + invoice + '/' + notrx);
    }

    // fungsi hapus berdasarkan invoice
    function hapus(invoice) {
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
                    url: siteUrl + 'Transaksi/delBeliOut/' + invoice,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik

                        if (result.status == 1) { // jika mendapatkan hasil 1
                            reloadTable();
                            reloadTable2();

                            Swal.fire("Penjualan", "Berhasil di hapus!", "success");
                        } else { // selain itu

                            Swal.fire("Penjualan", "Gagal di hapus!, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error

                        error_proccess();
                    }
                });
            }
        });
    }

    // fungsi aktif/non-aktif akun
    function actived(invoice, param) {
        if (param == 0) {
            var pesan = "Penjualan ini akan di re-batalkan!";
            var pesan2 = "di re-batalkan!";
        } else {
            var pesan = "Penjualan ini akan dibatalkan!";
            var pesan2 = "dibatalkan!";
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
                    url: siteUrl + 'Transaksi/activedbarang_out/' + invoice + '/' + param,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik

                        if (result.status == 1) { // jika mendapatkan hasil 1
                            reloadTable();

                            Swal.fire("Penjualan", "Berhasil " + pesan2, "success");
                        } else { // selain itu

                            Swal.fire("Penjualan", "Gagal " + pesan2 + ", silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error

                        error_proccess();
                    }
                });
            }
        });
    }

    // fungsi group by gudang
    function getGudang(x) {
        filter(x);
    }

    // fungsi cetak
    function cetak(x, y) {
        printsingle('Transaksi/single_print_bout/' + x + '/' + y);
    }

    // fungsi kirim email
    function email(x) {
        Swal.fire({
            title: "Masukan Email",
            input: "text",
            inputAttributes: {
                autocapitalize: "off"
            },
            showCancelButton: true,
            confirmButtonText: "Kirim",
            cancelButtonText: "Tutup",
            showLoaderOnConfirm: true,
            preConfirm: async (email) => {
                try {
                    const githubUrl = `<?= site_url() ?>Transaksi/email_out/${x}?email=${email}`;
                    const response = await fetch(githubUrl);
                    if (!response.ok) {
                        return Swal.showValidationMessage(`${JSON.stringify(await response.json())}`);
                    }
                    return response.json();
                } catch (error) {
                    Swal.showValidationMessage(`Request failed: ${error}`);
                }
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                if (result.value.status == 1) {
                    Swal.fire("Invoice Penjualan", "Berhasil dikirim via Email!, silahkan cek email", "success");
                } else {
                    Swal.fire("Invoice Penjualan", "Gagal dikirim via Email!, silahkan coba lagi", "info");
                }
            }
        });
    }
</script>