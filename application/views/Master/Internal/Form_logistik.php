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

<form method="post" id="form_logistik">
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
                                                <label for="id" class="control-label text-danger">ID</label>
                                                <input type="text" class="form-control" id="kodeLogistik" name="kodeLogistik" placeholder="Otomatis" value="<?= (!empty($logistik) ? $logistik->kode_logistik : '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="kode_satuan" class="control-label text-danger">Satuan</label>
                                                <select name="kode_satuan" id="kode_satuan" class="form-control select2_global" data-placeholder="~ Pilih">
                                                    <option value="">~ Pilih</option>
                                                    <?php foreach ($satuan as $s) : ?>
                                                        <option value="<?= $s->kode_satuan ?>" <?= (!empty($logistik) ? (($s->kode_satuan == $logistik->kode_satuan) ? 'selected' : '') : '') ?>><?= $s->keterangan ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="nama" class="control-label text-danger">Nama</label>
                                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan Nama" onkeyup="ubah_nama(this.value, 'nama')" value="<?= (!empty($logistik) ? $logistik->nama : '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="kode_satuan2" class="control-label">Satuan 2</label>
                                                <select name="kode_satuan2" id="kode_satuan2" class="form-control select2_global" data-placeholder="~ Pilih">
                                                    <option value="">~ Pilih</option>
                                                    <?php foreach ($satuan as $s) : ?>
                                                        <option value="<?= $s->kode_satuan ?>" <?= (!empty($logistik) ? (($s->kode_satuan == $logistik->kode_satuan2) ? 'selected' : '') : '') ?>><?= $s->keterangan ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="qty_satuan2">Qty Satuan 2</label>
                                                <input type="text" name="qty_satuan2" id="qty_satuan2" class="form-control text-right" value="<?= (!empty($logistik) ? number_format($logistik->qty_satuan2) : 0) ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="kode_kategori" class="control-label text-danger">Kategori</label>
                                                <select name="kode_kategori" id="kode_kategori" class="form-control select2_global" data-placeholder="~ Pilih">
                                                    <option value="">~ Pilih</option>
                                                    <?php foreach ($kategori as $k) : ?>
                                                        <option value="<?= $k->kode_kategori ?>" <?= (!empty($logistik) ? (($k->kode_kategori == $logistik->kode_kategori) ? 'selected' : '') : '') ?>><?= $k->keterangan ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="kode_satuan3" class="control-label">Satuan 3</label>
                                                <select name="kode_satuan3" id="kode_satuan3" class="form-control select2_global" data-placeholder="~ Pilih Satuan">
                                                    <option value="">~ Pilih Satuan</option>
                                                    <?php foreach ($satuan as $s) : ?>
                                                        <option value="<?= $s->kode_satuan ?>" <?= (!empty($logistik) ? (($s->kode_satuan == $logistik->kode_satuan3) ? 'selected' : '') : '') ?>><?= $s->keterangan ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="qty_satuan3">Qty Satuan 3</label>
                                                <input type="text" name="qty_satuan3" id="qty_satuan3" class="form-control text-right" value="<?= (!empty($logistik) ? number_format($logistik->qty_satuan3) : 0) ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="hna" class="control-label text-danger">HNA</label>
                                                <input type="text" name="hna" id="hna" class="form-control text-right" value="<?= (!empty($logistik) ? number_format($logistik->hna, 2) : '0') ?>" onchange="formatRp(this.value, 'hna'); getHpp(this.value); cek_opsi_hpp($('opsi_hpp').val());">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="hpp" class="control-label text-danger">HNA + PPN</label>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <select name="opsi_hpp" id="opsi_hpp" class="form-control select2_global" onchange="cek_opsi_hpp(this.value)">
                                                            <option value="1" <?= (!empty($logistik) ? (($logistik->opsi_hpp == 1) ? 'selected' : '') : '') ?>>Ya</option>
                                                            <option value="0" <?= (!empty($logistik) ? (($logistik->opsi_hpp == 0) ? 'selected' : '') : '') ?>>Tidak</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <input type="text" name="hpp" id="hpp" class="form-control text-right" value="<?= (!empty($logistik) ? number_format($logistik->hpp) : '0') ?>" onchange="formatRp(this.value, 'hpp'); cekHna(this.value, 'hpp')">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="harga_jual" class="control-label text-danger">Jual</label>
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <select name="opsi_jual" id="opsi_jual" class="form-control select2_global" onchange="cek_opsi_jual(this.value)">
                                                                    <option value="0" <?= (!empty($logistik) ? (($logistik->opsi_jual == 0) ? 'selected' : '') : '') ?>>Manual</option>
                                                                    <option value="1" <?= (!empty($logistik) ? (($logistik->opsi_jual == 1) ? 'selected' : '') : '') ?>>Margin</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <input type="text" name="margin" id="margin" class="form-control text-right" value="<?= (!empty($logistik) ? number_format($logistik->margin) : '0') ?>" onchange="get_hj(this.value)">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" name="harga_jual" id="harga_jual" class="form-control text-right" value="<?= (!empty($logistik) ? number_format($logistik->harga_jual) : '0') ?>" onchange="formatRp(this.value, 'harga_jual'); cekHpp(this.value, 'harga_jual')">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="nilai_persediaan" class="control-label text-danger">Nilai Persediaan</label>
                                                <input type="text" name="nilai_persediaan" id="nilai_persediaan" class="form-control text-right" value="<?= (!empty($logistik) ? number_format($logistik->nilai_persediaan, 2) : '0') ?>" onchange="cekHpp(this.value, 'nilai_persediaan')" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="kode_cabang" class="control-label text-danger">Cabang</label>
                                        <select name="kode_cabang[]" id="kode_cabang" class="form-control select2_global" data-placeholder="~ Pilih Cabang" multiple="multiple">
                                            <option value="">~ Pilih Cabang</option>
                                            <?php if (!empty($logistik)) :
                                                $cabang_arr = [];
                                                foreach ($barang_cabang as $bc) :
                                                    $cabang_arr[] = $bc->kode_cabang;
                                            ?>
                                            <?php endforeach;
                                            endif; ?>
                                            <?php foreach ($cabang_all as $ca) : ?>
                                                <option value="<?= $ca->kode_cabang ?>" <?= (!empty($logistik) ? (in_array($ca->kode_cabang, $cabang_arr) ? 'selected' : '') : '') ?>><?= $ca->cabang ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-danger" onclick="getUrl('Master/logistik')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                            <button type="button" class="btn btn-success float-right ml-2" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                            <?php if (!empty($logistik)) : ?>
                                <button type="button" class="btn btn-info float-right" onclick="getUrl('Master/form_logistik/0')" id="btnBaru"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
                            <?php else : ?>
                                <button type="button" class="btn btn-info float-right" onclick="reset()" id="btnReset"><i class="fa-solid fa-arrows-rotate"></i>&nbsp;&nbsp;Reset</button>
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
    const form = $('#form_logistik');
    const btnSimpan = $('#btnSimpan');
    var kodeLogistik = $('#kodeLogistik');
    var nama = $('#nama');
    var kode_satuan = $('#kode_satuan');
    var kode_satuan2 = $('#kode_satuan2');
    var kode_satuan3 = $('#kode_satuan3');
    var qty_satuan2 = $('#qty_satuan2');
    var qty_satuan3 = $('#qty_satuan3');
    var kode_kategori = $('#kode_kategori');
    var hna = $('#hna');
    var hpp = $('#hpp');
    var opsi_hpp = $('#opsi_hpp');
    var persentase_hpp = $('#persentase_hpp');
    var kode_cabang = $('#kode_cabang');
    var margin = $('#margin');
    var harga_jual = $('#harga_jual');
    var opsi_jual = $('#opsi_jual');
    var nilai_persediaan = $('#nilai_persediaan');

    btnSimpan.attr('disabled', false);

    <?php if (!empty($logistik)) : ?>
        <?php if ($logistik->opsi_hpp == 1) : ?>
            persentase_hpp.attr('readonly', true);
            hpp.attr('readonly', false);
        <?php else : ?>
            persentase_hpp.attr('readonly', false);
            hpp.attr('readonly', true);
        <?php endif; ?>
    <?php else : ?>
        persentase_hpp.attr('readonly', true);
        hpp.attr('readonly', false);
    <?php endif; ?>

    hpp.attr('readonly', true);
    // cek_opsi_hpp($('#opsi_hpp').val())

    function cek_opsi_hpp(param) {
        var harga_awal = hna.val().replaceAll(',', '');

        $.ajax({
            url: '<?= site_url('Master/getPajak') ?>',
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if (param == 0) {
                    hpp.val(formatRpNoId(harga_awal));
                } else {
                    hpp.val(formatRpNoId((Number(harga_awal) + (harga_awal * result.pajak))));
                }

                cek_opsi_jual($('#opsi_jual').val());
            },
            error: function(error) {
                error_proccess();
            }
        });
    }

    // opsi jual
    function cek_opsi_jual(param) {
        var hpp = $('#hpp').val().replaceAll(',', '');

        if (param == 1) {
            margin.attr('disabled', false);
            $('#harga_jual').val(0);
            $('#nilai_persediaan').val(0);
        } else {
            margin.attr('disabled', true);
            $('#harga_jual').val(formatRpNoId(hpp));
            $('#nilai_persediaan').val(formatRpNoId(hpp));
        }
    }

    // fungsi get harga jual
    function get_hj(param) {
        var opsi_jual = $('#opsi_jual').val();
        var hpp = Number($('#hpp').val().replaceAll(',', ''));

        if (opsi_jual == 1) {
            var harga_jual = (hpp + (hpp * (Number(param) / 100)));
            $('#margin').val(formatRpNoId(param));
        } else {
            var harga_jual = hpp;
            $('#margin').val(0);
        }

        $('#harga_jual').val(formatRpNoId(harga_jual));
        $('#nilai_persediaan').val(formatRpNoId(harga_jual));
    }

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
            num = num.substring(0, num.length - (4 * i + 3)) + ',' + num.substring(num.length - (4 * i + 3));
        }

        return (((sign) ? '' : '-') + '' + num);
    }

    // get hpp
    function get_hpp(persentase) {
        if (persentase > 100) {
            var harga_awal = Number(parseInt(hna.val().replaceAll(',', '')));
            var harga_tambahan = harga_awal * 1;
            var harga_hpp = harga_awal + harga_tambahan;

            formatRp(100, 'persentase_hpp');
            formatRp(harga_hpp, 'hpp');
            return Swal.fire("Persentase", "Maksimal adalah 100%", "info");
        }

        var harga_awal = Number(parseInt(hna.val().replaceAll(',', '')));
        var harga_tambahan = harga_awal * (Number(persentase) / 100);
        var harga_hpp = harga_awal + harga_tambahan;
        formatRp(persentase, 'persentase_hpp');
        formatRp(harga_hpp, 'hpp');
    }

    // fungsi hitung hpp
    function getHpp(param) {
        // if (param < 1) {
        //     hna.val(formatNonRp(param));
        //     Swal.fire("Nama", "Sudah ada!, silahkan isi nama lain ", "info");
        //     return;
        // }

        var a = parseInt(param.replaceAll(',', ''));
        // var result = a + (a * (<?= $pajak ?> / 100));
        // formatRp(result, 'hpp');
        var result = a;
        formatRp(result, 'hpp');
    }

    // fungsi cek HNA
    function cekHna(param) {
        var x = hna.val();
        var a = parseInt(param.replaceAll(',', ''));
        var b = parseInt(x.replaceAll(',', ''));

        if (b > a) {
            Swal.fire("Jual", "Tidak boleh lebih kecil dari HNA", "question");
            formatRp(b, 'harga_jual');
        } else {
            formatRp(a, 'harga_jual');
        }
    }

    // fungsi cek HPP
    function cekHpp(param, forid) {
        var x = hpp.val();
        var a = parseInt(param.replaceAll(',', ''));
        var b = parseInt(x.replaceAll(',', ''));

        if (forid == 'harga_jual') {
            if (b > a) {
                Swal.fire("Jual", "Tidak boleh lebih kecil dari HPP", "question");
                formatRp(b, 'harga_jual');
            } else {
                formatRp(a, 'harga_jual');
            }
        } else {
            if (b > a) {
                Swal.fire("Nilai Persediaan", "Tidak boleh lebih kecil dari HPP", "question");
                formatRp(b, 'nilai_persediaan');
            } else {
                formatRp(a, 'nilai_persediaan');
            }
        }
    }

    // fungsi simpan
    function save() {
        btnSimpan.attr('disabled', true);

        if (nama.val() == '' || nama.val() == null) { // jika nama null/ kosong
            btnSimpan.attr('disabled', false);

            Swal.fire("Nama", "Form sudah diisi?", "question");
            return;
        }

        if (kode_satuan.val() == '' || kode_satuan.val() == null) { // jika kode_satuan null/ kosong
            btnSimpan.attr('disabled', false);

            Swal.fire("Satuan", "Form sudah diisi?", "question");
            return;
        }

        if (kode_kategori.val() == '' || kode_kategori.val() == null) { // jika kode_kategori null/ kosong
            btnSimpan.attr('disabled', false);

            Swal.fire("Kategori", "Form sudah diisi?", "question");
            return;
        }

        if (hna.val() == '' || hna.val() == null || hna.val() == '0') { // jika hna null/ kosong
            btnSimpan.attr('disabled', false);

            Swal.fire("HNA", "Form sudah diisi?", "question");
            return;
        }

        if (hpp.val() == '' || hpp.val() == null || hpp.val() == '0') { // jika hpp null/ kosong
            btnSimpan.attr('disabled', false);

            Swal.fire("HPP", "Form sudah diisi?", "question");
            return;
        }

        if (harga_jual.val() == '' || harga_jual.val() == null || harga_jual.val() == '0') { // jika harga_jual null/ kosong
            btnSimpan.attr('disabled', false);

            Swal.fire("Jual", "Form sudah diisi?", "question");
            return;
        }

        if (nilai_persediaan.val() == '' || nilai_persediaan.val() == null || nilai_persediaan.val() == '0') { // jika nilai_persediaan null/ kosong
            btnSimpan.attr('disabled', false);

            Swal.fire("Nilai Persediaan", "Form sudah diisi?", "question");
            return;
        }

        if ($('#kode_cabang').val() == '' || $('#kode_cabang').val() == null || $('#kode_cabang').val() == '0') { // jika kode_cabang null/ kosong
            btnSimpan.attr('disabled', false);

            Swal.fire("Cabang", "Form sudah diisi?", "question");
            return;
        }

        if (kodeLogistik.val() == '' || kodeLogistik.val() == null) { // jika kode_logistik null/ kosong
            // isi param = 1
            var param = 1;
        } else { // selain itu
            // isi param = 2
            var param = 2;
        }

        // jalankan proses cek logistik
        if (param == 1) {
            $.ajax({
                url: siteUrl + 'Master/cekBar',
                type: 'POST',
                dataType: 'JSON',
                data: form.serialize(),
                success: function(result) { // jika fungsi berjalan dengan baik
                    if (result.status == 1) { // jika mendapatkan respon 1
                        // jalankan fungsi proses berdasarkan param
                        proses(param);
                    } else { // selain itu

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
            url: siteUrl + 'Master/logistik_proses/' + param,
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) { // jika fungsi berjalan dengan baik
                btnSimpan.attr('disabled', false);

                if (result.status == 1) { // jika mendapatkan respon 1

                    Swal.fire("Logistik", "Berhasil " + message, "success").then(() => {
                        getUrl('Master/logistik');
                    });
                } else { // selain itu

                    Swal.fire("Logistik", "Gagal " + message + ", silahkan dicoba kembali", "info");
                }
            },
            error: function(result) { // jika fungsi error
                btnSimpan.attr('disabled', false);

                error_proccess();
            }
        });
    }

    // fungsi reset form
    function reset() {
        if (kodeLogistik.val() == '' || kodeLogistik.val() == null) { // jika kode_logistiknya tidak ada isi/ null
            // kosongkan
            kodeLogistik.val('');
        }

        nama.val('');
        hna.val('0');
        hpp.val('0');
        kode_satuan.val('').change();
        kode_kategori.val('').change();
        harga_jual.val('0');
        nilai_persediaan.val('0');
    }

    function showGuide() {
        // clean text
        $('#modal_mgLabel').text(``);
        $('#modal-isi').text(``);

        $('#modal_mg').modal('show'); // show modal

        // isi text
        $('#modal_mgLabel').append(`Manual Guide Master Logistik`);
        $('#modal-isi').append(`
            <ol>
                <li style="font-weight: bold;">Tambah Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Tambah</li>
                        <li>Selanjutnya isikan Form yang tersedia<br><span style='color: red;'>Teks berawarna merah</span> mengartikan wajib terisi mengartikan wajib terisi</li>
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