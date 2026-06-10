<?php
require_once '../../config/connexion.php';
require_once '../../fonctions.php';
verifier_authentification();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$token = $_GET['csrf_token'] ?? '';

// Vérification CSRF
if (!verifier_csrf($token)) {
    die("Action non autorisée.");
}

// Sécurité supplémentaire : impossible de s'auto-supprimer
if ($id === $_SESSION['admin_id']) {
    die("Vous ne pouvez pas supprimer votre propre compte en cours d'utilisation.");
}

$stmt = $pdo->prepare("DELETE FROM administrateurs WHERE id = ?");
$stmt->execute([$id]);

header('Location: index.php');
exit();
