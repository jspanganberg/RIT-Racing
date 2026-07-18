<?php
require_once __DIR__ . '/includes/auth.php';
auth_require();
$adminTitle = 'Dashboard';
// NOTE: Maintenance mode was removed in v39.
// The toggle form, $maintenanceOn variable, and diagnostics warning
// have all been deleted. The maintenance_mode key in settings.json
// is ignored — do not re-add any maintenance checks here.

$team = data_load('team.json');
$sponsors = data_load('sponsors.json');
$vehicles = data_load('vehicles.json');
$programs = data_load('programs.json');
$settings = data_load('settings.json');
$siteImages = $settings['site_images'] ?? [];

$teamCount = count($team);
$sponsorCount = count($sponsors);
$vehicleCount = count($vehicles['electric'] ?? []) + count($vehicles['combustion'] ?? []);
$programCount = count($programs);
$userCount = count(users_load());

// Count media
$mediaCount = 0;
$imgBase = dirname(__DIR__) . '/assets/images';
foreach (['cars/electric','cars/combustion','team','sponsors','site','uploads','memorial','programs'] as $sub) {
    if (is_dir($imgBase.'/'.$sub)) $mediaCount += count(glob($imgBase.'/'.$sub.'/*.{jpg,jpeg,png,gif,webp,svg}', GLOB_BRACE));
}

require_once __DIR__ . '/includes/admin-header.php';
?>

<!-- Welcome Banner -->
<div class="dash-welcome">
    <div>
        <h2>Welcome back, <span><?= htmlspecialchars(auth_name()) ?></span></h2>
        <p><?= date('l, F j, Y') ?> — <?= $teamCount ?> team members, <?= $sponsorCount ?> sponsors, <?= $vehicleCount ?> vehicles</p>
    </div>
</div>

<!-- Stats Grid -->
<div class="dash-grid">
    <a href="team.php" class="dash-card">
        <div class="dash-card-icon"><i class="fa fa-users"></i></div>
        <div><div class="dash-card-num"><?= $teamCount ?></div><div class="dash-card-label">Team Members</div></div>
    </a>
    <a href="sponsors.php" class="dash-card">
        <div class="dash-card-icon"><i class="fa fa-handshake"></i></div>
        <div><div class="dash-card-num"><?= $sponsorCount ?></div><div class="dash-card-label">Sponsors</div></div>
    </a>
    <a href="vehicles.php" class="dash-card">
        <div class="dash-card-icon"><i class="fa fa-car"></i></div>
        <div><div class="dash-card-num"><?= $vehicleCount ?></div><div class="dash-card-label">Vehicles</div></div>
    </a>
    <a href="programs.php" class="dash-card">
        <div class="dash-card-icon"><i class="fa fa-cubes"></i></div>
        <div><div class="dash-card-num"><?= $programCount ?></div><div class="dash-card-label">Programs</div></div>
    </a>
    <a href="media.php" class="dash-card">
        <div class="dash-card-icon"><i class="fa fa-images"></i></div>
        <div><div class="dash-card-num"><?= $mediaCount ?></div><div class="dash-card-label">Media Files</div></div>
    </a>
    <a href="users.php" class="dash-card">
        <div class="dash-card-icon"><i class="fa fa-user-shield"></i></div>
        <div><div class="dash-card-num"><?= $userCount ?></div><div class="dash-card-label">Admin Users</div></div>
    </a>
</div>

<!-- Quick Actions -->
<div class="admin-card">
    <div class="admin-card-header"><h2><i class="fa fa-bolt"></i> Quick Actions</h2></div>
    <div class="admin-card-body">
        <div class="dash-actions-grid">
            <a href="media.php" class="dash-action dash-action-primary"><i class="fa fa-upload"></i><span>Upload Photos</span></a>
            <a href="team.php?new=1" class="dash-action"><i class="fa fa-user-plus"></i><span>Add Team Member</span></a>
            <a href="sponsors.php?new=1" class="dash-action"><i class="fa fa-handshake"></i><span>Add Sponsor</span></a>
            <a href="vehicles.php?new=1&program=electric" class="dash-action"><i class="fa fa-car"></i><span>Add Vehicle</span></a>
            <a href="programs.php?new=1" class="dash-action"><i class="fa fa-cubes"></i><span>Add Program</span></a>
            <a href="homepage.php" class="dash-action"><i class="fa fa-house"></i><span>Edit Homepage</span></a>
            <a href="about.php" class="dash-action"><i class="fa fa-book-open"></i><span>Edit About Page</span></a>
            <a href="settings.php" class="dash-action"><i class="fa fa-sliders"></i><span>Site Settings</span></a>
            <a href="model.php" class="dash-action"><i class="fa fa-cube"></i><span>3D Car Model</span></a>
        </div>
    </div>
