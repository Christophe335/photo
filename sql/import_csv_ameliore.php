<?php
/**
 * Script d'import CSV amélioré avec gestion de l'encodage
 */

require_once __DIR__ . '/../includes/database.php';

function importerCSVAmeliore($fichierCSV) {
    try {
        $db = Database::getInstance()->getConnection();
        
        // Vider la table complètement pour recommencer
        echo "Suppression des anciens produits...\n";
        $db->exec("DELETE FROM produits WHERE id > 0");
        $db->exec("ALTER TABLE produits AUTO_INCREMENT = 1");
        
        // Lire tout le contenu du fichier avec l'encodage correct
        $contenu = file_get_contents($fichierCSV);
        
        // Convertir depuis ISO-8859-1 vers UTF-8 si nécessaire
        if (!mb_check_encoding($contenu, 'UTF-8')) {
            $contenu = mb_convert_encoding($contenu, 'UTF-8', 'ISO-8859-1');
        }
        
        // Séparer en lignes
        $lignes = explode("\n", $contenu);
        
        // Ignorer la première ligne (en-têtes) 
        array_shift($lignes);
        
        // Préparer la requête d'insertion avec IGNORE pour éviter les doublons
        $sql = "INSERT IGNORE INTO produits (
            famille, nomDeLaFamille, reference, designation, format, 
            prixAchat, prixVente, conditionnement, matiere, couleur_interieur,
            couleur_ext1, imageCoul1, couleur_ext2, imageCoul2, couleur_ext3, imageCoul3,
            couleur_ext4, imageCoul4, couleur_ext5, imageCoul5, couleur_ext6, imageCoul6,
            couleur_ext7, imageCoul7, couleur_ext8, imageCoul8, couleur_ext9, imageCoul9,
            couleur_ext10, imageCoul10, couleur_ext11, imageCoul11, couleur_ext12, imageCoul12,
            couleur_ext13, imageCoul13
        ) VALUES (
            :famille, :nomDeLaFamille, :reference, :designation, :format,
            :prixAchat, :prixVente, :conditionnement, :matiere, :couleur_interieur,
            :couleur_ext1, :imageCoul1, :couleur_ext2, :imageCoul2, :couleur_ext3, :imageCoul3,
            :couleur_ext4, :imageCoul4, :couleur_ext5, :imageCoul5, :couleur_ext6, :imageCoul6,
            :couleur_ext7, :imageCoul7, :couleur_ext8, :imageCoul8, :couleur_ext9, :imageCoul9,
            :couleur_ext10, :imageCoul10, :couleur_ext11, :imageCoul11, :couleur_ext12, :imageCoul12,
            :couleur_ext13, :imageCoul13
        )";
        
        $stmt = $db->prepare($sql);
        
        $importees = 0;
        $erreurs = 0;
        $ignores = 0;
        
        foreach ($lignes as $numLigne => $ligne) {
            $ligne = trim($ligne);
            if (empty($ligne)) continue;
            
            // Séparer par point-virgule
            $data = str_getcsv($ligne, ';');
            
            // Ignorer les lignes avec pas assez de colonnes
            if (count($data) < 37) {
                continue;
            }
            
            try {
                // Nettoyer les données
                $famille = substr(trim($data[1]), 0, 100); // Limiter à 100 caractères
                $nomDeLaFamille = trim($data[2]);
                $reference = trim($data[3]);
                $designation = trim($data[4]);
                $format = trim($data[5]);
                
                // Gérer les prix (remplacer virgule par point, gérer les valeurs vides)
                $prixAchat = !empty(trim($data[6])) ? floatval(str_replace(',', '.', $data[6])) : 0;
                $prixVente = !empty(trim($data[7])) ? floatval(str_replace(',', '.', $data[7])) : 0;
                
                // Ignorer les lignes sans prix de vente
                if ($prixVente <= 0) {
                    $ignores++;
                    continue;
                }
                
                $values = [
                    ':famille' => $famille ?: null,
                    ':nomDeLaFamille' => $nomDeLaFamille ?: null,
                    ':reference' => $reference ?: null,
                    ':designation' => $designation ?: null,
                    ':format' => $format ?: null,
                    ':prixAchat' => $prixAchat,
                    ':prixVente' => $prixVente,
                    ':conditionnement' => trim($data[8]) ?: null,
                    ':matiere' => trim($data[9]) ?: null,
                    ':couleur_interieur' => trim($data[10]) ?: null,
                    ':couleur_ext1' => trim($data[11]) ?: null,
                    ':imageCoul1' => trim($data[12]) ?: null,
                    ':couleur_ext2' => trim($data[13]) ?: null,
                    ':imageCoul2' => trim($data[14]) ?: null,
                    ':couleur_ext3' => trim($data[15]) ?: null,
                    ':imageCoul3' => trim($data[16]) ?: null,
                    ':couleur_ext4' => trim($data[17]) ?: null,
                    ':imageCoul4' => trim($data[18]) ?: null,
                    ':couleur_ext5' => trim($data[19]) ?: null,
                    ':imageCoul5' => trim($data[20]) ?: null,
                    ':couleur_ext6' => trim($data[21]) ?: null,
                    ':imageCoul6' => trim($data[22]) ?: null,
                    ':couleur_ext7' => trim($data[23]) ?: null,
                    ':imageCoul7' => trim($data[24]) ?: null,
                    ':couleur_ext8' => trim($data[25]) ?: null,
                    ':imageCoul8' => trim($data[26]) ?: null,
                    ':couleur_ext9' => trim($data[27]) ?: null,
                    ':imageCoul9' => trim($data[28]) ?: null,
                    ':couleur_ext10' => trim($data[29]) ?: null,
                    ':imageCoul10' => trim($data[30]) ?: null,
                    ':couleur_ext11' => trim($data[31]) ?: null,
                    ':imageCoul11' => trim($data[32]) ?: null,
                    ':couleur_ext12' => trim($data[33]) ?: null,
                    ':imageCoul12' => trim($data[34]) ?: null,
                    ':couleur_ext13' => trim($data[35]) ?: null,
                    ':imageCoul13' => trim($data[36]) ?: null
                ];
                
                if ($stmt->execute($values)) {
                    $importees++;
                    if ($importees % 10 == 0) {
                        echo "Importé $importees produits...\n";
                    }
                } else {
                    $erreurs++;
                    if ($erreurs <= 5) { // Afficher seulement les 5 premières erreurs
                        echo "Erreur ligne " . ($numLigne + 2) . " : " . implode(', ', $stmt->errorInfo()) . "\n";
                    }
                }
                
            } catch (Exception $e) {
                $erreurs++;
                if ($erreurs <= 5) {
                    echo "Erreur ligne " . ($numLigne + 2) . " : " . $e->getMessage() . "\n";
                }
            }
        }
        
        echo "\n=== RÉSULTAT DE L'IMPORT AMÉLIORÉ ===\n";
        echo "Produits importés avec succès : $importees\n";
        echo "Lignes ignorées (prix manquant) : $ignores\n";
        echo "Erreurs : $erreurs\n";
        
        return $importees;
        
    } catch (Exception $e) {
        echo "ERREUR FATALE : " . $e->getMessage() . "\n";
        return false;
    }
}

// Exécution
echo "=== IMPORT CSV AMÉLIORÉ ===\n\n";

$fichierCSV = __DIR__ . '/produits.csv';
$resultat = importerCSVAmeliore($fichierCSV);

if ($resultat) {
    echo "\nImport terminé ! $resultat produits importés.\n";
    
    // Vérifications
    try {
        $db = Database::getInstance()->getConnection();
        
        // Compter le total
        $stmt = $db->query("SELECT COUNT(*) as total FROM produits");
        $total = $stmt->fetch()['total'];
        echo "Total des produits en base : $total\n\n";
        
        // Afficher les familles
        $stmt = $db->query("SELECT famille, COUNT(*) as nb FROM produits GROUP BY famille ORDER BY nb DESC");
        echo "Familles de produits disponibles :\n";
        while ($row = $stmt->fetch()) {
            echo "- " . $row['famille'] . " : " . $row['nb'] . " produits\n";
        }
        
    } catch (Exception $e) {
        echo "Erreur lors de la vérification : " . $e->getMessage() . "\n";
    }
}
?>