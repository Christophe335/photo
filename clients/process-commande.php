<?php
session_start();
require_once '../includes/database.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['client_id'])) {
    $_SESSION['redirect_after_login'] = '../pages/panier.php';
    header('Location: connexion.php');
    exit;
}

// Vérifier si le panier existe et n'est pas vide
if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
    $_SESSION['error_message'] = "Votre panier est vide.";
    header('Location: ../pages/panier.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/panier.php');
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Récupérer les informations du client
    $stmt = $db->prepare("
        SELECT prenom, nom, email, adresse, code_postal, ville, pays,
               adresse_livraison_differente, adresse_livraison, 
               code_postal_livraison, ville_livraison, pays_livraison
        FROM clients 
        WHERE id = ?
    ");
    $stmt->execute([$_SESSION['client_id']]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$client) {
        throw new Exception("Client non trouvé.");
    }
    
    // Calculer les totaux
    $sous_total = 0;
    foreach ($_SESSION['panier'] as $item) {
        $sous_total += $item['prix'] * $item['quantite'];
    }
    
    $tva = $sous_total * 0.20; // TVA 20%
    $frais_livraison = ($sous_total > 200) ? 0 : 13.95; // Livraison gratuite à partir de 200€ HT
    $total = $sous_total + $tva + $frais_livraison;
    
    // Récupérer les données du formulaire
    $mode_paiement = $_POST['mode_paiement'] ?? 'carte_bancaire';
    $commentaire_client = trim($_POST['commentaire'] ?? '');
    $utiliser_adresse_facturation = isset($_POST['utiliser_adresse_facturation']);
    
    // Déterminer les adresses
    $adresse_facturation = $client['adresse'];
    $code_postal_facturation = $client['code_postal'];
    $ville_facturation = $client['ville'];
    $pays_facturation = $client['pays'];
    
    if ($client['adresse_livraison_differente'] && !$utiliser_adresse_facturation) {
        $adresse_livraison = $client['adresse_livraison'];
        $code_postal_livraison = $client['code_postal_livraison'];
        $ville_livraison = $client['ville_livraison'];
        $pays_livraison = $client['pays_livraison'];
    } else {
        $adresse_livraison = $adresse_facturation;
        $code_postal_livraison = $code_postal_facturation;
        $ville_livraison = $ville_facturation;
        $pays_livraison = $pays_facturation;
    }
    
    // Commencer une transaction
    $db->beginTransaction();
    
    // Générer un numéro de commande unique
    $numero_commande = date('Ymd') . sprintf('%04d', mt_rand(1, 9999));
    
    // Vérifier l'unicité du numéro
    $stmt = $db->prepare("SELECT id FROM commandes WHERE numero_commande = ?");
    $stmt->execute([$numero_commande]);
    while ($stmt->fetch()) {
        $numero_commande = date('Ymd') . sprintf('%04d', mt_rand(1, 9999));
        $stmt->execute([$numero_commande]);
    }
    
    // Insérer la commande
    $stmt = $db->prepare("
        INSERT INTO commandes (
            client_id, numero_commande, statut, sous_total, tva, frais_livraison, total,
            adresse_facturation, code_postal_facturation, ville_facturation, pays_facturation,
            adresse_livraison, code_postal_livraison, ville_livraison, pays_livraison,
            mode_paiement, statut_paiement, commentaire_client, date_commande
        ) VALUES (?, ?, 'en_attente', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'en_attente', ?, NOW())
    ");
    
    $stmt->execute([
        $_SESSION['client_id'], $numero_commande, $sous_total, $tva, $frais_livraison, $total,
        $adresse_facturation, $code_postal_facturation, $ville_facturation, $pays_facturation,
        $adresse_livraison, $code_postal_livraison, $ville_livraison, $pays_livraison,
        $mode_paiement, $commentaire_client
    ]);
    
    $commande_id = $db->lastInsertId();
    
    // Insérer les items de la commande
    $stmt = $db->prepare("
        INSERT INTO commande_items (
            commande_id, produit_code, designation, format, couleur, conditionnement,
            quantite, prix_unitaire, total_ligne, donnees_produit
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($_SESSION['panier'] as $item) {
        $total_ligne = $item['prix'] * $item['quantite'];
        $donnees_produit = json_encode($item);
        
        $stmt->execute([
            $commande_id,
            $item['details']['code'] ?? '',
            $item['details']['designation'] ?? '',
            $item['details']['format'] ?? '',
            $item['details']['couleur'] ?? '',
            $item['details']['conditionnement'] ?? '',
            $item['quantite'],
            $item['prix'],
            $total_ligne,
            $donnees_produit
        ]);
    }
    
    // Ajouter une entrée dans l'historique
    $stmt = $db->prepare("
        INSERT INTO commande_historique (commande_id, nouveau_statut, commentaire)
        VALUES (?, 'en_attente', 'Commande créée')
    ");
    $stmt->execute([$commande_id]);
    
    // Valider la transaction
    $db->commit();
    
    // Vider le panier
    unset($_SESSION['panier']);
    
    // Rediriger vers la page de confirmation
    $_SESSION['success_message'] = "Votre commande n°$numero_commande a été enregistrée avec succès !";
    header("Location: confirmation-commande.php?numero=$numero_commande");
    exit;
    
} catch (Exception $e) {
    // Annuler la transaction en cas d'erreur
    if (isset($db)) {
        $db->rollback();
    }
    
    error_log("Erreur validation commande: " . $e->getMessage());
    $_SESSION['error_message'] = "Une erreur s'est produite lors de la validation de votre commande. Veuillez réessayer.";
    header('Location: ../pages/panier.php');
    exit;
}
?>