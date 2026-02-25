<?php
// includes/header.php
// Usage: include 'includes/header.php';
// Requires: $pageTitle, $pageDescription (set before including)
require_once __DIR__ . '/config.php';

$pageTitle       = $pageTitle       ?? SITE_NAME;
$pageDescription = $pageDescription ?? 'RIT Racing — Formula SAE team at the Rochester Institute of Technology. Designing, building, and racing the finest cars in FSAE since 1991.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?> — <?= SITE_NAME ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta property="og:image" content="<?= asset('images/hero/og-image.jpg') ?>">
    <title><?= htmlspecialchars($pageTitle) ?> — <?= SITE_NAME ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700&family=Barlow+Condensed:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">

    <!-- Favicon -->
    <!-- <link rel="icon" href="<?= asset('images/logos/favicon.ico') ?>"> -->
</head>
<body>

<!-- ======================================================
     NAVIGATION
     ====================================================== -->
<header class="site-header" id="site-header">
    <nav class="nav-container">

        <!-- Logo -->
        <a href="<?= url('index.php') ?>" class="nav-logo">
            <!-- Replace img src with real logo when available -->
            <div class="nav-logo-placeholder">
                <span class="logo-rit">RIT</span>
                <span class="logo-racing">RACING</span>
            </div>
        </a>

        <!-- Desktop Nav Links -->
        <ul class="nav-links" id="nav-links">
            <li><a href="<?= url('index.php') ?>" class="<?= active('index') ?>">Home</a></li>
            <li><a href="<?= url('about.php') ?>" class="<?= active('about') ?>">About</a></li>

            <!-- Dropdown: Vehicles -->
            <li class="has-dropdown">
                <a href="#" class="dropdown-toggle">
                    Vehicles <i class="fa fa-chevron-down"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="<?= url('vehicles/electric.php') ?>">⚡ Electric</a></li>
                    <li><a href="<?= url('vehicles/combustion.php') ?>">🔥 Combustion History</a></li>
                </ul>
            </li>

            <li><a href="<?= url('programs.php') ?>" class="<?= active('programs') ?>">Programs</a></li>
            <li><a href="<?= url('team.php') ?>" class="<?= active('team') ?>">Team</a></li>
            <li><a href="<?= url('sponsors.php') ?>" class="<?= active('sponsors') ?>">Sponsors</a></li>

            <!-- Dropdown: Get Involved -->
            <li class="has-dropdown">
                <a href="#" class="dropdown-toggle">
                    Get Involved <i class="fa fa-chevron-down"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="<?= url('join.php') ?>">Join the Team</a></li>
                    <li><a href="<?= url('contribute.php') ?>">Donate / Sponsor</a></li>
                    <li><a href="<?= url('contact.php') ?>">Contact Us</a></li>
                </ul>
            </li>
        </ul>

        <!-- CTA Button -->
        <a href="<?= url('join.php') ?>" class="nav-cta">Join Us</a>

        <!-- Mobile Hamburger -->
        <button class="hamburger" id="hamburger" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>

    </nav>
</header>

<!-- Page content begins below -->
<main class="page-main">
