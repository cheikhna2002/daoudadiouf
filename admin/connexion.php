<?php
require_once '../config/connexion.php';
require_once '../fonctions.php';

$erreur = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $mdp   = $_POST['mot_de_passe'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM administrateurs WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($mdp, $admin['mot_de_passe'])) {
        session_regenerate_id(true); // Protection contre la fixation de session
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_prenom'] = $admin['prenom'];
        header('Location: dashboard.php');
        exit();
    } else {
        $erreur = "Identifiants incorrects.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Administration</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 50px; text-align: center; }
        .login-box { background: white; padding: 30px; display: inline-block; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Connexion Admin</h2>
        <?php if ($erreur): ?><p style="color: red;"><?= echapper($erreur) ?></p><?php endif; ?>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Email" required><br><br>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required><br><br>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>
