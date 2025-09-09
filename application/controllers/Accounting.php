<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Accounting extends CI_Controller
{
    // variable open public untuk controller Home
    public $data;

    public function __construct()
    {
        parent::__construct();
        // load model M_auth
        $this->load->model("M_auth");

        if (!empty($this->session->userdata("email"))) { // jika session email masih ada

            $id_menu = $this->M_global->getData('m_menu', ['url' => 'Accounting'])->id;

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
                    'menu'      => 'Accounting',
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

    // piutang page
    public function piutang()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $hutang_num      = $this->db->query("SELECT * FROM piutang WHERE kode_cabang = '" . $this->session->userdata('cabang') . "' AND jenis > 0 AND status < 1")->num_rows();
        $hutang          = $this->db->query("SELECT SUM(jumlah) AS hutang FROM piutang WHERE kode_cabang = '" . $this->session->userdata('cabang') . "' AND jenis > 0 AND status < 1")->row();
        $piutang_num     = $this->db->query("SELECT * FROM piutang WHERE kode_cabang = '" . $this->session->userdata('cabang') . "' AND jenis < 1 AND status < 1")->num_rows();
        $piutang         = $this->db->query("SELECT SUM(jumlah) AS piutang FROM piutang WHERE kode_cabang = '" . $this->session->userdata('cabang') . "' AND jenis < 1 AND status < 1")->row();

        $parameter = [
            $this->data,
            'judul'         => 'Accounting',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Accounting',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'hutang_num'    => (($hutang_num > 0) ? $hutang_num : 0),
            'hutang'        => $this->db->query("SELECT SUM(jumlah) AS hutang FROM piutang WHERE kode_cabang = '" . $this->session->userdata('cabang') . "' AND jenis > 0 AND status = 0")->row(),
            'piutang'       => $this->db->query("SELECT SUM(jumlah) AS piutang FROM piutang WHERE kode_cabang = '" . $this->session->userdata('cabang') . "' AND jenis < 1 AND status = 0")->row(),
            'piutang_num'   => ($piutang_num > 0) ? $piutang_num : 0,
            'list_data'     => 'Accounting/piutang_list/',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Accounting/Piutang', $parameter);
    }

    // fungsi list piutang_list
    public function piutang_list($param1 = 1, $param2 = '')
    {
        // parameter untuk list table
        $table                  = 'piutang';
        $colum                  = ['id', 'kode_cabang', 'piutang_no', 'tanggal', 'jam', 'referensi', 'jumlah', 'status', 'tanggal_bayar', 'jam_bayar', 'jenis'];
        $order                  = 'id';
        $order2                 = 'desc';
        $order_arr              = ['id' => 'asc'];
        $kondisi_param2         = '';
        $kondisi_param1         = 'tanggal';

        // table server side tampung kedalam variable $list
        $dat                    = explode("~", $param1);

        if ($dat[0] == 1) {
            $bulan              = date('m');
            $tahun              = date('Y');
            $type               = 1;
        } else {
            $bulan              = date('Y-m-d', strtotime($dat[1]));
            $tahun              = date('Y-m-d', strtotime($dat[2]));
            $type               = 2;
        }

        $list                   = $this->M_datatables2->get_datatables($table, $colum, $order_arr, $order, $order2, $kondisi_param1, $type, $bulan, $tahun, $param2, $kondisi_param2);

        $data                   = [];
        $no                     = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {

            if ($rd->status > 0) {
                $confirm_diss   = 'disabled';
            } else {
                $confirm_diss   = '';
            }

            $jual               = $this->M_global->getData('barang_in_header', ['invoice' => $rd->referensi]);
            $retur_jual         = $this->M_global->getData('barang_in_retur_header', ['invoice' => $rd->referensi]);
            $pembayaran         = $this->M_global->getData('pembayaran', ['invoice' => $rd->referensi]);

            if ($jual) {
                $supplier       = $jual->kode_supplier;
                $x              = $this->M_global->getData('m_supplier', ['kode_supplier' => $supplier]);
                $link = 'onclick="printsingle(\'' . htmlspecialchars('Transaksi/single_print_bin/' . $rd->referensi . '/0', ENT_QUOTES, 'UTF-8') . '\')"';
            } else if ($retur_jual) {
                $supplier       = $retur_jual->kode_supplier;
                $x              = $this->M_global->getData('m_supplier', ['kode_supplier' => $supplier]);
                $link = 'onclick="printsingle(\'' . htmlspecialchars('Transaksi/single_print_bin/' . $rd->referensi . '/0', ENT_QUOTES, 'UTF-8') . '\')"';
            } else if ($pembayaran) {
                $cek            = $this->M_global->getData('pembayaran', ['invoice' => $rd->referensi]);
                $supplier       = $cek->kode_user;
                $x              = $this->M_global->getData('user', ['kode_user' => $supplier]);
                $link = 'onclick="printsingle(\'' . htmlspecialchars('Kasir/print_kwitansi/' . $cek->token_pembayaran . '/0', ENT_QUOTES, 'UTF-8') . '\')"';
            } else {
                $supplier       = $this->M_global->getData('pembayaran_uangmuka', ['invoice' => $rd->referensi])->kode_user;
                $x              = $this->M_global->getData('member', ['kode_member' => $supplier]);
                $link = 'onclick="printsingle(\'' . htmlspecialchars('Kasir/print_uangmuka/' . $rd->referensi . '/0', ENT_QUOTES, 'UTF-8') . '\')"';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = '<a type="button" class="text-primary" ' . $link . '>' . htmlspecialchars($rd->referensi, ENT_QUOTES, 'UTF-8') . '</a>';
            $row[]  = '<div class="text-center">' . (($rd->tanggal_bayar == null) ? 'xx/xx/xxxx' : date('d/m/Y', strtotime($rd->tanggal_bayar))) . ' ~ ' . (($rd->jam_bayar == null) ? '00:00:00' : date('H:i:s', strtotime($rd->jam_bayar))) . '</div>';
            $row[]  = ($x) ? $x->nama : '';
            $row[]  = (($rd->jenis > 0) ? '<span class="badge badge-warning">Hutang</span>' : '<span class="badge badge-info">Piutang</span>');
            $row[]  = 'Rp. <span class="float-right">' . number_format($rd->jumlah) . '</span>';
            $row[]  = '<div class="text-center">' . (($rd->status > 0) ? '<span class="badge badge-success">Terbayarkan</span>' : '<span class="badge badge-danger">Belum dibayar</span>') . '</div>';
            $row[]  = '<div class="text-center">
                <button class="btn btn-success" type="button" ' . $confirm_diss . ' title="Bayar #' . $rd->referensi . '" onclick="bayarin(' . "'" . $rd->piutang_no . "', '" . $rd->referensi . "', '" . $rd->jenis  . "'" . ')"><i class="fa-solid fa-circle-dollar-to-slot"></i></button>
            </div>';

            $data[] = $row;
        }

        // hasil server side
        $output = [
            "draw"            => $_POST['draw'],
            "recordsTotal"    => $this->M_datatables2->count_all($table, $colum, $order_arr, $order, $order2, $kondisi_param1, $type, $bulan, $tahun, $param2, $kondisi_param2),
            "recordsFiltered" => $this->M_datatables2->count_filtered($table, $colum, $order_arr, $order, $order2, $kondisi_param1, $type, $bulan, $tahun, $param2, $kondisi_param2),
            "data"            => $data,
        ];

        // kirimkan ke view
        echo json_encode($output);
    }

    public function piutang_bayar()
    {
        $piutang_no     = $this->input->get('inv');
        $jenis          = $this->input->get('jn');
        $kode_cabang    = $this->session->userdata('cabang');
        $data_piutang   = $this->M_global->getData('piutang', ['piutang_no' => $piutang_no, 'jenis' => $jenis]);
        $no_kb          = master_kode('Kas Besar', 10, 'KB', '-', $this->session->userdata('init_cabang'), '-');
        $kode_jurnal    = master_kode('jurnal', 15, 'JUR', '-', $this->session->userdata('init_cabang'), '-');
        $kode_supplier  = $this->M_global->getData('barang_in_header', ['invoice' => $data_piutang->referensi])->kode_supplier;

        $kas_besar = $this->db->select('saldo_akhir')
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get('kas_besar');

        $saldo_terakhir = $kas_besar->num_rows() > 0 ? $kas_besar->row()->saldo_akhir : 0;

        $jumlah_keluar  = $data_piutang->jumlah;
        $saldo_awal     = $saldo_terakhir;
        $saldo_akhir    = $saldo_awal - $jumlah_keluar;

        $data_kas_besar = [
            'tgl'               => date('Y-m-d'),
            'kode_cabang'       => $kode_cabang,
            'jam'               => date('H:i:s'),
            'no_kb'             => $no_kb,
            'jenis'             => 2, // 1 masuk, 2 keluar, 3 transfer, 4 penyesuaian, 5 dll
            'sumber'            => $kode_supplier,
            'tujuan'            => 'Kas Besar',
            'jumlah'            => $jumlah_keluar,
            'saldo_awal'        => $saldo_awal,
            'saldo_akhir'       => $saldo_akhir,
            'referensi'         => $data_piutang->referensi,
            'keterangan'        => 'Pembayaran Supplier',
            'status_verifikasi' => 0, // bisa diubah saat BO verifikasi, 0 pending, 1 diterima, 2 ditolak
            'kode_user'         => $this->session->userdata('kode_user'),
        ];

        $isi_jurnal = [
            'kode_jurnal'       => $kode_jurnal,
            'kode_cabang'       => $kode_cabang,
            'tgl_jurnal'        => date('Y-m-d'),
            'jam_jurnal'        => date('H:i:s'),
            'keterangan'        => 'Pembayaran Supplier',
            'referensi'         => $no_kb,
            'tgl_buat'          => date('Y-m-d'),
            'jam_buat'          => date('H:i:s'),
            'kode_user'         => $this->session->userdata('kode_user'),
        ];

        // Prepare journal entries array
        $isi_jurnal_d = [];

        // Add first journal entry
        $isi_jurnal_d[] = [
            'kode_jurnal'   => $kode_jurnal,
            'kode_coa'      => '2101',
            'debit'         => $jumlah_keluar,
            'credit'        => 0,
            'keterangan'    => 'Pelunasan Hutang ke Supplier',
        ];

        // Add second journal entry  
        $isi_jurnal_d[] = [
            'kode_jurnal'   => $kode_jurnal,
            'kode_coa'      => '1102',
            'debit'         => 0,
            'credit'        => $jumlah_keluar,
            'keterangan'    => 'Pembayaran via Bank'
        ];

        $cek = [
            $this->M_global->updateData('piutang', ['status' => 1, "tanggal_bayar" => date('Y-m-d'), "jam_bayar" => date('H:i:s')], ['piutang_no' => $piutang_no, 'jenis' => $jenis]),
            $this->M_global->insertData('kas_besar', $data_kas_besar),
            $this->M_global->insertData('jurnal_header', $isi_jurnal),
        ];

        // Insert each journal entry separately
        foreach ($isi_jurnal_d as $jurnal_detail) {
            $cek[] = $this->M_global->insertData('jurnal_detail', $jurnal_detail);
        }

        if ($cek) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // deposit_kas page
    public function deposit_kas()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Accounting',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Deposit',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'piutang_num'   => $this->M_global->getDataResult('piutang', ['kode_cabang' => $this->session->userdata('cabang')]),
            'list_data'     => 'Accounting/deposit_kas_list/',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Accounting/Deposit_kas', $parameter);
    }

    // fungsi list deposit_kas_list
    public function deposit_kas_list($param1 = 1, $param2 = '')
    {
        // parameter untuk list table
        $table            = 'deposit_kas';
        $colum            = ['id', 'kode_cabang', 'token', 'cash', 'card', 'jenis_pembayaran', 'tgl_masuk', 'jam_masuk', 'kode_user', 'total'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['tgl_masuk' => 'desc'];
        $kondisi_param2   = '';
        $kondisi_param1   = 'tgl_masuk';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;
        $confirmed        = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->confirmed;

        // table server side tampung kedalam variable $list
        $dat    = explode("~", $param1);

        if ($dat[0] == 1) {
            $bulan        = date('m');
            $tahun        = date('Y');
            $type         = 1;
        } else {
            $bulan        = date('Y-m-d', strtotime($dat[1]));
            $tahun        = date('Y-m-d', strtotime($dat[2]));
            $type         = 2;
        }

        $list             = $this->M_datatables2->get_datatables($table, $colum, $order_arr, $order, $order2, $kondisi_param1, $type, $bulan, $tahun, $param2, $kondisi_param2);

        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                $upd_diss = _lock_button();
            } else {
                $upd_diss = 'disabled';
            }

            if ($deleted > 0) {
                $del_diss = _lock_button();
            } else {
                $del_diss = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = date('d/m/Y', strtotime($rd->tgl_masuk)) . ' ~ ' . date('H:i:s', strtotime($rd->jam_masuk));
            $row[]  = $this->M_global->getData('user', ['kode_user' => $rd->kode_user])->nama;
            $row[]  = (($rd->jenis_pembayaran == 0) ? 'Cash' : (($rd->jenis_pembayaran == 1) ? 'Card' : 'Cash + Card'));
            $row[]  = 'Rp. <span class="float-right">' . number_format($rd->cash) . '</span>';
            $row[]  = 'Rp. <span class="float-right">' . number_format($rd->card) . '</span>';
            $row[]  = 'Rp. <span class="float-right">' . number_format($rd->total) . '</span>';
            $row[]  = '<div class="text-center">
                <button type="button" style="margin-bottom: 5px;" class="btn btn-warning" onclick="ubah(' . "'" . $rd->token . "'" . ')" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" style="margin-bottom: 5px;" class="btn btn-danger" onclick="hapus(' . "'" . $rd->token . "'" . ')" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
            </div>';

            $data[] = $row;
        }

        // hasil server side
        $output = [
            "draw"            => $_POST['draw'],
            "recordsTotal"    => $this->M_datatables2->count_all($table, $colum, $order_arr, $order, $order2, $kondisi_param1, $type, $bulan, $tahun, $param2, $kondisi_param2),
            "recordsFiltered" => $this->M_datatables2->count_filtered($table, $colum, $order_arr, $order, $order2, $kondisi_param1, $type, $bulan, $tahun, $param2, $kondisi_param2),
            "data"            => $data,
        ];

        // kirimkan ke view
        echo json_encode($output);
    }

    // form form_deposit_kas page
    public function form_deposit_kas($param, $param2 = '')
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param == '0') {
            $pembayaran     = null;
            $bayar_detail   = null;
        } else {
            $bayar_detail   = $this->M_global->getDataResult('bayar_kas_card', ['token_deposit' => $param]);
            $pembayaran     = $this->M_global->getData('deposit_kas', ['token' => $param]);
        }

        $parameter = [
            $this->data,
            'judul'             => 'Accounting',
            'nama_apps'         => $web_setting->nama,
            'page'              => 'Kas/Bank Deposit',
            'web'               => $web_setting,
            'web_version'       => $web_version->version,
            'list_data'         => '',
            'data_pembayaran'   => $pembayaran,
            'bayar_detail'      => $bayar_detail,
            'param2'            => $param2,
        ];

        $this->template->load('Template/Content', 'Accounting/Form_deposit', $parameter);
    }

    public function delDepositKas($token)
    {
        $cabang         = $this->session->userdata('cabang');
        $kas_utama      = $this->M_global->getData('kas_utama', ['kode_cabang' => $cabang]);
        $deposit_kas    = $this->M_global->getData('deposit_kas', ['token' => $token]);
        $total          = $deposit_kas->total;

        $this->M_global->updateData('kas_utama', ['masuk' => ($kas_utama->masuk - $deposit_kas->total), 'sisa' => ($kas_utama->sisa - $deposit_kas->total)], ['kode_cabang' => $cabang]);

        $cek = [
            $this->M_global->delData('deposit_kas', ['token' => $token]),
            $this->M_global->delData('bayar_kas_card', ['token_deposit' => $token]),
        ];

        if ($cek) {
            aktifitas_user_transaksi('Accounting', 'menghapus Deposit Kas/Bank', $token);

            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    public function deposit_kas_proses($param)
    {
        $cabang             = $this->session->userdata('cabang');
        $where_kas_utama    = ['kode_cabang' => $cabang];

        if ($param == 2) {
            $token = $this->input->post('token');
        } else {
            $token = tokenKasir(30);
        }

        $tgl_masuk          = $this->input->post('tgl_masuk');
        $jam_masuk          = $this->input->post('jam_masuk');
        $jenis_pembayaran   = $this->input->post('jenis_pembayaran');
        $total              = str_replace(',', '', $this->input->post('total'));
        $cash               = str_replace(',', '', $this->input->post('cash'));
        $card               = str_replace(',', '', $this->input->post('card'));
        $kode_bank          = $this->input->post('kode_bank');
        $tipe_bank          = $this->input->post('tipe_bank');
        $no_card            = $this->input->post('no_card');
        $approval           = $this->input->post('approval');
        $jumlah             = $this->input->post('jumlah_card');

        $isi = [
            'kode_cabang'       => $cabang,
            'token'             => $token,
            'tgl_masuk'         => $tgl_masuk,
            'jam_masuk'         => $jam_masuk,
            'total'             => $total,
            'cash'              => $cash,
            'card'              => $card,
            'jenis_pembayaran'  => $jenis_pembayaran,
            'kode_user'         => $this->session->userdata('kode_user'),
            'shift'             => $this->session->userdata('shift'),
        ];

        $kas_utama          = $this->M_global->getData('kas_utama', $where_kas_utama);

        if ($param == 2) {
            $depo_kas = $this->M_global->getData('deposit_kas', ['token' => $token]);

            // update1
            $this->db->query("UPDATE kas_utama SET masuk = masuk - '$depo_kas->total', sisa = sisa - '$depo_kas->total' WHERE kode_cabang = '$cabang'");

            // update2
            $this->db->query("UPDATE kas_utama SET masuk = masuk + '$total', sisa = sisa + '$total' WHERE kode_cabang = '$cabang'");

            $cek = [
                $this->M_global->updateData('deposit_kas', $isi, ['token' => $token]),
                $this->M_global->delData('bayar_kas_card', ['token_deposit' => $token]),
            ];

            aktifitas_user_transaksi('Accounting', 'mengubah Deposit Kas/Bank', $token);
        } else {
            if ($kas_utama) {
                $cek1 = $this->db->query("UPDATE kas_utama SET masuk = masuk + '$total', sisa = sisa + '$total' WHERE kode_cabang = '$cabang'");
            } else {
                $cek1 = $this->db->query("INSERT INTO kas_utama (kode_cabang, masuk, sisa) VALUES ('$cabang', '$total', '$total')");
            }

            $cek = [
                $this->M_global->insertData('deposit_kas', $isi),
                $cek1,
            ];

            aktifitas_user_transaksi('Accounting', 'menambahkan Deposit Kas/Bank', $token);
        }


        if ($cek) {
            if ($jenis_pembayaran > 0) {
                // detail card
                if (!empty($kode_bank)) {
                    $jum = count($kode_bank);

                    // lakukan loop dengan for
                    for ($x = 0; $x <= ($jum - 1); $x++) {
                        $_kode_bank   = $kode_bank[$x];
                        $_tipe_bank   = $tipe_bank[$x];
                        $_no_card     = $no_card[$x];
                        $_approval    = $approval[$x];
                        $_jumlah      = str_replace(',', '', $jumlah[$x]);

                        // isi detail card
                        $isi_card = [
                            'token_deposit'     => $token,
                            'kode_bank'         => $_kode_bank,
                            'kode_tipe'         => $_tipe_bank,
                            'no_card'           => $_no_card,
                            'approval'          => $_approval,
                            'jumlah'            => $_jumlah,
                        ];

                        // insert ke bayar_kas_card
                        $this->M_global->insertData('bayar_kas_card', $isi_card);
                    }
                }
            }

            if ($param == 1) {
                aktifitas_user_transaksi('Accounting', 'menambahkan deposit Kas/Bank', $token);
            } else {
                aktifitas_user_transaksi('Accounting', 'mengubah deposit Kas/Bank', $token);
            }

            echo json_encode(['status' => 1, 'token' => $token]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // mutasi_kas page
    public function mutasi_kas()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $kode_cabang = $this->session->userdata('cabang');

        $parameter = [
            $this->data,
            'judul'         => 'Accounting',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Mutasi',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'piutang_num'   => $this->M_global->getDataResult('piutang', ['kode_cabang' => $this->session->userdata('cabang')]),
            'list_data'     => 'Accounting/mutasi_kas_list/',
            'param1'        => '',
            'saldo_utama'   => $this->db->query("SELECT SUM(sisa) AS saldo FROM kas_utama WHERE kode_cabang = '$kode_cabang'")->row()->saldo,
            'saldo_second'  => $this->db->query("SELECT SUM(sisa) AS saldo FROM kas_second WHERE kode_cabang = '$kode_cabang'")->row()->saldo,
        ];

        $this->template->load('Template/Content', 'Accounting/Mutasi_kas', $parameter);
    }

    // fungsi list mutasi_kas_list
    public function mutasi_kas_list($param1 = 1, $param2 = '')
    {
        // parameter untuk list table
        $table            = 'mutasi_kas';
        $colum            = ['id', 'kode_cabang', 'invoice', 'tgl_mutasi', 'jam_mutasi', 'dari', 'menuju', 'saldo_dari', 'saldo_menuju', 'total', 'kode_user', 'status', 'tgl_confirm', 'jam_confirm', 'user_confirm'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['tgl_mutasi' => 'desc'];
        $kondisi_param2   = '';
        $kondisi_param1   = 'tgl_mutasi';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;
        $confirmed        = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->confirmed;

        // table server side tampung kedalam variable $list
        $dat    = explode("~", $param1);

        if ($dat[0] == 1) {
            $bulan        = date('m');
            $tahun        = date('Y');
            $type         = 1;
        } else {
            $bulan        = date('Y-m-d', strtotime($dat[1]));
            $tahun        = date('Y-m-d', strtotime($dat[2]));
            $type         = 2;
        }

        $list             = $this->M_datatables2->get_datatables($table, $colum, $order_arr, $order, $order2, $kondisi_param1, $type, $bulan, $tahun, $param2, $kondisi_param2);

        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                if ($rd->status > 0) {
                    $upd_diss = 'disabled';
                } else {
                    $upd_diss = _lock_button();
                }
            } else {
                $upd_diss = 'disabled';
            }

            if ($deleted > 0) {
                if ($rd->status > 0) {
                    $del_diss = 'disabled';
                } else {
                    $del_diss = _lock_button();
                }
            } else {
                $del_diss = 'disabled';
            }

            if ($confirmed > 0) {
                $confirm_diss =  _lock_button();
            } else {
                $confirm_diss = 'disabled';
            }

            $kas1 = $this->M_global->getData('kas_bank', ['kode_kas_bank' => $rd->dari]);
            if ($kas1) {
                $dari_kas = $kas1->nama;
            } else {
                $dari_kas = '** KAS UTAMA **';
            }

            $kas2 = $this->M_global->getData('kas_bank', ['kode_kas_bank' => $rd->menuju]);
            if ($kas2) {
                $menuju_kas = $kas2->nama;
            } else {
                $menuju_kas = '** KAS UTAMA **';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->invoice;
            $row[]  = date('d/m/Y', strtotime($rd->tgl_mutasi)) . ' ~ ' . date('H:i:s', strtotime($rd->jam_mutasi));
            $row[]  = $dari_kas . '<br>Rp. ' . number_format($rd->saldo_dari);
            $row[]  = $menuju_kas . '<br>Rp. ' . number_format($rd->saldo_menuju);
            $row[]  = 'Rp. <span class="float-right">' . number_format($rd->total) . '</span>';
            $row[]  = (($rd->status == 0) ? '<span class="badge badge-info">Belum di ACC</span>' : '<span class="badge badge-success">Sudah di ACC</span>');

            if ($rd->status < 1) {
                $accept = '<button type="button" style="margin-bottom: 5px;" class="btn btn-primary" title="ACC" onclick="valided(' . "'" . $rd->invoice . "', 1" . ')" ' . $confirm_diss . '><i class="fa-regular fa-circle-check"></i></button>';
            } else {
                $accept = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="Re-ACC" onclick="valided(' . "'" . $rd->invoice . "', 0" . ')" ' . $confirm_diss . '><i class="fa-solid fa-check-to-slot"></i></button>';
            }

            $row[]  = '<div class="text-center">
                ' . $accept . '
                <button type="button" style="margin-bottom: 5px;" class="btn btn-warning" onclick="ubah(' . "'" . $rd->invoice . "'" . ')" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" style="margin-bottom: 5px;" class="btn btn-danger" onclick="hapus(' . "'" . $rd->invoice . "'" . ')" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
            </div>';

            $data[] = $row;
        }

        // hasil server side
        $output = [
            "draw"            => $_POST['draw'],
            "recordsTotal"    => $this->M_datatables2->count_all($table, $colum, $order_arr, $order, $order2, $kondisi_param1, $type, $bulan, $tahun, $param2, $kondisi_param2),
            "recordsFiltered" => $this->M_datatables2->count_filtered($table, $colum, $order_arr, $order, $order2, $kondisi_param1, $type, $bulan, $tahun, $param2, $kondisi_param2),
            "data"            => $data,
        ];

        // kirimkan ke view
        echo json_encode($output);
    }

    // form form_mutasi_kas page
    public function form_mutasi_kas($param, $param2 = '')
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param == '0') {
            $data_mutasi     = null;
        } else {
            $data_mutasi     = $this->M_global->getData('mutasi_kas', ['invoice' => $param]);
        }

        $parameter = [
            $this->data,
            'judul'             => 'Accounting',
            'nama_apps'         => $web_setting->nama,
            'page'              => 'Kas/Bank Mutasi',
            'web'               => $web_setting,
            'web_version'       => $web_version->version,
            'list_data'         => '',
            'data_mutasi'       => $data_mutasi,
            'param2'            => $param2,
        ];

        $this->template->load('Template/Content', 'Accounting/Form_mutasi_kas', $parameter);
    }

    public function getSaldo($kode)
    {
        $cabang = $this->session->userdata('cabang');

        if ($kode == 'KB00000000') {
            $saldo_awal = $this->M_global->getData('kas_utama', ['kode_cabang' => $cabang]);

            $nama = '** SALDO UTAMA **';
            $saldo = $saldo_awal->sisa;
        } else {
            $kas_utama = $this->M_global->getData('kas_utama', ['kode_kas' => $kode]);

            if ($kas_utama) {
                $saldo = $kas_utama->sisa;
                $nama = 'KAS UTAMA';
            } else {
                $kas = $this->M_global->getData('kas_second', ['kode_kas' => $kode]);
                if ($kas) {
                    $saldo = $kas->sisa;
                    $kas_bank = $this->M_global->getData('kas_bank', ['kode_kas_bank' => $kode]);
                    $nama = $kas_bank->nama;
                } else {
                    $nama = 'Tidak Ada';
                    $saldo = 0;
                }
            }
        }

        if ($saldo > 0) {
            echo json_encode(['status' => 1, 'nama' => $nama, 'saldo' => $saldo]);
        } else {
            echo json_encode(['status' => 0, 'nama' => $nama, 'saldo' => 0]);
        }
    }

    public function mutasi_proses($param)
    {
        $kode_cabang    = $this->session->userdata('cabang');

        if ($param == 1) {
            $invoice    = _invoiceMutasiKas($kode_cabang);
        } else {
            $invoice    = $this->input->post('invoice');
        }

        $tgl_mutasi     = date('Y-m-d', strtotime($this->input->post('tgl_mutasi')));
        $jam_mutasi     = date('H:i:s', strtotime($this->input->post('jam_mutasi')));
        $dari           = $this->input->post('dari');
        $saldo_dari     = str_replace(',', '', $this->input->post('saldo_dari'));
        $menuju         = $this->input->post('menuju');
        $saldo_menuju   = str_replace(',', '', $this->input->post('saldo_menuju'));

        $isi = [
            'kode_cabang'   => $kode_cabang,
            'invoice'       => $invoice,
            'tgl_mutasi'    => $tgl_mutasi,
            'jam_mutasi'    => $jam_mutasi,
            'dari'          => $dari,
            'saldo_dari'    => $saldo_dari,
            'menuju'        => $menuju,
            'saldo_menuju'  => $saldo_menuju,
            'total'         => $saldo_menuju,
            'kode_user'     => $this->session->userdata('kode_user'),
            'status'        => 0,
        ];

        if ($param == 1) {
            $cek = $this->M_global->insertData('mutasi_kas', $isi);

            aktifitas_user_transaksi('Accounting', 'menambahkan Mutasi Kas & Bank', $invoice);
        } else {
            $cek = $this->M_global->updateData('mutasi_kas', $isi, ['invoice' => $invoice]);

            aktifitas_user_transaksi('Accounting', 'mengubah Mutasi Kas & Bank', $invoice);
        }

        if ($cek) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    public function delMutasiKas($invoice)
    {
        $cek = $this->M_global->delData('mutasi_kas', ['invoice' => $invoice]);

        if ($cek) {
            aktifitas_user_transaksi('Accounting', 'menghapus Mutasi Kas & Bank', $invoice);

            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    public function acc_mutasi($invoice, $acc)
    {

        $kode_cabang = $this->session->userdata('cabang');
        // header barang by invoice
        $header = $this->M_global->getData('mutasi_kas', ['invoice' => $invoice, 'kode_cabang' => $kode_cabang]);

        if ($acc == 0) { // jika acc = 0
            aktifitas_user_transaksi('Accounting', 'Reject Mutasi Kas & Bank', $invoice);

            // cek dari
            $cek_dari = $this->M_global->getData('kas_utama', ['kode_kas' => $header->dari, 'kode_cabang' => $kode_cabang]);

            if ($cek_dari) {
                $this->db->query("UPDATE kas_utama SET keluar = keluar - '$header->total', sisa = sisa + '$header->total', last_no = '$invoice' WHERE kode_kas = '$header->dari' AND kode_cabang = '$kode_cabang'");
            } else {
                $this->db->query("UPDATE kas_second SET keluar = keluar - '$header->total', sisa = sisa + '$header->total', last_no = '$invoice' WHERE kode_kas = '$header->dari' AND kode_cabang = '$kode_cabang'");
            }

            // cek menuju
            $cek_menuju = $this->M_global->getData('kas_utama', ['kode_kas' => $header->menuju]);

            if ($cek_menuju) {
                $this->db->query("UPDATE kas_utama SET masuk = masuk - '$header->total', sisa = sisa - '$header->total', last_no = '$invoice' WHERE kode_kas = '$header->menuju' AND kode_cabang = '$kode_cabang'");
            } else {
                $this->db->query("UPDATE kas_second SET masuk = masuk - '$header->total', sisa = sisa - '$header->total', last_no = '$invoice' WHERE kode_kas = '$header->menuju' AND kode_cabang = '$kode_cabang'");
            }

            // update is_valid jadi 0
            $cek = [
                $this->M_global->updateData('mutasi_kas', ['status' => 0, 'tgl_confirm' => null, 'jam_confirm' => null, 'user_confirm' => null], ['invoice' => $invoice]),
            ];
        } else { // selain itu
            aktifitas_user_transaksi('Accounting', 'Confirm Mutasi Kas & Bank', $invoice);

            // cek dari
            $cek_dari = $this->M_global->getData('kas_utama', ['kode_kas' => $header->dari, 'kode_cabang' => $kode_cabang]);

            if ($cek_dari) {
                $this->db->query("UPDATE kas_utama SET keluar = keluar + '$header->total', sisa = sisa - '$header->total', last_no = '$invoice' WHERE kode_kas = '$header->dari' AND kode_cabang = '$kode_cabang'");
            } else {
                $this->db->query("UPDATE kas_second SET keluar = keluar + '$header->total', sisa = sisa - '$header->total', last_no = '$invoice' WHERE kode_kas = '$header->dari' AND kode_cabang = '$kode_cabang'");
            }

            // cek menuju
            $cek_menuju = $this->M_global->getData('kas_utama', ['kode_kas' => $header->menuju]);

            if ($cek_menuju) {
                $this->db->query("UPDATE kas_utama SET masuk = masuk + '$header->total', sisa = sisa + '$header->total', last_no = '$invoice' WHERE kode_kas = '$header->menuju' AND kode_cabang = '$kode_cabang'");
            } else {
                $this->db->query("UPDATE kas_second SET masuk = masuk + '$header->total', sisa = sisa + '$header->total', last_no = '$invoice' WHERE kode_kas = '$header->menuju' AND kode_cabang = '$kode_cabang'");
            }

            // update is_valid jadi 1
            $cek = [
                $this->M_global->updateData('mutasi_kas', ['status' => 1, 'tgl_confirm' => date('Y-m-d'), 'jam_confirm' => date('H:i:s'), 'user_confirm' => $this->session->userdata('kode_user')], ['invoice' => $invoice]),
            ];
        }

        if ($cek) { // jika fungsi cek berjalan
            // kirim status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirim status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    /**
     * Verifikasi BO untuk Kas besar
     * */

    public function verifikasi_bo()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Accounting',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Verifikasi Back Office',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Accounting/bo_list/',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Accounting/Bo', $parameter);
    }

    // fungsi list bo
    public function bo_list($param1 = 1, $param2 = '')
    {
        // parameter untuk list table
        $table            = 'kas_besar';
        $colum            = ['id', 'kode_cabang', 'no_kb', 'tgl', 'jam', 'jenis', 'sumber', 'tujuan', 'keterangan', 'jumlah', 'saldo_awal', 'saldo_akhir', 'referensi', 'status_verifikasi', 'kode_user'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'asc'];
        $kondisi_param2   = '';
        $kondisi_param1   = 'tgl';

        $confirmed        = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->confirmed;

        // table server side tampung kedalam variable $list
        $dat              = explode("~", $param1);
        if ($dat[0] == 1) {
            $bulan        = date('m');
            $tahun        = date('Y');
            $list         = $this->M_datatables2->get_datatables($table, $colum, $order_arr, $order, $order2, $kondisi_param1, 1, $bulan, $tahun, $param2, $kondisi_param2);
        } else {
            $bulan        = date('Y-m-d', strtotime($dat[1]));
            $tahun        = date('Y-m-d', strtotime($dat[2]));
            $list         = $this->M_datatables2->get_datatables($table, $colum, $order_arr, $order, $order2, $kondisi_param1, 2, $bulan, $tahun, $param2, $kondisi_param2);
        }
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($confirmed > 0) {
                if ($rd->status_verifikasi == 0) {
                    $conf_diss = 'onclick="verifikasi_bo(\'' . $rd->no_kb . '\')"';
                } else {
                    $conf_diss = 'disabled';
                }
            } else {
                $conf_diss = 'disabled';
            }

            // Get source name from either member or supplier table
            $sumber = $this->M_global->getData('user', ['kode_user' => $rd->sumber]) ?? $this->M_global->getData('m_supplier', ['kode_supplier' => $rd->sumber]);

            $user = $this->M_global->getData('user', ['kode_user' => $rd->sumber]);
            if ($user) {
                $link = '<a href="' . site_url() . 'Marketing/closing_print/' . $rd->referensi . '" target="_blank">' . $rd->referensi . '</a>';
            } else {
                $link = '<a href="' . site_url() . 'Transaksi/single_print_bin/' . $rd->referensi . '/0/1" target="_blank">' . $rd->referensi . '</a>';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->no_kb;
            $row[]  = '<span>' . date('d/m/Y', strtotime($rd->tgl)) . ' - ' . date('H:i:s', strtotime($rd->jam))  . '</span>';
            $row[]  = ($sumber) ? $sumber->nama : '';
            $row[]  = '<div class="text-center">' . (($rd->jenis == 1) ? '<span class="badge badge-success">Masuk</span>' : '<span class="badge badge-danger">Keluar</span>') . '</div>';
            $row[]  = $rd->keterangan;
            $row[]  = $link;
            $row[]  = '<div class="text-center">' . (($rd->status_verifikasi == 0) ? '<span class="badge badge-info">Menunggu</span>' : '<span class="badge badge-success">Diterima</span>') . '</div>';
            $row[]  = 'Rp.<span class="float-right">' . number_format($rd->jumlah) . '</span>';
            $row[]  = '<div class="text-center">
                <button type="button" class="btn btn-success w-100" ' . $conf_diss . '>Verifikasi</button>
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

    function verif_bo($no_kb)
    {
        $cek = $this->M_global->updateData('kas_besar', ['status_verifikasi' => 1, 'tgl_verif' => date('Y-m-d'), 'jam_verif' => date('H:i:s'), 'user_verif' => $this->session->userdata('kode_user')], ['no_kb' => $no_kb]);

        if ($cek) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // jurnal_pembelian page
    public function jurnal_pembelian()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Jurnal',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Pembelian',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Accounting/jurnal_pembelian_list/',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Accounting/Jurnal/Pembelian', $parameter);
    }

    public function jurnal_pembelian_list($param1 = '')
    {
        // Set default value to current year-month if not provided
        if ($param1 === null) {
            $param1 = date('Y-m');
        }
        // parameter untuk list table
        $table            = 'jurnal_detail';
        $colum            = ['id', 'kode_jurnal', 'kode_coa', 'debit', 'credit', 'keterangan'];
        $order            = 'id';
        $order2           = 'asc';
        $order_arr        = ['id' => 'asc'];
        $kondisi_param1   = "(SELECT tgl_jurnal FROM jurnal_header WHERE jurnal_header.kode_jurnal = jurnal_detail.kode_jurnal)";

        // table server side tampung kedalam variable $list
        $list             = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        // Calculate totals
        $total_debit = 0;
        $total_credit = 0;

        foreach ($list as $rd) {
            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_jurnal;
            $row[]  = '<div class="text-center">' . $this->M_global->getData('jurnal_header', ['kode_jurnal' => $rd->kode_jurnal])->tgl_jurnal . '</div>';
            $row[]  = $rd->kode_coa . ' - ' . $this->M_global->getData('m_coa', ['kode_coa' => $rd->kode_coa])->coa_name;
            $row[]  = '<div>Rp. <span class="float-right">' . number_format($rd->debit) . '</span></div>';
            $row[]  = '<div>Rp. <span class="float-right">' . number_format($rd->credit) . '</span></div>';
            $row[]  = $rd->keterangan;
            $data[] = $row;

            // Add to totals
            $total_debit += $rd->debit;
            $total_credit += $rd->credit;
        }

        // Add footer row with totals
        $footer = [];
        $footer[] = '';
        $footer[] = '<strong>TOTAL</strong>';
        $footer[] = '';
        $footer[] = '';
        $footer[] = '<div><strong>Rp. <span class="float-right">' . number_format($total_debit) . '</span></strong></div>';
        $footer[] = '<div><strong>Rp. <span class="float-right">' . number_format($total_credit) . '</span></strong></div>';
        $footer[] = '<div class="text-center">' . (($total_debit == $total_credit)
            ? '<span class="font-weight-bold text-success" style="background-color: #d4edda; padding: 5px 10px; border-radius: 4px;">BALANCE</span>'
            : '<span class="font-weight-bold text-danger" style="background-color: #f8d7da; padding: 5px 10px; border-radius: 4px;">TIDAK BALANCE</span>') . '</div>';
        $data[] = $footer;

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
}
