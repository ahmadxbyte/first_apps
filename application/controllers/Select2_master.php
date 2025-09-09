<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Select2_master extends CI_Controller
{
    // variable open public untuk controller Home
    public $data;

    public function __construct()
    {
        parent::__construct();
        // load model M_auth
        $this->load->model("M_auth");

        // if (!empty($this->session->userdata("email"))) { // jika session email masih ada
        //     // ambil isi data berdasarkan email session dari table user, kemudian tampung ke variable $user
        //     $user = $this->M_global->getData("user", ["email" => $this->session->userdata("email")]);

        //     // tampung data ke variable data public
        //     $this->data = [
        //         'nama'      => $user->nama,
        //         'email'     => $user->email,
        //         'kode_role' => $user->kode_role,
        //         'actived'   => $user->actived,
        //         'foto'      => $user->foto,
        //         'shift'     => $this->session->userdata('shift'),
        //         'menu'      => 'Home',
        //     ];
        // } else { // selain itu
        //     // kirimkan kembali ke Auth
        //     redirect('Auth');
        // }
    }

    // master cabang
    function dataCabang()
    {
        $email = $this->input->get('email');
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getCabang($key, $email));
    }

    // master cabang member
    function dataCabangMember()
    {
        $email = $this->input->get('email');
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getCabangMember($key, $email));
    }

    function dataAllCabang()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getAllCabang($key));
    }

    function dataTindakanMasterx()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getTindakanMasterx($key));
    }

    function dataPaketTindakan($bayar, $kelas, $poli)
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getPaketTindakan($bayar, $kelas, $poli, $key));
    }

    function dataTindakanMaster($bayar, $kelas, $poli, $jenis)
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getTindakanMaster($bayar, $kelas, $poli, $jenis, $key));
    }

    function dataTindakanMasterLab($bayar, $kelas, $poli, $jenis)
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getTindakanMasterLab($bayar, $kelas, $poli, $jenis, $key));
    }

    function dataTindakanMasterRad($bayar, $kelas, $poli, $jenis)
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getTindakanMasterRad($bayar, $kelas, $poli, $jenis, $key));
    }

    function dataTarifSingle()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getTarifSingle($key));
    }

    function dataTarifSinglex()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getTarifSinglex($key));
    }

    function dataTarifPaketx()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getTarifPaketx($key));
    }

    function dataKlasifikasiAkun()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getKlasifikasiAkun($key));
    }

    function dataTarifPaket()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getTarifPaket($key));
    }

    function dataTerdaftar()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getTerdaftar($key));
    }

    // master kategori
    function dataKategori()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getKategori($key));
    }

    // master pajak
    function dataPajak()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getPajak($key));
    }

    // master prefix
    function dataPrefix()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getPrefix($key));
    }

    // master prosinsi
    function dataProvinsi()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getProvinsi($key));
    }

    // master kabupaten
    function dataKabupaten($kode_provinsi = '')
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getKabupaten($key, $kode_provinsi));
    }

    // akun select
    function dataAkunSel($kode_klasifikasi = '')
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getAkunSel($key, $kode_klasifikasi));
    }

    // master kecamatan
    function dataKecamatan($kode_kabupaten = '')
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getKecamatan($key, $kode_kabupaten));
    }

    // master member
    function dataMember($param)
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getMember($key, $param));
    }

    // master user
    function dataUser()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getUser($key));
    }

    // master user
    function dataUserAll()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getUserAll($key));
    }

    // master jenis bayar
    function dataJenisBayar()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getJenisBayar($key));
    }

    // master tindakan
    function dataTindakan()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getTindakan($key));
    }

    // master kelas
    function dataKelas()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getKelas($key));
    }

    // master poli
    function dataPoli()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getPoli($key));
    }

    // master dokter poli
    function dataDokterPoli($kode_poli = '')
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getDokterPoli($key, $kode_poli));
    }

    // master poli dokter
    function dataPoliDokter($kode_dokter = '')
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getPoliDokter($key, $kode_dokter));
    }

    // master bed
    function dataBed($kode_ruang = '')
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getBed($key, $kode_ruang));
    }

    // master dataBarangStok
    function dataBarangStok()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getBarangStok($key));
    }

    // master dokter all
    function dataDokterAll()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getDokterAll($key));
    }

    // master ruang jd
    function dataRuangJd($kode_poli, $hari, $kode_cabang)
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getRuangJd($key, $kode_poli, $hari, $kode_cabang));
    }

    // master ruang
    function dataRuang()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getRuang($key));
    }

    // master supplier
    function dataSupplier()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getSupplier($key));
    }

    // master gudang Internal
    function dataGudangInt()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getGudang($key, 1));
    }

    // master gudang Logistik
    function dataGudangLog()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getGudang($key, 2));
    }

    // master pekerjaan
    function dataPekerjaan()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getPekerjaan($key));
    }

    // master agama
    function dataAgama()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getAgama($key));
    }

    // master pendidikan
    function dataPendidikan()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getPendidikan($key));
    }

    // master pendaftaran
    function dataPendaftaran($kode_poli)
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getPendaftaran($key, $kode_poli));
    }

    // master penjualan
    function dataPenjualan()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getPenjualan($key));
    }

    // master penjualan retur
    function dataReturJual()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getPenjualanRetur($key));
    }

    // master bank
    function dataBank()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getBank($key));
    }

    // master tipe bank
    function dataTipeBank()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getTipeBank($key));
    }

    // master penjualan untuk diretur
    function dataJualForRetur()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getJualForRetur($key));
    }

    // master promo
    function dataPromo($min_buy)
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getPromo($key, $min_buy));
    }

    // master barang
    function dataBarang()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getBarang($key));
    }

    // master Kas Bank
    function dataKasBank()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->getKasBank($key));
    }

    // master kategori tarif
    function dataKatTarif()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->dataKatTarif($key));
    }

    // master icd9
    function dataIcd9()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->dataIcd9($key));
    }

    // master icd10
    function dataIcd10()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->dataIcd10($key));
    }

    // master dataCaraMasuk
    function dataCaraMasuk()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->dataCaraMasuk($key));
    }

    // master dataGroupCoa
    function dataGroupCoa()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->dataGroupCoa($key));
    }

    // master dataMasterCoa
    function dataMasterCoa()
    {
        $key = $this->input->post('searchTerm');
        echo json_encode($this->M_select2->dataMasterCoa($key));
    }
}
