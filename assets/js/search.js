/**
 * 1. Xử lý Gợi ý địa chỉ (Suggestion)
 */
function fetchSuggestions(val) {
    const box = document.getElementById('suggestion-box');
    
    // Nếu người dùng nhập ít hơn 2 ký tự thì xóa hộp gợi ý
    if (val.length < 2) { 
        if (box) box.innerHTML = ''; 
        return; 
    }

    // Gọi đến file xử lý PHP đã tạo
    fetch('ajax_suggest.php?keyword=' + encodeURIComponent(val))
        .then(res => res.text())
        .then(data => {
            if (box) box.innerHTML = data;
        })
        .catch(err => console.error('Lỗi khi lấy gợi ý:', err));
}

/**
 * 2. Hàm chọn địa chỉ từ danh sách gợi ý
 */
function selectSuggest(val) {
    const input = document.getElementById('search-input');
    const box = document.getElementById('suggestion-box');
    
    if (input) input.value = val;
    if (box) box.innerHTML = '';
}

/**
 * 3. Xử lý Thả tim (Wishlist)
 */
function toggleWishlist(motelId, btn) {
    const icon = btn.querySelector('i');
    
    // Gửi dữ liệu theo phương thức POST đến file xử lý
    const formData = new URLSearchParams();
    formData.append('motel_id', motelId);

    fetch('ajax_wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: formData.toString()
    })
    .then(res => {
        if (!res.ok) {
            throw new Error('HTTP ' + res.status);
        }
        return res.json();
    })
    .then(data => {
        const countEl = document.getElementById('wishlistCount');
        if (typeof data.count !== 'undefined' && countEl) {
            countEl.textContent = data.count;
        }

        if (data.status === 'added') {
            icon.classList.replace('fa-regular', 'fa-solid');
            alert(data.message || 'Đã thêm vào danh sách yêu thích!');
        } else if (data.status === 'removed') {
            icon.classList.replace('fa-solid', 'fa-regular');
            alert(data.message || 'Đã xóa khỏi danh sách yêu thích!');
        } else {
            alert(data.message || 'Bạn cần đăng nhập để sử dụng tính năng này.');
        }

        if (window.location.pathname.includes('favorites.php')) {
            location.reload();
        }
    })
    .catch(err => {
        console.error('Lỗi Wishlist:', err);
        alert('Có lỗi xảy ra, vui lòng thử lại sau.');
    });
}