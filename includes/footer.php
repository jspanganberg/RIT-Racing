<?php
// includes/footer.php
require_once __DIR__ . '/config.php';
?>

</main><!-- /.page-main -->

<!-- ======================================================
     FOOTER
     ====================================================== -->
<footer class="site-footer">
    <div class="footer-inner">

        <!-- Branding -->
        <div class="footer-brand">
            <div class="footer-logo">
                <span class="logo-rit">RIT</span>
                <span class="logo-racing">RACING</span>
            </div>
            <p class="footer-tagline"><?= SITE_TAGLINE ?></p>
            <p class="footer-sub">Rochester Institute of Technology</p>
            <p class="footer-address"><?= MEETING_LOCATION ?></p>
            <a href="mailto:<?= CONTACT_EMAIL ?>" class="footer-email">
                <i class="fa fa-envelope"></i> <?= CONTACT_EMAIL ?>
            </a>
        </div>

        <!-- Quick Links -->
        <div class="footer-col">
            <h4 class="footer-heading">Quick Links</h4>
            <ul>
                <li><a href="<?= url('index.php') ?>">Home</a></li>
                <li><a href="<?= url('about.php') ?>">About</a></li>
                <li><a href="<?= url('vehicles/electric.php') ?>">Electric Cars</a></li>
                <li><a href="<?= url('vehicles/combustion.php') ?>">Combustion History</a></li>
                <li><a href="<?= url('programs.php') ?>">Programs</a></li>
            </ul>
        </div>

        <!-- Team & Involvement -->
        <div class="footer-col">
            <h4 class="footer-heading">Get Involved</h4>
            <ul>
                <li><a href="<?= url('team.php') ?>">Meet the Team</a></li>
                <li><a href="<?= url('join.php') ?>">Join Us</a></li>
                <li><a href="<?= url('sponsors.php') ?>">Our Sponsors</a></li>
                <li><a href="<?= url('contribute.php') ?>">Donate / Sponsor</a></li>
                <li><a href="<?= url('contact.php') ?>">Contact</a></li>
                <li><a href="<?= url('in-memoriam.php') ?>">In Memoriam</a></li>
            </ul>
        </div>

        <!-- Social & Latest -->
        <div class="footer-col">
            <h4 class="footer-heading">Follow Us</h4>
            <div class="footer-social">
                <?php if (SOCIAL_INSTAGRAM): ?>
                <a href="<?= SOCIAL_INSTAGRAM ?>" target="_blank" rel="noopener" aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <?php endif; ?>
                <?php if (SOCIAL_FACEBOOK): ?>
                <a href="<?= SOCIAL_FACEBOOK ?>" target="_blank" rel="noopener" aria-label="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <?php endif; ?>
                <?php if (SOCIAL_TWITTER): ?>
                <a href="<?= SOCIAL_TWITTER ?>" target="_blank" rel="noopener" aria-label="Twitter/X">
                    <i class="fab fa-x-twitter"></i>
                </a>
                <?php endif; ?>
                <?php if (SOCIAL_YOUTUBE): ?>
                <a href="<?= SOCIAL_YOUTUBE ?>" target="_blank" rel="noopener" aria-label="YouTube">
                    <i class="fab fa-youtube"></i>
                </a>
                <?php endif; ?>
            </div>
            <div class="footer-latest">
                <p class="footer-result-label">Latest Result</p>
                <p class="footer-result"><?= LATEST_RESULT ?></p>
            </div>
        </div>

    </div>

    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
        <p>Kate Gleason College of Engineering — Rochester Institute of Technology</p>
    </div>
</footer>

<!-- ======================================================
     SCRIPTS
     ====================================================== -->
<script src="<?= asset('js/main.js') ?>"></script>

</body>
</html>
