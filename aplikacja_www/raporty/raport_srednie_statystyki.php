<?php
require_once '../db.php';

$pdo = getDbConnection();

// Widok jest teraz bardziej rozbudowany i zawiera wszystkie potrzebne kolumny.
// Dodajemy warunek, aby pokazywać tylko graczy, którzy mają zarejestrowane statystyki.
$sql = "SELECT * FROM widok_srednie_statystyki_zawodnika WHERE liczba_meczow > 0 ORDER BY srednie_punkty DESC";
$stmt = $pdo->query($sql);
$statystyki = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Średnie statystyki zawodników';
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2>Średnie statystyki zawodników</h2>
    <?php if (empty($statystyki)): ?>
        <p>Brak statystyk do wyświetlenia. Upewnij się, że zawodnicy mają przypisane statystyki w przynajmniej jednym meczu.</p>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Zawodnik</th>
                <th>Zespół</th>
                <th>Liczba Meczów</th>
                <th>Średnie punkty</th>
                <th>Średnie asysty</th>
                <th>Średnie zbiórki</th>
                <th>Średnio minut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($statystyki as $stat): ?>
            <tr>
                <td><a href="<?= $basePath ?>zawodnicy/statystyki.php?id=<?= $stat['id_zawodnika'] ?>"><?= htmlspecialchars($stat['zawodnik_nazwa']) ?></a></td>
                <td><?= htmlspecialchars($stat['nazwa_zespolu']) ?></td>
                <td><?= htmlspecialchars($stat['liczba_meczow']) ?></td>
                <td><?= htmlspecialchars($stat['srednie_punkty']) ?></td>
                <td><?= htmlspecialchars($stat['srednie_asysty']) ?></td>
                <td><?= htmlspecialchars($stat['srednie_zbiorki']) ?></td>
                <td><?= htmlspecialchars($stat['srednie_minuty']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    <a href="index.php" class="button">Powrót do listy raportów</a>
</main>
<?php require_once $basePath . 'layout/footer.php'; ?>
