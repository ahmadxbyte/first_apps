<?php
defined("BASEPATH") or exit("No direct script access allowed");

class M_coa extends CI_Model
{
    protected $table = "m_coa";

    protected $columns = ['id', 'kode_coa', 'kode_cabang', 'coa_name', 'coa_group', 'parent_id', 'is_header', 'is_active', 'coa_level', 'normal_balance', 'remark', 'tgl_coa', 'jam_coa'];

    protected $search_key = ['id', 'kode_coa', 'kode_cabang', 'coa_name', 'coa_group', 'parent_id', 'is_header', 'is_active', 'coa_level', 'normal_balance', 'remark', 'tgl_coa', 'jam_coa'];

    protected $order = ['id' => 'DESC'];

    public function __construct()
    {
        parent::__construct();
        setlocale(LC_ALL, "id_ID.utf8");
        date_default_timezone_set("Asia/Jakarta");
    }

    private function _get_datatables_query()
    {
        $this->db->query("SET SESSION sql_mode = ''");

        $this->db->select($this->columns);
        $this->db->from($this->table);
        $this->db->where("kode_cabang", $this->session->userdata("cabang"));

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

    public function get_datatables()
    {
        $this->_get_datatables_query();

        if (isset($_POST["length"]) && $_POST["length"] != -1) {
            $this->db->limit($_POST["length"], $_POST["start"]);
        }

        $query = $this->db->get();
        return $query->result();
    }


    public function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->query("SET SESSION sql_mode = ''");

        $this->db->select($this->columns);
        $this->db->from($this->table);

        $this->db->where("kode_cabang", $this->session->userdata("cabang"));

        return $this->db->count_all_results();
    }
}
