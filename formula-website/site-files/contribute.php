<?php
$pageTitle       = 'Contribute';
$pageDescription = 'Support RIT Racing through corporate sponsorship or personal donations.';
require_once 'includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <p class="page-hero-eyebrow">Support the Team</p>
        <h1>Partner With <span style="color:var(--rit-orange);">Champions</span></h1>
        <p>RIT Racing is a non-profit organization. Your contribution — monetary or in-kind — is tax deductible and goes directly toward building our next race car.</p>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="two-col" style="align-items:stretch;">
            <div style="background:var(--rit-dark-2);border:1px solid var(--rit-dark-3);padding:3rem;display:flex;flex-direction:column;" class="reveal">
                <div class="program-icon" style="margin-bottom:1.5rem;"><i class="fa fa-building"></i></div>
                <h2 class="section-title" style="font-size:2.5rem;margin-bottom:1rem;">Corporate <span>Sponsorship</span></h2>
                <div class="divider"></div>
                <p style="color:var(--rit-gray-light);line-height:1.8;margin-bottom:1.5rem;flex:1;">
                    We accept monetary and equipment donations from companies of all sizes.
                    In return, sponsors receive branding on the car and team apparel,
                    access to our talent pipeline, and recognition across all our media channels.
                    As a non-profit organization, we can provide tax credit documentation.
                </p>
                <div style="display:flex;gap:1rem;flex-wrap:wrap;">
                    <?php
                    $packet = get_setting('sponsor_packet_file', '');
                    $packet_fs = dirname(__FILE__) . '/assets/files/' . $packet;
                    if ($packet && file_exists($packet_fs)):
                    ?>
                    <a href="<?= asset('files/' . $packet) ?>" target="_blank" class="btn btn-primary">
                        <i class="fa fa-download"></i> Download Sponsor Packet
                    </a>
                    <?php endif; ?>
                    <a href="mailto:<?= htmlspecialchars(get_contact_email()) ?>" class="btn btn-outline">
                        <i class="fa fa-envelope"></i> Contact Us
                    </a>
                </div>
            </div>
            <div style="background:var(--rit-dark-2);border:1px solid var(--rit-dark-3);padding:3rem;display:flex;flex-direction:column;" class="reveal reveal-delay-2">
                <div class="program-icon" style="margin-bottom:1.5rem;"><i class="fa fa-heart"></i></div>
                <h2 class="section-title" style="font-size:2.5rem;margin-bottom:1rem;">Friends &amp; <span>Family</span></h2>
                <div class="divider"></div>
                <p style="color:var(--rit-gray-light);line-height:1.8;margin-bottom:1.5rem;flex:1;">
                    Alumni, family members, and individual supporters are a huge part of
                    what makes RIT Racing possible. If you'd like to support the team personally,
                    reach out to us directly and we'll walk you through the options.
                </p>
                <a href="mailto:<?= htmlspecialchars(get_contact_email()) ?>" class="btn btn-ghost">
                    <i class="fa fa-envelope"></i> Get in Touch
                </a>
            </div>
        </div>
        <div class="result-banner reveal" style="margin-top:3rem;">
            <i class="fa fa-trophy"></i>
            <div>
                <strong>Your investment is competing at the highest level</strong>
                Sponsor logos appear on our car, which competed at FSAE Michigan in front of hundreds of engineers and industry representatives.
            </div>
        </div>
    </div>
</section>
<section class="section" style="background:var(--rit-dark-2);">
    <div class="container">
        <div class="reveal text-center" style="margin-bottom:3rem;">
            <p class="section-label" style="justify-content:center;">What Your Support Funds</p>
            <h2 class="section-title">Where the Money <span>Goes</span></h2>
        </div>
        <div class="stats-row reveal">
            <div class="stat-block"><h3>Parts</h3><p>Raw materials, fasteners, composites, bearings</p></div>
            <div class="stat-block"><h3>Travel</h3><p>Transportation to FSAE Michigan and other competitions</p></div>
            <div class="stat-block"><h3>Software</h3><p>CAD, simulation, and analysis tool licenses</p></div>
            <div class="stat-block"><h3>Testing</h3><p>Track time, tire sets, and validation equipment</p></div>
        </div>
    </div>
</section>
<section class="cta-section">
    <div class="container">
        <h2>Ready to Sponsor?</h2>
        <p>Download our sponsorship packet or reach out directly.</p>
        <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
            <?php if ($packet && file_exists($packet_fs)): ?>
            <a href="<?= asset('files/' . $packet) ?>" target="_blank" class="btn-dark">
                <i class="fa fa-download"></i> Sponsor Packet PDF
            </a>
            <?php endif; ?>
            <a href="mailto:<?= htmlspecialchars(get_contact_email()) ?>" class="btn btn-outline" style="color:#000;border-color:rgba(0,0,0,0.3);">
                <i class="fa fa-envelope"></i> Email Us
            </a>
        </div>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
