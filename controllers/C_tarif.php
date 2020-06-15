<?php
include '../config/database.php';
include '../config/config.php';
include '../config/helpers.php';

class C_tarif
{
    public $conn = false;
    public $conn2 = false;

    public function __construct($conn, $conn2)
    {
        $this->conn = $conn;
        $this->conn2 = $conn2;
    }

    public function listTarif()
    {
        templateAdmin($this->conn2, '../views/v_listtarif.php', $data = "", $active1 = "MASTER", $active2 = "TARIF");
    }

    public function getListTarif()
    {
        $aColumns = array( 'FS_KD_SMF', 'FS_NM_SMF' );
	
        /* Indexed column (used for fast and accurate table cardinality) */
        $sIndexColumn = "tarif_id";
        $sTable = "m_tarif";
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
            $sOrder = "ORDER BY ";
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
            $sOrder = " ORDER BY tarif_created_date ASC ";
        }

        /* 
        * Filtering
        * NOTE this does not match the built-in DataTables filtering which does it
        * word by word on any field. It's possible to do here, but concerned about efficiency
        * on very large tables, and MySQL's regex functionality is very limited
        */
        $sWhere = "";
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

        /*
            * SQL queries
            * Get data to display
            */
            $sQuery = "
            SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
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

    public function createRow()
    {
        templateAdmin('../views/v_formtarif.php', $data = "", $active1 = "MASTER", $active2 = "TARIF");
    }
}

$tarif = new C_tarif($conn, $conn2, $config);
$action = isset($_GET["action"]) ? $_GET["action"] : "";
switch ($action) {
    case 'getListTarif':
        $tarif->getListTarif();
        break;
    
    case 'createrow':
        $tarif->createRow();
        break;

    case 'editrow':
        
        break;
    
    default:
        $tarif->listTarif();
        break;
}