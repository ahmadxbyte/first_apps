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

<style>
    :root {
        --fc-border-color: #e9ecef;
        --fc-daygrid-event-dot-width: 5px;
        --fc-button-primary: #007bff;
    }
</style>

<form method="post" id="form_jadwal">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Jadwal Dokter</span>
                </div>
                <div class="card-footer text-center">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="hadir" style="margin-right: 10px;">Hadir</label>
                                    <input type="radio" checked style="accent-color: #007bff;">
                                </div>
                                <div class="col-md-3">
                                    <label for="izin" style="margin-right: 10px;">Izin</label>
                                    <input type="radio" checked style="accent-color: #ffd000;">
                                </div>
                                <div class="col-md-3">
                                    <label for="sakit" style="margin-right: 10px;">Sakit</label>
                                    <input type="radio" checked style="accent-color: #ed1e32;">
                                </div>
                                <div class="col-md-3">
                                    <label for="cuti" style="margin-right: 10px;">Cuti</label>
                                    <input type="radio" checked style="accent-color: #2aae47;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Formulir</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div id='calendar' style="font-size: 10px;"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="kode_dokter" class="control-label text-danger">Dokter</label>
                                            <input type="hidden" class="form-control" id="kodeJadwal" name="kodeJadwal" placeholder="Otomatis" readonly>
                                            <select name="kode_dokter" id="kode_dokter" class="form-control select2_dokter_all" data-placeholder="~ Pilih Dokter" onchange="getPoli(this.value)"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6 col-12">
                                                    <label for="kode_poli" class="control-label text-danger">Poli</label>
                                                    <select name="kode_poli" id="kode_poli" class="form-control select2_poli_dokter" data-placeholder="~ Pilih Dokter Terlebih Dahulu" onchange="getRuang()"></select>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <label for="hari" class="control-label text-danger">Hari</label>
                                                    <select name="hari" id="hari" class="form-control select2_global" onchange="getRuang()">
                                                        <option value="Monday" <?= ((date('l') == 'Monday') ? 'selected' : '') ?>>Senin</option>
                                                        <option value="Tuesday" <?= ((date('l') == 'Tuesday') ? 'selected' : '') ?>>Selasa</option>
                                                        <option value="Wednesday" <?= ((date('l') == 'Wednesday') ? 'selected' : '') ?>>Rabu</option>
                                                        <option value="Thursday" <?= ((date('l') == 'Thursday') ? 'selected' : '') ?>>Kamis</option>
                                                        <option value="Friday" <?= ((date('l') == 'Friday') ? 'selected' : '') ?>>Jumat</option>
                                                        <option value="Saturday" <?= ((date('l') == 'Saturday') ? 'selected' : '') ?>>Sabtu</option>
                                                        <option value="Sunday" <?= ((date('l') == 'Sunday') ? 'selected' : '') ?>>Ahad</option>
                                                    </select>
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
                                            <label for="kode_cabang" class="control-label text-danger">Cabang</label>
                                            <select name="kode_cabang" id="kode_cabang" class="form-control select2_all_cabang" data-placeholder="~ Pilih Cabang">
                                                <option value="<?= $this->session->userdata('cabang') ?>">
                                                    <?= $this->M_global->getData('cabang', ['kode_cabang' => $this->session->userdata('cabang')])->cabang ?>
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="time_start" class="control-label text-danger">Dari Jam</label>
                                            <input type="time" name="time_start" id="time_start" class="form-control" value="<?= date('H:' . '00') ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="time_end" class="control-label text-danger">Sampai Jam</label>
                                            <input type="time" name="time_end" id="time_end" class="form-control" value="<?= date('H:' . '00', strtotime('+1 Hour')) ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="kode_ruang" class="control-label text-danger">Ruangan</label>
                                    <select name="kode_ruang" id="kode_ruang" class="form-control select2_ruang_jd" data-placeholder="~ Pilih Ruangan" disabled>
                                        <option value="">Pilih Dokter Dahulu</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-6 col-12">
                                            <label for="status_dokter" class="control-label text-danger">Status</label>
                                            <select name="status_dokter" id="status_dokter" class="form-control select2_global" data-placeholder="~ Pilih Status">
                                                <option value="">~ Pilih Status</option>
                                                <option value="1" selected>Hadir</option>
                                                <option value="2">Izin</option>
                                                <option value="3">Sakit</option>
                                                <option value="4">Cuti</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <label for="limit_px" class="control-label">Limit Pasien</label>
                                            <div class="input-group mb-3">
                                                <input type="number" name="limit_px" id="limit_px" value="0" class="form-control text-right" aria-describedby="basic-addon2">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2">Pasien</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="comment" class="control-label">Catatan</label>
                                    <textarea name="comment" id="comment" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
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
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    // first load
    $(document).ready(function() {
        initailizeSelect2_dokter_all();
        initailizeSelect2_all_cabang();
        initailizeSelect2_poli_dokter('');
        initailizeSelect2_ruang();

        fc_function();
    });

    // fungsi fullcalendar
    function fc_function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'id', // ubah lokasi ke Indonesia
            editable: false, // disable dragging
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
            customButtons: { // menambahkan button sebelumnya dan berikutnya
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
            events: {
                url: '<?= site_url() ?>Health/jdokter_list',
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
            eventClick: function(info) { // fungsi hapus jadwal jika di klik
                const real_id = info.event.id.split('_')[0];

                const start_date = info.event.start || new Date(info.event.startStr); // fallback ke startStr jika start tidak ada
                const formattedStartDate = formatDateWithDay(start_date); // Memformat tanggal mulai

                const end_date = info.event.end || new Date(info.event.endStr); // fallback ke endStr jika end tidak ada
                const formattedEndDate = formatDateWithDay(end_date); // Memformat tanggal selesai

                const formattedStartTime = formatTime(info.event.extendedProps.time_start);
                const formattedEndTime = formatTime(info.event.extendedProps.time_end);

                Swal.fire({
                    title: '<b>Hapus Jadwal<br>' + info.event.extendedProps.nama_dokter + '</b>',
                    text: "Hari: " + formattedStartDate + " (" + formattedStartTime + " - " + formattedEndTime + ") ?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: siteUrl + 'Health/jadwal_delete',
                            type: 'POST',
                            data: {
                                kode_jadwal: real_id,
                                kode_dokter: info.event.extendedProps.kode_dokter
                            },
                            dataType: 'JSON',
                            success: function(res) {
                                fc_function();

                                if (res.status == 1) {
                                    Swal.fire("Jadwal Dokter", "Berhasil dihapus", "success");
                                    info.event.remove();
                                } else {
                                    Swal.fire("Jadwal Dokter", "Gagal dihapus", "error");
                                }
                            }
                        });
                    }
                });
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
                    var limit_px = 'Tidak Terbatas';
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


    // set variable
    const form = $('#form_jadwal');
    var kodeJadwal = $('#kodeJadwal');
    var kode_dokter = $('#kode_dokter');
    var kode_poli = $('#kode_poli');
    var kode_cabang = $('#kode_cabang');
    var status_dokter = $('#status_dokter');
    var hari = $('#hari');
    var time_start = $('#time_start');
    var time_end = $('#time_end');
    var comment = $('#comment');

    // getpoli dokter
    function getPoli(param) {
        // hapus poli sebelumnya
        $('#kode_poli').val('').change();

        // cek poli berdasarkan kode_dokter
        initailizeSelect2_poli_dokter(param);
    }

    // get ruang
    function getRuang() {
        var kode_poli = $('#kode_poli').val();
        var hari = $('#hari').val();
        var kode_cabang = $('#kode_cabang').val();

        if (kode_poli == '' || hari == '' || kode_cabang == '') {
            $('#kode_ruang').attr('disabled', true);
            return Swal.fire("Poli/Hari/Cabang", "Sudah dipilih?", "question");
        }

        $('#kode_ruang').attr('disabled', false);

        initailizeSelect2_ruang_jd(kode_poli, hari, kode_cabang);
    }

    // fungsi reset
    function reseting() { // membuat semua param kembali ke default
        $('#kodeJadwal').val('');
        $('#kode_dokter').val('').change();
        $('#kode_ruang').val('').change();
        $('#kode_cabang').val("<?= $this->session->userdata('cabang') ?>").change();
        $('#status_dokter').val('1').change();
        $('#hari').val("<?= date('l') ?>").change();
        $('#time_start').val("<?= date('H:i') ?>");
        $('#time_end').val("<?= date('H:i') ?>");
        $('#comment').val('');
    }

    // fungsi simpan
    function save() {
        if ($('#kode_dokter').val() == '' || $('#kode_cabang').val() == '' || $('#status_dokter').val() == '' || $('#hari').val() == '' || $('#time_start').val() == '' || $('#time_end').val() == '' || $('#kode_poli').val() == '') { // cek data kosong
            return Swal.fire("Form Data", "Sudah diisi lengkap?", "question");
        }

        // jalankan fungsi
        $.ajax({
            url: siteUrl + 'Health/jdokter_insert',
            type: 'POST',
            data: form.serialize(),
            dataType: 'JSON',
            success: function(res) {
                // console.log(res)
                if (res.status == 1) {
                    Swal.fire("Jadwal Dokter", "Berhasil ditambahkan", "success");
                } else {
                    Swal.fire("Jadwal Dokter", "Gagal ditambahkan", "error");
                }

                fc_function();

                reseting();
            }
        })
    }

    function showGuide() {
        // clean text
        $('#modal_mgLabel').text(``);
        $('#modal-isi').text(``);

        $('#modal_mg').modal('show'); // show modal

        // isi text
        $('#modal_mgLabel').append(`Manual Guide Jadwal Dokter`);
        $('#modal-isi').append(`
            <ol>
                <li style="font-weight: bold;">Tambah Jadwal</li>
                <p>
                    <ul>
                        <li>Pastikan Form Dokter, Poli, Ruangan tidak kosong</li>
                        <li>Jika tidak terdapat batasan pada pendaftaran pasien, maka isikan Form limit ke angka 0</li>
                        <li>Isikan Form Hari, Jam, dan Catatan sesuai dengan kebutuhan</li>
                        <li><span style='color: red;'>Teks berawarna merah</span> mengartikan wajib terisi</li>
                        <li>Klik Proses</li>
                    </ul>
                </p>
                <li style="font-weight: bold;">Ubah Jadwal</li>
                <p>
                    <ul>
                        <li>Klik dan tahan jadwal dokter yang ingin diubah</li>
                        <li>Arahkan ke hari yang ingin di harapkan</li>
                    </ul>
                </p>
                <li style="font-weight: bold;">Hapus Jadwal</li>
                <p>
                    <ul>
                        <li>Klik jadwal dokter yang ingin hapus</li>
                        <li>Saat Muncul Pop Up, klik "Ya, Hapus"</li>
                    </ul>
                </p>
                <li style="font-weight: bold; color: red;">Catatan</li>
                <p>
                    <ul>
                        <li>Pembuatan jadwal berlaku hingga akhir tahun <?= date('Y') ?></li>
                        <li>Sehingga pada tahun berikutnya (<?= date('Y', strtotime('+1 Year')) ?>), harus membuat ulang jadwal dokter</li>
                    </ul>
                </p>
            </ol>
        `);
    }
</script>