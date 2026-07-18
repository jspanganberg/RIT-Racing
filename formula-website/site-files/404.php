<?php
http_response_code(404);
$pageTitle       = '404 — Page Not Found';
$pageDescription = 'Page not found — RIT Racing';
require_once 'includes/header.php';
?>

<section class="not-found-section">
    <div class="not-found-inner">
        <p class="not-found-num">404</p>
        <h1 class="not-found-title">Page Not <span>Found</span></h1>
        <p class="not-found-msg">
            Looks like you took a wrong turn. The page you're looking for doesn't exist
            or may have been moved.
        </p>
        <div class="not-found-actions">
            <a href="<?= url('index.php') ?>" class="btn btn-primary">
                <i class="fa fa-home"></i> Back to Home
            </a>
            <a href="<?= url('contact.php') ?>" class="btn btn-outline">
                Contact Us
            </a>
        </div>

        <div class="not-found-links">
            <a href="<?= url('vehicles/electric.php') ?>">Electric Cars</a>
            <a href="<?= url('team.php') ?>">Meet the Team</a>
            <a href="<?= url('join.php') ?>">Join Us</a>
            <a href="<?= url('sponsors.php') ?>">Sponsors</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
