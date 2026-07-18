<?php
// includes/header.php
// NOTE: Maintenance mode was removed in v39.
// This file no longer checks maintenance_mode from settings.json.
// The 503 holding page and admin banner have been deleted.
require_once __DIR__ . '/config.php';


$pageTitle       = $pageTitle       ?? SITE_NAME;
$pageDescription = $pageDescription ?? 'RIT Racing — Formula SAE team at Rochester Institute of Technology. Designing, building, and racing open-wheel electric race cars since 1991.';
$currentPage  = basename($_SERVER['SCRIPT_NAME'], '.php');
$canonicalUrl = SITE_URL . ($currentPage === 'index' ? '/' : '/' . $currentPage);
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — <?= SITE_NAME ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">

    <!-- Canonical -->
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= SITE_NAME ?>">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?> — <?= SITE_NAME ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl) ?>">
    <meta property="og:image" content="<?= site_image('og_image') ?: asset('images/site/og-image.jpg') ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle) ?> — <?= SITE_NAME ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta name="twitter:image" content="<?= site_image('og_image') ?: asset('images/site/og-image.jpg') ?>">

    <!-- Fonts: 2 families only (Barlow Condensed + Outfit) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Barlow+Condensed:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Icons (deferred) -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"></noscript>

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>?v=<?= @filemtime(dirname(dirname(__FILE__)) . '/assets/css/style.css') ?: time() ?>">

    <!-- Favicon -->
    <?php $fav = site_image('favicon'); ?>
    <link rel="icon" href="<?= $fav ?: asset('images/logos/favicon.svg') ?>"<?= $fav ? '' : ' type="image/svg+xml"' ?>>

    <!-- Anti-flash: apply saved theme before render -->
    <script>(function(){var t=localStorage.getItem("rit-theme");if(t)document.documentElement.setAttribute("data-theme",t)})()</script>

    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SportsTeam",
        "name": "RIT Racing",
        "alternateName": "RIT Formula SAE",
        "url": "<?= SITE_URL ?>",
        "logo": "<?= asset('images/logos/favicon.svg') ?>",
        "description": "Rochester Institute of Technology's Formula SAE team — designing, building, and racing open-wheel electric race cars since 1991.",
        "foundingDate": "1991",
        "parentOrganization": {
            "@type": "CollegeOrUniversity",
            "name": "Rochester Institute of Technology",
            "department": "Kate Gleason College of Engineering"
        },
        "sport": "Formula SAE",
        "email": "<?= htmlspecialchars(get_setting('contact_email', 'formula@rit.edu')) ?>",
        "location": {
            "@type": "Place",
            "name": "KGCOE Building 9",
            "address": {
                "@type": "PostalAddress",
                "addressLocality": "Rochester",
                "addressRegion": "NY",
                "postalCode": "14623",
                "addressCountry": "US"
            }
        }
    }
    </script>
<?php
// Google Analytics — set the Measurement ID in Admin → Site Settings
$ga_id = get_setting('google_analytics_id', '');
if ($ga_id): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($ga_id) ?>"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?= htmlspecialchars($ga_id) ?>');</script>
<?php endif; ?>
</head>
<body>


<!-- Skip Link (accessibility) -->
<a href="#main-content" class="skip-link">Skip to main content</a>

<!-- ======================================================
     NAVIGATION
     ====================================================== -->
