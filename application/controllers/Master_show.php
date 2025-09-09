<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master_show extends CI_Controller
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
            redirect('Auth');
        }
    }

    public function getInfo($table, $param)
    {
        if ($table == 'm_provinsi') {
            $kode = 'kode_provinsi AS id';
            $text = 'provinsi AS text';
            $where = ' WHERE kode_provinsi = "' . $param . '"';
        } else if ($table == 'kabupaten') {
            $kode = 'kode_kabupaten AS id';
            $text = 'kabupaten AS text';
            $where = ' WHERE kode_kabupaten = "' . $param . '"';
        } else {
            $kode = 'kode_kecamatan AS id';
            $text = 'kecamatan AS text';
            $where = ' WHERE kode_kecamatan = "' . $param . '"';
        }

        $data = $this->db->query('SELECT ' . $kode . ', ' . $text . ' FROM ' . $table . $where)->row();

        echo json_encode($data);
    }

    public function cek_promo($kode_promo)
    {
        // cek promo
        $promo = $this->M_global->getData('m_promo', ['kode_promo' => $kode_promo]);

        if ($promo) { // jika promo ada
            // kirimkan data promo ke view
            echo json_encode(['status' => 1, 'promo' => $promo->discpr]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0, 'promo' => 0]);
        }
    }
}
