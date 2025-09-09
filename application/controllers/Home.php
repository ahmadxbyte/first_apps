<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{
    // variable open public untuk controller Home
    public $data;

    public function __construct()
    {
        parent::__construct();
        // load model M_auth
        $this->load->model("M_auth");

        $this->db->query("SET SESSION sql_mode = REPLACE(
            REPLACE(
                REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY,', ''),
            ',ONLY_FULL_GROUP_BY', ''),
        'ONLY_FULL_GROUP_BY', '')");

        if (!empty($this->session->userdata("email"))) { // jika session email masih ada
            // ambil isi data berdasarkan email session dari table user, kemudian tampung ke variable $user
            $user = $this->M_global->getData("user", ["email" => $this->session->userdata("email")]);

            // tampung data ke variable data public
            $this->data = [
                'nama'      => $user->nama,
                'email'     => $user->email,
                'kode_role' => $user->kode_role,
                'actived'   => $user->actived,
                'foto'      => $user->foto,
                'shift'     => $this->session->userdata('shift'),
                'menu'      => 'Home',
            ];
        } else { // selain itu
            // kirimkan kembali ke Auth
            if (strtolower($this->router->fetch_class()) !== 'auth') {
                if (!$this->session->userdata('logged_in')) {
                    // Backup data user (jika masih tersedia)
                    if (!empty($this->data['foto'])) {
                        $this->session->set_userdata('foto', $this->data['foto']);
                    }

                    if (!empty($this->data['nama'])) {
                        $this->session->set_userdata('nama', $this->data['nama']);
                    }

                    if (!empty($this->data['email'])) {
                        $this->session->set_userdata('email', $this->data['email']);
                    }

                    redirect('auth/lockscreen');
                }
            }

            redirect('Auth');
        }
    }

    // home page
    public function index()
    {
        $sess_cabang    = $this->session->userdata('cabang');
        $sess_web       = $this->session->userdata('web_id');

        // website config
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version    = $this->M_global->getData('web_version', ['id_web' => $sess_web]);

        $now            = date('Y-m-d');

        $header_out     = $this->db->query("SELECT * FROM barang_out_header WHERE kode_cabang = '$sess_cabang' AND tgl_jual LIKE '%$now%' AND status_jual = 1")->result();
        $header_bayar   = $this->db->query("SELECT * FROM pembayaran WHERE kode_cabang = '$sess_cabang' AND tgl_pembayaran LIKE '%$now%' AND approved = 1")->result();
        $header_daftar  = $this->db->query("SELECT * FROM pendaftaran WHERE kode_cabang = '$sess_cabang' AND tgl_daftar LIKE '%$now%' AND status_trx != 2")->result();

        $saldo_utama    = $this->M_global->getData('kas_utama', ['kode_cabang' => $sess_cabang]);
        $saldo_second   = $this->db->query("SELECT SUM(sisa) AS saldo FROM kas_second WHERE kode_cabang = '$sess_cabang'")->row();

        $saldo          = ((!empty($saldo_utama)) ? $saldo_utama->sisa : 0) + ((!empty($saldo_second)) ? $saldo_second->saldo : 0);

        $barang_out     = $this->db->query('SELECT bd.*, b.hpp, b.harga_jual, IFNULL(bd.discrp, 0) AS discrp, IFNULL(bd.pajakrp, 0) AS pajakrp FROM barang_out_header bh JOIN barang_out_detail bd USING (invoice) JOIN barang b USING (kode_barang) WHERE bh.kode_cabang = "' . $sess_cabang . '"')->result();

        $total_untung = 0;
        foreach ($barang_out as $bo) {
            $qty_jual = $bo->qty;
            $untung = ($bo->harga_jual - $bo->hpp);
            $total = ($qty_jual * $untung) - (isset($bo->discrp) ? $bo->discrp : 0) + (isset($bo->pajakrp) ? $bo->pajakrp : 0);
            $total_untung += $total;
        }

        $pendaftaran = $this->M_global->getData('pendaftaran', ['kode_cabang' => $sess_cabang, 'tgl_daftar' => $now]);


        $kode_user = $this->session->userdata('kode_user');
        $menu = $this->db->query("
            SELECT m.* 
            FROM m_menu m 
            WHERE m.id IN (
                SELECT id_menu 
                FROM akses_menu 
                WHERE kode_role IN (
                    SELECT kode_role FROM user WHERE kode_user = ?
                )
            ) 
            ORDER BY m.id
        ", [$kode_user])->result();

        $parameter = [
            $this->data,
            'judul'             => 'Selamat Datang',
            'nama_apps'         => $web_setting->nama,
            'page'              => 'Beranda',
            'web'               => $web_setting,
            'web_version'       => $web_version->version,
            'kunjungan_poli'    => $this->db->query("SELECT p.keterangan AS poli, COUNT(boh.kode_poli) AS jumlah FROM pembayaran buy JOIN barang_out_header boh ON buy.inv_jual = boh.invoice JOIN m_poli p ON boh.kode_poli = p.kode_poli WHERE buy.kode_cabang = '$sess_cabang' AND buy.tgl_pembayaran LIKE '%$now%' AND buy.approved = 1 GROUP BY boh.kode_poli")->result(),
            'jumlah_beli'       => count($header_out),
            'jumlah_bayar'      => count($header_bayar),
            'saldo_kas'         => $saldo,
            'jumlah_daftar'     => count($header_daftar),
            'hutangx'           => $this->db->query("SELECT SUM(jumlah) AS hutang FROM piutang WHERE kode_cabang = '$sess_cabang' AND jenis > 0 AND status = 0")->row(),
            'piutangx'          => $this->db->query("SELECT SUM(jumlah) AS piutang FROM piutang WHERE kode_cabang = '$sess_cabang' AND jenis < 1 AND status = 0")->row(),
            'result_jual'       => $total_untung,
            'wilayah'           => $this->db->query(
                'SELECT COUNT(m.provinsi) AS total, mp.provinsi AS provinsi
                FROM pendaftaran p
                JOIN member m USING(kode_member)
                JOIN m_provinsi mp ON m.provinsi = mp.kode_provinsi
                WHERE p.kode_cabang = "' . $sess_cabang . '" 
                GROUP BY m.provinsi 
                ORDER BY COUNT(m.provinsi) DESC 
                LIMIT 5'
            )->result(),
            'menu'              => $menu,
        ];

        $this->template->load('Template/Content', 'Home/Dashboard', $parameter);
    }
}
