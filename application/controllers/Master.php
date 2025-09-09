<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master extends CI_Controller
{
    // variable open public untuk controller Home
    public $data;

    public function __construct()
    {
        parent::__construct();
        // load model M_auth
        $this->load->model("M_auth");
        // load model M_global
        $this->load->model("M_global");

        if (!empty($this->session->userdata("email"))) { // jika session email masih ada
            $id_menu          = $this->M_global->getData('m_menu', ['url' => 'Master'])->id;

            // ambil isi data berdasarkan email session dari table user, kemudian tampung ke variable $user
            $user             = $this->M_global->getData("user", ["email" => $this->session->userdata("email")]);

            $cek_akses_menu   = $this->M_global->getData('akses_menu', ['id_menu' => $id_menu, 'kode_role' => $user->kode_role]);
            if ($cek_akses_menu) {
                // tampung data ke variable data public
                $this->data = [
                    'nama'      => $user->nama,
                    'email'     => $user->email,
                    'kode_role' => $user->kode_role,
                    'actived'   => $user->actived,
                    'foto'      => $user->foto,
                    'shift'     => $this->session->userdata('shift'),
                    'menu'      => 'Master',
                ];

                $this->load->model('M_barang');
            } else {
                // kirimkan kembali ke Auth
                redirect('Where');
            }
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
            // kirimkan kembali ke Auth
            redirect('Auth');
        }
    }

    /**
     * Master Satuan
     * untuk menampilkan, menambahkan, dan mengubah satuan dalam sistem
     */

    // satuan page
    public function satuan()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Satuan',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/satuan_list/',
            'param1'        => '1',
        ];

        $this->template->load('Template/Content', 'Master/Umum/Satuan', $parameter);
    }

    // fungsi list satuan
    public function satuan_list($param1)
    {
        // parameter untuk list table
        $table            = 'm_satuan';
        $colum            = ['id', 'kode_satuan', 'keterangan'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param1   = 'hapus < ';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;


        // table server side tampung kedalam variable $list
        $list             = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss     = 'onclick="ubah(' . "'" . $rd->kode_satuan . "'" . ')"';
            } else {
                $upd_diss     = 'disabled';
            }

            if ($deleted > 0) {
                $barang             = $this->M_global->getResult('barang');

                $satuan             = [];
                foreach ($barang as $b) {
                    $satuan[]       = [$b->kode_satuan, $b->kode_satuan2, $b->kode_satuan3];
                }

                $flattened_satuan   = array_merge(...$satuan);

                if (in_array($rd->kode_satuan, $flattened_satuan)) {
                    $del_diss       = 'disabled';
                } else {
                    $del_diss       = 'onclick="hapus(' . "'" . $rd->kode_satuan . "'" . ')"';
                }
            } else {
                $del_diss           = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_satuan;
            $row[]  = $rd->keterangan;
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" style="margin-bottom: 5px;" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" style="margin-bottom: 5px;" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi cek satuan berdasarkan keterangan satuan
    public function cekSat()
    {
        // ambil keterangan inputan
        $keterangan   = $this->input->post('keterangan');

        // cek keterangan pada table m_satuan
        $cek          = $this->M_global->jumDataRow('m_satuan', ['keterangan' => $keterangan]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi proses simpan/update satuan
    public function satuan_proses($param)
    {
        // variable
        $keterangan       = $this->input->post('keterangan');

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kodeSatuan   = master_kode('satuan', 10, 'SAT');
        } else { // selain itu
            // ambil kode dari inputan
            $kodeSatuan   = $this->input->post('kodeSatuan');
        }

        $isi_sebelum = json_encode($this->M_global->getData('m_satuan', ['kode_satuan' => $kodeSatuan]));

        // tampung variable kedalam $isi
        $isi = [
            'kode_satuan'   => $kodeSatuan,
            'keterangan'    => $keterangan,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek = $this->M_global->insertData('m_satuan', $isi);

            $cek_param = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek = $this->M_global->updateData('m_satuan', $isi, ['kode_satuan' => $kodeSatuan]);

            $cek_param = 'mengubah';
        }

        $isi_sesudah = json_encode($this->M_global->getData('m_satuan', ['kode_satuan' => $kodeSatuan]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Satuan', $cek_param, $kodeSatuan, $this->M_global->getData('m_satuan', ['kode_satuan' => $kodeSatuan])->keterangan, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi ambil informasi satuan berdasarkan kode satuan
    public function getInfoSat($kode_satuan)
    {
        // ambil data satuan berdasarkan kode_satuan
        $data = $this->M_global->getData('m_satuan', ['kode_satuan' => $kode_satuan]);
        // lempar ke view
        echo json_encode($data);
    }

    // fungsi hapus satuan berdasarkan kode_satuan
    public function delSat($kode_satuan)
    {
        $isi_sebelum = json_encode($this->M_global->getData('m_satuan', ['kode_satuan' => $kode_satuan]));

        // update perubahan
        $cek = $this->M_global->updateData('m_satuan', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_satuan' => $kode_satuan]);

        $isi_sesudah = json_encode($this->M_global->getData('m_satuan', ['kode_satuan' => $kode_satuan]));

        if ($cek) { // jika fungsi berjalan
            // jalankan fungsi hapus satuan berdasarkan kode_satuan
            aktifitas_user('Master Satuan', 'menghapus', $kode_satuan, $this->M_global->getData('m_satuan', ['kode_satuan' => $kode_satuan])->keterangan, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Master Kategori
     * untuk menampilkan, menambahkan, dan mengubah kategori dalam sistem
     */

    // kategori page
    public function kategori()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Kategori',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/kategori_list',
            'param1'        => '1',
        ];

        $this->template->load('Template/Content', 'Master/Umum/Kategori', $parameter);
    }

    // fungsi list kategori
    public function kategori_list($param1 = '')
    {
        // parameter untuk list table
        $table            = 'm_kategori';
        $colum            = ['id', 'kode_kategori', 'keterangan'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param1   = 'hapus < ';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list             = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss     = 'onclick="ubah(' . "'" . $rd->kode_kategori . "'" . ')"';
            } else {
                $upd_diss     = 'disabled';
            }

            if ($deleted > 0) {
                $cekIsset       = $this->M_global->jumDataRow('barang', ['kode_kategori' => $rd->kode_kategori]);

                if ($cekIsset > 0) {
                    $del_diss   = 'disabled';
                } else {
                    $del_diss   = 'onclick="hapus(' . "'" . $rd->kode_kategori . "'" . ')"';
                }
            } else {
                $del_diss       = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_kategori;
            $row[]  = $rd->keterangan;
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" style="margin-bottom: 5px;" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" style="margin-bottom: 5px;" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi cek kategori berdasarkan keterangan kategori
    public function cekKat()
    {
        // ambil keterangan inputan
        $keterangan   = $this->input->post('keterangan');

        // cek keterangan pada table m_kategori
        $cek          = $this->M_global->jumDataRow('m_kategori', ['keterangan' => $keterangan]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi proses simpan/update kategori
    public function kategori_proses($param)
    {
        // variable
        $keterangan         = $this->input->post('keterangan');

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kodeKategori   = master_kode('kategori', 10, 'KAT');
        } else { // selain itu
            // ambil kode dari inputan
            $kodeKategori   = $this->input->post('kodeKategori');
        }

        $isi_sebelum = json_encode($this->M_global->getData('m_kategori', ['kode_kategori' => $kodeKategori]));

        // tampung variable kedalam $isi
        $isi = [
            'kode_kategori' => $kodeKategori,
            'keterangan'    => $keterangan,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek          = $this->M_global->insertData('m_kategori', $isi);

            $cek_param    = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek          = $this->M_global->updateData('m_kategori', $isi, ['kode_kategori' => $kodeKategori]);

            $cek_param    = 'mengubah';
        }

        $isi_sesudah = json_encode($this->M_global->getData('m_kategori', ['kode_kategori' => $kodeKategori]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Kategori', $cek_param, $kodeKategori, $this->M_global->getData('m_kategori', ['kode_kategori' => $kodeKategori])->keterangan, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi ambil informasi kategori berdasarkan kode kategori
    public function getInfoKat($kode_kategori)
    {
        // ambil data kategori berdasarkan kode_kategori
        $data = $this->M_global->getData('m_kategori', ['kode_kategori' => $kode_kategori]);
        // lempar ke view
        echo json_encode($data);
    }

    // fungsi hapus kategori berdasarkan kode_kategori
    public function delKat($kode_kategori)
    {
        // jalankan fungsi hapus kategori berdasarkan kode_kategori
        // ambil data sebelum perubahan
        $isi_sebelum    = json_encode($this->M_global->getData('m_kategori', ['kode_kategori' => $kode_kategori]));

        // update perubahan
        $cek            = $this->M_global->updateData('m_kategori', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_kategori' => $kode_kategori]);

        // ambil data sesudah perubahan
        $isi_sesudah    = json_encode($this->M_global->getData('m_kategori', ['kode_kategori' => $kode_kategori]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Kategori', 'menghapus', $kode_kategori, $this->M_global->getData('m_kategori', ['kode_kategori' => $kode_kategori])->keterangan, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Master Jenis
     * untuk menampilkan, menambahkan, dan mengubah kategori dalam sistem
     */

    // jenis page
    public function jenis()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Jenis',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/jenis_list',
            'param1'        => '1',
        ];

        $this->template->load('Template/Content', 'Master/Umum/Jenis', $parameter);
    }

    // fungsi list jenis
    public function jenis_list($param1)
    {
        // parameter untuk list table
        $table                  = 'm_jenis';
        $colum                  = ['id', 'kode_jenis', 'keterangan'];
        $order                  = 'id';
        $order2                 = 'desc';
        $order_arr              = ['id' => 'desc'];
        $kondisi_param1         = 'hapus < ';

        // kondisi role
        $updated                = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted                = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list                   = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data                   = [];
        $no                     = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss       = 'onclick="ubah(' . "'" . $rd->kode_jenis . "'" . ')"';
            } else {
                $upd_diss       = 'disabled';
            }

            if ($deleted > 0) {
                $cekIsset       = $this->M_global->jumDataRow('barang_jenis', ['kode_jenis' => $rd->kode_jenis]);
                if ($cekIsset > 0) {
                    $del_diss   = 'disabled';
                } else {
                    $del_diss   = 'onclick="hapus(' . "'" . $rd->kode_jenis . "'" . ')"';
                }
            } else {
                $del_diss       = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_jenis;
            $row[]  = $rd->keterangan;
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" style="margin-bottom: 5px;" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" style="margin-bottom: 5px;" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi cek jenis berdasarkan keterangan jenis
    public function cekJenis()
    {
        // ambil keterangan inputan
        $keterangan   = $this->input->post('keterangan');

        // cek keterangan pada table m_jenis
        $cek          = $this->M_global->jumDataRow('m_jenis', ['keterangan' => $keterangan]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi proses simpan/update jenis
    public function jenis_proses($param)
    {
        // variable
        $keterangan       = $this->input->post('keterangan');

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kodeJenis    = master_kode('Jenis', 10, 'JO');
        } else { // selain itu
            // ambil kode dari inputan
            $kodeJenis    = $this->input->post('kodeJenis');
        }

        $isi_sebelum = json_encode($this->M_global->getData('m_jenis', ['kode_jenis' => $kodeJenis]));

        // tampung variable kedalam $isi
        $isi = [
            'kode_jenis'    => $kodeJenis,
            'keterangan'    => $keterangan,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek          = $this->M_global->insertData('m_jenis', $isi);

            $cek_param    = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek          = $this->M_global->updateData('m_jenis', $isi, ['kode_jenis' => $kodeJenis]);

            $cek_param    = 'mengubah';
        }

        $isi_sesudah = json_encode($this->M_global->getData('m_jenis', ['kode_jenis' => $kodeJenis]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Jenis Obat', $cek_param, $kodeJenis, $this->M_global->getData('m_jenis', ['kode_jenis' => $kodeJenis])->keterangan, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi ambil informasi jenis berdasarkan kode jenis
    public function getInfoJenis($kode_jenis)
    {
        // ambil data jenis berdasarkan kode_jenis
        $data = $this->M_global->getData('m_jenis', ['kode_jenis' => $kode_jenis]);
        // lempar ke view
        echo json_encode($data);
    }

    // fungsi hapus jenis berdasarkan kode_jenis
    public function delJenis($kode_jenis)
    {
        // jalankan fungsi hapus jenis berdasarkan kode_jenis
        // ambil data sebelum update
        $isi_sebelum    = json_encode($this->M_global->getData('m_jenis', ['kode_jenis' => $kode_jenis]));
        // update data
        $cek            = $this->M_global->updateData('m_jenis', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_jenis' => $kode_jenis]);
        // ambil data sesudah update
        $isi_sesudah    = json_encode($this->M_global->getData('m_jenis', ['kode_jenis' => $kode_jenis]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Jenis Obat', 'menghapus', $kode_jenis, $this->M_global->getData('m_jenis', ['kode_jenis' => $kode_jenis])->keterangan, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Master Pemasok
     * untuk menampilkan, menambahkan, dan mengubah pemasok dalam sistem
     */

    // supplier page
    public function supplier()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Pemasok',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/supplier_list',
            'param1'        => '1',
        ];

        $this->template->load('Template/Content', 'Master/Umum/Supplier', $parameter);
    }

    // form supplier page
    public function form_supplier($param)
    {
        // website config
        $web_setting  = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version  = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param != '0') {
            $supplier = $this->M_global->getData('m_supplier', ['kode_supplier' => $param]);
        } else {
            $supplier = null;
        }

        $parameter = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Pemasok',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => '',
            'supplier'      => $supplier,
        ];

        $this->template->load('Template/Content', 'Master/Umum/Form_supplier', $parameter);
    }

    // fungsi list supplier
    public function supplier_list($param1)
    {
        // parameter untuk list table
        $table            = 'm_supplier';
        $colum            = ['id', 'kode_supplier', 'nama', 'nohp', 'alamat', 'email', 'fax'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param1   = 'hapus < ';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list             = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss           = 'onclick="ubah(' . "'" . $rd->kode_supplier . "'" . ')"';
            } else {
                $upd_diss           = 'disabled';
            }

            if ($deleted > 0) {
                $cekIsset1          = $this->M_global->jumDataRow('barang_in_header', ['kode_supplier' => $rd->kode_supplier]);
                if ($cekIsset1 > 0) {
                    $del_diss       = 'disabled';
                } else {
                    $cekIsset2      = $this->M_global->jumDataRow('barang_in_retur_header', ['kode_supplier' => $rd->kode_supplier]);
                    if ($cekIsset2 > 0) {
                        $del_diss   = 'disabled';
                    } else {
                        $del_diss   = 'onclick="hapus(' . "'" . $rd->kode_supplier . "'" . ')"';
                    }
                }
            } else {
                $del_diss           = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_supplier;
            $row[]  = $rd->nama;
            $row[]  = $rd->nohp;
            $row[]  = $rd->email;
            $row[]  = $rd->fax;
            $row[]  = $rd->alamat;
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" style="margin-bottom: 5px;" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" style="margin-bottom: 5px;" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi cek supplier berdasarkan nama supplier
    public function cekSup()
    {
        // ambil nama inputan
        $nama = $this->input->post('nama');

        // cek nama pada table m_supplier
        $cek  = $this->M_global->jumDataRow('m_supplier', ['nama' => $nama]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi proses simpan/update supplier
    public function supplier_proses($param)
    {
        // variable
        $nama   = $this->input->post('nama');
        $nohp   = $this->input->post('nohp');
        $alamat = $this->input->post('alamat');
        $email  = $this->input->post('email');
        $fax    = $this->input->post('fax');

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kodeSupplier = master_kode('supplier', 10, 'SUP');
        } else { // selain itu
            // ambil kode dari inputan
            $kodeSupplier = $this->input->post('kodeSupplier');
        }

        $isi_sebelum = json_encode($this->M_global->getData('m_supplier', ['kode_supplier' => $kodeSupplier]));

        // tampung variable kedalam $isi
        $isi = [
            'kode_supplier' => $kodeSupplier,
            'nama'          => $nama,
            'nohp'          => $nohp,
            'alamat'        => $alamat,
            'email'         => $email,
            'fax'           => $fax,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek          = $this->M_global->insertData('m_supplier', $isi);

            $cek_param    = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek          = $this->M_global->updateData('m_supplier', $isi, ['kode_supplier' => $kodeSupplier]);

            $cek_param    = 'mengubah';
        }

        $isi_sesudah = json_encode($this->M_global->getData('m_supplier', ['kode_supplier' => $kodeSupplier]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Pemasok', $cek_param, $kodeSupplier, $this->M_global->getData('m_supplier', ['kode_supplier' => $kodeSupplier])->nama, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi hapus supplier berdasarkan kode_supplier
    public function delSup($kode_supplier)
    {
        // jalankan fungsi hapus supplier berdasarkan kode_supplier
        // ambil data sebelum update
        $isi_sebelum = json_encode($this->M_global->getData('m_supplier', ['kode_supplier' => $kode_supplier]));
        // update data
        $cek = $this->M_global->updateData('m_supplier', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_supplier' => $kode_supplier]);
        // ambil data sesudah update
        $isi_sesudah = json_encode($this->M_global->getData('m_supplier', ['kode_supplier' => $kode_supplier]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Pemasok', 'menghapus', $kode_supplier, $this->M_global->getData('m_supplier', ['kode_supplier' => $kode_supplier])->nama, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Master Gudang
     * untuk menampilkan, menambahkan, dan mengubah gudang dalam sistem
     */

    // gudang page
    public function gudang()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Gudang',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/gudang_list',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Master/Umum/Gudang', $parameter);
    }

    // form gudang page
    public function form_gudang($param)
    {
        // website config
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version    = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param != '0') {
            $gudang     = $this->M_global->getData('m_gudang', ['kode_gudang' => $param]);
        } else {
            $gudang     = null;
        }

        $parameter = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Gudang',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => '',
            'gudang'        => $gudang,
        ];

        $this->template->load('Template/Content', 'Master/Umum/Form_gudang', $parameter);
    }

    // fungsi list gudang
    public function gudang_list($param1 = '')
    {
        // parameter untuk list table
        $table            = 'm_gudang';
        $colum            = ['id', 'kode_gudang', 'nama', 'bagian', 'keterangan', 'aktif', 'utama'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param1   = 'bagian';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list             = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss           = 'onclick="ubah(' . "'" . $rd->kode_gudang . "'" . ')"';
            } else {
                $upd_diss           = 'disabled';
            }

            if ($deleted > 0) {
                $cekIsset1          = $this->M_global->jumDataRow('barang_in_header', ['kode_gudang' => $rd->kode_gudang]);
                if ($cekIsset1 > 0) {
                    $del_diss       = 'disabled';
                } else {
                    $cekIsset2      = $this->M_global->jumDataRow('barang_in_retur_header', ['kode_gudang' => $rd->kode_gudang]);
                    if ($cekIsset2 > 0) {
                        $del_diss   = 'disabled';
                    } else {
                        $del_diss   = 'onclick="hapus(' . "'" . $rd->kode_gudang . "'" . ')"';
                    }
                }
            } else {
                $del_diss           = 'disabled';
            }

            $row    = [];
            $row[]  = $no;
            $row[]  = $rd->kode_gudang;
            $row[]  = $rd->nama;
            $row[]  = $rd->bagian;
            $row[]  = '<div class="text-center">' . (($rd->aktif > 0) ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-dark">Non-aktif</span>') . '</div>';
            $row[]  = $rd->keterangan;
            $row[]  = '<div class="text-center">' . '<input type="checkbox" class="form-control" name="default_ppn" id="default_ppn' . $no . '" ' . ($rd->utama == 1 ? 'checked' : '') . '  onclick="set_default(' . "'" . $rd->kode_gudang . "', '" . $no . "'" . ')" ' . (($rd->utama == 1) ? 'disabled' : '') . '>' . '</div>';
            $row[]  = '<div class="text-center">
                    <button type="button" class="btn btn-warning" style="margin-bottom: 5px;" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                    <button type="button" class="btn btn-danger" style="margin-bottom: 5px;" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
            </div>';
            $data[] = $row;

            $no++;
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

    public function setDefGudang($kode_gudang)
    {
        $cek = $this->db->query("UPDATE m_gudang SET utama = 0");

        if ($cek) {
            $cek2 = $this->db->query("UPDATE m_gudang SET utama = 1 WHERE kode_gudang = '$kode_gudang'");
        } else {
            $cek2 = TRUE;
        }

        if ($cek2) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi cek gudang berdasarkan nama gudang
    public function cekGud()
    {
        // ambil nama inputan
        $nama = $this->input->post('nama');

        // cek nama pada table m_gudang
        $cek  = $this->M_global->jumDataRow('m_gudang', ['nama' => $nama]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi proses simpan/update gudang
    public function gudang_proses($param)
    {
        // variable
        $nama             = $this->input->post('nama');
        $bagian           = $this->input->post('bagian');
        $aktif            = $this->input->post('aktif');
        $keterangan       = $this->input->post('keterangan');

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kodeGudang   = master_kode('gudang', 10, 'GUD');
        } else { // selain itu
            // ambil kode dari inputan
            $kodeGudang   = $this->input->post('kodeGudang');
        }

        $isi_sebelum = json_encode($this->M_global->getData('m_gudang', ['kode_gudang' => $kodeGudang]));

        // tampung variable kedalam $isi
        $isi = [
            'kode_gudang' => $kodeGudang,
            'nama'        => $nama,
            'bagian'      => $bagian,
            'aktif'       => $aktif,
            'keterangan'  => $keterangan,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek          = $this->M_global->insertData('m_gudang', $isi);

            $cek_param    = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek          = $this->M_global->updateData('m_gudang', $isi, ['kode_gudang' => $kodeGudang]);

            $cek_param    = 'mengubah';
        }

        $isi_sesudah = json_encode($this->M_global->getData('m_gudang', ['kode_gudang' => $kodeGudang]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Gudang', $cek_param, $kodeGudang, $this->M_global->getData('m_gudang', ['kode_gudang' => $kodeGudang])->nama, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi hapus gudang berdasarkan kode_gudang
    public function delGud($kode_gudang)
    {
        // jalankan fungsi hapus gudang berdasarkan kode_gudang
        // ambil data sebelum update
        $isi_sebelum = json_encode($this->M_global->getData('m_gudang', ['kode_gudang' => $kode_gudang]));
        // update data
        $cek = $this->M_global->updateData('m_gudang', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_gudang' => $kode_gudang]);
        // ambil data sesudah update
        $isi_sesudah = json_encode($this->M_global->getData('m_gudang', ['kode_gudang' => $kode_gudang]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Gudang', 'menghapus', $kode_gudang, $this->M_global->getData('m_gudang', ['kode_gudang' => $kode_gudang])->nama, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Master Bank
     * untuk menampilkan, menambahkan, dan mengubah bank dalam sistem
     */

    // bank page
    public function bank()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Bank',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/bank_list',
            'param1'        => '1',
        ];

        $this->template->load('Template/Content', 'Master/Umum/Bank', $parameter);
    }

    // fungsi list bank
    public function bank_list($param1 = '')
    {
        // parameter untuk list table
        $table            = 'm_bank';
        $colum            = ['id', 'kode_bank', 'keterangan', 'norek'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param1   = 'hapus < ';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list             = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss     = 'onclick="ubah(' . "'" . $rd->kode_bank . "'" . ')"';
            } else {
                $upd_diss     = 'disabled';
            }

            if ($deleted > 0) {
                $cek_del = $this->M_global->getData('bayar_card_detail', ['kode_bank' => $rd->kode_bank]);

                if ($cek_del) {
                    $del_diss     = 'disabled';
                } else {
                    $del_diss     = 'onclick="hapus(' . "'" . $rd->kode_bank . "'" . ')"';
                }
            } else {
                $del_diss     = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_bank;
            $row[]  = $rd->keterangan;
            $row[]  = $rd->norek;
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" style="margin-bottom: 5px;" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" style="margin-bottom: 5px;" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi cek bank berdasarkan keterangan bank
    public function cekBank()
    {
        // ambil keterangan inputan
        $keterangan   = $this->input->post('keterangan');

        // cek keterangan pada table m_bank
        $cek          = $this->M_global->jumDataRow('m_bank', ['keterangan' => $keterangan]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi proses simpan/update bank
    public function bank_proses($param)
    {
        // variable
        $keterangan   = $this->input->post('keterangan');
        $norek        = $this->input->post('norek');

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kodeBank = master_kode('edc', 10, 'B');
        } else { // selain itu
            // ambil kode dari inputan
            $kodeBank = $this->input->post('kodeBank');
        }

        $isi_sebelum = json_encode($this->M_global->getData('m_bank', ['kode_bank' => $kodeBank]));

        // tampung variable kedalam $isi
        $isi = [
            'kode_bank'     => $kodeBank,
            'keterangan'    => $keterangan,
            'norek'         => $norek,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek          = $this->M_global->insertData('m_bank', $isi);

            $cek_param    = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek          = $this->M_global->updateData('m_bank', $isi, ['kode_bank' => $kodeBank]);

            $cek_param    = 'mengubah';
        }

        $isi_sesudah = json_encode($this->M_global->getData('m_bank', ['kode_bank' => $kodeBank]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Bank EDC', $cek_param, $kodeBank, $this->M_global->getData('m_bank', ['kode_bank' => $kodeBank])->keterangan, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi ambil informasi bank berdasarkan kode bank
    public function getInfoBank($kode_bank)
    {
        // ambil data bank berdasarkan kode_bank
        $data = $this->M_global->getData('m_bank', ['kode_bank' => $kode_bank]);
        // lempar ke view
        echo json_encode($data);
    }

    // fungsi hapus bank berdasarkan kode_bank
    public function delBank($kode_bank)
    {
        // jalankan fungsi hapus bank berdasarkan kode_bank
        // ambil data sebelum update
        $isi_sebelum = json_encode($this->M_global->getData('m_bank', ['kode_bank' => $kode_bank]));
        // update data
        $cek = $this->M_global->updateData('m_bank', [
            'hapus' => 1,
            'tgl_hapus' => date('Y-m-d'),
            'jam_hapus' => date('H:i:s')
        ], ['kode_bank' => $kode_bank]);
        // ambil data sesudah update
        $isi_sesudah = json_encode($this->M_global->getData('m_bank', ['kode_bank' => $kode_bank]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Bank EDC', 'menghapus', $kode_bank, $this->M_global->getData('m_bank', ['kode_bank' => $kode_bank])->keterangan, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Master Pekerjaan
     * untuk menampilkan, menambahkan, dan mengubah pekerjaan dalam sistem
     */

    // pekerjaan page
    public function pekerjaan()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Pekerjaan',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/pekerjaan_list',
            'param1'        => '1',
        ];

        $this->template->load('Template/Content', 'Master/Umum/Pekerjaan', $parameter);
    }

    // fungsi list pekerjaan
    public function pekerjaan_list($param1 = '')
    {
        // parameter untuk list table
        $table            = 'm_pekerjaan';
        $colum            = ['id', 'kode_pekerjaan', 'keterangan'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param1   = 'hapus < ';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list             = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss       = 'onclick="ubah(' . "'" . $rd->kode_pekerjaan . "'" . ')"';
            } else {
                $upd_diss       = 'disabled';
            }

            if ($deleted > 0) {
                $cekIsset       = $this->M_global->getData('member', ['pekerjaan' => $rd->kode_pekerjaan]);

                if ($cekIsset) {
                    $del_diss   = 'disabled';
                } else {
                    $del_diss   = 'onclick="hapus(' . "'" . $rd->kode_pekerjaan . "'" . ')"';
                }
            } else {
                $del_diss       = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_pekerjaan;
            $row[]  = $rd->keterangan;
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" style="margin-bottom: 5px;" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" style="margin-bottom: 5px;" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi cek pekerjaan berdasarkan keterangan pekerjaan
    public function cekPekerjaan()
    {
        // ambil keterangan inputan
        $keterangan   = $this->input->post('keterangan');

        // cek keterangan pada table m_pekerjaan
        $cek          = $this->M_global->jumDataRow('m_pekerjaan', ['keterangan' => $keterangan]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi proses simpan/update pekerjaan
    public function pekerjaan_proses($param)
    {
        // variable
        $keterangan = $this->input->post('keterangan');

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kodePekerjaan = master_kode('pekerjaan', 10, 'PEK');
        } else { // selain itu
            // ambil kode dari inputan
            $kodePekerjaan = $this->input->post('kodePekerjaan');
        }

        $isi_sebelum = json_encode($this->M_global->getData('m_pekerjaan', ['kode_pekerjaan' => $kodePekerjaan]));

        // tampung variable kedalam $isi
        $isi = [
            'kode_pekerjaan' => $kodePekerjaan,
            'keterangan'     => $keterangan,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek          = $this->M_global->insertData('m_pekerjaan', $isi);

            $cek_param    = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek          = $this->M_global->updateData('m_pekerjaan', $isi, ['kode_pekerjaan' => $kodePekerjaan]);

            $cek_param    = 'mengubah';
        }

        $isi_sesudah = json_encode($this->M_global->getData('m_pekerjaan', ['kode_pekerjaan' => $kodePekerjaan]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Pekerjaan', $cek_param, $kodePekerjaan, $this->M_global->getData('m_pekerjaan', ['kode_pekerjaan' => $kodePekerjaan])->keterangan, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi ambil informasi pekerjaan berdasarkan kode pekerjaan
    public function getInfoPekerjaan($kode_pekerjaan)
    {
        // ambil data pekerjaan berdasarkan kode_pekerjaan
        $data = $this->M_global->getData('m_pekerjaan', ['kode_pekerjaan' => $kode_pekerjaan]);
        // lempar ke view
        echo json_encode($data);
    }

    // fungsi hapus pekerjaan berdasarkan kode_pekerjaan
    public function delPekerjaan($kode_pekerjaan)
    {
        // jalankan fungsi hapus pekerjaan berdasarkan kode_pekerjaan
        // ambil data sebelum update
        $isi_sebelum = json_encode($this->M_global->getData('m_pekerjaan', ['kode_pekerjaan' => $kode_pekerjaan]));
        // update data
        $cek = $this->M_global->updateData('m_pekerjaan', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_pekerjaan' => $kode_pekerjaan]);
        // ambil data sesudah update
        $isi_sesudah = json_encode($this->M_global->getData('m_pekerjaan', ['kode_pekerjaan' => $kode_pekerjaan]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Pekerjaan', 'menghapus', $kode_pekerjaan, $this->M_global->getData('m_pekerjaan', ['kode_pekerjaan' => $kode_pekerjaan])->keterangan, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Master Agama
     * untuk menampilkan, menambahkan, dan mengubah agama dalam sistem
     */

    // agama page
    public function agama()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Agama',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/agama_list',
            'param1'        => '1',
        ];

        $this->template->load('Template/Content', 'Master/Umum/Agama', $parameter);
    }

    // fungsi list agama
    public function agama_list($param1)
    {
        // parameter untuk list table
        $table                  = 'm_agama';
        $colum                  = ['id', 'kode_agama', 'keterangan'];
        $order                  = 'id';
        $order2                 = 'desc';
        $order_arr              = ['id' => 'desc'];
        $kondisi_param1         = 'hapus < ';

        // kondisi role
        $updated                = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted                = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list                   = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data                   = [];
        $no                     = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss       = 'onclick="ubah(' . "'" . $rd->kode_agama . "'" . ')"';
            } else {
                $upd_diss       = 'disabled';
            }

            if ($deleted > 0) {
                $cekIsset       = $this->M_global->getData('member', ['agama' => $rd->kode_agama]);

                if ($cekIsset) {
                    $del_diss   = 'disabled';
                } else {
                    $del_diss   = 'onclick="hapus(' . "'" . $rd->kode_agama . "'" . ')"';
                }
            } else {
                $del_diss       = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_agama;
            $row[]  = $rd->keterangan;
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" style="margin-bottom: 5px;" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" style="margin-bottom: 5px;" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi cek agama berdasarkan keterangan agama
    public function cekAgama()
    {
        // ambil keterangan inputan
        $keterangan   = $this->input->post('keterangan');

        // cek keterangan pada table m_agama
        $cek          = $this->M_global->jumDataRow('m_agama', ['keterangan' => $keterangan]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi proses simpan/update agama
    public function agama_proses($param)
    {
        // variable
        $keterangan       = $this->input->post('keterangan');

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kodeAgama    = master_kode('agama', 10, 'AGM');
        } else { // selain itu
            // ambil kode dari inputan
            $kodeAgama    = $this->input->post('kodeAgama');
        }

        $isi_sebelum = json_encode($this->M_global->getData('m_agama', ['kode_agama' => $kodeAgama]));

        // tampung variable kedalam $isi
        $isi = [
            'kode_agama'    => $kodeAgama,
            'keterangan'    => $keterangan,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek          = $this->M_global->insertData('m_agama', $isi);

            $cek_param    = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek          = $this->M_global->updateData('m_agama', $isi, ['kode_agama' => $kodeAgama]);

            $cek_param    = 'mengubah';
        }

        $isi_sesudah = json_encode($this->M_global->getData('m_agama', ['kode_agama' => $kodeAgama]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Agama', $cek_param, $kodeAgama, $this->M_global->getData('m_agama', ['kode_agama' => $kodeAgama])->keterangan, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi ambil informasi agama berdasarkan kode agama
    public function getInfoAgama($kode_agama)
    {
        // ambil data agama berdasarkan kode_agama
        $data = $this->M_global->getData('m_agama', ['kode_agama' => $kode_agama]);
        // lempar ke view
        echo json_encode($data);
    }

    // fungsi hapus agama berdasarkan kode_agama
    public function delAgama($kode_agama)
    {
        // jalankan fungsi hapus agama berdasarkan kode_agama
        // ambil data sebelum update
        $isi_sebelum = json_encode($this->M_global->getData('m_agama', ['kode_agama' => $kode_agama]));
        // update data
        $cek = $this->M_global->updateData('m_agama', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_agama' => $kode_agama]);
        // ambil data sesudah update
        $isi_sesudah = json_encode($this->M_global->getData('m_agama', ['kode_agama' => $kode_agama]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Agama', 'menghapus', $kode_agama, $this->M_global->getData('m_agama', ['kode_agama' => $kode_agama])->keterangan, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Master Pendidikan
     * untuk menampilkan, menambahkan, dan mengubah pendidikan dalam sistem
     */

    // pendidikan page
    public function pendidikan()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Pendidikan',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/pendidikan_list',
            'param1'        => '1',
        ];

        $this->template->load('Template/Content', 'Master/Umum/Pendidikan', $parameter);
    }

    // fungsi list pendidikan
    public function pendidikan_list($param1 = '')
    {
        // parameter untuk list table
        $table                  = 'm_pendidikan';
        $colum                  = ['id', 'kode_pendidikan', 'keterangan'];
        $order                  = 'id';
        $order2                 = 'desc';
        $order_arr              = ['id' => 'desc'];
        $kondisi_param1         = 'hapus < ';

        // kondisi role
        $updated                = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted                = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list                   = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data                   = [];
        $no                     = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss       = 'onclick="ubah(' . "'" . $rd->kode_pendidikan . "'" . ')"';
            } else {
                $upd_diss       = 'disabled';
            }

            if ($deleted > 0) {
                $cekIsset       = $this->M_global->getData('member', ['pendidikan' => $rd->kode_pendidikan]);

                if ($cekIsset) {
                    $del_diss   = 'disabled';
                } else {
                    $del_diss   = 'onclick="hapus(' . "'" . $rd->kode_pendidikan . "'" . ')"';
                }
            } else {
                $del_diss       = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_pendidikan;
            $row[]  = $rd->keterangan;
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" style="margin-bottom: 5px;" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" style="margin-bottom: 5px;" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi cek pendidikan berdasarkan keterangan pendidikan
    public function cekPendidikan()
    {
        // ambil keterangan inputan
        $keterangan   = $this->input->post('keterangan');

        // cek keterangan pada table m_pendidikan
        $cek          = $this->M_global->jumDataRow('m_pendidikan', ['keterangan' => $keterangan]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi proses simpan/update pendidikan
    public function pendidikan_proses($param)
    {
        // variable
        $keterangan           = $this->input->post('keterangan');

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kodePendidikan   = master_kode('pendidikan', 10, 'PEN');
        } else { // selain itu
            // ambil kode dari inputan
            $kodePendidikan   = $this->input->post('kodePendidikan');
        }

        $isi_sebelum = json_encode($this->M_global->getData('m_pendidikan', ['kode_pendidikan' => $kodePendidikan]));

        // tampung variable kedalam $isi
        $isi = [
            'kode_pendidikan'   => $kodePendidikan,
            'keterangan'        => $keterangan,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek          = $this->M_global->insertData('m_pendidikan', $isi);

            $cek_param    = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek          = $this->M_global->updateData('m_pendidikan', $isi, ['kode_pendidikan' => $kodePendidikan]);

            $cek_param    = 'mengubah';
        }

        $isi_sesudah = json_encode($this->M_global->getData('m_pendidikan', ['kode_pendidikan' => $kodePendidikan]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Pendidikan', $cek_param, $kodePendidikan, $this->M_global->getData('m_pendidikan', ['kode_pendidikan' => $kodePendidikan])->keterangan, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi ambil informasi pendidikan berdasarkan kode pendidikan
    public function getInfoPendidikan($kode_pendidikan)
    {
        // ambil data pendidikan berdasarkan kode_pendidikan
        $data = $this->M_global->getData('m_pendidikan', ['kode_pendidikan' => $kode_pendidikan]);
        // lempar ke view
        echo json_encode($data);
    }

    // fungsi hapus pendidikan berdasarkan kode_pendidikan
    public function delPendidikan($kode_pendidikan)
    {
        // jalankan fungsi hapus pendidikan berdasarkan kode_pendidikan
        // ambil data sebelum update
        $isi_sebelum = json_encode($this->M_global->getData('m_pendidikan', ['kode_pendidikan' => $kode_pendidikan]));
        // update data
        $cek = $this->M_global->updateData('m_pendidikan', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_pendidikan' => $kode_pendidikan]);
        // ambil data sesudah update
        $isi_sesudah = json_encode($this->M_global->getData('m_pendidikan', ['kode_pendidikan' => $kode_pendidikan]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Pendidikan', 'menghapus', $kode_pendidikan, $this->M_global->getData('m_pendidikan', ['kode_pendidikan' => $kode_pendidikan])->keterangan, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Master Poli
     * untuk menampilkan, menambahkan, dan mengubah poli dalam sistem
     */

    // poli page
    public function poli()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Poli',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/poli_list',
            'param1'        => '1',
        ];

        $this->template->load('Template/Content', 'Master/Umum/Poli', $parameter);
    }

    // fungsi list poli
    public function poli_list($param1 = '')
    {
        // parameter untuk list table
        $table                    = 'm_poli';
        $colum                    = ['id', 'kode_poli', 'keterangan'];
        $order                    = 'id';
        $order2                   = 'desc';
        $order_arr                = ['id' => 'desc'];
        $kondisi_param1           = 'hapus < ';

        // kondisi role
        $updated                  = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted                  = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list                     = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data                     = [];
        $no                       = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss             = 'onclick="ubah(' . "'" . $rd->kode_poli . "'" . ')"';
            } else {
                $upd_diss             = 'disabled';
            }

            if ($deleted > 0) {
                $cekIsset         = $this->M_global->jumDataRow('dokter_poli', ['kode_poli' => $rd->kode_poli]);
                if ($cekIsset < 1) {
                    $cekIsset2    = $this->M_global->jumDataRow('perawat_poli', ['kode_poli' => $rd->kode_poli]);

                    if ($cekIsset2 < 1) {
                        $del_diss = 'onclick="hapus(' . "'" . $rd->kode_poli . "'" . ')"';
                    } else {
                        $del_diss = 'disabled';
                    }
                } else {
                    $del_diss     = 'disabled';
                }
            } else {
                $del_diss         = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_poli;
            $row[]  = $rd->keterangan;
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" style="margin-bottom: 5px;" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" style="margin-bottom: 5px;" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi cek poli berdasarkan keterangan poli
    public function cekPol()
    {
        // ambil keterangan inputan
        $keterangan   = $this->input->post('keterangan');

        // cek keterangan pada table m_poli
        $cek          = $this->M_global->jumDataRow('m_poli', ['keterangan' => $keterangan]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi proses simpan/update poli
    public function poli_proses($param)
    {
        // variable
        $keterangan   = $this->input->post('keterangan');

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kodePoli = master_kode('poli', 10, 'POL');
        } else { // selain itu
            // ambil kode dari inputan
            $kodePoli = $this->input->post('kodePoli');
        }

        $isi_sebelum = json_encode($this->M_global->getData('m_poli', ['kode_poli' => $kodePoli]));

        // tampung variable kedalam $isi
        $isi = [
            'kode_poli'     => $kodePoli,
            'keterangan'    => $keterangan,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek          = $this->M_global->insertData('m_poli', $isi);

            $cek_param    = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek          = $this->M_global->updateData('m_poli', $isi, ['kode_poli' => $kodePoli]);

            $cek_param    = 'mengubah';
        }

        $isi_sesudah = json_encode($this->M_global->getData('m_poli', ['kode_poli' => $kodePoli]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Poli', $cek_param, $kodePoli, $this->M_global->getData('m_poli', ['kode_poli' => $kodePoli])->keterangan, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi ambil informasi poli berdasarkan kode poli
    public function getInfoPol($kode_poli)
    {
        // ambil data poli berdasarkan kode_poli
        $data = $this->M_global->getData('m_poli', ['kode_poli' => $kode_poli]);
        // lempar ke view
        echo json_encode($data);
    }

    // fungsi hapus poli berdasarkan kode_poli
    public function delPol($kode_poli)
    {
        // jalankan fungsi hapus poli berdasarkan kode_poli
        // ambil data sebelum update
        $isi_sebelum = json_encode($this->M_global->getData('m_poli', ['kode_poli' => $kode_poli]));
        // update data
        $cek = $this->M_global->updateData('m_poli', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_poli' => $kode_poli]);
        // ambil data sesudah update
        $isi_sesudah = json_encode($this->M_global->getData('m_poli', ['kode_poli' => $kode_poli]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Poli', 'menghapus', $kode_poli, $this->M_global->getData('m_poli', ['kode_poli' => $kode_poli])->keterangan, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Master Kas_bank
     * untuk menampilkan, menambahkan, dan mengubah poli dalam sistem
     */

    // kas_bank page
    public function kas_bank()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Kas & Bank',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/kas_bank_list',
            'param1'        => '1',
        ];

        $this->template->load('Template/Content', 'Master/Umum/Kas_bank', $parameter);
    }

    // fungsi list kas_bank
    public function kas_bank_list($param1)
    {
        // parameter untuk list table
        $table            = 'kas_bank';
        $colum            = ['id', 'kode_kas_bank', 'nama', 'tipe', 'akun'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param1   = 'hapus < ';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list             = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss     = 'onclick="ubah(' . "'" . $rd->kode_kas_bank . "'" . ')"';
            } else {
                $upd_diss     = 'disabled';
            }

            if ($deleted > 0) {
                $del_diss     = 'onclick="hapus(' . "'" . $rd->kode_kas_bank . "'" . ')"';
            } else {
                $del_diss     = 'disabled';
            }

            if ($rd->tipe == 1) {
                $tipe     = "Cash";
            } else {
                $tipe     = "Bank";
            }

            if ($rd->akun == 1) {
                $akun     = "Kas Besar";
            } else {
                $akun     = "Kas Kecil";
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_kas_bank;
            $row[]  = $rd->nama;
            $row[]  = $tipe;
            $row[]  = $akun;
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" style="margin-bottom: 5px;" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" style="margin-bottom: 5px;" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // form kas_bank page
    public function form_kas_bank($param)
    {
        // website config
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version    = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param != '0') {
            $kas_bank   = $this->M_global->getData('kas_bank', ['kode_kas_bank' => $param]);
        } else {
            $kas_bank   = null;
        }

        $parameter = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Kas & Bank',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => '',
            'data_kas_bank' => $kas_bank,
        ];

        $this->template->load('Template/Content', 'Master/Internal/Form_kas_bank', $parameter);
    }

    // fungsi cek kas_bank
    public function cekKas_bank()
    {
        $nama   = $this->input->post('nama');

        $cek    = $this->M_global->jumDataRow('kas_bank', ['nama' => $nama]);

        if ($cek < 1) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi kas_bank proses
    public function kas_bank_proses($param)
    {
        // variable
        $nama               = $this->input->post('nama');

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kodeKas_bank   = master_kode('kas_bank', 10, 'KB');
        } else { // selain itu
            // ambil kode dari inputan
            $kodeKas_bank   = $this->input->post('kode_kas_bank');
        }

        $isi_sebelum = json_encode($this->M_global->getData('kas_bank', ['kode_kas_bank' => $kodeKas_bank]));

        $nama               = $this->input->post('nama');
        $tipe               = $this->input->post('tipe');
        $akun               = $this->input->post('akun');

        // tampung variable kedalam $isi
        $isi = [
            'kode_kas_bank' => $kodeKas_bank,
            'nama'          => $nama,
            'tipe'          => $tipe,
            'akun'          => $akun,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek          = $this->M_global->insertData('kas_bank', $isi);

            $cek_param    = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek          = $this->M_global->updateData('kas_bank', $isi, ['kode_kas_bank' => $kodeKas_bank]);

            $cek_param    = 'mengubah';
        }

        $isi_sesudah = json_encode($this->M_global->getData('kas_bank', ['kode_kas_bank' => $kodeKas_bank]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Kas & Bank', $cek_param, $kodeKas_bank, $this->M_global->getData('kas_bank', ['kode_kas_bank' => $kodeKas_bank])->nama, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi hapus kas_bank berdasarkan kode_kas_bank
    public function delKas_bank($kode_kas_bank)
    {
        // jalankan fungsi hapus kas_bank berdasarkan kode_kas_bank
        // ambil data sebelum update
        $isi_sebelum = json_encode($this->M_global->getData('kas_bank', ['kode_kas_bank' => $kode_kas_bank]));
        // update data
        $cek = $this->M_global->updateData('kas_bank', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_kas_bank' => $kode_kas_bank]);
        // ambil data sesudah update
        $isi_sesudah = json_encode($this->M_global->getData('kas_bank', ['kode_kas_bank' => $kode_kas_bank]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Kas & Bank', 'menghapus', $kode_kas_bank, $this->M_global->getData('kas_bank', ['kode_kas_bank' => $kode_kas_bank])->nama, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    /**
     * Master Pajak
     * untuk menampilkan, menambahkan, dan mengubah pajak dalam sistem
     */

    // pajak page
    public function pajak()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Pajak',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/pajak_list',
            'param1'        => '1',
        ];

        $this->template->load('Template/Content', 'Master/Umum/Pajak', $parameter);
    }

    // fungsi list pajak
    public function pajak_list($param1 = '')
    {
        // parameter untuk list table
        $table            = 'm_pajak';
        $colum            = ['id', 'kode_pajak', 'nama', 'persentase', 'aktif'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param1   = 'hapus < ';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list             = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss     = 'onclick="ubah(' . "'" . $rd->kode_pajak . "'" . ')"';
            } else {
                $upd_diss     = 'disabled';
            }

            if ($deleted > 0) {
                if ($rd->aktif == 1) {
                    $del_diss = 'disabled';
                } else {
                    $del_diss = 'onclick="hapus(' . "'" . $rd->kode_pajak . "'" . ')"';
                }
            } else {
                $del_diss = 'disabled';
            }

            $row    = [];
            $row[]  = $no;
            $row[]  = $rd->kode_pajak;
            $row[]  = $rd->nama;
            $row[]  = '<span class="float-right">' . $rd->persentase . '%</span>';
            $row[]  = '<div class="text-center">' . '<input type="checkbox" class="form-control" name="default_ppn" id="default_ppn' . $no . '" ' . ($rd->aktif == 1 ? 'checked' : '') . '  onclick="set_default(' . "'" . $rd->kode_pajak . "', '" . $no . "'" . ')" ' . (($rd->aktif > 0) ? 'disabled' : '') . '>' . '</div>';
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" style="margin-bottom: 5px;" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" style="margin-bottom: 5px;" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
            </div>';
            $data[] = $row;

            $no++;
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

    public function setDefPajak($kode_pajak)
    {
        $cek = $this->db->query("UPDATE m_pajak SET aktif = 0");

        if ($cek) {
            $cek2 = $this->db->query("UPDATE m_pajak SET aktif = 1 WHERE kode_pajak = '$kode_pajak'");

            aktifitas_user('Master Pajak', 'menjadikan pajak aktif', $kode_pajak, $this->M_global->getData('m_pajak', ['kode_pajak' => $kode_pajak])->nama);
        } else {
            $cek2 = TRUE;
        }

        if ($cek2) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi cek pajak berdasarkan keterangan pajak
    public function cekPajak()
    {
        // ambil nama inputan
        $nama = $this->input->post('nama');

        // cek nama pada table m_pajak
        $cek = $this->M_global->jumDataRow('m_pajak', ['nama' => $nama]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi proses simpan/update pajak
    public function pajak_proses($param)
    {
        // variable
        $nama             = $this->input->post('nama');
        $persentase       = $this->input->post('persentase');

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kodePajak    = master_kode('pajak', 10, 'PJK');
        } else { // selain itu
            // ambil kode dari inputan
            $kodePajak    = $this->input->post('kodePajak');
        }

        $isi_sebelum = json_encode($this->M_global->getData('m_pajak', ['kode_pajak' => $kodePajak]));

        // tampung variable kedalam $isi
        $isi = [
            'kode_pajak'    => $kodePajak,
            'nama'          => $nama,
            'persentase'    => $persentase,
            'aktif'         => 0,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek          = $this->M_global->insertData('m_pajak', $isi);

            $cek_param    = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek          = $this->M_global->updateData('m_pajak', $isi, ['kode_pajak' => $kodePajak]);

            $cek_param    = 'mengubah';
        }

        $isi_sesudah = json_encode($this->M_global->getData('m_pajak', ['kode_pajak' => $kodePajak]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Pajak', $cek_param, $kodePajak, $this->M_global->getData('m_pajak', ['kode_pajak' => $kodePajak])->nama, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi ambil informasi pajak berdasarkan kode pajak
    public function getInfoPajak($kode_pajak)
    {
        // ambil data pajak berdasarkan kode_pajak
        $data = $this->M_global->getData('m_pajak', ['kode_pajak' => $kode_pajak]);
        // lempar ke view
        echo json_encode($data);
    }

    // fungsi hapus pajak berdasarkan kode_pajak
    public function delPajak($kode_pajak)
    {
        // jalankan fungsi hapus pajak berdasarkan kode_pajak
        // ambil data sebelum update
        $isi_sebelum = json_encode($this->M_global->getData('m_pajak', ['kode_pajak' => $kode_pajak]));
        // update data
        $cek = $this->M_global->updateData('m_pajak', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_pajak' => $kode_pajak]);
        // ambil data sesudah update
        $isi_sesudah = json_encode($this->M_global->getData('m_pajak', ['kode_pajak' => $kode_pajak]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Pajak', 'menghapus', $kode_pajak, $this->M_global->getData('m_pajak', ['kode_pajak' => $kode_pajak])->nama, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    /**
     * Master Akun
     * untuk menampilkan, menambahkan, dan mengubah akun dalam sistem
     */

    // akun page
    public function akun()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Akun',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'akun'          => $this->M_global->getResult('m_akun'),
            'list_data'     => 'Master/akun_list',
            'param1'        => '1',
        ];

        $this->template->load('Template/Content', 'Master/Umum/Akun', $parameter);
    }

    // fungsi list akun
    public function akun_list($param1 = '')
    {
        // Parameter untuk list table
        $table                    = 'm_akun';
        $columns                  = ['id', 'kode_akun', 'nama_akun', 'kode_klasifikasi', 'header', 'sub_akun'];
        $order                    = 'id';
        $order_dir                = 'desc';
        $order_arr                = ['id' => 'desc'];
        $param_condition          = 'hapus < ';

        // Kondisi role
        $role                     = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']]);
        $updated                  = $role->updated;
        $deleted                  = $role->deleted;

        // Table server side tampung kedalam variable $list
        $list                     = $this->M_datatables->get_datatables($table, $columns, $order_arr, $order, $order_dir, $param1, $param_condition);
        $data                     = [];
        $no                       = $_POST['start'] + 1;

        // Loop $list
        foreach ($list as $rd) {
            $upd_diss                 = ($updated > 0) ? 'onclick="ubah(' . "'" . $rd->kode_akun . "'" . ')"' : 'disabled';

            $sub_akun             = ($rd->sub_akun) ? $this->M_global->getData('m_akun', ['kode_akun' => $rd->sub_akun])->nama_akun : 'Root';

            if ($deleted > 0) {
                $sub_akun         = $rd->kode_akun;

                // Gunakan parameter binding untuk keamanan
                $query            = $this->db->get('m_akun');
                $cek_dis          = $query->result();

                // Inisialisasi array untuk menyimpan kode akun dari hasil query
                $cek_akun         = [];
                foreach ($cek_dis as $cd) {
                    $cek_akun[]   = $cd->sub_akun;
                }

                // Cek apakah kode akun ada dalam array $cek_akun
                if (in_array($rd->kode_akun, $cek_akun)) {
                    $del_diss     = 'disabled';  // Set to 'disabled' jika $kode_akun ditemukan
                } else {
                    $del_diss     = 'onclick="hapus(' . "'" . $rd->kode_akun . "'" . ')"';  // Set to '' (enabled) jika $kode_akun tidak ditemukan
                }
            } else {
                $del_diss         = 'disabled';
            }

            $row   = [];
            $row[] = $no++;
            $row[] = htmlspecialchars($rd->kode_akun);
            $row[] = htmlspecialchars($rd->nama_akun);
            $row[] = htmlspecialchars($this->M_global->getData('klasifikasi_akun', ['kode_klasifikasi' => $rd->kode_klasifikasi])->klasifikasi);
            $row[] = htmlspecialchars($sub_akun);
            $row[] = '<div class="text-center">
                <button type="button" class="btn btn-warning" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
            </div>';

            $data[] = $row;
        }

        // Hasil server side
        $output = [
            "draw"            => intval($_POST['draw']),
            "recordsTotal"    => $this->M_datatables->count_all($table, $columns, $order_arr, $order, $order_dir, $param1, $param_condition),
            "recordsFiltered" => $this->M_datatables->count_filtered($table, $columns, $order_arr, $order, $order_dir, $param1, $param_condition),
            "data"            => $data,
        ];

        // Kirimkan ke view
        echo json_encode($output);
    }


    // fungsi cek akun berdasarkan nama_akun akun
    public function cekAkun()
    {
        // ambil nama_akun inputan
        $nama_akun    = $this->input->post('nama_akun');

        // cek nama_akun pada table m_akun
        $cek          = $this->M_global->jumDataRow('m_akun', ['nama_akun' => $nama_akun]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi proses simpan/update akun
    public function akun_proses($param)
    {
        // variable
        $nama_akun          = $this->input->post('nama_akun');
        $kode_klasifikasi   = $this->input->post('kode_klasifikasi');
        $sub_akun           = $this->input->post('sub_akun');
        if (!$sub_akun || $sub_akun == null) {
            $header         = 1;
        } else {
            $header         = 2;
        }

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kodeAkun       = master_kode('akun', 10, 'AKN');
        } else { // selain itu
            // ambil kode dari inputan
            $kodeAkun       = $this->input->post('kodeAkun');
        }

        $isi_sebelum = json_encode($this->M_global->getData('m_akun', ['kode_akun' => $kodeAkun]));

        // tampung variable kedalam $isi
        $isi = [
            'kode_akun'         => $kodeAkun,
            'nama_akun'         => $nama_akun,
            'kode_klasifikasi'  => $kode_klasifikasi,
            'header'            => $header,
            'sub_akun'          => $sub_akun,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek          = $this->M_global->insertData('m_akun', $isi);

            $cek_param    = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek          = $this->M_global->updateData('m_akun', $isi, ['kode_akun' => $kodeAkun]);

            $cek_param    = 'mengubah';
        }

        $isi_sesudah = json_encode($this->M_global->getData('m_akun', ['kode_akun' => $kodeAkun]));

        if ($cek) { // jika fungsi berjalan
            $cek_bank = $this->M_global->getData('m_bank_perusahaan', ['kode_akun', $kodeAkun]);
            if (!$cek_bank) {
                if ($kode_klasifikasi == 'K0001') { // klasifikasi bank
                    $data_bank_perusahaan = [
                        'kode_cabang'   => $this->session->userdata('cabang'),
                        'kode_bank'     => master_kode('edc comp', 10, 'BC', '-'),
                        'kode_akun'     => $kodeAkun,
                        'keterangan'    => $nama_akun,
                        'masuk'         => 0,
                        'keluar'        => 0,
                        'saldo'         => 0,
                    ];

                    $this->M_global->insertData('m_bank_perusahaan', $data_bank_perusahaan);
                }
            }

            aktifitas_user('Master Akun', $cek_param, $kodeAkun, $this->M_global->getData('m_akun', ['kode_akun' => $kodeAkun])->nama_akun, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi ambil informasi akun berdasarkan kode akun
    public function getInfoAkun($kode_akun)
    {
        // ambil data akun berdasarkan kode_akun
        $data = $this->db->query("SELECT a.*, (SELECT nama_akun FROM m_akun WHERE kode_akun = a.sub_akun) AS nama_sub, (SELECT klasifikasi FROM klasifikasi_akun WHERE kode_klasifikasi = a.kode_klasifikasi) AS nama_klasifikasi FROM m_akun a WHERE a.kode_akun = '$kode_akun'")->row();
        // lempar ke view
        echo json_encode($data);
    }

    public function subAkun()
    {
        $sub_akun = $this->M_global->getResult('m_akun');

        echo json_encode($sub_akun);
    }

    // fungsi hapus akun berdasarkan kode_akun
    public function delAkun($kode_akun)
    {
        // jalankan fungsi hapus akun berdasarkan kode_akun
        // ambil data sebelum update
        $isi_sebelum = json_encode($this->M_global->getData('m_akun', ['kode_akun' => $kode_akun]));
        // update data
        $cek = $this->M_global->updateData('m_akun', [
            'hapus' => 1,
            'tgl_hapus' => date('Y-m-d'),
            'jam_hapus' => date('H:i:s')
        ], ['kode_akun' => $kode_akun]);
        // ambil data sesudah update
        $isi_sesudah = json_encode($this->M_global->getData('m_akun', ['kode_akun' => $kode_akun]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Akun', 'menghapus', $kode_akun, $this->M_global->getData('m_akun', ['kode_akun' => $kode_akun])->nama_akun, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    /**
     * Master tipe_bank
     * untuk menampilkan, menambahkan, dan mengubah tipe_bank dalam sistem
     */

    // tipe_bank page
    public function tipe_bank()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Tipe Bank',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/tipe_bank_list',
            'param1'        => '1',
        ];

        $this->template->load('Template/Content', 'Master/Umum/Tipe', $parameter);
    }

    // fungsi list tipe_bank
    public function tipe_bank_list($param1 = '')
    {
        // parameter untuk list table
        $table            = 'tipe_bank';
        $colum            = ['id', 'kode_tipe', 'keterangan'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param1   = 'hapus < ';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list             = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss               = 'onclick="ubah(' . "'" . $rd->kode_tipe . "'" . ')"';
            } else {
                $upd_diss               = 'disabled';
            }

            if ($deleted > 0) {
                $cek1                   = $this->M_global->getDataResult('bayar_um_card_detail', ['kode_tipe' => $rd->kode_tipe]);
                if (count($cek1) > 0) {
                    $del_diss           = 'disabled';
                } else {
                    $cek2               = $this->M_global->getDataResult('bayar_card_detail', ['kode_tipe' => $rd->kode_tipe]);
                    if (count($cek2) > 0) {
                        $del_diss       = 'disabled';
                    } else {
                        $cek3           = $this->M_global->getDataResult('bayar_kas_card', ['kode_tipe' => $rd->kode_tipe]);
                        if (count($cek3) > 0) {
                            $del_diss   = 'disabled';
                        } else {
                            $del_diss   = 'onclick="hapus(' . "'" . $rd->kode_tipe . "'" . ')"';
                        }
                    }
                }
            } else {
                $del_diss               = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_tipe;
            $row[]  = $rd->keterangan;
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" style="margin-bottom: 5px;" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" style="margin-bottom: 5px;" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi cek tipe_bank berdasarkan keterangan tipe_bank
    public function cekTipeBank()
    {
        // ambil keterangan inputan
        $keterangan   = $this->input->post('keterangan');

        // cek keterangan pada table tipe_bank
        $cek          = $this->M_global->jumDataRow('tipe_bank', ['keterangan' => $keterangan]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi proses simpan/update tipe_bank
    public function tipe_bank_proses($param)
    {
        // variable
        $keterangan       = $this->input->post('keterangan');

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kodeTipe   = master_kode('tipe_bank', 10, 'TBK');
        } else { // selain itu
            // ambil kode dari inputan
            $kodeTipe   = $this->input->post('kodeTipe');
        }

        $isi_sebelum = json_encode($this->M_global->getData('tipe_bank', ['kode_tipe' => $kodeTipe]));

        // tampung variable kedalam $isi
        $isi = [
            'kode_tipe'     => $kodeTipe,
            'keterangan'    => $keterangan,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek = $this->M_global->insertData('tipe_bank', $isi);

            $cek_param = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek = $this->M_global->updateData('tipe_bank', $isi, ['kode_tipe' => $kodeTipe]);

            $cek_param = 'mengubah';
        }

        $isi_sesudah = json_encode($this->M_global->getData('tipe_bank', ['kode_tipe' => $kodeTipe]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user(
                'Master Tipe Bank',
                $cek_param,
                $kodeTipe,
                $this->M_global->getData('tipe_bank', ['kode_tipe' => $kodeTipe])->keterangan,
                $isi_sesudah,
                $isi_sebelum
            );

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi ambil informasi tipe_bank berdasarkan kode tipe_bank
    public function getInfoTipe($kode_satuan)
    {
        // ambil data tipe berdasarkan kode_tipe
        $data = $this->M_global->getData('tipe_bank', ['kode_tipe' => $kode_satuan]);
        // lempar ke view
        echo json_encode($data);
    }

    // fungsi hapus tipe berdasarkan kode_tipe
    public function delTipe($kode_tipe)
    {
        // jalankan fungsi hapus tipe berdasarkan kode_tipe
        // ambil data sebelum update
        $isi_sebelum = json_encode($this->M_global->getData('tipe_bank', ['kode_tipe' => $kode_tipe]));
        // update data
        $cek = $this->M_global->updateData('tipe_bank', [
            'hapus' => 1,
            'tgl_hapus' => date('Y-m-d'),
            'jam_hapus' => date('H:i:s')
        ], ['kode_tipe' => $kode_tipe]);
        // ambil data sesudah update
        $isi_sesudah = json_encode($this->M_global->getData('tipe_bank', ['kode_tipe' => $kode_tipe]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Tipe Bank', 'menghapus', $kode_tipe, $this->M_global->getData('tipe_bank', ['kode_tipe' => $kode_tipe])->keterangan, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Master Ruang
     * untuk menampilkan, menambahkan, dan mengubah ruang dalam sistem
     */

    // ruang page
    public function ruang()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Ruang',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/ruang_list/',
            'param1'        => '1',
        ];

        $this->template->load('Template/Content', 'Master/Umum/Ruang', $parameter);
    }

    // fungsi list ruang
    public function ruang_list($param1)
    {
        // parameter untuk list table
        $table            = 'm_ruang';
        $colum            = ['id', 'kode_ruang', 'keterangan', 'jenis'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param1   = 'hapus < ';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list             = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss     = 'onclick="ubah(' . "'" . $rd->kode_ruang . "'" . ')"';
            } else {
                $upd_diss     = 'disabled';
            }

            if ($deleted > 0) {
                $bed             = $this->M_global->getResult('bed');

                $ruang             = [];
                foreach ($bed as $b) {
                    $ruang[]       = [$b->kode_ruang];
                }

                $flattened_ruang   = array_merge(...$ruang);

                if (in_array($rd->kode_ruang, $flattened_ruang)) {
                    $del_diss       = 'disabled';
                } else {
                    $del_diss       = 'onclick="hapus(' . "'" . $rd->kode_ruang . "'" . ')"';
                }
            } else {
                $del_diss           = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_ruang;
            $row[]  = $rd->keterangan;
            $row[]  = (($rd->jenis == 1) ? 'Rawat Jalan' : 'Rawat Inap');
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" style="margin-bottom: 5px;" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" style="margin-bottom: 5px;" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi cek ruang berdasarkan keterangan ruang
    public function cekRuang()
    {
        // ambil keterangan inputan
        $keterangan   = $this->input->post('keterangan');

        // cek keterangan pada table m_ruang
        $cek          = $this->M_global->jumDataRow('m_ruang', ['keterangan' => $keterangan]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi proses simpan/update ruang
    public function ruang_proses($param)
    {
        // variable
        $keterangan       = $this->input->post('keterangan');
        $jenis            = $this->input->post('jenis');

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kodeRuang   = master_kode('ruang', 10, 'RG');
        } else { // selain itu
            // ambil kode dari inputan
            $kodeRuang   = $this->input->post('kodeRuang');
        }

        $isi_sebelum = json_encode($this->M_global->getData('m_ruang', ['kode_ruang' => $kodeRuang]));

        // tampung variable kedalam $isi
        $isi = [
            'kode_ruang'    => $kodeRuang,
            'keterangan'    => $keterangan,
            'jenis'         => $jenis,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek = $this->M_global->insertData('m_ruang', $isi);

            $cek_param = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek = $this->M_global->updateData('m_ruang', $isi, ['kode_ruang' => $kodeRuang]);

            $cek_param = 'mengubah';
        }

        $isi_sesudah = json_encode($this->M_global->getData('m_ruang', ['kode_ruang' => $kodeRuang]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Ruang', $cek_param, $kodeRuang, $this->M_global->getData('m_ruang', ['kode_ruang' => $kodeRuang])->keterangan, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi ambil informasi ruang berdasarkan kode ruang
    public function getInfoRuang($kode_ruang)
    {
        // ambil data ruang berdasarkan kode_ruang
        $data = $this->M_global->getData('m_ruang', ['kode_ruang' => $kode_ruang]);
        // lempar ke view
        echo json_encode($data);
    }

    // fungsi hapus ruang berdasarkan kode_ruang
    public function delRuang($kode_ruang)
    {
        // jalankan fungsi hapus ruang berdasarkan kode_ruang
        // ambil data sebelum update
        $isi_sebelum = json_encode($this->M_global->getData('m_ruang', ['kode_ruang' => $kode_ruang]));
        // update data
        $cek = $this->M_global->updateData('m_ruang', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_ruang' => $kode_ruang]);
        // ambil data sesudah update
        $isi_sesudah = json_encode($this->M_global->getData('m_ruang', ['kode_ruang' => $kode_ruang]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Ruang', 'menghapus', $kode_ruang, $this->M_global->getData('m_ruang', ['kode_ruang' => $kode_ruang])->keterangan, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Master Bed
     * untuk menampilkan, menambahkan, dan mengubah bed dalam sistem
     */

    // bed page
    public function bed()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Bed',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/bed_list/',
            'ruang'         => $this->M_global->getResult('m_ruang'),
            'param1'        => '1',
        ];

        $this->template->load('Template/Content', 'Master/Umum/Bed', $parameter);
    }

    // fungsi list bed
    public function bed_list($param1)
    {
        // parameter untuk list table
        $table            = 'bed';
        $colum            = ['id', 'kode_bed', 'nama_bed', 'kode_ruang', 'status'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param1   = 'hapus < ';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list             = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss         = 'onclick="ubah(' . "'" . $rd->kode_bed . "'" . ')"';
            } else {
                $upd_diss         = 'disabled';
            }

            if ($deleted > 0) {
                $m_bed            = $this->db->query('SELECT bc.*, b.kode_ruang FROM bed_cabang bc JOIN bed b ON (b.kode_bed = bc.kode_bed AND bc.kode_cabang = "' . $this->session->userdata('cabang') . '") WHERE bc.kode_cabang = "' . $this->session->userdata('cabang') . '"  AND bc.status_bed = 1')->result();

                $bed              = [];
                foreach ($m_bed as $b) {
                    $bed[]        = [$b->kode_bed];
                }

                $flattened_bed    = array_merge(...$bed);

                if (in_array($rd->kode_bed, $flattened_bed)) {
                    $del_diss     = 'disabled';
                } else {
                    $del_diss     = 'onclick="hapus(' . "'" . $rd->kode_bed . "'" . ')"';
                }
            } else {
                $del_diss         = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_bed;
            $row[]  = $rd->nama_bed;
            $row[]  = $this->M_global->getData('m_ruang', ['kode_ruang' => $rd->kode_ruang])->keterangan;
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" style="margin-bottom: 5px;" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" style="margin-bottom: 5px;" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi cek bed berdasarkan nama_bed bed
    public function cekBed()
    {
        // ambil nama_bed inputan
        $nama_bed   = $this->input->post('nama_bed');

        // cek nama_bed pada table bed
        $cek          = $this->M_global->jumDataRow('bed', ['nama_bed' => $nama_bed]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi proses simpan/update bed
    public function bed_proses($param)
    {
        // variable
        $nama_bed       = $this->input->post('nama_bed');
        $kode_ruang     = $this->input->post('kode_ruang');
        $kode_cabang    = $this->session->userdata('cabang');

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kodeBed   = master_kode('bed', 10, 'BED');
        } else { // selain itu
            // ambil kode dari inputan
            $kodeBed   = $this->input->post('kodeBed');
        }

        $isi_sebelum = json_encode($this->M_global->getData('bed', ['kode_bed' => $kodeBed]));

        // tampung variable kedalam $isi
        $isi = [
            'kode_bed'      => $kodeBed,
            'kode_ruang'    => $kode_ruang,
            'nama_bed'      => $nama_bed,
        ];

        $isi2 = [
            'kode_bed'      => $kodeBed,
            'kode_cabang'   => $kode_cabang,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek = $this->M_global->insertData('bed', $isi);

            $cek_param = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek = [
                $this->M_global->updateData('bed', $isi, ['kode_bed' => $kodeBed]),
                $this->M_global->delData('bed_cabang', ['kode_bed' => $kodeBed, 'kode_cabang' => $kode_cabang]),
            ];

            $cek_param = 'mengubah';
        }

        $isi_sesudah = json_encode($this->M_global->getData('bed', ['kode_bed' => $kodeBed]));

        $this->M_global->insertData('bed_cabang', $isi2);

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Bed', $cek_param, $kodeBed, $this->M_global->getData('bed', ['kode_bed' => $kodeBed])->nama_bed, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi ambil informasi bed berdasarkan kode bed
    public function getInfoBed($kode_bed)
    {
        // ambil data bed berdasarkan kode_bed
        $data = $this->db->query('SELECT b.*, (SELECT keterangan FROM m_ruang WHERE kode_ruang = b.kode_ruang) AS ruang FROM bed b WHERE kode_bed = "' . $kode_bed . '"')->row();
        // lempar ke view
        echo json_encode($data);
    }

    // fungsi hapus bed berdasarkan kode_bed
    public function delBed($kode_bed)
    {
        // jalankan fungsi hapus bed berdasarkan kode_bed
        // ambil data sebelum update
        $isi_sebelum = json_encode($this->M_global->getData('bed', ['kode_bed' => $kode_bed]));
        // update data
        $cek = $this->M_global->updateData('bed', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_bed' => $kode_bed]);
        // ambil data sesudah update
        $isi_sesudah = json_encode($this->M_global->getData('bed', ['kode_bed' => $kode_bed]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Bed', 'menghapus', $kode_bed, $this->M_global->getData('bed', ['kode_bed' => $kode_bed])->nama_bed, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Master Prefix
     * untuk menampilkan, menambahkan, dan mengubah prefix dalam sistem
     */

    // prefix page
    public function prefix()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Prefix',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/prefix_list/',
            'param1'        => '1',
        ];

        $this->template->load('Template/Content', 'Master/Umum/Prefix', $parameter);
    }

    // fungsi list prefix
    public function prefix_list($param1)
    {
        // parameter untuk list table
        $table            = 'm_prefix';
        $colum            = ['id', 'kode_prefix', 'nama'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param1   = 'hapus < ';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list             = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss           = 'onclick="ubah(' . "'" . $rd->kode_prefix . "'" . ')"';
            } else {
                $upd_diss           = 'disabled';
            }

            if ($deleted > 0) {
                $barang             = $this->M_global->getResult('member');

                $prefix             = [];
                foreach ($barang as $b) {
                    $prefix[]       = [$b->kode_prefix];
                }

                $flattened_prefix   = array_merge(...$prefix);

                if (in_array($rd->kode_prefix, $flattened_prefix)) {
                    $del_diss       = 'disabled';
                } else {
                    $del_diss       = 'onclick="hapus(' . "'" . $rd->kode_prefix . "'" . ')"';
                }
            } else {
                $del_diss           = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_prefix;
            $row[]  = $rd->nama;
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" style="margin-bottom: 5px;" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" style="margin-bottom: 5px;" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi cek prefix berdasarkan nama prefix
    public function cekPrefix()
    {
        // ambil nama inputan
        $nama   = $this->input->post('nama');

        // cek nama pada table m_prefix
        $cek          = $this->M_global->jumDataRow('m_prefix', ['nama' => $nama]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi proses simpan/update prefix
    public function prefix_proses($param)
    {
        // variable
        $nama       = $this->input->post('nama');

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kodePrefix   = master_kode('prefix', 10, 'PRE');
        } else { // selain itu
            // ambil kode dari inputan
            $kodePrefix   = $this->input->post('kodePrefix');
        }

        $isi_sebelum = json_encode($this->M_global->getData('m_prefix', ['kode_prefix' => $kodePrefix]));

        // tampung variable kedalam $isi
        $isi = [
            'kode_prefix'   => $kodePrefix,
            'nama'    => $nama,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek = $this->M_global->insertData('m_prefix', $isi);

            $cek_param = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek = $this->M_global->updateData('m_prefix', $isi, ['kode_prefix' => $kodePrefix]);

            $cek_param = 'mengubah';
        }

        $isi_sesudah = json_encode($this->M_global->getData('m_prefix', ['kode_prefix' => $kodePrefix]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Prefix', $cek_param, $kodePrefix, $this->M_global->getData('m_prefix', ['kode_prefix' => $kodePrefix])->nama, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi ambil informasi prefix berdasarkan kode prefix
    public function getInfoPrefix($kode_prefix)
    {
        // ambil data prefix berdasarkan kode_prefix
        $data = $this->M_global->getData('m_prefix', ['kode_prefix' => $kode_prefix]);
        // lempar ke view
        echo json_encode($data);
    }

    // fungsi hapus prefix berdasarkan kode_prefix
    public function delPrefix($kode_prefix)
    {
        // jalankan fungsi hapus prefix berdasarkan kode_prefix
        // ambil data sebelum update
        $isi_sebelum = json_encode($this->M_global->getData('m_prefix', ['kode_prefix' => $kode_prefix]));
        // update data
        $cek = $this->M_global->updateData('m_prefix', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_prefix' => $kode_prefix]);
        // ambil data sesudah update
        $isi_sesudah = json_encode($this->M_global->getData('m_prefix', ['kode_prefix' => $kode_prefix]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Prefix', 'menghapus', $kode_prefix, $this->M_global->getData('m_prefix', ['kode_prefix' => $kode_prefix])->nama, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    // ############################################################################################################################################################################

    /**
     * Master Wilayah
     * untuk menampilkan, menambahkan, dan mengubah wilayah dalam sistem
     */

    // wilayah page
    public function wilayah()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Wilayah',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            //'list_data'     => 'Master/provinsi_list/0',
            'param1'        => '1',
        ];

        $this->template->load('Template/Content', 'Master/Umum/Wilayah', $parameter);
    }

    // fungsi list provinsi
    public function provinsi_list($param1 = '')
    {
        // parameter untuk list table
        $table            = 'm_provinsi';
        $colum            = ['id', 'kode_provinsi', 'provinsi'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param1   = 'hapus < ';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list             = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss             = 'onclick="ubah(' . "'tableProvinsi', " . "'" . $rd->kode_provinsi . "'" . ')"';
            } else {
                $upd_diss             = 'disabled';
            }

            if ($deleted > 0) {
                $kabupaten            = $this->M_global->getResult('kabupaten');

                $provinsi             = [];
                foreach ($kabupaten as $p) {
                    $provinsi[]       = [$p->kode_provinsi];
                }

                $flattened_provinsi   = array_merge(...$provinsi);

                if (in_array($rd->kode_provinsi, $flattened_provinsi)) {
                    $del_diss         = 'disabled';
                } else {
                    $del_diss         = 'onclick="hapus(' . "'tableProvinsi', " .  "'" . $rd->kode_provinsi . "'" . ')"';
                }
            } else {
                $del_diss             = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_provinsi;
            $row[]  = $rd->provinsi;
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" style="margin-bottom: 5px;" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" style="margin-bottom: 5px;" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi list kabupaten
    public function kabupaten_list($param1 = '')
    {
        // parameter untuk list table
        $table            = 'kabupaten';
        $colum            = ['id', 'kode_kabupaten', 'kabupaten', 'kode_provinsi'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param1   = 'hapus < ';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list             = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss     = 'onclick="ubah(' . "'tableKabupaten', " . "'" . $rd->kode_kabupaten . "'" . ')"';
            } else {
                $upd_diss     = 'disabled';
            }

            if ($deleted > 0) {
                $kecamatan            = $this->M_global->getResult('kecamatan');

                $kabupaten            = [];
                foreach ($kecamatan as $k) {
                    $kabupaten[]      = [$k->kode_kabupaten];
                }

                $flattened_kabupaten   = array_merge(...$kabupaten);

                if (in_array($rd->kode_kabupaten, $flattened_kabupaten)) {
                    $del_diss       = 'disabled';
                } else {
                    $del_diss       = 'onclick="hapus(' . "'tableKabupaten', " .  "'" . $rd->kode_kabupaten . "'" . ')"';
                }
            } else {
                $del_diss           = 'disabled';
            }

            $prov = $this->M_global->getData('m_provinsi', ['kode_provinsi' => $rd->kode_provinsi]);

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_kabupaten;
            $row[]  = $rd->kabupaten;
            $row[]  = $rd->kode_provinsi . ' - ' . $prov->provinsi;
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" style="margin-bottom: 5px;" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" style="margin-bottom: 5px;" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi list kecamatan
    public function kecamatan_list($param1 = '')
    {
        // parameter untuk list table
        $table            = 'kecamatan';
        $colum            = ['id', 'kode_kecamatan', 'kecamatan', 'kode_kabupaten'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param1   = 'hapus < ';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list             = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss     = 'onclick="ubah(' . "'tableKecamatan', " . "'" . $rd->kode_kecamatan . "'" . ')"';
            } else {
                $upd_diss     = 'disabled';
            }

            if ($deleted > 0) {
                $member            = $this->M_global->getResult('member');

                $kecamatan            = [];
                foreach ($member as $k) {
                    $kecamatan[]      = [$k->kecamatan];
                }

                $flattened_kecamatan   = array_merge(...$kecamatan);

                if (in_array($rd->kode_kecamatan, $flattened_kecamatan)) {
                    $del_diss       = 'disabled';
                } else {
                    $del_diss       = 'onclick="hapus(' . "'tableKecamatan', " . "'" . $rd->kode_kecamatan . "'" . ')"';
                }
            } else {
                $del_diss           = 'disabled';
            }

            $kab = $this->M_global->getData('kabupaten', ['kode_kabupaten' => $rd->kode_kabupaten]);
            $prov = $this->M_global->getData('m_provinsi', ['kode_provinsi' => $kab->kode_provinsi]);

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_kecamatan;
            $row[]  = $rd->kecamatan;
            $row[]  = $rd->kode_kabupaten . ' - ' . $kab->kabupaten;
            $row[]  = $kab->kode_provinsi . ' - ' . (!empty($prov) ? $prov->provinsi : '');
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" style="margin-bottom: 5px;" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" style="margin-bottom: 5px;" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi proses simpan/update wilayah
    public function wilayah_proses()
    {
        // variable
        $id_wil           = $this->input->post('id_wil');
        $cektab           = $this->input->post('cektab');

        $kode_provinsi    = $this->input->post('kode_provinsi');
        $provinsi         = $this->input->post('provinsi');
        $kode_kabupaten   = $this->input->post('kode_kabupaten');
        $kabupaten        = $this->input->post('kabupaten');
        $kode_kecamatan   = $this->input->post('kode_kecamatan');
        $kecamatan        = $this->input->post('kecamatan');

        if ($cektab == 1) {
            $data = [
                'kode_provinsi' => $kode_provinsi,
                'provinsi'      => $provinsi,
            ];

            $table        = 'm_provinsi';
            $kode         = $kode_provinsi;
            $nama         = $provinsi;
            $where        = ['kode_provinsi' => $id_wil];
        } else if ($cektab == 2) {
            $data = [
                'kode_provinsi'     => $provinsi,
                'kode_kabupaten'    => $kode_kabupaten,
                'kabupaten'         => $kabupaten,
            ];

            $table        = 'kabupaten';
            $kode         = $kode_kabupaten;
            $nama         = $kabupaten;
            $where        = ['kode_kabupaten' => $id_wil];
        } else {
            $data = [
                'kode_kabupaten'    => $kabupaten,
                'kode_kecamatan'    => $kode_kecamatan,
                'kecamatan'         => $kecamatan,
            ];

            $table        = 'kecamatan';
            $kode         = $kode_kecamatan;
            $nama         = $kecamatan;
            $where        = ['kode_kecamatan' => $id_wil];
        }

        $isi_sebelum = json_encode($this->M_global->getData($table, $where));

        if ($id_wil == '') {
            $cek_param    = 'Menambahkan';
            $cek          = $this->M_global->insertData($table, $data);
        } else {
            $cek_param    = 'Mengubah';
            $cek          = $this->M_global->updateData($table, $data, $where);
        }

        $isi_sesudah = json_encode($this->M_global->getData($table, $where));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Wilayah', $cek_param, $kode, $nama, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi ambil informasi provinsi berdasarkan kode wilayah
    public function getWilayah($param, $kode)
    {
        // ambil data wilayah berdasarkan kode_wilayah
        if ($param == 'tableProvinsi') {
            $data = $this->M_global->getData('m_provinsi', ['kode_provinsi' => $kode]);
        } else if ($param == 'tableKabupaten') {
            $data = $this->db->query('SELECT k.*, (SELECT provinsi FROM m_provinsi WHERE kode_provinsi = k.kode_provinsi) AS provinsi FROM kabupaten k WHERE kode_kabupaten = "' . $kode . '"')->row();
        } else {
            $data = $this->db->query('SELECT kec.*, kab.kabupaten, (SELECT provinsi FROM m_provinsi WHERE kode_provinsi = kab.kode_provinsi) AS provinsi FROM kecamatan kec JOIN kabupaten kab ON kec.kode_kabupaten = kab.kode_kabupaten WHERE kec.kode_kecamatan = "' . $kode . '"')->row();
        }
        // lempar ke view
        echo json_encode($data);
    }

    // fungsi hapus wilayah berdasarkan kode
    public function delWilayah($param, $kode)
    {
        // jalankan fungsi hapus wilayah berdasarkan kode_wilayah
        if ($param == 'tableProvinsi') {
            // ambil data sebelum update
            $isi_sebelum = json_encode($this->M_global->getData('m_provinsi', ['kode_provinsi' => $kode]));
            // update data
            $cek = $this->M_global->updateData('m_provinsi', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_provinsi' => $kode]);
            // ambil data sesudah update
            $isi_sesudah = json_encode($this->M_global->getData('m_provinsi', ['kode_provinsi' => $kode]));

            aktifitas_user('Master Provinsi', 'menghapus', $kode, $this->M_global->getData('m_provinsi', ['kode_provinsi' => $kode])->provinsi, $isi_sesudah, $isi_sebelum);
        } else if ($param == 'tableKabupaten') {
            // ambil data sebelum update
            $isi_sebelum = json_encode($this->M_global->getData('kabupaten', ['kode_kabupaten' => $kode]));
            // update data
            $cek = $this->M_global->updateData('kabupaten', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_kabupaten' => $kode]);
            // ambil data sesudah update
            $isi_sesudah = json_encode($this->M_global->getData('kabupaten', ['kode_kabupaten' => $kode]));

            aktifitas_user('Master Kabupaten', 'menghapus', $kode, $this->M_global->getData('kabupaten', ['kode_kabupaten' => $kode])->kabupaten, $isi_sesudah, $isi_sebelum);
        } else {
            // ambil data sebelum update
            $isi_sebelum = json_encode($this->M_global->getData('kecamatan', ['kode_kecamatan' => $kode]));
            // update data
            $cek = $this->M_global->updateData('kecamatan', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_kecamatan' => $kode]);
            // ambil data sesudah update
            $isi_sesudah = json_encode($this->M_global->getData('kecamatan', ['kode_kecamatan' => $kode]));

            aktifitas_user('Master Kecamatan', 'menghapus', $kode, $this->M_global->getData('kecamatan', ['kode_kecamatan' => $kode])->kecamatan, $isi_sesudah, $isi_sebelum);
        }

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
     * Master Jenis Bayar
     * untuk menampilkan, menambahkan, dan mengubah jenis_bayar dalam sistem
     */

    // jenis_bayar page
    public function jenis_bayar()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Jenis Bayar',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/jenis_bayar_list',
            'param1'        => '1',
        ];

        $this->template->load('Template/Content', 'Master/Umum/Jenis_bayar', $parameter);
    }

    // fungsi list jenis_bayar
    public function jenis_bayar_list($param1 = '')
    {
        // parameter untuk list table
        $table                    = 'm_jenis_bayar';
        $colum                    = ['id', 'kode_jenis_bayar', 'keterangan'];
        $order                    = 'id';
        $order2                   = 'desc';
        $order_arr                = ['id' => 'desc'];
        $kondisi_param1           = 'hapus < ';

        // kondisi role
        $updated                  = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted                  = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list                     = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data                     = [];
        $no                       = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss             = 'onclick="ubah(' . "'" . $rd->kode_jenis_bayar . "'" . ')"';
            } else {
                $upd_diss             = 'disabled';
            }

            if ($deleted > 0) {
                $cekIsset         = $this->M_global->jumDataRow('pendaftaran', ['kode_jenis_bayar' => $rd->kode_jenis_bayar]);
                if ($cekIsset < 1) {
                    $del_diss     = 'onclick="hapus(' . "'" . $rd->kode_jenis_bayar . "'" . ')"';
                } else {
                    $del_diss     = 'disabled';
                }
            } else {
                $del_diss         = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_jenis_bayar;
            $row[]  = $rd->keterangan;
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" style="margin-bottom: 5px;" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" style="margin-bottom: 5px;" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi cek jenis_bayar berdasarkan keterangan jenis_bayar
    public function cekJenisBayar()
    {
        // ambil keterangan inputan
        $keterangan   = $this->input->post('keterangan');

        // cek keterangan pada table m_jenis_bayar
        $cek          = $this->M_global->jumDataRow('m_jenis_bayar', ['keterangan' => $keterangan]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi proses simpan/update jenis_bayar
    public function jenis_bayar_proses($param)
    {
        // variable
        $keterangan   = $this->input->post('keterangan');

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kodeJenisBayar = master_kode('jenis_bayar', 10, 'JB');
        } else { // selain itu
            // ambil kode dari inputan
            $kodeJenisBayar = $this->input->post('kodeJenisBayar');
        }

        $isi_sebelum = json_encode($this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $kodeJenisBayar]));

        // tampung variable kedalam $isi
        $isi = [
            'kode_jenis_bayar'  => $kodeJenisBayar,
            'keterangan'        => $keterangan,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek          = $this->M_global->insertData('m_jenis_bayar', $isi);

            $cek_param    = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek          = $this->M_global->updateData('m_jenis_bayar', $isi, ['kode_jenis_bayar' => $kodeJenisBayar]);

            $cek_param    = 'mengubah';
        }

        $isi_sesudah = json_encode($this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $kodeJenisBayar]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Jenis Pembayaran', $cek_param, $kodeJenisBayar, $this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $kodeJenisBayar])->keterangan, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi ambil informasi jenis_bayar berdasarkan kode jenis_bayar
    public function getInfoJenisBayar($kode_jenis_bayar)
    {
        // ambil data jenis_bayar berdasarkan kode_jenis_bayar
        $data = $this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $kode_jenis_bayar]);
        // lempar ke view
        echo json_encode($data);
    }

    // fungsi hapus jenis_bayar berdasarkan kode_jenis_bayar
    public function delJenisBayar($kode_jenis_bayar)
    {
        // jalankan fungsi hapus jenis_bayar berdasarkan kode_jenis_bayar
        // ambil data sebelum update
        $isi_sebelum = json_encode($this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $kode_jenis_bayar]));
        // update data
        $cek = $this->M_global->updateData('m_jenis_bayar', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_jenis_bayar' => $kode_jenis_bayar]);
        // ambil data sesudah update
        $isi_sesudah = json_encode($this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $kode_jenis_bayar]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Jenis Bayar', 'menghapus', $kode_jenis_bayar, $this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $kode_jenis_bayar])->keterangan, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Master Barang
     * untuk menampilkan, menambahkan, dan mengubah barang dalam sistem
     */

    // barang page
    public function barang()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Barang',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/barang_list',
            'param1'        => '',
            'kategori'      => $this->M_global->getResult('m_kategori'),
        ];

        $this->template->load('Template/Content', 'Master/Internal/Barang', $parameter);
    }

    // form barang page
    public function form_barang($param)
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param == '0') {
            $barang = null;
            $barang_stok = null;
        } else {
            $barang = $this->M_global->getData('barang', ['kode_barang' => $param]);
            $barang_stok = $this->M_global->getDataResult('barang_stok', ['kode_barang' => $param, 'kode_cabang' => $this->session->userdata('kode_cabang')]);
        }

        $parameter = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Barang',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => '',
            'barang'        => $barang,
            'barang_stok'   => $barang_stok,
            'gudang'        => $this->M_global->getResult('m_gudang'),
            'satuan1'       => $this->M_global->getData('barang_satuan', ['kode_barang' => $param, 'ke' => 1]),
            'satuan2'       => $this->M_global->getData('barang_satuan', ['kode_barang' => $param, 'ke' => 2]),
            'satuan3'       => $this->M_global->getData('barang_satuan', ['kode_barang' => $param, 'ke' => 3]),
            'kategori'      => $this->M_global->getResult('m_kategori'),
            'm_satuan'      => $this->M_global->getResult('m_satuan'),
            'jenis'         => $this->M_global->getResult('m_jenis'),
            'barang_jenis'  => $this->M_global->getDataResult('barang_jenis', ['kode_barang' => $param]),
            'cabang_all'    => $this->M_global->getResult('cabang'),
            'barang_cabang' => $this->M_global->getDataResult('barang_cabang', ['kode_barang' => $param]),
            'pajak'         => $this->M_global->getData('m_pajak', ['id' => 1])->persentase,
        ];

        $this->template->load('Template/Content', 'Master/Internal/Form_barang', $parameter);
    }

    // fungsi list barang
    public function barang_list($param = '')
    {
        // kondisi role
        $updated        = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted        = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list           = $this->M_barang->get_datatables($param);
        $data           = [];
        $no             = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss   = 'onclick="ubah(' . "'" . $rd->kode_barang . "'" . ')"';
            } else {
                $upd_diss   = 'disabled';
            }

            if ($deleted > 0) {
                $cekIsset       = $this->M_global->jumDataRow('barang_in_detail', ['kode_barang' => $rd->kode_barang]);
                $cekIsset2      = $this->M_global->jumDataRow('barang_in_retur_detail', ['kode_barang' => $rd->kode_barang]);
                $cekIsset3      = $this->M_global->jumDataRow('barang_out_detail', ['kode_barang' => $rd->kode_barang]);
                $cekIsset4      = $this->M_global->jumDataRow('barang_out_retur_detail', ['kode_barang' => $rd->kode_barang]);

                if ($cekIsset > 0 || $cekIsset2 > 0 || $cekIsset3 > 0 || $cekIsset4 > 0) {
                    $del_diss   = 'disabled';
                } else {
                    $del_diss   = 'onclick="hapus(' . "'" . $rd->kode_barang . "'" . ')"';
                }
            } else {
                $del_diss       = 'disabled';
            }

            $satuan1    = $this->M_global->getData('m_satuan', ['kode_satuan' => $rd->kode_satuan]);
            $satuan2    = $this->M_global->getData('m_satuan', ['kode_satuan' => $rd->kode_satuan2]);
            $satuan3    = $this->M_global->getData('m_satuan', ['kode_satuan' => $rd->kode_satuan3]);

            $row        = [];
            $row[]      = $no++;
            $row[]      = $rd->kode_barang . '<br><a type="button" style="margin-bottom: 5px;" class="btn btn-dark" target="_blank" href="' . site_url('Master/print_barcode/') . $rd->kode_barang . '"><i class="fa-solid fa-barcode"></i> Barcode</a>';
            $row[]      = $rd->nama;
            $row[]      = $satuan1->keterangan . ((!empty($satuan2) ? '<br>' . $satuan2->keterangan . ' ~ ' . number_format($rd->qty_satuan2) . ' ' . $satuan1->keterangan : '')) . ((!empty($satuan3) ? '<br>' . $satuan3->keterangan . ' ~ ' . number_format($rd->qty_satuan3) . ' ' . $satuan1->keterangan : ''));
            $row[]      = $this->M_global->getData('m_kategori', ['kode_kategori' => $rd->kode_kategori])->keterangan;
            $row[]      = '<div class="text-right">' . number_format($rd->hna) . '</div>';
            $row[]      = '<div class="text-right">' . number_format($rd->hpp) . '</div>';
            $row[]      = '<div class="text-right">' . number_format($rd->harga_jual) . '</div>';
            $row[]      = '<div class="text-right">' . number_format($rd->nilai_persediaan) . '</div>';
            $row[]      = '<div class="text-center">
                <button type="button" style="margin-bottom: 5px;" class="btn btn-warning" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" style="margin-bottom: 5px;" class="btn btn-danger" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
            </div>';
            $data[]     = $row;
        }

        // hasil server side
        $output = [
            "draw"            => $_POST['draw'],
            "recordsTotal"    => $this->M_barang->count_all($param),
            "recordsFiltered" => $this->M_barang->count_filtered($param),
            "data"            => $data,
        ];

        // kirimkan ke view
        echo json_encode($output);
    }

    // fungsi print barcode
    public function print_barcode($kode_barang)
    {
        barcode($kode_barang);
    }

    // fungsi cek barang berdasarkan nama barang
    public function cekBar()
    {
        // ambil nama inputan
        $nama = $this->input->post('nama');

        // cek nama pada table barang
        $cek  = $this->M_global->jumDataRow('barang', ['nama' => $nama]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    // ambil pajak aktif
    public function getPajak()
    {
        $cek = $this->M_global->getData('m_pajak', ['aktif' => 1]);

        echo json_encode(['pajak' => ($cek->persentase / 100)]);
    }

    // fungsi proses simpan/update barang
    public function barang_proses($param)
    {
        // variable
        $input_kode         = $this->input->post('kodeBarang');
        $nama               = $this->input->post('nama');
        $kode_satuan        = $this->input->post('kode_satuan');
        $kode_satuan2       = $this->input->post('kode_satuan2');
        $kode_satuan3       = $this->input->post('kode_satuan3');
        $qty_satuan2        = $this->input->post('qty_satuan2');
        $qty_satuan3        = $this->input->post('qty_satuan3');
        $opsi_hpp           = $this->input->post('opsi_hpp');
        $opsi_jual          = $this->input->post('opsi_jual');
        $margin             = str_replace(",", "", $this->input->post('margin'));
        $kode_kategori      = $this->input->post('kode_kategori');
        $kode_jenis         = $this->input->post('kode_jenis');
        $hna                = str_replace(",", "", $this->input->post('hna'));
        $hpp                = str_replace(",", "", $this->input->post('hpp'));
        $harga_jual         = str_replace(",", "", $this->input->post('harga_jual'));
        $nilai_persediaan   = str_replace(",", "", $this->input->post('nilai_persediaan'));
        $stok_min           = str_replace(",", "", $this->input->post('stok_min'));
        $stok_max           = str_replace(",", "", $this->input->post('stok_max'));
        $kode_cabang        = $this->session->userdata('cabang');

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            if ($input_kode == "") {
                $kodeBarang = master_kode('barang', 10, $this->session->userdata('init_cabang'), '~', 'B');
            } else {
                $kodeBarang = $input_kode;
            }
        } else { // selain itu
            // ambil kode dari inputan
            $kodeBarang = $input_kode;
        }

        $isi_sebelum = json_encode($this->M_global->getData('barang', ['kode_barang' => $kodeBarang]));

        // configurasi upload file
        $config['upload_path']    = 'assets/img/obat/';
        $config['allowed_types']  = 'jpg|png|jpeg';
        $config['max_size']       = '10240';
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ($_FILES['filefoto']['name']) { // jika file didapatkan nama filenya
            // upload file
            $this->upload->do_upload('filefoto');

            // ambil namanya berdasarkan nama file upload
            $image                = $this->upload->data('file_name');
        } else { // selain itu
            // beri nilai default
            $cek_barang           = $this->M_global->getData('barang', ['kode_barang' => $kodeBarang]);
            if ($cek_barang) {
                $image            = $cek_barang->image;
            } else {
                $image            = 'default.jpg';
            }
        }

        // dell_field('barang', 'kode_jenis');

        $isi = [
            'kode_barang'       => $kodeBarang,
            'nama'              => $nama,
            'kode_satuan'       => $kode_satuan,
            'kode_satuan2'      => $kode_satuan2,
            'kode_satuan3'      => $kode_satuan3,
            'qty_satuan2'       => $qty_satuan2,
            'qty_satuan3'       => $qty_satuan3,
            'opsi_hpp'          => $opsi_hpp,
            'opsi_jual'         => $opsi_jual,
            'margin'            => $margin,
            'kode_kategori'     => $kode_kategori,
            'image'             => $image,
            'hna'               => $hna,
            'hpp'               => $hpp,
            'harga_jual'        => $harga_jual,
            'nilai_persediaan'  => $nilai_persediaan,
            'stok_min'          => $stok_min,
            'stok_max'          => $stok_max,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek = [
                $this->M_global->insertData('barang', $isi),
            ];

            $cek_param = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek = [
                $this->M_global->updateData('barang', $isi, ['kode_barang' => $kodeBarang]),
                $this->M_global->delData('barang_jenis', ['kode_barang' => $kodeBarang]),
                $this->M_global->delData('barang_cabang', ['kode_barang' => $kodeBarang]),
            ];

            $cek_param = 'mengubah';
        }

        // barang cabang
        $kode_cabang = $this->input->post('kode_cabang');
        foreach ($kode_cabang as $kc) {
            $_cabang        = $kc;
            $data_cabang    = [
                'kode_cabang' => $_cabang,
                'kode_barang' => $kodeBarang,
            ];

            $this->M_global->insertData('barang_cabang', $data_cabang);
        }

        foreach ($kode_jenis as $kj) {
            $_kode_jenis    = $kj;
            $isi_jenis      = [
                'kode_jenis'    => $_kode_jenis,
                'kode_barang'   => $kodeBarang,
            ];

            $this->M_global->insertData('barang_jenis', $isi_jenis);
        }

        $isi_sesudah = json_encode($this->M_global->getData('barang', ['kode_barang' => $kodeBarang]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Barang', $cek_param, $kodeBarang, $this->M_global->getData('barang', ['kode_barang' => $kodeBarang])->nama, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi hapus barang berdasarkan kode_barang
    public function delBar($kode_barang)
    {
        // jalankan fungsi hapus barang berdasarkan kode_barang
        // $barang_cabang = count($this->M_global->getDataResult('barang_cabang', ['kode_barang' => $kode_barang, 'kode_cabang <> ' => $this->session->userdata('cabang')]));

        // if ($barang_cabang > 0) {
        //     echo json_encode(['status' => 2]);
        // } else {
        // }
        aktifitas_user('Master Barang', 'menghapus', $kode_barang, $this->M_global->getData('barang', ['kode_barang' => $kode_barang])->nama);
        // $cek = [
        //     $this->M_global->delData('barang', ['kode_barang' => $kode_barang]),
        //     $this->M_global->delData('barang_cabang', ['kode_barang' => $kode_barang]),
        //     $this->M_global->delData('barang_jenis', ['kode_barang' => $kode_barang]),
        // ];
        $cek = $this->M_global->updateData('barang_cabang', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_barang' => $kode_barang, 'kode_cabang' => $this->session->userdata('cabang')]);

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
     * Master Logistik barang
     * untuk menampilkan, menambahkan, dan mengubah logistik dalam sistem
     */

    // logistik page
    public function logistik()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Logistik',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/logistik_list',
            'param1'        => '',
            'kategori'      => $this->M_global->getResult('m_kategori'),
        ];

        $this->template->load('Template/Content', 'Master/Internal/Logistik', $parameter);
    }

    // form logistik page
    public function form_logistik($param)
    {
        // website config
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version    = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param != '0') {
            $logistik   = $this->M_global->getData('logistik', ['kode_logistik' => $param]);
        } else {
            $logistik   = null;
        }

        $parameter = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Logistik',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => '',
            'logistik'      => $logistik,
            'satuan'        => $this->M_global->getResult('m_satuan'),
            'kategori'      => $this->M_global->getResult('m_kategori'),
            'cabang_all'    => $this->M_global->getResult('cabang'),
            'barang_cabang' => $this->M_global->getDataResult('logistik_cabang', ['kode_barang' => $param]),
            'pajak'         => $this->M_global->getData('m_pajak', ['id' => 1])->persentase,
        ];

        $this->template->load('Template/Content', 'Master/Internal/Form_logistik', $parameter);
    }

    // fungsi list logistik
    public function logistik_list($param1 = '')
    {
        // parameter untuk list table
        $table            = 'logistik';
        $colum            = ['logistik.id', 'logistik.kode_logistik', 'nama', 'kode_satuan', 'kode_kategori', 'hna', 'hpp', 'harga_jual', 'nilai_persediaan'];
        $order            = 'logistik.id';
        $order2           = 'desc';
        $order_arr        = ['logistik.id' => 'asc'];
        $kondisi_param1   = 'kode_kategori';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list             = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss     = 'onclick="ubah(' . "'" . $rd->kode_logistik . "'" . ')"';
            } else {
                $upd_diss     = 'disabled';
            }

            if ($deleted > 0) {
                $del_diss     = 'onclick="hapus(' . "'" . $rd->kode_logistik . "'" . ')"';
            } else {
                $del_diss     = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_logistik . '<br><a type="button" class="btn btn-dark" target="_blank" href="' . site_url('Master/print_barcode/') . $rd->kode_logistik . '"><i class="fa-solid fa-barcode"></i> Barcode</a>';
            $row[]  = $rd->nama;
            $row[]  = $this->M_global->getData('m_satuan', ['kode_satuan' => $rd->kode_satuan])->keterangan;
            $row[]  = $this->M_global->getData('m_kategori', ['kode_kategori' => $rd->kode_kategori])->keterangan;
            $row[]  = '<div class="text-right">' . number_format($rd->hna) . '</div>';
            $row[]  = '<div class="text-right">' . number_format($rd->hpp) . '</div>';
            $row[]  = '<div class="text-right">' . number_format($rd->harga_jual) . '</div>';
            $row[]  = '<div class="text-right">' . number_format($rd->nilai_persediaan) . '</div>';
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi cek logistik berdasarkan nama logistik
    public function cekLog()
    {
        // ambil nama inputan
        $nama = $this->input->post('nama');

        // cek nama pada table logistik
        $cek  = $this->M_global->jumDataRow('logistik', ['nama' => $nama]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi proses simpan/update logistik
    public function logistik_proses($param)
    {
        // variable
        $input_kode         = $this->input->post('kodeBarang');
        $nama               = $this->input->post('nama');
        $kode_satuan        = $this->input->post('kode_satuan');
        $kode_satuan2       = $this->input->post('kode_satuan2');
        $kode_satuan3       = $this->input->post('kode_satuan3');
        $kode_kategori      = $this->input->post('kode_kategori');
        $qty_satuan2        = str_replace(",", "", $this->input->post('qty_satuan2'));
        $qty_satuan3        = str_replace(",", "", $this->input->post('qty_satuan3'));
        $hna                = str_replace(",", "", $this->input->post('hna'));
        $hpp                = str_replace(",", "", $this->input->post('hpp'));
        $opsi_hpp           = str_replace(",", "", $this->input->post('opsi_hpp'));
        $opsi_jual          = str_replace(",", "", $this->input->post('opsi_jual'));
        $margin             = str_replace(",", "", $this->input->post('margin'));
        $harga_jual         = str_replace(",", "", $this->input->post('harga_jual'));
        $nilai_persediaan   = str_replace(",", "", $this->input->post('nilai_persediaan'));

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            if ($input_kode == "") {
                $kodeLogistik   = master_kode('logistik', 10, $this->session->userdata('init_cabang'), '~', 'L');
            } else {
                $kodeLogistik   = $input_kode;
            }
        } else { // selain itu
            // ambil kode dari inputan
            $kodeLogistik       = $this->input->post('kodeLogistik');
        }

        $isi_sebelum = json_encode($this->M_global->getData('logistik', ['kode_logistik' => $kodeLogistik]));

        // tampung variable kedalam $isi
        $isi = [
            'kode_logistik'     => $kodeLogistik,
            'nama'              => $nama,
            'kode_satuan'       => $kode_satuan,
            'kode_satuan2'      => $kode_satuan2,
            'kode_satuan3'      => $kode_satuan3,
            'qty_satuan2'       => $qty_satuan2,
            'qty_satuan3'       => $qty_satuan3,
            'kode_kategori'     => $kode_kategori,
            'hna'               => $hna,
            'hpp'               => $hpp,
            'opsi_hpp'          => $opsi_hpp,
            'opsi_jual'         => $opsi_jual,
            'margin'            => $margin,
            'harga_jual'        => $harga_jual,
            'nilai_persediaan'  => $nilai_persediaan,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek          = $this->M_global->insertData('logistik', $isi);

            $cek_param    = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek = [
                $this->M_global->updateData('logistik', $isi, ['kode_logistik' => $kodeLogistik]),
                $this->M_global->delData('logistik_cabang', ['kode_barang' => $kodeLogistik]),
            ];

            $cek_param    = 'mengubah';
        }

        // barang cabang
        $kode_cabang = $this->input->post('kode_cabang');
        foreach ($kode_cabang as $kc) {
            $_cabang        = $kc;
            $data_cabang    = [
                'kode_cabang' => $_cabang,
                'kode_barang' => $kodeLogistik,
            ];

            $this->M_global->insertData('logistik_cabang', $data_cabang);
        }

        $isi_sesudah = json_encode($this->M_global->getData('logistik', ['kode_logistik' => $kodeLogistik]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Logistik', $cek_param, $kodeLogistik, $this->M_global->getData('logistik', ['kode_logistik' => $kodeLogistik])->nama, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi hapus logistik berdasarkan kode_logistik
    public function delLog($kode_logistik)
    {
        // jalankan fungsi hapus logistik berdasarkan kode_logistik
        // ambil data sebelum diupdate
        $isi_sebelum = json_encode($this->M_global->getData('logistik', ['kode_logistik' => $kode_logistik]));
        // update data
        $cek = $this->M_global->updateData('logistik_cabang', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_barang' => $kode_logistik, 'kode_cabang' => $this->session->userdata('cabang')]);
        // ambil data sesudah diupdate
        $isi_sesudah = json_encode($this->M_global->getData('logistik', ['kode_logistik' => $kode_logistik]));

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Logistik', 'menghapus', $kode_logistik, $this->M_global->getData('logistik', ['kode_logistik' => $kode_logistik])->nama, $isi_sesudah, $isi_sebelum);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Master Pengguna
     * untuk menampilkan, menambahkan, dan mengubah pengguna dalam sistem
     */

    // user page
    public function user()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Pengguna',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/user_list',
            'param1'        => '1',
        ];

        $this->template->load('Template/Content', 'Master/Internal/Pengguna', $parameter);
    }

    // fungsi list user
    public function user_list($param1 = '')
    {
        // parameter untuk list table
        $table                  = 'user';
        $colum                  = ['id', 'kode_user', 'nama', 'email', 'password', 'secondpass', 'jkel', 'foto', 'kode_role', 'actived', 'joined', 'on_off'];
        $order                  = 'id';
        $order2                 = 'desc';
        $order_arr              = ['id' => 'desc'];
        $kondisi_param1         = 'hapus < ';

        // kondisi role
        $updated                = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted                = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list                   = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data                   = [];
        $no                     = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                if ($rd->on_off < 1) {
                    $upd_diss   = 'onclick="ubah(' . "'" . $rd->kode_user . "'" . ')"';
                } else {
                    $upd_diss   = 'disabled';
                }
            } else {
                $upd_diss       = 'disabled';
            }

            if ($deleted > 0) {
                if ($rd->on_off < 1) {
                    $del_diss   = 'onclick="hapus(' . "'" . $rd->kode_user . "'" . ')"';
                } else {
                    $del_diss   = 'disabled';
                }
            } else {
                $del_diss       = 'disabled';
            }

            $role = $this->M_global->getData('m_role', ['kode_role' => $rd->kode_role]);

            if ($rd->kode_role == 'R0009') {
                $color = 'primary';
            } else if ($rd->kode_role == 'R0010') {
                $color = 'success';
            } else {
                $color = 'info';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_user . '<br><span class="badge badge-' . $color . '">' . $role->keterangan . '</span>';
            $row[]  = $rd->nama;
            $row[]  = $rd->email;
            $row[]  = (($rd->jkel == 'P') ? 'Laki-laki' : 'Perempuan');
            $row[]  = $this->M_global->getData("m_role", ["kode_role" => $rd->kode_role])->keterangan;
            $row[]  = '<div class="text-center">' . (($rd->actived == 1) ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-dark">Non-aktif</span>') . '</div>';

            if ($rd->actived > 0) {
                $actived_akun = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" onclick="actived(' . "'" . $rd->kode_user . "', 0" . ')" ' . $upd_diss . '><i class="fa-solid fa-user-xmark"></i></button>';
            } else {
                $actived_akun = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" onclick="actived(' . "'" . $rd->kode_user . "', 1" . ')" ' . $upd_diss . '><i class="fa-solid fa-user-check"></i></button>';
            }

            $row[]  = '<div class="text-center">
                ' . $actived_akun . '
                <button type="button" style="margin-bottom: 5px;" class="btn btn-warning" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" style="margin-bottom: 5px;" class="btn btn-danger" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi aktif/nonaktif user
    public function activeduser($kode_user, $param)
    {
        // jalankan fungsi update actived user
        $cek = $this->M_global->updateData('user', ['actived' => $param], ['kode_user' => $kode_user]);

        if ($cek) { // jika fungsi berjalan
            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi cek user
    public function cekUser()
    {
        $email = $this->input->post('email');

        $cek   = $this->M_global->jumDataRow('user', ['email' => $email]);

        if ($cek < 1) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // form user page
    public function form_user($param)
    {
        // website config
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version    = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param != '0') {
            $user       = $this->M_global->getData('user', ['kode_user' => $param]);
        } else {
            $user       = null;
        }

        $parameter = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Pengguna',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => '',
            'data_user'     => $user,
            'role'          => $this->M_global->getResult('m_role'),
        ];

        $this->template->load('Template/Content', 'Master/Internal/Form_user', $parameter);
    }

    // fungsi user proses
    public function user_proses($param)
    {
        // variable
        $nama         = $this->input->post('nama');
        $email        = $this->input->post('email');
        $secondpass   = $this->input->post('password');
        $password     = md5($secondpass);
        $jkel         = $this->input->post('jkel');
        $kode_role    = $this->input->post('kode_role');
        $nohp         = $this->input->post('nohp');

        // cek jkel untuk foto
        if ($jkel == 'P') { // jika pria
            // isi dengan pria
            $foto = 'pria.png';
        } else { // selain itu
            // isi dengan wanita
            $foto = 'wanita.png';
        }

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kodeUser = _codeUser($nama);
        } else { // selain itu
            // ambil kode dari inputan
            $kodeUser = $this->input->post('kodeUser');
        }

        // tampung variable kedalam $isi
        $isi = [
            'kode_user'     => $kodeUser,
            'nama'          => $nama,
            'email'         => $email,
            'password'      => $password,
            'secondpass'    => $secondpass,
            'jkel'          => $jkel,
            'foto'          => $foto,
            'kode_role'     => $kode_role,
            'nohp'          => $nohp,
            'actived'       => 1,
            'joined'        => date('Y-m-d H:i:s'),
        ];

        // tampung variable kedalam $isi2
        $isi2 = [
            'email' => $email,
            'token' => '000000',
            'valid' => 1,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek = [
                $this->M_global->insertData('user', $isi), // insert ke table user
                $this->M_global->insertData('user_token', $isi2), // insert ke table user_token
            ];

            $cek_param    = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek          = $this->M_global->updateData('user', $isi, ['kode_user' => $kodeUser]);

            $cek_param    = 'mengubah';
        }

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Pengguna', $cek_param, $kodeUser, $this->M_global->getData('user', ['kode_user' => $kodeUser])->email);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi hapus user berdasarkan kode_user
    public function delUser($kode_user)
    {
        // jalankan fungsi hapus user berdasarkan kode_user
        aktifitas_user('Master Pengguna', 'menghapus', $kode_user, $this->M_global->getData('user', ['kode_user' => $kode_user])->email);
        // $cek = $this->M_global->delData('user', ['kode_user' => $kode_user]);
        $cek = $this->M_global->updateData(
            'user',
            ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')],
            ['kode_user' => $kode_user]
        );

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
     * Master Dokter
     * untuk menampilkan, menambahkan, dan mengubah dokter dalam sistem
     */

    // dokter page
    public function dokter()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Dokter',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/dokter_list',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Master/Internal/Dokter', $parameter);
    }

    // fungsi list dokter
    public function dokter_list($param1 = '')
    {
        // parameter untuk list table
        $table                  = 'dokter';
        $colum                  = ['id', 'kode_dokter', 'nama', 'email', 'nik', 'sip', 'npwp', 'nohp', 'tgl_mulai', 'tgl_berhenti', 'status', 'provinsi', 'kabupaten', 'kecamatan', 'desa', 'kodepos'];
        $order                  = 'id';
        $order2                 = 'desc';
        $order_arr              = ['id' => 'desc'];
        $kondisi_param1         = '';

        // kondisi role
        $updated                = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted                = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list                   = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data                   = [];
        $no                     = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {

            $prov               = $this->M_global->getData('m_provinsi', ['kode_provinsi' => $rd->provinsi])->provinsi;
            $kab                = $this->M_global->getData('kabupaten', ['kode_kabupaten' => $rd->kabupaten])->kabupaten;
            $kec                = $this->M_global->getData('kecamatan', ['kode_kecamatan' => $rd->kecamatan])->kecamatan;

            if ($updated > 0) {
                $upd_diss       = 'onclick="ubah(' . "'" . $rd->kode_dokter . "'" . ')"';
            } else {
                $upd_diss       = 'disabled';
            }

            if ($deleted > 0) {
                $cekIsset       = $this->M_global->jumDataRow('pendaftaran', ['kode_dokter' => $rd->kode_dokter]);
                if ($cekIsset > 0) {
                    $del_diss   = 'disabled';
                } else {
                    $del_diss   = 'onclick="hapus(' . "'" . $rd->kode_dokter . "'" . ')"';
                }
            } else {
                $del_diss       = 'disabled';
            }

            $dokter_poli        = $this->M_global->getDataResult('dokter_poli', ['kode_dokter' => $rd->kode_dokter]);

            $dpoli              = [];
            foreach ($dokter_poli as $dp) {
                $dpoli[]        = ' ' . $this->M_global->getData('m_poli', ['kode_poli' => $dp->kode_poli])->keterangan;
            }

            if ($rd->status > 0) {
                $actived_akun   = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" onclick="actived(' . "'" . $rd->kode_dokter . "', 0" . ')" ' . $upd_diss . '><i class="fa-solid fa-user-xmark"></i></button>';
            } else {
                $actived_akun   = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" onclick="actived(' . "'" . $rd->kode_dokter . "', 1" . ')" ' . $upd_diss . '><i class="fa-solid fa-user-check"></i></button>';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_dokter;
            $row[]  = $rd->nama;
            $row[]  = $rd->nohp;
            $row[]  = 'Prov. ' . $prov . ',<br>Kab. ' . $kab . ',<br>Kec. ' . $kec . ',<br>Ds. ' . $rd->desa . ',<br>(POS: ' . $rd->kodepos . ')';
            $row[]  = 'Mulai: <br><span class="float-right">' . date('d/m/Y', strtotime($rd->tgl_mulai)) . '</span><br>Hingga: <br><span class="float-right">' . date('d/m/Y', strtotime($rd->tgl_berhenti)) . '</span>';
            $row[]  = $dpoli;
            $row[]  = '<div class="text-center">' . (($rd->status == 1) ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-dark">Non-aktif</span>') . '</div>';
            $row[]  = '<div class="text-center">
                ' . $actived_akun . '
                <button type="button" style="margin-bottom: 5px;" class="btn btn-warning" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" style="margin-bottom: 5px;" class="btn btn-danger" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi aktif/nonaktif dokter
    public function activeddokter($kode_dokter, $param)
    {
        // jalankan fungsi update actived dokter
        $cek = $this->M_global->updateData('dokter', ['status' => $param], ['kode_dokter' => $kode_dokter]);

        if ($cek) { // jika fungsi berjalan
            if ($param == 1) {
                $cek_param = 'di aktifkan';
            } else {
                $cek_param = 'di non-aktifkan';
            }

            aktifitas_user('Master Dokter', $cek_param, $kode_dokter, $this->M_global->getData('dokter', ['kode_dokter' => $kode_dokter])->nama);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi cek dokter
    public function cekDokter()
    {
        $nik = $this->input->post('nik');

        $cek = $this->M_global->jumDataRow('dokter', ['nik' => $nik]);

        if ($cek < 1) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // form dokter page
    public function form_dokter($param)
    {
        // website config
        $web_setting        = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version        = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param != '0') {
            $dokter         = $this->M_global->getData('dokter', ['kode_dokter' => $param]);
            $dokter_poli    = $this->M_global->getDataResult('dokter_poli', ['kode_dokter' => $param]);

            $data_dokter    = $this->M_global->getData('dokter', ['kode_dokter' => $param]);
            $dokter_cabang  = $this->M_global->getDataResult('cabang_user', ['email' => $data_dokter->email]);
        } else {
            $dokter         = null;
            $dokter_poli    = null;
            $dokter_cabang  = null;
        }

        $parameter = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Dokter',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => '',
            'data_dokter'   => $dokter,
            'dokter_poli'   => $dokter_poli,
            'dokter_cabang' => $dokter_cabang,
            'role'          => $this->M_global->getResult('m_role'),
            'poli'          => $this->M_global->getResult('m_poli'),
            'cabang'        => $this->M_global->getResult('cabang'),
        ];

        $this->template->load('Template/Content', 'Master/Internal/Form_dokter', $parameter);
    }

    // fungsi dokter proses
    public function dokter_proses($param)
    {
        // variable
        $nama             = $this->input->post('nama');

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kodeDokter   = _kodeDokter($nama);
        } else { // selain itu
            // ambil kode dari inputan
            $kodeDokter   = $this->input->post('kodeDokter');

            $this->M_global->delData('dokter_poli', ['kode_dokter' => $kodeDokter]);
        }

        $nik                = $this->input->post('nik');
        $email              = $this->input->post('email');
        $nohp               = $this->input->post('nohp');
        $npwp               = $this->input->post('npwp');
        $sip                = $this->input->post('sip');
        $tgl_mulai          = $this->input->post('tgl_mulai');
        $tgl_berhenti       = $this->input->post('tgl_berhenti');
        $status             = $this->input->post('statusDokter');
        $provinsi           = $this->input->post('provinsi');
        $kabupaten          = $this->input->post('kabupaten');
        $kecamatan          = $this->input->post('kecamatan');
        $desa               = $this->input->post('desa');
        $kodepos            = $this->input->post('kodepos');
        $kode_poli          = $this->input->post('kode_poli');
        $password           = $this->input->post('password');
        $jkel               = $this->input->post('jkel');
        $kode_cabang_all    = $this->input->post('kode_cabang_all');
        $kode_cabang        = $this->input->post('kode_cabang');

        // tampung variable kedalam $isi
        $isi = [
            'kode_dokter'   => $kodeDokter,
            'nik'           => $nik,
            'sip'           => $sip,
            'npwp'          => $npwp,
            'nama'          => $nama,
            'email'         => $email,
            'nohp'          => $nohp,
            'tgl_mulai'     => $tgl_mulai,
            'tgl_berhenti'  => $tgl_berhenti,
            'status'        => $status,
            'provinsi'      => $provinsi,
            'kabupaten'     => $kabupaten,
            'kecamatan'     => $kecamatan,
            'desa'          => $desa,
            'kodepos'       => $kodepos,
        ];

        $data_user = [
            'kode_user'     => $kodeDokter,
            'nama'          => $nama,
            'email'         => $email,
            'password'      => md5($password),
            'secondpass'    => $password,
            'jkel'          => $jkel,
            'foto'          => (($jkel == 'P') ? 'pria.png' : 'wanita.png'),
            'kode_role'     => 'R0009',
            'nohp'          => $nohp,
            'actived'       => (($tgl_berhenti >= date('Y-m-d')) ? 1 : 0),
            'joined'        => $tgl_mulai,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek          = [
                $this->M_global->insertData('dokter', $isi),
                $this->M_global->insertData('user', $data_user),
            ];

            $cek_param    = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek_user_dokter = $this->M_global->getData('user', ['kode_user' => $kodeDokter]);

            if ($cek_user_dokter) {
                $run_user = $this->M_global->updateData('user', $data_user, ['kode_user' => $kodeDokter]);
            } else {
                $run_user = $this->M_global->insertData('user', $data_user);
            }

            $cek          = [
                $this->M_global->updateData('dokter', $isi, ['kode_dokter' => $kodeDokter]),
                $run_user,
                $this->M_global->delData('dokter_poli', ['kode_dokter' => $kodeDokter]),
                $this->M_global->delData('cabang_user', ['email' => $email]),
            ];

            $cek_param    = 'mengubah';
        }

        // insert cabang
        if ($kode_cabang_all) {
            $data_cabang = $this->M_global->getResult('cabang');
            foreach ($data_cabang as $dc) {
                $isi_cabang = [
                    'email'         => $email,
                    'kode_cabang'   => $dc->kode_cabang,
                ];

                $this->M_global->insertData('cabang_user', $isi_cabang);
            }
        } else {
            foreach ($kode_cabang as $kp) {
                $_kode_cabang_input = $kp;

                $isi_cabang = [
                    'email'         => $email,
                    'kode_cabang'   => $_kode_cabang_input,
                ];

                $this->M_global->insertData('cabang_user', $isi_cabang);
            }
        }

        // insert poli
        foreach ($kode_poli as $kp) {
            $_kode_poli_input = $kp;

            $isi_poli = [
                'kode_dokter'   => $kodeDokter,
                'kode_poli'     => $_kode_poli_input,
            ];

            $this->M_global->insertData('dokter_poli', $isi_poli);
        }

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Dokter', $cek_param, $kodeDokter, $this->M_global->getData('dokter', ['kode_dokter' => $kodeDokter])->nama);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi hapus dokter berdasarkan kode_dokter
    public function delDokter($kode_dokter)
    {
        // jalankan fungsi hapus dokter berdasarkan kode_dokter
        aktifitas_user('Master Dokter', 'menghapus', $kode_dokter, $this->M_global->getData('dokter', ['kode_dokter' => $kode_dokter])->nama);
        // $cek = [
        //     $this->M_global->delData('dokter', ['kode_dokter' => $kode_dokter]),
        //     $this->M_global->delData('dokter_poli', ['kode_dokter' => $kode_dokter]),
        // ];
        $cek = $this->M_global->updateData('dokter', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_dokter' => $kode_dokter]);

        if ($cek) { // jika fungsi berjalan

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi getPoli
    public function getPoli($kode_poli)
    {
        $data = $this->db->query('SELECT * FROM m_poli WHERE (kode_poli = "' . $kode_poli . '" OR keterangan LIKE "%' . $kode_poli . '%")')->row();
        if ($data) {
            echo json_encode($data);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Master Perawat
     * untuk menampilkan, menambahkan, dan mengubah perawat dalam sistem
     */

    // perawat page
    public function perawat()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Perawat',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/perawat_list',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Master/Internal/Perawat', $parameter);
    }

    // fungsi list perawat
    public function perawat_list($param1 = '')
    {
        // parameter untuk list table
        $table            = 'perawat';
        $colum            = ['id', 'kode_perawat', 'nama', 'email', 'nik', 'sip', 'npwp', 'nohp', 'tgl_mulai', 'tgl_berhenti', 'status', 'provinsi', 'kabupaten', 'kecamatan', 'desa', 'kodepos'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param1   = '';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // table server side tampung kedalam variable $list
        $list             = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {

            $prov         = $this->M_global->getData('m_provinsi', ['kode_provinsi' => $rd->provinsi])->provinsi;
            $kab          = $this->M_global->getData('kabupaten', ['kode_kabupaten' => $rd->kabupaten])->kabupaten;
            $kec          = $this->M_global->getData('kecamatan', ['kode_kecamatan' => $rd->kecamatan])->kecamatan;

            if ($updated > 0) {
                $upd_diss = 'onclick="ubah(' . "'" . $rd->kode_perawat . "'" . ')"';
            } else {
                $upd_diss = 'disabled';
            }

            if ($deleted > 0) {
                $del_diss = 'onclick="hapus(' . "'" . $rd->kode_perawat . "'" . ')"';
            } else {
                $del_diss = 'disabled';
            }

            $perawat_poli = $this->M_global->getDataResult('perawat_poli', ['kode_perawat' => $rd->kode_perawat]);

            $dpoli        = [];
            foreach ($perawat_poli as $dp) {
                $dpoli[] = ' ' . $this->M_global->getData('m_poli', ['kode_poli' => $dp->kode_poli])->keterangan;
            }

            if ($rd->status > 0) {
                $actived_akun = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" onclick="actived(' . "'" . $rd->kode_perawat . "', 0" . ')" ' . $upd_diss . '><i class="fa-solid fa-user-xmark"></i></button>';
            } else {
                $actived_akun = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" onclick="actived(' . "'" . $rd->kode_perawat . "', 1" . ')" ' . $upd_diss . '><i class="fa-solid fa-user-check"></i></button>';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_perawat;
            $row[]  = $rd->nama;
            $row[]  = $rd->nohp;
            $row[]  = 'Prov. ' . $prov . ',<br>Kab. ' . $kab . ',<br>Kec. ' . $kec . ',<br>Ds. ' . $rd->desa . ',<br>(POS: ' . $rd->kodepos . ')';
            $row[]  = 'Mulai: <br><span class="float-right">' . date('d/m/Y', strtotime($rd->tgl_mulai)) . '</span><br>Hingga: <br><span class="float-right">' . date('d/m/Y', strtotime($rd->tgl_berhenti)) . '</span>';
            $row[]  = $dpoli;
            $row[]  = '<div class="text-center">' . (($rd->status == 1) ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-dark">Non-aktif</span>') . '</div>';
            $row[]  = '<div class="text-center">
                ' . $actived_akun . '
                <button type="button" style="margin-bottom: 5px;" class="btn btn-warning" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" style="margin-bottom: 5px;" class="btn btn-danger" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi aktif/nonaktif perawat
    public function activedperawat($kode_perawat, $param)
    {
        // jalankan fungsi update actived perawat
        $cek = $this->M_global->updateData('perawat', ['status' => $param], ['kode_perawat' => $kode_perawat]);

        if ($cek) { // jika fungsi berjalan
            if ($param == 1) {
                $cek_param = 'di aktifkan';
            } else {
                $cek_param = 'di nonaktifkan';
            }

            aktifitas_user('Master Perawat', $cek_param, $kode_perawat, $this->M_global->getData('perawat', ['kode_perawat' => $kode_perawat])->nama);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi cek perawat
    public function cekPerawat()
    {
        $nik = $this->input->post('nik');

        $cek = $this->M_global->jumDataRow('perawat', ['nik' => $nik]);

        if ($cek < 1) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // form perawat page
    public function form_perawat($param)
    {
        // website config
        $web_setting        = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version        = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param != '0') {
            $perawat        = $this->M_global->getData('perawat', ['kode_perawat' => $param]);
            $perawat_poli   = $this->M_global->getDataResult('perawat_poli', ['kode_perawat' => $param]);

            $data_perawat   = $this->M_global->getData('perawat', ['kode_perawat' => $param]);
            $perawat_cabang = $this->M_global->getDataResult('cabang_user', ['email' => $data_perawat->email]);
        } else {
            $perawat        = null;
            $perawat_poli   = null;
            $perawat_cabang = null;
        }

        $parameter = [
            $this->data,
            'judul'             => 'Master',
            'nama_apps'         => $web_setting->nama,
            'page'              => 'Perawat',
            'web'               => $web_setting,
            'web_version'       => $web_version->version,
            'list_data'         => '',
            'data_perawat'      => $perawat,
            'perawat_poli'      => $perawat_poli,
            'perawat_cabang'    => $perawat_cabang,
            'role'              => $this->M_global->getResult('m_role'),
            'poli'              => $this->M_global->getResult('m_poli'),
            'cabang'            => $this->M_global->getResult('cabang'),
        ];

        $this->template->load('Template/Content', 'Master/Internal/Form_perawat', $parameter);
    }

    // fungsi perawat proses
    public function perawat_proses($param)
    {
        // variable
        $nama               = $this->input->post('nama');

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kodePerawat    = _kodePerawat($nama);
        } else { // selain itu
            // ambil kode dari inputan
            $kodePerawat    = $this->input->post('kodePerawat');

            $this->M_global->delData('perawat_poli', ['kode_perawat' => $kodePerawat]);
        }

        $nik                = $this->input->post('nik');
        $email              = $this->input->post('email');
        $nohp               = $this->input->post('nohp');
        $npwp               = $this->input->post('npwp');
        $sip                = $this->input->post('sip');
        $tgl_mulai          = $this->input->post('tgl_mulai');
        $tgl_berhenti       = $this->input->post('tgl_berhenti');
        $status             = $this->input->post('statusPerawat');
        $provinsi           = $this->input->post('provinsi');
        $kabupaten          = $this->input->post('kabupaten');
        $kecamatan          = $this->input->post('kecamatan');
        $desa               = $this->input->post('desa');
        $kodepos            = $this->input->post('kodepos');
        $kode_poli          = $this->input->post('kode_poli');
        $password           = $this->input->post('password');
        $jkel               = $this->input->post('jkel');
        $kode_cabang_all    = $this->input->post('kode_cabang_all');
        $kode_cabang        = $this->input->post('kode_cabang');

        // tampung variable kedalam $isi
        $isi = [
            'kode_perawat'  => $kodePerawat,
            'nik'           => $nik,
            'sip'           => $sip,
            'npwp'          => $npwp,
            'nama'          => $nama,
            'email'         => $email,
            'nohp'          => $nohp,
            'tgl_mulai'     => $tgl_mulai,
            'tgl_berhenti'  => $tgl_berhenti,
            'status'        => $status,
            'provinsi'      => $provinsi,
            'kabupaten'     => $kabupaten,
            'kecamatan'     => $kecamatan,
            'desa'          => $desa,
            'kodepos'       => $kodepos,
        ];

        $data_user = [
            'kode_user'     => $kodePerawat,
            'nama'          => $nama,
            'email'         => $email,
            'password'      => md5($password),
            'secondpass'    => $password,
            'jkel'          => $jkel,
            'foto'          => (($jkel == 'P') ? 'pria.png' : 'wanita.png'),
            'kode_role'     => 'R0010',
            'nohp'          => $nohp,
            'actived'       => (($tgl_berhenti >= date('Y-m-d')) ? 1 : 0),
            'joined'        => $tgl_mulai,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek          = [
                $this->M_global->insertData('perawat', $isi),
                $this->M_global->insertData('user', $data_user)
            ];

            $cek_param    = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek_user_dokter = $this->M_global->getData('user', ['kode_user' => $kodePerawat]);

            if ($cek_user_dokter) {
                $run_user = $this->M_global->updateData('user', $data_user, ['kode_user' => $kodePerawat]);
            } else {
                $run_user = $this->M_global->insertData('user', $data_user);
            }

            $cek          = [
                $this->M_global->updateData('perawat', $isi, ['kode_perawat' => $kodePerawat]),
                $run_user,
                $this->M_global->delData('perawat_poli', ['kode_perawat' => $kodePerawat]),
                $this->M_global->delData('cabang_user', ['email' => $email]),
            ];

            $cek_param    = 'mengubah';
        }

        // insert cabang
        if ($kode_cabang_all) {
            $data_cabang = $this->M_global->getResult('cabang');
            foreach ($data_cabang as $dc) {
                $isi_cabang = [
                    'email'         => $email,
                    'kode_cabang'   => $dc->kode_cabang,
                ];

                $this->M_global->insertData('cabang_user', $isi_cabang);
            }
        } else {
            foreach ($kode_cabang as $kp) {
                $_kode_cabang_input = $kp;

                $isi_cabang = [
                    'email'         => $email,
                    'kode_cabang'   => $_kode_cabang_input,
                ];

                $this->M_global->insertData('cabang_user', $isi_cabang);
            }
        }

        // insert poli
        foreach ($kode_poli as $kp) {
            $_kode_poli_input = $kp;

            $isi_poli = [
                'kode_perawat'  => $kodePerawat,
                'kode_poli'     => $_kode_poli_input,
            ];

            $this->M_global->insertData('perawat_poli', $isi_poli);
        }

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Master Perawat', $cek_param, $kodePerawat, $this->M_global->getData('perawat', ['kode_perawat' => $kodePerawat])->nama);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi hapus perawat berdasarkan kode_perawat
    public function delPerawat($kode_perawat)
    {
        // jalankan fungsi hapus perawat berdasarkan kode_perawat
        aktifitas_user('Master Perawat', 'menghapus', $kode_perawat, $this->M_global->getData('perawat', ['kode_perawat' => $kode_perawat])->nama);
        // $cek = [
        //     $this->M_global->delData('perawat', ['kode_perawat' => $kode_perawat]),
        //     $this->M_global->delData('perawat_poli', ['kode_perawat' => $kode_perawat]),
        // ];
        $cek = $this->M_global->updateData(
            'perawat',
            ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')],
            ['kode_perawat' => $kode_perawat]
        );

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
     * Master Tarif Single
     */

    // single page
    public function tin_single()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Tindakan Single',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/tin_single_list',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Master/Tarif/Single', $parameter);
    }

    // fungsi list single
    public function tin_single_list($param1 = '1')
    {
        $this->load->model('M_tarif');

        // kondisi role
        $updated                = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted                = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        $list                   = $this->M_tarif->get_datatables($param1);

        $data                   = [];
        $no                     = $_POST['start'] + 1;

        // Loop through the list to populate the data array
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss           = 'onclick="ubah(' . "'" . $rd->kode_tarif . "' ,'" . $rd->jenis_bayar . "', '" . $rd->kelas . "'" . ')"';
            } else {
                $upd_diss           = 'disabled';
            }

            if ($deleted > 0) {
                $cekIsset       = $this->M_global->jumDataRow('pembayaran_tarif_single', ['kode_tarif' => $rd->kode_tarif]);
                $cekIsset2      = $this->M_global->jumDataRow('pendaftaran', ['kode_jenis_bayar' => $rd->jenis_bayar]);
                if ($cekIsset > 0 && $cekIsset2 > 0) {
                    $del_diss   = 'disabled';
                } else {
                    $del_diss   = 'onclick="hapus(' . "'" . $rd->kode_tarif . "' ,'" . $rd->jenis_bayar . "', '" . $rd->kelas . "'" . ')"';
                }
            } else {
                $del_diss       = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_tarif;
            $row[]  = $rd->nama;
            $row[]  = $this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $rd->jenis_bayar])->keterangan;
            $row[]  = '<div class="text-center">' . $rd->kelas . '</div>';
            $row[]  = 'Rp. <div class="float-right">' . number_format($rd->jasa_rs) . '</div>';
            $row[]  = 'Rp. <div class="float-right">' . number_format($rd->jasa_dokter) . '</div>';
            $row[]  = 'Rp. <div class="float-right">' . number_format($rd->jasa_pelayanan) . '</div>';
            $row[]  = 'Rp. <div class="float-right">' . number_format($rd->jasa_poli) . '</div>';
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
            </div>';
            $data[] = $row;
        }

        // Prepare the output in JSON format
        $output = [
            "draw"              => $_POST['draw'],
            "recordsTotal"      => $this->M_tarif->count_all($param1),
            "recordsFiltered"   => $this->M_tarif->count_filtered($param1),
            "data"              => $data,
        ];

        // Send the output to the view
        echo json_encode($output);
    }

    // form tin_single page
    public function form_tin_single($param, $bayar = '', $kelas = '')
    {
        // website config
        $web_setting        = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version        = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param != '0') {
            $tarif          = $this->M_global->getData('m_tarif', ['kode_tarif' => $param, 'jenis_bayar' => $bayar, 'kelas' => $kelas]);
            $single_jasa    = $this->M_global->getDataResult('tarif_jasa', ['kode_tarif' => $param, 'jenis_bayar' => $bayar, 'kelas' => $kelas]);
            $single_bhp     = $this->M_global->getDataResult('tarif_single_bhp', ['kode_tarif' => $param]);
        } else {
            $tarif          = null;
            $single_jasa    = null;
            $single_bhp     = null;
        }

        $parameter = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Tarif Single',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => '',
            'tarif'         => $tarif,
            'single_jasa'   => $single_jasa,
            'single_bhp'    => $single_bhp,
        ];

        $this->template->load('Template/Content', 'Master/Tarif/Form_single', $parameter);
    }

    public function cekTarifSingle()
    {
        $kode_tarif = $this->input->post('kodeTarif');
        $jenis_bayar = $this->input->post('jenis_bayar');

        $cek_jenis_bayar = $this->M_global->getData('m_tarif', ['kode_tarif' => $kode_tarif, 'jenis_bayar' => $jenis_bayar]);

        if ($cek_jenis_bayar) {
            echo json_encode(['status' => 2]);
        } else {
            echo json_encode(['status' => 1]);
        }
    }

    public function getInfoTarif($kode_tarif)
    {
        $data = $this->M_global->getData('m_tarif', ['kode_tarif' => $kode_tarif]);

        if ($data) {
            echo json_encode($data);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    public function add_kategori_tarif()
    {
        $kode_kategori    = _kodeKategoriTarif();
        $inisial          = $this->input->post('inisial_kategori');
        $keterangan       = $this->input->post('keterangan_kategori');

        $cek              = $this->M_global->insertData('kategori_tarif', ['kode_kategori' => $kode_kategori, 'keterangan' => $keterangan, 'inisial_kode' => $inisial]);

        if ($cek) {
            aktifitas_user('Master Tarif (Kategori)', 'menambahkan Kategori Tarif', $kode_kategori, $keterangan);
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    public function tin_single_proses($param)
    {
        $kategori       = $this->input->post('kategori');
        $jenis_bayar    = $this->input->post('jenis_bayar');
        $kelas          = $this->input->post('kelas');
        $jenis          = 1;

        if ($param == 1) {
            if ($this->input->post('kodeTarif') == '') {
                $kode_tarif = _kodeTarif($jenis, $jenis_bayar, $kelas);
            } else {
                $kode_tarif = $this->input->post('kodeTarif');
            }
        } else {
            $kode_tarif = $this->input->post('kodeTarif');
        }

        $nama           = $this->input->post('nama');

        $kode_cabang    = $this->input->post('kode_cabang');
        $jasa_rs        = $this->input->post('jasa_rs');
        $jasa_dokter    = $this->input->post('jasa_dokter');
        $jasa_pelayanan = $this->input->post('jasa_pelayanan');
        $jasa_poli      = $this->input->post('jasa_poli');

        $kode_barang    = $this->input->post('kode_barang');
        $kode_satuan    = $this->input->post('kode_satuan');
        $harga          = $this->input->post('harga');
        $qty            = $this->input->post('qty');
        $jumlah         = $this->input->post('jumlah');

        $isi = [
            'kode_tarif'    => $kode_tarif,
            'nama'          => $nama,
            'kategori'      => $kategori,
            'jenis'         => $jenis,
            'jenis_bayar'   => $jenis_bayar,
            'kelas'         => $kelas,
        ];

        if (isset($kode_cabang)) {
            $jum = count($kode_cabang);

            if ($param == 1) {
                $cek_param = 'menambahkan';

                $cek = $this->M_global->insertData('m_tarif', $isi);
            } else {
                $cek_param = 'mengubah';

                $cek = [
                    $this->M_global->delData('tarif_single_bhp', ['kode_tarif' => $kode_tarif]),
                    $this->M_global->delData('tarif_jasa', ['kode_tarif' => $kode_tarif, 'jenis_bayar' => $jenis_bayar, 'kelas' => $kelas]),
                    $this->M_global->updateData('m_tarif', $isi, ['kode_tarif' => $kode_tarif, 'jenis_bayar' => $jenis_bayar, 'kelas' => $kelas]),
                ];
            }

            aktifitas_user('Master Tarif Single', $cek_param . ' Tarif Single', $kode_tarif, $nama . ' - Penjamin: ' . $this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $jenis_bayar])->keterangan . ' - Kelas: ' . $kelas, $isi);

            if ($cek) {
                // JASA
                for ($x = 0; $x <= ($jum - 1); $x++) {
                    $_kode_cabang       = $kode_cabang[$x];
                    $_jasa_rs           = str_replace(',', '', $jasa_rs[$x]);
                    $_jasa_dokter       = str_replace(',', '', $jasa_dokter[$x]);
                    $_jasa_pelayanan    = str_replace(',', '', $jasa_pelayanan[$x]);
                    $_jasa_poli         = str_replace(',', '', $jasa_poli[$x]);

                    $detail = [
                        'kode_tarif'        => $kode_tarif,
                        'jenis_bayar'       => $jenis_bayar,
                        'kelas'             => $kelas,
                        'kode_cabang'       => $_kode_cabang,
                        'jasa_rs'           => $_jasa_rs,
                        'jasa_dokter'       => $_jasa_dokter,
                        'jasa_pelayanan'    => $_jasa_pelayanan,
                        'jasa_poli'         => $_jasa_poli,
                    ];

                    $this->M_global->insertData('tarif_jasa', $detail);
                }

                // BHP
                if (isset($kode_barang)) {
                    $jumBhp = count($kode_barang);
                    for ($z = 0; $z <= ($jumBhp - 1); $z++) {
                        $_kode_barang   = $kode_barang[$z];
                        $_kode_satuan   = $kode_satuan[$z];
                        $_qty           = str_replace(',', '', $qty[$z]);
                        $_harga         = str_replace(',', '', $harga[$z]);
                        $_jumlah        = str_replace(',', '', $jumlah[$z]);

                        $barang1        = $this->M_global->getData('barang', ['kode_barang' => $_kode_barang, 'kode_satuan' => $_kode_satuan]);
                        $barang2        = $this->M_global->getData('barang', ['kode_barang' => $_kode_barang, 'kode_satuan2' => $_kode_satuan]);
                        $barang3        = $this->M_global->getData('barang', ['kode_barang' => $_kode_barang, 'kode_satuan3' => $_kode_satuan]);

                        if ($barang1) {
                            $qty_satuan = 1;
                        } else if ($barang2) {
                            $qty_satuan = $barang2->qty_satuan2;
                        } else {
                            $qty_satuan = $barang3->qty_satuan3;
                        }

                        $qty_konversi   = $_qty * $qty_satuan;

                        $detail_bhp = [
                            'kode_tarif'        => $kode_tarif,
                            'kode_barang'       => $_kode_barang,
                            'kode_satuan'       => $_kode_satuan,
                            'qty_konversi'      => $qty_konversi,
                            'qty'               => $_qty,
                            'harga'             => $_harga,
                            'jumlah'            => $_jumlah,
                        ];

                        $this->M_global->insertData('tarif_single_bhp', $detail_bhp);
                    }
                }

                echo json_encode(['status' => 1]);
            } else {
                echo json_encode(['status' => 0]);
            }
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    public function delTarifSingle($kode_tarif, $jenis_bayar, $kelas)
    {
        $m_tarif = $this->M_global->getData('m_tarif', ['kode_tarif' => $kode_tarif]);
        aktifitas_user('Master Tarif Single', 'hapus Tarif Single: ', $kode_tarif, $m_tarif->nama . ' - Penjamin: ' . $m_tarif->jenis_bayar . ' - Kelas: ' . $m_tarif->kelas);

        // $cek = [
        //     $this->M_global->delData('tarif_single_bhp', ['kode_tarif' => $kode_tarif]),
        //     $this->M_global->delData('tarif_jasa', ['kode_tarif' => $kode_tarif]),
        //     $this->M_global->delData('m_tarif', ['kode_tarif' => $kode_tarif]),
        // ];
        $cek = $this->M_global->updateData('tarif_jasa', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_tarif' => $kode_tarif, 'jenis_bayar' => $jenis_bayar, 'kelas' => $kelas, 'kode_cabang' => $this->session->userdata('cabang')]);

        if ($cek) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Master Tarif Paket
     */

    // paket page
    public function tin_paket()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Tindakan Paket',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/tin_paket_list',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Master/Tarif/Paket', $parameter);
    }

    // fungsi list paket
    public function tin_paket_list($param1 = 2)
    {
        $this->load->model('M_tarif');
        $kode_cabang                = $this->session->userdata('cabang');

        // kondisi role
        $updated                    = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted                    = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        $list                       = $this->M_tarif->get_datatables($param1);

        $data                       = [];
        $no                         = $_POST['start'] + 1;

        // Loop through the list to populate the data array
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss               = 'onclick="ubah(' . "'" . $rd->kode_tarif . "'" . ')"';
            } else {
                $upd_diss               = 'disabled';
            }

            if ($deleted > 0) {
                $cekIsset           = $this->M_global->jumDataRow('tarif_paket_pasien', ['kode_tarif' => $rd->kode_tarif]);
                if ($cekIsset > 0) {
                    $del_diss       = 'disabled';
                } else {
                    $del_diss       = 'onclick="hapus(' . "'" . $rd->kode_tarif . "'" . ')"';
                }
            } else {
                $del_diss           = 'disabled';
            }

            $kunjungan              = count($this->M_global->getDataResult('tarif_paket', ['kode_tarif' => $rd->kode_tarif, 'kode_cabang' => $kode_cabang, 'jenis_bayar' => $rd->jenis_bayar]));

            $jasa_rs                = [];
            $jasa_dokter            = [];
            $jasa_pelayanan         = [];
            $jasa_poli              = [];
            $kunj                   = [];

            for ($x = 1; $x <= $kunjungan; $x++) {
                $jasa               = $this->M_global->getData('tarif_paket', ['kode_tarif' => $rd->kode_tarif, 'kunjungan' => $x, 'jenis_bayar' => $rd->jenis_bayar]);

                $kunj[$x]           = $x;
                $jasa_rs[$x]        = number_format($jasa->jasa_rs);
                $jasa_dokter[$x]    = number_format($jasa->jasa_dokter);
                $jasa_pelayanan[$x] = number_format($jasa->jasa_pelayanan);
                $jasa_poli[$x]      = number_format($jasa->jasa_poli);
            }

            $jasa_rs_str            = implode('<br>', array_map(fn($k, $v) => "<div style='float: left;'>Paket $k: Rp.</div><div class='float-right'>$v</div>", array_keys($jasa_rs), $jasa_rs));
            $jasa_dokter_str        = implode('<br>', array_map(fn($k, $v) => "<div style='float: left;'>Paket $k: Rp.</div><div class='float-right'>$v</div>", array_keys($jasa_dokter), $jasa_dokter));
            $jasa_pelayanan_str     = implode('<br>', array_map(fn($k, $v) => "<div style='float: left;'>Paket $k: Rp.</div><div class='float-right'>$v</div>", array_keys($jasa_pelayanan), $jasa_pelayanan));
            $jasa_poli_str          = implode('<br>', array_map(fn($k, $v) => "<div style='float: left;'>Paket $k: Rp.</div><div class='float-right'>$v</div>", array_keys($jasa_poli), $jasa_poli));

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_tarif . '<br><span class="badge badge-primary">Kunjungan: ' . $kunjungan . '</span>';
            $row[]  = $rd->nama;
            $row[]  = $this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $rd->jenis_bayar])->keterangan;
            $row[]  = '<div>' . $jasa_rs_str . '</div>';
            $row[]  = '<div>' . $jasa_dokter_str . '</div>';
            $row[]  = '<div>' . $jasa_pelayanan_str . '</div>';
            $row[]  = '<div>' . $jasa_poli_str . '</div>';
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
            </div>';
            $data[] = $row;
        }

        // Prepare the output in JSON format
        $output = [
            "draw"              => $_POST['draw'],
            "recordsTotal"      => $this->M_tarif->count_all($param1),
            "recordsFiltered"   => $this->M_tarif->count_filtered($param1),
            "data"              => $data,
        ];

        // Send the output to the view
        echo json_encode($output);
    }

    // form tin_paket page
    public function form_tin_paket($param)
    {
        // website config
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version    = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param != '0') {
            $tarif      = $this->M_global->getData('m_tarif', ['kode_tarif' => $param]);
            $paket_jasa = $this->M_global->getDataResult('tarif_paket', ['kode_tarif' => $param]);
            $single_bhp = $this->M_global->getDataResult('tarif_paket_bhp', ['kode_tarif' => $param]);
        } else {
            $tarif      = null;
            $paket_jasa = null;
            $single_bhp = null;
        }

        $parameter = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Tarif Paket',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => '',
            'tarif'         => $tarif,
            'paket_jasa'    => $paket_jasa,
            'single_bhp'    => $single_bhp,
        ];

        $this->template->load('Template/Content', 'Master/Tarif/Form_paket', $parameter);
    }

    public function tin_paket_proses($param)
    {
        $kategori       = $this->input->post('kategori');
        $jenis_bayar    = $this->input->post('jenis_bayar');

        if ($param == 1) {
            if ($this->input->post('kodeTarif') == '') {
                $kode_tarif = _kodeTarif(2, $jenis_bayar);
            } else {
                $kode_tarif = $this->input->post('kodeTarif');
            }
        } else {
            $kode_tarif = $this->input->post('kodeTarif');
        }

        $nama           = $this->input->post('nama');
        $jenis          = 2;

        $kode_cabang    = $this->input->post('kode_cabang');
        $kunjungan      = $this->input->post('kunjungan');
        $jasa_rs        = $this->input->post('jasa_rs');
        $jasa_dokter    = $this->input->post('jasa_dokter');
        $jasa_pelayanan = $this->input->post('jasa_pelayanan');
        $jasa_poli      = $this->input->post('jasa_poli');

        $kode_barang    = $this->input->post('kode_barang');
        $kode_satuan    = $this->input->post('kode_satuan');
        $harga          = $this->input->post('harga');
        $qty            = $this->input->post('qty');
        $jumlah         = $this->input->post('jumlah');

        $isi = [
            'kode_tarif'    => $kode_tarif,
            'nama'          => $nama,
            'kategori'      => $kategori,
            'jenis'         => $jenis,
            'jenis_bayar'   => $jenis_bayar,
        ];

        if (isset($kode_cabang)) {
            $jum = count($kode_cabang);

            if ($param == 1) {
                $cek_param = 'menambahkan';
                $cek = $this->M_global->insertData('m_tarif', $isi, ['kode_tarif' => $kode_tarif]);
            } else {
                $cek_param = 'mengubah';
                $cek = [
                    $this->M_global->delData('tarif_paket_bhp', ['kode_tarif' => $kode_tarif, 'jenis_bayar' => $jenis_bayar]),
                    $this->M_global->delData('tarif_paket', ['kode_tarif' => $kode_tarif, 'jenis_bayar' => $jenis_bayar]),
                    $this->M_global->updateData('m_tarif', $isi, ['kode_tarif' => $kode_tarif, 'jenis_bayar' => $jenis_bayar]),
                ];
            }

            aktifitas_user('Master Tarif Paket', $cek_param . ' Tarif Paket', $kode_tarif, $nama, $isi);

            if ($cek) {
                // JASA
                for ($x = 0; $x <= ($jum - 1); $x++) {
                    $_kode_cabang       = $kode_cabang[$x];
                    $_kunjungan         = str_replace(',', '', $kunjungan[$x]);
                    $_jasa_rs           = str_replace(',', '', $jasa_rs[$x]);
                    $_jasa_dokter       = str_replace(',', '', $jasa_dokter[$x]);
                    $_jasa_pelayanan    = str_replace(',', '', $jasa_pelayanan[$x]);
                    $_jasa_poli         = str_replace(',', '', $jasa_poli[$x]);

                    $detail = [
                        'kode_tarif'        => $kode_tarif,
                        'jenis_bayar'       => $jenis_bayar,
                        'kode_cabang'       => $_kode_cabang,
                        'kunjungan'         => $_kunjungan,
                        'jasa_rs'           => $_jasa_rs,
                        'jasa_dokter'       => $_jasa_dokter,
                        'jasa_pelayanan'    => $_jasa_pelayanan,
                        'jasa_poli'         => $_jasa_poli,
                    ];

                    $this->M_global->insertData('tarif_paket', $detail);
                }

                // BHP
                if (isset($kode_barang)) {
                    $jumBhp             = count($kode_barang);

                    for ($z = 0; $z <= ($jumBhp - 1); $z++) {
                        $_kode_barang   = $kode_barang[$z];
                        $_kode_satuan   = $kode_satuan[$z];
                        $_qty           = str_replace(',', '', $qty[$z]);
                        $_harga         = str_replace(',', '', $harga[$z]);
                        $_jumlah        = str_replace(',', '', $jumlah[$z]);

                        $barang1        = $this->M_global->getData('barang', ['kode_barang' => $_kode_barang, 'kode_satuan' => $_kode_satuan]);
                        $barang2        = $this->M_global->getData('barang', ['kode_barang' => $_kode_barang, 'kode_satuan2' => $_kode_satuan]);
                        $barang3        = $this->M_global->getData('barang', ['kode_barang' => $_kode_barang, 'kode_satuan3' => $_kode_satuan]);

                        if ($barang1) {
                            $qty_satuan = 1;
                        } else if ($barang2) {
                            $qty_satuan = $barang2->qty_satuan2;
                        } else {
                            $qty_satuan = $barang3->qty_satuan3;
                        }

                        $qty_konversi   = $_qty * $qty_satuan;

                        $detail_bhp     = [
                            'kode_tarif'        => $kode_tarif,
                            'kode_barang'       => $_kode_barang,
                            'kode_satuan'       => $_kode_satuan,
                            'qty_konversi'      => $qty_konversi,
                            'qty'               => $_qty,
                            'harga'             => $_harga,
                            'jumlah'            => $_jumlah,
                        ];

                        $this->M_global->insertData('tarif_paket_bhp', $detail_bhp);
                    }
                }


                echo json_encode(['status' => 1]);
            } else {
                echo json_encode(['status' => 0]);
            }
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    public function delTarifPaket($kode_tarif)
    {
        aktifitas_user('Master Tarif Paket', 'hapus Tarif Paket', $kode_tarif, $this->M_global->getData('m_tarif', ['kode_tarif' => $kode_tarif])->nama);

        // $cek = [
        //     $this->M_global->delData('tarif_paket_bhp', ['kode_tarif' => $kode_tarif]),
        //     $this->M_global->delData('tarif_paket', ['kode_tarif' => $kode_tarif]),
        //     $this->M_global->delData('m_tarif', ['kode_tarif' => $kode_tarif]),
        // ];
        $cek = $this->M_global->updateData('tarif_paket', ['hapus' => 1, 'tgl_hapus' => date('Y-m-d'), 'jam_hapus' => date('H:i:s')], ['kode_tarif' => $kode_tarif, 'kode_cabang' => $this->session->userdata('cabang')]);

        if ($cek) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Master Tindakan Single
     */

    // single page
    public function tindakan_single()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Tindakan Single',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/tindakan_single_list/1',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Master/Tindakan/Single', $parameter);
    }

    // fungsi list single
    public function tindakan_single_list($param1 = '1', $param2 = '')
    {
        $this->load->model('M_tindakan');

        // kondisi role
        $updated            = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted            = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        $list               = $this->M_tindakan->get_datatables($param1, $param2);

        $data               = [];
        $no                 = $_POST['start'] + 1;

        // Loop through the list to populate the data array
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss       = 'onclick="ubah(' . "'" . $rd->kode_tindakan . "'" . ')"';
            } else {
                $upd_diss       = 'disabled';
            }

            if ($deleted > 0) {
                $cek = $this->db->query(
                    'SELECT * FROM (
                        SELECT kode_tarif FROM emr_tarif

                        UNION ALL

                        SELECT kode_tarif FROM emr_lab

                        UNION ALL

                        SELECT kode_tarif FROM emr_rad
                    ) AS semua WHERE kode_tarif = "' . $rd->kode_tindakan . '"'
                )->result();

                if (count($cek) > 0) {
                    $del_diss   = 'disabled';
                } else {
                    $del_diss   = 'onclick="hapus(' . "'" . $rd->kode_tindakan . "'" . ')"';
                }
            } else {
                $del_diss   = 'disabled';
            }

            $row            = [];
            $row[]          = $no++;
            $row[]          = $rd->kode_tindakan;
            $row[]          = $rd->keterangan;
            $row[]          = $rd->kategori;
            $row[]          = '<div class="text-center">
                <button type="button" class="btn btn-warning" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
            </div>';
            $data[]         = $row;
        }

        // Prepare the output in JSON format
        $output = [
            "draw"              => $_POST['draw'],
            "recordsTotal"      => $this->M_tindakan->count_all($param1, $param2),
            "recordsFiltered"   => $this->M_tindakan->count_filtered($param1, $param2),
            "data"              => $data,
        ];

        // Send the output to the view
        echo json_encode($output);
    }

    // form tindakan_single page
    public function form_tindakan_single($param)
    {
        // website config
        $web_setting        = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version        = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param != '0') {
            $tindakan       = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $param]);
            $tindakan_bhp   = $this->M_global->getDataResult('tindakan_bhp', ['kode_tindakan' => $param]);
        } else {
            $tindakan       = null;
            $tindakan_bhp   = null;
        }

        $parameter = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Tarif Single',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => '',
            'tindakan'      => $tindakan,
            'tindakan_bhp'  => $tindakan_bhp,
        ];

        $this->template->load('Template/Content', 'Master/Tindakan/Form_single', $parameter);
    }

    // tindakan proses
    public function tindakan_single_proses($param)
    {
        $kategori       = $this->input->post('kategori');
        $jenis          = 1;

        if ($param == 1) {
            if ($this->input->post('kodeTindakan') == '') {
                $kode_tindakan = _kodeTindakan($jenis, $kategori);
            } else {
                $kode_tindakan = $this->input->post('kodeTindakan');
            }
        } else {
            $kode_tindakan = $this->input->post('kodeTindakan');
        }

        $keterangan     = $this->input->post('keterangan');

        $kode_barang    = $this->input->post('kode_barang');
        $kode_satuan    = $this->input->post('kode_satuan');
        $harga          = $this->input->post('harga');
        $qty            = $this->input->post('qty');
        $jumlah         = $this->input->post('jumlah');

        $isi = [
            'kode_tindakan' => $kode_tindakan,
            'keterangan'    => $keterangan,
            'kode_kategori' => $kategori,
            'jenis'         => $jenis,
        ];

        if ($param == 1) {
            $cek_param = 'menambahkan';

            $cek = $this->M_global->insertData('m_tindakan', $isi);
        } else {
            $cek_param = 'mengubah';

            $cek = [
                $this->M_global->delData('tindakan_bhp', ['kode_tindakan' => $kode_tindakan]),
                $this->M_global->updateData('m_tindakan', $isi, ['kode_tindakan' => $kode_tindakan]),
            ];
        }

        aktifitas_user('Master Tindakan Single', $cek_param, $kode_tindakan, $keterangan, json_encode($isi), json_encode(['']));

        if ($cek) {
            // BHP
            if (isset($kode_barang)) {
                $jumBhp = count($kode_barang);
                for ($z = 0; $z <= ($jumBhp - 1); $z++) {
                    $_kode_barang   = $kode_barang[$z];
                    $_kode_satuan   = $kode_satuan[$z];
                    $_qty           = str_replace(',', '', $qty[$z]);
                    $_harga         = str_replace(',', '', $harga[$z]);
                    $_jumlah        = str_replace(',', '', $jumlah[$z]);

                    $barang1        = $this->M_global->getData('barang', ['kode_barang' => $_kode_barang, 'kode_satuan' => $_kode_satuan]);
                    $barang2        = $this->M_global->getData('barang', ['kode_barang' => $_kode_barang, 'kode_satuan2' => $_kode_satuan]);
                    $barang3        = $this->M_global->getData('barang', ['kode_barang' => $_kode_barang, 'kode_satuan3' => $_kode_satuan]);

                    if ($barang1) {
                        $qty_satuan = 1;
                    } else if ($barang2) {
                        $qty_satuan = $barang2->qty_satuan2;
                    } else {
                        $qty_satuan = $barang3->qty_satuan3;
                    }

                    $qty_konversi   = $_qty * $qty_satuan;

                    $detail_bhp = [
                        'kode_tindakan'     => $kode_tindakan,
                        'kode_barang'       => $_kode_barang,
                        'kode_satuan'       => $_kode_satuan,
                        'qty_konversi'      => $qty_konversi,
                        'qty'               => $_qty,
                        'harga'             => $_harga,
                        'jumlah'            => $_jumlah,
                    ];

                    $this->M_global->insertData('tindakan_bhp', $detail_bhp);
                }
            }
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    public function delTindakanSingle($kode_tindakan)
    {
        $m_tindakan = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $kode_tindakan]);
        aktifitas_user('Master Tindakan Single', 'hapus Tindakan Single: ', $kode_tindakan, $m_tindakan->keterangan);

        $cek = [
            $this->M_global->delData('tindakan_bhp', ['kode_tindakan' => $kode_tindakan]),
            $this->M_global->delData('m_tindakan', ['kode_tindakan' => $kode_tindakan]),
        ];

        if ($cek) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Master Tindakan Paket
     */

    // paket page
    public function tindakan_paket()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Tindakan Paket',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/tindakan_paket_list/2',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Master/Tindakan/Paket', $parameter);
    }

    // fungsi list paket
    public function tindakan_paket_list($param1 = '2', $param2 = '')
    {
        $this->load->model('M_tindakan');

        // kondisi role
        $updated            = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted            = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        $list               = $this->M_tindakan->get_datatables($param1, $param2);

        $data               = [];
        $no                 = $_POST['start'] + 1;

        // Loop through the list to populate the data array
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss       = 'onclick="ubah(' . "'" . $rd->kode_tindakan . "'" . ')"';
            } else {
                $upd_diss       = 'disabled';
            }

            if ($deleted > 0) {
                $cek = $this->M_global->jumDataResult('tarif_paket_pasien', ['kode_tindakan' => $rd->kode_tindakan]);

                if ($cek > 0) {
                    $del_diss   = 'disabled';
                } else {
                    $del_diss   = 'onclick="hapus(' . "'" . $rd->kode_tindakan . "'" . ')"';
                }
            } else {
                $del_diss   = 'disabled';
            }

            $detail = [];

            $detin = $this->M_global->getDataResult('detail_tindakan', ['kode_header' => $rd->kode_tindakan]);
            $detail_keterangan = [];
            $nodt = 1;
            foreach ($detin as $dt) {
                $detail_keterangan[] = '<i class="fa-solid fa-star-of-life"></i>&nbsp&nbsp' . $dt->keterangan;
                $nodt++;
            }
            $detail = implode('<br>', $detail_keterangan);

            $row            = [];
            $row[]          = $no++;
            $row[]          = $rd->kode_tindakan;
            $row[]          = $rd->keterangan;
            $row[]          = $rd->kategori;
            $row[]          = $detail;
            $row[]          = '<div class="text-center">
                <button type="button" class="btn btn-warning" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
            </div>';
            $data[]         = $row;
        }

        // Prepare the output in JSON format
        $output = [
            "draw"              => $_POST['draw'],
            "recordsTotal"      => $this->M_tindakan->count_all($param1, $param2),
            "recordsFiltered"   => $this->M_tindakan->count_filtered($param1, $param2),
            "data"              => $data,
        ];

        // Send the output to the view
        echo json_encode($output);
    }

    // form tindakan_paket page
    public function form_tindakan_paket($param)
    {
        // website config
        $web_setting            = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version            = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param != '0') {
            $tindakan           = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $param]);
            $detail_tindakan    = $this->M_global->getDataResult('detail_tindakan', ['kode_header' => $param]);
        } else {
            $tindakan           = null;
            $detail_tindakan    = null;
        }

        $parameter = [
            $this->data,
            'judul'            => 'Master',
            'nama_apps'        => $web_setting->nama,
            'page'             => 'Tarif Single',
            'web'              => $web_setting,
            'web_version'      => $web_version->version,
            'list_data'        => '',
            'tindakan'         => $tindakan,
            'detail_tindakan'  => $detail_tindakan,
        ];

        $this->template->load('Template/Content', 'Master/Tindakan/Form_paket', $parameter);
    }

    // tindakan proses
    public function tindakan_paket_proses($param)
    {
        $kategori                 = $this->input->post('kategori');
        $jenis                    = 2;

        if ($param == 1) {
            if ($this->input->post('kodeTindakan') == '') {
                $kode_tindakan    = _kodeTindakan($jenis, $kategori);
            } else {
                $kode_tindakan    = $this->input->post('kodeTindakan');
            }
        } else {
            $kode_tindakan        = $this->input->post('kodeTindakan');
        }

        $keterangan               = $this->input->post('keterangan');

        $kode_detail_tindakan     = $this->input->post('kode_detail_tindakan');

        $isi = [
            'kode_tindakan' => $kode_tindakan,
            'keterangan'    => $keterangan,
            'kode_kategori' => $kategori,
            'jenis'         => $jenis,
        ];

        $isi_sebelum = json_encode($this->M_global->getData('m_tindakan', ['kode_tindakan' => $kode_tindakan]));

        if ($param == 1) {
            $cek_param = 'menambahkan';

            $cek = $this->M_global->insertData('m_tindakan', $isi);
        } else {
            $cek_param = 'mengubah';

            $cek = [
                $this->M_global->delData('detail_tindakan', ['kode_header' => $kode_tindakan]),
                $this->M_global->updateData('m_tindakan', $isi, ['kode_tindakan' => $kode_tindakan]),
            ];
        }


        aktifitas_user('Master Tindakan Paket', $cek_param, $kode_tindakan, $keterangan, json_encode($isi), $isi_sebelum);

        if ($cek) {
            // BHP
            if (isset($kode_detail_tindakan)) {
                $jumBhp = count($kode_detail_tindakan);
                for ($z = 0; $z <= ($jumBhp - 1); $z++) {
                    $_kode_detail_tindakan   = $kode_detail_tindakan[$z];

                    $detail_bhp = [
                        'kode_header'    => $kode_tindakan,
                        'kode_tindakan'  => $_kode_detail_tindakan,
                        'keterangan'     => $this->M_global->getData('m_tindakan', ['kode_tindakan' => $_kode_detail_tindakan])->keterangan,
                        'kode_kategori'  => $this->M_global->getData('m_tindakan', ['kode_tindakan' => $_kode_detail_tindakan])->kode_kategori,
                        'jenis'          => 1,
                    ];

                    $this->M_global->insertData('detail_tindakan', $detail_bhp);
                }
            }
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    public function delTindakanPaket($kode_tindakan)
    {
        $m_tindakan = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $kode_tindakan]);
        aktifitas_user('Master Tindakan Paket', 'hapus Tindakan Paket: ', $kode_tindakan, $m_tindakan->keterangan);

        $cek = [
            $this->M_global->delData('detail_tindakan', ['kode_header' => $kode_tindakan]),
            $this->M_global->delData('m_tindakan', ['kode_tindakan' => $kode_tindakan]),
        ];

        if ($cek) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Master Tindakan Harga
     */

    // multiprice page
    public function multiprice()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Harga Tindakan',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/price_list',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Master/Tindakan/Harga', $parameter);
    }

    // cek multiprice
    public function cekMultiprice()
    {
        $kode_tindakan    = $this->input->post('kode_tindakan');
        $kode_poli        = $this->input->post('kode_poli');
        $penjamin         = $this->input->post('penjamin');
        $kelas            = $this->input->post('kelas');

        $cek              = $this->M_global->jumDataRow('multiprice_tindakan', ['kode_tindakan' => $kode_tindakan, 'kode_poli' => $kode_poli, 'kode_penjamin' => $penjamin, 'kelas' => $kelas]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 0]);
        } else { // selain itu
            echo json_encode(['status' => 1]);
            // kirimkan status 0
        }
    }

    // multiprice proses
    public function prosesMultiprice($param)
    {
        $kode_tindakan    = $this->input->post('kode_tindakan');
        $penjamin         = $this->input->post('penjamin');
        $kode_poli        = $this->input->post('kode_poli');
        $kelas            = $this->input->post('kelas');

        $klinik           = str_replace(',', '', $this->input->post('klinik'));
        $dokter           = str_replace(',', '', $this->input->post('dokter'));
        $pelayanan        = str_replace(',', '', $this->input->post('pelayanan'));
        $poli             = str_replace(',', '', $this->input->post('poli'));

        if ($param == 1) {
            $pesan            = 'menambahkan';
        } else {
            $pesan            = 'mengubah';
            $kode_multiprice  = $this->input->post('kode_multiprice');
            $this->M_global->delData('multiprice_tindakan', ['kode_multiprice' => $kode_multiprice]);
        }

        $kode_multiprice  = _kodeMultiprice($penjamin, $kelas, $kode_poli);

        $isi = [
            'kode_multiprice' => $kode_multiprice,
            'kode_tindakan'   => $kode_tindakan,
            'kode_penjamin'   => $penjamin,
            'kode_poli'       => $kode_poli,
            'kelas'           => $kelas,
            'klinik'          => $klinik,
            'dokter'          => $dokter,
            'pelayanan'       => $pelayanan,
            'poli'            => $poli,
        ];

        $cek = $this->M_global->insertData('multiprice_tindakan', $isi);

        if ($cek) {
            aktifitas_user('Master Multiprice', 'tambah data', $kode_multiprice, $kode_tindakan, json_encode($isi), json_encode(['']));

            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi list price
    public function price_list($param1 = '', $param2 = '', $param3 = '')
    {
        $this->load->model('M_multiprice');

        // kondisi role
        $updated                = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted                = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        $list                   = $this->M_multiprice->get_datatables($param1, $param2, $param3);

        $data                   = [];
        $no                     = $_POST['start'] + 1;

        // Loop through the list to populate the data array
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss           = 'onclick="ubah(' . "'" . $rd->kode_multiprice . "'" . ')"';
            } else {
                $upd_diss           = 'disabled';
            }

            if ($deleted > 0) {
                $cek = $this->db->query(
                    'SELECT * FROM (
                        SELECT kode_multiprice FROM emr_tarif

                        UNION ALL

                        SELECT kode_multiprice FROM emr_lab

                        UNION ALL

                        SELECT kode_multiprice FROM emr_rad
                    ) AS semua WHERE kode_multiprice = "' . $rd->kode_multiprice . '"'
                )->result();

                if (count($cek) > 0) {
                    $del_diss       = 'disabled';
                } else {
                    $del_diss       = 'onclick="hapus(' . "'" . $rd->kode_multiprice . "'" . ')"';
                }
            } else {
                $del_diss       = 'disabled';
            }

            $row    = [];
            $row[]  = '<div class="text-right">' . $no++ . '</div>';
            $row[]  = $rd->kode_multiprice;
            $row[]  = $rd->tindakan;
            $row[]  = $rd->polix;
            $row[]  = $rd->keterangan;
            $row[]  = '<div class="text-center">' . $rd->kelas . '</div>';
            $row[]  = 'Rp. <div class="float-right">' . number_format($rd->klinik) . '</div>';
            $row[]  = 'Rp. <div class="float-right">' . number_format($rd->dokter) . '</div>';
            $row[]  = 'Rp. <div class="float-right">' . number_format($rd->pelayanan) . '</div>';
            $row[]  = 'Rp. <div class="float-right">' . number_format($rd->poli) . '</div>';
            $row[]  = 'Rp. <div class="float-right">' . number_format($rd->klinik + $rd->dokter + $rd->pelayanan + $rd->poli) . '</div>';
            $row[]  = '<div class="text-center">
                <button type="button" style="margin-bottom: 5px;" class="btn btn-warning" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" style="margin-bottom: 5px;" class="btn btn-danger" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
            </div>';
            $data[] = $row;
        }

        // Prepare the output in JSON format
        $output = [
            "draw"              => $_POST['draw'],
            "recordsTotal"      => $this->M_multiprice->count_all($param1, $param2, $param3),
            "recordsFiltered"   => $this->M_multiprice->count_filtered($param1, $param2, $param3),
            "data"              => $data,
        ];

        // Send the output to the view
        echo json_encode($output);
    }

    public function getMultiprice($kode_multiprice)
    {
        $data = $this->db->query("SELECT m.*, jb.keterangan AS penjamin, t.keterangan AS tindakan, p.keterangan AS poli FROM multiprice_tindakan m JOIN m_jenis_bayar jb ON jb.kode_jenis_bayar = m.kode_penjamin JOIN m_tindakan t ON t.kode_tindakan = m.kode_tindakan JOIN m_poli p ON p.kode_poli = m.kode_poli WHERE m.kode_multiprice = '$kode_multiprice'")->row();

        if ($data) {
            echo json_encode($data);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    public function delMultiprice($kode_multiprice)
    {
        $cekx = $this->M_global->getData('multiprice_tindakan', ['kode_multiprice' => $kode_multiprice]);

        aktifitas_user('Master Multiprice', 'menghapus', $kode_multiprice, $this->M_global->getData('m_tindakan', ['kode_tindakan' => $cekx->kode_tindakan])->keterangan, json_encode(['']), json_encode($cekx));

        $cek = $this->M_global->delData('multiprice_tindakan', ['kode_multiprice' => $kode_multiprice]);

        if ($cek) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Master Tindakan Paket Kunjungan
     */

    // paket kunjungan page
    public function paket_kunjungan()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Tindakan Paket Kunjungan',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/paket_kunjungan_list',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Master/Tindakan/Paket_kunjungan', $parameter);
    }

    // fungsi list paket_kunjungan_list
    public function paket_kunjungan_list($param1 = '', $param2 = '', $param3 = '')
    {
        $this->load->model('M_multiprice');

        // kondisi role
        $created                = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->created;

        $list                   = $this->M_multiprice->get_datatables($param1, $param2, $param3);

        $data                   = [];
        $no                     = $_POST['start'] + 1;

        // Loop through the list to populate the data array
        foreach ($list as $rd) {
            if ($created > 0) {
                $crt_diss       = '';
            } else {
                $crt_diss       = 'disabled';
            }

            $paket  = $this->M_global->getDataResult('paket_kunjungan', ['kode_multiprice' => $rd->kode_multiprice]);

            $kunjungan = [];
            foreach ($paket as $p) {
                $kunjungan[] = $p->kunjungan . '<span class="float-right">Rp. ' . number_format($p->klinik + $p->dokter + $p->pelayanan + $p->poli) . '</span>';
            }

            $detail = implode('<hr>', $kunjungan);

            $row    = [];
            $row[]  = '<div class="text-right">' . $no++ . '</div>';
            $row[]  = $rd->kode_multiprice;
            $row[]  = $rd->tindakan;
            $row[]  = $rd->polix;
            $row[]  = $rd->keterangan;
            $row[]  = '<div class="text-center">' . $rd->kelas . '</div>';
            // $row[]  = '<div class="text-center">' . ((count($paket) > 0) ? number_format(count($paket)) : 1) . '</div>';
            $row[]  = $detail;
            if (count($paket) > 0) {
                $btn_aksi = '<button type="button" style="margin-bottom: 5px;" class="btn btn-primary" onclick="assign(' . "'" . $rd->kode_multiprice . "'" . ')" ' . $crt_diss . '><i class="fa-solid fa-check-circle"></i>&nbsp&nbspAda Paket</button>';
            } else {
                $btn_aksi = '<button type="button" style="margin-bottom: 5px;" class="btn btn-success" onclick="assign(' . "'" . $rd->kode_multiprice . "'" . ')" ' . $crt_diss . '><i class="fa-solid fa-ban"></i>&nbsp&nbspNon-Paket</button>';
            }
            $row[]  = '<div class="text-center">
                ' . $btn_aksi . '
            </div>';
            $data[] = $row;
        }

        // Prepare the output in JSON format
        $output = [
            "draw"              => $_POST['draw'],
            "recordsTotal"      => $this->M_multiprice->count_all($param1, $param2, $param3),
            "recordsFiltered"   => $this->M_multiprice->count_filtered($param1, $param2, $param3),
            "data"              => $data,
        ];

        // Send the output to the view
        echo json_encode($output);
    }

    // form tindakan_paket_kunjungan page
    public function form_tindakan_paket_kunjungan($param)
    {
        // website config
        $web_setting            = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version            = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $multiprice             = $this->db->query("SELECT * FROM multiprice_tindakan WHERE kode_multiprice = '$param'")->row();

        if ($param != '0') {
            $tindakan           = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $multiprice->kode_tindakan]);
            $paket_kunjungan    = $this->M_global->getDataResult('paket_kunjungan', ['kode_tindakan' => $multiprice->kode_tindakan]);
        } else {
            $tindakan           = null;
            $paket_kunjungan    = null;
        }

        $parameter = [
            $this->data,
            'judul'            => 'Master',
            'nama_apps'        => $web_setting->nama,
            'page'             => 'Tarif Paket Kunjungan',
            'web'              => $web_setting,
            'web_version'      => $web_version->version,
            'list_data'        => '',
            'tindakan'         => $tindakan,
            'param'            => $multiprice->kode_tindakan,
            'param1'           => $param,
            'multiprice'       => $multiprice,
            'm_penjamin'       => $this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $multiprice->kode_penjamin])->keterangan,
            'm_kelas'          => $this->M_global->getData('m_kelas', ['kode_kelas' => $multiprice->kelas])->keterangan,
            'm_poli'           => $this->M_global->getData('m_poli', ['kode_poli' => $multiprice->kode_poli])->keterangan,
            'paket_kunjungan'  => $paket_kunjungan,
            'list_data'        => 'Master/paket_list/' . $param . '/0',
        ];

        $this->template->load('Template/Content', 'Master/Tindakan/Form_paket_kunjungan', $parameter);
    }

    // fungsi list paket_list
    public function paket_list($param, $param1 = '')
    {
        $this->load->model('M_paket');

        $list               = $this->M_paket->get_datatables($param);

        $data               = [];
        $no                 = $_POST['start'] + 1;

        // Loop through the list to populate the data array
        foreach ($list as $rd) {
            if ($no == 1) {
                $deleted = '<button type="button" class="btn btn-danger" onclick="hapusPaket(' . "'" . $rd->kode_paket . "'" . ')"><i class="fa-solid fa-times"></i></button>';
            } else {
                $deleted = '<button type="button" class="btn btn-danger" disabled><i class="fa-solid fa-times"></i></button>';
            }

            $row    = [];
            $row[]  = '<div class="text-right">' . $no . '</div>';
            $row[]  = $rd->kode_paket;
            $row[]  = 'Rp. <div class="float-right">' . number_format($rd->klinik) . '</div>';
            $row[]  = 'Rp. <div class="float-right">' . number_format($rd->dokter) . '</div>';
            $row[]  = 'Rp. <div class="float-right">' . number_format($rd->pelayanan) . '</div>';
            $row[]  = 'Rp. <div class="float-right">' . number_format($rd->poli) . '</div>';
            $row[]  = 'Rp. <div class="float-right">' . number_format($rd->klinik + $rd->dokter + $rd->pelayanan + $rd->poli) . '</div>';
            $row[]  = '<div class="text-center">' . $rd->kunjungan . '</div>';
            $row[]  = '<div class="text-center">' . $deleted . '</div>';
            $data[] = $row;

            $no++;
        }

        // Prepare the output in JSON format
        $output = [
            "draw"              => $_POST['draw'],
            "recordsTotal"      => $this->M_paket->count_all($param),
            "recordsFiltered"   => $this->M_paket->count_filtered($param),
            "data"              => $data,
        ];

        // Send the output to the view
        echo json_encode($output);
    }

    // cek cekPaketKunjungan
    public function cekPaketKunjungan()
    {
        $kode_paket    = $this->input->post('kode_paket');

        $cek              = $this->M_global->jumDataRow('paket_kunjungan', ['kode_paket' => $kode_paket]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 0]);
        } else { // selain itu
            echo json_encode(['status' => 1]);
            // kirimkan status 0
        }
    }

    // multiprice proses
    public function prosesPaketKunjungan($param)
    {
        $kode_multiprice  = $this->input->post('kode_multiprice');
        $kode_tindakan    = $this->input->post('kode_tindakan');
        $kunjungan        = $this->input->post('kunjungan');

        $klinik           = str_replace(',', '', $this->input->post('klinik'));
        $dokter           = str_replace(',', '', $this->input->post('dokter'));
        $pelayanan        = str_replace(',', '', $this->input->post('pelayanan'));
        $poli             = str_replace(',', '', $this->input->post('poli'));

        if ($param == 1) {
            $pesan            = 'menambahkan';
        } else {
            $pesan            = 'mengubah';
            $kode_paket       = $this->input->post('kode_paket');
            $this->M_global->delData('paket_kunjungan', ['kode_paket' => $kode_paket]);
        }

        $kode_paket  = _kodePaketKunjungan();

        $isi = [
            'kode_paket'      => $kode_paket,
            'kode_multiprice' => $kode_multiprice,
            'kode_tindakan'   => $kode_tindakan,
            'kunjungan'       => $kunjungan,
            'klinik'          => $klinik,
            'dokter'          => $dokter,
            'pelayanan'       => $pelayanan,
            'poli'            => $poli,
        ];

        $cek = $this->M_global->insertData('paket_kunjungan', $isi);

        if ($cek) {
            aktifitas_user('Master Paket Kunjungan', 'tambah data', $kode_paket, $kode_multiprice, json_encode(['']), json_encode($isi));
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // hapus paket kunjungan
    public function delPaketKunjungan()
    {
        $kode_paket = $this->input->get('kode_paket');

        $cekx = $this->M_global->getData('paket_kunjungan', ['kode_paket' => $kode_paket]);
        aktifitas_user('Master Paket Kunjungan', 'menghapus', $kode_paket, 'Kunjungan ke: ' . $cekx->kunjungan);

        $cek = $this->M_global->delData('paket_kunjungan', ['kode_paket' => $kode_paket]);

        if ($cek) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // ############################################################################################################################################################################

    /**
     * Master COA
     */

    // paket kunjungan page
    public function coa()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter   = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'COA',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Master/coa_list',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Master/Umum/Coa', $parameter);
    }

    // fungsi list coa_list
    public function coa_list()
    {
        $this->load->model('M_coa');

        // kondisi role
        $created                = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->created;

        $list                   = $this->M_coa->get_datatables();

        $data                   = [];
        $no                     = $_POST['start'] + 1;

        // Loop through the list to populate the data array
        foreach ($list as $rd) {
            if ($created > 0) {
                $crt_diss       = '';
            } else {
                $crt_diss       = 'disabled';
            }

            $parent = $this->M_global->getData('m_coa', ['id' => $rd->parent_id]);
            if ($parent) {
                $parent = $parent->coa_name;
            } else {
                $parent = '';
            }

            $row    = [];
            $row[]  = '<div class="text-right">' . $no++ . '</div>';
            $row[]  = $rd->kode_coa . '<br>' . (($rd->is_active == 1) ? '<span class="badge badge-primary">Aktif</span>' : '<span class="badge badge-danger">Tidak</span>');
            $row[]  = $rd->coa_name;
            $row[]  = $this->M_global->getData('m_group_coa', ['id' => $rd->coa_group])->name_id;
            $row[]  = $parent;
            $row[]  = '<div class="text-center">' . (($rd->is_header == 1) ? '<span class="badge badge-primary">Ya</span>' : '<span class="badge badge-danger">Tidak</span>') . '</div>';
            $row[]  = $rd->normal_balance;
            $row[]  = '<div class="text-center"></div>';
            $data[] = $row;
        }

        // Prepare the output in JSON format
        $output = [
            "draw"              => $_POST['draw'],
            "recordsTotal"      => $this->M_coa->count_all(),
            "recordsFiltered"   => $this->M_coa->count_filtered(),
            "data"              => $data,
        ];

        // Send the output to the view
        echo json_encode($output);
    }

    // form coa page
    public function form_coa($param)
    {
        // website config
        $web_setting  = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version  = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param != '0') {
            $coa = $this->M_global->getData('m_coa', ['id' => $param]);
        } else {
            $coa = null;
        }

        $parameter = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Coa',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => '',
            'coa'      => $coa,
        ];

        $this->template->load('Template/Content', 'Master/Umum/Form_coa', $parameter);
    }

    // coa proses
    public function coa_proses()
    {
        $kode_cabang    = $this->session->userdata('cabang');
        $kode_coa       = $this->input->post('idCoa');
        $coa_name       = $this->input->post('coa_name');
        $coa_group      = $this->input->post('coa_group');
        $parent_id      = $this->input->post('parent_id');
        $is_header      = $this->input->post('is_header');
        $is_active      = $this->input->post('is_active');
        $remark         = $this->input->post('remark');
        $normal_balance = $this->input->post('normal_balance');

        // Get parent COA level and add 1 for child level
        $coa_level = 1; // Default level if no parent
        if ($parent_id) {
            $parent_coa = $this->M_global->getData('m_coa', ['kode_coa' => $parent_id]);
            if ($parent_coa) {
                $coa_level = $parent_coa->coa_level + 1;
            }
        }

        $data = [
            'kode_coa'          => $kode_coa,
            'kode_cabang'       => $kode_cabang,
            'coa_name'          => $coa_name,
            'coa_group'         => $coa_group,
            'parent_id'         => $parent_id,
            'is_header'         => $is_header,
            'is_active'         => $is_active,
            'coa_level'         => $coa_level,
            'normal_balance'    => $normal_balance,
            'remark'            => $remark,
            'tgl_coa'           => date('Y-m-d'),
            'jam_coa'           => date('H:i:s'),
        ];

        $where = ['kode_cabang' => $kode_cabang, 'kode_coa' => $kode_coa];

        $cek_coa = $this->M_global->getData('m_coa', $where);

        if ($cek_coa) {
            $cek = $this->M_global->updateData('m_coa', $data, $where);
        } else {
            $cek = $this->M_global->insertData('m_coa', $data);
        }

        if ($cek) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }
}
