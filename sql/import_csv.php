<?php
/**
 * Script d'import des données CSV vers la base de données
 */

require_once __DIR__ . '/../includes/database.php';

function importerCSV($fichierCSV) {
    try {
        $db = Database::getInstance()->getConnection();
        
        // Vider la table existante si souhaitée (décommenter la ligne suivante)
        // $db->exec("DELETE FROM produits WHERE id > 0");
        
        // Ouvrir le fichier CSV
        if (!file_exists($fichierCSV)) {
            throw new Exception("Fichier CSV non trouvé : $fichierCSV");
        }
        
        $handle = fopen($fichierCSV, 'r');
        if (!$handle) {
            throw new Exception("Impossible d'ouvrir le fichier CSV");
        }
        
        // Lire la première ligne (en-têtes)
        $headers = fgetcsv($handle, 0, ';');
        if (!$headers) {
            throw new Exception("Impossible de lire les en-têtes du fichier CSV");
        }
        
        echo "En-têtes détectées : " . implode(', ', $headers) . "\n\n";
        
        // Préparer la requête d'insertion
        $sql = "INSERT INTO produits (
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
        
        $ligneNumber = 1;
        $importees = 0;
        $erreurs = 0;
        
        // Lire chaque ligne du CSV
        while (($ligne = fgetcsv($handle, 0, ';')) !== FALSE) {
            $ligneNumber++;
            
            try {
                // Ignorer les lignes vides
                if (count($ligne) < 5) {
                    continue;
                }
                
                // Nettoyer et convertir les données
                $data = [
                    ':famille' => trim($ligne[1]) ?: null,
                    ':nomDeLaFamille' => trim($ligne[2]) ?: null,
                    ':reference' => trim($ligne[3]) ?: null,
                    ':designation' => trim($ligne[4]) ?: null,
                    ':format' => trim($ligne[5]) ?: null,
                    ':prixAchat' => !empty(trim($ligne[6])) ? floatval(str_replace(',', '.', $ligne[6])) : null,
                    ':prixVente' => !empty(trim($ligne[7])) ? floatval(str_replace(',', '.', $ligne[7])) : null,
                    ':conditionnement' => trim($ligne[8]) ?: null,
                    ':matiere' => trim($ligne[9]) ?: null,
                    ':couleur_interieur' => trim($ligne[10]) ?: null,
                    ':couleur_ext1' => trim($ligne[11]) ?: null,
                    ':imageCoul1' => trim($ligne[12]) ?: null,
                    ':couleur_ext2' => trim($ligne[13]) ?: null,
                    ':imageCoul2' => trim($ligne[14]) ?: null,
                    ':couleur_ext3' => trim($ligne[15]) ?: null,
                    ':imageCoul3' => trim($ligne[16]) ?: null,
                    ':couleur_ext4' => trim($ligne[17]) ?: null,
                    ':imageCoul4' => trim($ligne[18]) ?: null,
                    ':couleur_ext5' => trim($ligne[19]) ?: null,
                    ':imageCoul5' => trim($ligne[20]) ?: null,
                    ':couleur_ext6' => trim($ligne[21]) ?: null,
                    ':imageCoul6' => trim($ligne[22]) ?: null,
                    ':couleur_ext7' => trim($ligne[23]) ?: null,
                    ':imageCoul7' => trim($ligne[24]) ?: null,
                    ':couleur_ext8' => trim($ligne[25]) ?: null,
                    ':imageCoul8' => trim($ligne[26]) ?: null,
                    ':couleur_ext9' => trim($ligne[27]) ?: null,
                    ':imageCoul9' => trim($ligne[28]) ?: null,
                    ':couleur_ext10' => trim($ligne[29]) ?: null,
                    ':imageCoul10' => trim($ligne[30]) ?: null,
                    ':couleur_ext11' => trim($ligne[31]) ?: null,
                    ':imageCoul11' => trim($ligne[32]) ?: null,
                    ':couleur_ext12' => trim($ligne[33]) ?: null,
                    ':imageCoul12' => trim($ligne[34]) ?: null,
                    ':couleur_ext13' => trim($ligne[35]) ?: null,
                    ':imageCoul13' => trim($ligne[36]) ?: null
                ];
                
                // Exécuter l'insertion
                if ($stmt->execute($data)) {
                    $importees++;
                    if ($importees % 10 == 0) {
                        echo "Importé $importees produits...\n";
                    }
                } else {
                    echo "Erreur ligne $ligneNumber : " . implode(', ', $stmt->errorInfo()) . "\n";
                    $erreurs++;
                }
                
            } catch (Exception $e) {
                echo "Erreur ligne $ligneNumber : " . $e->getMessage() . "\n";
                $erreurs++;
            }
        }
        
        fclose($handle);
        
        echo "\n=== RÉSULTAT DE L'IMPORT ===\n";
        echo "Produits importés avec succès : $importees\n";
        echo "Erreurs : $erreurs\n";
        echo "Total lignes traitées : " . ($ligneNumber - 1) . "\n";
        
        return ['importees' => $importees, 'erreurs' => $erreurs];
        
    } catch (Exception $e) {
        echo "ERREUR FATALE : " . $e->getMessage() . "\n";
        return false;
    }
}

// Exécution du script
echo "=== IMPORT DES PRODUITS DEPUIS CSV ===\n\n";

$fichierCSV = __DIR__ . '/produits.csv';
$resultat = importerCSV($fichierCSV);

if ($resultat) {
    echo "\nImport terminé !\n";
    
    // Vérification rapide
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT COUNT(*) as total FROM produits");
        $total = $stmt->fetch()['total'];
        echo "Total des produits en base : $total\n";
        
        // Afficher quelques familles
        $stmt = $db->query("SELECT famille, COUNT(*) as nb FROM produits GROUP BY famille ORDER BY nb DESC LIMIT 10");
        echo "\nFamilles de produits :\n";
        while ($row = $stmt->fetch()) {
            echo "- " . $row['famille'] . " : " . $row['nb'] . " produits\n";
        }
        
    } catch (Exception $e) {
        echo "Erreur lors de la vérification : " . $e->getMessage() . "\n";
    }
} else {
    echo "\nL'import a échoué !\n";
}
?>