</div>

<!-- How It Works -->
<div class="admin-card">
    <div class="admin-card-header"><h2><i class="fa fa-circle-info"></i> How to Update the Site</h2></div>
    <div class="admin-card-body">
        <div class="dash-steps">
            <div>
                <div class="dash-step-head">
                    <span class="dash-step-num">1</span>
                    <strong>Upload Photos</strong>
                </div>
                <p>Go to <a href="media.php">Media</a> and upload images. Server limit: <strong class="text-warning"><?= ini_get('upload_max_filesize') ?: '2M' ?></strong> per file.</p>
            </div>
            <div>
                <div class="dash-step-head">
                    <span class="dash-step-num">2</span>
                    <strong>Link to Content</strong>
                </div>
                <p>In any editor, click <strong>"Browse Media"</strong> to pick photos. No re-uploading needed.</p>
            </div>
            <div>
                <div class="dash-step-head">
                    <span class="dash-step-num">3</span>
                    <strong>Check the Site</strong>
                </div>
                <p>Click <a href="../index.php" target="_blank">"View Site"</a> to see changes live.</p>
            </div>
        </div>
    </div>
</div>

<!-- Diagnostics -->
<?php
// ── Run all checks ─────────────────────────────────────────
$checks = [];
$warnings = [];
$errors = [];

$root = '/var/www';

// 1. Data file integrity
$data_files = ['settings.json','team.json','sponsors.json','vehicles.json','programs.json','memorial.json','about.json','users.json'];
foreach ($data_files as $df) {
    $path = $root . '/data/' . $df;
    if (!file_exists($path)) {
        if ($df === 'users.json') continue; // ok if missing pre-setup
        $warnings[] = ['icon'=>'fa-file','msg'=>"<strong>{$df}</strong> does not exist",'fix'=>"Will be created when you save data in the admin panel."];
    } else {
        $json = json_decode(file_get_contents($path), true);
        if ($json === null && json_last_error() !== JSON_ERROR_NONE) {
            $errors[] = ['icon'=>'fa-file-circle-xmark','msg'=>"<strong>{$df}</strong> has invalid JSON: " . json_last_error_msg(),'fix'=>"Re-upload the file or fix the JSON syntax."];
        } else {
            $checks[] = ['icon'=>'fa-file-circle-check','msg'=>"{$df} — valid"];
        }
    }
}

// 2. Folder permissions
$folders = ['assets/images/site','assets/images/team','assets/images/sponsors','assets/images/cars/electric','assets/images/cars/combustion','assets/images/uploads','assets/images/programs','assets/images/memorial','assets/models','assets/files'];
foreach ($folders as $f) {
    $fp = $root . '/html/' . $f;
    if (!is_dir($fp)) {
        $warnings[] = ['icon'=>'fa-folder-minus','msg'=>"<strong>{$f}/</strong> directory missing",'fix'=>"Create it: <code>mkdir -p {$f}</code>"];
    } elseif (!is_writable($fp)) {
        $errors[] = ['icon'=>'fa-lock','msg'=>"<strong>{$f}/</strong> is not writable",'fix'=>"Run: <code>chmod 755 {$f}</code>"];
    } else {
        $checks[] = ['icon'=>'fa-folder-open','msg'=>"{$f}/ — writable"];
    }
}

