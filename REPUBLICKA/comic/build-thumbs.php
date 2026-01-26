<?php
// One-time thumbnail builder for TComic
// Visit /tcomic/build-thumbs.php once to generate thumbs/001.jpg etc.

$totalPages  = 368;
$srcDir      = __DIR__;
$destDir     = __DIR__ . '/thumbs';
$maxWidth    = 800;   // thumbnail width in pixels
$quality     = 70;    // JPEG quality (0–100)

if (!extension_loaded('gd')) {
    die('PHP GD extension is not enabled on this server.');
}

if (!is_dir($destDir)) {
    if (!mkdir($destDir, 0755, true)) {
        die('Failed to create thumbs directory.');
    }
}

echo "<pre>";
for ($i = 1; $i <= $totalPages; $i++) {
    $num      = str_pad($i, 3, '0', STR_PAD_LEFT);
    $srcPath  = "{$srcDir}/{$num}.png";
    $destPath = "{$destDir}/{$num}.jpg";

    if (!file_exists($srcPath)) {
        echo "Missing source: {$srcPath}\n";
        continue;
    }

    if (file_exists($destPath)) {
        echo "Skipping existing thumb: {$destPath}\n";
        continue;
    }

    $src = @imagecreatefrompng($srcPath);
    if (!$src) {
        echo "Could not open: {$srcPath}\n";
        continue;
    }

    $srcW = imagesx($src);
    $srcH = imagesy($src);

    if ($srcW <= 0 || $srcH <= 0) {
        imagedestroy($src);
        echo "Bad dimensions: {$srcPath}\n";
        continue;
    }

    if ($srcW <= $maxWidth) {
        $newW = $srcW;
        $newH = $srcH;
    } else {
        $scale = $maxWidth / $srcW;
        $newW  = (int)round($srcW * $scale);
        $newH  = (int)round($srcH * $scale);
    }

    $thumb = imagecreatetruecolor($newW, $newH);

    // White background in case of transparency
    $white = imagecolorallocate($thumb, 255, 255, 255);
    imagefill($thumb, 0, 0, $white);

    imagecopyresampled($thumb, $src, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);
    imagejpeg($thumb, $destPath, $quality);

    imagedestroy($src);
    imagedestroy($thumb);

    echo "Created thumbnail: {$destPath}\n";
}
echo "Done.\n";
