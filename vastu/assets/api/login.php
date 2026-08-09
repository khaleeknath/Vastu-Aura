
<?php
session_start();

include('../config/db-conn.php');

$phone = str_replace([' ', '+91'], '', $_POST['phone']);
$password = $_POST['password'];

$query = "SELECT * FROM tbl_users WHERE phone = '$phone'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) > 0) {

    $user = mysqli_fetch_assoc($result);

   

    if (password_verify($password, $user['password'])) {

                // Store user data in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['phone'] = $user['phone'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role_id']; 
                $_SESSION['user'] = $user;
                if ((int)$user['role_id'] === 1) {
                    header("Location: ../../admin-admins.php");
                } else {
                    header("Location: ../../index.php");
                }
        exit;

    } else {
        $_SESSION['login_error'] = "Invalid password.";
        header("Location: ../../login.php");
        exit;
    }

} else {
    $_SESSION['login_error'] = "Mobile number is not registered.";
    header("Location: ../../login.php");
    exit;
}
?>
?>