<?php
session_start();
include 'includes/db_config.php';

$name = $_POST['name'];
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm = $_POST['confirm'];

// check pass
if(strlen($password) < 6){
    die("Mật khẩu phải >= 6 ký tự");
}

if($password !== $confirm){
    die("Mật khẩu không khớp");
}

// hash
$hash = password_hash($password, PASSWORD_DEFAULT);

// insert
$sql = "INSERT INTO user (Username, Email, Password, Name)
        VALUES ('$username','$email','$hash','$name')";

if(mysqli_query($conn,$sql)){
    header("Location: login.php");
}else{
    echo "Lỗi hoặc trùng!";
}
