<?php
session_start();
header("Content-Type: text/plain");

if (!isset($_SESSION['code'])) {
    echo "not_logged_in";
    exit;
}

if (!isset($_POST['code_exemplaire'])) {
    echo "missing_data";
    exit;
}

$codeClient = $_SESSION['code'];
$codeExemplaire = intval($_POST['code_exemplaire']);

try {
    $bdd = new PDO('pgsql:host=localhost;dbname=livres', 'samir', 'mdp');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $bdd->prepare("INSERT INTO panier (code_client, code_exemplaire) VALUES (:code_client, :code_exemplaire)");
    $stmt->bindParam(':code_client', $codeClient);
    $stmt->bindParam(':code_exemplaire', $codeExemplaire);
    $stmt->execute();

    echo "success";
} catch (PDOException $e) {
    echo "error";
}