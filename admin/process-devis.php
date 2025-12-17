<?php
// Désactiver l'affichage des erreurs PHP pour ne pas polluer le JSON
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

require_once 'functions.php';
require_once '../includes/database.php';

// Vérification de l'authentification admin
if (!checkAuth()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit;
}

header('Content-Type: application/json');

try {
    $pdo = Database::getInstance()->getConnection();
    $pdo->beginTransaction();
    
    // Gestion du client
    $client_id = null;
    
    if ($_POST['type_client'] === 'existant') {
        $client_id = $_POST['client_id'];
        if (!$client_id) {
            throw new Exception('Client non sélectionné');
        }
    } else {
        // Créer un nouveau client
        $stmt = $pdo->prepare("
            INSERT INTO clients (nom, prenom, email, telephone, societe, adresse, ville, code_postal, adresse_livraison) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $_POST['nouveau_nom'],
            $_POST['nouveau_prenom'], 
            $_POST['nouveau_email'],
            $_POST['nouveau_telephone'] ?? '',
            $_POST['nouveau_societe'] ?? '',
            $_POST['nouveau_adresse'] ?? '',
            $_POST['nouveau_ville'] ?? '',
            $_POST['nouveau_code_postal'] ?? '',
            $_POST['adresse_livraison'] ?? ''
        ]);
        
        $client_id = $pdo->lastInsertId();
    }
    
    // Créer le devis
    $numero_devis = 'DEV' . date('Y') . sprintf('%04d', rand(1000, 9999));
    
    $stmt = $pdo->prepare("
        INSERT INTO devis (numero, client_id, date_creation, adresse_facturation, adresse_livraison, 
                          notes, total_ht, frais_port, total_ttc, statut) 
        VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, 'brouillon')
    ");
    
    // Calculer les totaux
    $total_ht = 0;
    $designations = $_POST['designation'] ?? [];
    $quantites = $_POST['quantite'] ?? [];
    $prix_unitaires = $_POST['prix_unitaire'] ?? [];
    $remise_valeurs = $_POST['remise_valeur'] ?? [];
    $remise_types = $_POST['remise_type'] ?? [];
    
    for ($i = 0; $i < count($designations); $i++) {
        $quantite = floatval($quantites[$i]);
        $prix_unitaire = floatval($prix_unitaires[$i]);
        $remise_valeur = floatval($remise_valeurs[$i] ?? 0);
        $remise_type = $remise_types[$i] ?? 'percent';
        
        $sous_total = $quantite * $prix_unitaire;
        $remise_montant = 0;
        
        if ($remise_valeur > 0) {
            if ($remise_type === 'percent') {
                $remise_montant = $sous_total * ($remise_valeur / 100);
            } else {
                $remise_montant = $remise_valeur * $quantite;
            }
        }
        
        $total_ligne = max(0, $sous_total - $remise_montant);
        $total_ht += $total_ligne;
    }
    
    $frais_port = floatval($_POST['frais_port'] ?? 0);
    $sous_total_avec_port = $total_ht + $frais_port;
    $tva = $sous_total_avec_port * 0.20;
    $total_ttc = $sous_total_avec_port + $tva;
    
    $stmt->execute([
        $numero_devis,
        $client_id,
        $_POST['adresse_facturation'] ?? '',
        $_POST['adresse_livraison'] ?? '',
        $_POST['notes'] ?? '',
        $total_ht,
        $frais_port,
        $total_ttc
    ]);
    
    $devis_id = $pdo->lastInsertId();
    
    // Ajouter les articles du devis
    $stmt_article = $pdo->prepare("
        INSERT INTO devis_items (devis_id, produit_id, designation, description, quantite, 
                               prix_unitaire, remise_type, remise_valeur, total_ligne) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $descriptions = $_POST['description'] ?? [];
    $produit_ids = $_POST['produit_id'] ?? [];
    
    for ($i = 0; $i < count($designations); $i++) {
        if (empty(trim($designations[$i]))) continue;
        
        $quantite = floatval($quantites[$i]);
        $prix_unitaire = floatval($prix_unitaires[$i]);
        $remise_valeur = floatval($remise_valeurs[$i] ?? 0);
        $remise_type = $remise_types[$i] ?? 'percent';
        
        $sous_total = $quantite * $prix_unitaire;
        $remise_montant = 0;
        
        if ($remise_valeur > 0) {
            if ($remise_type === 'percent') {
                $remise_montant = $sous_total * ($remise_valeur / 100);
            } else {
                $remise_montant = $remise_valeur * $quantite;
            }
        }
        
        $total_ligne = max(0, $sous_total - $remise_montant);
        
        $stmt_article->execute([
            $devis_id,
            !empty($produit_ids[$i]) ? $produit_ids[$i] : null,
            $designations[$i],
            $descriptions[$i] ?? '',
            $quantite,
            $prix_unitaire,
            $remise_type,
            $remise_valeur,
            $total_ligne
        ]);
    }
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Devis créé avec succès',
        'devis_id' => $devis_id,
        'numero' => $numero_devis
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur lors de la création du devis : ' . $e->getMessage()
    ]);
}
?>