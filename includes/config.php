<?php
// ============================================================
//  RIT RACING — Site Configuration
//  Edit this file to update site-wide settings
// ============================================================

define('SITE_NAME',     'RIT Racing');
define('SITE_TAGLINE',  'Kate Gleason College of Engineering');
define('SITE_URL',      'https://www.ritformula.com'); // update when live
define('CONTACT_EMAIL', 'formula@rit.edu');

/**
 * Normalize a URL path prefix.
 *
 * Examples:
 * - ''            => ''
 * - 'ritracing'   => '/ritracing'
 * - '/ritracing/' => '/ritracing'
 */
function normalize_path_prefix(string $path): string {
    $trimmed = trim($path);
    if ($trimmed === '' || $trimmed === '/') {
        return '';
    }

    return '/' . trim($trimmed, '/');
}

// Keep BASE_PATH explicit and predictable.
// - Local/root hosting: leave unset => ''
// - Subdirectory hosting: set env var, e.g. RIT_BASE_PATH=/ritracing
$configuredBasePath = getenv('RIT_BASE_PATH');
$basePath = ($configuredBasePath !== false)
    ? normalize_path_prefix($configuredBasePath)
    : '';

define('BASE_PATH', $basePath);

// Social Media
define('SOCIAL_INSTAGRAM', 'https://www.instagram.com/rit_racing/');
define('SOCIAL_FACEBOOK',  'https://www.facebook.com/RITracing/');
define('SOCIAL_TWITTER',   'https://twitter.com/rit_racing');
define('SOCIAL_YOUTUBE',   ''); // Add when available

// Meeting Info (update each semester)
define('MEETING_LOCATION', 'Kate Gleason College of Engineering — Bldg 9, Rm 2360');
define('MEETING_TIMES', [
    'Tuesday'  => '8:00 PM',
    'Thursday' => '8:00 PM',
    'Saturday' => '10:00 AM',
]);

// Latest Competition Result (update each year)
define('LATEST_CAR',         'F33');
define('LATEST_RESULT',      '8th Place Overall — FSAE Michigan 2025');
define('BIGGEST_WIN',        '1st Place Overall — FSAE Michigan 2024 (F32)');

// Helper: Build asset URL
function asset(string $path): string {
    return BASE_PATH . '/assets/' . ltrim($path, '/');
}

// Helper: Build page URL
function url(string $page = ''): string {
    return BASE_PATH . '/' . ltrim($page, '/');
}

// Helper: Active nav link class
function active(string $page): string {
    $current = basename($_SERVER['PHP_SELF'], '.php');
    return ($current === $page) ? 'active' : '';
}
?>
