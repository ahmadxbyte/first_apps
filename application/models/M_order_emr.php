<?php
defined("BASEPATH") or exit("No direct script access allowed");

class M_order_emr extends CI_Model
{
    protected $table = "pendaftaran";

    protected $columns = ['p.id', 'p.status_trx', 'p.kode_member', 'ed.date_dok', 'ed.time_dok', 'pol.keterangan AS poli', 'p.no_trx', 'm.nama', 'd.nama AS dokter', 'u.nama AS perawat'];

    protected $search_key = ['p.id', 'p.status_trx', 'p.kode_member', 'ed.date_dok', 'ed.time_dok', 'pol.keterangan', 'p.no_trx', 'm.nama', 'd.dokter', 'u.nama'];

    protected $order = ['ed.date_dok' => 'desc'];

    public function __construct()
    {
        parent::__construct();
        setlocale(LC_ALL, "id_ID.utf8");
        date_default_timezone_set("Asia/Jakarta");
    }

    private function _get_datatables_query($dari, $sampai, $tipe)
    {
        $this->db->query("SET SESSION sql_mode = ''");

        $this->db->select($this->columns);
        $this->db->from($this->table . " AS p");

        $this->db->join("emr_dok ed", "ed.no_trx = p.no_trx");
        $this->db->join("dokter d", "d.kode_dokter = p.kode_dokter");
        $this->db->join("emr_per ep", "ep.no_trx = p.no_trx");
        $this->db->join("member m", "m.kode_member = p.kode_member");
        $this->db->join("m_poli pol", "pol.kode_poli = p.kode_poli");
        $this->db->join("user u", "u.kode_user = ep.kode_user");

        $this->db->where("p.kode_cabang", $this->session->userdata("cabang"));

        $this->db->where('(ed.eracikan != "" OR p.no_trx IN (SELECT no_trx FROM emr_per_barang))');
        $this->db->where('p.no_trx NOT IN (SELECT no_trx FROM barang_out_header)');

        if ($tipe == 1) {
            $this->db->where(['ed.date_dok >=' => $dari]);
        } else {
            $this->db->where(['ed.date_dok >=' => $dari, 'ed.date_dok <=' => $sampai]);
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

    public function get_datatables($dari, $sampai, $tipe)
    {
        $this->_get_datatables_query($dari, $sampai, $tipe);
        if ($_POST["length"] != -1) {
            $this->db->limit($_POST["length"], $_POST["start"]);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($dari, $sampai, $tipe)
    {
        $this->_get_datatables_query($dari, $sampai, $tipe);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($dari, $sampai, $tipe)
    {
        $this->db->query("SET SESSION sql_mode = ''");

        $this->db->select($this->columns);
        $this->db->from($this->table . " AS p");

        $this->db->join("emr_dok ed", "ed.no_trx = p.no_trx");
        $this->db->join("dokter d", "d.kode_dokter = p.kode_dokter");
        $this->db->join("emr_per ep", "ep.no_trx = p.no_trx");
        $this->db->join("member m", "m.kode_member = p.kode_member");
        $this->db->join("m_poli pol", "pol.kode_poli = p.kode_poli");
        $this->db->join("user u", "u.kode_user = ep.kode_user");

        $this->db->where("p.kode_cabang", $this->session->userdata("cabang"));

        $this->db->where('(ed.eracikan != "" OR p.no_trx IN (SELECT no_trx FROM emr_per_barang))');
        $this->db->where('p.no_trx NOT IN (SELECT no_trx FROM barang_out_header)');

        if ($tipe == 1) {
            $this->db->where(['ed.date_dok >=' => $dari]);
        } else {
            $this->db->where(['ed.date_dok >=' => $dari, 'ed.date_dok <=' => $sampai]);
        }

        return $this->db->count_all_results();
    }
}
