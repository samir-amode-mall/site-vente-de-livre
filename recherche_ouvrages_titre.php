<?php
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);


$dsn = "pgsql:host=localhost;dbname=livres;port=5432";
$user = "samir";
$password = "mdp";

try {
    $pdo = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo json_encode(["error" => "Connexion échouée"]);
    exit;
}



if (isset($_GET['debtitre'])) {
    $nom = $_GET['debtitre'];

    $stmt = $pdo->prepare("SELECT * FROM ouvrage WHERE nom ILIKE :nom");
    $stmt->execute(['nom' => "%$nom%"]);
    $ouvrages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($ouvrages as &$ouvrage) {
        $stmt = $pdo->prepare("SELECT ed.nom AS editeur_nom, e.code, e.prix 
                       FROM exemplaire e 
                       JOIN editeurs ed ON e.code_editeur = ed.code
                       WHERE e.code_ouvrage = :code_ouvrage");

        $stmt->execute(['code_ouvrage' => $ouvrage['code']]);
        $ouvrage['exemplaires'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode($ouvrages);
} else {
    echo json_encode(["error" => "Paramètre 'debtitre' manquant"]);
}
?>
