<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Health extends CI_Controller
{
    // variable open public untuk controller Home
    public $data;

    public function __construct()
    {
        parent::__construct();
        // load model M_auth
        $this->load->model("M_auth");

        if (!empty($this->session->userdata("email"))) { // jika session email masih ada

            $id_menu = $this->M_global->getData('m_menu', ['url' => 'Health'])->id;

            // ambil isi data berdasarkan email session dari table user, kemudian tampung ke variable $user
            $user = $this->M_global->getData("user", ["email" => $this->session->userdata("email")]);

            $cek_akses_menu = $this->M_global->getData('akses_menu', ['id_menu' => $id_menu, 'kode_role' => $user->kode_role]);
            if ($cek_akses_menu) {
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
            } else {
                // kirimkan kembali ke Auth
                redirect('Where');
            }
        } else { // selain itu
            // kirimkan kembali ke Auth
            redirect('Auth');
        }
    }

    // daftar page
    public function daftar()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Healt Management',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Pendaftaran',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Health/daftar_list',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Pendaftaran/Daftar', $parameter);
    }

    // fungsi list member
    public function daftar_list($param1 = '')
    {
        // parameter untuk list table
        $table            = 'member';
        $colum            = ['id', 'kode_prefix', 'kode_member', 'nama', 'email', 'password', 'secondpass', 'jkel', 'foto', 'kode_role', 'actived', 'joined', 'on_off', 'nohp', 'tmp_lahir', 'tgl_lahir', 'provinsi', 'kabupaten', 'kecamatan', 'desa', 'kodepos', 'nik', 'last_regist', 'status_regist', 'rt', 'rw', 'cek_karyawan', 'kode_karyawan'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param1   = '';

        // kondisi role
        $updated    = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted    = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list         = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data         = [];
        $no           = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                if ($rd->on_off < 1) {
                    if ($rd->kode_member == 'U00001') {
                        $upd_diss = 'disabled';
                    } else {
                        $upd_diss = '';
                    }
                } else {
                    $upd_diss = 'disabled';
                }
            } else {
                $upd_diss = 'disabled';
            }

            if ($deleted > 0) {
                if ($rd->on_off < 1) {
                    if ($rd->kode_member == 'U00001') {
                        $del_diss = 'disabled';
                    } else {
                        $del_diss = '';
                    }
                } else {
                    $del_diss = 'disabled';
                }
            } else {
                $del_diss = 'disabled';
            }

            $prov   = $this->M_global->getData('m_provinsi', ['kode_provinsi' => $rd->provinsi])->provinsi;
            $kab    = $this->M_global->getData('kabupaten', ['kode_kabupaten' => $rd->kabupaten])->kabupaten;
            $kec    = $this->M_global->getData('kecamatan', ['kode_kecamatan' => $rd->kecamatan])->kecamatan;

            $prefix = $this->M_global->getData('m_prefix', ['kode_prefix' => $rd->kode_prefix]);
            if ($prefix) {
                $prefix = $prefix->nama;
            } else {
                $prefix = 'None';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_member . (($rd->actived == 1) ? '<br><span class="badge badge-success">Aktif</span>' : '<br><span class="badge badge-dark">Non-aktif</span>');
            $row[]  = $rd->nik;
            $row[]  = $prefix . '. ' . $rd->nama . (($rd->kode_member == 'U00001') ? '' : (($rd->jkel == 'P') ? ' / Pria' : ' / Wanita')) . '<br><span class="badge badge-info">' . hitung_umur($rd->tgl_lahir) . '</span>';
            $row[]  = 'Prov. ' . $prov . ',<br>' . $kab . ',<br>Kec. ' . $kec . ',<br>Ds. ' . $rd->desa . ',<br>(POS: ' . $rd->kodepos . '), RT.' . $rd->rt . '/RW.' . $rd->rw;
            $row[]  = $rd->last_regist . (($rd->status_regist == 1) ? '<span class="badge badge-primary float-right">Buka</span>' : '<span class="badge badge-danger float-right">Tutup</span>');

            if ($rd->actived > 0) {
                $actived_akun = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" onclick="actived(' . "'" . $rd->kode_member . "', 0" . ')" ' . $upd_diss . '><i class="fa-solid fa-user-xmark"></i></button>';
            } else {
                $actived_akun = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" onclick="actived(' . "'" . $rd->kode_member . "', 1" . ')" ' . $upd_diss . '><i class="fa-solid fa-user-check"></i></button>';
            }

            $row[]  = '<div class="text-center">
                ' . $actived_akun . '
                <button type="button" style="margin-bottom: 5px;" class="btn btn-warning" onclick="ubah(' . "'" . $rd->kode_member . "'" . ')" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" style="margin-bottom: 5px;" class="btn btn-danger" onclick="hapus(' . "'" . $rd->kode_member . "'" . ')" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
                <br>
                <button type="button" style="margin-bottom: 5px;" class="btn btn-primary" onclick="info(' . "'" . $rd->kode_member . "'" . ')"><i class="fa-solid fa-circle-info"></i></button>
                <a type="button" style="margin-bottom: 5px;" target="_blank" class="btn btn-dark" href="' . site_url("Health/print_card/") . $rd->kode_member . '"><i class="fa-solid fa-id-badge"></i></a>
            </div>';

            $data[] = $row;
        }

        // hasil server side
        $output = [
            "draw"            => $_POST['draw'],
            "recordsTotal"    => $this->M_datatables->count_all($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1),
            "recordsFiltered" => $this->M_datatables->count_filtered($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1),
            "data"            => $data,
        ];

        // kirimkan ke view
        echo json_encode($output);
    }

    // fungsi cetak kartu member
    function print_card($kode_member)
    {
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $pencetak       = $this->M_global->getData('user', ['kode_user' => $this->session->userdata('kode_user')])->nama;

        $member = $this->M_global->getData('member', ['kode_member' => $kode_member]);

        $prov   = $this->M_global->getData('m_provinsi', ['kode_provinsi' => $member->provinsi])->provinsi;
        $kab    = $this->M_global->getData('kabupaten', ['kode_kabupaten' => $member->kabupaten])->kabupaten;
        $kec    = $this->M_global->getData('kecamatan', ['kode_kecamatan' => $member->kecamatan])->kecamatan;

        $judul = 'Kartu Member ' . $kode_member;
        $filename = $judul;

        $body .= '<table style="width: 100%; font-size: 9px;" cellpadding="2px">';

        $body .= '<tr>
            <td style="width: 23%;">RM</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . $kode_member . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">NIK</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . $member->nik . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . $member->nama . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">Tmp Lahir</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . $member->tmp_lahir . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">Tgl Lahir</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . $member->tgl_lahir . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">Umur</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . hitung_umur($member->tgl_lahir) . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">Alamat</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">Prov. ' . $prov . ', ' . $kab . ', Kec.' . $kec . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">&nbsp;</td>
            <td style="width: 2%;">&nbsp;</td>
            <td style="width: 75%;">Ds. ' . $member->desa . ' (' . $member->kodepos . ')</td>
        </tr>
        ';
        $body .= '</table>';

        cetak_pdf_small($judul, $body, 1, $position, $filename, $web_setting);
    }

    // fungsi form daftar
    public function form_daftar($param)
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param != '0') {
            $member = $this->M_global->getData('member', ['kode_member' => $param]);
        } else {
            $member = null;
        }

        $parameter = [
            $this->data,
            'judul'         => 'Health Management',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Pendaftaran',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => '',
            'data_member'   => $member,
            'role'          => $this->M_global->getResult('m_role'),
        ];

        $this->template->load('Template/Content', 'Pendaftaran/Form_member', $parameter);
    }

    // fungsi cek nik
    public function cekNik()
    {
        // ambil nik inputan
        $nik = $this->input->post('nik');

        // cek nik pada table member
        $cek = $this->M_global->jumDataRow('member', ['nik' => $nik]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi save/update member
    function member_proses($param)
    {
        $nik                = htmlspecialchars($this->input->post('nik'));
        $kode_prefix        = $this->input->post('kode_prefix');
        $nama               = htmlspecialchars($this->input->post('nama'));
        if ($param == 1) {
            $kode_member    = _codeMember($nama);
        } else {
            $kode_member    = htmlspecialchars($this->input->post('kodeMember'));
        }
        $email              = htmlspecialchars($this->input->post('email'));
        $secondpass         = htmlspecialchars($this->input->post('password'));
        $password           = md5($secondpass);
        $provinsi           = htmlspecialchars($this->input->post('provinsi'));
        $kabupaten          = htmlspecialchars($this->input->post('kabupaten'));
        $kecamatan          = htmlspecialchars($this->input->post('kecamatan'));
        $desa               = htmlspecialchars($this->input->post('desa'));
        $kodepos            = htmlspecialchars($this->input->post('kodepos'));
        $nohp               = htmlspecialchars($this->input->post('nohp'));
        $tmp_lahir          = htmlspecialchars($this->input->post('tmp_lahir'));
        $tgl_lahir          = htmlspecialchars($this->input->post('tgl_lahir'));
        $pekerjaan          = htmlspecialchars($this->input->post('pekerjaan'));
        $agama              = htmlspecialchars($this->input->post('agama'));
        $pendidikan         = htmlspecialchars($this->input->post('pendidikan'));
        $rt                 = htmlspecialchars($this->input->post('rt'));
        $rw                 = htmlspecialchars($this->input->post('rw'));
        $jkel               = htmlspecialchars($this->input->post('jkel'));
        if ($jkel == 'P') {
            $foto           = 'pria.png';
        } else {
            $foto           = 'wanit.png';
        }
        $suami              = htmlspecialchars($this->input->post('suami'));
        $nohp_suami         = htmlspecialchars($this->input->post('nohp_suami'));
        $alamat_suami       = htmlspecialchars($this->input->post('alamat_suami'));
        $istri              = htmlspecialchars($this->input->post('istri'));
        $nohp_istri         = htmlspecialchars($this->input->post('nohp_istri'));
        $alamat_istri       = htmlspecialchars($this->input->post('alamat_istri'));
        $kode_karyawan      = htmlspecialchars($this->input->post('kode_karyawan'));
        $kode_role          = (($kode_karyawan == '') ? 'R0005' : $this->M_global->getData('user', ['kode_user' => $kode_karyawan])->kode_role);
        $cek_karyawan       = (($kode_karyawan == '') ? 0 : 1);
        $joined             = date('Y-m-d H:i:s');
        $actived            = 1;
        $on_off             = 0;
        $last_regist        = 0;
        $status_regist      = 0;

        if ($param == 1) { // jika parameternya 1

            $isi = [
                'kode_member'   => $kode_member,
                'kode_prefix'   => $kode_prefix,
                'nama'          => (($cek_karyawan == 1) ? $this->M_global->getData('user', ['kode_user' => $kode_karyawan])->nama : $nama),
                'email'         => (($cek_karyawan == 1) ? $this->M_global->getData('user', ['kode_user' => $kode_karyawan])->email : $email),
                'password'      => $password,
                'secondpass'    => $secondpass,
                'nohp'          => (($cek_karyawan == 1) ? $this->M_global->getData('user', ['kode_user' => $kode_karyawan])->nohp : $nohp),
                'tmp_lahir'     => $tmp_lahir,
                'tgl_lahir'     => $tgl_lahir,
                'pekerjaan'     => $pekerjaan,
                'agama'         => $agama,
                'pendidikan'    => $pendidikan,
                'provinsi'      => $provinsi,
                'kabupaten'     => $kabupaten,
                'kecamatan'     => $kecamatan,
                'desa'          => $desa,
                'kodepos'       => $kodepos,
                'rt'            => $rt,
                'rw'            => $rw,
                'nik'           => $nik,
                'jkel'          => $jkel,
                'foto'          => $foto,
                'kode_role'     => $kode_role,
                'joined'        => $joined,
                'on_off'        => $on_off,
                'last_regist'   => $last_regist,
                'status_regist' => $status_regist,
                'actived'       => $actived,
                'suami'         => $suami,
                'nohp_suami'    => $nohp_suami,
                'alamat_suami'  => $alamat_suami,
                'istri'         => $istri,
                'nohp_istri'    => $nohp_istri,
                'alamat_istri'  => $alamat_istri,
                'cek_karyawan'  => $cek_karyawan,
                'kode_karyawan' => $kode_karyawan,
            ];
            $isi_sebelum = json_encode(['']);
            $isi_sesudah = json_encode($isi);
            // jalankan fungsi simpan
            $cek = $this->M_global->insertData('member', $isi);

            $cek_param = 'Menambahkan';
        } else { // selain itu

            $isi = [
                'kode_member'   => $kode_member,
                'kode_prefix'   => $kode_prefix,
                'nama'          => (($cek_karyawan == 1) ? $this->M_global->getData('user', ['kode_user' => $kode_karyawan])->nama : $nama),
                'email'         => (($cek_karyawan == 1) ? $this->M_global->getData('user', ['kode_user' => $kode_karyawan])->email : $email),
                'password'      => $password,
                'secondpass'    => $secondpass,
                'nohp'          => (($cek_karyawan == 1) ? $this->M_global->getData('user', ['kode_user' => $kode_karyawan])->nohp : $nohp),
                'tmp_lahir'     => $tmp_lahir,
                'tgl_lahir'     => $tgl_lahir,
                'pekerjaan'     => $pekerjaan,
                'agama'         => $agama,
                'pendidikan'    => $pendidikan,
                'provinsi'      => $provinsi,
                'kabupaten'     => $kabupaten,
                'kecamatan'     => $kecamatan,
                'desa'          => $desa,
                'kodepos'       => $kodepos,
                'rt'            => $rt,
                'rw'            => $rw,
                'nik'           => $nik,
                'jkel'          => $jkel,
                'foto'          => $foto,
                'kode_role'     => $kode_role,
                'actived'       => $actived,
                'suami'         => $suami,
                'nohp_suami'    => $nohp_suami,
                'alamat_suami'  => $alamat_suami,
                'istri'         => $istri,
                'nohp_istri'    => $nohp_istri,
                'alamat_istri'  => $alamat_istri,
                'cek_karyawan'  => $cek_karyawan,
                'kode_karyawan' => $kode_karyawan,
            ];

            $sebelum = $this->M_global->getData('member', ['kode_member' => $kode_member]);
            $isi_sebelum = json_encode($sebelum);
            // jalankan fungsi update
            $cek = $this->M_global->updateData('member', $isi, ['kode_member' => $kode_member]);
            $sesudah = $this->M_global->getData('member', ['kode_member' => $kode_member]);

            $isi_sesudah = json_encode($sesudah);
            $cek_param = 'Mengubah';
        }

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Data Member', $cek_param, $kode_member, $nama, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi aktif/nonaktif member
    public function activeddaftar($kode_member, $param)
    {
        // jalankan fungsi update actived member
        $cek = $this->M_global->updateData('member', ['actived' => $param], ['kode_member' => $kode_member]);

        if ($param == 1) {
            $cek_param = 'Di aktifkan';
        } else {
            $cek_param = 'Di nonaktifkan';
        }

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Data Member', $cek_param, $kode_member, $this->M_global->getData('member', ['kode_member' => $kode_member])->nama);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi ambil info member
    public function getInfoMember($kode_member)
    {
        $data = $this->db->query("SELECT m.*, p.keterangan AS pendidikan, pk.keterangan AS pekerjaan, a.keterangan AS agama FROM member m JOIN m_pendidikan p ON m.pendidikan = p.kode_pendidikan JOIN m_pekerjaan pk ON m.pekerjaan = pk.kode_pekerjaan JOIN m_agama a ON m.agama = a.kode_agama WHERE m.kode_member =  '$kode_member'")->row();

        echo json_encode($data);
    }

    // fungsi hapus member
    public function delMember($kode_member)
    {
        // jalankan fungsi hapus member berdasarkan kode_member
        $cek = $this->M_global->delData('member', ['kode_member' => $kode_member]);

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Data Member', 'Menghapus', $kode_member, $this->M_global->getData('member', ['kode_member' => $kode_member])->nama);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // pendaftaran page
    public function pendaftaran()
    {
        $cabang      = $this->session->userdata('cabang');
        $date        = date('Y-m-d');
        $anjungan    = $this->db->query('SELECT * FROM m_anjungan WHERE kode_cabang = "' . $cabang . '" AND tgl = "' . $date . '" AND panggil < 1 AND status < 1 ORDER BY id ASC LIMIT 1')->row();
        $nextx       = $this->db->query('SELECT * FROM m_anjungan WHERE kode_cabang = "' . $cabang . '" AND tgl = "' . $date . '" AND panggil > 0 AND status < 1 ORDER BY panggil ASC LIMIT 1')->row();
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Healt Management',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Pendaftaran',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'anjungan'      => (($anjungan) ? $anjungan->no_anjungan : (($nextx) ? $nextx->no_anjungan : '')),
            'list_data'     => 'Health/pendaftaran_list/',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Pendaftaran/Pendaftaran', $parameter);
    }

    // fungsi list pendaftaran
    public function pendaftaran_list($param1 = 1, $param2 = '')
    {
        // parameter untuk list table
        $table            = 'pendaftaran';
        $colum            = ['id', 'no_trx', 'kode_jenis_bayar', 'tgl_daftar', 'jam_daftar', 'kode_member', 'kode_poli', 'kode_ruang', 'kode_dokter', 'no_antrian', 'tgl_keluar', 'jam_keluar', 'tipe_daftar', 'status_trx', 'kode_user', 'shift'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['no_antrian' => 'desc'];
        $kondisi_param2   = 'kode_poli';
        $kondisi_param1   = 'tgl_daftar';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

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
            if ($updated > 0) {
                if ($rd->status_trx == 2) {
                    $upd_diss = 'disabled';
                } else {
                    if ($rd->status_trx == 1) {
                        $upd_diss = 'disabled';
                    } else {
                        $upd_diss = '';
                    }
                }
            } else {
                $upd_diss = 'disabled';
            }

            if ($deleted > 0) {
                if ($rd->status_trx == 2) {
                    $del_diss = 'disabled';
                } else {
                    if ($rd->status_trx == 1) {
                        $del_diss = 'disabled';
                    } else {
                        $del_diss = '';
                    }
                }
            } else {
                $del_diss = 'disabled';
            }

            $jenis_bayar = $this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $rd->kode_jenis_bayar]);

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->no_trx . '<br>' . (($rd->status_trx == 0) ? '<span class="badge badge-success">Buka</span>' : (($rd->status_trx == 2) ? '<span class="badge badge-danger">Batal</span>' : '<span class="badge badge-primary">Selesai</span>')) . (($rd->tipe_daftar == 1) ? ' <span class="badge badge-danger">Rawat Jalan</span>' : ' <span class="badge badge-warning">Rawat Inap</span>') . ' <span class="badge badge-dark badge-sm">' . (($jenis_bayar) ? $jenis_bayar->keterangan : '') . '</span>';
            $row[]  = $rd->kode_member . '<br>' . $this->M_global->getData('member', ['kode_member' => $rd->kode_member])->nama;
            $row[]  = date('d/m/Y', strtotime($rd->tgl_daftar)) . '<br>' . date('H:i:s', strtotime($rd->jam_daftar));
            $row[]  = '<span class="text-center">' . (($rd->status_trx < 1) ? '-' : (($rd->tgl_keluar == null) ? '' : date('d/m/Y', strtotime($rd->tgl_keluar))) . ' ~ ' . (($rd->jam_keluar == null) ? '' : date('H:i:s', strtotime($rd->jam_keluar)))) . '</>';
            $row[]  = $this->M_global->getData('m_poli', ['kode_poli' => $rd->kode_poli])->keterangan . (($rd->kode_ruang == null) ? '' :  '<br>(' . $this->M_global->getData('m_ruang', ['kode_ruang' => $rd->kode_ruang])->keterangan . ')');
            $row[]  = 'Dr. ' . $this->M_global->getData('dokter', ['kode_dokter' => $rd->kode_dokter])->nama;
            $row[]  = $rd->no_antrian;
            $row[]  = (!empty($this->M_global->getData('user', ['kode_user' => $rd->kode_user])) ? $this->M_global->getData('user', ['kode_user' => $rd->kode_user])->nama : 'xxx') . '<br><span class="badge badge-danger">Shift: ' . $rd->shift . '</span>';

            if ($rd->status_trx < 1) {
                $actived_akun = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="batalkan" onclick="actived(' . "'" . $rd->no_trx . "', 0" . ')" ' . $upd_diss . '><i class="fa-solid fa-user-xmark"></i></button>';
            } else {
                $actived_akun = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" disabled><i class="fa-solid fa-user-check"></i></button>';
            }

            $row[]  = '<div class="text-center">
                ' . $actived_akun . '
                <button type="button" style="margin-bottom: 5px;" class="btn btn-warning" title="ubah" onclick="ubah(' . "'" . $rd->no_trx . "'" . ')" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" style="margin-bottom: 5px;" class="btn btn-danger" title="hapus" onclick="hapus(' . "'" . $rd->no_trx . "'" . ')" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
                <br>
                <button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="kirim email" onclick="email(' . "'" . $rd->no_trx . "'" . ')"><i class="fa-solid fa-envelope-open-text"></i></button>
                <a type="button" target="_blank" style="margin-bottom: 5px;" title="cetak" class="btn btn-dark" href="' . site_url("Health/print_pendaftaran/") . $rd->no_trx . '/0"><i class="fa-solid fa-id-badge"></i></a>
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

    // fungsi kirim email barang in
    public function email($no_trx)
    {
        $email = $this->input->get('email');

        $header = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);

        $judul = 'Pendaftaran ' . $header->no_trx;

        // $attched_file    = base_url() . 'assets/file/pdf/' . $judul . '.pdf';ahmad.ummgl@gmail.com
        $attched_file    = $_SERVER["DOCUMENT_ROOT"] . '/first_apps/assets/file/pdf/' . $judul . '.pdf';

        $ready_message   = "";
        $ready_message   .= "<table border=0>
            <tr>
                <td style='width: 30%;'>No Pendaftaran</td>
                <td style='width: 10%;'> : </td>
                <td style='width: 60%;'> $header->no_trx </td>
            </tr>
            <tr>
                <td style='width: 30%;'>Tgl/Jam Daftar</td>
                <td style='width: 10%;'> : </td>
                <td style='width: 60%;'>" . date('d-m-Y', strtotime($header->tgl_daftar)) . " / " . date('H:i:s', strtotime($header->jam_daftar)) . "</td>
            </tr>
            <tr>
                <td style='width: 30%;'>Pembeli</td>
                <td style='width: 10%;'> : </td>
                <td style='width: 60%;'>" . $this->M_global->getData('member', ['kode_member' => $header->kode_member])->nama . "</td>
            </tr>
            <tr>
                <td style='width: 30%;'>Poli/Dokter</td>
                <td style='width: 10%;'> : </td>
                <td style='width: 60%;'>" . $this->M_global->getData('m_poli', ['kode_poli' => $header->kode_poli])->keterangan . ' / Dr. ' . $this->M_global->getData('dokter', ['kode_dokter' => $header->kode_dokter])->nama . "</td>
            </tr>
            <tr>
                <td style='width: 30%;'>Status</td>
                <td style='width: 10%;'> : </td>
                <td style='width: 60%;'>" . (($header->status_trx == 0) ? 'Open' : (($header->status_trx == 2) ? 'Cancel' : 'Close')) . " </td>
            </tr>
        </table>";

        $server_subject = $judul;

        if ($this->email->send_my_email($email, $server_subject, $ready_message, $attched_file)) {
            echo json_encode(["status" => 1, 'result' => $attched_file]);
        } else {
            echo json_encode(["status" => 0]);
        }

        // echo json_encode($attched_file);
    }

    // fungsi cetak histori
    public function print_hispas($no_trx)
    {
        $kode_cabang          = $this->session->userdata('cabang');
        $web_setting          = $this->M_global->getData('web_setting', ['id' => 1]);

        $position             = 'P'; // cek posisi l/p

        // body cetakan
        $body                 = '';
        $body                 .= '<br><br>'; // beri jarak antara kop dengan body

        $pendaftaran          = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);
        $tarif_paket_pasien   = $this->M_global->getDataResult('tarif_paket_pasien', ['no_trx' => $no_trx]);
        $pembayaran           = $this->M_global->getData('pembayaran', ['no_trx' => $no_trx]);
        $barang_out_header    = $this->M_global->getData('barang_out_header', ['invoice' => $pembayaran->inv_jual]);
        $barang_out_detail    = $this->M_global->getDataResult('barang_out_detail', ['invoice' => $pembayaran->inv_jual]);
        $member               = $this->M_global->getData('member', ['kode_member' => $pendaftaran->kode_member]);

        $judul                = 'Riwayat ~ ' . $no_trx;
        $filename             = $judul;

        if ($pendaftaran->status_trx == 1) {
            $open = '<input type="checkbox" style="width: 80px;" checked="checked"> Terbayar';
            $close = '<input type="checkbox" style="width: 80px;"> Belum Bayar';
        } else {
            $open = '<input type="checkbox" style="width: 80px;"> Terbayar';
            $close = '<input type="checkbox" style="width: 80px;" checked="checked"> Belum Bayar';
        }

        $body .= '<table style="width: 100%; font-size: 14px;" cellpadding="2px" autosize="1">
            <tr>
                <td>(Masuk: ' . date('d/m/Y', strtotime($pendaftaran->tgl_daftar)) . ' - ' . date('H:i:s', strtotime($pendaftaran->jam_daftar)) . ') ~ (Keluar: ' . date('d/m/Y', strtotime($pendaftaran->tgl_keluar)) . ' - ' . date('H:i:s', strtotime($pendaftaran->jam_keluar)) . ')</td>
                <td colspan="2" style="text-align: right; color: white;"><span style="border: 1px solid #0e1d2e; background-color: #0e1d2e;">NO: #' . $no_trx . '</span></td>
            </tr>
        </table>';

        $body .= '<br>';

        $body .= '<table style="width: 100%; font-size: 14px;" cellpadding="2px" autosize="1">
            <tr>
                <td style="width: 10%;">Poli</td>
                <td style="width: 2%;">:</td>
                <td style="width: 38%;">' . (($pendaftaran->kode_poli != 'UMUM') ? $this->M_global->getData('m_poli', ['kode_poli' => $pendaftaran->kode_poli])->keterangan : 'UMUM') . '</td>
                <td style="width: 10%;">RM</td>
                <td style="width: 2%;">:</td>
                <td style="width: 38%;">' . $member->kode_member . '</td>
            </tr>
            <tr>
                <td style="width: 10%;">Dr. Poli</td>
                <td style="width: 2%;">:</td>
                <td style="width: 38%;">' . $this->M_global->getData('dokter', ['kode_dokter' => $pendaftaran->kode_dokter])->nama . '</td>
                <td style="width: 10%;">Nama</td>
                <td style="width: 2%;">:</td>
                <td style="width: 38%;">' . $member->nama . '</td>
            </tr>
            <tr>
                <td style="width: 10%;">Ruangan</td>
                <td style="width: 2%;">:</td>
                <td style="width: 38%;">' . $this->M_global->getData('m_ruang', ['kode_ruang' => $pendaftaran->kode_ruang])->keterangan . '</td>
                <td style="width: 10%;">Umur</td>
                <td style="width: 2%;">:</td>
                <td style="width: 38%;">' . hitung_umur($member->tgl_lahir) . '</td>
            </tr>
            <tr>
                <td style="width: 10%;">Antrian</td>
                <td style="width: 2%;">:</td>
                <td style="width: 38%;">' . $pendaftaran->no_antrian . '</td>
                <td style="width: 10%;">Status</td>
                <td style="width: 2%;">:</td>
                <td style="width: 38%;">' . (($pendaftaran->status_trx == 0) ? 'Open' : (($pendaftaran->status_trx == 2) ? 'Cancel' : 'Close')) . '</td>
            </tr>
            <tr>
                <td style="width: 10%;"></td>
                <td style="width: 2%;"></td>
                <td style="width: 38%;"></td>
                <td style="width: 10%;">Status Bayar</td>
                <td style="width: 2%;">:</td>
                <td style="width: 38%;">' . $open . '&nbsp;&nbsp;' . $close . '</td>
            </tr>
        </table>';

        $body .= '<br>';

        $body .= '<table style="width: 100%; font-size: 18px;" autosize="1">
            <tr>
                <td><span style="background-color: #0e1d2e; color: white; margin: 10px 10px; text-align: center; border-radius: 5px;">~ Tarif Paket #' . $pembayaran->no_trx . ' / ' . $this->M_global->getData('user', ['kode_user' => $pendaftaran->kode_user])->nama . ' / ' . date('d-m-Y', strtotime($pendaftaran->tgl_daftar)) . ' - ' . date('H:i:s', strtotime($pendaftaran->jam_daftar)) . '</span></td>
            </tr>
        </table>';

        if ($tarif_paket_pasien) {
            $body .= '<table style="width: 100%; font-size: 14px;" autosize="1" cellpadding="5px">
                <thead>
                    <tr style="background-color: #0e1d2e;">
                        <th style="color: white; width: 5%;">No</th>
                        <th style="color: white; width: 55%;">Tindakan</th>
                        <th style="color: white; width: 20%;">Kunjungan</th>
                        <th style="color: white; width: 20%;">Harga</th>
                    </tr>
                </thead>
                <tbody>';
            $totalPaket = 0;
            $nop = 1;
            foreach ($tarif_paket_pasien as $tpp) {
                $paket = $this->M_global->getData('tarif_paket', ['kode_tarif' => $tpp->kode_tarif, 'kunjungan' => $tpp->kunjungan, 'kode_cabang' => $kode_cabang]);
                $body .= '<tr>
                    <td style="border: 1px solid black; text-align: right;">' . $nop++ . '</td>
                    <td style="border: 1px solid black; ">' . $this->M_global->getData('m_tarif', ['kode_tarif' => $tpp->kode_tarif])->nama . '</td>
                    <td style="border: 1px solid black; text-align: right;">' . number_format($tpp->kunjungan) . '</td>
                    <td style="border: 1px solid black; text-align: right;">' . number_format(($paket->jasa_rs + $paket->jasa_dokter + $paket->jasa_pelayanan + $paket->jasa_poli)) . '</td>
                </tr>';

                $totalPaket += ($paket->jasa_rs + $paket->jasa_dokter + $paket->jasa_pelayanan + $paket->jasa_poli);
            }
            $body .= '</tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right;">Total: Rp. </td>
                    <td style="text-align: right;">' . number_format($totalPaket) . '</td>
                </tr>
            </tfoot>
            </table>';

            $body .= '<br>';
        }

        if ($barang_out_header) {

            $body .= '<table style="width: 100%; font-size: 18px;" autosize="1">
                <tr>
                    <td><span style="background-color: #0e1d2e; color: white; margin: 10px 10px; text-align: center; border-radius: 5px;">~ Pembelian #' . $pembayaran->inv_jual . ' / ' . $this->M_global->getData('user', ['kode_user' => $barang_out_header->kode_user])->nama . ' / ' . date('d-m-Y', strtotime($barang_out_header->tgl_jual)) . ' - ' . date('H:i:s', strtotime($barang_out_header->jam_jual)) . '</span></td>
                </tr>
            </table>';


            $body .= '<table style="width: 100%; font-size: 14px;" autosize="1" cellpadding="5px">
                <thead>
                    <tr style="background-color: #0e1d2e;">
                        <th style="color: white; width: 5%;">No</th>
                        <th style="color: white; width: 35%;">Barang</th>
                        <th style="color: white; width: 10%;">Satuan</th>
                        <th style="color: white; width: 10%;">Harga</th>
                        <th style="color: white; width: 10%;">Qty</th>
                        <th style="color: white; width: 10%;">Diskon</th>
                        <th style="color: white; width: 10%;">Pajak</th>
                        <th style="color: white; width: 10%;">Jumlah</th>
                    </tr>
                </thead>
                <tbody>';
            $no = 1;
            foreach ($barang_out_detail as $bd) {
                $body .= '<tr>
                    <td style="border: 1px solid black; text-align: right;">' . $no++ . '</td>
                    <td style="border: 1px solid black; ">' . $bd->kode_barang . ' ~ ' . $this->M_global->getData('barang', ['kode_barang' => $bd->kode_barang])->nama . '</td>
                    <td style="border: 1px solid black; ">' . $this->M_global->getData('m_satuan', ['kode_satuan' => $bd->kode_satuan])->keterangan . '</td>
                    <td style="border: 1px solid black; text-align: right;">' . number_format($bd->harga) . '</td>
                    <td style="border: 1px solid black; text-align: right;">' . number_format($bd->qty) . '</td>
                    <td style="border: 1px solid black; text-align: right;">' . number_format($bd->discrp) . '</td>
                    <td style="border: 1px solid black; text-align: right;">' . number_format($bd->pajakrp) . '</td>
                    <td style="border: 1px solid black; text-align: right;">' . number_format($bd->jumlah) . '</td>
                </tr>';
            }
            $body .= '</tbody>
            <tfoot>
                <tr>
                    <td colspan="7" style="text-align: right;">Subtotal: Rp. </td>
                    <td style="text-align: right;">' . number_format($barang_out_header->subtotal) . '</td>
                </tr>
                <tr>
                    <td colspan="7" style="text-align: right;">Diskon: Rp. </td>
                    <td style="text-align: right;">' . number_format($barang_out_header->diskon) . '</td>
                </tr>
                <tr>
                    <td colspan="7" style="text-align: right;">Pajak: Rp. </td>
                    <td style="text-align: right;">' . number_format($barang_out_header->pajak) . '</td>
                </tr>
                <tr>
                    <td colspan="7" style="text-align: right;">Total: Rp. </td>
                    <td style="text-align: right;">' . number_format($barang_out_header->total) . '</td>
                </tr>
            </tfoot>
            </table>';

            $body .= '<br>';
        }

        $body .= '<table style="width: 100%; font-size: 18px;" autosize="1">
            <tr>
                <td><span style="background-color: #0e1d2e; color: white; margin: 10px 10px; text-align: center; border-radius: 5px;">~ Pembayaran #' . $pembayaran->invoice . ' / ' . $this->M_global->getData('user', ['kode_user' => $pembayaran->kode_user])->nama . ' / ' . date('d-m-Y', strtotime($pembayaran->tgl_pembayaran)) . ' - ' . date('H:i:s', strtotime($pembayaran->jam_pembayaran)) . '</span></td>
            </tr>
        </table>';

        $body .= '<table style="width: 100%; font-size: 14px;" autosize="1" cellpadding="5px">
            <thead>
                <tr>
                    <th style="background-color: #0e1d2e; color: white; width: 48%;" colspan="3">Pembayaran</th>
                    <th style="width: 4%;"></th>
                    <th style="background-color: #0e1d2e; color: white; width: 48%;" colspan="3">Kembalian</th>
                </tr>
                <tr>
                    <th style="background-color: #0e1d2e; color: white; width: 16%;">Uang Muka</th>
                    <th style="background-color: #0e1d2e; color: white; width: 16%;">Cash</th>
                    <th style="background-color: #0e1d2e; color: white; width: 16%;">Card</th>
                    <th style="width: 4%;"></th>
                    <th style="background-color: #0e1d2e; color: white; width: 24%;">Uang Muka</th>
                    <th style="background-color: #0e1d2e; color: white; width: 24%;">Cash</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border: 1px solid black; width: 16%; text-align: right;">' . number_format($pembayaran->um_keluar) . '</td>
                    <td style="border: 1px solid black; width: 16%; text-align: right;">' . number_format($pembayaran->cash) . '</td>
                    <td style="border: 1px solid black; width: 16%; text-align: right;">' . number_format($pembayaran->card) . '</td>
                    <td style="width: 4%;"></td>
                    <td style="border: 1px solid black; width: 24%; text-align: right;">' . number_format($pembayaran->um_masuk) . '</td>
                    <td style="border: 1px solid black; width: 24%; text-align: right;">' . (($pembayaran->cek_um > 0) ? 0 : number_format($pembayaran->kembalian)) . '</td>
                </tr>
            </tbody>
        </table>';

        cetak_pdf($judul, $body, 1, $position, $filename, $web_setting);
    }

    // fungsi cetak pendaftaran member
    public function print_pendaftaran($no_trx, $yes)
    {
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        $pendaftaran    = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);
        $member         = $this->M_global->getData('member', ['kode_member' => $pendaftaran->kode_member]);

        $judul          = 'Pendaftaran ' . $no_trx;
        $filename = $judul;

        if ($pendaftaran->status_trx == 1) {
            $open = '<input type="checkbox" style="width: 80px;" checked="checked"> Terbayar';
            $close = '<input type="checkbox" style="width: 80px;"> Belum Bayar';
        } else {
            $open = '<input type="checkbox" style="width: 80px;"> Terbayar';
            $close = '<input type="checkbox" style="width: 80px;" checked="checked"> Belum Bayar';
        }

        $body .= '<table style="width: 100%; font-size: 9px;" cellpadding="2px">';

        $body .= '<tr>
            <td colspan="3" style="text-align: right; color: white;"><span style="border: 1px solid #0e1d2e; background-color: #0e1d2e;">NO: #' . $no_trx . '</span></td>
        </tr>
        <tr>
            <td style="width: 23%;">RM</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . $member->kode_member . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . $member->nama . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">Umur</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . hitung_umur($member->tgl_lahir) . '</td>
        </tr>
        <tr>
            <td style="width: 100%;" colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td style="width: 23%;">Poli</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . (($pendaftaran->kode_poli != 'UMUM') ? $this->M_global->getData('m_poli', ['kode_poli' => $pendaftaran->kode_poli])->keterangan : 'UMUM') . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">Dr. Poli</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . $this->M_global->getData('dokter', ['kode_dokter' => $pendaftaran->kode_dokter])->nama . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">Ruangan</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . (($pendaftaran->kode_ruang == null) ? '' : $this->M_global->getData('m_ruang', ['kode_ruang' => $pendaftaran->kode_ruang])->keterangan) . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">Antrian</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . $pendaftaran->no_antrian . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">Status</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . (($pendaftaran->status_trx == 0) ? 'Open' : (($pendaftaran->status_trx == 2) ? 'Cancel' : 'Close')) . '</td>
        </tr>
        <tr>
            <td style="width: 100%;" colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td style="width: 23%;">Tgl Masuk</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . date('d/m/Y', strtotime($pendaftaran->tgl_daftar)) . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">Jam Masuk</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . date('H:i:s', strtotime($pendaftaran->jam_daftar)) . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">Tgl Keluar</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . (isset($pendaftaran->tgl_keluar) ? date('d/m/Y', strtotime($pendaftaran->tgl_keluar)) : '-')  . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">Jam Keluar</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . (isset($pendaftaran->jam_keluar) ? date('H:i:s', strtotime($pendaftaran->jam_keluar)) : '-')  . '</td>
        </tr>
        <tr>
            <td style="width: 100%;" colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td style="width: 23%;">Status</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . $open . '&nbsp;&nbsp;' . $close . '</td>
        </tr>
        ';
        $body .= '</table>';

        cetak_pdf_small($judul, $body, 1, $position, $filename, $web_setting, $yes);

        doc_px($no_trx, $judul, $no_trx, json_encode($pendaftaran));
    }

    // fungsi ambil riwayat
    public function getRiwayat($kode_member)
    {
        $data = $this->db->query('SELECT 
            p.no_trx, p.tgl_daftar, p.jam_daftar, p.tgl_keluar, p.jam_keluar, pol.keterangan AS nama_poli, dok.nama AS nama_dokter,
            c.cabang, p.status_trx
        FROM pendaftaran p 
        JOIN cabang c ON c.kode_cabang = p.kode_cabang
        JOIN m_poli pol ON pol.kode_poli = p.kode_poli 
        JOIN dokter dok ON dok.kode_dokter = p.kode_dokter 
        WHERE p.kode_member = "' . $kode_member . '"')->result();

        $member = $this->M_global->getData('member', ['kode_member' => $kode_member]);
        echo json_encode([$data, $member]);
    }

    // form pendaftaran page
    public function form_pendaftaran($param, $no_trx = '')
    {
        $no_anjungan              = $this->input->get('no_anjungan');
        // website config
        $web_setting              = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version              = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param != '0') {
            $pendaftaran          = $this->M_global->getData('pendaftaran', ['no_trx' => $param]);
            $daftar_ulang         = null;
            $kode_member          = $pendaftaran->kode_member;

            $riwayat = $this->db->where(['kode_member' => $kode_member])
                ->order_by('id', 'DESC')
                ->get('pendaftaran')
                ->result();
            $tarif_paket_pasien   = $this->M_global->getDataResult('tarif_paket_pasien', ['no_trx' => $param]);
        } else {
            if ($no_trx == '') {
                $pendaftaran          = null;
                $daftar_ulang         = null;
                $riwayat              = null;
                $tarif_paket_pasien   = null;
            } else {
                $pendaftaran          = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);
                $daftar_ulang         = $this->M_global->getData('daftar_ulang', ['no_trx' => $no_trx]);

                $kode_member          = $pendaftaran->kode_member;

                $riwayat = $this->db->where(['kode_member' => $kode_member])
                    ->order_by('id', 'DESC')
                    ->get('pendaftaran')
                    ->result();
                $tarif_paket_pasien   = null;
            }
        }

        $parameter = [
            $this->data,
            'judul'             => 'Health Management',
            'nama_apps'         => $web_setting->nama,
            'page'              => 'Pendaftaran',
            'web'               => $web_setting,
            'web_version'       => $web_version->version,
            'list_data'         => '',
            'data_pendaftaran'  => $pendaftaran,
            'riwayat'           => $riwayat,
            'role'              => $this->M_global->getResult('m_role'),
            'm_cara_masuk'      => $this->M_global->getResult('m_cara_masuk'),
            'pasien_paket'      => $tarif_paket_pasien,
            'ulang'             => (($param != '0') ? '0' : '1'),
            'daftar_ulang'      => $daftar_ulang,
            'no_anjungan'       => $no_anjungan,
        ];

        $this->template->load('Template/Content', 'Pendaftaran/Form_pendaftaran', $parameter);
    }

    // jadwal dokter
    public function jd_dkr()
    {
        $jadwal = $this->db->query('SELECT jd.*, CONCAT("Dr. ", d.nama) AS nama FROM jadwal_dokter jd JOIN dokter d ON jd.kode_dokter = d.kode_dokter WHERE jd.kode_cabang = "' . $this->session->userdata('cabang') . '"')->result();
        echo json_encode($jadwal);
    }

    public function getToken($no_trx)
    {
        $pembayaran = $this->M_global->getData('pembayaran', ['no_trx' => $no_trx]);

        echo json_encode(['status' => 1, 'token' => $pembayaran->token_pembayaran]);
    }

    public function getPaket($kode_multiprice, $kode_member)
    {
        $paket = $this->M_global->getDataResult('paket_kunjungan', ['kode_multiprice' => $kode_multiprice]);

        if (!empty($paket)) {
            // Cek apakah ada kunjungan dengan status 2
            $tarif_status2 = $this->db->query("SELECT * FROM tarif_paket_pasien WHERE kode_multiprice = '$kode_multiprice' AND kode_member = '$kode_member' AND status = 2 ORDER BY id DESC LIMIT 1")->row();

            if (!empty($tarif_status2)) {
                // Jika ada status 2, gunakan kunjungan dari status 2
                $new_kunjungan = (int)$tarif_status2->kunjungan;
            } else {
                // Jika tidak ada status 2, ambil kunjungan terakhir dengan status <> 2
                $tarif = $this->db->query("SELECT * FROM tarif_paket_pasien WHERE kode_multiprice = '$kode_multiprice' AND kode_member = '$kode_member' AND status <> 2 ORDER BY id DESC LIMIT 1")->row();

                if (!empty($tarif)) {
                    $last_kunjungan = (int)$tarif->kunjungan;
                    $count_paket = count($paket);

                    // Jika kunjungan terakhir sama dengan jumlah paket, reset ke 1, jika tidak tambah 1
                    if ($last_kunjungan >= $count_paket) {
                        $new_kunjungan = 1;
                    } else {
                        $new_kunjungan = $last_kunjungan + 1;
                    }
                } else {
                    $new_kunjungan = 1;
                }
            }

            $harga = $this->M_global->getData('paket_kunjungan', [
                'kode_multiprice' => $kode_multiprice,
                'kunjungan' => $new_kunjungan
            ]);

            echo json_encode([
                'status' => 1,
                'kunjungan' => $new_kunjungan,
                'harga' => ($harga ? ($harga->klinik + $harga->dokter + $harga->pelayanan + $harga->poli) : 0)
            ]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi cek member terdaftar/ tidak
    public function cekStatusMember()
    {
        $kode_member = $this->input->post('kode_member');

        $member = $this->M_global->getData('member', ['kode_member' => $kode_member]);

        if ($member->status_regist < 1) {
            echo json_encode(['status' => 1]);
        } else {
            $last_regist = $member->last_regist;
            $pendaftaran = $this->M_global->getData('pendaftaran', ['no_trx' => $last_regist]);

            if ($pendaftaran) {
                $cabang = $this->M_global->getData('cabang', ['kode_cabang' => $pendaftaran->kode_cabang]);
                echo json_encode(['status' => 0, "kode_member" => $kode_member, "cabang" => $cabang->inisial_cabang, "tgl" => date('d-m-Y', strtotime($pendaftaran->tgl_daftar))]);
            } else {
                echo json_encode(['status' => 1]);
            }
        }
    }

    // cek data membering untuk triage
    public function getMembering()
    {
        $key    = $this->input->get('key');
        $cek    = $this->db->query('SELECT * FROM member WHERE kode_member LIKE "%' . $key . '%" OR nama LIKE "%' . $key . '%"')->row();
        $triage = $this->db->query('SELECT * FROM triage_header WHERE nama = "' . $key . '" AND kode_cabang = "' . $this->session->userdata('cabang') . '"')->row();

        if ($cek) {
            echo json_encode(['status' => 1, 'kode_member' => $cek->kode_member, 'nama' => $cek->nama, 'no_triage' => $triage->no_triage]);
        } else {
            echo json_encode(['status' => 0, 'kode_member' => '', 'nama' => '']);
        }
    }

    // fungsi pendaftara proses
    public function pendaftaran_proses($param)
    {
        // variable
        $kode_user        = $this->session->userdata('kode_user');
        $shift            = $this->session->userdata('shift');
        $kode_cabang      = $this->session->userdata('cabang');

        $no_anjungan      = $this->input->post('no_anjungan');
        $kode_poli        = $this->input->post('kode_poli');
        $kode_dokter      = $this->input->post('kode_dokter');
        $tgl_daftar       = date('Y-m-d');
        $kode_member      = $this->input->post('kode_member');

        if ($param == 1) { // jika param = 1
            // buat kode baru
            $no_trx       = _kodeTrx($kode_poli, $kode_cabang);
            $no_antrian   = _noAntrian($kode_poli, $kode_cabang, $tgl_daftar);
        } else { // selain itu
            // ambil dari inputan
            $no_trx       = $this->input->post('no_trx');
            $no_antrian   = $this->input->post('no_antrian');
        }

        $no_triage        = $this->input->post('no_triage');

        $jam_daftar       = date('H:i:s');
        $kode_bed         = $this->input->post('kode_bed');
        $tipe_daftar      = $this->input->post('tipe_daftar');

        $kode_multiprice  = $this->input->post('kode_multiprice');
        $harga            = $this->input->post('harga');
        $kunjungan        = $this->input->post('kunjungan');

        $ulang            = $this->input->post('ulang');
        $kode_jenis_bayar = $this->input->post('kode_jenis_bayar');

        $kelas            = $this->input->post('kelas');
        $kode_masuk       = $this->input->post('cara_masuk');

        // cek triage
        $triage           = $this->db->query('SELECT * FROM triage_header WHERE no_triage = "' . $no_triage . '"')->row();

        if ($triage) {
            $cek_triage_htt = $this->M_global->getDataResult('triage_htt', ['no_triage' => $no_triage]);
            if (count($cek_triage_htt) > 0) {
                // jika sudah ada triage htt, update data
                foreach ($cek_triage_htt as $htt) {
                    $data_htt = [
                        'no_trx'      => $no_trx,
                        'fisik'       => $htt->fisik,
                        'desc_fisik'  => $htt->ket_fisik,
                    ];

                    $this->M_global->insertData('emr_dok_fisik', $data_htt);
                }
            }

            $data_emr_per = [
                'no_trx'                => $no_trx,
                'kode_member'           => $kode_member,
                'umur'                  => hitung_umur($triage->tgl_lahir),
                'date_per'              => date('Y-m-d', strtotime($triage->tgl_triage)),
                'time_per'              => date('H:i:s', strtotime($triage->jam_triage)),
                'sempoyongan'           => $triage->sempoyongan,
                'berjalan_dgn_alat'     => $triage->berjalan_dgn_alat,
                'penompang'             => $triage->penompang,
                'keterangan_assesment'  => $triage->ket_lain,
                'penyakit_keluarga'     => $triage->penyakit,
                'alergi'                => $triage->alergi,
                'tekanan_darah'         => $triage->td,
                'nadi'                  => $triage->nadi,
                'suhu'                  => $triage->suhu,
                'bb'                    => $triage->bb,
                'tb'                    => $triage->tb,
                'pernapasan'            => $triage->pernapasan,
                'saturasi'              => $triage->saturasi,
                'gizi'                  => $triage->gizi,
                'hamil'                 => $triage->status_hamil,
                'hpht'                  => date('Y-m-d', strtotime($triage->hpht)),
                'keterangan_hamil'      => $triage->ket_hamil,
                'scale'                 => $triage->skala_nyeri,
                'bicara'                => $triage->cara_bicara,
                'emosi'                 => $triage->psikologi,
                'spiritual'             => $triage->spiritual,
                'diagnosa_per'          => $triage->diagnosa,
                'anamnesa_per'          => $triage->anamnesa,
                'kode_user'             => $triage->user_input,
            ];

            $this->M_global->insertData('emr_per', $data_emr_per);

            $this->M_global->updateData('triage_header', ['status' => 1], ['no_triage' => $triage->no_triage]);
        }

        if ($tipe_daftar == 1) {
            // Mendapatkan jadwal dokter berdasarkan kode_dokter, hari, dan kode_cabang
            $get_ruang = $this->M_global->getData('jadwal_dokter', [
                'kode_dokter' => $kode_dokter,
                'hari' => date('l', strtotime($tgl_daftar)),
                'kode_cabang' => $kode_cabang
            ]);

            if ($get_ruang) {
                // Mengecek apakah ada pasien yang sudah mendaftar
                $cek_px_daftar = $this->M_global->getDataResult('pendaftaran', [
                    'kode_dokter' => $get_ruang->kode_dokter,
                    'kode_cabang' => $kode_cabang,
                    'status_trx <> ' => 2,
                ]);

                // Memeriksa apakah ada batasan jumlah pasien (limit_px)
                if ($get_ruang->limit_px > 0) {
                    // Jika jumlah pendaftaran pasien melebihi limit
                    if (count($cek_px_daftar) >= $get_ruang->limit_px) {
                        // Mengirimkan status 2 (limit penuh)
                        echo json_encode(['status' => 2, 'limit' => number_format($get_ruang->limit_px), 'dokter' => $this->M_global->getData('dokter', ['kode_dokter' => $get_ruang->kode_dokter])->nama]);
                        return;
                    }
                }

                // Menentukan kode ruang
                $kode_ruang = $get_ruang->kode_ruang;
            } else {
                // Jika jadwal dokter tidak ditemukan
                $kode_ruang = '';
            }
        } else {
            // Jika tipe pendaftaran bukan 1, menggunakan kode ruang yang dikirimkan
            $kode_ruang = $this->input->post('kode_ruang');
        }

        // jika ada last antrian + 1, jika tidak ada 0 + 1

        // masukan kedalam variable $isi
        $isi = [
            'kode_cabang'       => $kode_cabang,
            'no_trx'            => $no_trx,
            'kelas'             => $kelas,
            'kode_masuk'        => $kode_masuk,
            'tgl_daftar'        => $tgl_daftar,
            'jam_daftar'        => $jam_daftar,
            'kode_member'       => $kode_member,
            'no_antrian'        => $no_antrian,
            'kode_jenis_bayar'  => $kode_jenis_bayar,
            'kode_poli'         => $kode_poli,
            'kode_dokter'       => $kode_dokter,
            'tgl_keluar'        => null,
            'jam_keluar'        => null,
            'status_trx'        => 0,
            'kode_ruang'        => $kode_ruang,
            'kode_bed'          => $kode_bed,
            'kode_user'         => $kode_user,
            'tipe_daftar'       => $tipe_daftar,
            'shift'             => $shift,
        ];

        $layar = [
            'kode_cabang'   => $kode_cabang,
            'no_trx'        => $no_trx,
            'kode_ruang'    => $kode_ruang,
            'no_antrian'    => $no_antrian,
            'kode_poli'     => $kode_poli,
            'status'        => 0,
            'panggil'       => 0,
        ];

        if ($param == 1) { // jika param = 1
            aktifitas_user_transaksi('Pendaftaran', 'Mendaftarkan ' . $kode_member, $no_trx);

            // lakukan fungsi tambah ke table pendaftaran
            $cek = [
                $this->M_global->insertData('pendaftaran', $isi),
                $this->M_global->insertData('layar_perawat', $layar),
                $this->M_global->updateData('member', ['status_regist' => 1, 'last_regist' => $no_trx], ['kode_member' => $kode_member]),
                $this->M_global->updateData('bed_cabang', ['status_bed' => 1], ['kode_bed' => $kode_bed, 'kode_cabang' => $kode_cabang]),
                $this->M_global->delData('daftar_ulang', ['kode_member' => $kode_member]),
            ];
        } else { // selain itu
            aktifitas_user_transaksi('Pendaftaran', 'Mengubah Pendaftaran' . $kode_member, $no_trx);
            // lakukan fungsi ubah ke table pendaftaran
            $cek = [
                $this->M_global->updateData('layar_perawat', $layar, ['no_trx' => $no_trx]),
                $this->M_global->updateData('pendaftaran', $isi, ['no_trx' => $no_trx]),
                $this->M_global->delData('tarif_paket_pasien', ['no_trx' => $no_trx]),
                $this->M_global->updateData('bed_cabang', ['status_bed' => 1], ['kode_bed' => $kode_bed, 'kode_cabang' => $kode_cabang]),
            ];
        }

        if (isset($kode_multiprice)) {
            $jumPaket = count($kode_multiprice);

            for ($z = 0; $z <= ($jumPaket - 1); $z++) {
                $tindakan = $this->M_global->getData('paket_kunjungan', ['kode_multiprice' => $kode_multiprice[$z], 'kunjungan' => $kunjungan[$z]]);

                $paket = [
                    'no_trx'             => $no_trx,
                    'kode_multiprice'    => $kode_multiprice[$z],
                    'kode_tindakan'      => $tindakan->kode_tindakan,
                    'penjamin'           => $kode_jenis_bayar,
                    'poli'               => $kode_poli,
                    'kelas'              => $kelas,
                    'harga'              => str_replace(',', '', $harga[$z]),
                    'kode_member'        => $kode_member,
                    'status'             => 0,
                    'kunjungan'          => $kunjungan[$z],
                ];

                $this->M_global->insertData('tarif_paket_pasien', $paket);
            }
        }

        $this->print_pendaftaran($no_trx, 1);

        if ($cek) { // jika fungsi berjalan
            $cek_anjungan = $this->M_global->getData('m_anjungan', ['no_anjungan' => $no_anjungan, 'kode_cabang' => $kode_cabang, 'tgl' => date('Y-m-d')]);

            if ($cek_anjungan) {
                $this->M_global->updateData('m_anjungan', ['status' => 1, 'kode_member' => $kode_member, 'kode_user_panggil' => $kode_user, 'no_trx' => $no_trx], ['no_anjungan' => $no_anjungan, 'kode_cabang' => $kode_cabang, 'tgl' => date('Y-m-d')]);
            }
            // kirimkan status 1 ke view
            echo json_encode(['status' => 1, 'no_trx' => $no_trx]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi aktif/nonaktif pendaftaran
    public function activedpendaftaran($no_trx)
    {
        // jalankan fungsi update actived pendaftaran
        $pendaftaran = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);
        $reservasi = $this->M_global->getData('reservasi', ['no_trx' => $no_trx]);
        aktifitas_user_transaksi('Pendaftaran', 'Membatalkan Pendaftaran ' . $pendaftaran->kode_member, $no_trx);

        if ($reservasi) {
            $this->M_global->updateData('reservasi', ['status_reservasi' => 2, 'tgl_batal' => date('Y-m-d'), 'jam_batal' => date('H:i:s'), 'user_batal' => $this->session->userdata('kode_user')], ['no_trx' => $no_trx]);
        }

        $cek = [
            $this->M_global->updateData('pendaftaran', ['status_trx' => 2, 'tgl_keluar' => date('Y-m-d'), 'jam_keluar' => date('H:i:s')], ['no_trx' => $no_trx]),
            $this->M_global->updateData('member', ['status_regist' => 0], ['last_regist' => $no_trx]),
            $this->M_global->updateData('tarif_paket_pasien', ['status' => 2], ['no_trx' => $no_trx]),
            $this->M_global->updateData('bed_cabang', ['status_bed' => 0], ['kode_bed' => $pendaftaran->kode_bed, 'kode_cabang' => $pendaftaran->kode_cabang]),
        ];

        if ($cek) { // jika fungsi berjalan
            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi hapus pendaftaran
    public function delPendaftaran($no_trx)
    {
        // jalankan fungsi hapus pendaftaran berdasarkan no_trx
        $member = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);
        $reservasi = $this->M_global->getData('reservasi', ['no_trx' => $no_trx]);

        if ($reservasi) {
            $this->M_global->delData('reservasi', ['no_trx' => $no_trx]);
        }

        aktifitas_user_transaksi('Pendaftaran', 'Menghapus Pendaftaran ' . $member->kode_member, $no_trx);

        $cek = [
            $this->M_global->delData('pendaftaran', ['no_trx' => $no_trx]),
            $this->M_global->delData('tarif_paket_pasien', ['no_trx' => $no_trx]),
            $this->M_global->updateData('bed_cabang', ['status_bed' => 0], ['kode_bed' => $member->kode_bed, 'kode_cabang' => $member->kode_cabang]),
            $this->M_global->updateData('m_anjungan', ['status' => 0, 'no_trx' => '', 'kode_member' => '', 'kode_user_panggil' => '', 'panggil' => 0, 'p_ulang' => 0], ['no_trx' => $no_trx]),
        ];

        if ($cek) { // jika fungsi berjalan
            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Jadwal Dokter
     * untuk menampilkan, menambahkan, dan mengubah jadwal dokter dalam sistem
     */

    // jadwal_dokter page
    public function jdokter()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Healt Management',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Jadwal Dokter',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Health/jdokter_list',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Pendaftaran/Jdokter', $parameter);
    }

    public function jdokter_list()
    {
        // Ambil data jadwal dari database
        $cabang = $this->session->userdata('cabang');

        $events = $this->db->select("
        jd.id,
        jd.kode_poli,
        jd.kode_dokter, 
        jd.kode_cabang,
        jd.status,
        jd.hari AS hari,
        jd.time_start,
        jd.time_end,
        jd.comment,
        jd.limit_px,
        d.nama AS nama_dokter,
        p.keterangan AS nama_poli
    ")
            ->from('jadwal_dokter jd')
            ->join('dokter d', 'd.kode_dokter = jd.kode_dokter')
            ->join('m_poli p', 'p.kode_poli = jd.kode_poli')
            ->where('jd.kode_cabang', $cabang)
            ->get()
            ->result();

        $data = [];
        $today = new DateTime();
        $endOfYear = new DateTime('last day of December this year');

        foreach ($events as $event) {
            if ($event->status == 1) {
                // Jadwal rutin mingguan
                $current_date = new DateTime();
                $current_date->modify('next ' . $event->hari);

                while ($current_date <= $endOfYear) {
                    $event_date_str = $current_date->format('Y-m-d');

                    // Cek apakah ada jadwal non-rutin pada tanggal ini
                    $is_overridden = $this->db->where('kode_dokter', $event->kode_dokter)
                        ->where('kode_cabang', $event->kode_cabang)
                        ->where('tgl_status', $event_date_str)
                        ->from('jadwal_dokter_status')
                        ->count_all_results() > 0;

                    if (!$is_overridden) {
                        $reservasi = $this->db->where('kode_dokter', $event->kode_dokter)
                            ->where('kode_poli', $event->kode_poli)
                            ->where('kode_cabang', $event->kode_cabang)
                            ->where('status_reservasi <>', 2)
                            ->where('DATE(tgl)', $event_date_str)
                            ->from('reservasi')
                            ->count_all_results();

                        $slot_sisa = ($event->limit_px > 0) ? ($event->limit_px - $reservasi) : 'Tidak dibatasi';
                        $title = "Dr. " . $event->nama_dokter . ", " . $event->nama_poli . " / " . $slot_sisa . (($event->limit_px > 0) ? ' Pasien' : '');

                        $data[] = [
                            'id'                => $event->id . '_' . $event_date_str,
                            'title'             => $title,
                            'start'             => $event_date_str . 'T' . $event->time_start,
                            'end'               => $event_date_str . 'T' . $event->time_end,
                            'time_start'        => $event->time_start,
                            'time_end'          => $event->time_end,
                            'status_dokter'     => $event->status,
                            'limit_px'          => number_format($event->limit_px),
                            'slot_tersisa'      => is_numeric($slot_sisa) ? number_format($slot_sisa) : $slot_sisa,
                            'nama_dokter'       => "Dr. " . $event->nama_dokter,
                            'kode_dokter'       => $event->kode_dokter,
                            'comment'           => (($event->comment == '') ? 'Tidak ada catatan' : $event->comment),
                            'displayEventTime'  => true,
                        ];
                    }
                    $current_date->modify('+1 week');
                }
            } else {
                // Jadwal non-rutin (izin, sakit, cuti)
                $status_jadwal = $this->M_global->getDataResult('jadwal_dokter_status', [
                    'kode_poli'   => $event->kode_poli,
                    'kode_cabang' => $event->kode_cabang,
                    'kode_dokter' => $event->kode_dokter,
                    'status_absen' => $event->status
                ]);

                foreach ($status_jadwal as $sj) {
                    $status_text = '';
                    switch ($sj->status_absen) {
                        case 2:
                            $status_text = 'Izin';
                            break;
                        case 3:
                            $status_text = 'Sakit';
                            break;
                        case 4:
                            $status_text = 'Cuti';
                            break;
                    }

                    $title = "Dr. " . $event->nama_dokter . ", Status: " . $status_text;
                    $event_date_str = $sj->tgl_status;

                    $data[] = [
                        'id'                => $event->id . '_' . $event_date_str,
                        'title'             => $title,
                        'start'             => $event_date_str . 'T' . $event->time_start,
                        'end'               => $event_date_str . 'T' . $event->time_end,
                        'time_start'        => $event->time_start,
                        'time_end'          => $event->time_end,
                        'status_dokter'     => $sj->status_absen,
                        'limit_px'          => '0',
                        'slot_tersisa'      => '0',
                        'nama_dokter'       => "Dr. " . $event->nama_dokter,
                        'kode_dokter'       => $event->kode_dokter,
                        'comment'           => (($event->comment == '') ? 'Tidak ada catatan' : $event->comment),
                        'displayEventTime'  => true,
                    ];
                }
            }
        }

        // Kembalikan data dalam format JSON untuk ditampilkan di kalender
        echo json_encode($data);
    }


    public function jdokter_insert()
    {
        // ambil semua data dari veiw
        $kodeJadwal   = $this->input->post('kodeJadwal');
        $kode_dokter  = $this->input->post('kode_dokter');
        $kode_poli    = $this->input->post('kode_poli');
        $kode_ruang   = $this->input->post('kode_ruang');
        $kode_cabang  = $this->input->post('kode_cabang');
        $status       = $this->input->post('status_dokter');
        $limit_px     = $this->input->post('limit_px');
        $hari         = $this->input->post('hari');
        $time_start   = $this->input->post('time_start');
        $time_end     = $this->input->post('time_end');
        $comment      = $this->input->post('comment');
        // Get dates for current week
        $now = date('Y-m-d');

        // Get selected date between week range
        $tgl = date('Y-m-d', strtotime($hari));
        if ($tgl >= $now) {
            $tgl = $tgl;
        } else {
            $tgl = date('Y-m-d', strtotime("next $hari"));
        }

        // ambil data dokter dari table dokter berdasarkan kode_dokter
        $dokter       = $this->M_global->getData('dokter', ['kode_dokter' => $kode_dokter]);

        // simpan semua data lemparan kedalam array
        $data = [
            'kode_dokter'   => $kode_dokter,
            'kode_poli'     => $kode_poli,
            'kode_cabang'   => $kode_cabang,
            'kode_ruang'    => $kode_ruang,
            'status'        => $status,
            'limit_px'      => $limit_px,
            'hari'          => $hari,
            'time_start'    => $time_start,
            'time_end'      => $time_end,
            'comment'       => $comment,
        ];

        $data_status = [
            'kode_cabang'   => $kode_cabang,
            'kode_poli'     => $kode_poli,
            'kode_dokter'   => $kode_dokter,
            'tgl_status'    => $tgl,
            'status_absen'  => $status,
        ];

        // echo json_encode($data_status);

        $cek = $this->M_global->insertData('jadwal_dokter', $data);

        if ($cek) { // jika function cek berjalan, lempar status 1 ke view

            if ($status != '1' || $status != 1) {
                $this->M_global->insertData('jadwal_dokter_status', $data_status);
            }

            // simpan aktifitas user
            aktifitas_user('Jadwal Dokter', 'Menambahkan', $kodeJadwal, "Jadwal dokter: " . $dokter->nama . " Hari: " . $hari . " Jam: (" . $time_start . " - " . $time_end . ")", json_encode($data), json_encode(['']));

            echo json_encode(['status' => 1]);
        } else { // selain itu lempar status 0 ke veiw
            echo json_encode(['status' => 0]);
        }
    }

    // hapus jadwal
    public function jadwal_delete()
    {
        // ambild data
        $id             = $this->input->post('kode_jadwal');
        $kode_dokter    = $this->input->post('kode_dokter');

        // ambil data jadwal_dokter by id
        $jadwal_dokter  = $this->M_global->getData('jadwal_dokter', ['id' => $id]);
        // ambil data dokter by kode_dokter
        $dokter         = $this->M_global->getData('dokter', ['kode_dokter' => $kode_dokter]);

        // buat fungsi cek untuk menjalankan fungsi hapus
        $cek            = $this->M_global->delData('jadwal_dokter', ['id' => $id]);

        if ($cek) { // jika fungsi cek berjalan, lempar status 1 ke view
            // simpan aktifitas user
            aktifitas_user('Jadwal Dokter', 'Menghapus', $id, "Jadwal dokter: " . $dokter->nama . " Hari: " . $jadwal_dokter->hari . " (" . $jadwal_dokter->time_start . " - " . $jadwal_dokter->time_end . ")", json_encode(['']), json_encode($jadwal_dokter));

            echo json_encode(['status' => 1]);
        } else { // selain itu lempar status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // hapus cara masuk
    public function delCaraMasuk($kode_masuk)
    {
        $masuk = $this->M_global->getData('m_cara_masuk', ['kode_masuk' => $kode_masuk]);

        aktifitas_user('Cara Masuk', 'Menghapus', $kode_masuk, "Keterangan: " . $masuk->keterangan);

        $cek = $this->M_global->delData('m_cara_masuk', ['kode_masuk' => $kode_masuk]);

        if ($cek) { // jika fungsi cek berjalan, lempar status 1 ke view
            // simpan aktifitas user
            echo json_encode(['status' => 1]);
        } else { // selain itu lempar status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // proses cara masuk
    public function proses_cara_masuk($param)
    {
        $cara_masuk = $this->input->post('cara_masuk_m');

        if ($param == 1) {
            $kode_masuk = _caraMasuk();
            $cek = $this->M_global->insertData('m_cara_masuk', ['kode_masuk' => $kode_masuk, 'keterangan' => $cara_masuk]);
            $pesan = 'Menambahkan ' . $cara_masuk;
        } else {
            $kode_masuk = $this->input->post('kode_cara_masuk');
            $cek = $this->M_global->updateData('m_cara_masuk', ['keterangan' => $cara_masuk], ['kode_masuk' => $kode_masuk]);
            $pesan = 'Mengubah ' . $cara_masuk;
        }

        if ($cek) { // jika fungsi cek berjalan, lempar status 1 ke view
            aktifitas_user('Cara Masuk', $pesan, $kode_masuk, "Keterangan: " . $cara_masuk);

            // simpan aktifitas user
            echo json_encode(['status' => 1]);
        } else { // selain itu lempar status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    /**
     * Triage tanpa pendaftaran
     * untuk menampilkan, menambahkan, dan mengubah satuan dalam sistem
     */

    // triage page
    public function triage()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Healt Management',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Triage',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => '',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Pendaftaran/Form_triage', $parameter);
    }

    // fungsi proses triage
    public function triage_proses()
    {
        $cabang               = $this->session->userdata('init_cabang');
        $kode_cabang          = $this->session->userdata('cabang');
        $no_triage            = master_kode('triage', 10, $cabang, 'TR');
        $nama                 = $this->input->post('nama');
        $jkel                 = $this->input->post('jkel');
        $tgl_lahir            = $this->input->post('tgl_lahir');
        $kontak               = $this->input->post('kontak');
        $alamat               = $this->input->post('alamat');
        $tgl_triage           = $this->input->post('tgl_masuk');
        $jam_triage           = $this->input->post('jam_masuk');
        $cara_masuk           = $this->input->post('cara_masuk');
        $sempoyongan          = $this->input->post('sempoyongan');
        $berjalan_dgn_alat    = $this->input->post('berjalan_dgn_alat');
        $penompang            = $this->input->post('penompang');
        $ket_lain             = $this->input->post('keterangan_assesment');
        $label                = $this->input->post('label_triage');
        $user_input           = $this->input->post('petugas_triage');
        $anamnesa             = $this->input->post('anamnesa_per');
        $diagnosa             = $this->input->post('diagnosa_per');
        $penyakit             = $this->input->post('penyakit_keluarga');
        $alergi               = $this->input->post('alergi');
        $td                   = $this->input->post('tekanan_darah');
        $nadi                 = $this->input->post('nadi');
        $suhu                 = $this->input->post('suhu');
        $bb                   = $this->input->post('bb');
        $tb                   = $this->input->post('tb');
        $pernapasan           = $this->input->post('pernapasan');
        $saturasi             = $this->input->post('saturasi');
        $gizi                 = $this->input->post('gizi');
        $status_hamil         = $this->input->post('hamil');
        $hpht                 = $this->input->post('hpht');
        $ket_hamil            = $this->input->post('keterangan_hamil');
        $skala_nyeri          = $this->input->post('scale');
        $cara_bicara          = $this->input->post('bicara');
        $psikologi            = $this->input->post('emosi');
        $spiritual            = $this->input->post('spiritual');

        $fisik                = $this->input->post('fisik');
        $ket_fisik            = $this->input->post('desc_fisik');

        $data = [
            'kode_cabang'       => $kode_cabang,
            'no_triage'         => $no_triage,
            'nama'              => $nama,
            'jkel'              => $jkel,
            'tgl_lahir'         => $tgl_lahir,
            'kontak'            => $kontak,
            'alamat'            => $alamat,
            'tgl_triage'        => $tgl_triage,
            'jam_triage'        => $jam_triage,
            'cara_masuk'        => $cara_masuk,
            'sempoyongan'       => $sempoyongan,
            'berjalan_dgn_alat' => $berjalan_dgn_alat,
            'penompang'         => $penompang,
            'ket_lain'          => $ket_lain,
            'label'             => $label,
            'user_input'        => $this->session->userdata('kode_user'),
            'anamnesa'          => $anamnesa,
            'diagnosa'          => $diagnosa,
            'penyakit'          => $penyakit,
            'alergi'            => $alergi,
            'td'                => $td,
            'nadi'              => $nadi,
            'suhu'              => $suhu,
            'bb'                => $bb,
            'tb'                => $tb,
            'pernapasan'        => $pernapasan,
            'saturasi'          => $saturasi,
            'gizi'              => $gizi,
            'status_hamil'      => $status_hamil,
            'hpht'              => $hpht,
            'ket_hamil'         => $ket_hamil,
            'skala_nyeri'       => $skala_nyeri,
            'cara_bicara'       => $cara_bicara,
            'psikologi'         => $psikologi,
            'spiritual'         => $spiritual,
            'status'            => 0,
        ];

        $cek = $this->M_global->insertData('triage_header', $data);

        $isi_sesudah = '';
        $isi_sebelum = '';

        if (!empty($fisik)) {
            for ($x = 0; $x <= (count($fisik) - 1); $x++) {
                $_fisik     = $fisik[$x];
                $_ket_fisik = $ket_fisik[$x];

                $data_fisik = [
                    'no_triage' => $no_triage,
                    'fisik'     => $_fisik,
                    'ket_fisik' => $_ket_fisik,
                ];

                $this->M_global->insertData('triage_htt', $data_fisik);

                $isi_sesudah .= json_encode($data_fisik);
                $isi_sebelum .= '';
            }
        }

        if ($cek) {
            $isi_sesudah .= json_encode($data);
            $isi_sebelum .= '';
            // simpan aktifitas user
            aktifitas_user('Triage', 'Mendaftarkan Triage', $no_triage, "Nama: " . $nama . ", Tgl Lahir: " . $tgl_lahir . ", Kontak: " . $kontak, $isi_sesudah, $isi_sebelum);
            // kirimkan status 1 ke view
            echo json_encode(['status' => 1, 'no_triage' => $no_triage]);
        } else {
            // selain itu kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    public function panggil($no_anjungan, $komputer)
    {
        $kode_cabang = $this->session->userdata('cabang');
        $date = date('Y-m-d');
        $anjungan = $this->M_global->getData('m_anjungan', ['no_anjungan' => $no_anjungan, 'kode_cabang' => $kode_cabang, 'tgl' => $date]);

        $last_panggil = $this->db->query('SELECT MAX(panggil) AS panggil FROM m_anjungan WHERE kode_cabang = "' . $kode_cabang . '" AND tgl = "' . $date . '" AND status < 1 ORDER BY panggil DESC LIMIT 1')->row();

        if (!$anjungan) {
            echo json_encode(['status' => 0, 'message' => 'Registration data not found']);
            return;
        }

        $current_call = $last_panggil->panggil ?? 0;
        $next_call = $current_call + 1;

        $this->M_global->updateData(
            'm_anjungan',
            ['panggil' => $next_call, 'p_ulang' => 1, 'komputer' => $komputer],
            ['no_anjungan' => $no_anjungan, 'kode_cabang' => $kode_cabang, 'tgl' => $date]
        );

        $next    = $this->db->query('SELECT * FROM m_anjungan WHERE kode_cabang = "' . $kode_cabang . '" AND tgl = "' . $date . '" AND panggil < 1 ORDER BY id ASC LIMIT 1')->row();

        $nextx   = $this->db->query('SELECT * FROM m_anjungan WHERE kode_cabang = "' . $kode_cabang . '" AND tgl = "' . $date . '" AND panggil > 0 AND status < 1 ORDER BY panggil ASC LIMIT 1')->row();

        $next = (($next) ? $next->no_anjungan : (($nextx) ? $nextx->no_anjungan : ''));

        echo json_encode(['status' => 1, 'next_anjungan' => $next]);
    }

    public function lewati($no_anjungan)
    {
        $kode_cabang = $this->session->userdata('cabang');
        $date = date('Y-m-d');
        $anjungan = $this->M_global->getData('m_anjungan', ['no_anjungan' => $no_anjungan, 'kode_cabang' => $kode_cabang, 'tgl' => $date, 'status' => 0]);
        $id = $anjungan->id;
        $skipper = $this->M_global->getData('m_anjungan', ['id' => ($id + 1), 'kode_cabang' => $kode_cabang, 'tgl' => $date, 'status' => 0]);

        if ($skipper) {
            $anjungan = $skipper->no_anjungan;
        } else {
            $anjungan = $this->M_global->getData('m_anjungan', ['id < ' => ($id + 1), 'kode_cabang' => $kode_cabang, 'tgl' => $date, 'status' => 0])->no_anjungan;
        }

        echo json_encode(['status' => 1, 'next_anjungan' => $anjungan]);
    }
}
