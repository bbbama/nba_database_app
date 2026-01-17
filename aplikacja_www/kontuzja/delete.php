<?php
require_once '../db.php';

if (isset($_GET['id'])) {
    $id_kontuzji = $_GET['id'];
    $pdo = getDbConnection();

    try {
        $stmt = $pdo->prepare("DELETE FROM kontuzja WHERE id_kontuzji = :id_kontuzji");
        $stmt->bindParam(':id_kontuzji', $id_kontuzji, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        die("Błąd przy usuwaniu kontuzji: " . $e->getMessage());
    }
} else {
    header('Location: index.php');
    exit;
}
?>