<?php
session_start();
require_once '../../includes/database.php';

header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['client_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Récupération et validation des données
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    
    // Adresse de facturation
    $adresse = trim($_POST['adresse'] ?? '');
    $code_postal = trim($_POST['code_postal'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $pays = $_POST['pays'] ?? 'France';
    
    // Adresse de livraison
    $adresse_livraison_differente = isset($_POST['adresse_livraison_differente']) ? 1 : 0;
    $adresse_livraison = $adresse_livraison_differente ? trim($_POST['adresse_livraison'] ?? '') : null;
    $code_postal_livraison = $adresse_livraison_differente ? trim($_POST['code_postal_livraison'] ?? '') : null;
    $ville_livraison = $adresse_livraison_differente ? trim($_POST['ville_livraison'] ?? '') : null;
    $pays_livraison = $adresse_livraison_differente ? ($_POST['pays_livraison'] ?? 'France') : null;
    
    // Mots de passe
    $mot_de_passe_actuel = $_POST['mot_de_passe_actuel'] ?? '';
    $nouveau_mot_de_passe = $_POST['nouveau_mot_de_passe'] ?? '';
    $confirmer_mot_de_passe = $_POST['confirmer_mot_de_passe'] ?? '';
    
    // Préférences
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    
    // Validation des champs obligatoires
    $errors = [];
    
    if (empty($prenom)) {
        $errors[] = "Le prénom est requis.";
    }
    
    if (empty($nom)) {
        $errors[] = "Le nom est requis.";
    }
    
    if (empty($email)) {
        $errors[] = "L'adresse e-mail est requise.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format d'adresse e-mail invalide.";
    }
    
    if (empty($adresse)) {
        $errors[] = "L'adresse est requise.";
    }
    
    if (empty($code_postal)) {
        $errors[] = "Le code postal est requis.";
    }
    
    if (empty($ville)) {
        $errors[] = "La ville est requise.";
    }
    
    // Validation de l'adresse de livraison si différente
    if ($adresse_livraison_differente) {
        if (empty($adresse_livraison)) {
            $errors[] = "L'adresse de livraison est requise.";
        }
        if (empty($code_postal_livraison)) {
            $errors[] = "Le code postal de livraison est requis.";
        }
        if (empty($ville_livraison)) {
            $errors[] = "La ville de livraison est requise.";
        }
    }
    
    // Validation des mots de passe
    $update_password = false;
    if (!empty($nouveau_mot_de_passe) || !empty($confirmer_mot_de_passe)) {
        if (empty($mot_de_passe_actuel)) {
            $errors[] = "Veuillez saisir votre mot de passe actuel.";
        } else {
            // Vérifier le mot de passe actuel
            $stmt = $db->prepare("SELECT mot_de_passe FROM clients WHERE id = ?");
            $stmt->execute([$_SESSION['client_id']]);
            $current_password_hash = $stmt->fetchColumn();
            
            if (!password_verify($mot_de_passe_actuel, $current_password_hash)) {
                $errors[] = "Le mot de passe actuel est incorrect.";
            } else {
                if ($nouveau_mot_de_passe !== $confirmer_mot_de_passe) {
                    $errors[] = "Les nouveaux mots de passe ne correspondent pas.";
                } elseif (strlen($nouveau_mot_de_passe) < 6) {
                    $errors[] = "Le nouveau mot de passe doit contenir au moins 6 caractères.";
                } else {
                    $update_password = true;
                }
            }
        }
    }
    
    // Vérifier l'unicité de l'email (sauf pour le client actuel)
    $stmt = $db->prepare("SELECT id FROM clients WHERE email = ? AND id != ?");
    $stmt->execute([$email, $_SESSION['client_id']]);
    if ($stmt->fetch()) {
        $errors[] = "Cette adresse e-mail est déjà utilisée par un autre compte.";
    }
    
    if (!empty($errors)) {
        echo json_encode([
            'success' => false, 
            'message' => implode('<br>', $errors)
        ]);
        exit;
    }
    
    // Commencer une transaction
    $db->beginTransaction();
    
    // Préparer la requête de mise à jour
    $sql_parts = [];
    $params = [];
    
    $sql_parts[] = "prenom = ?";
    $params[] = $prenom;
    
    $sql_parts[] = "nom = ?";
    $params[] = $nom;
    
    $sql_parts[] = "email = ?";
    $params[] = $email;
    
    $sql_parts[] = "telephone = ?";
    $params[] = $telephone;
    
    $sql_parts[] = "adresse = ?";
    $params[] = $adresse;
    
    $sql_parts[] = "code_postal = ?";
    $params[] = $code_postal;
    
    $sql_parts[] = "ville = ?";
    $params[] = $ville;
    
    $sql_parts[] = "pays = ?";
    $params[] = $pays;
    
    $sql_parts[] = "adresse_livraison_differente = ?";
    $params[] = $adresse_livraison_differente;
    
    $sql_parts[] = "adresse_livraison = ?";
    $params[] = $adresse_livraison;
    
    $sql_parts[] = "code_postal_livraison = ?";
    $params[] = $code_postal_livraison;
    
    $sql_parts[] = "ville_livraison = ?";
    $params[] = $ville_livraison;
    
    $sql_parts[] = "pays_livraison = ?";
    $params[] = $pays_livraison;
    
    $sql_parts[] = "newsletter = ?";
    $params[] = $newsletter;
    
    if ($update_password) {
        $sql_parts[] = "mot_de_passe = ?";
        $params[] = password_hash($nouveau_mot_de_passe, PASSWORD_DEFAULT);
    }
    
    $params[] = $_SESSION['client_id'];
    
    $sql = "UPDATE clients SET " . implode(', ', $sql_parts) . " WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    // Valider la transaction
    $db->commit();
    
    // Mettre à jour les variables de session
    $_SESSION['client_prenom'] = $prenom;
    $_SESSION['client_nom'] = $nom;
    $_SESSION['client_email'] = $email;
    
    $message = "Vos informations ont été mises à jour avec succès.";
    if ($update_password) {
        $message .= " Votre mot de passe a également été modifié.";
    }
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'update_session' => true
    ]);
    
} catch (Exception $e) {
    // Annuler la transaction en cas d'erreur
    if (isset($db)) {
        $db->rollback();
    }
    
    error_log("Erreur update-profile: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => "Une erreur s'est produite lors de la mise à jour. Veuillez réessayer."
    ]);
}
?>