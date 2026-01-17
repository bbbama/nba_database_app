<?php
require_once '../db.php';

$sql = "SELECT * FROM widok_srednie_statystyki_zawodnika ORDER BY srednie_punkty DESC";
$stmt = $pdo->query($sql);
$statystyki = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Średnie statystyki zawodników</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Raport: Średnie statystyki zawodników</h1>
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
        </ul>
    </nav>
    <main>
        <h2>Średnie statystyki zawodników</h2>
        <table>
            <thead>
                <tr>
                    <th>Zawodnik</th>
                    <th>Średnie punkty</th>
                    <th>Średnie asysty</th>
                    <th>Średnie zbiórki</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($statystyki as $stat): ?>
                <tr>
                    <td><?= htmlspecialchars($stat['zawodnik_nazwa']) ?></td>
                    <td><?= htmlspecialchars($stat['srednie_punkty']) ?></td>
                    <td><?= htmlspecialchars($stat['srednie_asysty']) ?></td>
                    <td><?= htmlspecialchars($stat['srednie_zbiorki']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="index.php">Powrót do listy raportów</a>
    </main>
    <footer>
        <p>Projekt bazy danych - 2024</p>
    </footer>
</body>
</html>
