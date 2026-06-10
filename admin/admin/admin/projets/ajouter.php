<?php
require_once '../../config/connexion.php';
require_once '../../fonctions.php';
verifier_authentification();

$erreurs = [];
$succes = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || !verifier_csrf($_POST['csrf_token'])) {
        die("Échec de la sécurité CSRF.");
    }

    $titre        = trim($_POST['titre'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $technologies = trim($_POST['technologies'] ?? '');
    $lien         = trim($_POST['lien'] ?? '');
    $image_chemin = null;

    if (!champ_requis($titre)) $erreurs[] = "Le titre est obligatoire.";
    if (!champ_requis($description)) $erreurs[] = "La description est obligatoire.";
    if (!champ_requis($technologies)) $erreurs[] = "Les technologies sont obligatoires.";

    // Gestion sécurisée du téléchargement de l'image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $extensions_autorisees = ['jpg', 'jpeg', 'png', 'webp'];
        $infos_fichier = pathinfo($_FILES['image']['name']);
        $extension = strtolower($infos_fichier['extension']);

        if (!in_array($extension, $extensions_autorisees)) {
            $erreurs[] = "Format d'image interdit. Autorisés : JPG, JPEG, PNG, WEBP.";
        }

        if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $erreurs[] = "L'image est trop lourde (maximum 2 Mo).";
        }

        if (empty($erreurs)) {
            // On renomme l'image avec un identifiant unique pour éviter les doublons
            $nouveau_nom = uniqid('projet_', true) . '.' . $extension;
            $dossier_destination = '../../images/projets/' . $nouveau_nom;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dossier_destination)) {
                $image_chemin = 'images/projets/' . $nouveau_nom;
            } else {
                $erreurs[] = "Erreur lors du déplacement de l'image.";
            }
        }
    }

    if (empty($erreurs)) {
        $stmt = $pdo->prepare("INSERT INTO projets (titre, description, technologies, image, lien) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$titre, $description, $technologies, $image_chemin, !empty($lien) ? $lien : null]);
        $succes = true;
        header('Location: index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un projet</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; max-width: 600px; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input, textarea { width: 100%; padding: 8px; margin-top: 5px; }
        .error { color: red; }
    </style>
</head>
<body>
    <p><a href="index.php">⬅️ Retour à la liste</a></p>
    <h2>Ajouter un nouveau projet</h2>

    <?php foreach ($erreurs as $e): ?>
        <p class="error"><?= echapper($e) ?></p>
    <?php endforeach; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= generer_csrf() ?>">

        <label for="titre">Titre du projet :</label>
        <input type="text" name="titre" id="titre" required>

        <label for="description">Description :</label>
        <textarea name="description" id="description" rows="5" required></textarea>

        <label for="technologies">Technologies (séparées par des virgules) :</label>
        <input type="text" name="technologies" id="technologies" placeholder="HTML, CSS, PHP" required>

        <label for="image">Image d'illustration (Optionnel) :</label>
        <input type="file" name="image" id="image">

        <label for="lien">Lien vers le projet (Optionnel) :</label>
        <input type="url" name="lien" id="lien" placeholder="https://...">

        <button type="submit" style="margin-top: 20px; padding: 10px 20px; background: green; color: white; border: none; cursor: pointer;">Enregistrer le projet</button>
    </form>
</body>
</html>
