<?php
// Génère un rapport de propositions SEO pour les fichiers dans pages/ et pages-perso/
// Usage: php generate_seo_proposals.php

$root = realpath(__DIR__ . '/..');
$targets = [
    $root . DIRECTORY_SEPARATOR . 'pages',
    $root . DIRECTORY_SEPARATOR . 'pages-perso'
];

$files = [];
foreach ($targets as $dir) {
    if (!is_dir($dir)) continue;
    $it = new DirectoryIterator($dir);
    foreach ($it as $file) {
        if ($file->isDot() || !$file->isFile()) continue;
        $ext = $file->getExtension();
        if (!in_array($ext, ['php','html'])) continue;
        $files[] = $file->getPathname();
    }
}

function extract_meta($content) {
    $res = ['title' => null, 'description' => null, 'h1' => null];
    if (preg_match('#<title[^>]*>(.*?)</title>#is', $content, $m)) $res['title'] = trim(strip_tags($m[1]));
    if (preg_match('#<meta\s+name=["\']description["\']\s+content=["\'](.*?)["\']#is', $content, $m)) $res['description'] = trim($m[1]);
    if (preg_match('#<h1[^>]*>(.*?)</h1>#is', $content, $m)) $res['h1'] = trim(strip_tags($m[1]));
    return $res;
}

function human_label_from_filename($path) {
    $base = basename($path);
    $name = preg_replace('/\.php$|\.html$/i','',$base);
    $name = str_replace(['-','_'], ' ', $name);
    $name = preg_replace('/\bperso\b/i','personnalisé', $name);
    $name = preg_replace('/\bpanier\b/i','Panier', $name);
    return ucwords($name);
}

$md = [];
$md[] = "# Propositions SEO — pages & pages-perso";
$md[] = "Généré le: " . date('c');
$md[] = "";
$md[] = "| Fichier | Titre actuel | Meta description actuel | H1 actuel | Proposition titre | Proposition meta | Proposition H1 |";
$md[] = "|---|---|---|---|---|---|---|";

foreach ($files as $f) {
    $content = file_get_contents($f);
    $meta = extract_meta($content);
    $label = human_label_from_filename($f);

    // Proposition de title
    $prop_title = sprintf('%s - Impression photo & produits personnalisés | Bindy Studio', $label);
    // Proposition description (max ~155)
    $prop_meta = sprintf('Commandez %s chez Bindy Studio — impression photo professionnelle, finitions soignées et livraison rapide. Créez le vôtre en ligne.', strtolower($label));
    if (strlen($prop_meta) > 155) $prop_meta = substr($prop_meta,0,152) . '...';
    // Proposition H1
    $prop_h1 = $label;

    $md[] = '|' . str_replace('|','\|', str_replace($root . DIRECTORY_SEPARATOR, '', $f))
        . '|' . str_replace('|','\|', $meta['title'] ?? '')
        . '|' . str_replace('|','\|', $meta['description'] ?? '')
        . '|' . str_replace('|','\|', $meta['h1'] ?? '')
        . '|' . str_replace('|','\|', $prop_title)
        . '|' . str_replace('|','\|', $prop_meta)
        . '|' . str_replace('|','\|', $prop_h1) . '|';
}

$out = implode("\n", $md) . "\n";
file_put_contents($root . DIRECTORY_SEPARATOR . 'tools' . DIRECTORY_SEPARATOR . 'seo_proposals.md', $out);
echo "Rapport généré: tools/seo_proposals.md\n";
