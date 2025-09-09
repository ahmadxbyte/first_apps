<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layar Antrian - <?= $nama_apps ?></title>
    <link rel="icon" href="<?= base_url('assets/img/web/') . $web->logo ?>" type="image/ico">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            padding-top: 10px;
            max-height: 100%;
            justify-content: center !important;
            text-align: center;
        }

        .header {
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .header h1 {
            font-weight: 700;
            font-size: 2.5rem;
            margin: 0;
        }

        .header .time {
            font-size: 1.5rem;
            font-weight: 500;
        }

        .poli-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .poli-card {
            background-color: #1f1f1f;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .poli-card:hover {
            /* transform: translateY(-10px); */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }

        .poli-card .poli-name {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: #f2a142;
        }

        .poli-card .poli-name-2 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #003b47;
        }

        .poli-card .no-antrian {
            font-size: 2rem;
            font-weight: 700;
            color: #db383d;
            margin-bottom: 10px;
        }
    </style>

    <!-- sweetalert -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body style="background: url('<?= base_url('assets/img/web/') . $web->bg ?>') no-repeat center center fixed; background-size: cover;">
    <div class="container">

        <div class="header text-center">
            <h3 class="fw-bold"><?= $nama_apps ?> | Anjungan</h3>
            <h6>Cabang: <?= $this->M_global->getData('cabang', ['kode_cabang' => $cabang])->cabang ?></h6>
            <span class="time" id="live-time" style="font-size: 1vmax"></span>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card poli-card h-100">
                    <div class="card-header poli-name">
                        <h2>BOOKING</h2>
                    </div>
                    <div class="card-body">
                        <input type="text" name="kode_booking" id="kode_booking" class="form-control" style="background-color: rgba(0, 0, 0, 0.3); color: white; margin-top: 5vh" placeholder="Masukan Kode Booking">
                        <style>
                            #kode_booking::placeholder {
                                color: white;
                                opacity: 0.7;
                            }
                        </style>
                        <hr>
                        <button type="button" class="btn w-100" style="background-color: #2ebb92; color: white; font-weight: bold;" onclick="ambil_booking()">Ambil</button>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card poli-card h-100">
                    <div class="card-header poli-name">
                        <h2>MANDIRI</h2>
                    </div>
                    <div class="card-body">
                        <button onclick="ambil_antrian()" type="button" style="border: none; border-radius: 10px; padding: 2vw; width: 100%; height: 100%;">
                            <span class="poli-name-2">Antrian Ke</span>
                            <hr>
                            <div class="row">
                                <div class="col-md-7 col-7 m-auto">
                                    <input type="hidden" name="no_anjungan" id="no_anjungan" value="<?= $anjungan ?>">
                                    <div id="body_antrian" class="no-antrian"><?= $anjungan ?></div>
                                </div>
                                <div class="col-md-5 col-5">
                                    <div class="card">
                                        <div class="card-header">
                                            <span style="font-size: 1vw; font-weight: bold;">Dari</span>
                                        </div>
                                        <div class="card-body">
                                            <div class="col-md-12">
                                                <?= count($antrian) ?>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <span style="font-size: 1vw; font-weight: bold;">Tunggu</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // --- CONFIGURATION ---
        const FETCH_INTERVAL = 3000; // Ambil data setiap 3 detik
        const COOLDOWN_PERIODE = 5000; // Jeda 5 detik sebelum no_trx bisa dipanggil lagi

        // --- STATE ---
        let panggilanAktif = []; // Menyimpan no_trx yang sedang dalam proses panggil

        // --- UTILITIES ---
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            const dateString = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            document.getElementById('live-time').textContent = `${dateString} | ${timeString}`;
        }
        // --- CORE LOGIC ---

        function ambil_antrian() {
            var no_anjungan = $('#no_anjungan').val();
            $.ajax({
                url: '<?= site_url() ?>Anjungan/claim/<?= $cabang ?>/' + no_anjungan,
                type: 'POST',
                dataType: 'JSON',
                success: function(result) {
                    if (result.status == 1) {
                        Swal.fire("Antrian", "Berhasil dibuat!", "success");
                        window.location.reload()
                    } else {
                        Swal.fire("Antrian", "Gagal dibuat!, silahkan coba lagi", "success");
                    }
                },
                error: function(err) {
                    error_proccess();
                }
            })
        }

        function ambil_booking() {
            var kode = $('#kode_booking').val();
            if (kode == '' || kode == null) {
                return Swal.fire("Kode Booking", "Sudah diisi?", "question");
            }

            $.ajax({
                url: '<?= site_url() ?>Anjungan/daftar/' + kode + '/<?= $cabang ?>',
                type: 'POST',
                dataType: 'JSON',
                success: function(result) {
                    if (result.status == 1) { // jika mendapatkan hasil 1
                        Swal.fire("Reservasi", "Berhasil didaftarkan, silahkan menunggu dipanggil di poli", "success").then(() => {
                            window.location.reload()
                        });
                    } else if (result.status == 2) {
                        Swal.fire("Reservasi", "Gagal didaftarkan, kode booking tidak tersedia", "warning");
                    } else if (result.status == 3) {
                        Swal.fire("Reservasi", "Sudah didaftarkan, silahkan menuju poli", "info");
                    } else { // selain itu
                        Swal.fire("Reservasi", "Gagal didaftarkan" + ", silahkan dicoba kembali", "danger");
                    }
                },
                error: function(error) {
                    error_proccess();
                }
            });
        }

        // --- INITIALIZATION ---
        $(document).ready(function() {
            updateClock();
            setInterval(updateClock, 1000);
        });
    </script>
</body>

</html>