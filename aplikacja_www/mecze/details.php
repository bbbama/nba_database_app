<?php
$basePath = '../';
require_once $basePath . 'auth_check.php';
require_once $basePath . 'db.php';

$isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

$id_meczu = $_GET['id'] ?? null;

if (!$id_meczu) {
    header('Location: index.php'); // Przekieruj na listę meczów, jeśli brak ID
    exit;
}

try {
    $pdo = getDbConnection();

    // Pobierz podstawowe dane meczu
    $stmt_mecz = $pdo->prepare('SELECT m.id_meczu, m.data_meczu, s.rok_rozpoczecia, s.rok_zakonczenia,
                                       zg.nazwa AS nazwa_gospodarza, zg.miasto AS miasto_gospodarza,
                                       zz.nazwa AS nazwa_goscia, zz.miasto AS miasto_goscia,
                                       m.wynik_gospodarza, m.wynik_goscia
                                FROM mecz m
                                JOIN sezon s ON m.id_sezonu = s.id_sezonu
                                JOIN zespol zg ON m.id_gospodarza = zg.id_zespolu
                                JOIN zespol zz ON m.id_goscia = zz.id_zespolu
                                WHERE m.id_meczu = ?');
    $stmt_mecz->execute([$id_meczu]);
    $mecz = $stmt_mecz->fetch(PDO::FETCH_ASSOC);

    if (!$mecz) {
        header('Location: index.php'); // Przekieruj, jeśli mecz nie istnieje
        exit;
    }

    // Pobierz statystyki zawodników dla tego meczu
    $stmt_statystyki = $pdo->prepare('SELECT sm.minuty, sm.punkty, sm.asysty, sm.zbiorki,
                                            z.imie, z.nazwisko, z.pozycja,
                                            zes.nazwa AS nazwa_zespolu_zawodnika
                                     FROM statystyki_meczu sm
                                     JOIN zawodnik z ON sm.id_zawodnika = z.id_zawodnika
                                     JOIN zespol zes ON z.id_zespolu = zes.id_zespolu
                                     WHERE sm.id_meczu = ? ORDER BY zes.nazwa, z.nazwisko');
    $stmt_statystyki->execute([$id_meczu]);
    $statystyki_zawodnikow = $stmt_statystyki->fetchAll(PDO::FETCH_ASSOC);

    // Podziel statystyki na gospodarzy i gości
    $statystyki_gospodarzy = array_filter($statystyki_zawodnikow, function($stat) use ($mecz) {
        return $stat['nazwa_zespolu_zawodnika'] === $mecz['nazwa_gospodarza'];
    });
    $statystyki_gosci = array_filter($statystyki_zawodnikow, function($stat) use ($mecz) {
        return $stat['nazwa_zespolu_zawodnika'] === $mecz['nazwa_goscia'];
    });


} catch (PDOException $e) {
    die("Błąd przy pobieraniu danych meczu: " . $e->getMessage());
}

$pageTitle = 'Szczegóły Meczu: ' . htmlspecialchars($mecz['nazwa_gospodarza']) . ' vs ' . htmlspecialchars($mecz['nazwa_goscia']);
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2><?= htmlspecialchars($mecz['nazwa_gospodarza']) ?> vs <?= htmlspecialchars($mecz['nazwa_goscia']) ?></h2>
    <h3>Wynik: <?= htmlspecialchars($mecz['wynik_gospodarza']) ?> : <?= htmlspecialchars($mecz['wynik_goscia']) ?></h3>

    <section class="match-details">
        <h3>Informacje o meczu</h3>
        <p><strong>Data:</strong> <?= htmlspecialchars($mecz['data_meczu']) ?></p>
        <p><strong>Sezon:</strong> <?= htmlspecialchars($mecz['rok_rozpoczecia'] . '/' . $mecz['rok_zakonczenia']) ?></p>
        <p><strong>Gospodarz:</strong> <a href="../zespoly/details.php?id=<?= htmlspecialchars($mecz['id_gospodarza'] ?? '') ?>"><?= htmlspecialchars($mecz['nazwa_gospodarza'] ?? '') ?></a> (<?= htmlspecialchars($mecz['miasto_gospodarza'] ?? '') ?>)</p>
        <p><strong>Gość:</strong> <a href="../zespoly/details.php?id=<?= htmlspecialchars($mecz['id_goscia'] ?? '') ?>"><?= htmlspecialchars($mecz['nazwa_goscia'] ?? '') ?></a> (<?= htmlspecialchars($mecz['miasto_goscia'] ?? '') ?>)</p>
    </section>

    <section class="match-player-stats">
        <h3>Statystyki Zawodników (<?= htmlspecialchars($mecz['nazwa_gospodarza']) ?>)</h3>
        <?php if (!empty($statystyki_gospodarzy)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Zawodnik</th>
                        <th>Pozycja</th>
                        <th>Minuty</th>
                        <th>Punkty</th>
                        <th>Asysty</th>
                        <th>Zbiórki</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($statystyki_gospodarzy as $stat): ?>
                        <tr>
                            <td><a href="../zawodnicy/details.php?id=<?= htmlspecialchars($stat['id_zawodnika']) ?>"><?= htmlspecialchars($stat['imie'] . ' ' . $stat['nazwisko']) ?></a></td>
                            <td><?= htmlspecialchars($stat['pozycja']) ?></td>
                            <td><?= htmlspecialchars($stat['minuty']) ?></td>
                            <td><?= htmlspecialchars($stat['punkty']) ?></td>
                            <td><?= htmlspecialchars($stat['asysty']) ?></td>
                            <td><?= htmlspecialchars($stat['zbiorki']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Brak statystyk dla zawodników gospodarzy.</p>
        <?php endif; ?>

        <h3>Statystyki Zawodników (<?= htmlspecialchars($mecz['nazwa_goscia']) ?>)</h3>
        <?php if (!empty($statystyki_gosci)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Zawodnik</th>
                        <th>Pozycja</th>
                        <th>Minuty</th>
                        <th>Punkty</th>
                        <th>Asysty</th>
                        <th>Zbiórki</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($statystyki_gosci as $stat): ?>
                        <tr>
                            <td><a href="../zawodnicy/details.php?id=<?= htmlspecialchars($stat['id_zawodnika']) ?>"><?= htmlspecialchars($stat['imie'] . ' ' . $stat['nazwisko']) ?></a></td>
                            <td><?= htmlspecialchars($stat['pozycja']) ?></td>
                            <td><?= htmlspecialchars($stat['minuty']) ?></td>
                            <td><?= htmlspecialchars($stat['punkty']) ?></td>
                            <td><?= htmlspecialchars($stat['asysty']) ?></td>
                            <td><?= htmlspecialchars($stat['zbiorki']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Brak statystyk dla zawodników gości.</p>
        <?php endif; ?>
    </section>

    <p><a href="index.php" class="button">Powrót do listy meczów</a></p>

</main>

<?php require_once $basePath . 'layout/footer.php'; ?>
