<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Anjungan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("M_global");
    }

    public function index()
    {
        $cabangnya    = $this->input->get('cabang');
        $web_setting  = $this->M_global->getData('web_setting', ['id' => 1]);
        $anjungan     = _codeAnjungan($cabangnya);
        $antrian      = $this->db->query('SELECT * FROM m_anjungan WHERE kode_cabang = "' . $cabangnya . '" AND tgl = "' . date('Y-m-d') . '" AND status < 1')->result();

        $data = [
            'nama_apps' => $web_setting->nama,
            'web'       => $web_setting,
            'cabang'    => $cabangnya,
            'anjungan'  => $anjungan,
            'antrian'   => $antrian
        ];

        $this->load->view('Anjungan/index', $data);
    }

    public function claim($cabang, $no_anjungan)
    {
        $data = [
            'kode_cabang'   => $cabang,
            'tgl'           => date('Y-m-d'),
            'no_anjungan'   => $no_anjungan,
        ];

        $cek = $this->M_global->insertData('m_anjungan', $data);

        if ($cek) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    public function layar()
    {
        $cabangnya    = $this->input->get('cabang');
        $web_setting  = $this->M_global->getData('web_setting', ['id' => 1]);
        $anjungan     = $this->db->query('SELECT * FROM m_anjungan WHERE kode_cabang = "' . $cabangnya . '" AND tgl = "' . date('Y-m-d') . '" AND status = 1 AND panggil > 0 AND p_ulang > 0 ORDER BY panggil DESC LIMIT 1')->row();

        $data = [
            'nama_apps' => $web_setting->nama,
            'web'       => $web_setting,
            'cabang'    => $cabangnya,
            'anjungan'  => ($anjungan && isset($anjungan->no_anjungan)) ? $anjungan->no_anjungan : 0,
        ];

        $this->load->view('Anjungan/layar', $data);
    }

    public function get_data_antrian($cabang)
    {
        $sql = "SELECT * FROM m_anjungan 
                WHERE kode_cabang = ? 
                AND tgl = ? 
                AND panggil > 0
                AND status < 1
                ORDER BY panggil DESC 
                LIMIT 1";

        // AND p.tgl_daftar = '" . date('Y-m-d') . "'

        $anjungan = $this->db->query($sql, [
            $cabang,
            date('Y-m-d')
        ])->row();

        echo json_encode($anjungan);
    }

    public function reset_no($cabang)
    {
        $no_anjungan = $this->input->get('no_anjungan');

        $this->M_global->updateData(
            'm_anjungan',
            ['p_ulang' => 0],
            ['no_anjungan' => $no_anjungan, 'kode_cabang' => $cabang, 'tgl' => date('Y-m-d')]
        );

        echo json_encode(['status' => 1]);
    }

    public function daftar($kode_booking, $kode_cabang)
    {
        $reservasi = $this->M_global->getData('reservasi', ['kode_cabang' => $kode_cabang, 'tgl' => date('Y-m-d'), 'kode_reservasi' => $kode_booking]);

        if ($reservasi) {
            $kode_user          = $reservasi->kode_member;
            $shift              = 0;
            $kode_poli          = $reservasi->kode_poli;
            $tgl_daftar         = date('Y-m-d');
            $jam_daftar         = date('H:i:s');
            $kode_member        = $reservasi->kode_member;
            $no_antrian         = $reservasi->no_antrian;
            $kode_jenis_bayar   = 'JB00000001';
            $no_trx             = _kodeTrx($kode_poli, $kode_cabang);
            $kode_dokter        = $reservasi->kode_dokter;
            $kode_ruang         = $reservasi->kode_ruang;
            $kode_bed           = null;
            $tipe_daftar        = 1;
            $kelas              = 'Umum';
            $kode_masuk         = 'CM00000001';

            $data = [
                'kode_cabang'       => $kode_cabang,
                'no_trx'            => $no_trx,
                'kode_jenis_bayar'  => $kode_jenis_bayar,
                'kode_masuk'        => $kode_masuk,
                'kelas'             => $kelas,
                'tgl_daftar'        => $tgl_daftar,
                'jam_daftar'        => $jam_daftar,
                'kode_member'       => $kode_member,
                'kode_poli'         => $kode_poli,
                'kode_dokter'       => $kode_dokter,
                'no_antrian'        => $no_antrian,
                'tgl_keluar'        => null,
                'jam_keluar'        => null,
                'status_trx'        => 0,
                'kode_ruang'        => $kode_ruang,
                'kode_bed'          => $kode_bed,
                'tipe_daftar'       => $tipe_daftar,
                'kode_user'         => $kode_user,
                'shift'             => $shift,
                'panggil'           => 0,
                'p_ulang'           => 0,
            ];

            $anjungan = [
                'kode_cabang'       => $kode_cabang,
                'tgl'               => $tgl_daftar,
                'no_anjungan'       => _codeAnjungan($kode_cabang),
                'no_trx'            => $no_trx,
                'kode_member'       => $kode_member,
                'status'            => 1,
                'panggil'           => 1,
                'p_ulang'           => 1,
                'kode_user_panggil' => $kode_user,
                'waktu'             => date('Y-m-d H:i:s'),
            ];

            if ($reservasi->status_reservasi == 0) {
                $cek = $this->M_global->insertData('pendaftaran', $data);

                if ($cek) {
                    $this->M_global->insertData('m_anjungan', $anjungan);
                    $this->M_global->updateData('reservasi', ['status_reservasi' => 1, 'no_trx' => $no_trx], ['id' => $reservasi->id]);

                    echo json_encode(['status' => 1]);
                } else {
                    echo json_encode(['status' => 0]);
                }
            } else {
                echo json_encode(['status' => 3]);
            }
        } else {
            echo json_encode(['status' => 2]);
        }
    }
}
