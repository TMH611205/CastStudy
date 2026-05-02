<?php
session_start();
include 'includes/db_config.php';

$id = $_SESSION['user_id'];

$old = $_POST['old'];
$new = $_POST['new'];

// lấy password cũ từ DB
$result = mysqli_query($conn, "SELECT Password FROM user WHERE ID=$id");
$user = mysqli_fetch_assoc($result);

// kiểm tra password cũ
if (!password_verify($old, $user['Password'])) {
    die("Mật khẩu cũ sai!");
}

// hash password mới
$new_hash = password_hash($new, PASSWORD_DEFAULT);

// update
mysqli_query($conn, "UPDATE user SET Password='$new_hash' WHERE ID=$id");

echo "Đổi mật khẩu thành công!";
?>
