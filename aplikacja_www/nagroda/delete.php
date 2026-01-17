<?php
require_once '../db.php';

if (isset($_GET['id'])) {
    $id_nagrody = $_GET['id'];
    $pdo = getDbConnection();

    try {
        $stmt = $pdo->prepare("DELETE FROM nagroda WHERE id_nagrody = :id_nagrody");
        $stmt->bindParam(':id_nagrody', $id_nagrody, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        die("Błąd przy usuwaniu nagrody: " . $e->getMessage());
    }
} else {
    header('Location: index.php');
    exit;
}
?>