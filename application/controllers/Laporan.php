<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Laporan extends CI_Controller
{
    // variable open public untuk controller Home
    public $data;

    public function __construct()
    {
        parent::__construct();
        // load model M_auth
        $this->load->model("M_auth");

        if (!empty($this->session->userdata("email"))) { // jika session email masih ada

            $id_menu = $this->M_global2->getData('m_menu', ['url' => 'Laporan'])->id;

            // ambil isi data berdasarkan email session dari table user, kemudian tampung ke variable $user
            $user = $this->M_global2->getData("user", ["email" => $this->session->userdata("email")]);

            $cek_akses_menu = $this->M_global2->getData('akses_menu', ['id_menu' => $id_menu, 'kode_role' => $user->kode_role]);
            if ($cek_akses_menu) {
                // tampung data ke variable data public
                $this->data = [
                    'nama'      => $user->nama,
                    'email'     => $user->email,
                    'kode_role' => $user->kode_role,
                    'actived'   => $user->actived,
                    'foto'      => $user->foto,
                    'shift'     => $this->session->userdata('shift'),
                    'menu'      => 'Laporan',
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

    /*
    * Pembelian Laporan
    **/

    // index page
    public function index()
    {
        // website config
        $web_setting = $this->M_global2->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global2->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Laporan',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Laporan',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => '',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Laporan/Data', $parameter);
    }

    function report_print($param)
    {
        // param website
        $web_setting    = $this->M_global2->getData('web_setting', ['id' => 1]);

        $position       = 'L'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $pencetak       = $this->M_global2->getData('user', ['kode_user' => $this->session->userdata('kode_user')])->nama;
        $laporan        = $this->input->get('laporan');
        $dari           = $this->input->get('dari');
        $sampai         = $this->input->get('sampai');
        $kode_supplier  = $this->input->get('kode_supplier');
        $kode_gudang    = $this->input->get('kode_gudang');
        $kode_barang    = $this->input->get('kode_barang');
        $kode_poli      = $this->input->get('kode_poli');
        $kode_user      = $this->input->get('kode_user');
        $kode_cabang    = $this->session->userdata('cabang');

        $breaktable     = '<br>';

        // Pendaftaran
        if ($laporan == 0) {
            $file = 'Laporan Pendaftaran';

            // isi body
            if ($kode_poli == '') {
                $header     = $this->db->query("SELECT p.* FROM pendaftaran p WHERE p.kode_cabang = '$kode_cabang' AND tgl_daftar >= '$dari' AND tgl_daftar <= '$sampai'")->result();
            } else {
                $header     = $this->db->query("SELECT p.* FROM pendaftaran p WHERE p.kode_cabang = '$kode_cabang' AND tgl_daftar >= '$dari' AND tgl_daftar <= '$sampai' AND kode_poli = '$kode_poli'")->result();
            }

            // body header
            $body .= '<table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="width: 10%;">Perihal</td>
                    <td style="width: 2%;"> : </td>
                    <td colspan="2">' . $file . '</td>
                </tr>
                <tr>
                    <td style="width: 10%;">Periode</td>
                    <td style="width: 2%;"> : </td>
                    <td style="width: 38%;">' . date('d/m/Y', strtotime($dari)) . ' ~ ' . date('d/m/Y', strtotime($sampai)) . '</td>
                    <td style="width: 50%; text-align: right;">Pencetak : ' . $pencetak . '</td>
                </tr>
            </table>';

            $body .= $breaktable;

            $body .= '<table style="width: 100%; font-size: 14px;" autosize="2" cellpadding="5px">';
            $body .= '<thead>
                <tr>
                    <th style="width: 5%; border: 1px solid black; background-color: #0e1d2e; color: white;">#</th>
                    <th style="width: 20%; border: 1px solid black; background-color: #0e1d2e; color: white;">No Transaksi</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Tgl/Jam Daftar</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Tgl/Jam Keluar</th>
                    <th style="width: 20%; border: 1px solid black; background-color: #0e1d2e; color: white;">Member</th>
                    <th style="width: 7%; border: 1px solid black; background-color: #0e1d2e; color: white;">Poli</th>
                    <th style="width: 13%; border: 1px solid black; background-color: #0e1d2e; color: white;">Dokter</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">User</th>
                    <th style="width: 5%; border: 1px solid black; background-color: #0e1d2e; color: white;">Shift</th>
                </tr>
            </thead>';

            $body .= '<tbody>';

            if ($header) {
                $no = 1;
                foreach ($header as $h) {
                    $member = $this->M_global2->getData('member', ['kode_member' => $h->kode_member]);
                    $poli = $this->M_global2->getData('m_poli', ['kode_poli' => $h->kode_poli]);
                    $dokter = $this->M_global2->getData('dokter', ['kode_dokter' => $h->kode_dokter]);

                    $data_member = $member->nama . ' ~ ' . ($member->jkel == 'W' ? 'Wanita' : 'Pria') . '<br>' . hitung_umur($member->tgl_lahir) . ')';

                    $body .= '<tr>
                        <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                        <td style="border: 1px solid black;">' . $h->no_trx . '</td>
                        <td style="border: 1px solid black;">' . date('d-m-Y', strtotime($h->tgl_daftar)) . '<br>' . date('H:i:s', strtotime($h->jam_daftar)) . '</td>
                        <td style="border: 1px solid black;">' . ($h->tgl_keluar == null ? 'Proses' : (date('d-m-Y', strtotime($h->tgl_keluar)) . '<br>' . date('H:i:s', strtotime($h->jam_keluar)))) . '</td>
                        <td style="border: 1px solid black;">' . $data_member . '</td>
                        <td style="border: 1px solid black;">' . $poli->keterangan . '</td>
                        <td style="border: 1px solid black;">Dr. ' . $dokter->nama . '</td>
                        <td style="border: 1px solid black;">' . $this->M_global2->getData('user', ['kode_user' => $h->kode_user])->nama . '</td>
                        <td style="border: 1px solid black; text-align: center;">' . $h->shift . '</td>
                    </tr>';

                    $no++;
                }
            } else {
                $body .= '<tr>
                    <td colspan="9" style="border: 1px solid black; font-weight: bold; text-align: center;">Tidak Ada Transaksi</td>
                </tr>';
            }


            $body .= '</tbody>';

            $body .= '</table>';
        } else if ($laporan == '0.1') {
            $file = 'Laporan Pendaftaran Tanpa Paket';

            // isi body
            if ($kode_poli == '') {
                $header     = $this->db->query("SELECT p.* FROM pendaftaran p WHERE p.kode_cabang = '$kode_cabang' AND tgl_daftar >= '$dari' AND tgl_daftar <= '$sampai' AND no_trx NOT IN (SELECT no_trx FROM tarif_paket_pasien)")->result();
            } else {
                $header     = $this->db->query("SELECT p.* FROM pendaftaran p WHERE p.kode_cabang = '$kode_cabang' AND tgl_daftar >= '$dari' AND tgl_daftar <= '$sampai' AND no_trx NOT IN (SELECT no_trx FROM tarif_paket_pasien) AND kode_poli = '$kode_poli'")->result();
            }

            // body header
            $body .= '<table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="width: 10%;">Perihal</td>
                    <td style="width: 2%;"> : </td>
                    <td colspan="2">' . $file . '</td>
                </tr>
                <tr>
                    <td style="width: 10%;">Periode</td>
                    <td style="width: 2%;"> : </td>
                    <td style="width: 38%;">' . date('d/m/Y', strtotime($dari)) . ' ~ ' . date('d/m/Y', strtotime($sampai)) . '</td>
                    <td style="width: 50%; text-align: right;">Pencetak : ' . $pencetak . '</td>
                </tr>
            </table>';

            $body .= $breaktable;

            $body .= '<table style="width: 100%; font-size: 14px;" autosize="2" cellpadding="5px">';
            $body .= '<thead>
                <tr>
                    <th style="width: 5%; border: 1px solid black; background-color: #0e1d2e; color: white;">#</th>
                    <th style="width: 20%; border: 1px solid black; background-color: #0e1d2e; color: white;">No Transaksi</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Tgl/Jam Daftar</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Tgl/Jam Keluar</th>
                    <th style="width: 20%; border: 1px solid black; background-color: #0e1d2e; color: white;">Member</th>
                    <th style="width: 7%; border: 1px solid black; background-color: #0e1d2e; color: white;">Poli</th>
                    <th style="width: 13%; border: 1px solid black; background-color: #0e1d2e; color: white;">Dokter</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">User</th>
                    <th style="width: 5%; border: 1px solid black; background-color: #0e1d2e; color: white;">Shift</th>
                </tr>
            </thead>';

            $body .= '<tbody>';

            if ($header) {
                $no = 1;
                foreach ($header as $h) {
                    $member = $this->M_global2->getData('member', ['kode_member' => $h->kode_member]);
                    $poli = $this->M_global2->getData('m_poli', ['kode_poli' => $h->kode_poli]);
                    $dokter = $this->M_global2->getData('dokter', ['kode_dokter' => $h->kode_dokter]);

                    $data_member = $member->nama . ' ~ ' . ($member->jkel == 'W' ? 'Wanita' : 'Pria') . '<br>' . hitung_umur($member->tgl_lahir) . ')';

                    $body .= '<tr>
                        <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                        <td style="border: 1px solid black;">' . $h->no_trx . '</td>
                        <td style="border: 1px solid black;">' . date('d-m-Y', strtotime($h->tgl_daftar)) . '<br>' . date('H:i:s', strtotime($h->jam_daftar)) . '</td>
                        <td style="border: 1px solid black;">' . ($h->tgl_keluar == null ? 'Proses' : (date('d-m-Y', strtotime($h->tgl_keluar)) . '<br>' . date('H:i:s', strtotime($h->jam_keluar)))) . '</td>
                        <td style="border: 1px solid black;">' . $data_member . '</td>
                        <td style="border: 1px solid black;">' . $poli->keterangan . '</td>
                        <td style="border: 1px solid black;">Dr. ' . $dokter->nama . '</td>
                        <td style="border: 1px solid black;">' . $this->M_global2->getData('user', ['kode_user' => $h->kode_user])->nama . '</td>
                        <td style="border: 1px solid black; text-align: center;">' . $h->shift . '</td>
                    </tr>';

                    $no++;
                }
            } else {
                $body .= '<tr>
                    <td colspan="9" style="border: 1px solid black; font-weight: bold; text-align: center;">Tidak Ada Transaksi</td>
                </tr>';
            }


            $body .= '</tbody>';

            $body .= '</table>';
        } else if ($laporan == '0.2') {
            $file = 'Laporan Pendaftaran Dengan Paket';

            // isi body
            if ($kode_poli == '') {
                $header     = $this->db->query("SELECT p.* FROM first_apps_migrate.pendaftaran p WHERE p.kode_cabang = '$kode_cabang' AND tgl_daftar >= '$dari' AND tgl_daftar <= '$sampai' AND no_trx IN (SELECT no_trx FROM tarif_paket_pasien)")->result();
            } else {
                $header     = $this->db->query("SELECT p.* FROM first_apps_migrate.pendaftaran p WHERE p.kode_cabang = '$kode_cabang' AND tgl_daftar >= '$dari' AND tgl_daftar <= '$sampai' AND no_trx IN (SELECT no_trx FROM tarif_paket_pasien) AND kode_poli = '$kode_poli'")->result();
            }

            // body header
            $body .= '<table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="width: 10%;">Perihal</td>
                    <td style="width: 2%;"> : </td>
                    <td colspan="2">' . $file . '</td>
                </tr>
                <tr>
                    <td style="width: 10%;">Periode</td>
                    <td style="width: 2%;"> : </td>
                    <td style="width: 38%;">' . date('d/m/Y', strtotime($dari)) . ' ~ ' . date('d/m/Y', strtotime($sampai)) . '</td>
                    <td style="width: 50%; text-align: right;">Pencetak : ' . $pencetak . '</td>
                </tr>
            </table>';

            $body .= $breaktable;

            $body .= '<table style="width: 100%; font-size: 14px;" autosize="2" cellpadding="5px">';
            $body .= '<thead>
                <tr>
                    <th style="width: 5%; border: 1px solid black; background-color: #0e1d2e; color: white;">#</th>
                    <th style="width: 20%; border: 1px solid black; background-color: #0e1d2e; color: white;">No Transaksi</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Tgl/Jam Daftar</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Tgl/Jam Keluar</th>
                    <th style="width: 20%; border: 1px solid black; background-color: #0e1d2e; color: white;">Member</th>
                    <th style="width: 7%; border: 1px solid black; background-color: #0e1d2e; color: white;">Poli</th>
                    <th style="width: 13%; border: 1px solid black; background-color: #0e1d2e; color: white;">Dokter</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">User</th>
                    <th style="width: 5%; border: 1px solid black; background-color: #0e1d2e; color: white;">Shift</th>
                </tr>
            </thead>';

            $body .= '<tbody>';

            if ($header) {
                $no = 1;
                foreach ($header as $h) {
                    $member = $this->M_global2->getData('member', ['kode_member' => $h->kode_member]);
                    $poli = $this->M_global2->getData('m_poli', ['kode_poli' => $h->kode_poli]);
                    $dokter = $this->M_global2->getData('dokter', ['kode_dokter' => $h->kode_dokter]);

                    $data_member = $member->nama . ' ~ ' . ($member->jkel == 'W' ? 'Wanita' : 'Pria') . '<br>' . hitung_umur($member->tgl_lahir) . ')';

                    $body .= '<tr>
                        <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                        <td style="border: 1px solid black;">' . $h->no_trx . '</td>
                        <td style="border: 1px solid black;">' . date('d-m-Y', strtotime($h->tgl_daftar)) . '<br>' . date('H:i:s', strtotime($h->jam_daftar)) . '</td>
                        <td style="border: 1px solid black;">' . ($h->tgl_keluar == null ? 'Proses' : (date('d-m-Y', strtotime($h->tgl_keluar)) . '<br>' . date('H:i:s', strtotime($h->jam_keluar)))) . '</td>
                        <td style="border: 1px solid black;">' . $data_member . '</td>
                        <td style="border: 1px solid black;">' . $poli->keterangan . '</td>
                        <td style="border: 1px solid black;">Dr. ' . $dokter->nama . '</td>
                        <td style="border: 1px solid black;">' . $this->M_global2->getData('user', ['kode_user' => $h->kode_user])->nama . '</td>
                        <td style="border: 1px solid black; text-align: center;">' . $h->shift . '</td>
                    </tr>';

                    $no++;
                }
            } else {
                $body .= '<tr>
                    <td colspan="9" style="border: 1px solid black; font-weight: bold; text-align: center;">Tidak Ada Transaksi</td>
                </tr>';
            }


            $body .= '</tbody>';

            $body .= '</table>';
        } else if ($laporan == 1) {
            $file = 'Laporan Pembelian';

            // isi body
            if ($kode_gudang == '' && $kode_supplier == '') {
                $header     = $this->M_global2->getDataResult('barang_in_header', ['tgl_beli >= ' => $dari, 'tgl_beli <= ' => $sampai, 'is_valid' => 1, 'kode_cabang' => $kode_cabang]);
            } else {
                if ($kode_gudang == '' && $kode_supplier != '') {
                    $header     = $this->M_global2->getDataResult('barang_in_header', ['tgl_beli >= ' => $dari, 'tgl_beli <= ' => $sampai, 'is_valid' => 1, 'kode_supplier' => $kode_supplier, 'kode_cabang' => $kode_cabang]);
                } else if ($kode_gudang != '' && $kode_supplier == '') {
                    $header     = $this->M_global2->getDataResult('barang_in_header', ['tgl_beli >= ' => $dari, 'tgl_beli <= ' => $sampai, 'is_valid' => 1, 'kode_gudang' => $kode_gudang, 'kode_cabang' => $kode_cabang]);
                } else {
                    $header     = $this->M_global2->getDataResult('barang_in_header', ['tgl_beli >= ' => $dari, 'tgl_beli <= ' => $sampai, 'is_valid' => 1, 'kode_supplier' => $kode_supplier, 'kode_gudang' => $kode_gudang, 'kode_cabang' => $kode_cabang]);
                }
            }

            // body header
            $body .= '<table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="width: 10%;">Perihal</td>
                    <td style="width: 2%;"> : </td>
                    <td colspan="2">' . $file . '</td>
                </tr>
                <tr>
                    <td style="width: 10%;">Periode</td>
                    <td style="width: 2%;"> : </td>
                    <td style="width: 38%;">' . date('d/m/Y', strtotime($dari)) . ' ~ ' . date('d/m/Y', strtotime($sampai)) . '</td>
                    <td style="width: 50%; text-align: right;">Pencetak : ' . $pencetak . '</td>
                </tr>
            </table>';

            $body .= $breaktable;

            $body .= '<table style="width: 100%; font-size: 14px;" autosize="2" cellpadding="5px">';
            $body .= '<thead>
                <tr>
                    <th style="width: 5%; border: 1px solid black; background-color: #0e1d2e; color: white;">#</th>
                    <th style="width: 15%; border: 1px solid black; background-color: #0e1d2e; color: white;">Invoice</th>
                    <th style="width: 15%; border: 1px solid black; background-color: #0e1d2e; color: white;">Invoice PO</th>
                    <th style="width: 7%; border: 1px solid black; background-color: #0e1d2e; color: white;">Tgl/Jam Beli</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Pemasok</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Gudang</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Surat Jalan</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">No Faktur</th>
                    <th style="width: 8%; border: 1px solid black; background-color: #0e1d2e; color: white;">User</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Total</th>
                </tr>
            </thead>';

            $body .= '<tbody>';

            if ($header) {
                $no = 1;
                foreach ($header as $h) {
                    if ($param == 1) {
                        $total = number_format($h->total);
                    } else {
                        $total = ceil($h->total);
                    }
                    $body .= '<tr>
                        <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                        <td style="border: 1px solid black;">' . $h->invoice . '</td>
                        <td style="border: 1px solid black;">' . (($h->invoice_po == '' || $h->invoice_po == null) ? 'Non-PO' : $h->invoice_po) . '</td>
                        <td style="border: 1px solid black;">' . date('d-m-Y', strtotime($h->tgl_beli)) . '<br>' . date('H:i:s', strtotime($h->jam_beli)) . '</td>
                        <td style="border: 1px solid black;">' . $this->M_global2->getData('m_supplier', ['kode_supplier' => $h->kode_supplier])->nama . '</td>
                        <td style="border: 1px solid black;">' . $this->M_global2->getData('m_gudang', ['kode_gudang' => $h->kode_gudang])->nama . '</td>
                        <td style="border: 1px solid black;">' . $h->surat_jalan . '</td>
                        <td style="border: 1px solid black;">' . $h->no_faktur . '</td>
                        <td style="border: 1px solid black;">' . $this->M_global2->getData('user', ['kode_user' => $h->kode_user])->nama . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . $total . '</td>
                    </tr>';

                    $no++;
                }
            } else {
                $body .= '<tr>
                    <td colspan="10" style="border: 1px solid black; font-weight: bold; text-align: center;">Tidak Ada Transaksi</td>
                </tr>';
            }


            $body .= '</tbody>';

            $body .= '</table>';
        } else if ($laporan == '1.1') {
            $file       = 'Laporan Pembelian Detail';
            $position   = 'P'; // cek posisi l/p

            // isi body
            if ($kode_gudang == '' && $kode_supplier == '') {
                $header     = $this->M_global2->getDataResult('barang_in_header', ['tgl_beli >= ' => $dari, 'tgl_beli <= ' => $sampai, 'is_valid' => 1, 'kode_cabang' => $kode_cabang]);
            } else {
                if ($kode_gudang == '' && $kode_supplier != '') {
                    $header     = $this->M_global2->getDataResult('barang_in_header', ['tgl_beli >= ' => $dari, 'tgl_beli <= ' => $sampai, 'is_valid' => 1, 'kode_supplier' => $kode_supplier, 'kode_cabang' => $kode_cabang]);
                } else if ($kode_gudang != '' && $kode_supplier == '') {
                    $header     = $this->M_global2->getDataResult('barang_in_header', ['tgl_beli >= ' => $dari, 'tgl_beli <= ' => $sampai, 'is_valid' => 1, 'kode_gudang' => $kode_gudang, 'kode_cabang' => $kode_cabang]);
                } else {
                    $header     = $this->M_global2->getDataResult('barang_in_header', ['tgl_beli >= ' => $dari, 'tgl_beli <= ' => $sampai, 'is_valid' => 1, 'kode_supplier' => $kode_supplier, 'kode_gudang' => $kode_gudang, 'kode_cabang' => $kode_cabang]);
                }
            }

            // body header
            $body .= '<table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="width: 10%;">Perihal</td>
                    <td style="width: 2%;"> : </td>
                    <td colspan="2">' . $file . '</td>
                </tr>
                <tr>
                    <td style="width: 10%;">Periode</td>
                    <td style="width: 2%;"> : </td>
                    <td style="width: 38%;">' . date('d/m/Y', strtotime($dari)) . ' ~ ' . date('d/m/Y', strtotime($sampai)) . '</td>
                    <td style="width: 50%; text-align: right;">Pencetak : ' . $pencetak . '</td>
                </tr>
            </table>';

            $body .= $breaktable;

            $body .= '<table style="width: 100%; font-size: 14px;" autosize="1" cellpadding="5px">';
            $body .= '<thead>
                <tr>
                    <th rowspan="2" style="width: 5%; border: 1px solid black; background-color: #0e1d2e; color: white;">#</th>
                    <th rowspan="2" style="border: 1px solid black; background-color: #0e1d2e; color: white;">Barang</th>
                    <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Satuan</th>
                    <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Harga</th>
                    <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Jumlah</th>
                    <th colspan="2" style="width: 20%; border: 1px solid black; background-color: #0e1d2e; color: white;">Diskon</th>
                    <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Pajak</th>
                    <th rowspan="2" style="width: 15%; border: 1px solid black; background-color: #0e1d2e; color: white;">Total</th>
                </tr>
                <tr>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">%</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Rp</th>
                </tr>
            </thead>';
            $body .= '<tbody>';

            if ($header) {
                $total_all = 0;
                foreach ($header as $h) {
                    $total_all += $h->total;

                    if ($param == 1) {
                        $total = number_format($h->total);
                        $total_allx = number_format($total_all);
                    } else {
                        $total = ceil($h->total);
                        $total_allx = ceil($total_all);
                    }

                    $body .= '<tr>
                        <td colspan="8" style="border: 1px solid black; background-color: #007bff; color: white;">' . date('d-m-Y', strtotime($h->tgl_beli)) . ' / ' . date('H:i:s', strtotime($h->jam_beli)) . ' / ' . $h->invoice . ' / ' . $this->M_global2->getData('m_supplier', ['kode_supplier' => $h->kode_supplier])->nama . ' / ' . $this->M_global2->getData('m_gudang', ['kode_gudang' => $h->kode_gudang])->nama . '</td>
                        <td style="border: 1px solid black; background-color: #007bff; color: white; text-align: right; font-weight: bold;">' . $total . '</td>
                    </tr>';

                    $detail = $this->M_global2->getDataResult('barang_in_detail', ['invoice' => $h->invoice]);

                    $no = 1;
                    foreach ($detail as $d) {
                        $barang = $this->M_global2->getData('barang', ['kode_barang' => $d->kode_barang]);
                        $satuan = $this->M_global2->getData('m_satuan', ['kode_satuan' => $d->kode_satuan]);

                        if ($param == 1) {
                            $harga      = number_format($d->harga);
                            $qty        = number_format($d->qty);
                            $discpr     = number_format($d->discpr);
                            $discrp     = number_format($d->discrp);
                            $pajakrp    = number_format($d->pajakrp);
                            $jumlah     = number_format($d->jumlah);
                        } else {
                            $harga      = ceil($d->harga);
                            $qty        = ceil($d->qty);
                            $discpr     = ceil($d->discpr);
                            $discrp     = ceil($d->discrp);
                            $pajakrp    = ceil($d->pajakrp);
                            $jumlah     = ceil($d->jumlah);
                        }

                        $body .= '<tr>
                            <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                            <td style="border: 1px solid black;">' . $barang->kode_barang . ' ~ ' . $barang->nama . '</td>
                            <td style="border: 1px solid black;">' . $satuan->keterangan . '</td>
                            <td style="border: 1px solid black; text-align: right;">' . $harga . '</td>
                            <td style="border: 1px solid black; text-align: right;">' . $qty . '</td>
                            <td style="border: 1px solid black; text-align: right;">' . $discpr . '</td>
                            <td style="border: 1px solid black; text-align: right;">' . $discrp . '</td>
                            <td style="border: 1px solid black; text-align: right;">' . $pajakrp . '</td>
                            <td style="border: 1px solid black; text-align: right;">' . $jumlah . '</td>
                        </tr>';

                        $no++;
                    }
                }

                $body .= '<tr>
                    <td colspan="8" style="border: 1px solid black; background-color: #0e1d2e; color: white;">Total Keseluruhan</td>
                    <td style="border: 1px solid black; background-color: #0e1d2e; color: white; text-align: right;">' . $total_allx . '</td>
                </tr>';
            } else {
                $body .= '<tr>
                    <td colspan="9" style="border: 1px solid black; text-align: center;">Tidak Ada Transaksi</td>
                </tr>';
            }


            $body .= '</tbody>';

            $body .= '</table>';
        } else if ($laporan == 2) {
            $file = 'Laporan Retur Pembelian';

            // isi body
            if ($kode_gudang == '' && $kode_supplier == '') {
                $header     = $this->M_global2->getDataResult('barang_in_retur_header', ['tgl_retur >= ' => $dari, 'tgl_retur <= ' => $sampai, 'is_valid' => 1, 'kode_cabang' => $kode_cabang]);
            } else {
                if ($kode_gudang == '' && $kode_supplier != '') {
                    $header     = $this->M_global2->getDataResult('barang_in_retur_header', ['tgl_retur >= ' => $dari, 'tgl_retur <= ' => $sampai, 'is_valid' => 1, 'kode_supplier' => $kode_supplier, 'kode_cabang' => $kode_cabang]);
                } else if ($kode_gudang != '' && $kode_supplier == '') {
                    $header     = $this->M_global2->getDataResult('barang_in_retur_header', ['tgl_retur >= ' => $dari, 'tgl_retur <= ' => $sampai, 'is_valid' => 1, 'kode_gudang' => $kode_gudang, 'kode_cabang' => $kode_cabang]);
                } else {
                    $header     = $this->M_global2->getDataResult('barang_in_retur_header', ['tgl_retur >= ' => $dari, 'tgl_retur <= ' => $sampai, 'is_valid' => 1, 'kode_supplier' => $kode_supplier, 'kode_gudang' => $kode_gudang, 'kode_cabang' => $kode_cabang]);
                }
            }

            // body header
            $body .= '<table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="width: 10%;">Perihal</td>
                    <td style="width: 2%;"> : </td>
                    <td colspan="2">' . $file . '</td>
                </tr>
                <tr>
                    <td style="width: 10%;">Periode</td>
                    <td style="width: 2%;"> : </td>
                    <td style="width: 38%;">' . date('d/m/Y', strtotime($dari)) . ' ~ ' . date('d/m/Y', strtotime($sampai)) . '</td>
                    <td style="width: 50%; text-align: right;">Pencetak : ' . $pencetak . '</td>
                </tr>
            </table>';

            $body .= $breaktable;

            $body .= '<table style="width: 100%; font-size: 14px;" autosize="2" cellpadding="5px">';
            $body .= '<thead>
                <tr>
                    <th style="width: 5%; border: 1px solid black; background-color: #0e1d2e; color: white;">#</th>
                    <th style="width: 15%; border: 1px solid black; background-color: #0e1d2e; color: white;">Invoice</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Invoice In</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Tgl/Jam Beli</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Pemasok</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Gudang</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Surat Jalan</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">No Faktur</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">User</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Total</th>
                </tr>
            </thead>';

            $body .= '<tbody>';

            if ($header) {
                $no = 1;
                foreach ($header as $h) {
                    if ($param == 1) {
                        $total = number_format($h->total);
                    } else {
                        $total = ceil($h->total);
                    }
                    $body .= '<tr>
                        <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                        <td style="border: 1px solid black;">' . $h->invoice . '</td>
                        <td style="border: 1px solid black;">' . $h->invoice_in . '</td>
                        <td style="border: 1px solid black;">' . date('d-m-Y', strtotime($h->tgl_retur)) . ' / ' . date('H:i:s', strtotime($h->jam_retur)) . '</td>
                        <td style="border: 1px solid black;">' . $this->M_global2->getData('m_supplier', ['kode_supplier' => $h->kode_supplier])->nama . '</td>
                        <td style="border: 1px solid black;">' . $this->M_global2->getData('m_gudang', ['kode_gudang' => $h->kode_gudang])->nama . '</td>
                        <td style="border: 1px solid black;">' . $h->surat_jalan . '</td>
                        <td style="border: 1px solid black;">' . $h->no_faktur . '</td>
                        <td style="border: 1px solid black;">' . $this->M_global2->getData('user', ['kode_user' => $h->kode_user])->nama . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . $total . '</td>
                    </tr>';

                    $no++;
                }
            } else {
                $body .= '<tr>
                    <td colspan="10" style="border: 1px solid black; font-weight: bold; text-align: center;">Tidak Ada Transaksi</td>
                </tr>';
            }


            $body .= '</tbody>';

            $body .= '</table>';
        } else if ($laporan == '2.1') {
            $file       = 'Laporan Retur Pembelian Detail';
            $position   = 'P'; // cek posisi l/p

            // isi body
            if ($kode_gudang == '' && $kode_supplier == '') {
                $header     = $this->M_global2->getDataResult('barang_in_retur_header', ['tgl_retur >= ' => $dari, 'tgl_retur <= ' => $sampai, 'is_valid' => 1, 'kode_cabang' => $kode_cabang]);
            } else {
                if ($kode_gudang == '' && $kode_supplier != '') {
                    $header     = $this->M_global2->getDataResult('barang_in_retur_header', ['tgl_retur >= ' => $dari, 'tgl_retur <= ' => $sampai, 'is_valid' => 1, 'kode_supplier' => $kode_supplier, 'kode_cabang' => $kode_cabang]);
                } else if ($kode_gudang != '' && $kode_supplier == '') {
                    $header     = $this->M_global2->getDataResult('barang_in_retur_header', ['tgl_retur >= ' => $dari, 'tgl_retur <= ' => $sampai, 'is_valid' => 1, 'kode_gudang' => $kode_gudang, 'kode_cabang' => $kode_cabang]);
                } else {
                    $header     = $this->M_global2->getDataResult('barang_in_retur_header', ['tgl_retur >= ' => $dari, 'tgl_retur <= ' => $sampai, 'is_valid' => 1, 'kode_supplier' => $kode_supplier, 'kode_gudang' => $kode_gudang, 'kode_cabang' => $kode_cabang]);
                }
            }

            // body header
            $body .= '<table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="width: 10%;">Perihal</td>
                    <td style="width: 2%;"> : </td>
                    <td colspan="2">' . $file . '</td>
                </tr>
                <tr>
                    <td style="width: 10%;">Periode</td>
                    <td style="width: 2%;"> : </td>
                    <td style="width: 38%;">' . date('d/m/Y', strtotime($dari)) . ' ~ ' . date('d/m/Y', strtotime($sampai)) . '</td>
                    <td style="width: 50%; text-align: right;">Pencetak : ' . $pencetak . '</td>
                </tr>
            </table>';

            $body .= $breaktable;

            $body .= '<table style="width: 100%; font-size: 14px;" autosize="1" cellpadding="5px">';
            $body .= '<thead>
                <tr>
                    <th rowspan="2" style="width: 5%; border: 1px solid black; background-color: #0e1d2e; color: white;">#</th>
                    <th rowspan="2" style="border: 1px solid black; background-color: #0e1d2e; color: white;">Barang</th>
                    <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Satuan</th>
                    <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Harga</th>
                    <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Jumlah</th>
                    <th colspan="2" style="width: 20%; border: 1px solid black; background-color: #0e1d2e; color: white;">Diskon</th>
                    <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Pajak</th>
                    <th rowspan="2" style="width: 15%; border: 1px solid black; background-color: #0e1d2e; color: white;">Total</th>
                </tr>
                <tr>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">%</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Rp</th>
                </tr>
            </thead>';
            $body .= '<tbody>';

            if ($header) {
                $total_all = 0;
                foreach ($header as $h) {
                    $total_all += $h->total;

                    if ($param == 1) {
                        $total = number_format($h->total);
                        $total_allx = number_format($total_all);
                    } else {
                        $total = ceil($h->total);
                        $total_allx = ceil($total_all);
                    }

                    $body .= '<tr>
                        <td colspan="8" style="border: 1px solid black; background-color: #007bff; color: white;">' . date('d-m-Y', strtotime($h->tgl_retur)) . ' / ' . date('H:i:s', strtotime($h->jam_retur)) . ' / ' . $h->invoice . ' / ' . $this->M_global2->getData('m_supplier', ['kode_supplier' => $h->kode_supplier])->nama . ' / ' . $this->M_global2->getData('m_gudang', ['kode_gudang' => $h->kode_gudang])->nama . '</td>
                        <td style="border: 1px solid black; background-color: #007bff; color: white; text-align: right; font-weight: bold;">' . $total . '</td>
                    </tr>';

                    $detail = $this->M_global2->getDataResult('barang_in_retur_detail', ['invoice' => $h->invoice]);

                    $no = 1;
                    foreach ($detail as $d) {
                        $barang = $this->M_global2->getData('barang', ['kode_barang' => $d->kode_barang]);
                        $satuan = $this->M_global2->getData('m_satuan', ['kode_satuan' => $d->kode_satuan]);

                        if ($param == 1) {
                            $harga      = number_format($d->harga);
                            $qty        = number_format($d->qty);
                            $discpr     = number_format($d->discpr);
                            $discrp     = number_format($d->discrp);
                            $pajakrp    = number_format($d->pajakrp);
                            $jumlah     = number_format($d->jumlah);
                        } else {
                            $harga      = ceil($d->harga);
                            $qty        = ceil($d->qty);
                            $discpr     = ceil($d->discpr);
                            $discrp     = ceil($d->discrp);
                            $pajakrp    = ceil($d->pajakrp);
                            $jumlah     = ceil($d->jumlah);
                        }

                        $body .= '<tr>
                            <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                            <td style="border: 1px solid black;">' . $barang->kode_barang . ' ~ ' . $barang->nama . '</td>
                            <td style="border: 1px solid black;">' . $satuan->keterangan . '</td>
                            <td style="border: 1px solid black; text-align: right;">' . $harga . '</td>
                            <td style="border: 1px solid black; text-align: right;">' . $qty . '</td>
                            <td style="border: 1px solid black; text-align: right;">' . $discpr . '</td>
                            <td style="border: 1px solid black; text-align: right;">' . $discrp . '</td>
                            <td style="border: 1px solid black; text-align: right;">' . $pajakrp . '</td>
                            <td style="border: 1px solid black; text-align: right;">' . $jumlah . '</td>
                        </tr>';

                        $no++;
                    }
                }

                $body .= '<tr>
                    <td colspan="8" style="border: 1px solid black; background-color: #0e1d2e; color: white;">Total Keseluruhan</td>
                    <td style="border: 1px solid black; background-color: #0e1d2e; color: white; text-align: right;">' . $total_allx . '</td>
                </tr>';
            } else {
                $body .= '<tr>
                    <td colspan="9" style="border: 1px solid black; text-align: center;">Tidak Ada Transaksi</td>
                </tr>';
            }


            $body .= '</tbody>';

            $body .= '</table>';
        } else if ($laporan == 3) {
            $file = 'Laporan Stok Pembelian';

            $position = 'P';

            // isi body
            $detail = $this->M_global2->getReportPembelian($dari, $sampai, $kode_gudang, $kode_barang);

            // body header
            $body .= '<table style="width: 100%; font-size: 14px;" autosize="1">
                <tr>
                    <td style="width: 9%;">Perihal</td>
                    <td style="width: 1%;"> : </td>
                    <td colspan="2">' . $file . '</td>
                </tr>
                <tr>
                    <td style="width: 9%;">Periode</td>
                    <td style="width: 1%;"> : </td>
                    <td colspan="2">' . date('d-m-Y', strtotime($dari)) . ' ~ ' . date('d-m-Y', strtotime($sampai)) . '</td>
                </tr>
                <tr>
                    <td style="width: 9%;">Barang</td>
                    <td style="width: 1%;"> : </td>
                    <td colspan="2">' . (empty($detail) ? '' : $kode_barang . ' / ' . $this->M_global2->getData('barang', ['kode_barang' => $kode_barang])->nama) . '</td>
                </tr>
                <tr>
                    <td style="width: 9%;">Gudang</td>
                    <td style="width: 1%;"> : </td>
                    <td style="width: 40%;">' . $this->M_global2->getData('m_gudang', ['kode_gudang' => $kode_gudang])->nama . '</td>
                    <td style="width: 50%; text-align: right;">Pencetak : ' . $pencetak . '</td>
                </tr>
            </table>';

            $body .= $breaktable;

            $body .= '<table style="width: 100%; font-size: 14px;" autosize="1" cellpadding="5px">';

            $body .= '<thead>
                <tr>
                    <th rowspan="2" style="width: 5%; text-align: center; border: 1px solid black; background-color: #0e1d2e; color: white;">#</th>
                    <th rowspan="2" style="width: 15%; text-align: center; border: 1px solid black; background-color: #0e1d2e; color: white;">Tgl/Jam</th>
                    <th rowspan="2" style="width: 18%; text-align: center; border: 1px solid black; background-color: #0e1d2e; color: white;">Keterangan</th>
                    <th rowspan="2" style="width: 22%; text-align: center; border: 1px solid black; background-color: #0e1d2e; color: white;">No. Transaksi</th>
                    <th rowspan="2" style="width: 10%; text-align: center; border: 1px solid black; background-color: #0e1d2e; color: white;">Harga</th>
                    <th colspan="3" style="width: 30%; text-align: center; border: 1px solid black; background-color: #0e1d2e; color: white;">Stok</th>
                </tr>
                <tr>
                    <th style="width: 10%; text-align: center; border: 1px solid black; background-color: #0e1d2e; color: white;">Masuk</th>
                    <th style="width: 10%; text-align: center; border: 1px solid black; background-color: #0e1d2e; color: white;">Keluar</th>
                    <th style="width: 10%; text-align: center; border: 1px solid black; background-color: #0e1d2e; color: white;">Akhir</th>
                </tr>
            </thead>';

            if (empty($detail)) {

                $body .= '<tbody>
                    <tr>
                        <td colspan="8" style="border: 1px solid black; text-align: center;">Tidak Ada Transaksi</td>
                    </tr>
                </tbody>';
            } else {
                $body .= '<tbody>';

                $no           = 1;
                $stok_akhir   = 0;
                foreach ($detail as $d) {
                    $stok_akhir += ($d->masuk - $d->keluar);

                    if ($param == 1) {
                        $harga    = number_format($d->harga);
                        $masuk    = number_format($d->masuk);
                        $keluar   = number_format($d->keluar);
                        $akhir    = number_format($stok_akhir);
                    } else {
                        $harga    = ceil($d->harga);
                        $masuk    = ceil($d->masuk);
                        $keluar   = ceil($d->keluar);
                        $akhir    = ceil($stok_akhir);
                    }

                    $satuan = $this->M_global2->getData('m_satuan', ['kode_satuan' => $d->satuan]);

                    $body .= '<tr>
                        <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                        <td style="border: 1px solid black;">' . $d->record_date . '</td>
                        <td style="border: 1px solid black;">' . $d->keterangan . '</td>
                        <td style="border: 1px solid black;">' . $d->no_trx . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . $harga . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . (($masuk > 0) ? $masuk . ' ' . $satuan->keterangan : '-') . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . (($keluar > 0) ? $keluar . ' ' . $satuan->keterangan : '-') . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . konversi_show_satuan($akhir, $d->kode_barang) . '</td>
                    </tr>';

                    $no++;
                }

                $body .= '</tbody>';
            }

            $body .= '</table>';
        } else if ($laporan == 4) {
            $file = 'Laporan Penjualan';

            $position = 'L';

            // isi body
            if ($kode_gudang == '') {
                $header     = $this->M_global2->getDataResult('barang_out_header', ['tgl_jual >= ' => $dari, 'tgl_jual <= ' => $sampai, 'status_jual' => 1, 'kode_cabang' => $kode_cabang]);
            } else {
                $header     = $this->M_global2->getDataResult('barang_out_header', ['tgl_jual >= ' => $dari, 'tgl_jual <= ' => $sampai, 'status_jual' => 1, 'kode_gudang' => $kode_gudang, 'kode_cabang' => $kode_cabang]);
            }

            // body header
            $body .= '<table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="width: 10%;">Perihal</td>
                    <td style="width: 2%;"> : </td>
                    <td colspan="2">' . $file . '</td>
                </tr>
                <tr>
                    <td style="width: 10%;">Periode</td>
                    <td style="width: 2%;"> : </td>
                    <td style="width: 38%;">' . date('d/m/Y', strtotime($dari)) . ' ~ ' . date('d/m/Y', strtotime($sampai)) . '</td>
                    <td style="width: 50%; text-align: right;">Pencetak : ' . $pencetak . '</td>
                </tr>
            </table>';

            $body .= $breaktable;

            $body .= '<table style="width: 100%; font-size: 14px;" autosize="2" cellpadding="5px" autosize="1">';
            $body .= '<thead>
                <tr>
                    <th style="width: 5%; border: 1px solid black; background-color: #0e1d2e; color: white;">#</th>
                    <th style="width: 15%; border: 1px solid black; background-color: #0e1d2e; color: white;">Invoice</th>
                    <th style="width: 20%; border: 1px solid black; background-color: #0e1d2e; color: white;">No Pendaftaran</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Pembeli</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Tgl/Jam Jual</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Poli/Dokter</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Gudang</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">User</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Total</th>
                </tr>
            </thead>';

            $body .= '<tbody>';

            if ($header) {
                $no = 1;
                foreach ($header as $h) {
                    if ($param == 1) {
                        $total = number_format($h->total);
                    } else {
                        $total = ceil($h->total);
                    }
                    $body .= '<tr>
                        <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                        <td style="border: 1px solid black;">' . $h->invoice . '</td>
                        <td style="border: 1px solid black;">' . (($h->no_trx == '' || $h->no_trx == null) ? 'Umum' : $h->no_trx) . '</td>
                        <td style="border: 1px solid black;">' . $h->kode_member . ' ~ ' . $this->M_global2->getData('member', ['kode_member' => $h->kode_member])->nama . '</td>
                        <td style="border: 1px solid black;">' . date('d-m-Y', strtotime($h->tgl_jual)) . ' / ' . date('H:i:s', strtotime($h->jam_jual)) . '</td>
                        <td style="border: 1px solid black;">' . $this->M_global2->getData('m_poli', ['kode_poli' => $h->kode_poli])->keterangan . ' ~ ' . $this->M_global2->getData('dokter', ['kode_dokter' => $h->kode_dokter])->nama . '</td>
                        <td style="border: 1px solid black;">' . $this->M_global2->getData('m_gudang', ['kode_gudang' => $h->kode_gudang])->keterangan . '</td>
                        <td style="border: 1px solid black;">' . $this->M_global2->getData('user', ['kode_user' => $h->kode_user])->nama . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . $total . '</td>
                    </tr>';

                    $no++;
                }
            } else {
                $body .= '<tr>
                    <td colspan="9" style="border: 1px solid black; font-weight: bold; text-align: center;">Tidak Ada Transaksi</td>
                </tr>';
            }


            $body .= '</tbody>';

            $body .= '</table>';
        } else if ($laporan == '4.1') {
            $file       = 'Laporan Penjualan Detail';
            $position   = 'P'; // cek posisi l/p

            // isi body
            if ($kode_gudang == '') {
                $header     = $this->M_global2->getDataResult('barang_out_header', ['tgl_jual >= ' => $dari, 'tgl_jual <= ' => $sampai, 'status_jual' => 1, 'kode_cabang' => $kode_cabang]);
            } else {
                $header     = $this->M_global2->getDataResult('barang_out_header', ['tgl_jual >= ' => $dari, 'tgl_jual <= ' => $sampai, 'status_jual' => 1, 'kode_gudang' => $kode_gudang, 'kode_cabang' => $kode_cabang]);
            }

            // body header
            $body .= '<table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="width: 10%;">Perihal</td>
                    <td style="width: 2%;"> : </td>
                    <td colspan="2">' . $file . '</td>
                </tr>
                <tr>
                    <td style="width: 10%;">Periode</td>
                    <td style="width: 2%;"> : </td>
                    <td style="width: 38%;">' . date('d/m/Y', strtotime($dari)) . ' ~ ' . date('d/m/Y', strtotime($sampai)) . '</td>
                    <td style="width: 50%; text-align: right;">Pencetak : ' . $pencetak . '</td>
                </tr>
            </table>';

            $body .= $breaktable;

            $body .= '<table style="width: 100%; font-size: 14px;" autosize="1" cellpadding="5px">';
            $body .= '<thead>
                <tr>
                    <th rowspan="2" style="width: 5%; border: 1px solid black; background-color: #0e1d2e; color: white;">#</th>
                    <th rowspan="2" style="border: 1px solid black; background-color: #0e1d2e; color: white;">Barang</th>
                    <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Satuan</th>
                    <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Harga</th>
                    <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Jumlah</th>
                    <th colspan="2" style="width: 20%; border: 1px solid black; background-color: #0e1d2e; color: white;">Diskon</th>
                    <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Pajak</th>
                    <th rowspan="2" style="width: 15%; border: 1px solid black; background-color: #0e1d2e; color: white;">Total</th>
                </tr>
                <tr>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">%</th>
                    <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Rp</th>
                </tr>
            </thead>';
            $body .= '<tbody>';

            if ($header) {
                $total_all = 0;
                foreach ($header as $h) {
                    $total_all += $h->total;

                    if ($param == 1) {
                        $total        = number_format($h->total);
                        $total_allx   = number_format($total_all);
                    } else {
                        $total        = ceil($h->total);
                        $total_allx   = ceil($total_all);
                    }

                    $body .= '<tr>
                        <td colspan="8" style="border: 1px solid black; background-color: #007bff; color: white;">' . date('d-m-Y', strtotime($h->tgl_out)) . ' / ' . date('H:i:s', strtotime($h->jam_out)) . ' / ' . $h->invoice . ' / ' . $h->kode_member . ' ~ ' . $this->M_global2->getData('member', ['kode_member' => $h->kode_member])->nama . ' / ' . $this->M_global2->getData('m_gudang', ['kode_gudang' => $h->kode_gudang])->nama . '</td>
                        <td style="border: 1px solid black; background-color: #007bff; color: white; text-align: right; font-weight: bold;">' . $total . '</td>
                    </tr>';

                    $detail = $this->M_global2->getDataResult('barang_out_detail', ['invoice' => $h->invoice]);

                    $no = 1;
                    foreach ($detail as $d) {
                        $barang = $this->M_global2->getData('barang', ['kode_barang' => $d->kode_barang]);
                        $satuan = $this->M_global2->getData('m_satuan', ['kode_satuan' => $d->kode_satuan]);

                        if ($param == 1) {
                            $harga      = number_format($d->harga);
                            $qty        = number_format($d->qty);
                            $discpr     = number_format($d->discpr);
                            $discrp     = number_format($d->discrp);
                            $pajakrp    = number_format($d->pajakrp);
                            $jumlah     = number_format($d->jumlah);
                        } else {
                            $harga      = ceil($d->harga);
                            $qty        = ceil($d->qty);
                            $discpr     = ceil($d->discpr);
                            $discrp     = ceil($d->discrp);
                            $pajakrp    = ceil($d->pajakrp);
                            $jumlah     = ceil($d->jumlah);
                        }

                        $body .= '<tr>
                            <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                            <td style="border: 1px solid black;">' . $barang->kode_barang . ' ~ ' . $barang->nama . '</td>
                            <td style="border: 1px solid black;">' . $satuan->keterangan . '</td>
                            <td style="border: 1px solid black; text-align: right;">' . $harga . '</td>
                            <td style="border: 1px solid black; text-align: right;">' . $qty . '</td>
                            <td style="border: 1px solid black; text-align: right;">' . $discpr . '</td>
                            <td style="border: 1px solid black; text-align: right;">' . $discrp . '</td>
                            <td style="border: 1px solid black; text-align: right;">' . $pajakrp . '</td>
                            <td style="border: 1px solid black; text-align: right;">' . $jumlah . '</td>
                        </tr>';

                        $no++;
                    }
                }

                $body .= '<tr>
                    <td colspan="8" style="border: 1px solid black; background-color: #0e1d2e; color: white;">Total Keseluruhan</td>
                    <td style="border: 1px solid black; background-color: #0e1d2e; color: white; text-align: right;">' . $total_allx . '</td>
                </tr>';
            } else {
                $body .= '<tr>
                    <td colspan="9" style="border: 1px solid black; text-align: center;">Tidak Ada Transaksi</td>
                </tr>';
            }


            $body .= '</tbody>';

            $body .= '</table>';
        } else if ($laporan == 5) {
            $file = 'Laporan Retur Penjualan';

            // isi body
            $header = $this->M_global2->getDataResult('barang_out_retur_header', ['tgl_retur >= ' => $dari, 'tgl_retur <= ' => $sampai]);

            // body header
            $body .= '<table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="width: 15%;">Perihal</td>
                    <td style="width: 2%;"> : </td>
                    <td colspan="2">' . $file . '</td>
                </tr>
                <tr>
                    <td style="width: 15%;">Periode</td>
                    <td style="width: 2%;"> : </td>
                    <td colspan="2">' . date('d-m-Y', strtotime($dari)) . ' ~ ' . date('d-m-Y', strtotime($sampai)) . '</td>
                </tr>
                <tr>
                    <td style="width: 15%;">Gudang</td>
                    <td style="width: 2%;"> : </td>
                    <td style="width: 33%;">' . $this->M_global2->getData('m_gudang', ['kode_gudang' => $kode_gudang])->nama . '</td>
                    <td style="width: 50%; text-align: right;">Pencetak : ' . $pencetak . '</td>
                </tr>
            </table>';

            $body .= $breaktable;

            $body .= '<table style="width: 100%; font-size: 14px;" autosize="2" cellpadding="5px">';
            $body .= '<tr>
                <th rowspan="2" style="width: 5%; border: 1px solid black; background-color: #0e1d2e; color: white;">#</th>
                <th rowspan="2" style="width: 30%; border: 1px solid black; background-color: #0e1d2e; color: white;">Barang</th>
                <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Harga</th>
                <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Jumlah</th>
                <th colspan="2" style="width: 20%; border: 1px solid black; background-color: #0e1d2e; color: white;">Diskon</th>
                <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Pajak</th>
                <th rowspan="2" style="width: 15%; border: 1px solid black; background-color: #0e1d2e; color: white;">Total</th>
            </tr>
            <tr>
                <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">%</th>
                <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Rp</th>
            </tr>';
            foreach ($header as $h) {
                if ($param == 1) {
                    $total = number_format($h->total);
                } else {
                    $total = ceil($h->total);
                }
                $body .= '<tr style="background-color: skyblue;">
                    <td colspan="6" style="border: 1px solid black; font-weight: bold;">No. Transaksi: ' . $h->invoice . '</td>
                    <td colspan="2" style="border: 1px solid black; font-weight: bold; text-align: right">' . $total . '</td>
                </tr>';

                // detail barang
                $detail = $this->M_global2->getDataResult('barang_out_retur_detail', ['invoice' => $h->invoice]);

                $no = 1;
                foreach ($detail as $d) {
                    if ($param == 1) {
                        $harga = number_format($d->harga);
                        $qty = number_format($d->qty);
                        $discpr = number_format($d->discpr);
                        $discrp = number_format($d->discrp);
                        $pajak = number_format($d->pajakrp);
                        $jumlah = number_format($d->jumlah);
                    } else {
                        $harga = ceil($d->harga);
                        $qty = ceil($d->qty);
                        $discpr = ceil($d->discpr);
                        $discrp = ceil($d->discrp);
                        $pajak = ceil($d->pajakrp);
                        $jumlah = ceil($d->jumlah);
                    }
                    $body .= '<tr>
                        <td style="border: 1px solid black;">' . $no . '</td>
                        <td style="border: 1px solid black;">' . $d->kode_barang . ' ~ ' . $this->M_global2->getData('barang', ['kode_barang' => $d->kode_barang])->nama . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . $harga . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . $qty . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . $discpr . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . $discrp . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . $pajak . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . $jumlah . '</td>
                    </tr>';
                    $no++;
                }
            }

            $body .= '</table>';
        } else if ($laporan == 6) {
            $file = 'Laporan Stok Penjualan';

            $position = 'L';

            // isi body
            $detail = $this->M_global2->getReportPenjualan($dari, $sampai, $kode_gudang);

            // body header
            $body .= '<table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="width: 15%;">Perihal</td>
                    <td style="width: 2%;"> : </td>
                    <td colspan="2">' . $file . '</td>
                </tr>
                <tr>
                    <td style="width: 15%;">Periode</td>
                    <td style="width: 2%;"> : </td>
                    <td colspan="2">' . date('d-m-Y', strtotime($dari)) . ' ~ ' . date('d-m-Y', strtotime($sampai)) . '</td>
                </tr>
                <tr>
                    <td style="width: 15%;">Gudang</td>
                    <td style="width: 2%;"> : </td>
                    <td style="width: 33%;">' . $this->M_global2->getData('m_gudang', ['kode_gudang' => $kode_gudang])->nama . '</td>
                    <td style="width: 50%; text-align: right;">Pencetak : ' . $pencetak . '</td>
                </tr>
            </table>';

            $body .= $breaktable;

            $body .= '<table style="width: 100%; font-size: 14px;" autosize="2" cellpadding="5px">';

            $body .= '<tr>
                <th rowspan="2" style="width: 5%; text-align: center; border: 1px solid black; background-color: #0e1d2e; color: white;">#</th>
                <th rowspan="2" style="width: 15%; text-align: center; border: 1px solid black; background-color: #0e1d2e; color: white;">Tgl/Jam</th>
                <th rowspan="2" style="width: 15%; text-align: center; border: 1px solid black; background-color: #0e1d2e; color: white;">Keterangan</th>
                <th rowspan="2" style="text-align: center; border: 1px solid black; background-color: #0e1d2e; color: white;">No. Transaksi</th>
                <th rowspan="2" style="text-align: center; border: 1px solid black; background-color: #0e1d2e; color: white;">Barang</th>
                <th rowspan="2" style="width: 10%; text-align: center; border: 1px solid black; background-color: #0e1d2e; color: white;">Harga</th>
                <th colspan="3" style="width: 30%; text-align: center; border: 1px solid black; background-color: #0e1d2e; color: white;">Stok</th>
            </tr>
            <tr>
                <th style="width: 10%; text-align: center; border: 1px solid black; background-color: #0e1d2e; color: white;">Masuk</th>
                <th style="width: 10%; text-align: center; border: 1px solid black; background-color: #0e1d2e; color: white;">Keluar</th>
                <th style="width: 10%; text-align: center; border: 1px solid black; background-color: #0e1d2e; color: white;">Akhir</th>
            </tr>';

            if (empty($detail)) {
                $body .= '<tr>
                    <td colspan="8" style="border: 1px solid black; text-align: center;">Data Tidak Tersedia</td>
                </tr>';
            } else {
                $no = 1;
                $stok_akhir = 0;
                foreach ($detail as $d) {
                    $stok_akhir += ($d->masuk - $d->keluar);

                    if ($param == 1) {
                        $harga = number_format($d->harga);
                        $masuk = number_format($d->masuk);
                        $keluar = number_format($d->keluar);
                        $akhir = number_format($stok_akhir);
                    } else {
                        $harga = ceil($d->harga);
                        $masuk = ceil($d->masuk);
                        $keluar = ceil($d->keluar);
                        $akhir = ceil($stok_akhir);
                    }

                    $body .= '<tr>
                        <td style="border: 1px solid black;">' . $no . '</td>
                        <td style="border: 1px solid black;">' . $d->record_date . '</td>
                        <td style="border: 1px solid black;">' . $d->keterangan . '</td>
                        <td style="border: 1px solid black;">' . $d->no_trx . '</td>
                        <td style="border: 1px solid black;">' . $d->barang . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . $harga . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . $masuk . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . $keluar . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . $akhir . '</td>
                    </tr>';

                    $no++;
                }
            }

            $body .= '</table>';
        } else if ($laporan == 7) {
            $file = 'Laporan Harian Kasir';
            $position = 'L';

            // isi body
            if ($kode_user == '' || $kode_user == null || $kode_user == 'null') {
                $sel_user = "";
            } else {
                $sel_user = "AND p.kode_user = '$kode_user'";
            }

            $detail = $this->db->query("SELECT p.* FROM pembayaran p WHERE p.tgl_pembayaran >= '$dari' AND p.tgl_pembayaran <= '$sampai' $sel_user AND inv_jual IN (SELECT invoice FROM barang_out_header) AND kode_cabang = '$kode_cabang'")->result();

            // body header
            $body .= '<table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="width: 15%;">Perihal</td>
                    <td style="width: 2%;"> : </td>
                    <td colspan="2">' . $file . '</td>
                </tr>
                <tr>
                    <td style="width: 15%;">Periode</td>
                    <td style="width: 2%;"> : </td>
                    <td colspan="2">' . date('d-m-Y', strtotime($dari)) . ' ~ ' . date('d-m-Y', strtotime($sampai)) . '</td>
                </tr>
                <tr>
                    <td style="width: 15%;">Kasir</td>
                    <td style="width: 2%;"> : </td>
                    <td style="width: 33%;">' . $this->M_global2->getData('user', ['kode_user' => $kode_user])->nama . '</td>
                    <td style="width: 50%; text-align: right;">Pencetak : ' . $pencetak . '</td>
                </tr>
            </table>';

            $body .= $breaktable;

            $tipe_bank = $this->M_global2->getResult('tipe_bank');

            $body .= '<table style="width: 100%; font-size: 14px;" autosize="2" cellpadding="5px">';
            $body .= '<thead>';

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
                    $cek_member = $this->M_global2->getData('barang_out_header', ['invoice' => $d->inv_jual]);

                    if ($cek_member) {
                        $member = $this->M_global2->getData('member', ['kode_member' => $cek_member->kode_member])->nama;
                    } else {
                        $member = 'Masyarakat Umum';
                    }

                    if ($param == 1) {
                        $total        = number_format($d->total);
                        $cash         = number_format($d->cash);
                        $result       = number_format($d->total - $d->kembalian);
                        $um           = number_format($d->um_keluar);
                        $umm          = number_format($d->um_masuk);
                        $kembalian    = number_format(($d->cek_um == 1) ? 0 : $d->kembalian);
                    } else {
                        $total        = ceil($d->total);
                        $cash         = ceil($d->cash);
                        $result       = ceil($d->total - $d->kembalian);
                        $um           = ceil($d->um_keluar);
                        $umm          = ceil($d->um_masuk);
                        $kembalian    = ceil(($d->cek_um == 1) ? 0 : $d->kembalian);
                    }

                    $body .= '<tr>';

                    $body .= '<td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                        <td style="border: 1px solid black;">' . $d->invoice . '</td>
                        <td style="border: 1px solid black;">' . $cek_member->kode_member . ' ~ ' . $member . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . $um . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . $cash . '</td>';

                    foreach ($tipe_bank as $tb) {
                        $card_detail = $this->M_global2->getDataResult('bayar_card_detail', ['token_pembayaran' => $d->token_pembayaran, 'kode_tipe' => $tb->kode_tipe]);
                        if (count($card_detail) > 0) {
                            foreach ($card_detail as $cd) {
                                if ($param == 1) {
                                    $jumlah = number_format($cd->jumlah);
                                } else {
                                    $jumlah = ceil($cd->jumlah);
                                }

                                $body .= '<td style="border: 1px solid black; text-align: right;">' . $jumlah . '</td>';
                            }
                        } else {
                            $body .= '<td style="border: 1px solid black; text-align: right;">0.00</td>';
                        }
                    }

                    $promo            = $this->M_global2->getData('m_promo', ['kode_promo' => $d->kode_promo]);
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

                    if ($param == 1) {
                        $tjual    = number_format($total_jual);
                        $pprom    = number_format($potongan_promo);
                        $sprom    = number_format($subtotal_promo);
                        $tindakan = number_format($tindakan);
                    } else {
                        $tjual    = ceil($total_jual);
                        $pprom    = ceil($potongan_promo);
                        $sprom    = ceil($subtotal_promo);
                        $tindakan = ceil($tindakan);
                    }

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
        } else if ($laporan == '7.1') {
            $file = 'Laporan Record Uang Muka';
            $position = 'P';

            // isi body
            if (
                $kode_user == '' || $kode_user == null || $kode_user == 'null'
            ) {
                $sel_user = "";
            } else {
                $sel_user = "WHERE user = '$kode_user'";
            }

            $detail = $this->db->query("SELECT * FROM (
                SELECT pembayaran.invoice AS inv,
                'UM MASUK' AS keterangan,
                IF(cek_um = 1, um_masuk, 0) AS um_masuk,
                0 AS um_keluar,
                pembayaran.kode_user AS user,
                pembayaran.inv_jual,
                if(pendaftaran.kode_member IS NULL, barang_out_header.kode_member, pendaftaran.kode_member) AS kode_member
                FROM pembayaran
                JOIN pendaftaran ON pembayaran.no_trx = pendaftaran.no_trx
                JOIN barang_out_header ON pembayaran.inv_jual = barang_out_header.invoice
                WHERE approved = 1
                AND cek_um > 0

                UNION ALL

                SELECT pembayaran.invoice AS inv,
                'UM KELUAR' AS keterangan,
                0 AS um_masuk,
                um_keluar AS um_keluar,
                pembayaran.kode_user AS user,
                pembayaran.inv_jual,
                if(pendaftaran.kode_member IS NULL, barang_out_header.kode_member, pendaftaran.kode_member) AS kode_member
                FROM pembayaran
                JOIN pendaftaran ON pembayaran.no_trx = pendaftaran.no_trx
                JOIN barang_out_header ON pembayaran.inv_jual = barang_out_header.invoice
                WHERE approved = 1
                AND um_keluar > 0

                UNION ALL

                SELECT invoice AS inv,
                'UM MASUK' AS keterangan,
                total AS um_masuk,
                0 AS um_keluar,
                kode_user AS user,
                '' AS inv_jual,
                kode_member
                FROM pembayaran_uangmuka
            ) AS semua $sel_user")->result();

            // body header
            $body .= '<table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="width: 15%;">Perihal</td>
                    <td style="width: 2%;"> : </td>
                    <td colspan="2">' . $file . '</td>
                </tr>
                <tr>
                    <td style="width: 15%;">Periode</td>
                    <td style="width: 2%;"> : </td>
                    <td colspan="2">' . date('d-m-Y', strtotime($dari)) . ' ~ ' . date('d-m-Y', strtotime($sampai)) . '</td>
                </tr>
                <tr>
                    <td style="width: 15%;">Kasir</td>
                    <td style="width: 2%;"> : </td>
                    <td style="width: 33%;">' . $this->M_global2->getData('user', ['kode_user' => $this->session->userdata('kode_user')])->nama . '</td>
                    <td style="width: 50%; text-align: right;">Pencetak : ' . $pencetak . '</td>
                </tr>
            </table>';

            $body .= $breaktable;

            $tipe_bank = $this->M_global2->getResult('tipe_bank');

            $body .= '<table style="width: 100%; font-size: 14px;" autosize="2" cellpadding="5px">';
            $body .= '<thead>';

            $body .= '<tr>
                <th rowspan="2" style="width: 5%; border: 1px solid black; background-color: #0e1d2e; color: white;">#</th>
                <th rowspan="2" style="width: 25%; border: 1px solid black; background-color: #0e1d2e; color: white;">Kwitansi</th>
                <th rowspan="2" style="width: 25%; border: 1px solid black; background-color: #0e1d2e; color: white;">Member</th>
                <th colspan="2" style="width: 30%; border: 1px solid black; background-color: #0e1d2e; color: white;">Uang Muka</th>
                <th rowspan="2" style="width: 15%; border: 1px solid black; background-color: #0e1d2e; color: white;">Total Uang Muka</th>
            </tr>';

            $body .= '<tr>';

            $body .= '<th style="width: 15%; border: 1px solid black; background-color: #0e1d2e; color: white;">Masuk</th>';
            $body .= '<th style="width: 15%; border: 1px solid black; background-color: #0e1d2e; color: white;">Keluar</th>';

            $body .= '</tr>';

            $body .= '</thead>';
            $body .= '<tbody>';

            if (
                count($detail) < 1
            ) {
                $body .= '<tr>
                    <td colspan="6" style="border: 1px solid black; text-align: center;">Data Tidak Tersedia</td>
                </tr>';
            } else {
                $no = 1;
                $sisa_um = 0;
                foreach ($detail as $d) {
                    $sisa_um += ($d->um_masuk - $d->um_keluar);
                    $cek_member = $this->M_global2->getData('barang_out_header', ['invoice' => $d->inv_jual]);

                    if ($cek_member) {
                        $member = $this->M_global2->getData('member', ['kode_member' => $d->kode_member])->nama;

                        $memberx = $cek_member->kode_member . ' ~ ' . $member;
                    } else {
                        $member = 'Masyarakat Umum';
                        $memberx = $member;
                    }

                    if (
                        $param == 1
                    ) {
                        $um_masuk = number_format($d->um_masuk);
                        $um_keluar = number_format($d->um_keluar);
                        $um_total = number_format($sisa_um);
                    } else {
                        $um_masuk = ceil($d->um_masuk);
                        $um_keluar = ceil($d->um_keluar);
                        $um_total = ceil($sisa_um);
                    }

                    $body .= '<tr>';

                    $body .= '<td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                        <td style="border: 1px solid black;">' . $d->inv . '</td>
                        <td style="border: 1px solid black;">' . $memberx . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . $um_masuk . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . $um_keluar . '</td>
                        <td style="border: 1px solid black; text-align: right;">' . $um_total . '</td>';

                    $body .= '</tr>';

                    $no++;
                }
            }

            $body .= '</tbody>';
            $body .= '</table>';
        }

        $judul = $file;
        $filename = $file; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }
}
