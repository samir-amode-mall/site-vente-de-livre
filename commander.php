<?php
session_start();

if (!isset($_SESSION['code'])) {
    echo "Vous devez être connecté pour passer commande.";
    exit;
}

$codeClient = $_SESSION['code'];
$dateCommande = date("Y-m-d");

try {
    $bdd = new PDO('pgsql:host=localhost;dbname=livres', 'samir', 'mdp');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $bdd->prepare("SELECT COUNT(*) FROM panier WHERE code_client = :codeClient");
    $stmt->bindParam(':codeClient', $codeClient);
    $stmt->execute();
    $nbArticles = $stmt->fetchColumn();

    if ($nbArticles == 0) {
        echo "Votre panier est vide.";
        exit;
    }

    $stmt = $bdd->prepare("
        INSERT INTO commande (code_client, code_exemplaire, quantite, prix, date)
        SELECT p.code_client, p.code_exemplaire, COUNT(p.code_exemplaire) AS quantite, e.prix, :dateCommande
        FROM panier p
        JOIN exemplaire e ON p.code_exemplaire = e.code
        WHERE p.code_client = :codeClient
        GROUP BY p.code_client, p.code_exemplaire, e.prix
    ");
    $stmt->bindParam(':codeClient', $codeClient);
    $stmt->bindParam(':dateCommande', $dateCommande);
    $stmt->execute();

    $stmt = $bdd->prepare("DELETE FROM panier WHERE code_client = :codeClient");
    $stmt->bindParam(':codeClient', $codeClient);
    $stmt->execute();

    echo "✅ Votre commande a bien été enregistrée.";
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>
