<?php

class M_stockopname
{

    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->config = $config;
    }

    public function getStockOpname()
    {
        $sql = " SELECT 
                    stockopname_id,
                    stockopname_no,
                    stockopname_tgl,
                    m_user.user_nama,
                    stockopname_keterangan,
                    t_stockopname.stockopname_created_date
                FROM t_stockopname
                LEFT JOIN m_user ON m_user.user_id = t_stockopname.stockopname_created_by
                WHERE stockopname_aktif = 'Y' ORDER BY t_stockopname.stockopname_created_date DESC ";
        $qstockopname = $this->conn2->query($sql);
        $result = array();
        while ($val = $qstockopname->fetch_array()) {
            $result[] = array(
                'stockopname_id' => $val['stockopname_id'],
                'stockopname_no' => $val['stockopname_no'],
                'stockopname_tgl' => $val['stockopname_tgl'],
                'user_nama' => $val['user_nama'],
                'stockopname_keterangan' => $val['stockopname_keterangan'],
                'stockopname_created_date' => $val['stockopname_created_date'],
            );
        }

        return $result;
    }

    public function getStockOpnameData($stockopname_id)
    {
        $sql = " SELECT 
                    stockopname_id,
                    stockopname_no,
                    stockopname_tgl,
                    stockopname_keterangan
                FROM t_stockopname
                WHERE stockopname_aktif = 'Y' 
                AND stockopname_id = $stockopname_id ";
        $qstockopname = $this->conn2->query($sql);
        $rstockopname = $qstockopname->fetch_object();
        return $rstockopname;
    }

    public function getStockOpnameDataDetail($stockopname_id)
    {
        $sql = " SELECT
                t_stockopnamedet.stockopnamedet_id,
                t_stockopnamedet.t_stockopname_id,
                t_stockopnamedet.m_barang_id,
                m_barang.barang_nama,
                t_stockopnamedet.m_satuan_id,
                m_satuan.satuan_nama,
                m_satuan_konversi.satkonv_nilai,
                t_stockopnamedet.t_barangtrans_akhir,
                t_stockopnamedet.stockopnamedet_qty 
            FROM t_stockopnamedet
            LEFT JOIN m_barang ON m_barang.barang_id = t_stockopnamedet.m_barang_id
            LEFT JOIN m_satuan ON m_satuan.satuan_id = t_stockopnamedet.m_satuan_id
            LEFT JOIN m_satuan_konversi ON m_satuan_konversi.m_satuan_id = t_stockopnamedet.m_satuan_id AND m_satuan_konversi.m_barang_id = t_stockopnamedet.m_barang_id ";
        if ($stockopname_id > 0) {
            $sql .= " WHERE stockopnamedet_aktif = 'Y' AND t_stockopname_id = $stockopname_id ";
        }

            $qstockopname = $this->conn2->query($sql);
            $rstockopname = array();
            while ($val = $qstockopname->fetch_array()) {
            $rstockopname[] = array(
                'stockopnamedet_id' => $val['stockopnamedet_id'],
                't_stockopname_id' => $val['t_stockopname_id'],
                'm_barang_id' => $val['m_barang_id'],
                'barang_nama' => $val['barang_nama'],
                'm_satuan_id' => $val['m_satuan_id'],
                'satuan_nama' => $val['satuan_nama'],
                't_barangtrans_akhir' => $val['t_barangtrans_akhir'],
                'stockopnamedet_qty' => $val['stockopnamedet_qty'],
                'satkonv_nilai' => $val['satkonv_nilai']
            );
        }

        return $rstockopname;
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
                WHERE m_barang.barang_aktif = 'Y' ";

        $qbarang = $this->conn2->query($sql);
        $data['barang'] = [];
        while ($result = $qbarang->fetch_array(MYSQLI_ASSOC)) {
            array_push($data['barang'], $result);
        }

        $sql = " SELECT  
                    satkonv_id,
                    m_barang_id,
                    m_satuan.satuan_nama,
                    m_satuan_id,
                    satkonv_nilai
                FROM m_satuan_konversi
                LEFT JOIN m_satuan ON m_satuan.satuan_id = m_satuan_konversi.m_satuan_id
                WHERE m_satuan_konversi.satkonv_aktif = 'Y' ";

        $qsatuankonv = $this->conn2->query($sql);
        $data['satuan_konversi'] = [];
        while ($result = $qsatuankonv->fetch_array(MYSQLI_ASSOC)) {
            array_push($data['satuan_konversi'], $result);
        }

        $sql = " SELECT  
                    satuan_id,
                    satuan_nama
                FROM m_satuan
                WHERE m_satuan.satuan_aktif = 'Y' ";

        $qsatuankonv = $this->conn2->query($sql);
        $data['satuan'] = [];
        while ($result = $qsatuankonv->fetch_array(MYSQLI_ASSOC)) {
            array_push($data['satuan'], $result);
        }

        return $data;
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

    public function getStockOpnameDataDetail2($stockopnamedet_id)
    {
        $sql = " SELECT
                    t_stockopnamedet.stockopnamedet_id,
                    t_stockopnamedet.t_stockopname_id,
                    t_stockopnamedet.m_barang_id,
                    m_barang.m_satuan_id AS satuanutama,
                    t_stockopnamedet.m_satuan_id,
                    COALESCE(m_satuan_konversi.satkonv_nilai, 1) AS satkonv_nilai,
                    t_stockopnamedet.stockopnamedet_qty
                FROM t_stockopnamedet 
                LEFT JOIN m_barang ON m_barang.barang_id = t_stockopnamedet.m_barang_id
                LEFT JOIN m_satuan_konversi ON m_satuan_konversi.m_satuan_id = t_stockopnamedet.m_satuan_id AND m_satuan_konversi.m_barang_id = t_stockopnamedet.m_barang_id 
                WHERE t_stockopnamedet.stockopnamedet_aktif = 'Y' ";
        if ($stockopnamedet_id == '') {
            $sql .= "AND t_stockopname_id IN (" . $stockopnamedet_id . ") ";
        } else {
            $sql .= "AND stockopnamedet_id IN (" . $stockopnamedet_id . ") ";
        }
        
        $qstockopname = $this->conn2->query($sql);
        $rstockopname = array();
        while ($val = $qstockopname->fetch_array()) {
            $rstockopname[] = array(
                'stockopnamedet_id' => $val['stockopnamedet_id'],
                't_stockopname_id' => $val['t_stockopname_id'],
                'm_barang_id' => $val['m_barang_id'],
                'm_satuan_id' => $val['m_satuan_id'],
                'satuanutama' => $val['satuanutama'],
                'stockopnamedet_qty' => $val['stockopnamedet_qty'],
                'satkonv_nilai' => $val['satkonv_nilai']
            );
        }

        return $rstockopname;
    }
    
}