<?php
    include 'db_connection.php';
    session_start();
    $conn = OpenCon();
    echo "Connected Successfully";/*
    $address = "5252 Fight St., Scranton, PA 18510";
    $roomnum = 103;
    $price = 238;
    $maxoccup = 4;
    $distance = 2312;
    $insert = mysqli_query($conn, "INSERT INTO rooms (address_id, room_num, base_price, max_occup) VALUES ('$address', '$roomnum', '$price', '$maxoccup')") or die('query failed:\n' . mysqli_error($conn));
    $room_id = mysqli_insert_id($conn);
    $ama_list = mysqli_query($conn, "SELECT offer_id, ama_id FROM ama_offered WHERE address_id = '$address'") or die('query failed:\n' . mysqli_error($conn));
    if (mysqli_num_rows($ama_list) > 0) {
        $sql = "";
        while($ch = mysqli_fetch_assoc($ama_list)) {
            $offer_id = $ch['offer_id'];
            $sql .= "INSERT INTO ama_to_room (offer_id, room_id, distance) VALUES ('$offer_id', '$room_id', NULLIF('$distance', ''));";
            $distance = $distance + ($distance % 3);
        }
        $addto = mysqli_multi_query($conn, $sql) or die('query failed:\n' . mysqli_error($conn));
    }*/
    session_unset();
    session_destroy();
    CloseCon($conn);
?>