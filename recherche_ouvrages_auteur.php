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

if (isset($_GET['code'])) {
    $code_auteur = $_GET['code'];

    $stmt = $pdo->prepare("
        SELECT o.code, o.nom, o.parution, o.sujet
        FROM ouvrage o
        JOIN ecrit_par ep ON o.code = ep.code_ouvrage
        JOIN auteurs a ON ep.code_auteur = a.code
        WHERE a.code = :code_auteur
    ");
    $stmt->execute(['code_auteur' => $code_auteur]);
    $ouvrages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($ouvrages as &$ouvrage) {
        $stmt = $pdo->prepare("
            SELECT ed.nom AS editeur_nom, e.code, e.prix 
            FROM exemplaire e 
            JOIN editeurs ed ON e.code_editeur = ed.code
            WHERE e.code_ouvrage = :code_ouvrage
        ");
        $stmt->execute(['code_ouvrage' => $ouvrage['code']]);
        $ouvrage['exemplaires'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode($ouvrages);
} else {
    echo json_encode(["error" => "Paramètre 'code' manquant"]);
}
?>
