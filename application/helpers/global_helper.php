<?php

// add new field
function add_field($table, $kolom, $tipe, $length, $default)
{
    $CI = &get_instance();
    $CI->load->database(); // Ensure the database library is loaded

    $cfield = $CI->db->query("SELECT COLUMN_NAME 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = '$table' AND column_name = '$kolom'")->num_rows();

    if ($cfield < 1) {
        // Handle date type differently
        if (strtolower($tipe) == 'date') {
            if ($default === NULL) {
                $CI->db->query("ALTER TABLE $table ADD $kolom $tipe NULL");
            } else {
                $CI->db->query("ALTER TABLE $table ADD $kolom $tipe DEFAULT $default NULL");
            }
        } else {
            $CI->db->query("ALTER TABLE $table ADD $kolom $tipe($length) DEFAULT $default NULL");
        }
        $text = "proses tambah kolom";
    } else {
        $text = "sudah ada";
    }

    return $text;
}

// dell new field
function dell_field($table, $kolom)
{
    $CI           = &get_instance();

    $cfield = $CI->db->query("SELECT COLUMN_NAME 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = '$table' AND column_name = '$kolom'")->num_rows();

    if ($cfield < 1) {
        $CI->db->query("ALTER TABLE $table DROP COLUMN $kolom");
        $text = "proses tambah kolom";
    } else {
        $text = "sudah ada";
    }

    return $text;
}

// send email
function _sendMail($emailUser, $title, $message)
{
    $CI = &get_instance();
    $smtp_apps = $CI->db->query("SELECT * FROM web_setting WHERE id = 1")->row();
    // configurasi email
    $config = [
        'protocol'  => 'smtp',
        'smtp_host' => 'ssl://smtp.googlemail.com',
        'smtp_user' => $smtp_apps->email,
        'smtp_pass' => $smtp_apps->kode_email,
        'smtp_port' => 465,
        'mailtype'  => 'html',
        'charset'   => 'utf-8',
        'newline'   => "\r\n"
    ];
    $CI->email->initialize($config);
    $CI->email->from($smtp_apps->email, $smtp_apps->nama);
    $CI->email->to($emailUser);
    $CI->email->subject($title);
    $CI->email->message($message);
    if ($CI->email->send()) { // email terkirim
        echo json_encode(["status" => 1, "email" => $emailUser]);
    } else { // email gagal terkirim
        echo json_encode(["status" => 2, "email" => $emailUser]);
    }
}

function singkatTeks($teks, $panjang = 10)
{
    if (strlen($teks) > $panjang) {
        return substr($teks, 0, $panjang) . "...";
    } else {
        return $teks;
    }
}

function nosurat($nama)
{
    $CI           = &get_instance();
    $smtp_apps    = $CI->db->query("SELECT * FROM web_setting WHERE id = 1")->row();
    $surat        = $CI->db->query('SELECT * FROM surat WHERE nama = "' . $nama . '"')->row();

    if (!$surat) {
        $CI->M_global->insertData('surat', ['nomor' => 1, 'nama' => $nama]);
        $number     = 1;
    } else {
        $CI->M_global->updateData('surat', ['nomor' => ($surat->nomor + 1)], ['nama' => $nama]);
        $surat        = $CI->db->query('SELECT * FROM surat WHERE nama = "' . $nama . '"')->row();
        $number       = (int)$surat->nomor;
    }

    if ($nama == 'Suket_sakit_') {
        $param = 'SKS';
    } else if ($nama == 'Suket_dokter_') {
        $param = 'SDT';
    } else if ($nama == 'Suket_diagnosa_') {
        $param = 'SKD';
    } else {
        $param = 'SKDP';
    }

    $surat_ke     = $number;

    if (date('m') == 12) {
        $month = 'XII';
    } else if (date('m') == 11) {
        $month = 'XI';
    } else if (date('m') == 10) {
        $month = 'X';
    } else if (date('m') == 9) {
        $month = 'IX';
    } else if (date('m') == 8) {
        $month = 'VIII';
    } else if (date('m') == 7) {
        $month = 'VII';
    } else if (date('m') == 6) {
        $month = 'VI';
    } else if (date('m') == 5) {
        $month = 'V';
    } else if (date('m') == 4) {
        $month = 'IV';
    } else if (date('m') == 3) {
        $month = 'III';
    } else if (date('m') == 2) {
        $month = 'II';
    } else {
        $month = 'I';
    }

    return 'No. ' . $surat_ke . '/' . str_replace(' ', '', $smtp_apps->nama) . '/' . $param . '/' . $month . '/' . date('Y');
}

function aktifitas_user($menu, $message, $kode, $value, $detail = [], $detail_sebelum = [])
{
    $CI       = &get_instance();
    $sess     = $CI->session->userdata('email');
    $cabang   = $CI->session->userdata('init_cabang');
    $shift    = $CI->session->userdata('shift');

    // Ensure $detail is a string
    // $details  = empty($detail) ? '' : '<br>Detail: ' . implode(', ', $detail);

    $aktifitas = [
        'email'           => $sess,
        'kegiatan'        => $sess . " Telah <b>" . htmlspecialchars($message) . " " . htmlspecialchars($value) . "</b> dengan kode/inv <b>" . htmlspecialchars($kode) . "</b>",
        'detail_kegiatan' => $detail,
        'detail_sebelum'  => $detail_sebelum,
        'menu'            => $menu,
        'waktu'           => date('Y-m-d H:i:s'),
        'kode_cabang'     => $cabang,
        'shift'           => $shift,
    ];

    $CI->db->insert("activity_user", $aktifitas);
}


function aktifitas_user_transaksi($menu, $message, $kode)
{
    $CI         = &get_instance();
    $sess       = $CI->session->userdata('email');
    $cabang     = $CI->session->userdata('init_cabang');
    $shift      = $CI->session->userdata('shift');

    $aktifitas = [
        'email'         => $sess,
        'kegiatan'      => $sess . " Telah <b>" . $message . "</b> dengan kode/inv <b>" . $kode . "</b>",
        'menu'          => $menu,
        'waktu'         => date('Y-m-d H:i:s'),
        'kode_cabang'   => $cabang,
        'shift'         => $shift,
    ];

    $CI->db->insert("activity_user", $aktifitas);
}

function master_kode($keterangan, $digit, $param1 = '', $param2 = '', $param3 = '', $param4 = '', $param5 = '')
{
    $CI         = &get_instance();

    $lastNumber = $CI->M_global->getData('master_kode', ['keterangan' => $keterangan]);

    $params     = $param1 . $param2 . $param3 . $param4 . $param5;

    if ($lastNumber) {
        if ($lastNumber->urut < 1) {
            $last = 1;
        } else {
            $last = $lastNumber->urut + 1;
        }

        $generator = sprintf("%0" . ($digit - (strlen($params))) . "d", $last);

        $kode = $params . $generator;

        $CI->M_global->updateData('master_kode', ['urut' => $last], ['keterangan' => $keterangan]);
    } else {
        $data = [
            'keterangan' => $keterangan,
            'digit'      => $digit,
            'param1'     => $param1,
            'param2'     => $param2,
            'param3'     => $param3,
            'param4'     => $param4,
            'param5'     => $param5,
            'urut'       => 1,
        ];

        $CI->M_global->insertData('master_kode', $data);

        $generator = sprintf("%0" . ($digit - (strlen($params))) . "d", 1);

        $kode = $params . $generator;
    }

    return $kode;
}

function _codeUser($nama)
{
    $CI         = &get_instance();

    $inisial    = strtoupper(substr($nama, 0, 1));
    $lastNumber = $CI->db->query('SELECT * FROM user WHERE nama LIKE "' . $inisial . '%" ORDER BY nama DESC LIMIT 1')->row();
    $number     = 1;
    if ($lastNumber) {
        $number       = count($CI->db->query('SELECT * FROM user WHERE nama LIKE "' . $inisial . '%"')->result()) + 1;
        $kode_user    = $inisial . sprintf("%05d", $number);
    } else {
        $number       = 0;
        $kode_user    = $inisial . "00001";
    }
    return $kode_user;
}

function _caraMasuk()
{
    $CI         = &get_instance();

    $inisial    = 'CM';
    $lastNumber = $CI->db->query('SELECT * FROM m_cara_masuk WHERE kode_masuk LIKE "' . $inisial . '%" ORDER BY kode_masuk DESC LIMIT 1')->row();
    $number     = 1;
    if ($lastNumber) {
        $number       = count($CI->db->query('SELECT * FROM m_cara_masuk WHERE kode_masuk LIKE "' . $inisial . '%"')->result()) + 1;
        $kode_user    = $inisial . sprintf("%08d", $number);
    } else {
        $number       = 0;
        $kode_user    = $inisial . "00000001";
    }
    return $kode_user;
}

function _kodeRole()
{
    $CI         = &get_instance();

    $inisial    = "R";
    $lastNumber = $CI->db->query('SELECT * FROM m_role ORDER BY kode_role DESC LIMIT 1')->row();
    $number     = 1;
    if ($lastNumber) {
        $number       = count($CI->db->query('SELECT * FROM m_role')->result()) + 1;
        $kode_user    = $inisial . sprintf("%04d", $number);
    } else {
        $number       = 0;
        $kode_user    = $inisial . "0001";
    }
    return $kode_user;
}

