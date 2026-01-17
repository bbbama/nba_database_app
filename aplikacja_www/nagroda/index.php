<?php
require_once '../db.php';

$nagrody = [];
try {
    $pdo = getDbConnection();
    $stmt = $pdo->query("SELECT n.id_nagrody, z.imie, z.nazwisko, n.nazwa_nagrody, n.rok FROM nagroda n JOIN zawodnik z ON n.id_zawodnika = z.id_zawodnika ORDER BY n.rok DESC, n.nazwa_nagrody ASC");
    $nagrody = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd odczytu nagród: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie Nagrodami</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Zarządzanie Nagrodami</h1>
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
            <li><a href="index.php">Nagrody</a></li>
            <li><a href="../tabela_ligowa/">Tabela Ligowa</a></li>
        </ul>
    </nav>
    <main>
        <h2>Lista Nagród</h2>
        <p><a href="form.php">Dodaj nową nagrodę</a></p>
        <?php if (!empty($nagrody)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Zawodnik</th>
                        <th>Nazwa nagrody</th>
                        <th>Rok</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($nagrody as $nagroda): ?>
                    <tr>
                        <td><?= htmlspecialchars($nagroda['imie'] . ' ' . $nagroda['nazwisko']) ?></td>
                        <td><?= htmlspecialchars($nagroda['nazwa_nagrody']) ?></td>
                        <td><?= htmlspecialchars($nagroda['rok']) ?></td>
                        <td>
                            <a href="form.php?id=<?= htmlspecialchars($nagroda['id_nagrody']) ?>">Edytuj</a>
                            <a href="delete.php?id=<?= htmlspecialchars($nagroda['id_nagrody']) ?>" onclick="return confirm('Czy na pewno chcesz usunąć tę nagrodę?');">Usuń</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Brak nagród w bazie danych.</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>Projekt bazy danych - 2024</p>
    </footer>
</body>
</html>
