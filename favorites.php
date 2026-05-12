<?php
session_start();
require_once 'includes/db_config.php';
require_once 'includes/header.php';

$user_id = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
} elseif (isset($_SESSION['user']['ID'])) {
    $user_id = intval($_SESSION['user']['ID']);
}

if ($user_id <= 0) {
    header('Location: login.php');
    exit();
}

$sql = "SELECT favorites.*, motel.ID AS motel_id, motel.title, motel.address, motel.price, motel.images, categories.Name AS category_name
        FROM favorites
        JOIN motel ON favorites.motel_id = motel.ID
        LEFT JOIN categories ON motel.category_id = categories.ID
        WHERE favorites.user_id = $user_id";

$result = mysqli_query($conn, $sql);
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Danh sách tin đã lưu</h2>
            <p class="text-muted">Tất cả phòng trọ bạn đã thêm vào danh sách yêu thích.</p>
        </div>
        <a href="index.php" class="btn btn-outline-primary">Quay về trang chủ</a>
    </div>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <div class="row g-4">
            <?php while ($room = mysqli_fetch_assoc($result)): 
                $img_list = explode(',', $room['images']);
                $first_img = !empty($img_list[0]) ? trim($img_list[0]) : 'default-room.jpg';
                $img_src = (strpos($first_img, 'http') !== false) ? $first_img : 'uploads/rooms/' . $first_img;
                if (!file_exists($img_src)) {
                    $img_src = 'uploads/rooms/default-room.jpg';
                }
            ?>
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="row g-0">
                        <div class="col-5">
                            <img src="<?php echo htmlspecialchars($img_src); ?>" class="img-fluid rounded-start" alt="<?php echo htmlspecialchars($room['title']); ?>">
                        </div>
                        <div class="col-7">
                            <div class="card-body d-flex flex-column h-100">
                                <h5 class="card-title"><?php echo htmlspecialchars($room['title']); ?></h5>
                                <p class="card-text text-muted mb-1"><i class="fa-solid fa-location-dot me-1"></i><?php echo htmlspecialchars($room['address']); ?></p>
                                <p class="card-text text-muted mb-2"><i class="fa-solid fa-layer-group me-1"></i><?php echo htmlspecialchars($room['category_name'] ?: 'Chưa xác định'); ?></p>
                                <h6 class="text-danger mb-3"><?php echo number_format($room['price'], 0, ',', '.'); ?> đ/tháng</h6>
                                <div class="mt-auto d-flex gap-2">
                                    <a href="detail.php?id=<?php echo intval($room['motel_id']); ?>" class="btn btn-primary btn-sm">Xem chi tiết</a>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="toggleWishlist(<?php echo intval($room['motel_id']); ?>, this)">
                                        <i class="fa-solid fa-heart me-1"></i> Xóa khỏi yêu thích
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Bạn chưa lưu tin nào. Hãy quay lại tìm phòng và nhấn nút "Lưu tin này".</div>
    <?php endif; ?>
</div>

<script src="assets/js/search.js"></script>

<?php include 'includes/footer.php'; ?>
