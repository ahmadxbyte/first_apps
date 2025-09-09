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

<form method="post" id="form_penyesuaian_stok">
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
                                <label for="invoice">Invoice</label>
                                <input type="text" class="form-control" placeholder="Invoice (Otomatis)" id="invoice" name="invoice" value="<?= (!empty($data_penyesuaian_stok) ? $data_penyesuaian_stok->invoice : '') ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="">Tgl/Jam Penyesuaian</label>
                                <div class="row">
                                    <div class="col-md-6 col-6">
                                        <input type="date" title="Tgl Penyesuaian" class="form-control" placeholder="Tgl Penyesuaian" id="tgl_penyesuaian" name="tgl_penyesuaian" value="<?= (!empty($data_penyesuaian_stok) ? date('Y-m-d', strtotime($data_penyesuaian_stok->tgl_penyesuaian)) : date('Y-m-d')) ?>" readonly>
                                    </div>
                                    <div class="col-md-6 col-6">
                                        <input type="time" title="Jam Penyesuaian" class="form-control" placeholder="Jam Penyesuaian" id="jam_penyesuaian" name="jam_penyesuaian" value="<?= (!empty($data_penyesuaian_stok) ? date('H:i:s', strtotime($data_penyesuaian_stok->jam_penyesuaian)) : date('H:i:s')) ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="kode_gudang">Gudang <sup class="text-danger">**</sup></label>
                                <select name="kode_gudang" id="kode_gudang" class="form-control select2_gudang_int" data-placeholder="~ Pilih Gudang">
                                    <?php
                                    if (!empty($data_penyesuaian_stok)) :
                                        $gudang = $this->M_global->getData('m_gudang', ['kode_gudang' => $data_penyesuaian_stok->kode_gudang])->nama;
                                        echo '<option value="' . $data_penyesuaian_stok->kode_gudang . '">' . $data_penyesuaian_stok->kode_gudang . ' ~ ' . $gudang . '</option>';
                                    endif;
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="tipe_penyesuaian">Tipe Penyesuaian</label>
                                <input type="text" id="tipe_penyesuaianx" name="tipe_penyesuaianx" class="form-control" placeholder="Adjusment" readonly>
                                <input type="hidden" value="0" id="tipe_penyesuaian" name="tipe_penyesuaian">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Detail Barang</span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <input type="hidden" name="jumlahBarisBarang" id="jumlahBarisBarang" value="<?= (!empty($barang_detail) ? count($barang_detail) : '0') ?>">
                                <table class="table shadow-sm table-striped table-hover table-bordered" id="tableDetailPenyesuaianStok">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="5%">Hapus</th>
                                            <th width="65%">Barang</th>
                                            <th width="15%">Satuan</th>
                                            <th width="15%">Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyPenyesuaianStok">
                                        <?php if (!empty($barang_detail)) : ?>
                                            <?php
                                            $no = 1;
                                            foreach ($barang_detail as $bd) :
                                                // Fetching the item and its units
                                                $barang = $this->M_global->getData('barang', ['kode_barang' => $bd->kode_barang]);

                                                // Preparing units
                                                $satuan = [];
                                                foreach ([$barang->kode_satuan, $barang->kode_satuan2, $barang->kode_satuan3] as $satuanCode) {
                                                    $satuanDetail = $this->M_global->getData('m_satuan', ['kode_satuan' => $satuanCode]);
                                                    if ($satuanDetail) {
                                                        $satuan[] = [
                                                            'kode_satuan' => $satuanCode,
                                                            'keterangan'  => $satuanDetail->keterangan,
                                                        ];
                                                    }
                                                }
                                            ?>
                                                <tr id="rowPenyesuaianStok<?= $no ?>">
                                                    <td class="text-center">
                                                        <button class="btn btn-sm btn-danger" type="button" id="btnHapus<?= $no ?>" onclick="hapusBarang('<?= $no ?>')">
                                                            <i class="fa-solid fa-delete-left"></i>
                                                        </button>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" id="kode_penyesuaian_stok<?= $no ?>" name="kode_penyesuaian_stok[]" value="<?= htmlspecialchars($bd->kode_barang) ?>">
                                                        <span><?= htmlspecialchars($bd->kode_barang) ?> ~ <?= htmlspecialchars($barang->nama) ?></span>
                                                    </td>
                                                    <td>
                                                        <select name="kode_satuan[]" id="kode_satuan<?= $no ?>" class="form-control select2_global" data-placeholder="~ Pilih Satuan">
                                                            <option value="">~ Pilih Satuan</option>
                                                            <?php foreach ($satuan as $s) : ?>
                                                                <option value="<?= htmlspecialchars($s['kode_satuan']) ?>" <?= ($bd->kode_satuan == $s['kode_satuan']) ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($s['keterangan']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" id="qty_ps<?= $no ?>" name="qty_ps[]" value="<?= number_format($bd->qty) ?>" class="form-control text-right" onkeyup="formatRpNoId(this.value)">
                                                    </td>
                                                </tr>
                                            <?php
                                                $no++;
                                            endforeach;
                                            ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7 col-12">
                            <div class="row">
                                <div class="col-md-8 col-6">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" placeholder="Masukan Kode/Nama Barang" id="kode_barang" name="kode_barang">
                                        <div class="input-group-append" onclick="showBarang()">
                                            <div class="input-group-text">
                                                <i class="fa-solid fa-magnifying-glass-plus"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-6">
                                    <button type="button" class="btn btn-primary" onclick="searchBarang()" id="btnCari"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah Barang</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-danger" onclick="getUrl('Transaksi/penyesuaian_stok')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                            <button type="button" class="btn btn-success float-right ml-2" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                            <?php if (!empty($data_penyesuaian_stok)) : ?>
                                <button type="button" class="btn btn-info float-right" onclick="getUrl('Transaksi/form_penyesuaian_stok/0')" id="btnBaru"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Baru</button>
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

<!-- modal semua barang -->
<div class="modal fade" id="modal_barang" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" <?= $style_modal ?>>
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"># List Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="tutupModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div style="height: 400px; overflow: auto;">
                            <div class="table-responsive">
                                <table class="table shadow-sm table-hover table-bordered" id="tableSederhanaObat" style="width: 100%; border-radius: 10px;">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="5%" style="border-radius: 10px 0px 0px 0px;">#</th>
                                            <th width="90%">Obat</th>
                                            <th width="5%" style="border-radius: 0px 10px 0px 0px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $nolb = 1;
                                        foreach ($list_barang as $lb) :
                                        ?>
                                            <tr>
                                                <td><?= $nolb ?></td>
                                                <td>
                                                    <?= $lb->kode_barang . ' ~ ' . $lb->nama . ' ~ Satuan: ' . $this->M_global->getData('m_satuan', ['kode_satuan' => $lb->kode_satuan])->keterangan . ' ~ Kategori: ' . $this->M_global->getData('m_kategori', ['kode_kategori' => $lb->kode_kategori])->keterangan . ' ~ HNA: Rp. ' . number_format($lb->hna) ?>
                                                    <input type="hidden" name="selobat[]" id="selobat<?= $nolb ?>" value="<?= $lb->kode_barang ?>">
                                                </td>
                                                <td class="text-center">
                                                    <input type="hidden" class="form-control" name="select_barang[]" id="select_barang<?= $nolb ?>" value="0">
                                                    <input type="checkbox" class="form-control" name="select_barangx[]" id="select_barangx<?= $nolb ?>" onclick="selbar('<?= $nolb ?>')">
                                                </td>
                                            </tr>
                                        <?php $nolb++;
                                        endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary float-right" onclick="selbarfunc()"><i class="fa-regular fa-circle-check"></i> Pilih Obat</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var kode_barang = $('#kode_barang');
    const form = $('#form_penyesuaian_stok');
    const btnCari = $('#btnCari');
    const btnSimpan = $('#btnSimpan');

    // header
    var invoice = $('#invoice');
    var tgl_penyesuaian = $('#tgl_penyesuaian');
    var jam_penyesuaian = $('#jam_penyesuaian');
    var kode_gudang = $('#kode_gudang');

    // detail
    var tablePenyesuaianStok = $('#tableDetailPenyesuaianStok');
    var bodyPenyesuaianStok = $('#bodyPenyesuaianStok');
    var rowPenyesuaianStok = $('#rowPenyesuaianStok');
    var jumlahBarisBarang = $('#jumlahBarisBarang');

    $(".select2_global").select2({
        placeholder: $(this).data('placeholder'),
        width: '100%',
        allowClear: true,
    });

    $('#tableSederhanaObat').DataTable({
        "destroy": true,
        "processing": true,
        "responsive": true,
        "serverSide": false,
        "scrollCollapse": false,
        "paging": false,
        "oLanguage": {
            "sEmptyTable": "<div class='text-center'>Data Kosong</div>",
            "sInfoEmpty": "",
            "sInfoFiltered": "",
            "sSearch": "",
            "sSearchPlaceholder": "Cari data...",
            "sInfo": " Jumlah _TOTAL_ Data (_START_ - _END_)",
            "sLengthMenu": "_MENU_ Baris",
            "sZeroRecords": "<div class='text-center'>Data Kosong</div>",
            "oPaginate": {
                "sPrevious": "Sebelumnya",
                "sNext": "Berikutnya"
            }
        },
        "aLengthMenu": [
            [5, 15, 20, -1],
            [5, 15, 20, "Semua"]
        ],
        "columnDefs": [{
            "targets": [-1],
            "orderable": false,
        }, ],
    });

    // fungsi tampil modal list barang
    function showBarang() {
        $('#modal_barang').modal('show');
    }

    // fungsi tutup modal list barang
    function tutupModal() {
        $('#modal_barang').modal('hide');
    }

    // fungsi pencarian by input dan enter
    kode_barang.keypress(function(e) {
        if (e.which == 13) { // jika di enter
            // jalankan fungsi
            return searchBarang();
        }
    });

    // fungsi select barang on check
    function selbar(x) {
        if (document.getElementById('select_barangx' + x).checked == true) {
            $('#select_barang' + x).val(1);
        } else {
            $('#select_barang' + x).val(0);
        }
    }

    // tampilkan fungsi select barang
    function selbarfunc() {
        var tableBarang = $('#tableSederhanaObat').dataTable(); // ambil id table detail
        var rowCount = tableBarang.fnGetData().length; // hitung jumlah rownya
        var tableBarangIn = document.getElementById('tableDetailPenyesuaianStok'); // ambil id table detail
        var no = tableBarangIn.rows.length; // hitung jumlah rownya

        // lakukan loop
        for (var i = 1; i <= rowCount; i++) {
            if ($('#select_barang' + i).val() == 1) {
                $('#select_barang' + i).val(0);
                document.getElementById('select_barangx' + i).checked = false;
                var obat = $('#selobat' + i).val();
                $('#modal_barang').modal('hide');
                tampilList2(obat, i);
                no += 1;
                jumlahBarisBarang.val(no);
            }
        }
    }

    // fungsi tampilList2
    function tampilList2(brg, i) {
        // jalankan fungsi
        $.ajax({
            url: siteUrl + 'Transaksi/getBarang/' + brg,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) { // jika fungsi berjalan

                // reset inputan pencarian barang
                kode_barang.val('');

                if (result.status == 0) { // jika mendapatkan status 0
                    // munculkan notifikasi
                    return Swal.fire("Barang", "Tidak ditemukan!", "info");
                } else { // selain itu
                    console.log(result);
                    // tambahkan jumlah row
                    var tableBarangIn = document.getElementById('tableDetailPenyesuaianStok'); // ambil id table detail
                    var jum = tableBarangIn.rows.length; // hitung jumlah rownya
                    var x = Number(jum) + 1;

                    // masukan ke body table barang in detail
                    bodyPenyesuaianStok.append(`<tr id="rowPenyesuaianStok${x}">
                        <td class="text-center"><button class="btn btn-sm btn-danger" type="button" id="btnHapus${x}" onclick="hapusBarang('${x}')"><i class="fa-solid fa-delete-left"></i></button></td>
                        <td>
                            <input type="hidden" id="kode_penyesuaian_stok${x}" name="kode_penyesuaian_stok[]" value="${result[0].kode_barang}">
                            <span>${result[0].kode_barang} ~ ${result[0].nama}</span>
                        </td>
                        <td>
                            <select name="kode_satuan[]" id="kode_satuan${x}" class="form-control select2_global" data-placeholde="~ Pilih Satuan"></select>
                        </td>
                        <td>
                            <input type="text" id="qty_ps${x}" name="qty_ps[]" value="1" class="form-control text-right" onkeyup="formatRpNoId(this.value)">
                        </td>
                    </tr>`);

                    // each satuan
                    $.each(result[1], function(index, value) {
                        $('#kode_satuan' + x).append(`<option value="${value.kode_satuan}">${value.keterangan}</option>`)
                    });

                    jumlahBarisBarang.val(x);

                    $(".select2_global").select2({
                        placeholder: $(this).data('placeholder'),
                        width: '100%',
                        allowClear: true,
                    });
                }
            },
            error: function(result) { // jika fungsi error

                // jalankan notifikasi error
                error_proccess();
            }
        });
    }

    // fungsi pilih barang dari modal
    function selectBarang(x) {
        // ambil angka row terakhir
        var jum = Number(jumlahBarisBarang.val());

        if (x == '' || x == null) { // jika x kosong/ null
        } else { // selain itu

            // jalankan fungsi
            $('#modal_barang').modal('hide');
            tampilList(x, jum);
        }
    }

    // fungsi pencarian barang
    function searchBarang() {
        // ambil angka row terakhir
        var jum = Number(jumlahBarisBarang.val());

        if (kode_barang.val() == '' || kode_barang.val() == null) { // jika kode_barang kosong/ null
        } else { // selain itu

            // jalankan fungsi
            tampilList(kode_barang.val(), jum);
        }
    }

    // fungsi tampilList
    function tampilList(brg, jum) {

        // jalankan fungsi
        $.ajax({
            url: siteUrl + 'Transaksi/getBarang/' + brg,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) { // jika fungsi berjalan
                // reset inputan pencarian barang
                kode_barang.val('');

                if (result.status == 0) { // jika mendapatkan status 0
                    // munculkan notifikasi
                    return Swal.fire("Barang", "Tidak ditemukan!", "info");
                } else { // selain itu
                    // tambahkan jumlah row
                    var x = jum + 1;
                    jumlahBarisBarang.val(x);

                    // masukan ke body table barang in detail
                    bodyPenyesuaianStok.append(`<tr id="rowPenyesuaianStok${x}">
                        <td class="text-center"><button class="btn btn-sm btn-danger" type="button" id="btnHapus${x}" onclick="hapusBarang('${x}')"><i class="fa-solid fa-delete-left"></i></button></td>
                        <td>
                            <input type="hidden" id="kode_penyesuaian_stok${x}" name="kode_penyesuaian_stok[]" value="${result[0].kode_barang}">
                            <span>${result[0].kode_barang} ~ ${result[0].nama}</span>
                        </td>
                        <td>
                            <select name="kode_satuan[]" id="kode_satuan${x}" class="form-control select2_global" data-placeholde="~ Pilih Satuan"></select>
                        </td>
                        <td>
                            <input type="text" id="qty_ps${x}" name="qty_ps[]" value="1" class="form-control text-right" onkeyup="formatRpNoId(this.value)">
                        </td>
                    </tr>`);

                    // each satuan
                    $.each(result[1], function(index, value) {
                        $('#kode_satuan' + x).append(`<option value="${value.kode_satuan}">${value.keterangan}</option>`)
                    });

                    jumlahBarisBarang.val(x);

                    $(".select2_global").select2({
                        placeholder: $(this).data('placeholder'),
                        width: '100%',
                        allowClear: true,
                    });
                }
            },
            error: function(result) { // jika fungsi error

                // jalankan notifikasi error
                error_proccess();
            }
        });
    }

    // fungsi hapus baris barang detail
    function hapusBarang(x) {
        var awal = Number(jumlahBarisBarang.val());
        jumlahBarisBarang.val(awal - 1);

        // hapus baris barang detail dengan id tr table
        $('#rowPenyesuaianStok' + x).remove();
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
            num = num.substring(0, num.length - (4 * i + 3)) + ',' +
                num.substring(num.length - (4 * i + 3));
        }

        return (((sign) ? '' : '-') + '' + num);
    }

    // fungsi simpan
    function save() {
        btnSimpan.attr('disabled', true);

        var tableBarang = document.getElementById('tableDetailPenyesuaianStok'); // ambil id table detail
        var rowCount = tableBarang.rows.length; // hitung jumlah rownya

        if (rowCount < 1) { // jika jumlah baris detail kurang dari 1
            btnSimpan.attr('disabled', false);

            return Swal.fire("Detail Barang Penyesuaian", "Form sudah diisi?", "question");
        }

        if (tgl_penyesuaian.val() == '' || tgl_penyesuaian.val() == null) { // jika tgl_penyesuaian null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Tgl Penyesuaian", "Form sudah diisi?", "question");
        }

        if (jam_penyesuaian.val() == '' || jam_penyesuaian.val() == null) { // jika jam_penyesuaian null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Jam Penyesuaian", "Form sudah diisi?", "question");
        }

        if (kode_gudang.val() == '' || kode_gudang.val() == null) { // jika kode_gudang null/ kosong
            btnSimpan.attr('disabled', false);

            return Swal.fire("Gudang", "Form sudah dipilih?", "question");
        }

        if (invoice.val() == '' || invoice.val() == null) { // jika invoice null/ kosong
            // isi param = 1
            var param = 1;
        } else { // selain itu
            // isi param = 2
            var param = 2;
        }

        // jalankan proses cek barang
        proses(param);
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
            url: siteUrl + 'Transaksi/penyesuaian_stok_proses/' + param,
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) { // jika fungsi berjalan dengan baik
                btnSimpan.attr('disabled', false);

                if (result.status == 1) { // jika mendapatkan respon 1

                    Swal.fire("Penyesuaian Stok", "Berhasil " + message, "success").then(() => {
                        getUrl('Transaksi/penyesuaian_stok');
                    });
                } else { // selain itu

                    Swal.fire("Penyesuaian Stok", "Gagal " + message + ", silahkan dicoba kembali", "info");
                }
            },
            error: function(result) { // jika fungsi error
                btnSimpan.attr('disabled', false);

                error_proccess();
            }
        });
    }
</script>