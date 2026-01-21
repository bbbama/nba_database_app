<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - System NBA' : 'System NBA' ?></title>
    <link rel="stylesheet" href="<?= $basePath ?? '' ?>style.css">
</head>
<body>
<header>
    <h1><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'System Zarządzania Ligą NBA' ?></h1>
</header>
