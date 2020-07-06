<?php

class M_hargabrg
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

    public function getBarangHna()
    {
        $sql = " SELECT 
                    baranghna_id,
                    baranghna_no,
                    baranghna_tglawal,
                    baranghna_tglakhir,
                    m_user.user_nama,
                    t_baranghna.baranghna_created_date
                FROM t_baranghna
                LEFT JOIN m_user ON m_user.user_id = t_baranghna.baranghna_created_by
                WHERE t_baranghna.baranghna_aktif = 'Y' ";
        $qbaranghna = $this->conn2->query($sql);
        
        $result = array();
        while ($val = $qbaranghna->fetch_array()) {
            $result[] = array(
                'baranghna_id' => $val['baranghna_id'],
                'baranghna_no' => $val['baranghna_no'],
                'baranghna_tglawal' => $val['baranghna_tglawal'],
                'baranghna_tglakhir' => $val['baranghna_tglakhir'],
                'user_nama' => $val['user_nama'],
                'baranghna_created_date' => $val['baranghna_created_date']
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
                WHERE m_barang.barang_aktif = 'Y' ";

        $qbarang = $this->conn2->query($sql);
        $data['barang'] = [];
        while ($result = $qbarang->fetch_array(MYSQLI_ASSOC)) {
            array_push($data['barang'], $result);
        }

        $data['satuan_konversi'] = [];

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

    public function getBaranghnaData($baranghna_id)
    {
        $sql = " SELECT 
                baranghna_id,
                baranghna_no,
                baranghna_tglawal
            FROM t_baranghna
            WHERE baranghna_aktif = 'Y' AND baranghna_id = $baranghna_id";
        $qbaranghna = $this->conn2->query($sql);
        $rbaranghna = $qbaranghna->fetch_object();
        return $rbaranghna;
    }

    public function getBaranghnaDataDetail($baranghna_id, $tgl)
    {
        $sql = "SELECT
                    t_baranghna_detail.baranghnadet_id,
                    t_baranghna_detail.t_baranghna_id,
                    t_baranghna_detail.m_barang_id,
                    t_baranghna_detail.baranghnadet_tglawal,
                    t_baranghna_detail.baranghnadet_harga,
                    COALESCE ( baranghna_last.baranghnadet_harga, 0 ) AS baranghnadet_last 
                FROM
                    t_baranghna_detail
                    LEFT JOIN (
                    SELECT
                        * 
                    FROM
                        (
                        SELECT
                            ROW_NUMBER() OVER ( PARTITION BY m_barang_id ORDER BY t_baranghna_detail.baranghnadet_tglawal DESC, t_baranghna_detail.baranghnadet_created_date DESC ) AS rnumber,
                            m_barang_id,
                            baranghnadet_harga,
                            t_baranghna_detail.baranghnadet_tglawal 
                        FROM
                            t_baranghna_detail
                            INNER JOIN t_baranghna ON t_baranghna.baranghna_id = t_baranghna_detail.t_baranghna_id 
                        WHERE
                            t_baranghna_detail.baranghnadet_aktif = 'Y' 
                            AND t_baranghna_detail.baranghnadet_tglawal < '$tgl'
                            AND t_baranghna.baranghna_aktif = 'Y' 
                        ) AS last 
                    WHERE
                    last.rnumber = 1 
                    ) AS baranghna_last ON baranghna_last.m_barang_id = t_baranghna_detail.m_barang_id ";
        $sql .= " WHERE baranghnadet_aktif = 'Y' AND t_baranghna_id = $baranghna_id ";
        $qbaranghna = $this->conn2->query($sql);
        $rbaranghna = array();
        while ($val = $qbaranghna->fetch_array()) {
            $rbaranghna[] = array(
                'baranghnadet_id' => $val['baranghnadet_id'],
                't_baranghna_id' => $val['t_baranghna_id'],
                'm_barang_id' => $val['m_barang_id'],
                'baranghnadet_tglawal' => $val['baranghnadet_tglawal'],
                'baranghnadet_last' => $val['baranghnadet_last'],
                'baranghnadet_harga' => $val['baranghnadet_harga'],
            );
        }

        return $rbaranghna;
    }

    public function getLastHNA($barang_id, $baranghna_tglawal)
    {
        $sql = " SELECT
                    m_barang_id,
                    baranghnadet_harga
                FROM t_baranghna_detail
                INNER JOIN t_baranghna ON t_baranghna.baranghna_id = t_baranghna_detail.t_baranghna_id
                WHERE t_baranghna_detail.baranghnadet_aktif = 'Y' AND t_baranghna.baranghna_aktif = 'Y'
                AND (t_baranghna_detail.baranghnadet_tglakhir <= '$baranghna_tglawal' OR t_baranghna_detail.baranghnadet_tglakhir IS NULL) 
                AND m_barang_id = $barang_id
                ORDER BY t_baranghna_detail.baranghnadet_tglawal DESC,  t_baranghna_detail.baranghnadet_created_date DESC 
                LIMIT 1 ";
        $qbarang = $this->conn2->query($sql);
        $rbarang = $qbarang->fetch_object();
        return $rbarang;
    }

}