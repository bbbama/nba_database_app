<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';
require_once $basePath . 'db.php';

try {
    $pdo = getDbConnection();
    $stmt = $pdo->query('SELECT * FROM widok_liderzy_statystyk ORDER BY srednie_punkty DESC');
    $liderzy = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd przy pobieraniu danych raportu: " . $e->getMessage());
}

$pageTitle = 'Raport - Liderzy Statystyk';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2>Raport - Liderzy Statystyk</h2>
    <p>Ten raport pokazuje zawodników, którzy zagrali w co najmniej 5 meczach i mają średnią powyżej 10 punktów na mecz.</p>

    <?php if (!empty($liderzy)): ?>
        <table>
            <thead>
                <tr>
                    <th>Zawodnik</th>
                    <th>Zespół</th>
                    <th>Liczba Meczów</th>
                    <th>Średnie Punkty</th>
                    <th>Średnie Asysty</th>
                    <th>Średnie Zbiórki</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($liderzy as $lider): ?>
                    <tr>
                        <td><?= htmlspecialchars($lider['zawodnik_nazwa']) ?></td>
                        <td><?= htmlspecialchars($lider['nazwa_zespolu']) ?></td>
                        <td><?= htmlspecialchars($lider['liczba_meczow']) ?></td>
                        <td><?= htmlspecialchars($lider['srednie_punkty']) ?></td>
                        <td><?= htmlspecialchars($lider['srednie_asysty']) ?></td>
                        <td><?= htmlspecialchars($lider['srednie_zbiorki']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Brak zawodników spełniających kryteria raportu.</p>
    <?php endif; ?>

    <p><a href="index.php" class="button">Powrót do listy raportów</a></p>
</main>

<?php require_once $basePath . 'layout/footer.php'; ?>
