<?php
$pageTitle       = 'Our Sponsors';
$pageDescription = 'The companies and organizations that make RIT Racing possible.';
require_once 'includes/header.php';

$all_sponsors = data_load('sponsors.json');
if (!is_array($all_sponsors)) $all_sponsors = [];
usort($all_sponsors, fn($a,$b) => ($a['order']??99) <=> ($b['order']??99));
$all_sponsors = array_filter($all_sponsors, fn($s) => ($s['active'] ?? true));

$featured = array_filter($all_sponsors, fn($s) => !empty($s['featured']));
$regular  = array_filter($all_sponsors, fn($s) => empty($s['featured']));
?>

<section class="page-hero">
    <div class="container">
        <p class="page-hero-eyebrow">Our Partners</p>
        <h1>Our <span style="color:var(--rit-orange);">Sponsors</span></h1>
        <p>RIT Racing is made possible by the generous support of industry partners
           who share our passion for engineering excellence.</p>
        <div style="margin-top:2rem;display:flex;gap:1rem;flex-wrap:wrap;">
            <a href="<?= url('contribute.php') ?>" class="btn btn-primary">Become a Sponsor</a>
            <?php
            $packet = get_setting('sponsor_packet_file', '');
            $packet_path_check = dirname(__FILE__) . '/assets/files/' . $packet;
            if ($packet && file_exists($packet_path_check)):
            ?>
            <a href="<?= asset('files/' . $packet) ?>" target="_blank" class="btn btn-outline">
                <i class="fa fa-download"></i> Sponsor Packet (PDF)
            </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if (!empty($featured)): ?>
<section class="section" style="padding-bottom:0;">
    <div class="container">
        <?php foreach ($featured as $f):
            $name  = htmlspecialchars($f['name'] ?? '');
            $title = htmlspecialchars($f['title'] ?? '');
            $desc  = htmlspecialchars($f['description'] ?? '');
            $url   = $f['url'] ?? '';
            $logo  = $f['logo'] ?? '';
        ?>
        <div class="featured-sponsor reveal">
            <div class="featured-sponsor-photo">
                <?php if ($logo && file_exists(__DIR__ . "/assets/images/sponsors/{$logo}")): ?>
                <img src="<?= asset("images/sponsors/{$logo}") ?>" alt="<?= $name ?>" loading="lazy">
                <?php else: ?>
                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#1a1a1a;border-radius:50%;">
                    <i class="fa fa-user" style="font-size:3rem;color:var(--rit-orange);opacity:.5;"></i>
                </div>
                <?php endif; ?>
            </div>
            <div class="featured-sponsor-info">
                <div style="display:inline-flex;align-items:center;gap:.5rem;padding:.3rem .8rem;background:rgba(247,105,2,.15);border:1px solid rgba(247,105,2,.3);margin-bottom:1rem;">
                    <i class="fa fa-star" style="color:var(--rit-orange);font-size:.65rem;"></i>
                    <span style="font-family:var(--font-label);font-size:.7rem;letter-spacing:.12em;text-transform:uppercase;color:var(--rit-orange);font-weight:700;">Champion of RIT Racing</span>
                </div>
                <h2 class="section-title" style="font-size:2.5rem;margin-bottom:.5rem;"><?= $name ?></h2>
                <?php if ($title): ?>
                <p style="color:var(--rit-orange);font-family:var(--font-label);font-size:.85rem;letter-spacing:.1em;text-transform:uppercase;margin-bottom:1.25rem;font-weight:600;"><?= $title ?></p>
                <?php endif; ?>
                <?php if ($desc): ?>
                <p style="font-size:1.1rem;line-height:1.8;color:var(--rit-gray);max-width:600px;"><?= $desc ?></p>
                <?php endif; ?>
                <?php if ($url): ?>
                <a href="<?= htmlspecialchars($url) ?>" target="_blank" rel="noopener" class="btn btn-outline" style="margin-top:1.5rem;">
                    <i class="fa fa-external-link"></i> Learn More
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="section">
    <div class="container">
        <p class="section-label" style="justify-content:center;">Industry Partners</p>
        <h2 class="section-title" style="text-align:center;margin-bottom:3rem;">Our <span>Sponsors</span></h2>
        <div class="sponsor-grid sponsor-grid-responsive">
            <?php foreach ($regular as $s):
                $name = htmlspecialchars($s['name'] ?? '');
                $url  = $s['url'] ?? '';
                $logo = $s['logo'] ?? '';
                $tag  = $url ? 'a' : 'div';
                $href = $url ? " href=\"{$url}\" target=\"_blank\" rel=\"noopener\"" : '';
            ?>
            <<?= $tag ?><?= $href ?> class="sponsor-logo" title="<?= $name ?>">
                <?php if ($logo && file_exists(__DIR__ . "/assets/images/sponsors/{$logo}")): ?>
                    <img src="<?= asset("images/sponsors/{$logo}") ?>" alt="<?= $name ?>" loading="lazy">
                <?php else: ?>
                    <span style="font-family:var(--font-label);font-size:0.75rem;letter-spacing:0.06em;text-transform:uppercase;color:#555;text-align:center;">
                        <?= $name ?>
                    </span>
                <?php endif; ?>
            </<?= $tag ?>>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2>Partner With Champions</h2>
        <p>Support the team that won FSAE Michigan 2024. We are a non-profit organization.</p>
        <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
            <a href="<?= url('contribute.php') ?>" class="btn-dark">Become a Sponsor</a>
            <a href="mailto:<?= htmlspecialchars(get_contact_email()) ?>" class="btn btn-outline" style="color:#000;border-color:rgba(0,0,0,0.3);">
                <i class="fa fa-envelope"></i> Contact Us
            </a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
