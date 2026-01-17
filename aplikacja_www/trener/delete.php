<?php
require_once '../db.php';

if (isset($_GET['id'])) {
    $id_trenera = $_GET['id'];
    $pdo = getDbConnection();

    try {
        $stmt = $pdo->prepare("DELETE FROM trener WHERE id_trenera = :id_trenera");
        $stmt->bindParam(':id_trenera', $id_trenera, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        die("Błąd przy usuwaniu trenera: " . $e->getMessage());
    }
} else {
    header('Location: index.php');
    exit;
}
?>