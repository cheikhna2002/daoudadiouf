<?php
require_once '../../config/connexion.php';
require_once '../../fonctions.php';
verifier_authentification();

// Marquer un message comme lu si son ID est en GET
$id_details = isset($_GET['detail_id']) ? (int)$_GET['detail_id'] : 0;
$message_selectionne = null;

if ($id_details > 0) {
    // 1. Mettre à jour le statut en base de données
    $stmt_update = $pdo->prepare("UPDATE messages_contact SET lu = 1 WHERE id = ?");
    $stmt_update->execute([$id_details]);

    // 2. Récupérer le contenu de ce message pour l'afficher
    $stmt_select = $pdo->prepare("SELECT * FROM messages_contact WHERE id = ?");
    $stmt_select->execute([$id_details]);
    $message_selectionne = $stmt_select->fetch();
}

// Récupération de tous les messages (du plus récent au plus ancien)
$stmt = $pdo->query("SELECT * FROM messages_contact ORDER BY date_envoi DESC");
$messages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Messages de Contact</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .liste-messages { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .liste-messages th, .liste-messages td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .liste-messages th { background-color: #333; color: white; }
        /* Style demandé : Distinction visuelle claire */
        .non-lu { background-color: #fff3cd; font-weight: bold; }
        .lu { background-color: #ffffff; }
        .box-lecture { background: #f8f9fa; border: 1px solid #ccc; padding: 20px; margin-bottom: 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <p><a href="../dashboard.php">📊 Retour au Tableau de bord</a></p>
    <h2>Messages de contact reçus</h2>

    <!-- Affichage du message ouvert -->
    <?php if ($message_selectionne): ?>
        <div class="box-lecture">
            <h3>Message de : <?= echapper($message_selectionne['nom']) ?> (<?= echapper($message_selectionne['email']) ?>)</h3>
            <p><strong>Reçu le :</strong> <?= $message_selectionne['date_envoi'] ?></p>
            <hr>
            <p><?= nl2br(echapper($message_selectionne['message'])) ?></p>
        </div>
    <?php endif; ?>

    <table class="liste-messages">
        <thead>
            <tr>
                <th>Date</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($messages as $m): ?>
                <tr class="<?= $m['lu'] ? 'lu' : 'non-lu' ?>">
                    <td><?= $m['date_envoi'] ?></td>
                    <td><?= echapper($m['nom']) ?></td>
                    <td><?= echapper($m['email']) ?></td>
                    <td><?= $m['lu'] ? 'Lu ✅' : 'Nouveau ✉️' ?></td>
                    <td><a href="index.php?detail_id=<?= $m['id'] ?>">Ouvrir / Lire</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
