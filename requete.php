<?php
header("Content-Type: text/html; charset=UTF-8");

$dsn = "pgsql:host=localhost;dbname=livres;port=5432";
$user = "samir"; 
$password = "mdp";

try {
    $pdo = new PDO($dsn, $user, $password);
    echo "<p>Connexion réussie !</p>";
} catch (PDOException $e) {
    die("<p>Erreur : " . $e->getMessage() . "</p>");
}

echo "<h3>🔎 Recherche d'auteurs contenant 'a'</h3>";
$stmt = $pdo->prepare("SELECT * FROM auteurs WHERE nom ILIKE :nom");
$stmt->execute(['nom' => "%a%"]);
$auteurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($auteurs as $auteur) {
    echo "<p>{$auteur['nom']} {$auteur['prenom']}</p>";
}

echo "<h3>📚 Recherche de livres contenant 'le'</h3>";
$stmt = $pdo->prepare("SELECT * FROM ouvrage WHERE nom ILIKE :titre");
$stmt->execute(['titre' => "%le%"]);
$livres = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($livres as $livre) {
    echo "<p>{$livre['nom']}</p>";
}

echo "<h3>📖 Livres d'un auteur (code = 1)</h3>";
$stmt = $pdo->prepare("SELECT * FROM ouvrage WHERE code = :code");
$stmt->execute(['code' => 1]);
$livresAuteur = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($livresAuteur as $livre) {
    echo "<p>{$livre['nom']}</p>";
}

echo "<h3>📦 Exemplaires du livre (code = 1)</h3>";
$stmt = $pdo->prepare("
    SELECT ed.nom AS editeur, e.prix 
    FROM exemplaire e
    JOIN editeurs ed ON e.code_editeur = ed.code
    WHERE e.code_ouvrage = :code
");
$stmt->execute(['code' => 1]);
$exemplaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($exemplaires as $exemplaire) {
    echo "<p>{$exemplaire['editeur']} - {$exemplaire['prix']} €</p>";
}
?>

