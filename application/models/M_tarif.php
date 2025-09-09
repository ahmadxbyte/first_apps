<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_tarif extends CI_Model
{
    private $table = 'm_tarif';
    private $order = ['m.id', 'm.kode_tarif', 'm.nama', 'm.kategori', 'tj.kode_cabang', 'tj.jasa_rs', 'tj.jasa_dokter', 'tj.jasa_pelayanan', 'tj.jasa_poli', 'tj.jenis_bayar', 'm.kelas'];
    private $kolom = ['m.id', 'm.kode_tarif', 'm.nama', 'm.kategori', 'tj.kode_cabang', 'tj.jasa_rs', 'tj.jasa_dokter', 'tj.jasa_pelayanan', 'tj.jasa_poli', 'tj.jenis_bayar', 'm.kelas'];
    private $search = ['m.id', 'm.kode_tarif', 'm.nama', 'tj.jasa_rs', 'tj.jasa_dokter', 'tj.jasa_pelayanan', 'tj.jasa_poli', 'm.jenis_bayar'];

    public function __construct()
    {
        parent::__construct();
        setlocale(LC_ALL, 'id_ID.utf8');
        date_default_timezone_set('Asia/Jakarta');
    }

    private function _get_datatables_query($param)
    {
        $this->db->query("SET SESSION sql_mode = (SELECT REPLACE(REPLACE(REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY,', ''), ',ONLY_FULL_GROUP_BY', ''), 'ONLY_FULL_GROUP_BY', ''))");

        $this->db->select($this->kolom);
        $this->db->from($this->table . ' AS m');

        if (!empty($param)) {
            if ($param == 1) {
                $this->db->join('tarif_jasa tj', 'tj.kode_tarif = m.kode_tarif');
            } else {
                $this->db->join('tarif_paket tj', 'tj.kode_tarif = m.kode_tarif');
            }
        }

        $this->db->group_by('tj.kode_tarif, tj.jenis_bayar');

        $this->db->where("m.jenis", $param);
        $this->db->where("tj.kode_cabang", $this->session->userdata("cabang"));
        $this->db->where("tj.hapus < ", 1);

        $this->db->order_by('id', 'asc');


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

    public function get_datatables($param)
    {
        $this->_get_datatables_query($param);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($param)
    {
        $this->_get_datatables_query($param);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($param)
    {
        $this->db->query("SET SESSION sql_mode = (SELECT REPLACE(REPLACE(REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY,', ''), ',ONLY_FULL_GROUP_BY', ''), 'ONLY_FULL_GROUP_BY', ''))");

        $this->db->select($this->kolom);
        $this->db->from($this->table . ' AS m');

        if (!empty($param)) {
            if ($param == 1) {
                $this->db->join('tarif_jasa tj', 'tj.kode_tarif = m.kode_tarif');
            } else {
                $this->db->join('tarif_paket tj', 'tj.kode_tarif = m.kode_tarif');
            }
        }

        $this->db->group_by('tj.kode_tarif, tj.jenis_bayar');

        $this->db->where("m.jenis", $param);
        $this->db->where("tj.kode_cabang", $this->session->userdata("cabang"));
        $this->db->where("tj.hapus < ", 1);

        $this->db->order_by('id', 'asc');

        return $this->db->count_all_results();
    }
}
