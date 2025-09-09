<form id="form_cart" method="post">
    <div class="row mb-3">
        <div class="col-md-12">
            <div style="font-size: 14px; font-weight: bold;">Filter Obat</div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="row">
                        <div class="col-md-12">
                            <select name="kode_kategori" id="kode_kategori" class="select2_kategori" onchange="cari()"></select>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <input class="form-control" type="search" class="form-control" id="search" placeholder="~ Pencarian Barang..." autofocus onkeyup="cari()">
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card shadow mb-3" data-aos="zoom-in">
                <div class="row">
                    <div class="col-md-6">
                        <img src="<?= base_url('assets/img/obat/') . $barang2->image ?>" style="width: 100%; height: 300px; object-fit: cover;">
                    </div>
                    <div class="col-md-6 p-5">
                        <canvas id="graph_detail_barang" style="width: 100%;"></canvas>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row p-3">
                                <div class="col-md-8">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <h2><?= $barang2->nama ?></h2>
                                            <p class="card-text">
                                                <span>Kategori Obat: <?= $this->M_global->getData('m_kategori', ['kode_kategori' => $barang2->kode_kategori])->keterangan ?></span>
                                                <br>
                                                <span>Satuan Obat: <?= $this->M_global->getData('m_satuan', ['kode_satuan' => $barang2->kode_satuan])->keterangan ?></span>
                                                <br>
                                                <span>Harga Satuan: Rp. <?= number_format($barang2->harga_jual) ?></span>
                                                <br>
                                                <span>Terjual: <?= ((!empty($stok_barang)) ? number_format((int)$stok_barang->keluar) : 0) ?></span>
                                            </p>
                                            <p class="card-text"><small class="text-muted">Stok tersedia: <?= ((!empty($stok_barang)) ? number_format((int)$stok_barang->akhir) : 0) ?></small></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-6">
                                            <button type="button" class="btn btn-info"><ion-icon name="star-outline"></ion-icon> Lihat Ulasan</button>
                                        </div>
                                        <div class="col-md-6 col-6">
                                            <button type="button" class="btn btn-warning" onclick="modal_promo()"><ion-icon name="gift-outline"></ion-icon> Pakai Promo</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div>Atur Jumlah Pembelian</div>
                                            <div class="row mb-3">
                                                <div class="col-sm-12">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text bg-danger" onclick="min()"><i class="fa-solid fa-minus"></i></div>
                                                        </div>
                                                        <input type="text" class="form-control text-right" id="qty" value="1.00" min="1" name="qty" onchange="cekqty(this.value)">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text bg-primary" onclick="plus('<?= ((!empty($stok_barang)) ? ((int)$stok_barang->akhir) : 0) ?>')"><i class="fa-solid fa-plus"></i></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <textarea name="catatan" id="catatan" class="form-control" style="resize: none;" placeholder="Catatan..."></textarea>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <label for="">Subtotal</label>
                                                </div>
                                                <div class="col-md-12 h3 text-right">
                                                    <input type="hidden" name="harga_jual" id="harga_jual" value="<?= $barang2->harga_jual ?>">
                                                    Rp. <span id="hargajual"><?= number_format($barang2->harga_jual, 2); ?></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button type="button" class="btn btn-primary w-100" <?= $this->data['disabled'] ?> onclick="pesan()">Pesan</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center" id="body-card">
        <?php foreach ($barang as $b) : ?>
            <div class="col-md-3 col-6 pb-3" onclick="getUrl('App/detail/<?= $b->kode_barang ?>')">
                <div class="card h-100" data-aos="fade-up" title="<?= $b->nama ?>" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="<?= $b->nama ?>">
                    <div class="card-header" style="background-color: #b1bdc8;">
                        <span style="font-size: 14px;"><?= mb_strimwidth($b->nama, 0, 22, "..."); ?></span>
                    </div>
                    <div class="card-body">
                        <img src="<?= base_url('assets/img/obat/') . $b->image ?>" class="card-img-top" style="width: 100%; height: 200px; object-fit: cover;">
                    </div>
                    <div class="card-footer">
                        <div style="font-size: 12px;">
                            <span>Rp.<?= number_format($b->harga_jual, 2) ?></span>
                            <?php
                            $terjual = $this->db->query("SELECT SUM(keluar) AS qty, SUM(akhir) AS stok FROM barang_stok WHERE kode_barang = '$b->kode_barang'")->row();

                            $kat = $this->M_global->getData('m_kategori', ['kode_kategori' => $b->kode_kategori]);
                            if ($kat->keterangan == 'Biru') {
                                $color = 'blue';
                            } else if ($kat->keterangan == 'Hijau') {
                                $color = 'green';
                            } else if ($kat->keterangan == 'Merah') {
                                $color = 'red';
                            } else if ($kat->keterangan == 'Abu-abu') {
                                $color = 'grey';
                            } else if ($kat->keterangan == 'Hitam') {
                                $color = 'black';
                            } else {
                                $color = 'white';
                            }
                            ?>
                            <br>
                            <span>Stok: <?= (($terjual) ? number_format((int)$terjual->stok) : '-') . ' ' . $this->M_global->getData('m_satuan', ['kode_satuan' => $b->kode_satuan])->keterangan ?></span>
                            <br>
                            <span>Terjual: <?= (($terjual) ? number_format((int)$terjual->qty) : '-') ?> <span class="float-right" style="height: 12px; width: 12px; background-color: <?= $color; ?>; border-radius: 50%; display: inline-block;"></span></span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- modal semua promo -->
    <div class="modal fade" id="m_promo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"># Pemakaian Promo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="tutupModal()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <input type="hidden" name="jumPromo" id="jumPromo" value="0">
                                <table class="table table-striped table-hover table-bordered" id="tablePromo" style="width: 100%;">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="5%">Hapus</th>
                                            <th width="95%">Promo</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyPromo"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-success" onclick="tambahPromo()"><ion-icon name="add-circle-outline"></ion-icon> Tambah Promo</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>

