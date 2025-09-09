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

<form method="post" id="form_promo">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Formulir</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="id" class="control-label">ID <span class="text-danger">**</span></label>
                                                <input type="text" class="form-control" id="kodePromo" name="kodePromo" placeholder="Otomatis" readonly value="<?= (!empty($promo) ? $promo->kode_promo : '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="nama">Nama <span class="text-danger">**</span></label>
                                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan Nama" onkeyup="ubah_nama(this.value, 'nama')" value="<?= (!empty($promo) ? $promo->nama : '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="tgl_mulai" class="control-label">Tanggal Mulai <span class="text-danger">**</span></label>
                                                <input type="date" class="form-control" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tanggal Mulai" id="tgl_mulai" name="tgl_mulai" value="<?= (!empty($promo) ? date('Y-m-d', strtotime($promo->tgl_mulai)) : date('Y-m-d')) ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="tgl_selesai" class="control-label">Tanggal Selesai <span class="text-danger">**</span></label>
                                                <input type="date" class="form-control" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tanggal Selesai" id="tgl_selesai" name="tgl_selesai" value="<?= (!empty($promo) ? date('Y-m-d', strtotime($promo->tgl_selesai)) : date('Y-m-d')) ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="discpr" class="control-label">Diskon (%) <span class="text-danger">**</span></label>
                                                <input type="text" class="form-control text-right" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Diskon (%)" id="discpr" name="discpr" value="<?= (!empty($promo) ? number_format($promo->discpr) : '0') ?>" onchange="formatRp(this.value, 'discpr')">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="min_buy" class="control-label">Minimal Pembelian (Rp) <span class="text-danger">**</span></label>
                                                <input type="text" class="form-control text-right" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Minimal Pembelian (Rp)" id="min_buy" name="min_buy" value="<?= (!empty($promo) ? number_format($promo->min_buy) : '0') ?>" onchange="formatRp(this.value, 'min_buy')">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="keterangan">Keterangan <span class="text-danger">**</span></label>
                                                <textarea name="keterangan" id="keterangan" class="form-control" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Keterangan" onkeyup="ubah_nama(this.value, 'keterangan')"><?= (!empty($promo) ? ($promo->keterangan) : '') ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-danger" onclick="getUrl('Marketing/promo')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                            <button type="button" class="btn btn-success float-right ml-2" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                            <?php if (!empty($promo)) : ?>
                                <button type="button" class="btn btn-info float-right" onclick="getUrl('Marketing/form_promo/0')" id="btnBaru"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Baru</button>
                            <?php else : ?>
                                <button type="button" class="btn btn-info float-right" onclick="reseting()" id="btnReset"><i class="fa-solid fa-arrows-rotate"></i>&nbsp;&nbsp;Reset</button>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    var table;
    const form = $('#form_promo');
    const btnSimpan = $('#btnSimpan');
    var kodePromo = $('#kodePromo');
    var nama = $('#nama');
    var tgl_mulai = $('#tgl_mulai');
    var tgl_selesai = $('#tgl_selesai');
    var discpr = $('#discpr');
    var min_buy = $('#min_buy');
    var keterangan = $('#keterangan');

    btnSimpan.attr('disabled', false);

    // fungsi format Rupiah NoId
    function formatRpNoId(num) {
        num = num.toString().replace(/\$|\,/g, '');

        num = Math.ceil(num);

        if (isNaN(num)) num = "0";

        sign = (num == (num = Math.abs(num)));
        num = Math.floor(num * 100 + 0.50000000001);
        cents = num % 100;
        num = Math.floor(num / 100).toString();

        if (cents < 10) cents = "0" + cents;

        for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++) {
            num = num.substring(0, num.length - (4 * i + 3)) + ',' +
                num.substring(num.length - (4 * i + 3));
        }

        return (((sign) ? '' : '-') + '' + num);
    }

    // fungsi simpan
    function save() {
        btnSimpan.attr('disabled', true);

        if (nama.val() == '' || nama.val() == null) { // jika nama null/ kosong
            btnSimpan.attr('disabled', false);

            Swal.fire("Nama", "Form sudah diisi?", "question");
            return;
        }

        if (tgl_mulai.val() == '' || tgl_mulai.val() == null) { // jika tgl_mulai null/ kosong
            btnSimpan.attr('disabled', false);

            Swal.fire("Tanggal Mulai", "Form sudah diisi?", "question");
            return;
        }

        if (tgl_selesai.val() == '' || tgl_selesai.val() == null) { // jika tgl_selesai null/ kosong
            btnSimpan.attr('disabled', false);

            Swal.fire("Tanggal Selesai", "Form sudah diisi?", "question");
            return;
        }

        if (discpr.val() == '' || discpr.val() == null || discpr.val() == 0) { // jika discpr null/ kosong
            btnSimpan.attr('disabled', false);

            Swal.fire("Diskon (%)", "Form sudah diisi?", "question");
            return;
        }

        if ($('#min_buy').val() == '' || $('#min_buy').val() == null) { // jika min_buy null/ kosong
            btnSimpan.attr('disabled', false);

            Swal.fire("Minimal Pembelian (Rp)", "Form sudah diisi?", "question");
            return;
        }

        if (keterangan.val() == '' || keterangan.val() == null) { // jika keterangan null/ kosong
            btnSimpan.attr('disabled', false);

            Swal.fire("Keterangan", "Form sudah diisi?", "question");
            return;
        }

        if (kodePromo.val() == '' || kodePromo.val() == null) { // jika kode_promo null/ kosong
            // isi param = 1
            var param = 1;
        } else { // selain itu
            // isi param = 2
            var param = 2;
        }

        // jalankan proses cek promo
        if (param == 1) {
            $.ajax({
                url: siteUrl + 'Marketing/cekProm',
                type: 'POST',
                dataType: 'JSON',
                data: form.serialize(),
                success: function(result) { // jika fungsi berjalan dengan baik
                    if (result.status == 1) { // jika mendapatkan respon 1
                        // jalankan fungsi proses berdasarkan param
                        proses(param);
                    } else { // selain itu
                        btnSimpan.attr('disabled', false);

                        Swal.fire("Nama", "Sudah ada!, silahkan isi nama lain ", "info");
                    }
                },
                error: function(result) { // jika fungsi error
                    btnSimpan.attr('disabled', false);

                    error_proccess();
                }
            });
        } else {
            proses(param);
        }

    }

    // fungsi proses dengan param
    function proses(param) {

        if (param == 1) { // jika param 1 berarti insert/tambah
            var message = 'dibuat!';
        } else { // selain itu berarti update/ubah
            var message = 'diperbarui!';
        }

        // jalankan proses dengan param insert/update
        $.ajax({
            url: siteUrl + 'Marketing/promo_proses/' + param,
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) { // jika fungsi berjalan dengan baik
                btnSimpan.attr('disabled', false);

                if (result.status == 1) { // jika mendapatkan respon 1

                    Swal.fire("Promo", "Berhasil " + message, "success").then(() => {
                        getUrl('Marketing/promo');
                    });
                } else { // selain itu

                    Swal.fire("Promo", "Gagal " + message + ", silahkan dicoba kembali", "info");
                }
            },
            error: function(result) { // jika fungsi error
                btnSimpan.attr('disabled', false);

                error_proccess();
            }
        });
    }

    // fungsi reset form
    function reseting() {
        if (kodePromo.val() == '' || kodePromo.val() == null) { // jika kode_promonya tidak ada isi/ null
            // kosongkan
            kodePromo.val('');
        }

        nama.val('');
        discpr.val('0');
        min_buy.val('0');
    }
</script>