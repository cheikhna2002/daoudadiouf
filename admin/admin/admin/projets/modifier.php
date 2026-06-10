<?php
require_once '../../config/connexion.php';
require_once '../../fonctions.php';
verifier_authentification();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM projets WHERE id = ?");
$stmt->execute([$id]);
$projet = $stmt->fetch();

if (!$projet) {
    die("Projet introuvable.");
}

$erreurs = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || !verifier_csrf($_POST['csrf_token'])) {
        die("Échec CSRF.");
    }

    $titre        = trim($_POST['titre'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $technologies = trim($_POST['technologies'] ?? '');
    $lien         = trim($_POST['lien'] ?? '');
    $image_chemin = $projet['image']; // Par défaut on garde l'ancienne image

    if (!champ_requis($titre)) $erreurs[] = "Le titre est obligatoire.";
    if (!champ_requis($description)) $erreurs[] = "La description est obligatoire.";
    if (!champ_requis($technologies)) $erreurs[] = "Les technologies sont obligatoires.";

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $extensions_autorisees = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (in_array($extension, $extensions_autorisees) && $_FILES['image']['size'] <= 2 * 1024 * 1024) {
            // On supprime l'ancienne image physiquement du serveur s'il y en avait une
            if ($projet['image'] && file_exists('../../' . $projet['image'])) {
                unlink('../../' . $projet['image']);
            }
            
            $nouveau_nom = uniqid('projet_', true) . '.' . $extension;
            move_uploaded_file($_FILES['image']['tmp_name'], '../../images/projets/' . $nouveau_nom);
            $image_chemin = 'images/projets/' . $nouveau_nom;
        } else {
            $erreurs[] = "Fichier image invalide ou trop lourd.";
        }
    }

    if (empty($erreurs)) {
        $stmt = $pdo->prepare("UPDATE projets SET titre = ?, description = ?, technologies = ?, image = ?, lien = ? WHERE id = ?");
        $stmt->execute([$titre, $description, $technologies, $image_chemin, !empty($lien) ? $lien : null, $id]);
        header('Location: index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un projet</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; max-width: 600px; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input, textarea { width: 100%; padding: 8px; margin-top: 5px; }
    </style>
</head>
<body>
    <p><a href="index.php">⬅️ Annuler</a></p>
    <h2>Modifier le projet : <?= echapper($projet['titre']) ?></h2>

    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generer_csrf() ?>">

        <label>Titre :</label>
        <input type="text" name="titre" value="<?= echapper($projet['titre']) ?>" required>

        <label>Description :</label>
        <textarea name="description" rows="5" required><?= echapper($projet['description']) ?></textarea>

        <label>Technologies :</label>
        <input type="text" name="technologies" value="<?= echapper($projet['technologies']) ?>" required>

        <label>Image actuelle :</label><br>
        <?php if ($projet['image']): ?>
            <img src="../../<?= echapper($projet['image']) ?>" width="150" alt="Illustration"><br>
        <?php endif; ?>
        <input type="file" name="image">

        <label>Lien externe :</label>
        <input type="url" name="lien" value="<?= echapper($projet['lien'] ?? '') ?>">

        <button type="submit" style="margin-top: 20px; padding: 10px 20px; background: #007BFF; color: white; border: none; cursor: pointer;">Mettre à jour</button>
    </form>
</body>
</html>
