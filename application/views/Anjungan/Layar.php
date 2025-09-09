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
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }

        .poli-card .poli-name {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #bb86fc;
        }

        .poli-card .no-antrian {
            font-size: 5rem;
            font-weight: 700;
            color: #03dac6;
            margin-bottom: 10px;
        }
    </style>
</head>

<body style="background: url('<?= base_url('assets/img/web/') . $web->bg ?>') no-repeat center center fixed; background-size: cover;">
    <div class="container">
        <div class="header text-center">
            <h3 class="fw-bold"><?= $nama_apps ?> | Antrian Pendaftaran</h3>
            <h6>Cabang: <?= $this->M_global->getData('cabang', ['kode_cabang' => $cabang])->cabang ?></h6>
            <span class="time" id="live-time" style="font-size: 1vmax"></span>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-3">
                <div class="card h-100 poli-card">
                    <div class="card-header">
                        <div class="poli-name">DIPANGGIL</div>
                    </div>
                    <div class="card-body">
                        <div class="no-antrian">-</div>
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

        // Langkah 4: Putar suara (tugas non-kritis)
        function panggil_antrian(no_anjungan, komputer) {
            if (!('speechSynthesis' in window)) {
                alert('Browser tidak mendukung text-to-speech.');
                return;
            }
            const utterance = new SpeechSynthesisUtterance(`Nomor antrian ${no_anjungan}, silahkan menuju ke Pendaftaran ${komputer}`);
            utterance.lang = 'id-ID';
            utterance.rate = 1.0;
            utterance.pitch = 1.0;
            utterance.onerror = (event) => console.error('SpeechSynthesis Error:', event.error);

            speechSynthesis.cancel(); // Hentikan suara sebelumnya jika ada
            speechSynthesis.speak(utterance);
            console.log(`AUDIO: Mencoba memutar suara untuk: ${no_anjungan}  ${komputer}`);
        }

        // Langkah 3: Reset flag p_ulang di database (tugas kritis)
        function reset_antrian(no_anjungan, komputer) {
            $.ajax({
                url: `<?= site_url() ?>Anjungan/reset_no/<?= $cabang ?>?no_anjungan=${no_anjungan}`,
                type: 'GET',
                dataType: 'JSON',
                success: function(result) {
                    console.log(`DATABASE: p_ulang untuk ${no_anjungan} pada komputer ${no_anjungan} berhasil direset menjadi 0.`);
                },
                error: function(err) {
                    console.error(`DATABASE ERROR: Gagal mereset p_ulang untuk ${no_anjungan} pada komputer ${no_anjungan}.`, err);
                }
            });
        }

        // Langkah 2: Atur alur panggilan dan reset
        function suara(no_anjungan, komputer) {
            console.log(`PROCESS: Memulai proses untuk ${no_anjungan}.`);

            // Langsung jalankan tugas kritis
            reset_antrian(no_anjungan, komputer);

            // Jalankan tugas non-kritis secara paralel
            panggil_antrian(no_anjungan, komputer);

            // Buka kunci setelah jeda waktu agar bisa dipanggil ulang nanti
            setTimeout(() => {
                const index = panggilanAktif.indexOf(no_anjungan, komputer);
                if (index > -1) {
                    panggilanAktif.splice(index, 1);
                    console.log(`UNLOCK: Kunci untuk ${no_anjungan} telah dilepas.`);
                }
            }, COOLDOWN_PERIODE);
        }

        // Langkah 1: Ambil data dan putuskan apakah perlu ada panggilan
        function fetchDataAntrian() {
            $.ajax({
                url: '<?= site_url("Anjungan/get_data_antrian/") . $cabang ?>',
                type: 'POST',
                dataType: 'JSON',
                success: function(data) {
                    // Update display with latest queue number
                    if (data.no_anjungan) {
                        $('.no-antrian').html(data.no_anjungan + '<br><span style="font-size: 20px;">Pendaftaran' + data.komputer + '</span>');
                    }

                    // Check if call is needed
                    if (data.p_ulang == 1 && !panggilanAktif.includes(data.no_anjungan, data.komputer)) {
                        console.log(`TRIGGER: Found p_ulang = 1 for ${data.no_anjungan}. Locking and starting call.`);
                        panggilanAktif.push(data.no_anjungan, data.komputer); // Lock
                        suara(data.no_anjungan, data.komputer);
                    }
                },
                error: function(err) {
                    console.error("AJAX ERROR: Failed to fetch queue data.", err);
                    // Retry after short delay on error
                    setTimeout(fetchDataAntrian, 1000);
                },
                complete: function() {
                    // Log successful data fetch
                    console.log("Queue data fetch completed");
                }
            });
        }

        // --- INITIALIZATION ---
        $(document).ready(function() {
            console.log("Sistem Layar Antrian Siap.");
            updateClock();
            setInterval(updateClock, 1000);

            fetchDataAntrian();
            setInterval(fetchDataAntrian, FETCH_INTERVAL);
        });
    </script>
</body>

</html>