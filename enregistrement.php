<?php
$pdo = new PDO("pgsql:host=localhost;dbname=livres", "samir", "mdp");

$nom = $_POST['nom'] ?? '';
$prenom = $_POST['prenom'] ?? '';
$adresse = $_POST['adresse'] ?? '';
$cp = $_POST['cp'] ?? '';
$ville = $_POST['ville'] ?? '';
$pays = $_POST['pays'] ?? '';

if ($nom && $prenom && $adresse && $cp && $ville && $pays) {
    $stmt = $pdo->prepare("INSERT INTO clients (nom, prenom, adresse, cp, ville, pays) VALUES (?, ?, ?, ?, ?, ?)");
    $success = $stmt->execute([$nom, $prenom, $adresse, $cp, $ville, $pays]);

    if ($success) {
        echo "Inscription réussie !";
    } else {
        echo "Erreur lors de l'enregistrement.";
    }
} else {
    echo "Veuillez remplir tous les champs.";
}
?>
