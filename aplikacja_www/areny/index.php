<?php
require_once '../db.php';

try {
    $pdo = getDbConnection();
    $stmt = $pdo->query('SELECT id_arena, nazwa, miasto, pojemnosc, rok_otwarcia FROM arena ORDER BY nazwa');
    $areny = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd przy pobieraniu danych: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie Arenami</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Zarządzanie Arenami</h1>
    </header>
    <nav>
        <ul>
            <li><a href="../index.php">Strona główna</a></li>
            <li><a href="../zawodnicy/">Zawodnicy</a></li>
            <li><a href="../zespoly/">Zespoły</a></li>
            <li><a href="../mecze/">Mecze</a></li>
            <li><a href="../raporty.php">Raporty</a></li>
            <li><a href="index.php">Areny</a></li>
            <li><a href="../sezony/">Sezony</a></li>
        </ul>
    </nav>
    <main>
        <h2>Lista Aren</h2>
        <a href="form.php" class="button">Dodaj nową arenę</a>
        <table>
            <thead>
                <tr>
                    <th>Nazwa</th>
                    <th>Miasto</th>
                    <th>Pojemność</th>
                    <th>Rok otwarcia</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($areny) > 0): ?>
                    <?php foreach ($areny as $arena): ?>
                    <tr>
                        <td><?= htmlspecialchars($arena['nazwa']) ?></td>
                        <td><?= htmlspecialchars($arena['miasto']) ?></td>
                        <td><?= htmlspecialchars($arena['pojemnosc']) ?></td>
                        <td><?= htmlspecialchars($arena['rok_otwarcia']) ?></td>
                        <td>
                            <a href="form.php?id=<?= $arena['id_arena'] ?>" class="button edit">Edytuj</a>
                            <a href="delete.php?id=<?= $arena['id_arena'] ?>" class="button delete" onclick="return confirm('Czy na pewno chcesz usunąć tę arenę? Zespoły powiązane z tą areną będą miały ustawione pole areny na NULL.')">Usuń</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Brak danych o arenach w bazie.</td>
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
