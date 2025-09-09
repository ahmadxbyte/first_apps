<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_riwayat_stok extends CI_Model
{
    private $table = 'barang';
    private $order = ['bs.id', 'b.kode_barang', 'b.image', 'b.nama', 'b.hna', 'b.hpp', 'b.harga_jual', 'b.nilai_persediaan', 'bs.kode_gudang', 'g.nama AS nama_gudang', 'bs.akhir', 'b.stok_min', 'b.stok_max'];
    private $kolom = ['bs.id', 'b.kode_barang', 'b.image', 'b.nama', 'b.hna', 'b.hpp', 'b.harga_jual', 'b.nilai_persediaan', 'bs.kode_gudang', 'g.nama AS nama_gudang', 'bs.akhir', 'b.stok_min', 'b.stok_max'];
    private $search = ['bs.id', 'b.kode_barang', 'b.image', 'b.nama', 'b.hna', 'b.hpp', 'b.harga_jual', 'b.nilai_persediaan', 'bs.kode_gudang', 'g.nama', 'bs.akhir', 'b.stok_min', 'b.stok_max'];

    public function __construct()
    {
        parent::__construct();
        setlocale(LC_ALL, 'id_ID.utf8');
        date_default_timezone_set('Asia/Jakarta');
    }

    private function _get_datatables_query($gudang)
    {
        $this->db->query("SET SESSION sql_mode = (SELECT REPLACE(REPLACE(REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY,', ''), ',ONLY_FULL_GROUP_BY', ''), 'ONLY_FULL_GROUP_BY', ''))");

        $this->db->select($this->kolom);
        $this->db->from($this->table . ' AS b');
        $this->db->join('m_satuan s', 'b.kode_satuan = s.kode_satuan');
        $this->db->join('m_kategori k', 'b.kode_kategori = k.kode_kategori');
        $this->db->join('barang_jenis bj', 'b.kode_barang = bj.kode_barang');
        $this->db->join('m_jenis j', 'bj.kode_jenis = j.kode_jenis');
        $this->db->join('barang_stok bs', 'b.kode_barang = bs.kode_barang');
        $this->db->join('m_gudang g', 'bs.kode_gudang = g.kode_gudang');
        $this->db->order_by('bs.kode_gudang, b.nama', 'ASC');
        $this->db->group_by('bs.kode_barang, bs.kode_gudang');

        $this->db->where("bs.kode_cabang", $this->session->userdata("cabang"));

        if (!empty($gudang)) {
            $this->db->where('bs.kode_gudang', $gudang);
        }

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

    public function get_datatables($gudang)
    {
        $this->_get_datatables_query($gudang);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($gudang)
    {
        $this->_get_datatables_query($gudang);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($gudang)
    {
        $this->db->query("SET SESSION sql_mode = (SELECT REPLACE(REPLACE(REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY,', ''), ',ONLY_FULL_GROUP_BY', ''), 'ONLY_FULL_GROUP_BY', ''))");

        $this->db->select($this->kolom);
        $this->db->from($this->table . ' AS b');
        $this->db->join('m_satuan s', 'b.kode_satuan = s.kode_satuan');
        $this->db->join('m_kategori k', 'b.kode_kategori = k.kode_kategori');
        $this->db->join('barang_jenis bj', 'b.kode_barang = bj.kode_barang');
        $this->db->join('m_jenis j', 'bj.kode_jenis = j.kode_jenis');
        $this->db->join('barang_stok bs', 'b.kode_barang = bs.kode_barang');
        $this->db->join('m_gudang g', 'bs.kode_gudang = g.kode_gudang');
        $this->db->order_by('bs.kode_gudang, b.nama', 'ASC');
        $this->db->group_by('bs.kode_barang, bs.kode_gudang');

        if (!empty($gudang)) {
            $this->db->where('bs.kode_gudang', $gudang);
        }

        return $this->db->count_all_results();
    }
}
