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
?>

<div class="row">
    <div class="col-lg-6 col-6">
        <div class="small-box" style="background: rgb(200, 35, 51, 1); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);">
            <div class="inner">
                <h3><span id="kasir_count">0</span></h3>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const targetValue = <?= count($this->db->query("SELECT * FROM (
                            SELECT
                                p.id,
                                p.no_trx AS invoice,
                                'pembayaran' AS url,
                                p.tgl_daftar AS tgl,
                                p.jam_daftar AS jam,
                                p.kode_member
                            FROM pendaftaran p
                            JOIN emr_dok ed USING (no_trx)
                            LEFT JOIN tarif_paket_pasien t USING (no_trx)
                            LEFT JOIN barang_out_header bh USING (no_trx)
                            WHERE p.kode_cabang = '" . $this->session->userdata('cabang') . "' AND p.status_trx = 0 AND p.kode_member <> 'U00001' AND no_trx NOT IN (SELECT no_trx FROM pembayaran) AND (ed.eracikan <> '' OR ed.no_trx IN (SELECT no_trx FROM emr_per_barang)) AND ed.no_trx IN (SELECT no_trx FROM barang_out_header)

                            UNION ALL

                            SELECT
                                p.id,
                                p.no_trx AS invoice,
                                'pembayaran2' AS url,
                                p.tgl_daftar AS tgl,
                                p.jam_daftar AS jam,
                                p.kode_member
                            FROM pendaftaran p
                            JOIN emr_dok ed USING (no_trx)
                            LEFT JOIN tarif_paket_pasien t USING (no_trx)
                            LEFT JOIN barang_out_header bh USING (no_trx)
                            WHERE p.kode_cabang = '" . $this->session->userdata('cabang') . "' AND p.status_trx = 0 AND p.kode_member <> 'U00001' AND no_trx NOT IN (SELECT no_trx FROM pembayaran) AND (ed.no_trx NOT IN (SELECT no_trx FROM emr_per_barang)) AND ed.no_trx NOT IN (SELECT no_trx FROM barang_out_header) AND ed.eracikan = ''

                            UNION ALL

                            SELECT
                                id,
                                invoice AS invoice,
                                'kasir' AS url,
                                tgl_jual AS tgl,
                                jam_jual AS jam,
                                kode_member AS kode_member
                            FROM barang_out_header
                            WHERE kode_cabang = '" . $this->session->userdata('cabang') . "' AND status_jual = 0 AND no_trx IS NULL

                            UNION ALL

                            SELECT
                                id,
                                token_pembayaran AS invoice,
                                'kasir' AS url,
                                tgl_pembayaran AS tgl,
                                jam_pembayaran AS jam,
                                kode_member AS kode_member
                            FROM pembayaran
                            WHERE kode_cabang = '" . $this->session->userdata('cabang') . "' AND approved = 0
                        ) AS semuax
                        WHERE tgl = '" . date('Y-m-d') . "'
                        ORDER BY id")->result()) ?>;
                        const duration = 2000; // Animation duration in milliseconds
                        const counterElement = document.getElementById('kasir_count');
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
                <p>Belum Diproses</p>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-user-times"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-6">
        <div class="small-box" style="background: rgb(23, 162, 184, 1); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);">
            <div class="inner">
                <h3><span id="emr_nurse">0</span></h3>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const targetValue = <?= count($this->db->query("SELECT * FROM pembayaran WHERE kode_cabang = '" . $this->session->userdata('cabang') . "' AND tgl_pembayaran = '" . date('Y-m-d') . "' AND approved = 1")->result()) ?>;
                        const duration = 2000; // Animation duration in milliseconds
                        const counterElement = document.getElementById('emr_nurse');
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
                <p>Sudah Diproses</p>
            </div>
            <div class="icon">
                <i class="fa fa-fw fa-user-check"></i>
            </div>
        </div>
    </div>
</div>

<form method="post" id="form_kasir">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Daftar Pembayaran</span>
                    <div class="float-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-circle-down"></i>&nbsp;&nbsp;Unduh
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="preview('kasir')"><i class="fa-solid fa-fw fa-tv"></i>&nbsp;&nbsp;Preview</a></li>
                                <li><a class="dropdown-item" href="#" onclick="print('kasir')"><i class="fa-regular fa-fw fa-file-pdf"></i>&nbsp;&nbsp;Pdf</a></li>
                                <li><a class="dropdown-item" href="#" onclick="excel('kasir')"><i class="fa-regular fa-fw fa-file-excel"></i>&nbsp;&nbsp;Excel</a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="reloadTable()"><i class="fa-solid fa-rotate-right"></i>&nbsp;&nbsp;Refresh</button>
                        <?php if ($created == 1) : ?>
                            <button type="button" class="btn btn-success" onclick="getUrl('Kasir/form_kasir/0')">
                                <i class="fa-solid fa-receipt"></i>&nbsp;&nbsp;Pembayaran
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12 col-12">
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
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="tablePembayaran" width="100%" style="border-radius: 10px;">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="5%" style="border-radius: 10px 0px 0px 0px;">#</th>
                                            <th width="15%">Tgl/Jam Pembayaran</th>
                                            <th width="15%">Invoice</th>
                                            <th width="15%">No. Transaksi</th>
                                            <th width="15%">Jenis Pembayaran</th>
                                            <th width="10%">Total</th>
                                            <th width="10%">Penerima</th>
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
    var table = $('#tablePembayaran');

    // fungsi aktif/non-aktif akun
    function actived(token_pembayaran, param) {
        if (param == 0) {
            var pesan = "Pembayaran ini akan di Acc!";
            var pesan2 = "di Acc!";
        } else {
            var pesan = "Pembayaran ini akan dibatalkan!";
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
                    url: siteUrl + 'Kasir/actived_pembayaran/' + token_pembayaran + '/' + param,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik

                        if (result.status == 1) { // jika mendapatkan hasil 1
                            Swal.fire("Pembayaran", "Berhasil " + pesan2, "success").then(() => {
                                reloadTable();
                            });
                        } else { // selain itu

                            Swal.fire("Pembayaran", "Gagal " + pesan2 + ", silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error

                        error_proccess();
                    }
                });
            }
        });
    }

    // fungsi hapus berdasarkan invoice
    function hapus(token_pembayaran) {
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
                    url: siteUrl + 'Kasir/delPembayaran/' + token_pembayaran,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik

                        if (result.status == 1) { // jika mendapatkan hasil 1
                            Swal.fire("Pembayaran", "Berhasil di hapus!", "success").then(() => {
                                reloadTable();
                            });
                        } else { // selain itu

                            Swal.fire("Pembayaran", "Gagal di hapus!, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error

                        error_proccess();
                    }
                });
            }
        });
    }

    //fungsi ubah berdasarkan lemparan kode
    function ubah(token_pembayaran) {
        // jalankan fungsi
        getUrl('Kasir/form_kasir/' + token_pembayaran);
    }

    // fungsi cetak
    function cetak(x, y) {
        printsingle('Kasir/print_kwitansi/' + x + '/' + y);
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
                    const githubUrl = `${siteUrl}Kasir/email/${x}?email=${email}`;
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
                    Swal.fire("Kwitansi", "Berhasil dikirim via Email!, silahkan cek email", "success");
                } else {
                    Swal.fire("Kwitansi", "Gagal dikirim via Email!, silahkan cek email", "info");
                }
            }
        });
    }

    function showGuide() {
        // clean text
        $('#modal_mgLabel').text(``);
        $('#modal-isi').text(``);

        $('#modal_mg').modal('show'); // show modal

        // isi text
        $('#modal_mgLabel').append(`Manual Guide Pembayaran Kasir`);
        $('#modal-isi').append(`
        <ol>
            <li style="font-weight: bold;">Proses Pembayaran</li>
            <p>
                <ul>
                    <li>Klik tombol Pembayaran</li>
                    <li>Pilih Pendaftaran atau Penjualan<br>(Jika hanya beli obat cukup pilih yang Penjualan)</li>
                    <li>(Optional) - Tambahkan tindakan jika ada tambahan tindakan</li>
                    <li><span style='color: red;'>Teks berawarna merah</span> mengartikan wajib terisi</li>
                    <li>Pilih cara bayar</li>
                    <li>Klik tombol Proses</li>
                </ul>
            </p>
            <li style="font-weight: bold;">Ubah Data</li>
            <p>
                <ul>
                    <li>Klik tombol Ubah pada list data yang ingin di ubah</li>
                    <li>Ubah isi Form yang ingin diubah</li>
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