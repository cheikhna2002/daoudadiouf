<?php
$host = '://sql304.infinityfree.com';
$db   = 'if0_42130761_portfolio';
$user = 'if0_42130761'; // Changez selon votre configuration
$pass = 'ivohJDdws5UgLj';     // Changez selon votre configuration
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // On écrit l'erreur réelle dans le fichier log du serveur
     error_log($e->getMessage());
     // Message générique pour l'utilisateur
     die("Une erreur technique est survenue. Veuillez réessayer plus tard.");
}
