<?php
session_start();

$bienvenueMessage = '<a href="#" id="toggleInscription">Inscription</a>';

if (isset($_COOKIE['client'])) {
    $codeClient = $_COOKIE['client'];


    if (!isset($_SESSION['nom']) || !isset($_SESSION['prenom'])) {
        try {
            $bdd = new PDO('pgsql:host=localhost;dbname=livres', 'samir', 'mdp');
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $bdd->prepare("SELECT nom, prenom FROM clients WHERE code = :code");
            $stmt->bindParam(':code', $codeClient);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $_SESSION['nom'] = $result['nom'];
                $_SESSION['prenom'] = $result['prenom'];
                $_SESSION['code'] = $codeClient;
            }
        } catch (PDOException $e) {
            echo "Erreur de connexion à la base de données : " . $e->getMessage();
        }
    }

    if (isset($_SESSION['nom']) && isset($_SESSION['prenom'])) {
        $bienvenueMessage = "Bienvenue {$_SESSION['prenom']} {$_SESSION['nom']}";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vente de Livres</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
<div id="main-content">
    <header>
        <section><?php include 'compteur.php'; ?></section>
        <section><h1>Vente de Livres</h1></section>
        <section>
            <p>
                <?= $bienvenueMessage ?> | <a href="logout.php">Déconnecter</a> |
                <a href="#" onclick="afficher_panier()">🛒 Consulter le panier</a>
                <a href="#" onclick="vider_panier()">🗑️ Vider le panier</a>
            </p>
        </section>
    </header>

    <nav>
        <h3>Recherches</h3>
        <input type="text" id="searchAuteur" placeholder="Rechercher un auteur...">
        <div id="resultatsAuteurs"></div>

        <input type="text" id="searchLivre" placeholder="Rechercher un ouvrage...">
        <div id="resultatsLivres"></div>
    </nav>

    <section id="formInscription" style="display: none;">
        <h2>Inscription</h2>
        <form id="inscriptionForm">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>

            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" required>

            <label for="adresse">Adresse :</label>
            <input type="text" id="adresse" name="adresse" required>

            <label for="cp">Code Postal :</label>
            <input type="text" id="cp" name="cp" required>

            <label for="ville">Ville :</label>
            <input type="text" id="ville" name="ville" required>

            <label for="pays">Pays :</label>
            <input type="text" id="pays" name="pays" required>

            <button type="button" onclick="enregistrement()">S'inscrire</button>
        </form>
        <div id="message"></div>
    </section>
</div>

<div id="cart">
    <h2>🛒 Votre Panier</h2>
    <div id="contenu-panier"></div>
    <p id="total-panier"></p>
    <button onclick="commander()">✅ Commander</button>
    <button onclick="fermer_panier()">❌ Fermer</button>
</div>
</body>

</html>
