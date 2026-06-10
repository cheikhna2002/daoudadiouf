<?php
require_once '../../config/connexion.php';
require_once '../../fonctions.php';
verifier_authentification();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM administrateurs WHERE id = ?");
$stmt->execute([$id]);
$admin = $stmt->fetch();

if (!$admin) {
    die("Administrateur introuvable.");
}

$erreurs = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['csrf_token']) || !verifier_csrf($_POST['csrf_token'])) {
        die("Échec CSRF.");
    }

    $prenom = trim($_POST['prenom'] ?? '');
    $nom    = trim($_POST['nom'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $mdp    = $_POST['mot_de_passe'] ?? '';

    if (!champ_requis($prenom)) $erreurs[] = "Le prénom est obligatoire.";
    if (!champ_requis($nom)) $erreurs[] = "Le nom est obligatoire.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $erreurs[] = "L'adresse email n'est pas valide.";

    // Vérifier si le nouvel email n'est pas déjà pris par un AUTRE utilisateur
    if (empty($erreurs)) {
        $stmt = $pdo->prepare("SELECT id FROM administrateurs WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->fetch()) {
            $erreurs[] = "Cette adresse email est déjà prise.";
        }
    }

    if (empty($erreurs)) {
        // RÈGLE OBLIGATOIRE : Si le mot de passe est fourni, on le hache, sinon on garde l'ancien hash
        if (!empty($mdp)) {
            if (strlen($mdp) < 6) {
                $erreurs[] = "Le nouveau mot de passe doit faire au moins 6 caractères.";
            } else {
                $mdp_final = password_hash($mdp, PASSWORD_BCRYPT);
            }
        } else {
            $mdp_final = $admin['mot_de_passe'];
        }
    }

    if (empty($erreurs)) {
        $stmt = $pdo->prepare("UPDATE administrateurs SET prenom = ?, nom = ?, email = ?, mot_de_passe = ? WHERE id = ?");
        $stmt->execute([$prenom, $nom, $email, $mdp_final, $id]);

        // Si l'admin connecté modifie son propre prénom, on met à jour sa session d'affichage
        if ($id === $_SESSION['admin_id']) {
            $_SESSION['admin_prenom'] = $prenom;
        }

        header('Location: index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'administrateur</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; max-width: 500px; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input { width: 100%; padding: 8px; margin-top: 5px; }
        .error { color: red; }
    </style>
</head>
<body>
    <p><a href="index.php">⬅️ Annuler</a></p>
    <h2>Modifier le profil de : <?= echapper($admin['prenom']) ?></h2>

    <?php foreach ($erreurs as $e): ?>
        <p class="error"><?= echapper($e) ?></p>
    <?php endforeach; ?>

    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?= generer_csrf() ?>">

        <label>Prénom :</label>
        <input type="text" name="prenom" value="<?= echapper($admin['prenom']) ?>" required>

        <label>Nom :</label>
        <input type="text" name="nom" value="<?= echapper($admin['nom']) ?>" required>

        <label>Adresse Email :</label>
        <input type="email" name="email" value="<?= echapper($admin['email']) ?>" required>

        <label>Nouveau mot de passe :</label>
        <input type="password" name="mot_de_passe" placeholder="Laissez vide pour conserver l'ancien mot de passe">

        <button type="submit" style="margin-top: 20px; padding: 10px 20px; background: #007BFF; color: white; border: none; cursor: pointer;">Mettre à jour</button>
    </form>
</body>
</html>
