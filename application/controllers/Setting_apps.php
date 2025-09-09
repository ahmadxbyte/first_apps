<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Setting_apps extends CI_Controller
{
    // variable open public untuk controller Home
    public $data;

    public function __construct()
    {
        parent::__construct();
        // load model M_auth
        $this->load->model("M_auth");

        if (!empty($this->session->userdata("email"))) { // jika session email masih ada

            $id_menu = $this->M_global->getData('m_menu', ['url' => 'Setting_apps'])->id;

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
                    'menu'      => 'Setting_apps',
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

    // home page
    public function index()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Pengaturan Web',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Pengaturan Web',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
        ];

        $this->template->load('Template/Content', 'Setting/Web', $parameter);
    }

    // fungsi update profile website
    public function proses()
    {
        // variable
        $id                       = $this->input->post('id_web');
        $nohp                     = $this->input->post('nohp_web');
        $email                    = $this->input->post('email_web');
        $kode_email               = $this->input->post('kode_email');
        $nama                     = $this->input->post('nama_web');
        $bg_theme                 = $this->input->post('bg_theme');
        $ct_theme                 = $this->input->post('ct_theme');
        $alamat                   = $this->input->post('alamat_web');
        $limit_trash_web          = $this->input->post('limit_trash_web');
        $auto_reload              = $this->input->post('auto_reload');
        $auto_lock                = $this->input->post('auto_lock');
        $bg_0                     = $this->input->post('bg_0');
        $solid_bg                 = $this->input->post('solid_bg');
        $ig                       = $this->input->post('ig');
        $git                      = $this->input->post('git');

        // configurasi upload file
        $config['upload_path']    = 'assets/img/web/';
        $config['allowed_types']  = 'jpg|png|jpeg|gif';
        $config['max_size']       = '20480'; // Set max file size to 20MB for HD quality images
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        $web = $this->M_global->getData('web_setting', ['id' => 1]);

        if ($_FILES['filefoto']['name']) { // jika file didapatkan nama filenya
            // upload file
            $this->upload->do_upload('filefoto');

            // ambil namanya berdasarkan nama file upload
            $gambar = $this->upload->data('file_name');

            if ($web->logo !== $gambar) {
                unlink('assets/img/web/' . $web->logo);
            }
        } else { // selain itu
            // beri nilai default
            if ($web->logo == 'AdminLTELogo.png') {
                $gambar = 'AdminLTELogo.png';
            } else {
                $gambar = $web->logo;
            }
        }

        if ($_FILES['watermark']['name']) { // jika file didapatkan nama filenya
            // upload file
            $this->upload->do_upload('watermark');

            // ambil namanya berdasarkan nama file upload
            $theme = $this->upload->data('file_name');

            if ($web->watermark !== $theme) {
                unlink('assets/img/web/' . $web->watermark);
            }
        } else { // selain itu
            // beri nilai default
            if ($web->watermark == 'My_Logo_4_2.png') {
                $theme = 'My_Logo_4_2.png';
            } else {
                $theme = $web->watermark;
            }
        }

        if ($_FILES['loading_page']['name']) { // jika file didapatkan nama filenya
            // upload file
            $this->upload->do_upload('loading_page');

            // ambil namanya berdasarkan nama file upload
            $loading_gif = $this->upload->data('file_name');

            if ($web->loading !== $loading_gif) {
                unlink('assets/img/web/' . $web->loading);
            }
        } else { // selain itu
            // beri nilai default
            if ($web->loading == 'loading_2.gif') {
                $loading_gif = 'loading_2.gif';
            } else {
                $loading_gif = $web->loading;
            }
        }

        if ($_FILES['bg']['name']) { // jika file didapatkan nama filenya
            // upload file
            $this->upload->do_upload('bg');

            // ambil namanya berdasarkan nama file upload
            $mybg = $this->upload->data('file_name');

            if ($web->bg !== $mybg) {
                unlink('assets/img/web/' . $web->bg);
            }
        } else { // selain itu
            // beri nilai default
            if ($web->bg == 'waves-macos-big-sur-colorful-dark-5k-6016x6016-4990.png') {
                $mybg = 'waves-macos-big-sur-colorful-dark-5k-6016x6016-4990.png';
            } else {
                $mybg = $web->bg;
            }
        }

        // masukan variable ke dalam variable $isi untuk di update
        $isi = [
            'nama'              => $nama,
            'email'             => $email,
            'kode_email'        => $kode_email,
            'ig'                => $ig,
            'git'               => $git,
            'nohp'              => $nohp,
            'alamat'            => $alamat,
            'logo'              => $gambar,
            'bg_theme'          => $bg_theme,
            'loading'           => $loading_gif,
            'ct_theme'          => $ct_theme,
            'watermark'         => $theme,
            'bg'                => $mybg,
            'limit_trash_web'   => $limit_trash_web,
            'auto_reload'       => $auto_reload,
            'auto_lock'         => $auto_lock,
            'bg_0'              => $bg_0,
            'solid_bg'          => $solid_bg,
        ];

        // jalankan fungsi update berdasarkan id
        $cek = $this->M_global->updateData('web_setting', $isi, ['id' => $id]);

        if ($cek) { // jika proses berhasil beri nilai 1
            echo json_encode(['status' => 1]);
        } else { // selain itu beri nilai 0
            echo json_encode(['status' => 0]);
        }
    }
}
