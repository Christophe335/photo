<?php
// Usage: php seo_audit_fix.php [--apply]
// Scanne les fichiers .php/.html du site (hors dossiers exclus) et propose :
// - ajout d'un attribut alt pour les <img> sans alt
// - insertion d'un <h1> si absent (au début du <main> ou du <body>)
// Si --apply est passé, les fichiers sont modifiés in-place avec sauvegarde .bak

$apply = in_array('--apply', $argv);
$root = realpath(__DIR__ . '/..');
$exclude = ['vendor', 'admin', 'includes', 'ajax', 'uploads', 'storage', 'sql', 'tests', 'tools', '.git'];

$files = [];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
foreach ($it as $file) {
    if (!$file->isFile()) continue;
    $path = str_replace($root . DIRECTORY_SEPARATOR, '', $file->getPathname());
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    if (!in_array($ext, ['php', 'html'])) continue;
    $skip = false; foreach ($exclude as $e) { if (strpos($path, $e . DIRECTORY_SEPARATOR) === 0 || strpos($path, DIRECTORY_SEPARATOR . $e . DIRECTORY_SEPARATOR) !== false) { $skip = true; break; } }
    if ($skip) continue;
    $files[] = $file->getPathname();
}

$report = [];
foreach ($files as $f) {
    $content = file_get_contents($f);
    $orig = $content;
    $changed = false;

    // 1) trouver les imgs sans alt et ajouter alt="Image"
    $content = preg_replace_callback('#<img\s+([^>]*?)>#i', function($m) use (&$changed, $f) {
        $attrs = $m[1];
        if (preg_match('/\balt\s*=/i', $attrs)) return $m[0];
        // tenter d'extraire un nom de fichier depuis src
        if (preg_match('/src\s*=\s*["\']([^"\']+)["\']/i', $attrs, $s)) {
            $src = basename($s[1]);
            $alt = preg_replace('/[-_\.]+/', ' ', pathinfo($src, PATHINFO_FILENAME));
            $alt = trim($alt);
            if ($alt === '') $alt = 'Image';
        } else {
            $alt = 'Image';
        }
        $changed = true;
        return '<img ' . $attrs . ' alt="' . htmlspecialchars($alt, ENT_QUOTES) . '">';
    }, $content);

    // 2) vérifier présence d'un h1
    if (!preg_match('/<h1\b/i', $content)) {
        // essayer d'insérer après <main> ou après <body>
        $pageTitle = basename($f, '.' . pathinfo($f, PATHINFO_EXTENSION));
        $insert = "<h1>" . ucfirst(str_replace(['-', '_'], ' ', $pageTitle)) . "</h1>\n";
        if (preg_match('/<main[^>]*>/i', $content, $m, PREG_OFFSET_CAPTURE)) {
            $pos = $m[0][1] + strlen($m[0][0]);
            $content = substr_replace($content, '\n' . $insert, $pos, 0);
            $changed = true;
        } elseif (preg_match('/<body[^>]*>/i', $content, $m, PREG_OFFSET_CAPTURE)) {
            $pos = $m[0][1] + strlen($m[0][0]);
            $content = substr_replace($content, '\n' . $insert, $pos, 0);
            $changed = true;
        }
    }

    if ($changed) {
        $report[] = $f;
        if ($apply) {
            copy($f, $f . '.bak');
            file_put_contents($f, $content);
        }
    }
}

// Résumé
echo "Fichiers inspectés : " . count($files) . "\n";
echo "Fichiers modifiés/proposés : " . count($report) . "\n";
foreach ($report as $r) echo " - $r\n";
if ($apply) echo "Modifications appliquées (sauvegardes .bak créées).\n";
else echo "Exécution en mode 'dry-run'. Relancer avec --apply pour écrire les changements.\n";
