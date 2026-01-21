<?php
require_once '../db.php';

$pdo = getDbConnection();

$player_record = null;
try {
    $sql = "SELECT
                s.punkty,
                z.imie,
                z.nazwisko,
                m.data_meczu,
                zes.nazwa AS zespol_zawodnika
            FROM statystyki_meczu s
            JOIN zawodnik z ON s.id_zawodnika = z.id_zawodnika
            JOIN mecz m ON s.id_meczu = m.id_meczu
            JOIN zespol zes ON z.id_zespolu = zes.id_zespolu
            ORDER BY s.punkty DESC
            LIMIT 1";
    $stmt = $pdo->query($sql);
    $player_record = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Błąd odczytu danych: " . $e->getMessage();
}

$pageTitle = 'Zawodnik z największą liczbą punktów w jednym meczu';
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2>Zawodnik z największą liczbą punktów w pojedynczym meczu</h2>

    <?php if ($player_record): ?>
        <p><strong>Zawodnik:</strong> <?= htmlspecialchars($player_record['imie'] . ' ' . $player_record['nazwisko']) ?></p>
        <p><strong>Zespół:</strong> <?= htmlspecialchars($player_record['zespol_zawodnika']) ?></p>
        <p><strong>Data meczu:</strong> <?= htmlspecialchars($player_record['data_meczu']) ?></p>
        <p><strong>Punkty:</strong> <?= htmlspecialchars($player_record['punkty']) ?></p>
    <?php else: ?>
        <p>Brak danych o zawodniku z największą liczbą punktów w pojedynczym meczu.</p>
    <?php endif; ?>

    <a href="index.php">Powrót do listy raportów</a>
<?php require_once $basePath . 'layout/footer.php'; ?>