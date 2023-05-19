<?php
include 'db_connection.php';
session_start();
$conn = OpenCon();

if(isset($_POST['submit'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

    $select = mysqli_query($conn, "SELECT * FROM accounts WHERE username = '$email' AND mypass = '$pass'")
            or die('query failed:\n' . mysqli_error($conn));
    if(mysqli_num_rows($select) > 0){
        $row = mysqli_fetch_assoc($select);
        $_SESSION['email'] = $email;
        $_SESSION['acctype'] = $row['acc_type'];
        if ($row['acc_type'] == 1) {
            $addrget = mysqli_query($conn, "SELECT `address` FROM business_locations WHERE user_id = '$email'")
                    or die('query failed:\n' . mysqli_error($conn));
            $row = mysqli_fetch_assoc($addrget);
            $_SESSION['addr'] = $row['address'];
        }
        header('location:myAccount.php'); exit;
    } else{
        $message = 'incorrect email or password!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>login</title>

        <!-- custom css file link  -->
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <div class="form-container">
            <form action="" method="post" enctype="multipart/form-data">
                <h3>login now</h3>
                <input type="email" name="email" placeholder="enter email" class="box" required>
                <input type="password" name="password" placeholder="enter password" class="box" required>
                <?php
                    if(isset($message)){
                        echo '<div class="message">'.$message.'</div>';
                        unset($message);
                    }
                ?>
                <input type="submit" name="submit" value="login now" class="btn">
                <p>don't have an account? <a href="register.php">regiser now</a></p>
            </form>
        </div>
    </body>
</html>