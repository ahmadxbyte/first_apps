<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Emr extends CI_Controller
{
    // variable open public untuk controller Home
    public $data;

    public function __construct()
    {
        parent::__construct();
        // load model M_auth
        $this->load->model("M_auth");
        $this->load->model("M_Emr");

        if (!empty($this->session->userdata("email"))) { // jika session email masih ada

            $id_menu = $this->M_global->getData('m_menu', ['url' => 'Emr'])->id;

            // ambil isi data berdasarkan email session dari table user, kemudian tampung ke variable $user
            $user = $this->M_global->getData("user", ["email" => $this->session->userdata("email")]);

            $cek_akses_menu = $this->M_global->getData('akses_menu', ['id_menu' => $id_menu, 'kode_role' => $user->kode_role]);
            if ($cek_akses_menu) {
                // tampung data ke variable data public
                $this->data = [
                    'kode_user' => $user->kode_user,
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

    public function index()
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $parameter = [
            $this->data,
            'judul'         => 'Electrical Medical Record',
            'nama_apps'     => $web_setting->nama,
            'page'          => 'EMR Rawat Jalan',
            'web'           => $web_setting,
            'web_version'   => $web_version->version,
            'list_data'     => '',
            'param1'        => '',
        ];

        $this->template->load('Template/Content', 'Emr/Daftar', $parameter);
    }

    // fungsi list daftar
    public function daftar_list($param1)
    {
        // Parameter untuk list table
        $kode_poli    = $this->input->get('kode_poli');
        $kode_dokter  = $this->input->get('kode_dokter');

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

        $list         = $this->M_Emr->get_datatables($dari, $sampai, $kode_poli, $kode_dokter, $tipe);

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
                if (
                    $rd->status_trx == 2
                ) {
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

            $cek_per = $this->M_global->getData('emr_per', ['no_trx' => $rd->no_trx]);
            if ($cek_per) {
                $status_per = '<span class="badge badge-sm badge-info">Perawat&nbsp;&nbsp;<i class="fa fa-circle-check"></i></span>';
            } else {
                $status_per = '';
            }

            $cek_dok = $this->M_global->getData('emr_dok', ['no_trx' => $rd->no_trx]);
            if ($cek_dok) {
                $status_dok = '<span class="badge badge-sm badge-success">Dokter&nbsp;&nbsp;<i class="fa fa-circle-check"></i></span>';
                if ($cek_dok->status_lanjut == 0) {
                    $status_dok2 = '<span class="badge badge-sm badge-success">Pulang</span>';
                } else if ($cek_dok->status_lanjut == 1) {
                    $status_dok2 = '<span class="badge badge-sm badge-info">Kontrol</span>';
                } else if ($cek_dok->status_lanjut == 2) {
                    $status_dok2 = '<span class="badge badge-sm badge-danger">Rawat Inap</span>';
                } else {
                    $status_dok2 = '<span class="badge badge-sm badge-primary">Rujuk</span>';
                }
            } else {
                $status_dok = '';
                $status_dok2 = '';
            }

            $row = [];
            $row[] = $no++;
            $row[] = $rd->no_trx . '<br><span class="badge badge-dark badge-sm">' . $rd->jenis_bayar . '</span> ' . (($rd->status_trx == 0) ? '<span class="badge badge-sm badge-success">Buka</span>' : (($rd->status_trx == 2) ? '<span class="badge badge-sm badge-danger">Batal</span>' : '<span class="badge badge-sm badge-primary">Selesai</span>')) . '<br>' . $status_per . ' ' . $status_dok . $status_dok2;
            $row[] = 'No. RM: <span class="float-right">' . $rd->kode_member . '</span><hr>Nama: <span class="float-right">' . $this->M_global->getData('member', ['kode_member' => $rd->kode_member])->nama . '</span>';
            $row[] = 'D: <span class="float-right">' . date('d/m/Y', strtotime($rd->tgl_daftar)) . ' ~ ' . date('H:i:s', strtotime($rd->jam_daftar)) . '</span><br>' .
                '<hr>S: <span class="float-right">' . (($rd->status_trx < 1) ? '<i class="text-secondary">Null</i>' : (($rd->tgl_keluar == null) ? 'xx/xx/xxxx' : date('d/m/Y', strtotime($rd->tgl_keluar))) . ' ~ ' . (($rd->jam_keluar == null) ? 'xx:xx:xx' : date('H:i:s', strtotime($rd->jam_keluar)))) . '</span>';
            $row[] = 'Dr. ' . $this->M_global->getData('dokter', ['kode_dokter' => $rd->kode_dokter])->nama . (($rd->verifikasi == 0) ? '<span class="badge badge-warning badge-sm float-right" title="Belum Verifikasi">CPPT&nbsp;&nbsp;<i class="fa fa-info-circle"></i></span>' : '<span class="badge badge-success badge-sm float-right" title="Sudah Verifikasi">CPPT&nbsp;&nbsp;<i class="fa fa-circle-check"></i></span>') . '<hr>(Poli: ' . $this->M_global->getData('m_poli', ['kode_poli' => $rd->kode_poli])->keterangan . ')';
            $perawat = $this->M_global->getData('emr_per', ['no_trx' => $rd->no_trx]);
            $row[] = ((!empty($perawat) ? $this->M_global->getData('user', ['kode_user' => $perawat->kode_user])->nama : '-'));
            $row[] = (($rd->kode_ruang == null) ? '' : ' (' . $this->M_global->getData('m_ruang', ['kode_ruang' => $rd->kode_ruang])->keterangan . ')</span>') . '<hr><span class="float-right">' . $rd->no_antrian . '</span>';

            if ($rd->status_trx == 2) {
                $disabled = 'disabled';
                $disabled2 = 'disabled';
            } else {
                $emr_dok = $this->M_global->getData('emr_dok', ['no_trx' => $rd->no_trx]);
                if ($emr_dok) {
                    $disabled = '';
                } else {
                    $disabled = 'disabled';
                }

                if ($rd->kode_dokter == $this->data['kode_user']) {
                    $disabled2 = 'onclick="getUrl(' . "'" . "Emr/dokter/" . $rd->no_trx . "'" . ')"';
                } else {
                    $disabled2 = 'onclick="getUrl(' . "'" . "Emr/perawat/" . $rd->no_trx . "'" . ')"';
                }
            }

            if ($this->session->userdata('kode_role') == 'R0001') {
                $button = '<button ' . $disabled2 . ' type="button" style="margin-bottom: 5px; margin-right: 5px; width: 49%;" class="btn btn-success" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Tooltip on top" title="Perawat" onclick="getUrl(' . "'" . "Emr/perawat/" . $rd->no_trx . "'" . ')"><i class="fa-solid fa-user-nurse"></i> Perawat</button>
                <button ' . ((!empty($this->M_global->getData('emr_per', ['no_trx' => $rd->no_trx]))) ? '' : 'disabled') . ' type="button" style="margin-bottom: 5px; width: 49%;" class="btn btn-primary" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Tooltip on top" title="Dokter" onclick="getUrl(' . "'" . "Emr/dokter/" . $rd->no_trx . "'" . ')"><i class="fa-solid fa-user-doctor"></i> Dokter</button>';
            } else {
                if ($rd->kode_dokter == $this->data['kode_user']) {
                    $button = '<button ' . $disabled2 . ' type="button" style="margin-bottom: 5px; width: 100%;" class="btn btn-primary" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Tooltip on top" title="Dokter"><i class="fa-solid fa-user-doctor"></i> Dokter</button>';
                } else {
                    $button = '<button ' . $disabled2 . ' type="button" style="margin-bottom: 5px; width: 100%;" class="btn btn-success" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Tooltip on top" title="Perawat"><i class="fa-solid fa-user-nurse"></i> Perawat</button>';
                }
            }

            if ($rd->status_trx == 2) {
                $panggilan = '<button type="button" class="btn btn-info w-100" style="margin-bottom: 5px;" disabled><i class="fa-solid fa-volume-high"></i> Panggil</button>';
            } else {
                if ($rd->status_trx == 0) {
                    $panggilan = '<button type="button" class="btn btn-info w-100" style="margin-bottom: 5px;" onclick="panggil_pasien(' . "'" . $rd->no_trx . "','" . $rd->no_antrian . "', '" . $this->M_global->getData('m_ruang', ['kode_ruang' => $rd->kode_ruang])->keterangan . "'" . ')"><i class="fa-solid fa-volume-high"></i> Panggil</button>';
                } else {
                    $panggilan = '<button type="button" class="btn btn-info w-100" style="margin-bottom: 5px;" disabled><i class="fa-solid fa-volume-high"></i> Panggil</button>';
                }
            }

            // 52599

            $row[] = '<div class="">
                <div class="w-100 d-flex">
                    ' . $button . '
                </div>
                ' . $panggilan . '
                <div class="btn-group dropstart w-100" style="margin-bottom: 5px;">
                    <button type="button" class="btn btn-warning dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" ' . $disabled . '>
                        <i class="fa-solid fa-envelope-open-text"></i> Surat
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" onclick="buatSurat(' . "'" . $rd->no_trx . "', 'Surat Keterangan Sakit', '1', 'Suket_sakit_'" . ')" ' . $disabled . ' type="button">Surat Keterangan Sakit</a></li>
                        <li><a class="dropdown-item" onclick="buatSurat(' . "'" . $rd->no_trx . "', 'Surat Keterangan Dokter', '2', 'Suket_dokter_'" . ')" ' . $disabled . ' type="button">Surat Keterangan Dokter</a></li>
                        <li><a class="dropdown-item" onclick="buatSurat(' . "'" . $rd->no_trx . "', 'Surat Keterangan Diagnosa', '3', 'Suket_diagnosa_'" . ')" ' . $disabled . ' type="button">Surat Keterangan Diagnosa</a></li>
                        <li><a class="dropdown-item" onclick="buatSurat(' . "'" . $rd->no_trx . "', 'Surat Keterangan Perawatan', '4', 'Suket_dalam_perawatan_'" . ')" ' . $disabled . ' type="button">Surat Keterangan Perawatan</a></li>
                        <li><a target="_blank" class="dropdown-item" onclick="buatSurat(' . "'" . $rd->no_trx . "', 'Resume Medis', '5', 'Suket_resume_'" . ')" ' . $disabled . ' type="button">Resume Medis</a></li>
                        <li><a target="_blank" href="' . site_url('Emr/spri/') . $rd->no_trx . '" class="dropdown-item" type="button">SPRI</a></li>
                    </ul>
                </div>
            </div>';

            $data[] = $row;
        }

        // Hasil server side
        $output = [
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->M_Emr->count_all($dari, $sampai, $kode_poli, $kode_dokter, $tipe),
            "recordsFiltered" => $this->M_Emr->count_filtered($dari, $sampai, $kode_poli, $kode_dokter, $tipe),
            "data" => $data,
        ];

        // Kirimkan ke view
        echo json_encode($output);
    }

    // get Data Doc PX
    public function getDataDoc($no_trx, $ket)
    {
        $cek = $this->M_global->getData('doc_px', ['no_trx' => $no_trx, 'judul_surat LIKE' => '%' . $ket . '%']);

        if ($cek) {
            echo json_encode(['status' => 1, 'no_surat' => $cek->no_surat]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // cek apakah ada di emr dok
    public function checkDataDoc($no_trx)
    {
        $cek = $this->M_global->getData('emr_dok', ['no_trx' => $no_trx]);

        if ($cek) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    // cetak surat
    public function cetakSurat()
    {
        $no_surat = $this->input->get('no_surat');
        $cek = $this->M_global->getData('doc_px', ['no_surat' => $no_surat]);

        if ($cek) {
            $data = json_decode($cek->data, true);
            echo json_encode([['status' => 1], $data]);
        } else {
            echo json_encode([['status' => 0]]);
        }
    }

    // panggil px
    public function panggil($no_trx)
    {
        // Get registration data
        $pendaftaran = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);
        $kode_cabang = $pendaftaran->kode_cabang;
        $kode_poli = $pendaftaran->kode_poli;
        $last_panggil = $this->db->query('SELECT MAX(panggil) AS panggil FROM pendaftaran WHERE kode_cabang = "' . $kode_cabang . '" AND kode_poli = "' . $kode_poli . '" ORDER BY panggil DESC LIMIT 1')->row();

        if (!$pendaftaran) {
            echo json_encode(['status' => 0, 'message' => 'Registration data not found']);
            return;
        }

        // Get current call number and increment
        $current_call = $last_panggil->panggil ?? 0;
        $next_call = $current_call + 1;

        $this->M_global->updateData(
            'pendaftaran',
            ['panggil' => $next_call, 'p_ulang' => 1],
            ['no_trx' => $no_trx]
        );

        echo json_encode(['status' => 1]);
    }

    // fungsi cetak resume medis
    public function resume_medis($no_trx)
    {
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);
        $emr_dok        = $this->M_global->getData('emr_dok', ['no_trx' => $no_trx]);
        $emr_per        = $this->M_global->getData('emr_per', ['no_trx' => $no_trx]);
        $emr_tarif      = $this->M_global->getDataResult('emr_tarif', ['no_trx' => $no_trx]);
        $emr_lab        = $this->M_global->getDataResult('emr_lab', ['no_trx' => $no_trx]);
        $emr_rad        = $this->M_global->getDataResult('emr_rad', ['no_trx' => $no_trx]);
        $emr_per_barang = $this->M_global->getDataResult('emr_per_barang', ['no_trx' => $no_trx]);
        $pendaftaran    = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);
        $member         = $this->M_global->getData('member', ['kode_member' => $pendaftaran->kode_member]);

        $prov   = $this->M_global->getData('m_provinsi', ['kode_provinsi' => $member->provinsi])->provinsi;
        $kab    = $this->M_global->getData('kabupaten', ['kode_kabupaten' => $member->kabupaten])->kabupaten;
        $kec    = $this->M_global->getData('kecamatan', ['kode_kecamatan' => $member->kecamatan])->kecamatan;

        $judul          = 'Suket_resume_' . $no_trx;
        $filename       = $judul;
        $no_surat       = nosurat('Suket_resume_');
        $terapi         = '';
        $body           = '';
        $body           .= '<br><br>';

        if (count($emr_tarif) > 0) {
            $terapi .= 'Tindakan: ';
            foreach ($emr_tarif as $key => $et) {
                $terapi .= $this->M_global->getData('m_tindakan', ['kode_tindakan' => $et->kode_tarif])->keterangan;
                if ($key < count($emr_tarif) - 1) {
                    $terapi .= ', ';
                }
            }
            $terapi .= '<br>';
        }

        if (count($emr_lab) > 0) {
            $terapi .= 'Laboratorium: ';
            foreach ($emr_lab as $key => $et) {
                $terapi .= $this->M_global->getData('m_tindakan', ['kode_tindakan' => $et->kode_tarif])->keterangan;
                if ($key < count($emr_lab) - 1) {
                    $terapi .= ', ';
                }
            }
            $terapi .= '<br>';
        }

        if (count($emr_rad) > 0) {
            $terapi .= 'Radiologi: ';
            foreach ($emr_rad as $key => $et) {
                $terapi .= $this->M_global->getData('m_tindakan', ['kode_tindakan' => $et->kode_tarif])->keterangan;
                if ($key < count($emr_rad) - 1) {
                    $terapi .= ', ';
                }
            }
            $terapi .= '<br>';
        }

        if (count($emr_per_barang) > 0) {
            $terapi .= 'Obat: ';
            foreach ($emr_per_barang as $key => $et) {
                $terapi .= $this->M_global->getData('barang', ['kode_barang' => $et->kode_barang])->nama . ' @' . $et->qty . (($et->signa == '') ? '' : ' (' . $et->signa . ')');
                if ($key < count($emr_per_barang) - 1) {
                    $terapi .= ', ';
                }
            }
            $terapi .= '<br>';
        }

        $body .= '<div class="row">
            <div class="col-md-12" style="text-align: center; margin-top: 10px; font-size: 12px; font-weight: bold;"><u>RESUME MEDIS</u></div>
            <div class="col-md-12" style="text-align: center; margin-bottom: 10px; font-size: 7px;">' . $no_surat . '</div>
        </div>';

        $body .= '<table style="text-align: left; vertical-align: top;">';

        $body .= '<tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3">Menerangkan bahwa:</td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td style="width: 15%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . $member->nama . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Gender</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . (($member->jkel == 'P') ? 'Laki-laki' : 'Perempuan') . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Lahir</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . $member->tmp_lahir . ', ' . date('d-m-Y', strtotime($member->tgl_lahir)) . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Umur</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . hitung_umur($member->tgl_lahir) . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Pekerjaan</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . $this->M_global->getData('m_pekerjaan', ['kode_pekerjaan' => $member->pekerjaan])->keterangan . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Alamat</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">Prov: ' . $prov . ', Kab: ' . $kab . ', Kec: ' . $kec . '<br>Desa: ' . $member->desa . ', rt/rw: ' . $member->rt . '/' . $member->rw . ' (' . $member->kodepos . ')' . '</td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td style="width: 15%;">Anamnesa</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . $emr_dok->anamnesa_dok . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Fisis</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">Tekanan Darah: ' . (($emr_per->tekanan_darah == '') ? '-' : $emr_per->tekanan_darah) . ' mmHg, Nadi: ' . (($emr_per->nadi == '') ? '-' : $emr_per->nadi) . ' x/mnt, Suhu: ' . (($emr_per->suhu == '') ? '-' : $emr_per->suhu) . ' °c, Berat Badan: ' . (($emr_per->bb == '') ? '-' : $emr_per->bb) . ' Kg, <br>Tinggi Badan: ' . (($emr_per->tb == '') ? '-' : $emr_per->tb) . ' cm, Pernapasan: ' . (($emr_per->pernapasan == '') ? '-' : $emr_per->pernapasan) . ' x/mnt, Saturasi O2: ' . (($emr_per->saturasi == '') ? '-' : $emr_per->saturasi) . ' %, Gizi: ' . (($emr_per->gizi == '') ? '-' : (($emr_per->gizi == 0) ? 'Buruk' : (($emr_per->gizi == 1) ? 'Kurang' : (($emr_per->gizi == 2) ? 'Cukup' : 'Lebih')))) . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Diagnosa</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . $emr_dok->diagnosa_dok . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Terapi</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . $terapi . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Anjuran</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . $emr_dok->rencana_dok . '</td>
        </tr>
        ';

        $body .= '</table>';

        cetak_pdf_suket($judul, $body, 1, 'P', $filename, $web_setting, $pendaftaran->kode_dokter, $pendaftaran->kode_poli);
    }

    // fungsi cetak spri
    public function spri($no_trx)
    {
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);
        $emr_dok        = $this->M_global->getData('emr_dok', ['no_trx' => $no_trx]);
        $emr_per        = $this->M_global->getData('emr_per', ['no_trx' => $no_trx]);
        $pendaftaran    = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);
        $dokter         = $this->M_global->getData('dokter', ['kode_dokter' => $pendaftaran->kode_dokter]);
        $member         = $this->M_global->getData('member', ['kode_member' => $pendaftaran->kode_member]);

        $prov   = $this->M_global->getData('m_provinsi', ['kode_provinsi' => $member->provinsi])->provinsi;
        $kab    = $this->M_global->getData('kabupaten', ['kode_kabupaten' => $member->kabupaten])->kabupaten;
        $kec    = $this->M_global->getData('kecamatan', ['kode_kecamatan' => $member->kecamatan])->kecamatan;

        $judul          = 'Suket_spri_' . $no_trx;
        $filename       = $judul;
        $no_surat       = nosurat('Suket_spri_');
        $terapi         = '';
        $body           = '';
        $body           .= '<br><br>';

        $body .= '<div class="row">
            <div class="col-md-12" style="text-align: center; margin-top: 10px; font-size: 12px; font-weight: bold;"><u>SURAT PENGANTAR RAWAT INAP</u></div>
            <div class="col-md-12" style="text-align: center; margin-bottom: 10px; font-size: 7px;">' . $no_surat . '</div>
        </div>';

        $body .= '<table style="text-align: left; vertical-align: top;">';

        $body .= '<tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3">Menerangkan bahwa:</td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td style="width: 15%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . $member->nama . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Gender</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . (($member->jkel == 'P') ? 'Laki-laki' : 'Perempuan') . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Lahir</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . $member->tmp_lahir . ', ' . date('d-m-Y', strtotime($member->tgl_lahir)) . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Umur</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . hitung_umur($member->tgl_lahir) . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Pekerjaan</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . $this->M_global->getData('m_pekerjaan', ['kode_pekerjaan' => $member->pekerjaan])->keterangan . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Alamat</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">Prov: ' . $prov . ', Kab: ' . $kab . ', Kec: ' . $kec . '<br>Desa: ' . $member->desa . ', rt/rw: ' . $member->rt . '/' . $member->rw . ' (' . $member->kodepos . ')' . '</td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td style="width: 15%;">Anamnesa</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . $emr_dok->anamnesa_dok . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Fisis</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">Tekanan Darah: ' . (($emr_per->tekanan_darah == '') ? '-' : $emr_per->tekanan_darah) . ' mmHg, Nadi: ' . (($emr_per->nadi == '') ? '-' : $emr_per->nadi) . ' x/mnt, Suhu: ' . (($emr_per->suhu == '') ? '-' : $emr_per->suhu) . ' °c, Berat Badan: ' . (($emr_per->bb == '') ? '-' : $emr_per->bb) . ' Kg, <br>Tinggi Badan: ' . (($emr_per->tb == '') ? '-' : $emr_per->tb) . ' cm, Pernapasan: ' . (($emr_per->pernapasan == '') ? '-' : $emr_per->pernapasan) . ' x/mnt, Saturasi O2: ' . (($emr_per->saturasi == '') ? '-' : $emr_per->saturasi) . ' %, Gizi: ' . (($emr_per->gizi == '') ? '-' : (($emr_per->gizi == 0) ? 'Buruk' : (($emr_per->gizi == 1) ? 'Kurang' : (($emr_per->gizi == 2) ? 'Cukup' : 'Lebih')))) . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Diagnosa</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . $emr_dok->diagnosa_dok . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">DPJP</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">Dr. ' . $dokter->nama . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Tgl Masuk</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . tgl_indo($pendaftaran->tgl_daftar) . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Jam Masuk</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . $pendaftaran->jam_daftar . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Alasan Ranap</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . $emr_dok->alasan_ranap . '</td>
        </tr>
        ';

        $body .= '</table>';

        cetak_pdf($judul, $body, 1, 'P', $filename, $web_setting);
    }

    // fungsi cetak suket_sakit
    function suket_sakit($no_trx)
    {
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);
        $dari           = $this->input->get('dari');
        $sampai         = $this->input->get('sampai');

        if (($dari == '' || $dari == null) && ($sampai == '' || $sampai == null)) {
            $jarak = '(.....)';
        } else {
            $jarak = hitung_jarak_hari(date('Y-m-d', strtotime($dari)), date('Y-m-d', strtotime($sampai)));
        }

        $data_doc       = [
            'dari'      => $dari,
            'sampai'    => $sampai,
            'jarak'     => $jarak,
        ];

        if ($dari != '' || $dari != null) {
            $dari = tgl_indo($dari);
        } else {
            $dari = '(....................)';
        }

        if ($sampai != '' || $sampai != null) {
            $sampai = tgl_indo($sampai);
        } else {
            $sampai = '(....................)';
        }

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $emr_dok        = $this->M_global->getData('emr_dok', ['no_trx' => $no_trx]);
        $pendaftaran    = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);
        $pencetak       = $this->M_global->getData('user', ['kode_user' => $this->session->userdata('kode_user')])->nama;

        $member = $this->M_global->getData('member', ['kode_member' => $emr_dok->kode_member]);

        $prov   = $this->M_global->getData('m_provinsi', ['kode_provinsi' => $member->provinsi])->provinsi;
        $kab    = $this->M_global->getData('kabupaten', ['kode_kabupaten' => $member->kabupaten])->kabupaten;
        $kec    = $this->M_global->getData('kecamatan', ['kode_kecamatan' => $member->kecamatan])->kecamatan;

        $judul = 'Suket_sakit_' . $no_trx;
        $filename = $judul;
        $no_surat = nosurat('Suket_sakit_');

        $body .= '<div class="row">
            <div class="col-md-12" style="text-align: center; margin-top: 10px; font-size: 12px; font-weight: bold;"><u>SURAT KETERANGAN SAKIT</u></div>
            <div class="col-md-12" style="text-align: center; margin-bottom: 10px; font-size: 7px;">' . $no_surat . '</div>
        </div>';

        $body .= '<table style="text-align: left; vertical-align: top;">';

        $body .= '<tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3">Menerangkan bahwa:</td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td style="width: 15%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . $member->nama . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Gender</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . (($member->jkel == 'P') ? 'Laki-laki' : 'Perempuan') . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Lahir</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . $member->tmp_lahir . ', ' . date('d-m-Y', strtotime($member->tgl_lahir)) . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Umur</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . hitung_umur($member->tgl_lahir) . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Pekerjaan</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . $this->M_global->getData('m_pekerjaan', ['kode_pekerjaan' => $member->pekerjaan])->keterangan . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Alamat</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">Prov: ' . $prov . ', Kab: ' . $kab . ', Kec: ' . $kec . '<br>Desa: ' . $member->desa . ', rt/rw: ' . $member->rt . '/' . $member->rw . ' (' . $member->kodepos . ')' . '</td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3">Setelah diperiksa kesehatannya, ternyata pada saat ini dalam keadaan <b>SAKIT</b> dan memerlukan <b>istirahat selama ' . $jarak . ' Hari</b></td>
        </tr>
        <tr>
            <td colspan="3">Terhitung tanggal ' . $dari . ' s.d ' . $sampai . '</td>
        </tr>
        <tr>
            <td colspan="3">Demikian surat keterangan ini untuk dapat dipergunakan seperlunya</td>
        </tr>';

        $body .= '</table>';

        cetak_pdf_suket($judul, $body, 1, $position, $filename, $web_setting, $emr_dok->kode_user, $pendaftaran->kode_poli);

        doc_px($no_trx, $judul, $no_surat, json_encode($data_doc));
    }

    // fungsi cetak suket_dokter
    function suket_dokter($no_trx)
    {
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);
        $dari           = $this->input->get('dari');
        $sampai         = $this->input->get('sampai');

        if (($dari == '' || $dari == null) && ($sampai == '' || $sampai == null)) {
            $jarak = '(.....)';
        } else {
            $jarak = hitung_jarak_hari(date('Y-m-d', strtotime($dari)), date('Y-m-d', strtotime($sampai)));
        }

        $data_doc       = [
            'dari'      => $dari,
            'sampai'    => $sampai,
            'jarak'     => $jarak,
        ];

        if ($dari != '' || $dari != null) {
            $dari = tgl_indo($dari);
        } else {
            $dari = '(....................)';
        }

        if ($sampai != '' || $sampai != null) {
            $sampai = tgl_indo($sampai);
        } else {
            $sampai = '(....................)';
        }

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $emr_dok        = $this->M_global->getData('emr_dok', ['no_trx' => $no_trx]);
        $pendaftaran    = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);
        $pencetak       = $this->M_global->getData('user', ['kode_user' => $this->session->userdata('kode_user')])->nama;

        $member = $this->M_global->getData('member', ['kode_member' => $emr_dok->kode_member]);

        $prov   = $this->M_global->getData('m_provinsi', ['kode_provinsi' => $member->provinsi])->provinsi;
        $kab    = $this->M_global->getData('kabupaten', ['kode_kabupaten' => $member->kabupaten])->kabupaten;
        $kec    = $this->M_global->getData('kecamatan', ['kode_kecamatan' => $member->kecamatan])->kecamatan;

        $judul = 'Suket_dokter_' . $no_trx;
        $filename = $judul;
        $no_surat = nosurat('Suket_dokter_');

        $body .= '<div class="row">
            <div class="col-md-12" style="text-align: center; margin-top: 10px; font-size: 12px; font-weight: bold;"><u>SURAT KETERANGAN DOKTER</u></div>
            <div class="col-md-12" style="text-align: center; margin-bottom: 10px; font-size: 7px;">' . $no_surat . '</div>
        </div>';

        $body .= '<table style="text-align: left; vertical-align: top;">';

        $body .= '<tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3">Yang bertanda tangan dibawah ini:</td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td style="width: 15%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . $member->nama . ' (' . (($member->jkel == 'P') ? 'Laki-laki' : 'Perempuan') . ')' . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Umur</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . hitung_umur($member->tgl_lahir) . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Pekerjaan</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . $this->M_global->getData('m_pekerjaan', ['kode_pekerjaan' => $member->pekerjaan])->keterangan . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Alamat</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">Prov: ' . $prov . ', Kab: ' . $kab . ', Kec: ' . $kec . '<br>Desa: ' . $member->desa . ', rt/rw: ' . $member->rt . '/' . $member->rw . ' (' . $member->kodepos . ')' . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">No Hp</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . $member->nohp . '</td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3">Membutuhkan <b>istirahat selama ' . $jarak . ' Hari</b></td>
        </tr>
        <tr>
            <td colspan="3">Terhitung tanggal ' . $dari . ' s.d ' . $sampai . '</td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3">Diagnosa: ' . (($emr_dok->diagnosa_dok != '') ? $emr_dok->diagnosa_dok : '-') . '</td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3">Demikian surat keterangan sakit ini diberikan untuk digunakan sebagai mana mestinya</td>
        </tr>';

        $body .= '</table>';

        cetak_pdf_suket($judul, $body, 1, $position, $filename, $web_setting, $emr_dok->kode_user, $pendaftaran->kode_poli);

        doc_px($no_trx, $judul, $no_surat, json_encode($data_doc));
    }

    // fungsi cetak suket_diagnosa
    function suket_diagnosa($no_trx)
    {
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $emr_dok        = $this->M_global->getData('emr_dok', ['no_trx' => $no_trx]);
        $emr_per        = $this->M_global->getData('emr_per', ['no_trx' => $no_trx]);
        $pendaftaran    = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);
        $dokter         = $this->M_global->getData('dokter', ['kode_dokter' => $emr_dok->kode_user]);
        if ($dokter) {
            $pencetak = $dokter;
        } else {
            $pencetak = $this->M_global->getData('user', ['kode_user' => $emr_dok->kode_user]);
        }

        $member = $this->M_global->getData('member', ['kode_member' => $emr_dok->kode_member]);

        $prov   = $this->M_global->getData('m_provinsi', ['kode_provinsi' => $member->provinsi])->provinsi;
        $kab    = $this->M_global->getData('kabupaten', ['kode_kabupaten' => $member->kabupaten])->kabupaten;
        $kec    = $this->M_global->getData('kecamatan', ['kode_kecamatan' => $member->kecamatan])->kecamatan;

        $judul = 'Suket_diagnosa_' . $no_trx;
        $filename = $judul;
        $no_surat = nosurat('Suket_diagnosa_');

        $body .= '<div class="row">
            <div class="col-md-12" style="text-align: center; margin-top: 10px; font-size: 12px; font-weight: bold;"><u>SURAT KETERANGAN DIAGNOSA</u></div>
            <div class="col-md-12" style="text-align: center; margin-bottom: 10px; font-size: 7px;">' . $no_surat . '</div>
        </div>';

        $body .= '<table style="text-align: left; vertical-align: top;">';

        $body .= '<tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3">Yang bertanda tangan dibawah ini adalah <b>Dr. ' . $pencetak->nama . '</b> dari <b>' . $web_setting->nama . '</b> menerangkan bahwa:</td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td style="width: 25%;">No RM</td>
            <td style="width: 2%;">:</td>
            <td style="width: 73%;">' . $member->kode_member . '</td>
        </tr>
        <tr>
            <td style="width: 25%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td style="width: 73%;">' . $member->nama . '</td>
        </tr>
        <tr>
            <td style="width: 25%;">Lahir</td>
            <td style="width: 2%;">:</td>
            <td style="width: 73%;">' . $member->tmp_lahir . ', ' . date('d-m-Y', strtotime($member->tgl_lahir)) . '</td>
        </tr>
        <tr>
            <td style="width: 25%;">Umur</td>
            <td style="width: 2%;">:</td>
            <td style="width: 73%;">' . hitung_umur($member->tgl_lahir) . '</td>
        </tr>
        <tr>
            <td style="width: 25%;">Jenis Kelamin</td>
            <td style="width: 2%;">:</td>
            <td style="width: 73%;">' . (($member->jkel == 'P') ? 'Laki-laki' : 'Perempuan') . '</td>
        </tr>
        <tr>
            <td style="width: 25%;">Berat Badan</td>
            <td style="width: 2%;">:</td>
            <td style="width: 73%;">' . $emr_per->bb . ' (kg)</td>
        </tr>
        <tr>
            <td style="width: 25%;">Tinggi Badan</td>
            <td style="width: 2%;">:</td>
            <td style="width: 73%;">' . $emr_per->tb . ' (cm)</td>
        </tr>
        <tr>
            <td style="width: 25%;">Alamat</td>
            <td style="width: 2%;">:</td>
            <td style="width: 73%;">Prov: ' . $prov . ', Kab: ' . $kab . ', Kec: ' . $kec . '<br>Desa: ' . $member->desa . ', rt/rw: ' . $member->rt . '/' . $member->rw . ' (' . $member->kodepos . ')' . '</td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3">Menerangkan bahwa yang bersangkutan sedang dalam keadaan <b>SAKIT</b> dengan diagnosa: (' . (($emr_dok->diagnosa_dok != '') ? $emr_dok->diagnosa_dok : '-') . ')</td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3">Demikian surat keterangan sakit ini dibuat dengan sebenar-benarnya agar dapat dipergunakan sebaik-baiknya</td>
        </tr>';

        $body .= '</table>';

        cetak_pdf_suket($judul, $body, 1, $position, $filename, $web_setting, $emr_dok->kode_user, $pendaftaran->kode_poli);

        doc_px($no_trx, $judul, $no_surat, json_encode($no_trx));
    }

    // fungsi cetak suket_dalam_perawatan
    function suket_dalam_perawatan($no_trx)
    {
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $emr_dok        = $this->M_global->getData('emr_dok', ['no_trx' => $no_trx]);
        $pendaftaran    = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);
        $pencetak       = $this->M_global->getData('user', ['kode_user' => $this->session->userdata('kode_user')])->nama;

        $member = $this->M_global->getData('member', ['kode_member' => $emr_dok->kode_member]);

        $prov   = $this->M_global->getData('m_provinsi', ['kode_provinsi' => $member->provinsi])->provinsi;
        $kab    = $this->M_global->getData('kabupaten', ['kode_kabupaten' => $member->kabupaten])->kabupaten;
        $kec    = $this->M_global->getData('kecamatan', ['kode_kecamatan' => $member->kecamatan])->kecamatan;

        $judul = 'Suket_dalam_perawatan_' . $no_trx;
        $filename = $judul;
        $no_surat = nosurat('Suket_dalam_perawatan_');

        $body .= '<div class="row">
            <div class="col-md-12" style="text-align: center; margin-top: 10px; font-size: 12px; font-weight: bold;"><u>SURAT KETERANGAN DALAM PERAWATAN</u></div>
            <div class="col-md-12" style="text-align: center; margin-bottom: 10px; font-size: 7px;">' . $no_surat . '</div>
        </div>';

        $body .= '<table style="text-align: left; vertical-align: top;">';

        $body .= '<tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3">Bersama ini, kami yang bertanda tangan dibawah ini menerangkan bahwa pasien dengan identitas sebagai berikut:</td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td style="width: 15%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . $member->nama . ' (' . (($member->jkel == 'P') ? 'Laki-laki' : 'Perempuan') . ')' . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Umur</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">' . hitung_umur($member->tgl_lahir) . '</td>
        </tr>
        <tr>
            <td style="width: 15%;">Alamat</td>
            <td style="width: 2%;">:</td>
            <td style="width: 83%;">Prov: ' . $prov . ', Kab: ' . $kab . ', Kec: ' . $kec . '<br>Desa: ' . $member->desa . ', rt/rw: ' . $member->rt . '/' . $member->rw . ' (' . $member->kodepos . ')' . '</td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3">Saat ini <b>sedang dalam perawatan oleh dokter ' . $this->M_global->getData('m_poli', ['kode_poli' => $pendaftaran->kode_poli])->keterangan . '</b>, dan saat ini tidak memungkinkan untuk melakukan perjalanan jarak jauh</td>
        </tr>
        <tr>
            <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="3">Demikian surat keterangan ini kami buat untuk dipergunakan sebagaimana mestinya</td>
        </tr>';

        $body .= '</table>';

        cetak_pdf_suket($judul, $body, 1, $position, $filename, $web_setting, $emr_dok->kode_user, $pendaftaran->kode_poli);

        doc_px($no_trx, $judul, $no_surat, json_encode($no_trx));
    }

    // get satuan
    public function getSatuan($kode_barang)
    {
        $barang = $this->M_global->getData('barang', ['kode_barang' => $kode_barang]);

        if (!$barang) {
            echo json_encode(['error' => 'Barang not found']);
            return;
        }

        $kode_satuan_keys = [
            'kode_satuan',
            'kode_satuan2',
            'kode_satuan3'
        ];

        $satuan = [];

        foreach ($kode_satuan_keys as $key) {
            $kode_satuan = $barang->$key;
            $nama_satuan = $kode_satuan ? $this->M_global->getData('m_satuan', ['kode_satuan' => $kode_satuan])->keterangan : $kode_satuan;

            $satuan[] = [
                'kode_satuan' => $kode_satuan,
                'nama_satuan' => $nama_satuan
            ];
        }

        echo json_encode($satuan);
    }

    // histori px
    public function histori_px($no_trx)
    {
        $kode_member = $this->input->get('kode_member');
        $kode_dokter = $this->input->get('kode_dokter');

        if ($kode_dokter == '' || $kode_dokter == null || $kode_dokter == 'null') {
            $where_dokter = '';
        } else {
            $where_dokter = ' AND kode_dokter = "' . $kode_dokter . '"';
        }

        $pendaftaran = $this->db->query('SELECT *, (@row_number := @row_number + 1) AS eps FROM pendaftaran, (SELECT @row_number := 0) AS init WHERE kode_member = "' . $kode_member . '" ' . $where_dokter . '  ORDER BY id DESC')->result();

        $no_his = count($pendaftaran);
        foreach ($pendaftaran as $p) : ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="card" style="background-color: <?= ($p->no_trx == $no_trx) ? '#272a3f !important; color: white !important;' : 'white !important; color: #272a3f !important' ?>;">
                        <div class="card-header">
                            <span class="h5"><span>Kunj : <?= $no_his ?> </span><?= ($p->tipe_daftar == 1) ? '<span class="badge badge-sm badge-danger float-right">Rawat Jalan</span>' : '<span class="badge badge-sm badge-warning float-right">Rawat Inap</span>' ?></span>
                            <br>
                            <span style="font-size: 14px;"><?= (($p->status_trx == 0) ? '<span class="badge badge-sm badge-success">Buka</span>' : (($p->status_trx == 2) ? '<span class="badge badge-sm badge-danger">Batal</span>' : '<span class="badge badge-sm badge-primary">Selesai</span>')) ?></span>
                            <span style="font-size: 14px;" class="badge badge-dark badge-sm float-right"><?= $this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $p->kode_jenis_bayar])->keterangan ?></span>
                        </div>
                        <div class="card-footer text-center">
                            <button type="button" class="btn btn-sm btn-info" style="width: 49%;" <?= (($p->status_trx == 2) ? 'disabled' : '') ?> onclick="show_his('<?= $p->no_trx ?>', '<?= $no_his ?>', '<?= $p->kode_member ?>', '<?= $p->id ?>')">Perawat</button>
                            <button type="button" class="btn btn-sm btn-primary" style="width: 49%;" <?= (($p->status_trx == 2) ? 'disabled' : '') ?> onclick="show_his2('<?= $p->no_trx ?>', '<?= $no_his ?>', '<?= $p->kode_member ?>', '<?= $p->id ?>')">Dokter</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table style="font-size: 14px;">
                                    <tr>
                                        <td>Daftar</td>
                                        <td> : </td>
                                        <td><?= date('d M y', strtotime($p->tgl_daftar)) ?> / <?= date('H:i', strtotime($p->jam_daftar)) ?></td>
                                    </tr>
                                    <tr>
                                        <td>Pulang</td>
                                        <td> : </td>
                                        <td><?= (!$p->tgl_keluar) ? 'xx-xx-xxxx' : date('d M y', strtotime($p->tgl_keluar)) ?> / <?= (!$p->jam_keluar) ? 'xx:xx' : date('H:i', strtotime($p->jam_keluar)) ?></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 30%;">Dokter</td>
                                        <td style="width: 5%;"> : </td>
                                        <td style="width: 65%;">Dr. <?= $this->M_global->getData('dokter', ['kode_dokter' => $p->kode_dokter])->nama ?></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 30%;">Poli</td>
                                        <td style="width: 5%;"> : </td>
                                        <td style="width: 65%;"><?= $this->M_global->getData('m_poli', ['kode_poli' => $p->kode_poli])->keterangan . ' (' . $this->M_global->getData('m_ruang', ['kode_ruang' => $p->kode_ruang])->keterangan . ')' ?></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 30%;">Cabang</td>
                                        <td style="width: 5%;"> : </td>
                                        <td style="width: 65%;"><?= $this->M_global->getData('cabang', ['kode_cabang' => $p->kode_cabang])->cabang ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php $no_his--;
        endforeach;
    }

    // histori kunjungan px
    public function his_px($no_trx, $eps, $kode_member)
    {
        $web            = $this->M_global->getData('web_setting', ['id' => 1]);
        $pendaftaran    = $this->db->query('SELECT *, (@row_number := @row_number + 1) AS eps FROM pendaftaran, (SELECT @row_number := 0) AS init WHERE no_trx = "' . $no_trx . '"  ORDER BY id DESC')->result();

        $member         = $this->M_global->getData('member', ['kode_member' => $kode_member]);

        $prov           = $this->M_global->getData('m_provinsi', ['kode_provinsi' => $member->provinsi])->provinsi;
        $kab            = $this->M_global->getData('kabupaten', ['kode_kabupaten' => $member->kabupaten])->kabupaten;
        $kec            = $this->M_global->getData('kecamatan', ['kode_kecamatan' => $member->kecamatan])->kecamatan;

        $alamat         = 'Prov. ' . $prov . ', ' . $kab . ', Kec. ' . $kec . ', Ds. ' . $member->desa . ', (POS: ' . $member->kodepos . '), RT.' . $member->rt . '/RW.' . $member->rw;

        $emr_per        = $this->M_global->getData('emr_per', ['no_trx' => $no_trx]);

        $cek_dokter     = $this->M_global->getData('dokter', ['kode_dokter' => $this->data['kode_user']]);

        $no_his         = count($pendaftaran);
        foreach ($pendaftaran as $p) : ?>
            <div class="card-header">
                <?php
                if ($web->ct_theme == 1) {
                    $stylepop = "style='color: white !important;'";
                } else if ($web->ct_theme == 2) {
                    $stylepop = "style='color: white !important;'";
                } else {
                    $stylepop = "style='color: black !important;'";
                }
                ?>
                <span class="h4"><span <?= $stylepop ?>>Kunj : <?= $eps ?> </span><?= ($p->tipe_daftar == 1) ? '<span class="badge badge-sm badge-danger float-right">Rawat Jalan</span>' : '<span class="badge badge-sm badge-warning float-right">Rawat Inap</span>' ?></span>
            </div>
            <div class="card-footer">
                <span class="h5">Status :
                    <span class="float-right">
                        <span class="badge badge-dark badge-sm"><?= $this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $p->kode_jenis_bayar])->keterangan ?></span>&nbsp;<?= (($p->status_trx == 0) ? '<span class="badge badge-sm badge-success">Buka</span>' : (($p->status_trx == 2) ? '<span class="badge badge-sm badge-danger">Batal</span>' : '<span class="badge badge-sm badge-primary">Selesai</span>')) ?>
                    </span>
                </span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table <?= $stylepop ?>>
                        <tr>
                            <td style="width: 15%;" valign="top">No RM</td>
                            <td style="width: 5%;" valign="top"> : </td>
                            <td style="width: 80%;" valign="top"><?= $kode_member ?></td>
                        </tr>
                        <tr>
                            <td style="width: 15%;" valign="top">Nama</td>
                            <td style="width: 5%;" valign="top"> : </td>
                            <td style="width: 80%;" valign="top"><?= $member->nama ?></td>
                        </tr>
                        <tr>
                            <td style="width: 15%;" valign="top">Alamat</td>
                            <td style="width: 5%;" valign="top"> : </td>
                            <td style="width: 80%;" valign="top"><?= $alamat ?></td>
                        </tr>
                        <tr>
                            <td style="width: 15%;">Cabang</td>
                            <td style="width: 5%;"> : </td>
                            <td style="width: 80%;"><?= $this->M_global->getData('cabang', ['kode_cabang' => $p->kode_cabang])->cabang ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="card-footer card-outline card-primary">
                <div class="row mb-1">
                    <div class="col-dm-12">
                        <span class="font-weight-bold">Assesment
                            <?php if (!$cek_dokter) : ?>
                                <div class="float-right">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="copyTextAssesment('sempoyongan_emr', 'berjalan_dgn_alat_emr', 'penompang_emr', 'keterangan_assesment_emr')"><i class="fa fa-copy"></i> Copy</button>
                                        <button type="button" class="btn btn-primary btn-sm"
                                            onclick="implementAssesment(
                                                '<?= ((!empty($emr_per)) ? $emr_per->sempoyongan : '') ?>', 'sempoyongan',
                                                '<?= ((!empty($emr_per)) ? $emr_per->berjalan_dgn_alat : '') ?>', 'berjalan_dgn_alat',
                                                '<?= ((!empty($emr_per)) ? $emr_per->penompang : '') ?>', 'penompang',
                                                '<?= ((!empty($emr_per)) ? $emr_per->keterangan_assesment : '') ?>', 'keterangan_assesment',
                                            )"><i class="fa-solid fa-clone"></i> Apply</button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table style="width: 100%; border-radius: 10px;" border="0" cellpadding="5px;">
                                <?php
                                $a1 = (!empty($emr_per) ? $emr_per->sempoyongan : 0);
                                $a2 = (!empty($emr_per) ? $emr_per->berjalan_dgn_alat : 0);
                                $b = (!empty($emr_per) ? $emr_per->penompang : 0);

                                if (($a1 == 1) || ($a2 == 1)) {
                                    $a = 1;
                                } else {
                                    $a = 0;
                                }

                                if (($a == 0) && ($b == 0)) {
                                    $hasil = 'Tidak Beresiko';
                                    $nilai = 'Tidak Ditemukan A & B';
                                } else if (($a == 1) || ($b == 1)) {
                                    $hasil = 'Beresiko Sedang';
                                    $nilai = 'Ditemukan Salah Satu Antara A & B';
                                } else {
                                    $hasil = 'Beresiko Tinggi';
                                    $nilai = 'Ditemukan A & B';
                                }
                                ?>
                                <tr>
                                    <td style="width: 20%;">Sempoyongan</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="sempoyongan_emr"><?= ((!empty($emr_per)) ? (($emr_per->sempoyongan == 1) ? 'Ya' : 'Tidak') : '') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;">Berjalan Dgn Alat</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="berjalan_dgn_alat_emr"><?= ((!empty($emr_per)) ? (($emr_per->berjalan_dgn_alat == 1) ? 'Ya' : 'Tidak') : '') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;">Penompang Duduk</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="penompang_emr"><?= ((!empty($emr_per)) ? (($emr_per->penompang == 1) ? 'Ya' : 'Tidak') : '') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;">Hasil</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="hasil_emr"><?= $hasil ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;">Nilai</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="nilai_emr"><?= $nilai ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;">Ket Lain</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="keterangan_assesment_emr"><?= ((!empty($emr_per)) ? $emr_per->keterangan_assesment : '-') ?></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row mb-1">
                    <div class="col-md-12">
                        <span class="font-weight-bold">Pemeriksaan Fisik
                            <?php if (!$cek_dokter) : ?>
                                <div class="float-right">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <button type="button" class="btn btn-secondary btn-sm"
                                            onclick="copyTextPemeriksaan('anamnesa_per_emr', 'diagnosa_per_emr', 'tekanan_darah_emr', 'nadi_emr', 'suhu_emr', 'bb_emr', 'tb_emr', 'pernapasan_emr', 'saturasi_emr', 'gizi_emr', 'hamil_emr', 'hpht_emr', 'keterangan_hamil_emr', 'scale_emr')"><i class="fa fa-copy"></i> Copy</button>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="implementPemeriksaan(
                                                '<?= ((!empty($emr_per)) ? $emr_per->anamnesa_per : '') ?>',
                                                '<?= ((!empty($emr_per)) ? $emr_per->diagnosa_per : '') ?>',
                                                '<?= ((!empty($emr_per)) ? $emr_per->tekanan_darah : '') ?>',
                                                '<?= ((!empty($emr_per)) ? $emr_per->nadi : '') ?>',
                                                '<?= ((!empty($emr_per)) ? $emr_per->suhu : '') ?>',
                                                '<?= ((!empty($emr_per)) ? $emr_per->bb : '') ?>',
                                                '<?= ((!empty($emr_per)) ? $emr_per->tb : '') ?>',
                                                '<?= ((!empty($emr_per)) ? $emr_per->pernapasan : '') ?>',
                                                '<?= ((!empty($emr_per)) ? $emr_per->saturasi : '') ?>',
                                                '<?= ((!empty($emr_per)) ? $emr_per->gizi : '') ?>',
                                                '<?= ((!empty($emr_per)) ? $emr_per->hamil : '') ?>',
                                                '<?= ((!empty($emr_per)) ? $emr_per->hpht : '') ?>',
                                                '<?= ((!empty($emr_per)) ? $emr_per->keterangan_hamil : '') ?>',
                                                '<?= ((!empty($emr_per)) ? $emr_per->scale : '') ?>'
                                            )"><i class="fa-solid fa-clone"></i> Apply</button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <input type="hidden" name="tekanan_darah_emr" id="tekanan_darah_emr" value="<?= (!empty($emr_per) ? $emr_per->tekanan_darah : '') ?>">
                            <input type="hidden" name="nadi_emr" id="nadi_emr" value="<?= (!empty($emr_per) ? $emr_per->nadi : '') ?>">
                            <input type="hidden" name="suhu_emr" id="suhu_emr" value="<?= (!empty($emr_per) ? $emr_per->nadi : '') ?>">
                            <input type="hidden" name="bb_emr" id="bb_emr" value="<?= (!empty($emr_per) ? $emr_per->nadi : '') ?>">
                            <input type="hidden" name="tb_emr" id="tb_emr" value="<?= (!empty($emr_per) ? $emr_per->nadi : '') ?>">
                            <input type="hidden" name="pernapasan_emr" id="pernapasan_emr" value="<?= (!empty($emr_per) ? $emr_per->nadi : '') ?>">
                            <input type="hidden" name="saturasi_emr" id="saturasi_emr" value="<?= (!empty($emr_per) ? $emr_per->nadi : '') ?>">
                            <input type="hidden" name="gizi_emr" id="gizi_emr" value="<?= (!empty($emr_per) ? $emr_per->nadi : '') ?>">
                            <table style="width: 100%; border-radius: 10px;" border="0" cellpadding="5px;">
                                <tr>
                                    <td style="width: 20%;">Anamnesa</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="anamnesa_per_emr"><?= ((!empty($emr_per)) ? $emr_per->anamnesa_per : '-') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;">Diagnosa</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="diagnosa_per_emr"><?= ((!empty($emr_per)) ? $emr_per->diagnosa_per : '-') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;">Pemeriksaan Fisik</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="his_pem_fisik"><?= ((!empty($emr_per)) ? ('Tekanan Darah : ' . $emr_per->tekanan_darah . ' (mmHg) | Nadi : ' . $emr_per->nadi . ' (x/mnt) | Suhu : ' . $emr_per->suhu . ' (°c) | Berat Badan : ' . $emr_per->bb . ' (kg) | Tinggi Badang : ' . $emr_per->tb . ' (cm) | Pernapasan : ' . $emr_per->pernapasan . ' (x/mnt) | Saturasi : ' . $emr_per->saturasi . ' (%) | Gizi : ' . (($emr_per->gizi == 0) ? 'Buruk' : (($emr_per->gizi == 1) ? 'Kurang' : (($emr_per->gizi == 2) ? 'Cukup' : ''))) . '') : '-') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;">Kehamilan</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="hamil_emr"><?= ((!empty($emr_per)) ? (($emr_per->hamil == 1) ? 'Ya' : 'Tidak') : 'Tidak') ?></span> / HPHT: <span id="hpht_emr"><?= ((!empty($emr_per)) ? (($emr_per->hpht != null) ? date('d-m-Y', strtotime($emr_per->hpht)) : '-') : '-') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;"></td>
                                    <td style="width: 5%;"></td>
                                    <td style="width: 75%;">
                                        Ket: <span id="keterangan_hamil_emr"><?= ((!empty($emr_per)) ? (($emr_per->keterangan_hamil == '') ? '-' : $emr_per->keterangan_hamil) : '-') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;">Skala Nyeri</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="scale_emr"><?= ((!empty($emr_per)) ? $emr_per->scale : '-') ?></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row mb-1">
                    <div class="col-md-12">
                        <span class="font-weight-bold">Psikologi & Spiritual
                            <?php if (!$cek_dokter) : ?>
                                <div class="float-right">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="copyTextPsiko(
                                            '<?= (!empty($emr_per) ? $emr_per->bicara : '') ?>',
                                            '<?= (!empty($emr_per) ? $emr_per->emosi : '') ?>',
                                            '<?= (!empty($emr_per) ? $emr_per->spiritual : '') ?>',
                                            '<?= (!empty($emr_per) ? $emr_per->gangguan : '') ?>',
                                        )"><i class="fa fa-copy"></i> Copy</button>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="implementPsiko(
                                            '<?= (!empty($emr_per) ? $emr_per->bicara : '') ?>',
                                            '<?= (!empty($emr_per) ? $emr_per->emosi : '') ?>',
                                            '<?= (!empty($emr_per) ? $emr_per->spiritual : '') ?>',
                                            '<?= (!empty($emr_per) ? $emr_per->gangguan : '') ?>',
                                        )"><i class="fa-solid fa-clone"></i> Apply</button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table style="width: 100%; border-radius: 10px;" border="0" cellpadding="5px;">
                                <tr>
                                    <td style="width: 20%;">Cara Bicara</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="bicara_emr"><?= (!empty($emr_per) ? ((($emr_per->bicara == 1) ? 'Bicara Normal' : 'Bicara Terganggu') . ', Ket: ' . (($emr_per->bicara == 2) ? $emr_per->gangguan : '')) : '') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;">Psikologi</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="psiko_emr"><?= (!empty($emr_per) ? (($emr_per->emosi == 1) ? 'Tenang' : (($emr_per->emosi == 2) ? 'Gelisah' : 'Emosional')) : '') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;">Spiritual</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="spiritual_emr"><?= (!empty($emr_per) ? (($emr_per->spiritual == 1) ? 'Berdiri' : (($emr_per->spiritual == 2) ? 'Duduk' : 'Berbaring')) : '') ?></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <hr>
                <hr>
                <div class="row mb-1">
                    <div class="col-md-12">
                        <span class="font-weight-bold">Head to Toe
                            <?php if ((!$cek_dokter) || ($this->session->userdata('kode_role') == 'R0001')) : ?>
                                <?php
                                $headtotoe = $this->M_global->getDataResult('emr_dok_fisik', ['no_trx' => $no_trx]);
                                $htt = '';
                                if (!empty($headtotoe)) {
                                    foreach ($headtotoe as $head) {
                                        $htt .= $head->fisik . ' - ' . $head->desc_fisik . ', ';
                                    }
                                } else {
                                    $htt .= '';
                                }
                                ?>
                                <div class="float-right">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="copyTextHead('<?= $htt ?>')"><i class="fa fa-copy"></i> Copy</button>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="implementHead('<?= $no_trx ?>')"><i class="fa-solid fa-clone"></i> Apply</button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table style="width: 100%;" border="1" cellpadding="5px">
                                <tr class="text-center">
                                    <td style="width: 25%;">Bagian</td>
                                    <td style="width: 75%;">Keterangan</td>
                                </tr>
                                <?php if (!empty($headtotoe)) : ?>
                                    <?php foreach ($headtotoe as $edf) : ?>
                                        <tr>
                                            <td style="width: 25%;"><?= $edf->fisik ?></td>
                                            <td style="width: 75%;"><?= $edf->desc_fisik ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else : ?>
                                    <tr>
                                        <td style="width: 100%; text-align: center;" colspan="2">Tidak Ada Head to Toe</td>
                                    </tr>
                                <?php endif ?>
                            </table>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row mb-1">
                    <div class="col-md-12">
                        <span class="font-weight-bold">E-Order
                            <?php if (!$cek_dokter) : ?>
                                <div class="float-right">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <?php
                                        $tarif_text = '';
                                        $lab_text = '';
                                        $rad_text = '';
                                        $resep_text = '';
                                        $emr_per_barang = $this->M_global->getDataResult('emr_per_barang', ['no_trx' => $no_trx]);
                                        $emr_tarif = $this->M_global->getDataResult('emr_tarif', ['no_trx' => $no_trx]);
                                        $emr_lab = $this->M_global->getDataResult('emr_lab', ['no_trx' => $no_trx]);
                                        $emr_rad = $this->M_global->getDataResult('emr_rad', ['no_trx' => $no_trx]);
                                        $emr_per_barang = $this->M_global->getDataResult('emr_per_barang', ['no_trx' => $no_trx]);
                                        if ($emr_tarif) {
                                            foreach ($emr_tarif as $et) {
                                                $tarif = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $et->kode_tarif]);
                                                if ($tarif) {
                                                    $tarif_text .= '@' . $tarif->keterangan . ' | ' . $et->qty . ', ';
                                                } else {
                                                    $tarif_text .= '-';
                                                }
                                            }
                                        } else {
                                            $tarif_text .= '';
                                        }

                                        if ($emr_lab) {
                                            foreach ($emr_lab as $et) {
                                                $tarif = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $et->kode_tarif]);
                                                if ($tarif) {
                                                    $lab_text .= '@' . $tarif->keterangan . ' | ' . $et->qty . ', ';
                                                } else {
                                                    $lab_text .= '-';
                                                }
                                            }
                                        } else {
                                            $lab_text .= '';
                                        }

                                        if ($emr_rad) {
                                            foreach ($emr_rad as $et) {
                                                $tarif = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $et->kode_tarif]);
                                                if ($tarif) {
                                                    $rad_text .= '@' . $tarif->keterangan . ' | ' . $et->qty . ', ';
                                                } else {
                                                    $rad_text .= '-';
                                                }
                                            }
                                        } else {
                                            $rad_text .= '';
                                        }

                                        if (empty($emr_per_barang)) {
                                            $resep_text .= '-';
                                        } else {
                                            if (count($emr_per_barang) > 1) {
                                                $br = '<br>';
                                            } else {
                                                $br = '';
                                            }

                                            foreach ($emr_per_barang as $epb) :
                                                $barang = $this->M_global->getData('barang', ['kode_barang' => $epb->kode_barang]);
                                                $satuan = $this->M_global->getData('m_satuan', ['kode_satuan' => $epb->kode_satuan]);
                                                $resep_text .= '@' . $barang->nama . ' | ' . $epb->qty . ' ' . $satuan->keterangan . ' | ' . $epb->signa . $br;
                                            endforeach;
                                        }

                                        if (!empty($emr_per)) {
                                            if ($emr_per->eracikan != '') {
                                                $resep_text .= '<br>' . $emr_per->eracikan;
                                            }
                                        }

                                        $all_text = 'Tindakan: ' . $tarif_text . ' | Lab: ' . $lab_text . ' | Resep: ' . $resep_text;
                                        ?>
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="copyTextOrder('<?= $all_text ?>')"><i class="fa fa-copy"></i> Copy</button>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="implementOrder('<?= $no_trx ?>')"><i class="fa-solid fa-clone"></i> Apply</button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="text-primary"><b>Tindakan</b></span>
                                <br>
                                <?php
                                if (empty($emr_tarif)) {
                                    echo '-';
                                } else {
                                    if (count($emr_tarif) > 1) {
                                        $br = '<br>';
                                    } else {
                                        $br = '';
                                    }

                                    foreach ($emr_tarif as $et) :
                                        $tarif = $this->M_global->getData('m_tindakan', [
                                            'kode_tindakan' => $et->kode_tarif,
                                            'kode_kategori NOT IN ("KATTR00002", "KATTR00003")'
                                        ]);

                                        if ($tarif) {
                                            echo '@' . $tarif->keterangan . ' | ' . $et->qty . $br;
                                        } else {
                                            echo '-';
                                        }
                                    endforeach;
                                }
                                ?>
                            </div>
                            <div class="col-md-6">
                                <span class="text-primary"><b>Resep</b></span>
                                <br>
                                <?php
                                $emr_per_barang = $this->M_global->getDataResult('emr_per_barang', ['no_trx' => $p->no_trx]);
                                if (empty($emr_per_barang)) {
                                    echo '-';
                                } else {
                                    if (count($emr_per_barang) > 1) {
                                        $br = '<br>';
                                    } else {
                                        $br = '';
                                    }

                                    foreach ($emr_per_barang as $epb) :
                                        $barang = $this->M_global->getData('barang', ['kode_barang' => $epb->kode_barang]);
                                        $satuan = $this->M_global->getData('m_satuan', ['kode_satuan' => $epb->kode_satuan]);
                                        echo '@' . $barang->nama . ' | ' . $epb->qty . ' ' . $satuan->keterangan . ' | ' . $epb->signa . $br;
                                    endforeach;
                                }

                                if (!empty($emr_per)) {
                                    if ($emr_per->eracikan != '') {
                                        echo '<br>' . $emr_per->eracikan;
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <span class="text-primary"><b>Laboratorium</b></span>
                                <br>
                                <?php
                                if (empty($emr_lab)) {
                                    echo '-';
                                } else {
                                    if (count($emr_lab) > 1) {
                                        $br = '<br>';
                                    } else {
                                        $br = '';
                                    }

                                    foreach ($emr_lab as $et) :
                                        $elab = $this->M_global->getData('m_tindakan', [
                                            'kode_tindakan' => $et->kode_tarif,
                                            'kode_kategori' => 'KATTR00002'
                                        ]);

                                        if ($elab) {
                                            echo '@' . $elab->keterangan . ' | ' . $et->qty . $br;
                                        } else {
                                            echo '-';
                                        }
                                    endforeach;
                                }
                                ?>
                            </div>
                            <div class="col-md-6">
                                <span class="text-primary"><b>Radiologi</b></span>
                                <br>
                                <?php
                                if (empty($emr_rad)) {
                                    echo '-';
                                } else {
                                    if (count($emr_rad) > 1) {
                                        $br = '<br>';
                                    } else {
                                        $br = '';
                                    }

                                    foreach ($emr_rad as $et) :
                                        $erad = $this->M_global->getData('m_tindakan', [
                                            'kode_tindakan' => $et->kode_tarif,
                                            'kode_kategori' => 'KATTR00003'
                                        ]);

                                        if ($erad) {
                                            echo '@' . $erad->keterangan . ' | ' . $et->qty . $br;
                                        } else {
                                            echo '-';
                                        }
                                    endforeach;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php $no_his--;
        endforeach;
    }

    // histori kunjungan px2
    public function his_px2($no_trx, $eps, $kode_member)
    {
        $web            = $this->M_global->getData('web_setting', ['id' => 1]);
        $pendaftaran    = $this->db->query('SELECT *, (@row_number := @row_number + 1) AS eps FROM pendaftaran, (SELECT @row_number := 0) AS init WHERE no_trx = "' . $no_trx . '"  ORDER BY id DESC')->result();

        $member         = $this->M_global->getData('member', ['kode_member' => $kode_member]);

        $prov           = $this->M_global->getData('m_provinsi', ['kode_provinsi' => $member->provinsi])->provinsi;
        $kab            = $this->M_global->getData('kabupaten', ['kode_kabupaten' => $member->kabupaten])->kabupaten;
        $kec            = $this->M_global->getData('kecamatan', ['kode_kecamatan' => $member->kecamatan])->kecamatan;

        $alamat         = 'Prov. ' . $prov . ', ' . $kab . ', Kec. ' . $kec . ', Ds. ' . $member->desa . ', (POS: ' . $member->kodepos . '), RT.' . $member->rt . '/RW.' . $member->rw;

        $emr_per        = $this->M_global->getData('emr_per', ['no_trx' => $no_trx]);
        $emr_dok        = $this->M_global->getData('emr_dok', ['no_trx' => $no_trx]);
        $emr_dok_fisik  = $this->M_global->getDataResult('emr_dok_fisik', ['no_trx' => $no_trx]);
        $emr_dok_cppt   = $this->M_global->getData('emr_dok_cppt', ['no_trx' => $no_trx]);

        $icd9           = $this->M_global->getDataResult('emr_dok_icd9', ['no_trx' => $no_trx]);
        $icd10          = $this->M_global->getDataResult('emr_dok_icd10', ['no_trx' => $no_trx]);

        $cek_dokter     = $this->M_global->getData('dokter', ['kode_dokter' => $this->data['kode_user']]);

        $no_his         = count($pendaftaran);
        foreach ($pendaftaran as $p) : ?>
            <?php
            if ($web->ct_theme == 1) {
                $stylepop = "style='color: white !important;'";
            } else if ($web->ct_theme == 2) {
                $stylepop = "style='color: white !important;'";
            } else {
                $stylepop = "style='color: black !important;'";
            }
            ?>
            <div class="card-header">
                <span class="h4"><span <?= $stylepop ?>>Kunj : <?= $eps ?> </span><?= ($p->tipe_daftar == 1) ? '<span class="badge badge-sm badge-danger float-right">Rawat Jalan</span>' : '<span class="badge badge-sm badge-warning float-right">Rawat Inap</span>' ?></span>
            </div>
            <div class="card-footer">
                <span class="h5">Status :
                    <span class="float-right">
                        <span class="badge badge-dark badge-sm"><?= $this->M_global->getData('m_jenis_bayar', ['kode_jenis_bayar' => $p->kode_jenis_bayar])->keterangan ?></span>&nbsp;<?= (($p->status_trx == 0) ? '<span class="badge badge-sm badge-success">Buka</span>' : (($p->status_trx == 2) ? '<span class="badge badge-sm badge-danger">Batal</span>' : '<span class="badge badge-sm badge-primary">Selesai</span>')) ?>
                    </span>
                </span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table <?= $stylepop ?>>
                        <tr>
                            <td style="width: 15%;" valign="top">No RM</td>
                            <td style="width: 5%;" valign="top"> : </td>
                            <td style="width: 80%;" valign="top"><?= $kode_member ?></td>
                        </tr>
                        <tr>
                            <td style="width: 15%;" valign="top">Nama</td>
                            <td style="width: 5%;" valign="top"> : </td>
                            <td style="width: 80%;" valign="top"><?= $member->nama ?></td>
                        </tr>
                        <tr>
                            <td style="width: 15%;" valign="top">Alamat</td>
                            <td style="width: 5%;" valign="top"> : </td>
                            <td style="width: 80%;" valign="top"><?= $alamat ?></td>
                        </tr>
                        <tr>
                            <td style="width: 15%;">Cabang</td>
                            <td style="width: 5%;"> : </td>
                            <td style="width: 80%;"><?= $this->M_global->getData('cabang', ['kode_cabang' => $p->kode_cabang])->cabang ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="card-footer card-outline card-primary">
                <div class="row mb-1">
                    <div class="col-md-12">
                        <span class="font-weight-bold">Pemeriksaan
                            <?php if (($cek_dokter) || ($this->session->userdata('kode_role') == 'R0001')) : ?>
                                <div class="float-right">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="copyTextSoap('anamnesa_dok_emr', 'diagnosa_dok_emr', 'rencana_dok_emr', 'tekanan_darah_emr', 'nadi_emr', 'suhu_emr', 'bb_emr', 'tb_emr', 'pernapasan_emr', 'saturasi_emr', 'gizi_emr', '<?= $no_trx ?>')"><i class="fa fa-copy"></i> Copy</button>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="implementSoap('<?= ((!empty($emr_dok)) ? $emr_dok->anamnesa_dok : '') ?>', '<?= ((!empty($emr_dok)) ? $emr_dok->diagnosa_dok : '') ?>', '<?= ((!empty($emr_dok)) ? $emr_dok->rencana_dok : '') ?>', '<?= $no_trx ?>')"><i class="fa-solid fa-clone"></i> Apply</button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <input type="hidden" name="tekanan_darah_emr" id="tekanan_darah_emr" value="<?= (!empty($emr_per) ? $emr_per->tekanan_darah : '') ?>">
                            <input type="hidden" name="nadi_emr" id="nadi_emr" value="<?= (!empty($emr_per) ? $emr_per->nadi : '') ?>">
                            <input type="hidden" name="suhu_emr" id="suhu_emr" value="<?= (!empty($emr_per) ? $emr_per->nadi : '') ?>">
                            <input type="hidden" name="bb_emr" id="bb_emr" value="<?= (!empty($emr_per) ? $emr_per->nadi : '') ?>">
                            <input type="hidden" name="tb_emr" id="tb_emr" value="<?= (!empty($emr_per) ? $emr_per->nadi : '') ?>">
                            <input type="hidden" name="pernapasan_emr" id="pernapasan_emr" value="<?= (!empty($emr_per) ? $emr_per->nadi : '') ?>">
                            <input type="hidden" name="saturasi_emr" id="saturasi_emr" value="<?= (!empty($emr_per) ? $emr_per->nadi : '') ?>">
                            <input type="hidden" name="gizi_emr" id="gizi_emr" value="<?= (!empty($emr_per) ? $emr_per->nadi : '') ?>">
                            <table style="width: 100%; border-radius: 10px;" border="0" cellpadding="5px;">
                                <tr>
                                    <td style="width: 20%;">Pemeriksaan Fisik</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="his_pem_fisik"><?= ((!empty($emr_per)) ? ('Tekanan Darah : ' . $emr_per->tekanan_darah . ' (mmHg) | Nadi : ' . $emr_per->nadi . ' (x/mnt) | Suhu : ' . $emr_per->suhu . ' (°c) | Berat Badan : ' . $emr_per->bb . ' (kg) | Tinggi Badang : ' . $emr_per->tb . ' (cm) | Pernapasan : ' . $emr_per->pernapasan . ' (x/mnt) | Saturasi : ' . $emr_per->saturasi . ' (%) | Gizi : ' . (($emr_per->gizi == 0) ? 'Buruk' : (($emr_per->gizi == 1) ? 'Kurang' : (($emr_per->gizi == 2) ? 'Cukup' : ''))) . '') : '-') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;">Anamnesa</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="anamnesa_dok_emr"><?= ((!empty($emr_dok)) ? $emr_dok->anamnesa_dok : '-') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;">Diagnosa</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="diagnosa_dok_emr"><?= ((!empty($emr_dok)) ? $emr_dok->diagnosa_dok : '-') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;">Anjuran</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="rencana_dok_emr"><?= ((!empty($emr_dok)) ? $emr_dok->rencana_dok : '-') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;">ICD 9</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <table style="width: 100%;" border="1" cellpadding="5px">
                                            <tr class="text-center">
                                                <td style="width: 25%;">Kode</td>
                                                <td style="width: 75%;">Keterangan</td>
                                            </tr>
                                            <?php if (!empty($icd9)) : ?>
                                                <?php foreach ($icd9 as $i9) : ?>
                                                    <tr>
                                                        <td style="width: 25%;"><?= $i9->kode_icd ?></td>
                                                        <td style="width: 75%;"><?= $this->M_global->getData('icd9', ['kode' => $i9->kode_icd])->keterangan ?></td>
                                                    </tr>
                                                <?php endforeach ?>
                                            <?php else : ?>
                                                <tr>
                                                    <td style="width: 100%; text-align: center;" colspan="2">Tidak Ada ICD 9</td>
                                                </tr>
                                            <?php endif ?>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;">ICD 10</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <table style="width: 100%;" border="1" cellpadding="5px">
                                            <tr class="text-center">
                                                <td style="width: 25%;">Kode</td>
                                                <td style="width: 75%;">Keterangan</td>
                                            </tr>
                                            <?php if (!empty($icd10)) : ?>
                                                <?php foreach ($icd10 as $i10) : ?>
                                                    <tr>
                                                        <td style="width: 25%;"><?= $i10->kode_icd ?></td>
                                                        <td style="width: 75%;"><?= $this->M_global->getData('icd10', ['kode' => $i10->kode_icd])->keterangan ?></td>
                                                    </tr>
                                                <?php endforeach ?>
                                            <?php else : ?>
                                                <tr>
                                                    <td style="width: 100%; text-align: center;" colspan="2">Tidak Ada ICD 10</td>
                                                </tr>
                                            <?php endif ?>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row mb-1">
                    <div class="col-md-12">
                        <span class="font-weight-bold">CPPT
                            <?php if (($cek_dokter) || ($this->session->userdata('kode_role') == 'R0001')) : ?>
                                <div class="float-right">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="copyTextCppt('soap_s_emr', 'soap_o_emr', 'soap_a_emr', 'soap_p_emr', 'ppa_his', 'instruksi_emr', 'verifikasi_his')"><i class="fa fa-copy"></i> Copy</button>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="implementCppt(
                                            '<?= ((!empty($emr_dok_cppt)) ? $emr_dok_cppt->ppa : '') ?>',
                                            '<?= ((!empty($emr_dok_cppt)) ? 'Dr. ' . $this->M_global->getData('dokter', ['kode_dokter' => $emr_dok_cppt->ppa])->nama : '') ?>',
                                            '<?= ((!empty($emr_dok_cppt)) ? $emr_dok_cppt->instruksi : '') ?>',
                                            '<?= ((!empty($emr_dok_cppt)) ? $emr_dok_cppt->soap_s : '') ?>',
                                            '<?= ((!empty($emr_dok_cppt)) ? $emr_dok_cppt->soap_o : '') ?>',
                                            '<?= ((!empty($emr_dok_cppt)) ? $emr_dok_cppt->soap_a : '') ?>',
                                            '<?= ((!empty($emr_dok_cppt)) ? $emr_dok_cppt->soap_p : '') ?>'
                                        )"><i class="fa-solid fa-clone"></i> Apply</button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table style="width: 100%;" border="0" cellpadding="5px">
                                <tr>
                                    <td style="width: 20%;">Status</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="verifikasi_emr" style="display: none;"><?= ((!empty($emr_dok_cppt)) ? $emr_dok_cppt->verifikasi : '-') ?></span>
                                        <span id="verifikasi_his"><?= ((!empty($emr_dok_cppt)) ? (($emr_dok_cppt->verifikasi == 1) ? 'Terverifikasi' : 'Belum Diverifikasi') : '-') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;">PPA</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="ppa_emr" style="display: none;"><?= ((!empty($emr_dok_cppt)) ? $emr_dok_cppt->ppa : '-') ?></span>
                                        <span id="ppa_his"><?= ((!empty($emr_dok_cppt)) ? 'Dr. ' . $this->M_global->getData('dokter', ['kode_dokter' => $emr_dok_cppt->ppa])->nama : '-') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;">Instruksi</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="instruksi_emr"><?= ((!empty($emr_dok_cppt)) ? $emr_dok_cppt->instruksi : '-') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;">S</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="soap_s_emr"><?= ((!empty($emr_dok_cppt)) ? $emr_dok_cppt->soap_s : '-') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;">O</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="soap_o_emr"><?= ((!empty($emr_dok_cppt)) ? $emr_dok_cppt->soap_o : '-') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;">A</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="soap_a_emr"><?= ((!empty($emr_dok_cppt)) ? $emr_dok_cppt->soap_a : '-') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 20%;">P</td>
                                    <td style="width: 5%;"> : </td>
                                    <td style="width: 75%;">
                                        <span id="soap_p_emr"><?= ((!empty($emr_dok_cppt)) ? $emr_dok_cppt->soap_p : '-') ?></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row mb-1">
                    <div class="col-md-12">
                        <span class="font-weight-bold">Head to Toe
                            <?php if (($cek_dokter) || ($this->session->userdata('kode_role') == 'R0001')) : ?>
                                <?php
                                $headtotoe = $this->M_global->getDataResult('emr_dok_fisik', ['no_trx' => $no_trx]);
                                $htt = '';
                                if (!empty($headtotoe)) {
                                    foreach ($headtotoe as $head) {
                                        $htt .= $head->fisik . ' - ' . $head->desc_fisik . ', ';
                                    }
                                } else {
                                    $htt .= '';
                                }
                                ?>
                                <div class="float-right">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="copyTextHead('<?= $htt ?>')"><i class="fa fa-copy"></i> Copy</button>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="implementHead('<?= $no_trx ?>')"><i class="fa-solid fa-clone"></i> Apply</button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table style="width: 100%;" border="1" cellpadding="5px">
                                <tr class="text-center">
                                    <td style="width: 25%;">Bagian</td>
                                    <td style="width: 75%;">Keterangan</td>
                                </tr>
                                <?php if (!empty($emr_dok_fisik)) : ?>
                                    <?php foreach ($emr_dok_fisik as $edf) : ?>
                                        <tr>
                                            <td style="width: 25%;"><?= $edf->fisik ?></td>
                                            <td style="width: 75%;"><?= $edf->desc_fisik ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php else : ?>
                                    <tr>
                                        <td style="width: 100%; text-align: center;" colspan="2">Tidak Ada Head to Toe</td>
                                    </tr>
                                <?php endif ?>
                            </table>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row mb-1">
                    <div class="col-md-12">
                        <span class="font-weight-bold">E-Order
                            <?php if (($cek_dokter) || ($this->session->userdata('kode_role') == 'R0001')) : ?>
                                <div class="float-right">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <?php
                                        $tarif_text = '';
                                        $lab_text = '';
                                        $rad_text = '';
                                        $resep_text = '';
                                        $emr_per_barang = $this->M_global->getDataResult('emr_per_barang', ['no_trx' => $no_trx]);
                                        $emr_tarif = $this->M_global->getDataResult('emr_tarif', ['no_trx' => $no_trx]);
                                        $emr_lab = $this->M_global->getDataResult('emr_lab', ['no_trx' => $no_trx]);
                                        $emr_rad = $this->M_global->getDataResult('emr_rad', ['no_trx' => $no_trx]);
                                        $emr_per_barang = $this->M_global->getDataResult('emr_per_barang', ['no_trx' => $no_trx]);
                                        if ($emr_tarif) {
                                            foreach ($emr_tarif as $et) {
                                                $tarif = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $et->kode_tarif]);
                                                if ($tarif) {
                                                    $tarif_text .= '@' . $tarif->keterangan . ' | ' . $et->qty . ', ';
                                                } else {
                                                    $tarif_text .= '-';
                                                }
                                            }
                                        } else {
                                            $tarif_text .= '';
                                        }

                                        if ($emr_lab) {
                                            foreach ($emr_lab as $et) {
                                                $tarif = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $et->kode_tarif]);
                                                if ($tarif) {
                                                    $lab_text .= '@' . $tarif->keterangan . ' | ' . $et->qty . ', ';
                                                } else {
                                                    $lab_text .= '-';
                                                }
                                            }
                                        } else {
                                            $lab_text .= '';
                                        }

                                        if ($emr_rad) {
                                            foreach ($emr_rad as $et) {
                                                $tarif = $this->M_global->getData('m_tindakan', ['kode_tindakan' => $et->kode_tarif]);
                                                if ($tarif) {
                                                    $rad_text .= '@' . $tarif->keterangan . ' | ' . $et->qty . ', ';
                                                } else {
                                                    $rad_text .= '-';
                                                }
                                            }
                                        } else {
                                            $rad_text .= '';
                                        }

                                        if (empty($emr_per_barang)) {
                                            $resep_text .= '-';
                                        } else {
                                            if (count($emr_per_barang) > 1) {
                                                $br = '<br>';
                                            } else {
                                                $br = '';
                                            }

                                            foreach ($emr_per_barang as $epb) :
                                                $barang = $this->M_global->getData('barang', ['kode_barang' => $epb->kode_barang]);
                                                $satuan = $this->M_global->getData('m_satuan', ['kode_satuan' => $epb->kode_satuan]);
                                                $resep_text .= '@' . $barang->nama . ' | ' . $epb->qty . ' ' . $satuan->keterangan . ' | ' . $epb->signa . $br;
                                            endforeach;
                                        }

                                        if (!empty($emr_per)) {
                                            if ($emr_per->eracikan != '') {
                                                $resep_text .= '<br>' . $emr_per->eracikan;
                                            }
                                        }

                                        $all_text = 'Tindakan: ' . $tarif_text . ' | Lab: ' . $lab_text . ' | Resep: ' . $resep_text;
                                        ?>
                                        <button type="button" class="btn btn-secondary btn-sm" onclick="copyTextOrder('<?= $tarif_text ?>')"><i class="fa fa-copy"></i> Copy</button>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="implementOrder('<?= $no_trx ?>')"><i class="fa-solid fa-clone"></i> Apply</button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <span class="text-primary"><b>Tindakan</b></span>
                                <br>
                                <?php
                                if (empty($emr_tarif)) {
                                    echo '-';
                                } else {
                                    if (count($emr_tarif) > 1) {
                                        $br = '<br>';
                                    } else {
                                        $br = '';
                                    }

                                    foreach ($emr_tarif as $et) :
                                        $tarif = $this->M_global->getData('m_tindakan', [
                                            'kode_tindakan' => $et->kode_tarif,
                                            'kode_kategori NOT IN ("KATTR00002", "KATTR00003")'
                                        ]);

                                        if ($tarif) {
                                            echo '@' . $tarif->keterangan . ' | ' . $et->qty . $br;
                                        } else {
                                            echo '-';
                                        }
                                    endforeach;
                                }
                                ?>
                            </div>
                            <div class="col-md-6">
                                <span class="text-primary"><b>Resep</b></span>
                                <br>
                                <?php
                                $emr_per_barang = $this->M_global->getDataResult('emr_per_barang', ['no_trx' => $p->no_trx]);
                                if (empty($emr_per_barang)) {
                                    echo '-';
                                } else {
                                    if (count($emr_per_barang) > 1) {
                                        $br = '<br>';
                                    } else {
                                        $br = '';
                                    }

                                    foreach ($emr_per_barang as $epb) :
                                        $barang = $this->M_global->getData('barang', ['kode_barang' => $epb->kode_barang]);
                                        $satuan = $this->M_global->getData('m_satuan', ['kode_satuan' => $epb->kode_satuan]);
                                        echo '@' . $barang->nama . ' | ' . $epb->qty . ' ' . $satuan->keterangan . ' | ' . $epb->signa . $br;
                                    endforeach;
                                }

                                if (!empty($emr_per)) {
                                    if ($emr_per->eracikan != '') {
                                        echo '<br>' . $emr_per->eracikan;
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <span class="text-primary"><b>Laboratorium</b></span>
                                <br>
                                <?php
                                if (empty($emr_lab)) {
                                    echo '-';
                                } else {
                                    if (count($emr_lab) > 1) {
                                        $br = '<br>';
                                    } else {
                                        $br = '';
                                    }

                                    foreach ($emr_lab as $et) :
                                        $elab = $this->M_global->getData('m_tindakan', [
                                            'kode_tindakan' => $et->kode_tarif,
                                            'kode_kategori' => 'KATTR00002'
                                        ]);

                                        if ($elab) {
                                            echo '@' . $elab->keterangan . ' | ' . $et->qty . $br;
                                        } else {
                                            echo '-';
                                        }
                                    endforeach;
                                }
                                ?>
                            </div>
                            <div class="col-md-6">
                                <span class="text-primary"><b>Radiologi</b></span>
                                <br>
                                <?php
                                if (empty($emr_rad)) {
                                    echo '-';
                                } else {
                                    if (count($emr_rad) > 1) {
                                        $br = '<br>';
                                    } else {
                                        $br = '';
                                    }

                                    foreach ($emr_rad as $et) :
                                        $erad = $this->M_global->getData('m_tindakan', [
                                            'kode_tindakan' => $et->kode_tarif,
                                            'kode_kategori' => 'KATTR00003'
                                        ]);

                                        if ($erad) {
                                            echo '@' . $erad->keterangan . ' | ' . $et->qty . $br;
                                        } else {
                                            echo '-';
                                        }
                                    endforeach;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php $no_his--;
        endforeach;
    }

    // emr barang
    public function emr_per_barang($no_trx)
    {
        $emr_per_barang = $this->db->query('SELECT eb.*, (SELECT nama FROM barang WHERE kode_barang = eb.kode_barang) AS nama, (SELECT keterangan FROM m_satuan WHERE kode_satuan = eb.kode_satuan) AS satuan FROM emr_per_barang eb WHERE eb.no_trx = "' . $no_trx . '"')->result();

        echo json_encode($emr_per_barang);
    }

    // emr_tarif
    public function emr_tarif($no_trx)
    {
        $emr_tarif = $this->db->query('SELECT et.*, t.keterangan AS nama, (SELECT (klinik + dokter + pelayanan + poli) FROM multiprice_tindakan WHERE kode_multiprice = et.kode_multiprice) AS harga FROM emr_tarif et JOIN m_tindakan t ON et.kode_tarif = t.kode_tindakan WHERE et.no_trx = "' . $no_trx . '"')->result();

        echo json_encode($emr_tarif);
    }

    // emr_lab
    public function emr_lab($no_trx)
    {
        $emr_lab = $this->db->query('SELECT et.*, t.keterangan AS nama, (SELECT (klinik + dokter + pelayanan + poli) FROM multiprice_tindakan WHERE kode_multiprice = et.kode_multiprice) AS harga FROM emr_lab et JOIN m_tindakan t ON et.kode_tarif = t.kode_tindakan WHERE et.no_trx = "' . $no_trx . '"')->result();

        echo json_encode($emr_lab);
    }

    // emr_rad
    public function emr_rad($no_trx)
    {
        $emr_rad = $this->db->query('SELECT et.*, t.keterangan AS nama, (SELECT (klinik + dokter + pelayanan + poli) FROM multiprice_tindakan WHERE kode_multiprice = et.kode_multiprice) AS harga FROM emr_rad et JOIN m_tindakan t ON et.kode_tarif = t.kode_tindakan WHERE et.no_trx = "' . $no_trx . '"')->result();

        echo json_encode($emr_rad);
    }

    // get harga tindakan
    public function getHargaTindakan($kode_multiprice)
    {
        $cek = $this->M_global->getData('multiprice_tindakan', ['kode_multiprice' => $kode_multiprice]);
        if ($cek) {
            echo json_encode(['status' => 1, 'harga' => ($cek->klinik + $cek->dokter + $cek->pelayanan + $cek->poli)]);
        } else {
            echo json_encode(['status' => 0, 'harga' => 0]);
        }
    }

    // emr fisik
    public function emr_dok_fisik($no_trx)
    {
        $emr_dok_fisik = $this->M_global->getDataResult('emr_dok_fisik', ['no_trx' => $no_trx]);

        echo json_encode($emr_dok_fisik);
    }

    // emr icd 9
    public function emr_dok_icd9($no_trx)
    {
        $emr_dok_icd9 = $this->db->query('SELECT icd.*, (SELECT keterangan FROM icd9 WHERE kode = icd.kode_icd) AS nama FROM emr_dok_icd9 icd WHERE icd.no_trx = "' . $no_trx . '"')->result();

        echo json_encode($emr_dok_icd9);
    }

    // emr icd 10
    public function emr_dok_icd10($no_trx)
    {
        $emr_dok_icd10 = $this->db->query('SELECT icd.*, (SELECT keterangan FROM icd10 WHERE kode = icd.kode_icd) AS nama FROM emr_dok_icd10 icd WHERE icd.no_trx = "' . $no_trx . '"')->result();

        echo json_encode($emr_dok_icd10);
    }

    // perawat page
    public function perawat($no_trx)
    {
        // website config
        $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
        $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);

        $cek_session     = $this->session->userdata('kode_user');
        $cek_sess_dokter = $this->M_global->getData('dokter', ['kode_dokter' => $cek_session]);

        if ($cek_sess_dokter) {
            redirect('Where');
        } else {
            $kode_dokter = $this->input->get('kode_dokter');
            if (!$kode_dokter) {
                $pendaftaran = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);
            } else {
                $pendaftaran = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx, 'kode_dokter' => $kode_dokter]);
            }

            $hist_member = $this->M_global->getDataResult('pendaftaran', ['kode_member' => $pendaftaran->kode_member, 'status_trx <> ' => '2']);

            $parameter = [
                $this->data,
                'judul'             => 'EMR',
                'nama_apps'         => $web_setting->nama,
                'page'              => 'Perawat',
                'web'               => $web_setting,
                'web_version'       => $web_version->version,
                'list_data'         => '',
                'param1'            => '',
                'pendaftaran'       => $pendaftaran,
                'hist_member'       => $hist_member,
                'no_trx'            => $no_trx,
                'kode_dokter'       => $kode_dokter,
                'emr_per'           => $this->M_global->getData('emr_per', ['no_trx' => $no_trx]),
                'eresep'            => $this->M_global->getDataResult('emr_per_barang', ['no_trx' => $no_trx]),
                'etarif'            => $this->M_global->getDataResult('emr_tarif', ['no_trx' => $no_trx]),
                'emr_dok_fisik'     => $this->M_global->getDataResult('emr_dok_fisik', ['no_trx' => $no_trx]),
                'elab'              => $this->M_global->getDataResult('emr_lab', ['no_trx' => $no_trx]),
                'erad'              => $this->M_global->getDataResult('emr_rad', ['no_trx' => $no_trx]),
            ];

            $this->template->load('Template/Content', 'Emr/Perawat', $parameter);
        }
    }

    // proses simpan/update perawat
    public function proses_per()
    {
        // ambil data dari view
        $no_trx               = htmlspecialchars($this->input->post('no_trx'));
        $kode_member          = htmlspecialchars($this->input->post('kode_member'));
        $umur                 = htmlspecialchars($this->input->post('umur'));
        $penyakit_keluarga    = htmlspecialchars($this->input->post('penyakit_keluarga'));
        $alergi               = htmlspecialchars($this->input->post('alergi'));
        $tekanan_darah        = htmlspecialchars($this->input->post('tekanan_darah'));
        $nadi                 = htmlspecialchars($this->input->post('nadi'));
        $suhu                 = htmlspecialchars($this->input->post('suhu'));
        $bb                   = htmlspecialchars($this->input->post('bb'));
        $tb                   = htmlspecialchars($this->input->post('tb'));
        $pernapasan           = htmlspecialchars($this->input->post('pernapasan'));
        $saturasi             = htmlspecialchars($this->input->post('saturasi'));
        $gizi                 = $this->input->post('gizi');
        $hamil                = $this->input->post('hamil');
        $hpht                 = (($this->input->post('hpht') == '0000-00-00') ? null : $this->input->post('hpht'));
        $keterangan_hamil     = htmlspecialchars($this->input->post('keterangan_hamil'));
        $scale                = htmlspecialchars($this->input->post('scale'));
        $bicara               = htmlspecialchars($this->input->post('bicara'));
        $gangguan             = htmlspecialchars($this->input->post('gangguan_bcr'));
        $emosi                = htmlspecialchars($this->input->post('emosi'));
        $spiritual            = htmlspecialchars($this->input->post('spiritual'));
        $diagnosa_per         = htmlspecialchars($this->input->post('diagnosa_per'));
        $anamnesa_per         = htmlspecialchars($this->input->post('anamnesa_per'));
        $eracikan             = htmlspecialchars($this->input->post('eracikan'));
        $date_per             = date('Y-m-d');
        $time_per             = date('H:i:s');
        $sempoyongan          = $this->input->post('sempoyongan');
        $berjalan_dgn_alat    = $this->input->post('berjalan_dgn_alat');
        $penompang            = $this->input->post('penompang');
        $keterangan_assesment = $this->input->post('keterangan_assesment');

        $kode_barang          = $this->input->post('kode_barang');
        $kode_satuan          = $this->input->post('kode_satuan');
        $qty                  = $this->input->post('qty');
        $signa                = $this->input->post('signa');

        $kode_tarif           = $this->input->post('kode_tarif');
        $qty_tarif            = $this->input->post('qty_tarif');

        $kode_lab             = $this->input->post('kode_lab');
        $qty_lab              = $this->input->post('qty_lab');

        $kode_rad             = $this->input->post('kode_rad');
        $qty_rad              = $this->input->post('qty_rad');

        $pendaftaran          = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);
        $kelas                = $pendaftaran->kelas;
        $penjamin             = $pendaftaran->kode_jenis_bayar;

        $fisik                = $this->input->post('fisik');
        $desc_fisik           = $this->input->post('desc_fisik');

        // tampung dalam array
        $data = [
            'no_trx'                => $no_trx,
            'kode_member'           => $kode_member,
            'umur'                  => $umur,
            'date_per'              => $date_per,
            'time_per'              => $time_per,
            'sempoyongan'           => $sempoyongan,
            'berjalan_dgn_alat'     => $berjalan_dgn_alat,
            'penompang'             => $penompang,
            'keterangan_assesment'  => $keterangan_assesment,
            'penyakit_keluarga'     => $penyakit_keluarga,
            'alergi'                => $alergi,
            'tekanan_darah'         => (($tekanan_darah) ? $tekanan_darah : '-'),
            'nadi'                  => (($nadi) ? $nadi : '-'),
            'suhu'                  => (($suhu) ? $suhu : '-'),
            'bb'                    => (($bb) ? $bb : '-'),
            'tb'                    => (($tb) ? $tb : '-'),
            'pernapasan'            => (($pernapasan) ? $pernapasan : '-'),
            'saturasi'              => (($saturasi) ? $saturasi : '-'),
            'gizi'                  => (($gizi) ? $gizi : '-'),
            'hamil'                 => $hamil,
            'hpht'                  => $hpht,
            'keterangan_hamil'      => $keterangan_hamil,
            'scale'                 => $scale,
            'bicara'                => $bicara,
            'gangguan'              => $gangguan,
            'emosi'                 => $emosi,
            'spiritual'             => $spiritual,
            'diagnosa_per'          => $diagnosa_per,
            'anamnesa_per'          => $anamnesa_per,
            'eracikan'              => $eracikan,
            'kode_user'             => $this->data['kode_user'],
        ];

        // pengecekan data emr perawat
        $cek_emr_per = $this->M_global->getData('emr_per', ['no_trx' => $no_trx]);

        if ($cek_emr_per) { // jika ada data, maka update
            $cek = [
                $this->M_global->updateData('emr_per', $data, ['no_trx' => $no_trx]),
                $this->M_global->updateData('emr_dok', ['penyakit_keluarga' => $penyakit_keluarga, 'alergi' => $alergi, 'eracikan' => $eracikan], ['no_trx' => $no_trx]),
                $this->M_global->delData('emr_per_barang', ['no_trx' => $no_trx]),
                $this->M_global->delData('emr_tarif', ['no_trx' => $no_trx]),
                $this->M_global->delData('emr_lab', ['no_trx' => $no_trx]),
                $this->M_global->delData('emr_rad', ['no_trx' => $no_trx]),
            ];

            aktifitas_user_transaksi('EMR', 'Mengubah Emr Perawat ' . $kode_member, $no_trx);
        } else { // selain itu maka tambah
            $cek = [
                $this->M_global->insertData('emr_per', $data),
                $this->M_global->delData('emr_per_barang', ['no_trx' => $no_trx]),
                $this->M_global->delData('emr_tarif', ['no_trx' => $no_trx]),
                $this->M_global->delData('emr_lab', ['no_trx' => $no_trx]),
                $this->M_global->delData('emr_rad', ['no_trx' => $no_trx]),
            ];

            aktifitas_user_transaksi('EMR', 'Menambahkan Emr Perawat ' . $kode_member, $no_trx);
        }

        $loop = 0;
        if (isset($kode_barang) || !empty($kode_barang)) {
            foreach ($kode_barang as $k) {
                if ($k) {
                    $kode_barang_   = $k;
                    $kode_satuan_   = $kode_satuan[$loop];
                    $qty_           = $qty[$loop];
                    $signa_         = $signa[$loop];

                    $loop++;

                    $data_barang = [
                        'no_trx'        => $no_trx,
                        'kode_barang'   => $kode_barang_,
                        'kode_satuan'   => $kode_satuan_,
                        'qty'           => $qty_,
                        'signa'         => $signa_,
                    ];

                    $this->M_global->insertData('emr_per_barang', $data_barang);
                }
            }
        }

        $loop2 = 0;
        if (isset($kode_tarif) || !empty($kode_tarif)) {
            foreach ($kode_tarif as $kt) {
                if ($kt) {
                    $kode_tarif_   = $kt;
                    $qty_tarif_    = $qty_tarif[$loop2];

                    $loop2++;

                    $data_tarif = [
                        'no_trx'          => $no_trx,
                        'kode_multiprice' => $kode_tarif_,
                        'kode_tarif'      => $this->M_global->getData('multiprice_tindakan', ['kode_multiprice' => $kode_tarif_])->kode_tindakan,
                        'qty'             => $qty_tarif_,
                        'kelas'           => $kelas,
                        'poli'            => $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx])->kode_poli,
                        'penjamin'        => $penjamin,
                    ];

                    $this->M_global->insertData('emr_tarif', $data_tarif);
                }
            }
        }

        $loop3 = 0;
        if (isset($kode_lab) || !empty($kode_lab)) {
            foreach ($kode_lab as $kt) {
                if ($kt) {
                    $kode_lab_   = $kt;
                    $qty_lab_    = $qty_lab[$loop3];

                    $loop3++;

                    $data_lab = [
                        'no_trx'          => $no_trx,
                        'kode_multiprice' => $kode_lab_,
                        'kode_tarif'      => $this->M_global->getData('multiprice_tindakan', ['kode_multiprice' => $kode_lab_])->kode_tindakan,
                        'qty'             => $qty_lab_,
                        'kelas'           => $kelas,
                        'penjamin'        => $penjamin,
                    ];

                    $this->M_global->insertData('emr_lab', $data_lab);
                }
            }
        }

        $loop4 = 0;
        if (isset($kode_rad) || !empty($kode_rad)) {
            foreach ($kode_rad as $kt) {
                if ($kt) {
                    $kode_rad_   = $kt;
                    $qty_rad_    = $qty_rad[$loop4];

                    $loop4++;

                    $data_rad = [
                        'no_trx'          => $no_trx,
                        'kode_multiprice' => $kode_rad_,
                        'kode_tarif'      => $this->M_global->getData('multiprice_tindakan', ['kode_multiprice' => $kode_rad_])->kode_tindakan,
                        'qty'             => $qty_rad_,
                        'kelas'           => $kelas,
                        'penjamin'        => $penjamin,
                    ];

                    $this->M_global->insertData('emr_rad', $data_rad);
                }
            }
        }

        $loop5 = 0;
        if (isset($fisik)) {
            foreach ($fisik as $f) {
                if ($f) {
                    $fisik_        = $f;
                    $desc_fisik_   = $desc_fisik[$loop5];

                    $loop5++;

                    $data_fisik = [
                        'no_trx'        => $no_trx,
                        'fisik'         => $fisik_,
                        'desc_fisik'    => $desc_fisik_,
                    ];

                    $this->M_global->insertData('emr_dok_fisik', $data_fisik);
                }
            }
        }

        if ($cek) { // jika fungsi cek berjalan, maka status 1
            echo json_encode(['status' => 1]);
        } else { // selain itu status 0
            echo json_encode(['status' => 0]);
        }
    }

    // dokter page
    public function dokter($no_trx)
    {
        $cek_session     = $this->session->userdata('kode_user');
        $cek_sess_dokter = $this->M_global->getData('dokter', ['kode_dokter' => $cek_session]);

        // cek apakah dia dokter ?
        if (($cek_sess_dokter) || ($this->session->userdata('kode_role') == 'R0001')) { // jika dokter
            // website config
            $web_setting = $this->M_global->getData('web_setting', ['id' => 1]);
            $web_version = $this->M_global->getData('web_version', ['id_web' => $web_setting->id]);


            $kode_dokter = $this->input->get('kode_dokter');
            if (!$kode_dokter) {
                $pendaftaran = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);
            } else {
                $pendaftaran = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx, 'kode_dokter' => $kode_dokter]);
            }

            $hist_member = $this->M_global->getDataResult('pendaftaran', ['kode_member' => $pendaftaran->kode_member]);

            $parameter = [
                $this->data,
                'judul'             => 'EMR',
                'nama_apps'         => $web_setting->nama,
                'page'              => 'Dokter',
                'web'               => $web_setting,
                'web_version'       => $web_version->version,
                'list_data'         => '',
                'param1'            => '',
                'pendaftaran'       => $pendaftaran,
                'hist_member'       => $hist_member,
                'no_trx'            => $no_trx,
                'kode_dokter'       => $kode_dokter,
                'emr_per'           => $this->M_global->getData('emr_per', ['no_trx' => $no_trx]),
                'emr_dok'           => $this->M_global->getData('emr_dok', ['no_trx' => $no_trx]),
                'emr_dok_cppt'      => $this->M_global->getData('emr_dok_cppt', ['no_trx' => $no_trx]),
                'emr_dok_fisik'     => $this->M_global->getDataResult('emr_dok_fisik', ['no_trx' => $no_trx]),
                'eresep'            => $this->M_global->getDataResult('emr_per_barang', ['no_trx' => $no_trx]),
                'etarif'            => $this->M_global->getDataResult('emr_tarif', ['no_trx' => $no_trx]),
                'icd9'              => $this->M_global->getDataResult('emr_dok_icd9', ['no_trx' => $no_trx]),
                'icd10'             => $this->M_global->getDataResult('emr_dok_icd10', ['no_trx' => $no_trx]),
                'elab'              => $this->M_global->getDataResult('emr_lab', ['no_trx' => $no_trx]),
                'erad'              => $this->M_global->getDataResult('emr_rad', ['no_trx' => $no_trx]),
            ];

            $this->template->load('Template/Content', 'Emr/Dokter', $parameter);
        } else { // namun jika bukan dokter, arahkan ke page dokter
            redirect('Where'); // lempar ke url where
        }
    }

    // proses simpan/update dokter
    public function proses_dok()
    {
        // ambil data dari view
        $no_trx               = htmlspecialchars($this->input->post('no_trx'));
        $kode_member          = htmlspecialchars($this->input->post('kode_member'));
        $umur                 = htmlspecialchars($this->input->post('umur'));
        $penyakit_keluarga    = htmlspecialchars($this->input->post('penyakit_keluarga'));
        $alergi               = htmlspecialchars($this->input->post('alergi'));
        $diagnosa_dok         = htmlspecialchars($this->input->post('diagnosa_dok'));
        $anamnesa_dok         = htmlspecialchars($this->input->post('anamnesa_dok'));
        $rencana_dok          = htmlspecialchars($this->input->post('rencana_dok'));
        $eracikan             = htmlspecialchars($this->input->post('eracikan'));
        $date_dok             = date('Y-m-d');
        $time_dok             = date('H:i:s');

        $kode_barang          = $this->input->post('kode_barang');
        $kode_satuan          = $this->input->post('kode_satuan');
        $qty                  = $this->input->post('qty');
        $signa                = $this->input->post('signa');

        $fisik                = $this->input->post('fisik');
        $desc_fisik           = $this->input->post('desc_fisik');

        $kode_tarif           = $this->input->post('kode_tarif');
        $qty_tarif            = $this->input->post('qty_tarif');

        $kode_lab             = $this->input->post('kode_lab');
        $qty_lab              = $this->input->post('qty_lab');

        $kode_rad             = $this->input->post('kode_rad');
        $qty_rad              = $this->input->post('qty_rad');

        $icd9                 = $this->input->post('icd9');
        $icd10                = $this->input->post('icd10');

        $ppa                  = htmlspecialchars($this->input->post('ppa'));
        $instruksi            = htmlspecialchars($this->input->post('instruksi'));
        $soap_s               = htmlspecialchars($this->input->post('soap_s'));
        $soap_o               = htmlspecialchars($this->input->post('soap_o'));
        $soap_a               = htmlspecialchars($this->input->post('soap_a'));
        $soap_p               = htmlspecialchars($this->input->post('soap_p'));

        $pendaftaran          = $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx]);
        $kelas                = $pendaftaran->kelas;
        $penjamin             = $pendaftaran->kode_jenis_bayar;

        $status_lanjut        = $this->input->post('status_lanjut');
        $alasan_ranap         = $this->input->post('alasan_ranap');
        $rujuk_ke             = $this->input->post('rujuk_ke');
        $rujuk_alasan         = $this->input->post('rujuk_alasan');
        $tgl_kontrol          = $this->input->post('tgl_kontrol');
        $poli_kontrol         = $this->input->post('poli_kontrol');

        add_field('emr_dok', 'alasan_ranap', 'varchar', '255', '');
        add_field('emr_dok', 'status_lanjut', 'int', '11', '0');
        add_field('emr_dok', 'rujuk_ke', 'varchar', '255', '');
        add_field('emr_dok', 'rujuk_alasan', 'varchar', '255', '');
        add_field('emr_dok', 'tgl_kontrol', 'date', '', NULL);
        add_field('emr_dok', 'poli_kontrol', 'varchar', '10', '');

        // tampung dalam array
        $data = [
            'no_trx'            => $no_trx,
            'kode_member'       => $kode_member,
            'umur'              => $umur,
            'date_dok'          => $date_dok,
            'time_dok'          => $time_dok,
            'penyakit_keluarga' => $penyakit_keluarga,
            'alergi'            => $alergi,
            'diagnosa_dok'      => $diagnosa_dok,
            'anamnesa_dok'      => $anamnesa_dok,
            'rencana_dok'       => $rencana_dok,
            'eracikan'          => $eracikan,
            'kode_user'         => $this->data['kode_user'],
            'alasan_ranap'      => $alasan_ranap,
            'status_lanjut'     => $status_lanjut,
            'rujuk_ke'          => $rujuk_ke,
            'rujuk_alasan'      => $rujuk_alasan,
            'tgl_kontrol'       => $tgl_kontrol,
            'poli_kontrol'      => $poli_kontrol,
        ];

        // pengecekan data emr perawat
        $cek_emr_dok = $this->M_global->getData('emr_dok', ['no_trx' => $no_trx]);

        // tambung data soal dalam array
        $data_cppt = [
            'no_trx'            => $no_trx,
            'date_cppt'         => date('Y-m-d'),
            'time_cppt'         => date('H:i:s'),
            'dpjp'              => $this->data['kode_user'],
            'ppa'               => $ppa,
            'instruksi'         => $instruksi,
            'soap_s'            => $soap_s,
            'soap_o'            => $soap_o,
            'soap_a'            => $soap_a,
            'soap_p'            => $soap_p,
            'kode_member'       => $kode_member,
            'verifikasi'        => 0,
        ];

        if ($cek_emr_dok) { // jika ada data, maka update
            $cek = [
                $this->M_global->updateData('emr_dok', $data, ['no_trx' => $no_trx]),
                $this->M_global->updateData('emr_per', ['penyakit_keluarga' => $penyakit_keluarga, 'alergi' => $alergi, 'eracikan' => $eracikan], ['no_trx' => $no_trx]),
                $this->M_global->delData('emr_per_barang', ['no_trx' => $no_trx]),
                $this->M_global->delData('emr_dok_fisik', ['no_trx' => $no_trx]),
                $this->M_global->delData('emr_tarif', ['no_trx' => $no_trx]),
                $this->M_global->delData('emr_lab', ['no_trx' => $no_trx]),
                $this->M_global->delData('emr_rad', ['no_trx' => $no_trx]),
                $this->M_global->delData('emr_dok_icd9', ['no_trx' => $no_trx]),
                $this->M_global->delData('emr_dok_icd10', ['no_trx' => $no_trx]),
                $this->M_global->delData('emr_dok_cppt', ['no_trx' => $no_trx]),
            ];

            aktifitas_user_transaksi('EMR', 'Mengubah Emr Dokter ' . $kode_member, $no_trx);
        } else { // selain itu maka tambah
            $cek = [
                $this->M_global->insertData('emr_dok', $data),
                $this->M_global->updateData('emr_per', ['penyakit_keluarga' => $penyakit_keluarga, 'alergi' => $alergi, 'eracikan' => $eracikan], ['no_trx' => $no_trx]),
                $this->M_global->delData('emr_per_barang', ['no_trx' => $no_trx]),
                $this->M_global->delData('emr_dok_fisik', ['no_trx' => $no_trx]),
                $this->M_global->delData('emr_tarif', ['no_trx' => $no_trx]),
            ];

            aktifitas_user_transaksi('EMR', 'Menambahkan Emr Dokter ' . $kode_member, $no_trx);
        }

        // simpan data cppt
        $this->M_global->insertData('emr_dok_cppt', $data_cppt);

        $loop = 0;
        if (isset($kode_barang)) {
            foreach ($kode_barang as $k) {
                if ($k) {
                    $kode_barang_ = $k;
                    $kode_satuan_ = $kode_satuan[$loop];
                    $qty_         = $qty[$loop];
                    $signa_       = $signa[$loop];

                    // Increment loop here only once
                    $loop++;

                    $data_barang = [
                        'no_trx'      => $no_trx,
                        'kode_barang' => $kode_barang_,
                        'kode_satuan' => $kode_satuan_,
                        'qty'         => $qty_,
                        'signa'       => $signa_,
                    ];

                    // Insert data into the database
                    $this->M_global->insertData('emr_per_barang', $data_barang);
                }
            }
        }

        $loop2 = 0;
        if (isset($fisik)) {
            foreach ($fisik as $f) {
                if ($f) {
                    $fisik_        = $f;
                    $desc_fisik_   = $desc_fisik[$loop2];

                    $loop2++;

                    $data_fisik = [
                        'no_trx'        => $no_trx,
                        'fisik'         => $fisik_,
                        'desc_fisik'    => $desc_fisik_,
                    ];

                    $this->M_global->insertData('emr_dok_fisik', $data_fisik);
                }
            }
        }

        $loop3 = 0;
        if (isset($kode_tarif)) {
            foreach ($kode_tarif as $kt) {
                if ($kt) {
                    $kode_tarif_  = $kt;
                    $qty_tarif_   = $qty_tarif[$loop3];

                    $loop3++;

                    $data_tarif = [
                        'no_trx'          => $no_trx,
                        'kode_multiprice' => $kode_tarif_,
                        'kode_tarif'      => $this->M_global->getData('multiprice_tindakan', ['kode_multiprice' => $kode_tarif_])->kode_tindakan,
                        'qty'             => $qty_tarif_,
                        'kelas'           => $kelas,
                        'poli'            => $this->M_global->getData('pendaftaran', ['no_trx' => $no_trx])->kode_poli,
                        'penjamin'        => $penjamin,
                    ];

                    $this->M_global->insertData('emr_tarif', $data_tarif);
                }
            }
        }

        $loop4 = 0;
        if (isset($icd9)) {
            foreach ($icd9 as $i9) {
                if ($i9) {
                    $kode_  = $i9;

                    $loop4++;

                    $data_icd9 = [
                        'no_trx'      => $no_trx,
                        'kode_icd'    => $kode_,
                    ];

                    $this->M_global->insertData('emr_dok_icd9', $data_icd9);
                }
            }
        }

        $loop5 = 0;
        if (isset($icd10)) {
            foreach ($icd10 as $i10) {
                if ($i10) {
                    $kode_  = $i10;

                    $loop5++;

                    $data_icd10 = [
                        'no_trx'      => $no_trx,
                        'kode_icd'    => $kode_,
                    ];

                    $this->M_global->insertData('emr_dok_icd10', $data_icd10);
                }
            }
        }

        $loop6 = 0;
        if (isset($kode_lab) || !empty($kode_lab)) {
            foreach ($kode_lab as $kt) {
                if ($kt) {
                    $kode_lab_   = $kt;
                    $qty_lab_    = $qty_lab[$loop6];

                    $loop6++;

                    $data_lab = [
                        'no_trx'          => $no_trx,
                        'kode_multiprice' => $kode_lab_,
                        'kode_tarif'      => $this->M_global->getData('multiprice_tindakan', ['kode_multiprice' => $kode_lab_])->kode_tindakan,
                        'qty'             => $qty_lab_,
                        'kelas'           => $kelas,
                        'penjamin'        => $penjamin,
                    ];

                    $this->M_global->insertData('emr_lab', $data_lab);
                }
            }
        }

        $loop7 = 0;
        if (isset($kode_rad) || !empty($kode_rad)) {
            foreach ($kode_rad as $kt) {
                if ($kt) {
                    $kode_rad_   = $kt;
                    $qty_rad_    = $qty_rad[$loop7];

                    $loop7++;

                    $data_rad = [
                        'no_trx'          => $no_trx,
                        'kode_multiprice' => $kode_rad_,
                        'kode_tarif'      => $this->M_global->getData('multiprice_tindakan', ['kode_multiprice' => $kode_rad_])->kode_tindakan,
                        'qty'             => $qty_rad_,
                        'kelas'           => $kelas,
                        'penjamin'        => $penjamin,
                    ];

                    $this->M_global->insertData('emr_rad', $data_rad);
                }
            }
        }

        if ($cek) { // jika fungsi cek berjalan, maka status 1

            echo json_encode(['status' => 1]);
        } else { // selain itu status 0
            echo json_encode(['status' => 0]);
        }
    }

    public function body_cppt($kode_member)
    {
        $soap = $this->db->query('SELECT * FROM emr_dok_cppt WHERE kode_member = "' . $kode_member . '" ORDER BY id DESC')->result();
        if (!empty($soap)) :
            echo '<tbody>';
            foreach ($soap as $s) :
                $dokter = $this->M_global->getData('dokter', ['kode_dokter' => $s->dpjp]);
                $ppa = $this->M_global->getData('dokter', ['kode_dokter' => $s->ppa]);

                if ($dokter) {
                    $dokter = 'Dr. ' . $dokter->nama;
                } else {
                    $dokter = $this->M_global->getData('user', ['kode_user' => $s->dpjp])->nama;
                }

                if ($ppa) {
                    $ppa = 'Dr. ' . $ppa->nama;
                } else {
                    $ppa = $this->M_global->getData('user', ['kode_user' => $s->ppa])->nama;
                }

                if ($s->kode_verifikasi != null) {
                    $verif = $this->M_global->getData('dokter', ['kode_dokter' => $s->kode_verifikasi]);

                    if ($verif) {
                        $verif = 'Dr. ' . $verif->nama;
                    } else {
                        $verif = $this->M_global->getData('user', ['kode_user' => $s->kode_verifikasi])->nama;
                    }
                } else {
                    $verif = '';
                }
            ?>
                <tr>
                    <td>
                        <?php if ($s->verifikasi == 0) : ?>
                            <button type="button" class="btn btn-warning" onclick="verif_cppt('<?= $s->no_trx ?>', '1')">Verifikasi</button>
                        <?php else : ?>
                            <button type="button" class="btn btn-danger" onclick="verif_cppt('<?= $s->no_trx ?>', '0')">Batal Verifikasi</button>
                        <?php endif ?>
                    </td>
                    <td>
                        <?= date('d-m-Y', strtotime($s->date_cppt)) . ' / ' . date('H:i', strtotime($s->time_cppt)) ?>
                        <hr>
                        <?= 'DPJP: <span class="float-right">' . $dokter . '</span>' ?>
                        <br>
                        <?= 'PPA:  <span class="float-right">' . $ppa . '</span>' ?>
                        <hr>
                        <?php if ($verif != '') : ?>
                            <span><?= $verif ?></span>
                            <br>
                        <?php endif ?>
                        <span class="badge badge-<?= (($s->verifikasi == 1) ? 'primary' : 'info') ?>">
                            <?= ($s->verifikasi == 1) ? 'Terverifikasi' : 'Belum Diverifikasi' ?>
                        </span><span class="float-right"><?= (($s->date_verif != null) ? date('d-m-Y', strtotime($s->date_verif)) . ' / ' . date('H:i', strtotime($s->time_verif)) : '') ?></span>
                    </td>
                    <td>
                        S: <?= $s->soap_s ?>
                        <hr>
                        O: <?= $s->soap_o ?>
                        <hr>
                        A: <?= $s->soap_a ?>
                        <hr>
                        P: <?= $s->soap_p ?>
                    </td>
                </tr>
        <?php endforeach;
            echo '</tbody>';
        endif ?>
<?php
    }

    public function verif_cppt($no_trx, $param)
    {
        if ($param == 1) {
            $data = [
                'verifikasi' => 1,
                'date_verif' => date('Y-m-d'),
                'time_verif' => date('H:i:s'),
            ];
        } else {
            $data = [
                'verifikasi' => 0,
                'date_verif' => null,
                'time_verif' => null,
            ];
        }
        $cek = $this->M_global->updateData('emr_dok_cppt', $data, ['no_trx' => $no_trx]);

        if ($cek) {
            echo json_encode(['status' => 1]);
        } else {
            echo json_encode(['status' => 0]);
        }
    }

    public function getIcd($param, $key)
    {
        if ($param == 9) {
            $sintak = $this->db->query("SELECT kode AS id, CONCAT(kode, ', ', keterangan) AS text FROM icd9 WHERE kode LIKE '%$key%' OR keterangan LIKE '%$key%'")->row();
        } else {
            $sintak = $this->db->query("SELECT kode AS id, CONCAT(kode, ', ', keterangan) AS text FROM icd10 WHERE kode LIKE '%$key%' OR keterangan LIKE '%$key%'")->row();
        }

        echo json_encode($sintak);
    }
}
