<?php
$tindakan_master    = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $param]);
$kunjungan_master   = $this->db->query("SELECT kunjungan FROM paket_kunjungan WHERE kode_multiprice = '$param1' ORDER BY kunjungan DESC LIMIT 1")->row();
if ($kunjungan_master) {
    $kunjungan = $kunjungan_master->kunjungan + 1;
} else {
    $kunjungan = 1;
}

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

<form method="post" id="form_paket_kunjungan">
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
                                        <label for="kode_paket" class="control-label">ID</label>
                                        <input type="text" name="kode_paket" id="kode_paket" value="" class="form-control" placeholder="Otomatis" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="kode_multiprice" class="control-label">Tindakan</label>
                                        <input type="hidden" name="kode_tindakan" id="kode_tindakan" class="form-control" value="<?= $param ?>">
                                        <input type="hidden" name="kode_multiprice" id="kode_multiprice" class="form-control" value="<?= $param1 ?>">
                                        <input type="text" name="kode_tindakanx" id="kode_tindakanx" class="form-control" value="<?= $param1 . ' - [Nama: ' . $tindakan_master->keterangan . ' | Poli: ' . $m_poli . ' | Penjamin: ' . $m_penjamin . ' | Kelas: ' . $m_kelas . ']' ?>" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="kunjungan" class="control-label">Kunjungan</label>
                                        <input type="text" class="form-control text-right" id="kunjungan" name="kunjungan" placeholder="Kunjungan" readonly value="<?= $kunjungan ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="klinik" class="control-label">Klinik</label>
                                        <input type="text" class="form-control text-right" id="klinik" name="klinik" placeholder="Jasa Dokter" value="0" required onkeyup="formatRp(this.value, 'klinik'); hitungTotal()">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="dokter" class="control-label">Dokter</label>
                                        <input type="text" class="form-control text-right" id="dokter" name="dokter" placeholder="Jasa Dokter" value="0" required onkeyup="formatRp(this.value, 'dokter'); hitungTotal()">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="pelayanan" class="control-label">Pelayanan</label>
                                        <input type="text" class="form-control text-right" id="pelayanan" name="pelayanan" placeholder="Jasa Pelayanan" value="0" required onkeyup="formatRp(this.value, 'pelayanan'); hitungTotal()">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="poli" class="control-label">Poli</label>
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
                        <button type="button" class="btn btn-danger" onclick="getUrl('Master/paket_kunjungan')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                        <button type="button" class="btn btn-info" onclick="reseting()" id="btnReset"><i class="fa-solid fa-arrows-rotate"></i>&nbsp;&nbsp;Reset</button>
                        <button type="button" class="btn btn-success" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Daftar Kunjungan</span>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table shadow-sm table-hover table-bordered" id="tablePaket" width="100%" style="border-radius: 10px;">
                                    <thead>
                                        <tr class="text-center">
                                            <th rowspan="2" style="border-radius: 10px 0px 0px 0px; width: 40px;">#</th>
                                            <th rowspan="2" style="width: 180px;">ID</th>
                                            <th colspan="4" style="width: 400px;">Jasa</th>
                                            <th rowspan="2" style="width: 100px;">Total</th>
                                            <th rowspan="2" style="width: 100px;">Kunjungan</th>
                                            <th rowspan="2" style="border-radius: 0px 10px 0px 0px; width: 60px;">Aksi</th>
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
    var table = $('#tablePaket');

    const form = $('#form_paket_kunjungan');
    var kode_paket = $('#kode_paket');
    var kode_tindakan = $('#kode_tindakan');
    var kode_multiprice = $('#kode_multiprice');
    var kunjungan = $('#kunjungan');
    var klinik = $('#klinik');
    var dokter = $('#dokter');
    var pelayanan = $('#pelayanan');
    var poli = $('#poli');
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
        location.href = siteUrl + 'Master/form_tindakan_paket_kunjungan/<?= $param1 ?>'
    }

    function save() {
        btnSimpan.attr('disabled', true);

        if (kode_paket.val() == '' || kode_paket.val() == null) { // jika kode_paket null/ kosong
            var param = 1;
        } else {
            var param = 2;
        }

        if (param == 1) {
            $.ajax({
                url: siteUrl + "Master/cekPaketKunjungan",
                type: "POST",
                data: form.serialize(),
                dataType: "JSON",
                success: function(data) {
                    if (data.status == 1) {
                        btnSimpan.attr('disabled', false);

                        Swal.fire("Paket Kunjungan", "Sudah ada!, silahkan isi keterangan lain ", "info");
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
            url: siteUrl + "Master/prosesPaketKunjungan/" + param,
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) {
                if (result.status == 1) { // jika mendapatkan respon 1
                    btnSimpan.attr('disabled', false);

                    Swal.fire("Paket Tindakan", "Berhasil " + message, "success").then(() => {
                        location.href = siteUrl + 'Master/form_tindakan_paket_kunjungan/<?= $param1 ?>'
                    });
                } else { // selain itu
                    btnSimpan.attr('disabled', false);

                    Swal.fire("Paket Tindakan", "Gagal " + message + ", silahkan dicoba kembali", "info");
                }
            },
            error: function(xhr, status, error) {
                btnSimpan.attr('disabled', false);

                error_proccess();
            }
        });

        btnSimpan.attr('disabled', false);
    }

    // fungsi hapus berdasarkan kode_supplier
    function hapusPaket(kdpaket) {
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
                    url: siteUrl + 'Master/delPaketKunjungan?kode_paket=' + kdpaket,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik

                        if (result.status == 1) { // jika mendapatkan hasil 1

                            Swal.fire("Paket Tindakan", "Berhasil di hapus!", "success").then(() => {
                                reloadTable();
                            });
                        } else { // selain itu

                            Swal.fire("Paket Tindakan", "Gagal di hapus!, silahkan dicoba kembali", "info");
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