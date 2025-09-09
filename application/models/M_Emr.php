<?php
defined("BASEPATH") or exit("No direct script access allowed");

class M_Emr extends CI_Model
{
    protected $table = "pendaftaran";

    protected $columns = ['p.id', 'p.no_trx', 'p.kode_jenis_bayar', 'jb.keterangan AS jenis_bayar', 'p.tgl_daftar', 'p.jam_daftar', 'p.kode_member', 'm.nama AS member', 'd.nama AS dokter', 'pol.keterangan', 'p.kode_poli', 'p.kode_ruang', 'p.kode_dokter', 'p.no_antrian', 'p.tgl_keluar', 'p.jam_keluar', 'p.status_trx', 'p.kode_user', 'p.shift', 'edc.verifikasi'];

    protected $search_key = ['p.id', 'p.no_trx', 'jb.keterangan', 'p.tgl_daftar', 'p.jam_daftar', 'p.kode_member', 'm.nama', 'd.nama', 'pol.keterangan', 'p.kode_poli', 'p.kode_ruang', 'p.kode_dokter', 'p.no_antrian', 'p.tgl_keluar', 'p.jam_keluar', 'p.status_trx', 'p.kode_user', 'p.shift'];

    protected $order = ['no_antrian' => 'DESC'];

    public function __construct()
    {
        parent::__construct();
        setlocale(LC_ALL, "id_ID.utf8");
        date_default_timezone_set("Asia/Jakarta");
    }

    private function _get_datatables_query($dari, $sampai, $kode_poli, $kode_dokter, $tipe)
    {
        $this->db->query("SET SESSION sql_mode = ''");

        $this->db->select($this->columns);
        $this->db->from($this->table . " AS p");

        $this->db->join("member m", "m.kode_member = p.kode_member");
        $this->db->join("dokter d", "d.kode_dokter = p.kode_dokter");
        $this->db->join("m_poli pol", "pol.kode_poli = p.kode_poli");
        $this->db->join("m_jenis_bayar jb", "jb.kode_jenis_bayar = p.kode_jenis_bayar");
        $this->db->join("emr_dok_cppt edc", "edc.no_trx = p.no_trx", "LEFT");

        $this->db->where("p.kode_cabang", $this->session->userdata("cabang"));

        if (in_array($this->session->userdata("kode_role"), ['R0009'])) {
            $this->db->where("p.no_trx IN (SELECT no_trx FROM emr_per WHERE kode_cabang = '" . $this->session->userdata("cabang") . "')");
        }

        // if ($this->db->where("p.no_trx NOT IN (SELECT no_trx FROM emr_per) OR p.no_trx NOT IN (SELECT no_trx FROM emr_dok)")) {
        // } else {
        // }
        if ($tipe == 1) {
            $this->db->where(['p.tgl_daftar >=' => $dari]);
        } else {
            $this->db->where(['p.tgl_daftar >=' => $dari, 'p.tgl_daftar <=' => $sampai]);
        }

        if (!empty($kode_poli) && empty($kode_dokter)) {
            $this->db->where("p.kode_poli", $kode_poli);
        } else if (empty($kode_poli) && !empty($kode_dokter)) {
            $this->db->where("p.kode_dokter", $kode_dokter);
        } else if (!empty($kode_poli) && !empty($kode_dokter)) {
            $this->db->group_start();
            $this->db->where("p.kode_poli", $kode_poli);
            $this->db->or_where("p.kode_dokter", $kode_dokter);
            $this->db->group_end();
        } else {
            // Tidak ada filter tambahan jika kedua parameter kosong
        }

        $i = 0;

        foreach ($this->search_key as $item) {
            if (!empty($_POST["search"]["value"])) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST["search"]["value"]);
                } else {
                    $this->db->or_like($item, $_POST["search"]["value"]);
                }
                if (count($this->search_key) - 1 == $i) {
                    $this->db->group_end();
                }
            }
            $i++;
        }

        if (isset($_POST["order"])) {
            $this->db->order_by($this->columns[$_POST["order"]["0"]["column"]], $_POST["order"]["0"]["dir"]);
        } elseif (!empty($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables($dari, $sampai, $kode_poli, $kode_dokter, $tipe)
    {
        $this->_get_datatables_query($dari, $sampai, $kode_poli, $kode_dokter, $tipe);

        if (isset($_POST["length"]) && $_POST["length"] != -1) {
            $this->db->limit($_POST["length"], $_POST["start"]);
        }

        $query = $this->db->get();
        return $query->result();
    }


    public function count_filtered($dari, $sampai, $kode_poli, $kode_dokter, $tipe)
    {
        $this->_get_datatables_query($dari, $sampai, $kode_poli, $kode_dokter, $tipe);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($dari, $sampai, $kode_poli, $kode_dokter, $tipe)
    {
        $this->db->query("SET SESSION sql_mode = ''");

        $this->db->select($this->columns);
        $this->db->from($this->table . " AS p");

        $this->db->join("member m", "m.kode_member = p.kode_member");
        $this->db->join("dokter d", "d.kode_dokter = p.kode_dokter");
        $this->db->join("m_poli pol", "pol.kode_poli = p.kode_poli");
        $this->db->join("m_jenis_bayar jb", "jb.kode_jenis_bayar = p.kode_jenis_bayar");
        $this->db->join("emr_dok_cppt edc", "edc.no_trx = p.no_trx", "LEFT");

        $this->db->where("p.kode_cabang", $this->session->userdata("cabang"));

        if ($tipe == 1) {
            $this->db->where(['p.tgl_daftar >=' => $dari]);
        } else {
            $this->db->where(['p.tgl_daftar >=' => $dari, 'p.tgl_daftar <=' => $sampai]);
        }

        if (!empty($kode_poli) && empty($kode_dokter)) {
            $this->db->where("p.kode_poli", $kode_poli);
        } else if (empty($kode_poli) && !empty($kode_dokter)) {
            $this->db->where("p.kode_dokter", $kode_dokter);
        } else if (!empty($kode_poli) && !empty($kode_dokter)) {
            $this->db->group_start();
            $this->db->where("p.kode_poli", $kode_poli);
            $this->db->or_where("p.kode_dokter", $kode_dokter);
            $this->db->group_end();
        } else {
            // Tidak ada filter tambahan jika kedua parameter kosong
        }

        return $this->db->count_all_results();
    }
}
