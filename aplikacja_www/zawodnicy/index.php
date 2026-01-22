<?php
require_once '../db.php';

try {
    $pdo = getDbConnection();
    // Używamy widoku, aby pobrać dane zawodników wraz z nazwami zespołów
    $stmt = $pdo->query('SELECT id_zawodnika, imie, nazwisko, pozycja, nazwa_zespolu FROM widok_zawodnicy_z_zespolem ORDER BY nazwisko, imie');
    $zawodnicy = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd przy pobieraniu danych: " . $e->getMessage());
}

$pageTitle = 'Zarządzanie Zawodnikami';
$basePath = '../';
require_once $basePath . 'layout/header.php';
require_once $basePath . 'layout/nav.php';
?>

<main>
    <h2>Lista Zawodników</h2>
    <a href="form.php" class="button">Dodaj nowego zawodnika</a>
    <table>
        <thead>
            <tr>
                <th>Imię</th>
                <th>Nazwisko</th>
                <th>Pozycja</th>
                <th>Zespół</th>
                <th>Akcje</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($zawodnicy) > 0): ?>
                <?php foreach ($zawodnicy as $zawodnik): ?>
                <tr>
                    <td><?= htmlspecialchars($zawodnik['imie']) ?></td>
                    <td><?= htmlspecialchars($zawodnik['nazwisko']) ?></td>
                    <td><?= htmlspecialchars($zawodnik['pozycja']) ?></td>
                    <td><?= htmlspecialchars($zawodnik['nazwa_zespolu']) ?></td>
                    <td>
                        <a href="form.php?id=<?= $zawodnik['id_zawodnika'] ?>" class="button edit">Edytuj</a>
                        <a href="statystyki.php?id=<?= $zawodnik['id_zawodnika'] ?>" class="button">Statystyki</a>
                        <a href="delete.php?id=<?= $zawodnik['id_zawodnika'] ?>" class="button delete">Usuń</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Brak danych zawodników w bazie.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php require_once $basePath . 'layout/footer.php'; ?>