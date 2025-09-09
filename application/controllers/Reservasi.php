<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reservasi extends CI_Controller
{
    // variable open public untuk controller Home
    public $data;

    public function __construct()
    {
        parent::__construct();
        // load model M_auth
        $this->load->model("M_auth");

        if (!empty($this->session->userdata("kode_user"))) { // jika session email masih ada
            // ambil isi data berdasarkan email session dari table user, kemudian tampung ke variable $user
            $user = $this->M_global->getData("user", ["kode_user" => $this->session->userdata("kode_user")]);

            if ($user) {
                $user = $user;
            } else {
                $user = $this->M_global->getData("member", ["kode_member" => $this->session->userdata("kode_user")]);
            }

            // tampung data ke variable data public
            $this->data = [
                'nama'      => $user->nama,
                'email'     => $user->email,
                'kode_role' => $user->kode_role,
                'actived'   => $user->actived,
                'foto'      => $user->foto,
                'shift'     => (!empty($this->session->userdata('shift')) ? ($this->session->userdata('shift')) : ''),
                'menu'      => 'Profile',
            ];
        } else { // selain itu
            // kirimkan kembali ke Auth
            redirect('Auth');
        }
    }

    // home page
    public function index()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Reservasi',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Reservasi',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Reservasi/reservasi_list',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Member/Reservasi', $parameter);
    }

    // fungsi list pendaftaran
    public function reservasi_list($param1 = 1, $param2 = '')
    {
        // parameter untuk list table
        $table            = 'reservasi';
        $colum            = ['id', 'kode_reservasi', 'kode_cabang', 'kode_dokter', 'kode_poli', 'kode_member', 'tgl', 'jam', 'tgl_batal', 'jam_batal', 'user_batal', 'status_reservasi', 'no_antrian', 'no_trx'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $param2           = $this->session->userdata('kode_user');
        $kondisi_param2   = 'kode_member';
        $kondisi_param1   = 'tgl';

        // table server side tampung kedalam variable $list
        $dat    = explode("~", $param1);
        if ($dat[0] == 1) {
            $bulan   = date('m');
            $tahun   = date('Y');
            $list    = $this->M_datatables2->get_datatables($table, $colum, $order_arr, $order, $order2, $kondisi_param1, 1, $bulan, $tahun, $param2, $kondisi_param2);
        } else {
            $bulan   = date('Y-m-d', strtotime($dat[1]));
            $tahun   = date('Y-m-d', strtotime($dat[2]));
            $list    = $this->M_datatables2->get_datatables($table, $colum, $order_arr, $order, $order2, $kondisi_param1, 2, $bulan, $tahun, $param2, $kondisi_param2);
        }
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            $row    = [];

            if ($rd->status_reservasi == 0) {
                $batalin = 'onclick="batal(' . "'" . $rd->id . "', '" . $rd->kode_reservasi . "'" . ')"';
                $status = '<span class="badge badge-success">Terbooking</span>';
            } else if ($rd->status_reservasi == 1) {
                $batalin = 'disabled';
                $status = '<span class="badge badge-primary">Terdaftar</span>';
            } else {
                $batalin = 'disabled';
                $status = '<span class="badge badge-danger">Batal</span>';
            }

            if ($rd->user_batal === null) {
                $user_batal = '';
            } else {
                $user_batal = '<br>Dibatalkan: ' . (($rd->user_batal == $rd->kode_member) ? 'Diri sendiri' : $this->M_global->getData('user', ['kode_user' => $rd->user_batal])->nama);
            }

            $dokter = $this->M_global->getData('dokter', ['kode_dokter' => $rd->kode_dokter]);

            $row[]  = $no++;
            $row[]  = $rd->kode_reservasi . '<br>' . $status . $user_batal;
            $row[]  = date('d-m-Y', strtotime($rd->tgl)) . '<br>' . date('H:i:s', strtotime($rd->jam));
            $row[]  = (($rd->tgl_batal <> null) ? date('d-m-Y', strtotime($rd->tgl_batal)) . '<br>' . date('H:i:s', strtotime($rd->jam_batal)) : 'xx-xx-xxxx xx:xx:xx');
            $row[]  = $this->M_global->getData('m_poli', ['kode_poli' => $rd->kode_poli])->keterangan;
            $row[]  = 'Dr. ' . $dokter->nama;
            $row[]  = $rd->no_antrian;

            $row[]  = '<div class="text-center">
                <button type="button" style="margin-bottom: 5px;" class="btn btn-danger" title="batal" ' . $batalin . '><i class="fa-regular fa-circle-xmark"></i></button>
            </div>';
            $data[] = $row;
        }

        // hasil server side
        $output = [
            "draw"            => $_POST['draw'],
            "recordsTotal"    => $this->M_datatables2->count_all($table, $colum, $order_arr, $order, $order2, $kondisi_param1, 1, $bulan, $tahun, $param2, $kondisi_param2),
            "recordsFiltered" => $this->M_datatables2->count_filtered($table, $colum, $order_arr, $order, $order2, $kondisi_param1, 1, $bulan, $tahun, $param2, $kondisi_param2),
            "data"            => $data,
        ];

        // kirimkan ke view
        echo json_encode($output);
    }

    public function getPoli()
    {
        $kode_cabang    = $this->session->userdata('cabang');
        $tgl            = $this->input->post('tgl');

        $jdokter = $this->db->select('jadwal_dokter.*, m_poli.keterangan as nama_poli')
            ->from('jadwal_dokter')
            ->join('m_poli', 'm_poli.kode_poli = jadwal_dokter.kode_poli')
            ->where([
                'jadwal_dokter.kode_cabang' => $kode_cabang,
                'jadwal_dokter.hari' => date('l', strtotime($tgl)),
                'jadwal_dokter.status' => '1',
            ])
            ->get()
            ->result();

        if ($jdokter) {
            echo json_encode($jdokter);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    public function getRuang()
    {
        $kode_cabang    = $this->session->userdata('cabang');
        $tgl            = $this->input->post('tgl');
        $kode_poli      = $this->input->post('kode_poli');
        $kode_dokter    = $this->input->post('kode_dokter');

        $jadwal_dokter  = $this->M_global->getData('jadwal_dokter', ['kode_dokter' => $kode_dokter, 'kode_poli' => $kode_poli, 'kode_cabang' => $kode_cabang, 'hari' => date('l', strtotime($tgl))]);

        if ($jadwal_dokter) {
            $data = [
                'status'        => 1,
                'kode_ruang'    => $jadwal_dokter->kode_ruang,
            ];

            echo json_encode($data);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    public function getDokterPoli()
    {
        $kode_cabang    = $this->session->userdata('cabang');
        $tgl            = $this->input->post('tgl');
        $kode_poli      = $this->input->post('kode_poli');

        $jdokter = $this->db->select('jadwal_dokter.*, dokter.nama as nama_dokter')
            ->from('jadwal_dokter')
            ->join('dokter', 'dokter.kode_dokter = jadwal_dokter.kode_dokter')
            ->where([
                'jadwal_dokter.kode_cabang' => $kode_cabang,
                'jadwal_dokter.kode_poli' => $kode_poli,
                'jadwal_dokter.hari' => date('l', strtotime($tgl))
            ])
            ->get()
            ->result();

        if ($jdokter) {
            echo json_encode($jdokter);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    public function reservasi_proses()
    {
        $kode_cabang    = $this->session->userdata('cabang');
        $kode_member    = $this->session->userdata('kode_user');
        $kode_poli      = $this->input->post('kode_poli');
        $kode_dokter    = $this->input->post('kode_dokter');
        $kode_ruang     = $this->input->post('kode_ruang');
        $tgl            = $this->input->post('tgl');
        $jam            = date('H:i:s');
        $kode_reservasi = _codeReservasi($kode_poli, $kode_cabang, $tgl);
        $no_antrian     = _noAntrian($kode_poli, $kode_cabang, $tgl);
        $status         = 0;

        $reservasi      = $this->M_global->getData('reservasi', ['kode_cabang' => $kode_cabang, 'kode_dokter' => $kode_dokter, 'kode_poli' => $kode_poli, 'tgl' => $tgl, 'kode_member' => $kode_member]);
        $res            = $this->M_global->getDataResult('reservasi', ['kode_cabang' => $kode_cabang, 'kode_dokter' => $kode_dokter, 'kode_poli' => $kode_poli, 'tgl' => $tgl, 'status_reservasi <> ' => 2]);
        $jdok           = $this->M_global->getData('jadwal_dokter', ['kode_cabang' => $kode_cabang, 'kode_dokter' => $kode_dokter, 'kode_poli' => $kode_poli, 'hari' => date('l', strtotime($tgl))]);

        $data = [
            'kode_reservasi'    => $kode_reservasi,
            'kode_cabang'       => $kode_cabang,
            'kode_dokter'       => $kode_dokter,
            'kode_poli'         => $kode_poli,
            'kode_member'       => $kode_member,
            'kode_ruang'        => $kode_ruang,
            'tgl'               => $tgl,
            'jam'               => $jam,
            'status_reservasi'  => $status,
            'no_antrian'        => $no_antrian,
        ];

        if ($reservasi) {
            // Check if reservation exists and is not cancelled
            if ($reservasi->status_reservasi < 2) {
                echo json_encode(['status' => 2]);
                return;
            }

            // Check if patient limit is reached
            if (count($res) >= $jdok->limit_px) {
                echo json_encode(['status' => 3]);
                return;
            }

            // Attempt to insert new reservation
            $cek = $this->M_global->insertData('reservasi', $data);
            echo json_encode(['status' => $cek ? 1 : 0]);
        } else {
            // No existing reservation found, create new one
            $cek = $this->M_global->insertData('reservasi', $data);
            echo json_encode(['status' => $cek ? 1 : 0]);
        }
    }

    public function batal_reser($id)
    {
        $cek = $this->M_global->getData('reservasi', ['id' => $id]);

        if ($cek) {
            $cek2 = $this->M_global->updateData('reservasi', ['status_reservasi' => 2, 'user_batal' => $cek->kode_member], ['id' => $id]);
            if ($cek2) {
                echo json_encode(['status' => 1]);
            } else {
                echo json_encode(['status' => 0]);
            }
        } else {
            echo json_encode(['status' => 0]);
        }
    }
}
