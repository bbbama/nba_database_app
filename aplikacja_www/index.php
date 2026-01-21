<?php
require_once 'db.php';

$pageTitle = 'Strona główna';
$basePath = './';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2>Witaj w aplikacji!</h2>
    <p>Wybierz jedną z opcji w menu, aby rozpocząć zarządzanie danymi.</p>
<?php require_once $basePath . 'layout/footer.php'; ?>