function _kodeTarif($jenis, $bayar, $kelas)
{
    $CI         = &get_instance();

    if ($jenis == 1) {
        $inisial    = "TRF-S";
    } else {
        $inisial    = "TRF-P";
    }

    $lastNumber = $CI->db->query('SELECT * FROM m_tarif WHERE jenis = "' . $jenis . '" AND jenis_bayar = "' . $bayar . '" AND kelas = "' . $kelas . '" ORDER BY id DESC LIMIT 1')->row();
    $number     = 1;

    if ($lastNumber) {
        $number       = count($CI->db->query('SELECT * FROM m_tarif WHERE jenis = "' . $jenis . '" AND jenis_bayar = "' . $bayar . '" AND kelas = "' . $kelas . '"')->result()) + 1;
        $kode_user    = $inisial . sprintf("%05d", $number);
    } else {
        $number       = 0;
        $kode_user    = $inisial . "00001";
    }
    return $kode_user;
}

function _kodeTindakan($jenis, $kategori)
{
    $CI             = &get_instance();
    $inisial_kat    = $CI->db->query('SELECT inisial_kode FROM kategori_tarif WHERE kode_kategori = "' . $kategori . '"')->row()->inisial_kode;

    if ($jenis == 1) {
        $inisial    = "TRS-" . $inisial_kat;
    } else {
        $inisial    = "TRP-" . $inisial_kat;
    }

    $lastNumber     = $CI->db->query('SELECT * FROM m_tindakan WHERE jenis = "' . $jenis . '" ORDER BY id DESC LIMIT 1')->row();
    $number         = 1;

    if ($lastNumber) {
        $number     = count($CI->db->query('SELECT * FROM m_tindakan WHERE jenis = "' . $jenis . '"')->result()) + 1;
        $new_kode   = $inisial . sprintf("%03d", $number);
    } else {
        $number     = 0;
        $new_kode   = $inisial . "001";
    }
    return $new_kode;
}

function _kodeMultiprice($penjamin, $kelas, $kode_poli)
{
    $CI          = &get_instance();

    $id_penjamin = $CI->db->query('SELECT id FROM m_jenis_bayar WHERE kode_jenis_bayar = "' . $penjamin . '"')->row()->id;
    $id_poli     = $CI->db->query('SELECT id FROM m_poli WHERE kode_poli = "' . $kode_poli . '"')->row()->id;
    $id_kelas    = $kelas;

    $inisial     = "MP-" . $id_poli . '-' . $id_penjamin . '-' . $id_kelas . '-';
    $count       = strlen($inisial);
    $cn          = 20 - $count;

    $lastNumber  = $CI->db->query('SELECT * FROM multiprice_tindakan WHERE kode_multiprice LIKE "' . $inisial . '%" ORDER BY id DESC LIMIT 1')->row();

    $number      = 1;

    if ($lastNumber) {
        $number  = count($CI->db->query('SELECT * FROM multiprice_tindakan WHERE kode_multiprice LIKE "' . $inisial . '%"')->result()) + 1;
        $kode    = $inisial . sprintf("%0" . $cn . "d", $number);
    } else {
        $kode    = $inisial . sprintf("%0" . $cn . "d", 1);
    }

    return $kode;
}

function _kodePaketKunjungan()
{
    $CI         = &get_instance();

    $inisial    = "PKT-KJN-";
    $lastNumber = $CI->db->query('SELECT * FROM paket_kunjungan ORDER BY kode_paket DESC LIMIT 1')->row();
    $number     = 1;
    if ($lastNumber) {
        $number  = $CI->db->query('SELECT * FROM paket_kunjungan')->num_rows() + 1;
        $kode    = $inisial . sprintf("%012d", $number);
    } else {
        $number  = 0;
        $kode    = $inisial . "000000000001";
    }
    return $kode;
}

function _kodeCabang()
{
    $CI         = &get_instance();

    $inisial    = "CAB";
    $lastNumber = $CI->db->query('SELECT * FROM cabang ORDER BY kode_cabang DESC LIMIT 1')->row();
    $number     = 1;
    if ($lastNumber) {
        $number       = count($CI->db->query('SELECT * FROM cabang')->result()) + 1;
        $kode_user    = $inisial . sprintf("%07d", $number);
    } else {
        $number       = 0;
        $kode_user    = $inisial . "0000001";
    }
    return $kode_user;
}

function _kodeBarang($keterangan)
{
    $CI             = &get_instance();

    $kode_cabang    = $CI->session->userdata('init_cabang');

    $inisial        = strtoupper(substr($keterangan, 0, 1));
    $lastNumber     = $CI->db->query('SELECT * FROM barang WHERE kode_barang LIKE "' . $kode_cabang . $inisial . '%" ORDER BY kode_barang DESC LIMIT 1')->row();
    $number         = 1;
    if ($lastNumber) {
        $number       = count($CI->db->query('SELECT * FROM barang WHERE kode_barang LIKE "' . $kode_cabang . $inisial . '%"')->result()) + 1;
        $kode_user    = $kode_cabang . '~' . $inisial . sprintf("%05d", $number);
    } else {
        $number       = 0;
        $kode_user    = $kode_cabang . '~' . $inisial . "00001";
    }
    return $kode_user;
}

function _codeMember($nama)
{
    $CI         = &get_instance();

    $inisial    = strtoupper(substr($nama, 0, 1));
    $lastNumber = $CI->db->query('SELECT * FROM member WHERE nama LIKE "' . $inisial . '%" ORDER BY nama DESC LIMIT 1')->row();
    $number     = 1;
    if ($lastNumber) {
        $number         = count($CI->db->query('SELECT * FROM member WHERE nama LIKE "' . $inisial . '%"')->result()) + 1;
        $kode_member    = "MBR-" . $inisial . sprintf("%05d", $number);
    } else {
        $number         = 0;
        $kode_member    = "MBR-" . $inisial . "00001";
    }
    return $kode_member;
}

function _kodeDokter($keterangan)
{
    $CI         = &get_instance();

    $inisial    = strtoupper(substr($keterangan, 0, 1));
    $lastNumber = $CI->db->query('SELECT * FROM dokter WHERE kode_dokter LIKE "DR' . $inisial . '%" ORDER BY kode_dokter DESC LIMIT 1')->row();
    $number     = 1;
    if ($lastNumber) {
        $number       = count($CI->db->query('SELECT * FROM dokter WHERE kode_dokter LIKE "DR' . $inisial . '%"')->result()) + 1;
        $kode_user    = 'DR' . $inisial . sprintf("%05d", $number);
    } else {
        $number       = 0;
        $kode_user    = 'DR' . $inisial . "00001";
    }
    return $kode_user;
}

function _kodePerawat($keterangan)
{
    $CI         = &get_instance();

    $inisial    = strtoupper(substr($keterangan, 0, 1));
    $lastNumber = $CI->db->query('SELECT * FROM perawat WHERE kode_perawat LIKE "PR' . $inisial . '%" ORDER BY kode_perawat DESC LIMIT 1')->row();
    $number     = 1;
    if ($lastNumber) {
        $number       = count($CI->db->query('SELECT * FROM perawat WHERE kode_perawat LIKE "PR' . $inisial . '%"')->result()) + 1;
        $kode_user    = 'PR' . $inisial . sprintf("%05d", $number);
    } else {
        $number       = 0;
        $kode_user    = 'PR' . $inisial . "00001";
    }
    return $kode_user;
}

function _noAntrian($kode_poli, $kode_cabang, $tgl_daftar)
{
    $CI           = &get_instance();

    $poli         = $CI->db->get_where('m_poli', ['kode_poli' => $kode_poli])->row();

    $lastNumber   = $CI->db->query('SELECT * FROM pendaftaran WHERE kode_cabang = "' . $kode_cabang . '" AND tgl_daftar = "' . $tgl_daftar . '" AND kode_poli = "' . $kode_poli . '" ORDER BY id DESC LIMIT 1')->row();
    $number       = 1;
    if ($lastNumber) {
        $reservasi = $CI->db->query('SELECT * FROM reservasi WHERE kode_cabang = "' . $kode_cabang . '" AND kode_poli = "' . $kode_poli . '" AND tgl = "' . $tgl_daftar . '" AND status_reservasi = 0 ORDER BY id DESC LIMIT 1')->row();

        if ($reservasi) {
            $number       = intval(preg_replace('/[^0-9]/', '', $reservasi->no_antrian)) + 1;
        } else {
            $number       = count($CI->db->query('SELECT * FROM pendaftaran WHERE kode_cabang = "' . $kode_cabang . '" AND tgl_daftar = "' . $tgl_daftar . '" AND kode_poli = "' . $kode_poli . '"')->result()) + 1;
        }

        $kode_user    = $poli->inisial_room . sprintf("%05d", $number);
    } else {
        $number       = 0;
        $kode_user    = $poli->inisial_room . "00001";
    }
    return $kode_user;
}

