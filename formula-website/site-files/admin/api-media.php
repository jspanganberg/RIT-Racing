<?php
/**
 * api-media.php — Returns JSON list of images in a given subfolder.
 * Used by the Browse Media picker modal in all admin editors.
 *
 * GET ?folder=team          -> lists assets/images/team/*
 * GET ?folder=cars/electric -> lists assets/images/cars/electric/*
 * GET ?folder=all           -> lists all known subfolders
 *
 * Known subfolders: cars/electric, cars/combustion, team, sponsors,
 *                   site, uploads, memorial, programs, hero
 *
 * Response: JSON array of { folder, path, file, size } objects.
 */
/**
 * api-media.php — Returns JSON list of images in a given subfolder
 * Used by the photo picker modal in admin editors.
 * 
 * GET ?folder=team          → lists assets/images/team/*
 * GET ?folder=cars/electric → lists assets/images/cars/electric/*
 * GET ?folder=all           → lists all image subfolders
 */
require_once __DIR__ . '/includes/auth.php';
auth_require();

header('Content-Type: application/json');

$base = '/var/www/html/assets/images';
$folder = trim($_GET['folder'] ?? 'uploads');

// Sanitize — only allow alphanumeric, dash, slash
$folder = preg_replace('/[^a-zA-Z0-9\/\-_]/', '', $folder);

$images = [];

if ($folder === 'all') {
    // Scan all known subfolders
    $subs = ['cars/electric','cars/combustion','team','sponsors','site','uploads','memorial','programs'];
    foreach ($subs as $sub) {
        $dir = $base . '/' . $sub;
        if (!is_dir($dir)) continue;
        foreach (glob($dir . '/*.{jpg,jpeg,png,gif,webp,svg}', GLOB_BRACE) as $f) {
            $images[] = [
                'file'   => basename($f),
                'folder' => $sub,
                'path'   => '../assets/images/' . $sub . '/' . basename($f),
                'size'   => filesize($f),
                'mtime'  => filemtime($f),
            ];
        }
    }
} else {
    $dir = $base . '/' . $folder;
    if (is_dir($dir)) {
        foreach (glob($dir . '/*.{jpg,jpeg,png,gif,webp,svg}', GLOB_BRACE) as $f) {
            $images[] = [
                'file'   => basename($f),
                'folder' => $folder,
                'path'   => '../assets/images/' . $folder . '/' . basename($f),
                'size'   => filesize($f),
                'mtime'  => filemtime($f),
            ];
        }
    }
}

// Sort newest first
usort($images, fn($a, $b) => ($b['mtime'] ?? 0) <=> ($a['mtime'] ?? 0));

echo json_encode(['images' => $images, 'count' => count($images)]);
