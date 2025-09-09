<?php
defined("BASEPATH") or exit("No direct script access allowed");

class M_barang extends CI_Model
{
    protected $table = "barang";

    protected $columns = [
        "b.id",
        "b.kode_barang",
        "b.nama",
        "b.kode_satuan",
        "b.kode_satuan2",
        "b.kode_satuan3",
        "(SELECT keterangan FROM m_satuan WHERE kode_satuan = b.kode_satuan) AS satuan1",
        "IF(b.kode_satuan2 IS NOT NULL, (SELECT keterangan FROM m_satuan WHERE kode_satuan = b.kode_satuan2), '') AS satuan2",
        "IF(b.kode_satuan3 IS NOT NULL, (SELECT keterangan FROM m_satuan WHERE kode_satuan = b.kode_satuan3), '') AS satuan3",
        "IF(b.kode_satuan2 IS NOT NULL, b.qty_satuan2, '') AS qty_satuan2",
        "IF(b.kode_satuan3 IS NOT NULL, b.qty_satuan3, '') AS qty_satuan3",
        "b.kode_kategori",
        "b.hna",
        "b.hpp",
        "b.harga_jual",
        "b.nilai_persediaan",
        "b.stok_min",
        "b.stok_max"
    ];

    protected $search_key = [
        "b.kode_barang",
        "b.nama",
        "b.hna",
        "b.hpp",
        "b.harga_jual",
        "b.nilai_persediaan",
        "b.stok_min",
        "b.stok_max"
    ];

    protected $order = ["b.id" => "asc"];

    public function __construct()
    {
        parent::__construct();
        setlocale(LC_ALL, "id_ID.utf8");
        date_default_timezone_set("Asia/Jakarta");
    }

    private function _get_datatables_query($param)
    {
        $this->db->query("SET SESSION sql_mode = ''");

        $this->db->select($this->columns);
        $this->db->from($this->table . " AS b");

        $this->db->join("barang_cabang bc", "bc.kode_barang = b.kode_barang");
        $this->db->join("m_kategori k", "k.kode_kategori = b.kode_kategori", "LEFT");
        $this->db->join("barang_jenis bj", "bj.kode_barang = b.kode_barang");
        $this->db->join("m_jenis j", "j.kode_jenis = bj.kode_jenis", "LEFT");

        $this->db->where("bc.kode_cabang", $this->session->userdata("cabang"));
        $this->db->where("bc.hapus < ", 1);

        if (!empty($param)) {
            $this->db->where("b.kode_kategori", $param);
        }

        $this->db->group_by("b.kode_barang");

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

    public function get_datatables($param)
    {
        $this->_get_datatables_query($param);
        if ($_POST["length"] != -1) {
            $this->db->limit($_POST["length"], $_POST["start"]);
        }
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
        $this->db->query("SET SESSION sql_mode = ''");

        $this->db->select($this->columns);
        $this->db->from($this->table . " AS b");

        $this->db->join("barang_cabang bc", "bc.kode_barang = b.kode_barang");
        $this->db->join("m_kategori k", "k.kode_kategori = b.kode_kategori", "LEFT");
        $this->db->join("barang_jenis bj", "bj.kode_barang = b.kode_barang");
        $this->db->join("m_jenis j", "j.kode_jenis = bj.kode_jenis", "LEFT");

        $this->db->where("bc.kode_cabang", $this->session->userdata("cabang"));
        $this->db->where("bc.hapus < ", 1);

        if (!empty($param)) {
            $this->db->where("b.kode_kategori", $param);
        }

        $this->db->group_by("b.kode_barang");

        return $this->db->count_all_results();
    }
}
