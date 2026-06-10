<?php
require_once 'config/connexion.php';
require_once 'fonctions.php';
enregistrer_visite($pdo, 'Contact');

$erreurs = [];
$succes = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Vérification du Token CSRF
    if (!isset($_POST['csrf_token']) || !verifier_csrf($_POST['csrf_token'])) {
        die("Action non autorisée (Échec CSRF).");
    }

    $nom      = trim($_POST['nom'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $message   = trim($_POST['message'] ?? '');

    if (!champ_requis($nom)) $erreurs['nom'] = 'Le nom est obligatoire.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $erreurs['email'] = 'Email invalide.';
    if (!champ_requis($message)) $erreurs['message'] = 'Le message est obligatoire.';

    if (empty($erreurs)) {
        $stmt = $pdo->prepare("INSERT INTO messages_contact (nom, email, message) VALUES (?, ?, ?)");
        $stmt->execute([$nom, $email, $message]);
        $succes = true;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contact</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body class="contacter">
    <?php require 'navigation.php'; ?>
    <main>
        <h1>Me Contacter</h1>
        <?php if ($succes): ?>
            <p style="color: green; font-weight: bold;">Votre message a bien été enregistré !</p>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= generer_csrf() ?>">
            
            <label>Nom complet :</label><br>
            <input type="text" name="nom" required><br><br>

            <label>Adresse Email :</label><br>
            <input type="email" name="email" required><br><br>

            <label>Votre message :</label><br>
            <textarea name="message" rows="5" required></textarea><br><br>

            <button type="submit">Envoyer</button>
        </form>
    </main>
    <?php require 'pied-de-page.php'; ?>
</body>
</html>

