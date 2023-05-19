<?php
include 'db_connection.php';
session_start();
$conn = OpenCon();
if(!isset($_SESSION['email']) || !isset($_SESSION['acctype'])){
    header('location:login.php'); exit;
}
$user_id = mysqli_real_escape_string($conn, $_SESSION['email']);
$acc_type = mysqli_real_escape_string($conn, $_SESSION['acctype']);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Search</title>

        <!-- custom css file link  -->
        <link rel="stylesheet" href="css/account.css">
    </head>
    <body>
        <div class="searchdisplay">
            <?php
                $sql = "SELECT * FROM `bookings` WHERE customer_username = '$user_id'";
                $roomsavail = mysqli_query($conn, $sql) or die('query failed:\n' . mysqli_error($conn));
                if (mysqli_num_rows($roomsavail) > 0) {
                    while ($rom = mysqli_fetch_assoc($roomsavail)) {
                        $addr = $rom['address_id'];
                        $room_num = $rom['room_num'];
                        echo '<div class="searchresult">';
                        echo '    <p>Address:'.$addr.'</p>';
                        echo '    <p>Room #'.$room_num.'</p>';
                        echo '    <p>Room #'.$rom['from_date'].'</p>';
                        echo '    <p>Room #'.$rom['to_date'].'</p>';
                        echo '</div>';
                    }
                }
            ?>
        </div>
    </body>
</html>