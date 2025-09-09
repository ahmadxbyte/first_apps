<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Report extends CI_Controller
{
    // variable open public untuk controller Home
    public $data;

    public function __construct()
    {
        parent::__construct();
        // load model M_auth
        $this->load->model("M_auth");

        if (!empty($this->session->userdata("email"))) { // jika session email masih ada
            // ambil isi data berdasarkan email session dari table user, kemudian tampung ke variable $user
            $user = $this->M_global->getData("user", ["email" => $this->session->userdata("email")]);

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
        } else { // selain itu
            // kirimkan kembali ke Auth
            #272a3firect('Auth');
        }
    }

    // satuan
    public function satuan($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->db->query("SELECT s.* FROM m_satuan s ORDER BY s.kode_satuan ASC")->result();

        $body           .= '<br>';

        $body           .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';

        $body           .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Kode</th>
            <th style="width: 70%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body       .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_satuan . '</td>
                <td style="border: 1px solid black;">' . $s->keterangan . '</td>
            </tr>';
            $no++;
        }

        $body           .= '</table>';

        $judul          = 'Master Satuan';
        $filename       = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf_small($judul, $body, $param, $position, $filename, $web_setting);
    }

    // kategori
    public function kategori($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        $body           .= '<br>';

        // sintak
        $sintak         = $this->db->query("SELECT s.* FROM m_kategori s ORDER BY s.kode_kategori ASC")->result();

        $body           .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';

        $body           .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Kode</th>
            <th style="width: 70%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_kategori . '</td>
                <td style="border: 1px solid black;">' . $s->keterangan . '</td>
            </tr>';
            $no++;
        }

        $body           .= '</table>';

        $judul          = 'Master Kategori';
        $filename       = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf_small($judul, $body, $param, $position, $filename, $web_setting);
    }

    // jenis
    public function jenis($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->db->query("SELECT s.* FROM m_jenis s ORDER BY s.kode_jenis ASC")->result();

        $body           .= '<br>';

        $body           .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body           .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Kode</th>
            <th style="width: 70%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_jenis . '</td>
                <td style="border: 1px solid black;">' . $s->keterangan . '</td>
            </tr>';
            $no++;
        }

        $body           .= '</table>';

        $judul          = 'Master Jenis';
        $filename       = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf_small($judul, $body, $param, $position, $filename, $web_setting);
    }

    // supplier
    public function supplier($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->db->query("SELECT s.* FROM m_supplier s ORDER BY s.kode_supplier ASC")->result();

        $body           .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body           .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 12%; border: 1px solid black; background-color: #272a3f; color: white;">Kode</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
            <th style="width: 13%; border: 1px solid black; background-color: #272a3f; color: white;">Nohp</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Email</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Fax</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Alamat</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body       .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_supplier . '</td>
                <td style="border: 1px solid black;">' . $s->nama . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $s->nohp . '</td>
                <td style="border: 1px solid black;">' . $s->email . '</td>
                <td style="border: 1px solid black;">' . $s->fax . '</td>
                <td style="border: 1px solid black;">' . $s->alamat . '</td>
            </tr>';
            $no++;
        }

        $body           .= '</table>';

        $judul          = 'Master Supplier';
        $filename       = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // gudang
    public function gudang($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->db->query("SELECT s.* FROM m_gudang s ORDER BY s.kode_gudang ASC")->result();

        $body           .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body           .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Kode</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Bagian</th>
            <th style="width: 50%; border: 1px solid black; background-color: #272a3f; color: white;">Keterangan</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body       .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_gudang . '</td>
                <td style="border: 1px solid black;">' . $s->nama . '</td>
                <td style="border: 1px solid black;">' . $s->bagian . '</td>
                <td style="border: 1px solid black;">' . $s->keterangan . '</td>
            </tr>';
            $no++;
        }

        $body           .= '</table>';

        $judul          = 'Master Gudang';
        $filename       = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // bank
    public function bank($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->db->query("SELECT s.* FROM m_bank s ORDER BY s.kode_bank ASC")->result();
        $body           .= '<br>';

        $body           .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body           .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Kode</th>
            <th style="width: 70%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body       .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_bank . '</td>
                <td style="border: 1px solid black;">' . $s->keterangan . '</td>
            </tr>';
            $no++;
        }

        $body           .= '</table>';

        $judul          = 'Master Bank';
        $filename       = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf_small($judul, $body, $param, $position, $filename, $web_setting);
    }

    // pekerjaan
    public function pekerjaan($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->db->query("SELECT s.* FROM m_pekerjaan s ORDER BY s.kode_pekerjaan ASC")->result();
        $body           .= '<br>';

        $body           .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body           .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Kode</th>
            <th style="width: 70%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body       .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_pekerjaan . '</td>
                <td style="border: 1px solid black;">' . $s->keterangan . '</td>
            </tr>';
            $no++;
        }

        $body           .= '</table>';

        $judul          = 'Master Pekerjaan';
        $filename       = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf_small($judul, $body, $param, $position, $filename, $web_setting);
    }

    // agama
    public function agama($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->db->query("SELECT s.* FROM m_agama s ORDER BY s.kode_agama ASC")->result();
        $body           .= '<br>';

        $body           .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body           .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Kode</th>
            <th style="width: 70%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body       .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_agama . '</td>
                <td style="border: 1px solid black;">' . $s->keterangan . '</td>
            </tr>';
            $no++;
        }

        $body           .= '</table>';

        $judul          = 'Master Agama';
        $filename       = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf_small($judul, $body, $param, $position, $filename, $web_setting);
    }

    // pendidikan
    public function pendidikan($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->db->query("SELECT s.* FROM m_pendidikan s ORDER BY s.kode_pendidikan ASC")->result();
        $body           .= '<br>';

        $body           .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body           .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Kode</th>
            <th style="width: 70%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body       .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_pendidikan . '</td>
                <td style="border: 1px solid black;">' . $s->keterangan . '</td>
            </tr>';
            $no++;
        }

        $body           .= '</table>';

        $judul          = 'Master Pendidikan';
        $filename       = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf_small($judul, $body, $param, $position, $filename, $web_setting);
    }

    // poli
    public function poli($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->db->query("SELECT s.* FROM m_poli s ORDER BY s.kode_poli ASC")->result();
        $body           .= '<br>';

        $body           .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body           .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Kode</th>
            <th style="width: 70%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body       .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_poli . '</td>
                <td style="border: 1px solid black;">' . $s->keterangan . '</td>
            </tr>';
            $no++;
        }

        $body           .= '</table>';

        $judul          = 'Master Poli';
        $filename       = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf_small($judul, $body, $param, $position, $filename, $web_setting);
    }

    // kas_bank
    public function kas_bank($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->db->query("SELECT s.* FROM kas_bank s ORDER BY s.kode_kas_bank ASC")->result();
        $body           .= '<br>';

        $body           .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body           .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Kode</th>
            <th style="width: 30%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Tipe</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Akun</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body       .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_kas_bank . '</td>
                <td style="border: 1px solid black;">' . $s->nama . '</td>
                <td style="border: 1px solid black;">' . (($s->tipe == 1) ? 'Cash' : 'Bank') . '</td>
                <td style="border: 1px solid black;">' . (($s->akun == 1) ? 'Kas Besar' : 'Kas Kecil') . '</td>
            </tr>';
            $no++;
        }

        $body           .= '</table>';

        $judul          = 'Master Kas Bank';
        $filename       = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf_small($judul, $body, $param, $position, $filename, $web_setting);
    }

    // pajak
    public function pajak($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->db->query("SELECT s.* FROM m_pajak s ORDER BY s.kode_pajak ASC")->result();
        $body           .= '<br>';

        $body           .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body           .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Kode</th>
            <th style="width: 50%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Persentase</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body       .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_pajak . '</td>
                <td style="border: 1px solid black;">' . $s->nama . '</td>
                <td style="border: 1px solid black;">' . $s->persentase . '%' . '</td>
            </tr>';
            $no++;
        }

        $body           .= '</table>';

        $judul          = 'Master Pajak';
        $filename        = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf_small($judul, $body, $param, $position, $filename, $web_setting);
    }

    // akun
    public function akun($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->db->query("SELECT s.* FROM m_akun s ORDER BY s.kode_akun ASC")->result();
        $body           .= '<br>';

        $body           .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body           .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Kode</th>
            <th style="width: 50%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Klasifikasi</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body       .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_akun . '</td>
                <td style="border: 1px solid black;">' . $s->nama_akun . '</td>
                <td style="border: 1px solid black;">' . $this->M_global->getData('klasifikasi_akun', ['kode_klasifikasi' => $s->kode_klasifikasi])->klasifikasi . '</td>
            </tr>';
            $no++;
        }

        $body           .= '</table>';

        $judul          = 'Master Akun';
        $filename        = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf_small($judul, $body, $param, $position, $filename, $web_setting);
    }

    // tipe
    public function tipe($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->db->query("SELECT s.* FROM tipe_bank s ORDER BY s.kode_tipe ASC")->result();

        $body           .= '<br>';

        $body           .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';

        $body           .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Kode</th>
            <th style="width: 70%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body       .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_tipe . '</td>
                <td style="border: 1px solid black;">' . $s->keterangan . '</td>
            </tr>';
            $no++;
        }

        $body           .= '</table>';

        $judul          = 'Master Tipe Bank';
        $filename       = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf_small($judul, $body, $param, $position, $filename, $web_setting);
    }

    // ruang
    public function ruang($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->db->query("SELECT s.* FROM m_ruang s ORDER BY s.kode_ruang ASC")->result();

        $body           .= '<br>';

        $body           .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';

        $body           .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Kode</th>
            <th style="width: 40%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
            <th style="width: 30%; border: 1px solid black; background-color: #272a3f; color: white;">Jenis</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body       .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_ruang . '</td>
                <td style="border: 1px solid black;">' . $s->keterangan . '</td>
                <td style="border: 1px solid black;">' . (($s->jenis == 1) ? 'Rawat Jalan' : 'Rawat Inap') . '</td>
            </tr>';
            $no++;
        }

        $body           .= '</table>';

        $judul          = 'Master Ruang';
        $filename       = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf_small($judul, $body, $param, $position, $filename, $web_setting);
    }

    // bed
    public function bed($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->db->query("SELECT s.*, (SELECT keterangan FROM m_ruang WHERE kode_ruang = s.kode_ruang) AS ruang FROM bed s JOIN bed_cabang a USING(kode_bed) WHERE a.kode_cabang = '" . $this->session->userdata('cabang') . "' ORDER BY s.kode_ruang ASC")->result();

        $body           .= '<br>';

        $body           .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';

        $body           .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Kode</th>
            <th style="width: 40%; border: 1px solid black; background-color: #272a3f; color: white;">Bed</th>
            <th style="width: 30%; border: 1px solid black; background-color: #272a3f; color: white;">Ruang</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body       .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_bed . '</td>
                <td style="border: 1px solid black;">' . $s->nama_bed . '</td>
                <td style="border: 1px solid black;">' . $s->ruang . '</td>
            </tr>';
            $no++;
        }

        $body           .= '</table>';

        $judul          = 'Master Bed';
        $filename       = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf_small($judul, $body, $param, $position, $filename, $web_setting);
    }

    // prefix
    public function prefix($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->db->query("SELECT s.* FROM m_prefix s ORDER BY s.kode_prefix ASC")->result();

        $body           .= '<br>';

        $body           .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';

        $body           .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 65%; border: 1px solid black; background-color: #272a3f; color: white;">Kode</th>
            <th style="width: 30%; border: 1px solid black; background-color: #272a3f; color: white;">Prefix</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body       .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_prefix . '</td>
                <td style="border: 1px solid black;">' . $s->nama . '</td>
            </tr>';
            $no++;
        }

        $body           .= '</table>';

        $judul          = 'Master Prefix';
        $filename       = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf_small($judul, $body, $param, $position, $filename, $web_setting);
    }

    // provinsi
    public function provinsi($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->db->query("SELECT m.* FROM m_provinsi m")->result();

        $body .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">ID</th>
            <th style="width: 80%; border: 1px solid black; background-color: #272a3f; color: white;">Provinsi</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $s->kode_provinsi . '</td>
                <td style="border: 1px solid black;">' . $s->provinsi . '</td>
            </tr>';
            $no++;
        }

        $body .= '</table>';

        $judul = 'Report Wilayah Provinsi';
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // kabupaten
    public function kabupaten($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->db->query("SELECT m.* FROM kabupaten m")->result();

        $body .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">ID</th>
            <th style="width: 40%; border: 1px solid black; background-color: #272a3f; color: white;">Kabupaten</th>
            <th style="width: 40%; border: 1px solid black; background-color: #272a3f; color: white;">Provinsi</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $s->kode_kabupaten . '</td>
                <td style="border: 1px solid black;">' . $s->kabupaten . '</td>
                <td style="border: 1px solid black;">' . $s->kode_provinsi . ' - ' . ($this->M_global->getData('m_provinsi', ['kode_provinsi' => $s->kode_provinsi])->provinsi) . '</td>
            </tr>';
            $no++;
        }

        $body .= '</table>';

        $judul = 'Report Wilayah Kabupaten';
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // kecamatan
    public function kecamatan($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->db->query("SELECT m.* FROM kecamatan m")->result();

        $body .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body .= '<tr>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">ID</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Kecamatan</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Kabupaten</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Provinsi</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $kab = $this->M_global->getData('kabupaten', ['kode_kabupaten' => $s->kode_kabupaten]);
            $body .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $s->kode_kecamatan . '</td>
                <td style="border: 1px solid black;">' . $s->kecamatan . '</td>
                <td style="border: 1px solid black;">' . $s->kode_kabupaten . ' - ' . $kab->kabupaten . '</td>
                <td style="border: 1px solid black;">' . $kab->kode_provinsi . ' - ' . ($this->M_global->getData('m_provinsi', ['kode_provinsi' => $kab->kode_provinsi])->provinsi) . '</td>
            </tr>';
            $no++;
        }

        $body .= '</table>';

        $judul = 'Report Wilayah Kecamatan';
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // barang
    public function barang($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'L'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->db->query("SELECT b.*, (SELECT keterangan FROM m_satuan WHERE kode_satuan = b.kode_satuan) AS satuan1, (SELECT keterangan FROM m_satuan WHERE kode_satuan = b.kode_satuan2) AS satuan2, (SELECT keterangan FROM m_satuan WHERE kode_satuan = b.kode_satuan3) AS satuan3, k.keterangan AS kategori FROM barang b JOIN m_kategori k USING(kode_kategori) ORDER BY b.kode_barang ASC")->result();

        $body .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body .= '<tr>
            <th rowspan="2" style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Kode</th>
            <th rowspan="2" style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
            <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Satuan</th>
            <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Kategori</th>
            <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Jenis</th>
            <th colspan="4" style="width: 40%; border: 1px solid black; background-color: #272a3f; color: white;">Harga</th>
        </tr>
        <tr>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">HNA</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">HPP</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Jual</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Persediaan</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $barang_jenis = $this->M_global->getDataResult('barang_jenis', ['kode_barang' => $s->kode_barang]);

            $satuan1 = $this->M_global->getData('m_satuan', ['kode_satuan' => $s->kode_satuan])->keterangan;

            if ($s->kode_satuan2 == '' || $s->kode_satuan2 == null) {
                $satuan2 = '';
            } else {
                $satuan2 = '<br>' . $this->M_global->getData('m_satuan', ['kode_satuan' => $s->kode_satuan2])->keterangan . ' ~ ' . $s->qty_satuan2;
            }

            if ($s->kode_satuan3 == '' || $s->kode_satuan3 == null) {
                $satuan3 = '';
            } else {
                $satuan3 = '<br>' . $this->M_global->getData('m_satuan', ['kode_satuan' => $s->kode_satuan3])->keterangan . ' ~ ' . $s->qty_satuan3;
            }

            $body .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_barang . '</td>
                <td style="border: 1px solid black;">' . $s->nama . '</td>
                <td style="border: 1px solid black;">' . $satuan1 . $satuan2 . $satuan3 . '</td>
                <td style="border: 1px solid black;">' . $s->kategori . '</td>
                <td style="border: 1px solid black;">';

            if (!empty($barang_jenis)) {
                foreach ($barang_jenis  as $bj) {
                    $data_jenis = $this->M_global->getData('m_jenis', ['kode_jenis' => $bj->kode_jenis])->keterangan;

                    $body .= $data_jenis . '<br>';
                }
            } else {
                $body .= '';
            }

            $body .= '</td>
                <td style="border: 1px solid black; text-align: right;">' . (($param > 1) ? ceil($s->hna) : number_format($s->hna)) . '</td>
                <td style="border: 1px solid black; text-align: right;">' . (($param > 1) ? ceil($s->hpp) : number_format($s->hpp)) . '</td>
                <td style="border: 1px solid black; text-align: right;">' . (($param > 1) ? ceil($s->harga_jual) : number_format($s->harga_jual)) . '</td>
                <td style="border: 1px solid black; text-align: right;">' . (($param > 1) ? ceil($s->nilai_persediaan) : number_format($s->nilai_persediaan)) . '</td>
            </tr>';
            $no++;
        }

        $body .= '</table>';

        $judul = 'Master Barang';
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // logistik
    public function logistik($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->db->query("SELECT b.*, s.keterangan AS satuan, k.keterangan AS kategori FROM logistik b JOIN m_satuan s USING(kode_satuan) JOIN m_kategori k USING(kode_kategori) ORDER BY b.kode_logistik ASC")->result();

        $body .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body .= '<tr>
            <th rowspan="2" style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Kode</th>
            <th rowspan="2" style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
            <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Satuan</th>
            <th rowspan="2" style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Kategori</th>
            <th colspan="4" style="width: 40%; border: 1px solid black; background-color: #272a3f; color: white;">Harga</th>
        </tr>
        <tr>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">HNA</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">HPP</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Jual</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Persediaan</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_logistik . '</td>
                <td style="border: 1px solid black;">' . $s->nama . '</td>
                <td style="border: 1px solid black;">' . $s->satuan . '</td>
                <td style="border: 1px solid black;">' . $s->kategori . '</td>
                <td style="border: 1px solid black; text-align: right;">' . number_format($s->hna) . '</td>
                <td style="border: 1px solid black; text-align: right;">' . number_format($s->hpp) . '</td>
                <td style="border: 1px solid black; text-align: right;">' . number_format($s->harga_jual) . '</td>
                <td style="border: 1px solid black; text-align: right;">' . number_format($s->nilai_persediaan) . '</td>
            </tr>';
            $no++;
        }

        $body .= '</table>';

        $judul = 'Master Logistik';
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // pengguna
    public function pengguna($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->M_global->getResult('user');

        $body .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Kode</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Tingkatan</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">No Hp</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Email</th>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">Status</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {

            $body .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_user . '</td>
                <td style="border: 1px solid black;">' . $s->nama . '</td>
                <td style="border: 1px solid black;">' . $this->M_global->getData('m_role', ['kode_role' => $s->kode_role])->keterangan . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $s->nohp . '</td>
                <td style="border: 1px solid black;">' . $s->email . '</td>
                <td style="border: 1px solid black; font-weight: bold; text-align: center; background-color: ' . (($s->actived == 1) ? 'green' : 'grey') . '; color: ' . (($s->actived == 1) ? 'white' : 'black') . '">' . (($s->actived == 1) ? 'Aktif' : 'Non-aktif') . '</td>
            </tr>';
            $no++;
        }

        $body .= '</table>';

        $judul = 'Master Pengguna';
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // dokter
    public function dokter($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->M_global->getResult('dokter');

        $body .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">NIK</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">SIP</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">NPWP</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Nohp/Email</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Alamat</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Masa Kerja</th>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">Status</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {

            $prov   = $this->M_global->getData('m_provinsi', ['kode_provinsi' => $s->provinsi])->provinsi;
            $kab    = $this->M_global->getData('kabupaten', ['kode_kabupaten' => $s->kabupaten])->kabupaten;
            $kec    = $this->M_global->getData('kecamatan', ['kode_kecamatan' => $s->kecamatan])->kecamatan;

            $alamat = 'Prov. ' . $prov . ',<br>Kab. ' . $kab . ',<br>Kec. ' . $kec . ',<br>Ds. ' . $s->desa . ',<br>(POS: ' . $s->kodepos . ')';

            $body .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->nik . '</td>
                <td style="border: 1px solid black;">' . $s->sip . '</td>
                <td style="border: 1px solid black;">' . $s->npwp . '</td>
                <td style="border: 1px solid black;">' . $s->nama . '</td>
                <td style="border: 1px solid black;">Nohp: <br>' . $s->nohp . '<br><br>Email: <br>' . $s->email . '</td>
                <td style="border: 1px solid black;">' . $alamat . '</td>
                <td style="border: 1px solid black;">Mulai: <br>' . date('d/m/Y', strtotime($s->tgl_mulai)) . '<br><br>Berhenti: <br>' . date('d/m/Y', strtotime($s->tgl_berhenti)) . '</td>
                <td style="border: 1px solid black; font-weight: bold; text-align: center; background-color: ' . (($s->status == 1) ? 'green' : 'grey') . '; color: ' . (($s->status == 1) ? 'white' : 'black') . '">' . (($s->status == 1) ? 'Aktif' : 'Non-aktif') . '</td>
            </tr>';
            $no++;
        }

        $body .= '</table>';

        $judul = 'Master Dokter';
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // perawat
    public function perawat($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->M_global->getResult('perawat');

        $body .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">NIK</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">SIP</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">NPWP</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Nohp/Email</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Alamat</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Masa Kerja</th>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">Status</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {

            $prov   = $this->M_global->getData('m_provinsi', ['kode_provinsi' => $s->provinsi])->provinsi;
            $kab    = $this->M_global->getData('kabupaten', ['kode_kabupaten' => $s->kabupaten])->kabupaten;
            $kec    = $this->M_global->getData('kecamatan', ['kode_kecamatan' => $s->kecamatan])->kecamatan;

            $alamat = 'Prov. ' . $prov . ',<br>Kab. ' . $kab . ',<br>Kec. ' . $kec . ',<br>Ds. ' . $s->desa . ',<br>(POS: ' . $s->kodepos . ')';

            $body .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->nik . '</td>
                <td style="border: 1px solid black;">' . $s->sip . '</td>
                <td style="border: 1px solid black;">' . $s->npwp . '</td>
                <td style="border: 1px solid black;">' . $s->nama . '</td>
                <td style="border: 1px solid black;">Nohp: <br>' . $s->nohp . '<br><br>Email: <br>' . $s->email . '</td>
                <td style="border: 1px solid black;">' . $alamat . '</td>
                <td style="border: 1px solid black;">Mulai: <br>' . date('d/m/Y', strtotime($s->tgl_mulai)) . '<br><br>Berhenti: <br>' . date('d/m/Y', strtotime($s->tgl_berhenti)) . '</td>
                <td style="border: 1px solid black; font-weight: bold; text-align: center; background-color: ' . (($s->status == 1) ? 'green' : 'grey') . '; color: ' . (($s->status == 1) ? 'white' : 'black') . '">' . (($s->status == 1) ? 'Aktif' : 'Non-aktif') . '</td>
            </tr>';
            $no++;
        }

        $body .= '</table>';

        $judul = 'Master Perawat';
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // tin_single
    public function tin_single($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $kode_cabang    = $this->session->userdata('cabang');

        // sintak
        $sintak         = $this->db->query("SELECT m.*, tj.* FROM m_tarif m JOIN tarif_jasa tj USING(kode_tarif) WHERE tj.kode_cabang = '$kode_cabang'")->result();

        $body           .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body           .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Kode</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Jasa RS</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Jasa Dokter</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Jasa Pelayanan</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Jasa Poli</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            if ($param == 1) {
                $jasa_rs        = number_format($s->jasa_rs);
                $jasa_dokter    = number_format($s->jasa_dokter);
                $jasa_pelayanan = number_format($s->jasa_pelayanan);
                $jasa_poli      = number_format($s->jasa_poli);
            } else {
                $jasa_rs        = ceil($s->jasa_rs);
                $jasa_dokter    = ceil($s->jasa_dokter);
                $jasa_pelayanan = ceil($s->jasa_pelayanan);
                $jasa_poli      = ceil($s->jasa_poli);
            }

            $body       .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_tarif . '</td>
                <td style="border: 1px solid black;">' . $s->nama . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $jasa_rs . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $jasa_dokter . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $jasa_pelayanan . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $jasa_poli . '</td>
            </tr>';
            $no++;
        }

        $body           .= '</table>';

        $judul          = 'Master Tarif Single';
        $filename       = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // tin_paket
    public function tin_paket($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $kode_cabang    = $this->session->userdata('cabang');

        // sintak
        $sintak         = $this->db->query("SELECT m.*, tj.* FROM m_tarif m JOIN tarif_paket tj USING(kode_tarif) WHERE tj.kode_cabang = '$kode_cabang'")->result();

        $body           .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body           .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Kode</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Jasa RS</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Jasa Dokter</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Jasa Pelayanan</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Jasa Poli</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $kunjungan                  = count($this->M_global->getDataResult('tarif_paket', ['kode_tarif' => $s->kode_tarif, 'kode_cabang' => $kode_cabang]));

            $jasa_rs                    = [];
            $jasa_dokter                = [];
            $jasa_pelayanan             = [];
            $jasa_poli                  = [];
            $kunj                       = [];

            for ($x = 1; $x <= $kunjungan; $x++) {
                $jasa                   = $this->M_global->getData('tarif_paket', ['kode_tarif' => $s->kode_tarif, 'kunjungan' => $x]);

                $kunj[$x]               = $x;

                if ($param == 1) {
                    $jasa_rs[$x]        = number_format($jasa->jasa_rs);
                    $jasa_dokter[$x]    = number_format($jasa->jasa_dokter);
                    $jasa_pelayanan[$x] = number_format($jasa->jasa_pelayanan);
                    $jasa_poli[$x]      = number_format($jasa->jasa_poli);
                } else {
                    $jasa_rs[$x]        = ceil($jasa->jasa_rs);
                    $jasa_dokter[$x]    = ceil($jasa->jasa_dokter);
                    $jasa_pelayanan[$x] = ceil($jasa->jasa_pelayanan);
                    $jasa_poli[$x]      = ceil($jasa->jasa_poli);
                }
            }

            $jasa_rs_str                = implode('<br>', array_map(fn($k, $v) => "<div style='float: left;'>Paket $k: </div><div style='float: right;'>Rp. $v</div>", array_keys($jasa_rs), $jasa_rs));
            $jasa_dokter_str            = implode('<br>', array_map(fn($k, $v) => "<div style='float: left;'>Paket $k: </div><div style='float: right;'>Rp. $v</div>", array_keys($jasa_dokter), $jasa_dokter));
            $jasa_pelayanan_str         = implode('<br>', array_map(fn($k, $v) => "<div style='float: left;'>Paket $k: </div><div style='float: right;'>Rp. $v</div>", array_keys($jasa_pelayanan), $jasa_pelayanan));
            $jasa_poli_str              = implode('<br>', array_map(fn($k, $v) => "<div style='float: left;'>Paket $k: </div><div style='float: right;'>Rp. $v</div>", array_keys($jasa_poli), $jasa_poli));

            $body       .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_tarif . '</td>
                <td style="border: 1px solid black;">' . $s->nama . '</td>
                <td style="border: 1px solid black;">' . $jasa_rs_str . '</td>
                <td style="border: 1px solid black;">' . $jasa_dokter_str . '</td>
                <td style="border: 1px solid black;">' . $jasa_pelayanan_str . '</td>
                <td style="border: 1px solid black;">' . $jasa_poli_str . '</td>
            </tr>';
            $no++;
        }

        $body           .= '</table>';

        $judul          = 'Master Tarif Paket';
        $filename       = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // member
    public function member($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'L'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // sintak
        $sintak         = $this->db->query("SELECT m.*, pek.keterangan AS pekerjaan, pen.keterangan AS pendidikan, agm.keterangan AS agama, pro.provinsi AS provinsi, kab.kabupaten AS kabupaten, kec.kecamatan AS kecamatan FROM member m JOIN m_pekerjaan pek ON pek.kode_pekerjaan = m.pekerjaan JOIN m_pendidikan pen ON pen.kode_pendidikan = m.pendidikan JOIN m_agama agm ON agm.kode_agama = m.agama JOIN m_provinsi pro ON pro.kode_provinsi = m.provinsi JOIN kabupaten kab ON kab.kode_kabupaten = m.kabupaten JOIN kecamatan kec ON kec.kode_kecamatan = m.kecamatan JOIN m_role rol ON rol.kode_role = m.kode_role ORDER BY m.kode_member ASC")->result();

        $body .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">RM</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Nama</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Tgl Lahir</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Alamat</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Pendidikan</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Pekerjaan</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Agama</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->kode_member . '</td>
                <td style="border: 1px solid black;">' . $s->nama . '</td>
                <td style="border: 1px solid black;">' . date('d-m-y', strtotime($s->tgl_lahir)) . '</td>
                <td style="border: 1px solid black;">' . $s->provinsi . ', ' . $s->kabupaten . ', ' . $s->kecamatan . ', ' . $s->desa . ', ' . $s->kodepos . '</td>
                <td style="border: 1px solid black;">' . $s->pendidikan . '</td>
                <td style="border: 1px solid black;">' . $s->pekerjaan . '</td>
                <td style="border: 1px solid black;">' . $s->agama . '</td>
            </tr>';
            $no++;
        }

        $body .= '</table>';

        $judul = 'Report Member';
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // kasir
    public function kasir($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $dari           = $this->input->get('dari');
        $sampai         = $this->input->get('sampai');

        // sintak
        $sintak         = $this->db->query("SELECT m.* FROM pembayaran m WHERE tgl_pembayaran >= '$dari' AND tgl_pembayaran <= '$sampai'")->result();

        $body .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Tgl/Jam Bayar</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Invoice</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">No. Transaksi</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Jenis Bayar</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Kasir</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black; text-align: center;">' . date('d-m-Y', strtotime($s->tgl_pembayaran)) . ' ~ ' . date('H:i:s', strtotime($s->jam_pembayaran)) . '</td>
                <td style="border: 1px solid black;">' . $s->invoice . '</td>
                <td style="border: 1px solid black;">' . $s->no_trx . '</td>
                <td style="border: 1px solid black; text-align: center;">' . (($s->jenis_pembayaran > 0) ? (($s->jenis_pembayaran == 1) ? 'Card' : 'Cash + Card') : 'Cash') . '</td>
                <td style="border: 1px solid black;">' . '(' . $s->kode_user . ') ' . $this->M_global->getData('user', ['kode_user' => $s->kode_user])->nama . '</td>
            </tr>';
            $no++;
        }

        $body .= '</table>';

        $judul = 'Report Kasir';
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // barang_po_in
    public function barang_po_in($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $dari           = $this->input->get('dari');
        $sampai         = $this->input->get('sampai');

        // sintak
        $sintak         = $this->db->query("SELECT m.* FROM barang_po_in_header m WHERE tgl_po >= '$dari' AND tgl_po <= '$sampai'")->result();

        $body .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Invoice</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Tgl/Jam Po</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Pemasok</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Gudang</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Pengaju</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Total</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            if ($param == 1) {
                $total = number_format($s->total);
            } else {
                $total = ceil($s->total);
            }

            $body .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->invoice . '</td>
                <td style="border: 1px solid black; text-align: center;">' . date('d-m-Y', strtotime($s->tgl_po)) . ' ~ ' . date('H:i:s', strtotime($s->jam_po)) . '</td>
                <td style="border: 1px solid black;">' . $this->M_global->getData('m_supplier', ['kode_supplier' => $s->kode_supplier])->nama . '</td>
                <td style="border: 1px solid black;">' . $this->M_global->getData('m_gudang', ['kode_gudang' => $s->kode_gudang])->nama . '</td>
                <td style="border: 1px solid black;">' . '(' . $s->kode_user . ') ' . $this->M_global->getData('user', ['kode_user' => $s->kode_user])->nama . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $total . '</td>
            </tr>';
            $no++;
        }

        $body .= '</table>';

        $judul = 'Report Pre Order';
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // barang_in
    public function barang_in($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $dari           = $this->input->get('dari');
        $sampai         = $this->input->get('sampai');

        // sintak
        $sintak         = $this->db->query("SELECT m.* FROM barang_in_header m WHERE tgl_beli >= '$dari' AND tgl_beli <= '$sampai'")->result();

        $body .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Invoice</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Tgl/Jam Po</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Pemasok</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Gudang</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Pengaju</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Total</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            if ($param == 1) {
                $total = number_format($s->total);
            } else {
                $total = ceil($s->total);
            }

            $body .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->invoice . '</td>
                <td style="border: 1px solid black; text-align: center;">' . date('d-m-Y', strtotime($s->tgl_beli)) . ' ~ ' . date('H:i:s', strtotime($s->jam_beli)) . '</td>
                <td style="border: 1px solid black;">' . $this->M_global->getData('m_supplier', ['kode_supplier' => $s->kode_supplier])->nama . '</td>
                <td style="border: 1px solid black;">' . $this->M_global->getData('m_gudang', ['kode_gudang' => $s->kode_gudang])->nama . '</td>
                <td style="border: 1px solid black;">' . '(' . $s->kode_user . ') ' . $this->M_global->getData('user', ['kode_user' => $s->kode_user])->nama . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $total . '</td>
            </tr>';
            $no++;
        }

        $body .= '</table>';

        $judul = 'Report Pembelian';
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // barang_in_retur
    public function barang_in_retur($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $dari           = $this->input->get('dari');
        $sampai         = $this->input->get('sampai');

        // sintak
        $sintak         = $this->db->query("SELECT m.* FROM barang_in_retur_header m WHERE tgl_retur >= '$dari' AND tgl_retur <= '$sampai'")->result();

        $body .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Invoice</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Tgl/Jam Po</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Pemasok</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Gudang</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Pengaju</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Total</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            if ($param == 1) {
                $total = number_format($s->total);
            } else {
                $total = ceil($s->total);
            }

            $body .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->invoice . '</td>
                <td style="border: 1px solid black; text-align: center;">' . date('d-m-Y', strtotime($s->tgl_retur)) . ' ~ ' . date('H:i:s', strtotime($s->jam_retur)) . '</td>
                <td style="border: 1px solid black;">' . $this->M_global->getData('m_supplier', ['kode_supplier' => $s->kode_supplier])->nama . '</td>
                <td style="border: 1px solid black;">' . $this->M_global->getData('m_gudang', ['kode_gudang' => $s->kode_gudang])->nama . '</td>
                <td style="border: 1px solid black;">' . '(' . $s->kode_user . ') ' . $this->M_global->getData('user', ['kode_user' => $s->kode_user])->nama . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $total . '</td>
            </tr>';
            $no++;
        }

        $body .= '</table>';

        $judul = 'Report Pembelian';
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // barang_out
    public function barang_out($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $dari           = $this->input->get('dari');
        $sampai         = $this->input->get('sampai');

        // sintak
        $sintak         = $this->db->query("SELECT m.* FROM barang_out_header m WHERE tgl_jual >= '$dari' AND tgl_jual <= '$sampai'")->result();

        $body .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Invoice</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Tgl/Jam Beli</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Gudang</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Pembeli</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Total</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            if ($param == 1) {
                $total = number_format($s->total);
            } else {
                $total = ceil($s->total);
            }

            $body .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->invoice . '</td>
                <td style="border: 1px solid black; text-align: center;">' . date('d-m-Y', strtotime($s->tgl_jual)) . ' ~ ' . date('H:i:s', strtotime($s->jam_jual)) . '</td>
                <td style="border: 1px solid black;">' . $this->M_global->getData('m_gudang', ['kode_gudang' => $s->kode_gudang])->nama . '</td>
                <td style="border: 1px solid black;">' . '(' . $s->kode_user . ') ' . $this->M_global->getData('user', ['kode_user' => $s->kode_user])->nama . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $total . '</td>
            </tr>';
            $no++;
        }

        $body .= '</table>';

        $judul = 'Report Penjualan';
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // barang_out_retur
    public function barang_out_retur($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $dari           = $this->input->get('dari');
        $sampai         = $this->input->get('sampai');

        // sintak
        $sintak         = $this->db->query("SELECT m.* FROM barang_out_retur_header m WHERE tgl_retur >= '$dari' AND tgl_retur <= '$sampai'")->result();

        $body .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Invoice</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Tgl/Jam Beli</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Gudang</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Pembeli</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Total</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            if ($param == 1) {
                $total = number_format($s->total);
            } else {
                $total = ceil($s->total);
            }

            $body .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->invoice . '</td>
                <td style="border: 1px solid black; text-align: center;">' . date('d-m-Y', strtotime($s->tgl_retur)) . ' ~ ' . date('H:i:s', strtotime($s->jam_retur)) . '</td>
                <td style="border: 1px solid black;">' . $this->M_global->getData('m_gudang', ['kode_gudang' => $s->kode_gudang])->nama . '</td>
                <td style="border: 1px solid black;">' . '(' . $s->kode_user . ') ' . $this->M_global->getData('user', ['kode_user' => $s->kode_user])->nama . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $total . '</td>
            </tr>';
            $no++;
        }

        $body .= '</table>';

        $judul = 'Report Penjualan';
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // penyesuaian_stok
    public function penyesuaian_stok($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $dari           = $this->input->get('dari');
        $sampai         = $this->input->get('sampai');

        // sintak
        $sintak         = $this->db->query("SELECT m.* FROM penyesuaian_header m WHERE tgl_penyesuaian >= '$dari' AND tgl_penyesuaian <= '$sampai'")->result();

        $body .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 30%; border: 1px solid black; background-color: #272a3f; color: white;">Invoice</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Tgl/Jam Penyesuaian</th>
            <th style="width: 25%; border: 1px solid black; background-color: #272a3f; color: white;">Gudang</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Pengaju</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            if ($param == 1) {
                $total = number_format($s->total);
            } else {
                $total = ceil($s->total);
            }

            $body .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->invoice . '</td>
                <td style="border: 1px solid black; text-align: center;">' . date('d-m-Y', strtotime($s->tgl_penyesuaian)) . ' ~ ' . date('H:i:s', strtotime($s->jam_penyesuaian)) . '</td>
                <td style="border: 1px solid black;">' . $this->M_global->getData('m_gudang', ['kode_gudang' => $s->kode_gudang])->nama . '</td>
                <td style="border: 1px solid black;">' . '(' . $s->kode_user . ') ' . $this->M_global->getData('user', ['kode_user' => $s->kode_user])->nama . '</td>
            </tr>';
            $no++;
        }

        $body .= '</table>';

        $judul = 'Report Penjualan';
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // activity user
    public function activity_user()
    {
        $tgl            = $this->input->get('tgl');
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan

        // sintak
        $header         = $this->db->query("SELECT a.* FROM activity_user a WHERE a.waktu LIKE '%$tgl%' LIMIT 1")->row();
        $user           = $this->M_global->getData('user', ['email' => $header->email]);
        $role           = $this->M_global->getData('m_role', ['kode_role' => $user->kode_role]);
        $sintak         = $this->db->query("SELECT a.* FROM activity_user a WHERE a.waktu LIKE '%$tgl%'")->result();

        $body .= '<table style="width: 100%; font-size: 14px;" cellpadding="2px">';
        $body .= '<tr>
            <td style="width: 10%;">Email</td>
            <td style="width: 2%;">:</td>
            <td style="width: 88%;">' . (($header) ? $header->email : '@gmail') . '</td>
        </tr>
        <tr>
            <td style="width: 10%;">Nama</td>
            <td style="width: 2%;">:</td>
            <td style="width: 88%;">' . (($user) ? $user->nama : 'Undefined') . '</td>
        </tr>
        <tr>
            <td style="width: 10%;">Phone</td>
            <td style="width: 2%;">:</td>
            <td style="width: 88%;">' . (($user) ? $user->nohp : 'Undefined') . '</td>
        </tr>
        <tr>
            <td style="width: 10%;">Jabatan</td>
            <td style="width: 2%;">:</td>
            <td style="width: 88%;">' . (($role) ? $role->keterangan : 'Undefined') . '</td>
        </tr>
        ';
        $body .= '</table><br>';

        $body .= '<table style="width: 100%; font-size: 12px;" cellpadding="5px">';
        $body .= '<thead>
            <tr>
                <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
                <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Menu</th>
                <th style="width: 37%; border: 1px solid black; background-color: #272a3f; color: white;">Kegiatan</th>
                <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Waktu</th>
                <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Cabang</th>
                <th style="width: 8%; border: 1px solid black; background-color: #272a3f; color: white;">Shift</th>
            </tr>
        </thead>
        <tbody>';

        if (count($sintak) > 0) {
            $no = 1;
            foreach ($sintak as $s) {
                $body .= '<tr>
                    <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                    <td style="border: 1px solid black;">' . $s->menu . '</td>
                    <td style="border: 1px solid black;">' . str_replace($header->email, '', $s->kegiatan) . '</td>
                    <td style="border: 1px solid black;">' . date('d/m/Y H:i:s', strtotime($s->waktu)) . '</td>
                    <td style="border: 1px solid black; text-align: center;">' . $s->kode_cabang . '</td>
                    <td style="border: 1px solid black; text-align: center;">' . $s->shift . '</td>
                </tr>';
                $no++;
            }
        } else {
            $body .= '<tr>
                <td colspan="6" style="border: 1px solid black; text-align: center;">Tidak Ada Aktifitas</td>
            </tr>';
        }


        $body .= '</tbody>
        </table>';

        $judul = 'Aktifitas User ~ ' . (($header) ? $header->email : '@gmail');
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, 1, $position, $filename, $web_setting);
    }

    // pendaftaran
    public function pendaftaran($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'L'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan

        $poli = $this->input->get('poli');
        $dari = $this->input->get('dari');
        $sampai = $this->input->get('sampai');

        if (($poli == '' || $poli == null || $poli == 'null')) {
            $sin_add = ' WHERE (p.tgl_daftar >= "' . $dari . '" AND p.tgl_daftar <= "' . $sampai . '")';
        } else {
            $sin_add = ' WHERE p.kode_poli = "' . $poli . '" AND (p.tgl_daftar >= "' . $dari . '" AND p.tgl_daftar <= "' . $sampai . '")';
        }

        // sintak
        $sintak         = $this->db->query("SELECT p.*, m.nama, pol.keterangan AS poli, dok.nama AS dokter, r.keterangan AS ruang FROM pendaftaran p JOIN member m ON m.kode_member = p.kode_member JOIN m_poli pol ON pol.kode_poli = p.kode_poli JOIN dokter dok ON dok.kode_dokter = p.kode_dokter JOIN m_ruang r ON r.kode_ruang = p.kode_ruang $sin_add ORDER BY p.tgl_daftar, p.jam_daftar ASC")->result();

        $body .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 12%; border: 1px solid black; background-color: #272a3f; color: white;">Tgl/Jam Masuk</th>
            <th style="width: 12%; border: 1px solid black; background-color: #272a3f; color: white;">Tgl/Jam Keluar</th>
            <th style="width: 13%; border: 1px solid black; background-color: #272a3f; color: white;">No Trx</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Member</th>
            <th style="width: 33%; border: 1px solid black; background-color: #272a3f; color: white;">Dokter</th>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">Status</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            $body .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . (!empty($s->tgl_daftar) ?  date('d-m-y', strtotime($s->tgl_daftar)) : 'xx-xx-xxxx') . '/' . (!empty($s->jam_daftar) ? date('H:i:s', strtotime($s->jam_daftar)) : 'xx:xx') . '</td>
                <td style="border: 1px solid black;">' . (!empty($s->tgl_keluar) ? date('d-m-y', strtotime($s->tgl_keluar)) : 'xx-xx-xxxx') . '/' . (!empty($s->jam_keluar) ? date('H:i:s', strtotime($s->jam_keluar)) : 'xx:xx') . '</td>
                <td style="border: 1px solid black;">' . $s->no_trx . '</td>
                <td style="border: 1px solid black;">' . $s->kode_member . ' ~ ' . $s->nama . '</td>
                <td style="border: 1px solid black;">' . $s->kode_dokter . ' ~ Dokter: ' . $s->dokter . ' ~ Poli: ' . $s->poli . ' ~ Ruang: ' . $s->ruang . '</td>
                <td style="border: 1px solid black; text-align: center;">' . (($s->status_trx == 1) ? (($s->status_trx == 2) ? 'Batal' : 'Selesai') : 'Tutup') . '</td>
            </tr>';
            $no++;
        }

        $body .= '</table>';

        $judul = 'Report Pendaftaran';
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // riwayat_stok
    public function riwayat_stok($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan

        $kode_cabang = $this->session->userdata('cabang');
        $kode_barang = $this->input->get('kode_barang');
        $kode_gudang = $this->input->get('kode_gudang');

        // sintak
        $sintak         = $this->db->query("SELECT * FROM (
            SELECT
            CONCAT(DATE_FORMAT(h.tgl_beli, '%d/%m/%Y'), ' ~ ', h.jam_beli) AS record_date,
            h.invoice,
            CONCAT('Pembelian ~ ', s.nama) AS keterangan,
            d.qty_konversi AS masuk,
            0 AS keluar,
            h.tgl_beli AS tgl,
            h.jam_beli AS jam,
            d.kode_barang,
            h.kode_cabang AS kode_cabang,
            h.kode_gudang
            FROM barang_in_header h
            JOIN barang_in_detail d ON h.invoice = d.invoice
            JOIN barang b ON d.kode_barang = b.kode_barang
            JOIN m_supplier s ON h.kode_supplier = s.kode_supplier
            WHERE h.is_valid = 1

            UNION ALL

            SELECT
            CONCAT(DATE_FORMAT(h.tgl_retur, '%d/%m/%Y'), ' ~ ', h.jam_retur) AS record_date,
            h.invoice,
            CONCAT('Retur Pembelian ~ ', s.nama) AS keterangan,
            0 AS masuk,
            d.qty_konversi AS keluar,
            h.tgl_retur AS tgl,
            h.jam_retur AS jam,
            d.kode_barang,
            h.kode_cabang AS kode_cabang,
            h.kode_gudang
            FROM barang_in_retur_header h
            JOIN barang_in_retur_detail d ON h.invoice = d.invoice
            JOIN barang b ON d.kode_barang = b.kode_barang
            JOIN m_supplier s ON h.kode_supplier = s.kode_supplier
            WHERE h.is_valid = 1

            UNION ALL

            SELECT
            CONCAT(DATE_FORMAT(h.tgl_jual, '%d/%m/%Y'), ' ~ ', h.jam_jual) AS record_date,
            h.invoice,
            CONCAT('Penjualan ~ ', s.nama) AS keterangan,
            0 AS masuk,
            d.qty_konversi AS keluar,
            h.tgl_jual AS tgl,
            h.jam_jual AS jam,
            d.kode_barang,
            h.kode_cabang AS kode_cabang,
            h.kode_gudang
            FROM barang_out_header h
            JOIN barang_out_detail d ON h.invoice = d.invoice
            JOIN barang b ON d.kode_barang = b.kode_barang
            JOIN member s ON h.kode_member = s.kode_member
            WHERE h.status_jual = 1

            UNION ALL

            SELECT
            CONCAT(DATE_FORMAT(h.tgl_retur, '%d/%m/%Y'), ' ~ ', h.jam_retur) AS record_date,
            h.invoice,
            CONCAT('Retur Penjualan ~ ', s.nama) AS keterangan,
            d.qty_konversi AS masuk,
            0 AS keluar,
            h.tgl_retur AS tgl,
            h.jam_retur AS jam,
            d.kode_barang,
            h.kode_cabang AS kode_cabang,
            h.kode_gudang
            FROM barang_out_retur_header h
            JOIN barang_out_retur_detail d ON h.invoice = d.invoice
            JOIN barang b ON d.kode_barang = b.kode_barang
            JOIN member s ON h.kode_member = s.kode_member
            WHERE h.is_valid = 1

            UNION ALL

            SELECT
            CONCAT(DATE_FORMAT(h.tgl_penyesuaian, '%d/%m/%Y'), ' ~ ', h.jam_penyesuaian) AS record_date,
            h.invoice,
            CONCAT('Adjustment ~ ', s.nama) AS keterangan,
            d.qty_konversi AS masuk,
            0 AS keluar,
            h.tgl_penyesuaian AS tgl,
            h.jam_penyesuaian AS jam,
            d.kode_barang,
            h.kode_cabang AS kode_cabang,
            h.kode_gudang
            FROM penyesuaian_header h
            JOIN penyesuaian_detail d ON h.invoice = d.invoice
            JOIN barang b ON d.kode_barang = b.kode_barang
            JOIN user s ON h.kode_user = s.kode_user
            WHERE h.acc = 1

            UNION ALL -- Mutasi dari (keluar)

            SELECT
            CONCAT(DATE_FORMAT(h.tgl, '%d/%m/%Y'), ' ~ ', h.jam) AS record_date,
            h.invoice,
            CONCAT('Mutasi ', IF(jenis = 0, 'Gudang', 'Cabang'), ' Keluar ~ ', IF(jenis = 0, (SELECT nama FROM m_gudang WHERE kode_gudang = h.dari), (SELECT cabang FROM cabang WHERE kode_cabang = h.dari)), ' menuju ', IF(jenis = 0, (SELECT nama FROM m_gudang WHERE kode_gudang = h.menuju), (SELECT cabang FROM cabang WHERE kode_cabang = h.menuju))) AS keterangan,
            0 AS masuk,
            d.qty_konversi AS keluar,
            h.tgl AS tgl,
            h.jam AS jam,
            d.kode_barang,
            IF(jenis = 0, h.kode_cabang, h.dari) AS kode_cabang,
            IF(jenis = 0, h.dari, (SELECT kode_gudang FROM m_gudang WHERE utama = 1)) AS kode_gudang
            FROM mutasi_header h
            JOIN mutasi_detail d ON h.invoice = d.invoice
            JOIN barang b ON d.kode_barang = b.kode_barang
            JOIN user s ON h.user = s.kode_user
            WHERE h.status = 1

            UNION ALL -- Mutasi menuju (masuk)

            SELECT
            CONCAT(DATE_FORMAT(h.tgl, '%d/%m/%Y'), ' ~ ', h.jam) AS record_date,
            h.invoice,
            CONCAT('Mutasi ', IF(jenis = 0, 'Gudang', 'Cabang'), ' Masuk ~ ', IF(jenis = 0, (SELECT nama FROM m_gudang WHERE kode_gudang = h.menuju), (SELECT cabang FROM cabang WHERE kode_cabang = h.menuju)), ' dari ', IF(jenis = 0, (SELECT nama FROM m_gudang WHERE kode_gudang = h.dari), (SELECT cabang FROM cabang WHERE kode_cabang = h.dari))) AS keterangan,
            d.qty_konversi AS masuk,
            0 AS keluar,
            h.tgl AS tgl,
            h.jam AS jam,
            d.kode_barang,
            IF(jenis = 0, h.kode_cabang, h.menuju) AS kode_cabang,
            IF(jenis = 0, h.menuju, (SELECT kode_gudang FROM m_gudang WHERE utama = 1)) AS kode_gudang
            FROM mutasi_header h
            JOIN mutasi_detail d ON h.invoice = d.invoice
            JOIN barang b ON d.kode_barang = b.kode_barang
            JOIN user s ON h.user = s.kode_user
            WHERE h.status = 1
        ) AS semua WHERE kode_barang = '$kode_barang' AND kode_gudang = '$kode_gudang' AND kode_cabang = '$kode_cabang' ORDER BY tgl, jam ASC")->result();

        $barang = $this->M_global->getData('barang', ['kode_barang' => $kode_barang]);
        $gudang = $this->M_global->getData('m_gudang', ['kode_gudang' => $kode_gudang]);
        $cabang = $this->M_global->getData('cabang', ['kode_cabang' => $kode_cabang]);

        $body .= '<table style="font-size: 10px;">
            <tr>
                <td>Barang</td>
                <td> : </td>
                <td>' . $kode_barang . ' ~ ' . $barang->nama . '</td>
            </tr>
            <tr>
                <td>Gudang</td>
                <td> : </td>
                <td>' . $gudang->nama . '</td>
            </tr>
            <tr>
                <td>Cabang</td>
                <td> : </td>
                <td>' . $cabang->cabang . '</td>
            </tr>
        </table><br>';

        $body .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Waktu</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">No Transaksi</th>
            <th style="width: 30%; border: 1px solid black; background-color: #272a3f; color: white;">Keterangan</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Stok Masuk</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Stok Keluar</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Stok Akhir</th>
        </tr>';

        $no = 1;
        $stok_akhir = 0;
        foreach ($sintak as $s) {
            $stok_akhir += ($s->masuk - $s->keluar);

            $body .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->record_date . '</td>
                <td style="border: 1px solid black;">' . $s->invoice . '</td>
                <td style="border: 1px solid black;">' . $s->keterangan . '</td>
                <td style="border: 1px solid black; text-align: right;">' . konversi_show_satuan($s->masuk, $kode_barang) . '</td>
                <td style="border: 1px solid black; text-align: right;">' . konversi_show_satuan($s->keluar, $kode_barang) . '</td>
                <td style="border: 1px solid black; text-align: right;">' . konversi_show_satuan($stok_akhir, $kode_barang) . '</td>
            </tr>';
            $no++;
        }

        $body .= '</table>';

        $judul = 'Report Stock of History ' . $kode_barang;
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // mutasi_po
    public function mutasi_po($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $dari           = $this->input->get('dari');
        $sampai         = $this->input->get('sampai');

        // sintak
        $sintak         = $this->db->query("SELECT m.* FROM mutasi_po_header m WHERE tgl_po >= '$dari' AND tgl_po <= '$sampai'")->result();

        $body .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Invoice</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Tgl/Jam Pengajuan</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Jenis Mutasi</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Dari</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Menuju</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Total</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">User</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            if ($param == 1) {
                $total = number_format($s->total);
            } else {
                $total = ceil($s->total);
            }

            if ($s->jenis_po == 0) { // jenis gudang
                $dari = $this->M_global->getData('m_gudang', ['kode_gudang' => $s->dari])->nama;
                $menuju = $this->M_global->getData('m_gudang', ['kode_gudang' => $s->menuju])->nama;
            } else { // jenis cabang
                $dari = $this->M_global->getData('cabang', ['kode_cabang' => $s->dari])->cabang;
                $menuju = $this->M_global->getData('cabang', ['kode_cabang' => $s->menuju])->cabang;
            }

            $body .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->invoice . '</td>
                <td style="border: 1px solid black; text-align: center;">' . date('d-m-Y', strtotime($s->tgl_po)) . ' ~ ' . date('H:i:s', strtotime($s->jam_po)) . '</td>
                <td style="border: 1px solid black; text-align: center;">' . ($s->jenis_po > 0 ? 'Mutasi Cabang' : 'Mutasi Gudang') . '</td>
                <td style="border: 1px solid black;">' . $dari . '</td>
                <td style="border: 1px solid black;">' . $menuju . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $total . '</td>
                <td style="border: 1px solid black;">' . $this->M_global->getData('user', ['kode_user' => $s->user])->nama . '</td>
            </tr>';
            $no++;
        }

        $body .= '</table>';

        $judul = 'Report Pengajuan Mutasi';
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }

    // mutasi
    public function mutasi($param)
    {
        // param website
        $web_setting    = $this->M_global->getData('web_setting', ['id' => 1]);

        $position       = 'P'; // cek posisi l/p

        // body cetakan
        $body           = '';
        $body           .= '<br><br>'; // beri jarak antara kop dengan body

        // parameter dari view laporan
        $dari           = $this->input->get('dari');
        $sampai         = $this->input->get('sampai');

        // sintak
        $sintak         = $this->db->query("SELECT m.* FROM mutasi_header m WHERE tgl >= '$dari' AND tgl <= '$sampai'")->result();

        $body .= '<table style="width: 100%; font-size: 10px;" cellpadding="5px">';
        $body .= '<tr>
            <th style="width: 5%; border: 1px solid black; background-color: #272a3f; color: white;">#</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">Invoice</th>
            <th style="width: 20%; border: 1px solid black; background-color: #272a3f; color: white;">Tgl/Jam Pengajuan</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Jenis Mutasi</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Dari</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Menuju</th>
            <th style="width: 15%; border: 1px solid black; background-color: #272a3f; color: white;">Total</th>
            <th style="width: 10%; border: 1px solid black; background-color: #272a3f; color: white;">User</th>
        </tr>';

        $no = 1;
        foreach ($sintak as $s) {
            if ($param == 1) {
                $total = number_format($s->total);
            } else {
                $total = ceil($s->total);
            }

            if ($s->jenis == 0) { // jenis gudang
                $dari = $this->M_global->getData('m_gudang', ['kode_gudang' => $s->dari])->nama;
                $menuju = $this->M_global->getData('m_gudang', ['kode_gudang' => $s->menuju])->nama;
            } else { // jenis cabang
                $dari = $this->M_global->getData('cabang', ['kode_cabang' => $s->dari])->cabang;
                $menuju = $this->M_global->getData('cabang', ['kode_cabang' => $s->menuju])->cabang;
            }

            $body .= '<tr>
                <td style="border: 1px solid black; text-align: right;">' . $no . '</td>
                <td style="border: 1px solid black;">' . $s->invoice . '</td>
                <td style="border: 1px solid black; text-align: center;">' . date('d-m-Y', strtotime($s->tgl)) . ' ~ ' . date('H:i:s', strtotime($s->jam)) . '</td>
                <td style="border: 1px solid black; text-align: center;">' . ($s->jenis > 0 ? 'Mutasi Cabang' : 'Mutasi Gudang') . '</td>
                <td style="border: 1px solid black;">' . $dari . '</td>
                <td style="border: 1px solid black;">' . $menuju . '</td>
                <td style="border: 1px solid black; text-align: right;">' . $total . '</td>
                <td style="border: 1px solid black;">' . $this->M_global->getData('user', ['kode_user' => $s->user])->nama . '</td>
            </tr>';
            $no++;
        }

        $body .= '</table>';

        $judul = 'Report Pengajuan Mutasi';
        $filename = $judul; // nama file yang ingin di simpan

        // jalankan fungsi cetak_pdf
        cetak_pdf($judul, $body, $param, $position, $filename, $web_setting);
    }
}
