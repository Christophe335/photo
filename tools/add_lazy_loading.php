<?php
// Usage: php add_lazy_loading.php [--apply]
// Ajoute loading="lazy" aux <img> qui n'en ont pas, exclut les logos

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

$modified = [];
foreach ($files as $f) {
    $content = file_get_contents($f);
    $orig = $content;

    $content = preg_replace_callback('#<img\s+([^>]*?)>#i', function($m) use (&$f) {
        $attrs = $m[1];
        // if already has loading attribute, skip
        if (preg_match('/\bloading\s*=\s*/i', $attrs)) return $m[0];
        // skip logos: class contains logo OR src contains logo OR alt contains logo
        if (preg_match('/class\s*=\s*["\'][^"\']*logo[^"\']*["\']/i', $attrs)) return $m[0];
        if (preg_match('/src\s*=\s*["\'][^"\']*logo[^"\']*["\']/i', $attrs)) return $m[0];
        if (preg_match('/alt\s*=\s*["\'][^"\']*logo[^"\']*["\']/i', $attrs)) return $m[0];
        // Otherwise add loading="lazy" before closing
        return '<img ' . $attrs . ' loading="lazy">';
    }, $content);

    if ($content !== $orig) {
        $modified[] = $f;
        if ($apply) {
            copy($f, $f . '.bak');
            file_put_contents($f, $content);
        }
    }
}

echo "Fichiers inspectés: " . count($files) . "\n";
echo "Modifications proposées/appliquées: " . count($modified) . "\n";
foreach ($modified as $m) echo " - $m\n";
if ($apply) echo "Modifications écrites (sauvegardes .bak créées).\n"; else echo "Dry-run. Relancer avec --apply pour modifier les fichiers.\n";
