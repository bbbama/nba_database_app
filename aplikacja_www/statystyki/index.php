<?php
require_once '../db.php';

// Sprawdzenie, czy podano ID meczu
if (!isset($_GET['id_meczu'])) {
    header('Location: ../mecze/');
    exit;
}

$id_meczu = $_GET['id_meczu'];
$errors = [];
$success_message = '';

try {
    $pdo = getDbConnection();

    // Pobranie informacji o meczu
    $mecz_stmt = $pdo->prepare('SELECT m.id_meczu, m.data_meczu, m.id_gospodarza, m.id_goscia, g.nazwa as nazwa_gospodarza, go.nazwa as nazwa_goscia FROM mecz m JOIN zespol g ON m.id_gospodarza = g.id_zespolu JOIN zespol go ON m.id_goscia = go.id_zespolu WHERE m.id_meczu = ?');
    $mecz_stmt->execute([$id_meczu]);
    $mecz = $mecz_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$mecz) {
        die('Nie znaleziono meczu o podanym ID.');
    }

    // Pobranie zawodników z obu drużyn
    $zawodnicy_stmt = $pdo->prepare('SELECT id_zawodnika, imie, nazwisko, id_zespolu FROM zawodnik WHERE id_zespolu IN (?, ?)');
    $zawodnicy_stmt->execute([$mecz['id_gospodarza'], $mecz['id_goscia']]);
    $zawodnicy = $zawodnicy_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Pobranie istniejących statystyk dla tego meczu
    $statystyki_stmt = $pdo->prepare('SELECT * FROM statystyki_meczu WHERE id_meczu = ?');
    $statystyki_stmt->execute([$id_meczu]);
    $istniejace_statystyki_raw = $statystyki_stmt->fetchAll(PDO::FETCH_ASSOC);
    $istniejace_statystyki = [];
    foreach ($istniejace_statystyki_raw as $stat) {
        $istniejace_statystyki[$stat['id_zawodnika']] = $stat;
    }

} catch (PDOException $e) {
    die("Błąd przy pobieraniu danych: " . $e->getMessage());
}

// Obsługa formularza (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stats_data = $_POST['stats'] ?? [];

    try {
        $pdo->beginTransaction();

        $sql = "INSERT INTO statystyki_meczu (id_meczu, id_zawodnika, minuty, punkty, asysty, zbiorki)
                VALUES (:id_meczu, :id_zawodnika, :minuty, :punkty, :asysty, :zbiorki)
                ON CONFLICT (id_meczu, id_zawodnika) DO UPDATE SET
                    minuty = EXCLUDED.minuty,
                    punkty = EXCLUDED.punkty,
                    asysty = EXCLUDED.asysty,
                    zbiorki = EXCLUDED.zbiorki";
        
        $stmt = $pdo->prepare($sql);

        foreach ($stats_data as $id_zawodnika => $stats) {
            // Zapisujemy tylko jeśli jakiekolwiek dane zostały wprowadzone
            if (!empty($stats['minuty']) || !empty($stats['punkty']) || !empty($stats['asysty']) || !empty($stats['zbiorki'])) {
                $stmt->execute([
                    ':id_meczu' => $id_meczu,
                    ':id_zawodnika' => $id_zawodnika,
                    ':minuty' => empty($stats['minuty']) ? 0 : $stats['minuty'],
                    ':punkty' => empty($stats['punkty']) ? 0 : $stats['punkty'],
                    ':asysty' => empty($stats['asysty']) ? 0 : $stats['asysty'],
                    ':zbiorki' => empty($stats['zbiorki']) ? 0 : $stats['zbiorki']
                ]);
            }
        }

        $pdo->commit();
        $success_message = 'Statystyki zostały pomyślnie zaktualizowane!';
        // Odświeżenie danych po zapisie
        $statystyki_stmt->execute([$id_meczu]);
        $istniejace_statystyki_raw = $statystyki_stmt->fetchAll(PDO::FETCH_ASSOC);
        $istniejace_statystyki = [];
        foreach ($istniejace_statystyki_raw as $stat) {
            $istniejace_statystyki[$stat['id_zawodnika']] = $stat;
        }

    } catch (PDOException $e) {
        $pdo->rollBack();
        $errors[] = "Błąd zapisu do bazy danych: " . $e->getMessage();
    }
}

