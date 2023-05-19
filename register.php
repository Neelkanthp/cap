<?php
include 'db_connection.php';
session_start();
$conn = OpenCon();

if(isset($_POST['submit'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
    $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));
    $acc_type = mysqli_real_escape_string($conn, $_POST['account']);

    $select = mysqli_query($conn, "SELECT * FROM accounts WHERE username = '$email'")
            or die('query failed:\n' . mysqli_error($conn));
    if(mysqli_num_rows($select) > 0){
        $message = 'user already exist';
    } else{
        $acc_type = 0 + $acc_type;
        if($pass != $cpass){
            $message = 'confirm password not matched!';
        } elseif ($acc_type > 1) {
            $message = 'must select account type!';
        } else{
            $insert = mysqli_query($conn, "INSERT INTO accounts VALUES('$email', '$pass', '$acc_type')")
                    or die('query failed:' . mysqli_error($conn));
            if($insert){
                $message = 'registered successfully!';
                $_SESSION['email'] = $email;
                if ( $acc_type == 0 ) {
                    header('location:login.php'); exit;
                } else {
                    header('location:accountSetUp.php'); exit;
                }
            }else{
                $message = 'registeration failed!';
            }
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
        <title>Register</title>

        <!-- custom css file link  -->
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <div class="form-container">
            <form action="" method="post" enctype="multipart/form-data">
                <h3>register now</h3>
                <input type="email" name="email" placeholder="enter email" class="box" required>
                <input type="password" name="password" placeholder="enter password" class="box" required>
                <input type="password" name="cpassword" placeholder="confirm password" class="box" required>
                <select name="account" id="accountType" class="box" required>
                    <option value="5">Select Account Type:</option>
                    <option value="1">Business</option>
                    <option value="0">Customer</option>
                </select>
                <?php
                    if(isset($message)){
                        echo '<div class="message">'.$message.'</div>';
                        unset($message);
                    }
                ?>
                <input type="submit" name="submit" value="register now" class="btn">
                <p>already have an account? <a href="login.php">login now</a></p>
            </form>
        </div>
    </body>
</html>