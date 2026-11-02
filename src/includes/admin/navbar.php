<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>

<nav class="navbar navbar-expand-lg admin-navbar">
    <div class="container admin-navbar-container">
        <a class="navbar-brand admin-brand" href="/admin/">
            <span class="admin-brand-mark" aria-hidden="true">K</span>
            <span>Khoa<span>Mart</span><small>QUẢN TRỊ HỆ THỐNG</small></span>
        </a>
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#adminNavbar"
            aria-controls="adminNavbar"
            aria-expanded="false"
            aria-label="Mở menu quản trị"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav ms-lg-auto align-items-lg-center gap-lg-1">

                <li class="nav-item">
                    <a class="nav-link" href="/" target="_blank" rel="noopener">Xem cửa hàng</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with($currentPath, '/admin/categories') ? 'active' : '' ?>" href="/admin/categories/">
                        Danh mục
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with($currentPath, '/admin/products') ? 'active' : '' ?>" href="/admin/products/">
                        Sản phẩm
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with($currentPath, '/admin/orders') ? 'active' : '' ?>" href="/admin/orders/">
                        Đơn hàng
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with($currentPath, '/admin/shippers') ? 'active' : '' ?>" href="/admin/shippers/">
                        Nhà vận chuyển
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>