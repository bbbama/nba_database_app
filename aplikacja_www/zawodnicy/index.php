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
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie Zawodnikami</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Zarządzanie Zawodnikami</h1>
    </header>
    <nav>
        <ul>
            <li><a href="../index.php">Strona główna</a></li>
            <li><a href="index.php">Zawodnicy</a></li>
            <li><a href="../zespoly/">Zespoły</a></li>
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
                            <a href="delete.php?id=<?= $zawodnik['id_zawodnika'] ?>" class="button delete" onclick="return confirm('Czy na pewno chcesz usunąć tego zawodnika?')">Usuń</a>
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
    </main>
    <footer>
        <p>Projekt bazy danych - 2024</p>
    </footer>
</body>
</html>
