<?php
// includes/footer.php — all content editable from Admin → Site Settings
require_once __DIR__ . '/config.php';
?>

</main>

<footer class="site-footer" role="contentinfo">
    <div class="footer-inner">
        <div class="footer-brand">
            <div class="footer-logo"><span class="logo-rit">RIT</span> <span class="logo-racing">RACING</span></div>
            <p class="footer-tagline"><?= htmlspecialchars(get_setting('footer_tagline', SITE_TAGLINE)) ?></p>
            <p class="footer-sub"><?= htmlspecialchars(get_setting('footer_subtitle', 'Rochester Institute of Technology')) ?></p>
            <p class="footer-address"><?= htmlspecialchars(get_meeting_location()) ?></p>
            <a href="mailto:<?= htmlspecialchars(get_contact_email()) ?>" class="footer-email"><i class="fa fa-envelope"></i> <?= htmlspecialchars(get_contact_email()) ?></a>
        </div>
        <nav class="footer-col">
            <h4 class="footer-heading"><?= htmlspecialchars(get_setting('footer_links_1_title', 'Quick Links')) ?></h4>
            <ul><?php foreach (get_setting('footer_links_1', []) as $lk): ?>
                <li><a href="<?= url($lk['page'] ?? '') ?>"><?= htmlspecialchars($lk['label'] ?? '') ?></a></li>
            <?php endforeach; ?></ul>
        </nav>
        <nav class="footer-col">
            <h4 class="footer-heading"><?= htmlspecialchars(get_setting('footer_links_2_title', 'Get Involved')) ?></h4>
            <ul><?php foreach (get_setting('footer_links_2', []) as $lk): ?>
                <li><a href="<?= url($lk['page'] ?? '') ?>"><?= htmlspecialchars($lk['label'] ?? '') ?></a></li>
            <?php endforeach; ?></ul>
        </nav>
        <div class="footer-col">
            <h4 class="footer-heading">Follow Us</h4>
            <div class="footer-social">
                <?php foreach (['instagram'=>'fa-instagram','facebook'=>'fa-facebook-f','twitter'=>'fa-x-twitter','youtube'=>'fa-youtube','linkedin'=>'fa-linkedin-in'] as $key => $icon):
                    $link = get_setting("social_{$key}", '');
                    if ($link): ?>
                <a href="<?= htmlspecialchars($link) ?>" target="_blank" rel="noopener" aria-label="<?= ucfirst($key) ?>"><i class="fab <?= $icon ?>"></i></a>
                <?php endif; endforeach; ?>
            </div>
            <div class="footer-latest">
                <p class="footer-result-label">Latest Result</p>
                <p class="footer-result"><?= htmlspecialchars(get_setting('latest_result', '')) ?></p>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> <?= htmlspecialchars(get_setting('footer_copyright', SITE_NAME . '. All rights reserved.')) ?></p>
        <p><?= htmlspecialchars(get_setting('footer_tagline', SITE_TAGLINE)) ?> — <?= htmlspecialchars(get_setting('footer_subtitle', 'Rochester Institute of Technology')) ?></p>
    </div>
</footer>
<script src="<?= asset('js/main.js') ?>?v=<?= @filemtime(dirname(dirname(__FILE__)) . '/assets/js/main.js') ?: time() ?>" defer></script>
</body>
</html>
