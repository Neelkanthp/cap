<?php
include 'db_connection.php';
session_start();
$conn = OpenCon();
if(!isset($_SESSION['email']) || !isset($_SESSION['acctype'])){
    header('location:login.php'); exit;
}
$user_id = mysqli_real_escape_string($conn, $_SESSION['email']);
$acc_type = mysqli_real_escape_string($conn, $_SESSION['acctype']);

if(isset($_POST['update_profile'])){
    if($acc_type == 1) {
        $update_image = $_FILES['update_image']['name'];
        $update_image_size = $_FILES['update_image']['size'];
        $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
        $update_image_folder = 'uploaded_img/'.$update_image;
        if(!empty($update_image)){
            if($update_image_size > 200000){
                $message[] = 'image is too large';
            } else{
                $image_update_query = mysqli_query($conn, "UPDATE `business_locations` SET `image` = '$update_image' WHERE `user_id` = '$user_id'")
                        or die('query failed:' . mysqli_error($conn));
                if($image_update_query){
                    move_uploaded_file($update_image_tmp_name, $update_image_folder);
                    $message[] = 'image updated succssfully!';
                } else {
                    $message[] = 'failed to upload image.';
                }
            }
        }

        $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
        if ( !empty($update_name) ) {
            $changename =  mysqli_query($conn, "UPDATE `business_locations` SET `name` = '$update_name' WHERE `user_id` = '$user_id'") or die('query failed:' . mysqli_error($conn));
        } else {
            $message[] = 'Hotel Name cannot be empty';
        }

        $update_phone = mysqli_real_escape_string($conn, $_POST['update_phone']);
        $update_about = mysqli_real_escape_string($conn, $_POST['update_about']);
        mysqli_query($conn, "UPDATE `business_locations` SET `about` = NULLIF('$update_about', ''), `phone` = NULLIF('$update_phone', '') WHERE `user_id` = '$user_id'") or die('query failed:' . mysqli_error($conn));
    }

    $stored_pass = $_POST['stored_pass'];
    $old_pass = mysqli_real_escape_string($conn, md5($_POST['old_pass']));
    $new_pass = mysqli_real_escape_string($conn, md5($_POST['new_pass']));
    $confirm_pass = mysqli_real_escape_string($conn, md5($_POST['confirm_pass']));
    if($_POST['old_pass'] != '' && $_POST['new_pass'] != '' && $_POST['new_pass'] != ''){
        if($old_pass != $stored_pass){
            $message[] = 'old password not matched!';
        }elseif($new_pass != $confirm_pass){
            $message[] = 'confirm password not matched!';
        }else{
            mysqli_query($conn, "UPDATE `accounts` SET mypass = '$confirm_pass' WHERE username = '$user_id'") or die('query failed');
            $message[] = 'password updated successfully!';
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
        <title>Profile</title>

        <!-- custom css file link  -->
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <div class="update-profile">
            <?php
                $sql = "SELECT mypass FROM accounts WHERE username='$user_id'";
                if ($acc_type == 1) {
                    $sql = "SELECT a.mypass, bl.* FROM accounts AS a, business_locations AS bl WHERE a.username='$user_id' AND a.username = bl.user_id";
                }
                $select = mysqli_query($conn, $sql) or die('query failed:\n' . mysqli_error($conn));
                if(mysqli_num_rows($select) > 0){
                    $fetch = mysqli_fetch_assoc($select);
                }
            ?>

            <form action="" method="post" enctype="multipart/form-data">
                <?php
                    if ($acc_type == 1) {
                        if($fetch['image'] == ''){
                            echo '<img src="images/defaultHotelSilhouette.jpeg">';
                        } else{
                            echo '<img src="uploaded_img/'.$fetch['image'].'">';
                        }
                    }
                ?>
                <div class="flex">
                    <?php
                        if ($acc_type == 1) {
                            echo '<div class="inputBox">';
                            echo '  <span>Update Picture :</span>';
                            echo '  <input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png" class="box">';
                            echo '  <span>Hotel Name :</span>';
                            echo '  <input type="text" name="update_name" value="'.$fetch['name'].'" class="box" required>';
                            echo '  <span>Address/Location :</span>';
                            echo '  <input type="text" value="'.$fetch['address'].'" class="box" readonly>';
                            echo '  <span>Phone Number :</span>';
                            echo '  <input type="number" name="update_phone" value="'.$fetch['phone'].'" placeholder="enter phone number" class="box">';
                            echo '</div>';
                        }
                    ?>
                    <div class="inputBox">
                        <span>Your Email :</span>
                        <input type="email" value="<?php echo $_SESSION['email']; ?>" class="box" readonly>
                        <input type="hidden" name="stored_pass" value="<?php echo $fetch['mypass']; ?>">
                        <span>Old Password :</span>
                        <input type="password" name="old_pass" placeholder="enter previous password" class="box">
                        <span>New Password :</span>
                        <input type="password" name="new_pass" placeholder="enter new password" class="box">
                        <span>Confirm Password :</span>
                        <input type="password" name="confirm_pass" placeholder="confirm new password" class="box">
                    </div>
                </div>
                <?php
                    if ($acc_type == 1) {
                        echo '<textarea name="update_about" cols="30" rows="10" placeholder="Enter description" maxlength="500" class="box">'.$fetch['about'].'</textarea>';
                    }
                    if(isset($message)){
                        foreach($message as $message){
                            echo '<div class="message">'.$message.'</div>';
                        }
                    }
                ?>
                <input type="submit" name="update_profile" value="update profile" class="btn">
            </form>
        </div>
    </body>
</html>