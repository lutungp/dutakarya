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
        $sSearch = isset($_GET['sSearch']) ? $_GET['sSearch'] : "";
        if ($sSearch != "" ){
            $sWhere = " WHERE ";
            $sWhere .= $aColumnsfilter[0]." LIKE '%".$sSearch."%'";
        }

        $tsql = "   SELECT fs_nm_layanan, SUM (kunjunganbaru) AS kunjunganbaru, SUM (kunjunganlama) AS kunjunganlama, SUM(kunjunganbaru) + SUM(kunjunganlama) AS total
                    FROM (
                        SELECT
                            fs_nm_layanan, 
                            CASE WHEN fn_status = 1 THEN Jumlah ELSE 0 END AS kunjunganlama,
                            CASE WHEN fn_status = 0 THEN Jumlah ELSE 0 END AS kunjunganbaru
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
            "sEcho" => isset($_GET['sEcho']) ? intval($_GET['sEcho']) : 0,
            // "iTotalRecords" => count($rResult),
            "iTotalDisplayRecords" => $iTotal,
            "aaData" => array()
        );
        
        while ( $aRow = sqlsrv_fetch_array( $rResult ) )
        {
            $row = array();
            for ( $i=0 ; $i<count($aColumns) ; $i++ )
            {
                $row[] = $aRow[ $aColumns[$i] ];
            }
            $output['aaData'][] = $row;
        }

        return $output;
    }

    public function getLayanan()
    {
        $sql = "SELECT FS_KD_LAYANAN, FS_NM_LAYANAN FROM TA_LAYANAN WHERE FB_AKTIF = '1' AND FS_KD_INSTALASI = '01'";
        $result = sqlsrv_query($this->conn, $sql);
        $layananArr = array();
        while ($row = sqlsrv_fetch_array($result)) {
            array_push($layananArr, $row["FS_NM_LAYANAN"]);
        }

        return $layananArr;
    }

    public function getChartData($tglawal, $tglakhir, $instalasi, $layanan, $groupjaminan, $tipejaminan)
    {
        $aColumns = array('fs_nm_layanan', 'kunjunganbaru', 'kunjunganlama', 'total');
        $tgla = date("Y-m-01", strtotime($tglakhir));
        $tsql = "   SELECT fs_nm_layanan, SUM (kunjunganbaru) AS kunjunganbaru, SUM (kunjunganlama) AS kunjunganlama, SUM(kunjunganbaru) + SUM(kunjunganlama) AS total
                    FROM (
                        SELECT
                            fs_nm_layanan, 
                            CASE WHEN fn_status = 1 THEN Jumlah ELSE 0 END AS kunjunganlama,
                            CASE WHEN fn_status = 0 THEN Jumlah ELSE 0 END AS kunjunganbaru
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
                    ) AS laporan GROUP BY fs_nm_layanan";
                
        $result = sqlsrv_query($this->conn, $tsql);
        $chart = [];
        while ( $aRow = sqlsrv_fetch_array( $result ) )
        {
            $row = array();
            for ( $i=0 ; $i<count($aColumns) ; $i++ )
            {
                $row[] = $aRow[ $aColumns[$i] ];
            }
            $chart[] = $row;
        }

        return $chart;
    }

}