<?php

require_once '/var/www/src/config/database.php';

$sqlCategories = "
    SELECT
        CategoryID,
        CategoryName
    FROM categories
    ORDER BY CategoryName
";

$categoryResult = $conn->query($sqlCategories);

$categories = [];

while ($category = $categoryResult->fetch_assoc()) {
    $categories[] = $category;
}

$categoryResult->free();

$categoryID = isset($_GET['category'])
    ? (int) $_GET['category']
    : 0;

$keyword = isset($_GET['keyword'])
    ? trim((string) $_GET['keyword'])
    : '';

$itemsPerPage = 6;

$page = isset($_GET['page'])
    ? (int) $_GET['page']
    : 1;

if ($page < 1) {
    $page = 1;
}

$searchKeyword = '%' . $keyword . '%';

$sqlCount = "
    SELECT
        COUNT(*) AS TotalProducts
    FROM
        products p,
        categories c
    WHERE
        p.CategoryID = c.CategoryID
        AND p.IsActive = 1
";

$paramsCount = [];
$typesCount = '';

if ($categoryID > 0) {
    $sqlCount .= "
        AND p.CategoryID = ?
    ";
    $typesCount .= 'i';
    $paramsCount[] = $categoryID;
}

if ($keyword !== '') {
    $sqlCount .= "
        AND (
            p.ProductName LIKE ?
            OR p.ProductCode LIKE ?
        )
    ";
    $typesCount .= 'ss';
    $paramsCount[] = $searchKeyword;
    $paramsCount[] = $searchKeyword;
}

$stmtCount = $conn->prepare($sqlCount);

if ($paramsCount !== []) {
    $stmtCount->bind_param($typesCount, ...$paramsCount);
}

$stmtCount->execute();

$countResult = $stmtCount->get_result();
$countRow = $countResult->fetch_assoc();

$totalProducts = (int) ($countRow['TotalProducts'] ?? 0);

$countResult->free();
$stmtCount->close();

$totalPages = (int) ceil($totalProducts / $itemsPerPage);
if ($totalPages < 1) {
    $totalPages = 1;
}

