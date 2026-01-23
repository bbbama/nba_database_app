<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';
require_once $basePath . 'db.php';

// Sprawdzenie, czy podano ID zawodnika
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}
$id_zawodnika = $_GET['id'];

$pdo = getDbConnection();

// Pobranie danych zawodnika
try {
    $stmt_zawodnik = $pdo->prepare("SELECT imie, nazwisko FROM zawodnik WHERE id_zawodnika = ?");
    $stmt_zawodnik->execute([$id_zawodnika]);
    $zawodnik = $stmt_zawodnik->fetch(PDO::FETCH_ASSOC);

    if (!$zawodnik) {
        die("Nie znaleziono zawodnika o podanym ID.");
    }
} catch (PDOException $e) {
    die("Błąd przy pobieraniu danych zawodnika: " . $e->getMessage());
}

// Pobranie statystyk meczowych dla zawodnika
try {
    $sql = "
        SELECT
            m.data_meczu,
            gosp.nazwa AS nazwa_gospodarza,
            gosc.nazwa AS nazwa_goscia,
            sm.minuty,
            sm.punkty,
            sm.asysty,
            sm.zbiorki
        FROM statystyki_meczu sm
        JOIN mecz m ON sm.id_meczu = m.id_meczu
        JOIN zespol gosp ON m.id_gospodarza = gosp.id_zespolu
        JOIN zespol gosc ON m.id_goscia = gosc.id_zespolu
        WHERE sm.id_zawodnika = ?
        ORDER BY m.data_meczu DESC;
    ";
    $stmt_statystyki = $pdo->prepare($sql);
    $stmt_statystyki->execute([$id_zawodnika]);
    $statystyki = $stmt_statystyki->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd podczas pobierania statystyk: " . $e->getMessage());
}

$pageTitle = 'Statystyki Zawodnika';
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2>Statystyki meczowe dla: <strong><?= htmlspecialchars($zawodnik['imie'] . ' ' . $zawodnik['nazwisko']) ?></strong></h2>

    <?php if (empty($statystyki)): ?>
        <p>Ten zawodnik nie ma jeszcze zarejestrowanych żadnych statystyk.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Data Meczu</th>
                    <th>Mecz</th>
                    <th>Minuty</th>
                    <th>Punkty</th>
                    <th>Asysty</th>
                    <th>Zbiórki</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($statystyki as $stat): ?>
                    <tr>
                        <td><?= htmlspecialchars(date('Y-m-d', strtotime($stat['data_meczu']))) ?></td>
                        <td><?= htmlspecialchars($stat['nazwa_gospodarza']) ?> vs <?= htmlspecialchars($stat['nazwa_goscia']) ?></td>
                        <td><?= htmlspecialchars($stat['minuty']) ?></td>
                        <td><?= htmlspecialchars($stat['punkty']) ?></td>
                        <td><?= htmlspecialchars($stat['asysty']) ?></td>
                        <td><?= htmlspecialchars($stat['zbiorki']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <a href="index.php" class="button">Powrót do listy zawodników</a>
</main>

<?php require_once $basePath . 'layout/footer.php'; ?>
