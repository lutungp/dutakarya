<?php

class M_penagihan
{

    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->config = $config;
    }

    public function getPenagihan()
    {
        $sql = " SELECT 
                    penagihan_id,
                    penagihan_no,
                    penagihan_tgl,
                    m_rekanan_id,
                    m_rekanan.rekanan_nama
                FROM t_penagihan
                LEFT JOIN m_rekanan ON m_rekanan.rekanan_id = t_penagihan.m_rekanan_id
                WHERE penagihan_aktif = 'Y'";
        $qpenagihan = $this->conn2->query($sql);
        $result = array();
        while ($val = $qpenagihan->fetch_array()) {
            $result[] = array(
                'penagihan_id' => $val['penagihan_id'],
                'penagihan_no' => $val['penagihan_no'],
                'penagihan_tgl' => $val['penagihan_tgl'],
                'm_rekanan_id' => $val['m_rekanan_id'],
                'rekanan_nama' => $val['rekanan_nama']
            );
        }

        return $result;
    }

    public function getPengiriman($m_rekanan_id, $penagihan_tgl)
    {
        $sql = "SELECT
                    t_pengiriman.pengiriman_id,
                    t_pengiriman.pengiriman_no,
                    t_pengiriman.pengiriman_tgl,
                    t_pengiriman_detail.pengirimandet_id,
                    t_pengiriman.m_rekanan_id,
                    t_pengiriman_detail.m_barang_id,
                    m_barang.barang_nama,
                    m_barang.m_satuan_id AS m_barangsatuan_id,
                    satuanutama.satuan_nama AS m_barangsatuan_nama,
                    t_pengiriman_detail.m_satuan_id,
                    COALESCE(m_satuan_konversi.satkonv_nilai, 1) AS satkonv_nilai,
                    t_pengiriman_detail.pengirimandet_qty,
                    t_pengiriman_detail.pengirimandet_subtotal,
                    t_pengiriman_detail.pengirimandet_potongan,
                    t_pengiriman_detail.pengirimandet_total,
                    t_pengiriman_detail.pengirimandet_total,
                    t_pengiriman_detail.t_returdet_qty
                FROM t_pengiriman_detail
                JOIN t_pengiriman ON t_pengiriman.pengiriman_id = t_pengiriman_detail.t_pengiriman_id
                LEFT JOIN m_satuan_konversi ON m_satuan_konversi.m_satuan_id = t_pengiriman_detail.m_satuan_id AND m_satuan_konversi.m_barang_id = t_pengiriman_detail.m_barang_id
                JOIN m_barang ON m_barang.barang_id = t_pengiriman_detail.m_barang_id
                JOIN m_satuan satuanutama ON satuanutama.satuan_id = m_barang.m_satuan_id
                WHERE t_pengiriman.pengiriman_aktif = 'Y' AND t_pengiriman_detail.pengirimandet_aktif = 'Y' AND t_pengiriman.pengiriman_tgl <= '$penagihan_tgl'
                AND t_pengiriman.m_rekanan_id = $m_rekanan_id";
        $qkirim = $this->conn2->query($sql);
        $rkirim = array();
        while ($val = $qkirim->fetch_array()) {
            $rkirim[] = array(
                'penagihandet_id' => 0,
                't_penagihan_id' => 0,
                'pengiriman_id' => $val['pengiriman_id'],
                'pengiriman_no' => $val['pengiriman_no'],
                'pengiriman_tgl' => $val['pengiriman_tgl'],
                'pengirimandet_id' => $val['pengirimandet_id'],
                'm_rekanan_id' => $val['m_rekanan_id'],
                'm_barang_id' => $val['m_barang_id'],
                'barang_nama' => $val['barang_nama'],
                'm_barangsatuan_id' => $val['m_barangsatuan_id'],
                'm_barangsatuan_nama' => $val['m_barangsatuan_nama'],
                'm_satuan_id' => $val['m_satuan_id'],
                'satkonv_nilai' => $val['satkonv_nilai'],
                'pengirimandet_qty' => $val['pengirimandet_qty'],
                'pengirimandet_qtyreal' => $val['pengirimandet_qty'] * $val['satkonv_nilai'],
                'pengirimandet_subtotal' => $val['pengirimandet_subtotal'],
                'pengirimandet_potongan' => $val['pengirimandet_potongan'],
                'pengirimandet_total' => $val['pengirimandet_total'],
                't_returdet_qty' => $val['t_returdet_qty']
            );
        }

        return $rkirim;
    }
}