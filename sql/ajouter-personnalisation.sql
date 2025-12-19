-- Script pour créer les familles de personnalisation
-- À exécuter dans votre base de données

-- Insertion des produits de Dorure
INSERT INTO produits (reference, designation, format, matiere, prixVente, famille, nomDeLaFamille, ordre) VALUES 
('DORU-001', 'Dorure standard', 'Format A4', 'Dorure à chaud', 15.00, 'DORU', 'Dorure', 1),
('DORU-002', 'Dorure premium', 'Format A4', 'Dorure à chaud premium', 25.00, 'DORU', 'Dorure', 2),
('DORU-003', 'Dorure large format', 'Format A3', 'Dorure à chaud', 35.00, 'DORU', 'Dorure', 3);

-- Insertion des produits d'Impression couleur
INSERT INTO produits (reference, designation, format, matiere, prixVente, famille, nomDeLaFamille, ordre) VALUES 
('IMPR-001', 'Impression couleur standard', 'Format A4', 'Impression numérique', 8.00, 'IMPR', 'Impression couleur', 1),
('IMPR-002', 'Impression couleur haute qualité', 'Format A4', 'Impression offset', 12.00, 'IMPR', 'Impression couleur', 2),
('IMPR-003', 'Impression couleur grand format', 'Format A3', 'Impression numérique', 18.00, 'IMPR', 'Impression couleur', 3);

-- Vérifier que les insertions ont réussi
SELECT * FROM produits WHERE famille IN ('DORU', 'IMPR') ORDER BY famille, ordre;