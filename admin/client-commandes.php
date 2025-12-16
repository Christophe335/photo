<?php
// Redirection vers client-details.php car les commandes y sont déjà affichées
$client_id = $_GET['id'] ?? 0;

if ($client_id) {
    header('Location: client-details.php?id=' . $client_id . '#commandes');
} else {
    header('Location: gestion-clients.php');
}
exit;
?>