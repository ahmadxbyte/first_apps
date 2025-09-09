<?php
class M_auth extends CI_Model
{
    // fungsi untuk ambil jumlah baris berdasarkan lemparan tertentu
    function jumRow($table, $where)
    {
        return $this->db->get_where($table, $where)->num_rows();
    }

    // ambil data 1 baris berdasarkan lemparan tertentu
    function getRow($table, $where)
    {
        return $this->db->get_where($table, $where)->row();
    }

    // tambahkan ke table yang sudah ditentukan
    function insert($table, $isi)
    {
        return $this->db->insert($table, $isi);
    }

    // update ke table yang sudah ditentukan berdasarkan lemparan tertentu
    function update($table, $isi, $where)
    {
        return $this->db->update($table, $isi, $where);
    }
}
