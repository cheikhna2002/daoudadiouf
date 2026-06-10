<?php
require_once 'config/connexion.php';
require_once 'fonctions.php';
enregistrer_visite($pdo, 'Sommaire');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Sommaire — Cheikhna Diouf</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body class="pagesommaire">
    <?php require 'navigation.php'; ?>
    <main>
        <div class="sommaire">
            <h1 class="titre">SOMMAIRE</h1>
            <ol class="legende">
                <li><a href="./presentation.php">À propos de moi</a></li>
                <li>Mes compétences</li>
                <li><a href="./projet.php">Mes projets</a></li>
                <li><a href="./formulaire.php">Me contacter</a></li>
            </ol>
        </div>
    </main>
    <?php require 'pied-de-page.php'; ?>
</body>
</html>
