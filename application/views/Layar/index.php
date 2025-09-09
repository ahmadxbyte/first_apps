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
            font-size: 2rem;
            font-weight: 700;
            color: #03dac6;
            margin-bottom: 10px;
        }
    </style>
</head>

<body style="background: url('<?= base_url('assets/img/web/') . $web->bg ?>') no-repeat center center fixed; background-size: cover;">
    <div class="container">
        <div class="header text-center">
            <h3 class="fw-bold"><?= $nama_apps ?> | Antrian Poli</h3>
            <h6>Cabang: <?= $this->M_global->getData('cabang', ['kode_cabang' => $cabang])->cabang ?></h6>
            <span class="time" id="live-time" style="font-size: 1vmax"></span>
        </div>

        <div class="poli-container" id="poli-container">
            <?php foreach ($polis as $poli) : ?>
                <div class="poli-card" id="poli-<?= $poli->kode_poli ?>">
                    <div class="poli-name"><?= $poli->keterangan ?></div>
                    <div class="no-antrian">-</div>
                </div>
            <?php endforeach; ?>
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
        function panggil_antrian(no_antrian, nama_poli, nama_ruang) {
            if (!('speechSynthesis' in window)) {
                alert('Browser tidak mendukung text-to-speech.');
                return;
            }
            const utterance = new SpeechSynthesisUtterance(`Nomor antrian ${no_antrian}, silahkan menuju ke Poli ${nama_poli} yang berada di ${nama_ruang}.`);
            utterance.lang = 'id-ID';
            utterance.rate = 1.0;
            utterance.pitch = 1.0;
            utterance.onerror = (event) => console.error('SpeechSynthesis Error:', event.error);

            speechSynthesis.cancel(); // Hentikan suara sebelumnya jika ada
            speechSynthesis.speak(utterance);
            console.log(`AUDIO: Mencoba memutar suara untuk: ${no_antrian}`);
        }

        // Langkah 3: Reset flag p_ulang di database (tugas kritis)
        function reset_antrian(no_trx) {
            $.ajax({
                url: `<?= site_url() ?>Layar/reset_no?no_trx=${no_trx}`,
                type: 'GET',
                dataType: 'JSON',
                success: function(result) {
                    console.log(`DATABASE: p_ulang untuk ${no_trx} berhasil direset menjadi 0.`);
                },
                error: function(err) {
                    console.error(`DATABASE ERROR: Gagal mereset p_ulang untuk ${no_trx}.`, err);
                }
            });
        }

        // Langkah 2: Atur alur panggilan dan reset
        function suara(no_antrian, nama_poli, no_trx, nama_ruang) {
            console.log(`PROCESS: Memulai proses untuk ${no_trx}.`);

            // Langsung jalankan tugas kritis
            reset_antrian(no_trx);

            // Jalankan tugas non-kritis secara paralel
            panggil_antrian(no_antrian, nama_poli, nama_ruang);

            // Buka kunci setelah jeda waktu agar bisa dipanggil ulang nanti
            setTimeout(() => {
                const index = panggilanAktif.indexOf(no_trx);
                if (index > -1) {
                    panggilanAktif.splice(index, 1);
                    console.log(`UNLOCK: Kunci untuk ${no_trx} telah dilepas.`);
                }
            }, COOLDOWN_PERIODE);
        }

        // Langkah 1: Ambil data dan putuskan apakah perlu ada panggilan
        function fetchDataAntrian() {
            $.ajax({
                url: '<?= site_url("Layar/get_data_antrian/") . $cabang ?>',
                type: 'POST',
                dataType: 'JSON',
                success: function(data) {
                    if (!Array.isArray(data)) return;

                    data.forEach(item => {
                        const poliCard = $(`#poli-${item.kode_poli}`);
                        if (!poliCard.length) return;

                        // Update tampilan di layar
                        const noAntrianElement = poliCard.find('.no-antrian');
                        noAntrianElement.text(item.panggil > 0 ? item.no_antrian : '-');

                        // Cek apakah panggilan diperlukan
                        if (item.p_ulang == 1 && !panggilanAktif.includes(item.no_trx)) {
                            console.log(`TRIGGER: Ditemukan p_ulang=1 untuk ${item.no_trx}. Mengunci dan memulai panggilan.`);
                            panggilanAktif.push(item.no_trx); // Kunci
                            suara(item.no_antrian, item.nama_poli, item.no_trx, item.nama_ruang);
                        }
                    });
                },
                error: function(err) {
                    console.error("AJAX ERROR: Gagal mengambil data antrian.", err);
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