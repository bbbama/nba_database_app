<?php
require_once '../db.php';

$kontuzje = [];
try {
    $pdo = getDbConnection();
    $stmt = $pdo->query("SELECT k.id_kontuzji, z.imie, z.nazwisko, k.typ_kontuzji, k.data_rozpoczecia, k.data_zakonczenia, k.status FROM kontuzja k JOIN zawodnik z ON k.id_zawodnika = z.id_zawodnika ORDER BY k.data_rozpoczecia DESC");
    $kontuzje = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Błąd odczytu kontuzji: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie Kontuzjami</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <header>
        <h1>Zarządzanie Kontuzjami</h1>
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
            <li><a href="index.php">Kontuzje</a></li>
            <li><a href="../nagroda/">Nagrody</a></li>
            <li><a href="../tabela_ligowa/">Tabela Ligowa</a></li>
        </ul>
    </nav>
    <main>
        <h2>Lista Kontuzji</h2>
        <p><a href="form.php">Dodaj nową kontuzję</a></p>
        <?php if (!empty($kontuzje)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Zawodnik</th>
                        <th>Typ kontuzji</th>
                        <th>Data rozpoczęcia</th>
                        <th>Data zakończenia</th>
                        <th>Status</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kontuzje as $kontuzja): ?>
                    <tr>
                        <td><?= htmlspecialchars($kontuzja['imie'] . ' ' . $kontuzja['nazwisko']) ?></td>
                        <td><?= htmlspecialchars($kontuzja['typ_kontuzji']) ?></td>
                        <td><?= htmlspecialchars($kontuzja['data_rozpoczecia']) ?></td>
                        <td><?= htmlspecialchars($kontuzja['data_zakonczenia']) ?></td>
                        <td><?= htmlspecialchars($kontuzja['status']) ?></td>
                        <td>
                            <a href="form.php?id=<?= htmlspecialchars($kontuzja['id_kontuzji']) ?>">Edytuj</a>
                            <a href="delete.php?id=<?= htmlspecialchars($kontuzja['id_kontuzji']) ?>" onclick="return confirm('Czy na pewno chcesz usunąć tę kontuzję?');">Usuń</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Brak kontuzji w bazie danych.</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>Projekt bazy danych - 2024</p>
    </footer>
</body>
</html>
