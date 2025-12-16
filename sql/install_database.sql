-- Script d'installation complet de la base de données pour le système client
-- Ce fichier crée toutes les tables nécessaires pour la gestion des clients

-- ==================================================
-- Table des clients avec adresses de facturation et livraison
-- ==================================================

CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Informations personnelles
    prenom VARCHAR(50) NOT NULL COMMENT 'Prénom du client',
    nom VARCHAR(50) NOT NULL COMMENT 'Nom de famille du client',
    email VARCHAR(100) NOT NULL UNIQUE COMMENT 'Adresse email (identifiant unique)',
    mot_de_passe VARCHAR(255) NOT NULL COMMENT 'Mot de passe haché avec password_hash()',
    telephone VARCHAR(20) NULL COMMENT 'Numéro de téléphone',
    
    -- Adresse de facturation (obligatoire)
    adresse TEXT NOT NULL COMMENT 'Adresse de facturation',
    code_postal VARCHAR(10) NOT NULL COMMENT 'Code postal de facturation',
    ville VARCHAR(50) NOT NULL COMMENT 'Ville de facturation',
    pays VARCHAR(50) DEFAULT 'France' COMMENT 'Pays de facturation',
    
    -- Adresse de livraison (optionnelle)
    adresse_livraison_differente TINYINT(1) DEFAULT 0 COMMENT '1 si adresse livraison différente',
    adresse_livraison TEXT NULL COMMENT 'Adresse de livraison si différente',
    code_postal_livraison VARCHAR(10) NULL COMMENT 'Code postal de livraison',
    ville_livraison VARCHAR(50) NULL COMMENT 'Ville de livraison',
    pays_livraison VARCHAR(50) NULL COMMENT 'Pays de livraison',
    
    -- Préférences et statut
    newsletter TINYINT(1) DEFAULT 0 COMMENT '1 si inscrit à la newsletter',
    actif TINYINT(1) DEFAULT 1 COMMENT '1 si compte activé, 0 sinon',
    
    -- Tokens de sécurité
    token_activation VARCHAR(64) NULL COMMENT 'Token pour activation email',
    token_reset VARCHAR(64) NULL COMMENT 'Token pour reset mot de passe',
    token_reset_expiration DATETIME NULL COMMENT 'Expiration du token reset',
    
    -- Horodatage
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création du compte',
    derniere_connexion TIMESTAMP NULL COMMENT 'Dernière connexion du client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==================================================
-- Index pour optimiser les performances
-- ==================================================

CREATE INDEX idx_clients_email ON clients(email);
CREATE INDEX idx_clients_actif ON clients(actif);
CREATE INDEX idx_clients_token_activation ON clients(token_activation);
CREATE INDEX idx_clients_token_reset ON clients(token_reset);
CREATE INDEX idx_clients_derniere_connexion ON clients(derniere_connexion);

-- ==================================================
-- Table des sessions clients (optionnel - sécurité avancée)
-- ==================================================

CREATE TABLE IF NOT EXISTS client_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL COMMENT 'ID du client',
    session_id VARCHAR(128) NOT NULL UNIQUE COMMENT 'ID de session PHP',
    ip_address VARCHAR(45) NULL COMMENT 'Adresse IP du client',
    user_agent TEXT NULL COMMENT 'User agent du navigateur',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création',
    expires_at TIMESTAMP NOT NULL COMMENT 'Date d\'expiration',
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_client_sessions_client_id ON client_sessions(client_id);
CREATE INDEX idx_client_sessions_expires_at ON client_sessions(expires_at);
CREATE INDEX idx_client_sessions_session_id ON client_sessions(session_id);

-- ==================================================
-- Table de log des tentatives de connexion (sécurité)
-- ==================================================

CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL COMMENT 'Email utilisé pour la tentative',
    ip_address VARCHAR(45) NOT NULL COMMENT 'Adresse IP',
    success TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 si succès, 0 si échec',
    user_agent TEXT NULL COMMENT 'User agent',
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de la tentative'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_login_attempts_email ON login_attempts(email);
CREATE INDEX idx_login_attempts_ip ON login_attempts(ip_address);
CREATE INDEX idx_login_attempts_date ON login_attempts(attempted_at);

