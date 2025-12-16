-- Tables pour la gestion des commandes clients

-- ==================================================
-- Table des commandes
-- ==================================================

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

-- ==================================================
-- Table des lignes de commande (détails des produits)
-- ==================================================

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

-- ==================================================
-- Table de suivi des statuts (historique)
-- ==================================================

CREATE TABLE IF NOT EXISTS commande_historique (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    ancien_statut VARCHAR(50),
    nouveau_statut VARCHAR(50) NOT NULL,
    commentaire TEXT,
    date_changement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==================================================
-- Index pour optimiser les performances
-- ==================================================

CREATE INDEX idx_commandes_client_id ON commandes(client_id);
CREATE INDEX idx_commandes_numero ON commandes(numero_commande);
CREATE INDEX idx_commandes_statut ON commandes(statut);
CREATE INDEX idx_commandes_date ON commandes(date_commande);
CREATE INDEX idx_commande_items_commande_id ON commande_items(commande_id);
CREATE INDEX idx_commande_historique_commande_id ON commande_historique(commande_id);

-- ==================================================
-- Fonction pour générer un numéro de commande unique
-- ==================================================

DELIMITER //
CREATE FUNCTION GenererNumeroCommande() RETURNS VARCHAR(20)
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE nouveau_numero VARCHAR(20);
    DECLARE compteur INT DEFAULT 1;
    DECLARE date_prefix VARCHAR(8);
    
    SET date_prefix = DATE_FORMAT(NOW(), '%Y%m%d');
    
    -- Trouver le prochain numéro disponible pour aujourd'hui
    SELECT COALESCE(MAX(CAST(SUBSTRING(numero_commande, 9) AS UNSIGNED)), 0) + 1 
    INTO compteur
    FROM commandes 
    WHERE numero_commande LIKE CONCAT(date_prefix, '%');
    
    SET nouveau_numero = CONCAT(date_prefix, LPAD(compteur, 4, '0'));
    
    RETURN nouveau_numero;
END //
DELIMITER ;

SELECT 'Tables de commandes créées avec succès!' AS status;