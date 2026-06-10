<?php
require_once '../fonctions.php';
$_SESSION = array();
session_destroy();
header('Location: connexion.php');
exit();
?>
