<?php  
    $serverName = "10.1.1.5"; 
    $uid = "sa";   
    $pwd = "Rshaji123!@";  
    $databaseName = "HOSPITAL"; 

    $connectionInfo = array( "UID" => $uid,
                            "PWD"  => $pwd,
                            "Database" => $databaseName);

    $conn = sqlsrv_connect( $serverName, $connectionInfo);

    $servername2 = "127.0.0.1";
    $username2 = "client";
    $password2 = "TcLZqrr4WSpxG6D6";

    $conn2 = mysqli_connect($servername2, $username2, $password2, "hospital");
?>  