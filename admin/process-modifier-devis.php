<?php
require_once 'functions.php';

// Vérifier l'authentification admin
checkAuth();

require_once '../includes/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $devis_id = $_POST['devis_id'] ?? null;
    $action = $_POST['action'] ?? '';
    
    if (!$devis_id || !is_numeric($devis_id)) {
        $_SESSION['error'] = "ID de devis invalide.";
        header('Location: gestion-devis.php');
        exit;
    }
    
    try {
        $db = Database::getInstance()->getConnection();
        
        // Commencer une transaction
        $db->beginTransaction();
        
        // Mettre à jour les informations du devis
        $stmt = $db->prepare("
            UPDATE devis SET 
                client_nom = ?, client_prenom = ?, client_email = ?, 
                client_societe = ?, client_telephone = ?, client_adresse = ?,
                client_code_postal = ?, client_ville = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $_POST['client_nom'],
            $_POST['client_prenom'], 
            $_POST['client_email'],
            $_POST['client_societe'] ?? '',
            $_POST['client_telephone'] ?? '',
            $_POST['client_adresse'] ?? '',
            $_POST['client_code_postal'] ?? '',
            $_POST['client_ville'] ?? '',
            $devis_id
        ]);
        
        // Supprimer les anciens articles
        $stmt = $db->prepare("DELETE FROM devis_items WHERE devis_id = ?");
        $stmt->execute([$devis_id]);
        
        // Ajouter les nouveaux articles
        $total_ht = 0;
        $total_tva = 0;
        
        if (isset($_POST['articles']) && is_array($_POST['articles'])) {
            foreach ($_POST['articles'] as $article) {
                if (empty($article['nom']) || empty($article['quantite']) || empty($article['prix_unitaire'])) {
                    continue;
                }
                
                $quantite = (int)$article['quantite'];
                $prix_unitaire = (float)$article['prix_unitaire'];
                $taux_tva = (float)($article['taux_tva'] ?? 20);
                
                $ligne_ht = $quantite * $prix_unitaire;
                $ligne_tva = $ligne_ht * ($taux_tva / 100);
                
                $total_ht += $ligne_ht;
                $total_tva += $ligne_tva;
                
                $stmt = $db->prepare("
                    INSERT INTO devis_items (devis_id, nom, description, quantite, prix_unitaire, taux_tva)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $devis_id,
                    $article['nom'],
                    $article['description'] ?? '',
                    $quantite,
                    $prix_unitaire,
                    $taux_tva
                ]);
            }
        }
        
        // Mettre à jour les totaux
        $total_ttc = $total_ht + $total_tva;
        
        $stmt = $db->prepare("
            UPDATE devis SET 
                total_ht = ?, total_tva = ?, total_ttc = ?
            WHERE id = ?
        ");
        
        $stmt->execute([$total_ht, $total_tva, $total_ttc, $devis_id]);
        
        // Valider la transaction
        $db->commit();
        
        $_SESSION['success'] = "Devis modifié avec succès.";
        header('Location: gestion-devis.php');
        
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $db->rollback();
        error_log("Erreur modification devis: " . $e->getMessage());
        $_SESSION['error'] = "Erreur lors de la modification du devis.";
        header('Location: modifier-devis.php?id=' . $devis_id);
    }
} else {
    header('Location: gestion-devis.php');
}

exit;