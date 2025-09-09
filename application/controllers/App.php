<?php
defined('BASEPATH') or exit('No direct script access allowed');

class App extends CI_Controller
{
    // variable open public untuk controller Home
    public $data;

    public function __construct()
    {
        parent::__construct();
        // load model M_auth
        $this->load->model("M_auth");

        if (!empty($this->session->userdata("email"))) { // jika session email masih ada
            // ambil isi data berdasarkan email session dari table user, kemudian tampung ke variable $user
            $user = $this->M_global->getData("member", ["email" => $this->session->userdata("email")]);
            $this->data = [
                'nama'      => $user->nama,
                'email'     => $user->email,
                'kode_role' => $user->kode_role,
                'actived'   => $user->actived,
                'foto'      => $user->foto,
                'menu'      => 'App',
            ];
        } else { // selain itu
            // kirimkan kembali ke Auth
            redirect('Auth');
        }
    }

    // home page
    public function index()
    {
        // // website config
        // $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        // $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        // $parameter = [
        //     $this->data,
        //     'judul'         => 'Selamat Datang',
        //     'nama_apps'     => $web_setting->nama,
        //     'page'          => 'Selamat Datang',
        //     'web'           => $web_setting,
        //     'web_version'   => $web_version->version,
        // ];

        // $this->template->load('Template/Content', 'Auth/App', $parameter);
        redirect('Reservasi');
    }

    // fungsi pencarian
    public function pencarian($key = '')
    {
        $kode_kategori = $this->input->get('kode_kategori');

        if ($kode_kategori == '' || $kode_kategori == null || $kode_kategori == 'null') {
            if ($key == '' || $key == null || $key == 'null') {
                $barang = $this->db->query("SELECT * FROM barang")->result();
            } else {
                $barang = $this->db->query("SELECT b.* FROM barang b JOIN m_jenis j USING(kode_jenis) JOIN m_kategori k USING(kode_kategori) WHERE (b.nama LIKE '%$key%' OR k.keterangan LIKE '%$key%' OR j.keterangan LIKE '%$key%')")->result();
            }
        } else {
            if ($key == '' || $key == null || $key == 'null') {
                $barang = $this->db->query("SELECT * FROM barang WHERE kode_kategori = '$kode_kategori'")->result();
            } else {
                $barang = $this->db->query("SELECT b.* FROM barang b JOIN m_jenis j USING(kode_jenis) JOIN m_kategori k USING(kode_kategori) WHERE k.kode_kategori = '$kode_kategori' AND (b.nama LIKE '%$key%' OR k.keterangan LIKE '%$key%' OR j.keterangan LIKE '%$key%')")->result();
            }
        }


        if (count($barang) > 0) :
            foreach ($barang as $b) { ?>
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
            <?php }
        else : ?>
            <div class="row">
                <div class="col-md-12" style="text-align: center;">
                    <div class="h3 text-danger">OBAT BELUM TERSEDIA</div>
                </div>
            </div>
            <?php endif;
    }

    // fungsi detail
    public function detail($kode_barang)
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Obat Detail',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Obat Detail',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'barang2'       => $this->M_global->getData('barang', ['kode_barang' => $kode_barang]),
            'barang'        => $this->M_global->getDataResult('barang', ['kode_barang <> ' => $kode_barang]),
            'stok_barang'   => $this->db->query("SELECT SUM(masuk) AS masuk, SUM(keluar) AS keluar, SUM(akhir) AS akhir FROM barang_stok WHERE kode_barang = '$kode_barang' GROUP BY kode_barang")->row(),
            'kategori'      => $this->M_global->getResult('m_kategori'),
        ];

        $this->template->load('Template/App', 'Auth/Detail', $parameter);
    }

    // fungsi pencarian
    public function pencarian2($key = '')
    {
        $kode_kategori = $this->input->get('kode_kategori');
        $selain = $this->input->get('selain');

        if ($kode_kategori == '' || $kode_kategori == null || $kode_kategori == 'null') {
            if ($key == '' || $key == null || $key == 'null') {
                $barang_search = $this->db->query("SELECT * FROM barang WHERE kode_barang <> '$selain'")->result();
            } else {
                $barang_search = $this->db->query("SELECT b.* FROM barang b JOIN m_jenis j USING(kode_jenis) JOIN m_kategori k USING(kode_kategori) WHERE b.kode_barang <> '$selain' AND (b.nama LIKE '%$key%' OR k.keterangan LIKE '%$key%' OR j.keterangan LIKE '%$key%')")->result();
            }
        } else {
            if ($key == '' || $key == null || $key == 'null') {
                $barang_search = $this->db->query("SELECT b.* FROM barang b JOIN m_jenis j USING(kode_jenis) JOIN m_kategori k USING(kode_kategori) WHERE b.kode_barang <> '$selain' AND b.kode_kategori = '$kode_kategori'")->result();
            } else {
                $barang_search = $this->db->query("SELECT b.* FROM barang b JOIN m_jenis j USING(kode_jenis) JOIN m_kategori k USING(kode_kategori) WHERE b.kode_barang <> '$selain' AND b.kode_kategori = '$kode_kategori' AND (b.nama LIKE '%$key%' OR k.keterangan LIKE '%$key%' OR j.keterangan LIKE '%$key%')")->result();
            }
        }


        if (count($barang_search) > 0) :
            foreach ($barang_search as $b) { ?>
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
            <?php }
        else : ?>
            <div class="row">
                <div class="col-md-12" style="text-align: center;">
                    <div class="h3 text-danger">OBAT BELUM TERSEDIA</div>
                </div>
            </div>
<?php endif;
    }

    // fungsi pesan proses
    public function pesan_proses($kode_barang)
    {
        $kode_member    = $this->session->userdata('kode_user');
        $invoice        = _invoiceChart($kode_member);
        $member         = $this->M_global->getData('member', ['kode_member' => $kode_member]);

        $prov           = $this->M_global->getData('m_provinsi', ['kode_provinsi' => $member->provinsi])->provinsi;
        $kab            = $this->M_global->getData('kabupaten', ['kode_kabupaten' => $member->kabupaten])->kabupaten;
        $kec            = $this->M_global->getData('kecamatan', ['kode_kecamatan' => $member->kecamatan])->kecamatan;
        $desa           = $member->desa;
        $kodepos        = $member->kodepos;

        $alamat         = 'Prov. ' . $prov . ',<br>Kab. ' . $kab . ',<br>Kec. ' . $kec . ',<br>Ds. ' . $desa . ',<br>(POS: ' . $kodepos . '), RT.' . $member->rt . '/RW.' . $member->rw;

        $qty_order      = str_replace(',', '', $this->input->post('qty'));
        $barang         = $this->M_global->getData('barang', ['kode_barang' => $kode_barang]);
        $harga_jual     = $barang->harga_jual;

        $isi_header = [
            'invoice' => $invoice,
            'kode_member' => $kode_member,
            'alamat' => $alamat,
            'tgl_order' => date('Y-m-d'),
            'jam_order' => date('H:i:s'),
            'status_order' => 0,
            'subtotal' => $qty_order * $harga_jual,
            'total' => $qty_order * $harga_jual,
        ];
    }
}
