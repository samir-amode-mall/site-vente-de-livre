<?php
session_start();

if (!isset($_SESSION['code'])) {
    echo "❌ Vous devez être connecté pour vider votre panier.";
    exit;
}

$codeClient = $_SESSION['code'];

try {
    $bdd = new PDO('pgsql:host=localhost;dbname=livres', 'samir', 'mdp');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $bdd->prepare("DELETE FROM panier WHERE code_client = :codeClient");
    $stmt->bindParam(':codeClient', $codeClient);
    $stmt->execute();

    echo "✅ Le panier a été vidé.";
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>
