<?php
$pageTitle = 'Quản trị hệ thống';

require_once '/var/www/src/includes/admin/header.php';
require_once '/var/www/src/includes/admin/navbar.php';
?>

<main class="container admin-main py-4 py-lg-5">

    <div class="admin-welcome mb-4 mb-lg-5">
        <div>
            <p class="admin-eyebrow mb-2">TỔNG QUAN CỬA HÀNG</p>
            <h1>Quản trị hệ thống</h1>
            <p class="mb-0">
                Chọn một khu vực để quản lý cửa hàng KhoaMart.
            </p>
        </div>
        <a href="/" target="_blank" rel="noopener" class="btn btn-admin-primary">
            Xem cửa hàng <span aria-hidden="true">↗</span>
        </a>
    </div>

    <div class="row g-3 g-lg-4">
        <div class="col-sm-6 col-xl-3">
            <a class="admin-module-card" href="/admin/products/">
                <span class="admin-module-icon">▣</span>
                <span class="admin-module-label">KHO HÀNG</span>
                <strong>Quản lý sản phẩm</strong>
                <span class="admin-module-description">Thông tin, giá bán và số lượng tồn kho</span>
                <span class="admin-module-arrow" aria-hidden="true">→</span>
            </a>
        </div>
        <div class="col-sm-6 col-xl-3">
            <a class="admin-module-card" href="/admin/categories/">
                <span class="admin-module-icon admin-module-icon-orange">⌘</span>
                <span class="admin-module-label">PHÂN LOẠI</span>
                <strong>Quản lý danh mục</strong>
                <span class="admin-module-description">Sắp xếp các nhóm sản phẩm của cửa hàng</span>
                <span class="admin-module-arrow" aria-hidden="true">→</span>
            </a>
        </div>
        <div class="col-sm-6 col-xl-3">
            <a class="admin-module-card" href="/admin/orders/">
                <span class="admin-module-icon admin-module-icon-blue">▤</span>
                <span class="admin-module-label">BÁN HÀNG</span>
                <strong>Quản lý đơn hàng</strong>
                <span class="admin-module-description">Tra cứu và cập nhật trạng thái đơn hàng</span>
                <span class="admin-module-arrow" aria-hidden="true">→</span>
            </a>
        </div>
        <div class="col-sm-6 col-xl-3">
            <a class="admin-module-card" href="/admin/shippers/">
                <span class="admin-module-icon admin-module-icon-purple">⇢</span>
                <span class="admin-module-label">VẬN CHUYỂN</span>
                <strong>Nhà vận chuyển</strong>
                <span class="admin-module-description">Quản lý đối tác giao nhận đơn hàng</span>
                <span class="admin-module-arrow" aria-hidden="true">→</span>
            </a>
        </div>
    </div>

</main>

<?php
require_once '/var/www/src/includes/admin/footer.php';