<header class="site-header" id="site-header" role="banner">
    <nav class="nav-container" aria-label="Main navigation">

        <!-- Logo (dark/light versions swap based on theme) -->
        <?php
        $logo_dark  = site_image('nav_logo_dark')  ?: asset('images/site/nav-logo-dark.png');
        $logo_light = site_image('nav_logo_light') ?: asset('images/site/nav-logo-light.png');
        ?>
        <a href="<?= url('index.php') ?>" class="nav-logo" aria-label="RIT Racing — Home">
            <img class="nav-logo-img nav-logo-dark" src="<?= $logo_dark ?>" alt="RIT Racing" width="180" height="54">
            <img class="nav-logo-img nav-logo-light" src="<?= $logo_light ?>" alt="RIT Racing" width="180" height="54">
        </a>

        <!--
            NAVIGATION MENU — REQUIRES CODE CHANGES TO MODIFY
            
            To add a page: Add a new <li> with url() and active() helpers.
            To add a dropdown: Use the .has-dropdown pattern (see Vehicles example).
            The mobile hamburger menu reads these same items — no separate config.
            
            See DEVELOPER_GUIDE.md Section 3 for full details.
        -->
        <ul class="nav-links" id="nav-links" role="menubar">
            <li role="none"><a href="<?= url('index.php') ?>" class="<?= active('index') ?>" role="menuitem">Home</a></li>
            <li role="none"><a href="<?= url('about.php') ?>" class="<?= active('about') ?>" role="menuitem">About</a></li>

            <!-- Dropdown: Vehicles -->
            <li class="has-dropdown" role="none">
                <a href="#" class="dropdown-toggle" role="menuitem" aria-haspopup="true" aria-expanded="false">
                    Vehicles <i class="fa fa-chevron-down" aria-hidden="true"></i>
                </a>
                <ul class="dropdown-menu" role="menu">
                    <li role="none"><a href="<?= url('vehicles/electric.php') ?>" role="menuitem">⚡ Electric</a></li>
                    <li role="none"><a href="<?= url('vehicles/combustion.php') ?>" role="menuitem">🔥 Combustion</a></li>
                </ul>
            </li>

            <li role="none"><a href="<?= url('programs.php') ?>" class="<?= active('programs') ?>" role="menuitem">Programs</a></li>
            <li role="none"><a href="<?= url('team.php') ?>" class="<?= active('team') ?>" role="menuitem">Team</a></li>
            <li role="none"><a href="<?= url('sponsors.php') ?>" class="<?= active('sponsors') ?>" role="menuitem">Sponsors</a></li>

            <!-- Dropdown: Get Involved -->
            <li class="has-dropdown" role="none">
                <a href="#" class="dropdown-toggle" role="menuitem" aria-haspopup="true" aria-expanded="false">
                    Get Involved <i class="fa fa-chevron-down" aria-hidden="true"></i>
                </a>
                <ul class="dropdown-menu" role="menu">
                    <li role="none"><a href="<?= url('join.php') ?>" role="menuitem">Join the Team</a></li>
                    <li role="none"><a href="<?= url('contribute.php') ?>" role="menuitem">Donate / Sponsor</a></li>
                    <li role="none"><a href="<?= url('contact.php') ?>" role="menuitem">Contact Us</a></li>
                </ul>
            </li>
        </ul>

        <!-- Theme Toggle -->
        <button class="theme-switch" id="theme-switch" aria-label="Toggle light/dark mode" type="button">
            <span class="theme-switch-knob">
                <svg class="theme-icon-sun" viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="5"/><g stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></g></svg>
                <svg class="theme-icon-moon" viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
            </span>
        </button>
        <script>
        (function(){
            var btn=document.getElementById('theme-switch');
            if(!btn)return;
            var html=document.documentElement;
            btn.addEventListener('click',function(e){
                e.preventDefault();
                var next=(html.getAttribute('data-theme')||'dark')==='dark'?'light':'dark';
                html.classList.add('theme-transitioning');
                html.setAttribute('data-theme',next);
                try{localStorage.setItem('rit-theme',next)}catch(ex){}
                setTimeout(function(){html.classList.remove('theme-transitioning')},350);
            });
        })();
        </script>

        <a href="<?= url('join.php') ?>" class="nav-cta">Join Us</a>

        <!-- Mobile Hamburger -->
        <button class="hamburger" id="hamburger" aria-label="Toggle navigation menu" aria-expanded="false" type="button">
            <span></span><span></span><span></span>
        </button>

    </nav>
</header>

<!-- Page content begins below -->
<main class="page-main" id="main-content">
