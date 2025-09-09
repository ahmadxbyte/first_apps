<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_multiprice extends CI_Model
{
    private $table = 'multiprice_tindakan';
    private $order = ['m.id', 'm.kode_multiprice', 'm.kode_tindakan', 'p.keterangan AS polix', 'm.kode_penjamin', 'm.kelas', 'm.klinik', 'm.dokter', 'm.pelayanan', 'm.poli', 'jb.keterangan', 't.keterangan AS tindakan'];
    private $kolom = ['m.id', 'm.kode_multiprice', 'm.kode_tindakan', 'p.keterangan AS polix', 'm.kode_penjamin', 'm.kelas', 'm.klinik', 'm.dokter', 'm.pelayanan', 'm.poli', 'jb.keterangan', 't.keterangan AS tindakan'];
    private $search = ['m.id', 'm.kode_multiprice', 'm.kode_tindakan', 'p.keterangan', 'm.kode_penjamin', 'm.kelas', 'm.klinik', 'm.dokter', 'm.pelayanan', 'm.poli', 'jb.keterangan', 't.keterangan'];

    public function __construct()
    {
        parent::__construct();
        setlocale(LC_ALL, 'id_ID.utf8');
        date_default_timezone_set('Asia/Jakarta');
    }

    private function _get_datatables_query($param1, $param2, $param3)
    {
        $this->db->query("SET SESSION sql_mode = (SELECT REPLACE(REPLACE(REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY,', ''), ',ONLY_FULL_GROUP_BY', ''), 'ONLY_FULL_GROUP_BY', ''))");

        $this->db->select($this->kolom);
        $this->db->from($this->table . ' AS m');
        $this->db->join('m_jenis_bayar jb', 'jb.kode_jenis_bayar = m.kode_penjamin');
        $this->db->join('m_tindakan t', 't.kode_tindakan = m.kode_tindakan');
        $this->db->join('m_poli p', 'p.kode_poli = m.kode_poli');

        if (!empty($param1) || $param1 !== '') {
            $this->db->where("m.kode_penjamin", $param1);
        }

        if (!empty($param2) || $param2 !== '') {
            $this->db->where("m.kelas", $param2);
        }

        if (!empty($param3) || $param3 !== '') {
            $this->db->where("m.kode_poli", $param3);
        }

        $this->db->order_by('id', 'desc');

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

    public function get_datatables($param1, $param2, $param3)
    {
        $this->_get_datatables_query($param1, $param2, $param3);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($param1, $param2, $param3)
    {
        $this->_get_datatables_query($param1, $param2, $param3);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($param1, $param2, $param3)
    {
        $this->db->query("SET SESSION sql_mode = (SELECT REPLACE(REPLACE(REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY,', ''), ',ONLY_FULL_GROUP_BY', ''), 'ONLY_FULL_GROUP_BY', ''))");

        $this->db->select($this->kolom);
        $this->db->from($this->table . ' AS m');
        $this->db->join('m_jenis_bayar jb', 'jb.kode_jenis_bayar = m.kode_penjamin');
        $this->db->join('m_tindakan t', 't.kode_tindakan = m.kode_tindakan');
        $this->db->join('m_poli p', 'p.kode_poli = m.kode_poli');

        if (!empty($param1) || $param1 !== '') {
            $this->db->where("m.kode_penjamin", $param1);
        }

        if (!empty($param2) || $param2 !== '') {
            $this->db->where("m.kelas", $param2);
        }

        if (!empty($param3) || $param3 !== '') {
            $this->db->where("m.kode_poli", $param3);
        }

        $this->db->order_by('id', 'desc');

        return $this->db->count_all_results();
    }
}
