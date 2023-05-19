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
        <title>My Account</title>

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
        <div class="sidenav">
            <a href="profile.php" target="accountdisplay">Profile</a>
            <?php if ($acc_type == 1) {
                echo '<a href="management.php" target="accountdisplay">Management</a>';
            } else {
                echo '<a href="history.php" target="accountdisplay">History</a>';
            }?>
        </div>
        <iframe name="accountdisplay" class="accountinfo"></iframe>
    </body>
</html>