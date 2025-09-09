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

<form method="post" id="form_barang">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Formulir</span>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="id" class="control-label">Barcode</label>
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" id="kodeBarang" name="kodeBarang" placeholder="Otomatis, Bila sudah ada isikan barcode" value="<?= (!empty($barang) ? $barang->kode_barang : '') ?>" <?= (!empty($barang) ? 'readonly' : '') ?>>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" <?= ((!empty($barang) ? '' : 'disabled')) ?>>
                                                    <i class="fa-regular fa-hourglass-half"></i>&nbsp;&nbsp;<?= ((!empty($barang) ? 'Stok Barang Gudang' : 'Stok Barang Baru Tidak Tersedia')) ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="kode_satuan" class="control-label text-danger">Satuan Kecil</label>
                                        <select name="kode_satuan" id="kode_satuan" class="form-control select2_global" data-placeholder="~ Pilih Satuan">
                                            <option value="">~ Pilih Satuan</option>
                                            <?php foreach ($m_satuan as $s) : ?>
                                                <option value="<?= $s->kode_satuan ?>" <?= (!empty($barang) ? (($s->kode_satuan == $barang->kode_satuan) ? 'selected' : '') : '') ?>><?= $s->keterangan ?></option>
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
                                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan Nama" onkeyup="ubah_nama(this.value, 'nama')" value="<?= (!empty($barang) ? $barang->nama : '') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="kode_satuan2" class="control-label">Satuan Sedang</label>
                                        <select name="kode_satuan2" id="kode_satuan2" class="form-control select2_global" data-placeholder="~ Pilih Satuan" onchange="qty_satuan(this.value, 2)">
                                            <option value="">~ Pilih Satuan</option>
                                            <?php foreach ($m_satuan as $s) : ?>
                                                <option value="<?= $s->kode_satuan ?>" <?= (!empty($barang) ? (($s->kode_satuan == $barang->kode_satuan2) ? 'selected' : '') : '') ?>><?= $s->keterangan ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="qty_satuan2">Qty Satuan Sedang</label>
                                        <input type="text" name="qty_satuan2" id="qty_satuan2" class="form-control text-right" value="<?= (!empty($barang) ? number_format($barang->qty_satuan2) : 0) ?>">
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
                                                <option value="<?= $k->kode_kategori ?>" <?= (!empty($barang) ? (($k->kode_kategori == $barang->kode_kategori) ? 'selected' : '') : '') ?>><?= $k->keterangan ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="kode_satuan3" class="control-label">Satuan Besar</label>
                                        <select name="kode_satuan3" id="kode_satuan3" class="form-control select2_global" data-placeholder="~ Pilih Satuan" onchange="qty_satuan(this.value, 3)">
                                            <option value="">~ Pilih Satuan</option>
                                            <?php foreach ($m_satuan as $s) : ?>
                                                <option value="<?= $s->kode_satuan ?>" <?= (!empty($barang) ? (($s->kode_satuan == $barang->kode_satuan3) ? 'selected' : '') : '') ?>><?= $s->keterangan ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="qty_satuan3">Qty Satuan Besar</label>
                                        <input type="text" name="qty_satuan3" id="qty_satuan3" class="form-control text-right" value="<?= (!empty($barang) ? number_format($barang->qty_satuan3) : 0) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="hna" class="control-label text-danger">HNA</label>
                                        <input type="text" name="hna" id="hna" class="form-control text-right" value="<?= (!empty($barang) ? number_format($barang->hna) : '0') ?>" onchange="formatRp(this.value, 'hna'); getHpp(this.value); cek_opsi_hpp($('opsi_hpp').val());">
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
                                                    <option value="1" <?= (!empty($barang) ? (($barang->opsi_hpp == 1) ? 'selected' : '') : '') ?>>Ya</option>
                                                    <option value="0" <?= (!empty($barang) ? (($barang->opsi_hpp == 0) ? 'selected' : '') : '') ?>>Tidak</option>
                                                </select>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" name="hpp" id="hpp" class="form-control text-right" value="<?= (!empty($barang) ? number_format($barang->hpp) : '0') ?>" onchange="formatRp(this.value, 'hpp'); cekHna(this.value, 'hpp')">
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
                                                            <option value="0" <?= (!empty($barang) ? (($barang->opsi_jual == 0) ? 'selected' : '') : '') ?>>Manual</option>
                                                            <option value="1" <?= (!empty($barang) ? (($barang->opsi_jual == 1) ? 'selected' : '') : '') ?>>Margin</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" name="margin" id="margin" class="form-control text-right" value="<?= (!empty($barang) ? number_format($barang->margin) : '0') ?>" onchange="get_hj(this.value)">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" name="harga_jual" id="harga_jual" class="form-control text-right" value="<?= (!empty($barang) ? number_format($barang->harga_jual) : '0') ?>" onchange="formatRp(this.value, 'harga_jual'); cekHpp(this.value, 'harga_jual')">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="nilai_persediaan" class="control-label text-danger">Nilai Persediaan</label>
                                        <input type="text" name="nilai_persediaan" id="nilai_persediaan" class="form-control text-right" value="<?= (!empty($barang) ? number_format($barang->nilai_persediaan) : '0') ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="stok_min" class="control-label text-danger">Stok Minimal</label>
                                        <input type="text" name="stok_min" id="stok_min" class="form-control text-right" value="<?= (!empty($barang) ? number_format($barang->stok_min) : '0') ?>" onchange="formatRp(this.value, 'stok_min')">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="stok_max" class="control-label text-danger">Stok Maksimal</label>
                                        <input type="text" name="stok_max" id="stok_max" class="form-control text-right" value="<?= (!empty($barang) ? number_format($barang->stok_max) : '0') ?>" onchange="formatRp(this.value, 'stok_max')">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="filefoto" class="control-label">Gambar</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card">
                                            <img id="preview_img" class="rounded mx-auto d-block" style="border: 2px solid grey; width: 100%;" src="<?= base_url('assets/img/obat/') . (!empty($barang) ? $barang->image : 'default.jpg'); ?>" alt="User profile picture">
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="filefoto" aria-describedby="inputGroupFileAddon01" name="filefoto" onchange="readURL(this)">
                                                <label class="custom-file-label" id="label-gambar" for="inputGroupFile01">Cari Gambar</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label for="kode_jenis" class="control-label text-danger">Jenis Obat</label>
                                        <select name="kode_jenis[]" id="kode_jenis" class="form-control select2_global" data-placeholder="~ Pilih Jenis Obat" multiple="multiple">
                                            <option value="">~ Pilih Jenis Obat</option>
                                            <?php if (!empty($barang)) :
                                                $bj_arr = [];
                                                foreach ($barang_jenis as $bj) :
                                                    $bj_arr[] = $bj->kode_jenis;
                                            ?>
                                            <?php endforeach;
                                            endif; ?>
                                            <?php foreach ($jenis as $j) : ?>
                                                <option value="<?= $j->kode_jenis ?>" <?= (!empty($barang) ? (in_array($j->kode_jenis, $bj_arr) ? 'selected' : '') : '') ?>><?= $j->keterangan ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="kode_cabang" class="control-label text-danger">Cabang</label>
                                <select name="kode_cabang[]" id="kode_cabang" class="form-control select2_global" data-placeholder="~ Pilih Cabang" multiple="multiple">
                                    <option value="">~ Pilih Cabang</option>
                                    <?php if (!empty($barang)) :
                                        $cabang_arr = [];
                                        foreach ($barang_cabang as $bc) :
                                            $cabang_arr[] = $bc->kode_cabang;
                                    ?>
                                    <?php endforeach;
                                    endif; ?>
                                    <?php foreach ($cabang_all as $ca) : ?>
                                        <option value="<?= $ca->kode_cabang ?>" <?= (!empty($barang) ? (in_array($ca->kode_cabang, $cabang_arr) ? 'selected' : '') : '') ?>><?= $ca->cabang ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-danger" onclick="getUrl('Master/barang')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                            <button type="button" class="btn btn-success float-right ml-2" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                            <?php if (!empty($barang)) : ?>
                                <button type="button" class="btn btn-info float-right" onclick="getUrl('Master/form_barang/0')" id="btnBaru"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
                            <?php else : ?>
                                <button type="button" class="btn btn-info float-right" onclick="reset()" id="btnReset"><i class="fa-solid fa-arrows-rotate"></i>&nbsp;&nbsp;Reset</button>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" <?= $style_modal ?>>
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Stok Barang Gudang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" width="100%" style="border-radius: 10px;">
                            <thead>
                                <tr class="text-center">
                                    <th width="70%" style="border-radius: 10px 0px 0px 0px;">Gudang</th>
                                    <th width="30%" style="border-radius: 0px 10px 0px 0px;">Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($barang_stok)) : ?>
                                    <?php foreach ($barang_stok as $bs) :
                                        $gudang = $this->M_global->getData('m_gudang', ['kode_gudang'  => $bs->kode_gudang]);
                                    ?>
                                        <tr>
                                            <td>
                                                <?= $gudang->nama ?>
                                            </td>
                                            <td class="text-right">
                                                <?= number_format($bs->akhir) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2" class="text-center">
                                            <span>Data Tidak Tersedia</span>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    var table;
    const form = $('#form_barang');
    const btnSimpan = $('#btnSimpan');
    var kodeBarang = $('#kodeBarang');
    var nama = $('#nama');
    var kode_satuan = $('#kode_satuan');
    var kode_satuan2 = $('#kode_satuan2');
    var kode_satuan3 = $('#kode_satuan3');
    var qty_satuan2 = $('#qty_satuan2');
    var qty_satuan3 = $('#qty_satuan3');
    var opsi_hpp = $('#opsi_hpp');
    var kode_kategori = $('#kode_kategori');
    var hna = $('#hna');
    var hpp = $('#hpp');
    var margin = $('#margin');
    var harga_jual = $('#harga_jual');
    var opsi_jual = $('#opsi_jual');
    var nilai_persediaan = $('#nilai_persediaan');

    btnSimpan.attr('disabled', false);

    <?php if (!empty($barang)) : ?>
        <?php if ($barang->opsi_hpp == 1) : ?>
            hpp.attr('readonly', false);
        <?php else : ?>
            hpp.attr('readonly', true);
        <?php endif; ?>
    <?php else : ?>
        hpp.attr('readonly', false);
    <?php endif; ?>

    // opsi hpp
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

            formatRp(harga_hpp, 'hpp');
            return Swal.fire("Persentase", "Maksimal adalah 100%", "info");
        }

        var harga_awal = Number(parseInt(hna.val().replaceAll(',', '')));
        var harga_tambahan = harga_awal * (Number(persentase) / 100);
        var harga_hpp = harga_awal + harga_tambahan;
        formatRp(harga_hpp, 'hpp');
    }

    // fungsi hitung hpp
    function getHpp(param) {

        var a = parseInt(param.replaceAll(',', ''));
        var result = a;
        formatRp(result, 'hpp');
    }

    // qty_satuan required
    function qty_satuan(param, ke) {
        if ($('#kode_satuan' + ke).val() == '' || $('#kode_satuan' + ke).val() == null) {
            $('#qty_satuan' + ke).val(0);
        } else {
            if ($('#qty_satuan' + ke).val() < 1) {
                $('#qty_satuan' + ke).val(1);
            }
        }
    }

    // fungsi cek HNA
    function cekHna(param) {
        var x = hna.val();
        var a = parseInt(param.replaceAll(',', ''));
        var b = parseInt(x.replaceAll(',', ''));

        if (b > a) {
            Swal.fire("HPP", "Tidak boleh lebih kecil dari HNA", "question");
            formatRp(b, 'hpp');
        } else {
            formatRp(a, 'hpp');
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

    // preview image
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#div_preview_foto').css("display", "block");
                $('#preview_img').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            $('#div_preview_foto').css("display", "none");
            $('#preview_img').attr('src', '');
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

        if (hna.val() == '' || hna.val() == null || hna.val() == '0.00') { // jika hna null/ kosong
            btnSimpan.attr('disabled', false);

            Swal.fire("HNA", "Form sudah diisi?", "question");
            return;
        }

        if (hpp.val() == '' || hpp.val() == null || hpp.val() == '0.00') { // jika hpp null/ kosong
            btnSimpan.attr('disabled', false);

            Swal.fire("HPP", "Form sudah diisi?", "question");
            return;
        }

        if (harga_jual.val() == '' || harga_jual.val() == null || harga_jual.val() == '0.00') { // jika harga_jual null/ kosong
            btnSimpan.attr('disabled', false);

            Swal.fire("Jual", "Form sudah diisi?", "question");
            return;
        }

        if (nilai_persediaan.val() == '' || nilai_persediaan.val() == null || nilai_persediaan.val() == '0.00') { // jika nilai_persediaan null/ kosong
            btnSimpan.attr('disabled', false);

            Swal.fire("Nilai Persediaan", "Form sudah diisi?", "question");
            return;
        }

        if (kodeBarang.val() == '' || kodeBarang.val() == null) { // jika kode_barang null/ kosong
            // isi param = 1
            var param = 1;
        } else { // selain itu
            // isi param = 2
            var param = 2;
        }

        // jalankan proses cek barang
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
        var form = $('#form_barang')[0];
        var data = new FormData(form);

        $.ajax({
            url: siteUrl + 'Master/barang_proses/' + param,
            type: "POST",
            enctype: 'multipart/form-data',
            data: data,
            dataType: "JSON",
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function(result) { // jika fungsi berjalan dengan baik
                btnSimpan.attr('disabled', false);

                if (result.status == 1) { // jika mendapatkan respon 1

                    Swal.fire("Barang", "Berhasil " + message, "success").then(() => {
                        getUrl('Master/barang');
                    });
                } else { // selain itu

                    Swal.fire("Barang", "Gagal " + message + ", silahkan dicoba kembali", "info");
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
        if (kodeBarang.val() == '' || kodeBarang.val() == null) { // jika kode_barangnya tidak ada isi/ null
            // kosongkan
            kodeBarang.val('');
        }

        nama.val('');
        hna.val('0.00');
        hpp.val('0.00');
        kode_satuan.val('').change();
        kode_kategori.val('').change();
        harga_jual.val('0.00');
        nilai_persediaan.val('0.00');
    }

    function showGuide() {
        // clean text
        $('#modal_mgLabel').text(``);
        $('#modal-isi').text(``);

        $('#modal_mg').modal('show'); // show modal

        // isi text
        $('#modal_mgLabel').append(`Manual Guide Master Barang`);
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