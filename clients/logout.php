<?php
session_start();

// Détruire toutes les variables de session liées au client
unset($_SESSION['client_id']);
unset($_SESSION['client_nom']);
unset($_SESSION['client_prenom']);
unset($_SESSION['client_email']);

// Message de confirmation
$_SESSION['logout_message'] = "Vous avez été déconnecté avec succès.";

// Redirection vers la page de connexion
header('Location: connexion.php');
exit;
?>