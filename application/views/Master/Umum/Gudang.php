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

<form method="post" id="form_gudang">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Daftar Gudang</span>
                    <div class="float-right">
                        <button type="button" class="btn btn-info" onclick="send_data_mail('Master Gudang')"><i class="fa-solid fa-paper-plane"></i>&nbsp;&nbsp;Kirim Email</button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-circle-down"></i>&nbsp;&nbsp;Unduh
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="preview('gudang')"><i class="fa-solid fa-fw fa-tv"></i>&nbsp;&nbsp;Preview</a></li>
                                <li><a class="dropdown-item" href="#" onclick="print('gudang')"><i class="fa-regular fa-fw fa-file-pdf"></i>&nbsp;&nbsp;Pdf</a></li>
                                <li><a class="dropdown-item" href="#" onclick="excel('gudang')"><i class="fa-regular fa-fw fa-file-excel"></i>&nbsp;&nbsp;Excel</a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="reloadTable()"><i class="fa-solid fa-rotate-right"></i>&nbsp;&nbsp;Refresh</button>
                        <?php if ($created == 1) : ?>
                            <button type="button" class="btn btn-success" onclick="getUrl('Master/form_gudang/0')"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer">
                    <select name="bagian" id="bagian" onchange="getBagian(this.value)" data-placeholder="~ Pilih Bagian" class="form-control select2_global">
                        <option value="">~ Pilih Bagian</option>
                        <option value="Internal">Internal</option>
                        <option value="Logistik">Logistik</option>
                    </select>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table shadow-sm table-hover table-bordered" id="tableGudang" width="100%" style="border-radius: 10px;">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="5%" style="border-radius: 10px 0px 0px 0px;">#</th>
                                            <th width="10%">ID</th>
                                            <th>Nama</th>
                                            <th>Bagian</th>
                                            <th>Status</th>
                                            <th>Keterangan</th>
                                            <th>Utama</th>
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
    // variable
    var table = $('#tableGudang');

    // fungsi ubah bagian
    function getBagian(bagian) {
        table.DataTable().ajax.url(siteUrl + 'Master/gudang_list/' + bagian).load();
    }

    //fungsi ubah berdasarkan lemparan kode
    function ubah(kode_gudang) {
        // jalankan fungsi
        getUrl('Master/form_gudang/' + kode_gudang);
    }

    // fungsi hapus berdasarkan kode_gudang
    function hapus(kode_gudang) {
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
                    url: siteUrl + 'Master/delGud/' + kode_gudang,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik

                        if (result.status == 1) { // jika mendapatkan hasil 1
                            Swal.fire("Gudang", "Berhasil di hapus!", "success").then(() => {
                                reloadTable();
                            });
                        } else { // selain itu

                            Swal.fire("Gudang", "Gagal di hapus!, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error

                        error_proccess();
                    }
                });
            }
        });
    }

    // fungsi set_default berdasarkan kode_gudang
    function set_default(kode_gudang, no) {
        // ajukan pertanyaaan
        Swal.fire({
            title: "Kamu yakin?",
            text: "Data ini akan dijadikan Gudang default!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, atur default!",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin

                // jalankan fungsi
                $.ajax({
                    url: siteUrl + 'Master/setDefGudang/' + kode_gudang,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik
                        if (result.status == 1) { // jika mendapatkan hasil 1

                            Swal.fire("Gudang", "Berhasil diupdate!", "success").then(() => {
                                reloadTable();
                            });
                        } else { // selain itu

                            Swal.fire("Gudang", "Gagal diupdate!, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error
                        error_proccess();
                    }
                });
            } else if (result.dismiss == 'cancel') {
                document.getElementById('default_ppn' + no).checked = false
            } else {
                document.getElementById('default_ppn' + no).checked = false
            }
        });
    }

    function showGuide() {
        // clean text
        $('#modal_mgLabel').text(``);
        $('#modal-isi').text(``);

        $('#modal_mg').modal('show'); // show modal

        // isi text
        $('#modal_mgLabel').append(`Manual Guide Master Gudang`);
        $('#modal-isi').append(`
            <ol>
                <li style="font-weight: bold;">Atur Default</li>
                <p>
                    <ul>
                        <li>Ceklis pada kolom utama yang akan di jadikan Default</li>
                        <li>Saat Muncul Pop up, klik "Ya, Atur Default"</li>
                    </ul>
                </p>
                <li style="font-weight: bold;">Tambah Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Tambah</li>
                        <li>Selanjutnya isikan Form yang tersedia<br><span style='color: red;'>Teks berawarna merah</span> mengartikan wajib terisi</li>
                        <li>Klik tombol Proses</li>
                    </ul>
                </p>
                <li style="font-weight: bold;">Ubah Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Ubah pada list data yang ingin di ubah</li>
                        <li>Ubah isi Form yang akan di ubah<br><span style='color: red;'>Teks berawarna merah</span> mengartikan wajib terisi</li>
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