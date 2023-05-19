<?php
function OpenCon() {
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "Nilrutu99";
    $db = "booker";
    $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $db) 
            or die("Connect failed: \n". mysqli_connect_error());
    return $conn;
}

function CloseCon($conn) {
    mysqli_close($conn);
}
?>