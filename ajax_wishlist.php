<?php
session_start();
require_once 'includes/db_config.php';

header('Content-Type: application/json; charset=utf-8');

function wishlist_response(array $payload): void
{
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

$user_id = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
} elseif (isset($_SESSION['user']['ID'])) {
    $user_id = intval($_SESSION['user']['ID']);
}

if ($user_id <= 0) {
    wishlist_response(['status' => 'error', 'message' => 'Bạn cần đăng nhập để sử dụng tính năng này!']);
}

$motel_id = isset($_POST['motel_id']) ? intval($_POST['motel_id']) : 0;

if ($motel_id <= 0) {
    wishlist_response(['status' => 'error', 'message' => 'ID phòng trọ không hợp lệ.']);
}

try {
    $result = mysqli_query($conn, "SELECT * FROM favorites WHERE user_id = $user_id AND motel_id = $motel_id");

    $response = ['status' => 'error', 'message' => 'Không xác định được hành động.', 'count' => 0];

    if (mysqli_num_rows($result) > 0) {
        $sql = "DELETE FROM favorites WHERE user_id = $user_id AND motel_id = $motel_id";
        mysqli_query($conn, $sql);
        $response['status'] = 'removed';
        $response['message'] = 'Đã xóa khỏi danh sách yêu thích.';
    } else {
        $sql = "INSERT INTO favorites (user_id, motel_id) VALUES ($user_id, $motel_id)";
        mysqli_query($conn, $sql);
        $response['status'] = 'added';
        $response['message'] = 'Đã thêm vào danh sách yêu thích.';
    }

    $countRes = mysqli_query($conn, "SELECT COUNT(*) AS total FROM favorites WHERE user_id = $user_id");
    if ($countRes) {
        $countRow = mysqli_fetch_assoc($countRes);
        $response['count'] = intval($countRow['total']);
    }

    wishlist_response($response);
} catch (Throwable $e) {
    error_log('ajax_wishlist.php error: ' . $e->getMessage());
    wishlist_response([
        'status' => 'error',
        'message' => 'Lỗi xử lý yêu thích: ' . $e->getMessage(),
        'count' => 0,
    ]);
}
?>