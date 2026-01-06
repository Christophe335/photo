<?php
// Usage: php inject_srcset.php [--apply]
// Lit images_optimized_manifest.json et injecte srcset/sizes dans les <img> des pages

$apply = in_array('--apply', $argv);
$root = realpath(__DIR__ . '/..');
$manifestFile = $root . DIRECTORY_SEPARATOR . 'images_optimized_manifest.json';
if (!file_exists($manifestFile)) { echo "Manifest not found: images_optimized_manifest.json\n"; exit(1); }
$manifest = json_decode(file_get_contents($manifestFile), true);
if (!$manifest || !isset($manifest['manifest'])) { echo "Manifest invalide\n"; exit(1); }
$map = $manifest['manifest']; // keys: images/...

$targets = [
    $root . DIRECTORY_SEPARATOR . 'pages',
    $root . DIRECTORY_SEPARATOR . 'pages-perso',
    $root . DIRECTORY_SEPARATOR . 'index.php',
    $root . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'header.php'
];

$files = [];
foreach ($targets as $t) {
    if (is_dir($t)) {
        $it = new DirectoryIterator($t);
        foreach ($it as $f) {
            if ($f->isDot() || !$f->isFile()) continue;
            $ext = strtolower($f->getExtension());
            if (!in_array($ext, ['php','html'])) continue;
            $files[] = $f->getPathname();
        }
    } elseif (is_file($t)) {
        $files[] = $t;
    }
}

$modified = [];
foreach ($files as $fpath) {
    $orig = file_get_contents($fpath);
    $content = $orig;

    $content = preg_replace_callback('#<img\s+([^>]*?)>#i', function($m) use ($map, $root, &$fpath, &$modified) {
        $attrs = $m[1];
        // if already has srcset, skip
        if (preg_match('/\bsrcset\s*=\s*/i', $attrs)) return $m[0];
        // find src
        if (!preg_match('/src\s*=\s*["\']([^"\']+)["\']/i', $attrs, $s)) return $m[0];
        $src = $s[1];
        $norm = ltrim($src, '/');
        // remove ../ or ./ prefixes
        while (strpos($norm, '../') === 0) $norm = substr($norm, 3);
        while (strpos($norm, './') === 0) $norm = substr($norm, 2);

        // try to match manifest key by exact or endswith
        $matchedKey = null;
        if (isset($map[$norm])) {
            $matchedKey = $norm;
        } else {
            foreach ($map as $k => $v) {
                if (substr($k, -strlen($norm)) === $norm) { $matchedKey = $k; break; }
            }
        }
        if (!$matchedKey) return $m[0];

        $entry = $map[$matchedKey];
        $variants = $entry['variants'] ?? [];
        if (empty($variants)) return $m[0];

        // build srcset entries; use root-relative URLs
        $parts = [];
        foreach ($variants as $v) {
            if (isset($v['filesize']) && $v['filesize']!==null) {
                $url = '/' . str_replace('\\','/',$v['path']);
                $parts[] = $url . ' ' . $v['width'] . 'w';
            }
        }
        if (empty($parts)) return $m[0];

        $srcset = implode(', ', $parts);
        // simple sizes: allow full width responsiveness
        $sizes = '(max-width: 800px) 100vw, 800px';

        // insert srcset and sizes before closing
        $newAttrs = $attrs . ' srcset="' . $srcset . '" sizes="' . $sizes . '"';
        $modified[] = $fpath;
        return '<img ' . $newAttrs . '>';
    }, $content);

    if ($content !== $orig) {
        if ($apply) {
            copy($fpath, $fpath . '.bak');
            file_put_contents($fpath, $content);
        }
    }
}

// report
$uniq = array_unique($modified);
echo "Fichiers inspectés: " . count($files) . "\n";
echo "Fichiers modifiés: " . count($uniq) . "\n";
foreach ($uniq as $u) echo " - $u\n";
if ($apply) echo "Modifications appliquées (sauvegardes .bak créées).\n"; else echo "Dry-run. Relancer avec --apply pour écrire les modifications.\n";
