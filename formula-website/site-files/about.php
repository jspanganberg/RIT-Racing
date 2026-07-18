<?php
// All content editable from Admin → About Page
$pageTitle       = 'About';
$pageDescription = 'The story of RIT Racing — Formula SAE engineering at Rochester Institute of Technology.';
require_once 'includes/header.php';

$a = data_load('about.json');
if (!is_array($a)) $a = [];

// Helper: render paragraphs from text with double newlines
function render_paragraphs(string $text): void {
    foreach (explode("\n\n", $text) as $para) {
        $para = trim($para);
        if ($para) echo '<p>' . htmlspecialchars($para) . '</p>';
    }
}
?>

<section class="page-hero">
    <div class="container">
        <p class="page-hero-eyebrow"><?= htmlspecialchars($a['hero_eyebrow'] ?? 'Est. 1991') ?></p>
        <h1><?= htmlspecialchars($a['hero_title_1'] ?? 'Our') ?> <span style="color:var(--rit-orange);"><?= htmlspecialchars($a['hero_title_2'] ?? 'Story') ?></span></h1>
        <p><?= htmlspecialchars($a['hero_subtitle'] ?? '') ?></p>
    </div>
</section>

<!-- Section 1: History -->
<section class="section">
    <div class="container">
        <div class="two-col">
            <div class="content-block reveal">
                <p class="section-label"><?= htmlspecialchars($a['section1_label'] ?? 'The Beginning') ?></p>
                <h2 class="section-title"><?= htmlspecialchars($a['section1_title_1'] ?? 'Born in the') ?> <span><?= htmlspecialchars($a['section1_title_2'] ?? 'Shop') ?></span></h2>
                <div class="divider"></div>
                <?php render_paragraphs($a['section1_text'] ?? ''); ?>
            </div>
            <div class="reveal reveal-delay-2">
                <?php $heritage_img = site_image('about_heritage'); ?>
                <?php if ($heritage_img): ?>
                <div class="img-wrapper" style="height:400px;">
                    <img src="<?= $heritage_img ?>" alt="RIT Racing heritage" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <?php else: ?>
                <div class="img-placeholder" style="height:400px;">
                    <i class="fa fa-car"></i>
                    <span>Heritage Photo — upload via Admin → Settings → Images</span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Stats Bar -->
<section class="section" style="background:var(--rit-dark-2);">
    <div class="container">
        <div class="stats-row reveal">
            <?php foreach ($a['stats'] ?? [] as $st): ?>
            <div class="stat-block"><h3><?= htmlspecialchars($st['number'] ?? '') ?></h3><p><?= htmlspecialchars($st['label'] ?? '') ?></p></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Section 2: What We Do -->
<section class="section">
    <div class="container">
        <div class="two-col flip">
            <div class="reveal reveal-delay-2">
                <?php $shop_img = site_image('about_shop'); ?>
                <?php if ($shop_img): ?>
                <div class="img-wrapper" style="height:400px;">
                    <img src="<?= $shop_img ?>" alt="RIT Racing shop" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <?php else: ?>
                <div class="img-placeholder" style="height:400px;">
                    <i class="fa fa-tools"></i><span>Shop Photo — upload via Admin → Settings → Images</span>
                </div>
                <?php endif; ?>
            </div>
            <div class="content-block reveal">
                <p class="section-label"><?= htmlspecialchars($a['section2_label'] ?? 'What We Do') ?></p>
                <h2 class="section-title"><?= htmlspecialchars($a['section2_title_1'] ?? 'Design. Build.') ?> <span><?= htmlspecialchars($a['section2_title_2'] ?? 'Race.') ?></span></h2>
                <div class="divider"></div>
                <?php render_paragraphs($a['section2_text'] ?? ''); ?>
                <?php if (!empty($a['section2_quote'])): ?>
                <div class="pullquote">
                    "<?= htmlspecialchars($a['section2_quote']) ?>"
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Milestones -->
<?php if (!empty($a['milestones'])): ?>
<section class="section" style="background:var(--rit-dark-2);">
    <div class="container">
        <div class="reveal text-center" style="margin-bottom:3rem;">
            <p class="section-label" style="justify-content:center;">Milestones</p>
            <h2 class="section-title">A History of <span>Excellence</span></h2>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1px;background:var(--rit-dark-3);border:1px solid var(--rit-dark-3);" class="reveal">
            <?php foreach ($a['milestones'] as $ms): ?>
            <div style="background:var(--rit-dark-2);padding:2rem;">
                <p style="font-family:var(--font-display);font-size:3rem;color:var(--rit-orange);line-height:1;margin-bottom:0.5rem;"><?= htmlspecialchars($ms['year'] ?? '') ?></p>
                <h3 style="font-family:var(--font-label);font-size:1rem;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;margin-bottom:0.75rem;"><?= htmlspecialchars($ms['title'] ?? '') ?></h3>
                <p style="font-size:0.875rem;color:var(--rit-gray-light);line-height:1.7;"><?= htmlspecialchars($ms['description'] ?? '') ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Section 3: Electric Era -->
<section class="section">
    <div class="container">
        <div class="two-col">
            <div class="content-block reveal">
                <p class="section-label"><?= htmlspecialchars($a['section3_label'] ?? 'The Electric Era') ?></p>
                <h2 class="section-title"><?= htmlspecialchars($a['section3_title_1'] ?? 'All In on') ?> <span><?= htmlspecialchars($a['section3_title_2'] ?? 'Electric') ?></span></h2>
                <div class="divider"></div>
                <?php render_paragraphs($a['section3_text'] ?? ''); ?>
                <a href="<?= url('vehicles/electric.php') ?>" class="btn btn-primary" style="margin-top:1rem;">
                    See Our Electric Cars <i class="fa fa-bolt"></i>
                </a>
            </div>
            <div class="reveal reveal-delay-2">
                <?php $comp_img = site_image('about_competition'); ?>
                <?php if ($comp_img): ?>
                <div class="img-wrapper" style="height:360px;">
                    <img src="<?= $comp_img ?>" alt="RIT Racing at competition" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <?php else: ?>
                <div class="img-placeholder" style="height:360px;">
                    <i class="fa fa-bolt"></i><span>Competition Photo — upload via Admin → Settings → Images</span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2>Be Part of the Next Chapter</h2>
        <p>Join us, support us, or partner with us.</p>
        <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
            <a href="<?= url('join.php') ?>" class="btn-dark">Join the Team</a>
            <a href="<?= url('contribute.php') ?>" class="btn btn-outline" style="color:#000;border-color:rgba(0,0,0,0.3);">Sponsor Us</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