<script>
    const form = $('#form_cart');

    // fungsi modal promo show
    function modal_promo() {
        $('#m_promo').modal('show');
    }

    // fungsi tutup modal
    function tutupModal() {
        $('#m_promo').modal('hide');
    }

    // fungsi tambah promo
    function tambahPromo() {
        var bodyPromo = $('#bodyPromo');

        var jumPromo = Number($('#jumPromo').val());
        var x = jumPromo + 1;
        bodyPromo.append(`<tr id="rowPromo${x}">
            <td><button type="button" class="btn btn-danger" onclick="delRow('${x}')"><ion-icon name="close-circle-outline"></ion-icon></button></td>
            <td><select id="kode_promo${x}" name="kode_promo[]" class="form-control select2_promo" data-placeholder="~ Pilih Promo"><option value="">~ Pilih Promo</option></select></td>
        </tr>`);

        $('#jumPromo').val(x);

        initailizeSelect2_promo();
    }

    // fungsi hapus promo
    function delRow(x) {
        $('#rowPromo' + x).remove();
    }

    // fungsi cari
    function cari() {
        var kode_kategori = $("#kode_kategori").val();
        var params = ($('#search').val()).toLowerCase();
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("body-card").innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", "<?= base_url('App/pencarian2/'); ?>" + params + '?kode_kategori=' + kode_kategori + '&selain=<?= $barang2->kode_barang ?>', true);
        xhttp.send();
    }

    function cekqty(qty) {
        var hargajual = $("#harga_jual").val();
        if (qty <= 1) {
            $("#qty").val('1.00');
            $("#hargajual").text(formatRpNoId(hargajual * 1));
            $("#btnpesan").attr("disabled", true);
            return;
        } else {
            $("#qty").val(formatRpNoId(qty));
            $("#hargajual").text(formatRpNoId(hargajual * (qty)));
        }

        if ((hargajual * (qty)) <= 1) {
            $("#qty").val('1.00');
            $("#hargajual").text(formatRpNoId(hargajual * 0));
            $("#btnpesan").attr("disabled", true);
        } else {
            $("#qty").val(formatRpNoId(qty));
            $("#hargajual").text(formatRpNoId(hargajual * (qty)));
        }
    }

    function min() {
        var hargajual = $("#harga_jual").val();
        var qtyx = $("#qty").val();
        var qty = Number(parseInt(qtyx.replaceAll(',', '')));
        if (qty <= 1) {
            $("#qty").val('1.00');
            $("#hargajual").text(formatRpNoId(hargajual * 1));
            $("#btnpesan").attr("disabled", true);
            return;
        } else {
            $("#qty").val(formatRpNoId(qty - 1));
            $("#hargajual").text(formatRpNoId(hargajual * (qty - 1)));
        }

        if ((hargajual * (qty - 1)) <= 1) {
            $("#qty").val('1.00');
            $("#hargajual").text(formatRpNoId(hargajual * 0));
            $("#btnpesan").attr("disabled", true);
        } else {
            $("#qty").val(formatRpNoId(qty - 1));
            $("#hargajual").text(formatRpNoId(hargajual * (qty - 1)));
        }
    }

    function plus(saldo_akhir) {
        var hargajual = $("#harga_jual").val();
        var qtyx = $("#qty").val();
        var qty = Number(parseInt(qtyx.replaceAll(',', '')));
        if ((qty + 1) > saldo_akhir) {
            Swal.fire({
                icon: 'error',
                title: 'SISA STOK',
                text: 'Tersisa ' + formatRpNoId(saldo_akhir) + ', qty melebihi batas!',
            });
            $("#qty").val(formatRpNoId(saldo_akhir));
            $("#hargajual").text(formatRpNoId(hargajual * (saldo_akhir)));
        } else {
            $("#qty").val(formatRpNoId(qty + 1));
            $("#hargajual").text(formatRpNoId(hargajual * (qty + 1)));
        }
    }

    var graph_detail_barang = document.getElementById("graph_detail_barang").getContext('2d');

    var myLineChart = new Chart(graph_detail_barang, {
        type: 'bar',
        data: {
            labels: [
                'Masuk', 'Keluar'
            ],
            datasets: [{
                label: "Grafik Stok Obat <?= $this->M_global->getData('barang', ['kode_barang' => $barang2->kode_barang])->nama ?>",
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: [
                    <?= (!empty($stok_barang) ? $stok_barang->masuk : 0) ?>, <?= ((!empty($stok_barang)) ? $stok_barang->keluar : 0) ?>
                ],
            }],
        }
    });

    // fungsi pesan obat
    function pesan() {
        var qty = ($('#qty').val()).replaceAll(',', '');
        var kode_barang = '<?= $barang2->kode_barang; ?>';
        var saldo_akhir = '<?= $stok_barang->akhir; ?>';

        if (qty > saldo_akhir) {
            $('#qty').val(formatRpNoId(saldo_akhir));
            cekqty(saldo_akhir);

            return Swal.fire("Qty", "Melebihi saldo akhir!", "info");
        }

        // jalankan fungsi
        $.ajax({
            url: siteUrl + 'App/pesan_proses/<?= $barang2->kode_barang ?>',
            type: 'POST',
            dataType: 'JSON',
            data: form.serialize(),
            success: function(result) {
                if (result.status == 1) {
                    Swal.fire("Obat", "Berhasil dimasukan ke keranjang!", "success");
                } else {
                    Swal.fire("Obat", "Gagal dimasukan ke keranjang!", "info");
                }
            },
            error: function(result) {
                error_proccess();
            }
        });
    }
</script>