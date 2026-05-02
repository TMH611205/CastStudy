<?php
session_start();
include 'includes/db_config.php';

$user_id = $_SESSION['user_id'];
$motel_id = $_POST['motel_id'];
$content = $_POST['content'];
$rating = $_POST['rating'];

mysqli_query($conn,"INSERT INTO comments(user_id,motel_id,content,rating)
VALUES($user_id,$motel_id,'$content',$rating)");

header("Location: detail.php?id=".$motel_id);