function _kodeTrx($kode_poli, $kode_cabang)
{
    $CI           = &get_instance();

    $now          = date('Y-m-d');

    $poli         = $CI->db->get_where('m_poli', ['kode_poli' => $kode_poli])->row();

    $awal         = strtoupper(substr($poli->keterangan, 0, 2));

    $lastNumber   = $CI->db->query('SELECT * FROM pendaftaran WHERE kode_cabang = "' . $kode_cabang . '" AND tgl_daftar = "' . $now . '" AND kode_poli = "' . $kode_poli . '" ORDER BY id DESC LIMIT 1')->row();
    $number       = 1;
    if ($lastNumber) {
        $number       = count($CI->db->query('SELECT * FROM pendaftaran WHERE kode_cabang = "' . $kode_cabang . '" AND tgl_daftar = "' . $now . '" AND kode_poli = "' . $kode_poli . '"')->result()) + 1;
        $kode_user    = $CI->session->userdata('init_cabang') . 'P' . $awal . '-' . date('Ymd') . sprintf("%05d", $number);
    } else {
        $number       = 0;
        $kode_user    = $CI->session->userdata('init_cabang') . 'P' . $awal . '-' . date('Ymd') . "00001";
    }
    return $kode_user;
}

function _invoicePO($cabang)
{
    $CI           = &get_instance();

    $now          = date('Y-m-d');

    $lastNumber   = $CI->db->query('SELECT * FROM barang_po_in_header WHERE tgl_po = "' . $now . '" AND kode_cabang = "' . $cabang . '" ORDER BY id DESC LIMIT 1')->row();
    $number       = 1;
    if ($lastNumber) {
        $number   = $CI->db->query('SELECT * FROM barang_po_in_header WHERE tgl_po = "' . $now . '" AND kode_cabang = "' . $cabang . '"')->num_rows() + 1;
        $invoice  = $CI->session->userdata('init_cabang') . 'TPO-' . date('Ymd') . sprintf("%05d", $number);
    } else {
        $number   = 0;
        $invoice  = $CI->session->userdata('init_cabang') . 'TPO-' . date('Ymd') . "00001";
    }
    return $invoice;
}

function _invoicePOM($cabang)
{
    $CI           = &get_instance();

    $now          = date('Y-m-d');

    $lastNumber   = $CI->db->query('SELECT * FROM mutasi_po_header WHERE tgl_po = "' . $now . '" AND kode_cabang = "' . $cabang . '" ORDER BY id DESC LIMIT 1')->row();
    $number       = 1;
    if ($lastNumber) {
        $number   = $CI->db->query('SELECT * FROM mutasi_po_header WHERE tgl_po = "' . $now . '" AND kode_cabang = "' . $cabang . '"')->num_rows() + 1;
        $invoice  = $CI->session->userdata('init_cabang') . 'MPO-' . date('Ymd') . sprintf("%05d", $number);
    } else {
        $number   = 0;
        $invoice  = $CI->session->userdata('init_cabang') . 'MPO-' . date('Ymd') . "00001";
    }
    return $invoice;
}

function _invoiceMutasi($cabang)
{
    $CI           = &get_instance();

    $now          = date('Y-m-d');

    $lastNumber   = $CI->db->query('SELECT * FROM mutasi_header WHERE tgl = "' . $now . '" AND kode_cabang = "' . $cabang . '" ORDER BY id DESC LIMIT 1')->row();
    $number       = 1;
    if ($lastNumber) {
        $number   = $CI->db->query('SELECT * FROM mutasi_header WHERE tgl = "' . $now . '" AND kode_cabang = "' . $cabang . '"')->num_rows() + 1;
        $invoice  = $CI->session->userdata('init_cabang') . 'MTS-' . date('Ymd') . sprintf("%05d", $number);
    } else {
        $number   = 0;
        $invoice  = $CI->session->userdata('init_cabang') . 'MTS-' . date('Ymd') . "00001";
    }
    return $invoice;
}

function _invoice($cabang)
{
    $CI           = &get_instance();

    $now          = date('Y-m-d');

    $lastNumber   = $CI->db->query('SELECT * FROM barang_in_header WHERE tgl_beli = "' . $now . '" AND kode_cabang = "' . $cabang . '" ORDER BY id DESC LIMIT 1')->row();
    $number       = 1;
    if ($lastNumber) {
        $number   = $CI->db->query('SELECT * FROM barang_in_header WHERE tgl_beli = "' . $now . '" AND kode_cabang = "' . $cabang . '"')->num_rows() + 1;
        $invoice  = $CI->session->userdata('init_cabang') . 'TPB-' . date('Ymd') . sprintf("%05d", $number);
    } else {
        $number   = 0;
        $invoice  = $CI->session->userdata('init_cabang') . 'TPB-' . date('Ymd') . "00001";
    }
    return $invoice;
}

function _invoiceMutasiKas($cabang)
{
    $CI           = &get_instance();

    $now          = date('Y-m-d');

    $lastNumber   = $CI->db->query('SELECT * FROM mutasi_kas WHERE tgl_mutasi = "' . $now . '" AND kode_cabang = "' . $cabang . '" ORDER BY id DESC LIMIT 1')->row();
    $number       = 1;
    if ($lastNumber) {
        $number   = $CI->db->query('SELECT * FROM mutasi_kas WHERE tgl_mutasi = "' . $now . '" AND kode_cabang = "' . $cabang . '"')->num_rows() + 1;
        $invoice  = $CI->session->userdata('init_cabang') . 'MKB-' . date('Ymd') . sprintf("%05d", $number);
    } else {
        $number   = 0;
        $invoice  = $CI->session->userdata('init_cabang') . 'MKB-' . date('Ymd') . "00001";
    }
    return $invoice;
}

function _noPiutang($cabang)
{
    $CI           = &get_instance();

    $now          = date('Y-m-d');

    $lastNumber   = $CI->db->query('SELECT * FROM piutang WHERE tanggal = "' . $now . '" AND kode_cabang = "' . $cabang . '" ORDER BY id DESC LIMIT 1')->row();
    $number       = 1;
    if ($lastNumber) {
        $number   = $CI->db->query('SELECT * FROM piutang WHERE tanggal = "' . $now . '" AND kode_cabang = "' . $cabang . '"')->num_rows() + 1;
        $invoice  = $CI->session->userdata('init_cabang') . 'PUT-' . date('Ymd') . sprintf("%05d", $number);
    } else {
        $number   = 0;
        $invoice  = $CI->session->userdata('init_cabang') . 'PUT-' . date('Ymd') . "00001";
    }
    return $invoice;
}

function _surat_jalan($cabang)
{
    $CI           = &get_instance();

    $now          = date('Y-m-d');

    $lastNumber   = $CI->db->query('SELECT * FROM barang_in_header WHERE tgl_beli = "' . $now . '" AND kode_cabang = "' . $cabang . '" ORDER BY id DESC LIMIT 1')->row();
    $number       = 1;
    if ($lastNumber) {
        $number   = $CI->db->query('SELECT * FROM barang_in_header WHERE tgl_beli = "' . $now . '" AND kode_cabang = "' . $cabang . '"')->num_rows() + 1;
        $invoice  = 'NSJ~' . $CI->session->userdata('init_cabang') . date('dmY') . sprintf("%05d", $number);
    } else {
        $number   = 0;
        $invoice  = 'NSJ~' . $CI->session->userdata('init_cabang') . date('dmY') . "00001";
    }
    return $invoice;
}

function _no_faktur($cabang)
{
    $CI           = &get_instance();

    $now          = date('Y-m-d');

    $lastNumber   = $CI->db->query('SELECT * FROM barang_in_header WHERE tgl_beli = "' . $now . '" AND kode_cabang = "' . $cabang . '" ORDER BY id DESC LIMIT 1')->row();
    $number       = 1;
    if ($lastNumber) {
        $number   = $CI->db->query('SELECT * FROM barang_in_header WHERE tgl_beli = "' . $now . '" AND kode_cabang = "' . $cabang . '"')->num_rows() + 1;
        $invoice  = 'NSF~' . $CI->session->userdata('init_cabang') . date('dmY') . sprintf("%05d", $number);
    } else {
        $number   = 0;
        $invoice  = 'NSF~' . $CI->session->userdata('init_cabang') . date('dmY') . "00001";
    }
    return $invoice;
}

function _invoice_retur($cabang)
{
    $CI           = &get_instance();

    $now          = date('Y-m-d');

    $lastNumber   = $CI->db->query('SELECT * FROM barang_in_retur_header WHERE tgl_retur = "' . $now . '" AND kode_cabang = "' . $cabang . '" ORDER BY id DESC LIMIT 1')->row();
    $number       = 1;
    if ($lastNumber) {
        $number   = $CI->db->query('SELECT * FROM barang_in_retur_header WHERE tgl_retur = "' . $now . '" AND kode_cabang = "' . $cabang . '"')->num_rows() + 1;
        $invoice  = $CI->session->userdata('init_cabang') . 'TRB-' . date('Ymd') . sprintf("%05d", $number);
    } else {
        $number   = 0;
        $invoice  = $CI->session->userdata('init_cabang') . 'TRB-' . date('Ymd') . "00001";
    }
    return $invoice;
}

