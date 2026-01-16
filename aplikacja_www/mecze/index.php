<?php
require_once '../db.php';

try {
    $pdo = getDbConnection();
    // Używamy widoku, aby pobrać dane meczów wraz z nazwami zespołów
    $stmt = $pdo->query('SELECT id_meczu, data_meczu, gospodarz, wynik_gospodarza, gosc, wynik_goscia FROM widok_wyniki_meczy ORDER BY data_meczu DESC');
    $mecze = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd przy pobieraniu danych: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie Meczami</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Zarządzanie Meczami</h1>
    </header>
    <nav>
        <ul>
            <li><a href="../index.php">Strona główna</a></li>
            <li><a href="../zawodnicy/">Zawodnicy</a></li>
            <li><a href="../zespoly/">Zespoły</a></li>
            <li><a href="index.php">Mecze</a></li>
            <li><a href="../raporty.php">Raporty</a></li>
            <li><a href="../areny/">Areny</a></li>
            <li><a href="../sezony/">Sezony</a></li>
        </ul>
    </nav>
    <main>
        <h2>Lista Meczów</h2>
        <a href="form.php" class="button">Dodaj nowy mecz</a>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Gospodarz</th>
                    <th>Wynik</th>
                    <th>Gość</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($mecze) > 0): ?>
                    <?php foreach ($mecze as $mecz): ?>
                    <tr>
                        <td><?= htmlspecialchars($mecz['data_meczu']) ?></td>
                        <td><?= htmlspecialchars($mecz['gospodarz']) ?></td>
                        <td><?= htmlspecialchars($mecz['wynik_gospodarza']) ?> : <?= htmlspecialchars($mecz['wynik_goscia']) ?></td>
                        <td><?= htmlspecialchars($mecz['gosc']) ?></td>
                        <td>
                            <a href="../statystyki/index.php?id_meczu=<?= $mecz['id_meczu'] ?>" class="button">Statystyki</a>
                            <a href="form.php?id=<?= $mecz['id_meczu'] ?>" class="button edit">Edytuj</a>
                            <a href="delete.php?id=<?= $mecz['id_meczu'] ?>" class="button delete" onclick="return confirm('Czy na pewno chcesz usunąć ten mecz? Usunięcie meczu usunie również wszystkie powiązane z nim statystyki.')">Usuń</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Brak danych o meczach w bazie.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>
    <footer>
        <p>Projekt bazy danych - 2024</p>
    </footer>
</body>
</html>
