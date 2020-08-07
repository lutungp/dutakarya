<?php

class M_barangrusak
{
    public $conn = false;
    public $conn2 = false;
    public $config = false;
    
    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->config = $config;
    }

    public function getBarangRusak()
    {
        $sql = "SELECT 
                    t_barangrusak.barangrusak_id,
                    t_barangrusak.barangrusak_tgl,
                    t_barangrusak.barangrusak_no,
                    t_barangrusak.m_rekanan_id,
                    m_rekanan.rekanan_nama,
                    m_rekanan.rekanan_alamat
                FROM t_barangrusak
                INNER JOIN m_rekanan ON m_rekanan.rekanan_id = t_barangrusak.m_rekanan_id
                WHERE t_barangrusak.barangrusak_aktif = 'Y' ";
        $qbarangrusak = $this->conn2->query($sql);
        $result = array();
        while ($val = $qbarangrusak->fetch_array()) {
            $result[] = array(
                'barangrusak_id' => $val['barangrusak_id'],
                'barangrusak_tgl' => $val['barangrusak_tgl'],
                'barangrusak_no' => $val['barangrusak_no'],
                'm_rekanan_id' => $val['m_rekanan_id'],
                'rekanan_nama' => $val['rekanan_nama'],
                'rekanan_alamat' => $val['rekanan_alamat'],
            );
        }

        return $result;
    }

    public function getBarangSatkonv()
    {
        $sql = " SELECT  
                    barang_id,
                    barang_kode,
                    barang_nama,
                    m_satuan_id,
                    m_satuan.satuan_nama
                FROM m_barang
                LEFT JOIN m_satuan ON m_satuan.satuan_id = m_barang.m_satuan_id
                WHERE m_barang.barang_aktif = 'Y'
                AND barang_id IN (
                    SELECT m_brg_id FROM m_bahanbrg WHERE bahanbrg_ketika = 'rusak' AND bahanbrg_aktif = 'Y'
                ) ";

        $qbarang = $this->conn2->query($sql);
        $barang = [];
        while ($result = $qbarang->fetch_array(MYSQLI_ASSOC)) {
            array_push($barang, $result);
        }

        return $barang;
    }

    public function getHargaKontrak($barang_id, $tanggal, $m_rekanan_id)
    {
        $sql = "SELECT * FROM (
                    SELECT
                        ROW_NUMBER() OVER ( PARTITION BY t_hargakontrak_detail.m_barang_id ORDER BY hargakontrakdet_tgl DESC, hargakontrakdet_id DESC ) AS rnumber,
                        hargakontrakdet_harga,
                        hargakontrakdet_ppn
                    FROM
                        t_hargakontrak_detail 
                    WHERE
                        m_barang_id = $barang_id AND hargakontrakdet_tgl <= '$tanggal' AND m_rekanan_id = $m_rekanan_id
                        AND t_hargakontrak_detail.hargakontrakdet_brgrusak = 'Y'
                        AND t_hargakontrak_detail.hargakontrakdet_aktif = 'Y'
                    ) AS kontrak 
                WHERE kontrak.rnumber <= 1";
                
        $qbarang = $this->conn2->query($sql);
        $result['hargakontrakdet_harga'] = 0;
        $result['hargakontrakdet_ppn'] = 'N';
        if ($qbarang) {
            $rbarang = $qbarang->fetch_object();
            $result['hargakontrakdet_harga'] = isset($rbarang->hargakontrakdet_harga) ? $rbarang->hargakontrakdet_harga : 0;
            $result['hargakontrakdet_ppn'] = isset($rbarang->hargakontrakdet_ppn) ? $rbarang->hargakontrakdet_ppn : 'N';
        }
        return $result;
    }

    public function getBahanBaku($barang_id, $ketika = 'rusak')
    {
        $sql = "SELECT 
                    bahanbrg_id, 
                    m_brg_id, 
                    m_barang_id, 
                    m_barang.barang_nama, 
                    m_barang.m_satuan_id,
                    m_satuan.satuan_nama,
                    bahanbrg_qty, 
                    bahanbrg_ketika
                FROM m_bahanbrg
                LEFT JOIN m_barang ON m_barang.barang_id = m_bahanbrg.m_barang_id
                LEFT JOIN m_satuan ON m_satuan.satuan_id = m_barang.m_satuan_id
                WHERE m_bahanbrg.bahanbrg_aktif = 'Y'
                AND m_bahanbrg.m_brg_id = $barang_id AND bahanbrg_ketika = '$ketika'";
        
        $qbarang = $this->conn2->query($sql);
        $rbarang = array();
        while ($val = $qbarang->fetch_array()) {
            $rbarang[] = array(
                'bahanbrg_id' => $val['bahanbrg_id'],
                'm_barang_id' => $val['m_barang_id'],
                'm_barang_nama' => $val['barang_nama'],
                'm_satuan_id' => $val['m_satuan_id'],
                'satuan_nama' => $val['satuan_nama'],
                'bahanbrg_qty' => $val['bahanbrg_qty'],
                'bahanbrg_ketika' => $val['bahanbrg_ketika'],
            );
        }

        return $rbarang;
    }


    public function getStokAkhir($barang_id, $tanggal)
    {
        $sql = "SELECT * FROM (
                    SELECT
                        ROW_NUMBER() OVER ( PARTITION BY t_barangtrans.m_barang_id ORDER BY barangtrans_tgl DESC, barangtrans_id DESC ) AS rnumber,
                        barangtrans_akhir 
                    FROM
                        t_barangtrans 
                    WHERE
                        m_barang_id = $barang_id AND barangtrans_tgl <= '$tanggal'
                    ) AS barangtrans 
                WHERE barangtrans.rnumber <= 1 ";
                
        $qbarang = $this->conn2->query($sql);
        $barangtrans_akhir = 0;
        if ($qbarang) {
            $rbarang = $qbarang->fetch_object();
            $barangtrans_akhir = isset($rbarang->barangtrans_akhir) ? $rbarang->barangtrans_akhir : 0;
        }
        return $barangtrans_akhir;
    }

    public function getBarangRusakData($barangrusak_id)
    {
        $sql = " SELECT 
                    t_barangrusak.barangrusak_id,
                    t_barangrusak.barangrusak_tgl,
                    t_barangrusak.barangrusak_no,
                    t_barangrusak.m_rekanan_id,
                    m_rekanan.rekanan_nama,
                    m_rekanan.rekanan_alamat,
                    t_barangrusak.t_penagihan_id,
                    t_barangrusak.t_penagihan_no
                FROM t_barangrusak
                INNER JOIN m_rekanan ON m_rekanan.rekanan_id = t_barangrusak.m_rekanan_id
                WHERE t_barangrusak.barangrusak_aktif = 'Y' 
                AND t_barangrusak.barangrusak_id = $barangrusak_id";
        $qbarangrusak = $this->conn2->query($sql);
        $rbarangrusak = $qbarangrusak->fetch_object();
        return $rbarangrusak;
    }

    public function getBarangRusakDataDetail($barangrusak_id)
    {
        $sql = " SELECT
                    t_barangrusakdet.barangrusakdet_id,
                    t_barangrusakdet.t_barangrusak_id,
                    t_barangrusakdet.m_barang_id,
                    m_barang.barang_nama,
                    t_barangrusakdet.m_satuan_id,
                    m_satuan.satuan_nama,
                    t_barangrusakdet.barangrusakdet_qty,
                    t_barangrusakdet.barangrusakdet_harga,
                    t_barangrusakdet.barangrusakdet_subtotal,
                    t_barangrusakdet.barangrusakdet_alasan
                FROM t_barangrusakdet
                INNER JOIN m_barang ON m_barang.barang_id = t_barangrusakdet.m_barang_id
                INNER JOIN m_satuan ON m_satuan.satuan_id = t_barangrusakdet.m_satuan_id ";

        if ($barangrusak_id > 0) {
            $sql .= " WHERE barangrusakdet_aktif = 'Y' AND t_barangrusak_id = $barangrusak_id ";
        }
        
        $qbarangrusak = $this->conn2->query($sql);
        $rbarangrusak = array();
        while ($val = $qbarangrusak->fetch_array()) {
            $rbarangrusak[] = array(
                'barangrusakdet_id' => $val['barangrusakdet_id'],
                't_barangrusak_id' => $val['t_barangrusak_id'],
                'm_barang_id' => $val['m_barang_id'],
                'barang_nama' => $val['barang_nama'],
                'm_satuan_id' => $val['m_satuan_id'],
                'satuan_nama' => $val['satuan_nama'],
                'barangrusakdet_qty' => $val['barangrusakdet_qty'],
                'barangrusakdet_qtyold' => $val['barangrusakdet_qty'],
                'barangrusakdet_harga' => $val['barangrusakdet_harga'],
                'barangrusakdet_subtotal' => $val['barangrusakdet_subtotal'],
                'barangrusakdet_alasan' => $val['barangrusakdet_alasan'],
            );
        }

        return $rbarangrusak;
    }

}