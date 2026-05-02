<?php
include 'includes/db_config.php';

$id = $_POST['id'];
$type = $_POST['type'];

if($type == "like"){
    mysqli_query($conn,"UPDATE comments SET react_like = react_like + 1 WHERE ID=$id");
}
if($type == "love"){
    mysqli_query($conn,"UPDATE comments SET react_love = react_love + 1 WHERE ID=$id");
}
if($type == "haha"){
    mysqli_query($conn,"UPDATE comments SET react_haha = react_haha + 1 WHERE ID=$id");
}
if($type == "angry"){
    mysqli_query($conn,"UPDATE comments SET react_angry = react_angry + 1 WHERE ID=$id");
}
