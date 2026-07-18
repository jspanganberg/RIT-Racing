<?php
// ============================================================
//  RIT RACING — Site Configuration
//  
//  This file is loaded by every public page via header.php.
//  It provides:
//    - Path detection (works on any server without config changes)
//    - Helper functions for loading/saving JSON data
//    - Settings accessors (get_setting, site_image, etc.)
//    - URL and asset path builders
//
//  NOTE: These constants below are FALLBACK defaults only.
//  The admin panel stores the real values in data/settings.json
//  and the get_setting() function reads from there first.
// ============================================================

define('SITE_NAME',     'RIT Racing');
define('SITE_TAGLINE',  'Kate Gleason College of Engineering');
define('CONTACT_EMAIL', 'formula@rit.edu');

// ── Auto-detect BASE_PATH ────────────────────────────────────
(function () {
    $site_root_fs = realpath(dirname(dirname(__FILE__)));
    $script_fs    = realpath(dirname($_SERVER['SCRIPT_FILENAME']));

    if ($site_root_fs && $script_fs) {
        $site_root_fs = str_replace('\\', '/', $site_root_fs);
        $script_fs    = str_replace('\\', '/', $script_fs);
        $script_rel   = str_replace($site_root_fs, '', $script_fs);
        $web_dir      = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $base         = rtrim(substr($web_dir, 0, strlen($web_dir) - strlen($script_rel)), '/');
    } else {
        $doc_root = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
        $site_dir = rtrim(str_replace('\\', '/', dirname(dirname(__FILE__))), '/');
        $base     = rtrim(str_replace($doc_root, '', $site_dir), '/');
    }

    define('BASE_PATH', $base);
})();

// ── Auto-detect SITE_URL (works on any server) ──────────────
define('SITE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
    . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE_PATH);

// ── Data file helpers (shared by public + admin) ─────────────
// Single shared registry — both data_load and data_save access the same static array
// so saves immediately invalidate the cache within the same request.
function &_data_registry(): array {
    static $r = [];
    return $r;
}

function data_load(string $filename): array {
    $r = &_data_registry();
    if (array_key_exists($filename, $r)) return $r[$filename];
    $path = '/var/www/data/' . $filename;
    if (file_exists($path) && is_readable($path)) {
        $data = json_decode(file_get_contents($path), true);
        if (is_array($data)) return $r[$filename] = $data;
    }
    return $r[$filename] = [];
}

function data_save(string $filename, array $data): bool {
    $dir = '/var/www/data';
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    $path = $dir . '/' . $filename;

    // Auto-backup: save a copy before overwriting
    if (file_exists($path)) {
        $backupDir = $dir . '/backups';
        if (!is_dir($backupDir)) @mkdir($backupDir, 0755, true);
        // Keep timestamped backup (e.g., settings.json → backups/settings_2026-04-02_143022.json)
        $ts = date('Y-m-d_His');
        $backupName = pathinfo($filename, PATHINFO_FILENAME) . '_' . $ts . '.json';
        @copy($path, $backupDir . '/' . $backupName);

        // Prune old backups: keep only the last 20 per file
        $prefix = pathinfo($filename, PATHINFO_FILENAME) . '_';
        $existing = glob($backupDir . '/' . $prefix . '*.json');
        if (count($existing) > 20) {
            sort($existing); // oldest first
            $toDelete = array_slice($existing, 0, count($existing) - 20);
            foreach ($toDelete as $old) @unlink($old);
        }
    }

    $ok = (bool) file_put_contents(
        $path,
        json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
    );
    if ($ok) { $r = &_data_registry(); $r[$filename] = $data; }
    return $ok;
}

// Social Media
define('SOCIAL_INSTAGRAM', 'https://www.instagram.com/rit_racing/');
define('SOCIAL_FACEBOOK',  'https://www.facebook.com/RITracing/');
define('SOCIAL_TWITTER',   'https://twitter.com/rit_racing');
define('SOCIAL_YOUTUBE',   '');

