<?php

class M_lapkunjunganpasien
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

    public function getPerLayanan($tglawal, $tglakhir, $instalasi, $layanan, $groupjaminan, $tipejaminan, $dataget)
    {
        $aColumns = array('fs_nm_layanan', 'kunjunganbaru', 'kunjunganlama', 'total');

        $tgla = date("Y-m-01", strtotime($tglakhir));
        $aColumnsfilter = array('fs_nm_layanan');
        $sWhere   = "";
        if ( $_GET['sSearch'] != "" ){
            $sWhere = " WHERE ";
            $sWhere .= $aColumnsfilter[0]." LIKE '%".$_GET['sSearch']."%'";
        }

        $tsql = "   SELECT fs_nm_layanan, SUM (kunjunganbaru) AS kunjunganbaru, SUM (kunjunganlama) AS kunjunganlama, SUM(kunjunganbaru) + SUM(kunjunganlama) AS total
                    FROM (
                        SELECT
                            fs_nm_layanan, 
                            CASE WHEN fn_status = 1 THEN Jumlah ELSE 0 END AS kunjunganbaru,
                            CASE WHEN fn_status = 0 THEN Jumlah ELSE 0 END AS kunjunganlama
                        FROM (
                            SELECT
                                cc.fs_nm_layanan,
                                CASE WHEN bb.fn_kunjunganke > 1 THEN 1 ELSE 0 END AS fn_status,
                                COUNT (DISTINCT aa.fs_kd_reg + aa.fs_kd_layanan) Jumlah
                            FROM dbo.ifc_gabung_tdk2 ('$tgla', '$tglakhir') aa
                            LEFT JOIN ta_registrasi bb ON aa.fs_kd_reg = bb.fs_kd_reg
                            LEFT JOIN ta_layanan cc ON aa.fs_kd_layanan = cc.fs_kd_layanan
                            LEFT JOIN ta_tipe_jaminan dd ON bb.fs_kd_tipe_jaminan = dd.fs_kd_tipe_jaminan
                            LEFT JOIN ta_jaminan gg ON dd.fs_kd_jaminan = gg.fs_kd_jaminan
                            LEFT JOIN ta_grup_jaminan ee ON gg.fs_kd_grup_jaminan = ee.fs_kd_grup_jaminan
                            LEFT JOIN ta_instalasi dd1 ON cc.fs_kd_instalasi = dd1.fs_kd_instalasi
                            WHERE fd_tgl_trs BETWEEN '$tglawal' AND '$tglakhir'
                            AND bb.fb_external = 0
                            AND aa.FD_TGL_VOID = '3000-01-01'
                            AND (cc.FS_KD_INSTALASI = '01')
                            GROUP BY cc.fs_nm_layanan, CASE WHEN bb.fn_kunjunganke > 1 THEN 1 ELSE 0 END
                        ) AS laporan
                    ) AS laporan $sWhere GROUP BY fs_nm_layanan";
        
        $aColumns2 = [];
        foreach ($aColumns as $key => $val) {
            array_push($aColumns2, $val);
        }

        $sOrder="";
        if ( isset( $dataget['iSortCol_0'] ) )
        {
            $sOrder = "ORDER BY ";
            for ($i=0 ; $i<intval( $dataget['iSortingCols'] ) ; $i++){
                if ($dataget[ 'bSortable_'.intval($dataget['iSortCol_'.$i]) ] == "true"){
                    $sOrder .= $aColumns[ intval( $dataget['iSortCol_'.$i] ) ] . " " . $dataget['sSortDir_'.$i] .", ";
                }
            }

            $sOrder = substr_replace( $sOrder, "", -2 );
            if ( $sOrder == "ORDER BY " )
            {
                $sOrder = "";
            }
        }
        /*
            * SQL queries
            * Get data to display
            */
        $sQuery = " SELECT ".str_replace(" , ", " ", implode(", ", $aColumns2))." FROM ($tsql) AS lapkunjungan";
        
        $rResult = sqlsrv_query($this->conn, $sQuery);
        $qCount = "SELECT COUNT(*) AS count FROM ($tsql) AS lapkunjungan";
        $rResultTotal = sqlsrv_query($this->conn, $qCount);
        $aResultTotal = sqlsrv_fetch_object($rResultTotal);
        $iTotal = $aResultTotal->count;
        /*
        * Output
        */
        // $rResult = sqlsrv_fetch_array($rResult);
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            // "iTotalRecords" => count($rResult),
            "iTotalDisplayRecords" => $iTotal,
            "aaData" => array()
        );
        
        while ( $aRow = sqlsrv_fetch_array( $rResult ) )
        {
            $row = array();
            for ( $i=0 ; $i<count($aColumns) ; $i++ )
            {
                /* General output */
                $row[] = $aRow[ $aColumns[$i] ];
            }
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
        // return sqlsrv_fetch_array($result);
    }

}