if ($page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $itemsPerPage;

$sql = "
    SELECT
        p.ProductID,
        p.ProductCode,
        p.ProductName,
        p.Price,
        c.CategoryName,

        (
            SELECT pi.ImageFile
            FROM product_images pi
            WHERE pi.ProductID = p.ProductID
              AND pi.IsPrimary = 1
            LIMIT 1
        ) AS ImageFile

    FROM
        products p,
        categories c

    WHERE
        p.CategoryID = c.CategoryID
        AND p.IsActive = 1
";

$params = [];
$types = '';

if ($categoryID > 0) {
    $sql .= "
        AND p.CategoryID = ?
    ";
    $types .= 'i';
    $params[] = $categoryID;
}

if ($keyword !== '') {
    $sql .= "
        AND (
            p.ProductName LIKE ?
            OR p.ProductCode LIKE ?
        )
    ";
    $types .= 'ss';
    $params[] = $searchKeyword;
    $params[] = $searchKeyword;
}

$sql .= "
    ORDER BY
        p.ProductID DESC
    LIMIT ?
    OFFSET ?
";

$types .= 'ii';
$params[] = $itemsPerPage;
$params[] = $offset;

$stmt = $conn->prepare($sql);

if ($params !== []) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$result = $stmt->get_result();

$pageTitle = 'Sản phẩm';

require_once '/var/www/src/includes/frontend/header.php';
require_once '/var/www/src/includes/frontend/navbar.php';
?>

<main class="store-main">
    <div class="container py-4 py-lg-5">
        <div class="store-page-heading">
            <div>
                <p class="section-kicker mb-1">KHÁM PHÁ KHO HÀNG</p>
                <h1 class="h2 mb-2">Tất cả sản phẩm</h1>
                <p class="text-muted mb-0">
                    Tìm lựa chọn phù hợp từ các danh mục đang có tại cửa hàng.
                </p>
            </div>
        </div>

    <form
        method="get"
        action="/products.php"
        class="store-panel row g-3 align-items-end mb-4"
    >

        <div class="col-md-6 col-lg-4">

            <label
                for="keyword"
                class="form-label"
            >
                Tìm sản phẩm
            </label>

            <input
                type="text"
                name="keyword"
                id="keyword"
                class="form-control"
                value="<?= htmlspecialchars($keyword) ?>"
                placeholder="Nhập tên hoặc mã sản phẩm"
            >

        </div>

        <div class="col-md-6 col-lg-4">

            <label
                for="category"
                class="form-label"
            >
                Danh mục
            </label>

            <select
                name="category"
                id="category"
                class="form-select"
            >

                <option value="0">
                    Tất cả danh mục
                </option>

                <?php foreach ($categories as $category): ?>

                    <option
                        value="<?= (int) $category['CategoryID'] ?>"
                        <?=
                            $categoryID ===
                            (int) $category['CategoryID']
                                ? 'selected'
                                : ''
                        ?>
                    >
                        <?=
                            htmlspecialchars(
                                $category['CategoryName']
                            )
                        ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <div class="col-md-auto">

            <button
                type="submit"
                class="btn btn-store-primary px-4"
            >
                Tìm kiếm
            </button>

            <a
                href="/products.php"
                class="btn btn-outline-secondary"
            >
                Xóa bộ lọc
            </a>

        </div>

    </form>

    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="text-muted small mb-0">
            Tìm thấy <strong><?= $totalProducts ?></strong> sản phẩm
            <?= $keyword !== '' ? 'cho “' . htmlspecialchars($keyword) . '”' : '' ?>.
        </p>
    </div>

    <?php if ($result->num_rows > 0): ?>

        <div class="row g-3 g-lg-4">

            <?php while ($product = $result->fetch_assoc()): ?>

                <div class="col-6 col-lg-4">

                    <article class="card product-card h-100">

                        <?php if (!empty($product['ImageFile'])): ?>

                            <a class="product-image-wrap" href="/product-detail.php?id=<?= (int) $product['ProductID'] ?>">

                                <img
                                    src="/uploads/products/<?=
                                        htmlspecialchars(
                                            $product['ImageFile']
                                        )
                                    ?>"
                                    alt="<?=
                                        htmlspecialchars(
                                            $product['ProductName']
                                        )
                                    ?>"
                                    loading="lazy"
                                    onerror="this.parentElement.classList.add('is-image-missing'); this.remove();"
                                >
                                <span class="product-image-placeholder">Chưa có hình ảnh</span>
                                <span class="product-image-badge">Sản phẩm</span>

                            </a>

                        <?php else: ?>

                            <a class="product-image-wrap is-image-missing" href="/product-detail.php?id=<?= (int) $product['ProductID'] ?>">
                                <span class="product-image-placeholder">Chưa có hình ảnh</span>
                            </a>

                        <?php endif; ?>

                        <div class="card-body d-flex flex-column">

                            <span class="product-category">
                                <?=
                                    htmlspecialchars(
                                        $product['CategoryName']
                                    )
                                ?>
                            </span>

                            <h2 class="product-name">
                                <a href="/product-detail.php?id=<?= (int) $product['ProductID'] ?>">
                                <?=
                                    htmlspecialchars(
                                        $product['ProductName']
                                    )
                                ?>
                                </a>
                            </h2>

                            <span class="product-code">
                                Mã
                                <?=
                                    htmlspecialchars(
                                        $product['ProductCode']
                                    )
                                ?>
                            </span>

                            <div class="mt-auto pt-3 d-flex align-items-center justify-content-between gap-2">
                                <strong class="product-price">
                                <?=
                                    number_format(
                                        (float) $product['Price'],
                                        0,
                                        ',',
                                        '.'
                                    )
                                ?> đ
                                </strong>
                                <a
                                    href="/product-detail.php?id=<?= (int) $product['ProductID'] ?>"
                                    class="product-quick-link"
                                    aria-label="Xem chi tiết <?= htmlspecialchars($product['ProductName']) ?>"
                                >+</a>
                            </div>

                        </div>

                    </article>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="store-empty">
            <div class="store-empty-mark" aria-hidden="true">⌕</div>
            <h2 class="h5">Chưa tìm thấy sản phẩm phù hợp</h2>
            <p class="text-muted mb-3">
                Thử từ khóa khác hoặc xóa bộ lọc để xem toàn bộ sản phẩm.
            </p>
            <a href="/products.php" class="btn btn-store-primary px-4">Xem tất cả sản phẩm</a>
        </div>

    <?php endif; ?>

    <?php if ($totalPages > 1): ?>

        <nav
            class="mt-5"
            aria-label="Phân trang sản phẩm"
        >

            <ul
                class="pagination
                       justify-content-center
                       flex-wrap"
            >

                <?php
                $previousQuery = http_build_query([
                    'keyword' => $keyword,
                    'category' => $categoryID,
                    'page' => max(1, $page - 1)
                ]);
                ?>

                <li
                    class="page-item <?=
                        $page <= 1
                            ? 'disabled'
                            : ''
                    ?>"
                >
                    <a
                        class="page-link"
                        href="/products.php?<?= $previousQuery ?>"
                    >
                        &laquo; Trước
                    </a>
                </li>

                <?php for (
                    $pageNumber = 1;
                    $pageNumber <= $totalPages;
                    $pageNumber++
                ): ?>

                    <?php
                    $query = http_build_query([
                        'keyword' => $keyword,
                        'category' => $categoryID,
                        'page' => $pageNumber
                    ]);
                    ?>

                    <li
                        class="page-item <?=
                            $pageNumber === $page
                                ? 'active'
                                : ''
                        ?>"
                    >
                        <a
                            class="page-link"
                            href="/products.php?<?= $query ?>"
                        >
                            <?= $pageNumber ?>
                        </a>
                    </li>

                <?php endfor; ?>

                <?php
                $nextQuery = http_build_query([
                    'keyword' => $keyword,
                    'category' => $categoryID,
                    'page' => min($totalPages, $page + 1)
                ]);
                ?>

                <li
                    class="page-item <?=
                        $page >= $totalPages
                            ? 'disabled'
                            : ''
                    ?>"
                >
                    <a
                        class="page-link"
                        href="/products.php?<?= $nextQuery ?>"
                    >
                        Sau &raquo;
                    </a>
                </li>

            </ul>

        </nav>

    <?php endif; ?>

    </div>
</main>

<?php
$result->free();
$stmt->close();

require_once '/var/www/src/includes/frontend/footer.php';