<?php
include 'db_connection.php';
session_start();
$conn = OpenCon();
$user_id = mysqli_real_escape_string($conn, $_SESSION['email']);

if(isset($_POST['submit'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['street'].', '.$_POST['city'].', '.$_POST['state'].' '.$_POST['zip']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $about = mysqli_real_escape_string($conn, $_POST['about']);
    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/'.$image;

    $select = mysqli_query($conn, "SELECT `address` FROM `business_locations` WHERE `address` = '$address'")
            or die('query failed:\n' . mysqli_error($conn));
    if(mysqli_num_rows($select) > 0){
        $message = 'Someone else has this address';
    } elseif ($image_size > 200000) {
        $message = 'image size too large!';
    } else {
        $insert = mysqli_query($conn, "INSERT INTO business_locations VALUES('$address','$user_id','$name', NULLIF('$about', ''), NULLIF('$phone', ''), NULLIF('$image', ''))")
                or die('query failed:' . mysqli_error($conn));
        $ama_list = mysqli_query($conn, "SELECT * FROM amanities") or die('query failed:\n' . mysqli_error($conn));
        if (mysqli_num_rows($ama_list) > 0) {
            // output data of each row
            $sql = "";
            while($ch = mysqli_fetch_assoc($ama_list)) {
                $ama = mysqli_real_escape_string($conn, $_POST[$ch['amanity']] );
                if ( $ama != '' ) {
                    $sql .= "INSERT INTO ama_offered (address_id, ama_id) VALUES ('$address', '$ama');";
                }
            }
            $insert = mysqli_multi_query($conn, $sql) or die('query failed:\n' . mysqli_error($conn));
        }
        if($insert){
            move_uploaded_file($image_tmp_name, $image_folder);
            $message = 'account setup successful!';
            header('location:login.php'); exit;
        }else{
            $message = 'account setup failed!';
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
        <title>Setup your Account</title>

        <!-- custom css file link  -->
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <div class="form-container">
            <form action="" method="post" enctype="multipart/form-data">
                <h3>Enter Account Info</h3>
                <input type="text" name="name" placeholder="Enter Hotel Name" class="box" required>
                <input type="text" name="street" placeholder="Enter Street Addr." class="box" required>
                <input type="text" name="city" placeholder="Enter City" class="box" required>
                <input type="text" name="state" placeholder="Enter State (eg. PA, OH, etc)" maxlength="2" class="box" required>
                <input type="number" name="zip" placeholder="Enter zipcode" maxlength="5" class="box" required>
                <input type="tel" name="phone" placeholder="Enter Phone Number" class="box">
                <textarea name="about" id="description" cols="30" rows="10" placeholder="Enter something" maxlength="500" class="box"></textarea>
                <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
                <?php
                    $ama_list = mysqli_query($conn, "SELECT * FROM amanities") or die('query failed:\n' . mysqli_error($conn));
                    if (mysqli_num_rows($ama_list) > 0) {
                        // output data of each row
                        while($row = mysqli_fetch_assoc($ama_list)) {
                            echo '<input type="checkbox" id="'.$row['amanity'].'" name="'.$row["amanity"].'" value="'.$row["amanity"].'">';
                            echo '<label for="'.$row['amanity'].'">'.$row['amanity'].'</label> <br>';
                        }
                    }
                    if(isset($message)){
                        echo '<div class="message">'.$message.'</div>';
                        unset($message);
                    }
                ?>
                <button type="submit" name="submit" class="btn"> submit </button>
            </form>
        </div>
    </body>
</html>