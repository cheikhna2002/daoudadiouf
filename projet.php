<?php
require_once 'config/connexion.php';
require_once 'fonctions.php';
enregistrer_visite($pdo, 'Mes Projets');

$mot_cle = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($mot_cle !== '') {
    // Requête préparée sécurisée contre les injections SQL
    $stmt = $pdo->prepare("SELECT * FROM projets WHERE titre LIKE :search OR description LIKE :search OR technologies LIKE :search ORDER BY date_creation DESC");
    $stmt->execute(['search' => "%$mot_cle%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM projets ORDER BY date_creation DESC");
}
$resultats = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Projets</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body class="mesprojets">
    <?php require 'navigation.php'; ?>
    <main>
        <h1>MES PROJETS</h1>
        
        <!-- Barre de recherche -->
        <form method="GET" action="projet.php" style="margin-bottom: 20px;">
            <input type="text" name="q" value="<?= echapper($mot_cle) ?>" placeholder="Rechercher un projet ou une techno...">
            <button type="submit">Rechercher</button>
        </form>

        <div class="liste-projets">
            <?php if (empty($resultats)): ?>
                <p>Aucun projet ne correspond à votre recherche.</p>
            <?php else: ?>
                <?php foreach ($resultats as $p): ?>
                    <div style="background: rgba(255,255,255,0.1); padding: 15px; margin-bottom: 15px; border-radius: 5px;">
                        <h3><?= echapper($p['titre']) ?></h3>
                        <p><?= echapper($p['description']) ?></p>
                        <p><strong>Technologies :</strong> <?= echapper($p['technologies']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    <?php require 'pied-de-page.php'; ?>
</body>
</html>
