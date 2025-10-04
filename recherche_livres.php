<?php
header("Content-Type: application/json; charset=UTF-8");

$dsn = "pgsql:host=localhost;dbname=livres;port=5432";
$user = "samir";
$password = "mdp";

try {
    $pdo = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo json_encode(["error" => "Connexion échouée"]);
    exit;
}

if (isset($_GET['nom'])) {
    $nom = $_GET['nom'];
    $stmt = $pdo->prepare("SELECT * FROM ouvrage WHERE nom ILIKE :nom");
    $stmt->execute(['nom' => "%$nom%"]);

    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($resultats);
} else {
    echo json_encode(["error" => "Paramètre 'nom' manquant"]);
}
?>
