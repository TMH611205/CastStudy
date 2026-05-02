<?php
session_start();
include 'includes/db_config.php';

// kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    die("Bạn chưa đăng nhập!");
}

$id = $_SESSION['user_id'];

$result = mysqli_query($conn, "SELECT * FROM user WHERE ID=$id");
$user = mysqli_fetch_assoc($result);

// avatar mặc định
$avatar = $user['Avatar'] ? $user['Avatar'] : 'default.png';
?>

<h2>Profile</h2>

<img src="uploads/avatars/<?php echo $avatar; ?>" width="120">

<p><b>Username:</b> <?php echo $user['Username']; ?></p>
<p><b>Email:</b> <?php echo $user['Email']; ?></p>

<!-- Cập nhật profile -->
<form action="update_profile.php" method="POST" enctype="multipart/form-data">
    <input name="name" value="<?php echo $user['Name']; ?>" required>
    
    <input type="file" name="avatar" accept="image/*">

    <button type="submit">Cập nhật</button>
</form>

<hr>

<!-- Đổi mật khẩu -->
<form action="change_password.php" method="POST">
    <input type="password" name="old" placeholder="Mật khẩu cũ" required>
    <input type="password" name="new" placeholder="Mật khẩu mới" required>
    <button type="submit">Đổi mật khẩu</button>
</form>
