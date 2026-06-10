<?php
require_once '../../config/connexion.php';
require_once '../../fonctions.php';
verifier_authentification();

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
    if (strlen($mdp) < 6) $erreurs[] = "Le mot de passe doit faire au moins 6 caractères.";

    // Vérifier si l'email existe déjà
    if (empty($erreurs)) {
        $stmt = $pdo->prepare("SELECT id FROM administrateurs WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erreurs[] = "Cette adresse email est déjà utilisée par un autre administrateur.";
        }
    }

    // Insertion si tout est correct
    if (empty($erreurs)) {
        // Hachage sécurisé du mot de passe
        $mdp_hache = password_hash($mdp, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO administrateurs (prenom, nom, email, mot_de_passe) VALUES (?, ?, ?, ?)");
        $stmt->execute([$prenom, $nom, $email, $mdp_hache]);

        header('Location: index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un administrateur</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; max-width: 500px; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input { width: 100%; padding: 8px; margin-top: 5px; }
        .error { color: red; }
    </style>
</head>
<body>
    <p><a href="index.php">⬅️ Retour à la liste</a></p>
    <h2>Créer un compte administrateur</h2>

    <?php foreach ($erreurs as $e): ?>
        <p class="error"><?= echapper($e) ?></p>
    <?php endforeach; ?>

    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?= generer_csrf() ?>">

        <label for="prenom">Prénom :</label>
        <input type="text" name="prenom" id="prenom" required>

        <label for="nom">Nom :</label>
        <input type="text" name="nom" id="nom" required>

        <label for="email">Adresse Email :</label>
        <input type="email" name="email" id="email" required>

        <label for="mot_de_passe">Mot de passe :</label>
        <input type="password" name="mot_de_passe" id="mot_de_passe" required placeholder="6 caractères minimum">

        <button type="submit" style="margin-top: 20px; padding: 10px 20px; background: green; color: white; border: none; cursor: pointer;">Créer le compte</button>
    </form>
</body>
</html>
