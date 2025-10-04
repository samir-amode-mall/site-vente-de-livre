<?php
session_start();

if (!isset($_SESSION['code'])) {
    echo "<p>Vous devez être connecté pour consulter votre panier.</p>";
    exit;
}

$codeClient = $_SESSION['code'];

try {
    $bdd = new PDO('pgsql:host=localhost;dbname=livres', 'samir', 'mdp');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $bdd->prepare("
    SELECT o.nom AS titre, ed.nom AS editeur, COUNT(p.code_exemplaire) AS quantite, e.prix
    FROM panier p
    JOIN exemplaire e ON p.code_exemplaire = e.code
    JOIN editeurs ed ON e.code_editeur = ed.code
    JOIN ouvrage o ON e.code_ouvrage = o.code
    WHERE p.code_client = :code_client
    GROUP BY o.nom, ed.nom, e.prix
");

    $stmt->bindParam(':code_client', $codeClient);
    $stmt->execute();
    $panier = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($panier) > 0) {
        $total = 0;
        echo "<ul>";
        foreach ($panier as $item) {
            $total += $item['quantite'] * $item['prix'];
            echo "<li>{$item['titre']} ({$item['editeur']}) - Quantité: {$item['quantite']} - Prix: {$item['prix']}€</li>";
        }
        echo "</ul>";
        echo "<p>Total: <strong>{$total}€</strong></p>";
    } else {
        echo "<p>Votre panier est vide.</p>";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
