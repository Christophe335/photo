<?php
// Usage: php add_button_aria.php [--apply]
// Ajoute des attributs aria-label aux <button> sans nom accessible, en se basant sur la classe ou onclick.

$apply = in_array('--apply', $argv);
$root = realpath(__DIR__ . '/..');
$exclude = ['vendor','admin','includes','ajax','uploads','storage','sql','tests','tools','.git'];

$files = [];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
foreach ($it as $file) {
    if (!$file->isFile()) continue;
    $path = str_replace($root . DIRECTORY_SEPARATOR, '', $file->getPathname());
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    if (!in_array($ext, ['php','html'])) continue;
    $skip = false; foreach ($exclude as $e) { if (strpos($path, $e . DIRECTORY_SEPARATOR) === 0 || strpos($path, DIRECTORY_SEPARATOR . $e . DIRECTORY_SEPARATOR) !== false) { $skip = true; break; } }
    if ($skip) continue;
    $files[] = $file->getPathname();
}

$mapClass = [
    'btn-supprimer' => 'Supprimer',
    'btn-ajouter-panier' => 'Ajouter au panier',
    'btn-moins' => 'Diminuer la quantité',
    'btn-plus' => 'Augmenter la quantité',
    'mobile-menu-toggle' => 'Ouvrir le menu',
    'search-btn' => 'Rechercher',
    'btn-cart' => 'Voir le panier',
    'btn-account' => 'Mon compte',
    'btn-ajouter' => 'Ajouter',
    'btn-supprimer-panier' => 'Supprimer du panier'
];

$modified = [];
foreach ($files as $f) {
    $content = file_get_contents($f);
    $orig = $content;

    $content = preg_replace_callback('#<button\b([^>]*)>(.*?)</button>#is', function($m) use ($mapClass, &$modified, $f, $apply) {
        $attrs = $m[1];
        $inner = trim(strip_tags($m[2]));
        // if already has aria-label or title or has meaningful text (>2 chars alnum), skip
        if (preg_match('/\baria-label\s*=\s*["\"]/i', $attrs)) return $m[0];
        if (preg_match('/\btitle\s*=\s*["\"]/i', $attrs)) return $m[0];
        // consider inner text meaningful if contains letters or numbers length>=2
        if (preg_match('/[A-Za-z0-9\p{L}]{2,}/u', $inner)) return $m[0];

        // try to derive from class
        $label = null;
        if (preg_match('/class\s*=\s*["\']([^"\']+)["\']/i', $attrs, $c)) {
            $classes = preg_split('/\s+/', $c[1]);
            foreach ($classes as $cl) {
                if (isset($mapClass[$cl])) { $label = $mapClass[$cl]; break; }
            }
        }

        // try onclick function name
        if (!$label && preg_match('/onclick\s*=\s*["\']\s*([a-zA-Z0-9_\-]+)\s*\(/i', $attrs, $o)) {
            $fn = $o[1];
            $label = ucfirst(str_replace(['_','-'],' ', $fn));
        }

        // emoji or single char
        if (!$label && preg_match('/^[^\w]{1,3}$/u', $inner)) {
            // map common emojis
            if (strpos($inner, '🗑') !== false || strpos($inner, '🗑️') !== false) $label = 'Supprimer';
            elseif (strpos($inner, '+') !== false) $label = 'Augmenter';
            elseif (strpos($inner, '−') !== false || strpos($inner, '-') !== false) $label = 'Diminuer';
            else $label = 'Action';
        }

        if (!$label) return $m[0];

        $newAttrs = rtrim($attrs) . ' aria-label="' . htmlspecialchars($label, ENT_QUOTES) . '"';
        $modified[] = $f;
        return '<button' . $newAttrs . '>' . $m[2] . '</button>';
    }, $content);

    if ($content !== $orig) {
        if ($apply) {
            copy($f, $f . '.bak');
            file_put_contents($f, $content);
        }
    }
}

echo "Fichiers inspectés: " . count($files) . "\n";
echo "Fichiers modifiés: " . count(array_unique($modified)) . "\n";
foreach (array_unique($modified) as $m) echo " - $m\n";
if ($apply) echo "Modifications écrites (sauvegardes .bak créées).\n"; else echo "Dry-run. Relancer avec --apply pour écrire les modifications.\n";
