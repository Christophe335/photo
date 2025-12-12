-- Tables pour la gestion des articles composés

-- Ajout d'un champ pour marquer les produits comme étant composés
ALTER TABLE produits 
ADD COLUMN est_compose BOOLEAN DEFAULT FALSE,
ADD COLUMN composition_auto BOOLEAN DEFAULT TRUE COMMENT 'Si true, la désignation et le prix sont calculés automatiquement';

-- Table de liaison pour les articles composés
CREATE TABLE IF NOT EXISTS produit_compositions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produit_parent_id INT NOT NULL,
    produit_enfant_id INT NOT NULL,
    quantite INT DEFAULT 1,
    ordre_affichage INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (produit_parent_id) REFERENCES produits(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_enfant_id) REFERENCES produits(id) ON DELETE CASCADE,
    
    -- Un produit enfant ne peut être ajouté qu'une seule fois dans un même produit parent
    UNIQUE KEY unique_composition (produit_parent_id, produit_enfant_id)
);

-- Index pour améliorer les performances
CREATE INDEX idx_parent ON produit_compositions(produit_parent_id);
CREATE INDEX idx_enfant ON produit_compositions(produit_enfant_id);

-- Vue pour faciliter l'affichage des compositions
CREATE OR REPLACE VIEW vue_compositions AS
SELECT 
    pc.produit_parent_id,
    pc.produit_enfant_id,
    pc.quantite,
    pc.ordre_affichage,
    p_parent.reference AS parent_reference,
    p_parent.designation AS parent_designation,
    p_enfant.reference AS enfant_reference,
    p_enfant.designation AS enfant_designation,
    p_enfant.prixVente AS enfant_prix_unitaire,
    (p_enfant.prixVente * pc.quantite) AS enfant_prix_total
FROM produit_compositions pc
JOIN produits p_parent ON pc.produit_parent_id = p_parent.id
JOIN produits p_enfant ON pc.produit_enfant_id = p_enfant.id
ORDER BY pc.produit_parent_id, pc.ordre_affichage, pc.id;