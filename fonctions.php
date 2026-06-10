<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validation de champ
function champ_requis(string $valeur): bool {
    return !empty(trim($valeur));
}

// Protection XSS à l'affichage
function echapper(string $valeur): string {
    return htmlspecialchars(trim($valeur), ENT_QUOTES, 'UTF-8');
}

// Génération du Jeton CSRF
function generer_csrf(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Vérification du Jeton CSRF
function verifier_csrf(string $token): bool {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Journalisation des visites
function enregistrer_visite(PDO $pdo, string $nom_page): void {
    $ip = $_SERVER['REMOTE_ADDR'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    }
    
    $stmt = $pdo->prepare("INSERT INTO visites (adresse_ip, page) VALUES (?, ?)");
    $stmt->execute([$ip, $nom_page]);
}

// Vérification de sécurité pour l'espace Admin
function verifier_authentification(): void {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: /admin/connexion.php');
        exit();
    }
}
?>

