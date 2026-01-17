<?php
require_once '../db.php';
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Raporty</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Dostępne Raporty</h1>
    </header>
    <nav>
        <ul>
            <li><a href="../index.php">Strona główna</a></li>
            <li><a href="../zawodnicy/">Zawodnicy</a></li>
            <li><a href="../zespoly/">Zespoły</a></li>
            <li><a href="../mecze/">Mecze</a></li>
            <li><a href="index.php">Raporty</a></li>
            <li><a href="../areny/">Areny</a></li>
            <li><a href="../sezony/">Sezony</a></li>
            <li><a href="../trener/">Trenerzy</a></li>
            <li><a href="../kontrakt/">Kontrakty</a></li>
            <li><a href="../kontuzja/">Kontuzje</a></li>
            <li><a href="../nagroda/">Nagrody</a></li>
            <li><a href="../tabela_ligowa/">Tabela Ligowa</a></li>
        </ul>
    </nav>
    <main>
        <h2>Wybierz raport do wygenerowania:</h2>
        <ul>
            <li><a href="raport_srednie_statystyki.php">Średnie statystyki zawodników</a></li>
            <li><a href="raport_tabela_ligowa.php">Tabela ligowa dla sezonu</a></li>
            <li><a href="raport_najwiecej_punktow.php">Zawodnik z największą liczbą punktów w jednym meczu</a></li>
        </ul>
    </main>
    <footer>
        <p>Projekt bazy danych - 2024</p>
    </footer>
</body>
</html>
