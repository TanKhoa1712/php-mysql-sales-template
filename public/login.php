<?php

require_once '/var/www/src/config/session.php';
require_once '/var/www/src/config/database.php';

$pageTitle = 'Đăng nhập';

if (isset($_SESSION['customer_id'])) {
    header('Location: /');
    exit;
}

$email = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email =
        trim($_POST['email'] ?? '');

    $password =
        $_POST['password'] ?? '';

    if ($email === '' || $password === '') {

        $errorMessage =
            'Vui lòng nhập email và mật khẩu.';

    } elseif (!filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    )) {

        $errorMessage =
            'Email không hợp lệ.';

    } else {

        $sql = "
            SELECT
                CustomerID,
                CustomerName,
                Email,
                PasswordHash
            FROM customers
            WHERE Email = ?
        ";

        $stmt =
            $conn->prepare($sql);

        $stmt->bind_param(
            's',
            $email
        );

        $stmt->execute();

        $result =
            $stmt->get_result();

        $customer =
            $result->fetch_assoc();

        $result->free();
        $stmt->close();

        if ($customer
            && !empty($customer['PasswordHash'])
            && password_verify(
                $password,
                $customer['PasswordHash']
            )) {

            session_regenerate_id(true);

            $_SESSION['customer_id'] =
                (int) $customer['CustomerID'];

            $_SESSION['customer_name'] =
                $customer['CustomerName'];

            header('Location: /');
            exit;

        } else {

            $errorMessage =
                'Email hoặc mật khẩu không đúng.';
        }
    }
}

require_once '/var/www/src/includes/frontend/header.php';
require_once '/var/www/src/includes/frontend/navbar.php';

?>

<main class="store-main">
<div class="container py-4 py-lg-5">

    <div class="row justify-content-center">

        <div class="col-md-8 col-lg-6">
            <div class="store-auth-card">
            <div class="store-auth-form">

            <p class="section-kicker mb-1">CHÀO MỪNG TRỞ LẠI</p>
            <h1 class="h3 fw-bold mb-2">Đăng nhập</h1>
            <p class="text-muted mb-4">Đăng nhập để tiếp tục trải nghiệm mua sắm tại KhoaMart.</p>

            <?php if (isset($_GET['registered'])): ?>

                <div class="alert alert-success">
                    Đăng ký thành công.
                    Bạn có thể đăng nhập.
                </div>

            <?php endif; ?>

            <?php if ($errorMessage !== ''): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>

            <?php endif; ?>

            <form method="post">

                <div class="mb-3">

                    <label
                        for="email"
                        class="form-label"
                    >
                        Email
                    </label>

                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($email) ?>"
                        required
                    >

                </div>

                <div class="mb-3">

                    <label
                        for="password"
                        class="form-label"
                    >
                        Mật khẩu
                    </label>

                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        required
                    >

                </div>

                <button
                    type="submit"
                    class="btn btn-store-primary w-100 py-2"
                >
                    Đăng nhập
                </button>

            </form>

            <p class="text-center text-muted small mt-4 mb-0">
                Chưa có tài khoản?
                <a class="section-link" href="/register.php">Đăng ký ngay</a>
            </p>

            </div>
            </div>
        </div>

    </div>
    </main>

</div>

<?php

require_once '/var/www/src/includes/frontend/footer.php';

$conn->close();