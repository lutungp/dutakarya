<?php

class M_kontrakrekanan
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

    public function getHargaKontrak()
    {
        $sql = "SELECT 
                    t_hargakontrak.hargakontrak_id,
                    t_hargakontrak.hargakontrak_no,
                    t_hargakontrak.hargakontrak_tgl,
                    t_hargakontrak.m_rekanan_id,
                    m_rekanan.rekanan_nama,
                    m_user.user_nama,
                    t_hargakontrak.hargakontrak_created_by,
                    t_hargakontrak.hargakontrak_created_date
                FROM t_hargakontrak
                INNER JOIN m_rekanan ON m_rekanan.rekanan_id = t_hargakontrak.m_rekanan_id
                INNER JOIN m_user ON m_user.user_id = t_hargakontrak.hargakontrak_created_by
                WHERE t_hargakontrak.hargakontrak_aktif = 'Y' ";

        $qhargakontrak = $this->conn2->query($sql);
        
        $result = array();
        while ($val = $qhargakontrak->fetch_array()) {
            $result[] = array(
                'hargakontrak_id' => $val['hargakontrak_id'],
                'hargakontrak_no' => $val['hargakontrak_no'],
                'hargakontrak_tgl' => $val['hargakontrak_tgl'],
                'm_rekanan_id' => $val['m_rekanan_id'],
                'user_nama' => $val['user_nama'],
                'hargakontrak_created_date' => $val['hargakontrak_created_date']
            );
        }

        return $result;
    }

    public function getKontrakData($hargakontrak_id)
    {
        $sql = "SELECT
                    hargakontrak_id,
                    hargakontrak_no,
                    hargakontrak_tgl,
                    m_rekanan_id,
                    m_rekanan.rekanan_nama,
                    hargakontrak_aktif
                FROM t_hargakontrak
                INNER JOIN m_rekanan ON m_rekanan.rekanan_id = t_hargakontrak.m_rekanan_id
                WHERE t_hargakontrak.hargakontrak_id = $hargakontrak_id ";
        $qkontrak = $this->conn2->query($sql);
        $rkontrak = $qkontrak->fetch_object();
        return $rkontrak;
    }

    public function getKontrakDataDetail($hargakontrak_id)
    {
        $sql = " SELECT
                    t_hargakontrak_detail.hargakontrakdet_id,
                    t_hargakontrak_detail.t_hargakontrak_id,
                    t_hargakontrak_detail.hargakontrakdet_tgl,
                    t_hargakontrak_detail.m_rekanan_id,
                    t_hargakontrak_detail.m_barang_id,
                    m_barang.m_satuan_id AS m_barangsatuan_id,
                    m_satuan.satuan_nama AS m_satuan_id,
                    m_satuan_konversi.satkonv_nilai,
                    t_hargakontrak_detail.hargakontrakdet_ppn,
                    t_hargakontrak_detail.hargakontrakdet_harga
                FROM t_hargakontrak_detail
                LEFT JOIN m_barang ON m_barang.barang_id = t_hargakontrak_detail.m_barang_id
                LEFT JOIN m_satuan ON m_satuan.satuan_id = t_hargakontrak_detail.m_satuan_id
                LEFT JOIN m_satuan_konversi ON m_satuan_konversi.m_satuan_id = t_hargakontrak_detail.m_satuan_id AND m_satuan_konversi.m_barang_id = t_hargakontrak_detail.m_barang_id 
                WHERE t_hargakontrak_detail.hargakontrakdet_aktif = 'Y' AND t_hargakontrak_detail.t_hargakontrak_id = $hargakontrak_id ";

        $qkontrak = $this->conn2->query($sql);
        $rkontrak = array();
        while ($val = $qkontrak->fetch_array()) {
        $rkontrak[] = array(
            'hargakontrakdet_id' => $val['hargakontrakdet_id'],
            't_hargakontrak_id' => $val['t_hargakontrak_id'],
            'hargakontrakdet_tgl' => $val['hargakontrakdet_tgl'],
            'm_rekanan_id' => $val['m_rekanan_id'],
            'm_barang_id' => $val['m_barang_id'],
            'm_barangsatuan_id' => $val['m_barangsatuan_id'],
            'm_satuan_id' => $val['m_satuan_id'],
            'satkonv_nilai' => $val['satkonv_nilai'],
            'hargakontrakdet_ppn' => $val['hargakontrakdet_ppn'],
            'hargakontrakdet_harga' => $val['hargakontrakdet_harga'],
        );
        }

        return $rkontrak;
    }

}