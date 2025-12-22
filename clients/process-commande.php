<?php
session_start();
require_once '../includes/database.php';

// Debug: dump POST and session panier au début pour diagnostiquer
try {
    $storageDir = __DIR__ . '/../storage';
    if (!is_dir($storageDir)) @mkdir($storageDir, 0777, true);
    $meta = [
        'time' => time(),
        'POST_keys' => array_keys($_POST),
        'POST_panier_complet_present' => isset($_POST['panier_complet']),
        'SESSION_panier_count' => isset($_SESSION['panier']) ? count($_SESSION['panier']) : 0
    ];
    @file_put_contents($storageDir . '/debug_process_commande_meta_' . time() . '.json', json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    // full dumps
    @file_put_contents($storageDir . '/debug_process_commande_POST_' . time() . '.json', json_encode($_POST, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    @file_put_contents($storageDir . '/debug_process_commande_SESSION_panier_' . time() . '.json', json_encode($_SESSION['panier'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    error_log('process-commande: debug meta écrit');
} catch (Exception $e) {
    error_log('process-commande: erreur écriture debug meta: ' . $e->getMessage());
}

// Si le panier session est vide mais que le client a posté un `panier_complet` (localStorage), l'utiliser
if ((empty($_SESSION['panier']) || !isset($_SESSION['panier'])) && isset($_POST['panier_complet']) && !empty($_POST['panier_complet'])) {
    $panierClient = json_decode($_POST['panier_complet'], true);
    if (is_array($panierClient) && !empty($panierClient)) {
        $_SESSION['panier'] = $panierClient;
    }
}

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

    // Déterminer les frais de livraison avant le calcul de la TVA
    $frais_livraison = ($sous_total > 200) ? 0 : 13.95; // Livraison gratuite à partir de 200€ HT
    // Calculer la TVA sur le total HT + frais de port (TVA applicable aux frais de port)
    $tva = ($sous_total + $frais_livraison) * 0.20; // TVA 20%
    $total = $sous_total + $frais_livraison + $tva;
    
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
    
    // Construire la liste des fichiers uploadés (photos/personnalisations) depuis le panier
    $fichiersUploades = [];
    $panierPourEmail = $_SESSION['panier'];
    foreach ($panierPourEmail as $item) {
        // plusieurs formats possibles : top-level 'photos' ou dans 'details' => 'photos'
        $candidates = [];
        if (!empty($item['photos']) && is_array($item['photos'])) $candidates[] = $item['photos'];
        if (!empty($item['details']['photos']) && is_array($item['details']['photos'])) $candidates[] = $item['details']['photos'];

        foreach ($candidates as $photosList) {
            foreach ($photosList as $p) {
                $fichiersUploades[] = $p;
            }
        }
    }

    // Normaliser les objets dataUrl en contenu binaire pour l'envoi par email
    foreach ($fichiersUploades as $k => $f) {
        if (is_array($f)) {
            // support: dataUrl, dataUrl base64, data_url, originalDataUrl, file object
            $dataUrl = $f['dataUrl'] ?? $f['data_url'] ?? $f['originalDataUrl'] ?? ($f['data'] ?? null);
            if (is_string($dataUrl) && preg_match('#^data:([^;]+);base64,(.*)$#', $dataUrl, $m)) {
                $mime = $m[1];
                $base64 = $m[2];
                $decoded = base64_decode($base64);
                if ($decoded !== false) {
                    $name = $f['name'] ?? $f['nom'] ?? ('file_' . uniqid());
                    $fichiersUploades[$k] = [
                        'name' => $name,
                        'type' => $mime,
                        'content' => $decoded
                    ];
                    continue;
                }
            }
            // If content already provided
            if (isset($f['content']) && is_string($f['content'])) {
                // keep as is
                continue;
            }
        }
        // If it's a string path or filename, keep as-is
    }

    // Dump debug des fichiers uploadés (avant envoi)
    try {
        $storageDir = __DIR__ . '/../storage';
        if (!is_dir($storageDir)) @mkdir($storageDir, 0777, true);
        $dumpFile = $storageDir . '/debug_fichiers_uploades_' . time() . '.json';
        @file_put_contents($dumpFile, json_encode($fichiersUploades, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        error_log('process-commande: dump fichiersUploades écrit dans ' . $dumpFile);
    } catch (Exception $e) {
        error_log('process-commande: erreur écriture dump fichiersUploades: ' . $e->getMessage());
    }

    // Envoyer emails (webmaster + client) via EmailManager
    try {
        require_once __DIR__ . '/../includes/email-manager.php';
        $emailManager = new EmailManager();

        // Construire un tableau client complet pour l'email (inclut adresses)
        $clientInfo = [
            'prenom' => $client['prenom'] ?? '',
            'nom' => $client['nom'] ?? '',
            'email' => $client['email'] ?? '',
            'telephone' => $client['telephone'] ?? '',
            'societe' => $client['societe'] ?? '',
            'adresse_facturation' => [
                'ligne1' => $adresse_facturation ?? ($client['adresse'] ?? ''),
                'code_postal' => $code_postal_facturation ?? ($client['code_postal'] ?? ''),
                'ville' => $ville_facturation ?? ($client['ville'] ?? ''),
                'pays' => $pays_facturation ?? ($client['pays'] ?? '')
            ],
            'adresse_livraison' => [
                'ligne1' => $adresse_livraison ?? ($client['adresse_livraison'] ?? ($adresse_facturation ?? '')),
                'code_postal' => $code_postal_livraison ?? ($client['code_postal_livraison'] ?? ($code_postal_facturation ?? ($client['code_postal'] ?? ''))),
                'ville' => $ville_livraison ?? ($client['ville_livraison'] ?? ($ville_facturation ?? ($client['ville'] ?? ''))),
                'pays' => $pays_livraison ?? ($client['pays_livraison'] ?? ($pays_facturation ?? ($client['pays'] ?? '')))
            ],
        ];

        // Debug: log détaillé des fichiers uploadés avant envoi
        error_log('process-commande: fichiersUploades (count=' . count($fichiersUploades) . '): ' . json_encode($fichiersUploades));

        // Envoyer au webmaster (avec pièces jointes)
        $sentWebmaster = $emailManager->envoyerConfirmationCommande($panierPourEmail, $fichiersUploades, $numero_commande, $clientInfo);
        error_log('process-commande: envoi email webmaster retour: ' . var_export($sentWebmaster, true));

        // Envoyer au client (liste des fichiers, sans pièces jointes)
        if (!empty($client['email']) && filter_var($client['email'], FILTER_VALIDATE_EMAIL)) {
            $sentClient = $emailManager->envoyerConfirmationClient($client['email'], $panierPourEmail, $fichiersUploades, $numero_commande, $clientInfo);
            error_log('process-commande: envoi email client retour: ' . var_export($sentClient, true));
        }
    } catch (Exception $e) {
        error_log('Erreur envoi email commande: ' . $e->getMessage());
    }

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