// Meeting defaults (overridden by admin settings)
define('MEETING_LOCATION', 'Kate Gleason College of Engineering — Bldg 9, Rm 2360');
define('MEETING_TIMES', [
    'Tuesday'  => '8:00 PM',
    'Thursday' => '8:00 PM',
    'Saturday' => '10:00 AM',
]);

// Competition result defaults (overridden by admin settings)
define('LATEST_CAR',    'F33');
define('LATEST_RESULT', '8th Place Overall — FSAE Michigan 2025');
define('BIGGEST_WIN',   '1st Place Overall — FSAE Michigan 2024 (F32)');

define('SMTP_HOST', 'mail.smtp2go.com');
define('SMTP_PORT', 587);
define('SMTP_USER', getenv('SMTP_USER'));
define('SMTP_PASS', getenv('SMTP_PASS'));

// ── Asset / URL helpers ──────────────────────────────────────
function asset(string $path): string {
    $path = preg_replace('#^(\.\./)+#', '', $path);
    return BASE_PATH . '/assets/' . ltrim($path, '/');
}

// ── Clean URLs ───────────────────────────────────────────────
// Set to true if your server supports mod_rewrite (removes .php from URLs).
// Set to false if clean URLs cause 404 errors (e.g., people.rit.edu user dirs).
// The .htaccess rewrite rules must also be in place for this to work.
define('CLEAN_URLS', true);

function url(string $page = ''): string {
    $page = preg_replace('#^(\.\./)+#', '', $page);
    $page = ltrim($page, '/');
    if ($page === 'index.php' || $page === '') return BASE_PATH . '/';
    if (CLEAN_URLS) {
        $page = preg_replace('/\.php$/', '', $page);
    }
    return BASE_PATH . '/' . $page;
}

function active(string $page): string {
    $current = basename($_SERVER['PHP_SELF'], '.php');
    return ($current === $page) ? 'active' : '';
}

// ── Dynamic settings from admin ──────────────────────────────
// Loads /data/settings.json saved by the admin panel.
// Falls back to the constants above if not set.
function get_setting(string $key, $default = '') {
    $s = data_load('settings.json');
    return $s[$key] ?? $default;
}

function get_meeting_times(): array {
    $t = get_setting('meeting_times');
    return (is_array($t) && !empty($t)) ? $t : MEETING_TIMES;
}
function get_meeting_location(): string {
    return get_setting('meeting_location', MEETING_LOCATION);
}
function get_latest_result(): string {
    return get_setting('latest_result', LATEST_RESULT);
}
function get_latest_car(): string {
    return get_setting('latest_car', LATEST_CAR);
}
function get_contact_email(): string {
    return get_setting('contact_email', CONTACT_EMAIL);
}
function get_hero_heading(): string {
    return get_setting('hero_heading', 'Built to Win.');
}
function get_hero_sub(): string {
    return get_setting('hero_sub', "RIT Racing is Rochester Institute of Technology's Formula SAE team — designing, fabricating, testing, and racing the finest open-wheel electric race cars in the world.");
}

// ── Site image helper (admin-uploaded or default) ───────────
function site_image(string $slot, string $default = ''): string {
    $images = get_setting('site_images', []);
    if (!empty($images[$slot])) {
        $path = dirname(dirname(__FILE__)) . '/assets/images/site/' . $images[$slot];
        if (file_exists($path)) {
            return asset('images/site/' . $images[$slot]);
        }
    }
    return $default ? asset($default) : '';
}

// ── Vehicle data helpers ─────────────────────────────────────
function get_vehicles(string $program = 'electric'): array {
    $data = data_load('vehicles.json');
    $list = $data[$program] ?? [];
    usort($list, fn($a,$b) => ($a['order']??99) <=> ($b['order']??99));
    return $list;
}


