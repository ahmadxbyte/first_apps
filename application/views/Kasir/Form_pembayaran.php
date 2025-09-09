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

<form method="post" id="form_pembayaran">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary" <?= $style ?>>
                <div class="card-header">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Formulir Pembayaran</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="invoice" class="control-label text-danger">Invoice</label>
                                    <input type="hidden" name="token_pembayaran" id="token_pembayaran" value="<?= (!empty($data_pembayaran) ? $data_pembayaran->token_pembayaran : '') ?>">
                                    <input type="text" class="form-control" placeholder="Otomatis" id="invoice" name="invoice" value="<?= (!empty($data_pembayaran) ? $data_pembayaran->invoice : '') ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="" class="control-label">Waktu Bayar</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="date" class="form-control" placeholder="Tgl Pembayaran" id="tgl_pembayaran" name="tgl_pembayaran" value="<?= (!empty($data_pembayaran) ? date('Y-m-d', strtotime($data_pembayaran->tgl_pembayaran)) : date('Y-m-d')) ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" placeholder="Jam Pembayaran" id="jam_pembayaran" name="jam_pembayaran" value="<?= (!empty($data_pembayaran) ? date('H:i:s', strtotime($data_pembayaran->jam_pembayaran)) : date('H:i:s')) ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="no_trx" class="control-label">Pendaftaran</label>
                                    <select name="no_trx" id="no_trx" class="form-control select2_global" data-placeholder="~ Pilih Pendaftaran" onchange="getPendaftaran(this.value)">
                                        <option value="">~ Pilih Pendaftaran</option>
                                        <?php foreach ($pendaftaran as $p): ?>
                                            <option value="<?= $p->no_trx ?>" <?= (!empty($pendaftaran2) ? (($p->no_trx == $pendaftaran2->no_trx) ? 'selected' : '') : ((!empty($data_pembayaran) ? (($data_pembayaran->no_trx == $p->no_trx) ? 'selected' : '') : ''))) ?>><?= $p->no_trx . ' | Tgl/Jam: ' . $p->tgl_daftar . '/' . $p->jam_daftar . ' | Poli/Dokter: ' . $this->M_global->getData('m_poli', ['kode_poli' => $p->kode_poli])->keterangan . '/' . $this->M_global->getData('dokter', ['kode_dokter' => $p->kode_dokter])->nama ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="inv_jual" class="control-label">Penjualan</label>
                                    <select name="inv_jual" id="inv_jual" class="form-control select2_global" data-placeholder="~ Pilih Penjualan" onchange="cekJual(this.value)">
                                        <option value="">~ Pilih Penjualan</option>
                                        <?php foreach ($data_penjualan as $dp): ?>
                                            <option value="<?= $dp->invoice ?>" <?= (!empty($data_pembayaran) ? (($data_pembayaran->inv_jual == $dp->invoice) ? 'selected' : '') : '') ?>><?= $dp->invoice . ' ~ Kode Member: ' . $dp->kode_member . ' | Nama Member: ' . $this->M_global->getData('member', ['kode_member' => $dp->kode_member])->nama ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="kasir" class="control-label">Kasir</label>
                                    <input type="text" class="form-control font-weight-bold" placeholder="Kasir" id="kasir" name="kasir" value="<?= $this->M_global->getData('user', ['kode_user' => $this->session->userdata('kode_user')])->nama ?>" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="shift">Shift</label>
                                    <input type="text" class="form-control font-weight-bold" placeholder="shift" id="shift" name="shift" value="<?= $this->session->userdata('shift') ?>" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="kode_promo" class="control-label">Promo</label>
                                    <select name="kode_promo" id="kode_promo" class="form-control select2_global" data-placeholder="~ Pilih Promo" onchange="cek_promo(this.value)">
                                        <option value="">~ Pilih Promo</option>
                                        <?php foreach ($promo as $p) : ?>
                                            <option value="<?= $p->kode_promo ?>" <?= (!empty($data_pembayaran) ? (($data_pembayaran->kode_promo == $p->kode_promo) ? 'selected' : '') : '') ?>><?= $p->nama ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="potongan_promo" class="control-label">Potongan Promo (%/Rp)</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control text-right" placeholder="Potongan Promo" id="potongan_promo" name="potongan_promo" value="<?= (!empty($data_pembayaran) ? number_format($data_pembayaran->discpr_promo) : '0') ?>" readonly>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control text-right" placeholder="Potongan Promo" id="potongan_promo_rp" name="potongan_promo_rp" value="<?= (!empty($data_pembayaran) ? number_format($data_pembayaran->discrp_promo) : '0') ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="" class="font-weight-bold control-label">Jenis Bayar</label>
                                    <input type="hidden" name="kelas" id="kelas" value="">
                                    <input type="hidden" name="kode_poli" id="kode_poli" value="">
                                    <select name="kode_jenis_bayar" id="kode_jenis_bayar" class="form-control select2_jenis_bayar" data-placeholder="~ Pilih Jenis Bayar" onchange="ubahJenisBayar(this.value)">
                                        <?php
                                        if (!empty($data_pembayaran)) :
                                            $jenis_bayar = $this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $data_pembayaran->kode_jenis_bayar]);
                                            echo '<option value="' . $jenis_bayar->kode_jenis_bayar . '">' . $jenis_bayar->keterangan . '</option>';
                                        else :
                                            echo '<option value="JB00000001">Perorangan</option>';
                                        endif;
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="" class="text-primary font-weight-bold">Tercover</label>
                                    <input type="text" class="form-control text-right text-primary font-weight-bold" placeholder="Pembayaran Dicover" id="tercover" name="tercover" value="<?= (!empty($data_pembayaran) ? number_format($data_pembayaran->tercover) : '0') ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-outline card-primary" style="background-color: rgba(255, 255, 255, 0.1); -webkit-backdrop-filter: blur(1px); backdrop-filter: blur(1px);">
                                <div class="card-header">
                                    <span class="h5">Data Pasien</span>
                                </div>
                                <div class="card-body">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td style="width: 20%;">No RM</td>
                                            <td style="width: 5%;"> : </td>
                                            <td style="width: 75%;">
                                                <span id="norm_px"></span>
                                                <input type="hidden" id="kode_member" name="kode_member">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%;">Nama</td>
                                            <td style="width: 5%;"> : </td>
                                            <td style="width: 75%;">
                                                <span id="nama_px"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%;">Umur</td>
                                            <td style="width: 5%;"> : </td>
                                            <td style="width: 75%;">
                                                <span id="umur_px"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%;">Phone</td>
                                            <td style="width: 5%;"> : </td>
                                            <td style="width: 75%;">
                                                <span id="nohp_px"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%;">Poli</td>
                                            <td style="width: 5%;"> : </td>
                                            <td style="width: 75%;">
                                                <span id="poli_px"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%;">Dokter</td>
                                            <td style="width: 5%;"> : </td>
                                            <td style="width: 75%;">
                                                <span id="dokter_px"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 20%;">Alamat Pasien</td>
                                            <td style="width: 5%;"> : </td>
                                            <td style="width: 75%;">
                                                <span id="alamat_px"></span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Resep Obat</span>
                </div>
                <div class="card-body">
                    <input type="hidden" name="sumJual" id="sumJual" value="<?= (!empty($data_pembayaran) ? $data_pembayaran->jual : 0) ?>">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table  table-striped table-bordered" id="tableJual" width="100%">
                                    <thead>
                                        <tr class="text-center">
                                            <th style="width: 5%;">No</th>
                                            <th style="width: 25%;">Barang</th>
                                            <th style="width: 10%;">Satuan</th>
                                            <th style="width: 10%;">Qty</th>
                                            <th style="width: 10%;">Harga</th>
                                            <th style="width: 10%;">Disc</th>
                                            <th style="width: 10%;">Pajak</th>
                                            <th style="width: 10%;">Jumlah</th>
                                            <th style="width: 10%;">Signa</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyJual"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <span class="font-weight-bold h4"><i class="fa-solid fa-bookmark text-primary"></i> Tindakan Tarif</span>
                </div>
                <div class="card-body">
                    <input type="hidden" name="discTarif" id="discTarif" value="<?= (!empty($data_pembayaran) ? $data_pembayaran->disc_single : 0) ?>">
                    <input type="hidden" name="sumTarif" id="sumTarif" value="<?= (!empty($data_pembayaran) ? $data_pembayaran->single : 0) ?>">
                    <input type="hidden" name="sumPaket" id="sumPaket" value="<?= (!empty($data_pembayaran) ? $data_pembayaran->paket : 0) ?>">
                    <div class="row">
                        <div class="col-md-7 col-12">
                            <input type="hidden" id="forRowTarif" name="forRowTarif" value="<?= (!empty($single_tarif) ? count($single_tarif) : 1) ?>">
                            <div class="table-responsive">
                                <table class="table shadow-sm table-striped table-bordered" id="tableTarifSingle" width="100%" style="border-radius: 10px;">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="5%">Hapus</th>
                                            <th width="35%">Tindakan</th>
                                            <th width="15%">Harga</th>
                                            <th width="15%">Disc (%)</th>
                                            <th width="15%">Disc (Rp)</th>
                                            <th width="15%">Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyTarif">
                                        <?php if (!empty($single_tarif)) : ?>
                                            <?php $not = 1;
                                            foreach ($single_tarif as $st) :
                                                $tsingle = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $st->kode_tarif]);
                                            ?>
                                                <tr id="rowTarif<?= $not ?>">
                                                    <td>
                                                        <button type="button" class="btn btn-danger" onclick="hapusTindakanTarif('<?= $not ?>')">
                                                            <i class="fa-solid fa-delete-left"></i>
                                                        </button>
                                                    </td>
                                                    <td>
                                                        <select name="kode_tarif_single[]" id="kode_tarif_single<?= $not ?>" class="form-control select2_tindakan_single" data-placeholder="~ Pilih Tarif" onchange="getTarifSingle(this.value, '<?= $not ?>')">
                                                            <option value="<?= $st->kode_tarif ?>"><?= $tsingle->keterangan ?></option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control text-right" name="jasa_total[]" id="jasa_total<?= $not ?>" value="<?= number_format($st->harga) ?>" readonly>
                                                        <input type="hidden" class="form-control text-right" name="jasa_rs[]" id="jasa_rs<?= $not ?>" value="0" readonly>
                                                        <input type="hidden" class="form-control text-right" name="jasa_dokter[]" id="jasa_dokter<?= $not ?>" value="0" readonly>
                                                        <input type="hidden" class="form-control text-right" name="jasa_pelayanan[]" id="jasa_pelayanan<?= $not ?>" value="0" readonly>
                                                        <input type="hidden" class="form-control text-right" name="jasa_poli[]" id="jasa_poli<?= $not ?>" value="0" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control text-right" name="discpr_tarif[]" id="discpr_tarif<?= $not ?>" value="<?= number_format($st->discpr) ?>" onchange="changediscpr(this.value, '<?= $not ?>')">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control text-right" name="discrp_tarif[]" id="discrp_tarif<?= $not ?>" value="<?= number_format($st->discrp) ?>" onchange="changediscrp(this.value, '<?= $not ?>')">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control text-right" name="jumlah_tarif[]" id="jumlah_tarif<?= $not ?>" value="<?= number_format($st->jumlah) ?>" readonly>
                                                    </td>
                                                </tr>
                                            <?php $not++;
                                            endforeach; ?>
                                        <?php else : ?>
                                            <tr id="rowTarif1">
                                                <td>
                                                    <button type="button" class="btn btn-danger" onclick="hapusTindakanTarif('1')">
                                                        <i class="fa-solid fa-delete-left"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    <select name="kode_tarif_single[]" id="kode_tarif_single1" class="form-control select2_tindakan_single" data-placeholder="~ Pilih Tarif" onchange="getTarifSingle(this.value, '1')"></select>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control text-right" name="jasa_total[]" id="jasa_total1" value="0" readonly>
                                                    <input type="hidden" class="form-control text-right" name="jasa_rs[]" id="jasa_rs1" value="0" readonly>
                                                    <input type="hidden" class="form-control text-right" name="jasa_dokter[]" id="jasa_dokter1" value="0" readonly>
                                                    <input type="hidden" class="form-control text-right" name="jasa_pelayanan[]" id="jasa_pelayanan1" value="0" readonly>
                                                    <input type="hidden" class="form-control text-right" name="jasa_poli[]" id="jasa_poli1" value="0" readonly>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control text-right" name="discpr_tarif[]" id="discpr_tarif1" value="0" onchange="changediscpr(this.value, 1)">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control text-right" name="discrp_tarif[]" id="discrp_tarif1" value="0" onchange="changediscrp(this.value, 1)">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control text-right" name="jumlah_tarif[]" id="jumlah_tarif1" value="0" readonly>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="tambahTarif()" id="btnTambahTarif"><i class="fa-solid fa-folder-plus"></i> Tambah Tarif</button>
                        </div>
                        <div class="col-md-5 col-12">
                            <div class="table-responsive">
                                <table class="table shadow-sm table-striped table-bordered" width="100%" style="border-radius: 10px;">
                                    <thead>
                                        <tr class="text-center">
                                            <th width="60%">Paket</th>
                                            <th width="20%">Kunjungan</th>
                                            <th width="20%">Harga</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyPaket">
                                        <?php if (!empty($tarif_paket)) : ?>
                                            <?php $nop = 1;
                                            foreach ($tarif_paket as $tp) :
                                                $m_tarif = $this->M_global->getData('m_tarif', ['kode_tarif' => $tp->kode_tarif]);
                                                $tarif = $this->M_global->getData('tarif_paket', ['kode_tarif' => $tp->kode_tarif, 'kunjungan' => $tp->kunjungan]);
                                            ?>
                                                <tr id="rowPaket<?= $nop ?>">
                                                    <td>
                                                        <input type="hidden" name="kode_tarif[]" id="kode_tarif<?= $nop ?>" value="<?= $tp->kode_tarif ?>">
                                                        <input type="text" class="form-control" readonly name="kode_tarifx[]" id="kode_tarifx<?= $nop ?>" value="<?= $m_tarif->nama ?>">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control text-center" readonly name="kunjungan[]" id="kunjungan<?= $nop ?>" value="<?= $tp->kunjungan ?>">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control text-right" readonly name="harga[]" id="harga<?= $nop ?>" value="<?= number_format($tarif->jasa_rs + $tarif->jasa_dokter + $tarif->jasa_pelayanan + $tarif->jasa_poli) ?>">
                                                    </td>
                                                </tr>
                                            <?php $nop++;
                                            endforeach; ?>
                                        <?php endif ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body" id="bodyPerorangan">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="jenis_pembayaran" class="control-label text-danger">Jenis Pembayaran</label>
                            <input type="hidden" name="jenis_pembayaran" id="jenis_pembayaran" value="<?= (!empty($data_pembayaran) ? $data_pembayaran->jenis_pembayaran : 0) ?>">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-6 col-6">
                                            <input type="checkbox" id="cek_cash" name="cek_cash" class="form-control" onclick="cek_cc(0)" <?= (!empty($data_pembayaran) ? (($data_pembayaran->jenis_pembayaran == 0) ? 'checked' : '') : '') ?>>
                                        </div>
                                        <div class="col-md-6 col-6 my-auto">Cash</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-6 col-6">
                                            <input type="checkbox" id="cek_card" name="cek_card" class="form-control" onclick="cek_cc(1)" <?= (!empty($data_pembayaran) ? (($data_pembayaran->jenis_pembayaran == 1) ? 'checked' : '') : '') ?>>
                                        </div>
                                        <div class="col-md-6 col-6 my-auto">Card</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row">
                                        <div class="col-md-6 col-6">
                                            <input type="checkbox" id="cek_cash_card" name="cek_cash_card" class="form-control" onclick="cek_cc(2)" <?= (!empty($data_pembayaran) ? (($data_pembayaran->jenis_pembayaran == 2) ? 'checked' : '') : '') ?>>
                                        </div>
                                        <div class="col-md-6 col-6 my-auto">Cash + Card</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12" id="fortableCash">
                                    <label for="cash" class="control-label">Cash</label>
                                    <input type="text" class="form-control text-right" placeholder="Pembayaran Cash" id="cash" name="cash" value="<?= (!empty($data_pembayaran) ? number_format($data_pembayaran->cash) : '0') ?>" onkeyup="hitung_bayar()">
                                </div>
                            </div>
                            <br>
                            <div class="row" id="fortableCard">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table shadow-sm table-hover table-bordered" id="tableBayarCard" width="100%" style="border-radius: 10px;">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th style="width: 5%;" style="border-radius: 10px 0px 0px 0px;">Hapus</th>
                                                            <th style="width: 15%;">Bank</th>
                                                            <th style="width: 10%;">Tipe</th>
                                                            <th style="width: 20%;">No. Kartu</th>
                                                            <th style="width: 20%;">Approval</th>
                                                            <th style="width: 20%;" style="border-radius: 0px 10px 0px 0px;">Pembayaran</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="bodyBayarCard">
                                                        <?php if (!empty($bayar_detail)) : ?>
                                                            <?php $no = 1;
                                                            foreach ($bayar_detail as $bd) : ?>
                                                                <tr id="rowCard<?= $no ?>">
                                                                    <td>
                                                                        <button type="button" class="btn btn-dark" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tooltip on bottom" title="Hapus" onclick="hapusBaris(<?= $no ?>)"><i class="fa-solid fa-delete-left"></i></button>
                                                                    </td>
                                                                    <td>
                                                                        <select name="kode_bank[]" id="kode_bank<?= $no ?>" class="select2_bank" data-placeholder="~ Pilih Bank">
                                                                            <option value="<?= $bd->kode_bank ?>"><?= $this->M_global->getData('m_bank', ['kode_bank' => $bd->kode_bank])->keterangan; ?></option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <select name="tipe_bank[]" id="tipe_bank<?= $no ?>" class="select2_tipe_bank" data-placeholder="~ Pilih Tipe Bank">
                                                                            <option value="<?= $bd->kode_tipe ?>"><?= $this->M_global->getData('tipe_bank', ['kode_tipe' => $bd->kode_tipe])->keterangan; ?></option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="no_card[]" id="no_card<?= $no ?>" class="form-control" maxlength="16" value="<?= $bd->no_card ?>">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="approval[]" id="approval<?= $no ?>" class="form-control" maxlength="6" value="<?= $bd->approval ?>">
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="jumlah_card[]" id="jumlah_card<?= $no ?>" class="form-control text-right" value="<?= number_format($bd->jumlah) ?>" onkeyup="hitung_card(<?= $no ?>); formatRp(this.value, 'jumlah_card1')">
                                                                    </td>
                                                                </tr>
                                                            <?php $no++;
                                                            endforeach; ?>
                                                        <?php else : ?>
                                                            <tr id="rowCard1">
                                                                <td>
                                                                    <button type="button" class="btn btn-dark" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tooltip on bottom" title="Hapus" onclick="hapusBaris(1)"><i class="fa-solid fa-delete-left"></i></button>
                                                                </td>
                                                                <td>
                                                                    <select name="kode_bank[]" id="kode_bank1" class="select2_bank" data-placeholder="~ Pilih Bank"></select>
                                                                </td>
                                                                <td>
                                                                    <select name="tipe_bank[]" id="tipe_bank1" class="select2_tipe_bank" data-placeholder="~ Pilih Tipe Bank"></select>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="no_card[]" id="no_card1" class="form-control" maxlength="16">
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="approval[]" id="approval1" class="form-control" maxlength="6">
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="jumlah_card[]" id="jumlah_card1" class="form-control text-right" value="0" onkeyup="hitung_card(1); formatRp(this.value, 'jumlah_card1')">
                                                                </td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="hidden" class="form-control" id="jumCard" value="<?= (!empty($bayar_detail) ? count($bayar_detail) : '1') ?>">
                                            <button type="button" class="btn btn-primary" onclick="tambah_card()"><i class="fa-solid fa-folder-plus"></i> Tambah Card</button>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <div class="row">
                                                <label for="" class="control-label col-md-3 my-auto">Total Card</label>
                                                <div class="col-md-9">
                                                    <input type="text" class="form-control text-right" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tooltip on bottom" title="Total Card" placeholder="Total Card" id="card" name="card" value="<?= (!empty($data_pembayaran) ? number_format($data_pembayaran->card) : '0') ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="not_umum">
                            <div class="row not_umum">
                                <div class="col-md-6 col-6">
                                    <label for="" class="text-success font-weight-bold control-label">Uang Muka Tersedia</label>
                                    <input type="text" class="form-control text-right text-primary font-weight-bold" placeholder="Uang Muka Tersedia" id="uang_sisa" name="uang_sisa" value="0" readonly>
                                </div>
                                <div class="col-md-6 col-6">
                                    <label for="" class="font-weight-bold control-label">Uang Muka Pakai</label>
                                    <input type="text" class="form-control text-right" placeholder="Uang Muka Pakai" id="um_keluar" name="um_keluar" value="<?= (!empty($data_pembayaran) ? number_format($data_pembayaran->um_keluar) : '0') ?>" onkeyup="pakai_um()">
                                </div>
                            </div>
                            <hr class="not_umum">
                            <div class="row not_umum">
                                <div class="col-md-12">
                                    <label for="" class="control-label">Deposit Ke Uang Muka</label>
                                    <div class="row mb-3">
                                        <div class="col-md-1 col-2 mt-auto">
                                            <input type="checkbox" name="cek_umx" id="cek_umx" class="form-control" onclick="cek_um_in()" <?= (!empty($data_pembayaran) ? (($data_pembayaran->cek_um == 1) ? 'checked' : '') : '') ?>>
                                            <input type="hidden" name="cek_um" id="cek_um" class="form-control" value="<?= (!empty($data_pembayaran) ? $data_pembayaran->cek_um : '0') ?>">
                                        </div>
                                        <div class="col-md-11 col-10 ms-auto font-weight-bold">
                                            <span>Keterangan: </span>
                                            <br>
                                            <span class="text-danger">Hasil kembalian Pasien akan masuk kedalam uang muka, untuk di depositkan!</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="" class="text-danger font-weight-bold control-label">Total Pembayaran</label>
                                    <input type="text" class="form-control text-right text-primary font-weight-bold" placeholder="Pembayaran Total" id="total" name="total" value="<?= (!empty($data_pembayaran) ? number_format($data_pembayaran->total) : '0') ?>" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="Kekurangan" class="control-label">Kembali ke pasien <?= (!empty($data_pembayaran) ? (($data_pembayaran->cek_um == 1) ? '<span class="badge badge-primary">Masuk Ke Uang Muka</span>' : '') : '') ?></label>
                                    <input type="text" class="form-control text-right font-weight-bold" placeholder="Kekurangan" id="total_kurang" name="total_kurang" value="<?= (!empty($data_pembayaran) ? number_format(($data_pembayaran->cek_um > 0) ? $data_pembayaran->um_masuk : $data_pembayaran->kembalian) : '0') ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div id="daftar_ulang">
                                <div class="card card-outline card-danger shadow" <?= $style ?>>
                                    <div class="card-header">
                                        <span class="h5">Daftar ulang</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <label for="tgl_ulang" class="form-label col-md-3 control-label">Tanggal</label>
                                            <div class="col-md-9">
                                                <input type="date" name="tgl_ulang" id="tgl_ulang" class="form-control" min="<?= date('Y-m-d', strtotime('+1 Day')) ?>" value="<?= ((!empty($ulang) ? date('Y-m-d', strtotime($ulang->tgl_ulang)) : date('Y-m-d', strtotime('+1 Day'))))  ?>">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="status_ulang" class="form-label col-md-3 control-label">Status</label>
                                            <div class="col-md-9">
                                                <select name="status_ulang" id="status_ulang" class="form-control select2_global" data-placeholder="- Status Pendaftaran Ulang">
                                                    <option value="0" <?= (!empty($ulang) ? (($ulang->status_ulang == 0) ? 'selected' : '') : 'selected') ?>>Tidak</option>
                                                    <option value="1" <?= (!empty($ulang) ? (($ulang->status_ulang == 1) ? 'selected' : '') : '') ?>>Ya</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-danger" onclick="getUrl('Kasir')" id="btnKembali"><i class="fa-solid fa-circle-chevron-left"></i>&nbsp;&nbsp;Kembali</button>
                            <button type="button" class="btn btn-success float-right ml-2" onclick="save()" id="btnSimpan"><i class="fa-regular fa-hard-drive"></i>&nbsp;&nbsp;Proses</button>
                            <?php if (!empty($data_pembayaran)) : ?>
                                <button type="button" class="btn btn-info float-right" onclick="getUrl('Kasir/form_kasir/0')" id="btnBaru"><i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Tambah</button>
                            <?php else : ?>
                                <button type="button" class="btn btn-info float-right" onclick="reset()" id="btnReset"><i class="fa-solid fa-arrows-rotate"></i>&nbsp;&nbsp;Reset</button>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    var token_pembayaran = $('#token_pembayaran');
    var invoice = $('#invoice');
    var tgl_pembayaran = $('#tgl_pembayaran');
    var jam_pembayaran = $('#jam_pembayaran');
    var inv_jual = $('#inv_jual');
    var kode_promo = $('#kode_promo');
    var potongan_promo = $('#potongan_promo');
    var potongan_promo_rp = $('#potongan_promo_rp');
    var fortableCard = $('#fortableCard');
    var fortableCash = $('#fortableCash');
    var bodyCard = $('#bodyBayarCard');
    var bodyNonPerorangan = $('#bodyNonPerorangan');
    var bodyPerorangan = $('#bodyPerorangan');

    const btnSimpan = $('#btnSimpan');
    const form = $('#form_pembayaran');
    const forJual = $('#forJual');
    const bodyPaket = $('#bodyPaket');
    const bodyTarif = $('#bodyTarif');

    $('.forPaket').hide();
    $('#daftar_ulang').hide();

    forJual.show();
    display_ct_kasir();

    <?php if (!empty($data_pembayaran)) :  ?>
        kode_promo.attr('disabled', false);
        cek_promo('<?= $data_pembayaran->kode_promo ?>')

        <?php if ($this->M_global->getData('barang_out_header', ['invoice' => $data_pembayaran->inv_jual])) : ?>
            <?php if ($this->M_global->getData('barang_out_header', ['invoice' => $data_pembayaran->inv_jual])->kode_member == 'U00001') : ?>
                $('.not_umum').hide();
            <?php endif; ?>
        <?php else : ?>
            <?php if ($this->M_global->getData('barang_out_header', ['invoice' => $data_pembayaran->inv_jual])) : ?>
                getDataPx('<?= $this->M_global->getData('barang_out_header', ['invoice' => $data_pembayaran->inv_jual])->kode_member ?>', '<?= $this->M_global->getData('barang_out_header', ['invoice' => $data_pembayaran->inv_jual])->no_trx ?>', '<?= $data_pembayaran->inv_jual ?>');

                $('.not_umum').show();
            <?php endif; ?>
        <?php endif ?>

        cekPendaftaran('<?= $data_pembayaran->no_trx ?>')

        <?php if (!empty($bayar_detail)) : ?> fortableCard.show();
        <?php else : ?> fortableCard.hide();
        <?php endif; ?>
    <?php else :  ?>
        $('.not_umum').hide();
        kode_promo.attr('disabled', true);
        document.getElementById('cek_cash').checked = true;
        fortableCard.hide();
        bodyNonPerorangan.hide();
    <?php endif;  ?>

    function cek_cc(isi) {
        if (isi == 0) {
            document.getElementById('cek_card').checked = false;
            document.getElementById('cek_cash').checked = true;
            document.getElementById('cek_cash_card').checked = false;

            fortableCash.show(200);
            fortableCard.hide(200);

            $('#card').val(0);
            $('#bodyBayarCard').empty();
            hitung_card_all();
        } else if (isi == 1) {
            document.getElementById('cek_card').checked = true;
            document.getElementById('cek_cash').checked = false;
            document.getElementById('cek_cash_card').checked = false;

            fortableCash.hide(200);
            fortableCard.show(200);

            $('#cash').val(0);
            hitung_bayar(0);
        } else {
            document.getElementById('cek_card').checked = false;
            document.getElementById('cek_cash').checked = false;
            document.getElementById('cek_cash_card').checked = true;

            fortableCash.show(200);
            fortableCard.show(200);
        }

        $('#jenis_pembayaran').val(isi);
    }

    // fungsi cek um
    function cek_um_in() {
        if (document.getElementById('cek_umx').checked == true) {
            $('#cek_um').val(1);
        } else {
            $('#cek_um').val(0);
        }
    }

    function display_ct_kasir() {
        var x = new Date();

        // Mendapatkan jam, menit, dan detik
        var hours = x.getHours();
        var minutes = x.getMinutes();
        var seconds = x.getSeconds();

        // Menampilkan waktu pada elemen dengan id 'time'
        document.getElementById('jam_pembayaran').value = hours + ":" + minutes + ":" + seconds;
        setTimeout(display_ct_kasir, 1000); // Memperbarui setiap detik
    }

    // fungsi cek promo
    function cek_promo(kopro) {
        if (kopro == '' || kopro == null) {
            potongan_promo.val(0);
            potongan_promo_rp.val(0);
            hitung_promo();

            return Swal.fire("Promo", "Form sudah dipilih?", "question");
        }

        $.ajax({
            url: '<?= site_url() ?>Master_show/cek_promo/' + kopro,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) { // jika fungsi berjalan
                if (result.status == 0) { // jika mendapatkan respon 0
                    Swal.fire("Promo", "Tidak ditemukan!, coba lagi", "info");
                } else {
                    potongan_promo.val(Number(result.promo));

                    var total = ($('#total_kurang').val().replaceAll(',', ''));
                    potongan_promo_rp.val(formatRpNoId(Math.floor(Math.abs(Number(total) * (Number(result.promo) / 100)))));

                    hitung_promo();
                }
            },
            error: function(result) { // jika fungsi error
                btnSimpan.attr('disabled', false);

                error_proccess();
            }
        });
    }

    // fungsi hitung promo
    function hitung_promo() {
        var sumTarif = parseFloat(($('#sumTarif').val()).replaceAll(',', ''));
        var sumPaket = parseFloat(($('#sumPaket').val()).replaceAll(',', ''));
        var sumJual = parseFloat(($('#sumJual').val()).replaceAll(',', ''));
        var discpr_prom = parseFloat((potongan_promo.val()).replaceAll(',', ''));

        if ($('#kode_jenis_bayar').val() == 'JB00000001') {
            var new_total_jual = (sumJual + sumPaket + sumTarif) - ((sumJual + sumPaket + sumTarif) * (discpr_prom / 100));
            $('#tercover').val(formatRpNoId(0));
            $('#total').val(formatRpNoId(0));
            $('#total_kurang').val(formatRpNoId(new_total_jual));
        } else {
            var new_total_jual = (sumJual + sumPaket + sumTarif) - ((sumJual + sumPaket + sumTarif) * (discpr_prom / 100));
            $('#tercover').val(formatRpNoId(new_total_jual));
            $('#total').val(formatRpNoId(new_total_jual));
            $('#total_kurang').val(formatRpNoId(0));
        }

        $('#potongan_promo_rp').val(formatRpNoId(Math.floor((sumJual + sumPaket + sumTarif) * (discpr_prom / 100))))
    }

    var cek_param = "<?= $this->input->get('invoice') ?>";

    if (cek_param !== '' && cek_param !== '0') {
        cekPendaftaran(cek_param);
        // alert(cek_param)
    } else if ('<?= $no_trx ?>' != '') {
        cekPendaftaran('<?= $no_trx ?>');
    }

    function cekPendaftaran(param) {
        $.ajax({
            url: '<?= site_url() ?>Kasir/cekPendaftaran/' + param,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if (result.status == 1) {
                    $('#no_trx').val(param).change();
                    var norm_px = result.norm;
                    var notrx_px = param;
                } else {
                    $('#inv_jual').val(param).change();
                    var norm_px = 'U00001';
                    var notrx_px = '';
                }

                $('#jenis_bayar').val(result.jenis_bayar);
                $('#kode_jenis_bayar').val(result.kode_jenis_bayar);

                if (norm_px == 'U00001') {
                    bodyPerorangan.show(200);
                    bodyNonPerorangan.hide(200);
                } else {
                    if (result.kode_jenis_bayar == 'JB00000001') {
                        bodyPerorangan.show(200);
                        bodyNonPerorangan.hide(200);
                    } else {
                        bodyPerorangan.hide(200);
                        bodyNonPerorangan.show(200);
                    }
                }

                getDataPx(norm_px, notrx_px);
            },
            error: function(error) {
                error_proccess();
            }
        });
    }

    function getDataPx(norm, notrx) {
        $.ajax({
            url: '<?= site_url() ?>Kasir/getMember/' + norm + '/' + notrx,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if (result.status == 1) {
                    if (result.cek == 1) {
                        $('#norm_px').text(result.norm);
                        $('#kode_member').val(result.norm);
                        $('#nama_px').text(result.nama);
                        $('#umur_px').text(result.umur);
                        $('#nohp_px').text(result.nohp);
                        $('#poli_px').text(result.poli);
                        $('#dokter_px').text(result.dokter);
                        $('#alamat_px').text(result.alamat);
                        $('#kelas').val(result.kelas);
                        $('#kode_poli').val(result.kode_poli);

                        $('#daftar_ulang').show(200);

                        $('.not_umum').show();
                        // get_um(norm, 0);
                    } else {
                        $('#norm_px').text('U00001');
                        $('#kode_member').val('U00001');
                        $('#nama_px').text('Umum');
                        $('#umur_px').text('-');
                        $('#nohp_px').text('-');
                        $('#poli_px').text('Umum');
                        $('#dokter_px').text('-');
                        $('#alamat_px').text('-');
                        $('#kelas').val('Umum');
                        $('#kode_poli').val('');

                        $('#daftar_ulang').hide(200);
                    }

                } else {
                    Swal.fire({
                        position: "center",
                        icon: "info",
                        title: "Data pasien tidak ditemukan!",
                        showConfirmButton: false,
                        timer: 500
                    });
                }

                hitung_kurang();
            },
            error: function(error) {
                error_proccess();
            }
        });
    }

    function getPendaftaran(notrx) {
        if (notrx == '' || notrx == null) {
            return Swal.fire("Pendaftaran", "Form sudah dipilih?", "info");
        }

        cekJenisBayar(notrx);

        cekPaket(notrx);
        cekTarif(notrx);
    }

    var kd_master = $('#kode_jenis_bayar').val();

    function ubahJenisBayar(kd_jb) {
        if (kd_jb == '' || kd_jb == null) {
            return
        }

        var notrx = $('#no_trx').val();

        $.ajax({
            url: '<?= site_url() ?>Kasir/ubahJenisBayar/' + notrx + '/' + kd_jb,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if (result.status == 1) {
                    var kelas = result.kelas;
                    var kode_poli = result.poli;

                    var param = `?kelas=${kelas}&kode_poli=${kode_poli}&kode_jenis_bayar=${kd_jb}`;

                    $.ajax({
                        url: '<?= site_url() ?>Kasir/ubahHargaTindakan/' + notrx + param,
                        type: 'POST',
                        dataType: 'JSON',
                        success: function(result) {
                            if (result[0]['status'] == 1) { // jika mendapatkan respon 1

                                bodyTarif.empty();
                                var sumTarif = 0;
                                var row = 1;
                                $.each(result[1], function(index, value) {
                                    sumTarif += value.jasa_total;

                                    bodyTarif.append(`<tr id="rowTarif${row}">
                                        <td>
                                            <button type="button" class="btn btn-danger" onclick="hapusTindakanTarif('${row}')">
                                                <i class="fa-solid fa-delete-left"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <select name="kode_tarif_single[]" id="kode_tarif_single${row}" class="form-control select2_tindakan_single" data-placeholder="~ Pilih Tarif" onchange="getTarifSingle(this.value, '${row}')">
                                                <option value="${value.kode_tarif}">${value.nama_tarif}</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control text-right" name="jasa_total[]" id="jasa_total${row}" value="${formatRpNoId(value.jasa_total)}" readonly>
                                            <input type="hidden" class="form-control text-right" name="jasa_rs[]" id="jasa_rs${row}" value="${(value.jasa_rs)}" readonly>
                                            <input type="hidden" class="form-control text-right" name="jasa_dokter[]" id="jasa_dokter${row}" value="${(value.jasa_dokter)}" readonly>
                                            <input type="hidden" class="form-control text-right" name="jasa_pelayanan[]" id="jasa_pelayanan${row}" value="${(value.jasa_pelayanan)}" readonly>
                                            <input type="hidden" class="form-control text-right" name="jasa_poli[]" id="jasa_poli${row}" value="${(value.jasa_poli)}" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control text-right" name="discpr_tarif[]" id="discpr_tarif${row}" value="0" onchange="changediscpr(this.value, '${row}')">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control text-right" name="discrp_tarif[]" id="discrp_tarif${row}" value="0" onchange="changediscrp(this.value, '${row}')">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control text-right" name="jumlah_tarif[]" id="jumlah_tarif${row}" value="${formatRpNoId(value.jasa_total)}" readonly>
                                        </td>
                                    </tr>`);

                                    initailizeSelect2_tindakan_single(kd_jb, kelas, kode_poli);

                                    $('#forRowTarif').val(row);

                                    row++;

                                });

                            } else {
                                var sumTarif = 0;
                            }

                            $('#sumTarif').val(sumTarif);

                            $('#cash').val(0);
                            $('#card').val(0);
                            $('#um_keluar').val(0);

                            hitung_kurang_penjamin();
                        },
                        error: function(error) {
                            $('#kode_jenis_bayar').val(kd_master).change()

                            Swal.fire({
                                title: "Informasi",
                                text: "Harga belum tersedia!, tambahkan terlebih dahulu",
                                icon: "info"
                            });

                            return;
                        }
                    });
                }
            },
            error: function(error) {
                error_proccess();
            }
        });
    }

    function cekJenisBayar(notrx) {
        $.ajax({
            url: '<?= site_url() ?>Kasir/cekJenisBayar/' + notrx,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) { // jika fungsi berjalan dengan baik
                if (result.status == 1) { // jika mendapatkan respon 1
                    $('#kode_jenis_bayar').html('<option value="' + result.kode_jenis_bayar + '">' + result.jenis_bayar + '</option>');

                    if (result.kode_jenis_bayar == 'JB00000001') {
                        bodyPerorangan.show(200);
                        bodyNonPerorangan.hide(200);
                    } else {
                        bodyPerorangan.hide(200);
                        bodyNonPerorangan.show(200);
                    }
                } else {
                    Swal.fire("Pendaftaran", "Tidak ditemukan!, coba lagi", "info");
                }
            },
            error: function(result) { // jika fungsi error
                btnSimpan.attr('disabled', false);

                error_proccess();
            }
        });
    }

    function getDataPx2(inv) {
        $.ajax({
            url: `${siteUrl}Kasir/getDataJual/${inv}`,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if (result.status == 1) {
                    $('#norm_px').text(result.norm);
                    $('#kode_member').val(result.norm);
                    $('#nama_px').text(result.nama);
                    $('#umur_px').text(result.umur);
                    $('#nohp_px').text(result.nohp);
                    $('#poli_px').text(result.poli);
                    $('#dokter_px').text(result.dokter);
                    $('#alamat_px').text(result.alamat);
                } else {
                    Swal.fire({
                        position: "center",
                        icon: "info",
                        title: "Data pasien tidak ditemukan!",
                        showConfirmButton: false,
                        timer: 500
                    });
                }
            },
            error: function(error) {
                error_proccess();
            }
        })
    }

    // fungsi cek jual
    function cekJual(x) {
        if (x == '' || x == null) { // jika x kosong/ null
            return;
        }

        getDataPx2(x);

        // jalankan fungsi
        $.ajax({
            url: '<?= site_url() ?>Kasir/getInfoJual/' + x,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) { // jika fungsi berjalan dengan baik
                if (result.status == 0) { // jika mendapatkan respon 0
                    if (result[1].kode_member == 'U00001') {} else {
                        Swal.fire("Total Penjualan", "Tidak ditemukan!, coba lagi", "info");
                    }

                    $('#sumJual').val(0);

                    hitung_kurang();
                } else { // selain itu
                    $('#sumJual').val(result[0]['total']);

                    getJual(x);
                    hitung_kurang();

                    kode_promo.attr('disabled', false);

                    initailizeSelect2_promo((Number(result[0]['total'])));

                    if (result[1].kode_member == 'U00001') {
                        $('.not_umum').hide();
                    } else {
                        // ambil uangmuka
                        $('.not_umum').show();
                        get_um(result[0].kode_member, '');
                    }
                }
            },
            error: function(result) { // jika fungsi error
                btnSimpan.attr('disabled', false);

                error_proccess();
            }
        });
    }

    function getJual(x) {
        if (!x || x == null) {
            return
        }

        $.ajax({
            url: '<?= site_url() ?>Kasir/getJual/' + x,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                var row = 1;

                $.each(result, function(index, value) {
                    $('#bodyJual').append(`<tr id="rowJual${row}">
                        <td>
                            <span style="font-weight: normal;">${row}</span>
                        </td>
                        <td>
                            <span style="font-weight: normal;">(${value.kode_barang}) ${value.nama_barang}</span>
                        </td>
                        <td>
                            <span style="font-weight: normal;">${value.nama_satuan}</span>
                        </td>
                        <td>
                            <span class="float-right" style="font-weight: normal;">${formatRpNoId(value.qty)}</span>
                        </td>
                        <td>
                            Rp. <span class="float-right" style="font-weight: normal;">${formatRpNoId(value.harga)}</span>
                        </td>
                        <td>
                            Rp. <span class="float-right" style="font-weight: normal;">${formatRpNoId(value.discrp)}</span>
                        </td>
                        <td>
                            Rp. <span class="float-right" style="font-weight: normal;">${formatRpNoId(value.pajakrp)}</span>
                        </td>
                        <td>
                            Rp. <span class="float-right" style="font-weight: normal;">${formatRpNoId((Number(value.jumlah) + Number(value.pajakrp)))}</span>
                        </td>
                        <td>
                            <span style="font-weight: normal;">${value.signa}</span>
                        </td>
                    </tr>`);

                    row++;
                })
            },
            error: function(result) {
                error_proccess();
            }
        });
    }

    function cekTarif(notrx) {
        $.ajax({
            url: '<?= site_url() ?>Kasir/getTarif/' + notrx,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) { // jika fungsi berjalan dengan baik
                if (result[0]['status'] == 1) { // jika mendapatkan respon 1

                    bodyTarif.empty();
                    var sumTarif = 0;
                    var row = 1;
                    $.each(result[1], function(index, value) {
                        sumTarif += value.harga;

                        bodyTarif.append(`<tr id="rowTarif${row}">
                            <td>
                                <button type="button" class="btn btn-danger" onclick="hapusTindakanTarif('${row}')">
                                    <i class="fa-solid fa-delete-left"></i>
                                </button>
                            </td>
                            <td>
                                <select name="kode_tarif_single[]" id="kode_tarif_single${row}" class="form-control select2_tindakan_single" data-placeholder="~ Pilih Tarif" onchange="getTarifSingle(this.value, '${row}')">
                                    <option value="${value.kode_tarif}">${value.nama_tarif}</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control text-right" name="jasa_total[]" id="jasa_total${row}" value="${formatRpNoId(value.harga)}" readonly>
                                <input type="hidden" class="form-control text-right" name="jasa_rs[]" id="jasa_rs${row}" value="${(value.jasa_rs)}" readonly>
                                <input type="hidden" class="form-control text-right" name="jasa_dokter[]" id="jasa_dokter${row}" value="${(value.jasa_dokter)}" readonly>
                                <input type="hidden" class="form-control text-right" name="jasa_pelayanan[]" id="jasa_pelayanan${row}" value="${(value.jasa_pelayanan)}" readonly>
                                <input type="hidden" class="form-control text-right" name="jasa_poli[]" id="jasa_poli${row}" value="${(value.jasa_poli)}" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control text-right" name="discpr_tarif[]" id="discpr_tarif${row}" value="0" onchange="changediscpr(this.value, '${row}')">
                            </td>
                            <td>
                                <input type="text" class="form-control text-right" name="discrp_tarif[]" id="discrp_tarif${row}" value="0" onchange="changediscrp(this.value, '${row}')">
                            </td>
                            <td>
                                <input type="text" class="form-control text-right" name="jumlah_tarif[]" id="jumlah_tarif${row}" value="${formatRpNoId(value.harga)}" readonly>
                            </td>
                        </tr>`);

                        initailizeSelect2_tindakan_single($('#kode_jenis_bayar').val(), $('#kelas').val(), $('#kode_poli').val());

                        $('#forRowTarif').val(row);

                        row++;

                    });
                } else {
                    var sumTarif = 0;
                }

                $('#sumTarif').val(sumTarif);

                hitung_kurang();
            },
            error: function(result) { // jika fungsi error
                error_proccess();
            }
        });
    }

    function cekPaket(notrx) {
        $.ajax({
            url: '<?= site_url() ?>Kasir/getPaket/' + notrx,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) { // jika fungsi berjalan dengan baik
                if (result[0]['status'] == 1) { // jika mendapatkan respon 1

                    $('.forPaket').show();

                    var sumPaket = 0;
                    var row = 1;
                    $.each(result[1], function(index, value) {
                        sumPaket += value.harga;

                        bodyPaket.append(`<tr id="rowPaket${row}">
                            <td>
                                <input type="hidden" name="kode_tarif[]" id="kode_tarif${row}" value="${value.kode_tarif}">
                                <input type="text" class="form-control" readonly name="kode_tarifx[]" id="kode_tarifx${row}" value="${value.nama_tarif}">
                            </td>
                            <td>
                                <input type="text" class="form-control text-center" readonly name="kunjungan[]" id="kunjungan${row}" value="${value.kunjungan}">
                            </td>
                            <td>
                                <input type="text" class="form-control text-right" readonly name="harga[]" id="harga${row}" value="${formatRpNoId(value.harga)}">
                            </td>
                        </tr>`);
                        row++;

                    });
                } else {
                    $('.forPaket').hide();
                    var sumPaket = 0;
                }

                $('#sumPaket').val(sumPaket);

                hitung_kurang();

                $('#inv_jual').html(`<option value="${result[0]['invoice']}">${result[0]['invoice']}</option>`);
                cekJual(result[0]['invoice'])

                if (result[0]['kode_member'] == 'U00001') {
                    $('.not_umum').hide();
                } else {
                    // ambil uangmuka
                    $('.not_umum').show();
                    get_um(result[0]['kode_member'], '');
                }

                getDataPx(result[0]['kode_member'], notrx)
            },
            error: function(result) { // jika fungsi error
                error_proccess();
            }
        });
    }

    function tambahTarif() {
        var jum = Number($('#forRowTarif').val());
        var row = jum + 1;

        $('#forRowTarif').val(row);

        bodyTarif.append(`<tr id="rowTarif${row}">
            <td>
                <button type="button" class="btn btn-danger" onclick="hapusTindakanTarif('${row}')">
                    <i class="fa-solid fa-delete-left"></i>
                </button>
            </td>
            <td>
                <select name="kode_tarif_single[]" id="kode_tarif_single${row}" class="form-control select2_tindakan_single" data-placeholder="~ Pilih Tarif" onchange="getTarifSingle(this.value, '${row}')"></select>
            </td>
            <td>
                <input type="text" class="form-control text-right" name="jasa_total[]" id="jasa_total${row}" value="0" readonly>
                <input type="hidden" class="form-control text-right" name="jasa_rs[]" id="jasa_rs${row}" value="0" readonly>
                <input type="hidden" class="form-control text-right" name="jasa_dokter[]" id="jasa_dokter${row}" value="0" readonly>
                <input type="hidden" class="form-control text-right" name="jasa_pelayanan[]" id="jasa_pelayanan${row}" value="0" readonly>
                <input type="hidden" class="form-control text-right" name="jasa_poli[]" id="jasa_poli${row}" value="0" readonly>
            </td>
            <td>
                <input type="text" class="form-control text-right" name="discpr_tarif[]" id="discpr_tarif${row}" value="0" onchange="changediscpr(this.value, '${row}')">
            </td>
            <td>
                <input type="text" class="form-control text-right" name="discrp_tarif[]" id="discrp_tarif${row}" value="0" onchange="changediscrp(this.value, '${row}')">
            </td>
            <td>
                <input type="text" class="form-control text-right" name="jumlah_tarif[]" id="jumlah_tarif${row}" value="0" readonly>
            </td>
        </tr>`);

        var kd_jb = $('#kode_jenis_bayar').val();
        var kelas = $('#kelas').val();
        var kode_poli = $('#kode_poli').val();

        initailizeSelect2_tindakan_single(kd_jb, kelas, kode_poli);
    }

    function hapusTindakanTarif(i) {
        var jum = Number($('#forRowTarif').val());
        var row = jum - 1;

        $('#forRowTarif').val(row);

        $('#rowTarif' + i).remove();

        hitung_t();
    }

    function getTarifSingle(kd_multiprice, x) {
        if (!kd_multiprice || kd_multiprice == null) {
            return $('#kode_tarif_single' + x).html(`<option value="">~ Pilih Tarif</option>`)
        }

        $.ajax({
            url: '<?= site_url() ?>Kasir/getTarifSingle/' + kd_multiprice,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) {
                if (result.status == 1) {
                    $('#jumlah_tarif' + x).val(formatRpNoId(result.jasa_total));
                    $('#jasa_total' + x).val(formatRpNoId(result.jasa_total));
                    $('#jasa_rs' + x).val(formatRpNoId(result.jasa_rs));
                    $('#jasa_dokter' + x).val(formatRpNoId(result.jasa_dokter));
                    $('#jasa_pelayanan' + x).val(formatRpNoId(result.jasa_pelayanan));
                    $('#jasa_poli' + x).val(formatRpNoId(result.jasa_poli));

                    hitung_t();
                } else {
                    Swal.fire("Tindakan", "Tidak tersedia", "info");
                }
            },
            error: function(result) {
                error_proccess();
            }
        });
    }

    function changediscpr(discpr, x) {
        var jasa_total = ($('#jasa_total' + x).val()).replaceAll(',', '');

        if (Number(discpr) > 100) { // jika disc pr > 100
            // munculkan notifikasi
            Swal.fire("Diskon (%)", "Maksimal 100%!", "info");

            // identifikasi x = 100
            var a = 100;
        } else { // selain itu
            // identifikasi x = discpr
            var a = discpr;
        }

        // buat rumus diskon rp
        var discrp = jasa_total * (a / 100);

        var jumlah = jasa_total - discrp;

        // tampilkan hasil ke dalam format koma
        $('#discpr_tarif' + x).val(formatRpNoId(a));
        $('#discrp_tarif' + x).val(formatRpNoId(discrp));
        $('#jumlah_tarif' + x).val(formatRpNoId(jumlah));

        hitung_t();
    }

    // perhitungan diskon rp row
    function changediscrp(discrp, x) {
        var jasa_total = ($('#jasa_total' + x).val()).replaceAll(',', '');

        // buat rumus jumlah
        var jumlah = (jasa_total) - discrp;

        // tampilkan hasil ke dalam format koma
        $('#discrp_tarif' + x).val(formatRpNoId(discrp));
        $('#discpr_tarif' + x).val('0');

        $('#jumlah_tarif' + x).val(formatRpNoId(jumlah));

        hitung_t();
    }

    // perhitungan total;
    function hitung_t() {
        var tableBarang = document.getElementById('tableTarifSingle'); // ambil id table detail
        var rowCount = tableBarang.rows.length; // hitung jumlah rownya

        // buat variable untuk di sum
        var tjumlah = 0;
        var tdiskon = 0;

        // lakukan loop
        for (var i = 1; i < rowCount; i++) {
            var row = tableBarang.rows[i];

            // ambil data berdasarkan loop
            var discrp1 = Number((row.cells[4].children[0].value).replace(/[^0-9\.]+/g, ""));
            var jumlah1 = Number((row.cells[5].children[0].value).replace(/[^0-9\.]+/g, ""));

            // lakukan rumus sum
            tjumlah += jumlah1;
            tdiskon += discrp1;
        }

        // tampilkan hasil ke dalam format koma
        $('#discTarif').val(tdiskon);
        $('#sumTarif').val(tjumlah);

        hitung_kurang();
    }

    // fungsi ambil uang muka
    function get_um(x, y) {
        if (y == 1) {
            return;
        }

        if (x == '' || x == null) { // jika x kosong/ null
            return Swal.fire("Member", "Form sudah dipilih?", "question");
        }

        $.ajax({
            url: '<?= site_url() ?>Kasir/getInfoUM/' + x,
            type: 'POST',
            dataType: 'JSON',
            success: function(result) { // jika fungsi berjalan dengan baik
                if (result.status == 0) { // jika mendapatkan respon 0
                    Swal.fire("Uang Muka", "Tidak tersedia", "info");
                } else { // selain itu
                    $('#uang_sisa').val(formatRpNoId(result.uang_sisa));
                }
            },
            error: function(result) { // jika fungsi error
                btnSimpan.attr('disabled', false);

                error_proccess();
            }
        });
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
            num = num.substring(0, num.length - (4 * i + 3)) + ',' + num.substring(num.length - (4 * i + 3));
        }

        return (((sign) ? '' : '-') + '' + num);
    }

    // fungsi tambah baris card
    function tambah_card() {
        var jum = Number($('#jumCard').val());
        var row = jum + 1;

        $('#jumCard').val(row);
        bodyCard.append(`<tr id="rowCard${row}">
            <td>
                <button type="button" class="btn btn-dark" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Tooltip on bottom" title="Hapus" onclick="hapusBaris(${row})"><i class="fa-solid fa-delete-left"></i></button>
            </td>
            <td>
                <select name="kode_bank[]" id="kode_bank${row}" class="select2_bank" data-placeholder="~ Pilih Bank"></select>
            </td>
            <td>
                <select name="tipe_bank[]" id="tipe_bank${row}" class="select2_tipe_bank" data-placeholder="~ Pilih Tipe Bank"></select>
            </td>
            <td>
                <input type="text" name="no_card[]" id="no_card${row}" class="form-control" maxlength="16">
            </td>
            <td>
                <input type="text" name="approval[]" id="approval${row}" class="form-control" maxlength="6">
            </td>
            <td>
                <input type="text" name="jumlah_card[]" id="jumlah_card${row}" class="form-control text-right" value="0" onkeyup="hitung_card(${row}); formatRp(this.value, 'jumlah_card${row}')">
            </td>
        </tr>`);


        initailizeSelect2_bank();
        initailizeSelect2_tipe_bank();
    }

    // fungsi hapus baris card
    function hapusBaris(row) {
        $('#rowCard' + row).remove();

        hitung_card_all();
    }

    // fungsi pakai um
    function pakai_um() {
        var uang_sisa = parseFloat(($('#uang_sisa').val()).replaceAll(',', ''));
        var um_keluar = parseFloat(($('#um_keluar').val()).replaceAll(',', ''));

        if (um_keluar > uang_sisa) {
            $('#um_keluar').val(formatRpNoId(uang_sisa));

            Swal.fire("Uang Muka Pakai", "Lebih besar dari Uang Muka Tersedia", "question");
        } else {
            $('#um_keluar').val(formatRpNoId(um_keluar));
        }

        hitung_bayar();
    }

    // fungsi hitung pembayaran
    function hitung_bayar() {
        if ($('#cash').val() == '') {
            var cash = 0;
        } else {
            var cash = parseFloat(($('#cash').val()).replaceAll(',', ''));
        }

        if ($('#card').val() == '') {
            var card = 0;
        } else {
            var card = parseFloat(($('#card').val()).replaceAll(',', ''));
        }

        if ($('#um_keluar').val() == '') {
            var um_keluar = 0;
        } else {
            var um_keluar = parseFloat(($('#um_keluar').val()).replaceAll(',', ''));
        }


        var semua = cash + card + um_keluar;
        $('#cash').val(formatRpNoId(cash));
        $('#total').val(formatRpNoId(semua));

        hitung_kurang();
    }

    function hitung_kurang_penjamin() {
        var sumTarif = parseFloat(($('#sumTarif').val()).replaceAll(',', ''));
        var sumPaket = parseFloat(($('#sumPaket').val()).replaceAll(',', ''));
        var sumJual = parseFloat(($('#sumJual').val()).replaceAll(',', ''));
        var total = parseFloat(($('#total').val()).replaceAll(',', ''));

        if ($('#norm_px').text() == 'U00001') {
            var kurang = total - (sumJual + sumTarif + sumPaket);

            $('#bodyPerorangan').show();
        } else {
            if ($('#kode_jenis_bayar').val() == 'JB00000001') {
                var kurang = 0 - (sumJual + sumTarif + sumPaket);
                $('#tercover').val(0);
                $('#total').val(0);

                $('#bodyPerorangan').show();
            } else {
                var cover = (sumJual + sumTarif + sumPaket);
                $('#tercover').val(formatRpNoId(cover));
                $('#total').val(formatRpNoId(cover));
                var kurang = cover - (sumJual + sumTarif + sumPaket);

                $('#bodyPerorangan').hide();
            }
        }

        $('#total_kurang').val(formatRpNoId(kurang));

        cek_button();
    }

    // fungsi hitung kurang
    function hitung_kurang() {
        var sumTarif = parseFloat(($('#sumTarif').val()).replaceAll(',', ''));
        var sumPaket = parseFloat(($('#sumPaket').val()).replaceAll(',', ''));
        var sumJual = parseFloat(($('#sumJual').val()).replaceAll(',', ''));
        var total = parseFloat(($('#total').val()).replaceAll(',', ''));
        var potongan_promo_rp = parseFloat(($('#potongan_promo_rp').val()).replaceAll(',', ''));

        if ($('#norm_px').text() == 'U00001') {
            var kurang = total - (sumJual + sumTarif + sumPaket - potongan_promo_rp);

            $('#total').val(kurang);
            $('#bodyPerorangan').show();
        } else {
            if ($('#kode_jenis_bayar').val() == 'JB00000001') {
                var kurang = total - (sumJual + sumTarif + sumPaket - potongan_promo_rp);
                $('#tercover').val(0);

                if ($('#cash').val() == '') {
                    var cash = 0;
                } else {
                    var cash = parseFloat(($('#cash').val()).replaceAll(',', ''));
                }

                if ($('#card').val() == '') {
                    var card = 0;
                } else {
                    var card = parseFloat(($('#card').val()).replaceAll(',', ''));
                }

                if ($('#um_keluar').val() == '') {
                    var um_keluar = 0;
                } else {
                    var um_keluar = parseFloat(($('#um_keluar').val()).replaceAll(',', ''));
                }


                var semua = cash + card + um_keluar;
                $('#total').val(formatRpNoId(semua));

                $('#bodyPerorangan').show();
            } else {
                var cover = (sumJual + sumTarif + sumPaket);
                $('#tercover').val(formatRpNoId(cover));
                $('#total').val(formatRpNoId(cover));
                var kurang = cover - (sumJual + sumTarif + sumPaket - potongan_promo_rp);

                $('#bodyPerorangan').hide();
            }
        }

        $('#total_kurang').val(formatRpNoId(kurang));

        cek_button();
    }

    // fungsi hitung row card
    function hitung_card(x) {
        var jumlah = ($('#jumlah_card' + x).val()).replaceAll(',', '');

        hitung_card_all();
    }

    // fungsi hitung seluruh card
    function hitung_card_all() {
        var tableBayarCard = document.getElementById('tableBayarCard'); // ambil id table detail
        var rowCount = tableBayarCard.rows.length; // hitung jumlah rownya

        // buat variable untuk di sum
        var tjumlah = 0;
        for (var i = 1; i < rowCount; i++) {
            var row = tableBayarCard.rows[i];

            var jumlah1 = Number((row.cells[5].children[0].value).replace(/[^0-9\.]+/g, ""));

            // lakukan rumus sum
            tjumlah += jumlah1;
        }

        $('#card').val(formatRpNoId(tjumlah));

        hitung_bayar();
    }

    // fungsi save
    function save() {
        btnSimpan.attr('disabled', true);

        // if (inv_jual.val() == '' || inv_jual.val() == null) { // jika inv_jual null/ kosong
        //     btnSimpan.attr('disabled', false);

        //     return Swal.fire("Penjualan", "Form sudah dipilih?", "question");
        // }

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
            url: '<?= site_url() ?>kasir/kasir_proses/' + param,
            type: "POST",
            data: form.serialize(),
            dataType: "JSON",
            success: function(result) { // jika fungsi berjalan dengan baik
                btnSimpan.attr('disabled', false);

                if (result.status == 1) { // jika mendapatkan respon 1

                    Swal.fire("Pembayaran", "Berhasil " + message, "success").then(() => {
                        // question_cetak(result.token_pembayaran);
                        getUrl('Kasir');
                    });
                } else { // selain itu

                    Swal.fire("Pembayaran", "Gagal " + message + ", silahkan dicoba kembali", "info");
                }
            },
            error: function(result) { // jika fungsi error
                btnSimpan.attr('disabled', false);

                error_proccess();
            }
        });
    }

    function question_cetak(x) {
        Swal.fire({
            title: "Cetak Bukti?",
            text: 'Cetak bukti pembayaran!',
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Cetak",
            cancelButtonText: "Tidak!"
        }).then((result) => {
            if (result.isConfirmed) { // jika yakin
                window.open('<?= site_url() ?>Kasir/print_kwitansi/' + x + '/0', '_blank');
                getUrl('Kasir');
            } else {
                getUrl('Kasir');
            }
        });
    }

    // fungsi cek tombol simpan 
    function cek_button() {
        var kurang = parseFloat(($('#total_kurang').val()).replaceAll(',', ''));

        if (kurang < 0) {
            btnSimpan.attr('disabled', true);
        } else {
            btnSimpan.attr('disabled', false);
        }
    }

    function showGuide() {
        // clean text
        $('#modal_mgLabel').text(``);
        $('#modal-isi').text(``);

        $('#modal_mg').modal('show'); // show modal

        // isi text
        $('#modal_mgLabel').append(`Manual Guide Pembayaran Kasir`);
        $('#modal-isi').append(`
            <ol>
                <li style="font-weight: bold;">Proses Pembayaran</li>
                <p>
                    <ul>
                        <li>Klik tombol Pembayaran</li>
                        <li>Pilih Pendaftaran atau Penjualan<br>(Jika hanya beli obat cukup pilih yang Penjualan)</li>
                        <li>(Optional) - Tambahkan tindakan jika ada tambahan tindakan</li>
                        <li><span style='color: red;'>Teks berawarna merah</span> mengartikan wajib terisi</li>
                        <li>Pilih cara bayar</li>
                        <li>Klik tombol Proses</li>
                    </ul>
                </p>
                <li style="font-weight: bold;">Ubah Data</li>
                <p>
                    <ul>
                        <li>Klik tombol Ubah pada list data yang ingin di ubah</li>
                        <li>Ubah isi Form yang ingin diubah</li>
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