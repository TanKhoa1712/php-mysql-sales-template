<?php
require_once '/var/www/src/config/session.php';
require_once '/var/www/src/config/database.php';

$productID = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;

if ($productID <= 0) {
    header('Location: /products.php');
    exit;
}

$cartMessage = '';

$sql = "
    SELECT
        p.ProductID,
        p.ProductCode,
        p.ProductName,
        p.Description,
        p.Unit,
        p.Price,
        p.StockQuantity,
        c.CategoryName,
        s.SupplierName

    FROM
        products p,
        categories c,
        suppliers s

    WHERE
        p.CategoryID = c.CategoryID
        AND p.SupplierID = s.SupplierID
        AND p.ProductID = ?
        AND p.IsActive = 1
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    'i',
    $productID
);

$stmt->execute();

$result = $stmt->get_result();

$product = $result->fetch_assoc();

$result->free();
$stmt->close();

if (!$product) {
    header('Location: /products.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['add_to_cart'])) {

    $quantity = isset($_POST['quantity'])
        ? (int) $_POST['quantity']
        : 1;

    if ($quantity < 1) {
        $quantity = 1;
    }

    $stockQuantity = (int) $product['StockQuantity'];

    if ($stockQuantity <= 0) {

        $cartMessage = 'Sản phẩm hiện đã hết hàng.';

    } else {

        $currentQuantity =
            $_SESSION['cart'][$productID] ?? 0;

        $newQuantity =
            $currentQuantity + $quantity;

        if ($newQuantity > $stockQuantity) {
            $newQuantity = $stockQuantity;
        }

        $_SESSION['cart'][$productID] =
            $newQuantity;

        $cartMessage =
            'Đã thêm sản phẩm vào giỏ hàng.';
    }
}

$sqlImages = "
    SELECT
        ProductImageID,
        ImageFile,
        IsPrimary,
        SortOrder

    FROM
        product_images

    WHERE
        ProductID = ?

    ORDER BY
        IsPrimary DESC,
        SortOrder ASC,
        ProductImageID ASC
";

$stmtImages = $conn->prepare($sqlImages);

$stmtImages->bind_param(
    'i',
    $productID
);

$stmtImages->execute();

$imageResult = $stmtImages->get_result();

$images = [];

while ($image = $imageResult->fetch_assoc()) {
    $images[] = $image;
}

$imageResult->free();
$stmtImages->close();
$pageTitle = $product['ProductName'];

require_once '/var/www/src/includes/frontend/header.php';
require_once '/var/www/src/includes/frontend/navbar.php';
?>
<main class="store-main">
    <div class="container py-4 py-lg-5">

    <div class="mb-4">

        <a
            href="/products.php"
            class="section-link"
        >
            <span aria-hidden="true">←</span> Quay lại danh sách sản phẩm
        </a>

    </div>

    <div class="row g-5">

        <div class="col-lg-6">

            <?php if (!empty($images)): ?>

                <div class="store-details-image mb-3">
                    <span class="product-image-placeholder">Chưa có hình ảnh</span>

                    <img
                        src="/uploads/products/<?=
                            htmlspecialchars(
                                $images[0]['ImageFile']
                            )
                        ?>"
                        onerror="this.parentElement.classList.add('is-image-missing'); this.remove();"
                        alt="<?=
                            htmlspecialchars(
                                $product['ProductName']
                            )
                        ?>"
                    >

                </div>

                <?php if (count($images) > 1): ?>

                    <div class="row g-2">

                        <?php foreach ($images as $image): ?>

                            <div class="col-4">

                                <div
                                    class="border rounded
                                           bg-light
                                           d-flex
                                           align-items-center
                                           justify-content-center
                                           p-2"
                                    style="height: 120px;"
                                >

                                    <img
                                        src="/uploads/products/<?=
                                            htmlspecialchars(
                                                $image['ImageFile']
                                            )
                                        ?>"
                                        alt="<?=
                                            htmlspecialchars(
                                                $product['ProductName']
                                            )
                                        ?>"
                                        loading="lazy"
                                        onerror="this.closest('.col-4').remove();"
                                        style="width: 100%; height: 100%; object-fit: contain;"
                                    >

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            <?php else: ?>

                <div
                    class="store-details-image is-image-missing
                           bg-light d-flex
                           align-items-center
                           justify-content-center
                           text-muted"
                    style="min-height: 400px;"
                >
                    <span class="product-image-placeholder">Chưa có hình ảnh</span>
                </div>

            <?php endif; ?>

        </div>

        <div class="col-lg-6">
            <div class="store-detail-content h-100">

            <p class="section-kicker mb-2">
                <?=
                    htmlspecialchars(
                        $product['CategoryName']
                    )
                ?>
            </p>

            <h1 class="h2 fw-bold mb-3">
                <?=
                    htmlspecialchars(
                        $product['ProductName']
                    )
                ?>
            </h1>

            <p class="text-muted">
                Mã sản phẩm:
                <?=
                    htmlspecialchars(
                        $product['ProductCode']
                    )
                ?>
            </p>

            <p class="store-detail-price mb-3">
                <?=
                    number_format(
                        (float) $product['Price'],
                        0,
                        ',',
                        '.'
                    )
                ?> đ
            </p>

            <hr class="my-4">

            <dl class="row">

                <dt class="col-sm-4 text-muted fw-normal">
                    Danh mục
                </dt>

                <dd class="col-sm-8">
                    <?=
                        htmlspecialchars(
                            $product['CategoryName']
                        )
                    ?>
                </dd>

                <dt class="col-sm-4 text-muted fw-normal">
                    Nhà cung cấp
                </dt>

                <dd class="col-sm-8">
                    <?=
                        htmlspecialchars(
                            $product['SupplierName']
                        )
                    ?>
                </dd>

                <dt class="col-sm-4 text-muted fw-normal">
                    Đơn vị tính
                </dt>

                <dd class="col-sm-8">
                    <?=
                        htmlspecialchars(
                            $product['Unit'] ?? ''
                        )
                    ?>
                </dd>

                <dt class="col-sm-4 text-muted fw-normal">
                    Tồn kho
                </dt>

                <dd class="col-sm-8">
                    <span class="store-stock">
                        <?= (int) $product['StockQuantity'] > 0
                            ? 'Còn ' . (int) $product['StockQuantity'] . ' sản phẩm'
                            : 'Tạm hết hàng' ?>
                    </span>
                </dd>

            </dl>
            <?php if ($cartMessage !== ''): ?>

                <div class="alert alert-info">
                    <?= htmlspecialchars($cartMessage) ?>
                </div>

            <?php endif; ?>

            <?php if ((int) $product['StockQuantity'] > 0): ?>

                <form method="post" class="mb-4">

                    <div class="row g-3 align-items-end">

                        <div class="col-auto">

                            <label
                                for="quantity"
                                class="form-label"
                            >
                                Số lượng
                            </label>

                            <input
                                type="number"
                                name="quantity"
                                id="quantity"
                                class="form-control"
                                value="1"
                                min="1"
                                max="<?= (int) $product['StockQuantity'] ?>"
                                style="width: 105px;"
                            >

                        </div>

                        <div class="col-auto">

                            <button
                                type="submit"
                                name="add_to_cart"
                                class="btn btn-store-primary px-4"
                            >
                                Thêm vào giỏ hàng
                            </button>

                        </div>

                    </div>

                </form>

            <?php else: ?>

                <div class="alert alert-warning">
                    Sản phẩm hiện đã hết hàng.
                </div>

            <?php endif; ?>
            <?php if (!empty($product['Description'])): ?>

                <hr>

                <h2 class="h5 fw-bold">Mô tả sản phẩm</h2>

                <p>
                    <?=
                        nl2br(
                            htmlspecialchars(
                                $product['Description']
                            )
                        )
                    ?>
                </p>

            <?php endif; ?>

            </div>
        </div>

    </div>

    </div>
</main>

<?php
require_once '/var/www/src/includes/frontend/footer.php';