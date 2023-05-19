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
        <div class="topnav">
            <a href="home.php">Booker</a>
            <?php
                if( isset($_SESSION['email']) ) { 
                    echo '<a href="logout.php" class="split">Logout</a>';
                    echo '<a href="myAccount.php" class="split">Account</a>'; }
                else { echo '<a href="loginPage.php" class="split">Login/Signup</a>'; }
            ?>
        </div>
        <div class="searchform">
            <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data" class="searchformcon">
                <input type="text" name="place" placeholder="Enter City, State or Zipcode" class="box" required>
                <input type="date" name="checkin" placeholder="Select check-in date" class="box" required>
                <input type="date" name="checkout" placeholder="Select check-out date" class="box" required>
                <input type="submit" name="submit" value="Search" class="btn">
            </form>
        </div>
        <div class="searchdisplay">
            <?php
                if( $_SERVER["REQUEST_METHOD"] == "POST" ){
                    $place = mysqli_real_escape_string($conn, $_POST['place']);
                    $_SESSION['checkin'] = $_POST['checkin'];
                    $_SESSION['checkout'] = $_POST['checkout'];
                    $checkin = mysqli_real_escape_string($conn, $_SESSION['checkin']);
                    $checkout = mysqli_real_escape_string($conn, $_SESSION['checkout']);

                    $sql = "SELECT * FROM rooms WHERE address_id LIKE '%".$place."%' AND room_num NOT IN 
                            (SELECT room_num FROM bookings WHERE from_date BETWEEN '$checkin' AND '$checkout' OR 
                            to_date BETWEEN '$checkin' AND '$checkout' OR (from_date < '$checkin' AND to_date >'$checkout') );";
                    $roomsavail = mysqli_query($conn, $sql) or die('query failed:\n' . mysqli_error($conn));
                    if (mysqli_num_rows($roomsavail) > 0) {
                        while ($rom = mysqli_fetch_assoc($roomsavail)) {
                            $room_id = $rom['room_id'];
                            $addr = $rom['address_id'];
                            $room_num = $rom['room_num'];
                            $price = $rom['base_price'];
                            $getni = mysqli_query($conn, "SELECT `image`, `name` FROM business_locations WHERE `address` = '$addr'") or die('query failed:\n' . mysqli_error($conn));
                            $row = mysqli_fetch_assoc($getni);
                            echo '<div class="searchresult">';
                            echo '  <a href="selection.php?roomid='.$room_id.'">';
                            if ($row['image'] == '') {
                                echo '<img src="images/defaultHotelSilhouette.jpeg">';
                            } else {
                                echo '<img src="uploaded_img/'.$row['image'].'">';
                            }
                            echo '    <p><b>'.$row['name'].'</b></p>';
                            echo '    <p>'.$addr.'</p>';
                            echo '    <p>Room #'.$room_num.'</p>';
                            echo '    <p>$'.$price.'</p>';
                            echo '  </a>';
                            echo '</div>';
                        }
                    }
                }
            ?>
        </div>
    </body>
</html>