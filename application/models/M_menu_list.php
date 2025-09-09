<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_menu_list extends CI_Model
{
    private $table = 'm_menu';
    private $order = ['m.id AS idm', 'm.url', 'm.icon', 'm.nama'];
    private $kolom = ['m.id AS idm', 'm.url', 'm.icon', 'm.nama'];
    private $search = ['m.id', 'm.url', 'm.icon', 'm.nama'];

    public function __construct()
    {
        parent::__construct();
        setlocale(LC_ALL, 'id_ID.utf8');
        date_default_timezone_set('Asia/Jakarta');
    }

    private function _get_datatables_query()
    {
        $this->db->query("SET SESSION sql_mode = (SELECT REPLACE(REPLACE(REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY,', ''), ',ONLY_FULL_GROUP_BY', ''), 'ONLY_FULL_GROUP_BY', ''))");

        $this->db->select($this->kolom);
        $this->db->from($this->table . ' AS m');
        $this->db->order_by('m.id', 'asc');

        $i = 0;

        foreach ($this->search as $item) {
            if (!empty($_POST['search']['value'])) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->kolom[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $orderan = $this->order;
            $this->db->order_by(key($orderan), $orderan[key($orderan)]);
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
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
        $this->db->query("SET SESSION sql_mode = (SELECT REPLACE(REPLACE(REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY,', ''), ',ONLY_FULL_GROUP_BY', ''), 'ONLY_FULL_GROUP_BY', ''))");

        $this->db->select($this->kolom);
        $this->db->from($this->table . ' AS m');
        $this->db->order_by('m.id', 'asc');

        return $this->db->count_all_results();
    }
}
