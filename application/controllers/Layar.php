<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Layar extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("M_global");
    }

    public function index()
    {
        $cabangnya    = $this->input->get('cabang');
        $web_setting  = $this->M_global->getData('web_setting', ['id' => 1]);
        $polis        = $this->M_global->getDataResult('m_poli', ['hapus' => 0]);

        $data = [
            'nama_apps' => $web_setting->nama,
            'web'       => $web_setting,
            'polis'     => $polis,
            'cabang'    => $cabangnya,
        ];

        $this->load->view('Layar/index', $data);
    }

    public function get_data_antrian($cabang)
    {
        $sql = "SELECT p.*, mp.keterangan as nama_poli, (SELECT keterangan FROM m_ruang WHERE kode_ruang = p.kode_ruang) AS nama_ruang
        FROM pendaftaran p 
        JOIN m_poli mp USING (kode_poli) 
        WHERE p.panggil > 0 
        AND p.kode_cabang = '" . $cabang . "'
        AND p.tgl_daftar = '" . date('Y-m-d') . "'
        AND p.panggil = (
            SELECT MAX(p2.panggil)
            FROM pendaftaran p2
            WHERE p2.kode_poli = p.kode_poli
            AND p2.kode_cabang = p.kode_cabang
            AND p2.tgl_daftar = p.tgl_daftar
        )
        ORDER BY p.panggil DESC";

        // AND p.tgl_daftar = '" . date('Y-m-d') . "'

        $pendaftaran = $this->db->query($sql)->result();

        echo json_encode($pendaftaran);
    }

    function suara($cabang)
    {
        $sql = "SELECT p.*, mp.keterangan 
        FROM pendaftaran p 
        JOIN m_poli mp USING (kode_poli) 
        WHERE p.p_ulang > 0
        AND p.kode_cabang = '" . $cabang . "' 
        AND p.tgl_daftar = '" . date('Y-m-d') . "'
        ORDER BY p.panggil DESC 
        LIMIT 1";

        $pendaftaran = $this->db->query($sql)->row();

        $antrian_data = [
            'no_trx'            => $pendaftaran ? $pendaftaran->no_trx : '',
            'no_antrian'        => $pendaftaran ? $pendaftaran->no_antrian : '-',
            'nama_poli'         => $pendaftaran ? $pendaftaran->keterangan : '',
            'kode_poli'         => $pendaftaran ? $pendaftaran->kode_poli : '',
            'panggil'           => $pendaftaran ? $pendaftaran->panggil : 0,
            'p_ulang'           => $pendaftaran ? $pendaftaran->p_ulang : 0,
        ];

        echo json_encode($antrian_data);
    }

    public function reset_no()
    {
        $no_trx = $this->input->get('no_trx');

        $this->M_global->updateData(
            'pendaftaran',
            ['p_ulang' => 0],
            ['no_trx' => $no_trx]
        );

        echo json_encode(['status' => 1]);
    }

    public function next_panggil($now_panggil)
    {
        $kode_cabang = $this->input->get('cabang');
        $kode_poli = $this->input->get('kode_poli');

        // Get next 3 patients in queue who haven't been called yet
        $list_px = $this->M_global->getDataResult(
            'pendaftaran',
            [
                'kode_cabang' => $kode_cabang,
                'no_antrian <>' => $now_panggil,
                'kode_poli' => $kode_poli,
                'panggil' => 0
            ],
            3,
            ['no_antrian', 'ASC']
        );

?>
        <div class="card">
            <div class="card-body">
                <?php

                foreach ($list_px as $lpx) {
                    echo '<div class="no-antrian-2">' . $lpx->no_antrian . '</div>';
                } ?>
            </div>
        </div>
<?php
    }
}
