<?php
require_once '../config/connexion.php';
require_once '../fonctions.php';
verifier_authentification();

// Récupération des statistiques rapides
$total_projets = $pdo->query("SELECT COUNT(*) FROM projets")->fetchColumn();
$total_messages = $pdo->query("SELECT COUNT(*) FROM messages_contact")->fetchColumn();
$total_visites = $pdo->query("SELECT COUNT(*) FROM visites")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; display: flex; }
        .sidebar { width: 250px; background: #333; color: white; padding: 20px; min-height: 100vh; }
        .sidebar a { color: white; display: block; padding: 10px 0; text-decoration: none; }
        .content { flex: 1; padding: 20px; }
        .cards { display: flex; gap: 20px; }
        .card { background: #007BFF; color: white; padding: 20px; border-radius: 8px; flex: 1; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3>Admin Portfolio</h3>
        <p>Bonjour, <?= echapper($_SESSION['admin_prenom']) ?> 👋</p>
        <hr>
        <a href="dashboard.php">📊 Tableau de bord</a>
        <a href="messages/index.php">✉️ Messages de contact</a>
        <a href="deconnexion.php" style="color: red; margin-top: 50px;">🚪 Déconnexion</a>
    </div>
    <div class="content">
        <h2>Tableau de Bord</h2>
        <div class="cards">
            <div class="card">
                <h3>Projets</h3>
                <p><?= $total_projets ?> enregistrés</p>
            </div>
            <div class="card" style="background: #28a745;">
                <h3>Messages</h3>
                <p><?= $total_messages ?> reçus</p>
            </div>
            <div class="card" style="background: #ffc107; color: black;">
                <h3>Visites</h3>
                <p><?= $total_visites ?> pages vues</p>
            </div>
        </div>
    </div>
</body>
</html>
