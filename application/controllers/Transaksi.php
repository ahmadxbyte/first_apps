<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Transaksi extends CI_Controller
{
    // variable open public untuk controller Home
    public $data;

    public function __construct()
    {
        parent::__construct();
        // load model M_auth
        $this->load->model("M_auth");
        $this->load->model("M_order_emr");

        if (!empty($this->session->userdata("email"))) { // jika session email masih ada

            $id_menu = $this->M_global->getData('m_menu', ['url' => 'Transaksi'])->id;

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
                    'menu'      => 'Transaksi',
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

    // barang_po_in page
    public function barang_po_in()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Transaksi',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Pengajuan Pembelian',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Transaksi/barang_po_in_list/',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Barang/Pengajuan', $parameter);
    }

    // fungsi list barang_po_in
    public function barang_po_in_list($param1 = 1, $param2 = '')
    {
        // parameter untuk list table
        $table            = 'barang_po_in_header';
        $colum            = ['id', 'invoice', 'tgl_po', 'jam_po', 'kode_supplier', 'kode_gudang', 'pajak', 'diskon', 'total', 'kode_user', 'batal', 'tgl_batal', 'jam_batal', 'user_batal', 'is_valid', 'shift'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param2   = 'kode_gudang';
        $kondisi_param1   = 'tgl_po';

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
                if ($rd->batal > 0) {
                    $upd_diss = 'disabled';
                } else {
                    if ($rd->is_valid > 0) {
                        $upd_diss = 'disabled';
                    } else {
                        $upd_diss =  _lock_button();
                    }
                }
            } else {
                $upd_diss = 'disabled';
            }

            if ($deleted > 0) {
                if ($rd->batal > 0) {
                    $del_diss = 'disabled';
                } else {
                    if ($rd->is_valid > 0) {
                        $del_diss = 'disabled';
                    } else {
                        $del_diss =  _lock_button();
                    }
                }
            } else {
                $del_diss = 'disabled';
            }

            if ($confirmed > 0) {
                $confirm_diss =  _lock_button();
            } else {
                $confirm_diss = 'disabled';
            }

            $cek_bin = $this->M_global->getData('barang_in_header', ['invoice_po' => $rd->invoice]);

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->invoice . '<br>' . (($rd->batal == 0) ? (($rd->is_valid > 0) ? '<span class="badge badge-primary">ACC</span>' : '<span class="badge badge-success">Buka</span>') : '<span class="badge badge-danger">Batal</span>') . (($cek_bin) ? (($cek_bin->is_valid == 1) ? ' <span class="badge badge-info">Sudah diproses</span>' : ' <span class="badge badge-warning">Belum diapprove</span>') : ' <span class="badge badge-danger">Belum diproses</span>');
            $row[]  = date('d/m/Y', strtotime($rd->tgl_po)) . ' ~ ' . date('H:i:s', strtotime($rd->jam_po));
            $row[]  = $this->M_global->getData('m_supplier', ['kode_supplier' => $rd->kode_supplier])->nama;
            $row[]  = $this->M_global->getData('m_gudang', ['kode_gudang' => $rd->kode_gudang])->nama;
            $row[]  = 'Rp. <span class="float-right">' . number_format($rd->total) . '</span>';
            $row[]  = $this->M_global->getData('user', ['kode_user' => $rd->kode_user])->nama . '<br><span class="badge badge-danger">Shift: ' . $rd->shift . '</span>';

            if ($rd->is_valid < 1) {
                if ($rd->batal < 1) {
                    $batal = '<button type="button" style="margin-bottom: 5px;" class="btn btn-secondary" title="Batalkan" onclick="actived(' . "'" . $rd->invoice . "', 1" . ')" ' . $confirm_diss . '><i class="fa-solid fa-ban"></i></button>';

                    $ubah = '<button type="button" style="margin-bottom: 5px;" class="btn btn-warning" title="Ubah" onclick="ubah(' . "'" . $rd->invoice . "'" . ')" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>';

                    $accept = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="ACC" onclick="valided(' . "'" . $rd->invoice . "', 1" . ')" ' . $confirm_diss . '><i class="fa-regular fa-circle-check"></i></button>';
                } else {
                    $batal = '<button type="button" style="margin-bottom: 5px;" class="btn btn-light" title="Re-Batalkan" onclick="actived(' . "'" . $rd->invoice . "', 0" . ')" ' . $confirm_diss . '><i class="fa-solid fa-arrow-rotate-left"></i></button>';

                    $ubah = '<button type="button" style="margin-bottom: 5px;" class="btn btn-warning" title="Ubah" disabled><i class="fa-regular fa-pen-to-square"></i></button>';

                    $accept = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="ACC" disabled><i class="fa-regular fa-circle-check"></i></button>';
                }
            } else {
                if ($cek_bin) {
                    $accept = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="Re-ACC" disabled><i class="fa-solid fa-check-to-slot"></i></button>';
                } else {
                    $accept = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="Re-ACC" onclick="valided(' . "'" . $rd->invoice . "', 0" . ')" ' . $confirm_diss . '><i class="fa-solid fa-check-to-slot"></i></button>';
                }

                $ubah = '<button type="button" style="margin-bottom: 5px;" class="btn btn-warning" title="Ubah" disabled><i class="fa-regular fa-pen-to-square"></i></button>';

                $batal = '<button type="button" style="margin-bottom: 5px;" class="btn btn-secondary" title="Batalkan" disabled><i class="fa-solid fa-ban"></i></button>';
            }

            $row[]  = '<div class="text-center">
                ' . $accept . '
                ' . $ubah . '
                <button type="button" style="margin-bottom: 5px;" class="btn btn-danger" title="Hapus" onclick="hapus(' . "'" . $rd->invoice . "'" . ')" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
                <br>
                ' . $batal . '
                <button type="button" style="margin-bottom: 5px;" class="btn btn-dark" title="Cetak" onclick="cetak(' . "'" . $rd->invoice . "', 0" . ')"><i class="fa-solid fa-print"></i></button>
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

    // form barang_po_in page
    public function form_barang_po_in($param)
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param != '0') {
            $barang_po_in     = $this->M_global->getData('barang_po_in_header', ['invoice' => $param]);
            $barang_po_detail = $this->M_global->getDataResult('barang_po_in_detail', ['invoice' => $param]);
        } else {
            $barang_po_in     = null;
            $barang_po_detail = null;
        }

        $parameter = [
            $this->data,
            'judul'             => 'Transaksi',
            'nama_apps'         => $web_setting->nama,
            'page'              => 'Pengajuan Pembelian',
            'web'               => $web_setting,
            'web_version'       => $web_version->version,
            'list_data'         => '',
            'data_barang_po_in' => $barang_po_in,
            'barang_po_detail'  => $barang_po_detail,
            'role'              => $this->M_global->getResult('m_role'),
            'pajak'             => $this->M_global->getData('m_pajak', ['aktif' => 1])->persentase,
            'list_barang'       => $this->M_global->getResult('barang'),
        ];

        $this->template->load('Template/Content', 'Barang/Form_barang_po_in', $parameter);
    }

    // fungsi insert/update proses barang_po_in
    public function barang_po_in_proses($param)
    {
        $kode_cabang      = $this->session->userdata('cabang');
        $shift            = $this->session->userdata('shift');

        // header
        if ($param == 1) { // jika param = 1
            $invoice = _invoicePO($kode_cabang);
        } else {
            $invoice = $this->input->post('invoice');
        }

        $tgl_po           = $this->input->post('tgl_po');
        $jam_po           = $this->input->post('jam_po');
        $kode_supplier    = $this->input->post('kode_supplier');
        $kode_gudang      = $this->input->post('kode_gudang');

        $subtotal         = str_replace(',', '', $this->input->post('subtotal'));
        $diskon           = str_replace(',', '', $this->input->post('diskon'));
        $pajak            = str_replace(',', '', $this->input->post('pajak'));
        $total            = str_replace(',', '', $this->input->post('total'));

        // detail
        $kode_barang_po_in  = $this->input->post('kode_barang_po_in');
        $kode_satuan_in     = $this->input->post('kode_satuan');
        $harga_in           = $this->input->post('harga_in');
        $qty_in             = $this->input->post('qty_in');
        $discpr_in          = $this->input->post('discpr_in');
        $discrp_in          = $this->input->post('discrp_in');
        $pajakrp_in         = $this->input->post('pajakrp_in');
        $jumlah_in          = $this->input->post('jumlah_in');

        // cek jumlah detail barang_in
        $jum                = count($kode_barang_po_in);

        // tampung isi header
        $isi_header = [
            'kode_cabang'   => $kode_cabang,
            'invoice'       => $invoice,
            'tgl_po'        => $tgl_po,
            'jam_po'        => $jam_po,
            'kode_supplier' => $kode_supplier,
            'kode_gudang'   => $kode_gudang,
            'pajak'         => $pajak,
            'diskon'        => $diskon,
            'subtotal'      => $subtotal,
            'total'         => $total,
            'kode_user'     => $this->session->userdata('kode_user'),
            'shift'         => $shift,
            'batal'         => 0,
            'is_valid'      => 0,
        ];

        if ($param == 2) { // jika param = 2
            aktifitas_user_transaksi('Transaksi Masuk', 'mengubah PO', $invoice);

            // jalankan fungsi cek
            $cek = [
                $this->M_global->updateData('barang_po_in_header', $isi_header, ['invoice' => $invoice]), // update header
                $this->M_global->delData('barang_po_in_detail', ['invoice' => $invoice]), // delete detail
            ];
        } else { // selain itu
            aktifitas_user_transaksi('Transaksi Masuk', 'menambahkan PO', $invoice);

            // jalankan fungsi cek
            $cek = $this->M_global->insertData('barang_po_in_header', $isi_header); // insert header
        }

        if ($cek) { // jika fungsi cek berjalan
            // lakukan loop
            for ($x = 0; $x <= ($jum - 1); $x++) {
                $kode_barang    = $kode_barang_po_in[$x];
                $kode_satuan    = $kode_satuan_in[$x];
                $harga          = str_replace(',', '', $harga_in[$x]);
                $qty            = str_replace(',', '', $qty_in[$x]);
                $discpr         = str_replace(',', '', $discpr_in[$x]);
                $discrp         = str_replace(',', '', $discrp_in[$x]);
                $pajakrp        = str_replace(',', '', $pajakrp_in[$x]);
                $jumlah         = str_replace(',', '', $jumlah_in[$x]);

                $barang1 = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan' => $kode_satuan]);
                $barang2 = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan2' => $kode_satuan]);
                $barang3 = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan3' => $kode_satuan]);

                if ($barang1) {
                    $qty_satuan = 1;
                } else if ($barang2) {
                    $qty_satuan = $barang2->qty_satuan2;
                } else {
                    $qty_satuan = $barang3->qty_satuan3;
                }

                $qty_konversi   = $qty * $qty_satuan;

                // tamping isi detail
                $isi_detail = [
                    'invoice'       => $invoice,
                    'kode_barang'   => $kode_barang,
                    'kode_satuan'   => $kode_satuan,
                    'harga'         => $harga,
                    'qty_konversi'  => $qty_konversi,
                    'qty'           => $qty,
                    'qty_terima'    => '0.00',
                    'discpr'        => $discpr,
                    'discrp'        => $discrp,
                    'pajak'         => (($pajakrp > 0) ? 1 : 0),
                    'pajakrp'       => $pajakrp,
                    'jumlah'        => $jumlah,
                ];

                // insert detail
                $this->M_global->insertData('barang_po_in_detail', $isi_detail);
            }

            $this->single_print_bin_po($invoice, 1);

            // beri nilai status = 1 kirim ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // beri nilai status = 0 kirim ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi print single barang_in
    public function single_print_bin_po($invoice, $yes)
    {
        $param          = 1;

        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $pencetak       = $this->M_global->getData('user', ['kode_user' => $this->session->userdata('kode_user')])->nama;

        $breaktable     = '<br>';
        $file = "Pengajuan Pembelian";

        // isi body
        $header = $this->M_global->getData('barang_po_in_header', ['invoice' => $invoice]);

        // body header
        $body .= '<table style="width: 100%; font-size: 11px;">
            <tr>
                <td style="width: 15%;">Perihal</td>
                <td style="width: 2%;"> : </td>
                <td style="width: 33%;">' . $file . '</td>
                <td style="width: 50%; text-align: right; font-weight: bold; color: white;"><span style="border: 1px solid #0e1d2e; background-color: #0e1d2e;">' . $invoice . '</span></td>
            </tr>
            <tr>
                <td style="width: 15%;">Tgl/Jam PO</td>
                <td style="width: 2%;"> : </td>
                <td colspan="2">' . date('d-m-Y', strtotime($header->tgl_po)) . ' / ' . date('H:i:s', strtotime($header->jam_po)) . '</td>
            </tr>
            <tr>
                <td style="width: 15%;">Pemasok</td>
                <td style="width: 2%;"> : </td>
                <td colspan="2">' . $this->M_global->getData('m_supplier', ['kode_supplier' => $header->kode_supplier])->nama . '</td>
            </tr>
            <tr>
                <td style="width: 15%;">Gudang</td>
                <td style="width: 2%;"> : </td>
                <td colspan="2">' . $this->M_global->getData('m_gudang', ['kode_gudang' => $header->kode_gudang])->nama . '</td>
            </tr>
            <tr>
                <td style="width: 15%;">User Input</td>
                <td style="width: 2%;"> : </td>
                <td style="width: 33%;">' . $this->M_global->getData('user', ['kode_user' => $header->kode_user])->nama . '</td>
                <td style="width: 50%; text-align: right;">Pencetak : ' . $pencetak . '</td>
            </tr>
        </table>';

        $body .= $breaktable;

        $body .= '<table style="width: 100%; font-size: 10px;" autosize="1" cellpadding="5px">';

        $body .= '<thead>
            <tr>
                <th rowspan="2" style="width: 5%; border: 1px solid black; background-color: #0e1d2e; color: white;">#</th>
                <th rowspan="2" style="width: 20%; border: 1px solid black; background-color: #0e1d2e; color: white;">Barang</th>
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

        if ($param == 1) {
            $total = number_format($header->total);
        } else {
            $total = ceil($header->total);
        }
        $body .= '<tr style="background-color: skyblue;">
            <td colspan="7" style="border: 1px solid black; font-weight: bold;">No. Transaksi: ' . $header->invoice . '</td>
            <td colspan="2" style="border: 1px solid black; font-weight: bold; text-align: right">' . $total . '</td>
        </tr>';

        // detail barang
        $detail   = $this->M_global->getDataResult('barang_po_in_detail', ['invoice' => $header->invoice]);

        $no       = 1;
        $tdiskon  = 0;
        $tpajak   = 0;
        $ttotal   = 0;
        foreach ($detail as $d) {
            $tdiskon    += $d->discrp;
            $tpajak     += $d->pajakrp;
            $ttotal     += $d->jumlah;

            if ($param == 1) {
                $harga    = number_format($d->harga);
                $qty      = number_format($d->qty);
                $discpr   = number_format($d->discpr);
                $discrp   = number_format($d->discrp);
                $pajak    = number_format($d->pajakrp);
                $jumlah   = number_format($d->jumlah);

                $tdiskonx = number_format($tdiskon);
                $tpajakx  = number_format($tpajak);
                $ttotalx  = number_format($ttotal);
            } else {
                $harga    = ceil($d->harga);
                $qty      = ceil($d->qty);
                $discpr   = ceil($d->discpr);
                $discrp   = ceil($d->discrp);
                $pajak    = ceil($d->pajakrp);
                $jumlah   = ceil($d->jumlah);

                $tdiskonx = ceil($tdiskon);
                $tpajakx  = ceil($tpajak);
                $ttotalx  = ceil($ttotal);
            }

            $body .= '<tr>
                <td style="border: 1px solid black;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $d->kode_barang . ' ~ ' . $this->M_global->getData('barang', ['kode_barang' => $d->kode_barang])->nama . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $this->M_global->getData('m_satuan', ['kode_satuan' => $d->kode_satuan])->keterangan . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $harga . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $qty . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $discpr . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $discrp . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $pajak . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $jumlah . '</td>
            </tr>';
            $no++;
        }

        $body .= '<tr style="background-color: green;">
            <td colspan="6" style="border: 1px solid black; font-weight: bold; color: white;">Total</td>
            <td style="border: 1px solid black; font-weight: bold; color: white; text-align: right">' . $tdiskonx . '</td>
            <td style="border: 1px solid black; font-weight: bold; color: white; text-align: right">' . $tpajakx . '</td>
            <td style="border: 1px solid black; font-weight: bold; color: white; text-align: right">' . $ttotalx . '</td>
        </tr>';

        $body .= '</tbody>';

        $body .= '</table>';

        $judul = $invoice;
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting, $yes);
    }

    // fungsi hapus barang po in
    public function delBeliPoIn($invoice)
    {
        // jalankan fungsi cek
        $cek = [
            $this->M_global->delData('barang_po_in_detail', ['invoice' => $invoice]), // del data detail pembelian
            $this->M_global->delData('barang_po_in_header', ['invoice' => $invoice]), // del data header pembelian
        ];

        if ($cek) { // jika fungsi cek berjalan
            // kirim status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirim status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi acc/re-acc
    public function accbarang_po_in($invoice, $acc)
    {
        // header barang by invoice
        $header = $this->M_global->getData('barang_po_in_header', ['invoice' => $invoice]);
        // kode_gudang
        $kode_gudang = $header->kode_gudang;

        // detail barang
        $detail = $this->M_global->getDataResult('barang_po_in_detail', ['invoice' => $invoice]);

        if ($acc == 0) { // jika acc = 0
            aktifitas_user_transaksi('Transaksi Masuk', 'Reject PO', $invoice);

            // update is_valid jadi 0
            $cek = $this->M_global->updateData('barang_po_in_header', ['is_valid' => 0, 'tgl_valid' => null, 'jam_valid' => null], ['invoice' => $invoice]);
        } else { // selain itu
            aktifitas_user_transaksi('Transaksi Masuk', 'Confirm PO', $invoice);

            // update is_valid jadi 1
            $cek = $this->M_global->updateData('barang_po_in_header', ['is_valid' => 1, 'tgl_valid' => date('Y-m-d'), 'jam_valid' => date('H:i:s')], ['invoice' => $invoice]);
        }

        if ($cek) { // jika fungsi cek berjalan
            // kirim status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirim status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi batal/re-batal
    public function activedbarang_po_in($invoice, $batal)
    {
        $user_batal = $this->session->userdata('kode_user');

        if ($batal == 0) { // jika batal = 0
            // update batal jadi 0
            $cek = $this->M_global->updateData('barang_po_in_header', ['batal' => 0, 'tgl_batal' => null, 'jam_batal' => null, 'user_batal' => null], ['invoice' => $invoice]);
        } else { // selain itu
            // update batal jadi 1
            $cek = $this->M_global->updateData('barang_po_in_header', ['batal' => 1, 'tgl_batal' => date('Y-m-d'), 'jam_batal' => date('H:i:s'), 'user_batal' => $user_batal], ['invoice' => $invoice]);
        }

        if ($cek) { // jika fungsi cek berjalan
            // kirim status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirim status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi getPajak
    public function getPajak($supplier)
    {
        $pajak_supplier = $this->M_global->getData('m_supplier', ['kode_supplier' => $supplier]);

        $pajak = $this->M_global->getData('m_pajak', ['kode_pajak' => $pajak_supplier->pajak]);

        if ($pajak) {
            $pajak = $pajak->persentase;
        } else {
            $pajak = 0;
        }

        echo json_encode(['pajak_supplier' => $pajak]);
    }

    // barang_in page
    public function barang_in()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Transaksi',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Pembelian',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Transaksi/barang_in_list/',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Barang/Masuk', $parameter);
    }

    // fungsi list barang_in
    public function barang_in_list($param1 = 1, $param2 = '')
    {
        // parameter untuk list table
        $table            = 'barang_in_header';
        $colum            = ['id', 'invoice', 'tgl_beli', 'jam_beli', 'kode_supplier', 'kode_gudang', 'surat_jalan', 'no_faktur', 'pajak', 'diskon', 'total', 'kode_user', 'batal', 'tgl_batal', 'jam_batal', 'user_batal', 'is_valid', 'shift'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param2   = 'kode_gudang';
        $kondisi_param1   = 'tgl_beli';

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
            $returan = $this->M_global->getData('barang_in_retur_header', ['invoice_in' => $rd->invoice]);

            if ($updated > 0) {
                if ($rd->batal > 0) {
                    $upd_diss = 'disabled';
                } else {
                    if ($rd->is_valid > 0) {
                        $upd_diss = 'disabled';
                    } else {
                        if ($returan) {
                            $upd_diss = 'disabled';
                        } else {
                            $upd_diss =  _lock_button();
                        }
                    }
                }
            } else {
                $upd_diss = 'disabled';
            }

            if ($deleted > 0) {
                if ($rd->batal > 0) {
                    $del_diss = 'disabled';
                } else {
                    if ($rd->is_valid > 0) {
                        $del_diss = 'disabled';
                    } else {
                        if ($returan) {
                            $del_diss =  'disabled';
                        } else {
                            $del_diss =  _lock_button();
                        }
                    }
                }
            } else {
                $del_diss = 'disabled';
            }

            if ($confirmed > 0) {
                if ($returan) {
                    $confirm_diss =  'disabled';
                } else {
                    $confirm_diss =  _lock_button();
                }
            } else {
                $confirm_diss = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->invoice . '<br>' . (($rd->batal == 0) ? (($rd->is_valid > 0) ? '<span class="badge badge-primary">ACC</span>' : '<span class="badge badge-success">Buka</span>') : '<span class="badge badge-danger">Batal</span>');
            $row[]  = date('d/m/Y', strtotime($rd->tgl_beli)) . ' ~ ' . date('H:i:s', strtotime($rd->jam_beli));
            $row[]  = $this->M_global->getData('m_supplier', ['kode_supplier' => $rd->kode_supplier])->nama;
            $row[]  = $this->M_global->getData('m_gudang', ['kode_gudang' => $rd->kode_gudang])->nama;
            $row[]  = 'Rp. <span class="float-right">' . number_format($rd->total) . '</span>';
            $row[]  = $this->M_global->getData('user', ['kode_user' => $rd->kode_user])->nama . '<br><span class="badge badge-danger">Shift: ' . $rd->shift . '</span>';

            if ($rd->is_valid < 1) {
                if ($rd->batal < 1) {
                    $batal = '<button type="button" style="margin-bottom: 5px;" class="btn btn-secondary" title="Batalkan" onclick="actived(' . "'" . $rd->invoice . "', 1" . ')" ' . $confirm_diss . '><i class="fa-solid fa-ban"></i></button>';

                    $ubah = '<button type="button" style="margin-bottom: 5px;" class="btn btn-warning" title="Ubah" onclick="ubah(' . "'" . $rd->invoice . "'" . ')" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>';

                    $accept = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="ACC" onclick="valided(' . "'" . $rd->invoice . "', 1" . ')" ' . $confirm_diss . '><i class="fa-regular fa-circle-check"></i></button>';

                    $email = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="Kirim Email" disabled><i class="fa-solid fa-envelope-open-text"></i></button>';
                } else {
                    $batal = '<button type="button" style="margin-bottom: 5px;" class="btn btn-light" title="Re-Batalkan" onclick="actived(' . "'" . $rd->invoice . "', 0" . ')" ' . $confirm_diss . '><i class="fa-solid fa-arrow-rotate-left"></i></button>';

                    $ubah = '<button type="button" style="margin-bottom: 5px;" class="btn btn-warning" title="Ubah" disabled><i class="fa-regular fa-pen-to-square"></i></button>';

                    $accept = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="ACC" disabled><i class="fa-regular fa-circle-check"></i></button>';

                    $email = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="Kirim Email" disabled><i class="fa-solid fa-envelope-open-text"></i></button>';
                }
            } else {
                $accept = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="Re-ACC" onclick="valided(' . "'" . $rd->invoice . "', 0" . ')" ' . $confirm_diss . '><i class="fa-solid fa-check-to-slot"></i></button>';

                $ubah = '<button type="button" style="margin-bottom: 5px;" class="btn btn-warning" title="Ubah" disabled><i class="fa-regular fa-pen-to-square"></i></button>';

                $batal = '<button type="button" style="margin-bottom: 5px;" class="btn btn-secondary" title="Batalkan" disabled><i class="fa-solid fa-ban"></i></button>';

                $email = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="Kirim Email" onclick="email(' . "'" . $rd->invoice . "', 0" . ')"><i class="fa-solid fa-envelope-open-text"></i></button>';
            }

            $row[]  = '<div class="text-center">
                ' . $accept . '
                ' . $ubah . '
                <button type="button" style="margin-bottom: 5px;" class="btn btn-danger" title="Hapus" onclick="hapus(' . "'" . $rd->invoice . "'" . ')" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
                <br>
                ' . $batal . '
                <button type="button" style="margin-bottom: 5px;" class="btn btn-dark" title="Cetak" onclick="cetak(' . "'" . $rd->invoice . "', 0" . ')"><i class="fa-solid fa-print"></i></button>
                ' . $email . '
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

    // fungsi print single barang_in
    public function single_print_bin($invoice, $yes)
    {
        $param          = 1;

        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $pencetak       = $this->M_global->getData('user', ['kode_user' => $this->session->userdata('kode_user')])->nama;

        $breaktable     = '<br>';
        $file = 'Pembelian';

        // isi body
        $header = $this->M_global->getData('barang_in_header', ['invoice' => $invoice]);

        // body header
        $body .= '<table style="width: 100%; font-size: 11px;">
            <tr>
                <td style="width: 15%;">Perihal</td>
                <td style="width: 2%;"> : </td>
                <td style="width: 33%;">' . $file . '</td>
                <td style="width: 50%; text-align: right; font-weight: bold; color: white;"><span style="border: 1px solid #0e1d2e; background-color: #0e1d2e;">' . $invoice . '</span></td>
            </tr>
            <tr>
                <td style="width: 15%;">Tgl/Jam Beli</td>
                <td style="width: 2%;"> : </td>
                <td colspan="2">' . date('d-m-Y', strtotime($header->tgl_beli)) . ' / ' . date('H:i:s', strtotime($header->jam_beli)) . '</td>
            </tr>
            <tr>
                <td style="width: 15%;">Pemasok</td>
                <td style="width: 2%;"> : </td>
                <td colspan="2">' . $this->M_global->getData('m_supplier', ['kode_supplier' => $header->kode_supplier])->nama . '</td>
            </tr>
            <tr>
                <td style="width: 15%;">Gudang</td>
                <td style="width: 2%;"> : </td>
                <td style="width: 33%;">' . $this->M_global->getData('m_gudang', ['kode_gudang' => $header->kode_gudang])->nama . '</td>
                <td style="width: 50%; text-align: right;">Pencetak : ' . $pencetak . '</td>
            </tr>
        </table>';

        $body .= $breaktable;

        $body .= '<table style="width: 100%; font-size: 10px;" autosize="1" cellpadding="5px">';

        $body .= '<thead>
            <tr>
                <th rowspan="2" style="width: 5%; border: 1px solid black; background-color: #0e1d2e; color: white;">#</th>
                <th rowspan="2" style="width: 30%; border: 1px solid black; background-color: #0e1d2e; color: white;">Barang</th>
                <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Satuan</th>
                <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Harga</th>
                <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Jumlah</th>
                <th colspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Diskon</th>
                <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Pajak</th>
                <th rowspan="2" style="width: 15%; border: 1px solid black; background-color: #0e1d2e; color: white;">Total</th>
            </tr>
            <tr>
                <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">%</th>
                <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Rp</th>
            </tr>
        </thead>';

        $body .= '<tbody>';

        if ($param == 1) {
            $total = number_format($header->total);
        } else {
            $total = ceil($header->total);
        }
        $body .= '<tr style="background-color: skyblue;">
            <td colspan="7" style="border: 1px solid black; font-weight: bold;">No. Transaksi: ' . $header->invoice . '</td>
            <td colspan="2" style="border: 1px solid black; font-weight: bold; text-align: right">' . $total . '</td>
        </tr>';

        // detail barang
        $detail   = $this->M_global->getDataResult('barang_in_detail', ['invoice' => $header->invoice]);

        $no       = 1;
        $tdiskon  = 0;
        $tpajak   = 0;
        $ttotal   = 0;
        foreach ($detail as $d) {
            $tdiskon    += $d->discrp;
            $tpajak     += $d->pajakrp;
            $ttotal     += $d->jumlah;

            if ($param == 1) {
                $harga    = number_format($d->harga);
                $qty      = number_format($d->qty);
                $discpr   = number_format($d->discpr);
                $discrp   = number_format($d->discrp);
                $pajak    = number_format($d->pajakrp);
                $jumlah   = number_format($d->jumlah);

                $tdiskonx = number_format($tdiskon);
                $tpajakx  = number_format($tpajak);
                $ttotalx  = number_format($ttotal);
            } else {
                $harga    = ceil($d->harga);
                $qty      = ceil($d->qty);
                $discpr   = ceil($d->discpr);
                $discrp   = ceil($d->discrp);
                $pajak    = ceil($d->pajakrp);
                $jumlah   = ceil($d->jumlah);

                $tdiskonx = ceil($tdiskon);
                $tpajakx  = ceil($tpajak);
                $ttotalx  = ceil($ttotal);
            }
            $body .= '<tr>
                <td style="border: 1px solid black;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $d->kode_barang . ' ~ ' . $this->M_global->getData('barang', ['kode_barang' => $d->kode_barang])->nama . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $this->M_global->getData('m_satuan', ['kode_satuan' => $d->kode_satuan])->keterangan . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $harga . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $qty . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $discpr . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $discrp . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $pajak . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $jumlah . '</td>
            </tr>';
            $no++;
        }
        $body .= '<tr style="background-color: green;">
            <td colspan="6" style="border: 1px solid black; font-weight: bold; color: white;">Total</td>
            <td style="border: 1px solid black; font-weight: bold; color: white; text-align: right">' . $tdiskonx . '</td>
            <td style="border: 1px solid black; font-weight: bold; color: white; text-align: right">' . $tpajakx . '</td>
            <td style="border: 1px solid black; font-weight: bold; color: white; text-align: right">' . $ttotalx . '</td>
        </tr>';

        $body .= '</tbody>';

        $body .= '</table>';

        $judul = 'Pembelian ~ ' . $invoice;
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting, $yes);
    }

    // fungsi kirim email barang in
    public function email($invoice)
    {
        $email = $this->input->get('email');

        $header = $this->M_global->getData('barang_in_header', ['invoice' => $invoice]);

        $judul = 'Pembelian ~ ' . $invoice;

        // $attched_file    = base_url() . 'assets/file/pdf/' . $judul . '.pdf';ahmad.ummgl@gmail.com
        $attched_file    = $_SERVER["DOCUMENT_ROOT"] . '/first_apps/assets/file/pdf/' . $judul . '.pdf';

        $ready_message   = "";
        $ready_message   .= "<table border=0>
            <tr>
                <td style='width: 30%;'>Invoice</td>
                <td style='width: 10%;'> : </td>
                <td style='width: 60%;'> $invoice </td>
            </tr>
            <tr>
                <td style='width: 30%;'>Tgl/Jam</td>
                <td style='width: 10%;'> : </td>
                <td style='width: 60%;'>" . date('d-m-Y', strtotime($header->tgl_beli)) . " / " . date('H:i:s', strtotime($header->jam_beli)) . "</td>
            </tr>
            <tr>
                <td style='width: 30%;'>Pemasok</td>
                <td style='width: 10%;'> : </td>
                <td style='width: 60%;'>" . $this->M_global->getData('m_supplier', ['kode_supplier' => $header->kode_supplier])->nama . "</td>
            </tr>
            <tr>
                <td style='width: 30%;'>Gudang</td>
                <td style='width: 10%;'> : </td>
                <td style='width: 60%;'>" . $this->M_global->getData('m_gudang', ['kode_gudang' => $header->kode_gudang])->nama . "</td>
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

    // form barang_in page
    public function form_barang_in($param)
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $kode_cabang = $this->session->userdata('cabang');

        if ($param != '0') {
            $barang_in     = $this->M_global->getData('barang_in_header', ['invoice' => $param]);
            $barang_detail = $this->M_global->getDataResult('barang_in_detail', ['invoice' => $param]);
        } else {
            $barang_in     = null;
            $barang_detail = null;
        }

        $parameter = [
            $this->data,
            'judul'             => 'Transaksi',
            'nama_apps'         => $web_setting->nama,
            'page'              => 'Pembelian',
            'web'               => $web_setting,
            'web_version'       => $web_version->version,
            'list_data'         => '',
            'data_barang_in'    => $barang_in,
            'barang_detail'     => $barang_detail,
            'barang_po_in_x'    => $this->db->query('SELECT bpo.* FROM barang_po_in_header bpo WHERE bpo.is_valid = 1 AND bpo.kode_cabang = "' . $kode_cabang . '"')->result(),
            'barang_po_in'      => $this->db->query('SELECT dpo.invoice, hpo.tgl_po, hpo.jam_po FROM barang_po_in_detail dpo JOIN barang_po_in_header hpo ON dpo.invoice = hpo.invoice WHERE hpo.kode_cabang = "' . $kode_cabang . '" AND (hpo.invoice NOT IN (SELECT ht.invoice_po FROM barang_in_header ht WHERE ht.kode_cabang = "' . $kode_cabang . '") OR dpo.qty != (SELECT COALESCE(SUM(dt.qty), 0) FROM barang_in_detail dt JOIN barang_in_header ht ON dt.invoice = ht.invoice WHERE ht.invoice_po = hpo.invoice AND dt.kode_barang = dpo.kode_barang AND ht.kode_cabang = "' . $kode_cabang . '"))GROUP BY dpo.invoice, hpo.tgl_po, hpo.jam_po')->result(),
            'role'              => $this->M_global->getResult('m_role'),
            'pajak'             => $this->M_global->getData('m_pajak', ['aktif' => 1])->persentase,
            'list_barang'       => $this->db->query("SELECT b.* FROM barang b JOIN barang_cabang bc USING (kode_barang) WHERE bc.kode_cabang = '$kode_cabang'")->result(),
        ];

        $this->template->load('Template/Content', 'Barang/Form_barang_in', $parameter);
    }

    public function getPengajuan($invoice)
    {
        $header = $this->db->query('SELECT bpo.*, (SELECT nama FROM m_supplier WHERE kode_supplier = bpo.kode_supplier) AS nama_supplier, (SELECT nama FROM m_gudang WHERE kode_gudang = bpo.kode_gudang) AS nama_gudang FROM barang_po_in_header bpo WHERE bpo.invoice = "' . $invoice . '"')->row();

        if ($header) {
            $detail = $this->db->query('SELECT hpo.invoice, dpo.kode_barang, dpo.kode_satuan AS satuan_default,
            dpo.qty - dpo.qty_terima AS qty_po, b.nama, b.kode_satuan, b.kode_satuan2, b.kode_satuan3,
            dpo.harga, dpo.discpr, dpo.discrp, dpo.pajak, dpo.pajakrp, dpo.jumlah, (SELECT keterangan FROM m_satuan WHERE kode_satuan = dpo.kode_satuan) AS nama_satuan
            FROM barang_po_in_detail dpo
            JOIN barang_po_in_header hpo ON hpo.invoice = dpo.invoice
            JOIN barang b ON b.kode_barang = dpo.kode_barang
            WHERE dpo.qty_terima != dpo.qty AND hpo.invoice = "' . $invoice . '"')->result();

            foreach ($detail as $value) {
                $satuan = [];
                foreach ([$value->kode_satuan, $value->kode_satuan2, $value->kode_satuan3] as $satuanCode) {
                    $satuanDetail = $this->M_global->getData('m_satuan', ['kode_satuan' => $satuanCode]);
                    if ($satuanDetail) {
                        $satuan[] = [
                            'kode_satuan' => $satuanCode,
                            'keterangan' => $satuanDetail->keterangan,
                        ];
                    } else {
                        $satuan[] = '';
                    }
                }
            }

            echo json_encode([['status' => 1, 'header' => $header], $detail, $satuan]);
        } else {
            echo json_encode([['status' => 0]]);
        }
    }

    // fungsi ambil data barang
    public function getBarang($kode_barang)
    {
        $cabang = $this->session->userdata('cabang');
        $barang = $this->M_global->getDataLike('barang', 'nama', 'kode_barang', $kode_barang);

        if ($barang) {
            $cek_cabang = $this->M_global->getData('barang_cabang', ['kode_cabang' => $cabang, 'kode_barang' => $barang->kode_barang]);

            if ($cek_cabang) {
                $barang = $barang;
                $satuan = [];
                foreach ([$barang->kode_satuan, $barang->kode_satuan2, $barang->kode_satuan3] as $satuanCode) {
                    $satuanDetail = $this->M_global->getData('m_satuan', ['kode_satuan' => $satuanCode]);
                    if ($satuanDetail) {
                        $satuan[] = [
                            'kode_satuan' => $satuanCode,
                            'keterangan' => $satuanDetail->keterangan,
                        ];
                    }
                }
            } else {
                $barang = '';
                $satuan = '';
            }
        } else {
            $barang = '';
            $satuan = '';
        }


        if ($barang) {
            echo json_encode([$barang, $satuan]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi ambil data satuan
    public function getSatuan($kode_satuan, $kode_barang)
    {
        $barang = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan' => $kode_satuan]);
        $barang2 = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan2' => $kode_satuan]);
        $barang3 = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan3' => $kode_satuan]);

        if ($barang) {
            $qty_result = 1;
            $harga_result = (int)$barang->hna;
            $harga_jual = (int)$barang->harga_jual;
        } else if ($barang2) {
            $qty_result = $barang2->qty_satuan2;
            $harga_result = (int)$barang2->hna;
            $harga_jual = (int)$barang2->harga_jual;
        } else {
            $qty_result = $barang3->qty_satuan3;
            $harga_result = (int)$barang3->hna;
            $harga_jual = (int)$barang3->harga_jual;
        }

        echo json_encode(['qty_satuan' => $qty_result, "hna" => $harga_result, "harga_jual" => $harga_jual]);
    }

    // fungsi insert/update proses barang_in
    public function barang_in_proses($param)
    {
        $kode_cabang      = $this->session->userdata('cabang');
        $shift            = $this->session->userdata('shift');

        // header
        if ($param == 1) { // jika param = 1
            $invoice = _invoice($kode_cabang);
        } else {
            $invoice = $this->input->post('invoice');
        }

        $invoice_po       = $this->input->post('invoice_po');
        $tgl_beli         = $this->input->post('tgl_beli');
        $jam_beli         = $this->input->post('jam_beli');
        $kode_supplier    = $this->input->post('kode_supplier');
        $kode_gudang      = $this->input->post('kode_gudang');
        $surat_jalan      = $this->input->post('surat_jalan');
        $no_faktur        = $this->input->post('no_faktur');
        $kirim_via        = $this->input->post('kirim_via');
        $tempo            = $this->input->post('tempo');

        if (!$surat_jalan || $surat_jalan == null) {
            $sj = _surat_jalan($kode_cabang);
        } else {
            $sj = $surat_jalan;
        }

        if (!$no_faktur || $no_faktur == null) {
            $nf = _no_faktur($kode_cabang);
        } else {
            $nf = $no_faktur;
        }

        $subtotal         = str_replace(',', '', $this->input->post('subtotal'));
        $diskon           = str_replace(',', '', $this->input->post('diskon'));
        $pajak            = str_replace(',', '', $this->input->post('pajak'));
        $total            = str_replace(',', '', $this->input->post('total'));

        // detail
        $kode_barang_in   = $this->input->post('kode_barang_in');
        $kode_satuan_in   = $this->input->post('kode_satuan');
        $harga_in         = $this->input->post('harga_in');
        $qty_in           = $this->input->post('qty_in');
        $discpr_in        = $this->input->post('discpr_in');
        $discrp_in        = $this->input->post('discrp_in');
        $pajakrp_in       = $this->input->post('pajakrp_in');
        $jumlah_in        = $this->input->post('jumlah_in');

        // cek jumlah detail barang_in
        if (isset($kode_barang_in)) {

            $jum              = count($kode_barang_in);

            // tampung isi header
            $isi_header = [
                'kode_cabang'   => $kode_cabang,
                'invoice'       => $invoice,
                'invoice_po'    => $invoice_po,
                'tgl_beli'      => $tgl_beli,
                'jam_beli'      => $jam_beli,
                'kode_supplier' => $kode_supplier,
                'kode_gudang'   => $kode_gudang,
                'surat_jalan'   => $sj,
                'no_faktur'     => $nf,
                'pajak'         => $pajak,
                'diskon'        => $diskon,
                'subtotal'      => $subtotal,
                'total'         => $total,
                'kode_user'     => $this->session->userdata('kode_user'),
                'shift'         => $shift,
                'kirim_via'     => $kirim_via,
                'tempo'         => $tempo,
                'batal'         => 0,
                'is_valid'      => 0,
            ];

            if ($param == 2) { // jika param = 2
                aktifitas_user_transaksi('Transaksi Masuk', 'mengubah Terima Barang', $invoice);

                if ($invoice_po != '' || $invoice_po != null || !empty($invoice_po) || isset($invoice_po)) {
                    $detail_terima = $this->M_global->getDataResult('barang_in_detail', ['invoice' => $invoice]);

                    foreach ($detail_terima as $dt) {
                        $where_po = ['invoice' => $invoice_po, 'kode_barang' => $dt->kode_barang, 'kode_satuan' => $dt->kode_satuan];

                        $data_update = [
                            'qty_terima' => 0
                        ];

                        $this->M_global->updateData('barang_po_in_detail', $data_update, $where_po);
                    }
                }

                // jalankan fungsi cek
                $cek = [
                    $this->M_global->updateData('barang_in_header', $isi_header, ['invoice' => $invoice]), // update header
                    $this->M_global->delData('barang_in_detail', ['invoice' => $invoice]), // delete detail
                ];
            } else { // selain itu
                aktifitas_user_transaksi('Transaksi Masuk', 'menambahkan Terima Barang', $invoice);

                // jalankan fungsi cek
                $cek = [
                    $this->M_global->insertData('barang_in_header', $isi_header),
                ]; // insert header
            }

            if ($cek) { // jika fungsi cek berjalan
                // lakukan loop
                for ($x = 0; $x <= ($jum - 1); $x++) {
                    $kode_barang    = $kode_barang_in[$x];
                    $kode_satuan    = $kode_satuan_in[$x];
                    $harga          = str_replace(',', '', $harga_in[$x]);
                    $qty            = str_replace(',', '', $qty_in[$x]);
                    $discpr         = str_replace(',', '', $discpr_in[$x]);
                    $discrp         = str_replace(',', '', $discrp_in[$x]);
                    $pajakrp        = str_replace(',', '', $pajakrp_in[$x]);
                    $jumlah         = str_replace(',', '', $jumlah_in[$x]);

                    $barang1 = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan' => $kode_satuan]);
                    $barang2 = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan2' => $kode_satuan]);
                    $barang3 = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan3' => $kode_satuan]);

                    if ($barang1) {
                        $qty_satuan = 1;
                    } else if ($barang2) {
                        $qty_satuan = $barang2->qty_satuan2;
                    } else {
                        $qty_satuan = $barang3->qty_satuan3;
                    }

                    $qty_konversi   = $qty * $qty_satuan;

                    // tamping isi detail
                    $isi_detail = [
                        'invoice'       => $invoice,
                        'kode_barang'   => $kode_barang,
                        'kode_satuan'   => $kode_satuan,
                        'harga'         => $harga,
                        'qty_konversi'  => $qty_konversi,
                        'qty'           => $qty,
                        'discpr'        => $discpr,
                        'discrp'        => $discrp,
                        'pajak'         => (($pajakrp > 0) ? 1 : 0),
                        'pajakrp'       => $pajakrp,
                        'jumlah'        => $jumlah,
                    ];

                    // insert detail
                    $this->M_global->insertData('barang_in_detail', $isi_detail);

                    if ($invoice_po != '' || $invoice_po != null || !empty($invoice_po) || isset($invoice_po)) {
                        $where_po = ['invoice' => $invoice_po, 'kode_barang' => $kode_barang];

                        $detail_po = $this->M_global->getData('barang_po_in_detail', $where_po);

                        $data_update = [
                            'qty_terima' => ($detail_po->qty_terima + $qty)
                        ];

                        $this->M_global->updateData('barang_po_in_detail', $data_update, $where_po);
                    }
                }

                $this->single_print_bin($invoice, 1);

                // beri nilai status = 1 kirim ke view
                echo json_encode(['status' => 1]);
            } else { // selain itu
                // beri nilai status = 0 kirim ke view
                echo json_encode(['status' => 0]);
            }
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi batal/re-batal
    public function activedbarang_in($invoice, $batal)
    {
        $user_batal = $this->session->userdata('kode_user');

        if ($batal == 0) { // jika batal = 0
            // update batal jadi 0
            $cek = $this->M_global->updateData('barang_in_header', ['batal' => 0, 'tgl_batal' => null, 'jam_batal' => null, 'user_batal' => null], ['invoice' => $invoice]);
        } else { // selain itu
            // update batal jadi 1
            $cek = $this->M_global->updateData('barang_in_header', ['batal' => 1, 'tgl_batal' => date('Y-m-d'), 'jam_batal' => date('H:i:s'), 'user_batal' => $user_batal], ['invoice' => $invoice]);
        }

        if ($cek) { // jika fungsi cek berjalan
            // kirim status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirim status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi acc/re-acc
    public function accbarang_in($invoice, $acc)
    {
        $kode_cabang = $this->session->userdata('cabang');
        // header barang by invoice
        $header = $this->M_global->getData('barang_in_header', ['invoice' => $invoice]);
        // kode_gudang
        $kode_gudang = $header->kode_gudang;

        // detail barang
        $detail = $this->M_global->getDataResult('barang_in_detail', ['invoice' => $invoice]);

        if ($acc == 0) { // jika acc = 0
            aktifitas_user_transaksi('Transaksi Masuk', 'Reject Terima Barang', $invoice);

            // update piutang
            $piutang = $this->M_global->getData('piutang', ['referensi' => $invoice, 'kode_cabang' => $kode_cabang]);
            $piutang_no = $piutang->piutang_no;

            // update jurnal
            $jurnal_header = $this->M_global->getData('jurnal_header', ['referensi' => $invoice, 'kode_cabang' => $kode_cabang]);
            $kode_jurnal = $jurnal_header->kode_jurnal;

            // update is_valid jadi 0
            $cek = [
                $this->M_global->updateData('barang_in_header', ['is_valid' => 0, 'tgl_valid' => null, 'jam_valid' => null], ['invoice' => $invoice]),
                $this->M_global->delData('piutang', ['piutang_no' => $piutang_no]),
                $this->M_global->delData('jurnal_header', ['kode_jurnal' => $kode_jurnal]),
                $this->M_global->delData('jurnal_detail', ['kode_jurnal' => $kode_jurnal]),
            ];

            hitungStokBrgOut($detail, $kode_gudang, $invoice);
        } else { // selain itu
            aktifitas_user_transaksi('Transaksi Masuk', 'Confirm Terima Barang', $invoice);

            // insert piutang ketika di acc
            $piutang_no = _noPiutang($kode_cabang);
            $kode_jurnal = master_kode('jurnal', 15, 'JUR', '-', $this->session->userdata('init_cabang'), '-');

            $isi_piutang = [
                'kode_cabang'       => $kode_cabang,
                'piutang_no'        => $piutang_no,
                'tanggal'           => $header->tgl_beli,
                'jam'               => $header->jam_beli,
                'referensi'         => $invoice,
                'jumlah'            => $header->total,
                'jenis'             => 1, // 1 adalah hutang 0 adalh piutang
                'status'            => 0, // 0 belum dibayar
            ];

            $isi_jurnal = [
                'kode_jurnal'       => $kode_jurnal,
                'kode_cabang'       => $kode_cabang,
                'tgl_jurnal'        => date('Y-m-d'),
                'jam_jurnal'        => date('H:i:s'),
                'keterangan'        => 'Pembelian barang Supplier',
                'referensi'         => $invoice,
                'tgl_buat'          => date('Y-m-d'),
                'jam_buat'          => date('H:i:s'),
                'kode_user'         => $this->session->userdata('kode_user'),
            ];

            // Prepare journal entries array
            $isi_jurnal_d = [];

            // Add first journal entry
            $isi_jurnal_d[] = [
                'kode_jurnal'   => $kode_jurnal,
                'kode_coa'      => '1201',
                'debit'         => $header->total,
                'credit'        => 0,
                'keterangan'    => 'Persediaan Obat dari Pembelian'
            ];

            // Add second journal entry  
            $isi_jurnal_d[] = [
                'kode_jurnal'   => $kode_jurnal,
                'kode_coa'      => '2101',
                'debit'         => 0,
                'credit'        => $header->total,
                'keterangan'    => 'Hutang ke Supplier atas Pembelian Obat'
            ];

            // update is_valid jadi 1
            $cek = [
                $this->M_global->updateData('barang_in_header', ['is_valid' => 1, 'tgl_valid' => date('Y-m-d'), 'jam_valid' => date('H:i:s')], ['invoice' => $invoice]),
                $this->M_global->insertData('piutang', $isi_piutang),
                $this->M_global->insertData('jurnal_header', $isi_jurnal)
            ];

            // Insert each journal entry separately
            foreach ($isi_jurnal_d as $jurnal_detail) {
                $cek[] = $this->M_global->insertData('jurnal_detail', $jurnal_detail);
            }

            $detail = $this->M_global->getDataResult('barang_in_detail', ['invoice' => $invoice]);

            foreach ($detail as $d) {
                $barang         = $this->M_global->getData('barang', ['kode_barang' => $d->kode_barang]);

                $kode_barang    = $d->kode_barang;
                $harga          = $d->harga;
                $qty_konversi   = $d->qty_konversi;
                $qty            = $d->qty;

                $new_hna = ($harga / ($qty_konversi / $qty));

                if ($barang->opsi_hpp == 2) {
                    $hpp = $new_hna + ($new_hna * ($barang->persentase_hpp / 100));
                } else {
                    $hpp = $new_hna;
                }
                $this->M_global->updateData('barang', ['hna' => $new_hna, 'hpp' => $hpp], ['kode_barang' => $kode_barang]); // update barang
            }

            hitungStokBrgIn($detail, $kode_gudang, $invoice);
        }

        if ($cek) { // jika fungsi cek berjalan
            // kirim status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirim status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi hapus barang in
    public function delBeliIn($invoice)
    {
        $header = $this->M_global->getData('barang_in_header', ['invoice' => $invoice]);
        $detail_cek = $this->M_global->getDataResult('barang_in_detail', ['invoice' => $invoice]);

        if ($header->invoice_po != '' || $header->invoice_po != null || !empty($header->invoice_po) || isset($header->invoice_po)) {
            foreach ($detail_cek as $dc) {
                $detail_terima = $this->M_global->getData('barang_in_detail', ['invoice' => $invoice, 'kode_barang' => $dc->kode_barang]);
                $detail_po = $this->M_global->getData('barang_po_in_detail', ['invoice' => $header->invoice_po, 'kode_barang' => $dc->kode_barang]);

                if ($detail_po->kode_barang == $detail_terima->kode_barang) {
                    $where_po = ['invoice' => $header->invoice_po, 'kode_barang' => $detail_po->kode_barang, 'kode_satuan' => $detail_po->kode_satuan];

                    $data_update = [
                        'qty_terima' => $detail_po->qty_terima - $detail_terima->qty
                    ];

                    $this->M_global->updateData('barang_po_in_detail', $data_update, $where_po);
                }
            }
        }
        aktifitas_user_transaksi('Transaksi Masuk', 'menghapus Terima Barang', $invoice);

        // jalankan fungsi cek
        $cek = [
            $this->M_global->delData('barang_in_detail', ['invoice' => $invoice]), // del data detail pembelian
            $this->M_global->delData('barang_in_header', ['invoice' => $invoice]), // del data header pembelian
            $this->M_global->delData('piutang', ['referensi' => $invoice]),
        ];

        if ($cek) { // jika fungsi cek berjalan
            // kirim status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirim status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    /*
    * Pembelian Retur
    **/

    // barang_in_retur page
    public function barang_in_retur()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Transaksi',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Retur Pembelian',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Transaksi/barang_in_retur_list/',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Barang/Retur', $parameter);
    }

    // fungsi list barang_in_retur
    public function barang_in_retur_list($param1 = 1, $param2 = '')
    {
        // parameter untuk list table
        $table            = 'barang_in_retur_header';
        $colum            = ['id', 'invoice', 'invoice_in', 'tgl_retur', 'jam_retur', 'kode_supplier', 'kode_gudang', 'surat_jalan', 'no_faktur', 'pajak', 'diskon', 'total', 'kode_user', 'batal', 'tgl_batal', 'jam_batal', 'user_batal', 'is_valid', 'shift'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param2   = 'kode_gudang';
        $kondisi_param1   = 'tgl_retur';

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
                if ($rd->batal > 0) {
                    $upd_diss = 'disabled';
                } else {
                    if ($rd->is_valid > 0) {
                        $upd_diss = 'disabled';
                    } else {
                        $upd_diss =  _lock_button();
                    }
                }
            } else {
                $upd_diss = 'disabled';
            }

            if ($deleted > 0) {
                if ($rd->batal > 0) {
                    $del_diss = 'disabled';
                } else {
                    if ($rd->is_valid > 0) {
                        $del_diss = 'disabled';
                    } else {
                        $del_diss = _lock_button();
                    }
                }
            } else {
                $del_diss = 'disabled';
            }

            if ($confirmed > 0) {
                $confirm_diss = _lock_button();
            } else {
                $confirm_diss = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->invoice . '<br>' . (($rd->batal == 0) ? (($rd->is_valid > 0) ? '<span class="badge badge-primary">ACC</span>' : '<span class="badge badge-success">Buka</span>') : '<span class="badge badge-danger">Batal</span>');
            $row[]  = date('d/m/Y', strtotime($rd->tgl_retur)) . ' ~ ' . date('H:i:s', strtotime($rd->jam_retur));
            $row[]  = $this->M_global->getData('m_supplier', ['kode_supplier' => $rd->kode_supplier])->nama;
            $row[]  = $this->M_global->getData('m_gudang', ['kode_gudang' => $rd->kode_gudang])->nama;
            $row[]  = 'Rp. <span class="float-right">' . number_format($rd->total) . '</span>';
            $row[]  = $this->M_global->getData('user', ['kode_user' => $rd->kode_user])->nama . '<br><span class="badge badge-danger">Shift: ' . $rd->shift . '</span>';

            if ($rd->is_valid < 1) {
                if ($rd->batal < 1) {
                    $batal = '<button type="button" style="margin-bottom: 5px;" class="btn btn-secondary" title="Batalkan" onclick="actived(' . "'" . $rd->invoice . "', 1" . ')" ' . $confirm_diss . '><i class="fa-solid fa-ban"></i></button>';

                    $ubah = '<button type="button" style="margin-bottom: 5px;" class="btn btn-warning" title="Ubah" onclick="ubah(' . "'" . $rd->invoice . "'" . ')" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>';

                    $accept = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="ACC" onclick="valided(' . "'" . $rd->invoice . "', 1" . ')" ' . $confirm_diss . '><i class="fa-regular fa-circle-check"></i></button>';

                    $email = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="Kirim Email" disabled><i class="fa-solid fa-envelope-open-text"></i></button>';
                } else {
                    $batal = '<button type="button" style="margin-bottom: 5px;" class="btn btn-light" title="Re-Batalkan" onclick="actived(' . "'" . $rd->invoice . "', 0" . ')" ' . $confirm_diss . '><i class="fa-solid fa-arrow-rotate-left"></i></button>';

                    $ubah = '<button type="button" style="margin-bottom: 5px;" class="btn btn-warning" title="Ubah" disabled><i class="fa-regular fa-pen-to-square"></i></button>';

                    $accept = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="ACC" disabled><i class="fa-regular fa-circle-check"></i></button>';

                    $email = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="Kirim Email" disabled><i class="fa-solid fa-envelope-open-text"></i></button>';
                }
            } else {
                $accept = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="Re-ACC" onclick="valided(' . "'" . $rd->invoice . "', 0" . ')" ' . $confirm_diss . '><i class="fa-solid fa-check-to-slot"></i></button>';

                $ubah = '<button type="button" style="margin-bottom: 5px;" class="btn btn-warning" title="Ubah" disabled><i class="fa-regular fa-pen-to-square"></i></button>';

                $batal = '<button type="button" style="margin-bottom: 5px;" class="btn btn-secondary" title="Batalkan" disabled><i class="fa-solid fa-ban"></i></button>';

                $email = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="Kirim Email" onclick="email(' . "'" . $rd->invoice . "', 0" . ')"><i class="fa-solid fa-envelope-open-text"></i></button>';
            }

            $row[]  = '<div class="text-center">
                ' . $accept . '
                ' . $ubah . '
                <button type="button" style="margin-bottom: 5px;" class="btn btn-danger" title="Hapus" onclick="hapus(' . "'" . $rd->invoice . "'" . ')" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
                <br>
                ' . $batal . '
                <button type="button" style="margin-bottom: 5px;" class="btn btn-dark" title="Cetak" onclick="cetak(' . "'" . $rd->invoice . "', 0" . ')"><i class="fa-solid fa-print"></i></button>
                ' . $email . '
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

    // form barang_in_retur page
    public function form_barang_in_retur($param)
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $kode_cabang = $this->session->userdata('cabang');

        if ($param != '0') {
            $barang_in_retur    = $this->M_global->getData('barang_in_retur_header', ['invoice' => $param]);
            $barang_detail      = $this->M_global->getDataResult('barang_in_retur_detail', ['invoice' => $param]);
            $pembeli            = $this->db->query('SELECT dpo.invoice, hpo.tgl_beli, hpo.tgl_beli AS tgl_retur, hpo.jam_beli AS jam_retur, hpo.kode_supplier, hpo.kode_gudang FROM barang_in_detail dpo JOIN barang_in_header hpo ON dpo.invoice = hpo.invoice WHERE hpo.kode_cabang = "' . $kode_cabang . '" AND (hpo.invoice NOT IN (SELECT ht.invoice FROM barang_in_header ht WHERE ht.kode_cabang = "' . $kode_cabang . '") OR dpo.qty != (SELECT COALESCE(SUM(dt.qty), 0) FROM barang_in_retur_detail dt JOIN barang_in_retur_header ht ON dt.invoice = ht.invoice WHERE ht.invoice = hpo.invoice AND dt.kode_barang = dpo.kode_barang AND ht.kode_cabang = "' . $kode_cabang . '"))GROUP BY dpo.invoice, hpo.tgl_beli, hpo.jam_beli, hpo.kode_supplier, hpo.kode_gudang')->result();
        } else {
            $barang_in_retur    = null;
            $barang_detail      = null;
            $pembeli            = $this->db->query('SELECT bpo.* FROM barang_in_header bpo WHERE bpo.is_valid = 1 AND bpo.kode_cabang = "' . $kode_cabang . '"')->result();
        }

        $parameter = [
            $this->data,
            'judul'                 => 'Transaksi',
            'nama_apps'             => $web_setting->nama,
            'page'                  => 'Retur Pembelian',
            'web'                   => $web_setting,
            'web_version'           => $web_version->version,
            'list_data'             => '',
            'data_barang_in_retur'  => $barang_in_retur,
            'barang_detail'         => $barang_detail,
            'pembelian'             => $pembeli,
            'role'                  => $this->M_global->getResult('m_role'),
            'pajak'                 => $this->M_global->getData('m_pajak', ['aktif' => 1])->persentase,
        ];

        $this->template->load('Template/Content', 'Barang/Form_barang_in_retur', $parameter);
    }

    // fungsi get Barang In 
    public function getBarangIn($invoice)
    {
        $header = $this->db->query('SELECT bpo.*, (SELECT nama FROM m_supplier WHERE kode_supplier = bpo.kode_supplier) AS nama_supplier, (SELECT nama FROM m_gudang WHERE kode_gudang = bpo.kode_gudang) AS nama_gudang FROM barang_in_header bpo WHERE bpo.invoice = "' . $invoice . '"')->row();

        if ($header) {
            $detail = $this->db->query('SELECT hpo.invoice, dpo.kode_barang, dpo.kode_satuan AS satuan_default,
            dpo.qty - dpo.qty_retur AS qty_po, b.nama, b.kode_satuan, b.kode_satuan2, b.kode_satuan3,
            dpo.harga, dpo.discpr, dpo.discrp, dpo.pajak, dpo.pajakrp, dpo.jumlah, hpo.surat_jalan, hpo.no_faktur, (SELECT keterangan FROM m_satuan WHERE kode_satuan = dpo.kode_satuan) AS nama_satuan
            FROM barang_in_detail dpo
            JOIN barang_in_header hpo ON hpo.invoice = dpo.invoice
            JOIN barang b ON b.kode_barang = dpo.kode_barang
            WHERE dpo.qty_retur != dpo.qty AND hpo.invoice = "' . $invoice . '"')->result();

            foreach ($detail as $value) {
                $satuanDetail = $this->M_global->getData('m_satuan', ['kode_satuan' => $value->satuan_default]);
                if ($satuanDetail) {
                    $satuan = [
                        'kode_satuan'   => $value->satuan_default,
                        'keterangan'    => $satuanDetail->keterangan,
                    ];
                } else {
                    $satuan = '';
                }
            }

            echo json_encode([['status' => 1, 'header' => $header], $detail, $satuan]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi ambil satuan
    public function getSatuanBarangIn($kode_satuan)
    {
        $satuan = $this->M_global->getData('m_satuan', ['kode_satuan' => $kode_satuan]);

        if ($satuan) {
            echo json_encode(['status' => 1, 'kode_satuan' => $satuan->kode_satuan, 'keterangan' => $satuan->keterangan]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi insert/update proses barang_in_retur
    public function barang_in_retur_proses($param)
    {
        $kode_cabang    = $this->session->userdata('cabang');
        $shift          = $this->session->userdata('shift');

        // header
        if ($param == 1) { // jika param = 1
            $invoice    = _invoice_retur($kode_cabang);
        } else {
            $invoice    = $this->input->post('invoice');
        }

        $invoice_in     = $this->input->post('invoice_in');
        $tgl_retur      = $this->input->post('tgl_retur');
        $jam_retur      = $this->input->post('jam_retur');
        $kode_supplier  = $this->input->post('kode_supplier');
        $kode_gudang    = $this->input->post('kode_gudang');
        $surat_jalan    = $this->input->post('surat_jalan');
        $no_faktur      = $this->input->post('no_faktur');
        $alasan         = $this->input->post('alasan');

        if (!$surat_jalan || $surat_jalan == null) {
            $sj = _surat_jalan($kode_cabang);
        } else {
            $sj = $surat_jalan;
        }

        if (!$no_faktur || $no_faktur == null) {
            $nf = _no_faktur($kode_cabang);
        } else {
            $nf = $no_faktur;
        }

        $subtotal       = str_replace(',', '', $this->input->post('subtotal'));
        $diskon         = str_replace(',', '', $this->input->post('diskon'));
        $pajak          = str_replace(',', '', $this->input->post('pajak'));
        $total          = str_replace(',', '', $this->input->post('total'));

        // detail
        $kode_barang_in = $this->input->post('kode_barang_in');
        $kode_satuan_in = $this->input->post('kode_satuan');
        $harga_in       = $this->input->post('harga_in');
        $qty_in         = $this->input->post('qty_in');
        $discpr_in      = $this->input->post('discpr_in');
        $discrp_in      = $this->input->post('discrp_in');
        $pajakrp_in     = $this->input->post('pajakrp_in');
        $jumlah_in      = $this->input->post('jumlah_in');

        if (isset($kode_barang_in)) {
            // cek jumlah detail barang_in
            $jum            = count($kode_barang_in);

            // tampung isi header
            $isi_header = [
                'kode_cabang'   => $kode_cabang,
                'invoice'       => $invoice,
                'invoice_in'    => $invoice_in,
                'tgl_retur'     => $tgl_retur,
                'jam_retur'     => $jam_retur,
                'alasan'        => $alasan,
                'kode_supplier' => $kode_supplier,
                'kode_gudang'   => $kode_gudang,
                'surat_jalan'   => $sj,
                'no_faktur'     => $nf,
                'pajak'         => $pajak,
                'diskon'        => $diskon,
                'subtotal'      => $subtotal,
                'total'         => $total,
                'kode_user'     => $this->session->userdata('kode_user'),
                'shift'         => $shift,
                'batal'         => 0,
                'is_valid'      => 0,
            ];

            if ($param == 2) { // jika param = 2
                aktifitas_user_transaksi('Transaksi Masuk', 'mengubah Retur Pembelian', $invoice);

                if ($invoice_in != '' || $invoice_in != null || !empty($invoice_in) || isset($invoice_in)) {
                    $detail_retur = $this->M_global->getDataResult('barang_in_retur_detail', ['invoice' => $invoice]);

                    foreach ($detail_retur as $dt) {
                        $where_po = ['invoice' => $invoice_in, 'kode_barang' => $dt->kode_barang, 'kode_satuan' => $dt->kode_satuan];

                        $data_update = [
                            'qty_retur' => 0
                        ];

                        $this->M_global->updateData('barang_in_detail', $data_update, $where_po);
                    }
                }

                // jalankan fungsi cek
                $cek = [
                    $this->M_global->updateData('barang_in_retur_header', $isi_header, ['invoice' => $invoice]), // update header
                    $this->M_global->delData('barang_in_retur_detail', ['invoice' => $invoice]), // delete detail
                ];
            } else { // selain itu
                aktifitas_user_transaksi('Transaksi Masuk', 'menambahkan Retur Pembelian', $invoice);

                // jalankan fungsi cek
                $cek = $this->M_global->insertData('barang_in_retur_header', $isi_header); // insert header
            }

            if ($cek) { // jika fungsi cek berjalan
                // lakukan loop
                for ($x = 0; $x <= ($jum - 1); $x++) {
                    $kode_barang    = $kode_barang_in[$x];
                    $kode_satuan    = $kode_satuan_in[$x];
                    $harga          = str_replace(',', '', $harga_in[$x]);
                    $qty            = str_replace(',', '', $qty_in[$x]);
                    $discpr         = str_replace(',', '', $discpr_in[$x]);
                    $discrp         = str_replace(',', '', $discrp_in[$x]);
                    $pajakrp        = str_replace(',', '', $pajakrp_in[$x]);
                    $jumlah         = str_replace(',', '', $jumlah_in[$x]);

                    $barang1 = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan' => $kode_satuan]);
                    $barang2 = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan2' => $kode_satuan]);
                    $barang3 = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan3' => $kode_satuan]);

                    if ($barang1) {
                        $qty_satuan = 1;
                    } else if ($barang2) {
                        $qty_satuan = $barang2->qty_satuan2;
                    } else {
                        $qty_satuan = $barang3->qty_satuan3;
                    }

                    $qty_konversi   = $qty * $qty_satuan;

                    // tamping isi detail
                    $isi_detail = [
                        'invoice'       => $invoice,
                        'kode_barang'   => $kode_barang,
                        'kode_satuan'   => $kode_satuan,
                        'harga'         => $harga,
                        'qty_konversi'  => $qty_konversi,
                        'qty'           => $qty,
                        'discpr'        => $discpr,
                        'discrp'        => $discrp,
                        'pajak'         => (($pajakrp > 0) ? 1 : 0),
                        'pajakrp'       => $pajakrp,
                        'jumlah'        => $jumlah,
                    ];

                    // insert detail
                    $this->M_global->insertData('barang_in_retur_detail', $isi_detail);

                    if ($invoice_in != '' || $invoice_in != null || !empty($invoice_in) || isset($invoice_in)) {
                        $where_in = ['invoice' => $invoice_in, 'kode_barang' => $kode_barang];

                        $detail_in = $this->M_global->getData('barang_in_detail', $where_in);

                        $data_update = [
                            'qty_retur' => ($detail_in->qty_retur + $qty)
                        ];

                        $this->M_global->updateData('barang_in_detail', $data_update, $where_in);
                    }
                }

                $this->single_print_bin_ret($invoice, 1);

                // beri nilai status = 1 kirim ke view
                echo json_encode(['status' => 1]);
            } else { // selain itu
                // beri nilai status = 0 kirim ke view
                echo json_encode(['status' => 0]);
            }
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi print single barang_in_retur
    public function single_print_bin_ret($invoice, $yes)
    {
        $param          = 1;

        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $pencetak       = $this->M_global->getData('user', ['kode_user' => $this->session->userdata('kode_user')])->nama;

        $breaktable     = '<br>';
        $file = 'Retur Pembelian';

        // isi body
        $header = $this->M_global->getData('barang_in_retur_header', ['invoice' => $invoice]);

        // body header
        $body .= '<table style="width: 100%; font-size: 11px;">
            <tr>
                <td style="width: 15%;">Perihal</td>
                <td style="width: 2%;"> : </td>
                <td style="width: 33%;">' . $file . '</td>
                <td style="width: 50%; text-align: right; font-weight: bold; color: white;"><span style="border: 1px solid #0e1d2e; background-color: #0e1d2e;">' . $invoice . '</span></td>
            </tr>
            <tr>
                <td style="width: 15%;">Tgl/Jam Beli</td>
                <td style="width: 2%;"> : </td>
                <td colspan="2">' . date('d-m-Y', strtotime($header->tgl_retur)) . ' / ' . date('H:i:s', strtotime($header->jam_retur)) . '</td>
            </tr>
            <tr>
                <td style="width: 15%;">Pemasok</td>
                <td style="width: 2%;"> : </td>
                <td colspan="2">' . $this->M_global->getData('m_supplier', ['kode_supplier' => $header->kode_supplier])->nama . '</td>
            </tr>
            <tr>
                <td style="width: 15%;">Gudang</td>
                <td style="width: 2%;"> : </td>
                <td style="width: 33%;">' . $this->M_global->getData('m_gudang', ['kode_gudang' => $header->kode_gudang])->nama . '</td>
                <td style="width: 50%; text-align: right;">Pencetak : ' . $pencetak . '</td>
            </tr>
        </table>';

        $body .= $breaktable;

        $body .= '<table style="width: 100%; font-size: 10px;" autosize="1" cellpadding="5px">';

        $body .= '<thead>
            <tr>
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
            </tr>
        </thead>';

        $body .= '<tbody>';

        if ($param == 1) {
            $total = number_format($header->total);
        } else {
            $total = ceil($header->total);
        }
        $body .= '<tr style="background-color: skyblue;">
            <td colspan="6" style="border: 1px solid black; font-weight: bold;">No. Transaksi: ' . $header->invoice . '</td>
            <td colspan="2" style="border: 1px solid black; font-weight: bold; text-align: right">' . $total . '</td>
        </tr>';

        // detail barang
        $detail   = $this->M_global->getDataResult('barang_in_retur_detail', ['invoice' => $header->invoice]);

        $no       = 1;
        $tdiskon  = 0;
        $tpajak   = 0;
        $ttotal   = 0;
        foreach ($detail as $d) {
            $tdiskon    += $d->discrp;
            $tpajak     += $d->pajakrp;
            $ttotal     += $d->jumlah;

            if ($param == 1) {
                $harga    = number_format($d->harga);
                $qty      = number_format($d->qty);
                $discpr   = number_format($d->discpr);
                $discrp   = number_format($d->discrp);
                $pajak    = number_format($d->pajakrp);
                $jumlah   = number_format($d->jumlah);

                $tdiskonx = number_format($tdiskon);
                $tpajakx  = number_format($tpajak);
                $ttotalx  = number_format($ttotal);
            } else {
                $harga    = ceil($d->harga);
                $qty      = ceil($d->qty);
                $discpr   = ceil($d->discpr);
                $discrp   = ceil($d->discrp);
                $pajak    = ceil($d->pajakrp);
                $jumlah   = ceil($d->jumlah);

                $tdiskonx = ceil($tdiskon);
                $tpajakx  = ceil($tpajak);
                $ttotalx  = ceil($ttotal);
            }
            $body .= '<tr>
                <td style="border: 1px solid black;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $d->kode_barang . ' ~ ' . $this->M_global->getData('barang', ['kode_barang' => $d->kode_barang])->nama . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $harga . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $qty . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $discpr . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $discrp . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $pajak . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $jumlah . '</td>
            </tr>';
            $no++;
        }
        $body .= '<tr style="background-color: green;">
            <td colspan="5" style="border: 1px solid black; font-weight: bold; color: white;">Total</td>
            <td style="border: 1px solid black; font-weight: bold; color: white; text-align: right">' . $tdiskonx . '</td>
            <td style="border: 1px solid black; font-weight: bold; color: white; text-align: right">' . $tpajakx . '</td>
            <td style="border: 1px solid black; font-weight: bold; color: white; text-align: right">' . $ttotalx . '</td>
        </tr>';

        $body .= '</tbody>';

        $body .= '<tfoot>
            <tr>
                <td colspan="5">&nbsp;</td>
                <td colspan="3" style="text-align: center;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="5" style="width:60%;">&nbsp;</td>
                <td colspan="3" style="width:40%; text-align: center;">Yogyakarta, ' . date('d M Y') . '</td>
            </tr>
            <tr>
                <td colspan="5" style="width:60%;">&nbsp;</td>
                <td colspan="3" style="width:40%; text-align: center;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="5" style="width:60%;">&nbsp;</td>
                <td colspan="3" style="width:40%; text-align: center;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="5" style="width:60%;">&nbsp;</td>
                <td colspan="3" style="width:40%; text-align: center;">' . $pencetak . '</td>
            </tr>
        </tfoot>';

        $body .= '</table>';

        $judul = $invoice;
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting, $yes);
    }

    // fungsi batal/re-batal
    public function activedbarang_in_retur($invoice, $batal)
    {
        if ($batal == 0) { // jika batal = 0
            // update batal jadi 0
            $cek = $this->M_global->updateData('barang_in_retur_header', ['batal' => 0, 'tgl_batal' => null, 'jam_batal' => null], ['invoice' => $invoice]);
        } else { // selain itu
            // update batal jadi 1
            $cek = $this->M_global->updateData('barang_in_retur_header', ['batal' => 1, 'tgl_batal' => date('Y-m-d'), 'jam_batal' => date('H:i:s')], ['invoice' => $invoice]);
        }

        if ($cek) { // jika fungsi cek berjalan
            // kirim status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirim status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi acc/re-acc
    public function accbarang_in_retur($invoice, $acc)
    {
        $kode_cabang    = $this->session->userdata('cabang');
        // header barang retur by invoice
        $header         = $this->M_global->getData('barang_in_retur_header', ['invoice' => $invoice]);
        $detail         = $this->M_global->getDataResult('barang_in_retur_detail', ['invoice' => $invoice]);
        $kode_gudang    = $header->kode_gudang;

        // barang beli
        $invoice_in     = $header->invoice_in;
        $piutang_in     = $this->M_global->getData('piutang', ['referensi' => $invoice_in]);

        if ($acc == 0) { // jika acc = 0
            aktifitas_user_transaksi('Transaksi Masuk', 'Reject Retur Pembelian', $invoice);

            // update piutang
            $jumlah_piutang = $piutang_in->jumlah + $header->total;

            // update jurnal
            $jurnal_header = $this->M_global->getData('jurnal_header', ['referensi' => $invoice, 'kode_cabang' => $kode_cabang]);
            $kode_jurnal = $jurnal_header->kode_jurnal;

            // update is_valid jadi 0
            $cek = [
                $this->M_global->updateData('barang_in_retur_header', ['is_valid' => 0, 'tgl_valid' => null, 'jam_valid' => null], ['invoice' => $invoice]),
                $this->M_global->updateData('piutang', ['jumlah' => $jumlah_piutang], ['piutang_no' => $piutang_in->piutang_no]),
                $this->M_global->delData('jurnal_header', ['kode_jurnal' => $kode_jurnal]),
                $this->M_global->delData('jurnal_detail', ['kode_jurnal' => $kode_jurnal]),
            ];

            hitungStokBrgRtOut($detail, $kode_gudang, $invoice);
        } else { // selain itu
            aktifitas_user_transaksi('Transaksi Masuk', 'Confirm Retur Pembelian', $invoice);

            // update piutang dan jurnal ketika di acc
            $jumlah_piutang = $piutang_in->jumlah - $header->total;
            $kode_jurnal = master_kode('jurnal', 15, 'JUR', '-', $this->session->userdata('init_cabang'), '-');

            // cek_pembayaran
            if ($piutang_in->status == 1) { // sudah bayar
                $isi_jurnal = [
                    'kode_jurnal'       => $kode_jurnal,
                    'kode_cabang'       => $kode_cabang,
                    'tgl_jurnal'        => date('Y-m-d'),
                    'jam_jurnal'        => date('H:i:s'),
                    'keterangan'        => 'Retur pembelian Supplier',
                    'referensi'         => $invoice,
                    'tgl_buat'          => date('Y-m-d'),
                    'jam_buat'          => date('H:i:s'),
                    'kode_user'         => $this->session->userdata('kode_user'),
                ];

                // Prepare journal entries array
                $isi_jurnal_d = [];

                // Add first journal entry
                $isi_jurnal_d[] = [
                    'kode_jurnal'   => $kode_jurnal,
                    'kode_coa'      => '1102',
                    'debit'         => $header->total,
                    'credit'        => 0,
                    'keterangan'    => 'Pengembalian dana'
                ];

                // Add second journal entry  
                $isi_jurnal_d[] = [
                    'kode_jurnal'   => $kode_jurnal,
                    'kode_coa'      => '1201',
                    'debit'         => 0,
                    'credit'        => $header->total,
                    'keterangan'    => 'Pengurangan stok'
                ];
            } else { // belum bayar
                $isi_jurnal = [
                    'kode_jurnal'       => $kode_jurnal,
                    'kode_cabang'       => $kode_cabang,
                    'tgl_jurnal'        => date('Y-m-d'),
                    'jam_jurnal'        => date('H:i:s'),
                    'keterangan'        => 'Retur pembelian Supplier',
                    'referensi'         => $invoice,
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
                    'debit'         => $header->total,
                    'credit'        => 0,
                    'keterangan'    => 'Koreksi Hutang'
                ];

                // Add second journal entry  
                $isi_jurnal_d[] = [
                    'kode_jurnal'   => $kode_jurnal,
                    'kode_coa'      => '1201',
                    'debit'         => 0,
                    'credit'        => $header->total,
                    'keterangan'    => 'Pengurangan Stok'
                ];
            }

            // update is_valid jadi 1
            $cek = [
                $this->M_global->updateData('barang_in_retur_header', ['is_valid' => 1, 'tgl_valid' => date('Y-m-d'), 'jam_valid' => date('H:i:s')], ['invoice' => $invoice]),
                $this->M_global->updateData('piutang', ['jumlah' => $jumlah_piutang], ['piutang_no' => $piutang_in->piutang_no]),
                $this->M_global->insertData('jurnal_header', $isi_jurnal)
            ];

            // Insert each journal entry separately
            foreach ($isi_jurnal_d as $jurnal_detail) {
                $cek[] = $this->M_global->insertData('jurnal_detail', $jurnal_detail);
            }

            hitungStokBrgRtIn($detail, $kode_gudang, $invoice);
        }

        if ($cek) { // jika fungsi cek berjalan
            // kirim status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirim status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi hapus barang in retur
    public function delBeliInRetur($invoice)
    {
        $header = $this->M_global->getData('barang_in_retur_header', ['invoice' => $invoice]);
        $detail_cek = $this->M_global->getDataResult('barang_in_retur_detail', ['invoice' => $invoice]);

        if ($header->invoice_in != '' || $header->invoice_in != null || !empty($header->invoice_in) || isset($header->invoice_in)) {
            foreach ($detail_cek as $dc) {
                $detail_terima = $this->M_global->getData('barang_in_retur_detail', ['invoice' => $invoice, 'kode_barang' => $dc->kode_barang]);
                $detail_po = $this->M_global->getData('barang_in_detail', ['invoice' => $header->invoice_in, 'kode_barang' => $dc->kode_barang]);

                if ($detail_po->kode_barang == $detail_terima->kode_barang) {
                    $where_po = ['invoice' => $header->invoice_in, 'kode_barang' => $detail_po->kode_barang, 'kode_satuan' => $detail_po->kode_satuan];

                    $data_update = [
                        'qty_retur' => $detail_po->qty_retur - $detail_terima->qty
                    ];

                    $this->M_global->updateData('barang_in_detail', $data_update, $where_po);
                }
            }
        }
        aktifitas_user_transaksi('Transaksi Masuk', 'menghapus Retur Pembelian', $invoice);

        // jalankan fungsi cek
        $cek = [
            $this->M_global->delData('barang_in_retur_detail', ['invoice' => $invoice]), // del data detail retur pembelian
            $this->M_global->delData('barang_in_retur_header', ['invoice' => $invoice]), // del data header retur pembelian
            $this->M_global->delData('piutang', ['referensi' => $invoice]),
        ];

        if ($cek) { // jika fungsi cek berjalan
            // kirim status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirim status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi kirim email barang in
    public function email_retur($invoice)
    {
        $email = $this->input->get('email');

        $header = $this->M_global->getData('barang_in_retur_header', ['invoice' => $invoice]);

        $judul = 'Retur Pembelian ~ ' . $invoice;

        // $attched_file    = base_url() . 'assets/file/pdf/' . $judul . '.pdf';ahmad.ummgl@gmail.com
        $attched_file    = $_SERVER["DOCUMENT_ROOT"] . '/first_apps/assets/file/pdf/' . $judul . '.pdf';

        $ready_message   = "";
        $ready_message   .= "<table border=0>
            <tr>
                <td style='width: 30%;'>Invoice</td>
                <td style='width: 10%;'> : </td>
                <td style='width: 60%;'> $invoice </td>
            </tr>
            <tr>
                <td style='width: 30%;'>Tgl/Jam</td>
                <td style='width: 10%;'> : </td>
                <td style='width: 60%;'>" . date('d-m-Y', strtotime($header->tgl_retur)) . " / " . date('H:i:s', strtotime($header->jam_retur)) . "</td>
            </tr>
            <tr>
                <td style='width: 30%;'>Pemasok</td>
                <td style='width: 10%;'> : </td>
                <td style='width: 60%;'>" . $this->M_global->getData('m_supplier', ['kode_supplier' => $header->kode_supplier])->nama . "</td>
            </tr>
            <tr>
                <td style='width: 30%;'>Gudang</td>
                <td style='width: 10%;'> : </td>
                <td style='width: 60%;'>" . $this->M_global->getData('m_gudang', ['kode_gudang' => $header->kode_gudang])->nama . "</td>
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

    /*
    * Penjualan
    **/

    // barang_out page
    public function barang_out()
    {
        // website config
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version    = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter      = [
            $this->data,
            'judul'         => 'Transaksi',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Penjualan',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Transaksi/barang_out_list/',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Jual/Keluar', $parameter);
    }

    // fungsi list emr dokter
    public function emr_list($param1)
    {

        // Kondisi role
        $updated      = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted      = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;

        // Table server side tampung kedalam variable $list
        $dat          = explode("~", $param1);
        if ($dat[0] == 1) {
            $dari     = date('Y-m-d');
            $sampai   = date('Y-m-d');
            $tipe     = 1;
        } else {
            $dari     = date('Y-m-d', strtotime($dat[1])); // Extract month from date
            $sampai   = date('Y-m-d', strtotime($dat[2])); // Extract year from date
            $tipe     = 2;
        }

        $list         = $this->M_order_emr->get_datatables($dari, $sampai, $tipe);

        $data         = [];
        $no           = $_POST['start'] + 1;

        // Loop $list
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

            $row = [];
            $row[] = $no++;
            $row[] = $rd->no_trx . '<br>' . (($rd->status_trx == 0) ? '<span class="badge badge-success">Buka</span>' : (($rd->status_trx == 2) ? '<span class="badge badge-danger">Batal</span>' : '<span class="badge badge-primary">Selesai</span>'));
            $row[] = '<span class="float-right">' . date('d/m/Y', strtotime($rd->date_dok)) . ' ~ ' . date('H:i:s', strtotime($rd->time_dok)) . '</span>';
            $row[] = 'No. RM: <span class="float-right">' . $rd->kode_member . '</span><hr>Nama: <span class="float-right">' . $this->M_global->getData('member', ['kode_member' => $rd->kode_member])->nama . '</span>';
            $row[] = 'Dr. ' . $rd->dokter . '<hr>(Poli: ' . $rd->poli . ')';
            $row[] = $rd->perawat;

            if ($rd->status_trx == 0) {
                $disabled = '';
            } else {
                $disabled = 'disabled';
            }

            $row[] = '<div class="d-flex justify-content-center">
                <button type="button" class="btn btn-success" onclick="ubah(' . "'emr', '" . $rd->no_trx . "'" . ')" ' . $disabled . '>
                    <i class="fa-solid fa-receipt"></i> Proses
                </button>
            </div>';

            $data[] = $row;
        }

        // Hasil server side
        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->M_order_emr->count_all($dari, $sampai, $tipe),
            "recordsFiltered" => $this->M_order_emr->count_filtered($dari, $sampai, $tipe),
            "data" => $data,
        ];

        // Kirimkan ke view
        echo json_encode($output);
    }

    // fungsi list barang_out
    public function barang_out_list($param1 = 1, $param2 = '')
    {
        // parameter untuk list table
        $table            = 'barang_out_header';
        $colum            = ['id', 'invoice', 'kode_member', 'no_trx', 'tgl_jual', 'jam_jual', 'status_jual', 'kode_gudang', 'pajak', 'diskon', 'total', 'kode_user', 'batal', 'tgl_batal', 'jam_batal', 'user_batal', 'shift'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param2   = 'kode_gudang';
        $kondisi_param1   = 'tgl_jual';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;
        $confirmed        = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->confirmed;

        // table server side tampung kedalam variable $list
        $dat              = explode("~", $param1);

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
                if ($rd->batal > 0) {
                    $upd_diss = 'disabled';
                } else {
                    if ($rd->status_jual > 0) {
                        $upd_diss = 'disabled';
                    } else {
                        $upd_diss =  _lock_button();
                    }
                }
            } else {
                $upd_diss = 'disabled';
            }

            if ($deleted > 0) {
                if ($rd->batal > 0) {
                    $del_diss = 'disabled';
                } else {
                    if ($rd->status_jual > 0) {
                        $del_diss = 'disabled';
                    } else {
                        $del_diss = _lock_button();
                    }
                }
            } else {
                $del_diss = 'disabled';
            }

            if ($confirmed > 0) {
                if ($rd->status_jual > 0) {
                    $confirm_diss = 'disabled';
                } else {
                    $confirm_diss = _lock_button();
                }
            } else {
                $confirm_diss = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            // $row[]  = $rd->invoice . '<br>' . (($rd->status_jual == 0) ? (($rd->status_jual > 1) ? '<span class="badge badge-danger">Batal</span>' : '<span class="badge badge-success">Buka</span>') : '<span class="badge badge-primary">Selesai</span>') . (($this->M_global->jumDataRow('barang_out_retur_header', ['invoice_jual' => $rd->invoice]) > 0) ? '<br><span class="badge badge-warning">Tedapat Returan ~ ' . (($this->M_global->jumDataRow('pembayaran', ['inv_jual' => $this->M_global->getData('barang_out_retur_header', ['invoice_jual' => $rd->invoice])->invoice]) > 0) ? 'Sudah diproses kasir' : 'Belum diproses kasir') . '</span>' : '');
            $row[]  = $rd->invoice . '<br>' . (($rd->status_jual == 0) ? (($rd->status_jual > 1) ? '<span class="badge badge-danger">Batal</span>' : '<span class="badge badge-success">Buka</span>') : '<span class="badge badge-primary">Selesai</span>') . (($this->M_global->jumDataRow('barang_out_retur_header', ['invoice_jual' => $rd->invoice]) > 0) ? ' <span class="badge badge-warning">Tedapat Returan</span>' : '');
            $row[]  = date('d/m/Y', strtotime($rd->tgl_jual)) . ' ~ ' . date('H:i:s', strtotime($rd->jam_jual));
            $row[]  = $rd->kode_member . ' ~ ' . $this->M_global->getData('member', ['kode_member' => $rd->kode_member])->nama;
            $row[]  = $this->M_global->getData('m_gudang', ['kode_gudang' => $rd->kode_gudang])->nama;
            $row[]  = 'Rp. <span class="float-right">' . number_format($rd->total) . '</span>';
            $row[]  = $this->M_global->getData('user', ['kode_user' => $rd->kode_user])->nama . '<br><span class="badge badge-danger">Shift: ' . $rd->shift . '</span>';

            if ($rd->batal < 1) {
                $batal = '<button type="button" style="margin-bottom: 5px;" class="btn btn-secondary" title="Batalkan" onclick="actived(' . "'" . $rd->invoice . "', 1" . ')" ' . $confirm_diss . '><i class="fa-solid fa-ban"></i></button>';

                $ubah = '<button type="button" style="margin-bottom: 5px;" class="btn btn-warning" title="Ubah" onclick="ubah(' . "'" . $rd->invoice . "', '" . $rd->no_trx . "'" . ')" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>';
            } else {
                $batal = '<button type="button" style="margin-bottom: 5px;" class="btn btn-light" title="Re-Batalkan" onclick="actived(' . "'" . $rd->invoice . "', 0" . ')" ' . $confirm_diss . '><i class="fa-solid fa-arrow-rotate-left"></i></button>';

                $ubah = '<button type="button" style="margin-bottom: 5px;" class="btn btn-warning" title="Ubah" disabled><i class="fa-regular fa-pen-to-square"></i></button>';
            }

            $row[]  = '<div class="text-center">
                ' . $ubah . '
                <button type="button" style="margin-bottom: 5px;" class="btn btn-danger" title="Hapus" onclick="hapus(' . "'" . $rd->invoice . "'" . ')" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
                <br>
                ' . $batal . '
                <button type="button" style="margin-bottom: 5px;" class="btn btn-dark" title="Cetak" onclick="cetak(' . "'" . $rd->invoice . "', 0" . ')"><i class="fa-solid fa-print"></i></button>
                <button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="Kirim Email" onclick="email(' . "'" . $rd->invoice . "', 0" . ')"><i class="fa-solid fa-envelope-open-text"></i></button>
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

    // fungsi cetak barang out
    public function print_barang_out($invoice)
    {
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        $barang_out_header    = $this->M_global->getData('barang_out_header', ['invoice' => $invoice]);
        $barang_out_detail    = $this->M_global->getDataResult('barang_out_detail', ['invoice' => $invoice]);
        $member               = $this->M_global->getData('member', ['kode_member' => $barang_out_header->kode_member]);

        $judul                = 'Pendaftaran ' . $invoice;
        $filename             = $judul;

        if ($barang_out_header->status_jual == 1) {
            $open   = '<input type="checkbox" style="width: 80px;" checked="checked"> Terbayar';
            $close  = '<input type="checkbox" style="width: 80px;"> Belum Bayar';
        } else {
            $open   = '<input type="checkbox" style="width: 80px;"> Terbayar';
            $close  = '<input type="checkbox" style="width: 80px;" checked="checked"> Belum Bayar';
        }

        $body .= '<table style="width: 100%; font-size: 12px;" cellpadding="2px">';

        $body .= '<tr>
            <td style="width: 13%;">No Trx</td>
            <td style="width: 2%;">:</td>
            <td style="width: 35%;">' . $invoice . '</td>
            <td style="width: 13%;">No RM</td>
            <td style="width: 2%;">:</td>
            <td style="width: 35%;">' . $member->kode_member . '</td>
        </tr>
        <tr>
            <td style="width: 13%;">Poli</td>
            <td style="width: 2%;">:</td>
            <td style="width: 35%;">' . $this->M_global->getData('m_poli', ['kode_poli' => $barang_out_header->kode_poli])->keterangan . '</td>
            <td style="width: 13%;">Member</td>
            <td style="width: 2%;">:</td>
            <td style="width: 35%;">' . $member->kode_member . ' ~ ' . $member->nama . '</td>
        </tr>
        <tr>
            <td style="width: 13%;">Dokter</td>
            <td style="width: 2%;">:</td>
            <td style="width: 35%;">' . (($barang_out_header->kode_dokter == null || $barang_out_header->kode_dokter == '') ? '' : $this->M_global->getData('dokter', ['kode_dokter' => $barang_out_header->kode_dokter])->nama) . '</td>
            <td style="width: 13%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td style="width: 35%;">' . $member->nama . '</td>
        </tr>
        <tr>
            <td style="width: 13%;">Gudang</td>
            <td style="width: 2%;">:</td>
            <td style="width: 35%;">' . $this->M_global->getData('m_gudang', ['kode_gudang' => $barang_out_header->kode_gudang])->keterangan . '</td>
            <td style="width: 13%;">Umur</td>
            <td style="width: 2%;">:</td>
            <td style="width: 35%;">' . hitung_umur($member->tgl_lahir) . '</td>
        </tr>
        <tr>
            <td style="width: 13%;">Tgl/Jam Order</td>
            <td style="width: 2%;">:</td>
            <td style="width: 35%;">' . date('d/m/Y', strtotime($barang_out_header->tgl_jual)) . ' ~ ' . date('H:i:s', strtotime($barang_out_header->jam_jual)) . '</td>
            <td style="width: 13%;">Status</td>
            <td style="width: 2%;">:</td>
            <td style="width: 35%;">' . $open . '&nbsp;&nbsp;' . $close . '</td>
        </tr>
        <tr>
            <td style="width: 100%;" colspan="3">&nbsp;</td>
        </tr>';
        $body .= '</table>';

        $body .= '<table style="width: 100%; font-size: 10px;" autosize="1" cellpadding="5px">';

        $body .= '<thead>
            <tr>
                <th style="width: 5%; border: 1px solid black; background-color: red; color: white;">#</th>
                <th style="width: 20%; border: 1px solid black; background-color: red; color: white;">Barang</th>
                <th style="width: 15%; border: 1px solid black; background-color: red; color: white;">Harga</th>
                <th style="width: 15%; border: 1px solid black; background-color: red; color: white;">Jumlah</th>
                <th style="width: 15%; border: 1px solid black; background-color: red; color: white;">Diskon</th>
                <th style="width: 15%; border: 1px solid black; background-color: red; color: white;">Pajak</th>
                <th style="width: 15%; border: 1px solid black; background-color: red; color: white;">Total</th>
            </tr>
        </thead>';

        $body .= '<tbody>';

        $no = 1;
        foreach ($barang_out_detail as $bod) {
            $barang = $this->M_global->getData('barang', ['kode_barang' => $bod->kode_barang]);

            $body .= '<tr>
                <td style="border: 1px solid black;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $barang->kode_barang . ' ~ ' . $barang->nama . '</td>
                <td style="border: 1px solid black; text-align: right;">Rp. ' . number_format($bod->harga) . '</td>
                <td style="border: 1px solid black; text-align: right;">' . number_format($bod->qty) . '</td>
                <td style="border: 1px solid black; text-align: right;">Rp. ' . number_format($bod->discrp) . '</td>
                <td style="border: 1px solid black; text-align: right;">Rp. ' . number_format($bod->pajakrp) . '</td>
                <td style="border: 1px solid black; text-align: right;">Rp. ' . number_format($bod->jumlah) . '</td>
            </tr>';

            $no++;
        }

        $body .= '</tbody>';

        $body .= '<tfoot>';

        $body .= '<tr>
            <th colspan="6" style="text-align: right;">Subtotal: Rp. </th>
            <th style="text-align: right;">' . number_format($barang_out_header->subtotal) . '</th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: right;">Diskon: Rp. </th>
            <th style="text-align: right;">' . number_format($barang_out_header->diskon) . '</th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: right;">Pajak: Rp. </th>
            <th style="text-align: right;">' . number_format($barang_out_header->pajak) . '</th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: right;">Total: Rp. </th>
            <th style="text-align: right;">' . number_format($barang_out_header->total) . '</th>
        </tr>';

        $body .= '</tfoot>';

        $body .= '</table>';

        cetak_pdf($judul, $body, 1, $position, $filename, $web_setting);
    }

    // fungsi acc/re-acc
    public function accbarang_out_retur($invoice, $acc)
    {
        $kode_cabang    = $this->session->userdata('cabang');
        // header barang by invoice
        $header         = $this->M_global->getData('barang_out_retur_header', ['invoice' => $invoice]);
        // kode_gudang
        $kode_gudang    = $header->kode_gudang;

        // detail barang
        $detail         = $this->M_global->getDataResult('barang_out_retur_detail', ['invoice' => $invoice]);

        if ($acc == 0) { // jika acc = 0
            aktifitas_user_transaksi('Transaksi Keluar', 'Reject Retur Penjualan', $invoice);

            // update is_valid jadi 0
            $cek = [
                $this->M_global->updateData('barang_out_retur_header', ['is_valid' => 0, 'tgl_valid' => null, 'jam_valid' => null], ['invoice' => $invoice]),
            ];

            hitungStokReturJualIn($detail, $kode_gudang, $invoice);
        } else { // selain itu
            aktifitas_user_transaksi('Transaksi Keluar', 'Confirm Retur Penjualan', $invoice);

            // update is_valid jadi 1
            $cek = [
                $this->M_global->updateData('barang_out_retur_header', ['is_valid' => 1, 'tgl_valid' => date('Y-m-d'), 'jam_valid' => date('H:i:s')], ['invoice' => $invoice]),
            ];

            hitungStokReturJualOut($detail, $kode_gudang, $invoice);
        }

        if ($cek) { // jika fungsi cek berjalan
            // kirim status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirim status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // getBarangEmr
    public function getBarangEmr($no_trx)
    {
        $barang = $this->db->query('SELECT epb.*, b.nama AS barang, b.harga_jual, (SELECT keterangan FROM m_satuan WHERE kode_satuan = epb.kode_satuan) AS satuan FROM emr_per_barang epb JOIN barang b ON epb.kode_barang = b.kode_barang WHERE epb.no_trx = "' . $no_trx . '"')->result();

        echo json_encode($barang);
    }

    // form barang_out page
    public function form_barang_out($param, $no_trx = '')
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param == 'emr') {
            $emr_per_barang     = $this->M_global->getDataResult('emr_per_barang', ['no_trx' => $no_trx]);
            $barang_out     = null;
            $barang_detail  = null;
        } else {
            $emr_per_barang     = null;
            if ($param != '0') {
                $barang_out     = $this->M_global->getData('barang_out_header', ['invoice' => $param]);
                $barang_detail  = $this->M_global->getDataResult('barang_out_detail', ['invoice' => $param]);
            } else {
                $barang_out     = null;
                $barang_detail  = null;
            }
        }

        $cabang = $this->session->userdata('kode_cabang');

        $parameter = [
            $this->data,
            'judul'             => 'Transaksi',
            'nama_apps'         => $web_setting->nama,
            'page'              => 'Penjualan',
            'web'               => $web_setting,
            'web_version'       => $web_version->version,
            'list_data'         => '',
            'param'             => $param,
            'no_trx'            => $no_trx,
            'emr_per_barang'    => $emr_per_barang,
            'data_barang_out'   => $barang_out,
            'barang_detail'     => $barang_detail,
            'role'              => $this->M_global->getResult('m_role'),
            'pajak'             => $this->M_global->getData('m_pajak', ['aktif' => 1])->persentase,
            'list_barang'       => $this->db->query("SELECT b.*, bs.akhir AS stok FROM barang_stok bs JOIN barang b USING (kode_barang) WHERE bs.kode_cabang = '$cabang' AND bs.akhir > 0")->result(),
        ];

        $this->template->load('Template/Content', 'Jual/Form_barang_out', $parameter);
    }

    // fungsi barang stok by gudang
    public function getBarangGudang($key_barang, $kode_gudang)
    {
        $cabang = $this->session->userdata('cabang');
        $barang = $this->M_global->getDataLike('barang', 'nama', 'kode_barang', $key_barang);

        if ($barang) {
            $cek_cabang = $this->db->query("SELECT bs.kode_barang, bs.akhir, b.nama, b.harga_jual FROM barang_stok bs JOIN barang b ON bs.kode_barang = b.kode_barang WHERE bs.kode_cabang = '$cabang' AND bs.kode_gudang = '$kode_gudang' AND (bs.kode_barang LIKE '%$key_barang%' OR b.nama LIKE '%$key_barang%')")->row();

            if ($cek_cabang) {
                $barang = $barang;
                $satuan = [];
                foreach ([$barang->kode_satuan, $barang->kode_satuan2, $barang->kode_satuan3] as $satuanCode) {
                    $satuanDetail = $this->M_global->getData('m_satuan', ['kode_satuan' => $satuanCode]);
                    if ($satuanDetail) {
                        $satuan[] = [
                            'kode_satuan' => $satuanCode,
                            'keterangan' => $satuanDetail->keterangan,
                        ];
                    }
                }
            } else {
                $barang = '';
                $satuan = '';
            }
        } else {
            $barang = '';
            $satuan = '';
        }


        if ($barang) {
            echo json_encode([$barang, $satuan]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi info pendaftaran
    public function getInfoPendaftaran($no_trx)
    {
        $pendaftaran = $this->db->query("SELECT p.*, m.nama AS nama_member, d.nama AS nama_dokter FROM pendaftaran p JOIN member m ON p.kode_member = m.kode_member JOIN dokter d ON p.kode_dokter = d.kode_dokter WHERE p.no_trx = '$no_trx'")->row();

        if ($pendaftaran) {
            echo json_encode($pendaftaran);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi info alamat
    public function getAddressMember($kode_member)
    {
        $member = $this->M_global->getData('member', ['kode_member' => $kode_member]);

        if ($member) {
            $prov       = $this->M_global->getData('m_provinsi', ['kode_provinsi' => $member->provinsi])->provinsi;
            $kab        = $this->M_global->getData('kabupaten', ['kode_kabupaten' => $member->kabupaten])->kabupaten;
            $kec        = $this->M_global->getData('kecamatan', ['kode_kecamatan' => $member->kecamatan])->kecamatan;

            $address    = 'Prov.' . $prov . ', Kab.' . $kab . ', Kec.' . $kec . ', Ds.' . $member->desa . ', (POS: ' . $member->kodepos . '), RT.' . $member->rt . '/RW.' . $member->rw;

            echo json_encode(['alamat' => $address]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi cek member terdaftar
    public function cekMember($kode_member)
    {
        $member = $this->M_global->getData('member', ['kode_member' => $kode_member, 'status_regist' => 1]);

        if ($member) {
            if ($member->status_regist == 1) {
                echo json_encode(['status' => 1, 'no_trx' => $member->last_regist]);
            } else {
                echo json_encode(['status' => 0]);
            }
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi cek notrx exist or not in penjualan
    public function cekJual($no_trx)
    {
        $jual = $this->M_global->jumDataRow('barang_out_header', ['no_trx' => $no_trx]);

        if ($jual < 1) { // jika jual exist/ lebih dari 1
            // kirimkan status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi insert/update proses barang_out
    public function barang_out_proses($param)
    {
        $kode_cabang      = $this->session->userdata('cabang');

        // header
        if ($param == 1) { // jika param = 1
            $invoice = _invoiceJual($kode_cabang);
        } else {
            $invoice = $this->input->post('invoice');
        }

        $tgl_jual           = $this->input->post('tgl_jual');
        $jam_jual           = $this->input->post('jam_jual');
        $kode_pendaftaran   = $this->input->post('kode_pendaftaran');
        $kode_dokter        = $this->input->post('kode_dokter');
        $kode_poli          = $this->input->post('kode_poli');
        $kode_member        = $this->input->post('kode_member');
        $alamat             = $this->input->post('alamat');
        $kode_gudang        = $this->input->post('kode_gudang');

        $subtotal           = str_replace(',', '', $this->input->post('subtotal'));
        $diskon             = str_replace(',', '', $this->input->post('diskon'));
        $pajak              = str_replace(',', '', $this->input->post('pajak'));
        $total              = str_replace(',', '', $this->input->post('total'));

        // detail
        $kode_barang_out    = $this->input->post('kode_barang_out');
        $kode_satuan_out    = $this->input->post('kode_satuan');
        $harga_out          = $this->input->post('harga_out');
        $qty_out            = $this->input->post('qty_out');
        $discpr_out         = $this->input->post('discpr_out');
        $discrp_out         = $this->input->post('discrp_out');
        $pajakrp_out        = $this->input->post('pajakrp_out');
        $jumlah_out         = $this->input->post('jumlah_out');
        $signa_out          = $this->input->post('signa_out');

        // cek jumlah detail barang_out
        if (isset($kode_barang_out)) {

            $jum              = count($kode_barang_out);

            // tampung isi header
            $isi_header = [
                'kode_cabang'       => $kode_cabang,
                'invoice'           => $invoice,
                'no_trx'            => $kode_pendaftaran,
                'kode_member'       => $kode_member,
                'alamat'            => $alamat,
                'kode_dokter'       => $kode_dokter,
                'kode_poli'         => $kode_poli,
                'tgl_jual'          => $tgl_jual,
                'jam_jual'          => $jam_jual,
                'status_jual'       => 0,
                'kode_gudang'       => $kode_gudang,
                'pajak'             => $pajak,
                'diskon'            => $diskon,
                'subtotal'          => $subtotal,
                'total'             => $total,
                'kode_user'         => $this->session->userdata('kode_user'),
                'shift'             => $this->session->userdata('shift'),
                'batal'             => 0,
            ];

            if ($param == 2) { // jika param = 2
                aktifitas_user_transaksi('Transaksi Keluar', 'mengubah Penjualan Barang', $invoice);

                // jalankan fungsi cek
                $cek = [
                    $this->M_global->updateData('barang_out_header', $isi_header, ['invoice' => $invoice]), // update header
                    $this->M_global->delData('barang_out_detail', ['invoice' => $invoice]), // delete detail
                ];
            } else { // selain itu
                aktifitas_user_transaksi('Transaksi Keluar', 'menjual Barang', $invoice);

                // jalankan fungsi cek
                $cek = [
                    $this->M_global->insertData('barang_out_header', $isi_header),
                ]; // insert header
            }

            if ($cek) { // jika fungsi cek berjalan
                // lakukan loop
                for ($x = 0; $x <= ($jum - 1); $x++) {
                    $kode_barang    = $kode_barang_out[$x];
                    $kode_satuan    = $kode_satuan_out[$x];
                    $harga          = str_replace(',', '', $harga_out[$x]);
                    $qty            = str_replace(',', '', $qty_out[$x]);
                    $discpr         = str_replace(',', '', $discpr_out[$x]);
                    $discrp         = str_replace(',', '', $discrp_out[$x]);
                    $pajakrp        = str_replace(',', '', $pajakrp_out[$x]);
                    $jumlah         = str_replace(',', '', $jumlah_out[$x]);
                    $signa          = $signa_out[$x];

                    $barang1 = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan' => $kode_satuan]);
                    $barang2 = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan2' => $kode_satuan]);
                    $barang3 = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan3' => $kode_satuan]);

                    if ($barang1) {
                        $qty_satuan = 1;
                    } else if ($barang2) {
                        $qty_satuan = $barang2->qty_satuan2;
                    } else {
                        $qty_satuan = $barang3->qty_satuan3;
                    }

                    $qty_konversi   = $qty * $qty_satuan;

                    // tamping isi detail
                    $isi_detail = [
                        'invoice'       => $invoice,
                        'kode_barang'   => $kode_barang,
                        'harga'         => $harga,
                        'kode_satuan'   => $kode_satuan,
                        'qty_konversi'  => $qty_konversi,
                        'qty'           => $qty,
                        'discpr'        => $discpr,
                        'discrp'        => $discrp,
                        'pajak'         => (($pajakrp > 0) ? 1 : 0),
                        'pajakrp'       => $pajakrp,
                        'jumlah'        => $jumlah,
                        'signa'         => $signa,
                    ];

                    // insert detail
                    $this->M_global->insertData('barang_out_detail', $isi_detail);

                    $barang_stok = $this->M_global->getData('barang_stok', ['kode_barang' => $kode_barang, 'kode_gudang' => $kode_gudang, 'kode_cabang' => $kode_cabang])->akhir;
                    $hpp = $this->M_global->getData('barang', ['kode_barang' => $kode_barang])->hpp;
                    $new_persediaan = $barang_stok * $hpp;

                    $this->M_global->updateData('barang', ['nilai_persediaan' => $new_persediaan], ['kode_barang' => $kode_barang]);
                }

                $detail = $this->M_global->getDataResult('barang_out_detail', ['invoice' => $invoice]);
                hitungStokJualIn($detail, $kode_gudang, $invoice);

                $this->single_print_bout($invoice, 1);

                // beri nilai status = 1 kirim ke view
                echo json_encode(['status' => 1]);
            } else { // selain itu
                // beri nilai status = 0 kirim ke view
                echo json_encode(['status' => 0]);
            }
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // cekQty
    public function cekQty()
    {
        $kode_barang = $this->input->get('kode_barang');
        $kode_satuan = $this->input->get('kode_satuan');
        $qty = $this->input->get('qty');
        $kode_gudang = $this->input->get('kode_gudang');
        $kode_cabang = $this->session->userdata('kode_cabang');

        $barang1 = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan' => $kode_satuan]);
        $barang2 = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan2' => $kode_satuan]);
        $barang3 = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan3' => $kode_satuan]);

        if ($barang1) {
            $qty_satuan = 1;
        } else if ($barang2) {
            $qty_satuan = $barang2->qty_satuan2;
        } else {
            $qty_satuan = $barang3->qty_satuan3;
        }

        $qty_konversi   = $qty * $qty_satuan;

        $stok = $this->M_global->getData('barang_stok', ['kode_barang' => $kode_barang, 'kode_gudang' => $kode_gudang, 'kode_cabang' => $kode_cabang]);

        if ($stok) {
            $hasil = $stok->akhir;
        } else {
            $hasil = 0;
        }

        echo json_encode(["konversi" => $qty_konversi, "stok" => $hasil]);
    }

    // fungsi print single barang_out
    public function single_print_bout($invoice, $yes)
    {
        $param          = 1;

        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $pencetak       = $this->M_global->getData('user', ['kode_user' => $this->session->userdata('kode_user')])->nama;

        $breaktable     = '<br>';
        $file = 'Penjualan';

        // isi body
        $header = $this->M_global->getData('barang_out_header', ['invoice' => $invoice]);

        // body header
        $body .= '<table style="width: 100%; font-size: 11px;">
            <tr>
                <td style="width: 15%;">Perihal</td>
                <td style="width: 2%;"> : </td>
                <td style="width: 33%;">' . $file . '</td>
                <td style="width: 50%; text-align: right; font-weight: bold; color: white;"><span style="border: 1px solid #0e1d2e; background-color: #0e1d2e;">' . $invoice . '</span></td>
            </tr>
            <tr>
                <td style="width: 15%;">Tgl/Jam Jual</td>
                <td style="width: 2%;"> : </td>
                <td colspan="2">' . date('d-m-Y', strtotime($header->tgl_jual)) . ' / ' . date('H:i:s', strtotime($header->jam_jual)) . '</td>
            </tr>
            <tr>
                <td style="width: 15%;">Pembeli</td>
                <td style="width: 2%;"> : </td>
                <td colspan="2">' . $this->M_global->getData('member', ['kode_member' => $header->kode_member])->nama . '</td>
            </tr>
            <tr>
                <td style="width: 15%;">Gudang</td>
                <td style="width: 2%;"> : </td>
                <td style="width: 33%;">' . $this->M_global->getData('m_gudang', ['kode_gudang' => $header->kode_gudang])->nama . '</td>
                <td style="width: 50%; text-align: right;">Pencetak : ' . $pencetak . '</td>
            </tr>
        </table>';

        $body .= $breaktable;

        $body .= '<table style="width: 100%; font-size: 10px;" autosize="1" cellpadding="5px">';

        $body .= '<thead>
            <tr>
                <th rowspan="2" style="width: 5%; border: 1px solid black; background-color: #0e1d2e; color: white;">#</th>
                <th rowspan="2" style="width: 30%; border: 1px solid black; background-color: #0e1d2e; color: white;">Barang</th>
                <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Satuan</th>
                <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Harga</th>
                <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Jumlah</th>
                <th colspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Diskon</th>
                <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Pajak</th>
                <th rowspan="2" style="width: 15%; border: 1px solid black; background-color: #0e1d2e; color: white;">Total</th>
            </tr>
            <tr>
                <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">%</th>
                <th style="width: 10%; border: 1px solid black; background-color: #0e1d2e; color: white;">Rp</th>
            </tr>
        </thead>';

        $body .= '<tbody>';

        if ($param == 1) {
            $total = number_format($header->total);
        } else {
            $total = ceil($header->total);
        }
        $body .= '<tr style="background-color: skyblue;">
            <td colspan="7" style="border: 1px solid black; font-weight: bold;">No. Transaksi: ' . $header->invoice . '</td>
            <td colspan="2" style="border: 1px solid black; font-weight: bold; text-align: right">' . $total . '</td>
        </tr>';

        // detail barang
        $detail   = $this->M_global->getDataResult('barang_out_detail', ['invoice' => $header->invoice]);

        $no       = 1;
        $tdiskon  = 0;
        $tpajak   = 0;
        $ttotal   = 0;
        foreach ($detail as $d) {
            $tdiskon    += $d->discrp;
            $tpajak     += $d->pajakrp;
            $ttotal     += $d->jumlah;

            if ($param == 1) {
                $harga    = number_format($d->harga);
                $qty      = number_format($d->qty);
                $discpr   = number_format($d->discpr);
                $discrp   = number_format($d->discrp);
                $pajak    = number_format($d->pajakrp);
                $jumlah   = number_format($d->jumlah);

                $tdiskonx = number_format($tdiskon);
                $tpajakx  = number_format($tpajak);
                $ttotalx  = number_format($ttotal);
            } else {
                $harga    = ceil($d->harga);
                $qty      = ceil($d->qty);
                $discpr   = ceil($d->discpr);
                $discrp   = ceil($d->discrp);
                $pajak    = ceil($d->pajakrp);
                $jumlah   = ceil($d->jumlah);

                $tdiskonx = ceil($tdiskon);
                $tpajakx  = ceil($tpajak);
                $ttotalx  = ceil($ttotal);
            }
            $body .= '<tr>
                <td style="border: 1px solid black;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $d->kode_barang . ' ~ ' . $this->M_global->getData('barang', ['kode_barang' => $d->kode_barang])->nama . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $this->M_global->getData('m_satuan', ['kode_satuan' => $d->kode_satuan])->keterangan . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $harga . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $qty . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $discpr . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $discrp . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $pajak . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $jumlah . '</td>
            </tr>';
            $no++;
        }
        $body .= '<tr style="background-color: green;">
            <td colspan="6" style="border: 1px solid black; font-weight: bold; color: white;">Total</td>
            <td style="border: 1px solid black; font-weight: bold; color: white; text-align: right">' . $tdiskonx . '</td>
            <td style="border: 1px solid black; font-weight: bold; color: white; text-align: right">' . $tpajakx . '</td>
            <td style="border: 1px solid black; font-weight: bold; color: white; text-align: right">' . $ttotalx . '</td>
        </tr>';

        $body .= '</tbody>';

        $body .= '<tfoot>
            <tr>
                <td colspan="6">&nbsp;</td>
                <td colspan="3" style="text-align: center;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" style="width:60%;">&nbsp;</td>
                <td colspan="3" style="width:40%; text-align: center;">Yogyakarta, ' . date('d M Y') . '</td>
            </tr>
            <tr>
                <td colspan="6" style="width:60%;">&nbsp;</td>
                <td colspan="3" style="width:40%; text-align: center;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" style="width:60%;">&nbsp;</td>
                <td colspan="3" style="width:40%; text-align: center;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" style="width:60%;">&nbsp;</td>
                <td colspan="3" style="width:40%; text-align: center;">' . $pencetak . '</td>
            </tr>
        </tfoot>';

        $body .= '</table>';

        $judul = 'Penjualan ~ ' . $invoice;
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting, $yes);
    }

    // fungsi kirim email barang out
    public function email_out($invoice)
    {
        $email = $this->input->get('email');

        $header = $this->M_global->getData('barang_out_header', ['invoice' => $invoice]);

        $judul = 'Penjualan ~ ' . $invoice;

        // $attched_file    = base_url() . 'assets/file/pdf/' . $judul . '.pdf';ahmad.ummgl@gmail.com
        $attched_file    = $_SERVER["DOCUMENT_ROOT"] . '/first_apps/assets/file/pdf/' . $judul . '.pdf';

        $ready_message   = "";
        $ready_message   .= "<table border=0>
            <tr>
                <td style='width: 30%;'>Invoice</td>
                <td style='width: 10%;'> : </td>
                <td style='width: 60%;'> $invoice </td>
            </tr>
            <tr>
                <td style='width: 30%;'>Tgl/Jam</td>
                <td style='width: 10%;'> : </td>
                <td style='width: 60%;'>" . date('d-m-Y', strtotime($header->tgl_jual)) . " / " . date('H:i:s', strtotime($header->jam_jual)) . "</td>
            </tr>
            <tr>
                <td style='width: 30%;'>Pembeli</td>
                <td style='width: 10%;'> : </td>
                <td style='width: 60%;'>" . $this->M_global->getData('member', ['kode_member' => $header->kode_member])->nama . "</td>
            </tr>
            <tr>
                <td style='width: 30%;'>Gudang</td>
                <td style='width: 10%;'> : </td>
                <td style='width: 60%;'>" . $this->M_global->getData('m_gudang', ['kode_gudang' => $header->kode_gudang])->nama . "</td>
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

    // fungsi hapus barang out
    public function delBeliOut($invoice)
    {
        // jalankan fungsi cek
        $header         = $this->M_global->getData('barang_out_header', ['invoice' => $invoice]);

        $kode_gudang    = $header->kode_gudang;

        $detail         = $this->M_global->getDataResult('barang_out_detail', ['invoice' => $invoice]);
        hitungStokJualOut($detail, $kode_gudang, $invoice);

        $cek = [
            $this->M_global->delData('barang_out_detail', ['invoice' => $invoice]), // del data detail penjualan
            $this->M_global->delData('barang_out_header', ['invoice' => $invoice]), // del data header penjualan
        ];

        if ($cek) { // jika fungsi cek berjalan
            // kirim status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirim status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi batal/re-batal
    public function activedbarang_out($invoice, $batal)
    {
        $user_batal = $this->session->userdata('kode_user');

        if ($batal == 0) { // jika batal = 0
            // update batal jadi 0
            $cek = $this->M_global->updateData('barang_out_header', ['batal' => 0, 'tgl_batal' => null, 'jam_batal' => null, 'user_batal' => null], ['invoice' => $invoice]);
        } else { // selain itu
            // update batal jadi 1
            $cek = $this->M_global->updateData('barang_out_header', ['batal' => 1, 'tgl_batal' => date('Y-m-d'), 'jam_batal' => date('H:i:s'), 'user_batal' => $user_batal], ['invoice' => $invoice]);
            $header         = $this->M_global->getData('barang_out_header', ['invoice' => $invoice]);

            $kode_gudang    = $header->kode_gudang;

            $detail         = $this->M_global->getDataResult('barang_out_detail', ['invoice' => $invoice]);
            hitungStokJualOut($detail, $kode_gudang, $invoice);
        }

        if ($cek) { // jika fungsi cek berjalan
            // kirim status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirim status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    /*
    * Penjualan
    **/

    // barang_out_retur page
    public function barang_out_retur()
    {
        // website config
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version    = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter      = [
            $this->data,
            'judul'         => 'Transaksi',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Retur Penjualan',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Transaksi/barang_out_retur_list/',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Jual/Retur', $parameter);
    }

    // fungsi list barang_out_retur
    public function barang_out_retur_list($param1 = 1, $param2 = '')
    {
        // parameter untuk list table
        $table                      = 'barang_out_retur_header';
        $colum                      = ['id', 'invoice', 'invoice_jual', 'tgl_retur', 'jam_retur', 'kode_gudang', 'pajak', 'diskon', 'total', 'kode_user', 'batal', 'tgl_batal', 'jam_batal', 'user_batal', 'is_valid', 'tgl_valid', 'jam_valid', 'kode_member', 'shift'];
        $order                      = 'id';
        $order2                     = 'desc';
        $order_arr                  = ['id' => 'desc'];
        $kondisi_param2             = 'kode_gudang';
        $kondisi_param1             = 'tgl_retur';

        // kondisi role
        $updated                    = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted                    = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;
        $confirmed                  = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->confirmed;

        // table server side tampung kedalam variable $list
        $dat                        = explode("~", $param1);

        if ($dat[0] == 1) {
            $bulan                  = date('m');
            $tahun                  = date('Y');
            $type                   = 1;
        } else {
            $bulan                  = date('Y-m-d', strtotime($dat[1]));
            $tahun                  = date('Y-m-d', strtotime($dat[2]));
            $type                   = 2;
        }

        $list                       = $this->M_datatables2->get_datatables($table, $colum, $order_arr, $order, $order2, $kondisi_param1, $type, $bulan, $tahun, $param2, $kondisi_param2);

        $data                       = [];
        $no                         = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                if ($rd->batal > 0) {
                    $upd_diss       = 'disabled';
                } else {
                    if ($rd->is_valid > 0) {
                        $upd_diss   = 'disabled';
                    } else {
                        $upd_diss   = _lock_button();
                    }
                }
            } else {
                $upd_diss           = 'disabled';
            }

            if ($deleted > 0) {
                if ($rd->batal > 0) {
                    $del_diss       = 'disabled';
                } else {
                    if ($rd->is_valid > 0) {
                        $del_diss   = 'disabled';
                    } else {
                        $del_diss   = _lock_button();
                    }
                }
            } else {
                $del_diss           = 'disabled';
            }

            if ($confirmed > 0) {
                $confirm_diss       = _lock_button();
            } else {
                $confirm_diss       = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->invoice . '<br>' . (($rd->batal == 0) ? (($rd->is_valid > 0) ? '<span class="badge badge-primary">ACC</span>' : '<span class="badge badge-success">Buka</span>') : '<span class="badge badge-danger">Batal</span>');
            $row[]  = date('d/m/Y', strtotime($rd->tgl_retur)) . ' ~ ' . date('H:i:s', strtotime($rd->jam_retur));
            $row[]  = $this->M_global->getData('member', ['kode_member' => $rd->kode_member])->nama;
            $row[]  = $this->M_global->getData('m_gudang', ['kode_gudang' => $rd->kode_gudang])->nama;
            $row[]  = 'Rp. <span class="float-right">' . number_format($rd->total) . '</span>';
            $row[]  = $this->M_global->getData('user', ['kode_user' => $rd->kode_user])->nama . '<br><span class="badge badge-danger">Shift: ' . $rd->shift . '</span>';

            if ($rd->is_valid < 1) {
                if ($rd->batal < 1) {
                    $batal = '<button type="button" style="margin-bottom: 5px;" class="btn btn-secondary" title="Batalkan" onclick="actived(' . "'" . $rd->invoice . "', 1" . ')" ' . $confirm_diss . '><i class="fa-solid fa-ban"></i></button>';

                    $ubah = '<button type="button" style="margin-bottom: 5px;" class="btn btn-warning" title="Ubah" onclick="ubah(' . "'" . $rd->invoice . "'" . ')" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>';

                    $accept = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="ACC" onclick="valided(' . "'" . $rd->invoice . "', 1" . ')" ' . $confirm_diss . '><i class="fa-regular fa-circle-check"></i></button>';

                    $email = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="Kirim Email" disabled><i class="fa-solid fa-envelope-open-text"></i></button>';
                } else {
                    $batal = '<button type="button" style="margin-bottom: 5px;" class="btn btn-light" title="Re-Batalkan" onclick="actived(' . "'" . $rd->invoice . "', 0" . ')" ' . $confirm_diss . '><i class="fa-solid fa-arrow-rotate-left"></i></button>';

                    $ubah = '<button type="button" style="margin-bottom: 5px;" class="btn btn-warning" title="Ubah" disabled><i class="fa-regular fa-pen-to-square"></i></button>';

                    $accept = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="ACC" disabled><i class="fa-regular fa-circle-check"></i></button>';

                    $email = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="Kirim Email" disabled><i class="fa-solid fa-envelope-open-text"></i></button>';
                }
            } else {
                $accept = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="Re-ACC" onclick="valided(' . "'" . $rd->invoice . "', 0" . ')" ' . $confirm_diss . '><i class="fa-solid fa-check-to-slot"></i></button>';

                $ubah = '<button type="button" style="margin-bottom: 5px;" class="btn btn-warning" title="Ubah" disabled><i class="fa-regular fa-pen-to-square"></i></button>';

                $batal = '<button type="button" style="margin-bottom: 5px;" class="btn btn-secondary" title="Batalkan" disabled><i class="fa-solid fa-ban"></i></button>';

                $email = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="Kirim Email" onclick="email(' . "'" . $rd->invoice . "', 0" . ')"><i class="fa-solid fa-envelope-open-text"></i></button>';
            }

            $row[]  = '<div class="text-center">
                ' . $accept . '
                ' . $ubah . '
                <button type="button" style="margin-bottom: 5px;" class="btn btn-danger" title="Hapus" onclick="hapus(' . "'" . $rd->invoice . "'" . ')" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
                <br>
                ' . $batal . '
                <button type="button" style="margin-bottom: 5px;" class="btn btn-dark" title="Cetak" onclick="cetak(' . "'" . $rd->invoice . "', 0" . ')"><i class="fa-solid fa-print"></i></button>
                ' . $email . '
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

    // form barang_out_retur page
    public function form_barang_out_retur($param)
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param != '0') {
            $barang_out_retur   = $this->M_global->getData('barang_out_retur_header', ['invoice' => $param]);
            $barang_detail      = $this->M_global->getDataResult('barang_out_retur_detail', ['invoice' => $param]);
        } else {
            $barang_out_retur   = null;
            $barang_detail      = null;
        }

        $parameter = [
            $this->data,
            'judul'                     => 'Transaksi',
            'nama_apps'                 => $web_setting->nama,
            'page'                      => 'Retur Penjualan',
            'web'                       => $web_setting,
            'web_version'               => $web_version->version,
            'list_data'                 => '',
            'data_barang_out_retur'     => $barang_out_retur,
            'barang_detail'             => $barang_detail,
            'role'                      => $this->M_global->getResult('m_role'),
            'pajak'                     => $this->M_global->getData('m_pajak', ['aktif' => 1])->persentase,
            'list_barang'               => $this->M_global->getResult('barang'),
        ];

        $this->template->load('Template/Content', 'Jual/Form_barang_out_retur', $parameter);
    }

    // fungsi ambil data penjualan
    public function getBarangOut($invoice)
    {
        $header = $this->db->query('SELECT bpo.*, (SELECT nama FROM member WHERE kode_member = bpo.kode_member) AS nama_member, (SELECT nama FROM m_gudang WHERE kode_gudang = bpo.kode_gudang) AS nama_gudang FROM barang_out_header bpo WHERE bpo.invoice = "' . $invoice . '"')->row();

        if ($header) {
            $detail = $this->db->query('SELECT hpo.invoice, dpo.kode_barang, dpo.kode_satuan AS satuan_default,
            dpo.qty - dpo.qty_retur AS qty_po, b.nama, b.kode_satuan, b.kode_satuan2, b.kode_satuan3,
            dpo.harga, dpo.discpr, dpo.discrp, dpo.pajak, dpo.pajakrp, dpo.jumlah, hpo.kode_member
            FROM barang_out_detail dpo
            JOIN barang_out_header hpo ON hpo.invoice = dpo.invoice
            JOIN barang b ON b.kode_barang = dpo.kode_barang
            WHERE dpo.qty_retur != dpo.qty AND hpo.invoice = "' . $invoice . '"')->result();

            foreach ($detail as $value) {
                $satuanDetail = $this->M_global->getData('m_satuan', ['kode_satuan' => $value->satuan_default]);
                if ($satuanDetail) {
                    $satuan = [
                        'kode_satuan' => $value->satuan_default,
                        'keterangan'  => $satuanDetail->keterangan,
                    ];
                } else {
                    $satuan = '';
                }
            }

            echo json_encode([['status' => 1, 'header' => $header], $detail, $satuan]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi cek qty penjualan untuk di retur
    public function getQtyJual($invoice, $kode_barang)
    {
        $cek = $this->db->query("SELECT d.* FROM barang_out_detail d WHERE d.invoice = '$invoice' AND d.kode_barang = '$kode_barang'")->row();

        if ($cek) { // jika cek ada
            // kirimkan data qty ke view
            echo json_encode(['qty' => $cek->qty]);
        } else { // selain itu
            // kirimkan status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi proses insert/update retur
    public function barang_out_retur_proses($param)
    {
        $kode_cabang = $this->session->userdata('cabang');

        // header
        if ($param == 1) { // jika param = 1
            $invoice        = _invoiceRetur($kode_cabang);
        } else { // selain itu
            $invoice        = $this->input->post('invoice');
        }

        $tgl_retur          = $this->input->post('tgl_retur');
        $jam_retur          = $this->input->post('jam_retur');
        $invoice_jual       = $this->input->post('invoice_jual');
        $kode_gudang        = $this->input->post('kode_gudang');
        $alasan             = $this->input->post('alasan');

        $subtotal           = str_replace(',', '', $this->input->post('subtotal'));
        $diskon             = str_replace(',', '', $this->input->post('diskon'));
        $pajak              = str_replace(',', '', $this->input->post('pajak'));
        $total              = str_replace(',', '', $this->input->post('total'));

        // detail
        $kode_barang_out    = $this->input->post('kode_barang_out');
        $kode_satuan        = $this->input->post('kode_satuan');
        $harga_out          = $this->input->post('harga_out');
        $qty_out            = $this->input->post('qty_out');
        $discpr_out         = $this->input->post('discpr_out');
        $discrp_out         = $this->input->post('discrp_out');
        $pajakrp_out        = $this->input->post('pajakrp_out');
        $jumlah_out         = $this->input->post('jumlah_out');

        // cek jumlah detail barang_out
        $jum                = count($kode_barang_out);

        $jual               = $this->M_global->getData('barang_out_header', ['invoice' => $invoice_jual]);

        // tampung isi header
        $isi_header = [
            'kode_cabang'   => $kode_cabang,
            'invoice'       => $invoice,
            'invoice_jual'  => $invoice_jual,
            'alasan'        => $alasan,
            'tgl_retur'     => $tgl_retur,
            'jam_retur'     => $jam_retur,
            'kode_member'   => $jual->kode_member,
            'kode_gudang'   => $kode_gudang,
            'pajak'         => $pajak,
            'diskon'        => $diskon,
            'subtotal'      => $subtotal,
            'total'         => $total,
            'kode_user'     => $this->session->userdata('kode_user'),
            'shift'         => $this->session->userdata('shift'),
            'batal'         => 0,
        ];

        if ($param == 2) { // jika param = 2
            aktifitas_user_transaksi('Transaksi Keluar', 'mengubah Retur Penjualan', $invoice);

            // jalankan fungsi cek
            $cek = [
                $this->M_global->updateData('barang_out_retur_header', $isi_header, ['invoice' => $invoice]), // update header
                $this->M_global->delData('barang_out_retur_detail', ['invoice' => $invoice]), // delete detail
            ];
        } else { // selain itu
            aktifitas_user_transaksi('Transaksi Keluar', 'menambahkan Retur Penjualan', $invoice);

            // jalankan fungsi cek
            $cek = $this->M_global->insertData('barang_out_retur_header', $isi_header); // insert header
        }

        if ($cek) { // jika fungsi cek berjalan
            // lakukan loop
            for ($x = 0; $x <= ($jum - 1); $x++) {
                $kode_barang    = $kode_barang_out[$x];
                $kode_satuan    = $kode_satuan[$x];
                $harga          = str_replace(',', '', $harga_out[$x]);
                $qty            = str_replace(',', '', $qty_out[$x]);
                $discpr         = str_replace(',', '', $discpr_out[$x]);
                $discrp         = str_replace(',', '', $discrp_out[$x]);
                $pajakrp        = str_replace(',', '', $pajakrp_out[$x]);
                $jumlah         = str_replace(',', '', $jumlah_out[$x]);

                $barang1        = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan' => $kode_satuan]);
                $barang2        = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan2' => $kode_satuan]);
                $barang3        = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan3' => $kode_satuan]);

                if ($barang1) {
                    $qty_satuan = 1;
                } else if ($barang2) {
                    $qty_satuan = $barang2->qty_satuan2;
                } else {
                    $qty_satuan = $barang3->qty_satuan3;
                }

                $qty_konversi   = $qty * $qty_satuan;

                // tamping isi detail
                $isi_detail = [
                    'invoice'       => $invoice,
                    'kode_barang'   => $kode_barang,
                    'kode_satuan'   => $kode_satuan,
                    'harga'         => $harga,
                    'qty_konversi'  => $qty_konversi,
                    'qty'           => $qty,
                    'discpr'        => $discpr,
                    'discrp'        => $discrp,
                    'pajak'         => (($pajakrp > 0) ? 1 : 0),
                    'pajakrp'       => $pajakrp,
                    'jumlah'        => $jumlah,
                ];

                // insert detail
                $this->M_global->insertData('barang_out_retur_detail', $isi_detail);
            }

            // beri nilai status = 1 kirim ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // beri nilai status = 0 kirim ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi print single barang_out_retur
    public function single_print_bout_ret($invoice, $yes)
    {
        $param            = 1;

        // param website
        $web_setting      = $this->M_global->getData('web_setting', ['id' => 1]);

        $position         = 'P'; // cek posisi l/p

        // body cetakan
        $body             = '';
        $body             .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $pencetak         = $this->M_global->getData('user', ['kode_user' => $this->session->userdata('kode_user')])->nama;

        $breaktable       = '<br>';
        $file             = 'Retur Penjualan';

        // isi body
        $header           = $this->M_global->getData('barang_out_retur_header', ['invoice' => $invoice]);

        // body header
        $body .= '<table style="width: 100%; font-size: 11px;">
            <tr>
                <td style="width: 15%;">Perihal</td>
                <td style="width: 2%;"> : </td>
                <td style="width: 33%;">' . $file . '</td>
                <td style="width: 50%; text-align: right; font-weight: bold; color: white;"><span style="border: 1px solid #0e1d2e; background-color: #0e1d2e;">' . $invoice . '</span></td>
            </tr>
            <tr>
                <td style="width: 15%;">Tgl/Jam Retur</td>
                <td style="width: 2%;"> : </td>
                <td colspan="2">' . date('d-m-Y', strtotime($header->tgl_retur)) . ' / ' . date('H:i:s', strtotime($header->jam_retur)) . '</td>
            </tr>
            <tr>
                <td style="width: 15%;">Pembeli</td>
                <td style="width: 2%;"> : </td>
                <td colspan="2">' . $this->M_global->getData('member', ['kode_member' => $header->kode_member])->nama . '</td>
            </tr>
            <tr>
                <td style="width: 15%;">Gudang</td>
                <td style="width: 2%;"> : </td>
                <td style="width: 33%;">' . $this->M_global->getData('m_gudang', ['kode_gudang' => $header->kode_gudang])->nama . '</td>
                <td style="width: 50%; text-align: right;">Pencetak : ' . $pencetak . '</td>
            </tr>
        </table>';

        $body .= $breaktable;

        $body .= '<table style="width: 100%; font-size: 10px;" autosize="1" cellpadding="5px">';

        $body .= '<thead>
            <tr>
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
            </tr>
        </thead>';

        $body .= '<tbody>';

        if ($param == 1) {
            $total = number_format($header->total);
        } else {
            $total = ceil($header->total);
        }
        $body .= '<tr style="background-color: skyblue;">
            <td colspan="6" style="border: 1px solid black; font-weight: bold;">No. Transaksi: ' . $header->invoice . '</td>
            <td colspan="2" style="border: 1px solid black; font-weight: bold; text-align: right">' . $total . '</td>
        </tr>';

        // detail barang
        $detail   = $this->M_global->getDataResult('barang_out_retur_detail', ['invoice' => $header->invoice]);

        $no       = 1;
        $tdiskon  = 0;
        $tpajak   = 0;
        $ttotal   = 0;
        foreach ($detail as $d) {
            $tdiskon    += $d->discrp;
            $tpajak     += $d->pajakrp;
            $ttotal     += $d->jumlah;

            if ($param == 1) {
                $harga    = number_format($d->harga);
                $qty      = number_format($d->qty);
                $discpr   = number_format($d->discpr);
                $discrp   = number_format($d->discrp);
                $pajak    = number_format($d->pajakrp);
                $jumlah   = number_format($d->jumlah);

                $tdiskonx = number_format($tdiskon);
                $tpajakx  = number_format($tpajak);
                $ttotalx  = number_format($ttotal);
            } else {
                $harga    = ceil($d->harga);
                $qty      = ceil($d->qty);
                $discpr   = ceil($d->discpr);
                $discrp   = ceil($d->discrp);
                $pajak    = ceil($d->pajakrp);
                $jumlah   = ceil($d->jumlah);

                $tdiskonx = ceil($tdiskon);
                $tpajakx  = ceil($tpajak);
                $ttotalx  = ceil($ttotal);
            }
            $body .= '<tr>
                <td style="border: 1px solid black;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $d->kode_barang . ' ~ ' . $this->M_global->getData('barang', ['kode_barang' => $d->kode_barang])->nama . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $harga . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $qty . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $discpr . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $discrp . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $pajak . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $jumlah . '</td>
            </tr>';
            $no++;
        }
        $body .= '<tr style="background-color: green;">
            <td colspan="5" style="border: 1px solid black; font-weight: bold; color: white;">Total</td>
            <td style="border: 1px solid black; font-weight: bold; color: white; text-align: right">' . $tdiskonx . '</td>
            <td style="border: 1px solid black; font-weight: bold; color: white; text-align: right">' . $tpajakx . '</td>
            <td style="border: 1px solid black; font-weight: bold; color: white; text-align: right">' . $ttotalx . '</td>
        </tr>';

        $body .= '</tbody>';

        $body .= '<tfoot>
            <tr>
                <td colspan="5">&nbsp;</td>
                <td colspan="3" style="text-align: center;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="5" style="width:60%;">&nbsp;</td>
                <td colspan="3" style="width:40%; text-align: center;">Yogyakarta, ' . date('d M Y') . '</td>
            </tr>
            <tr>
                <td colspan="5" style="width:60%;">&nbsp;</td>
                <td colspan="3" style="width:40%; text-align: center;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="5" style="width:60%;">&nbsp;</td>
                <td colspan="3" style="width:40%; text-align: center;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="5" style="width:60%;">&nbsp;</td>
                <td colspan="3" style="width:40%; text-align: center;">' . $pencetak . '</td>
            </tr>
        </tfoot>';

        $body .= '</table>';

        $judul = $invoice;
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting, $yes);
    }

    // fungsi hapus retur barang out
    public function delBeliOutRetur($invoice)
    {
        $header     = $this->M_global->getData('barang_out_retur_header', ['invoice' => $invoice]);
        $detail_cek = $this->M_global->getDataResult('barang_out_retur_detail', ['invoice' => $invoice]);

        if ($header->invoice_out != '' || $header->invoice_out != null || !empty($header->invoice_out) || isset($header->invoice_out)) {
            foreach ($detail_cek as $dc) {
                $detail_terima    = $this->M_global->getData('barang_out_retur_detail', ['invoice' => $invoice, 'kode_barang' => $dc->kode_barang]);
                $detail_po        = $this->M_global->getData('barang_out_detail', ['invoice' => $header->invoice_out, 'kode_barang' => $dc->kode_barang]);

                if ($detail_po->kode_barang == $detail_terima->kode_barang) {
                    $where_po     = ['invoice' => $header->invoice_out, 'kode_barang' => $detail_po->kode_barang, 'kode_satuan' => $detail_po->kode_satuan];

                    $data_update  = [
                        'qty_retur' => $detail_po->qty_retur - $detail_terima->qty
                    ];

                    $this->M_global->updateData('barang_out_detail', $data_update, $where_po);
                }
            }
        }

        // jalankan fungsi cek
        aktifitas_user_transaksi('Transaksi Keluar', 'menghapus Retur Penjualan', $invoice);

        $cek = [
            $this->M_global->delData('barang_out_retur_detail', ['invoice' => $invoice]), // del data detail penjualan
            $this->M_global->delData('barang_out_retur_header', ['invoice' => $invoice]), // del data header penjualan
        ];

        if ($cek) { // jika fungsi cek berjalan
            // kirim status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirim status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    /*
    * Stok
    **/

    // penyesuaian_stok page
    public function penyesuaian_stok()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Transaksi',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Penyesuaian Stok',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Transaksi/penyesuaian_stok_list/',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Barang/Penyesuaian_stok', $parameter);
    }

    // fungsi list penyesuaian_stok
    public function penyesuaian_stok_list($param1 = 1, $param2 = '0')
    {
        // parameter untuk list table
        $table            = 'penyesuaian_header';
        $colum            = ['id', 'invoice', 'tgl_penyesuaian', 'jam_penyesuaian', 'kode_user', 'kode_gudang', 'tipe_penyesuaian', 'acc', 'user_acc', 'tgl_acc', 'jam_acc'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param2   = 'tipe_penyesuaian';
        $kondisi_param1   = 'tgl_penyesuaian';

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
                if ($rd->acc > 0) {
                    $upd_diss = 'disabled';
                } else {
                    $upd_diss =  _lock_button();
                }
            } else {
                $upd_diss = 'disabled';
            }

            if ($deleted > 0) {
                if ($rd->acc > 0) {
                    $del_diss = 'disabled';
                } else {
                    $del_diss = _lock_button();
                }
            } else {
                $del_diss = 'disabled';
            }

            if ($confirmed > 0) {
                $confirm_diss = _lock_button();
            } else {
                $confirm_diss = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = date('d/m/Y', strtotime($rd->tgl_penyesuaian)) . ' ~ ' . date('H:i:s', strtotime($rd->jam_penyesuaian));
            $row[]  = $rd->invoice . '<span class="float-right">' . (($rd->acc == 1) ? '<span class="badge badge-primary">ACC</span>' : '<span class="badge badge-danger">Belum di ACC</span>') . '</span>';
            $row[]  = $this->M_global->getData('m_gudang', ['kode_gudang' => $rd->kode_gudang])->nama;
            $row[]  = '<div class="text-center">' . (($rd->tipe_penyesuaian == 1) ? '<span class="badge badge-primary text-center">SO</span>' : '<span class="badge badge-success text-center">Adjusment</span>') . '</div>';

            if ($rd->acc < 1) {
                $valid = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="ACC" onclick="valided(' . "'" . $rd->invoice . "', 1" . ')" ' . $confirm_diss . '><i class="fa-regular fa-circle-check"></i></button>';
            } else {
                $valid = '<button type="button" style="margin-bottom: 5px;" class="btn btn-light" title="Re-Batalkan" onclick="valided(' . "'" . $rd->invoice . "', 0" . ')" ' . $confirm_diss . '><i class="fa-solid fa-arrow-rotate-left"></i></button>';
            }

            $row[]  = '<div class="text-center">
                ' . $valid . '
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

    // form penyesuaian_stok page
    public function form_penyesuaian_stok($param)
    {
        // website config
        $web_setting            = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version            = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param != '0') {
            $penyesuaian_stok   = $this->M_global->getData('penyesuaian_header', ['invoice' => $param]);
            $barang_detail      = $this->M_global->getDataResult('penyesuaian_detail', ['invoice' => $param]);
        } else {
            $penyesuaian_stok   = null;
            $barang_detail      = null;
        }

        $parameter = [
            $this->data,
            'judul'                 => 'Transaksi',
            'nama_apps'             => $web_setting->nama,
            'page'                  => 'Penyesuaian Stok',
            'web'                   => $web_setting,
            'web_version'           => $web_version->version,
            'list_data'             => '',
            'data_penyesuaian_stok' => $penyesuaian_stok,
            'barang_detail'         => $barang_detail,
            'role'                  => $this->M_global->getResult('m_role'),
            'pajak'                 => $this->M_global->getData('m_pajak', ['aktif' => 1])->persentase,
            'list_barang'           => $this->M_global->getResult('barang'),
        ];

        $this->template->load('Template/Content', 'Barang/Form_penyesuaian_stok', $parameter);
    }

    // fungsi proses insert/update penyesuaian stok
    public function penyesuaian_stok_proses($param)
    {
        $kode_cabang              = $this->session->userdata('cabang');
        // header
        if ($param == 1) { // jika param = 1
            $invoice              = _invoicePenyesuaianStok($kode_cabang);
        } else { // selain itu
            $invoice              = $this->input->post('invoice');
        }

        $tgl_penyesuaian          = $this->input->post('tgl_penyesuaian');
        $jam_penyesuaian          = $this->input->post('jam_penyesuaian');
        $kode_gudang              = $this->input->post('kode_gudang');
        $tipe_penyesuaian         = $this->input->post('tipe_penyesuaian');
        $kode_user                = $this->session->userdata('kode_user');

        // detail
        $kode_penyesuaian_stok    = $this->input->post('kode_penyesuaian_stok');
        $kode_satuan_ps           = $this->input->post('kode_satuan');
        $qty_ps                   = $this->input->post('qty_ps');

        // cek jumlah detail barang
        if (isset($kode_penyesuaian_stok)) {
            $jum                      = count($kode_penyesuaian_stok);

            // tampung isi header
            $isi_header = [
                'kode_cabang'       => $kode_cabang,
                'invoice'           => $invoice,
                'tgl_penyesuaian'   => $tgl_penyesuaian,
                'jam_penyesuaian'   => $jam_penyesuaian,
                'kode_gudang'       => $kode_gudang,
                'tipe_penyesuaian'  => $tipe_penyesuaian,
                'acc'               => 0,
                'kode_user'         => $kode_user,
            ];

            if ($param == 2) { // jika param = 2
                aktifitas_user_transaksi('Transaksi Adjusment', 'mengubah Adjusment', $invoice);
                // jalankan fungsi cek
                $cek = [
                    $this->M_global->updateData('penyesuaian_header', $isi_header, ['invoice' => $invoice]), // update header
                    $this->M_global->delData('penyesuaian_detail', ['invoice' => $invoice]), // delete detail
                ];
            } else { // selain itu
                aktifitas_user_transaksi('Transaksi Adjusment', 'menambahkan Adjusment', $invoice);
                // jalankan fungsi cek
                $cek = $this->M_global->insertData('penyesuaian_header', $isi_header); // insert header
            }

            if ($cek) { // jika fungsi cek berjalan
                // lakukan loop
                for ($x = 0; $x <= ($jum - 1); $x++) {
                    $kode_barang    = $kode_penyesuaian_stok[$x];
                    $kode_satuan    = $kode_satuan_ps[$x];
                    $qty            = str_replace(',', '', $qty_ps[$x]);

                    $barang1        = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan' => $kode_satuan]);
                    $barang2        = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan2' => $kode_satuan]);
                    $barang3        = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan3' => $kode_satuan]);

                    if ($barang1) {
                        $qty_satuan = 1;
                    } else if ($barang2) {
                        $qty_satuan = $barang2->qty_satuan2;
                    } else {
                        $qty_satuan = $barang3->qty_satuan3;
                    }

                    $qty_konversi   = $qty * $qty_satuan;

                    // tamping isi detail
                    $isi_detail = [
                        'invoice'       => $invoice,
                        'kode_barang'   => $kode_barang,
                        'kode_satuan'   => $kode_satuan,
                        'qty'           => $qty,
                        'qty_konversi'  => $qty_konversi,
                    ];

                    // insert detail
                    $this->M_global->insertData('penyesuaian_detail', $isi_detail);
                }

                // beri nilai status = 1 kirim ke view
                echo json_encode(['status' => 1]);
            } else { // selain itu
                // beri nilai status = 0 kirim ke view
                echo json_encode(['status' => 0]);
            }
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi hapus barang in
    public function delPenyeStok($invoice)
    {
        // jalankan fungsi cek
        aktifitas_user_transaksi('Transaksi Adjusment', 'mengahpus Adjusment', $invoice);

        $cek = [
            $this->M_global->delData('penyesuaian_detail', ['invoice' => $invoice]), // del data detail pembelian
            $this->M_global->delData('penyesuaian_header', ['invoice' => $invoice]), // del data header pembelian
        ];

        if ($cek) { // jika fungsi cek berjalan
            // kirim status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirim status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi acc/re-acc
    public function accpenyesuaian_stok($invoice, $acc)
    {
        // header barang by invoice
        $header = $this->M_global->getData('penyesuaian_header', ['invoice' => $invoice]);
        // kode_gudang
        $kode_gudang = $header->kode_gudang;

        // detail barang
        $detail = $this->M_global->getDataResult('penyesuaian_detail', ['invoice' => $invoice]);

        if ($acc == 0) { // jika acc = 0
            aktifitas_user_transaksi('Transaksi Adjusment', 'Reject Adjusment', $invoice);

            $cek = $this->M_global->updateData('penyesuaian_header', ['acc' => 0, 'tgl_acc' => null, 'jam_acc' => null], ['invoice' => $invoice]);

            hitungStokAdjOut($detail, $kode_gudang, $invoice);
        } else { // selain itu
            aktifitas_user_transaksi('Transaksi Adjusment', 'Confirm Adjusment', $invoice);

            // update acc jadi 1
            $cek = $this->M_global->updateData('penyesuaian_header', ['acc' => 1, 'tgl_acc' => date('Y-m-d'), 'jam_acc' => date('H:i:s')], ['invoice' => $invoice]);

            hitungStokAdjIn($detail, $kode_gudang, $invoice);
        }

        if ($cek) { // jika fungsi cek berjalan
            // kirim status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirim status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    /*
    * Stok Opname
    **/

    // so page
    public function so()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $cek_jadwal_so = $this->M_global->getData('jadwal_so', ['id' => 1]);

        $parameter = [
            $this->data,
            'judul'         => 'Transaksi',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Stock Opname',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Transaksi/so_list/',
            'param1'        => '',
            'cek_jadwal'    => $cek_jadwal_so,
        ];

        $this->template->load('Template/Content', 'Barang/So', $parameter);
    }

    // fungsi list so
    public function so_list($param1 = 1, $param2 = '')
    {
        // parameter untuk list table
        $table            = 'jadwal_so';
        $colum            = ['id', 'tgl_dari', 'jam_dari', 'tgl_sampai', 'jam_sampai', 'status', 'kode_user', 'shift'];
        $order            = 'id';
        $order2           = 'desc';
        $order_arr        = ['id' => 'desc'];
        $kondisi_param2   = '';
        $kondisi_param1   = 'tgl_dari';

        // kondisi role
        $updated          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted          = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;
        $confirmed        = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->confirmed;

        // table server side tampung kedalam variable $list
        $dat              = explode("~", $param1);


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
                $upd_diss =  '';
            } else {
                $upd_diss = 'disabled';
            }

            if ($deleted > 0) {
                $del_diss = '';
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
            $row[]  = date('d/m/Y', strtotime($rd->tgl_dari)) . ' ~ ' . date('H:i:s', strtotime($rd->jam_dari));
            $row[]  = date('d/m/Y', strtotime($rd->tgl_sampai)) . ' ~ ' . date('H:i:s', strtotime($rd->jam_sampai));
            $row[]  = $this->M_global->getData('user', ['kode_user' => $rd->kode_user])->nama . '<br><span class="badge badge-danger text-center">Shift: ' . $rd->shift . '</span>';
            $row[]  = '<div class="text-center">
                <button type="button" style="margin-bottom: 5px;" class="btn btn-warning" title="Ubah" onclick="ubah(' . "'" . $rd->id . "'" . ')" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>
                <button type="button" style="margin-bottom: 5px;" class="btn btn-danger" title="Hapus" onclick="hapus(' . "'" . $rd->id . "'" . ')" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
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

    public function schedule_so()
    {
        $id             = $this->input->post('id_so');
        $tgl_dari_so    = $this->input->post('tgl_dari_so');
        $jam_dari_so    = $this->input->post('jam_dari_so');
        $tgl_sampai_so  = $this->input->post('tgl_sampai_so');
        $jam_sampai_so  = $this->input->post('jam_sampai_so');
        $status         = 1;
        $kode_user      = $this->session->userdata('kode_user');

        $data_so = [
            'tgl_dari'      => $tgl_dari_so,
            'jam_dari'      => $jam_dari_so,
            'tgl_sampai'    => $tgl_sampai_so,
            'jam_sampai'    => $jam_sampai_so,
            'status'        => $status,
            'kode_user'     => $kode_user,
            'shift'         => $this->session->userdata('shift'),
            'kode_cabang'   => $this->session->userdata('cabang'),
        ];

        if ($id == '' || $id == null) {
            $cek = $this->M_global->insertData('jadwal_so', $data_so);
        } else {
            $cek = $this->M_global->updateData('jadwal_so', $data_so, ['id' => $id]);
        }

        if ($cek) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // get Data SO
    public function getDataSo($id)
    {
        $so = $this->M_global->getData('jadwal_so', ['id' => $id]);

        echo json_encode($so);
    }

    // hapus jadwal so
    public function delJadwalSo($id)
    {
        $cek = $this->M_global->delData('jadwal_so', ['id' => $id]);

        if ($cek) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    /*
    * Riwayat Stok
    **/

    // riwayat_stok page
    public function riwayat_stok()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Transaksi',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Riwayat Stok',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Transaksi/riwayat_stok_list/',
            'param1'        => null,
        ];

        $this->template->load('Template/Content', 'Barang/Riwayat_stok', $parameter);
    }

    // fungsi list riwayat_stok
    public function riwayat_stok_list($gudang = null)
    {
        $this->load->model("M_riwayat_stok");
        // Retrieve data from the model
        $list = $this->M_riwayat_stok->get_datatables($gudang);

        $data = [];
        $no = $_POST['start'] + 1;

        // Loop through the list to populate the data array
        foreach ($list as $rd) {
            $s_akhir    = (int)$rd->akhir;
            $barang     = $this->M_global->getData('barang', ['kode_barang' => $rd->kode_barang]);

            $row = [];
            $row[] = $no++;
            $row[] = $rd->kode_barang . ' ~ ' . $rd->nama;
            $row[] = $rd->nama_gudang;
            $row[] = '<div class="float-right">' . konversi_show_satuan($rd->stok_min, $rd->kode_barang) . '</div>';
            $row[] = '<div class="float-right">' . konversi_show_satuan($rd->stok_max, $rd->kode_barang) . '</div>';
            $row[] = '<div class="float-right">' . konversi_show_satuan($s_akhir, $rd->kode_barang) . '</div>';
            $row[] = '<div class="text-center">' . ((($s_akhir < $rd->stok_min) && ($s_akhir > 0)) ? '<span class="badge badge-danger">Stok Menipis</span>' : (($s_akhir > $rd->stok_max) ? '<span class="badge badge-warning">Stok Melebihi Batas</span>' : (($s_akhir < 1) ? '<span class="badge badge-dark">Stok Kosong</span>' : '<span class="badge badge-success">Stok Tersedia</span>'))) . '</div>';
            $row[] = '<div class="text-center">
                <button style="margin-bottom: 5px;" type="button" class="btn btn-info" onclick="lihat(' . "'" . $rd->kode_barang . "', '" . $rd->kode_gudang . "'" . ')">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </button>
            </div>';
            $data[] = $row;
        }

        // Prepare the output in JSON format
        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->M_riwayat_stok->count_all($gudang),
            "recordsFiltered" => $this->M_riwayat_stok->count_filtered($gudang),
            "data" => $data,
        ];

        // Send the output to the view
        echo json_encode($output);
    }

    /**
     * Pengajuan Mutasi
     * untuk menampilkan, menambahkan, dan mengubah satuan dalam sistem
     */

    public function pengajuan_mutasi()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Transaksi',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Mutasi PO',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Transaksi/mutasi_po_list/',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Mutasi/Pengajuan', $parameter);
    }

    // fungsi list mutasi_po
    public function mutasi_po_list($param1 = 1, $param2 = '')
    {
        // parameter untuk list table
        $table                      = 'mutasi_po_header';
        $colum                      = ['id', 'invoice', 'tgl_po', 'jam_po', 'jenis_po', 'dari', 'menuju', 'total', 'user', 'status_po', 'shift'];
        $order                      = 'id';
        $order2                     = 'desc';
        $order_arr                  = ['id' => 'desc'];
        $kondisi_param2             = '';
        $kondisi_param1             = 'tgl_po';

        // kondisi role
        $updated                    = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted                    = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;
        $confirmed                  = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->confirmed;

        // table server side tampung kedalam variable $list
        $dat                        = explode("~", $param1);

        if ($dat[0] == 1) {
            $bulan                  = date('m');
            $tahun                  = date('Y');
            $type                   = 1;
        } else {
            $bulan                  = date('Y-m-d', strtotime($dat[1]));
            $tahun                  = date('Y-m-d', strtotime($dat[2]));
            $type                   = 2;
        }

        $list                       = $this->M_datatables2->get_datatables($table, $colum, $order_arr, $order, $order2, $kondisi_param1, $type, $bulan, $tahun, $param2, $kondisi_param2);

        $data                       = [];
        $no                         = $_POST['start'] + 1;

        // loop $list
        foreach ($list as $rd) {
            if ($updated > 0) {
                if ($rd->status_po > 0) {
                    $upd_diss   = 'disabled';
                } else {
                    $upd_diss   = _lock_button();
                }
            } else {
                $upd_diss           = 'disabled';
            }

            if ($deleted > 0) {
                if ($rd->status_po > 0) {
                    $del_diss   = 'disabled';
                } else {
                    $del_diss   = _lock_button();
                }
            } else {
                $del_diss           = 'disabled';
            }

            if ($confirmed > 0) {
                $confirm_diss       = _lock_button();
            } else {
                $confirm_diss       = 'disabled';
            }

            $cek_mutasi = $this->M_global->getData('mutasi_header', ['invoice_po' => $rd->invoice]);

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->invoice . '<br>' . (($rd->status_po > 0) ? '<span class="badge badge-primary">ACC</span>' : '<span class="badge badge-success">Buka</span>') . (($cek_mutasi) ? (($cek_mutasi->status == 1) ? ' <span class="badge badge-info">Sudah diproses</span>' : ' <span class="badge badge-warning">Belum diapprove</span>') : ' <span class="badge badge-danger">Belum diproses</span>');
            $row[]  = date('d/m/Y', strtotime($rd->tgl_po)) . ' ~ ' . date('H:i:s', strtotime($rd->jam_po));
            $row[]  = '<div class="text-center">' . (($rd->jenis_po > 0) ? '<span class="badge badge-primary">Mutasi Cabang</span>' : '<span class="badge badge-success">Mutasi Gudang</span>') . '</div>';
            $row[]  = (($rd->jenis_po > 0) ? $this->M_global->getData('cabang', ['kode_cabang' => $rd->dari])->cabang : $this->M_global->getData('m_gudang', ['kode_gudang' => $rd->dari])->nama);
            $row[]  = (($rd->jenis_po > 0) ? $this->M_global->getData('cabang', ['kode_cabang' => $rd->menuju])->cabang : $this->M_global->getData('m_gudang', ['kode_gudang' => $rd->menuju])->nama);
            $row[]  = 'Rp. <span class="float-right">' . number_format($rd->total) . '</span>';
            $row[]  = $this->M_global->getData('user', ['kode_user' => $rd->user])->nama . '<br><span class="badge badge-danger">Shift: ' . $rd->shift . '</span>';

            if ($rd->status_po > 0) {
                $ubah   = '<button type="button" style="margin-bottom: 5px;" class="btn btn-warning" title="Ubah" disabled><i class="fa-regular fa-pen-to-square"></i></button>';

                if ($cek_mutasi) {
                    $accept = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" disabled><i class="fa-solid fa-check-to-slot"></i></button>';
                } else {
                    $accept = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="Re-ACC" onclick="valided(' . "'" . $rd->invoice . "', 0" . ')" ' . $confirm_diss . '><i class="fa-solid fa-check-to-slot"></i></button>';
                }
            } else {
                $accept = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="ACC" onclick="valided(' . "'" . $rd->invoice . "', 1" . ')"><i class="fa-regular fa-circle-check"></i></button>';

                $ubah   = '<button type="button" style="margin-bottom: 5px;" class="btn btn-warning" title="Ubah" onclick="ubah(' . "'" . $rd->invoice . "', 0" . ')" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>';
            }

            $row[]  = '<div class="text-center">
                ' . $accept . '
                ' . $ubah . '
                <button type="button" style="margin-bottom: 5px;" class="btn btn-danger" title="Hapus" onclick="hapus(' . "'" . $rd->invoice . "'" . ')" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
                <button type="button" style="margin-bottom: 5px;" class="btn btn-dark" title="Cetak" onclick="cetak(' . "'" . $rd->invoice . "', 0" . ')"><i class="fa-solid fa-print"></i></button>
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

    // form mutasi_po page
    public function form_mutasi_po($param)
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        if ($param != '0') {
            $mutasi_po        = $this->M_global->getData('mutasi_po_header', ['invoice' => $param]);
            $mutasi_po_detail = $this->M_global->getDataResult('mutasi_po_detail', ['invoice' => $param]);
        } else {
            $mutasi_po        = null;
            $mutasi_po_detail = null;
        }

        $parameter = [
            $this->data,
            'judul'             => 'Transaksi',
            'nama_apps'         => $web_setting->nama,
            'page'              => 'Pengajuan Mutasi',
            'web'               => $web_setting,
            'web_version'       => $web_version->version,
            'list_data'         => '',
            'data_mutasi_po'    => $mutasi_po,
            'mutasi_po_detail'  => $mutasi_po_detail,
            'role'              => $this->M_global->getResult('m_role'),
            'pajak'             => $this->M_global->getData('m_pajak', ['aktif' => 1])->persentase,
            'list_barang'       => $this->M_global->getResult('barang'),
        ];

        $this->template->load('Template/Content', 'Mutasi/Form_mutasi_po', $parameter);
    }

    // get jenis mutasi
    public function getJenisMutasi($jenis)
    {
        if ($jenis == 0) {
            $jenism   = $this->db->query('SELECT kode_gudang AS kode, nama AS nama FROM m_gudang WHERE aktif = 1')->result();
        } else {
            $jenism   = $this->db->query('SELECT kode_cabang AS kode, cabang AS nama FROM cabang')->result();
        }

        echo json_encode($jenism);
    }

    // fungsi insert/update proses mutasi_po
    public function mutasi_po_proses($param)
    {
        $kode_cabang    = $this->session->userdata('cabang');
        $shift          = $this->session->userdata('shift');

        // header
        if ($param == 1) { // jika param = 1
            $invoice    = _invoicePOM($kode_cabang);
        } else {
            $invoice    = $this->input->post('invoice');
        }

        $tgl_po         = $this->input->post('tgl_po');
        $jam_po         = $this->input->post('jam_po');
        $jenis_po       = $this->input->post('jenis_po');
        $dari           = $this->input->post('dari');
        $menuju         = $this->input->post('menuju');

        $subtotal       = str_replace(',', '', $this->input->post('subtotal'));
        $diskon         = str_replace(',', '', $this->input->post('diskon'));
        $pajak          = str_replace(',', '', $this->input->post('pajak'));
        $total          = str_replace(',', '', $this->input->post('total'));

        // detail
        $kode_barang_po_in = $this->input->post('kode_barang_po_in');
        $kode_satuan_in = $this->input->post('kode_satuan');
        $harga_in       = $this->input->post('harga_in');
        $qty_in         = $this->input->post('qty_in');
        $discpr_in      = $this->input->post('discpr_in');
        $discrp_in      = $this->input->post('discrp_in');
        $pajakrp_in     = $this->input->post('pajakrp_in');
        $jumlah_in      = $this->input->post('jumlah_in');

        // cek jumlah detail barang_in
        $jum            = count($kode_barang_po_in);

        // tampung isi header
        $isi_header = [
            'kode_cabang'   => $kode_cabang,
            'invoice'       => $invoice,
            'tgl_po'        => $tgl_po,
            'jam_po'        => $jam_po,
            'jenis_po'      => $jenis_po,
            'dari'          => $dari,
            'menuju'        => $menuju,
            'pajak'         => $pajak,
            'diskon'        => $diskon,
            'subtotal'      => $subtotal,
            'total'         => $total,
            'user'          => $this->session->userdata('kode_user'),
            'shift'         => $shift,
            'status_po'     => 0,
        ];

        if ($param == 2) { // jika param = 2
            aktifitas_user_transaksi('Mutasi', 'mengubah PO', $invoice);

            // jalankan fungsi cek
            $cek = [
                $this->M_global->updateData('mutasi_po_header', $isi_header, ['invoice' => $invoice]), // update header
                $this->M_global->delData('mutasi_po_detail', ['invoice' => $invoice]), // delete detail
            ];
        } else { // selain itu
            aktifitas_user_transaksi('Mutasi', 'menambahkan PO', $invoice);

            // jalankan fungsi cek
            $cek = $this->M_global->insertData('mutasi_po_header', $isi_header); // insert header
        }

        if ($cek) { // jika fungsi cek berjalan
            // lakukan loop
            for ($x = 0; $x <= ($jum - 1); $x++) {
                $kode_barang    = $kode_barang_po_in[$x];
                $kode_satuan    = $kode_satuan_in[$x];
                $harga          = str_replace(',', '', $harga_in[$x]);
                $qty            = str_replace(',', '', $qty_in[$x]);
                $discpr         = str_replace(',', '', $discpr_in[$x]);
                $discrp         = str_replace(',', '', $discrp_in[$x]);
                $pajakrp        = str_replace(',', '', $pajakrp_in[$x]);
                $jumlah         = str_replace(',', '', $jumlah_in[$x]);

                $barang1        = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan' => $kode_satuan]);
                $barang2        = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan2' => $kode_satuan]);
                $barang3        = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan3' => $kode_satuan]);

                if ($barang1) {
                    $qty_satuan = 1;
                } else if ($barang2) {
                    $qty_satuan = $barang2->qty_satuan2;
                } else {
                    $qty_satuan = $barang3->qty_satuan3;
                }

                $qty_konversi   = $qty * $qty_satuan;

                // tamping isi detail
                $isi_detail = [
                    'invoice'       => $invoice,
                    'kode_barang'   => $kode_barang,
                    'kode_satuan'   => $kode_satuan,
                    'harga'         => $harga,
                    'qty_konversi'  => $qty_konversi,
                    'qty'           => $qty,
                    'qty_terima'    => '0.00',
                    'discpr'        => $discpr,
                    'discrp'        => $discrp,
                    'pajak'         => (($pajakrp > 0) ? 1 : 0),
                    'pajakrp'       => $pajakrp,
                    'jumlah'        => $jumlah,
                ];

                // insert detail
                $this->M_global->insertData('mutasi_po_detail', $isi_detail);
            }

            $this->single_print_mutasi_po($invoice, 1);

            // beri nilai status = 1 kirim ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // beri nilai status = 0 kirim ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi print single barang_in
    public function single_print_mutasi_po($invoice, $yes)
    {
        $param          = 1;

        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $pencetak       = $this->M_global->getData('user', ['kode_user' => $this->session->userdata('kode_user')])->nama;

        $breaktable     = '<br>';
        $file = "Pengajuan Mutasi";

        // isi body
        $header = $this->M_global->getData('mutasi_po_header', ['invoice' => $invoice]);

        if ($header->jenis_po == 0) {
            $dari = $this->M_global->getData('m_gudang', ['kode_gudang' => $header->dari])->nama;
            $menuju = $this->M_global->getData('m_gudang', ['kode_gudang' => $header->menuju])->nama;
        } else {
            $dari = $this->M_global->getData('cabang', ['kode_cabang' => $header->dari])->cabang;
            $menuju = $this->M_global->getData('cabang', ['kode_cabang' => $header->menuju])->cabang;
        }

        // body header
        $body .= '<table style="width: 100%; font-size: 11px;">
            <tr>
                <td style="width: 15%;">Perihal</td>
                <td style="width: 2%;"> : </td>
                <td style="width: 33%;">' . $file . '</td>
                <td style="width: 50%; text-align: right; font-weight: bold; color: white;"><span style="border: 1px solid #0e1d2e; background-color: #0e1d2e;">' . $invoice . '</span></td>
            </tr>
            <tr>
                <td style="width: 15%;">Tgl/Jam PO</td>
                <td style="width: 2%;"> : </td>
                <td colspan="2">' . date('d-m-Y', strtotime($header->tgl_po)) . ' / ' . date('H:i:s', strtotime($header->jam_po)) . '</td>
            </tr>
            <tr>
                <td style="width: 15%;">Dari</td>
                <td style="width: 2%;"> : </td>
                <td colspan="2">' . $dari . '</td>
            </tr>
            <tr>
                <td style="width: 15%;">Menuju</td>
                <td style="width: 2%;"> : </td>
                <td colspan="2">' . $menuju . '</td>
            </tr>
            <tr>
                <td style="width: 15%;">User Input</td>
                <td style="width: 2%;"> : </td>
                <td style="width: 33%;">' . $this->M_global->getData('user', ['kode_user' => $header->user])->nama . '</td>
                <td style="width: 50%; text-align: right;">Pencetak : ' . $pencetak . '</td>
            </tr>
        </table>';

        $body .= $breaktable;

        $body .= '<table style="width: 100%; font-size: 10px;" autosize="1" cellpadding="5px">';

        $body .= '<thead>
            <tr>
                <th rowspan="2" style="width: 5%; border: 1px solid black; background-color: #0e1d2e; color: white;">#</th>
                <th rowspan="2" style="width: 20%; border: 1px solid black; background-color: #0e1d2e; color: white;">Barang</th>
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

        if ($param == 1) {
            $total = number_format($header->total);
        } else {
            $total = ceil($header->total);
        }
        $body .= '<tr style="background-color: skyblue;">
            <td colspan="7" style="border: 1px solid black; font-weight: bold;">No. Transaksi: ' . $header->invoice . '</td>
            <td colspan="2" style="border: 1px solid black; font-weight: bold; text-align: right">' . $total . '</td>
        </tr>';

        // detail barang
        $detail   = $this->M_global->getDataResult('mutasi_po_detail', ['invoice' => $header->invoice]);

        $no       = 1;
        $tdiskon  = 0;
        $tpajak   = 0;
        $ttotal   = 0;
        foreach ($detail as $d) {
            $tdiskon    += $d->discrp;
            $tpajak     += $d->pajakrp;
            $ttotal     += $d->jumlah;

            if ($param == 1) {
                $harga    = number_format($d->harga);
                $qty      = number_format($d->qty);
                $discpr   = number_format($d->discpr);
                $discrp   = number_format($d->discrp);
                $pajak    = number_format($d->pajakrp);
                $jumlah   = number_format($d->jumlah);

                $tdiskonx = number_format($tdiskon);
                $tpajakx  = number_format($tpajak);
                $ttotalx  = number_format($ttotal);
            } else {
                $harga    = ceil($d->harga);
                $qty      = ceil($d->qty);
                $discpr   = ceil($d->discpr);
                $discrp   = ceil($d->discrp);
                $pajak    = ceil($d->pajakrp);
                $jumlah   = ceil($d->jumlah);

                $tdiskonx = ceil($tdiskon);
                $tpajakx  = ceil($tpajak);
                $ttotalx  = ceil($ttotal);
            }

            $body .= '<tr>
                <td style="border: 1px solid black;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $d->kode_barang . ' ~ ' . $this->M_global->getData('barang', ['kode_barang' => $d->kode_barang])->nama . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $this->M_global->getData('m_satuan', ['kode_satuan' => $d->kode_satuan])->keterangan . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $harga . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $qty . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $discpr . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $discrp . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $pajak . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $jumlah . '</td>
            </tr>';
            $no++;
        }

        $body .= '<tr style="background-color: green;">
            <td colspan="6" style="border: 1px solid black; font-weight: bold; color: white;">Total</td>
            <td style="border: 1px solid black; font-weight: bold; color: white; text-align: right">' . $tdiskonx . '</td>
            <td style="border: 1px solid black; font-weight: bold; color: white; text-align: right">' . $tpajakx . '</td>
            <td style="border: 1px solid black; font-weight: bold; color: white; text-align: right">' . $ttotalx . '</td>
        </tr>';

        $body .= '</tbody>';

        $body .= '<tfoot>
            <tr>
                <td colspan="6">&nbsp;</td>
                <td colspan="3" style="text-align: center;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" style="width:60%;">&nbsp;</td>
                <td colspan="3" style="width:40%; text-align: center;">Yogyakarta, ' . strtotime(date('Y-m-d')) . '</td>
            </tr>
            <tr>
                <td colspan="6" style="width:60%;">&nbsp;</td>
                <td colspan="3" style="width:40%; text-align: center;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" style="width:60%;">&nbsp;</td>
                <td colspan="3" style="width:40%; text-align: center;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" style="width:60%;">&nbsp;</td>
                <td colspan="3" style="width:40%; text-align: center;">' . $pencetak . '</td>
            </tr>
        </tfoot>';

        $body .= '</table>';

        $judul = $invoice;
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting, $yes);
    }

    // fungsi hapus barang po in
    public function delMutasiPo($invoice)
    {
        aktifitas_user_transaksi('Mutasi', 'menghapus Pengajuan', $invoice);

        // jalankan fungsi cek
        $cek = [
            $this->M_global->delData('mutasi_po_detail', ['invoice' => $invoice]), // del data detail mutasi
            $this->M_global->delData('mutasi_po_header', ['invoice' => $invoice]), // del data header mutasi
        ];

        if ($cek) { // jika fungsi cek berjalan
            // kirim status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirim status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi acc/re-acc
    public function accmutasi_po($invoice, $acc)
    {
        if ($acc == 0) { // jika acc = 0
            aktifitas_user_transaksi('Mutasi', 'Reject PO', $invoice);

            // update is_approve jadi 0
            $cek = $this->M_global->updateData('mutasi_po_header', ['status_po' => 0, 'tgl_approve' => null, 'jam_approve' => null], ['invoice' => $invoice]);
        } else { // selain itu
            aktifitas_user_transaksi('Mutasi', 'Confirm PO', $invoice);

            // update is_approve jadi 1
            $cek = $this->M_global->updateData('mutasi_po_header', ['status_po' => 1, 'tgl_approve' => date('Y-m-d'), 'jam_approve' => date('H:i:s'), 'user_approve' => $this->session->userdata('kode_user')], ['invoice' => $invoice]);
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
     * Penerimaan Mutasi
     * untuk menampilkan, menambahkan, dan mengubah satuan dalam sistem
     */

    public function penerimaan_mutasi()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Transaksi',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'Mutasi',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => 'Transaksi/mutasi_list/',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Mutasi/Penerimaan', $parameter);
    }

    // fungsi list mutasi
    public function mutasi_list($param1 = 1, $param2 = '')
    {
        // parameter untuk list table
        $table                  = 'mutasi_header';
        $colum                  = ['id', 'invoice', 'tgl', 'jam', 'jenis', 'dari', 'menuju', 'total', 'user', 'status', 'shift'];
        $order                  = 'id';
        $order2                 = 'desc';
        $order_arr              = ['id' => 'desc'];
        $kondisi_param2         = '';
        $kondisi_param1         = 'tgl';

        // kondisi role
        $updated                = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->updated;
        $deleted                = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->deleted;
        $confirmed              = $this->M_global->getData('m_role', ['kode_role' => $this->data['kode_role']])->confirmed;

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
            if ($updated > 0) {
                if ($rd->status > 0) {
                    $upd_diss   = 'disabled';
                } else {
                    $upd_diss   = _lock_button();
                }
            } else {
                $upd_diss       = 'disabled';
            }

            if ($deleted > 0) {
                if ($rd->status > 0) {
                    $del_diss   = 'disabled';
                } else {
                    $del_diss   = _lock_button();
                }
            } else {
                $del_diss       = 'disabled';
            }

            if ($confirmed > 0) {
                $confirm_diss   = _lock_button();
            } else {
                $confirm_diss   = 'disabled';
            }

            $row    = [];
            $row[]  = $no++;
            $row[]  = $rd->invoice . '<br>' . (($rd->status > 0) ? '<span class="badge badge-primary">ACC</span>' : '<span class="badge badge-success">Buka</span>');
            $row[]  = date('d/m/Y', strtotime($rd->tgl)) . ' ~ ' . date('H:i:s', strtotime($rd->jam));
            $row[]  = '<div class="text-center">' . (($rd->jenis > 0) ? '<span class="badge badge-primary">Mutasi Cabang</span>' : '<span class="badge badge-success">Mutasi Gudang</span>') . '</div>';
            $row[]  = (($rd->jenis > 0) ? $this->M_global->getData('cabang', ['kode_cabang' => $rd->dari])->cabang : $this->M_global->getData('m_gudang', ['kode_gudang' => $rd->dari])->nama);
            $row[]  = (($rd->jenis > 0) ? $this->M_global->getData('cabang', ['kode_cabang' => $rd->menuju])->cabang : $this->M_global->getData('m_gudang', ['kode_gudang' => $rd->menuju])->nama);
            $row[]  = 'Rp. <span class="float-right">' . number_format($rd->total) . '</span>';
            $row[]  = $this->M_global->getData('user', ['kode_user' => $rd->user])->nama . '<br><span class="badge badge-danger">Shift: ' . $rd->shift . '</span>';

            if ($rd->status > 0) {
                $ubah   = '<button type="button" style="margin-bottom: 5px;" class="btn btn-warning" title="Ubah" disabled><i class="fa-regular fa-pen-to-square"></i></button>';

                $accept = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="Re-ACC" onclick="valided(' . "'" . $rd->invoice . "', 0" . ')" ' . $confirm_diss . '><i class="fa-solid fa-check-to-slot"></i></button>';
            } else {
                $accept = '<button type="button" style="margin-bottom: 5px;" class="btn btn-info" title="ACC" onclick="valided(' . "'" . $rd->invoice . "', 1" . ')"><i class="fa-regular fa-circle-check"></i></button>';

                $ubah   = '<button type="button" style="margin-bottom: 5px;" class="btn btn-warning" title="Ubah" onclick="ubah(' . "'" . $rd->invoice . "', 0" . ')" ' . $upd_diss . '><i class="fa-regular fa-pen-to-square"></i></button>';
            }

            $row[]  = '<div class="text-center">
                ' . $accept . '
                ' . $ubah . '
                <button type="button" style="margin-bottom: 5px;" class="btn btn-danger" title="Hapus" onclick="hapus(' . "'" . $rd->invoice . "'" . ')" ' . $del_diss . '><i class="fa-regular fa-circle-xmark"></i></button>
                <button type="button" style="margin-bottom: 5px;" class="btn btn-dark" title="Cetak" onclick="cetak(' . "'" . $rd->invoice . "', 0" . ')"><i class="fa-solid fa-print"></i></button>
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

    // form mutasi page
    public function form_mutasi($param)
    {
        // website config
        $web_setting          = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version          = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $kode_cabang          = $this->session->userdata('cabang');

        if ($param != '0') {
            $mutasi           = $this->M_global->getData('mutasi_header', ['invoice' => $param]);
            $mutasi_detail    = $this->M_global->getDataResult('mutasi_detail', ['invoice' => $param]);
        } else {
            $mutasi           = null;
            $mutasi_detail    = null;
        }

        $parameter = [
            $this->data,
            'judul'             => 'Transaksi',
            'nama_apps'         => $web_setting->nama,
            'page'              => 'Penerimaan Mutasi',
            'web'               => $web_setting,
            'web_version'       => $web_version->version,
            'list_data'         => '',
            'data_mutasi'       => $mutasi,
            'mutasi_detail'     => $mutasi_detail,
            'data_pm'           => $this->db->query("SELECT * FROM mutasi_po_header WHERE status_po = 1 AND invoice NOT IN (SELECT invoice_po FROM mutasi_header) AND IF(jenis_po = 1, dari, kode_cabang) = '$kode_cabang'")->result(),
            'role'              => $this->M_global->getResult('m_role'),
            'pajak'             => $this->M_global->getData('m_pajak', ['aktif' => 1])->persentase,
            'list_barang'       => $this->M_global->getResult('barang'),
        ];

        $this->template->load('Template/Content', 'Mutasi/Form_mutasi', $parameter);
    }

    // get data pengajuan mutasi
    public function getDataMPO($invoice_po)
    {
        $mutasi_po = $this->M_global->getData('mutasi_po_header', ['invoice' => $invoice_po]);

        if ($mutasi_po) {
            if ($mutasi_po->jenis_po == 0) {
                $mutasi_po_header = $this->db->query("SELECT h.*, (SELECT nama FROM m_gudang WHERE kode_gudang = h.dari) AS dari_nama, (SELECT nama FROM m_gudang WHERE kode_gudang = h.menuju) AS menuju_nama FROM mutasi_po_header h WHERE invoice = '$invoice_po'")->row();
            } else {
                $mutasi_po_header = $this->db->query("SELECT h.*, (SELECT cabang FROM cabang WHERE kode_cabang = h.dari) AS dari_nama, (SELECT cabang FROM cabang WHERE kode_cabang = h.menuju) AS menuju_nama FROM mutasi_po_header h WHERE invoice = '$invoice_po'")->row();
            }

            $mutasi_po_detail = $this->db->query("SELECT d.*, (SELECT nama FROM barang WHERE kode_barang = d.kode_barang) AS nama_barang, s.keterangan AS nama_satuan FROM mutasi_po_detail AS d JOIN m_satuan s ON d.kode_satuan = s.kode_satuan WHERE d.invoice = '$invoice_po'")->result();

            echo json_encode([['status' => 1, 'header' => $mutasi_po_header], $mutasi_po_detail]);
        } else {
            echo json_encode([['status' => 0]]);
        }
    }

    // fungsi insert/update proses mutasi
    public function mutasi_proses($param)
    {
        $kode_cabang    = $this->session->userdata('cabang');
        $shift          = $this->session->userdata('shift');

        // header
        if ($param == 1) { // jika param = 1
            $invoice    = _invoiceMutasi($kode_cabang);
        } else {
            $invoice    = $this->input->post('invoice');
        }

        $invoice_po     = $this->input->post('invoice_po');
        $tgl            = $this->input->post('tgl');
        $jam            = $this->input->post('jam');
        $jenis          = $this->input->post('jenis');
        $dari           = $this->input->post('dari');
        $menuju         = $this->input->post('menuju');

        $subtotal       = str_replace(',', '', $this->input->post('subtotal'));
        $diskon         = str_replace(',', '', $this->input->post('diskon'));
        $pajak          = str_replace(',', '', $this->input->post('pajak'));
        $total          = str_replace(',', '', $this->input->post('total'));

        // detail
        $kode_barang_po_in = $this->input->post('kode_barang_po_in');
        $kode_satuan_in = $this->input->post('kode_satuan');
        $harga_in       = $this->input->post('harga_in');
        $qty_in         = $this->input->post('qty_in');
        $discpr_in      = $this->input->post('discpr_in');
        $discrp_in      = $this->input->post('discrp_in');
        $pajakrp_in     = $this->input->post('pajakrp_in');
        $jumlah_in      = $this->input->post('jumlah_in');

        // cek jumlah detail barang_in
        $jum            = count($kode_barang_po_in);

        // tampung isi header
        $isi_header = [
            'kode_cabang'   => $kode_cabang,
            'invoice'       => $invoice,
            'invoice_po'    => $invoice_po,
            'tgl'           => $tgl,
            'jam'           => $jam,
            'jenis'         => $jenis,
            'dari'          => $dari,
            'menuju'        => $menuju,
            'pajak'         => $pajak,
            'diskon'        => $diskon,
            'subtotal'      => $subtotal,
            'total'         => $total,
            'user'          => $this->session->userdata('kode_user'),
            'shift'         => $shift,
            'status'        => 0,
        ];

        if ($param == 2) { // jika param = 2
            aktifitas_user_transaksi('Mutasi', 'mengubah Penerimaan', $invoice);

            // jalankan fungsi cek
            $cek = [
                $this->M_global->updateData('mutasi_header', $isi_header, ['invoice' => $invoice]), // update header
                $this->M_global->delData('mutasi_detail', ['invoice' => $invoice]), // delete detail
            ];
        } else { // selain itu
            aktifitas_user_transaksi('Mutasi', 'menambahkan Penerimaan', $invoice);

            // jalankan fungsi cek
            $cek = $this->M_global->insertData('mutasi_header', $isi_header); // insert header
        }

        if ($cek) { // jika fungsi cek berjalan
            // lakukan loop
            for ($x = 0; $x <= ($jum - 1); $x++) {
                $kode_barang    = $kode_barang_po_in[$x];
                $kode_satuan    = $kode_satuan_in[$x];
                $harga          = str_replace(',', '', $harga_in[$x]);
                $qty            = str_replace(',', '', $qty_in[$x]);
                $discpr         = str_replace(',', '', $discpr_in[$x]);
                $discrp         = str_replace(',', '', $discrp_in[$x]);
                $pajakrp        = str_replace(',', '', $pajakrp_in[$x]);
                $jumlah         = str_replace(',', '', $jumlah_in[$x]);

                $barang1        = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan' => $kode_satuan]);
                $barang2        = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan2' => $kode_satuan]);
                $barang3        = $this->M_global->getData('barang', ['kode_barang' => $kode_barang, 'kode_satuan3' => $kode_satuan]);

                if ($barang1) {
                    $qty_satuan = 1;
                } else if ($barang2) {
                    $qty_satuan = $barang2->qty_satuan2;
                } else {
                    $qty_satuan = $barang3->qty_satuan3;
                }

                $qty_konversi   = $qty * $qty_satuan;

                // tamping isi detail
                $isi_detail = [
                    'invoice'       => $invoice,
                    'kode_barang'   => $kode_barang,
                    'kode_satuan'   => $kode_satuan,
                    'harga'         => $harga,
                    'qty_konversi'  => $qty_konversi,
                    'qty'           => $qty,
                    'discpr'        => $discpr,
                    'discrp'        => $discrp,
                    'pajak'         => (($pajakrp > 0) ? 1 : 0),
                    'pajakrp'       => $pajakrp,
                    'jumlah'        => $jumlah,
                ];

                // insert detail
                $this->M_global->insertData('mutasi_detail', $isi_detail);
            }

            $this->single_print_mutasi($invoice, 1);

            // beri nilai status = 1 kirim ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // beri nilai status = 0 kirim ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi print single barang_in
    public function single_print_mutasi($invoice, $yes)
    {
        $param          = 1;

        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $pencetak       = $this->M_global->getData('user', ['kode_user' => $this->session->userdata('kode_user')])->nama;

        $breaktable     = '<br>';
        $file = "Penerimaan Mutasi";

        // isi body
        $header = $this->M_global->getData('mutasi_header', ['invoice' => $invoice]);

        if ($header->jenis == 0) {
            $dari = $this->M_global->getData(
                'm_gudang',
                ['kode_gudang' => $header->dari]
            )->nama;
            $menuju = $this->M_global->getData('m_gudang', ['kode_gudang' => $header->menuju])->nama;
        } else {
            $dari = $this->M_global->getData('cabang', ['kode_cabang' => $header->dari])->cabang;
            $menuju = $this->M_global->getData(
                'cabang',
                ['kode_cabang' => $header->menuju]
            )->cabang;
        }

        // body header
        $body .= '<table style="width: 100%; font-size: 11px;">
            <tr>
                <td style="width: 15%;">Perihal</td>
                <td style="width: 2%;"> : </td>
                <td style="width: 33%;">' . $file . '</td>
                <td style="width: 50%; text-align: right; font-weight: bold; color: white;"><span style="border: 1px solid #0e1d2e; background-color: #0e1d2e;">' . $invoice . '</span></td>
            </tr>
            <tr>
                <td style="width: 15%;">Tgl/Jam PO</td>
                <td style="width: 2%;"> : </td>
                <td colspan="2">' . date('d-m-Y', strtotime($header->tgl)) . ' / ' . date('H:i:s', strtotime($header->jam)) . '</td>
            </tr>
            <tr>
                <td style="width: 15%;">Dari</td>
                <td style="width: 2%;"> : </td>
                <td colspan="2">' . $dari . '</td>
            </tr>
            <tr>
                <td style="width: 15%;">Menuju</td>
                <td style="width: 2%;"> : </td>
                <td colspan="2">' . $menuju . '</td>
            </tr>
            <tr>
                <td style="width: 15%;">User Input</td>
                <td style="width: 2%;"> : </td>
                <td style="width: 33%;">' . $this->M_global->getData('user', ['kode_user' => $header->user])->nama . '</td>
                <td style="width: 50%; text-align: right;">Pencetak : ' . $pencetak . '</td>
            </tr>
        </table>';

        $body .= $breaktable;

        $body .= '<table style="width: 100%; font-size: 10px;" autosize="1" cellpadding="5px">';

        $body .= '<thead>
            <tr>
                <th rowspan="2" style="width: 5%; border: 1px solid black; background-color: #0e1d2e; color: white;">#</th>
                <th rowspan="2" style="width: 20%; border: 1px solid black; background-color: #0e1d2e; color: white;">Barang</th>
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

        if ($param == 1) {
            $total = number_format($header->total);
        } else {
            $total = ceil($header->total);
        }
        $body .= '<tr style="background-color: skyblue;">
            <td colspan="7" style="border: 1px solid black; font-weight: bold;">No. Transaksi: ' . $header->invoice . '</td>
            <td colspan="2" style="border: 1px solid black; font-weight: bold; text-align: right">' . $total . '</td>
        </tr>';

        // detail barang
        $detail   = $this->M_global->getDataResult('mutasi_detail', ['invoice' => $header->invoice]);

        $no       = 1;
        $tdiskon  = 0;
        $tpajak   = 0;
        $ttotal   = 0;
        foreach ($detail as $d) {
            $tdiskon    += $d->discrp;
            $tpajak     += $d->pajakrp;
            $ttotal     += $d->jumlah;

            if ($param == 1) {
                $harga    = number_format($d->harga);
                $qty      = number_format($d->qty);
                $discpr   = number_format($d->discpr);
                $discrp   = number_format($d->discrp);
                $pajak    = number_format($d->pajakrp);
                $jumlah   = number_format($d->jumlah);

                $tdiskonx = number_format($tdiskon);
                $tpajakx  = number_format($tpajak);
                $ttotalx  = number_format($ttotal);
            } else {
                $harga    = ceil($d->harga);
                $qty      = ceil($d->qty);
                $discpr   = ceil($d->discpr);
                $discrp   = ceil($d->discrp);
                $pajak    = ceil($d->pajakrp);
                $jumlah   = ceil($d->jumlah);

                $tdiskonx = ceil($tdiskon);
                $tpajakx  = ceil($tpajak);
                $ttotalx  = ceil($ttotal);
            }

            $body .= '<tr>
                <td style="border: 1px solid black;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $d->kode_barang . ' ~ ' . $this->M_global->getData('barang', ['kode_barang' => $d->kode_barang])->nama . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $this->M_global->getData('m_satuan', ['kode_satuan' => $d->kode_satuan])->keterangan . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $harga . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $qty . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $discpr . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $discrp . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $pajak . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $jumlah . '</td>
            </tr>';
            $no++;
        }

        $body .= '<tr style="background-color: green;">
            <td colspan="6" style="border: 1px solid black; font-weight: bold; color: white;">Total</td>
            <td style="border: 1px solid black; font-weight: bold; color: white; text-align: right">' . $tdiskonx . '</td>
            <td style="border: 1px solid black; font-weight: bold; color: white; text-align: right">' . $tpajakx . '</td>
            <td style="border: 1px solid black; font-weight: bold; color: white; text-align: right">' . $ttotalx . '</td>
        </tr>';

        $body .= '</tbody>';

        $body .= '<tfoot>
            <tr>
                <td colspan="6">&nbsp;</td>
                <td colspan="3" style="text-align: center;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" style="width:60%;">&nbsp;</td>
                <td colspan="3" style="width:40%; text-align: center;">Yogyakarta, ' . strtotime(date('Y-m-d')) . '</td>
            </tr>
            <tr>
                <td colspan="6" style="width:60%;">&nbsp;</td>
                <td colspan="3" style="width:40%; text-align: center;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" style="width:60%;">&nbsp;</td>
                <td colspan="3" style="width:40%; text-align: center;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" style="width:60%;">&nbsp;</td>
                <td colspan="3" style="width:40%; text-align: center;">' . $pencetak . '</td>
            </tr>
        </tfoot>';

        $body .= '</table>';

        $judul = $invoice;
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting, $yes);
    }

    // fungsi hapus barang  in
    public function delMutasi($invoice)
    {
        aktifitas_user_transaksi('Mutasi', 'menghapus Penerimaan', $invoice);

        // jalankan fungsi cek
        $cek = [
            $this->M_global->delData(
                'mutasi_detail',
                ['invoice' => $invoice]
            ), // del data detail mutasi
            $this->M_global->delData(
                'mutasi_header',
                ['invoice' => $invoice]
            ), // del data header mutasi
        ];

        if ($cek) { // jika fungsi cek berjalan
            // kirim status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirim status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi acc/re-acc
    public function accmutasi($invoice, $acc)
    {
        $header         = $this->M_global->getData('mutasi_header', ['invoice' => $invoice]);
        $detail         = $this->M_global->getDataResult('mutasi_detail', ['invoice' => $invoice]);

        $jenis          = $header->jenis;
        $kode_cabang    = $header->kode_cabang;

        if ($acc == 0) { // jika acc = 0
            aktifitas_user_transaksi('Mutasi', 'Reject', $invoice);

            // update is_approve jadi 0
            $cek = $this->M_global->updateData('mutasi_header', ['status' => 0, 'tgl_approve' => null, 'jam_approve' => null], ['invoice' => $invoice]);

            if ($jenis == 0) { // mutasi gudang
                foreach ($detail as $d) {
                    mutasi_gudang_rjt($kode_cabang, $header->dari, $header->menuju, $d->kode_barang, $d->qty_konversi, $header->tgl, $header->jam, $header->invoice, $header->user);
                }
            } else { // mutasi cabang
                foreach ($detail as $d) {
                    mutasi_cabang_rjt($kode_cabang, $header->dari, $header->menuju, $d->kode_barang, $d->qty_konversi, $header->tgl, $header->jam, $header->invoice, $header->user);
                }
            }
        } else { // selain itu
            aktifitas_user_transaksi('Mutasi', 'Confirm', $invoice);

            // update is_approve jadi 1
            $cek = $this->M_global->updateData('mutasi_header', ['status' => 1, 'tgl_approve' => date('Y-m-d'), 'jam_approve' => date('H:i:s'), 'user_approve' => $this->session->userdata('kode_user')], ['invoice' => $invoice]);

            if ($jenis == 0) { // mutasi gudang
                foreach ($detail as $d) {
                    mutasi_gudang_acc($kode_cabang, $header->dari, $header->menuju, $d->kode_barang, $d->qty_konversi, $header->tgl, $header->jam, $header->invoice, $header->user);
                }
            } else { // mutasi cabang
                foreach ($detail as $d) {
                    mutasi_cabang_acc($kode_cabang, $header->dari, $header->menuju, $d->kode_barang, $d->qty_konversi, $header->tgl, $header->jam, $header->invoice, $header->user);
                }
            }
        }

        if ($cek) { // jika fungsi cek berjalan
            // kirim status 1 ke view
            echo json_encode(['status' => 1]);
        } else { // selain itu
            // kirim status 0 ke view
            echo json_encode(['status' => 0]);
        }
    }

    // fungsi sinkronisasi barang stok
    public function sinkron()
    {
        // ambil parameter
        $cabang = $this->session->userdata('kode_cabang');
        $riwayat_stok = $this->M_global->getDataResult('barang_stok', ['kode_cabang' => $cabang]);

        // proses sinkron
        foreach ($riwayat_stok as $rs) {
            $gudang = $rs->kode_gudang;
            $barang = $rs->kode_barang;

            $stok = $this->M_global->stokBarang($cabang, $gudang, $barang);

            foreach ($stok as $s) {
                $this->db->query(
                    "UPDATE barang_stok SET masuk = $s->qty_in, keluar = $s->qty_out, akhir = ($s->qty_in - $s->qty_out) WHERE kode_cabang = '$cabang' AND kode_gudang = '$gudang' AND kode_barang = '$barang'"
                );
            }
        }

        echo json_encode(['status' => 1]);
    }
}
