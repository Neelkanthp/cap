<?php
include 'db_connection.php';
session_start();
$conn = OpenCon();
if(!isset($_SESSION['email']) || !isset($_SESSION['acctype'])){
    header('location:login.php'); exit;
}
$user_id = mysqli_real_escape_string($conn, $_SESSION['email']);
$acc_type = mysqli_real_escape_string($conn, $_SESSION['acctype']);

if(isset($_POST['submit'])){
    $addre = mysqli_real_escape_string($conn, $_POST['address']);
    $roomnum = mysqli_real_escape_string($conn, $_POST['room_num']);
    $fromdate = mysqli_real_escape_string($conn, $_SESSION['checkin']);
    $todate = mysqli_real_escape_string($conn, $_SESSION['checkout']);

    $select = mysqli_query($conn, "INSERT INTO `bookings`(`customer_username`, `address_id`, `room_num`, `from_date`, `to_date`) VALUES ('$user_id','$addre','$roomnum','$fromdate','$todate')")
            or die('query failed:\n' . mysqli_error($conn));
    if($select){
        $message = 'registered successfully!';
        unset( $_SESSION['checkin']);
        unset( $_SESSION['checkout']);
        header('location:myAccount.php'); exit;
    } else{
        $message = 'booking not confirmed, something went wrong!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Selected Room</title>

        <!-- custom css file link  -->
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <div class="form-container">
            <form action="" method="post" enctype="multipart/form-data">
                <?php
                    if(isset($message)){
                        echo '<div class="message">'.$message.'</div>';
                        unset($message);
                    }
                    if(isset($_GET['roomid'])){
                        $room_id = mysqli_real_escape_string($conn, $_GET['roomid']);
                        $hotelinfo = mysqli_query($conn, "SELECT b.image, b.name, b.about, b.phone, r.* FROM rooms AS r, business_locations AS b WHERE b.address = r.address_id AND r.room_id = '$room_id'") or die('query failed:\n' . mysqli_error($conn));
                        $row = mysqli_fetch_assoc($hotelinfo);
                        if($row['image'] == ''){
                            echo '<img src="images/defaultHotelSilhouette.jpeg">';
                        } else{
                            echo '<img src="uploaded_img/'.$row['image'].'">';
                        }
                        echo '<br><label>Hotel Name: <h1>'.$row['name'].'</h1></label>';
                        echo '<p>'.$row['address_id'].'</p>';
                        
                        echo '<br><p>About '.$row['name'].':</p>';
                        echo '<p>'.$row['about'].'</p>';
                        echo '<p>'.$row['phone'].'</p>';

                        echo '<br><p>About the Room: '.$row['room_num'].'</p>';
                        echo '<p>Price: $'.$row['base_price'].'</p>';
                        echo '<p>Max occupation Capacity'.$row['max_occup'].'</p>';
                        $aman = mysqli_query($conn, "SELECT ao.ama_id, ar.distance FROM ama_to_room AS ar CROSS JOIN ama_offered AS ao WHERE ar.offer_id = ao.offer_id AND ar.room_id = '$room_id'") or die('query failed:\n' . mysqli_error($conn));
                        if (mysqli_num_rows($aman) > 0) {
                            echo '<br><p>The Amanities provided: (and its distance from your room door, in yards)</p>';
                            while($amadis = mysqli_fetch_assoc($aman)) {
                                echo '<p>'.$amadis['ama_id'].': '.$amadis['distance'].'</p>';
                            }
                        }
                    }
                ?>
                <input type="hidden" name="address" value="<?php echo $row['address_id']; ?>">
                <input type="hidden" name="room_num" value="<?php echo $row['room_num']; ?>">
                <?php if ($acc_type == 0) {
                    echo '<input type="submit" name="submit" value="Confirm" class="btn">';
                } ?>
            </form>
        </div>
    </body>
</html>