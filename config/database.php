<?php  
    $serverName = "10.1.10.1"; 
    $uid = "sa";   
    $pwd = "Rshaji123!@";  
    $databaseName = "HOSPITAL_LAT"; 

    $connectionInfo = array( "UID" => $uid,
                            "PWD"  => $pwd,
                            "Database" => $databaseName);

    $conn = sqlsrv_connect( $serverName, $connectionInfo);

    $servername2 = "localhost";
    $username2 = "root";
    $password2 = "";

    $conn2 = mysqli_connect($servername2, $username2, $password2, "hospital");
?>  