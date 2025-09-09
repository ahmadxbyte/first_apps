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

<form id="form_reservasi">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Reservasi</span>
                </div>
                <div class="card-footer">
                    <input type="hidden" name="kode_ruang" id="kode_ruang" value="">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <input type="date" name="tgl" id="tgl" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" class="form-control" onchange="getPoli()">
                                </div>
                                <div class="col-md-3">
                                    <select name="kode_poli" id="kode_poli" class="select2_global form-control" data-placeholder="Pilih Poli" onchange="getDokterPoli()">
                                        <option value="">Pilih Poli</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="kode_dokter" id="kode_dokter" class="select2_global form-control" data-placeholder="Pilih Dokter" onchange="getRuang(this.value)">
                                        <option value="">Pilih Dokter</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-primary" onclick="proses()">Daftar Reservasi</button>
                                    <button type="button" class="btn btn-info float-right" onclick="reloadTable()"><i class="fa-solid fa-rotate-right"></i>&nbsp;&nbsp;Refresh</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table shadow-sm table-hover table-bordered" id="tableReservasi" width="100%" style="border-radius: 10px;">
                            <thead>
                                <tr class="text-center">
                                    <th width="5%" style="border-radius: 10px 0px 0px 0px;">#</th>
                                    <th width="20%">Booking</th>
                                    <th width="15%">Untuk</th>
                                    <th width="15%">Batal</th>
                                    <th>Poli</th>
                                    <th>Dokter</th>
                                    <th width="10%">Antri</th>
                                    <th width="5%" style="border-radius: 0px 10px 0px 0px;">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-body">
                    <div id='calendar' style="min-width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    var table = $('#tableReservasi');

    document.addEventListener('DOMContentLoaded', function() {
        kalendar();
        getPoli();
    });

    $(".select2_global").select2({
        placeholder: $(this).data('placeholder'),
        width: '100%',
        allowClear: true,
    });

    function getPoli() {
        $('#kode_poli').empty();
        $('#kode_dokter').empty();
        var tgl = $('#tgl').val();
        var form = $('#form_reservasi');

        if (tgl == '' || tgl == null) { // jika kode poli kosong/ null
            // tampilkan notif
            return Swal.fire("Tgl", "Sudah dipilih?", "question");
        }

        $.ajax({
            url: '<?= site_url() ?>Reservasi/getPoli',
            type: 'POST',
            dataType: 'JSON',
            data: form.serialize(),
            success: function(result) {
                $('#kode_poli').append('<option value="" selected>Pilih Poli</option>');

                if (result.status == 0) {
                    Swal.fire("Poli", "Tidak terserdia", "info");
                } else {
                    $.each(result, function(index, value) {
                        $('#kode_poli').append('<option value="' + value.kode_poli + '">' + value.nama_poli + '</option>');

                        $(".select2_global").select2({
                            placeholder: $(this).data('placeholder'),
                            width: '100%',
                            allowClear: true,
                        });
                    });
                }
            },
            error: function(error) {
                error_proccess();
            }
        });

    }

    function getDokterPoli() {
        var tgl = $('#tgl').val();
        var kode_poli = $('#kode_poli').val();
        var form = $('#form_reservasi');

        if (kode_poli == '' || kode_poli == null) {
            return Swal.fire("Poli", "Sudah dipilih?", "question");
        }

        $.ajax({
            url: '<?= site_url() ?>Reservasi/getDokterPoli',
            type: 'POST',
            dataType: 'JSON',
            data: form.serialize(),
            success: function(result) {
                $('#kode_dokter').append('<option value="" selected>Pilih Dokter</option>');

                $.each(result, function(index, value) {
                    $('#kode_dokter').append('<option value="' + value.kode_dokter + '">' + value.nama_dokter + '</option>');

                    $(".select2_global").select2({
                        placeholder: $(this).data('placeholder'),
                        width: '100%',
                        allowClear: true,
                    });
                });
            },
            error: function(error) {
                error_proccess();
            }
        })
    }

    function getRuang(kdokter) {
        var tgl = $('#tgl').val();
        var kode_poli = $('#kode_poli').val();
        var form = $('#form_reservasi');

        if (tgl == '' || tgl == null || kode_poli == '' || kode_poli == null) {
            return Swal.fire("Tgl/Poli", "Sudah dipilih?", "question");
        }

        $.ajax({
            url: '<?= site_url() ?>Reservasi/getRuang',
            type: 'POST',
            dataType: 'JSON',
            data: form.serialize(),
            success: function(result) {
                if (result.status == 1) {
                    $('#kode_ruang').val(result.kode_ruang);
                } else {
                    Swal.fire("Ruang", "Gagal didapatkan", "danger");
                }
            },
            error: function(error) {
                error_proccess();
            }
        });
    }

    // fungsi kalendar
    function kalendar() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'id', // ubah lokasi ke indonesia
            initialView: 'dayGridMonth',
            editable: true,
            headerToolbar: { // menampilkan button yang akan ditampilkan
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: { // merubah text button
                today: 'Hari ini',
                month: 'Bulan',
                week: 'Minggu',
                day: 'Hari'
            },
            customButtons: { // merubah text button
                prev: {
                    text: 'Sebelumnya',
                    click: function() {
                        calendar.prev();
                    }
                },
                next: {
                    text: 'Berikutnya',
                    click: function() {
                        calendar.next();
                    }
                }
            },
            events: { // load data fullcalendar
                url: siteUrl + 'Health/jdokter_list',
                method: 'GET',
                failure: function() {
                    Swal.fire("Jadwal Dokter", "Gagal diload", "error");
                },
                allDay: false
            },
            eventContent: function(arg) { // mengubah tanda koma (,) menjadi <br>
                let title = arg.event.title.split(',').join('<br>');
                return {
                    html: title
                };
            },
            eventDidMount: function(info) {
                // Mengatur warna teks menjadi putih
                info.el.style.color = 'white';

                // Mengatur warna latar belakang berdasarkan status
                switch (info.event.extendedProps.status_dokter) {
                    case '1': // Hadir
                        info.el.style.backgroundColor = '#007bff'; // Biru
                        break;
                    case '2': // Izin
                        info.el.style.backgroundColor = '#ffd000'; // Kuning
                        break;
                    case '3': // Sakit
                        info.el.style.backgroundColor = '#ed1e32'; // Merah
                        break;
                    case '4': // Cuti
                        info.el.style.backgroundColor = '#2aae47'; // Hijau
                        break;
                    default:
                        info.el.style.backgroundColor = '#76818d'; // Warna default jika status tidak dikenali
                }
            },
            eventMouseEnter: function(info) {
                // Fungsi untuk memformat tanggal
                function formatDate(date) {
                    const yyyy = date.getFullYear();
                    let mm = date.getMonth() + 1;
                    let dd = date.getDate();

                    if (dd < 10) dd = '0' + dd;
                    if (mm < 10) mm = '0' + mm;

                    return dd + '-' + mm + '-' + yyyy;
                }

                const start_date = info.event.start || new Date(info.event.startStr); // fallback ke startStr jika start tidak ada
                const formattedStartDate = formatDateWithDay(start_date); // Memformat tanggal mulai

                const end_date = info.event.end || new Date(info.event.endStr); // fallback ke endStr jika end tidak ada
                const formattedEndDate = formatDateWithDay(end_date); // Memformat tanggal selesai

                const formattedStartTime = formatTime(info.event.extendedProps.time_start);
                const formattedEndTime = formatTime(info.event.extendedProps.time_end);

                if (info.event.extendedProps.limit_px == 0) {
                    var limit_px = 'Tidak dibatasi';
                } else {
                    var limit_px = info.event.extendedProps.limit_px + ' Pasien';
                }

                // Buat tooltip dengan informasi dokter, waktu mulai dan selesai, serta catatan
                $(info.el).tooltip({
                    title: 'Nama: ' + info.event.extendedProps.nama_dokter + '<br>Hari: ' + formattedStartDate + ' (' + formattedStartTime + ' - ' + formattedEndTime + ')<br>Limit Pasien: ' + limit_px + '<br>Catatan: ' + info.event.extendedProps.comment,
                    html: true,
                    placement: 'top'
                });

                // Tampilkan tooltip
                $(info.el).tooltip('show');
            },
            eventMouseLeave: function(info) { // saat tidak di hover
                // sembunyikan tooltip
                $(info.el).tooltip('hide');
            }
        });
        calendar.render();
    }

    function proses() {
        var kode_poli = $('#kode_poli').val();
        var kode_dokter = $('#kode_dokter').val();
        const form = $('#form_reservasi');

        if (kode_poli == '' || kode_poli == null) {
            return Swal.fire("Poli", "Sudah dipilih?", "question");
        }

        if (kode_dokter == '' || kode_dokter == null) {
            return Swal.fire("Dokter", "Sudah dipilih?", "question");
        }

        $.ajax({
            url: '<?= site_url() ?>Reservasi/reservasi_proses',
            type: 'POST',
            dataType: 'JSON',
            data: form.serialize(),
            success: function(result) {
                if (result.status == 1) { // jika mendapatkan hasil 1
                    Swal.fire("Reservasi", "Berhasil diproses", "success").then(() => {
                        reloadTable();
                    });
                } else if (result.status == 2) {
                    Swal.fire("Reservasi", "Gagal diproses!, anda sudah melakukan reservasi", "info").then(() => {
                        reloadTable();
                    });
                } else if (result.status == 3) {
                    Swal.fire("Reservasi", "Kuota sudah penuh!", "warning").then(() => {
                        reloadTable();
                    });
                } else { // selain itu
                    Swal.fire("Reservasi", "Gagal diproses" + ", silahkan dicoba kembali", "info");
                }
            },
            error: function(error) {
                error_proccess();
            }
        });
    }

    function batal(x, param) {
        Swal.fire({
            title: "Kamu yakin?",
            text: 'Akan membatalkan reservasi : ' + param,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Batalkan",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin

                // jalankan fungsi
                $.ajax({
                    url: siteUrl + 'Reservasi/batal_reser/' + x,
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) { // jika fungsi berjalan dengan baik

                        if (result.status == 1) { // jika mendapatkan hasil 1
                            Swal.fire("Reservasi", "Berhasil dibatalkan", "success").then(() => {
                                reloadTable();
                            });
                        } else if (result.status == 2) {
                            Swal.fire("Reservasi", "Sudah didaftarkan!", "info").then(() => {
                                reloadTable();
                            });
                        } else { // selain itu
                            Swal.fire("Reservasi", "Gagal dibatalkan" + ", silahkan dicoba kembali", "danger");
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
        $('#modal_mgLabel').append(`Manual Guide Reservasi`);
        $('#modal-isi').append(`
            <ol>
                <ul>
                    <li>Pilih tanggal reservasi</li>
                    <li>Pilih Poli yang dituju</li>
                    <li>Pilih Dokter yang ingin memeriksa</li>
                    <li>Klik tombol Daftar Reservasi</li>
                </ul>
            </ol>
        `);
    }
</script>