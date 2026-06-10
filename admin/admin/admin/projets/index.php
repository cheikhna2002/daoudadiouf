<?php
require_once '../../config/connexion.php';
require_once '../../fonctions.php';
verifier_authentification();

// Récupération de tous les projets
$stmt = $pdo->query("SELECT * FROM projets ORDER BY date_creation DESC");
$projets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Projets</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #333; color: white; }
        .btn { padding: 5px 10px; text-decoration: none; border-radius: 3px; color: white; }
        .btn-add { background: #28a745; padding: 10px 15px; }
        .btn-edit { background: #007BFF; }
        .btn-delete { background: #dc3545; }
    </style>
</head>
<body>
    <p><a href="../dashboard.php">📊 Retour au Tableau de bord</a></p>
    <h2>Gestion de vos projets</h2>
    <p style="margin-top: 20px;"><a href="ajouter.php" class="btn btn-add">➕ Ajouter un nouveau projet</a></p>

    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Titre</th>
                <th>Description</th>
                <th>Technologies</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($projets)): ?>
                <tr>
                    <td colspan="5" style="text-align: center;">Aucun projet enregistré pour le moment.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($projets as $p): ?>
                    <tr>
                        <td>
                            <?php if ($p['image']): ?>
                                <img src="../../<?= echapper($p['image']) ?>" width="80" alt="Miniature">
                            <?php else: ?>
                                Pas d'image
                            <?php endif; ?>
                        </td>
                        <td><?= echapper($p['titre']) ?></td>
                        <td><?= echapper($p['description']) ?></td>
                        <td><?= echapper($p['technologies']) ?></td>
                        <td>
                            <a href="modifier.php?id=<?= $p['id'] ?>" class="btn btn-edit">Modifier</a>
                            <a href="supprimer.php?id=<?= $p['id'] ?>&csrf_token=<?= generer_csrf() ?>" class="btn btn-delete" onclick="return confirm('Voulez-vous vraiment supprimer ce projet ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