function konversi_show_satuan($s_akhir, $kode_barang)
{
    $CI = &get_instance();
    $kode_cabang = $CI->session->userdata("cabang");
    $barang = $CI->M_global->getData('barang', ['kode_barang' => $kode_barang]);

    $satuan1 = $CI->M_global->getData('m_satuan', ['kode_satuan' => $barang->kode_satuan]);
    $satuan2 = $barang->kode_satuan2 ? $CI->M_global->getData('m_satuan', ['kode_satuan' => $barang->kode_satuan2]) : null;
    $satuan3 = $barang->kode_satuan3 ? $CI->M_global->getData('m_satuan', ['kode_satuan' => $barang->kode_satuan3]) : null;

    $sat = '';

    $isMinus = $s_akhir < 0;
    $abs_s_akhir = abs($s_akhir);

    if ($satuan3 && $barang->qty_satuan3 > 0) {
        $stok3 = floor($abs_s_akhir / $barang->qty_satuan3);
        $sisa3 = $abs_s_akhir % $barang->qty_satuan3;

        if ($stok3 > 0) {
            $sat .= number_format($stok3) . ' ' . $satuan3->keterangan;
        }

        if ($satuan2 && $barang->qty_satuan2 > 0) {
            $stok2 = floor($sisa3 / $barang->qty_satuan2);
            $sisa2 = $sisa3 % $barang->qty_satuan2;

            if ($stok2 > 0) {
                $sat .= ($sat ? '<br>' : '') . number_format($stok2) . ' ' . $satuan2->keterangan;
            }

            if ($sisa2 > 0 || (!$stok3 && !$stok2)) {
                $sat .= ($sat ? '<br>' : '') . number_format($sisa2) . ' ' . $satuan1->keterangan;
            }
        } else {
            if ($sisa3 > 0 || !$stok3) {
                $sat .= ($sat ? '<br>' : '') . number_format($sisa3) . ' ' . $satuan1->keterangan;
            }
        }
    } elseif ($satuan2 && $barang->qty_satuan2 > 0) {
        $stok2 = floor($abs_s_akhir / $barang->qty_satuan2);
        $sisa2 = $abs_s_akhir % $barang->qty_satuan2;

        if ($stok2 > 0) {
            $sat .= number_format($stok2) . ' ' . $satuan2->keterangan;
        }

        if ($sisa2 > 0 || !$stok2) {
            $sat .= ($sat ? '<br>' : '') . number_format($sisa2) . ' ' . $satuan1->keterangan;
        }
    } else {
        $sat = number_format($abs_s_akhir) . ' ' . $satuan1->keterangan;
    }

    if ($isMinus) {
        $sat = '-' . $sat;
    }

    return $sat;
}


