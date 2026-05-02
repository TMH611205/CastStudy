<?php 
session_start();
require_once 'includes/db_config.php'; 
require_once 'includes/header.php'; 

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    $sql = "SELECT motel.*, user.Name as owner_name, user.Avatar as owner_avatar 
            FROM motel 
            JOIN user ON motel.user_id = user.ID 
            WHERE motel.ID = '$id' AND motel.approve = 1";
            
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $room = mysqli_fetch_assoc($result);
        mysqli_query($conn, "UPDATE motel SET count_view = count_view + 1 WHERE ID = '$id'");
        
        
        $raw_images = explode(',', $room['images']); 
        $all_images = array_filter(array_map('trim', $raw_images));
        $first_img = !empty($all_images) ? reset($all_images) : 'default-room.jpg';
    } else {
        echo "<div class='container my-5'><h3>không tìm thấy phòng trọ này m ơi!</h3></div>";
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                    <li class="breadcrumb-item active"><?php echo $room['title']; ?></li>
                </ol>
            </nav>

            <div class="gallery-container mb-4">
                <div class="main-img-box rounded-4 overflow-hidden shadow-sm mb-2" style="height: 450px;">
                    <img id="main-view" src="uploads/rooms/<?php echo $first_img; ?>" class="w-100 h-100 object-fit-cover" alt="Phòng trọ">
                </div>
                
                <div class="row g-2">
                    <?php 
                    foreach($all_images as $img_item) { 
                        // thumbnail cũng chỉ hiện nếu có ảnh thật
                    ?>
                    <div class="col-3 col-md-2">
                        <div class="thumb-box rounded-3 overflow-hidden border" style="height: 70px; cursor: pointer;">
                            <img src="uploads/rooms/<?php echo $img_item; ?>" 
                                 class="w-100 h-100 object-fit-cover" 
                                 onclick="document.getElementById('main-view').src=this.src">
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
<!-- COMMENT -->
<div class="mt-5">
    <h4 class="fw-bold mb-3">Đánh giá</h4>

    <?php if(isset($_SESSION['user_id'])): ?>

    <form action="add_comment.php" method="POST" class="mb-4">
        <input type="hidden" name="motel_id" value="<?php echo $room['ID']; ?>">

        <textarea name="content" class="form-control mb-2" placeholder="Viết bình luận..." required></textarea>

        <div class="d-flex justify-content-between">
            <select name="rating" class="form-select w-auto">
                <option value="5">⭐⭐⭐⭐⭐</option>
                <option value="4">⭐⭐⭐⭐</option>
                <option value="3">⭐⭐⭐</option>
                <option value="2">⭐⭐</option>
                <option value="1">⭐</option>
            </select>

            <button class="btn btn-primary">Gửi</button>
        </div>
    </form>

    <?php else: ?>
        <p>👉 Vui lòng đăng nhập để bình luận</p>
    <?php endif; ?>

    <!-- HIỂN THỊ COMMENT -->
    <?php
    $sql_cmt = "SELECT comments.*, user.Name, user.Avatar 
                FROM comments 
                JOIN user ON comments.user_id = user.ID
                WHERE motel_id = {$room['ID']}
                ORDER BY created_at DESC";

    $res_cmt = mysqli_query($conn, $sql_cmt);

    while($c = mysqli_fetch_assoc($res_cmt)){
    ?>

    <div class="card mb-2 p-3">
        <div class="d-flex align-items-center mb-2">
            <img src="uploads/avatars/<?php echo $c['Avatar']; ?>" 
                 width="40" height="40" 
                 class="rounded-circle me-2"
                 style="object-fit:cover;">

            <div>
                <b><?php echo $c['Name']; ?></b>
                <div style="font-size:12px;color:gray;">
                    <?php echo $c['created_at']; ?>
                </div>
            </div>
        </div>

        <div>
            <?php echo str_repeat("⭐", $c['rating']); ?>
        </div>

       <p class="mt-2 mb-1"><?php echo $c['content']; ?></p>

<!-- REACTION -->
<div class="small text-muted position-relative mt-1">

    <span style="cursor:pointer;" onmouseover="showReact(<?php echo $c['ID']; ?>)">
        ❤️ Yêu thích
    </span>

    · <?php echo $c['created_at']; ?>

    <!-- BOX -->
    <div id="react-box-<?php echo $c['ID']; ?>" class="react-box">
        <span onclick="react(<?php echo $c['ID']; ?>,'love')">❤️</span>
    </div>

    <!-- COUNT -->
    <div class="mt-1">
        ❤️ <?php echo $c['react_love'] ?? 0; ?>
    </div>

</div>

    </div>

    <?php } ?>

</div>

            <h2 class="fw-bold mb-3"><?php echo $room['title']; ?></h2>
            <p class="text-muted"><i class="fa-solid fa-location-dot me-2"></i> <?php echo $room['address']; ?></p>
            
            <hr class="my-4 opacity-50">

            <h5 class="fw-bold mb-3">Mô tả chi tiết</h5>
            <p class="text-secondary leading-relaxed">
                <?php echo nl2br($room['description']); ?>
            </p>

            <div class="row g-3 my-4 text-center">
                <?php 
                $utils = explode(',', $room['utilities']); 
                foreach($utils as $u) {
                    if(!empty(trim($u))) {
                ?>
                <div class="col-md-3 col-6">
                    <div class="p-3 bg-white rounded-4 shadow-sm border border-light h-100">
                        <i class="fa-solid fa-check-circle text-success mb-2 fs-4"></i>
                        <div class="small fw-bold"><?php echo trim($u); ?></div>
                    </div>
                </div>
                <?php } } ?>
            </div>

            <div class="mt-5">
                <h5 class="fw-bold mb-3">Vị trí & Chỉ đường</h5>
                <div id="map" class="rounded-4 bg-light d-flex align-items-center justify-content-center" style="height: 350px; border: 2px dashed #ddd;">
                    <span><i class="fa-solid fa-map-location-dot me-2"></i> Tọa độ: <?php echo $room['lating']; ?></span>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="sticky-top" style="top: 100px; z-index: 10;">
                <div class="card border-0 shadow-lg p-4 rounded-4 mb-4">
                    <div class="text-muted small mb-2">Giá phòng tháng này:</div>
                    <h3 class="text-danger fw-extrabold mb-4"><?php echo number_format($room['price'], 0, ',', '.'); ?>đ <small class="text-muted fs-6">/ tháng</small></h3>
                    
                    <div class="d-grid gap-2">
                        <a href="tel:<?php echo $room['phone']; ?>" class="btn btn-primary py-3 fw-bold rounded-pill shadow">
                            <i class="fa-solid fa-phone me-2"></i> Gọi ngay: <?php echo $room['phone']; ?>
                        </a>
                        <button class="btn btn-outline-dark py-3 fw-bold rounded-pill">
                            <i class="fa-solid fa-heart me-2"></i> Lưu tin này
                        </button>
                    </div>

                    <div class="mt-4 pt-4 border-top">
                        <div class="d-flex align-items-center">
                            <img src="uploads/<?php echo $room['owner_avatar']; ?>" class="rounded-circle me-3" width="50" height="50" style="object-fit: cover;">
                            <div>
                                <div class="fw-bold"><?php echo $room['owner_name']; ?></div>
                                <div class="text-muted small">Chủ trọ tin cậy</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
.react-box{
    display:none;
    position:absolute;
    background:#fff;
    padding:5px 10px;
    border-radius:30px;
    box-shadow:0 2px 10px rgba(0,0,0,0.2);
    top:-40px;
}

.react-box span{
    font-size:20px;
    margin:0 5px;
    cursor:pointer;
    transition:0.2s;
}

.react-box span:hover{
    transform:scale(1.3);
}
<script>
function showReact(id){
    document.getElementById("react-box-"+id).style.display = "block";
}

function react(id, type){
    fetch("react.php", {
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:"id="+id+"&type="+type
    }).then(()=>location.reload());
}
</script>

</style>

<?php include 'includes/footer.php'; ?>