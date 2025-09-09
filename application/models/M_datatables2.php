<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_datatables2 extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        setlocale(LC_ALL, 'id_ID.utf8');
        date_default_timezone_set('Asia/Jakarta');
    }

    private function _get_datatables_query($table, $columns, $order_arr, $order, $order2, $condition_param1, $type, $month, $year, $param2, $condition_param2, $param3 = '', $condition_param3 = '')
    {
        $this->db->query("SET SESSION sql_mode = REPLACE(
            REPLACE(
                REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY,', ''),
            ',ONLY_FULL_GROUP_BY', ''),
        'ONLY_FULL_GROUP_BY', '')");

        $add_condition = $this->uri->segment(1) == 'Marketing' ? ' <= ' : '';

        $this->db->select($columns);
        $this->db->from($table);

        if ($this->uri->segment(1) === 'Reservasi') {
        } else {
            $this->db->where(['kode_cabang' => $this->session->userdata('cabang')]);
            if ($type == 1) {
                $date = date('Y-m-d');
                $this->db->where([$condition_param1 . $add_condition => $date]);
            } else {
                $this->db->where([$condition_param1 . ' >=' => $month, $condition_param1 . ' <= ' => $year]);
            }
        }

        if (!empty($param2)) {
            $this->db->where([$condition_param2 => $param2]);
        }

        foreach ($columns as $index => $item) {
            if (!empty($_POST['search']['value'])) {
                if ($index === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if ($index == count($columns) - 1)
                    $this->db->group_end();
            }
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($columns[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($order_arr)) {
            $this->db->order_by(key($order_arr), $order_arr[key($order_arr)]);
        }
    }

    public function get_datatables($table, $columns, $order_arr, $order, $order2, $condition_param1, $type, $month, $year, $param2, $condition_param2)
    {
        $this->_get_datatables_query($table, $columns, $order_arr, $order, $order2, $condition_param1, $type, $month, $year, $param2, $condition_param2);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($table, $columns, $order_arr, $order, $order2, $condition_param1, $type, $month, $year, $param2, $condition_param2)
    {
        $this->_get_datatables_query($table, $columns, $order_arr, $order, $order2, $condition_param1, $type, $month, $year, $param2, $condition_param2);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($table, $columns, $order_arr, $order, $order2, $condition_param1, $type, $month, $year, $param2, $condition_param2)
    {

        $this->db->select($columns);
        $this->db->from($table);

        $this->db->where(['kode_cabang' => $this->session->userdata('cabang')]);

        $add_condition = $this->uri->segment(1) == 'Marketing' ? ' <= ' : '';

        if ($this->uri->segment(1) === 'Reservasi') {
        } else {
            if ($type == 1) {
                $date = date('Y-m-d');
                $this->db->where([$condition_param1 . $add_condition => $date]);
            } else {
                $this->db->where([$condition_param1 . ' >=' => $month, $condition_param1 . ' <= ' => $year]);
            }
        }

        if (!empty($param2)) {
            $this->db->where([$condition_param2 => $param2]);
        }

        return $this->db->count_all_results();
    }
}
