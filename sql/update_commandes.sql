-- Mise à jour rapide pour ajouter le système de commandes
-- Exécuter ce script si vous avez déjà une installation existante

-- Ajouter les colonnes d'adresse de livraison si elles n'existent pas
-- Vérification et ajout conditionnel des colonnes
SET @db_name = DATABASE();

SET @s = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'adresse_livraison_differente') = 0,
    "ALTER TABLE clients ADD COLUMN adresse_livraison_differente TINYINT(1) DEFAULT 0 AFTER pays",
    "SELECT 'Column adresse_livraison_differente already exists' AS msg"
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'adresse_livraison') = 0,
    "ALTER TABLE clients ADD COLUMN adresse_livraison TEXT NULL AFTER adresse_livraison_differente",
    "SELECT 'Column adresse_livraison already exists' AS msg"
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'code_postal_livraison') = 0,
    "ALTER TABLE clients ADD COLUMN code_postal_livraison VARCHAR(10) NULL AFTER adresse_livraison",
    "SELECT 'Column code_postal_livraison already exists' AS msg"
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'ville_livraison') = 0,
    "ALTER TABLE clients ADD COLUMN ville_livraison VARCHAR(50) NULL AFTER code_postal_livraison",
    "SELECT 'Column ville_livraison already exists' AS msg"
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'clients' AND COLUMN_NAME = 'pays_livraison') = 0,
    "ALTER TABLE clients ADD COLUMN pays_livraison VARCHAR(50) NULL AFTER ville_livraison",
    "SELECT 'Column pays_livraison already exists' AS msg"
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Créer les tables de commandes si elles n'existent pas
CREATE TABLE IF NOT EXISTS commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    numero_commande VARCHAR(20) NOT NULL UNIQUE,
    statut ENUM('en_attente', 'confirmee', 'en_preparation', 'en_cours', 'expediee', 'livree', 'annulee') DEFAULT 'en_attente',
    sous_total DECIMAL(10,2) NOT NULL,
    tva DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    frais_livraison DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    adresse_facturation TEXT NOT NULL,
    code_postal_facturation VARCHAR(10) NOT NULL,
    ville_facturation VARCHAR(50) NOT NULL,
    pays_facturation VARCHAR(50) NOT NULL,
    adresse_livraison TEXT,
    code_postal_livraison VARCHAR(10),
    ville_livraison VARCHAR(50),
    pays_livraison VARCHAR(50),
    mode_paiement VARCHAR(50) DEFAULT 'carte_bancaire',
    statut_paiement ENUM('en_attente', 'paye', 'echec', 'rembourse') DEFAULT 'en_attente',
    reference_paiement VARCHAR(100),
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_confirmation TIMESTAMP NULL,
    date_expedition TIMESTAMP NULL,
    date_livraison TIMESTAMP NULL,
    numero_suivi VARCHAR(50) NULL,
    transporteur VARCHAR(50) NULL,
    commentaire_client TEXT,
    note_interne TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS commande_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    produit_code VARCHAR(50) NOT NULL,
    designation TEXT NOT NULL,
    format VARCHAR(100),
    couleur VARCHAR(50),
    conditionnement VARCHAR(100),
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    total_ligne DECIMAL(10,2) NOT NULL,
    donnees_produit JSON COMMENT 'Sauvegarde des détails produit au moment de la commande',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS commande_historique (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    ancien_statut VARCHAR(50),
    nouveau_statut VARCHAR(50) NOT NULL,
    commentaire TEXT,
    date_changement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ajouter la table login_attempts si elle n'existe pas
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL COMMENT 'Email utilisé pour la tentative',
    ip_address VARCHAR(45) NOT NULL COMMENT 'Adresse IP',
    success TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 si succès, 0 si échec',
    user_agent TEXT NULL COMMENT 'User agent',
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de la tentative'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Créer les index avec vérification d'existence
SET @s = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'commandes' AND INDEX_NAME = 'idx_commandes_client_id') = 0,
    "CREATE INDEX idx_commandes_client_id ON commandes(client_id)",
    "SELECT 'Index idx_commandes_client_id already exists' AS msg"
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'commandes' AND INDEX_NAME = 'idx_commandes_numero') = 0,
    "CREATE INDEX idx_commandes_numero ON commandes(numero_commande)",
    "SELECT 'Index idx_commandes_numero already exists' AS msg"
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'commandes' AND INDEX_NAME = 'idx_commandes_statut') = 0,
    "CREATE INDEX idx_commandes_statut ON commandes(statut)",
    "SELECT 'Index idx_commandes_statut already exists' AS msg"
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'commandes' AND INDEX_NAME = 'idx_commandes_date') = 0,
    "CREATE INDEX idx_commandes_date ON commandes(date_commande)",
    "SELECT 'Index idx_commandes_date already exists' AS msg"
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'commande_items' AND INDEX_NAME = 'idx_commande_items_commande_id') = 0,
    "CREATE INDEX idx_commande_items_commande_id ON commande_items(commande_id)",
    "SELECT 'Index idx_commande_items_commande_id already exists' AS msg"
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'commande_historique' AND INDEX_NAME = 'idx_commande_historique_commande_id') = 0,
    "CREATE INDEX idx_commande_historique_commande_id ON commande_historique(commande_id)",
    "SELECT 'Index idx_commande_historique_commande_id already exists' AS msg"
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Ajouter les index de login_attempts
SET @s = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'login_attempts' AND INDEX_NAME = 'idx_login_attempts_email') = 0,
    "CREATE INDEX idx_login_attempts_email ON login_attempts(email)",
    "SELECT 'Index idx_login_attempts_email already exists' AS msg"
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'login_attempts' AND INDEX_NAME = 'idx_login_attempts_ip') = 0,
    "CREATE INDEX idx_login_attempts_ip ON login_attempts(ip_address)",
    "SELECT 'Index idx_login_attempts_ip already exists' AS msg"
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'login_attempts' AND INDEX_NAME = 'idx_login_attempts_date') = 0,
    "CREATE INDEX idx_login_attempts_date ON login_attempts(attempted_at)",
    "SELECT 'Index idx_login_attempts_date already exists' AS msg"
));
PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT 'Mise à jour du système de commandes terminée!' AS status;