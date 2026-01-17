<?php
require_once '../db.php';

try {
    $pdo = getDbConnection();
    // Zapytanie łączące zespół z areną, aby wyświetlić nazwę areny
    $stmt = $pdo->query('
        SELECT z.id_zespolu, z.nazwa, z.miasto, z.rok_zalozenia, z.trener_glowny, a.nazwa AS nazwa_areny
        FROM zespol z
        LEFT JOIN arena a ON z.id_arena = a.id_arena
        ORDER BY z.nazwa
    ');
    $zespoly = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd przy pobieraniu danych: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie Zespołami</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Zarządzanie Zespołami</h1>
    </header>
    <nav>
        <ul>
            <li><a href="../index.php">Strona główna</a></li>
            <li><a href="../zawodnicy/">Zawodnicy</a></li>
            <li><a href="index.php">Zespoły</a></li>
            <li><a href="../mecze/">Mecze</a></li>
            <li><a href="../raporty/">Raporty</a></li>
            <li><a href="../areny/">Areny</a></li>
            <li><a href="../sezony/">Sezony</a></li>
            <li><a href="../trener/">Trenerzy</a></li>
            <li><a href="../kontrakt/">Kontrakty</a></li>
            <li><a href="../kontuzja/">Kontuzje</a></li>
            <li><a href="../nagroda/">Nagrody</a></li>
            <li><a href="../tabela_ligowa/">Tabela Ligowa</a></li>
        </ul>
    </nav>
    <main>
        <h2>Lista Zespołów</h2>
        <a href="form.php" class="button">Dodaj nowy zespół</a>
        <table>
            <thead>
                <tr>
                    <th>Nazwa</th>
                    <th>Miasto</th>
                    <th>Rok Założenia</th>
                    <th>Główny Trener</th>
                    <th>Arena</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($zespoly) > 0): ?>
                    <?php foreach ($zespoly as $zespol): ?>
                    <tr>
                        <td><?= htmlspecialchars($zespol['nazwa']) ?></td>
                        <td><?= htmlspecialchars($zespol['miasto']) ?></td>
                        <td><?= htmlspecialchars($zespol['rok_zalozenia']) ?></td>
                        <td><?= htmlspecialchars($zespol['trener_glowny']) ?></td>
                        <td><?= htmlspecialchars($zespol['nazwa_areny'] ?? 'Brak danych') ?></td>
                        <td>
                            <a href="form.php?id=<?= $zespol['id_zespolu'] ?>" class="button edit">Edytuj</a>
                            <a href="delete.php?id=<?= $zespol['id_zespolu'] ?>" class="button delete" onclick="return confirm('Czy na pewno chcesz usunąć ten zespół? Usunięcie zespołu może wpłynąć na inne dane (np. zawodników w tym zespole).')">Usuń</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">Brak danych o zespołach w bazie.</td>
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