$pageTitle = "Statystyki Meczu: " . htmlspecialchars($mecz['nazwa_gospodarza']) . " vs " . htmlspecialchars($mecz['nazwa_goscia']);
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <?php if (!empty($errors)): ?>
        <div class="errors" style="color: red;"><?= htmlspecialchars($errors[0]) ?></div>
    <?php endif; ?>
    <?php if ($success_message): ?>
        <div class="success" style="color: green;"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <h3>Gospodarze: <?= htmlspecialchars($mecz['nazwa_gospodarza']) ?></h3>
        <table>
            <thead>
                <tr>
                    <th>Zawodnik</th>
                    <th>Minuty</th>
                    <th>Punkty</th>
                    <th>Asysty</th>
                    <th>Zbiórki</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($zawodnicy as $zawodnik): if ($zawodnik['id_zespolu'] == $mecz['id_gospodarza']): ?>
                <tr>
                    <td><?= htmlspecialchars($zawodnik['imie'] . ' ' . $zawodnik['nazwisko']) ?></td>
                    <td><input type="number" name="stats[<?= $zawodnik['id_zawodnika'] ?>][minuty]" value="<?= htmlspecialchars($istniejace_statystyki[$zawodnik['id_zawodnika']]['minuty'] ?? '') ?>" min="0"></td>
                    <td><input type="number" name="stats[<?= $zawodnik['id_zawodnika'] ?>][punkty]" value="<?= htmlspecialchars($istniejace_statystyki[$zawodnik['id_zawodnika']]['punkty'] ?? '') ?>" min="0"></td>
                    <td><input type="number" name="stats[<?= $zawodnik['id_zawodnika'] ?>][asysty]" value="<?= htmlspecialchars($istniejace_statystyki[$zawodnik['id_zawodnika']]['asysty'] ?? '') ?>" min="0"></td>
                    <td><input type="number" name="stats[<?= $zawodnik['id_zawodnika'] ?>][zbiorki]" value="<?= htmlspecialchars($istniejace_statystyki[$zawodnik['id_zawodnika']]['zbiorki'] ?? '') ?>" min="0"></td>
                </tr>
                <?php endif; endforeach; ?>
            </tbody>
        </table>

        <h3>Goście: <?= htmlspecialchars($mecz['nazwa_goscia']) ?></h3>
        <table>
            <thead>
                <tr>
                    <th>Zawodnik</th>
                    <th>Minuty</th>
                    <th>Punkty</th>
                    <th>Asysty</th>
                    <th>Zbiórki</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($zawodnicy as $zawodnik): if ($zawodnik['id_zespolu'] == $mecz['id_goscia']): ?>
                <tr>
                    <td><?= htmlspecialchars($zawodnik['imie'] . ' ' . $zawodnik['nazwisko']) ?></td>
                    <td><input type="number" name="stats[<?= $zawodnik['id_zawodnika'] ?>][minuty]" value="<?= htmlspecialchars($istniejace_statystyki[$zawodnik['id_zawodnika']]['minuty'] ?? '') ?>" min="0"></td>
                    <td><input type="number" name="stats[<?= $zawodnik['id_zawodnika'] ?>][punkty]" value="<?= htmlspecialchars($istniejace_statystyki[$zawodnik['id_zawodnika']]['punkty'] ?? '') ?>" min="0"></td>
                    <td><input type="number" name="stats[<?= $zawodnik['id_zawodnika'] ?>][asysty]" value="<?= htmlspecialchars($istniejace_statystyki[$zawodnik['id_zawodnika']]['asysty'] ?? '') ?>" min="0"></td>
                    <td><input type="number" name="stats[<?= $zawodnik['id_zawodnika'] ?>][zbiorki]" value="<?= htmlspecialchars($istniejace_statystyki[$zawodnik['id_zawodnika']]['zbiorki'] ?? '') ?>" min="0"></td>
                </tr>
                <?php endif; endforeach; ?>
            </tbody>
        </table>
        <br>
        <div>
            <button type="submit">Zapisz statystyki</button>
        </div>
    </form>
<?php require_once $basePath . 'layout/footer.php'; ?>