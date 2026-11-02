<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="theme-color" content="#126b55">
    <title><?= htmlspecialchars($pageTitle ?? 'Quản trị hệ thống') ?> | KhoaMart</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>

<body class="admin-ui">
<div class="admin-context-bar">
    <div class="container d-flex align-items-center justify-content-between gap-3">
        <span>KHU VỰC QUẢN TRỊ</span>
        <span><?= htmlspecialchars($pageTitle ?? 'Quản trị hệ thống') ?></span>
    </div>
</div>