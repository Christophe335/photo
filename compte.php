<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['client_id']) && !empty($_SESSION['client_id'])) {
    // Utilisateur connecté -> rediriger vers mon compte
    header('Location: clients/mon-compte.php');
} else {
    // Utilisateur non connecté -> rediriger vers connexion
    header('Location: clients/connexion.php');
}
exit;
?>