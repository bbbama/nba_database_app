<?php
require_once '../db.php';

$pdo = getDbConnection();

$sql = "SELECT * FROM widok_srednie_statystyki_zawodnika ORDER BY srednie_punkty DESC";
$stmt = $pdo->query($sql);
$statystyki = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Średnie statystyki zawodników';
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2>Średnie statystyki zawodników</h2>
    <table>
        <thead>
            <tr>
                <th>Zawodnik</th>
                <th>Zespół</th>
                <th>Liczba Meczów</th>
                <th>Średnie punkty</th>
                <th>Średnie asysty</th>
                <th>Średnie zbiórki</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($statystyki as $stat): ?>
            <tr>
                <td><?= htmlspecialchars($stat['zawodnik_nazwa']) ?></td>
                <td><?= htmlspecialchars($stat['nazwa_zespolu']) ?></td>
                <td><?= htmlspecialchars($stat['liczba_meczow']) ?></td>
                <td><?= htmlspecialchars($stat['srednie_punkty']) ?></td>
                <td><?= htmlspecialchars($stat['srednie_asysty']) ?></td>
                <td><?= htmlspecialchars($stat['srednie_zbiorki']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="index.php">Powrót do listy raportów</a>
<?php require_once $basePath . 'layout/footer.php'; ?>