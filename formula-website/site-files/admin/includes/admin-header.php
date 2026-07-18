<?php
// admin/includes/admin-header.php
// Renders the full admin shell: HTML head, sidebar nav, topbar, flash messages.
// NOTE: Maintenance mode banner was removed in v39.
//   This file no longer reads maintenance_mode from settings.json.
//   Do not re-add any maintenance checks here.
$current_admin_page = basename($_SERVER['PHP_SELF'], '.php');
$upload_limit = ini_get('upload_max_filesize') ?: '2M';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $adminTitle ?? 'Admin' ?> — RIT Racing Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Barlow+Condensed:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/png" href="/assets/images/site/favicon.png">
    <link rel="stylesheet" href="/assets/css/admin.css?v=<?= @filemtime(dirname(dirname(dirname(__FILE__))) . '/assets/css/admin.css') ?: time() ?>">
</head>
<body class="admin-shell">

<aside class="sidebar">
    <div class="sidebar-brand">
        <a href="index.php" style="color:inherit;">RIT <span>Racing</span></a>
        <small>Admin Panel</small>
    </div>
    <nav class="sidebar-nav">
        <div class="sidebar-label">Content</div>
        <a href="index.php" class="<?= $current_admin_page==='index'?'active':'' ?>"><i class="fa fa-gauge"></i> Dashboard</a>
        <a href="homepage.php" class="<?= $current_admin_page==='homepage'?'active':'' ?>"><i class="fa fa-house"></i> Homepage</a>
        <a href="about.php" class="<?= $current_admin_page==='about'?'active':'' ?>"><i class="fa fa-book-open"></i> About Page</a>
        <a href="team.php" class="<?= $current_admin_page==='team'?'active':'' ?>"><i class="fa fa-users"></i> Team</a>
        <a href="sponsors.php" class="<?= $current_admin_page==='sponsors'?'active':'' ?>"><i class="fa fa-handshake"></i> Sponsors</a>
        <a href="vehicles.php" class="<?= $current_admin_page==='vehicles'?'active':'' ?>"><i class="fa fa-car"></i> Vehicles</a>
        <a href="programs.php" class="<?= $current_admin_page==='programs'?'active':'' ?>"><i class="fa fa-cubes"></i> Programs</a>
        <a href="memorial.php" class="<?= $current_admin_page==='memorial'?'active':'' ?>"><i class="fa fa-dove"></i> Memorial</a>
        <div class="sidebar-label">Assets</div>
        <a href="media.php" class="<?= $current_admin_page==='media'?'active':'' ?>"><i class="fa fa-images"></i> Media</a>
        <a href="model.php" class="<?= $current_admin_page==='model'?'active':'' ?>"><i class="fa fa-cube"></i> 3D Model</a>
        <div class="sidebar-label">Settings</div>
        <a href="settings.php" class="<?= $current_admin_page==='settings'?'active':'' ?>"><i class="fa fa-sliders"></i> Site Settings</a>
        <?php if(auth_role()==='admin'): ?>
        <a href="users.php" class="<?= $current_admin_page==='users'?'active':'' ?>"><i class="fa fa-user-shield"></i> Users</a>
        <?php endif; ?>
        <div class="sidebar-label">Site</div>
        <a href="../index.php" target="_blank"><i class="fa fa-arrow-up-right-from-square"></i> View Site</a>
    </nav>
    <div class="sidebar-foot">
        <div>
            <span class="user-name"><?= htmlspecialchars(auth_name()) ?></span>
            <span class="user-role"><?= htmlspecialchars(auth_role()) ?></span>
        </div>
        <a href="logout.php" class="btn-logout"><i class="fa fa-right-from-bracket"></i> Sign Out</a>
    </div>
</aside>

<main class="main">
    <div class="topbar">
        <h1><?= $adminTitle ?? 'Dashboard' ?><?php if(isset($adminTitleSpan)): ?> <span><?= $adminTitleSpan ?></span><?php endif; ?></h1>
        <div class="topbar-actions"><?php if(isset($topbarActions)) echo $topbarActions; ?></div>
    </div>
    <div class="content">

    <?php
    // Show flash messages if any
    if (function_exists('flash_pull')) {
        $flashes = flash_pull();
        if (!empty($flashes)) {
            foreach ($flashes as $f) {
                $type = ($f['type'] === 'error') ? 'danger' : htmlspecialchars($f['type']);
                $icon = match($type) { 'success'=>'check-circle', 'danger'=>'exclamation-circle', default=>'info-circle' };
                echo "<div class=\"alert alert-{$type}\"><i class=\"fa fa-{$icon}\"></i> " . htmlspecialchars($f['message']) . "</div>";
            }
        }
    }
    ?>
