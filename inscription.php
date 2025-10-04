<?php
$dsn = "pgsql:host=localhost;dbname=livres;user=samir;password=mdp";
try {
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion : " . $e->getMessage();
    exit();
}

$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');
$adresse = trim($_POST['adresse'] ?? '');
$cp = trim($_POST['cp'] ?? '');
$ville = trim($_POST['ville'] ?? '');
$pays = trim($_POST['pays'] ?? '');

if (!$nom || !$prenom || !$adresse || !$cp || !$ville || !$pays) {
    echo "missing_fields";
    exit();
}

$sql = "SELECT inscription(:nom, :prenom, :adresse, :cp, :ville, :pays) AS codeclient";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':nom' => $nom,
    ':prenom' => $prenom,
    ':adresse' => $adresse,
    ':cp' => $cp,
    ':ville' => $ville,
    ':pays' => $pays
]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result['codeclient'] > 0) {
    setcookie('client', $result['codeclient'], time() + (365 * 24 * 3600 * 25), "/");
    echo $result['codeclient'];
} else {
    echo "duplicate";
}
?>
