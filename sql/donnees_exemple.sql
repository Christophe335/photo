-- Insertion de données d'exemple pour tester le système

-- Famille CLASSEURS
INSERT INTO produits (famille, nomDeLaFamille, reference, designation, format, prixAchat, prixVente, conditionnement, matiere, couleur_interieur, couleur_ext1, imageCoul1, couleur_ext2, imageCoul2, couleur_ext3, imageCoul3) VALUES
('CLAS', 'Classeurs', 'CLAS001', 'Classeur 4 anneaux', 'A4 - 35mm', 8.50, 15.90, '10 pièces', 'Carton rigide', 'Blanc', 'Bleu Marine', 'bleu_marine.jpg', 'Rouge', 'rouge.jpg', 'Noir', 'noir.jpg'),
('CLAS', 'Classeurs', 'CLAS002', 'Classeur 2 anneaux', 'A4 - 25mm', 6.80, 12.50, '15 pièces', 'Carton rigide', 'Blanc', 'Vert', 'vert.jpg', 'Bleu', 'bleu.jpg', 'Gris', 'gris.jpg'),
('CLAS', 'Classeurs', 'CLAS003', 'Classeur personnalisable', 'A4 - 50mm', 12.30, 22.90, '5 pièces', 'Carton extra rigide', 'Crème', 'Bordeaux', 'bordeaux.jpg', 'Bleu Marine', 'bleu_marine.jpg', 'Noir', 'noir.jpg');

-- Famille RELIURE
INSERT INTO produits (famille, nomDeLaFamille, reference, designation, format, prixAchat, prixVente, conditionnement, matiere, couleur_interieur, couleur_ext1, imageCoul1, couleur_ext2, imageCoul2, couleur_ext3, imageCoul3, couleur_ext4, imageCoul4) VALUES
('RELI', 'Reliure', 'RELI001', 'Reliure spirale métal', 'A4', 3.20, 6.90, '50 pièces', 'Métal laqué', 'Blanc', 'Noir', 'noir.jpg', 'Blanc', 'blanc.jpg', 'Rouge', 'rouge.jpg', 'Bleu', 'bleu.jpg'),
('RELI', 'Reliure', 'RELI002', 'Reliure thermique', 'A4 - 3mm', 1.80, 4.50, '100 pièces', 'Plastique thermofusible', 'Transparent', 'Transparent', 'transparent.jpg', 'Bleu', 'bleu_clair.jpg', 'Rouge', 'rouge_clair.jpg', NULL, NULL),
('RELI', 'Reliure', 'RELI003', 'Reliure manuelle premium', 'A4 - 15mm', 8.90, 18.90, '20 pièces', 'Cuir synthétique', 'Crème', 'Noir', 'cuir_noir.jpg', 'Marron', 'cuir_marron.jpg', 'Bleu Marine', 'cuir_bleu.jpg', NULL, NULL);

-- Famille CARTONNAGE
INSERT INTO produits (famille, nomDeLaFamille, reference, designation, format, prixAchat, prixVente, conditionnement, matiere, couleur_interieur, couleur_ext1, imageCoul1, couleur_ext2, imageCoul2) VALUES
('CART', 'Cartonnage', 'CART001', 'Boîte de rangement', '32x25x8 cm', 4.50, 9.90, '25 pièces', 'Carton ondulé', 'Blanc', 'Kraft', 'kraft.jpg', 'Blanc', 'blanc_mat.jpg'),
('CART', 'Cartonnage', 'CART002', 'Chemise de présentation', 'A4', 2.10, 5.20, '50 pièces', 'Carte bristol', 'Blanc', 'Bleu', 'bristol_bleu.jpg', 'Vert', 'bristol_vert.jpg'),
('CART', 'Cartonnage', 'CART003', 'Portfolio professionnel', 'A3', 15.60, 32.90, '10 pièces', 'Carton plume', 'Gris clair', 'Noir', 'portfolio_noir.jpg', 'Gris anthracite', 'portfolio_gris.jpg');

-- Famille ACCESSOIRES
INSERT INTO produits (famille, nomDeLaFamille, reference, designation, format, prixAchat, prixVente, conditionnement, matiere, couleur_interieur, couleur_ext1, imageCoul1, couleur_ext2, imageCoul2, couleur_ext3, imageCoul3) VALUES
('ACCE', 'Accessoires', 'ACCE001', 'Étiquettes autocollantes', '50x20 mm', 0.80, 2.50, '500 pièces', 'Papier adhésif', 'Blanc', 'Blanc', 'etiq_blanc.jpg', 'Jaune', 'etiq_jaune.jpg', 'Rouge', 'etiq_rouge.jpg'),
('ACCE', 'Accessoires', 'ACCE002', 'Œillets métalliques', '8 mm', 1.20, 3.90, '100 pièces', 'Métal nickelé', 'Argent', 'Argent', 'oeillet_argent.jpg', 'Doré', 'oeillet_dore.jpg', NULL, NULL),
('ACCE', 'Accessoires', 'ACCE003', 'Coins de protection', '25x25 mm', 2.40, 6.80, '200 pièces', 'Plastique rigide', 'Transparent', 'Transparent', 'coin_transp.jpg', 'Noir', 'coin_noir.jpg', 'Blanc', 'coin_blanc.jpg');

-- Famille OUTILS
INSERT INTO produits (famille, nomDeLaFamille, reference, designation, format, prixAchat, prixVente, conditionnement, matiere, couleur_interieur, couleur_ext1, imageCoul1) VALUES
('OUTI', 'Outils', 'OUTI001', 'Perforatrice professionnelle', '2 trous - 6mm', 25.80, 49.90, '1 pièce', 'Métal et plastique', 'Gris', 'Noir/Gris', 'perfo_pro.jpg'),
('OUTI', 'Outils', 'OUTI002', 'Massicot de précision', '45 cm', 89.50, 159.90, '1 pièce', 'Acier inoxydable', 'Gris', 'Gris métallique', 'massicot.jpg'),
('OUTI', 'Outils', 'OUTI003', 'Presse à relier manuelle', 'Standard', 156.90, 289.00, '1 pièce', 'Acier peint', 'Rouge', 'Rouge/Noir', 'presse_rouge.jpg');

-- Vérification des données insérées
SELECT famille, COUNT(*) as nb_produits FROM produits GROUP BY famille ORDER BY famille;