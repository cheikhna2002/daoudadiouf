<?php
require_once '../../config/connexion.php';
require_once '../../fonctions.php';
verifier_authentification();

$id_details = isset($_GET['detail_id']) ? (int)$_GET['detail_id'] : 0;
$demande_selectionnee = null;

if ($id_details > 0) {
    $stmt_update = $pdo->prepare("UPDATE demandes_projet SET lu = 1 WHERE id = ?");
    $stmt_update->execute([$id_details]);

    $stmt_select = $pdo->prepare("SELECT * FROM demandes_projet WHERE id = ?");
    $stmt_select->execute([$id_details]);
    $demande_selectionnee = $stmt_select->fetch();
}

$stmt = $pdo->query("SELECT * FROM demandes_projet ORDER BY date_demande DESC");
$demandes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Demandes de Projet</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .liste-demandes { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .liste-demandes th, .liste-demandes td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .liste-demandes th { background-color: #0056b3; color: white; }
        .non-lu { background-color: #d1ecf1; font-weight: bold; }
        .lu { background-color: #ffffff; }
        .box-lecture { background: #f8f9fa; border: 1px solid #0056b3; padding: 20px; margin-bottom: 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <p><a href="../dashboard.php">📊 Retour au Tableau de bord</a></p>
    <h2>Demandes de projet de vos clients</h2>

    <?php if ($demande_selectionnee): ?>
        <div class="box-lecture">
            <h3>Demande de : <?= echapper($demande_selectionnee['nom']) ?></h3>
            <p><strong>Email :</strong> <?= echapper($demande_selectionnee['email']) ?></p>
            <p><strong>Type de projet cherché :</strong> <?= echapper($demande_selectionnee['type_projet']) ?></p>
            <p><strong>Budget estimé :</strong> <?= echapper($demande_selectionnee['budget'] ?? 'Non spécifié') ?></p>
            <p><strong>Reçu le :</strong> <?= $demande_selectionnee['date_demande'] ?></p>
            <hr>
            <p><strong>Description du besoin :</strong></p>
            <p><?= nl2br(echapper($demande_selectionnee['description'])) ?></p>
        </div>
    <?php endif; ?>

    <table class="liste-demandes">
        <thead>
            <tr>
                <th>Date</th>
                <th>Nom</th>
                <th>Type de Projet</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($demandes as $d): ?>
                <tr class="<?= $d['lu'] ? 'lu' : 'non-lu' ?>">
                    <td><?= $d['date_demande'] ?></td>
                    <td><?= echapper($d['nom']) ?></td>
                    <td><?= echapper($d['type_projet']) ?></td>
                    <td><?= $d['lu'] ? 'Traitée ✅' : 'En attente ⏱️' ?></td>
                    <td><a href="index.php?detail_id=<?= $d['id'] ?>">Voir la demande</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
