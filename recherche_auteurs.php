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

if (isset($_GET['debnom'])) {
    $debnom = $_GET['debnom'];
    $stmt = $pdo->prepare("SELECT code, nom, prenom FROM auteurs WHERE nom ILIKE :debnom");
    $stmt->execute(['debnom' => "%$debnom%"]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} else {
    echo json_encode(["error" => "Paramètre manquant"]);
}
?>
