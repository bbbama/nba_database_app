<?php
require_once '../db.php';

$tabela_ligowa_entries = [];
try {
    $pdo = getDbConnection();
    $stmt = $pdo->query("SELECT tl.id_tabeli, s.rok_rozpoczecia, s.rok_zakonczenia, z.nazwa AS nazwa_zespolu, tl.liczba_zwyciestw, tl.liczba_porazek, vtl.miejsce_w_tabeli FROM tabela_ligowa tl JOIN sezon s ON tl.id_sezonu = s.id_sezonu JOIN zespol z ON tl.id_zespolu = z.id_zespolu JOIN widok_tabeli_ligowej vtl ON tl.id_sezonu = vtl.id_sezonu AND tl.id_zespolu = vtl.id_zespolu ORDER BY s.rok_rozpoczecia DESC, vtl.miejsce_w_tabeli ASC");
    $tabela_ligowa_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd odczytu wpisów tabeli ligowej: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie Tabelą Ligową</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Zarządzanie Tabelą Ligową</h1>
    </header>
    <nav>
        <ul>
            <li><a href="../index.php">Strona główna</a></li>
            <li><a href="../zawodnicy/">Zawodnicy</a></li>
            <li><a href="../zespoly/">Zespoły</a></li>
            <li><a href="../mecze/">Mecze</a></li>
            <li><a href="../raporty/">Raporty</a></li>
            <li><a href="../areny/">Areny</a></li>
            <li><a href="../sezony/">Sezony</a></li>
            <li><a href="../trener/">Trenerzy</a></li>
            <li><a href="../kontrakt/">Kontrakty</a></li>
            <li><a href="../kontuzja/">Kontuzje</a></li>
            <li><a href="../nagroda/">Nagrody</a></li>
            <li><a href="index.php">Tabela Ligowa</a></li>
        </ul>
    </nav>
    <main>
        <h2>Lista Wpisów Tabeli Ligowej</h2>
        <p><a href="form.php">Dodaj nowy wpis do tabeli ligowej</a></p>
        <?php if (!empty($tabela_ligowa_entries)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Sezon</th>
                        <th>Zespół</th>
                        <th>Zwycięstwa</th>
                        <th>Porażki</th>
                        <th>Miejsce</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tabela_ligowa_entries as $entry): ?>
                    <tr>
                        <td><?= htmlspecialchars($entry['rok_rozpoczecia'] . '/' . $entry['rok_zakonczenia']) ?></td>
                        <td><?= htmlspecialchars($entry['nazwa_zespolu']) ?></td>
                        <td><?= htmlspecialchars($entry['liczba_zwyciestw']) ?></td>
                        <td><?= htmlspecialchars($entry['liczba_porazek']) ?></td>
                        <td><?= htmlspecialchars($entry['miejsce_w_tabeli']) ?></td>
                        <td>
                            <a href="form.php?id=<?= htmlspecialchars($entry['id_tabeli']) ?>">Edytuj</a>
                            <a href="delete.php?id=<?= htmlspecialchars($entry['id_tabeli']) ?>" onclick="return confirm('Czy na pewno chcesz usunąć ten wpis z tabeli ligowej?');">Usuń</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Brak wpisów w tabeli ligowej w bazie danych.</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>Projekt bazy danych - 2024</p>
    </footer>
</body>
</html>
