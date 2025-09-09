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

<form method="post" id="form_wilayah">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Wilayah</span>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" id="btn_prov" class="btn btn-primary" onclick="sel_tab(1)">Provinsi</button>
                    <button type="button" id="btn_kab" class="btn" onclick="sel_tab(2)">Kabupaten/Kota</button>
                    <button type="button" id="btn_kec" class="btn" onclick="sel_tab(3)">Kecamatan</button>
                    <input type="hidden" name="cektab" id="cektab" value="1">
                    <input type="hidden" name="text_email" id="text_email" value="Report Wilayah Provinsi">
                    <input type="hidden" name="dropdown_cetak" id="dropdown_cetak" value="provinsi">
                    <div class="float-right">
                        <button type="button" id="btn_add" class="btn btn-success" onclick="add()"><i class="fa fa-plus"></i> Tambah Data</button>
                        <button type="button" class="btn btn-info" onclick="send_data_mailx()"><i class="fa-solid fa-paper-plane"></i>&nbsp;&nbsp;Kirim Email</button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-circle-down"></i>&nbsp;&nbsp;Unduh
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="previewx()"><i class="fa-solid fa-fw fa-tv"></i>&nbsp;&nbsp;Preview</a></li>
                                <li><a class="dropdown-item" href="#" onclick="printx()"><i class="fa-regular fa-fw fa-file-pdf"></i>&nbsp;&nbsp;Pdf</a></li>
                                <li><a class="dropdown-item" href="#" onclick="excelx()"><i class="fa-regular fa-fw fa-file-excel"></i>&nbsp;&nbsp;Excel</a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="reloadTable2('tableProvinsi')"><i class="fa-solid fa-rotate-right"></i>&nbsp;&nbsp;Refresh</button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="for_prov">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table shadow-sm table-hover table-bordered" id="tableProvinsi" width="100%" style="border-radius: 10px;">
                                        <thead>
                                            <tr class="text-center">
                                                <th width="5%" style="border-radius: 10px 0px 0px 0px;">#</th>
                                                <th width="20%">ID</th>
                                                <th width="60%">Provinsi</th>
                                                <th width="15%" style="border-radius: 0px 10px 0px 0px;">Aksi</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="for_kab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table shadow-sm table-hover table-bordered" id="tableKabupaten" width="100%" style="border-radius: 10px;">
                                        <thead>
                                            <tr class="text-center">
                                                <th width="5%" style="border-radius: 10px 0px 0px 0px;">#</th>
                                                <th width="20%">ID</th>
                                                <th width="30%">Kabupaten/Kota</th>
                                                <th width="30%">Provinsi</th>
                                                <th width="15%" style="border-radius: 0px 10px 0px 0px;">Aksi</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="for_kec">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table shadow-sm table-hover table-bordered" id="tableKecamatan" width="100%" style="border-radius: 10px;">
                                        <thead>
                                            <tr class="text-center">
                                                <th width="5%" style="border-radius: 10px 0px 0px 0px;">#</th>
                                                <th width="20%">ID</th>
                                                <th width="20%">Kecamatan</th>
                                                <th width="20%">Kabupaten/Kota</th>
                                                <th width="20%">Provinsi</th>
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
    </div>

    <div class="modal fade" id="modal_wilayah" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" <?= $style_modal ?>>
                <div class="modal-header">
                    <h5 class="modal-title" id="title">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="close_tab()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" name="id_wil" id="id_wil" value="">
                <div class="modal-body"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="close_tab()">Tutup</button>
                    <button type="button" class="btn btn-primary" id="btnSimpan" onclick="proses()"><i class="fa fa-server"></i> Proses</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    var siteUrl;
    var table2 = '';
    var list_table;

    const btnSimpan = $('#btnSimpan');
    const form = $('#form_wilayah');
    const btn_prov = $('#btn_prov');
    const btn_kab = $('#btn_kab');
    const btn_kec = $('#btn_kec');
    const btn_des = $('#btn_des');
    const for_prov = $('#for_prov');
    const for_kab = $('#for_kab');
    const for_kec = $('#for_kec');
    const btn_add = $('#btn_add');
    const modal_wilayah = $('#modal_wilayah');
    const title = $('#title');
    const modal_body = $('.modal-body');
    var cektab = $('#cektab');
    var id_wil = $('#id_wil');

    $(document).ready(function() {
        sel_tab(1);
    });

    function send_data_mailx() {
        send_data_mail($('#text_email').val());
    }

    function previewx() {
        preview($('#dropdown_cetak').val());
    }

    function printx() {
        print($('#dropdown_cetak').val());
    }

    function excelx() {
        excel($('#dropdown_cetak').val());
    }

    function initializeDataTable(tableId, ajaxUrl) {
        return $(tableId).DataTable({
            "destroy": true,
            "processing": true,
            "serverSide": true,
            "order": [],
            "ajax": {
                "url": ajaxUrl,
                "type": "POST"
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
    }

    function reloadTable2(tableId) {
        $('#' + tableId).DataTable().ajax.reload(null, false);
    }

    function sel_tab(param) {
        cektab.val(param);

        if (param == 1) {
            btn_prov.addClass('btn-primary');
            btn_kab.removeClass('btn-primary');
            btn_kec.removeClass('btn-primary');
            btn_des.removeClass('btn-primary');

            for_prov.show();
            for_kab.hide();
            for_kec.hide();

            txt_emx = 'Report Wilayah Provinsi';
            dp_ctkx = 'provinsi';

            initializeDataTable($('#tableProvinsi'), '<?= site_url() ?>Master/provinsi_list/1');
        } else if (param == 2) {
            btn_prov.removeClass('btn-primary');
            btn_kab.addClass('btn-primary');
            btn_kec.removeClass('btn-primary');
            btn_des.removeClass('btn-primary');

            for_prov.hide();
            for_kab.show();
            for_kec.hide();

            txt_emx = 'Report Wilayah Kabupaten';
            dp_ctkx = 'kabupaten';

            initializeDataTable($('#tableKabupaten'), '<?= site_url() ?>Master/kabupaten_list/1');
        } else {
            btn_prov.removeClass('btn-primary');
            btn_kab.removeClass('btn-primary');
            btn_kec.addClass('btn-primary');
            btn_des.removeClass('btn-primary');

            for_prov.hide();
            for_kab.hide();
            for_kec.show();

            txt_emx = 'Report Wilayah Kecamatan';
            dp_ctkx = 'kecamatan';

            initializeDataTable($('#tableKecamatan'), '<?= site_url() ?>Master/kecamatan_list/1');
        }

        $('#text_email').val(txt_emx);
        $('#dropdown_cetak').val(dp_ctkx);
    }

    function add() {
        modal_wilayah.modal('show');

        if (cektab.val() == 1) {
            title.html('Tambah Provinsi');

            modal_body.html(`
                <div class="form-group">
                    <label for="kode_provinsi" class="control-label text-danger">ID</label>
                    <input type="text" class="form-control" id="kode_provinsi" name="kode_provinsi" placeholder="Kode Provinsi">
                </div>
                <div class="form-group">
                    <label for="provinsi" class="control-label text-danger">Provinsi</label>
                    <input type="text" class="form-control" id="provinsi" name="provinsi" placeholder="Provinsi">
                </div>
            `);
        } else if (cektab.val() == 2) {
            title.html('Tambah Kabupaten/Kota');

            modal_body.html(`
                <div class="form-group">
                    <label for="provinsi" class="control-label text-danger">Provinsi</label>
                    <select class="form-control select2_prov" id="provinsi" name="provinsi" data-placeholder="~ Pilih Provinsi">
                        <option value="">Pilih Provinsi</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="kode_kabupaten" class="control-label text-danger">ID KAB/KOTA</label>
                    <input type="text" class="form-control" id="kode_kabupaten" name="kode_kabupaten" placeholder="Kode Kab/Kota">
                </div>
                <div class="form-group">
                    <label for="kabupaten" class="control-label text-danger">Kabupaten/Kota</label>
                    <input type="text" class="form-control" id="kabupaten" name="kabupaten" placeholder="Kabupaten/Kota">
                </div>
            `);

            initailizeSelect2_prov();
        } else {
            title.html('Tambah Kecamatan');

            modal_body.html(`
                <div class="form-group">
                    <label for="provinsi" class="control-label text-danger">Provinsi</label>
                    <select class="form-control select2_prov" id="provinsi" name="provinsi" data-placeholder="~ Pilih Provinsi" onchange="getKecamatan(this.value)">
                        <option value="">Pilih Provinsi</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="kabupaten" class="control-label text-danger">Kabupaten</label>
                    <select class="form-control select2_kab" id="kabupaten" name="kabupaten" data-placeholder="~ Pilih Kabupaten">
                        <option value="">Pilih Kabupaten</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="kode_kecamatan" class="control-label text-danger">ID KEC</label>
                    <input type="text" class="form-control" id="kode_kecamatan" name="kode_kecamatan" placeholder="Kode Kec">
                </div>
                <div class="form-group">
                    <label for="kecamatan" class="control-label text-danger">Kecamatan</label>
                    <input type="text" class="form-control" id="kecamatan" name="kecamatan" placeholder="Kecamatan">
                </div>
            `);

            initailizeSelect2_prov();
        }
    }

    function close_tab() {
        modal_wilayah.modal('hide');
    }

    function getKecamatan(param) {
        initailizeSelect2_kab(param);
    }

    function ubah(param, kode) {
        modal_wilayah.modal('show');

        $.ajax({
            url: '<?= site_url() ?>Master/getWilayah/' + param + '/' + kode,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if (param == 'tableProvinsi') {
                    id_wil.val(result.kode_provinsi);

                    title.html('Ubah Provinsi');
                    modal_body.html(`
                        <div class="form-group">
                            <label for="kode_provinsi">ID</label>
                            <input type="text" class="form-control" id="kode_provinsi" name="kode_provinsi" placeholder="Kode Provinsi" value="${result.kode_provinsi}">
                        </div>
                        <div class="form-group">
                            <label for="provinsi">Provinsi</label>
                            <input type="text" class="form-control" id="provinsi" name="provinsi" placeholder="Provinsi" value="${result.provinsi}">
                        </div>
                    `);
                } else if (param == 'tableKabupaten') {
                    id_wil.val(result.kode_kabupaten);

                    title.html('Ubah Kabupaten/Kota');
                    modal_body.html(`
                        <div class="form-group">
                            <label for="provinsi">Provinsi</label>
                            <select class="form-control select2_prov" id="provinsi" name="provinsi" data-placeholder="~ Pilih Provinsi">
                                <option value="${result.kode_provinsi}">${result.provinsi}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="kode_kabupaten">ID KAB/KOTA</label>
                            <input type="text" class="form-control" id="kode_kabupaten" name="kode_kabupaten" placeholder="Kode Kab/Kota" value="${result.kode_kabupaten}">
                        </div>
                        <div class="form-group">
                            <label for="kabupaten">Kabupaten/Kota</label>
                            <input type="text" class="form-control" id="kabupaten" name="kabupaten" placeholder="Kabupaten/Kota" value="${result.kabupaten}">
                            <input type="text" name="kode_kabupaten" id="kode_kabupaten" value="${result.kode_kabupaten}" hidden>
                        </div>
                    `);

                    initailizeSelect2_prov();
                } else {
                    id_wil.val(result.kode_kecamatan);

                    title.html('Ubah Kecamatan');
                    modal_body.html(`
                        <div class="form-group">
                            <label for="provinsi">Provinsi</label>
                            <select class="form-control select2_prov" id="provinsi" name="provinsi" data-placeholder="~ Pilih Provinsi" onchange="getKecamatan(this.value)">
                                <option value="${result.kode_provinsi}">${result.provinsi}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="kabupaten">Kabupaten</label>
                            <select class="form-control select2_kab" id="kabupaten" name="kabupaten" data-placeholder="~ Pilih Kabupaten">
                                <option value="${result.kode_kabupaten}">${result.kabupaten}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="kode_kecamatan">ID KEC</label>
                            <input type="text" class="form-control" id="kode_kecamatan" name="kode_kecamatan" placeholder="Kode Kec" value="${result.kode_kecamatan}">
                        </div>
                        <div class="form-group">
                            <label for="kecamatan">Kecamatan</label>
                            <input type="text" class="form-control" id="kecamatan" name="kecamatan" placeholder="Kecamatan" value="${result.kecamatan}">
                        </div>
                    `);

                    initailizeSelect2_prov();
                    getKecamatan(result.kode_provinsi);
                }
            },
            error: function(result) {
                error_proccess();
            }
        })
    }

    function proses() {
        modal_wilayah.modal('hide');

        var tab_val = cektab.val();
        if (tab_val == 1) {
            var tableId = 'tableProvinsi';
            var wilayah = 'Provinsi';
        } else if (tab_val == 2) {
            var tableId = 'tableKabupaten';
            var wilayah = 'Kabupaten/Kota';
        } else {
            var tableId = 'tableKecamatan';
            var wilayah = 'Kecamatan';
        }

        if (id_wil.val() == '') {
            var message = 'ditambahkan';
        } else {
            var message = 'diubah';
        }

        $.ajax({
            url: siteUrl + 'Master/wilayah_proses',
            type: 'POST',
            data: form.serialize(),
            dataType: 'JSON',
            success: function(result) {
                btnSimpan.attr('disabled', false);

                if (result.status == 1) { // jika mendapatkan respon 1

                    Swal.fire(wilayah, "Berhasil " + message, "success").then(() => {
                        reloadTable2(tableId);
                    });
                } else { // selain itu

                    Swal.fire(wilayah, "Gagal " + message + ", silahkan dicoba kembali", "info");
                    modal_wilayah.modal('show');
                    reloadTable2(tableId);
                }
            },
            error: function(result) { // jika fungsi error
                btnSimpan.attr('disabled', false);

                error_proccess();
            }
        });
    }

    // fungsi hapus berdasarkan kodeId
    function hapus(param, kodeId) {
        modal_wilayah.modal('hide');

        if (param == 'tableProvinsi') {
            var wilayah = 'Provinsi';
        } else if (param == 'tableKabupaten') {
            var wilayah = 'Kabupaten/Kota';
        } else {
            var wilayah = 'Kecamatan';
        }

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
                    url: siteUrl + 'Master/delWilayah/' + param + '/' + kodeId,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik
                        btnSimpan.attr('disabled', false);

                        if (result.status == 1) { // jika mendapatkan hasil 1

                            Swal.fire(wilayah, "Berhasil di hapus!", "success").then(() => {
                                reloadTable2(param);
                            });
                        } else { // selain itu

                            Swal.fire(wilayah, "Gagal di hapus!, silahkan dicoba kembali", "info");
                        }
                    },
                    error: function(result) { // jika fungsi error
                        btnSimpan.attr('disabled', false);

                        error_proccess();
                    }
                });
            }
        });
    }

    initailizeSelect2_prov();
    initailizeSelect2_kab(param = '');

    function select2_default(param) {
        var mymessage = "Data tidak ditemukan";
        $("." + param).select2({
            placeholder: $(this).data('placeholder'),
            width: '100%',
            language: {
                noResults: function() {
                    return mymessage;
                }
            },
        });
    }

    function initailizeSelect2_prov() {
        $(".select2_prov").select2({
            allowClear: true,
            multiple: false,
            placeholder: '~ Pilih Provinsi',
            //minimumInputLength: 2,
            dropdownAutoWidth: true,
            dropdownParent: $('#modal_wilayah'),
            width: '100%',
            language: {
                inputTooShort: function() {
                    return 'Ketikan Nomor minimal 2 huruf';
                },
                noResults: function() {
                    return 'Data Tidak Ditemukan';
                }
            },
            ajax: {
                url: siteUrl + 'Select2_master/dataProvinsi',
                type: 'POST',
                dataType: 'JSON',
                delay: 100,
                data: function(result) {
                    return {
                        searchTerm: result.term
                    };
                },

                processResults: function(result) {
                    return {
                        results: result
                    };
                },
                cache: true
            }
        });
    }

    function initailizeSelect2_kab(param) {
        if (param == '' || param == null || param == 'null') { // jika parameter kosong/ null
            // jalankan fungsi select2_default
            select2_default('select2_kab');
        } else { // selain itu
            // jalan fungsi select2 asli
            $(".select2_kab").select2({
                allowClear: true,
                multiple: false,
                placeholder: '~ Pilih Kabupaten',
                dropdownAutoWidth: true,
                dropdownParent: $('#modal_wilayah'),
                width: '100%',
                language: {
                    inputTooShort: function() {
                        return 'Ketikan Nomor minimal 1 huruf';
                    },
                    noResults: function() {
                        return 'Data Tidak Ditemukan';
                    }
                },
                ajax: {
                    url: siteUrl + 'Select2_master/dataKabupaten/' + param,
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 100,
                    data: function(result) {
                        return {
                            searchTerm: result.term
                        };
                    },
                    processResults: function(result) {
                        return {
                            results: result
                        };
                    },
                    cache: true
                }
            });
        }
    }

    function showGuide() {
        // clean text
        $('#modal_mgLabel').text(``);
        $('#modal-isi').text(``);

        $('#modal_mg').modal('show'); // show modal

        // isi text
        $('#modal_mgLabel').append(`Manual Guide Master Wilayah`);
        $('#modal-isi').append(`
            <ol>
                <li style="font-weight: bold;">Tambah Data</li>
                <p>
                    <ul>
                        <li>Pastikan Form ID kosong</li>
                        <li>Isi Form<br><span style='color: red;'>Teks berawarna merah</span> mengartikan wajib terisi</li>
                        <li>Klik tombol Proses</li>
                    </ul>
                </p>
                <li style="font-weight: bold;">Ubah Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Ubah pada list data yang ingin di ubah</li>
                        <li>Ubah isi Form<br><span style='color: red;'>Teks berawarna merah</span> mengartikan wajib terisi</li>
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