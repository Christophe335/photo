<?php
// Usage: php optimize_images_webp.php [--quality=80] [--sizes=480,800,1200,1600] [--apply]
// Scanne le dossier images/, sauvegarde originaux, génère variantes redimensionnées WebP

$apply = in_array('--apply', $argv);
$quality = 80;
$sizes = [480,800,1200,1600];
foreach ($argv as $a) {
    if (strpos($a,'--quality=')===0) $quality = (int)substr($a,10);
    if (strpos($a,'--sizes=')===0) $sizes = array_map('intval', explode(',', substr($a,8)));
}

if (!extension_loaded('gd')) {
    echo "Extension GD PHP manquante. Le script nécessite GD (imagecreatefromwebp/imagewebp).\n";
    exit(1);
}

$root = realpath(__DIR__ . '/..');
$imagesDir = $root . DIRECTORY_SEPARATOR . 'images';
if (!is_dir($imagesDir)) { echo "Dossier images/ introuvable.\n"; exit(1); }

$backupDir = $root . DIRECTORY_SEPARATOR . 'images_backup_' . date('Ymd_His');
mkdir($backupDir, 0755, true);

$manifest = [];
$totalSaved = 0;
$totalOrig = 0;

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($imagesDir));
foreach ($it as $file) {
    if (!$file->isFile()) continue;
    $ext = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
    if ($ext !== 'webp') continue;

    $origPath = $file->getPathname();
    $relPath = str_replace($root . DIRECTORY_SEPARATOR, '', $origPath);
    $info = [];

    // backup original
    $destBackup = $backupDir . DIRECTORY_SEPARATOR . $relPath;
    @mkdir(dirname($destBackup), 0755, true);
    copy($origPath, $destBackup);

    $img = @imagecreatefromwebp($origPath);
    if (!$img) {
        echo "Impossible d'ouvrir $relPath, saut.\n";
        continue;
    }
    $w = imagesx($img);
    $h = imagesy($img);
    $origSize = filesize($origPath);
    $totalOrig += $origSize;

    $variants = [];
    foreach ($sizes as $tw) {
        if ($w <= $tw) continue; // pas besoin
        $ratio = $h / $w;
        $th = (int)round($tw * $ratio);
        $dst = imagecreatetruecolor($tw, $th);
        // preserve alpha
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $img, 0,0,0,0, $tw, $th, $w, $h);

        $newPath = dirname($origPath) . DIRECTORY_SEPARATOR . pathinfo($file->getFilename(), PATHINFO_FILENAME) . '-w' . $tw . '.webp';
        if ($apply) {
            imagewebp($dst, $newPath, $quality);
        } else {
            // simulate by writing to temp and deleting
            $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('webp').'.webp';
            imagewebp($dst, $tmp, $quality);
            @unlink($tmp);
        }
        imagedestroy($dst);

        if (file_exists($newPath)) {
            $size = filesize($newPath);
            $variants[] = ['path' => str_replace($root . DIRECTORY_SEPARATOR, '', $newPath), 'width' => $tw, 'filesize' => $size];
        } else {
            $variants[] = ['path' => str_replace($root . DIRECTORY_SEPARATOR, '', $newPath), 'width' => $tw, 'filesize' => null];
        }
    }

    // Recompress original to target quality and overwrite (after backup)
    $recompressedPath = $origPath;
    if ($apply) {
        imagewebp($img, $recompressedPath, $quality);
    }
    imagedestroy($img);

    $newSize = filesize($recompressedPath);
    $saved = $origSize - $newSize;
    $totalSaved += max(0,$saved);

    $manifest[$relPath] = ['original_size' => $origSize, 'new_size' => $newSize, 'saved' => max(0,$saved), 'variants' => $variants];

    echo "Optimisé: $relPath — orig:" . round($origSize/1024,1) . "KiB -> new:" . round($newSize/1024,1) . "KiB (saved:" . round(max(0,$saved)/1024,1) . "KiB)\n";
}

file_put_contents($root . DIRECTORY_SEPARATOR . 'images_optimized_manifest.json', json_encode(['generated'=>date('c'),'quality'=>$quality,'manifest'=>$manifest], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));

echo "\nTotal original size: " . round($totalOrig/1024,1) . " KiB\n";
echo "Total estimated saved: " . round($totalSaved/1024,1) . " KiB\n";
echo "Backup originals in: $backupDir\n";
echo "Manifest: images_optimized_manifest.json\n";
