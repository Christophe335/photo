<?php
require_once __DIR__ . '/../includes/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "<h2>Installation des tables de devis</h2>";
    echo "<ul>";
    
    // Table devis
    $sql = "CREATE TABLE IF NOT EXISTS devis (
        id INT AUTO_INCREMENT PRIMARY KEY,
        numero_devis VARCHAR(20) NOT NULL UNIQUE,
        client_id INT NULL,
        client_nom VARCHAR(100) NOT NULL,
        client_prenom VARCHAR(100) NOT NULL,
        client_email VARCHAR(255) NOT NULL,
        client_telephone VARCHAR(20),
        client_adresse TEXT NOT NULL,
        client_code_postal VARCHAR(10) NOT NULL,
        client_ville VARCHAR(50) NOT NULL,
        client_pays VARCHAR(50) NOT NULL DEFAULT 'France',
        adresse_livraison_differente TINYINT(1) DEFAULT 0,
        adresse_livraison TEXT NULL,
        code_postal_livraison VARCHAR(10) NULL,
        ville_livraison VARCHAR(50) NULL,
        pays_livraison VARCHAR(50) NULL,
        sous_total DECIMAL(10,2) NOT NULL DEFAULT 0,
        total_remise DECIMAL(10,2) NOT NULL DEFAULT 0,
        frais_port DECIMAL(10,2) NOT NULL DEFAULT 0,
        total_ht DECIMAL(10,2) NOT NULL DEFAULT 0,
        tva_taux DECIMAL(5,2) NOT NULL DEFAULT 20.00,
        tva_montant DECIMAL(10,2) NOT NULL DEFAULT 0,
        total_ttc DECIMAL(10,2) NOT NULL DEFAULT 0,
        statut ENUM('brouillon', 'envoye', 'accepte', 'refuse', 'expire') DEFAULT 'brouillon',
        date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        date_envoi TIMESTAMP NULL,
        date_expiration DATE NULL,
        notes TEXT,
        conditions_particulieres TEXT,
        created_by VARCHAR(50),
        FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->exec($sql);
    echo "<li style='color: green;'>✓ Table 'devis' créée</li>";
    
    // Table devis_items
    $sql = "CREATE TABLE IF NOT EXISTS devis_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        devis_id INT NOT NULL,
        produit_id INT NULL,
        produit_reference VARCHAR(50),
        designation TEXT NOT NULL,
        description TEXT,
        quantite INT NOT NULL DEFAULT 1,
        prix_unitaire DECIMAL(10,2) NOT NULL,
        type_remise ENUM('pourcentage', 'montant') DEFAULT 'pourcentage',
        remise DECIMAL(8,2) NOT NULL DEFAULT 0,
        montant_remise DECIMAL(10,2) NOT NULL DEFAULT 0,
        total_ligne DECIMAL(10,2) NOT NULL,
        ordre_affichage INT NOT NULL DEFAULT 0,
        FOREIGN KEY (devis_id) REFERENCES devis(id) ON DELETE CASCADE,
        FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->exec($sql);
    echo "<li style='color: green;'>✓ Table 'devis_items' créée</li>";
    
    echo "</ul>";
    echo "<p style='color: green; font-weight: bold;'>Installation terminée avec succès !</p>";
    echo "<p><a href='gestion-devis.php'>→ Aller à la gestion des devis</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>";
}
?>