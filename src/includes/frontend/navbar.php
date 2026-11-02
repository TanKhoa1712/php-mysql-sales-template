<?php

$cartCount = array_sum($_SESSION['cart'] ?? []);
$isLoggedIn = isset($_SESSION['customer_id']);
$customerName = $_SESSION['customer_name'] ?? '';

?>

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container store-navbar-wrap">
        <a class="navbar-brand store-brand" href="/" aria-label="KhoaMart - Trang chủ">
            <span class="brand-mark" aria-hidden="true">K</span>
            <span class="brand-name">Khoa<span>Mart</span><small>MUA SẮM THÔNG MINH</small></span>
        </a>

        <div class="store-account-shortcuts">
            <?php if ($isLoggedIn): ?>
                <span class="store-customer">Xin chào, <?= htmlspecialchars($customerName) ?></span>
                <a class="store-login-shortcut" href="/logout.php">Đăng xuất</a>
            <?php else: ?>
                <a class="store-login-shortcut" href="/login.php">Đăng nhập</a>
                <a class="store-register-shortcut" href="/register.php">Đăng ký</a>
            <?php endif; ?>
        </div>

    </div>
    <div class="store-category-nav">
        <div class="container d-flex align-items-center gap-4">
            <a href="/">Trang chủ</a>
            <a href="/products.php">Tất cả sản phẩm</a>
            <span class="ms-auto d-none d-lg-inline">Giá tốt · Chọn nhanh · Đặt hàng tiện lợi</span>
        </div>
    </div>
</nav>
