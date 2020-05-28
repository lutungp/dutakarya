<?php
include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';
include '../models/M_antrianbooking.php';

class C_antrianbooking
{
    public $conn = false;
    public $conn2 = false;
    public $config = false;
    public $model = false;

    public function __construct($conn, $conn2, $config)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
        $this->config = $config;
        $this->model = new M_antrianbooking($conn, $conn2, $config);
    }

    public function getAntrian($data)
    {
        $dataantrian = $this->model->getAntrianBooking($data["pasien_norm"]);
        template("./../views/v_nomerurut.php", $dataantrian);
    }

    public function listAntrian($data)
    {
        template("./../views/v_listantrian.php", $data);
    }

    public function getListAntrian()
    {
        $aColumns = array( 'bookinghosp_id', 'bookinghosp_created_date', 'pasien_norm', 'pasien_nama', 'bookinghosp_tanggal', 'bookinghosp_jammulai', 'bookinghosp_jamselesai', 
                            'bookinghosp_status', 'bookinghosp_urutan', 'pasien_email', 'pasien_telp', 'pegawai_nama' );
	
        /* Indexed column (used for fast and accurate table cardinality) */
        $sIndexColumn = "bookinghosp_id";
        $sTable = "t_booking_hospital";
        /* 
        * Paging
        */
        $sLimit = "";
        if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
        {
            $sLimit = "LIMIT ".mysqli_real_escape_string($this->conn2, $_GET['iDisplayStart'] ).", ".
                mysqli_real_escape_string($this->conn2, $_GET['iDisplayLength'] );
        }

        /*
        * Ordering
        */
        if ( isset( $_GET['iSortCol_0'] ) )
        {
            $sOrder = "ORDER BY  bookinghosp_tanggal ASC, bookinghosp_urutan ASC, ";
            for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
            {
                if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
                {
                    $sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
                        ".mysqli_real_escape_string($this->conn2, $_GET['sSortDir_'.$i] ) .", ";
                }
            }
            
            $sOrder = substr_replace( $sOrder, "", -2 );
            if ( $sOrder == "ORDER BY" )
            {
                $sOrder = "";
            }
        } else {
            $sOrder = " ORDER BY bookinghosp_tanggal ASC ";
        }

        /* 
        * Filtering
        * NOTE this does not match the built-in DataTables filtering which does it
        * word by word on any field. It's possible to do here, but concerned about efficiency
        * on very large tables, and MySQL's regex functionality is very limited
        */
        $sWhere = "WHERE bookinghosp_aktif = 'Y' ";
        if($_GET["datefilter"] <> 'Invalid Date') {
            $sWhere .= " AND DATE(bookinghosp_tanggal) = '" . $_GET["datefilter"] . "'";
        }

        if ( $_GET['sSearch'] != "" )
        {
            $sWhere = "WHERE (";
            for ( $i=0 ; $i<count($aColumns) ; $i++ )
            {
                $sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($this->conn2, $_GET['sSearch'] )."%' OR ";
            }
            $sWhere = substr_replace( $sWhere, "", -3 );
            $sWhere .= ')';
        }

        /* Individual column filtering */
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
            {
                if ( $sWhere == "" )
                {
                    $sWhere = "WHERE ";
                }
                else
                {
                    $sWhere .= " AND ";
                }
                $sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($this->conn2, $_GET['sSearch_'.$i])."%' ";
            }
        }

        $aColumns2 = [];
        foreach ($aColumns as $key => $val) {
            if($val == 'bookinghosp_created_date' || $val == 'bookinghosp_tanggal'){
                array_push($aColumns2, 'DATE_FORMAT('.$val.', "%d-%m-%Y %H:%i:%s") AS ' . $val);
            } else {
                array_push($aColumns2, $val);
            }
        }
        /*
            * SQL queries
            * Get data to display
            */
            $sQuery = "
            SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns2))."
            FROM   $sTable
            $sWhere
            $sOrder
            $sLimit
        ";
        
        $rResult = mysqli_query( $this->conn2, $sQuery ) or die(mysqli_error());


        /* Data set length after filtering */
        $sQuery = "
            SELECT FOUND_ROWS()
        ";
        $rResultFilterTotal = mysqli_query( $this->conn2, $sQuery ) or die(mysqli_error());
        $aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
        $iFilteredTotal = $aResultFilterTotal[0];

        /* Total data set length */
        $sQuery = "
            SELECT COUNT(".$sIndexColumn.")
            FROM   $sTable
        ";
        $rResultTotal = mysqli_query( $this->conn2, $sQuery ) or die(mysqli_error());
        $aResultTotal = mysqli_fetch_array($rResultTotal);
        $iTotal = $aResultTotal[0];


        /*
        * Output
        */
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );

        while ( $aRow = mysqli_fetch_array( $rResult ) )
        {
            $row = array();
            for ( $i=0 ; $i<count($aColumns) ; $i++ )
            {
                if ( $aColumns[$i] == "bookinghosp_tanggal" )
                {
                    /* Special output formatting for 'version' column */
                    $row[] = date("d-m-Y", strtotime($aRow[ $aColumns[$i] ]));
                }
                else if ( $aColumns[$i] != ' ' )
                {
                    /* General output */
                    $row[] = $aRow[ $aColumns[$i] ];
                }
            }
            $output['aaData'][] = $row;
        }

        echo json_encode( $output );
        
    }

    public function listdaftar()
    {
        templateAdmin($this->conn2, '../views/v_listdaftar.php', $data = "", $active1 = "TRANSAKSI", $active2 = "DAFTAR ANTRIAN");
    }

    public function deletedaftar($bookinghosp_id)
    {
        $sql = "UPDATE t_booking_hospital SET bookinghosp_aktif = 'N' WHERE bookinghosp_id = $bookinghosp_id ";
        $action = $this->conn2->query($sql);
        if($action){
            return "200";
        } else {
            return "202";
        }
    }

    public function sudahbayar($bookinghosp_id)
    {
        $tglbayar = date("Y-m-d H:i:s");
        $sql = "UPDATE t_booking_hospital SET bookinghosp_status = 'SUDAH BAYAR', bookinghosp_tglbayar = '" . $tglbayar . "' WHERE bookinghosp_id = $bookinghosp_id ";
        $action = $this->conn2->query($sql);
        if($action){
            return "200";
        } else {
            return "202";
        }
    }

    public function exceldaftar($tanggal)
    {
        $tanggal = date("Y-m-d", strtotime($tanggal));
        $sql = " SELECT
                    bookinghosp_id, bookinghosp_created_date, pasien_norm, pasien_nama, bookinghosp_tanggal, bookinghosp_jammulai, 
                    bookinghosp_jamselesai, bookinghosp_status, bookinghosp_urutan, pasien_email, pasien_telp, pegawai_nama
                 FROM t_booking_hospital
                 WHERE t_booking_hospital.bookinghosp_aktif = 'Y' 
                 AND t_booking_hospital.bookinghosp_tanggal = '$tanggal' ";
        
        $exceldaftar = $this->conn2->query($sql);

        $html = "<html>
        <head>
            <title>Export Data Antrian - telemedicine.rshaji-jakarta.com</title>
        </head>
        <body>
            <style type='text/css'>
            html {
                margin: 10px;
            }
            body{
                font-family: sans-serif;
            }
            table{
                margin: 20px auto;
                border-collapse: collapse;
            }
            
            table th,
            table td{
                border: 1px solid #3c3c3c;
                padding: 3px 8px;
            }
            a{
                background: blue;
                color: #fff;
                padding: 8px 10px;
                text-decoration: none;
                border-radius: 2px;
            }
            .nomor {
                text-align: center;
                width: 50px;
            }
        
            .col-biru {
                background-color: #d0e1fc;
            }
            </style>
         
            <?php 
                header('Content-type: application/vnd-ms-excel');
                header('Content-Disposition: attachment; filename=lapstock.xls');
            ?>
         
            <table border='1'>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>No. Rekam Medik</th>
                        <th>Nama Pasien</th>
                        <th>Tanggal Pesan</th>
                        <th>Jam Mulai</th>
                        <th>Jam Selesai</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>";
        foreach ($exceldaftar as $key => $val) {
            $html .= "<tr>
                        <td>" . date("d-m-Y H:i:s", strtotime($val["bookinghosp_created_date"])) . "</td>
                        <td>" . $val["pasien_norm"] . "</td>
                        <td>" . $val["pasien_nama"] . "</td>
                        <td>" . date("d-m-Y H:i:s", strtotime($val["bookinghosp_tanggal"])) . "</td>
                        <td>" . $val["bookinghosp_jammulai"] . "</td>
                        <td>" . $val["bookinghosp_jamselesai"] . "</td>
                        <td>" . $val["bookinghosp_status"] . "</td>
                      </tr>";    
        }
        $html .= "</tbody>
            </table>
        </body>
        </html>";
        
        $filename = "../documents/temp/exceldaftar.xls";
        file_put_contents($filename, $html);
        $url = $this->config["base_url"] . "/documents/temp/exceldaftar.xls";

        echo $url;
    }
}

$antrianbooking = new C_antrianbooking($conn, $conn2, $config);
$data = $_GET;
$action = isset($data["action"]) ? $data["action"] : '';
switch ($action) {
    case 'list_antrian':
        $antrianbooking->listAntrian($data);
        break;
    
    case 'getListAntrian':
        $antrianbooking->getListAntrian();
        break;
    
    case 'listdaftar':
        $antrianbooking->listdaftar();
        break;
    
    case 'delete':
        $action = $antrianbooking->deletedaftar($_POST["bookinghosp_id"]);
        echo $action;
        break;
    
    case 'sudahbayar':
        $action = $antrianbooking->sudahbayar($_POST["bookinghosp_id"]);
        echo $action;
        break;
    
    case 'exceldaftar';
        $result = $antrianbooking->exceldaftar($_POST["date1"]);

        break;

    default:
        $antrianbooking->getAntrian($data);
        break;
}
