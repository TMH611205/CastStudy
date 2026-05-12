<?php
session_start();
require_once 'includes/db_config.php';

header('Content-Type: application/json; charset=utf-8');

$user_id = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
} elseif (isset($_SESSION['user']['ID'])) {
    $user_id = intval($_SESSION['user']['ID']);
}

if ($user_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Bạn cần đăng nhập để sử dụng tính năng này!']);
    exit;
}

$motel_id = isset($_POST['motel_id']) ? intval($_POST['motel_id']) : 0;

if ($motel_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID phòng trọ không hợp lệ.']);
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM favorites WHERE user_id = $user_id AND motel_id = $motel_id");
if (!$result) {
    echo json_encode(['status' => 'error', 'message' => 'Lỗi truy vấn cơ sở dữ liệu.']);
    exit;
}

$response = ['status' => 'error', 'message' => 'Không xác định được hành động.', 'count' => 0];

if (mysqli_num_rows($result) > 0) {
    $sql = "DELETE FROM favorites WHERE user_id = $user_id AND motel_id = $motel_id";
    if (mysqli_query($conn, $sql)) {
        $response['status'] = 'removed';
        $response['message'] = 'Đã xóa khỏi danh sách yêu thích.';
    } else {
        $response['message'] = 'Xóa thất bại.';
    }
} else {
    $sql = "INSERT INTO favorites (user_id, motel_id) VALUES ($user_id, $motel_id)";
    if (mysqli_query($conn, $sql)) {
        $response['status'] = 'added';
        $response['message'] = 'Đã thêm vào danh sách yêu thích.';
    } else {
        $response['message'] = 'Thêm thất bại.';
    }
}

$countRes = mysqli_query($conn, "SELECT COUNT(*) AS total FROM favorites WHERE user_id = $user_id");
if ($countRes) {
    $countRow = mysqli_fetch_assoc($countRes);
    $response['count'] = intval($countRow['total']);
}

echo json_encode($response);
exit;
?>