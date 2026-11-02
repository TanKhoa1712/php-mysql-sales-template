<?php

require_once '/var/www/src/config/session.php';
require_once '/var/www/src/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['update_cart'])) {

        $quantities = $_POST['quantities'] ?? [];

        foreach ($quantities as $productID => $quantity) {

            $productID = (int) $productID;
            $quantity = (int) $quantity;

            if ($productID <= 0) {
                continue;
            }

            if ($quantity <= 0) {
                unset($_SESSION['cart'][$productID]);
                continue;
            }

            $sqlStock = "
                SELECT StockQuantity
                FROM products
                WHERE ProductID = ?
                  AND IsActive = 1
            ";

            $stmtStock = $conn->prepare($sqlStock);
            $stmtStock->bind_param('i', $productID);
            $stmtStock->execute();

            $stockResult = $stmtStock->get_result();
            $stockRow = $stockResult->fetch_assoc();

            $stockResult->free();
            $stmtStock->close();

            if (!$stockRow) {
                unset($_SESSION['cart'][$productID]);
                continue;
            }

            $stockQuantity =
                (int) $stockRow['StockQuantity'];

            if ($stockQuantity <= 0) {
                unset($_SESSION['cart'][$productID]);
                continue;
            }

            $_SESSION['cart'][$productID] =
                min($quantity, $stockQuantity);
        }

        header('Location: /cart.php');
        exit;
    }

    if (isset($_POST['remove_product'])) {

        $productID =
            (int) $_POST['remove_product'];

        if ($productID > 0) {
            unset($_SESSION['cart'][$productID]);
        }

        header('Location: /cart.php');
        exit;
    }
}

$cart = $_SESSION['cart'] ?? [];

$cartItems = [];
$totalAmount = 0;

if (!empty($cart)) {

    foreach ($cart as $productID => $quantity) {

        $sql = "
            SELECT
                p.ProductID,
                p.ProductCode,
                p.ProductName,
                p.Price,
                p.StockQuantity,

                (
                    SELECT pi.ImageFile
                    FROM product_images pi
                    WHERE pi.ProductID = p.ProductID
                      AND pi.IsPrimary = 1
                    LIMIT 1
                ) AS ImageFile

            FROM products p

            WHERE
                p.ProductID = ?
                AND p.IsActive = 1
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $productID);
        $stmt->execute();

        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        $result->free();
        $stmt->close();

        if ($product) {

            $quantity = (int) $quantity;

            $subtotal =
                (float) $product['Price'] * $quantity;

            $product['Quantity'] = $quantity;
            $product['Subtotal'] = $subtotal;

            $cartItems[] = $product;
            $totalAmount += $subtotal;
        }
    }
}

$pageTitle = 'Giỏ hàng';

require_once '/var/www/src/includes/frontend/header.php';
require_once '/var/www/src/includes/frontend/navbar.php';

?>

<main class="store-main">
    <div class="container py-4 py-lg-5">

    <div class="store-page-heading">
        <div>
            <p class="section-kicker mb-1">KIỂM TRA LỰA CHỌN</p>
            <h1 class="h2 mb-2">Giỏ hàng của bạn</h1>
            <p class="text-muted mb-0">Xem lại sản phẩm và số lượng trước khi đặt hàng.</p>
        </div>
    </div>

    <?php if (empty($cartItems)): ?>

        <div class="store-empty">
            <div class="store-empty-mark" aria-hidden="true">▱</div>
            <h2 class="h5">Giỏ hàng đang trống</h2>
            <p class="text-muted mb-3">Hãy khám phá sản phẩm và thêm món bạn yêu thích vào giỏ.</p>
            <a href="/products.php" class="btn btn-store-primary px-4">Khám phá sản phẩm</a>
        </div>

    <?php else: ?>

        <form method="post" action="/cart.php">

            <div class="store-panel p-0 mb-4">
            <div class="table-responsive">

                <table class="table store-table align-middle">

                    <thead>

                        <tr>
                            <th>Sản phẩm</th>
                            <th class="text-end">Đơn giá</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Thành tiền</th>
                            <th class="text-center">Thao tác</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($cartItems as $item): ?>

                            <tr>

                                <td>

                                    <div
                                        class="d-flex
                                               align-items-center
                                               gap-3"
                                    >

                                        <?php if (!empty($item['ImageFile'])): ?>

                                            <img
                                                src="/uploads/products/<?=
                                                    htmlspecialchars(
                                                        $item['ImageFile']
                                                    )
                                                ?>"
                                                alt="<?=
                                                    htmlspecialchars(
                                                        $item['ProductName']
                                                    )
                                                ?>"
                                                class="store-cart-image"
                                                loading="lazy"
                                                onerror="this.remove();"
                                            >

                                        <?php endif; ?>

                                        <div>

                                            <strong>
                                                <?=
                                                    htmlspecialchars(
                                                        $item['ProductName']
                                                    )
                                                ?>
                                            </strong>

                                            <div class="text-muted small">
                                                <?=
                                                    htmlspecialchars(
                                                        $item['ProductCode']
                                                    )
                                                ?>
                                            </div>

                                        </div>

                                    </div>

                                </td>

                                <td class="text-end">

                                    <?=
                                        number_format(
                                            (float) $item['Price'],
                                            0,
                                            ',',
                                            '.'
                                        )
                                    ?> đ

                                </td>

                                <td class="text-center">

                                    <input
                                        type="number"
                                        name="quantities[<?=
                                            (int) $item['ProductID']
                                        ?>]"
                                        value="<?=
                                            (int) $item['Quantity']
                                        ?>"
                                        min="1"
                                        max="<?=
                                            (int) $item['StockQuantity']
                                        ?>"
                                        class="form-control mx-auto"
                                        style="width: 90px;"
                                    >

                                </td>

                                <td class="text-end fw-bold">

                                    <?=
                                        number_format(
                                            (float) $item['Subtotal'],
                                            0,
                                            ',',
                                            '.'
                                        )
                                    ?> đ

                                </td>

                                <td class="text-center">

                                    <button
                                        type="submit"
                                        name="remove_product"
                                        value="<?=
                                            (int) $item['ProductID']
                                        ?>"
                                        class="btn btn-sm btn-outline-danger"
                                        formnovalidate
                                    >
                                        Xóa
                                    </button>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                    <tfoot>

                        <tr>

                            <th
                                colspan="3"
                                class="text-end"
                            >
                                Tổng cộng
                            </th>

                            <th class="text-end fs-5">

                                <?=
                                    number_format(
                                        $totalAmount,
                                        0,
                                        ',',
                                        '.'
                                    )
                                ?> đ

                            </th>

                            <th></th>

                        </tr>

                    </tfoot>

                </table>

            </div>
            </div>

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-3">

                <a
                    href="/products.php"
                    class="btn btn-outline-secondary"
                >
                    Tiếp tục mua hàng
                </a>

                <div class="d-flex gap-2">
                    <button
                        type="submit"
                        name="update_cart"
                        class="btn btn-outline-success"
                    >
                        Cập nhật giỏ hàng
                    </button>

                    <a
                        href="/checkout.php"
                        class="btn btn-store-primary"
                    >
                        Tiến hành đặt hàng
                    </a>
                </div>

            </div>

        </form>

    <?php endif; ?>

    </div>
</main>

<?php

require_once '/var/www/src/includes/frontend/footer.php';