-- Table pour les devis
CREATE TABLE IF NOT EXISTS devis (
    id int(11) NOT NULL AUTO_INCREMENT,
    numero varchar(50) NOT NULL UNIQUE,
    client_id int(11) NOT NULL,
    date_creation datetime DEFAULT CURRENT_TIMESTAMP,
    adresse_facturation text,
    adresse_livraison text,
    notes text,
    total_ht decimal(10,2) DEFAULT 0.00,
    frais_port decimal(10,2) DEFAULT 0.00,
    tva decimal(10,2) DEFAULT 0.00,
    total_ttc decimal(10,2) DEFAULT 0.00,
    statut enum('brouillon','envoye','accepte','refuse','expire') DEFAULT 'brouillon',
    date_envoi datetime NULL,
    date_acceptation datetime NULL,
    date_expiration datetime NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY client_id (client_id),
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Table pour les articles/items d'un devis
CREATE TABLE IF NOT EXISTS devis_items (
    id int(11) NOT NULL AUTO_INCREMENT,
    devis_id int(11) NOT NULL,
    produit_id int(11) NULL,
    designation varchar(255) NOT NULL,
    description text,
    quantite decimal(10,2) NOT NULL,
    prix_unitaire decimal(10,2) NOT NULL,
    remise_type enum('percent','euro') DEFAULT 'percent',
    remise_valeur decimal(10,2) DEFAULT 0.00,
    total_ligne decimal(10,2) NOT NULL,
    ordre int(3) DEFAULT 0,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY devis_id (devis_id),
    KEY produit_id (produit_id),
    FOREIGN KEY (devis_id) REFERENCES devis(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;