// 3. Broken image references
$broken_images = [];
// Team photos
foreach ($team as $m) {
    $ph = $m['photo'] ?? '';
    if ($ph && !file_exists($root . '/html/assets/images/team/' . $ph)) {
        $broken_images[] = "Team: <strong>" . htmlspecialchars($m['name']) . "</strong> → " . htmlspecialchars($ph);
    }
}
// Sponsor logos
foreach ($sponsors as $sp) {
    $lg = $sp['logo'] ?? '';
    if ($lg && !file_exists($root . '/html/assets/images/sponsors/' . $lg)) {
        $broken_images[] = "Sponsor: <strong>" . htmlspecialchars($sp['name']) . "</strong> → " . htmlspecialchars($lg);
    }
}
// Vehicle photos
foreach (['electric','combustion'] as $vtype) {
    foreach ($vehicles[$vtype] ?? [] as $car) {
        $cp = $car['photo'] ?? '';
        if ($cp && !file_exists($root . '/html/assets/images/cars/' . $vtype . '/' . $cp)) {
            $broken_images[] = ucfirst($vtype) . ": <strong>" . htmlspecialchars($car['name']) . "</strong> → " . htmlspecialchars($cp);
        }
    }
}
// Program photos
foreach ($programs as $pg) {
    $pp = $pg['photo'] ?? '';
    if ($pp && !file_exists($root . '/html/assets/images/programs/' . $pp)) {
        $broken_images[] = "Program: <strong>" . htmlspecialchars($pg['title']) . "</strong> → " . htmlspecialchars($pp);
    }
}
// Site images
foreach ($siteImages as $slot => $fname) {
    if ($fname && !file_exists($root . '/html/assets/images/site/' . $fname)) {
        $broken_images[] = "Site image: <strong>{$slot}</strong> → " . htmlspecialchars($fname);
    }
}
if (!empty($broken_images)) {
    foreach ($broken_images as $bi) {
        $warnings[] = ['icon'=>'fa-image','msg'=>"Missing: {$bi}",'fix'=>"Re-upload the image via Media Library and re-link it."];
    }
} else {
    $checks[] = ['icon'=>'fa-images','msg'=>"All image references valid (" . ($teamCount + $sponsorCount + $vehicleCount) . " checked)"];
}

// 4. PHP config
$upload_max = ini_get('upload_max_filesize') ?: '2M';
$post_max = ini_get('post_max_size') ?: '8M';
$memory = ini_get('memory_limit') ?: '64M';
$checks[] = ['icon'=>'fa-server','msg'=>"PHP " . phpversion() . " — upload: {$upload_max}, post: {$post_max}, memory: {$memory}"];

if (!function_exists('finfo_open')) {
    $warnings[] = ['icon'=>'fa-puzzle-piece','msg'=>"<strong>fileinfo</strong> extension not loaded",'fix'=>"MIME detection falls back to file extensions. Ask server admin to enable it."];
}
if (!function_exists('mail')) {
    $warnings[] = ['icon'=>'fa-envelope','msg'=>"<strong>mail()</strong> function not available",'fix'=>"Contact form emails won't send. Check PHP config."];
}
if (function_exists('imagecreatefromjpeg')) {
    $checks[] = ['icon'=>'fa-compress','msg'=>"GD library loaded — auto-compression active (JPEG 92%, max 2560px, skips files under 150KB)"];
} else {
    $warnings[] = ['icon'=>'fa-compress','msg'=>'<strong>GD library</strong> not installed — image auto-compression disabled','fix'=>'Images are saved as-is. Install GD: <code>sudo apt install php-gd</code> then restart Apache.'];
}

// 5. Missing critical settings
$critical = ['contact_email'=>'Contact email','site_name'=>'Site name'];
foreach ($critical as $key => $label) {
    if (empty($settings[$key])) {
        $warnings[] = ['icon'=>'fa-gear','msg'=>"<strong>{$label}</strong> not set",'fix'=>"Go to <a href='settings.php'>Site Settings</a> to set it."];
    }
}
// GLB model
$glb = $settings['car_model_file'] ?? '';
if ($glb && !file_exists($root . '/html/assets/models/' . $glb)) {
    $warnings[] = ['icon'=>'fa-cube','msg'=>"3D model file missing: <strong>{$glb}</strong>",'fix'=>"Re-upload via <a href='model.php'>3D Model</a>."];
}

// 6. Disk usage
$totalSize = 0;
$imgDir = $root . '/html/assets/images';
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($imgDir, RecursiveDirectoryIterator::SKIP_DOTS));
foreach ($rii as $file) { if ($file->isFile()) $totalSize += $file->getSize(); }
$diskMB = round($totalSize / 1024 / 1024, 1);

// 7. Backup count
$backupDir = $root . '/data/backups';
$backupCount = is_dir($backupDir) ? count(glob($backupDir . '/*.json')) : 0;
$checks[] = ['icon'=>'fa-clock-rotate-left','msg'=>"Auto-backups: {$backupCount} backup files in data/backups/"];

// 8. Google Analytics
$gaId = $settings['google_analytics_id'] ?? '';
if ($gaId) {
    $checks[] = ['icon'=>'fa-chart-line','msg'=>"Google Analytics: <strong>" . htmlspecialchars($gaId) . "</strong> — tracking active"];
} else {
    $warnings[] = ['icon'=>'fa-chart-line','msg'=>'<strong>Google Analytics</strong> not configured','fix'=>'Go to <a href="settings.php">Site Settings</a> and enter a Measurement ID to track visitors.'];
}

