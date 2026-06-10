<?php
require_once '../../config/connexion.php';
require_once '../../fonctions.php';
verifier_authentification();

// Récupération de tous les administrateurs
$stmt = $pdo->query("SELECT id, prenom, nom, email, date_creation FROM administrateurs ORDER BY nom ASC");
$admins = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Administrateurs</title>
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
    <h2>Gestion des comptes administrateurs</h2>
    <p style="margin-top: 20px;"><a href="ajouter.php" class="btn btn-add">➕ Ajouter un administrateur</a></p>

    <table>
        <thead>
            <tr>
                <th>Prénom</th>
                <th>Nom</th>
                <th>Adresse Email</th>
                <th>Date de création</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($admins as $a): ?>
                <tr>
                    <td><?= echapper($a['prenom']) ?></td>
                    <td><?= echapper($a['nom']) ?></td>
                    <td><?= echapper($a['email']) ?></td>
                    <td><?= echapper($a['date_creation']) ?></td>
                    <td>
                        <a href="modifier.php?id=<?= $a['id'] ?>" class="btn btn-edit">Modifier</a>
                        <!-- Sécurité : On empêche un admin de se supprimer lui-même par erreur -->
                        <?php if ($a['id'] !== $_SESSION['admin_id']): ?>
                            <a href="supprimer.php?id=<?= $a['id'] ?>&csrf_token=<?= generer_csrf() ?>" class="btn btn-delete" onclick="return confirm('Supprimer définitivement cet administrateur ?');">Supprimer</a>
                        <?php else: ?>
                            <span style="color: gray; font-style: italic;">Votre compte</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
