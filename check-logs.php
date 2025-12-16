<?php
// Script pour voir les logs récents liés au profil
$logFile = ini_get('error_log');

// Si pas de fichier de log configuré, essayer les chemins standards de Laragon
if (empty($logFile) || !file_exists($logFile)) {
    $possibleLogs = [
        'C:/laragon/logs/apache_error.log',
        'C:/laragon/www/logs/error.log',
        'C:/laragon/bin/apache/apache-2.4.47/logs/error.log', // Ajustez la version d'Apache
        'C:/Windows/temp/php-errors.log'
    ];
    
    foreach ($possibleLogs as $path) {
        if (file_exists($path)) {
            $logFile = $path;
            break;
        }
    }
}

echo "<h1>Check des logs récents</h1>";
echo "<p><strong>Fichier de log:</strong> " . ($logFile ?: 'Non trouvé') . "</p>";

if ($logFile && file_exists($logFile)) {
    // Lire les 50 dernières lignes
    $lines = file($logFile);
    $recentLines = array_slice($lines, -50);
    
    echo "<h2>Dernières entrées contenant 'UPDATE PROFILE DEBUG':</h2>";
    echo "<div style='background: #f5f5f5; padding: 10px; border-radius: 5px; font-family: monospace; white-space: pre-wrap;'>";
    
    $found = false;
    foreach ($recentLines as $line) {
        if (stripos($line, 'UPDATE PROFILE DEBUG') !== false) {
            echo htmlspecialchars($line) . "\n";
            $found = true;
        }
    }
    
    if (!$found) {
        echo "Aucune entrée 'UPDATE PROFILE DEBUG' trouvée dans les dernières lignes.";
    }
    
    echo "</div>";
    
    echo "<h2>Toutes les dernières entrées du log:</h2>";
    echo "<div style='background: #f5f5f5; padding: 10px; border-radius: 5px; font-family: monospace; white-space: pre-wrap; max-height: 300px; overflow-y: auto;'>";
    foreach ($recentLines as $line) {
        echo htmlspecialchars($line) . "\n";
    }
    echo "</div>";
} else {
    echo "<p style='color: red;'>Fichier de log non trouvé. Vérifiez la configuration de PHP.</p>";
    echo "<p>Configuration PHP actuelle:</p>";
    echo "<ul>";
    echo "<li>error_log: " . ini_get('error_log') . "</li>";
    echo "<li>log_errors: " . (ini_get('log_errors') ? 'On' : 'Off') . "</li>";
    echo "<li>display_errors: " . (ini_get('display_errors') ? 'On' : 'Off') . "</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><a href='check-logs.php'>Actualiser</a></p>";
?>