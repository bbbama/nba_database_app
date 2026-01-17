<?php
require_once '../db.php';

$trenerzy = [];
try {
    $pdo = getDbConnection();
    $stmt = $pdo->query("SELECT t.id_trenera, t.imie, t.nazwisko, t.rola, z.nazwa AS nazwa_zespolu FROM trener t LEFT JOIN zespol z ON t.id_zespolu = z.id_zespolu ORDER BY t.nazwisko, t.imie ASC");
    $trenerzy = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd odczytu trenerów: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie Trenerami</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Zarządzanie Trenerami</h1>
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
            <li><a href="index.php">Trenerzy</a></li>
            <li><a href="../kontrakt/">Kontrakty</a></li>
            <li><a href="../kontuzja/">Kontuzje</a></li>
            <li><a href="../nagroda/">Nagrody</a></li>
            <li><a href="../tabela_ligowa/">Tabela Ligowa</a></li>
        </ul>
    </nav>
    <main>
        <h2>Lista Trenerów</h2>
        <p><a href="form.php">Dodaj nowego trenera</a></p>
        <?php if (!empty($trenerzy)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Imię</th>
                        <th>Nazwisko</th>
                        <th>Rola</th>
                        <th>Zespół</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trenerzy as $trener): ?>
                    <tr>
                        <td><?= htmlspecialchars($trener['imie']) ?></td>
                        <td><?= htmlspecialchars($trener['nazwisko']) ?></td>
                        <td><?= htmlspecialchars($trener['rola']) ?></td>
                        <td><?= htmlspecialchars($trener['nazwa_zespolu'] ?? 'Brak') ?></td>
                        <td>
                            <a href="form.php?id=<?= htmlspecialchars($trener['id_trenera']) ?>">Edytuj</a>
                            <a href="delete.php?id=<?= htmlspecialchars($trener['id_trenera']) ?>" onclick="return confirm('Czy na pewno chcesz usunąć tego trenera?');">Usuń</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Brak trenerów w bazie danych.</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>Projekt bazy danych - 2024</p>
    </footer>
</body>
</html>
