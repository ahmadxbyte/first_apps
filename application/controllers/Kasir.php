<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kasir extends CI_Controller
{
    // variable open public untuk controller Kasir
    public $data;

    public function __construct()
    {
        parent::__construct();
        // load model M_auth
        $this->load->model("M_auth");

        if (!empty($this->session->userdata("email"))) { // jika session email masih ada

            $id_menu = $this->M_global->getData('m_menu', ['url' => 'Kasir'])->id;

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
                    'menu'      => 'Kasir',
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
            'judul'         => 'Pembayaran',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Pembayaran',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Kasir/pembayaran_list/',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Kasir/Daftar', $parameter);
    }

    // fungsi list pembayaran
    public function pembayaran_list($param1 = 1, $param2 = '')
    {
        $date_now         = date('Y-m-d');

        // parameter untuk list table
        $table            = 'pembayaran';
        $colum            = ['id', 'approved', 'token_pembayaran', 'invoice', 'inv_jual', 'no_trx', 'tgl_pembayaran', 'jam_pembayaran', 'kembalian', 'total', 'kode_user', 'jenis_pembayaran', 'cash', 'card', 'shift', 'um_masuk', 'kode_jenis_bayar', 'tercover'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param2   = '';
        $kondisi_param1   = 'tgl_pembayaran';

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
            $jenis_bayar      = $this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $rd->kode_jenis_bayar]);

            if ($updated > 0) {
                if ($rd->approved < 1) {
                    $upd_diss = '';
                } else {
                    $upd_diss = 'disabled';
                }
            } else {
                $upd_diss = 'disabled';
            }

            if ($deleted > 0) {
                if ($rd->approved < 1) {
                    $del_diss = '';
                } else {
                    $del_diss = 'disabled';
                }
            } else {
                $del_diss = 'disabled';
            }

            if ($confirmed > 0) {
                $confirm_diss = '';
            } else {
                $confirm_diss = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = date('d/m/Y', strtotime($rd->tgl_pembayaran)) . ' ~ ' . date('H:i:s', strtotime($rd->jam_pembayaran)) . '<br>' . (($rd->approved > 0) ? '<span class="badge badge-primary">Acc</span>' : '<span class="badge badge-danger">Belum diAcc</span>') . ' <span class="badge badge-dark">' . $jenis_bayar->keterangan . '</span>';
            $row[]  = $rd->invoice;
            $row[]  = ($rd->no_trx == null) ? 'UMUM' : $rd->no_trx;
            $row[]  = (($rd->kode_jenis_bayar != 'JB00000001') ? '<span class="text-danger font-weight-bold">TERCOVER</span>' : ($rd->jenis_pembayaran == 0 ? 'CASH' : (($rd->jenis_pembayaran == 1) ? 'CARD' : 'CASH & CARD')));
            $row[]  = 'Rp. ' . '<span class="float-right">' . (($rd->tercover > 0) ? number_format($rd->tercover) : number_format($rd->total - $rd->kembalian - $rd->um_masuk))  . '</span>';
            $row[]  = $this->M_global->getData('user', ['kode_user' => $rd->kode_user])->nama . '<br><span class="badge badge-danger">Shift: ' . $rd->shift . '</span>';

            if ($confirmed > 0) {
                $cek_date = (strtotime($date_now) <= strtotime($rd->tgl_pembayaran)) ? '' : 'disabled';

                if ($rd->approved > 0) {
                    $acc_approved   = 1;
                    $icon           = '<i class="fa-solid fa-circle-xmark"></i>';

                    if (strtotime($date_now) <= strtotime($rd->tgl_pembayaran)) {
                        $cek_date   = '';
                    } else {
                        $cek_date   = 'disabled';
                    }
                    $color          = 'btn-dark';
                } else {
                    $acc_approved   = 0;
                    $icon           = '<i class="fa-solid fa-circle-check"></i>';
                    $cek_date       = '';
                    $color          = 'btn-primary';
                }

                $actived_akun = '<button type="button" style="margin-bottom: 5px;" class="btn ' . $color . '" onclick="actived(' . "'" . $rd->token_pembayaran . "', " . $acc_approved . ')" ' . $confirm_diss . ' ' . $cek_date . '>' . $icon . '</button>';
            } else {
                $actived_akun = '<button type="button" style="margin-bottom: 5px;" class="btn btn-primary" disabled><i class="fa-solid fa-circle-check"></i></button>';
            }

            $row[]  = '<div class="text-center">
                ' . $actived_akun . '
                <button type="button" style="margin-bottom: 5px;" class="btn btn-secondary" onclick="cetak(' . "'" . $rd->token_pembayaran . "', 0" . ')"><i class="fa-solid fa-file-pdf"></i></button>
                <button type="button" style="margin-bottom: 5px;" class="btn btn-info" onclick="email(' . "'" . $rd->token_pembayaran . "'" . ')"><i class="fa-solid fa-envelope-open-text"></i></button>
                <br>
                <button type="button" style="margin-bottom: 5px;" class="btn btn-warning" onclick="ubah(' . "'" . $rd->token_pembayaran . "'" . ')" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" style="margin-bottom: 5px;" class="btn btn-danger" onclick="hapus(' . "'" . $rd->token_pembayaran . "'" . ')" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // fungsi kirim email barang in
    public function email($token_pembayaran)
    {
        $email = $this->input->get('email');

        $header = $this->M_global->getData('pembayaran', ['token_pembayaran' => $token_pembayaran]);

        $jual = $this->M_global->getData('barang_out_header', ['invoice' => $header->inv_jual]);

        $judul = 'Kwitansi ' . $header->invoice;

        // $attched_file    = base_url() . 'assets/file/pdf/' . $judul . '.pdf';ahmad.ummgl@gmail.com
        $attched_file    = $_SERVER["DOCUMENT_ROOT"] . '/first_apps/assets/file/pdf/' . $judul . '.pdf';

        $ready_message   = "";
        $ready_message   .= "<table border=0>
            <tr>
                <td style='width: 30%;'>Invoice</td>
                <td style='width: 10%;'> : </td>
                <td style='width: 60%;'> $header->invoice </td>
            </tr>
            <tr>
                <td style='width: 30%;'>Tgl/Jam</td>
                <td style='width: 10%;'> : </td>
                <td style='width: 60%;'>" . date('d-m-Y', strtotime($header->tgl_pembayaran)) . " / " . date('H:i:s', strtotime($header->jam_pembayaran)) . "</td>
            </tr>
            <tr>
                <td style='width: 30%;'>Pembeli</td>
                <td style='width: 10%;'> : </td>
                <td style='width: 60%;'>" . $this->M_global->getData('member', ['kode_member' => $jual->kode_member])->nama . "</td>
            </tr>
            <tr>
                <td style='width: 30%;'>Gudang</td>
                <td style='width: 10%;'> : </td>
                <td style='width: 60%;'>" . $this->M_global->getData('m_gudang', ['kode_gudang' => $jual->kode_gudang])->nama . "</td>
            </tr>
            <tr>
                <td style='width: 30%;'>Jumlah</td>
                <td style='width: 10%;'> : </td>
                <td style='width: 60%;'>Rp. " . number_format($header->total) . " </td>
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

    // fungsi cetak kwitansi
    public function print_kwitansi($token_pembayaran, $yes)
    {
        $kode_cabang          = $this->session->userdata('cabang');
        $web_setting          = $this->M_global->getData('web_setting', ['id' => 1]);

        $position             = 'P'; // cek posisi l/p

        // body cetakan
        $body                 = '';
        $body                 .= '<br><br>'; // beri jarak antara kop dengan body

        $pembayaran           = $this->M_global->getData('pembayaran', ['token_pembayaran' => $token_pembayaran]);
        $pendaftaran          = $this->M_global->getData('pendaftaran', ['no_trx' => $pembayaran->no_trx]);
        $barang_out_header    = $this->M_global->getData('barang_out_header', ['invoice' => $pembayaran->inv_jual]);
        $barang_out_detail    = $this->M_global->getDataResult('barang_out_detail', ['invoice' => $pembayaran->inv_jual]);
        $tarif_paket_pasien   = $this->M_global->getDataResult('tarif_paket_pasien', ['no_trx' => $pembayaran->no_trx]);
        $tarif_single_pasien  = $this->M_global->getDataResult('pembayaran_tarif_single', ['token_pembayaran' => $token_pembayaran]);
        $member               = $this->M_global->getData('member', ['kode_member' => (($pendaftaran) ? $pendaftaran->kode_member : $barang_out_header->kode_member)]);

        $judul                = 'Kwitansi ' . $pembayaran->invoice;
        $filename             = $judul;

        if ($pembayaran->approved == 1) {
            $open       = '<input type="checkbox" style="width: 80px;" checked="checked"> Lunas';
            $close      = '<input type="checkbox" style="width: 80px;"> Belum Lunas';
        } else {
            $open       = '<input type="checkbox" style="width: 80px;"> Lunas';
            $close      = '<input type="checkbox" style="width: 80px;" checked="checked"> Belum Lunas';
        }

        if ($pembayaran->cek_um == 1) {
            $umopen     = '<input type="checkbox" style="width: 80px;" checked="checked"> Uang Muka';
            $umclose    = '<input type="checkbox" style="width: 80px;"> Member';
        } else {
            if ((($pendaftaran) ? $pendaftaran->kode_member : $barang_out_header->kode_member) != 'U00001') {
                $umopen   = '<input type="checkbox" style="width: 80px;"> Uang Muka';
                $umclose  = '<input type="checkbox" style="width: 80px;" checked="checked"> Member';
            } else {
                $umopen   = '';
                $umclose  = '<input type="checkbox" style="width: 80px;" checked="checked"> Umum';
            }
        }

        $body .= '<table style="width: 100%; font-size: 9px;" cellpadding="2px">';

        $body .= '<tr>
            <td style="text-align: center;">' . date('d/m/Y') . ' ~ ' . date('H:i:s') . '</td>
        </tr>';

        $body .= '</table>';

        $body .= '<table style="width: 100%; font-size: 9px;" cellpadding="2px">';

        $body .= '<tr>
            <td style="width: 23%;">Invoice</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . $pembayaran->invoice . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">Kasir</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . $this->M_global->getData('user', ['kode_user' => $pembayaran->kode_user])->nama . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">Member</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . $member->nama . ' (' . $member->jkel . ', ' . hitung_umur($member->tgl_lahir) . ')</td>
        </tr>
        <tr>
            <td style="width: 23%;">Alamat</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . $this->M_global->getData('m_provinsi', ['kode_provinsi' => $member->provinsi])->provinsi . ', ' . $this->M_global->getData('kabupaten', ['kode_kabupaten' => $member->kabupaten])->kabupaten . ', ' . $this->M_global->getData('kecamatan', ['kode_kecamatan' => $member->kecamatan])->kecamatan . '</td>
        </tr>
        <tr>
            <td style="width: 23%;"></td>
            <td style="width: 2%;"></td>
            <td style="width: 75%;">' . $member->desa . ' (' . $member->kodepos . '), RT/RW (' . $member->rt . '/' . $member->rw . ')</td>
        </tr>';

        $body .= '<tr>
            <td style="width: 100%;" colspan="3">&nbsp;</td>
        </tr>';

        $body .= '</table>';

        $body .= '<table style="width: 100%; font-size: 9px;" cellpadding="2px">';

        $body .= '<tbody>';

        if (!empty($tarif_paket_pasien)) {
            $body .= '<tr>
                <td style="width: 80%; font-weight: bold;" colspan="3">Tarif Paket</td>
                <td style="width: 20%; text-align: right; font-weight: bold;">' . (!empty($tarif_paket_pasien) ? number_format($pembayaran->paket) : 0) . '</td>
            </tr>';

            $body .= '<tr>
                <td style="width: 100%;" colspan="4"><hr style="margin: 0px;"></td>
            </tr>';

            foreach ($tarif_paket_pasien as $tpp) {
                $kode_multiprice = $tpp->kode_multiprice;
                $m_paket = $this->M_global->getData('paket_kunjungan', ['kode_multiprice' => $kode_multiprice, 'kode_tindakan' => $tpp->kode_tindakan, 'kunjungan' => $tpp->kunjungan]);
                $m_tarif = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $tpp->kode_tindakan]);
                $body .= '<tr>
                    <td style="width: 60%;" colspan="2">' . $m_tarif->keterangan . '</td>
                    <td style="text-align: right; width: 20%;">@Kunj ' . number_format($tpp->kunjungan) . '</td>
                    <td style="text-align: right; width: 20%;">' . number_format(($m_paket->klinik + $m_paket->dokter + $m_paket->pelayanan + $m_paket->poli)) . '</td>
                </tr>';
            }

            $body .= '<tr>
                <td style="width: 100%;" colspan="4"><hr style="margin: 0px;"></td>
            </tr>';
        }

        $disc_paket = 0;

        if (!empty($tarif_single_pasien)) {
            $body .= '<tr>
                <td style="width: 80%; font-weight: bold;" colspan="3">Tarif Single</td>
                <td style="width: 20%; text-align: right; font-weight: bold;">' . (!empty($tarif_single_pasien) ? number_format($pembayaran->single) : 0) . '</td>
            </tr>';

            $body .= '<tr>
                <td style="width: 100%;" colspan="4"><hr style="margin: 0px;"></td>
            </tr>';

            foreach ($tarif_single_pasien as $tsp) {
                $m_tindakan = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $tsp->kode_tarif]);
                $body .= '<tr>
                    <td style="width: 40%;">' . $m_tindakan->keterangan . '</td>
                    <td style="text-align: right; width: 20%;">' . number_format($tsp->harga) . '</td>
                    <td style="text-align: right; width: 20%;">' . number_format($tsp->discrp) . '</td>
                    <td style="text-align: right; width: 20%;">' . number_format($tsp->jumlah) . '</td>
                </tr>';
            }

            $body .= '<tr>
                <td style="width: 100%;" colspan="4"><hr style="margin: 0px;"></td>
            </tr>';
        }

        $disc_single = $pembayaran->disc_single;

        if (!empty($barang_out_header)) {
            $body .= '<tr>
                <td style="width: 80%; font-weight: bold;" colspan="3">Penjualan Obat</td>
                <td style="width: 20%; text-align: right; font-weight: bold;">' . (!empty($pembayaran) ? number_format($pembayaran->jual) : 0) . '</td>
            </tr>';

            $body .= '<tr>
                <td style="width: 100%;" colspan="4"><hr style="margin: 0px;"></td>
            </tr>';

            foreach ($barang_out_detail as $bod) {
                $barang = $this->M_global->getData('barang', ['kode_barang' => $bod->kode_barang]);
                $body .= '<tr>
                    <td style="width: 40%;">' . $barang->nama . '(' . $this->M_global->getData('m_satuan', ['kode_satuan' => $barang->kode_satuan])->keterangan . ')' . '</td>
                    <td style="text-align: right; width: 20%;">' . number_format($bod->qty) . ' @ ' . number_format($bod->harga) . '</td>
                    <td style="text-align: right; width: 20%;">' . number_format($bod->discrp) . '</td>
                    <td style="text-align: right; width: 20%;">' . number_format(($bod->jumlah)) . '</td>
                </tr>';
            }

            $body .= '<tr>
                <td style="width: 100%;" colspan="4"><hr style="margin: 0px;"></td>
            </tr>';

            $disc_jual = $barang_out_header->diskon;
        } else {
            $disc_jual = 0;
        }

        $body .= '</tbody>';

        $body .= '</table>';

        $body .= '<page_break>';


        $body .= '<table style="width: 100%; font-size: 9px; padding-top: 35vh;" cellpadding="2px">';

        $body .= '<tr>
            <td style="text-align: center;">' . date('d/m/Y') . ' ~ ' . date('H:i:s') . '</td>
        </tr>';

        $body .= '</table>';

        $body .= '<table style="width: 100%; font-size: 9px;" cellpadding="2px">';

        if ($pembayaran->kode_jenis_bayar == 'JB00000001') {
            $jenis_bayar = '(Cash: Rp. ' . number_format($pembayaran->cash) . ') @ (Card: Rp. ' . number_format($pembayaran->card) . ')';
        } else {
            $jenis_bayar = '(Tercover: Rp. ' . number_format($pembayaran->tercover) . ')';
        }

        $body .= '<tr>
            <td style="width: 23%;">Invoice</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . $pembayaran->invoice . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">Jenis</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . (($pembayaran->kode_jenis_bayar == '') ? 'Perorangan' : $this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $pembayaran->kode_jenis_bayar])->keterangan) . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">Bayar</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . $jenis_bayar . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">Status</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . $open . '&nbsp;&nbsp;' . $close . '</td>
        </tr>';

        if ((($pendaftaran) ? $pendaftaran->kode_member : $barang_out_header->kode_member) != 'U00001') {
            $body .= '<tr>
                <td style="width: 23%;">UM Pakai</td>
                <td style="width: 2%;">:</td>
                <td style="width: 75%;">Rp. ' . number_format($pembayaran->um_keluar) . '</td>
            </tr>';
        }

        $body .= '<tr>
            <td style="width: 100%;" colspan="3">&nbsp;</td>
        </tr>';

        $body .= '</table>';

        $body .= '<table style="width: 50%; font-size: 9px;" cellpadding="2px" autosize="1">
            <tr>
                <td style="width: 38%;">Total</td>
                <td style="width: 2%;">: </td>
                <td style="text-align: right; font-weight: bold; width: 60%;">' . number_format($pembayaran->paket + $pembayaran->single + $pembayaran->jual) . '</td>
            </tr>
            <tr>
                <td style="width: 38%;">Pembayaran</td>
                <td style="width: 2%;">: </td>
                <td style="text-align: right; font-weight: bold; width: 60%;">' . number_format($pembayaran->total) . '</td>
            </tr>
            <tr>
                <td style="width: 38%;">Kembalian</td>
                <td style="width: 2%;">: </td>
                <td style="text-align: right; font-weight: bold; width: 60%;">' . (($pembayaran->cek_um == 1) ? number_format($pembayaran->um_masuk) : number_format($pembayaran->kembalian)) . '</td>
            </tr>
            <tr>
                <td colspan="3">' . $umopen . '&nbsp;&nbsp;' . $umclose . '</td>
            </tr>
            <tr>
        </table>';

        if ($pembayaran->approved == 1) {
            $body .= '<div style="position: fixed; top: 55%; left: 58%; transform: translate(-55%, -58%); pointer-events: none;">
                <img src="' . base_url('assets/img/web/lunas.png') . '" style="width: 100px; opacity: 0.5;">
            </div>';
        }

        cetak_pdf_small($judul, $body, 1, $position, $filename, $web_setting, $yes);
    }

    // fungsi aktif/non-aktif pembayaran
    public function actived_pembayaran($token_pembayaran, $batal)
    {
        $user_batal = $this->session->userdata('kode_user');
        $pembayaran = $this->M_global->getData('pembayaran', ['token_pembayaran' => $token_pembayaran]);

        if ($batal == 0) { // jika batal = 0
            // cek um keluar
            if ($pembayaran->um_keluar > 0) {
                $um_keluar = $pembayaran->um_keluar;
                $this->db->query("UPDATE uang_muka SET uang_keluar = uang_keluar + '$um_keluar', uang_sisa = uang_sisa - '$um_keluar' WHERE last_invoice = '$pembayaran->invoice'");

                $this->db->query("UPDATE piutang SET status = 1, tanggal_bayar = '" . date('Y-m-d') . "', jam_bayar = '" . date('H:i:s') . "' WHERE referensi = '$pembayaran->invoice' AND jenis = 1");
            }

            // cek um_masuk
            if ($pembayaran->cek_um > 0) {
                $um_masuk = $pembayaran->um_masuk;
                $this->db->query("UPDATE uang_muka SET uang_masuk = uang_masuk + '$um_masuk', uang_sisa = uang_sisa + '$um_masuk' WHERE last_invoice = '$pembayaran->invoice'");

                $this->db->query("UPDATE piutang SET status = 1, tanggal_bayar = '" . date('Y-m-d') . "', jam_bayar = '" . date('H:i:s') . "' WHERE referensi = '$pembayaran->invoice' AND jenis = 0");
            }

            // jika non tunai
            if ($pembayaran->kode_jenis_bayar != 'JB00000001') {
                $this->M_global->updateData('piutang', ['tanggal' => date('Y-md'), 'jam' => date('H:i:s'), 'jumlah' => $pembayaran->total], ['referensi' => $pembayaran->invoice, 'jenis' => 1]);
            }

            // update batal jadi 0
            $cek = [
                $this->M_global->updateData('pembayaran', ['approved' => 1, 'batal' => 0, 'tgl_batal' => null, 'jam_batal' => null, 'user_batal' => null], ['token_pembayaran' => $token_pembayaran]),
                $this->M_global->updateData('pendaftaran', ['status_trx' => 1, 'tgl_keluar' => date('Y-m-d'), 'jam_keluar' => date('H:i:s')], ['no_trx' => $pembayaran->no_trx]),
                $this->M_global->updateData('member', ['status_regist' => 1], ['last_regist' => $pembayaran->no_trx]),
                $this->M_global->updateData('tarif_paket_pasien', ['status' => 1], ['no_trx' => $pembayaran->no_trx]),
                $this->M_global->updateData('daftar_ulang', ['status_ulang' => 1], ['no_trx' => $pembayaran->no_trx]),
            ];
        } else { // selain itu
            // cek um keluar
            if ($pembayaran->um_keluar > 0) {
                $um_keluar = $pembayaran->um_keluar;
                $this->db->query("UPDATE uang_muka SET uang_keluar = uang_keluar - '$um_keluar', uang_sisa = uang_sisa + '$um_keluar' WHERE last_invoice = '$pembayaran->invoice'");
            }

            // cek um_masuk
            if ($pembayaran->cek_um > 0) {
                $um_masuk = $pembayaran->um_masuk;
                $this->db->query("UPDATE uang_muka SET uang_masuk = uang_masuk - '$um_masuk', uang_sisa = uang_sisa - '$um_masuk' WHERE last_invoice = '$pembayaran->invoice'");

                $this->M_global->updateData('piutang', ['tanggal' => null, 'jam' => null, 'jumlah' => 0], ['referensi' => $pembayaran->invoice, 'jenis' => 0]);
            }

            // jika non tunai
            if ($pembayaran->kode_jenis_bayar != 'JB00000001') {
                $this->M_global->updateData('piutang', ['tanggal' => null, 'jam' => null, 'jumlah' => 0], ['referensi' => $pembayaran->invoice, 'jenis' => 1]);
            }

            // update batal jadi 1
            $cek = [
                $this->M_global->updateData('pembayaran', ['approved' => 0, 'batal' => 1, 'tgl_batal' => date('Y-m-d'), 'jam_batal' => date('H:i:s'), 'user_batal' => $user_batal], ['token_pembayaran' => $token_pembayaran]),
                $this->M_global->updateData('pendaftaran', ['status_trx' => 0, 'tgl_keluar' => null, 'jam_keluar' => null], ['no_trx' => $pembayaran->no_trx]),
                $this->M_global->updateData('member', ['status_regist' => 0], ['last_regist' => $pembayaran->no_trx]),
                $this->M_global->updateData('tarif_paket_pasien', ['status' => 0], ['no_trx' => $pembayaran->no_trx]),
                $this->M_global->updateData('daftar_ulang', ['status_ulang' => 0], ['no_trx' => $pembayaran->no_trx]),
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

    // fungsi hapus pembayaran
    public function delPembayaran($token_pembayaran)
    {
        $pembayaran = $this->M_global->getData('pembayaran', ['token_pembayaran' => $token_pembayaran]);

        $jual       = $this->M_global->getData('barang_out_header', ['invoice' => $pembayaran->inv_jual]);
        if ($jual) {
            $km = $jual->kode_member;
        } else {
            $pendaftaran = $this->M_global->getData('pendaftaran', ['no_trx' => $pembayaran->no_trx]);
            $km = $pendaftaran->kode_member;
        }

        if ($pembayaran->cek_um == 1) {
            $um_awal = $pembayaran;
            $total_awal = $um_awal->kembalian;

            updateUangMukaUpdate($km, $pembayaran->invoice, $pembayaran->tgl_pembayaran, $pembayaran->jam_pembayaran, 0, $total_awal);
        }

        $cek_retur = $this->M_global->getData('barang_out_retur_header', ['invoice' => $pembayaran->inv_jual]);

        if ($pembayaran->no_trx != null) {
            $this->M_global->updateData('pendaftaran', ['status_trx' => 0], ['no_trx' => $pembayaran->no_trx]);
        }

        $this->M_global->updateData('member', ['status_regist' => 1], ['last_regist' => $pembayaran->no_trx]);

        if ($cek_retur) {
            $kasir = $this->M_global->updateData('barang_out_retur_header', ['status_retur' => 0], ['invoice' => $pembayaran->inv_jual]);
        } else {
            if ($jual) {
                $kasir = $this->M_global->updateData('barang_out_header', ['status_jual' => 0], ['invoice' => $pembayaran->inv_jual]);
            } else {
                $kasir = '';
            }
        }

        if ($kasir) {
            $kasir = $kasir;
        } else {
            $kasir = '';
        }

        if ($pembayaran->kode_jenis_bayar != 'JB00000001') {
            $this->M_global->delData('piutang', ['referensi' => $pembayaran->invoice]);
        }

        $cek = [
            $kasir,
            $this->M_global->delData('pembayaran', ['token_pembayaran' => $token_pembayaran]),
            $this->M_global->delData('pembayaran_tarif_single', ['token_pembayaran' => $token_pembayaran]),
            $this->M_global->delData('bayar_card_detail', ['token_pembayaran' => $token_pembayaran]),
            $this->M_global->updateData('tarif_paket_pasien', ['status' => 0], ['no_trx' => $pembayaran->no_trx]),
            $this->M_global->delData('daftar_ulang', ['no_trx' => $pembayaran->no_trx]),
        ];

        if ($cek) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // form kasir page
    public function form_kasir($param, $no_trx = '')
    {
        $kode_cabang = $this->session->userdata('cabang');
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param == '0') {
            $pembayaran = null;

            if ($no_trx == '') {
                $pendaftaran2 = '';
            } else {
                $pendaftaran2 = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);
            }

            $riwayat        = null;
            $bayar_detail   = null;
            $tarif_paket    = null;
            $single_tarif   = null;
            $penjualan      = null;

            $pendaftaranx   = $this->M_global->getDataResult('pendaftaran', ['kode_cabang' => $kode_cabang, 'status_trx' => 0]);
            $jualx          = $this->M_global->getDataResult('barang_out_header', ['kode_cabang' => $kode_cabang, 'status_jual' => 0]);
        } else {
            $bayar_detail   = $this->M_global->getDataResult('bayar_card_detail', ['token_pembayaran' => $param]);
            $pembayaran     = $this->M_global->getData('pembayaran', ['token_pembayaran' => $param]);
            $pendaftaran    = $this->M_global->getData('pendaftaran', ['no_trx' => $pembayaran->no_trx]);
            $pendaftaran2   = $pendaftaran;
            $jualx          = $this->M_global->getDataResult('barang_out_header', ['kode_cabang' => $kode_cabang, 'invoice' => $pembayaran->inv_jual]);
            if (!empty($pendaftaran)) {
                $tarif_paket    = $this->M_global->getDataResult('tarif_paket_pasien', ['no_trx' => $pendaftaran->no_trx]);

                $kode_member    = $pendaftaran->kode_member;

                $riwayat        = $this->M_global->getDataResult('pendaftaran', ['kode_member' => $kode_member]);
                $single_tarif   = $this->M_global->getDataResult('pembayaran_tarif_single', ['token_pembayaran' => $param]);
                $penjualan      = $this->db->query("SELECT bo.*, b.nama AS nama_barang, s.keterangan AS nama_satuan FROM barang_out_detail bo JOIN barang b ON b.kode_barang = bo.kode_barang JOIN m_satuan s ON s.kode_satuan = bo.kode_satuan WHERE bo.invoice = '$pembayaran->inv_jual'")->result();

                $pendaftaranx   = $this->M_global->getDataResult('pendaftaran', ['kode_cabang' => $kode_cabang, 'no_trx' => $pembayaran->no_trx]);
            } else {
                $tarif_paket    = null;
                $riwayat        = null;
                $single_tarif   = null;
                $penjualan      = null;
                $pendaftaranx   = null;
            }
        }

        $parameter = [
            $this->data,
            'judul'             => 'Pembayaran',
            'nama_apps'         => $web_setting->nama,
            'page'              => 'Pembayaran',
            'web'               => $web_setting,
            'web_version'       => $web_version->version,
            'list_data'         => '',
            'no_trx'            => $no_trx,
            'data_pembayaran'   => $pembayaran,
            'pendaftaran'       => $pendaftaranx,
            'pendaftaran2'      => $pendaftaran2,
            'data_penjualan'    => $jualx,
            'bayar_detail'      => $bayar_detail,
            'tarif_paket'       => $tarif_paket,
            'riwayat'           => $riwayat,
            'single_tarif'      => $single_tarif,
            'penjualan'         => $penjualan,
            'role'              => $this->M_global->getResult('m_role'),
            'promo'             => $this->M_global->getDataResult('m_promo', ['kode_cabang' => $kode_cabang]),
            'ulang'             => $this->M_global->getData('daftar_ulang', ['no_trx']),
        ];

        $this->template->load('Template/Content', 'Kasir/Form_pembayaran', $parameter);
    }

    public function cekPendaftaran($notrx)
    {
        $cek = $this->M_global->getData('pendaftaran', ['no_trx' => $notrx]);

        if ($cek) {
            $jenis_bayar = $this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $cek->kode_jenis_bayar]);
            $jual = $this->M_global->getData('barang_out_header', ['no_trx' => $notrx]);
            if ($jual) {
                $inv_jual = $jual->invoice;
            } else {
                $inv_jual = '';
            }

            echo json_encode(['status' => 1, 'norm' => $cek->kode_member, 'no_trx' => $cek->no_trx, 'jenis_bayar' => $jenis_bayar->keterangan, 'kode_jenis_bayar' => $cek->kode_jenis_bayar, 'inv_jual' => $inv_jual]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    public function cekJenisBayar($no_trx)
    {
        $pendaftaran = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);

        if ($pendaftaran) {
            $jenis_bayar = $this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $pendaftaran->kode_jenis_bayar]);
            echo json_encode(['status' => 1, 'jenis_bayar' => $jenis_bayar->keterangan, 'kode_jenis_bayar' => $pendaftaran->kode_jenis_bayar]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    public function ubahJenisBayar($no_trx, $kode_jenis_bayar)
    {
        $pendaftaran = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);
        $penjamin_awal = $this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $pendaftaran->kode_jenis_bayar]);
        $penjamin_baru = $this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $kode_jenis_bayar]);

        $isi_sebelum = json_encode($pendaftaran);

        if ($pendaftaran->kode_jenis_bayar == $kode_jenis_bayar) {
            echo json_encode(['status' => 0, 'kode' => $pendaftaran->kode_jenis_bayar, 'ket_jb' => $this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $pendaftaran->kode_jenis_bayar])->keterangan]);
        } else {
            $this->M_global->updateData('pendaftaran', ['kode_jenis_bayar' => $kode_jenis_bayar], ['no_trx' => $no_trx]);
            $isi_sesudah = json_encode($this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]));
            aktifitas_user('Kasir', 'Merubah penjamin', $no_trx, 'dari penjamin: ' . $penjamin_awal->keterangan . ' (' . $pendaftaran->kode_jenis_bayar . '), menjadi: ' . $penjamin_baru->keterangan . ' (' . $kode_jenis_bayar . ')', $isi_sesudah, $isi_sebelum);

            echo json_encode(['status' => 1, 'penjamin' => $kode_jenis_bayar, 'kelas' => $pendaftaran->kelas, 'poli' => $pendaftaran->kode_poli]);
        }
    }

    public function ubahHargaTindakan($no_trx)
    {
        $kelas = $this->input->get('kelas');
        $kode_jenis_bayar = $this->input->get('kode_jenis_bayar');
        $kode_poli = $this->input->get('kode_poli');

        $tarif = $this->db->query(
            'SELECT * FROM(
                SELECT id, no_trx, kode_multiprice, kode_tarif, kelas, penjamin, poli, qty FROM emr_tarif

                UNION ALL

                SELECT id, no_trx, kode_multiprice, kode_tarif, kelas, penjamin, poli, qty FROM emr_lab

                UNION ALL

                SELECT id, no_trx, kode_multiprice, kode_tarif, kelas, penjamin, poli, qty FROM emr_rad
            ) AS semua WHERE no_trx = "' . $no_trx . '"'
        )->result();

        $data = [];
        foreach ($tarif as $t) {
            $m_tarif = $this->M_global->getData('multiprice_tindakan', [
                'kode_tindakan' => $t->kode_tarif,
                'kode_penjamin' => $kode_jenis_bayar,
                'kode_poli' => $kode_poli,
                'kelas' => $kelas
            ]);
            $m_tarif2 = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $t->kode_tarif]);
            $data[] = [
                'kode_tarif'        => $m_tarif->kode_multiprice,
                'nama_tarif'        => $m_tarif2->keterangan,
                'jasa_rs'           => $m_tarif->klinik,
                'jasa_dokter'       => $m_tarif->dokter,
                'jasa_pelayanan'    => $m_tarif->pelayanan,
                'jasa_poli'         => $m_tarif->poli,
                'jasa_total'        => ($m_tarif->klinik + $m_tarif->dokter + $m_tarif->pelayanan + $m_tarif->poli),
            ];
        }

        echo json_encode([['status' => 1], $data]);
    }

    public function getMember($kode_member, $no_trx = '')
    {
        try {
            // Get registration and member data
            $pendaftaran = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);
            $member = $this->M_global->getData('member', ['kode_member' => $kode_member]);

            if (!$member) {
                return $this->output->set_content_type('application/json')
                    ->set_output(json_encode(['status' => 0]));
            }

            // Handle general member case
            if ($member->kode_member == 'U00001') {
                $data = [
                    'status' => 1,
                    'cek' => 0,
                    'norm' => 'U00001',
                    'nama' => 'Umum',
                    'umur' => 'x tahun x bulan x hari',
                    'nohp' => '-',
                    'poli' => 'Umum',
                    'dokter' => '-',
                    'alamat' => '-',
                    'kelas' => 'Umum',
                    'kode_poli' => '',
                ];

                return $this->output->set_content_type('application/json')
                    ->set_output(json_encode($data));
            }

            // Get location data
            $location = $this->getLocationData($member);

            // Get prefix data
            $prefix = $this->getPrefixName($member->kode_prefix);

            // Get clinic and doctor data if registration exists
            $clinic = null;
            $doctor = null;
            if ($pendaftaran) {
                $clinic = $this->M_global->getData('m_poli', ['kode_poli' => $pendaftaran->kode_poli]);
                $doctor = $this->M_global->getData('dokter', ['kode_dokter' => $pendaftaran->kode_dokter]);
            }

            // Build member data response
            $data = [
                'status' => 1,
                'cek' => 1,
                'norm' => $kode_member,
                'nama' => $prefix . '. ' . $member->nama,
                'umur' => hitung_umur($member->tgl_lahir),
                'nohp' => $member->nohp,
                'poli' => $clinic ? $clinic->keterangan : '-',
                'dokter' => $doctor ? 'Dr. ' . $doctor->nama : '-',
                'alamat' => $this->formatAddress($member, $location),
                'kelas' => $pendaftaran ? $pendaftaran->kelas : '-',
                'kode_poli' => $pendaftaran ? $pendaftaran->kode_poli : '',
            ];

            return $this->output->set_content_type('application/json')
                ->set_output(json_encode($data));
        } catch (Exception $e) {
            log_message('error', 'Error in getMember: ' . $e->getMessage());
            return $this->output->set_content_type('application/json')
                ->set_output(json_encode(['status' => 0, 'message' => 'Internal server error']));
        }
    }

    private function getLocationData($member)
    {
        return [
            'provinsi' => $this->M_global->getData('m_provinsi', ['kode_provinsi' => $member->provinsi])->provinsi,
            'kabupaten' => $this->M_global->getData('kabupaten', ['kode_kabupaten' => $member->kabupaten])->kabupaten,
            'kecamatan' => $this->M_global->getData('kecamatan', ['kode_kecamatan' => $member->kecamatan])->kecamatan
        ];
    }

    private function getPrefixName($kode_prefix)
    {
        $prefix = $this->M_global->getData('m_prefix', ['kode_prefix' => $kode_prefix]);
        return $prefix ? $prefix->nama : 'None';
    }

    private function formatAddress($member, $location)
    {
        return sprintf(
            'Prov. %s, %s, Kec. %s, Ds. %s, (POS: %s), RT.%s/RW.%s',
            $location['provinsi'],
            $location['kabupaten'],
            $location['kecamatan'],
            $member->desa,
            $member->kodepos,
            $member->rt,
            $member->rw
        );
    }

    public function getJual($invoice)
    {
        $barang_out = $this->db->query("SELECT bo.*, b.nama AS nama_barang, s.keterangan AS nama_satuan FROM barang_out_detail bo JOIN barang b ON b.kode_barang = bo.kode_barang JOIN m_satuan s ON s.kode_satuan = bo.kode_satuan WHERE bo.invoice = '$invoice'")->result();

        echo json_encode($barang_out);
    }

    public function getTarifSingle($kode_multiprice)
    {
        $multiprice = $this->M_global->getData('multiprice_tindakan', ['kode_multiprice' => $kode_multiprice]);

        $tarif = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $multiprice->kode_tindakan]);

        $data = [
            'status'            => 1,
            'jasa_rs'           => $multiprice->klinik,
            'jasa_dokter'       => $multiprice->dokter,
            'jasa_pelayanan'    => $multiprice->pelayanan,
            'jasa_poli'         => $multiprice->poli,
            'jasa_total'        => ($multiprice->klinik + $multiprice->dokter + $multiprice->pelayanan + $multiprice->poli),
        ];

        echo json_encode($data);
    }

    public function getTarif($no_trx)
    {
        $pendaftaran = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);
        $tarif = $this->db->query(
            'SELECT * FROM(
                SELECT id, no_trx, kode_multiprice, kode_tarif, kelas, penjamin, poli, qty FROM emr_tarif

                UNION ALL

                SELECT id, no_trx, kode_multiprice, kode_tarif, kelas, penjamin, poli, qty FROM emr_lab

                UNION ALL

                SELECT id, no_trx, kode_multiprice, kode_tarif, kelas, penjamin, poli, qty FROM emr_rad
            ) AS semua WHERE no_trx = "' . $no_trx . '"'
        )->result();

        $data = [];
        foreach ($tarif as $t) {
            $m_tarif = $this->M_global->getData('multiprice_tindakan', ['kode_multiprice' => $t->kode_multiprice]);
            $m_tarif2 = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $t->kode_tarif]);
            $data[] = [
                'kode_tarif'        => $m_tarif->kode_multiprice,
                'nama_tarif'        => $m_tarif2->keterangan,
                'jasa_rd'           => $m_tarif->klinik,
                'jasa_dokter'       => $m_tarif->dokter,
                'jasa_pelayanan'    => $m_tarif->pelayanan,
                'jasa_poli'         => $m_tarif->poli,
                'harga'             => ($m_tarif->klinik + $m_tarif->dokter + $m_tarif->pelayanan + $m_tarif->poli),
            ];
        }

        echo json_encode([['status' => 1, 'kode_member' => $pendaftaran->kode_member], $data]);
    }

    public function getPaket($no_trx)
    {
        $kode_cabang = $this->session->userdata('cabang');
        $pendaftaran = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);
        $tarif = $this->M_global->getDataResult('tarif_paket_pasien', ['no_trx' => $no_trx]);
        $jual = $this->M_global->getData('barang_out_header', ['no_trx' => $no_trx]);

        if ($jual) {
            $invoice = $jual->invoice;
        } else {
            $invoice = '';
        }

        $data = [];
        foreach ($tarif as $t) {
            $m_tarif = $this->M_global->getData('paket_kunjungan', ['kode_multiprice' => $t->kode_multiprice, 'kunjungan' => $t->kunjungan]);
            $m_tarif2 = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $t->kode_tindakan]);
            $data[] = [
                'kode_tarif' => $m_tarif->kode_tindakan,
                'nama_tarif' => $m_tarif2->keterangan,
                'kunjungan' => $t->kunjungan,
                'harga' => ($m_tarif->klinik + $m_tarif->dokter + $m_tarif->pelayanan + $m_tarif->poli),
            ];
        }

        echo json_encode([['status' => 1, 'invoice' => $invoice, 'kode_member' => $pendaftaran->kode_member], $data]);
    }

    // fungsi get Info
    public function getInfoJual($inv_jual)
    {
        $data = $this->M_global->getData('barang_out_header', ['invoice' => $inv_jual]);
        $kode_member = $data->kode_member;

        if ($data) {
            echo json_encode([$data, ['kode_member' => $kode_member]]);
        } else {
            echo json_encode(['status' => 0, 'kode_member' => $kode_member]);
            if ($kode_member == '') {
                echo json_encode(['status' => 1, 'total' => $data->total]);
            } else {
                echo json_encode(['status' => 0, 'kode_member' => $kode_member]);
            }
        }
    }

    // fungsi get info um
    public function getInfoUM($kode_member)
    {
        $data = $this->M_global->getData('uang_muka', ['kode_member' => $kode_member]);

        if ($data) {
            echo json_encode($data);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi proses insert/update
    public function kasir_proses($param)
    {
        $kode_cabang            = $this->session->userdata('cabang');
        $shift                  = $this->session->userdata('shift');

        if ($param == 1) { // jika param 1
            // buat token dan invoice
            $token_pembayaran   = tokenKasir(30);
            $invoice            = _invoiceKasir($kode_cabang);
        } else { // selain itu
            // ambil token dan invoice dari inputan
            $token_pembayaran   = $this->input->post('token_pembayaran');
            $invoice            = $this->input->post('invoice');
        }

        // variable
        $no_trx                 = $this->input->post('no_trx');
        $jenis_pembayaran       = $this->input->post('jenis_pembayaran');
        $tgl_pembayaran         = $this->input->post('tgl_pembayaran');
        $jam_pembayaran         = $this->input->post('jam_pembayaran');
        $inv_jual               = $this->input->post('inv_jual');
        $kode_promo             = $this->input->post('kode_promo');
        $cek_um                 = $this->input->post('cek_um');
        $kode_member            = $this->input->post('kode_member');
        $discpr_promo           = str_replace(',', '', $this->input->post('potongan_promo'));
        $discrp_promo           = str_replace(',', '', $this->input->post('potongan_promo_rp'));
        $paket                  = str_replace(',', '', $this->input->post('sumPaket'));
        $jual                   = str_replace(',', '', $this->input->post('sumJual'));
        $single                 = str_replace(',', '', $this->input->post('sumTarif'));
        $disc_single            = str_replace(',', '', $this->input->post('discTarif'));

        $tercover               = str_replace(',', '', $this->input->post('tercover'));
        $kode_jenis_bayar       = $this->input->post('kode_jenis_bayar');

        if ($kode_jenis_bayar == '') {
            $kode_jenis_bayar   = 'JB00000001';
        } else {
            $kode_jenis_bayar   = $kode_jenis_bayar;
        }

        $kode_tarif             = $this->input->post('kode_tarif');
        $kunjungan              = $this->input->post('kunjungan');

        $kode_tarif_single      = $this->input->post('kode_tarif_single');
        $harga                  = $this->input->post('jasa_total');
        $discpr                 = $this->input->post('discpr_tarif');
        $discrp                 = $this->input->post('discrp_tarif');
        $jumlah                 = $this->input->post('jumlah_tarif');

        $tgl_ulang              = $this->input->post('tgl_ulang');
        $status_ulang           = $this->input->post('status_ulang');

        // query barang out header
        $cek_pendaftaran        = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);

        // ambil kode member
        if ($cek_pendaftaran) { // jika ada di barang out header
            $kode_member        = $cek_pendaftaran->kode_member;
            // ambil notrx nya
            $no_trx             = $cek_pendaftaran->no_trx;

            // update status_trx di pendaftaran menjadi 1
            $this->M_global->updateData('pendaftaran', ['status_trx' => 1, 'tgl_keluar' => $tgl_pembayaran, 'jam_keluar' => $jam_pembayaran], ['no_trx' => $no_trx]);
            $this->M_global->updateData('member', ['status_regist' => 1], ['last_regist' => $no_trx]);

            if ($param == 1) {
                if ($status_ulang == 1) {
                    $this->M_global->insertData('daftar_ulang', ['kode_member' => $kode_member, 'kode_cabang' => $kode_cabang, 'no_trx' => $no_trx, 'tgl_ulang' => $tgl_ulang, 'status_ulang' => $status_ulang, 'kode_dokter' => $cek_pendaftaran->kode_dokter, 'kode_poli' => $cek_pendaftaran->kode_poli]);
                }
            } else {
                if ($status_ulang == 0) {
                    $this->M_global->delData('daftar_ulang', ['no_trx' => $no_trx]);
                } else {
                    $cek_ulang = $this->M_global->getData('daftar_ulang', ['no_trx' => $no_trx]);

                    if ($cek_ulang) {
                        $this->M_global->updateData('daftar_ulang', ['tgl_ulang' => $tgl_ulang, 'status_ulang' => $status_ulang], ['no_trx' => $no_trx]);
                    } else {
                        $this->M_global->insertData('daftar_ulang', ['kode_member' => $kode_member, 'kode_cabang' => $kode_cabang, 'no_trx' => $no_trx, 'tgl_ulang' => $tgl_ulang, 'status_ulang' => $status_ulang, 'kode_dokter' => $cek_pendaftaran->kode_dokter, 'kode_poli' => $cek_pendaftaran->kode_poli]);
                    }
                }
            }
        } else { // selain itu
            // notrx null
            // cek kode member
            $penjualan          = $this->M_global->getData('barang_out_header', ['invoice' => $inv_jual]);
            if ($penjualan) {
                $kode_member    = $penjualan->kode_member;
            } else {
                $kode_member    = null;
            }
            $no_trx             = null;
        }

        // variable card
        $cash                   = str_replace(',', '', $this->input->post('cash'));
        $card                   = str_replace(',', '', $this->input->post('card'));
        $total                  = str_replace(',', '', $this->input->post('total'));
        $kembalian              = str_replace(',', '', $this->input->post('total_kurang'));
        $um_keluar              = str_replace(',', '', $this->input->post('um_keluar'));
        $kode_user              = $this->session->userdata('kode_user');

        // isi pembayaran
        $isi_pembayaran = [
            'kode_cabang'       => $kode_cabang,
            'token_pembayaran'  => $token_pembayaran,
            'approved'          => 1,
            'invoice'           => $invoice,
            'inv_jual'          => $inv_jual,
            'no_trx'            => $no_trx,
            'tgl_pembayaran'    => $tgl_pembayaran,
            'jam_pembayaran'    => $jam_pembayaran,
            'paket'             => $paket,
            'single'            => $single,
            'jual'              => $jual,
            'disc_single'       => $disc_single,
            'total'             => $total,
            'kode_user'         => $kode_user,
            'shift'             => $shift,
            'um_keluar'         => $um_keluar,
            'jenis_pembayaran'  => $jenis_pembayaran,
            'cash'              => $cash,
            'card'              => $card,
            'kode_promo'        => $kode_promo,
            'discpr_promo'      => $discpr_promo,
            'discrp_promo'      => $discrp_promo,
            'kembalian'         => ($cek_um > 0) ? 0 : $kembalian,
            'um_masuk'          => ($cek_um > 0) ? $kembalian : 0,
            'cek_um'            => $cek_um,
            'tercover'          => $tercover,
            'kode_jenis_bayar'  => $kode_jenis_bayar,
            'kode_member'       => $kode_member,
        ];

        $nopiutang = _noPiutang($kode_cabang);

        $data_cover = [
            'piutang_no'    => $nopiutang,
            'referensi'     => $invoice,
            'kode_cabang'   => $kode_cabang,
            'tanggal'       => $tgl_pembayaran,
            'jam'           => $jam_pembayaran,
            'jumlah'        => $tercover,
            'status'        => 0,
            'jenis'         => 1,
        ];

        $data_cover2 = [
            'piutang_no'    => $nopiutang,
            'referensi'     => $invoice,
            'kode_cabang'   => $kode_cabang,
            'tanggal'       => $tgl_pembayaran,
            'jam'           => $jam_pembayaran,
            'jumlah'        => $kembalian,
            'status'        => 0,
            'jenis'         => 0,
        ];

        if ($param > 1) {
            // cek bagian piutang, bpjs = 1, pasien = 0
            $cek_piutang = $this->M_global->getData('piutang', ['referensi' => $invoice]);

            if ($cek_piutang) { // jika ada piutang
                $this->M_global->delData('piutang', ['referensi' => $invoice]);
            }

            if (isset($kode_tarif)) {
                $this->M_global->updateData('tarif_paket_pasien', ['status' => 0], ['no_trx' => $no_trx]);

                $jumPaket = count($kode_tarif);

                for ($x = 0; $x <= ($jumPaket - 1); $x++) {
                    $this->M_global->updateData('tarif_paket_pasien', ['status' => 1], ['no_trx' => $no_trx, 'kode_tarif' => $kode_tarif[$x], 'kunjungan' => $kunjungan[$x]]);
                }
            }

            $um_awal = $this->M_global->getData('pembayaran', ['invoice' => $invoice]);
            $total_awal = $um_awal->kembalian;

            updateUangMukaUpdate($kode_member, $invoice, $tgl_pembayaran, $jam_pembayaran, $kembalian, $total_awal);

            // update pembayaran dan hapus cardnya
            $cek = [
                $this->M_global->updateData('pembayaran', $isi_pembayaran, ['token_pembayaran' => $token_pembayaran]),
                $this->M_global->delData('bayar_card_detail', ['token_pembayaran' => $token_pembayaran]),
                $this->M_global->delData('pembayaran_tarif_single', ['token_pembayaran' => $token_pembayaran]),
            ];
        } else {
            // insert ke pembayaran
            $update_um = $this->db->query("UPDATE uang_muka SET 
                last_tgl = '$tgl_pembayaran', 
                last_jam = '$jam_pembayaran', 
                last_invoice = '$invoice', 
                uang_keluar = uang_keluar + '$um_keluar', 
                uang_sisa = uang_sisa - '$um_keluar' 
            WHERE kode_member = '$kode_member'");

            if ($cek_um == 1) {
                updateUangMukaIn($kode_member, $invoice, $tgl_pembayaran, $jam_pembayaran, $kembalian);
            }

            $cek = [
                $this->M_global->insertData('pembayaran', $isi_pembayaran),
                $update_um,
            ];

            if (isset($kode_tarif)) {
                $jumPaket = count($kode_tarif);

                for ($x = 0; $x <= ($jumPaket - 1); $x++) {
                    $this->M_global->updateData('tarif_paket_pasien', ['status' => 1], ['no_trx' => $no_trx, 'kode_tindakan' => $kode_tarif[$x], 'kunjungan' => $kunjungan[$x]]);
                }
            }
        }

        $cek_emr_tarif = $this->M_global->getData('emr_tarif', ['no_trx' => $no_trx]);
        $cek_emr_lab = $this->M_global->getData('emr_lab', ['no_trx' => $no_trx]);
        $cek_emr_rad = $this->M_global->getData('emr_rad', ['no_trx' => $no_trx]);

        if ($cek_emr_tarif) {
            $this->M_global->updateData('emr_tarif', ['penjamin' => $kode_jenis_bayar], ['no_trx' => $no_trx]);
        }

        if ($cek_emr_lab) {
            $this->M_global->updateData('emr_lab', ['penjamin' => $kode_jenis_bayar], ['no_trx' => $no_trx]);
        }

        if ($cek_emr_rad) {
            $this->M_global->updateData('emr_rad', ['penjamin' => $kode_jenis_bayar], ['no_trx' => $no_trx]);
        }

        if ($kode_jenis_bayar != 'JB00000001') {
            $this->M_global->insertData('piutang', $data_cover);
        }

        if ($cek_um > 0) {
            $this->M_global->insertData('piutang', $data_cover2);
        }

        if ($cek) { // jika fungsi cek berjalan
            // variable detail card
            $kode_bank    = $this->input->post('kode_bank');
            $tipe_bank    = $this->input->post('tipe_bank');
            $no_card      = $this->input->post('no_card');
            $approval     = $this->input->post('approval');
            $jumlah_card  = $this->input->post('jumlah_card');

            if (!empty($kode_bank)) { // jika kodebank exist/ ada
                // ambil jumlah row berdasarkan kode_bank
                $jum = count($kode_bank);

                // lakukan loop dengan for
                for ($x = 0; $x <= ($jum - 1); $x++) {
                    $_kode_bank   = $kode_bank[$x];
                    $_tipe_bank   = $tipe_bank[$x];
                    $_no_card     = $no_card[$x];
                    $_approval    = $approval[$x];
                    $_jumlah_card = str_replace(',', '', $jumlah_card[$x]);

                    // isi detail card
                    $isi_card = [
                        'token_pembayaran'  => $token_pembayaran,
                        'kode_cabang'       => $kode_cabang,
                        'kode_bank'         => $_kode_bank,
                        'kode_tipe'         => $_tipe_bank,
                        'no_card'           => $_no_card,
                        'approval'          => $_approval,
                        'jumlah'            => $_jumlah_card,
                    ];

                    // insert ke bayar_card_detail
                    $this->M_global->insertData('bayar_card_detail', $isi_card);
                }
            }

            if (isset($kode_tarif_single)) {
                $jumTarif = count($kode_tarif_single);

                for ($y = 0; $y <= ($jumTarif - 1); $y++) {
                    $kode_single    = $kode_tarif_single[$y];
                    $harga_single   = str_replace(',', '', $harga[$y]);
                    $discpr_single  = str_replace(',', '', $discpr[$y]);
                    $discrp_single  = str_replace(',', '', $discrp[$y]);
                    $jumlah_single  = str_replace(',', '', $jumlah[$y]);

                    $data_tarif = [
                        'token_pembayaran'  => $token_pembayaran,
                        'kode_multiprice'   => $kode_single,
                        'kode_tarif'        => $this->M_global->getData('multiprice_tindakan', ['kode_multiprice' => $kode_single])->kode_tindakan,
                        'harga'             => $harga_single,
                        'discpr'            => $discpr_single,
                        'discrp'            => $discrp_single,
                        'jumlah'            => $jumlah_single,
                    ];

                    $this->M_global->insertData('pembayaran_tarif_single', $data_tarif);
                }
            }

            aktifitas_user('Pembayaran', 'membayar Kasir', $invoice, 'Pembayaran dengan invoice: ' . $invoice . ', no trx: ' . $no_trx . ', kode member: ' . $kode_member . ', total: Rp. ' . number_format($total) . ', jenis pembayaran: ' . $jenis_pembayaran, json_encode($isi_pembayaran), '');

            // update barang_out_header dan member
            $this->M_global->updateData(
                'barang_out_header',
                ['status_jual' => 1],
                ['invoice' => $inv_jual]
            );

            $this->M_global->updateData(
                'member',
                ['status_regist' => 0],
                ['kode_member' => $kode_member]
            );

            $this->print_kwitansi($token_pembayaran, 1);

            // kirim status 1 ke view
            echo json_encode(['status' => 1, 'token_pembayaran' => $token_pembayaran]);
        } else { // salain itu
            // kirim status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    public function getDataJual($invoice)
    {
        $penjualan = $this->M_global->getData('barang_out_header', ['invoice' => $invoice]);
        $this->getMember($penjualan->kode_member);
    }

    // report_um page
    public function report_um()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Pembayaran',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Pembayaran',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Kasir/uangmuka_list/',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Kasir/Uangmuka', $parameter);
    }

    // fugsi bantu untuk sinkron uang_muka
    function update_pembayaran()
    {
        $pembayaran = $this->M_global->getResult('pendaftaran');
        foreach ($pembayaran as $p) {
            $kode_member = $p->kode_member;
            $no_trx = $p->no_trx;

            // Update tabel pembayaran
            $this->M_global->updateData('pembayaran', [
                'kode_member' => $kode_member,
            ], ['no_trx' => $no_trx]);
        }
    }

    // sinkron um
    public function sinkron_um()
    {
        $this->update_pembayaran();
        // Fetch all members
        $members = $this->M_global->getResult('member');

        foreach ($members as $member) {
            $um_masuk = 0;
            $um_keluar = 0;

            // Ambil total um_masuk dari pembayaran (um_masuk) dan pembayaran_uangmuka (total)
            $pembayaran = $this->M_global->getDataResult('pembayaran', ['kode_member' => $member->kode_member]);
            foreach ($pembayaran as $p) {
                $um_masuk += (int)$p->um_masuk;
                $um_keluar += (int)$p->um_keluar;
            }

            $deposit = $this->M_global->getDataResult('pembayaran_uangmuka', ['kode_member' => $member->kode_member]);
            foreach ($deposit as $d) {
                $um_masuk += (int)$d->total;
            }

            // Update tabel uang_muka
            $this->M_global->updateData('uang_muka', [
                'uang_masuk' => $um_masuk,
                'uang_keluar' => $um_keluar,
                'uang_sisa' => ($um_masuk - $um_keluar)
            ], ['kode_member' => $member->kode_member]);
        }

        echo json_encode(['status' => 1]);
    }

    // fungsi list uang muka
    public function uangmuka_list($param1 = '')
    {
        // parameter untuk list table
        $table            = 'uang_muka';
        $colum            = ['id', 'last_tgl', 'last_jam', 'last_invoice', 'kode_member', 'uang_masuk', 'uang_keluar', 'uang_sisa'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param1   = '';

        // table server side tampung kedalam variable $list
        $list             = $this->M_datatables->get_datatables($table, $colum, $order_arr, $order, $order2, $param1, $kondisi_param1);
        $data             = [];
        $no               = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->kode_member . ' ~ ' . $this->M_global->getData('member', ['kode_member' => $rd->kode_member])->nama;
            $row[]  = date('d/m/Y', strtotime($rd->last_tgl)) . ' ~ ' . date('H:i:s', strtotime($rd->last_jam));
            $row[]  = $rd->last_invoice;
            $row[]  = 'Rp. <span class="float-right">' . number_format($rd->uang_masuk) . '</sp>';
            $row[]  = 'Rp. <span class="float-right">' . number_format($rd->uang_keluar) . '</sp>';
            $row[]  = 'Rp. <span class="float-right">' . number_format($rd->uang_sisa) . '</sp>';
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

    // deposit_um page
    public function deposit_um()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Pembayaran',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Pembayaran',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Kasir/uangmukadepo_list/',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Kasir/UangmukaDepo', $parameter);
    }

    // fungsi list uangmukadepo_list
    public function uangmukadepo_list($param1 = 1, $param2 = '')
    {
        // parameter untuk list table
        $table            = 'pembayaran_uangmuka';
        $colum            = ['id', 'invoice', 'tgl_pembayaran', 'jam_pembayaran', 'kode_member', 'jenis_pembayaran', 'cash', 'card', 'total', 'shift', 'kode_user'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param2   = '';
        $kondisi_param1   = 'tgl_pembayaran';

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
                $upd_diss = '';
            } else {
                $upd_diss = 'disabled';
            }

            if ($deleted > 0) {
                $del_diss = '';
            } else {
                $del_diss = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->invoice;
            $row[]  = date('d/m/Y', strtotime($rd->tgl_pembayaran)) . ' ~ ' . date('H:i:s', strtotime($rd->jam_pembayaran));
            $row[]  = $rd->kode_member . ' ~ ' . $this->M_global->getData('member', ['kode_member' => $rd->kode_member])->nama;
            $row[]  = ($rd->jenis_pembayaran == 0 ? 'CASH' : (($rd->jenis_pembayaran == 1) ? 'CARD' : 'CASH & CARD'));
            $row[]  = 'Rp. <span class="float-right">' . number_format($rd->total) . '</span>';
            $row[]  = $this->M_global->getData('user', ['kode_user' => $rd->kode_user])->nama . '<br><span class="badge badge-danger">Shift: ' . $rd->shift . '</span>';
            $row[]  = '<div class="text-center">
            <button type="button" style="margin-bottom: 5px;" class="btn btn-secondary" onclick="cetak(' . "'" . $rd->invoice . "', 0" . ')"><i class="fa-solid fa-file-pdf"></i></button>
                <button type="button" style="margin-bottom: 5px;" class="btn btn-warning" title="Ubah" onclick="ubah(' . "'" . $rd->invoice . "'" . ')" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" style="margin-bottom: 5px;" class="btn btn-danger" title="Hapus" onclick="hapus(' . "'" . $rd->invoice . "'" . ')" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    // form uangmuka page
    public function form_uangmuka($param)
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param == '0') {
            $pembayaran     = null;
            $bayar_detail   = null;
        } else {
            $bayar_detail   = $this->M_global->getDataResult('bayar_um_card_detail', ['invoice' => $param]);
            $pembayaran     = $this->M_global->getData('pembayaran_uangmuka', ['invoice' => $param]);
        }

        $parameter = [
            $this->data,
            'judul'             => 'Pembayaran',
            'nama_apps'         => $web_setting->nama,
            'page'              => 'Pembayaran Uang Muka',
            'web'               => $web_setting,
            'web_version'       => $web_version->version,
            'list_data'         => '',
            'data_pembayaran'   => $pembayaran,
            'bayar_detail'      => $bayar_detail,
        ];

        $this->template->load('Template/Content', 'Kasir/Form_pembayaran_um', $parameter);
    }

    // fungsi proses insert/update
    public function um_proses($param)
    {
        if ($param == 1) { // jika param 1
            // buat invoice
            $invoice            = _invoiceDepoUM();
        } else { // selain itu
            // ambil invoice dari inputan
            $invoice            = $this->input->post('invoice');
        }

        $kode_cabang            = $this->session->userdata('cabang');

        // variable
        $jenis_pembayaran       = $this->input->post('jenis_pembayaran');
        $tgl_pembayaran         = $this->input->post('tgl_pembayaran');
        $jam_pembayaran         = $this->input->post('jam_pembayaran');
        $kode_member            = $this->input->post('kode_member');

        // variable card
        $cash                   = str_replace(',', '', $this->input->post('cash'));
        $card                   = str_replace(',', '', $this->input->post('card'));
        $total                  = str_replace(',', '', $this->input->post('total'));
        $kode_user              = $this->session->userdata('kode_user');
        $shift                  = $this->session->userdata('shift');

        // isi pembayaran
        $isi_pembayaran = [
            'invoice'           => $invoice,
            'kode_cabang'       => $kode_cabang,
            'kode_member'       => $kode_member,
            'tgl_pembayaran'    => $tgl_pembayaran,
            'jam_pembayaran'    => $jam_pembayaran,
            'total'             => $total,
            'kode_user'         => $kode_user,
            'shift'             => $shift,
            'jenis_pembayaran'  => $jenis_pembayaran,
            'cash'              => $cash,
            'card'              => $card,
        ];


        if ($param == 1) { // jika param = 1
            // insert ke pembayaran_uangmuka
            $cek = $this->M_global->insertData('pembayaran_uangmuka', $isi_pembayaran);

            $cek_param = 'menambahkan';

            updateUangMukaIn($kode_member, $invoice, $tgl_pembayaran, $jam_pembayaran, $total);
        } else { // selain itu

            $um_awal = $this->M_global->getData('pembayaran_uangmuka', ['invoice' => $invoice]);
            $total_awal = $um_awal->total;

            updateUangMukaUpdate($kode_member, $invoice, $tgl_pembayaran, $jam_pembayaran, $total, $total_awal);

            $cek_param = 'mengubah';

            // update piutang
            $piutang = $this->M_global->getData('piutang', ['referensi' => $invoice, 'kode_cabang' => $kode_cabang, 'jenis' => 0]);
            $piutang_no = $piutang->piutang_no;

            // update pembayaran_uangmuka dan hapus cardnya
            $cek = [
                $this->M_global->updateData('pembayaran_uangmuka', $isi_pembayaran, ['invoice' => $invoice]),
                $this->M_global->delData('bayar_um_card_detail', ['invoice' => $invoice]),
                $this->M_global->delData('piutang', ['piutang_no' => $piutang_no, 'jenis' => 0]),
            ];
        }

        aktifitas_user('Pembayaran', $cek_param . ' Uang Muka', $invoice, $kode_user, $isi_pembayaran);

        // insert piutang ketika di acc
        $piutang_no = _noPiutang($kode_cabang);

        $isi_piutang = [
            'kode_cabang'       => $kode_cabang,
            'piutang_no'        => $piutang_no,
            'tanggal'           => $tgl_pembayaran,
            'jam'               => $jam_pembayaran,
            'referensi'         => $invoice,
            'jumlah'            => 0 - $total,
            'status'            => 0,
            'jenis'             => 0,
        ];

        $this->M_global->insertData('piutang', $isi_piutang);

        if ($cek) { // jika fungsi cek berjalan
            // variable detail card
            $kode_bank    = $this->input->post('kode_bank');
            $tipe_bank    = $this->input->post('tipe_bank');
            $no_card      = $this->input->post('no_card');
            $approval     = $this->input->post('approval');
            $jumlah_card  = $this->input->post('jumlah_card');

            if (!empty($kode_bank)) { // jika kodebank exist/ ada
                // ambil jumlah row berdasarkan kode_bank
                $jum = count($kode_bank);

                // lakukan loop dengan for
                for ($x = 0; $x <= ($jum - 1); $x++) {
                    $_kode_bank   = $kode_bank[$x];
                    $_tipe_bank   = $tipe_bank[$x];
                    $_no_card     = $no_card[$x];
                    $_approval    = $approval[$x];
                    $_jumlah_card = str_replace(',', '', $jumlah_card[$x]);

                    // isi detail card
                    $isi_card = [
                        'invoice'           => $invoice,
                        'kode_bank'         => $_kode_bank,
                        'kode_tipe'         => $_tipe_bank,
                        'no_card'           => $_no_card,
                        'approval'          => $_approval,
                        'jumlah'            => $_jumlah_card,
                    ];

                    // insert ke bayar_card_detail
                    $this->M_global->insertData('bayar_um_card_detail', $isi_card);
                }
            }

            // kirim status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // salain itu
            // kirim status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi hapus pembayaran UM
    public function delPembayaran_um($invoice)
    {
        $pembayaran = $this->M_global->getData('pembayaran_uangmuka', ['invoice' => $invoice]);

        updateUangMukaDelete($pembayaran->kode_member, $invoice, $pembayaran->tgl_pembayaran, $pembayaran->jam_pembayaran, $pembayaran->total);

        $cek = [
            $this->M_global->delData('pembayaran_uangmuka', ['invoice' => $invoice]),
            $this->M_global->delData('bayar_um_card_detail', ['invoice' => $invoice])
        ];

        if ($cek) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi cetak uangmuka
    public function print_uangmuka($invoice, $yes)
    {
        $kode_cabang          = $this->session->userdata('cabang');
        $web_setting          = $this->M_global->getData('web_setting', ['id' => 1]);

        $position             = 'P'; // cek posisi l/p

        // body cetakan
        $body                 = '';
        $body                 .= '<br><br>'; // beri jarak antara kop dengan body

        $pembayaran_uangmuka  = $this->M_global->getData('pembayaran_uangmuka', ['invoice' => $invoice]);
        $um_awal              = $this->db->query("SELECT * FROM pembayaran_uangmuka WHERE kode_member = '$pembayaran_uangmuka->kode_member' AND invoice <> '$invoice' ORDER BY id DESC LIMIT 1")->row();
        $um_masuk             = $pembayaran_uangmuka->total;
        $um_total             = $um_awal->total + $um_masuk;


        $bayar_um_card_detail = $this->M_global->getData('bayar_um_card_detail', ['invoice' => $invoice]);
        $member               = $this->M_global->getData('member', ['kode_member' => $pembayaran_uangmuka->kode_member]);
        $bayar_um_card_detail = $this->M_global->getDataResult('bayar_um_card_detail', ['invoice' => $invoice]);

        $judul                = 'Deposit Uang Muka ' . $pembayaran_uangmuka->invoice;

        $body .= '<table style="width: 100%; font-size: 9px;" cellpadding="2px">';

        $body .= '<tr>
            <td style="text-align: center;">' . date('d/m/Y') . ' ~ ' . date('H:i:s') . '</td>
        </tr>';

        $body .= '</table>';

        $body .= '<table style="width: 100%; font-size: 9px;" cellpadding="2px">';

        $body .= '<tr>
            <td style="width: 23%;">Invoice</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . $pembayaran_uangmuka->invoice . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">Kasir</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . $this->M_global->getData('user', ['kode_user' => $pembayaran_uangmuka->kode_user])->nama . '</td>
        </tr>
        <tr>
            <td style="width: 23%;">Member</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . $member->nama . ' (' . $member->jkel . ', ' . hitung_umur($member->tgl_lahir) . ')</td>
        </tr>
        <tr>
            <td style="width: 23%;">Alamat</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . $this->M_global->getData('m_provinsi', ['kode_provinsi' => $member->provinsi])->provinsi . ', ' . $this->M_global->getData('kabupaten', ['kode_kabupaten' => $member->kabupaten])->kabupaten . ', ' . $this->M_global->getData('kecamatan', ['kode_kecamatan' => $member->kecamatan])->kecamatan . '</td>
        </tr>
        <tr>
            <td style="width: 23%;"></td>
            <td style="width: 2%;"></td>
            <td style="width: 75%;">' . $member->desa . ' (' . $member->kodepos . '), RT/RW (' . $member->rt . '/' . $member->rw . ')</td>
        </tr>
        <tr>
            <td style="width: 23%;">Pembayaran</td>
            <td style="width: 2%;">:</td>
            <td style="width: 75%;">' . (($pembayaran_uangmuka->jenis_pembayaran == 0) ? 'Cash' : (($pembayaran_uangmuka->jenis_pembayaran == 1) ? 'Card' : 'Cash & Card')) . '</td>
        </tr>';

        $body .= '<tr>
            <td style="width: 100%;" colspan="3">&nbsp;</td>
        </tr>';

        $body .= '</table>';

        $body .= '<table style="width: 100%; font-size: 9px;" cellpadding="2px">';

        $body .= '<tbody>
            <tr>
                <td style="width: 60%; font-weight: bold;" colspan="2">Uang Muka</td>
                <td style="width: 40%; text-align: right; font-weight: bold;">Rp. ' . number_format($um_total) . '</td>
            </tr>
            <tr>
                <td style="width: 100%;" colspan="3"><hr style="margin: 0px;"></td>
            </tr>
            <tr>
                <td style="width: 20%;">Rp.' . number_format($um_awal->total) . '</td>
                <td style="text-align: right; width: 40%;">' . ' @ Rp. ' . number_format($um_masuk) . '</td>
                <td style="text-align: right; width: 40%;">Rp.' . number_format(($um_total)) . '</td>
            </tr>
            <tr>
                <td style="width: 100%;" colspan="3"><hr style="margin: 0px;"></td>
            </tr>
            <tr>
                <td style="width: 60%; text-align: right; font-weight: bold;" colspan="2">Awal: Rp.</td>
                <td style="width: 40%; text-align: right; text-align: right;">' . number_format($um_awal->total) . '</td>
            </tr>
            <tr>
                <td style="width: 60%; text-align: right; font-weight: bold;" colspan="2">Masuk: Rp.</td>
                <td style="width: 40%; text-align: right; text-align: right;">' . number_format($um_masuk) . '</td>
            </tr>
            <tr>
                <td style="width: 60%; text-align: right; font-weight: bold;" colspan="2">Total: Rp.</td>
                <td style="width: 40%; text-align: right; text-align: right;">' . number_format($um_total) . '</td>
            </tr>
            <tr>
                <td style="width: 100%;" colspan="3"></td>
            </tr>
            <tr>
                <td style="width: 60%; font-weight: bold;" colspan="2">Detail Pembayaran</td>
                <td style="width: 40%; text-align: right; font-weight: bold;"></td>
            </tr>
            <tr>
                <td style="width: 100%;" colspan="3"><hr style="margin: 0px;"></td>
            </tr>
            <tr>
                <td style="width: 60%;" colspan="2">Cash: Rp.</td>
                <td style="width: 40%; text-align: right; font-weight: bold;">' . number_format($pembayaran_uangmuka->cash) . '</td>
            </tr>
            <tr>
                <td style="width: 20%; padding-top: 0px;">Card: Rp.</td>
                <td style="width: 80%; text-align: right; padding-right: 0px;" colspan="2">
                    <table style="padding: 0px;">';

        foreach ($bayar_um_card_detail as $card) {
            $body .= '<tr>
                <td style="width: 50%;">' . $this->M_global->getData('m_bank', ['kode_bank' => $card->kode_bank])->keterangan . '</td>
                <td style="width: 50%; text-align: right; padding: 0px;">' . number_format($card->jumlah) . '</td>
            </tr>';
        }

        $body .= '<tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%;">
                    <hr style="margin: 0px;">
                </td>
            </tr>
            <tr>
                <td style="width: 100%; text-align: right; font-weight: bold;" colspan="2">' . number_format($pembayaran_uangmuka->card) . '</td>
            </tr>';

        $body .= '</table>
                </td>
            </tr>
        <tbody>';

        $body .= '</table>';

        cetak_pdf_small($judul, $body, 1, $position, $judul, $web_setting, $yes);
    }
}
