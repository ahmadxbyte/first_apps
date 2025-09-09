<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends CI_Controller
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

        $cek         = $this->M_global->getData('user', ['kode_user' => $this->session->userdata('kode_user')]);

        if ($cek) {
            $data = $cek;
        } else {
            $data = $this->db->query("SELECT m.*, m.kode_member AS kode_user FROM member m WHERE m.kode_member = '" . $this->session->userdata('kode_user') . "'");
        }

        $now = date('Y-m-d');

        $parameter = [
            $this->data,
            'judul'         => 'Profile',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Informasi Personal',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'data_user'     => $data,
            "in_out"        => $this->M_global->getData("activity_log", ['kode' => $this->session->userdata('email')]),
            "aktifitas"     => $this->db->query("SELECT * FROM activity_user WHERE email = '" . $this->data["email"] . "' AND waktu LIKE '%$now%' ORDER BY id_activity DESC")->result(),
            "jum_aktif"     => $this->db->query("SELECT * FROM activity_user WHERE email = '" . $this->data["email"] . "' AND waktu LIKE '%$now%'")->num_rows(),
        ];

        $this->template->load('Template/Content', 'Pengaturan/Profile', $parameter);
    }

    // fungsi nonaktifkan akun
    public function nonaktif($kode_user)
    {
        // jalankan fungsi nonaktif akun
        $cek = $this->M_global->updateData('user', ['actived' => 0], ['kode_user' => $kode_user]);

        if ($cek) { // jika fungsi berjalan
            // kembalikan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kembalikan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi update akun user
    public function updateAkun($kode_user)
    {
        // variable
        $nama         = $this->input->post('nama');
        $email        = $this->input->post('email');
        $jkel         = $this->input->post('jkel');
        $nohp         = $this->input->post('nohp');
        $secondpass   = $this->input->post('secondpass');
        $password     = (!empty($secondpass) ? md5($secondpass) : '');

        $cek_user     = $this->M_global->getData('user', ['email' => $email]);

        if ($cek_user) {
            $cek_user = $cek_user;
        } else {
            $cek_user = $this->M_global->getData('member', ['email' => $email]);
        }

        if ($secondpass == '') {
            $secondpass = $cek_user->secondpass;
            $password   = $cek_user->password;
        } else {
            $secondpass = $secondpass;
            $password   = $password;
        }

        // configurasi upload file
        $config['upload_path']    = 'assets/user/';
        $config['allowed_types']  = 'jpg|png|jpeg';
        $config['max_size']       = '2048';
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ($_FILES['filefoto']['name']) { // jika file didapatkan nama filenya
            // upload file
            $this->upload->do_upload('filefoto');

            // ambil namanya berdasarkan nama file upload
            $gambar = $this->upload->data('file_name');

            if ($cek_user->foto !== $gambar) {
                unlink('assets/user/' . $cek_user->foto);
            }
        } else { // selain itu
            if ($cek_user) {
                if ($cek_user->foto == '' || $cek_user->foto == null) {
                    // beri nilai default
                    if ($jkel == 'P') { // jika pria
                        $gambar = 'pria.png';
                    } else { // selain itu
                        $gambar = 'wanita.png';
                    }
                } else {
                    $gambar = $cek_user->foto;
                }
            } else {
                if ($cek_user) {
                    if ($cek_user->foto == '' || $cek_user->foto == null) {
                        // beri nilai default
                        if ($jkel == 'P') { // jika pria
                            $gambar = 'pria.png';
                        } else { // selain itu
                            $gambar = 'wanita.png';
                        }
                    } else {
                        $cek_user->foto;
                    }
                    $gambar = $cek_user->foto;
                } else {
                    // beri nilai default
                    if ($jkel == 'P') { // jika pria
                        $gambar = 'pria.png';
                    } else { // selain itu
                        $gambar = 'wanita.png';
                    }
                }
            }
        }

        // masukan variable ke dalam variable $isi untuk di update
        $isi = [
            'nama'          => $nama,
            'email'         => $email,
            'jkel'          => $jkel,
            'secondpass'    => $secondpass,
            'password'      => $password,
            'foto'          => $gambar,
            'nohp'          => $nohp,
        ];

        // jalankan fungsi update berdasarkan id
        $cek = $this->M_global->updateData('user', $isi, ['kode_user' => $kode_user]);

        $init_cabang = $this->M_global->getData('cabang', ['kode_cabang' => $this->session->userdata('cabang')])->inisial_cabang;
        $shift = $this->session->userdata('shift');

        if ($cek) { // jika proses berhasil beri nilai 1
            $aktifitas = [
                'email'         => $email,
                'kegiatan'      => $email . " <b>Mengubah Profile</b>",
                'menu'          => "Profile",
                'waktu'         => date('Y-m-d H:i:s'),
                'kode_cabang'   => $init_cabang,
                'shift'         => $shift,
            ];

            $this->db->insert("activity_user", $aktifitas);

            echo json_encode(['status' => 1]);
        } else { // selain itu beri nilai 0
            echo json_encode(['status' => 0]);
        }
    }

    public function aktifitas_user($date, $kode_user)
    {
        $web = $this->M_global->getData('web_setting', ['id' => 1]);
        $user = $this->M_global->getData('user', ['kode_user' => $kode_user]);
        if ($user) {
            $user = $user;
        } else {
            $user = $this->M_global->getData('member', ['kode_member' => $kode_user]);
        }

        $email = $user->email;

        $jum_aktif = $this->db->query("SELECT * FROM activity_user WHERE email = '$email' AND waktu LIKE '%$date%'")->num_rows();
        $aktifitas = $this->db->query("SELECT * FROM activity_user WHERE email = '$email' AND waktu LIKE '%$date%' ORDER BY id_activity DESC")->result();
        if ($aktifitas) :
?>
            <br>
            <span class="badge bg-info" type="button" onclick="lihat_aktifitas()"><i class="fa-solid fa-arrows-rotate"></i> Refresh</span>
            <span class="badge bg-warning" type="button" onclick="download_au($('#tgl').val())"><i class="fa-solid fa-arrows-rotate"></i> Cetak</span>
            <span class="badge bg-danger float-right">Banyaknya aktifitas : <?= $jum_aktif; ?></span>
            <br>
            <br>
            <div class="table-responsive">
                <table class="table table-striped w-100" <?= ($web->ct_theme == 2) ? ' style="border-radius: 10px; color: white !important;"' : ' style="border-radius: 10px;"' ?>>
                    <?php foreach ($aktifitas as $au) { ?>
                        <tr>
                            <td style="width: 12%;" class="text-left align-middle"><span class="badge bg-success"><?= date("d m Y", strtotime($au->waktu)); ?></span></td>
                            <td style="width: 18%;" class="text-left align-middle"><?= $au->menu; ?></td>
                            <td style="width: 40%;" class="text-left align-middle">
                                <?= $au->kegiatan . (($this->session->userdata('kode_role') == 'R0001') ? '<hr>Sesudah: <br><a href="#" onclick="copyActivity(' . "'" . $au->id_activity . "', '0'" . ')">' . $au->detail_kegiatan . '</a><br>Sebelumnya: <br><a href="#" onclick="copyActivity(' . "'" . $au->id_activity . "', '1'" . ')">' . $au->detail_sebelum . '</a>' : ''); ?>
                            </td>
                            <td style="width: 10%;" class="text-left align-middle"><?= $au->kode_cabang; ?></td>
                            <td style="width: 10%;" class="text-left align-middle">Shif: <?= $au->shift; ?></td>
                            <td style="width: 10%;" class="text-right align-middle">Jam : <?= date("H:i", strtotime($au->waktu)); ?></td>
                        </tr>
                    <?php } ?>
                </table>
                <style>
                    /* Remove horizontal scroll for table-responsive in this context */
                    #cekaktif_user .table-responsive {
                        overflow-x: unset !important;
                    }

                    /* Ensure table fills container */
                    #cekaktif_user table.table {
                        width: 100% !important;
                        min-width: 100% !important;
                        table-layout: fixed;
                    }

                    #cekaktif_user td {
                        white-space: normal !important;
                        word-break: break-word;
                    }
                </style>
            </div>
        <?php else : ?>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <span class="badge bg-info" type="button" onclick="lihat_aktifitas()"><i class="fa-solid fa-arrows-rotate"></i> Refresh</span>
                    <span class="badge bg-warning" type="button" onclick="download_au($('#tgl').val())"><i class="fa-solid fa-arrows-rotate"></i> Cetak</span>
                    <span class="badge bg-danger float-right">Banyaknya aktifitas : 0</span>
                    <br>
                    <br>
                    <div class="table-responsive">
                        <table width="100%" class="table table-striped" <?= ($web->ct_theme == 2) ? ' style="border-radius: 10px; color: white !important;"' : ' style="border-radius: 10px;"' ?>>
                            <tr>
                                <td>
                                    <span class="text-center font-weight-bold">Tidak ada aktifitas</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
