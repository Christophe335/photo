<?php
// Usage: php generate_sitemap.php example.com
// Scanne le site et génère sitemap.xml et robots.txt dans le répertoire racine.

$base = isset($argv[1]) ? rtrim($argv[1], '/') : 'https://example.com';
if (!preg_match('#^https?://#', $base)) $base = 'https://' . $base;

$root = realpath(__DIR__ . '/..');
$exclude = ['vendor', 'admin', 'includes', 'ajax', 'uploads', 'storage', 'sql', 'tests', 'tools', '.git'];
$urls = [];

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
foreach ($it as $file) {
    if (!$file->isFile()) continue;
    $path = str_replace($root . DIRECTORY_SEPARATOR, '', $file->getPathname());
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    if (!in_array($ext, ['php', 'html'])) continue;
    // exclude by path segment
    $skip = false;
    foreach ($exclude as $e) {
        if (strpos($path, $e . DIRECTORY_SEPARATOR) === 0 || strpos($path, DIRECTORY_SEPARATOR . $e . DIRECTORY_SEPARATOR) !== false) { $skip = true; break; }
    }
    if ($skip) continue;
    // exclude PHP files that are includes
    $basename = basename($path);
    if (strpos($basename, 'seo') !== false || strpos($basename, 'header') !== false || strpos($basename, 'footer') !== false) continue;

    // Normaliser l'URL
    $urlPath = str_replace('\\', '/', $path);
    $url = $base . '/' . $urlPath;
    // if index.php at root map to base
    if (in_array($urlPath, ['index.php', 'index.html'])) {
        $urls[] = $base . '/';
    } else {
        $urls[] = $url;
    }
}

// Déduplique et trie
$urls = array_values(array_unique($urls));
sort($urls);

$sitemapPath = $root . DIRECTORY_SEPARATOR . 'sitemap.xml';
$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');
foreach ($urls as $u) {
    $url = $xml->addChild('url');
    $url->addChild('loc', htmlspecialchars($u, ENT_QUOTES | ENT_XML1));
    $url->addChild('changefreq', 'weekly');
    $url->addChild('priority', '0.7');
}

file_put_contents($sitemapPath, $xml->asXML());
echo "sitemap.xml généré : $sitemapPath\n";

// Générer robots.txt
$robots = "User-agent: *\nAllow: /\nSitemap: $base/sitemap.xml\n";
file_put_contents($root . DIRECTORY_SEPARATOR . 'robots.txt', $robots);
echo "robots.txt généré : " . $root . DIRECTORY_SEPARATOR . "robots.txt\n";

echo "Total URLs: " . count($urls) . "\n";