// 7. PHP error log
$errorLogContent = '';
$errorLogPath = '';
$possibleLogs = [
    ini_get('error_log'),
    '/home/' . get_current_user() . '/php_data/php.log',
    $root . '/php_errors.log',
];
foreach ($possibleLogs as $lp) {
    if ($lp && file_exists($lp) && is_readable($lp)) {
        $errorLogPath = $lp;
        $lines = file($lp, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $errorLogContent = implode("\n", array_slice($lines, -15));
        break;
    }
}

$totalChecks = count($checks) + count($warnings) + count($errors);
$statusColor = count($errors) > 0 ? 'var(--danger,#e74c3c)' : (count($warnings) > 0 ? 'var(--warning,#f39c12)' : 'var(--success,#2ecc71)');
$statusIcon = count($errors) > 0 ? 'fa-circle-xmark' : (count($warnings) > 0 ? 'fa-triangle-exclamation' : 'fa-circle-check');
$statusText = count($errors) > 0 ? count($errors) . ' error(s)' : (count($warnings) > 0 ? count($warnings) . ' warning(s)' : 'All clear');
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2><i class="fa fa-stethoscope"></i> Diagnostics</h2>
        <span class="diag-status" style="color:<?= $statusColor ?>;"><i class="fa <?= $statusIcon ?>"></i> <?= $statusText ?></span>
    </div>
    <div class="admin-card-body">

        <?php if (!empty($errors)): ?>
        <div style="margin-bottom:1rem;">
            <div class="diag-group-label is-danger"><i class="fa fa-circle-xmark"></i> Errors</div>
            <?php foreach ($errors as $e): ?>
            <div class="diag-row is-danger">
                <i class="fa <?= $e['icon'] ?>"></i>
                <div><span><?= $e['msg'] ?></span><br><span class="diag-fix"><?= $e['fix'] ?></span></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($warnings)): ?>
        <div style="margin-bottom:1rem;">
            <div class="diag-group-label is-warning"><i class="fa fa-triangle-exclamation"></i> Warnings</div>
            <?php foreach ($warnings as $w): ?>
            <div class="diag-row is-warning">
                <i class="fa <?= $w['icon'] ?>"></i>
                <div><span><?= $w['msg'] ?></span><br><span class="diag-fix"><?= $w['fix'] ?></span></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Passed checks (collapsed by default) -->
        <details style="margin-bottom:1rem;">
            <summary class="diag-passed-summary">
                <i class="fa fa-circle-check"></i>
                <?= count($checks) ?> checks passed — click to expand
            </summary>
            <div style="margin-top:.5rem;">
                <?php foreach ($checks as $c): ?>
                <div class="diag-passed-row">
                    <i class="fa <?= $c['icon'] ?>"></i>
                    <?= $c['msg'] ?>
                </div>
                <?php endforeach; ?>
            </div>
        </details>

        <!-- Disk & server info -->
        <dl class="diag-server-info">
            <div><dt>PHP</dt><dd><?= phpversion() ?></dd></div>
            <div><dt>Upload Limit</dt><dd class="is-warning"><?= $upload_max ?></dd></div>
            <div><dt>Memory</dt><dd><?= $memory ?></dd></div>
            <div><dt>Images on Disk</dt><dd><?= $diskMB ?> MB</dd></div>
            <div><dt>Backups</dt><dd><?= $backupCount ?></dd></div>
            <div><dt>User</dt><dd><?= htmlspecialchars(auth_name()) ?></dd></div>
        </dl>

        <!-- PHP Error Log -->
        <?php if ($errorLogContent): ?>
        <details style="margin-top:.75rem;">
            <summary class="diag-log-summary">
                <i class="fa fa-terminal"></i>
                PHP Error Log (last 15 lines) — <?= basename($errorLogPath) ?>
            </summary>
            <pre class="diag-log-pre"><?= htmlspecialchars($errorLogContent) ?></pre>
        </details>
        <?php elseif ($errorLogPath === ''): ?>
        <div class="diag-log-empty"><i class="fa fa-terminal"></i> PHP error log not found or not readable.</div>
        <?php else: ?>
        <div class="diag-log-empty"><i class="fa fa-terminal"></i> PHP error log is empty — no errors recorded.</div>
        <?php endif; ?>

    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>
