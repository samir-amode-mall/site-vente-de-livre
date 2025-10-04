<?php
header("Content-Type: application/json");

if (!isset($_GET['code'])) {
    echo json_encode([]);
    exit;
}

$codeOuvrage = intval($_GET['code']);

try {
    $bdd = new PDO('pgsql:host=localhost;dbname=livres', 'samir', 'mdp');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $bdd->prepare("SELECT code, code_ouvrage, code_editeur, code_emplacement, prix FROM exemplaire WHERE code_ouvrage = :code_ouvrage");
    $stmt->bindParam(':code_ouvrage', $codeOuvrage, PDO::PARAM_INT);
    $stmt->execute();
    $exemplaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$exemplaires) {
        error_log("Aucun exemplaire trouvé pour code ouvrage: " . $codeOuvrage);
    } else {
        error_log("Exemplaires récupérés: " . print_r($exemplaires, true));
    }

    echo json_encode($exemplaires);
} catch (PDOException $e) {
    error_log("Erreur SQL: " . $e->getMessage());
    echo json_encode([]);
}

?>
