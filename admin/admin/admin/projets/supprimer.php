<?php
require_once '../../config/connexion.php';
require_once '../../fonctions.php';
verifier_authentification();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$token = $_GET['csrf_token'] ?? '';

// Vérification de sécurité stricte
if (!verifier_csrf($token)) {
    die("Action refusée : Jeton de sécurité invalide.");
}

// Recherche du projet pour trouver son image
$stmt = $pdo->prepare("SELECT image FROM projets WHERE id = ?");
$stmt->execute([$id]);
$projet = $stmt->fetch();

if ($projet) {
    // Suppression physique de l'image sur le disque dur
    if ($projet['image'] && file_exists('../../' . $projet['image'])) {
        unlink('../../' . $projet['image']);
    }

    // Suppression de l'enregistrement en BDD
    $stmt = $pdo->prepare("DELETE FROM projets WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: index.php');
exit();
