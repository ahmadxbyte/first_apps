<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Marketing extends CI_Controller
{
    // variable open public untuk controller Home
    public $data;

    public function __construct()
    {
        parent::__construct();
        // load model M_auth
        $this->load->model("M_auth");

        if (!empty($this->session->userdata("email"))) { // jika session email masih ada

            $id_menu = $this->M_global->getData('m_menu', ['url' => 'Marketing'])->id;

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
                    'menu'      => 'Marketing',
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

    // promo page
    public function promo()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Marketing',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Promo',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Marketing/promo_list/',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Marketing/Promo', $parameter);
    }

    // form promo page
    public function form_promo($param)
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param != '0') {
            $promo = $this->M_global->getData('m_promo', ['kode_promo' => $param]);
        } else {
            $promo = null;
        }

        $parameter = [
            $this->data,
            'judul'         => 'Master',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Barang',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => '',
            'promo'         => $promo,
        ];

        $this->template->load('Template/Content', 'Marketing/Form_promo', $parameter);
    }

    // cek promo
    public function cekProm()
    {
        // ambil nama inputan
        $nama = $this->input->post('nama');

        // cek nama pada table barang
        $cek = $this->M_global->jumDataRow('m_promo', ['nama' => $nama]);

        if ($cek < 1) { // jika tidak ada/ kurang dari 1
            // kirimkan status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi proses ismpan/update
    public function promo_proses($param)
    {
        $kode_cabang = $this->session->userdata('cabang');
        // header
        if ($param == 1) { // jika param = 1
            $kode_promo = _code_promo();
        } else {
            $kode_promo = $this->input->post('kodePromo');
        }
        $nama         = $this->input->post('nama');
        $tgl_mulai    = $this->input->post('tgl_mulai');
        $tgl_selesai  = $this->input->post('tgl_selesai');
        $keterangan   = $this->input->post('keterangan');

        $min_buy      = str_replace(',', '', $this->input->post('min_buy'));
        $discpr       = str_replace(',', '', $this->input->post('discpr'));

        $isi = [
            'kode_cabang'   => $kode_cabang,
            'kode_promo'    => $kode_promo,
            'nama'          => $nama,
            'tgl_mulai'     => $tgl_mulai,
            'tgl_selesai'   => $tgl_selesai,
            'keterangan'    => $keterangan,
            'min_buy'       => $min_buy,
            'discpr'        => $discpr,
        ];

        if ($param == 1) {
            $cek = $this->M_global->insertData('m_promo', $isi);
        } else {
            $cek = $this->M_global->updateData('m_promo', $isi, ['kode_promo' => $kode_promo]);
        }

        if ($cek) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi list promo
    public function promo_list($param1 = 1, $param2 = '')
    {
        // parameter untuk list table
        $table            = 'm_promo';
        $colum            = ['id', 'kode_promo', 'nama', 'tgl_mulai', 'tgl_selesai', 'keterangan', 'min_buy', 'discpr'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'asc'];
        $kondisi_param2   = '';
        $kondisi_param1   = 'tgl_mulai';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;
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

        $now              = date('Y-m-d');

        // loop $list
        foreach ($list as $rd) {
            $cek_guna = $this->M_global->jumDataRow('pembayaran', ['kode_promo' => $rd->kode_promo]);

            if ($updated > 0) {
                if ($rd->tgl_selesai < $now) {
                    $upd_diss = 'disabled';
                } else {
                    if ($cek_guna < 1) {
                        $upd_diss = '';
                    } else {
                        $upd_diss = 'disabled';
                    }
                }
            } else {
                $upd_diss = 'disabled';
            }

            if ($deleted > 0) {
                if ($rd->tgl_selesai < $now) {
                    $del_diss = 'disabled';
                } else {
                    if ($cek_guna < 1) {
                        $del_diss = '';
                    } else {
                        $del_diss = 'disabled';
                    }
                }
            } else {
                $del_diss = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = 'Mulai: <br><span class="float-right">' . date('d/m/Y', strtotime($rd->tgl_mulai)) . '</span><br>Berakhir: <br><span class="float-right">' . date('d/m/Y', strtotime($rd->tgl_selesai)) . '</span>';
            $row[]  = $rd->nama;
            $row[]  = $rd->keterangan;
            $row[]  = 'Min Pembelian: <br><span class="float-right">Rp.' . number_format($rd->min_buy) . '</span>';
            $row[]  = '<span class="float-right">' . number_format($rd->discpr) . ' %</span>';
            $row[]  = (($rd->tgl_selesai < $now) ? '<span class="badge badge-danger">Promo Berakhir</span>' : '<span class="badge badge-success">Promo Berjalan</span>');
            $row[]  = '<div class="text-center">
                <button type="button" style="margin-bottom: 5px;" class="btn btn-warning" title="Ubah" onclick="ubah(' . "'" . $rd->kode_promo . "'" . ')" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" style="margin-bottom: 5px;" class="btn btn-danger" title="Hapus" onclick="hapus(' . "'" . $rd->kode_promo . "'" . ')" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi hapus promo
    public function delPromo($kode_promo)
    {
        // jalankan fungsi hapus promo berdasarkan kode_promo
        $cek = $this->M_global->delData('m_promo', ['kode_promo' => $kode_promo]);

        if ($cek) { // jika fungsi berjalan
            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    /**
     * Closing kasir
     */

    // closing page
    public function closing_kasir()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Marketing',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Closing Kasir',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Marketing/closing_list/',
            'param1'        => '',
            'm_bank'        => $this->M_global->getResult('m_bank'),
            'tipe_bank'     => $this->M_global->getResult('tipe_bank'),
        ];

        $this->template->load('Template/Content', 'Marketing/Closing', $parameter);
    }

    // fungsi list closing
    public function closing_list($param1 = 1, $param2 = '')
    {
        // parameter untuk list table
        $table            = 'closing_kasir';
        $colum            = ['id', 'no_closing', 'kode_cabang', 'user_closing', 'tgl_closing', 'jam_closing', 'tunai', 'nontunai'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'asc'];
        $kondisi_param2   = '';
        $kondisi_param1   = 'tgl_closing';

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
            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->no_closing;
            $row[]  = 'Closing: <span class="float-right">' . date('d/m/Y', strtotime($rd->tgl_closing)) . ' - ' . date('H:i:s', strtotime($rd->jam_closing))  . '</span>';
            $row[]  = $rd->user_closing . ' / ' . $this->M_global->getData('user', ['kode_user' => $rd->user_closing])->nama;
            $row[]  = 'Rp.<span class="float-right">' . number_format($rd->tunai) . '</span>';
            $row[]  = 'Rp.<span class="float-right">' . number_format($rd->nontunai) . '</span>';
            $row[]  = '<div class="text-center">
                <a type="button" target="_blank" style="margin-bottom: 5px;" title="cetak" class="btn btn-warning" href="' . site_url("Marketing/closing_print/") . $rd->no_closing . '"><i class="fa-solid fa-print"></i></a>
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

    public function closing_proses()
    {
        $user_closing   = $this->session->userdata('kode_user');
        $jam_closing    = date('H:i:s');
        $tgl_closing    = date('Y-m-d');
        $tunai          = $this->input->post('tunai');
        $nontunai       = $this->input->post('nontunai');

        $pembayaran     = $this->M_global->getDataResult('pembayaran', ['kode_user' => $user_closing, 'tgl_pembayaran' => $tgl_closing]);

        $no_closing     = master_kode('closing_kasir', 10, 'CC', '-', $this->session->userdata('init_cabang'));

        $data = [
            'user_closing'  => $user_closing,
            'tgl_closing'   => $tgl_closing,
            'jam_closing'   => $jam_closing,
            'tunai'         => $tunai,
            'nontunai'      => $nontunai,
            'no_closing'    => $no_closing,
            'kode_cabang'   => $this->session->userdata('cabang'),
            'status'        => 1,
        ];

        $result = $this->M_global->insertData('closing_kasir', $data);

        if ($result) {
            foreach ($pembayaran as $p) {
                $detail = [
                    'no_closing'        => $no_closing,
                    'token_pembayaran'  => $p->token_pembayaran,
                ];

                $this->M_global->insertData('closing_detail', $detail);
            }

            $closing_data = $this->M_global->getData('closing_kasir', ['no_closing' => $no_closing]);

            $kas_besar = $this->db->select('saldo_akhir')
                ->order_by('id', 'DESC')
                ->limit(1)
                ->get('kas_besar');

            $saldo_terakhir = $kas_besar->num_rows() > 0 ? $kas_besar->row()->saldo_akhir : 0;

            $jumlah_masuk   = $closing_data->tunai;
            $saldo_awal     = $saldo_terakhir;
            $saldo_akhir    = $saldo_awal + $jumlah_masuk;

            $data_kas_besar = [
                'tgl'               => date('Y-m-d'),
                'kode_cabang'       => $this->session->userdata('cabang'),
                'jam'               => date('H:i:s'),
                'no_kb'             => master_kode('Kas Besar', 10, 'KB', '-', $this->session->userdata('init_cabang'), '-'),
                'jenis'             => 1, // 1 masuk, 2 keluar, 3 transfer, 4 penyesuaian, 5 dll
                'sumber'            => $closing_data->user_closing,
                'tujuan'            => 'Kas Besar',
                'jumlah'            => $jumlah_masuk,
                'saldo_awal'        => $saldo_awal,
                'saldo_akhir'       => $saldo_akhir,
                'referensi'         => $no_closing,
                'keterangan'        => 'Setoran dari closing kasir',
                'status_verifikasi' => 0, // bisa diubah saat BO verifikasi, 0 pending, 1 diterima, 2 ditolak
                'kode_user'         => $this->session->userdata('kode_user'),
            ];

            $cek = [
                $this->M_global->insertData('kas_besar', $data_kas_besar),
            ];

            if ($cek) {
                echo json_encode(['status' => 1]);
            } else {
                $this->M_global->delData('closing_kasir', $data);
                $this->M_global->delData('closing_detail', $data);

                echo json_encode(['status' => 0]);
            }
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    public function closing_print($no_closing)
    {
        $kode_cabang          = $this->session->userdata('cabang');
        $web_setting          = $this->M_global->getData('web_setting', ['id' => 1]);

        $position             = 'L'; // cek posisi l/p

        // body cetakan
        $body                 = '';
        $body                 .= '<br><br>';

        $closing              = $this->M_global->getData('closing_kasir', ['no_closing' => $no_closing]);
        $detail               = $this->M_global->getDataResult('closing_detail', ['no_closing' => $no_closing]);

        $judul                = 'Closing ~ ' . $no_closing;
        $filename             = $judul;

        $body .= '<table style="width: 100%; font-size: 14px;" cellpadding="2px" autosize="1">
            <tr>
                <td>(Closing: ' . date('d/m/Y', strtotime($closing->tgl_closing)) . ' - ' . date('H:i:s', strtotime($closing->jam_closing)) . ')</td>
                <td colspan="2" style="text-align: right; color: white;"><span style="border: 1px solid #0e1d2e; background-color: #0e1d2e;">NO: #' . $no_closing . '</span></td>
            </tr>
        </table>';

        $body .= '<br>';

        $tipe_bank = $this->M_global->getResult('tipe_bank');

        $body .= '<table style="width: 100%; font-size: 14px;" autosize="1" cellpadding="5px">';
        $body .= '<tr>
                <th rowspan="2" style="width: 5%; border: 1px solid black; background-color: #0e1d2e; color: white;">#</th>
                <th rowspan="2" style="width: 15%; border: 1px solid black; background-color: #0e1d2e; color: white;">Kwitansi</th>
                <th rowspan="2" style="width: 20%; border: 1px solid black; background-color: #0e1d2e; color: white;">Member</th>
                <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">UM Keluar</th>
                <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Cash</th>
                <th colspan="' . count($tipe_bank) . '" style="width: ' . count($tipe_bank) . '0%; border: 1px solid black; background-color: #0e1d2e; color: white;">Card</th>
                <th colspan="3" style="width: 30%; border: 1px solid black; background-color: #0e1d2e; color: white;">Promo</th>
                <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Jumlah Bayar</th>
                <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Jual</th>
                <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Tindakan</th>
                <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Total</th>
                <th colspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Kembalian</th>
            </tr>';

        $body .= '<tr>';

        foreach ($tipe_bank as $tb) {
            $body .= '<th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">' . $tb->keterangan . '</th>';
        }

        $body .= '<th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Nama</th>';
        $body .= '<th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Potongan (%)</th>';
        $body .= '<th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Subtotal (Rp)</th>';
        $body .= '<th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">UM</th>';
        $body .= '<th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Pasien</th>';

        $body .= '</tr>';

        $body .= '</thead>';

        $body .= '<tbody>';

        if (count($detail) < 1) {
            $body .= '<tr>
                <td colspan="17" style="border: 1px solid black; text-align: center;">Data Tidak Tersedia</td>
            </tr>';
        } else {
            $no = 1;
            foreach ($detail as $d) {
                $dd = $this->M_global->getData('pembayaran', ['token_pembayaran' => $d->token_pembayaran]);

                if ($dd) {
                    $member = $this->M_global->getData('member', ['kode_member' => $dd->kode_member])->nama;
                } else {
                    $member = 'Masyarakat Umum';
                }

                $total        = number_format($dd->total);
                $cash         = number_format($dd->cash);
                $result       = number_format($dd->total - $dd->kembalian);
                $um           = number_format($dd->um_keluar);
                $umm          = number_format($dd->um_masuk);
                $kembalian    = number_format(($dd->cek_um == 1) ? 0 : $dd->kembalian);

                $body .= '<tr>';

                $body .= '<td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                    <td style="border: 1px solid black;">' . $dd->invoice . '</td>
                    <td style="border: 1px solid black;">' . $dd->kode_member . ' ~ ' . $member . '</td>
                    <td style="border: 1px solid black; text-align: right;">' . $um . '</td>
                    <td style="border: 1px solid black; text-align: right;">' . $cash . '</td>';

                foreach ($tipe_bank as $tb) {
                    $card_detail = $this->M_global->getDataResult('bayar_card_detail', ['token_pembayaran' => $d->token_pembayaran, 'kode_tipe' => $tb->kode_tipe]);
                    if (count($card_detail) > 0) {
                        foreach ($card_detail as $cd) {
                            $jumlah = number_format($cd->jumlah);
                            $body .= '<td style="border: 1px solid black; text-align: right;">' . $jumlah . '</td>';
                        }
                    } else {
                        $body .= '<td style="border: 1px solid black; text-align: right;">0.00</td>';
                    }
                }

                $promo            = $this->M_global->getData('m_promo', ['kode_promo' => $dd->kode_promo]);
                $total_jual       = $d->jual;
                $tindakan         = $d->paket + $d->single;

                if ($promo) {
                    $nama_promo     = $promo->nama;
                    $potongan_promo = $promo->discpr;
                    $subtotal_promo = ($total_jual * ($promo->discpr / 100));
                } else {
                    $nama_promo     = '';
                    $potongan_promo = 0;
                    $subtotal_promo = 0;
                }

                $tjual    = number_format($total_jual);
                $pprom    = number_format($potongan_promo);
                $sprom    = number_format($subtotal_promo);
                $tindakan = number_format($tindakan);

                $body .= '<td style="border: 1px solid black; text-align: right;">' . $nama_promo . '</td>';
                $body .= '<td style="border: 1px solid black; text-align: right;">' . $pprom . '</td>';
                $body .= '<td style="border: 1px solid black; text-align: right;">' . $sprom . '</td>';
                $body .= '<td style="border: 1px solid black; text-align: right;">' . $total . '</td>';
                $body .= '<td style="border: 1px solid black; text-align: right;">' . $tjual . '</td>';
                $body .= '<td style="border: 1px solid black; text-align: right;">' . $tindakan . '</td>';
                $body .= '<td style="border: 1px solid black; text-align: right;">' . $result . '</td>';
                $body .= '<td style="border: 1px solid black; text-align: right;">' . $umm . '</td>';
                $body .= '<td style="border: 1px solid black; text-align: right;">' . $kembalian . '</td>';


                $body .= '</tr>';

                $no++;
            }
        }

        $body .= '</tbody>';
        $body .= '</table>';

        cetak_pdf($judul, $body, 1, $position, $filename, $web_setting);
    }
}
