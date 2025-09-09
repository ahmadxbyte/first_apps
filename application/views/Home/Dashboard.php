<?php
if ($web->ct_theme == 1) {
    $style = 'style="background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px);"';
    $style2 = 'style="backdrop-filter: blur(10px);"';
    $style3 = 'style="background: transparent;"';
    $style_modal = 'style="background-color: rgba(255, 255, 255, 0.4); -webkit-backdrop-filter: blur(4px); backdrop-filter: blur(4px);"';
} else if ($web->ct_theme == 2) {
    $style = 'style="background: rgba(30, 30, 30, 0.8); backdrop-filter: blur(5px); -webkit-backdrop-filter: blur(5px); color: white !important;"';
    $style2 = 'style="backdrop-filter: blur(10px);"';
    $style3 = 'style="background: transparent;"';
    $style_modal = 'style="background-color: rgba(30, 30, 30, 0.9); -webkit-backdrop-filter: blur(30px); backdrop-filter: blur(5px); color: white !important;"';
} else {
    $style = 'style="background-color: white;"';
    $style2 = '';
    $style3 = '';
    $style_modal = '';
}
?>
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box" <?= $style ?>>
            <div class="inner">
                <h3><span id="trx_out-counter">0</span></h3>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const targetValue = <?= $jumlah_beli ?>;
                        const duration = 2000; // Animation duration in milliseconds
                        const counterElement = document.getElementById('trx_out-counter');
                        let startValue = 0;
                        const increment = targetValue / (duration / 10);

                        const counterInterval = setInterval(() => {
                            startValue += increment;
                            if (startValue >= targetValue) {
                                startValue = targetValue;
                                clearInterval(counterInterval);
                            }
                            counterElement.textContent = new Intl.NumberFormat().format(Math.floor(startValue));
                        }, 10);
                    });
                </script>
                <p>Transaksi Keluar Hari Ini</p>
            </div>
            <div class="icon">
                <i class="ion ion-bag"></i>
            </div>
            <a type="button" onclick="getUrl('Transaksi/barang_out')" class="small-box-footer">Info Lanjut <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box" <?= $style ?>>
            <div class="inner">
                <h3><span id="trx_today-counter">0</span></h3>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const targetValue = <?= $jumlah_bayar ?>;
                        const duration = 2000; // Animation duration in milliseconds
                        const counterElement = document.getElementById('trx_today-counter');
                        let startValue = 0;
                        const increment = targetValue / (duration / 10);

                        const counterInterval = setInterval(() => {
                            startValue += increment;
                            if (startValue >= targetValue) {
                                startValue = targetValue;
                                clearInterval(counterInterval);
                            }
                            counterElement.textContent = new Intl.NumberFormat().format(Math.floor(startValue));
                        }, 10);
                    });
                </script>
                <p>Transaksi Dibayar Hari Ini</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a type="button" onclick="getUrl('Kasir')" class="small-box-footer">Info Lanjut <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box" <?= $style ?>>
            <div class="inner">
                <h3>Rp. <span id="profit-counter">0</span></h3>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const targetValue = <?= $result_jual ?>;
                        const duration = 2000; // Animation duration in milliseconds
                        const counterElement = document.getElementById('profit-counter');
                        let startValue = 0;
                        const increment = targetValue / (duration / 10);

                        const counterInterval = setInterval(() => {
                            startValue += increment;
                            if (startValue >= targetValue) {
                                startValue = targetValue;
                                clearInterval(counterInterval);
                            }
                            counterElement.textContent = new Intl.NumberFormat().format(Math.floor(startValue));
                        }, 10);
                    });
                </script>
                <p>Keuntungan</p>
            </div>
            <div class="icon">
                <i class="fa-solid fa-landmark"></i>
            </div>
            <a type="button" class="small-box-footer">&ensp;</a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box" <?= $style ?>>
            <div class="inner">
                <h3>Rp. <span id="saldo-counter">0</span></h3>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const targetValue = <?= $saldo_kas ?>;
                        const duration = 2000; // Animation duration in milliseconds
                        const counterElement = document.getElementById('saldo-counter');
                        let startValue = 0;
                        const increment = targetValue / (duration / 10);

                        const counterInterval = setInterval(() => {
                            startValue += increment;
                            if (startValue >= targetValue) {
                                startValue = targetValue;
                                clearInterval(counterInterval);
                            }
                            counterElement.textContent = new Intl.NumberFormat().format(Math.floor(startValue));
                        }, 10);
                    });
                </script>
                <p>Saldo Kas/Bank</p>
            </div>
            <div class="icon">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <a type="button" class="small-box-footer">&ensp;</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-6">
        <div class="small-box" <?= $style ?>>
            <div class="inner" style="height: 58vh">
                <canvas id="poli"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-6">
        <div class="row">
            <div class="col-lg-6 col-6">
                <div class="small-box" <?= $style ?>>
                    <div class="inner">
                        <h3>Rp. <span id="piutang-counter">0</span></h3>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const targetValue = <?= (!empty($piutangx) && !empty($piutangx->piutang)) ? $piutangx->piutang : 0 ?>;
                                const duration = 2000; // Animation duration in milliseconds
                                const counterElement = document.getElementById('piutang-counter');
                                let startValue = 0;
                                const increment = targetValue / (duration / 10);

                                const counterInterval = setInterval(() => {
                                    startValue += increment;
                                    if (startValue >= targetValue) {
                                        startValue = targetValue;
                                        clearInterval(counterInterval);
                                    }
                                    counterElement.textContent = new Intl.NumberFormat().format(Math.floor(startValue));
                                }, 10);
                            });
                        </script>
                        <p>Piutang</p>
                    </div>
                    <div class="icon">
                        <i class="fa-solid fa-scale-unbalanced-flip"></i>
                    </div>
                    <a type="button" class="small-box-footer">&ensp;</a>
                </div>
            </div>
            <div class="col-lg-6 col-6">
                <div class="small-box" <?= $style ?>>
                    <div class="inner">
                        <h3>Rp. <span id="hutang-counter">0</span></h3>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const targetValue = '<?= (!empty($hutangx) && !empty($hutangx->hutang)) ? $hutangx->hutang : 0 ?>';
                                const duration = 2000; // Animation duration in milliseconds
                                const counterElement = document.getElementById('hutang-counter');
                                let startValue = 0;
                                const increment = targetValue / (duration / 10);

                                const counterInterval = setInterval(() => {
                                    startValue += increment;
                                    if (startValue >= targetValue) {
                                        startValue = targetValue;
                                        clearInterval(counterInterval);
                                    }
                                    counterElement.textContent = new Intl.NumberFormat().format(Math.floor(startValue));
                                }, 10);
                            });
                        </script>
                        <p>Hutang</p>
                    </div>
                    <div class="icon">
                        <i class="fa-solid fa-scale-unbalanced"></i>
                    </div>
                    <a type="button" class="small-box-footer">&ensp;</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="small-box" <?= $style ?>>
                    <div class="inner">
                        <div class="table-responsive">
                            <table class="table table-hover" id="tableSederhana2" style="width: 100%;">
                                <thead>
                                    <tr class="text-center">
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 70%;">Wilayah</th>
                                        <th style="width: 25%;">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($wilayah)) : ?>
                                        <tr class="text-center">
                                            <td colspan="3">Tidak ada data</td>
                                        </tr>
                                    <?php else : ?>
                                        <?php $no = 1;
                                        foreach ($wilayah as $w) : ?>
                                            <tr class="text-center">
                                                <td><?= $no ?></td>
                                                <td><?= $w->provinsi ?></td>
                                                <td><?= $w->total ?></td>
                                            </tr>
                                        <?php $no++;
                                        endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <a type="button" class="small-box-footer">TOP 5 Wilayah</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="small-box" <?= $style ?>>
                    <div class="inner">
                        <div class="table-responsive">
                            <table class="table" id="tableNonSearch2" style="max-width: 100%;">
                                <thead>
                                    <tr class="text-center">
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 70%;">Penyakit</th>
                                        <th style="width: 25%;">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="text-center">
                                        <td colspan="3">Tidak ada data</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <a type="button" class="small-box-footer">TOP 5 Penyakit</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const poli = document.getElementById('poli');

    new Chart(poli, {
        type: 'doughnut',
        data: {
            labels: [<?php foreach ($kunjungan_poli as $kp) : ?> '<?= $kp->poli ?>',
                <?php endforeach ?>
            ],
            datasets: [{
                label: '# Orang',
                data: [<?php foreach ($kunjungan_poli as $kp) : ?> '<?= $kp->jumlah ?>',
                    <?php endforeach ?>
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    $('#tableSederhana2').DataTable({
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
        "columnDefs": [{
            "targets": [-1],
            "orderable": false,
        }, ],
    });

    $('#tableNonSearch2').DataTable({
        "destroy": true,
        "processing": true,
        "responsive": true,
        "serverSide": false,
        "scrollCollapse": false,
        "paging": false,
        "searching": false,
        "oLanguage": {
            "sEmptyTable": "<div class='text-center'>Data Kosong</div>",
            "sInfoEmpty": "",
            "sInfoFiltered": "",
            "sSearch": "",
            "sInfo": " Jumlah _TOTAL_ Data (_START_ - _END_)",
            "sLengthMenu": "_MENU_ Baris",
            "sZeroRecords": "<div class='text-center'>Data Kosong</div>",
            "oPaginate": {
                "sPrevious": "Sebelumnya",
                "sNext": "Berikutnya"
            }
        },
        "columnDefs": [{
            "targets": [-1],
            "orderable": false,
        }, ],
    });
</script>