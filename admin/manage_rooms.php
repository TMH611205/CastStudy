<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once '../includes/db_config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 2) {
    header('Location: ../index.php'); exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'])) {
    $id = (int)$_POST['id'];
    if ($id > 0) {
        if ($_POST['action'] === 'approve') {
            mysqli_query($conn, "UPDATE motel SET approve = 1 WHERE ID = $id");
            $message = 'Đã duyệt tin đăng.';
        } elseif ($_POST['action'] === 'unapprove') {
            mysqli_query($conn, "UPDATE motel SET approve = 0 WHERE ID = $id");
            $message = 'Đã chuyển tin về trạng thái chờ duyệt.';
        } elseif ($_POST['action'] === 'delete') {
            mysqli_query($conn, "DELETE FROM motel WHERE ID = $id");
            $message = 'Đã xóa tin đăng.';
        }
    }
}

$sql = "SELECT motel.*, user.Name AS owner_name, categories.Name AS category_name
        FROM motel
        LEFT JOIN user ON motel.user_id = user.ID
        LEFT JOIN categories ON motel.category_id = categories.ID
        ORDER BY motel.created_at DESC";
$result = mysqli_query($conn, $sql);

include '../includes/header.php';
?>

<div class="container my-5">
    <div class="mb-4">
        <h2 class="fw-bold">Quản lý tin đăng</h2>
        <p class="text-muted">Quản lý tất cả tin đăng, bao gồm duyệt, hủy duyệt và xóa tin.</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="table-responsive shadow-sm rounded-4 border">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tiêu đề</th>
                        <th>Người đăng</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($room = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $room['ID']; ?></td>
                            <td><?php echo htmlspecialchars($room['title']); ?></td>
                            <td><?php echo htmlspecialchars($room['owner_name'] ?? 'Khách'); ?></td>
                            <td><?php echo number_format((int)$room['price']); ?> đ</td>
                            <td>
                                <?php if ($room['approve'] == 1): ?>
                                    <span class="badge bg-success">Đã duyệt</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($room['created_at']); ?></td>
                            <td>
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="../detail.php?id=<?php echo $room['ID']; ?>" class="btn btn-sm btn-outline-primary">Xem</a>
                                    <form method="post" class="m-0">
                                        <input type="hidden" name="id" value="<?php echo $room['ID']; ?>">
                                        <input type="hidden" name="action" value="<?php echo $room['approve'] == 1 ? 'unapprove' : 'approve'; ?>">
                                        <button type="submit" class="btn btn-sm btn-<?php echo $room['approve'] == 1 ? 'warning text-dark' : 'success'; ?>">
                                            <?php echo $room['approve'] == 1 ? 'Hủy duyệt' : 'Duyệt'; ?>
                                        </button>
                                    </form>
                                    <form method="post" class="m-0" onsubmit="return confirm('Bạn có chắc muốn xóa tin này?');">
                                        <input type="hidden" name="id" value="<?php echo $room['ID']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Chưa có tin đăng nào trong hệ thống.</div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
