<?php
require_once '../db.php';

try {
    $pdo = getDbConnection();
    $stmt = $pdo->query('SELECT id_sezonu, rok_rozpoczecia, rok_zakonczenia FROM sezon ORDER BY rok_rozpoczecia DESC');
    $sezony = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd przy pobieraniu danych: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie Sezonami</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Zarządzanie Sezonami</h1>
    </header>
    <nav>
        <ul>
            <li><a href="../index.php">Strona główna</a></li>
            <li><a href="../zawodnicy/">Zawodnicy</a></li>
            <li><a href="../zespoly/">Zespoły</a></li>
            <li><a href="../mecze/">Mecze</a></li></li>
            <li><a href="../raporty.php">Raporty</a></li>
            <li><a href="../areny/">Areny</a></li>
            <li><a href="index.php">Sezony</a></li>
        </ul>
    </nav>
    <main>
        <h2>Lista Sezonów</h2>
        <a href="form.php" class="button">Dodaj nowy sezon</a>
        <table>
            <thead>
                <tr>
                    <th>ID Sezonu</th>
                    <th>Rok rozpoczęcia</th>
                    <th>Rok zakończenia</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($sezony) > 0): ?>
                    <?php foreach ($sezony as $sezon): ?>
                    <tr>
                        <td><?= htmlspecialchars($sezon['id_sezonu']) ?></td>
                        <td><?= htmlspecialchars($sezon['rok_rozpoczecia']) ?></td>
                        <td><?= htmlspecialchars($sezon['rok_zakonczenia']) ?></td>
                        <td>
                            <a href="form.php?id=<?= $sezon['id_sezonu'] ?>" class="button edit">Edytuj</a>
                            <a href="delete.php?id=<?= $sezon['id_sezonu'] ?>" class="button delete" onclick="return confirm('Czy na pewno chcesz usunąć ten sezon? Usunięcie sezonu może wpłynąć na powiązane mecze i tabele ligowe.')">Usuń</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Brak danych o sezonach w bazie.</td>
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