<?php endif;
    }

    // get data activity by id
    public function getDataActivity($id, $ket)
    {
        if ($ket == 0) {
            $data = 'detail_sebelum';
        } else {
            $data = 'detail_sesudah';
        }

        $cek = $this->M_global->getData('activity_user', ['id_activity' => $id])->$data;

        if ($cek) {
            echo json_encode(['status' => 1, 'hasil' => $cek]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi update akun member
    public function updateAkunMember($kode_member)
    {
        // variable
        $nama         = $this->input->post('nama');
        $email        = $this->input->post('email');
        $jkel         = $this->input->post('jkel');
        $secondpass   = $this->input->post('secondpass');
        $password     = md5($secondpass);

        // configurasi upload file
        $config['upload_path']    = 'assets/member/';
        $config['allowed_types']  = 'jpg|png|jpeg';
        $config['max_size']       = '2048';
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ($_FILES['filefoto']['name']) { // jika file didapatkan nama filenya
            // upload file
            $this->upload->do_upload('filefoto');

            // ambil namanya berdasarkan nama file upload
            $gambar = $this->upload->data('file_name');
        } else { // selain itu
            $cek_member = $this->M_global->getData('member', ['email' => $email]);

            if ($cek_member) {
                if ($cek_member->foto == '' || $cek_member->foto == null) {
                    // beri nilai default
                    if ($jkel == 'P') { // jika pria
                        $gambar = 'pria.png';
                    } else { // selain itu
                        $gambar = 'wanita.png';
                    }
                } else {
                    $gambar = $cek_member->foto;
                }
            } else {
                $cek_member = $this->M_global->getData('member', ['email' => $email]);

                if ($cek_member) {
                    if ($cek_member->foto == '' || $cek_member->foto == null) {
                        // beri nilai default
                        if ($jkel == 'P') { // jika pria
                            $gambar = 'pria.png';
                        } else { // selain itu
                            $gambar = 'wanita.png';
                        }
                    } else {
                        $cek_member->foto;
                    }
                    $gambar = $cek_member->foto;
                } else {
                    // beri nilai default
                    if ($jkel == 'P') { // jika pria
                        $gambar = 'pria.png';
                    } else { // selain itu
                        $gambar = 'wanita.png';
                    }
                }
            }
        }

        // masukan variable ke dalam variable $isi untuk di update
        $isi = [
            'nama'          => $nama,
            'email'         => $email,
            'jkel'          => $jkel,
            'secondpass'    => $secondpass,
            'password'      => $password,
            'foto'          => $gambar,
        ];

        // jalankan fungsi update berdasarkan id
        $cek = $this->M_global->updateData('member', $isi, ['kode_member' => $kode_member]);

        $init_cabang = $this->M_global->getData('cabang', ['kode_cabang' => $this->session->userdata('cabang')])->inisial_cabang;
        $shift = $this->session->userdata('shift');

        if ($cek) { // jika proses berhasil beri nilai 1
            $aktifitas = [
                'email'         => $email,
                'kegiatan'      => $email . " <b>Mengubah Profile</b>",
                'menu'          => "Profile",
                'waktu'         => date('Y-m-d H:i:s'),
                'kode_cabang'   => $init_cabang,
                'shift'         => $shift,
            ];

            $this->db->insert("activity_user", $aktifitas);

            echo json_encode(['status' => 1]);
        } else { // selain itu beri nilai 0
            echo json_encode(['status' => 0]);
        }
    }

    // profile member page
    public function profile_member()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'             => 'Profile',
            'nama_apps'         => $web_setting->nama,
            'page'              => 'Akun Pengguna',
            'web'               => $web_setting,
            'web_version'       => $web_version->version,
            'data_pendaftaran'  => $this->M_global->getDataResult('pendaftaran', ['kode_member' => $this->session->userdata('kode_user')]),
            'data_user'         => $this->db->query("SELECT m.* FROM member m WHERE m.kode_member = '" . $this->session->userdata('kode_user') . "'")->row(),
        ];

        $this->template->load('Template/Content', 'Pengaturan/Profile_member', $parameter);
    }

    public function update_pass()
    {
        $secondpass = $this->input->post('password2');
        $password = md5($secondpass);
        $email = $this->session->userdata('email');

        $cek = $this->M_global->updateData('user', ['secondpass' => $secondpass, 'password' => $password], ['email' => $email]);

        if ($cek) { // jika proses berhasil beri nilai 1
            $aktifitas = [
                'email'         => $email,
                'kegiatan'      => $email . " <b>Mengubah Password</b>",
                'menu'          => "Profile",
                'waktu'         => date('Y-m-d H:i:s'),
                'kode_cabang'   => $this->session->userdata('init_cabang'),
                'shift'         => $this->session->userdata('shift'),
            ];

            $this->db->insert("activity_user", $aktifitas);

            echo json_encode(['status' => 1]);
        } else { // selain itu beri nilai 0
            echo json_encode(['status' => 0]);
        }
    }
}
