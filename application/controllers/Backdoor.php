<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Backdoor extends CI_Controller
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

            $id_menu = $this->M_global->getData('m_menu', ['url' => 'Backdoor'])->id;

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

    // home page
    public function index()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'             => 'Pintasan',
            'nama_apps'         => $web_setting->nama,
            'page'              => 'Backdoor',
            'web'               => $web_setting,
            'web_version'       => $web_version->version,
            'kunjungan_poli'    => $this->db->query("SELECT p.keterangan AS poli, COUNT(boh.kode_poli) AS jumlah FROM pembayaran buy JOIN barang_out_header boh ON buy.inv_jual = boh.invoice JOIN m_poli p ON boh.kode_poli = p.kode_poli GROUP BY boh.kode_poli")->result(),
            'jumlah_beli'       => count($this->M_global->getResult('barang_out_header')),
            'jumlah_member'     => count($this->M_global->getResult('member')),
        ];

        $this->template->load('Template/Content', 'Backdoor/Data', $parameter);
    }

    // trx_empty page
    public function trx_empty()
    {
        $sess = $this->session->userdata('email');
        $cabang = $this->session->userdata('init_cabang');
        $shift = $this->session->userdata('shift');

        // Log activity
        $aktifitas = [
            'email' => $sess,
            'kegiatan' => $sess . " Telah <b>mengosongkan Transaksi</b>",
            'menu' => 'Pintu Belakang',
            'waktu' => date('Y-m-d H:i:s'),
            'kode_cabang' => $cabang,
            'shift' => $shift,
        ];

        $this->db->insert("activity_user", $aktifitas);

        // Get all tables in database
        $tables = $this->db->list_tables();

        // Tables to exclude from truncation
        $excluded_tables = [
            'barang_in_detail',
            'barang_in_header',
            'barang_in_retur_detail',
            'barang_in_retur_header',
            'barang_out_detail',
            'barang_out_header',
            'barang_out_retur_detail',
            'barang_out_retur_header',
            'barang_po_in_detail',
            'barang_po_in_header',
            'barang_stok',
            'bayar_card_detail',
            'bayar_kas_card',
            'bayar_um_card_detail',
            'cart_detail',
            'cart_header',
            'cart_promo',
            'closing_detail',
            'closing_kasir',
            'daftar_ulang',
            'deposit_kas',
            'detail_tindakan',
            'doc_px',
            'emr_dok',
            'emr_dok_cppt',
            'emr_dok_fisik',
            'emr_dok_icd9',
            'emr_dok_icd10',
            'emr_lab',
            'emr_per',
            'emr_per_barang',
            'emr_rad',
            'emr_tarif',
            'jurnal_detail',
            'jurnal_header',
            'jurnal_rule',
            'kas_bank',
            'kas_besar',
            'kas_second',
            'kas_utama',
            'layar_perawat',
            'master_kode',
            'mutasi_detail',
            'mutasi_header',
            'mutasi_kas',
            'mutasi_po_detail',
            'm_anjungan',
            'mutasi_po_header',
            'paket_kunjungan',
            'pembayaran',
            'pembayaran_tarif_single',
            'pembayaran_uangmuka',
            'pendaftaran',
            'penyesuaian_detail',
            'penyesuaian_header',
            'piutang',
            'surat',
            'tarif_jasa',
            'tarif_paket',
            'tarif_paket_bhp',
            'tarif_paket_pasien',
            'tarif_single_bhp',
            'tindakan_bhp',
            'triage_header',
            'triage_htt',
            'uang_muka',
        ];

        try {
            // Disable foreign key checks
            $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

            // Truncate all tables except excluded ones
            foreach ($tables as $table) {
                if (in_array($table, $excluded_tables)) {
                    $this->db->query("TRUNCATE TABLE $table");
                }
            }

            // Re-enable foreign key checks
            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

            echo json_encode(['status' => 1]);
        } catch (Exception $e) {
            // Re-enable foreign key checks in case of error
            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
            echo json_encode(['status' => 0]);
        }
    }

    public function db_empty()
    {
        $sess = $this->session->userdata('email');
        $cabang = $this->session->userdata('init_cabang');
        $shift = $this->session->userdata('shift');

        // Log activity
        $aktifitas = [
            'email' => $sess,
            'kegiatan' => $sess . " Telah <b>mengosongkan Database</b>",
            'menu' => 'Pintu Belakang',
            'waktu' => date('Y-m-d H:i:s'),
            'kode_cabang' => $cabang,
            'shift' => $shift,
        ];

        $this->db->insert("activity_user", $aktifitas);

        // Get all tables in database
        $tables = $this->db->list_tables();

        // Tables to exclude from truncation
        $excluded_tables = [
            'akses_menu',
            'backup_db',
            'barang',
            'barang_cabang',
            'barang_in_detail',
            'barang_in_header',
            'barang_in_retur_detail',
            'barang_in_retur_header',
            'barang_jenis',
            'barang_out_detail',
            'barang_out_header',
            'barang_out_retur_detail',
            'barang_out_retur_header',
            'barang_po_in_detail',
            'barang_po_in_header',
            'barang_satuan',
            'barang_stok',
            'bayar_card_detail',
            'bayar_kas_card',
            'bayar_um_card_detail',
            'bed',
            'bed_cabang',
            'cabang',
            'cabang_user',
            'cart_detail',
            'cart_header',
            'cart_promo',
            'closing_detail',
            'closing_kasir',
            'daftar_ulang',
            'deposit_kas',
            'detail_tindakan',
            'doc_px',
            'dokter',
            'dokter_poli',
            'emr_dok',
            'emr_dok_cppt',
            'emr_dok_fisik',
            'emr_dok_icd9',
            'emr_dok_icd10',
            'emr_lab',
            'emr_per',
            'emr_per_barang',
            'emr_rad',
            'emr_tarif',
            'jadwal_dokter',
            'jadwal_so',
            'jurnal_detail',
            'jurnal_header',
            'jurnal_rule',
            'kas_bank',
            'kas_besar',
            'kas_second',
            'kas_utama',
            'kategori_tarif',
            'klasifikasi_akun',
            'layar_perawat',
            'logistik',
            'logistik_cabang',
            'master_kode',
            'member',
            'member_token',
            'multiprice_tindakan',
            'mutasi_detail',
            'mutasi_header',
            'mutasi_kas',
            'mutasi_po_detail',
            'm_akun',
            'm_anjungan',
            'm_bank',
            'm_bank_perusahaan',
            'm_cara_masuk',
            'm_coa',
            'm_group_coa',
            'm_gudang',
            'm_jenis',
            'm_jenis_bayar',
            'm_kategori',
            'm_kelas',
            'm_pajak',
            'm_pekerjaan',
            'm_pendidikan',
            'm_poli',
            'm_prefix',
            'm_promo',
            'm_role',
            'm_ruang',
            'm_satuan',
            'm_supplier',
            'm_tarif',
            'm_tindakan',
            'mutasi_po_header',
            'paket_kunjungan',
            'pembayaran',
            'pembayaran_tarif_single',
            'pembayaran_uangmuka',
            'pendaftaran',
            'penyesuaian_detail',
            'penyesuaian_header',
            'perawat',
            'perawat_poli',
            'piutang',
            'surat',
            'tarif_jasa',
            'tarif_paket',
            'tarif_paket_bhp',
            'tarif_paket_pasien',
            'tarif_single_bhp',
            'tindakan_bhp',
            'tipe_bank',
            'triage_header',
            'triage_htt',
            'uang_muka',
        ];

        try {
            // Disable foreign key checks
            $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

            // Truncate all tables except excluded ones
            foreach ($tables as $table) {
                if (in_array($table, $excluded_tables)) {
                    $this->db->query("TRUNCATE TABLE $table");
                }
            }

            // Re-enable foreign key checks
            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

            echo json_encode(['status' => 1]);
        } catch (Exception $e) {
            // Re-enable foreign key checks in case of error
            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
            echo json_encode(['status' => 0]);
        }
    }

    // home page
    public function data_db()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'             => 'Database',
            'nama_apps'         => $web_setting->nama,
            'page'              => 'Backdoor',
            'web'               => $web_setting,
            'web_version'       => $web_version->version,
            'kunjungan_poli'    => $this->db->query("SELECT p.keterangan AS poli, COUNT(boh.kode_poli) AS jumlah FROM pembayaran buy JOIN barang_out_header boh ON buy.inv_jual = boh.invoice JOIN m_poli p ON boh.kode_poli = p.kode_poli GROUP BY boh.kode_poli")->result(),
            'backup_db'         => $this->db->query('SELECT * FROM backup_db ORDER BY id DESC')->result(),
        ];

        $this->template->load('Template/Content', 'Backdoor/Data_db', $parameter);
    }

    // backup db
    public function backup_db()
    {
        $this->load->dbutil();
        $this->load->helper('file');

        $date   = date('Ymd');
        $clock  = date('Hi');

        $dbname = $this->db->database . '_' . $date . '_' . $clock;

        $sess       = $this->session->userdata('email');
        $cabang     = $this->session->userdata('init_cabang');
        $shift      = $this->session->userdata('shift');

        $aktifitas = [
            'email'         => $sess,
            'kegiatan'      => $sess . " Telah <b>melakukan Backup Database " . $dbname . "</b>",
            'menu'          => 'Pintu Belakang',
            'waktu'         => date('Y-m-d H:i:s'),
            'kode_cabang'   => $cabang,
            'shift'         => $shift,
        ];

        $this->db->insert("activity_user", $aktifitas);

        $data = [
            'nama'          => $dbname . '.sql',
            'tgl_backup'    => date('Y-m-d H:i:s'),
        ];

        $this->M_global->insertData('backup_db', $data);

        $save   = 'database/' . $dbname . '.sql';

        $config = [
            'tables'                => [],
            'ignore'                => [],
            'format'                => 'txt',
            'filename'              => $dbname . '.sql',
            'add_drop'              => TRUE,
            'add_insert'            => TRUE,
            'foreign_key_checks'    => FALSE,
            'newline'               => "\n",
        ];

        $backup = $this->dbutil->backup($config);
        write_file($save, $backup);

        echo json_encode(['status' => 1]);
    }

    public function del_db($id)
    {
        $dbname     = $this->M_global->getData('backup_db', ['id' => $id])->nama;

        $sess       = $this->session->userdata('email');
        $cabang     = $this->session->userdata('init_cabang');
        $shift      = $this->session->userdata('shift');

        $aktifitas = [
            'email'         => $sess,
            'kegiatan'      => $sess . " Telah <b>menghapus Backupan Database " . $dbname . "</b>",
            'menu'          => 'Pintu Belakang',
            'waktu'         => date('Y-m-d H:i:s'),
            'kode_cabang'   => $cabang,
            'shift'         => $shift,
        ];

        $this->db->insert("activity_user", $aktifitas);

        $cek = $this->M_global->delData('backup_db', ['id' => $id]);

        if ($cek) {
            if (file_exists("database/" . $dbname)) {
                unlink("database/" . $dbname);
            }

            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    public function restore_db()
    {
        // Load database utility
        $this->load->dbutil();

        // Target database configuration
        $target_db = [
            'hostname' => 'localhost',
            'username' => $this->db->username,
            'password' => $this->db->password,
            'database' => 'first_apps',
            'dbdriver' => 'mysqli',
            'dbprefix' => '',
            'pconnect' => FALSE,
            'db_debug' => TRUE,
            'char_set' => 'utf8',
            'dbcollat' => 'utf8_general_ci'
        ];

        // Source database configuration  
        $source_db = [
            'hostname' => 'localhost',
            'username' => $this->db->username,
            'password' => $this->db->password,
            'database' => 'first_apps_migrate',
            'dbdriver' => 'mysqli',
            'dbprefix' => '',
            'pconnect' => FALSE,
            'db_debug' => TRUE,
            'char_set' => 'utf8',
            'dbcollat' => 'utf8_general_ci'
        ];

        try {
            // Connect to source database
            $source_conn = $this->load->database($source_db, TRUE);

            // Connect to target database
            $target_conn = $this->load->database($target_db, TRUE);

            // Create target database if not exists
            $target_conn->query("CREATE DATABASE IF NOT EXISTS first_apps");

            // Drop all existing tables in target database
            $target_tables = $target_conn->list_tables();
            $target_conn->query('SET FOREIGN_KEY_CHECKS = 0');
            foreach ($target_tables as $table) {
                $target_conn->query("DROP TABLE IF EXISTS $table");
            }

            // Get all tables from source database
            $tables = $source_conn->list_tables();

            foreach ($tables as $table) {
                // Get table structure
                $query = $source_conn->query("SHOW CREATE TABLE $table");
                $row = $query->row_array();
                $create_table_sql = $row['Create Table'];

                // Modify create table SQL to allow NULL for date/time columns
                $create_table_sql = preg_replace('/datetime NOT NULL/i', 'datetime NULL', $create_table_sql);
                $create_table_sql = preg_replace('/date NOT NULL/i', 'date NULL', $create_table_sql);
                $create_table_sql = preg_replace('/timestamp NOT NULL/i', 'timestamp NULL', $create_table_sql);

                // Create table in target database
                $target_conn->query($create_table_sql);

                // Copy data
                $data = $source_conn->query("SELECT * FROM $table")->result_array();
                if (!empty($data)) {
                    $batch_size = 1000;
                    $chunks = array_chunk($data, $batch_size);

                    foreach ($chunks as $chunk) {
                        $target_conn->insert_batch($table, $chunk);
                    }
                }
            }

            // Re-enable foreign key checks
            $target_conn->query('SET FOREIGN_KEY_CHECKS = 1');

            // Log the migration
            $sess = $this->session->userdata('email');
            $cabang = $this->session->userdata('init_cabang');
            $shift = $this->session->userdata('shift');

            $this->M_global->updateData('web_setting', ['last_res' => date('Y-m-d H:i:s')], ['id' => 1]);

            $activity = [
                'email' => $sess,
                'kegiatan' => $sess . " has successfully <b>restored database with data from first_apps to first_apps_migrate</b>",
                'menu' => 'Database Restore',
                'waktu' => date('Y-m-d H:i:s'),
                'kode_cabang' => $cabang,
                'shift' => $shift,
            ];

            $this->db->insert("activity_user", $activity);

            echo json_encode([
                'status' => 1,
                'message' => 'Database restore completed successfully with all data'
            ]);
        } catch (Exception $e) {
            // Re-enable foreign key checks in case of error
            if (isset($target_conn)) {
                $target_conn->query('SET FOREIGN_KEY_CHECKS = 1');
            }

            echo json_encode([
                'status' => 0,
                'message' => 'Restore failed: ' . $e->getMessage()
            ]);
        }
    }

    // user akses page
    public function user_akses()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'             => 'Pintasan',
            'nama_apps'         => $web_setting->nama,
            'page'              => 'Backdoor',
            'web'               => $web_setting,
            'web_version'       => $web_version->version,
            'kunjungan_poli'    => $this->db->query("SELECT p.keterangan AS poli, COUNT(boh.kode_poli) AS jumlah FROM pembayaran buy JOIN barang_out_header boh ON buy.inv_jual = boh.invoice JOIN m_poli p ON boh.kode_poli = p.kode_poli GROUP BY boh.kode_poli")->result(),
            'role'              => $this->M_global->getResult('m_role'),
            'list_data'         => 'Backdoor/akses_user_list/',
            'param1'            => null,
        ];

        $this->template->load('Template/Content', 'Backdoor/Akses_user', $parameter);
    }

    // list akses user
    public function akses_user_list()
    {
        $this->load->model("M_user_list");
        // Retrieve data from the model
        $list = $this->M_user_list->get_datatables();

        $data = [];
        $no = $_POST['start'] + 1;

        // Loop through the list to populate the data array
        foreach ($list as $rd) {
            $role       = $this->M_global->getResult('m_role');

            $row = [];
            $row[] = $no;
            $row[] = $rd->kode_user . ' ~ ' . $rd->nama;
            $nor = 1;
            foreach ($role as $r) {
                $row[] = '<div class="text-center">
                    <input type="checkbox" class="form-control" id="krole' . $no . '_' . $nor . '" ' . (($r->kode_role == $rd->kode_role) ? 'checked' : '') . ' name="krole[]" value="' . $r->kode_role . '" onclick="changeRole(' . "'" . $rd->kode_user . "', '" . $r->kode_role . "', '" . $no . "', '" . $nor . "', '" . $rd->nama . "', '" . $r->keterangan . "'" . ')" ' . (($r->kode_role == $rd->kode_role) ? 'disabled' : '') . '>
                </div>';
                $nor++;
            }
            $data[] = $row;
            $no++;
        }

        // Prepare the output in JSON format
        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->M_user_list->count_all(),
            "recordsFiltered" => $this->M_user_list->count_filtered(),
            "data" => $data,
        ];

        // Send the output to the view
        echo json_encode($output);
    }

    // change akses
    public function changeAkses()
    {
        $kode_user    = $this->input->get('kduser');
        $kode_role    = $this->input->get('kdrole');

        $role         = $this->M_global->getData('m_role', ['kode_role' => $kode_role]);
        $cek          = $this->M_global->updateData('user', ['kode_role' => $kode_role], ['kode_user' => $kode_user]);

        if ($cek) {
            $sess       = $this->session->userdata('email');
            $cabang     = $this->session->userdata('init_cabang');
            $shift      = $this->session->userdata('shift');

            $aktifitas = [
                'email'         => $sess,
                'kegiatan'      => $sess . " Telah <b>mengubah Akses User " . $kode_user . " untuk Role " . $role->keterangan . "</b>",
                'menu'          => 'Pintu Belakang',
                'waktu'         => date('Y-m-d H:i:s'),
                'kode_cabang'   => $cabang,
                'shift'         => $shift,
            ];

            $this->db->insert("activity_user", $aktifitas);

            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // menu akses page
    public function menu_akses()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'             => 'Pintasan',
            'nama_apps'         => $web_setting->nama,
            'page'              => 'Backdoor',
            'web'               => $web_setting,
            'web_version'       => $web_version->version,
            'kunjungan_poli'    => $this->db->query("SELECT p.keterangan AS poli, COUNT(boh.kode_poli) AS jumlah FROM pembayaran buy JOIN barang_out_header boh ON buy.inv_jual = boh.invoice JOIN m_poli p ON boh.kode_poli = p.kode_poli GROUP BY boh.kode_poli")->result(),
            'role'              => $this->M_global->getResult('m_role'),
            'list_data'         => 'Backdoor/akses_menu_list/',
            'param1'            => null,
        ];

        $this->template->load('Template/Content', 'Backdoor/Akses_menu', $parameter);
    }

    // list akses menu
    public function akses_menu_list()
    {
        $this->load->model("M_menu_list");
        // Retrieve data from the model
        $list = $this->M_menu_list->get_datatables();

        $data = [];
        $no = $_POST['start'] + 1;

        // Loop through the list to populate the data array
        foreach ($list as $rd) {
            $role       = $this->M_global->getResult('m_role');

            $row = [];
            $row[] = $no;
            $row[] = $rd->nama;
            $nor = 1;
            foreach ($role as $r) {
                $menu_akses = $this->db->query("SELECT * FROM akses_menu WHERE kode_role = '$r->kode_role' AND id_menu = '$rd->idm' LIMIT 1")->row();

                $akses = ($menu_akses) ? $menu_akses->id : '0';
                $row[] = '<div class="text-center">
                    <input type="checkbox" class="form-control" id="krole' . $no . '_' . $nor . '" ' . (($akses > 0) ? 'checked' : '') . ' name="krole[]" value="' . $r->kode_role . '" onclick="changeAkses(' . "'" . $rd->idm . "', '" . $r->kode_role . "', '" . $no . "', '" . $nor . "', '" . $rd->nama . "', '" . $r->keterangan . "', '" . $rd->idm . "'" . ')">
                </div>';
                $nor++;
            }
            $data[] = $row;
            $no++;
        }

        // Prepare the output in JSON format
        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->M_menu_list->count_all(),
            "recordsFiltered" => $this->M_menu_list->count_filtered(),
            "data" => $data,
        ];

        // Send the output to the view
        echo json_encode($output);
    }

    // change menu
    public function changeMenu()
    {
        $kdrole   = $this->input->get('kdrole');
        $idmenu   = $this->input->get('idmenu');

        $menu     = $this->M_global->getData('m_menu', ['id' => $idmenu]);
        $role     = $this->M_global->getData('m_role', ['kode_role' => $kdrole]);
        $cek_menu = $this->M_global->getData('akses_menu', ['kode_role' => $kdrole, 'id_menu' => $idmenu]);

        if ($cek_menu) {
            $cek = $this->M_global->delData('akses_menu', ['kode_role' => $kdrole, 'id_menu' => $idmenu]);
        } else {
            $cek = $this->M_global->insertData('akses_menu', ['kode_role' => $kdrole, 'id_menu' => $idmenu]);
        }

        if ($cek) {
            $sess       = $this->session->userdata('email');
            $cabang     = $this->session->userdata('init_cabang');
            $shift      = $this->session->userdata('shift');

            $aktifitas = [
                'email'         => $sess,
                'kegiatan'      => $sess . " Telah <b>mengubah Akses Menu " . $menu->nama . " untuk Role " . $role->keterangan . "</b>",
                'menu'          => 'Pintu Belakang',
                'waktu'         => date('Y-m-d H:i:s'),
                'kode_cabang'   => $cabang,
                'shift'         => $shift,
            ];

            $this->db->insert("activity_user", $aktifitas);

            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // cabang akses page
    public function cabang_akses()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'             => 'Pintasan',
            'nama_apps'         => $web_setting->nama,
            'page'              => 'Backdoor',
            'web'               => $web_setting,
            'web_version'       => $web_version->version,
            'kunjungan_poli'    => $this->db->query("SELECT p.keterangan AS poli, COUNT(boh.kode_poli) AS jumlah FROM pembayaran buy JOIN barang_out_header boh ON buy.inv_jual = boh.invoice JOIN m_poli p ON boh.kode_poli = p.kode_poli GROUP BY boh.kode_poli")->result(),
            'cabang'            => $this->M_global->getResult('cabang'),
            'list_data'         => 'Backdoor/akses_cabang_list/',
            'param1'            => null,
        ];

        $this->template->load('Template/Content', 'Backdoor/Akses_cabang', $parameter);
    }

    // list akses cabang
    public function akses_cabang_list()
    {
        $this->load->model("M_cabang_list");
        // Retrieve data from the model
        $list = $this->M_cabang_list->get_datatables();

        $sess_cabang = $this->session->userdata('cabang');

        $data = [];
        $no = $_POST['start'] + 1;

        // Loop through the list to populate the data array
        foreach ($list as $rd) {
            $user = $this->M_global->getData('user', ['kode_user' => $rd->kode_user]);
            if ($user->on_off > 0) {
                $sess_email = $user->email;
            } else {
                $sess_email = '';
            }
            $cabang       = $this->M_global->getResult('cabang');

            $row = [];
            $row[] = $no;
            $row[] = $rd->nama;
            $nor = 1;
            foreach ($cabang as $c) {
                $cabang_akses = $this->db->query("SELECT * FROM cabang_user WHERE kode_cabang = '$c->kode_cabang' AND email = '$rd->email' LIMIT 1")->row();

                $akses = ($cabang_akses) ? $cabang_akses->id : '0';
                $row[] = '<div class="text-center">
                    <input type="checkbox" class="form-control" ' . (($sess_email == $rd->email) ? (($sess_cabang == $c->kode_cabang) ? 'disabled' : '') : '') . ' id="kcabang' . $no . '_' . $nor . '" ' . (($akses > 0) ? 'checked' : '') . ' name="kcabang[]" value="' . $c->kode_cabang . '" onclick="changeAkses(' . "'" . $rd->email . "', '" . $c->kode_cabang . "', '" . $no . "', '" . $nor . "', '" . $rd->nama . "', '" . $c->cabang . "', '" . $rd->email . "'" . ')">
                </div>';
                $nor++;
            }
            $data[] = $row;
            $no++;
        }

        // Prepare the output in JSON format
        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->M_cabang_list->count_all(),
            "recordsFiltered" => $this->M_cabang_list->count_filtered(),
            "data" => $data,
        ];

        // Send the output to the view
        echo json_encode($output);
    }

    // change cabang
    public function changeCabang()
    {
        $email          = $this->input->get('email');
        $kode_cabang    = $this->input->get('kcabang');

        $cabangx        = $this->M_global->getData('cabang', ['kode_cabang' => $kode_cabang]);
        $userx          = $this->M_global->getData('user', ['email' => $email]);
        $cek_cabang     = $this->M_global->getData('cabang_user', ['kode_cabang' => $kode_cabang, 'email' => $email]);

        if ($cek_cabang) {
            $cek = $this->M_global->delData('cabang_user', ['kode_cabang' => $kode_cabang, 'email' => $email]);
        } else {
            $cek = $this->M_global->insertData('cabang_user', ['kode_cabang' => $kode_cabang, 'email' => $email]);
        }

        if ($cek) {
            $sess       = $this->session->userdata('email');
            $cabang     = $this->session->userdata('init_cabang');
            $shift      = $this->session->userdata('shift');

            $aktifitas = [
                'email'         => $sess,
                'kegiatan'      => $sess . " Telah <b>mengubah Akses Cabang " . $cabangx->cabang . " untuk User " . $userx->nama . "</b>",
                'menu'          => 'Pintu Belakang',
                'waktu'         => date('Y-m-d H:i:s'),
                'kode_cabang'   => $cabang,
                'shift'         => $shift,
            ];

            $this->db->insert("activity_user", $aktifitas);

            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    public function user_role()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'             => 'Role User',
            'nama_apps'         => $web_setting->nama,
            'page'              => 'Backdoor',
            'web'               => $web_setting,
            'web_version'       => $web_version->version,
            'kunjungan_poli'    => $this->db->query("SELECT p.keterangan AS poli, COUNT(boh.kode_poli) AS jumlah FROM pembayaran buy JOIN barang_out_header boh ON buy.inv_jual = boh.invoice JOIN m_poli p ON boh.kode_poli = p.kode_poli GROUP BY boh.kode_poli")->result(),
            'role'              => $this->db->query('SELECT * FROM m_role ORDER BY keterangan ASC')->result(),
        ];

        $this->template->load('Template/Content', 'Backdoor/Data_role', $parameter);
    }

    public function setRole($param, $id)
    {
        $table = 'm_role';
        $kondisi = ['id' => $id];
        $query = $this->M_global->getData($table, $kondisi);

        if ($param == 1) { // tambah
            $isi = ($query->created == 1) ? ['created' => 0] : ['created' => 1];
        } else if ($param == 2) { // ubah
            $isi = ($query->updated == 1) ? ['updated' => 0] : ['updated' => 1];
        } else if ($param == 3) { // hapus
            $isi = ($query->deleted == 1) ? ['deleted' => 0] : ['deleted' => 1];
        } else { // konfirmasi
            $isi = ($query->confirmed == 1) ? ['confirmed' => 0] : ['confirmed' => 1];
        }

        $cek = $this->M_global->updateData($table, $isi, $kondisi);

        if ($cek) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    public function for_role()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'             => 'Backdoor',
            'nama_apps'         => $web_setting->nama,
            'page'              => 'Role',
            'web'               => $web_setting,
            'web_version'       => $web_version->version,
            'list_data'         => 'Backdoor/role_list/',
            'param1'            => '',
        ];

        $this->template->load('Template/Content', 'Backdoor/Data_akses_role', $parameter);
    }

    // fungsi list role
    public function role_list($param1 = '')
    {
        // parameter untuk list table
        $table            = 'm_role';
        $colum            = ['id', 'kode_role', 'keterangan'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'asc'];
        $kondisi_param1   = '';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        if ($updated > 0) {
            $upd_diss     = '';
        } else {
            $upd_diss     = 'disabled';
        }

        // table server side tampung kedalam variable $list
        $list             = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($deleted > 0) {
                $user             = $this->M_global->getResult('user');

                $role             = [];
                foreach ($user as $u) {
                    $role[]       = [
                        $u->kode_role
                    ];
                }

                $flattened_role   = array_merge(...$role);

                if (in_array($rd->kode_role, $flattened_role)) {
                    $del_diss       = 'disabled';
                } else {
                    $del_diss       = '';
                }
            } else {
                $del_diss           = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_role;
            $row[]  = $rd->keterangan;
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" style="margin-bottom: 5px;" onclick="ubah(' . "'" . $rd->kode_role . "'" . ')" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" style="margin-bottom: 5px;" onclick="hapus(' . "'" . $rd->kode_role . "'" . ')" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi cek role berdasarkan keterangan role
    public function cekRole()
    {
        // ambil keterangan inputan
        $keterangan   = $this->input->post('keterangan');

        // cek keterangan pada table m_role
        $cek          = $this->M_global->jumDataRow('m_role', ['keterangan' => $keterangan]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi proses simpan/update role
    public function role_proses($param)
    {
        // variable
        $keterangan       = $this->input->post('keterangan');

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kodeRole   = _kodeRole();
        } else { // selain itu
            // ambil kode dari inputan
            $kodeRole   = $this->input->post('kodeRole');
        }

        // tampung variable kedalam $isi
        $isi = [
            'kode_role'   => $kodeRole,
            'keterangan'    => $keterangan,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek = $this->M_global->insertData('m_role', $isi);

            $cek_param = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek = $this->M_global->updateData('m_role', $isi, ['kode_role' => $kodeRole]);

            $cek_param = 'mengubah';
        }

        if ($cek) { // jika fungsi berjalan
            aktifitas_user(
                'Backdoor Role',
                $cek_param,
                $kodeRole,
                $this->M_global->getData('m_role', ['kode_role' => $kodeRole])->keterangan
            );

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi ambil informasi role berdasarkan kode role
    public function getInfoRole($kode_role)
    {
        // ambil data role berdasarkan kode_role
        $data = $this->M_global->getData('m_role', ['kode_role' => $kode_role]);
        // lempar ke view
        echo json_encode($data);
    }

    // fungsi hapus role berdasarkan kode_role
    public function delRole($kode_role)
    {
        // jalankan fungsi hapus role berdasarkan kode_role
        aktifitas_user('Backdoor Role', 'menghapus', $kode_role, $this->M_global->getData('m_role', ['kode_role' => $kode_role])->keterangan);
        $cek = $this->M_global->delData('m_role', ['kode_role' => $kode_role]);

        if ($cek) { // jika fungsi berjalan

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    public function for_cabang()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'             => 'Backdoor',
            'nama_apps'         => $web_setting->nama,
            'page'              => 'cabang',
            'web'               => $web_setting,
            'web_version'       => $web_version->version,
            'list_data'         => 'Backdoor/cabang_list/',
            'param1'            => '',
        ];

        $this->template->load('Template/Content', 'Backdoor/Data_cabang', $parameter);
    }

    // fungsi list cabang
    public function cabang_list($param1 = '')
    {
        // parameter untuk list table
        $table            = 'cabang';
        $colum            = ['id', 'kode_cabang', 'inisial_cabang', 'cabang', 'email', 'kontak', 'owner', 'provinsi', 'kabupaten', 'kecamatan', 'desa', 'kode_pos', 'rt', 'rw', 'aktif_dari', 'aktif_sampai'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'asc'];
        $kondisi_param1   = '';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        if ($updated > 0) {
            $upd_diss     = '';
        } else {
            $upd_diss     = 'disabled';
        }

        // table server side tampung kedalam variable $list
        $list             = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            $prov   = ($rd->provinsi == null) ? '-' : $this->M_global->getData('m_provinsi', ['kode_provinsi' => $rd->provinsi])->provinsi;
            $kab    = ($rd->kabupaten == null) ? '-' : $this->M_global->getData('kabupaten', ['kode_kabupaten' => $rd->kabupaten])->kabupaten;
            $kec    = ($rd->kecamatan == null) ? '-' : $this->M_global->getData('kecamatan', ['kode_kecamatan' => $rd->kecamatan])->kecamatan;

            if ($deleted > 0) {
                (date('Y-m-d') < $rd->aktif_sampai) ? $del_diss = '' : $del_diss = 'disabled';
            } else {
                $del_diss           = 'disabled';
            }

            $tgl1 = strtotime(date('Y-m-d'));
            $tgl2 = strtotime($rd->aktif_sampai);

            $jarak = ($tgl2 - $tgl1);

            $aktif_cabang = $jarak / 60 / 60 / 24;

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->inisial_cabang;
            $row[]  = $rd->cabang;
            $row[]  = '<span class="text-primary font-weight-bold">' . $rd->owner . '</span><br>Email: <span class="float-right">' . $rd->email . '</span><br>Hp: <span class="float-right">' . $rd->kontak . '</span>';
            $row[]  = 'Prov. ' . $prov . ',<br>' . $kab . ',<br>Kec. ' . $kec . ',<br>Ds. ' . (($rd->desa == null) ? '-' : $rd->desa) . ',<br>(POS: ' . (($rd->kode_pos == null) ? '-' : $rd->kode_pos) . '), RT.' . (($rd->rt == null) ? '-' : $rd->rt) . '/RW.' . (($rd->rw == null) ? '-' : $rd->rw);
            $row[]  = 'Dari: <span class="float-right">' . $rd->aktif_dari . '</span><br>Sampai: <span class="float-right">' . $rd->aktif_sampai . '</span><br><br><span class="float-right text-danger font-weight-bold">' . $aktif_cabang . ' Hari Lagi</span>';
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-warning" style="margin-bottom: 5px;" onclick="ubah(' . "'" . $rd->kode_cabang . "'" . ')" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-danger" style="margin-bottom: 5px;" onclick="hapus(' . "'" . $rd->kode_cabang . "'" . ')" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // form cabang page
    public function form_cabang($param)
    {
        // website config
        $web_setting  = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version  = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param != '0') {
            $cabang = $this->M_global->getData('cabang', ['kode_cabang' => $param]);
        } else {
            $cabang = null;
        }

        $parameter = [
            $this->data,
            'judul'         => 'Backdoor',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Cabang',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => '',
            'cabang'        => $cabang,
        ];

        $this->template->load('Template/Content', 'Backdoor/Form_cabang', $parameter);
    }

    public function cekCabang()
    {
        // ambil cabang inputan
        $cabang = $this->input->post('cabang');

        // cek cabang pada table cabang
        $cek  = $this->M_global->jumDataRow('cabang', ['cabang' => $cabang]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    public function cabang_proses($param)
    {
        // variable
        $inisial_cabang   = $this->input->post('inisial_cabang');
        $cabang           = $this->input->post('cabang');
        $kontak           = $this->input->post('kontak');
        $email            = $this->input->post('email');
        $owner            = $this->input->post('owner');
        $provinsi         = $this->input->post('provinsi');
        $kabupaten        = $this->input->post('kabupaten');
        $kecamatan        = $this->input->post('kecamatan');
        $desa             = $this->input->post('desa');
        $kode_pos         = $this->input->post('kode_pos');
        $rt               = $this->input->post('rt');
        $rw               = $this->input->post('rw');
        $aktif_dari       = $this->input->post('aktif_dari');
        $aktif_sampai     = $this->input->post('aktif_sampai');

        if ($param == 1) { // jika parameternya 1
            // maka buat kode baru
            $kode_cabang = _kodeCabang();
        } else { // selain itu
            // ambil kode dari inputan
            $kode_cabang = $this->input->post('kode_cabang');
        }

        // tampung variable kedalam $isi
        $isi = [
            'kode_cabang'       => $kode_cabang,
            'inisial_cabang'    => $inisial_cabang,
            'cabang'            => $cabang,
            'kontak'            => $kontak,
            'email'             => $email,
            'owner'             => $owner,
            'provinsi'          => $provinsi,
            'kabupaten'         => $kabupaten,
            'kecamatan'         => $kecamatan,
            'desa'              => $desa,
            'kode_pos'          => $kode_pos,
            'rt'                => $rt,
            'rw'                => $rw,
            'aktif_dari'        => $aktif_dari,
            'aktif_sampai'      => $aktif_sampai,
        ];

        if ($param == 1) { // jika parameternya 1
            // jalankan fungsi simpan
            $cek          = $this->M_global->insertData('cabang', $isi);

            $cek_param    = 'menambahkan';
        } else { // selain itu
            // jalankan fungsi update
            $cek          = $this->M_global->updateData('cabang', $isi, ['kode_cabang' => $kode_cabang]);

            $cek_param    = 'mengubah';
        }

        if ($cek) { // jika fungsi berjalan
            aktifitas_user('Backdoor Cabang', $cek_param, $kode_cabang, $this->M_global->getData('cabang', ['kode_cabang' => $kode_cabang])->cabang);

            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi hapus cabang berdasarkan kode_cabang
    public function delCabang($kode_cabang)
    {
        // jalankan fungsi hapus cabang berdasarkan kode_cabang
        aktifitas_user('Backdoor Cabang', 'menghapus', $kode_cabang, $this->M_global->getData('cabang', ['kode_cabang' => $kode_cabang])->cabang);
        $cek = $this->M_global->delData('cabang', ['kode_cabang' => $kode_cabang]);

        if ($cek) { // jika fungsi berjalan
            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    public function migrasi()
    {
        // Load required libraries
        $this->load->dbutil();
        $this->load->library('cache');

        // Check if target database exists
        try {
            $check_db = mysqli_connect(
                'localhost',
                $this->db->username,
                $this->db->password
            );

            $result = mysqli_query($check_db, "SHOW DATABASES LIKE 'first_apps_migrate'");

            if (mysqli_num_rows($result) == 0) {
                // Create the database if it doesn't exist
                mysqli_query($check_db, "CREATE DATABASE first_apps_migrate");
            }

            mysqli_close($check_db);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 0,
                'message' => 'Failed to check/create database: ' . $e->getMessage()
            ]);
            return;
        }

        // Source database configuration
        $source_db = [
            'hostname' => 'localhost',
            'username' => $this->db->username,
            'password' => $this->db->password,
            'database' => 'first_apps',
            'dbdriver' => 'mysqli',
            'dbprefix' => '',
            'pconnect' => FALSE,
            'db_debug' => TRUE,
            'char_set' => 'utf8',
            'dbcollat' => 'utf8_general_ci'
        ];

        // Target database configuration  
        $target_db = [
            'hostname' => 'localhost',
            'username' => $this->db->username,
            'password' => $this->db->password,
            'database' => 'first_apps_migrate',
            'dbdriver' => 'mysqli',
            'dbprefix' => '',
            'pconnect' => FALSE,
            'db_debug' => TRUE,
            'char_set' => 'utf8',
            'dbcollat' => 'utf8_general_ci'
        ];

        try {
            // Connect to source database
            $source_conn = $this->load->database($source_db, TRUE);

            // Connect to target database
            $target_conn = $this->load->database($target_db, TRUE);

            // Cache the database connections
            $this->cache->save('source_conn', $source_conn, 300);
            $this->cache->save('target_conn', $target_conn, 300);

            // Drop all existing tables in target database
            $target_tables = $target_conn->list_tables();
            $target_conn->query('SET FOREIGN_KEY_CHECKS = 0');
            foreach ($target_tables as $table) {
                $target_conn->query("DROP TABLE IF EXISTS $table");
            }

            // Get all tables from source database
            $tables = $source_conn->list_tables();

            foreach ($tables as $table) {
                // Get table structure
                $query = $source_conn->query("SHOW CREATE TABLE $table");
                $row = $query->row_array();
                $create_table_sql = $row['Create Table'];

                // Modify create table SQL to allow NULL for date/time columns
                $create_table_sql = preg_replace('/datetime NOT NULL/i', 'datetime NULL', $create_table_sql);
                $create_table_sql = preg_replace('/date NOT NULL/i', 'date NULL', $create_table_sql);
                $create_table_sql = preg_replace('/timestamp NOT NULL/i', 'timestamp NULL', $create_table_sql);

                // Create table in target database
                $target_conn->query($create_table_sql);

                // Copy data
                $data = $source_conn->query("SELECT * FROM $table")->result_array();
                if (!empty($data)) {
                    // Process data to convert invalid dates to NULL
                    foreach ($data as &$row) {
                        foreach ($row as $key => &$value) {
                            if (in_array($value, ['0000-00-00', '00:00:00', '0000-00-00 00:00:00'])) {
                                $value = NULL;
                            }
                        }
                    }

                    $batch_size = 1000;
                    $chunks = array_chunk($data, $batch_size);

                    foreach ($chunks as $chunk) {
                        $target_conn->insert_batch($table, $chunk);
                    }
                }
            }

            // Re-enable foreign key checks
            $target_conn->query('SET FOREIGN_KEY_CHECKS = 1');

            // Clear database connections from cache
            $this->cache->delete('source_conn');
            $this->cache->delete('target_conn');

            // Log the migration
            $sess = $this->session->userdata('email');
            $cabang = $this->session->userdata('init_cabang');
            $shift = $this->session->userdata('shift');

            $this->M_global->updateData('web_setting', ['last_bak' => date('Y-m-d H:i:s')], ['id' => 1]);

            $activity = [
                'email' => $sess,
                'kegiatan' => $sess . " telah berhasil <b>melakukan migrasi data dari first_apps menuju first_apps_migrate</b>",
                'menu' => 'Migrasi Database',
                'waktu' => date('Y-m-d H:i:s'),
                'kode_cabang' => $cabang,
                'shift' => $shift,
            ];

            $this->db->insert("activity_user", $activity);

            echo json_encode([
                'status' => 1,
                'message' => 'Database berhasil dimigrasikan'
            ]);
        } catch (Exception $e) {
            // Re-enable foreign key checks in case of error
            if (isset($target_conn)) {
                $target_conn->query('SET FOREIGN_KEY_CHECKS = 1');
            }

            // Clear database connections from cache on error
            $this->cache->delete('source_conn');
            $this->cache->delete('target_conn');

            echo json_encode([
                'status' => 0,
                'message' => 'Gagal melakukan migrasi: ' . $e->getMessage()
            ]);
        }
    }

    public function auto_migrasi()
    {
        // Load required libraries
        $this->load->dbutil();
        $this->load->library('cache');

        // Check if target database exists
        try {
            $check_db = mysqli_connect(
                'localhost',
                $this->db->username,
                $this->db->password
            );

            $result = mysqli_query($check_db, "SHOW DATABASES LIKE 'first_apps_migrate'");

            if (mysqli_num_rows($result) == 0) {
                mysqli_query($check_db, "CREATE DATABASE first_apps_migrate");
            }

            mysqli_close($check_db);
        } catch (Exception $e) {
            return;
        }

        // Source database configuration
        $source_db = [
            'hostname' => 'localhost',
            'username' => $this->db->username,
            'password' => $this->db->password,
            'database' => 'first_apps',
            'dbdriver' => 'mysqli',
            'dbprefix' => '',
            'pconnect' => FALSE,
            'db_debug' => TRUE,
            'char_set' => 'utf8',
            'dbcollat' => 'utf8_general_ci'
        ];

        // Target database configuration  
        $target_db = [
            'hostname' => 'localhost',
            'username' => $this->db->username,
            'password' => $this->db->password,
            'database' => 'first_apps_migrate',
            'dbdriver' => 'mysqli',
            'dbprefix' => '',
            'pconnect' => FALSE,
            'db_debug' => TRUE,
            'char_set' => 'utf8',
            'dbcollat' => 'utf8_general_ci'
        ];

        try {
            $source_conn = $this->load->database($source_db, TRUE);
            $target_conn = $this->load->database($target_db, TRUE);

            $this->cache->save('source_conn', $source_conn, 300);
            $this->cache->save('target_conn', $target_conn, 300);

            $target_tables = $target_conn->list_tables();
            $target_conn->query('SET FOREIGN_KEY_CHECKS = 0');
            foreach ($target_tables as $table) {
                $target_conn->query("DROP TABLE IF EXISTS $table");
            }

            $tables = $source_conn->list_tables();

            foreach ($tables as $table) {
                $query = $source_conn->query("SHOW CREATE TABLE $table");
                $row = $query->row_array();
                $create_table_sql = $row['Create Table'];

                $create_table_sql = preg_replace('/datetime NOT NULL/i', 'datetime NULL', $create_table_sql);
                $create_table_sql = preg_replace('/date NOT NULL/i', 'date NULL', $create_table_sql);
                $create_table_sql = preg_replace('/timestamp NOT NULL/i', 'timestamp NULL', $create_table_sql);

                $target_conn->query($create_table_sql);

                $data = $source_conn->query("SELECT * FROM $table")->result_array();
                if (!empty($data)) {
                    foreach ($data as &$row) {
                        foreach ($row as $key => &$value) {
                            if (in_array($value, ['0000-00-00', '00:00:00', '0000-00-00 00:00:00'])) {
                                $value = NULL;
                            }
                        }
                    }

                    $batch_size = 1000;
                    $chunks = array_chunk($data, $batch_size);

                    foreach ($chunks as $chunk) {
                        $target_conn->insert_batch($table, $chunk);
                    }
                }
            }

            $target_conn->query('SET FOREIGN_KEY_CHECKS = 1');

            $this->cache->delete('source_conn');
            $this->cache->delete('target_conn');

            $sess = $this->session->userdata('email');
            $cabang = $this->session->userdata('init_cabang');
            $shift = $this->session->userdata('shift');

            $this->M_global->updateData('web_setting', ['last_bak' => date('Y-m-d H:i:s')], ['id' => 1]);

            $activity = [
                'email' => $sess,
                'kegiatan' => $sess . " telah berhasil <b>melakukan migrasi data dari first_apps menuju first_apps_migrate</b>",
                'menu' => 'Migrasi Database',
                'waktu' => date('Y-m-d H:i:s'),
                'kode_cabang' => $cabang,
                'shift' => $shift,
            ];

            $this->db->insert("activity_user", $activity);
        } catch (Exception $e) {
            if (isset($target_conn)) {
                $target_conn->query('SET FOREIGN_KEY_CHECKS = 1');
            }
            $this->cache->delete('source_conn');
            $this->cache->delete('target_conn');
        }
    }
}
