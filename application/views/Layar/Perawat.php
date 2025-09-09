<div class="row w-100 h-auto justify-content-center">
    <?php foreach ($ruang as $r) : ?>
        <div class="col-md-4">
            <?php
            $colors = ['#FF5733', '#33FF57', '#3357FF', '#F3FF33', '#FF33A1']; // Array of colors
            $colorIndex = array_search($r->kode_ruang, array_column($ruang, 'kode_ruang')) % count($colors); // Assign color based on index
            $backgroundColor = $colors[$colorIndex];
            ?>
            <div class="card w-100" style="background-color: <?= $backgroundColor ?>;">
                <div class="card-header bg-dark font-weight-bold">
                    <span class="h1"><?= (($r->kode_poli == "U00001") ? "Umum" : $this->M_global->getData('m_poli', ['kode_poli' => $r->kode_poli])->keterangan) ?></span>
                    <span class="h1 float-right"><?= (($r->kode_ruang == "RG0000000") ? "Ruang 0" : $this->M_global->getData('m_ruang', ['kode_ruang' => $r->kode_ruang])->keterangan) ?></span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div id="panggilan<?= $r->kode_ruang ?>"></div>
                        </div>
                        <div class="col-md-4">
                            <div id="antrian<?= $r->kode_ruang ?>"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach ?>
</div>

<script>
    setInterval(function() {
        panggil();
        antri();
    }, 3000);

    function panggil() {
        <?php foreach ($ruang as $r) : ?>
                (function(<?= $r->kode_ruang ?>) {
                    xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            document.getElementById("panggilan" + <?= $r->kode_ruang ?>).innerHTML = this.responseText;
                        }
                    };
                    xhttp.open("GET", "<?= base_url('Layar/antrianPerawat/'); ?>" + <?= $r->kode_ruang ?>, true);
                    xhttp.send();
                })('<?= $r->kode_ruang ?>');
        <?php endforeach ?>
    }

    function antri() {
        <?php foreach ($ruang as $r) : ?>
                (function(kodeRuang) {
                    var now = $('#now').val();
                    xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            document.getElementById("antrian" + kodeRuang).innerHTML = this.responseText;
                        }
                    };
                    xhttp.open("GET", "<?= base_url('Layar/antrianPerawat2/'); ?>" + kodeRuang + "/" + now, true);
                    xhttp.send();
                })('<?= $r->kode_ruang ?>');
        <?php endforeach ?>
    }
</script>