-- ==================================================
-- Procédure stockée pour nettoyer les anciennes sessions
-- ==================================================

DELIMITER //
CREATE PROCEDURE CleanExpiredSessions()
BEGIN
    DELETE FROM client_sessions WHERE expires_at < NOW();
    DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
END //
DELIMITER ;

-- ==================================================
-- Données de test (optionnel - à commenter en production)
-- ==================================================

-- Insertion d'un compte de test (mot de passe: "test123")
-- INSERT INTO clients (
--     prenom, nom, email, mot_de_passe, telephone,
--     adresse, code_postal, ville, pays, newsletter, actif
-- ) VALUES (
--     'Test', 'Utilisateur', 'test@example.com', 
--     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
--     '0123456789', '123 Rue de Test', '75000', 'Paris', 'France', 0, 1
-- );

-- ==================================================
-- Fin du script d'installation
-- ==================================================

-- ==================================================
-- Tables pour les commandes (ajout des tables de commandes)
-- ==================================================

-- Table des commandes
CREATE TABLE IF NOT EXISTS commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    numero_commande VARCHAR(20) NOT NULL UNIQUE,
    
    -- Statut de la commande
    statut ENUM('en_attente', 'confirmee', 'en_preparation', 'en_cours', 'expediee', 'livree', 'annulee') DEFAULT 'en_attente',
    
    -- Montants
    sous_total DECIMAL(10,2) NOT NULL,
    tva DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    frais_livraison DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    
    -- Adresses
    adresse_facturation TEXT NOT NULL,
    code_postal_facturation VARCHAR(10) NOT NULL,
    ville_facturation VARCHAR(50) NOT NULL,
    pays_facturation VARCHAR(50) NOT NULL,
    
    adresse_livraison TEXT,
    code_postal_livraison VARCHAR(10),
    ville_livraison VARCHAR(50),
    pays_livraison VARCHAR(50),
    
    -- Informations de paiement
    mode_paiement VARCHAR(50) DEFAULT 'carte_bancaire',
    statut_paiement ENUM('en_attente', 'paye', 'echec', 'rembourse') DEFAULT 'en_attente',
    reference_paiement VARCHAR(100),
    
    -- Suivi
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_confirmation TIMESTAMP NULL,
    date_expedition TIMESTAMP NULL,
    date_livraison TIMESTAMP NULL,
    
    -- Numéro de suivi
    numero_suivi VARCHAR(50) NULL,
    transporteur VARCHAR(50) NULL,
    
    -- Notes
    commentaire_client TEXT,
    note_interne TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des lignes de commande
CREATE TABLE IF NOT EXISTS commande_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    
    -- Informations produit
    produit_code VARCHAR(50) NOT NULL,
    designation TEXT NOT NULL,
    format VARCHAR(100),
    couleur VARCHAR(50),
    conditionnement VARCHAR(100),
    
    -- Prix et quantité
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    total_ligne DECIMAL(10,2) NOT NULL,
    
    -- Métadonnées
    donnees_produit JSON COMMENT 'Sauvegarde des détails produit au moment de la commande',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table de suivi des statuts
CREATE TABLE IF NOT EXISTS commande_historique (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    ancien_statut VARCHAR(50),
    nouveau_statut VARCHAR(50) NOT NULL,
    commentaire TEXT,
    date_changement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index pour les commandes
CREATE INDEX idx_commandes_client_id ON commandes(client_id);
CREATE INDEX idx_commandes_numero ON commandes(numero_commande);
CREATE INDEX idx_commandes_statut ON commandes(statut);
CREATE INDEX idx_commandes_date ON commandes(date_commande);
CREATE INDEX idx_commande_items_commande_id ON commande_items(commande_id);
CREATE INDEX idx_commande_historique_commande_id ON commande_historique(commande_id);

SELECT 'Installation de la base de données terminée avec succès!' AS status;