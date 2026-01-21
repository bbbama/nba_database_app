<?php
require_once '../db.php';

$pageTitle = 'Raporty';
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2>Wybierz raport do wygenerowania:</h2>
    <ul>
        <li><a href="raport_srednie_statystyki.php">Średnie statystyki zawodników</a></li>
        <li><a href="raport_tabela_ligowa.php">Tabela ligowa dla sezonu</a></li>
        <li><a href="raport_najwiecej_punktow.php">Zawodnik z największą liczbą punktów w jednym meczu</a></li>
    </ul>
<?php require_once $basePath . 'layout/footer.php'; ?>