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

<form method="post" id="form_multiprice">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Formulir</span>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="kode_multiprice" class="control-label">ID <span class="text-danger">**</span></label>
                                        <input type="text" name="kode_multiprice" id="kode_multiprice" value="" class="form-control" placeholder="Otomatis" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="kode_tindakan" class="control-label">Tindakan <span class="text-danger">**</span></label>
                                        <select name="kode_tindakan" id="kode_tindakan" class="form-control select2_tindakan" data-placeholder="~ Pilih Tindakan"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="kode_poli">Poli <sup class="text-danger">**</sup></label>
                                        <select name="kode_poli" id="kode_poli" class="form-control select2_poli" data-placeholder="~ Pilih Poli"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="penjamin">Penjamin <sup class="text-danger">**</sup></label>
                                        <select name="penjamin" id="penjamin" class="form-control select2_jenis_bayar" data-placeholder="~ Pilih Penjamin"></select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="kelas">Kelas <sup class="text-danger">**</sup></label>
                                        <select name="kelas" id="kelas" class="form-control select2_kelas" data-placeholder="~ Pilih Kelas"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="klinik" class="control-label">Klinik <span class="text-danger">**</span></label>
                                        <input type="text" class="form-control text-right" id="klinik" name="klinik" placeholder="Jasa Klinik" value="0" required onkeyup="formatRp(this.value, 'klinik'); hitungTotal()">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="dokter" class="control-label">Dokter <span class="text-danger">**</span></label>
                                        <input type="text" class="form-control text-right" id="dokter" name="dokter" placeholder="Jasa Dokter" value="0" required onkeyup="formatRp(this.value, 'dokter'); hitungTotal()">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="pelayanan" class="control-label">Pelayanan <span class="text-danger">**</span></label>
                                        <input type="text" class="form-control text-right" id="pelayanan" name="pelayanan" placeholder="Jasa Pelayanan" value="0" required onkeyup="formatRp(this.value, 'pelayanan'); hitungTotal()">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="poli" class="control-label">Poli <span class="text-danger">**</span></label>
                                        <input type="text" class="form-control text-right" id="poli" name="poli" placeholder="Jasa Poli" value="0" required onkeyup="formatRp(this.value, 'poli'); hitungTotal()">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="font-weight-bold text-primary">Total Tindakan: Rp. <span id="total_harga">0</span></span>
                    <div class="float-right">
                        <button type="button" class="btn btn-info" onclick="reseting()" id="btnReset"><i class="fa-solid fa-arrows-rotate"></i>&nbsp;&nbsp;Reset</button>
                        <?php if ($created == 1) : ?>
                            <button type="button" class="btn btn-success" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Jasa Tindakan</span>
                    <div class="float-right">
                        <button type="button" class="btn btn-info" onclick="send_data_mail('Master Multiprice')"><i class="fa-solid fa-paper-plane"></i>&nbsp;&nbsp;Kirim Email</button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-circle-down"></i>&nbsp;&nbsp;Unduh
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="preview('multiprice')"><i class="fa-solid fa-fw fa-tv"></i>&nbsp;&nbsp;Preview</a></li>
                                <li><a class="dropdown-item" href="#" onclick="print('multiprice')"><i class="fa-regular fa-fw fa-file-pdf"></i>&nbsp;&nbsp;Pdf</a></li>
                                <li><a class="dropdown-item" href="#" onclick="excel('multiprice')"><i class="fa-regular fa-fw fa-file-excel"></i>&nbsp;&nbsp;Excel</a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="reloadTable()"><i class="fa-solid fa-rotate-right"></i>&nbsp;&nbsp;Refresh</button>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-4">
                            <select name="polix" id="polix" class="form-control select2_poli" data-placeholder="~ Pilih Poli" onchange="filtering()"></select>
                        </div>
                        <div class="col-md-4">
                            <select name="penjaminx" id="penjaminx" class="form-control select2_jenis_bayar" data-placeholder="~ Pilih Penjamin" onchange="filtering()"></select>
                        </div>
                        <div class="col-md-4">
                            <select name="kelasx" id="kelasx" class="form-control select2_kelas" data-placeholder="~ Pilih Kelas" onchange="filtering()"></select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table shadow-sm table-hover table-bordered" id="tableMultiprice" width="100%" style="border-radius: 10px;">
                                    <thead>
                                        <tr class="text-center">
                                            <th rowspan="2" style="border-radius: 10px 0px 0px 0px; width: 40px;">#</th>
                                            <th rowspan="2" style="width: 80px;">ID</th>
                                            <th rowspan="2" style="width: 180px;">Keterangan</th>
                                            <th rowspan="2" style="width: 120px;">Poli</th>
                                            <th rowspan="2" style="width: 120px;">Penjamin</th>
                                            <th rowspan="2" style="width: 100px;">Kelas</th>
                                            <th colspan="4" style="width: 400px;">Jasa</th>
                                            <th rowspan="2" style="width: 120px;">Total</th>
                                            <th rowspan="2" style="border-radius: 0px 10px 0px 0px; width: 120px;">Aksi</th>
                                        </tr>
                                        <tr class="text-center">
                                            <th style="width: 100px;">Klinik</th>
                                            <th style="width: 100px;">Dokter</th>
                                            <th style="width: 100px;">Pelayanan</th>
                                            <th style="width: 100px;">Poli</th>
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
    var table = $('#tableMultiprice');

    function filtering() {
        var polix = $('#polix').val();
        var penjaminx = $('#penjaminx').val();
        var kelasx = $('#kelasx').val();

        if (polix == '' || polix == null) {
            polix = '';
        } else {
            polix = polix;
        }

        if (penjaminx == '' || penjaminx == null) {
            penjaminx = '';
        } else {
            penjaminx = penjaminx;
        }

        if (kelasx == '' || kelasx == null) {
            kelasx = '';
        } else {
            kelasx = kelasx;
        }

        var param = penjaminx + '/' + kelasx + '/' + polix;

        table.DataTable().ajax.url(siteUrl + 'Master/price_list/' + param).load();
    }

    const form = $('#form_multiprice');
    var kode_multiprice = $('#kode_multiprice');
    var kode_tindakan = $('#kode_tindakan');
    var penjamin = $('#penjamin');
    var kode_poli = $('#kode_poli');
    var kelas = $('#kelas');
    var klinik = $('#klinik');
    var dokter = $('#dokter');
    var pelayanan = $('#pelayanan');
    var poli = $('#poli');
    var penjaminx = $('#penjaminx');
    var kelasx = $('#kelasx');
    var btnSimpan = $('#btnSimpan');
    var btnReset = $('#btnReset');
    var total_harga = $('#total_harga');

    function hitungTotal() {
        var klinik = ($('#klinik').val()).replaceAll(',', '');
        var dokter = ($('#dokter').val()).replaceAll(',', '');
        var pelayanan = ($('#pelayanan').val()).replaceAll(',', '');
        var poli = ($('#poli').val()).replaceAll(',', '');

        var total = Number(klinik) + Number(dokter) + Number(pelayanan) + Number(poli);

        total_harga.text(formatRpNoId(total));
    }

    function reseting() {
        form[0].reset();
        kode_multiprice.val('');
        kode_tindakan.val('').trigger('change');
        penjamin.val('').trigger('change');
        $('#kode_poli').val('').trigger('change');
        $('#kelas').val('').trigger('change');
        klinik.val(0);
        dokter.val(0);
        pelayanan.val(0);
        $('#poli').val(0);
        total_harga.text(0);
    }

    function save() {
        btnSimpan.attr('disabled', true);

        if (kode_multiprice.val() == '' || kode_multiprice.val() == null) { // jika kode_multiprice null/ kosong
            var param = 1;
        } else {
            var param = 2;
        }

        if (kode_tindakan.val() == '' || kode_tindakan.val() == null) { // jika kode_tindakan null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Tindakan", "Form sudah dipilih?", "question");
        }

        if ($('#kode_poli').val() == '' || $('#kode_poli').val() == null) { // jika kode_poli null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Poli", "Form sudah dipilih?", "question");
        }

        if (penjamin.val() == '' || penjamin.val() == null) { // jika penjamin null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Penjamin", "Form sudah dipilih?", "question");
        }

        if ($('#kelas').val() == '' || $('#kelas').val() == null) { // jika kelas null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Kelas", "Form sudah dipilih?", "question");
        }

        if (param == 1) {
            $.ajax({
                url: siteUrl + "Master/cekMultiprice",
                type: "POST",
                data: form.serialize(),
                dataType: "JSON",
                success: function(data) {
                    if (data.status == 1) {
                        btnSimpan.attr('disabled', false);

                        Swal.fire("Multiprice", "Sudah ada!, silahkan isi keterangan lain ", "info");
                    } else {
                        proses(param);
                    }
                }
            });
        } else {
            proses(param);
        }

    }

    function proses(param) {
        if (param == 1) { // jika param 1 berarti insert/tambah
            var message = 'dibuat!';
        } else { // selain itu berarti update/ubah
            var message = 'diperbarui!';
        }

        $.ajax({
            url: siteUrl + "Master/prosesMultiprice/" + param,
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) {
                if (result.status == 1) { // jika mendapatkan respon 1
                    btnSimpan.attr('disabled', false);
                    reloadTable();
                    reseting();

                    Swal.fire("Jasa Tindakan", "Berhasil " + message, "success").then(() => {});
                } else { // selain itu
                    btnSimpan.attr('disabled', false);

                    Swal.fire("Jasa Tindakan", "Gagal " + message + ", silahkan dicoba kembali", "info");
                }
            },
            error: function(xhr, status, error) {
                btnSimpan.attr('disabled', false);

                error_proccess();
            }
        });

        btnSimpan.attr('disabled', false);
    }

    function ubah(kdmultiprice) {
        reseting();

        $.ajax({
            url: siteUrl + 'Master/getMultiprice/' + kdmultiprice,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if (result.status == 0) { // jika mendapatkan hasil 1
                    btnSimpan.attr('disabled', false);

                    Swal.fire("Multiprice", "Gagal diambil!, silahkan dicoba kembali", "info");
                } else { // selain itu
                    kode_multiprice.val(result.kode_multiprice);
                    kode_tindakan.html('<option value="' + result.kode_tindakan + '">' + result.tindakan + '</option>');
                    penjamin.html('<option value="' + result.kode_penjamin + '">' + result.penjamin + '</option>');
                    $('#kode_poli').html('<option value="' + result.kode_poli + '">' + result.poli + '</option>');
                    $('#kelas').html('<option value="' + result.kelas + '">' + result.kelas + '</option>');
                    klinik.val(formatRpNoId(result.klinik));
                    dokter.val(formatRpNoId(result.dokter));
                    pelayanan.val(formatRpNoId(result.pelayanan));
                    $('#poli').val(formatRpNoId(result.poli));

                    hitungTotal();

                    btnSimpan.attr('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                btnSimpan.attr('disabled', false);

                error_proccess();
            }
        });
    }

    // fungsi hapus berdasarkan kode_supplier
    function hapus(kdmultiprice) {
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
                    url: siteUrl + 'Master/delMultiprice/' + kdmultiprice,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik

                        if (result.status == 1) { // jika mendapatkan hasil 1

                            Swal.fire("Jasa Tindakan", "Berhasil di hapus!", "success").then(() => {
                                reloadTable();
                            });
                        } else { // selain itu

                            Swal.fire("Jasa Tindakan", "Gagal di hapus!, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error

                        error_proccess();
                    }
                });
            }
        });
    }

    function showGuide() {
        // clean text
        $('#modal_mgLabel').text(``);
        $('#modal-isi').text(``);

        $('#modal_mg').modal('show'); // show modal

        // isi text
        $('#modal_mgLabel').append(`Manual Guide Master Tarif Single`);
        $('#modal-isi').append(`
            <ol>
                <li style="font-weight: bold;">Tambah Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Tambah</li>
                        <li>Selanjutnya isikan Form yang tersedia<br><span style='color: red;'>Teks berawarna merah</span> mengartikan wajib terisi mengartikan wajib terisi</li>
                        <li>Isi Tab Jasa dan BHP (disesuaikan)</li>
                        <li>Klik tombol Proses</li>
                    </ul>
                </p>
                <li style="font-weight: bold;">Ubah Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Ubah pada list data yang ingin di ubah</li>
                        <li>Ubah isi Form yang akan di ubah<br><span style='color: red;'>Teks berawarna merah</span> mengartikan wajib terisi mengartikan wajib terisi</li>
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