function hitungStokBrgIn($detail, $kode_gudang, $invoice)
{
    $CI   = &get_instance();

    $date = date('Y-m-d');
    $time = date('H:i:s');
    $user = $CI->session->userdata('kode_user');

    $kode_cabang = $CI->session->userdata("cabang");

    foreach ($detail as $d) {
        $cek = $CI->M_global->jumDataRow('barang_stok', ['kode_gudang' => $kode_gudang, 'kode_barang' => $d->kode_barang, 'kode_cabang' => $kode_cabang]);

        if ($cek < 1) {
            $isi_stok = [
                'kode_cabang'   => $kode_cabang,
                'kode_barang'   => $d->kode_barang,
                'kode_gudang'   => $kode_gudang,
                'masuk'         => $d->qty_konversi,
                'akhir'         => $d->qty_konversi,
                'last_tgl_trx'  => $date,
                'last_jam_trx'  => $time,
                'last_no_trx'   => $invoice,
                'last_user'     => $user,
            ];

            $CI->M_global->insertData('barang_stok', $isi_stok);
        } else {
            $CI->db->query("UPDATE barang_stok SET 
            masuk           = masuk + $d->qty_konversi, 
            akhir           = akhir + $d->qty_konversi, 
            last_tgl_trx    = '$date', 
            last_jam_trx    = '$time',
            last_no_trx     = '$invoice',
            last_user       = '$user' 
            WHERE kode_barang = '$d->kode_barang' AND kode_gudang = '$kode_gudang' AND kode_cabang = '$kode_cabang'");
        }
    }
}

function hitungStokBrgOut($detail, $kode_gudang, $invoice)
{
    $CI   = &get_instance();

    $date = date('Y-m-d');
    $time = date('H:i:s');
    $user = $CI->session->userdata('kode_user');

    $kode_cabang = $CI->session->userdata("cabang");

    foreach ($detail as $d) {
        $cek = $CI->M_global->jumDataRow('barang_stok', ['kode_gudang' => $kode_gudang, 'kode_barang' => $d->kode_barang, 'kode_cabang' => $kode_cabang]);

        if ($cek < 1) {
            $isi_stok = [
                'kode_cabang'   => $d->kode_cabang,
                'kode_barang'   => $d->kode_barang,
                'kode_gudang'   => $kode_gudang,
                'masuk'         => 0 - $d->qty_konversi,
                'akhir'         => 0 - $d->qty_konversi,
                'last_tgl_trx'  => $date,
                'last_jam_trx'  => $time,
                'last_no_trx'   => $invoice,
                'last_user'     => $user,
            ];

            $CI->M_global->insertData('barang_stock', $isi_stok);
        } else {
            $CI->db->query("UPDATE barang_stok SET 
            masuk = masuk - $d->qty_konversi, 
            akhir = akhir - $d->qty_konversi, 
            last_tgl_trx = '$date', 
            last_jam_trx = '$time',
            last_no_trx = '$invoice',
            last_user = '$user' 
            WHERE kode_barang = '$d->kode_barang' AND kode_gudang = '$kode_gudang' AND kode_cabang = '$kode_cabang'");
        }
    }
}

function hitungStokBrgRtIn($detail, $kode_gudang, $invoice)
{
    $CI   = &get_instance();

    $kode_cabang = $CI->session->userdata('cabang');

    $date = date('Y-m-d');
    $time = date('H:i:s');
    $user = $CI->session->userdata('kode_user');

    foreach ($detail as $d) {
        $cek = $CI->M_global->jumDataRow('barang_stok', ['kode_gudang' => $kode_gudang, 'kode_cabang' => $kode_cabang, 'kode_barang' => $d->kode_barang]);

        if ($cek < 1) {
            $isi_stok = [
                'kode_barang'   => $d->kode_barang,
                'kode_cabang'   => $kode_cabang,
                'kode_gudang'   => $kode_gudang,
                'keluar'        => $d->qty_konversi,
                'akhir'         => 0 - $d->qty_konversi,
                'last_tgl_trx'  => $date,
                'last_jam_trx'  => $time,
                'last_no_trx'   => $invoice,
                'last_user'     => $user,
            ];

            $CI->M_global->insertData('barang_stok', $isi_stok);
        } else {
            $CI->db->query("UPDATE barang_stok SET 
            keluar          = keluar + $d->qty_konversi, 
            akhir           = akhir - $d->qty_konversi, 
            last_tgl_trx    = '$date', 
            last_jam_trx    = '$time',
            last_no_trx     = '$invoice',
            last_user       = '$user' 
            WHERE kode_barang = '$d->kode_barang' AND kode_gudang = '$kode_gudang' AND kode_cabang = '$kode_cabang'");
        }
    }
}

function hitungStokBrgRtOut($detail, $kode_gudang, $invoice)
{
    $CI   = &get_instance();

    $date = date('Y-m-d');
    $time = date('H:i:s');
    $user = $CI->session->userdata('kode_user');
    $kode_cabang = $CI->session->userdata('cabang');

    foreach ($detail as $d) {
        $cek = $CI->M_global->jumDataRow('barang_stok', ['kode_gudang' => $kode_gudang, 'kode_cabang' => $kode_cabang, 'kode_barang' => $d->kode_barang]);

        if ($cek < 1) {
            $isi_stok = [
                'kode_barang'   => $d->kode_barang,
                'kode_cabang'   => $kode_cabang,
                'kode_gudang'   => $kode_gudang,
                'keluar'        => 0 - $d->qty_konversi,
                'akhir'         => $d->qty_konversi,
                'last_tgl_trx'  => $date,
                'last_jam_trx'  => $time,
                'last_no_trx'   => $invoice,
                'last_user'     => $user,
            ];

            $CI->M_global->insertData('barang_stock', $isi_stok);
        } else {
            $CI->db->query("UPDATE barang_stok SET 
            keluar = keluar - $d->qty_konversi, 
            akhir = akhir + $d->qty_konversi, 
            last_tgl_trx = '$date', 
            last_jam_trx = '$time',
            last_no_trx = '$invoice',
            last_user = '$user' 
            WHERE kode_barang = '$d->kode_barang' AND kode_gudang = '$kode_gudang' AND kode_cabang = '$kode_cabang'");
        }
    }
}

function _invoiceJual($cabang)
{
    $CI           = &get_instance();

    $now          = date('Y-m-d');

    $lastNumber   = $CI->db->query('SELECT * FROM barang_out_header WHERE tgl_jual = "' . $now . '" AND kode_cabang = "' . $cabang . '" ORDER BY id DESC LIMIT 1')->row();
    $number       = 1;
    if ($lastNumber) {
        $number   = $CI->db->query('SELECT * FROM barang_out_header WHERE tgl_jual = "' . $now . '" AND kode_cabang = "' . $cabang . '"')->num_rows() + 1;
        $invoice  = $CI->session->userdata('init_cabang') . 'TJB-' . date('Ymd') . sprintf("%05d", $number);
    } else {
        $number   = 0;
        $invoice  = $CI->session->userdata('init_cabang') . 'TJB-' . date('Ymd') . "00001";
    }
    return $invoice;
}

function hitungStokJualOut($detail, $kode_gudang, $invoice)
{
    $CI   = &get_instance();

    $kode_cabang = $CI->session->userdata('cabang');

    $date = date('Y-m-d');
    $time = date('H:i:s');
    $user = $CI->session->userdata('kode_user');

    foreach ($detail as $d) {
        $cek = $CI->M_global->jumDataRow('barang_stok', ['kode_gudang' => $kode_gudang, 'kode_barang' => $d->kode_barang, 'kode_cabang' => $kode_cabang]);

        if ($cek < 1) {
            $isi_stok = [
                'kode_barang'   => $d->kode_barang,
                'kode_cabang'   => $kode_cabang,
                'kode_gudang'   => $kode_gudang,
                'keluar'        => 0 - $d->qty_konversi,
                'akhir'         => $d->qty_konversi,
                'last_tgl_trx'  => $date,
                'last_jam_trx'  => $time,
                'last_no_trx'   => $invoice,
                'last_user'     => $user,
            ];

            $CI->M_global->insertData('barang_stock', $isi_stok);
        } else {
            $CI->db->query("UPDATE barang_stok SET 
            keluar = keluar - $d->qty_konversi, 
            akhir = akhir + $d->qty_konversi, 
            last_tgl_trx = '$date', 
            last_jam_trx = '$time',
            last_no_trx = '$invoice',
            last_user = '$user' 
            WHERE kode_barang = '$d->kode_barang' AND kode_gudang = '$kode_gudang' AND kode_cabang = '$kode_cabang'");
        }
    }
}

function hitungStokJualIn($detail, $kode_gudang, $invoice)
{
    $CI   = &get_instance();

    $kode_cabang = $CI->session->userdata('cabang');

    $date = date('Y-m-d');
    $time = date('H:i:s');
    $user = $CI->session->userdata('kode_user');

    foreach ($detail as $d) {
        $cek = $CI->M_global->jumDataRow('barang_stok', ['kode_gudang' => $kode_gudang, 'kode_cabang' => $kode_cabang, 'kode_barang' => $d->kode_barang]);

        if ($cek < 1) {
            $isi_stok = [
                'kode_barang'   => $d->kode_barang,
                'kode_cabang'   => $kode_cabang,
                'kode_gudang'   => $kode_gudang,
                'keluar'        => $d->qty_konversi,
                'akhir'         => 0 - $d->qty_konversi,
                'last_tgl_trx'  => $date,
                'last_jam_trx'  => $time,
                'last_no_trx'   => $invoice,
                'last_user'     => $user,
            ];

            $CI->M_global->insertData('barang_stock', $isi_stok);
        } else {
            $CI->db->query("UPDATE barang_stok SET 
            keluar = keluar + $d->qty_konversi, 
            akhir = akhir - $d->qty_konversi, 
            last_tgl_trx = '$date', 
            last_jam_trx = '$time',
            last_no_trx = '$invoice',
            last_user = '$user' 
            WHERE kode_barang = '$d->kode_barang' AND kode_gudang = '$kode_gudang' AND kode_cabang = '$kode_cabang'");
        }
    }
}

function generatorToken($jum)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';

    for ($i = 0; $i < $jum; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }
    return $randomString;
}

function tokenKasir($jum)
{
    $CI             = &get_instance();
    $generatorToken = generatorToken($jum);
    $cek_token      = $CI->db->query("SELECT * FROM pembayaran WHERE token_pembayaran = '$generatorToken'")->num_rows();

    if ($cek_token > 0) {
        generatorToken($jum);
    } else {
        return $generatorToken;
    }
}

function _invoiceKasir($cabang)
{
    $CI           = &get_instance();

    $init_cabang = $CI->session->userdata('init_cabang');

    $now          = date('Y-m-d');

    $lastNumber   = $CI->db->query('SELECT * FROM pembayaran WHERE kode_cabang = "' . $cabang . '" AND tgl_pembayaran = "' . $now . '" ORDER BY id DESC LIMIT 1')->row();
    $number       = 1;
    if ($lastNumber) {
        $number   = $CI->db->query('SELECT * FROM pembayaran WHERE kode_cabang = "' . $cabang . '" AND tgl_pembayaran = "' . $now . '"')->num_rows() + 1;
        $invoice  = $init_cabang . 'KWI-' . date('Ymd') . sprintf("%05d", $number);
    } else {
        $number   = 0;
        $invoice  = $init_cabang . 'KWI-' . date('Ymd') . "00001";
    }
    return $invoice;
}

function _invoiceDepoUM()
{
    $CI           = &get_instance();

    $now          = date('Y-m-d');

    $lastNumber   = $CI->db->query('SELECT * FROM pembayaran_uangmuka WHERE tgl_pembayaran = "' . $now . '" ORDER BY id DESC LIMIT 1')->row();
    $number       = 1;
    if ($lastNumber) {
        $number   = $CI->db->query('SELECT * FROM pembayaran_uangmuka WHERE tgl_pembayaran = "' . $now . '"')->num_rows() + 1;
        $invoice  = 'UM-' . date('Ymd') . sprintf("%05d", $number);
    } else {
        $number   = 0;
        $invoice  = 'UM-' . date('Ymd') . "00001";
    }
    return $invoice;
}

function _invoiceChart($user)
{
    $CI           = &get_instance();

    $now          = date('Y-m-d');

    $lastNumber   = $CI->db->query('SELECT * FROM cart_header WHERE tgl_order = "' . $now . '" ORDER BY id DESC LIMIT 1')->row();
    $number       = 1;
    if ($lastNumber) {
        $number   = $CI->db->query('SELECT * FROM cart_header WHERE tgl_order = "' . $now . '"')->num_rows() + 1;
        $invoice  = 'ORDER~' . $user . '/' . date('dmY') . sprintf("%05d", $number);
    } else {
        $number   = 0;
        $invoice  = 'ORDER~' . $user . '/' . date('dmY') . "00001";
    }
    return $invoice;
}

function _invoiceRetur($cabang)
{
    $CI           = &get_instance();

    $init_cabang = $CI->session->userdata('init_cabang');

    $now          = date('Y-m-d');

    $lastNumber   = $CI->db->query('SELECT * FROM barang_out_retur_header WHERE kode_cabang = "' . $cabang . '" AND tgl_retur = "' . $now . '" ORDER BY id DESC LIMIT 1')->row();
    $number       = 1;
    if ($lastNumber) {
        $number   = $CI->db->query('SELECT * FROM barang_out_retur_header WHERE kode_cabang = "' . $cabang . '" AND tgl_retur = "' . $now . '"')->num_rows() + 1;
        $invoice  = $init_cabang . 'TRJ-' . date('Ymd') . sprintf("%05d", $number);
    } else {
        $number   = 0;
        $invoice  = $init_cabang . 'TRJ-' . date('Ymd') . "00001";
    }
    return $invoice;
}

function hitungStokReturJualIn($detail, $kode_gudang, $invoice)
{
    $CI   = &get_instance();

    $kode_cabang = $CI->session->userdata('cabang');

    $date = date('Y-m-d');
    $time = date('H:i:s');
    $user = $CI->session->userdata('kode_user');

    foreach ($detail as $d) {
        $cek = $CI->M_global->jumDataRow('barang_stok', ['kode_gudang' => $kode_gudang, 'kode_cabang' => $kode_cabang, 'kode_barang' => $d->kode_barang]);

        if ($cek < 1) {
            $isi_stok = [
                'kode_barang'   => $d->kode_barang,
                'kode_cabang'   => $kode_cabang,
                'kode_gudang'   => $kode_gudang,
                'keluar'        => $d->qty_konversi,
                'masuk'         => 0 - $d->qty_konversi,
                'akhir'         => 0 - $d->qty_konversi,
                'last_tgl_trx'  => $date,
                'last_jam_trx'  => $time,
                'last_no_trx'   => $invoice,
                'last_user'     => $user,
            ];

            $CI->M_global->insertData('barang_stok', $isi_stok);
        } else {
            $CI->db->query("UPDATE barang_stok SET 
            masuk           = masuk - $d->qty_konversi, 
            akhir           = akhir - $d->qty_konversi, 
            last_tgl_trx    = '$date', 
            last_jam_trx    = '$time',
            last_no_trx     = '$invoice',
            last_user       = '$user' 
            WHERE kode_barang = '$d->kode_barang' AND kode_gudang = '$kode_gudang' AND kode_cabang = '$kode_cabang'");
        }
    }
}

function hitungStokReturJualOut($detail, $kode_gudang, $invoice)
{
    $CI   = &get_instance();

    $date = date('Y-m-d');
    $time = date('H:i:s');
    $user = $CI->session->userdata('kode_user');
    $kode_cabang = $CI->session->userdata('cabang');

    foreach ($detail as $d) {
        $cek = $CI->M_global->jumDataRow('barang_stok', ['kode_gudang' => $kode_gudang, 'kode_cabang' => $kode_cabang, 'kode_barang' => $d->kode_barang]);

        if ($cek < 1) {
            $isi_stok = [
                'kode_barang'   => $d->kode_barang,
                'kode_cabang'   => $kode_cabang,
                'kode_gudang'   => $kode_gudang,
                'masuk'         => $d->qty_konversi,
                'akhir'         => $d->qty_konversi,
                'last_tgl_trx'  => $date,
                'last_jam_trx'  => $time,
                'last_no_trx'   => $invoice,
                'last_user'     => $user,
            ];

            $CI->M_global->insertData('barang_stock', $isi_stok);
        } else {
            $CI->db->query("UPDATE barang_stok SET 
            masuk = masuk + $d->qty_konversi, 
            akhir = akhir + $d->qty_konversi, 
            last_tgl_trx = '$date', 
            last_jam_trx = '$time',
            last_no_trx = '$invoice',
            last_user = '$user' 
            WHERE kode_barang = '$d->kode_barang' AND kode_gudang = '$kode_gudang' AND kode_cabang = '$kode_cabang'");
        }
    }
}

function updateUangMukaIn($kode_member, $invoice, $tgl_pembayaran, $jam_pembayaran, $total)
{
    $CI = &get_instance();

    $cek = $CI->M_global->jumDataRow('uang_muka', ['kode_member' => $kode_member]);

    if ($cek > 0) { // jika cek ada um maka update
        $CI->db->query("UPDATE uang_muka SET 
            last_tgl = '$tgl_pembayaran', 
            last_jam = '$jam_pembayaran', 
            last_invoice = '$invoice', 
            uang_masuk = uang_masuk + '$total', 
            uang_sisa = uang_sisa + '$total' 
        WHERE kode_member = '$kode_member'");
    } else { // selain itu inser
        $isi = [
            'last_tgl'      => $tgl_pembayaran,
            'last_jam'      => $jam_pembayaran,
            'last_invoice'  => $invoice,
            'kode_member'   => $kode_member,
            'uang_masuk'    => $total,
            'uang_keluar'   => 0,
            'uang_sisa'     => $total,
        ];

        $CI->db->insert('uang_muka', $isi);
    }
}

function updateUangMukaUpdate($kode_member, $invoice, $tgl_pembayaran, $jam_pembayaran, $total, $um_awal)
{
    $CI = &get_instance();

    $cek = $CI->M_global->jumDataRow('uang_muka', ['kode_member' => $kode_member]);

    if ($um_awal) {
        $total_awal = $um_awal;
    } else {
        $total_awal = 0;
    }

    if ($cek > 0) { // jika cek ada um maka update
        $CI->db->query("UPDATE uang_muka SET 
            last_tgl = '$tgl_pembayaran', 
            last_jam = '$jam_pembayaran', 
            last_invoice = '$invoice', 
            uang_masuk = uang_masuk - '$total_awal' + '$total', 
            uang_sisa = uang_sisa - '$total_awal' + '$total' 
        WHERE kode_member = '$kode_member'");
    } else { // selain itu inser
        $isi = [
            'last_tgl'      => $tgl_pembayaran,
            'last_jam'      => $jam_pembayaran,
            'last_invoice'  => $invoice,
            'kode_member'   => $kode_member,
            'uang_masuk'    => $total,
            'uang_keluar'   => 0,
            'uang_sisa'     => $total,
        ];

        $CI->db->insert('uang_muka', $isi);
    }
}

function updateUangMukaDelete($kode_member, $invoice, $tgl_pembayaran, $jam_pembayaran, $total)
{
    $CI = &get_instance();

    $cek = $CI->M_global->jumDataRow('uang_muka', ['kode_member' => $kode_member]);

    if ($cek > 0) { // jika cek ada um maka update
        $CI->db->query("UPDATE uang_muka SET 
            last_tgl = '$tgl_pembayaran', 
            last_jam = '$jam_pembayaran', 
            last_invoice = '$invoice', 
            uang_masuk = uang_masuk - '$total', 
            uang_sisa = uang_sisa - '$total' 
        WHERE kode_member = '$kode_member'");
    } else { // selain itu inser
        $isi = [
            'last_tgl'      => $tgl_pembayaran,
            'last_jam'      => $jam_pembayaran,
            'last_invoice'  => $invoice,
            'kode_member'   => $kode_member,
            'uang_masuk'    => 0 - $total,
            'uang_keluar'   => 0,
            'uang_sisa'     => 0 - $total,
        ];

        $CI->db->insert('uang_muka', $isi);
    }
}

function hitung_umur($tgl_lahir)
{
    $tanggal_lahir = new DateTime($tgl_lahir);
    $sekarang = new DateTime("today");
    if ($tanggal_lahir > $sekarang) {
        $thn = "0";
        $bln = "0";
        $tgl = "0";
    }
    $thn = $sekarang->diff($tanggal_lahir)->y;
    $bln = $sekarang->diff($tanggal_lahir)->m;
    $tgl = $sekarang->diff($tanggal_lahir)->d;
    return $thn . " tahun " . $bln . " bulan " . $tgl . " hari";
}

function tgl_indo($tgl)
{
    if (date('m', strtotime($tgl)) == 1) {
        $bulan = 'Januari';
    } else if (date('m', strtotime($tgl)) == 2) {
        $bulan = 'Februari';
    } else if (date('m', strtotime($tgl)) == 3) {
        $bulan = 'Maret';
    } else if (date('m', strtotime($tgl)) == 4) {
        $bulan = 'April';
    } else if (date('m', strtotime($tgl)) == 5) {
        $bulan = 'Mei';
    } else if (date('m', strtotime($tgl)) == 6) {
        $bulan = 'Juni';
    } else if (date('m', strtotime($tgl)) == 7) {
        $bulan = 'Juli';
    } else if (date('m', strtotime($tgl)) == 8) {
        $bulan = 'Agustus';
    } else if (date('m', strtotime($tgl)) == 9) {
        $bulan = 'September';
    } else if (date('m', strtotime($tgl)) == 10) {
        $bulan = 'Oktober';
    } else if (date('m', strtotime($tgl)) == 11) {
        $bulan = 'November';
    } else {
        $bulan = 'Desember';
    }
    $tanggal = date('d', strtotime($tgl)) . ' ' . $bulan . ' ' . date('Y', strtotime($tgl));

    return $tanggal;
}

function hitung_jarak_hari($tanggal_awal, $tanggal_akhir)
{
    try {
        $awal = new DateTime($tanggal_awal);
        $akhir = new DateTime($tanggal_akhir);

        $selisih = $awal->getTimestamp() - $akhir->getTimestamp();

        return abs(floor($selisih / (60 * 60 * 24)));
    } catch (Exception $e) {
        return 'Format tanggal tidak valid: ' . $e->getMessage();
    }
}

function barcode($kode_barang)
{
    $redColor = [255, 0, 0];
    $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
    file_put_contents('barcode.png', $generator->getBarcode($kode_barang, $generator::TYPE_CODE_128, 3, 50, $redColor));

    echo '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($kode_barang, $generator::TYPE_CODE_128)) . '"><br>' . $kode_barang;
}

function _code_promo()
{
    $CI         = &get_instance();

    $lastNumber = $CI->db->query('SELECT * FROM m_promo ORDER BY id DESC LIMIT 1')->row();
    $number     = 1;
    if ($lastNumber) {
        $number         = count($CI->db->query('SELECT * FROM m_promo')->result()) + 1;
        $kode_member    = 'P' . sprintf("%04d", $number);
    } else {
        $number         = 0;
        $kode_member    = 'P' . "0001";
    }
    return $kode_member;
}

function _invoicePenyesuaianStok($cabang)
{
    $CI           = &get_instance();

    $now          = date('Y-m-d');

    $lastNumber   = $CI->db->query('SELECT * FROM penyesuaian_header WHERE kode_cabang = "' . $cabang . '" AND tgl_penyesuaian = "' . $now . '" ORDER BY id DESC LIMIT 1')->row();
    $number       = 1;
    if ($lastNumber) {
        $number   = $CI->db->query('SELECT * FROM penyesuaian_header WHERE kode_cabang = "' . $cabang . '" AND tgl_penyesuaian = "' . $now . '"')->num_rows() + 1;
        $invoice  = $CI->session->userdata('init_cabang') . 'TPS-' . date('Ymd') . sprintf("%05d", $number);
    } else {
        $number   = 0;
        $invoice  = $CI->session->userdata('init_cabang') . 'TPS-' . date('Ymd') . "00001";
    }
    return $invoice;
}

function hitungStokAdjIn($detail, $kode_gudang, $invoice)
{
    $CI   = &get_instance();

    $date = date('Y-m-d');
    $time = date('H:i:s');
    $user = $CI->session->userdata('kode_user');

    foreach ($detail as $d) {
        $cek = $CI->M_global->jumDataRow('barang_stok', ['kode_gudang' => $kode_gudang, 'kode_barang' => $d->kode_barang]);

        if ($cek < 1) {
            $isi_stok = [
                'kode_barang'   => $d->kode_barang,
                'kode_gudang'   => $kode_gudang,
                'so'            => 0,
                'penyesuaian'   => $d->qty_konversi,
                'akhir'         => $d->qty_konversi,
                'last_tgl_trx'  => $date,
                'last_jam_trx'  => $time,
                'last_no_trx'   => $invoice,
                'last_user'     => $user,
            ];

            $CI->M_global->insertData('barang_stok', $isi_stok);
        } else {
            $CI->db->query("UPDATE barang_stok SET 
            masuk = masuk + $d->qty_konversi, 
            akhir = akhir + $d->qty_konversi, 
            penyesuaian = $d->qty,
            last_tgl_trx = '$date', 
            last_jam_trx = '$time',
            last_no_trx = '$invoice',
            last_user = '$user' 
            WHERE kode_barang = '$d->kode_barang' AND kode_gudang = '$kode_gudang'");
        }
    }
}

function hitungStokAdjOut($detail, $kode_gudang, $invoice)
{
    $CI   = &get_instance();

    $kode_cabang = $CI->session->userdata('cabang');

    $date = date('Y-m-d');
    $time = date('H:i:s');
    $user = $CI->session->userdata('kode_user');

    foreach ($detail as $d) {
        $cek = $CI->M_global->jumDataRow('barang_stok', ['kode_gudang' => $kode_gudang, 'kode_cabang' => $kode_cabang, 'kode_barang' => $d->kode_barang]);

        if ($cek < 1) {
            $isi_stok = [
                'kode_barang'   => $d->kode_barang,
                'kode_gudang'   => $kode_gudang,
                'kode_cabang'   => $kode_cabang,
                'masuk'         => 0 - $d->qty_konversi,
                'akhir'         => 0 - $d->qty_konversi,
                'so'            => 0,
                'penyesuaian'   => 0 - $d->qty_konversi,
                'last_tgl_trx'  => $date,
                'last_jam_trx'  => $time,
                'last_no_trx'   => $invoice,
                'last_user'     => $user,
            ];

            $CI->M_global->insertData('barang_stock', $isi_stok);
        } else {
            $CI->db->query("UPDATE barang_stok SET 
            masuk = masuk - $d->qty_konversi, 
            akhir = akhir - $d->qty_konversi,
            penyesuaian = penyesuaian - $d->qty_konversi,
            last_tgl_trx = '$date', 
            last_jam_trx = '$time',
            last_no_trx = '$invoice',
            last_user = '$user' 
            WHERE kode_barang = '$d->kode_barang' AND kode_gudang = '$kode_gudang' AND kode_cabang = '$kode_cabang'");
        }
    }
}

function _lock_so()
{
    $CI       = &get_instance();

    $date     = date('Y-m-d');
    $clock    = date('H:i:s');

    $last_so  = $CI->db->query("SELECT * FROM jadwal_so ORDER BY id DESC LIMIT 1")->row();

    if ($last_so) {
        $cek_so   = $CI->db->query("SELECT * FROM jadwal_so WHERE tgl_sampai >= '$date' AND jam_sampai >= '$clock' AND id = '$last_so->id'")->row();

        if ($cek_so) {
            $lock = '<div class="alert alert-danger text-center" role="alert">
                    Saat ini sedang dalam proses Stock Opname, Transaksi sedang tidak bisa dilakukan!
                </div>';
        } else {
            $lock = '';
        }
    } else {
        $lock = '';
    }

    return $lock;
}

function _lock_button()
{
    $CI       = &get_instance();

    $date     = date('Y-m-d');
    $clock    = date('H:i:s');

    $last_so  = $CI->db->query("SELECT * FROM jadwal_so ORDER BY id DESC LIMIT 1")->row();

    if ($last_so) {
        $cek_so   = $CI->db->query("SELECT * FROM jadwal_so WHERE tgl_sampai >= '$date' AND jam_sampai >= '$clock' AND id = '$last_so->id'")->row();

        if ($cek_so) {
            $lock = 'disabled';
        } else {
            $lock = '';
        }
    } else {
        $lock = '';
    }

    return $lock;
}

function cek_so()
{
    $CI       = &get_instance();

    $date     = date('Y-m-d');
    $clock    = date('H:i:s');

    $last_so  = $CI->db->query("SELECT * FROM jadwal_so WHERE id = 1")->row();

    if ($last_so) {
        $cek_so   = $CI->db->query("SELECT * FROM jadwal_so WHERE (tgl_sampai >= '$date' AND jam_sampai >= '$clock') AND id = '$last_so->id'")->row();

        if ($cek_so) {
            $lock = 'disabled';
        } else {
            $CI->db->query("UPDATE jadwal_so SET status = 0 WHERE id = 1");

            $lock = '';
        }
    } else {
        $lock = '';
    }

    return $lock;
}

function _drop_db()
{
    $CI         = &get_instance();

    $cek        = $CI->db->query("SELECT table_name AS my_table FROM information_schema.tables
    WHERE table_schema = '" . $CI->db->database . "'");

    if ($cek->num_rows() > 0) {
        foreach ($cek->result() as $c) {
            // if ($c->my_table == 'user' || $c->my_table == 'member' || $c->my_table == 'cabang' || $c->my_table == 'kecamatan' || $c->my_table == 'kabupaten' || $c->my_table == 'backup_db' || $c->my_table == 'cabang_user' || $c->my_table == 'm_agama' || $c->my_table == 'm_gudang' || $c->my_table == 'm_pekerjaan' || $c->my_table == 'm_pendidikan' || $c->my_table == 'm_provinsi' || $c->my_table == 'm_role'  || $c->my_table == 'member_token' || $c->my_table == 'sub_menu' || $c->my_table == 'sub_menu2' || $c->my_table == 'user_token' || $c->my_table == 'web_setting' || $c->my_table == 'web_version' || $c->my_table == 'm_menu') {
            //     $query = TRUE;
            // } else {
            //     $query = $CI->db->query("DROP TABLE $c->my_table");
            // }
            $query = $CI->db->query("DROP TABLE $c->my_table");
        }
    } else {
        $query = TRUE;
    }

    return $query;
}

function _kodeKategoriTarif()
{
    $CI           = &get_instance();

    $lastNumber   = $CI->db->query('SELECT * FROM kategori_tarif ORDER BY id DESC LIMIT 1')->row();
    $number       = 1;
    if ($lastNumber) {
        $number   = $CI->db->query('SELECT * FROM kategori_tarif')->num_rows() + 1;
        $kode  = 'KATTR' . sprintf("%05d", $number);
    } else {
        $number   = 0;
        $kode  = 'KATTR' . "00001";
    }
    return $kode;
}

function mutasi_gudang_acc($kode_cabang, $dari, $menuju, $kode_barang, $qty, $tgl, $jam, $invoice, $kode_user)
{
    $CI           = &get_instance();

    // dari itu mengurangi, menuju itu menambah stok
    $stok_dari      = $CI->M_global->getData('barang_stok', ['kode_barang' => $kode_barang, 'kode_gudang' => $dari, 'kode_cabang' => $kode_cabang]);
    $stok_menuju    = $CI->M_global->getData('barang_stok', ['kode_barang' => $kode_barang, 'kode_gudang' => $menuju, 'kode_cabang' => $kode_cabang]);

    if ($stok_dari) {
        $dari       = $CI->db->query("UPDATE barang_stok SET keluar = keluar + $qty, akhir = akhir - $qty WHERE kode_cabang = '$kode_cabang' AND kode_gudang = '$dari' AND kode_barang = '$kode_barang'");
    } else {
        $data_dari  = [
            'kode_cabang'   => $kode_cabang,
            'kode_barang'   => $kode_barang,
            'kode_gudang'   => $dari,
            'masuk'         => 0,
            'keluar'        => $qty,
            'so'            => 0,
            'penyesuaian'   => 0,
            'akhir'         => 0 - $qty,
            'last_tgl_trx'  => $tgl,
            'last_jam_trx'  => $jam,
            'last_no_trx'   => $invoice,
            'last_user'     => $kode_user,
        ];

        $dari       = $CI->M_global->insertData('barang_stok', $data_dari);
    }

    if ($stok_menuju) {
        $menuju         = $CI->db->query("UPDATE barang_stok SET masuk = masuk + $qty, akhir = akhir + $qty WHERE kode_cabang = '$kode_cabang' AND kode_gudang = '$menuju' AND kode_barang = '$kode_barang'");
    } else {
        $data_menuju    = [
            'kode_cabang'   => $kode_cabang,
            'kode_barang'   => $kode_barang,
            'kode_gudang'   => $menuju,
            'masuk'         => $qty,
            'keluar'        => 0,
            'so'            => 0,
            'penyesuaian'   => 0,
            'akhir'         => $qty,
            'last_tgl_trx'  => $tgl,
            'last_jam_trx'  => $jam,
            'last_no_trx'   => $invoice,
            'last_user'     => $kode_user,
        ];

        $menuju       = $CI->M_global->insertData('barang_stok', $data_menuju);
    }

    return true;
}

function mutasi_cabang_acc($kode_cabang, $dari, $menuju, $kode_barang, $qty, $tgl, $jam, $invoice, $kode_user)
{
    $CI           = &get_instance();

    // dari itu mengurangi, menuju itu menambah stok
    $gutama         = $CI->M_global->getData('m_gudang', ['utama' => 1])->kode_gudang;

    $stok_dari      = $CI->M_global->getData('barang_stok', ['kode_barang' => $kode_barang, 'kode_gudang' => $gutama, 'kode_cabang' => $dari]);
    $stok_menuju    = $CI->M_global->getData('barang_stok', ['kode_barang' => $kode_barang, 'kode_gudang' => $gutama, 'kode_cabang' => $menuju]);

    if ($stok_dari) {
        $dari       = $CI->db->query("UPDATE barang_stok SET keluar = keluar + $qty, akhir = akhir - $qty WHERE kode_cabang = '$dari' AND kode_gudang = '$gutama' AND kode_barang = '$kode_barang'");
    } else {
        $data_dari  = [
            'kode_cabang'   => $dari,
            'kode_barang'   => $kode_barang,
            'kode_gudang'   => $gutama,
            'masuk'         => 0,
            'keluar'        => $qty,
            'so'            => 0,
            'penyesuaian'   => 0,
            'akhir'         => 0 - $qty,
            'last_tgl_trx'  => $tgl,
            'last_jam_trx'  => $jam,
            'last_no_trx'   => $invoice,
            'last_user'     => $kode_user,
        ];

        $dari       = $CI->M_global->insertData('barang_stok', $data_dari);
    }

    if ($stok_menuju) {
        $menuju         = $CI->db->query("UPDATE barang_stok SET masuk = masuk + $qty, akhir = akhir + $qty WHERE kode_cabang = '$menuju' AND kode_gudang = '$gutama' AND kode_barang = '$kode_barang'");
    } else {
        $data_menuju    = [
            'kode_cabang'   => $menuju,
            'kode_barang'   => $kode_barang,
            'kode_gudang'   => $gutama,
            'masuk'         => $qty,
            'keluar'        => 0,
            'so'            => 0,
            'penyesuaian'   => 0,
            'akhir'         => $qty,
            'last_tgl_trx'  => $tgl,
            'last_jam_trx'  => $jam,
            'last_no_trx'   => $invoice,
            'last_user'     => $kode_user,
        ];

        $menuju       = $CI->M_global->insertData('barang_stok', $data_menuju);
    }

    return true;
}

function mutasi_gudang_rjt($kode_cabang, $dari, $menuju, $kode_barang, $qty, $tgl, $jam, $invoice, $kode_user)
{
    $CI           = &get_instance();

    // dari itu mengurangi, menuju itu menambah stok
    $stok_dari      = $CI->M_global->getData('barang_stok', ['kode_barang' => $kode_barang, 'kode_gudang' => $dari, 'kode_cabang' => $kode_cabang]);
    $stok_menuju    = $CI->M_global->getData('barang_stok', ['kode_barang' => $kode_barang, 'kode_gudang' => $menuju, 'kode_cabang' => $kode_cabang]);

    if ($stok_dari) {
        $dari       = $CI->db->query("UPDATE barang_stok SET keluar = keluar - $qty, akhir = akhir + $qty WHERE kode_cabang = '$kode_cabang' AND kode_gudang = '$dari' AND kode_barang = '$kode_barang'");
    } else {
        $data_dari  = [
            'kode_cabang'   => $kode_cabang,
            'kode_barang'   => $kode_barang,
            'kode_gudang'   => $dari,
            'masuk'         => 0,
            'keluar'        => 0 - $qty,
            'so'            => 0,
            'penyesuaian'   => 0,
            'akhir'         => $qty,
            'last_tgl_trx'  => $tgl,
            'last_jam_trx'  => $jam,
            'last_no_trx'   => $invoice,
            'last_user'     => $kode_user,
        ];

        $dari       = $CI->M_global->insertData('barang_stok', $data_dari);
    }

    if ($stok_menuju) {
        $menuju         = $CI->db->query("UPDATE barang_stok SET masuk = masuk - $qty, akhir = akhir - $qty WHERE kode_cabang = '$kode_cabang' AND kode_gudang = '$menuju' AND kode_barang = '$kode_barang'");
    } else {
        $data_menuju    = [
            'kode_cabang'   => $kode_cabang,
            'kode_barang'   => $kode_barang,
            'kode_gudang'   => $menuju,
            'masuk'         => 0 - $qty,
            'keluar'        => 0,
            'so'            => 0,
            'penyesuaian'   => 0,
            'akhir'         => 0 - $qty,
            'last_tgl_trx'  => $tgl,
            'last_jam_trx'  => $jam,
            'last_no_trx'   => $invoice,
            'last_user'     => $kode_user,
        ];

        $menuju       = $CI->M_global->insertData('barang_stok', $data_menuju);
    }

    return true;
}

function mutasi_cabang_rjt($kode_cabang, $dari, $menuju, $kode_barang, $qty, $tgl, $jam, $invoice, $kode_user)
{
    $CI           = &get_instance();

    // dari itu mengurangi, menuju itu menambah stok
    $gutama         = $CI->M_global->getData('m_gudang', ['utama' => 1])->kode_gudang;

    $stok_dari      = $CI->M_global->getData('barang_stok', ['kode_barang' => $kode_barang, 'kode_gudang' => $gutama, 'kode_cabang' => $dari]);
    $stok_menuju    = $CI->M_global->getData('barang_stok', ['kode_barang' => $kode_barang, 'kode_gudang' => $gutama, 'kode_cabang' => $menuju]);

    if ($stok_dari) {
        $dari       = $CI->db->query("UPDATE barang_stok SET keluar = keluar - $qty, akhir = akhir + $qty WHERE kode_cabang = '$dari' AND kode_gudang = '$gutama' AND kode_barang = '$kode_barang'");
    } else {
        $data_dari  = [
            'kode_cabang'   => $dari,
            'kode_barang'   => $kode_barang,
            'kode_gudang'   => $gutama,
            'masuk'         => 0,
            'keluar'        => 0 - $qty,
            'so'            => 0,
            'penyesuaian'   => 0,
            'akhir'         => $qty,
            'last_tgl_trx'  => $tgl,
            'last_jam_trx'  => $jam,
            'last_no_trx'   => $invoice,
            'last_user'     => $kode_user,
        ];

        $dari       = $CI->M_global->insertData('barang_stok', $data_dari);
    }

    if ($stok_menuju) {
        $menuju         = $CI->db->query("UPDATE barang_stok SET masuk = masuk - $qty, akhir = akhir - $qty WHERE kode_cabang = '$menuju' AND kode_gudang = '$gutama' AND kode_barang = '$kode_barang'");
    } else {
        $data_menuju    = [
            'kode_cabang'   => $menuju,
            'kode_barang'   => $kode_barang,
            'kode_gudang'   => $gutama,
            'masuk'         => 0 - $qty,
            'keluar'        => 0,
            'so'            => 0,
            'penyesuaian'   => 0,
            'akhir'         => 0 - $qty,
            'last_tgl_trx'  => $tgl,
            'last_jam_trx'  => $jam,
            'last_no_trx'   => $invoice,
            'last_user'     => $kode_user,
        ];

        $menuju       = $CI->M_global->insertData('barang_stok', $data_menuju);
    }

    return true;
}

function doc_px($no_trx, $judul_surat, $no_surat, $data)
{
    $CI           = &get_instance();

    $data_doc = [
        'no_trx'        => $no_trx,
        'judul_surat'   => $judul_surat,
        'no_surat'      => $no_surat,
        'tgl'           => date('Y-m-d'),
        'jam'           => date('H:i:s'),
        'data'          => $data,
    ];

    $cek = $CI->M_global->getData('doc_px', ['no_trx' => $no_trx, 'judul_surat' => $judul_surat]);

    if ($cek) {
        $CI->M_global->updateData('doc_px', $data_doc, ['no_trx' => $no_trx, 'judul_surat' => $judul_surat]);
    } else {
        $CI->M_global->insertData('doc_px', $data_doc);
    }

    return true;
}

function _codeAnjungan($cabang)
{
    $CI         = &get_instance();

    $inisial    = 'AP';
    $lastNumber = $CI->db->query('SELECT * FROM m_anjungan WHERE no_anjungan LIKE "' . $inisial . '%" AND kode_cabang = "' . $cabang . '" AND tgl = "' . date('Y-m-d') . '" ORDER BY id DESC LIMIT 1')->row();
    $number     = 1;
    if ($lastNumber) {
        $number       = count($CI->db->query('SELECT * FROM m_anjungan WHERE no_anjungan LIKE "' . $inisial . '%" AND kode_cabang = "' . $cabang . '" AND tgl = "' . date('Y-m-d') . '"')->result()) + 1;
        $kode_user    = $inisial . sprintf("%03d", $number);
    } else {
        $number       = 0;
        $kode_user    = $inisial . "001";
    }
    return $kode_user;
}

function _codeReservasi($kode_poli, $kode_cabang, $tgl)
{
    $CI = &get_instance();

    // Get poli data
    $poli = $CI->db->get_where('m_poli', ['kode_poli' => $kode_poli])->row();

    // Get total registrations for this date/poli/branch
    $total_pendaftaran = $CI->db->where([
        'kode_cabang' => $kode_cabang,
        'tgl' => $tgl,
        'kode_poli' => $kode_poli
    ])->count_all_results('reservasi');

    if ($total_pendaftaran > 0) {
        $number = $total_pendaftaran + 1;
    } else {
        // Get total active reservations
        $total_reservasi = $CI->db->where([
            'kode_cabang' => $kode_cabang,
            'tgl' => $tgl,
            'kode_poli' => $kode_poli
        ])->count_all_results('reservasi');

        $number = $total_reservasi > 0 ? $total_reservasi + 1 : 1;
    }

    // Generate reservation code with format: B + poli_initial + 4 digit number
    return 'B' . $poli->inisial_room . sprintf("%04d", $number);
}
