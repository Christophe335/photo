-- Script de mise à jour pour ajouter les champs d'adresse de livraison
-- À utiliser si la table clients existe déjà sans ces champs

-- Vérifier si les colonnes existent déjà avant de les ajouter
SET @db_name = DATABASE();

-- Ajouter les colonnes d'adresse de livraison si elles n'existent pas
SET @s = (SELECT IF(
    (SELECT COUNT(*)
     FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = @db_name 
     AND TABLE_NAME = 'clients' 
     AND COLUMN_NAME = 'adresse_livraison_differente') = 0,
    "ALTER TABLE clients ADD COLUMN adresse_livraison_differente TINYINT(1) DEFAULT 0 AFTER pays",
    "SELECT 'Column adresse_livraison_differente already exists' AS msg"
));
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    (SELECT COUNT(*)
     FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = @db_name 
     AND TABLE_NAME = 'clients' 
     AND COLUMN_NAME = 'adresse_livraison') = 0,
    "ALTER TABLE clients ADD COLUMN adresse_livraison TEXT NULL AFTER adresse_livraison_differente",
    "SELECT 'Column adresse_livraison already exists' AS msg"
));
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    (SELECT COUNT(*)
     FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = @db_name 
     AND TABLE_NAME = 'clients' 
     AND COLUMN_NAME = 'code_postal_livraison') = 0,
    "ALTER TABLE clients ADD COLUMN code_postal_livraison VARCHAR(10) NULL AFTER adresse_livraison",
    "SELECT 'Column code_postal_livraison already exists' AS msg"
));
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    (SELECT COUNT(*)
     FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = @db_name 
     AND TABLE_NAME = 'clients' 
     AND COLUMN_NAME = 'ville_livraison') = 0,
    "ALTER TABLE clients ADD COLUMN ville_livraison VARCHAR(50) NULL AFTER code_postal_livraison",
    "SELECT 'Column ville_livraison already exists' AS msg"
));
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @s = (SELECT IF(
    (SELECT COUNT(*)
     FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = @db_name 
     AND TABLE_NAME = 'clients' 
     AND COLUMN_NAME = 'pays_livraison') = 0,
    "ALTER TABLE clients ADD COLUMN pays_livraison VARCHAR(50) NULL AFTER ville_livraison",
    "SELECT 'Column pays_livraison already exists' AS msg"
));
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Mise à jour du statut actif par défaut à 1 au lieu de 0
UPDATE clients SET actif = 1 WHERE actif = 0 AND token_activation IS NOT NULL;

SELECT 'Migration des adresses de livraison terminée' AS status;