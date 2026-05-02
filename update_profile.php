<?php
session_start();
include 'includes/db_config.php';

$id = $_SESSION['user_id'];
$name = $_POST['name'];

// xử lý avatar
if (!empty($_FILES['avatar']['name'])) {

    $file = $_FILES['avatar'];
    $filename = time() . "_" . $file['name'];
    $target = "uploads/avatars/" . $filename;

    move_uploaded_file($file['tmp_name'], $target);

    $sql = "UPDATE user SET Name='$name', Avatar='$filename' WHERE ID=$id";
} else {
    $sql = "UPDATE user SET Name='$name' WHERE ID=$id";
}

mysqli_query($conn, $sql);

header("Location: profile.php");
?>
