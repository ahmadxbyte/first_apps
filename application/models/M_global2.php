<?php
class M_global2 extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->migrate = $this->load->database('migrate', TRUE);
    }

    // fungsi ambil semua baris
    function getResult($table)
    {
        return $this->migrate->get($table)->result();
    }

    // fungsi ambil data 1 baris berdasarkan lemparan tertentu
    function getData($table, $kondisi)
    {
        return $this->migrate->get_where($table, $kondisi)->row();
    }

    // fungsi ambil data 1 baris berdasarkan lemparan tertentu
    function getDataResult($table, $kondisi)
    {
        return $this->migrate->get_where($table, $kondisi)->result();
    }

    // fungsi cek jumlah data
    function jumDataRow($table, $kondisi)
    {
        return $this->migrate->get_where($table, $kondisi)->num_rows();
    }

    // fungsi cek jumlah data
    function jumDataResult($table)
    {
        return $this->migrate->get($table)->num_rows();
    }

    // fungsi insert data
    function insertData($table, $isi)
    {
        return $this->migrate->insert($table, $isi);
    }

    // fungsi update data berdasarkan lemparan tertentu
    function updateData($table, $isi, $kondisi)
    {
        return $this->migrate->update($table, $isi, $kondisi);
    }

    // fungsi hapus data berdasarkan lemparan tertentu
    function delData($table, $kondisi)
    {
        return $this->migrate->delete($table, $kondisi);
    }

    // fungsi ambil data menggunakan like
    function getDataLike($table, $field1, $field2, $kondisi)
    {
        return $this->migrate->query('SELECT * FROM ' . $table . ' WHERE (' . $field1 . ' LIKE "%' . $kondisi . '%" OR ' . $field2 . ' LIKE "%' . $kondisi . '%")')->row();
    }

    // fungsi ambil data 1 baris berdasarkan lemparan tertentu
    function getWhereIn($table, $kondisi, $array)
    {
        return $this->migrate->where_in($kondisi, $array)->get($table)->result();
    }

    // fungsi track record stok pembelian
    function getReportPembelian($dari, $sampai, $kode_gudang, $kode_barang)
    {
        $cabang = $this->session->userdata('cabang');

        $sintax = $this->migrate->query("SELECT * FROM (
            SELECT h.invoice AS no_trx,
            h.kode_cabang AS cabang,
            CONCAT('Pembelian ~ ', s.nama) AS keterangan,
            CONCAT(d.kode_barang, ' ~ ', b.nama) AS barang,
            d.kode_barang,
            d.qty AS masuk,
            '0' AS keluar,
            d.kode_satuan AS satuan,
            (d.harga - (d.discrp / d.qty)) AS harga,
            h.tgl_beli AS tgl,
            h.jam_beli AS jam,
            h.kode_gudang,
            CONCAT(DATE_FORMAT(h.tgl_beli, '%d/%m/%Y'), ' ~ ', h.jam_beli) AS record_date
            FROM barang_in_header h
            JOIN barang_in_detail d ON h.invoice = d.invoice
            JOIN barang b ON d.kode_barang = b.kode_barang
            JOIN m_supplier s ON h.kode_supplier = s.kode_supplier
            WHERE h.is_valid = 1

            UNION ALL

            SELECT h.invoice AS no_trx,
            h.kode_cabang AS cabang,
            CONCAT('Retur Pembelian ~ ', s.nama) AS keterangan,
            CONCAT(d.kode_barang, ' ~ ', b.nama) AS barang,
            d.kode_barang,
            '0' AS masuk,
            d.qty AS keluar,
            d.kode_satuan AS satuan,
            (d.harga - (d.discrp / d.qty)) AS harga,
            h.tgl_retur AS tgl,
            h.jam_retur AS jam,
            h.kode_gudang,
            CONCAT(DATE_FORMAT(h.tgl_retur, '%d/%m/%Y'), ' ~ ', h.jam_retur) AS record_date
            FROM barang_in_retur_header h
            JOIN barang_in_retur_detail d ON h.invoice = d.invoice
            JOIN barang b ON d.kode_barang = b.kode_barang
            JOIN m_supplier s ON h.kode_supplier = s.kode_supplier
            WHERE h.is_valid = 1
        ) AS m_pembelian
        WHERE kode_gudang = '$kode_gudang' AND cabang = '$cabang' AND tgl >= '$dari' AND tgl <= '$sampai' AND kode_barang = '$kode_barang' ORDER BY tgl, jam ASC")->result();

        return $sintax;
    }

    // fungsi track record stok penjualan
    function getReportPenjualan($dari, $sampai, $kode_gudang)
    {
        $sintax = $this->migrate->query("SELECT * FROM (
            SELECT h.invoice AS no_trx,
            CONCAT('Penjualan ~ ', s.nama) AS keterangan,
            CONCAT(d.kode_barang, ' ~ ', b.nama) AS barang,
            d.qty AS masuk,
            '0' AS keluar,
            (d.harga - (d.discrp / d.qty)) AS harga,
            h.tgl_jual AS tgl,
            h.jam_jual AS jam,
            h.kode_gudang,
            CONCAT(DATE_FORMAT(h.tgl_jual, '%d/%m/%Y'), ' ~ ', h.jam_jual) AS record_date
            FROM barang_out_header h
            JOIN barang_out_detail d ON h.invoice = d.invoice
            JOIN barang b ON d.kode_barang = b.kode_barang
            JOIN member s ON h.kode_member = s.kode_member

            UNION ALL

            SELECT h.invoice AS no_trx,
            CONCAT('Retur Penjualan ~ ', h.invoice_jual) AS keterangan,
            CONCAT(d.kode_barang, ' ~ ', b.nama) AS barang,
            '0' AS masuk,
            d.qty AS keluar,
            (d.harga - (d.discrp / d.qty)) AS harga,
            h.tgl_retur AS tgl,
            h.jam_retur AS jam,
            h.kode_gudang,
            CONCAT(DATE_FORMAT(h.tgl_retur, '%d/%m/%Y'), ' ~ ', h.jam_retur) AS record_date
            FROM barang_out_retur_header h
            JOIN barang_out_retur_detail d ON h.invoice = d.invoice
            JOIN barang b ON d.kode_barang = b.kode_barang
        ) AS m_pembelian
        WHERE kode_gudang = '$kode_gudang' AND tgl >= '$dari' AND tgl <= '$sampai' ORDER BY tgl, jam ASC")->result();

        return $sintax;
    }

    function getDataSampah($bagian = '')
    {
        $cabang = $this->session->userdata('cabang');

        if (!$bagian || $bagian == '' || $bagian == '0') {
            $where = "WHERE (cabang = '" . $cabang . "' OR cabang = '')";
        } else {
            $where = "WHERE (cabang = '" . $cabang . "' OR cabang = '') AND bagian = '" . $bagian . "'";
        }

        $sintak = $this->migrate->query("SELECT * FROM (
            -- master
            SELECT 
            kode_satuan AS id,
            'Master ~ Satuan' AS menu,
            keterangan AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'm_satuan' AS tabel,
            3 AS bagian,
            '' AS cabang
            FROM m_satuan
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_kategori AS id,
            'Master ~ Kategori' AS menu,
            keterangan AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'm_kategori' AS tabel,
            3 AS bagian,
            '' AS cabang
            FROM m_kategori
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_jenis AS id,
            'Master ~ jenis' AS menu,
            keterangan AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'm_jenis' AS tabel,
            3 AS bagian,
            '' AS cabang
            FROM m_jenis
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_supplier AS id,
            'Master ~ supplier' AS menu,
            nama AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'm_supplier' AS tabel,
            3 AS bagian,
            '' AS cabang
            FROM m_supplier
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_bank AS id,
            'Master ~ bank' AS menu,
            keterangan AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'm_bank' AS tabel,
            3 AS bagian,
            '' AS cabang
            FROM m_bank
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_pekerjaan AS id,
            'Master ~ pekerjaan' AS menu,
            keterangan AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'm_pekerjaan' AS tabel,
            3 AS bagian,
            '' AS cabang
            FROM m_pekerjaan
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_agama AS id,
            'Master ~ agama' AS menu,
            keterangan AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'm_agama' AS tabel,
            3 AS bagian,
            '' AS cabang
            FROM m_agama
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_pendidikan AS id,
            'Master ~ pendidikan' AS menu,
            keterangan AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'm_pendidikan' AS tabel,
            3 AS bagian,
            '' AS cabang
            FROM m_pendidikan
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_poli AS id,
            'Master ~ poli' AS menu,
            keterangan AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'm_poli' AS tabel,
            3 AS bagian,
            '' AS cabang
            FROM m_poli
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_kas_bank AS id,
            'Master ~ kas bank' AS menu,
            nama AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'kas_bank' AS tabel,
            3 AS bagian,
            '' AS cabang
            FROM kas_bank
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_pajak AS id,
            'Master ~ pajak' AS menu,
            nama AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'm_pajak' AS tabel,
            3 AS bagian,
            '' AS cabang
            FROM m_pajak
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_akun AS id,
            'Master ~ akun' AS menu,
            nama_akun AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'm_akun' AS tabel,
            3 AS bagian,
            '' AS cabang
            FROM m_akun
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_tipe AS id,
            'Master ~ tipe akun' AS menu,
            keterangan AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'tipe_bank' AS tabel,
            3 AS bagian,
            '' AS cabang
            FROM tipe_bank
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_gudang AS id,
            'Master ~ gudang' AS menu,
            nama AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'm_gudang' AS tabel,
            3 AS bagian,
            '' AS cabang
            FROM m_gudang
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_barang AS id,
            'Master ~ barang' AS menu,
            (SELECT nama FROM barang WHERE kode_barang = barang_cabang.kode_barang) AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'barang_cabang' AS tabel,
            3 AS bagian,
            kode_cabang AS cabang
            FROM barang_cabang
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_barang AS id,
            'Master ~ logistik' AS menu,
            (SELECT nama FROM logistik WHERE kode_logistik = logistik_cabang.kode_barang) AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'logistik_cabang' AS tabel,
            3 AS bagian,
            kode_cabang AS cabang
            FROM logistik_cabang
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_user AS id,
            'Master ~ pengguna' AS menu,
            nama AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'user' AS tabel,
            3 AS bagian,
            '' AS cabang
            FROM user
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_dokter AS id,
            'Master ~ dokter' AS menu,
            nama AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'dokter' AS tabel,
            3 AS bagian,
            '' AS cabang
            FROM dokter
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_perawat AS id,
            'Master ~ perawat' AS menu,
            nama AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'perawat' AS tabel,
            3 AS bagian,
            '' AS cabang
            FROM perawat
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_tarif AS id,
            'Master ~ tarif single' AS menu,
            (SELECT nama FROM m_tarif WHERE kode_tarif = tarif_jasa.kode_tarif) AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'tarif_jasa' AS tabel,
            3 AS bagian,
            kode_cabang AS cabang
            FROM tarif_jasa
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_tarif AS id,
            'Master ~ tarif single' AS menu,
            (SELECT nama FROM m_tarif WHERE kode_tarif = tarif_paket.kode_tarif) AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'tarif_paket' AS tabel,
            3 AS bagian,
            kode_cabang AS cabang
            FROM tarif_paket
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_ruang AS id,
            'Master ~ ruang / bangsal' AS menu,
            keterangan AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'm_ruang' AS tabel,
            3 AS bagian,
            '' AS cabang
            FROM m_ruang
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_prefix AS id,
            'Master ~ Prefix' AS menu,
            nama AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'm_prefix' AS tabel,
            3 AS bagian,
            '' AS cabang
            FROM m_prefix
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_provinsi AS id,
            'Master ~ Wilayah/Provinsi' AS menu,
            provinsi AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'm_provinsi' AS tabel,
            3 AS bagian,
            '' AS cabang
            FROM m_provinsi
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_kabupaten AS id,
            'Master ~ Wilayah/Kabupaten' AS menu,
            kabupaten AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'kabupaten' AS tabel,
            3 AS bagian,
            '' AS cabang
            FROM kabupaten
            WHERE hapus > 0

            UNION ALL

            SELECT 
            kode_kecamatan AS id,
            'Master ~ Wilayah/Kecamatan' AS menu,
            kecamatan AS nama,
            tgl_hapus AS tgl,
            jam_hapus AS jam,
            'kecamatan' AS tabel,
            3 AS bagian,
            '' AS cabang
            FROM kecamatan
            WHERE hapus > 0

            -- end master

        ) AS query_all
        $where
        ORDER BY tgl, jam DESC")->result();

        return $sintak;
    }

    function stokBarang($kode_cabang, $kode_gudang, $kode_barang)
    {
        $sintax = $this->migrate->query(
            "SELECT * FROM (
                -- barang in
                SELECT bd.kode_barang, bd.qty_konversi AS qty_in, 0 AS qty_out
                FROM barang_in_header bh 
                JOIN barang_in_detail bd USING (invoice)
                WHERE bh.kode_cabang = '$kode_cabang' AND bh.kode_gudang = '$kode_gudang' AND bd.kode_barang = '$kode_barang' AND bh.is_valid = 1 AND bh.batal = 0

                UNION ALL

                SELECT bd.kode_barang, bd.qty_konversi AS qty_in, 0 AS qty_out 
                FROM barang_out_retur_header bh 
                JOIN barang_out_retur_detail bd USING (invoice)
                WHERE bh.kode_cabang = '$kode_cabang' AND bh.kode_gudang = '$kode_gudang' AND bd.kode_barang = '$kode_barang' AND bh.is_valid = 1 AND bh.batal = 0

                UNION ALL

                SELECT bd.kode_barang, bd.qty_konversi AS qty_in, 0 AS qty_out 
                FROM mutasi_header bh 
                JOIN mutasi_detail bd USING (invoice)
                WHERE bh.kode_cabang = '$kode_cabang' AND bh.menuju = '$kode_gudang' AND bd.kode_barang = '$kode_barang' AND bh.status = 1

                UNION ALL

                -- barang out
                SELECT bd.kode_barang, 0 AS qty_in, bd.qty_konversi AS qty_out 
                FROM barang_out_header bh 
                JOIN barang_out_detail bd USING (invoice)
                WHERE bh.kode_cabang = '$kode_cabang' AND bh.kode_gudang = '$kode_gudang' AND bd.kode_barang = '$kode_barang' AND bh.status_jual = 1 AND bh.batal = 0

                UNION ALL

                SELECT bd.kode_barang, 0 AS qty_in, bd.qty_konversi AS qty_out 
                FROM barang_in_retur_header bh 
                JOIN barang_in_retur_detail bd USING (invoice)
                WHERE bh.kode_cabang = '$kode_cabang' AND bh.kode_gudang = '$kode_gudang' AND bd.kode_barang = '$kode_barang' AND bh.is_valid = 1 AND bh.batal = 0

                UNION ALL

                SELECT bd.kode_barang, 0 AS qty_in, bd.qty_konversi AS qty_out 
                FROM mutasi_header bh 
                JOIN mutasi_detail bd USING (invoice)
                WHERE bh.kode_cabang = '$kode_cabang' AND bh.dari = '$kode_gudang' AND bd.kode_barang = '$kode_barang' AND bh.status = 1

                -- brang adjustment
                UNION ALL

                SELECT bd.kode_barang, bd.qty_konversi AS qty_in, 0 AS qty_out 
                FROM penyesuaian_header bh 
                JOIN penyesuaian_detail bd USING (invoice)
                WHERE bh.kode_cabang = '$kode_cabang' AND bh.kode_gudang = '$kode_gudang' AND bd.kode_barang = '$kode_barang' AND bh.acc = 1
            ) AS stok_barang"
        );

        return $sintax->result();
    }
}
