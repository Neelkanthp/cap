<?php
include 'db_connection.php';
session_start();
$conn = OpenCon();
if(!isset($_SESSION['email']) || !isset($_SESSION['acctype'])){
    header('location:login.php'); exit;
}
$user_id = mysqli_real_escape_string($conn, $_SESSION['email']);
$acc_type = mysqli_real_escape_string($conn, $_SESSION['acctype']);
$address = mysqli_real_escape_string($conn, $_SESSION['addr']);

if(isset($_POST['submitroom'])){
    $roomnum = mysqli_real_escape_string($conn, $_POST['roomnum']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $maxoccup = mysqli_real_escape_string($conn, $_POST['maxoccup']);

    $get_room = mysqli_query($conn, "SELECT room_num FROM rooms WHERE address_id = '$address' AND room_num = '$roomnum'") or die('query failed:' . mysqli_error($conn));
    
    if ( mysqli_num_rows($get_room) > 0) {
        $message = 'room allready exits';
    } else {
        $insert = mysqli_query($conn, "INSERT INTO rooms (address_id, room_num, base_price, max_occup) VALUES ('$address', '$roomnum', '$price', '$maxoccup')")
                or die('query failed:\n' . mysqli_error($conn));
        $room_id = mysqli_insert_id($conn);
        $ama_list = mysqli_query($conn, "SELECT offer_id, ama_id FROM ama_offered WHERE address_id = '$address'") or die('query failed:\n' . mysqli_error($conn));
        if (mysqli_num_rows($ama_list) > 0) {
            $sql = "";
            while($ch = mysqli_fetch_assoc($ama_list)) {
                if(isset( $_POST[$ch['ama_id']] )) { $dist = mysqli_real_escape_string($conn, $_POST[$ch['ama_id']] ); }
                else { $dist = ""; }
                $offer_id = $ch['offer_id'];
                $sql .= "INSERT INTO ama_to_room (offer_id, room_id, distance) VALUES ('$offer_id', '$room_id', NULLIF('$dist', ''));";
            }
            $insert = mysqli_multi_query($conn, $sql) or die('query failed:\n' . mysqli_error($conn));
        }
        if($insert){
            $message = 'room added successful!';
        }else{
            $message = 'room adding failed!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management</title>

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/manage.css">
</head>
<body>
    <div class="thebuttons">
        <!-- A button to open add room from -->
        <button class="open-button" onclick="openForm('addRoom')">Add Rooms</button>

        <!-- The add room form -->
        <div class="form-popup" id="addRoom">
            <form action="" method="post" enctype="multipart/form-data" class="form-container">
                <h1>Add Room</h1><br>
                <label for="roomnum">Room Number :</label>
                <input type="number" name="roomnum" placeholder="Enter room number" required>
                <br><label for="price">Room's Price :</label>
                <input type="number" name="price" placeholder="Enter room's price" required>
                <br><label for="maxoccup">MAX Occupancy :</label>
                <input type="number" name="maxoccup" placeholder="Enter MAX occupancy limit" required>
                <?php
                    $get_amaoff = mysqli_query($conn, "SELECT ama_id FROM ama_offered WHERE address_id = '$address'") or die('query failed:\n' . mysqli_error($conn));
                    if (mysqli_num_rows($get_amaoff) > 0) {
                        // output data of each row
                        while($row = mysqli_fetch_assoc($get_amaoff)) {
                            echo '<br><label for="'.$row['ama_id'].'">'.$row['ama_id'].' :</label>';
                            echo '<input type="number" name="'.$row["ama_id"].'" placeholder="Enter distance">';
                        }
                    }
                ?>
                <button type="submit" name="submitroom" class="btn">Add Room</button>
                <button type="button" class="btn cancel" onclick="closeForm('addRoom')">Close</button>
            </form>
        </div>
        <script>
            function openForm(val) {
                document.getElementById(val).style.display = "block";
            }

            function closeForm(val) {
                document.getElementById(val).style.display = "none";
            }
        </script>
    </div>
    <?php
        if(isset($message)){
            echo '<div class="message">'.$message.'</div>';
            unset($message);
        }
    ?>
    <div class="thetable" style="overflow-x: auto;">
        <table>
            <?php
                $getamaof = mysqli_query($conn, "SELECT offer_id, ama_id FROM ama_offered WHERE address_id = '$address'") or die('query failed:\n' . mysqli_error($conn));
                if (mysqli_num_rows($getamaof) > 0) {
                    $getamaof = mysqli_fetch_all($getamaof, MYSQLI_ASSOC);
                } else {
                    mysqli_free_result( $getamaof);
                    $getamaof = "";
                }
                $getroom = mysqli_query($conn, "SELECT * FROM rooms WHERE address_id = '$address'") or die('query failed:\n' . mysqli_error($conn));
                echo '<tr><th>Room No.</th><th>Price</th><th>MAX Occ.</th>';
                if (is_array($getamaof) && count($getamaof) > 0) {
                    for ($i=0; $i < count($getamaof); $i++) { 
                        echo '<th>'.$getamaof[$i]['ama_id'].'</th>';
                    }
                }
                echo '</tr>';
                if (mysqli_num_rows($getroom) > 0) {
                    while ($oneroom = mysqli_fetch_assoc($getroom)) {
                        echo '<tr><th>'.$oneroom['room_num'].'</th><th>'.$oneroom['base_price'].'</th><th>'.$oneroom['max_occup'].'</th>';
                        if (is_array($getamaof) && count($getamaof) > 0) {
                            for ($i=0; $i < count($getamaof); $i++) {
                                $offer_id = $getamaof[$i]['offer_id'];
                                $roomama = mysqli_query($conn, "SELECT distance FROM ama_to_room WHERE offer_id = '$offer_id' And room_id = '".$oneroom['room_id']."'") or die('query failed:\n' . mysqli_error($conn));
                                $dis = mysqli_fetch_assoc($roomama);
                                echo '<th>'.$dis['distance'].'</th>';
                            }
                        }
                        echo '</tr>';
                    }
                }
            ?>
        </table>
    </div>
</body>
</html>