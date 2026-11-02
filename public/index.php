<?php
require_once '/var/www/src/config/session.php';

$pageTitle = 'Trang chủ';

require_once '/var/www/src/includes/frontend/header.php';
require_once '/var/www/src/includes/frontend/navbar.php';
?>

<main class="store-main">
    <div class="container py-4 py-lg-5">
        <section class="store-hero mb-4 mb-lg-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="hero-eyebrow">MUA SẮM THẬT DỄ, GIÁ THẬT VUI</span>
                    <h1 class="display-5 fw-bold mt-3 mb-3">
                        Chọn món bạn thích,<br>
                        <span>giao tận nơi nhanh chóng.</span>
                    </h1>
                    <p class="hero-description mb-4">
                        Khám phá các sản phẩm công nghệ thiết thực cho học tập,
                        công việc và cuộc sống mỗi ngày.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="/products.php" class="btn btn-light btn-lg px-4">
                            Khám phá sản phẩm
                        </a>
                        <a href="/products.php" class="btn btn-hero-outline btn-lg px-4">
                            Xem sản phẩm
                        </a>
                    </div>
                    <div class="hero-highlights mt-4">
                        <span>✓ Sản phẩm rõ nguồn gốc</span>
                        <span>✓ Đặt hàng nhanh gọn</span>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="hero-showcase" aria-label="Ưu đãi mua sắm">
                        <div class="hero-orbit hero-orbit-one"></div>
                        <div class="hero-orbit hero-orbit-two"></div>
                        <div class="hero-product-card">
                            <span class="hero-product-icon">✦</span>
                            <span class="hero-product-label">GỢI Ý HÔM NAY</span>
                            <strong>Thiết bị tốt<br>cho ngày năng động</strong>
                            <a href="/products.php" class="hero-product-link">
                                Tìm sản phẩm phù hợp <span aria-hidden="true">→</span>
                            </a>
                        </div>
                        <span class="hero-floating-tag">Mua sắm an tâm</span>
                        <span class="hero-floating-dot" aria-hidden="true"></span>
                    </div>
                </div>
            </div>
        </section>

        <section class="benefit-strip mb-5" aria-label="Lợi ích mua sắm">
            <div class="benefit-item">
                <span class="benefit-icon">01</span>
                <div><strong>Chọn mua dễ dàng</strong><span>Tìm kiếm và lọc sản phẩm nhanh</span></div>
            </div>
            <div class="benefit-item">
                <span class="benefit-icon">02</span>
                <div><strong>Thông tin minh bạch</strong><span>Giá bán và tồn kho rõ ràng</span></div>
            </div>
            <div class="benefit-item">
                <span class="benefit-icon">03</span>
                <div><strong>Đặt hàng tiện lợi</strong><span>Theo dõi thông tin đơn hàng</span></div>
            </div>
        </section>

        <section class="bottom-cta mt-5">
            <div>
                <p class="section-kicker mb-1">BẮT ĐẦU NGAY</p>
                <h2 class="h4 mb-1">Bạn đang tìm món đồ phù hợp?</h2>
                <p class="mb-0 text-muted">Duyệt danh sách sản phẩm và tìm lựa chọn dành riêng cho bạn.</p>
            </div>
            <a href="/products.php" class="btn btn-store-primary">Mua sắm ngay <span aria-hidden="true">→</span></a>
        </section>
    </div>
</main>

<?php
require_once '/var/www/src/includes/frontend/footer.php';