<?php

include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_infopengiriman.php';

class C_infopengiriman
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->model = new M_infopengiriman($conn, $conn2, $config);
    }

    public function getPengiriman($tanggal, $data)
    {
        $rekananArr = isset($data['rekananArr']) ? implode(',', $data['rekananArr']) : '';
        $barangArr = isset($data['barangArr']) ? implode(',', $data['barangArr']) : '';
        $tanggal = $tanggal <> '' ? $tanggal : explode(' - ', $data['tanggal']);
        $data = $this->model->getPengiriman($tanggal, $rekananArr, $barangArr);
        echo json_encode($data);
    }

    public function getBarang()
    {
        $data = $this->model->getBarang();
        echo json_encode($data);
    }

    public function getRekanan()
    {
        $data = $this->model->getRekanan();
        echo json_encode($data);
    }

    public function getJadwalKirim($data)
    {
        if (!isset($data['barang_id'])) {
            echo json_encode([]);
            exit();
        }
        if ($data['rit'] == 1) {
            $rekananArr = isset($data['rekananArr2']) ? implode(',', $data['rekananArr2']) : '';
        } else {
            $rekananArr = isset($data['rekananArr3']) ? implode(',', $data['rekananArr3']) : '';
        }
        $rekanan    = $this->model->getRekanan($rekananArr);
        $rpengiriman = $this->model->getJadwalKirim($rekananArr, $data['barang_id'], $data['hari'], $data['bulan'], $data['tahun'], $data['rit']);
        $rjadwal = $this->model->getJadwal($rekananArr, $data['barang_id'], $data['hari'], $data['bulan'], $data['tahun'], $data['rit']);
        
        $data = array();
        foreach ($rekanan as $key => $value) {
            $rekanan_id = $value['rekanan_id'];
            $pengiriman = array_filter($rpengiriman, function($element) use ($rekanan_id) {
                if ($element['m_rekanan_id'] == $rekanan_id) {
                    return $element;
                }
            });

            $isi = array_filter($rjadwal, function($element) use ($rekanan_id) {
                if ($element['m_rekanan_id'] == $rekanan_id) {
                    return $element;
                }
            });
            
            $DOminggu1 = array_filter($pengiriman, function($element) {
                if ($element['minggu'] == 1) {
                    return $element;
                }
            });
            $qty1 = 0;
            $retur1 = 0;
            foreach ($DOminggu1 as $k => $v) {
                $qty1 = $qty1+$v['pengirimandet_qty'];
                $retur1 = $retur1+$v['t_returdet_qty'];
            }
            /* minggu 2 */
            $DOminggu2 = array_filter($pengiriman, function($element) {
                if ($element['minggu'] == 2) {
                    return $element;
                }
            });
            $qty2 = 0;
            $retur2 = 0;
            foreach ($DOminggu2 as $k => $v) {
                $qty2 = $qty2+$v['pengirimandet_qty'];
                $retur2 = $retur2+$v['t_returdet_qty'];
            }
            /* minggu 3 */
            $DOminggu3 = array_filter($pengiriman, function($element) {
                if ($element['minggu'] == 3) {
                    return $element;
                }
            });
            $qty3 = 0;
            $retur3 = 0;
            foreach ($DOminggu3 as $k => $v) {
                $qty3 = $qty3+$v['pengirimandet_qty'];
                $retur3 = $retur3+$v['t_returdet_qty'];
            }
            /* minggu 4 */
            $DOminggu4 = array_filter($pengiriman, function($element) {
                if ($element['minggu'] == 4) {
                    return $element;
                }
            });
            $qty4 = 0;
            $retur4 = 0;
            foreach ($DOminggu4 as $k => $v) {
                $qty4 = $qty4+$v['pengirimandet_qty'];
                $retur4 = $retur4+$v['t_returdet_qty'];
            }
            /* minggu 5 */
            $DOminggu5 = array_filter($pengiriman, function($element) {
                if ($element['minggu'] == 5) {
                    return $element;
                }
            });
            $qty5 = 0;
            $retur5 = 0;
            foreach ($DOminggu5 as $k => $v) {
                $qty5 = $qty5+$v['pengirimandet_qty'];
                $retur5 = $retur5+$v['t_returdet_qty'];
            }
            $minggu1 = isset($isi[0]['minggu1']) ? $isi[0]['minggu1'] : 0;
            $minggu2 = isset($isi[0]['minggu2']) ? $isi[0]['minggu2'] : 0;
            $minggu3 = isset($isi[0]['minggu3']) ? $isi[0]['minggu3'] : 0;
            $minggu4 = isset($isi[0]['minggu4']) ? $isi[0]['minggu4'] : 0;
            $minggu5 = isset($isi[0]['minggu5']) ? $isi[0]['minggu5'] : 0;
            $data[] = array(
                'rekanan_nama' => $value['rekanan_nama'],
                'rekanan_alamat' => str_replace("<br />", "\\n", $value['rekanan_alamat']),
                'm_barang_id' => isset($DOminggu1[0]['m_barang_id']) ? $DOminggu1[0]['m_barang_id'] : 0,
                'DO_minggu1' => $minggu1,
                'Isi_minggu1' => $qty1-$retur1,
                'Kosong_minggu1' => $qty1-$retur1,
                'Sisa_minggu1' => $qty1-($qty1-$retur1),
                'DO_minggu2' => $minggu2,
                'Isi_minggu2' => $qty2-$retur2,
                'Kosong_minggu2' => $qty2-$retur2,
                'Sisa_minggu2' => $qty2-($qty2-$retur2),
                'DO_minggu3' => $minggu3,
                'Isi_minggu3' => $qty3-$retur3,
                'Kosong_minggu3' => $qty3-$retur3,
                'Sisa_minggu3' => $qty3-($qty3-$retur3),
                'DO_minggu4' => $minggu4,
                'Isi_minggu4' => $qty4-$retur4,
                'Kosong_minggu4' => $qty4-$retur4,
                'Sisa_minggu4' => $qty4-($qty4-$retur4),
                'DO_minggu5' => $minggu5,
                'Isi_minggu5' => $qty5-$retur5,
                'Kosong_minggu5' => $qty5-$retur5,
                'Sisa_minggu5' => $qty5-($qty5-$retur5),
            );
        }

        echo json_encode($data);
    }

}

$infopengiriman = new C_infopengiriman($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : "";
switch ($action) {
    case 'getpengiriman':
        $tanggal = isset($data['tanggal']) ? $data['tanggal'] : '';
        $infopengiriman->getPengiriman($tanggal, $_POST);
        break;

    case 'getbarang':
        $infopengiriman->getBarang();
        break;
    
    case 'getrekanan':
        $infopengiriman->getRekanan();
        break;
    
    case 'getjadwalkirim':
        $infopengiriman->getJadwalKirim($_POST);
        break;

    default:
        templateAdmin($conn2, '../views/info/v_infopengiriman.php', NULL, "INFO", "INFO PENGIRIMAN");
        break;
}