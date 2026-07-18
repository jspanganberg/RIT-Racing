<?php
$pageTitle       = 'In Memoriam';
$pageDescription = 'Remembering those who shaped RIT Racing.';
require_once 'includes/header.php';

$memorials = data_load('memorial.json');
usort($memorials, fn($a,$b) => ($a['order']??1) <=> ($b['order']??1));
$memorials = array_filter($memorials, fn($m) => ($m['active'] ?? true));
?>

<section class="page-hero">
    <div class="container">
        <p class="page-hero-eyebrow">In Memoriam</p>
        <h1>Remembering <span style="color:var(--rit-orange);">Our Own</span></h1>
        <p>Honoring the people who shaped RIT Racing and the lives of everyone they touched.</p>
    </div>
</section>

<?php if (empty($memorials)): ?>
<section class="section">
    <div class="container" style="max-width:860px;text-align:center;padding:4rem 0;">
        <p style="color:var(--rit-gray);">No memorial entries at this time.</p>
    </div>
</section>
<?php endif; ?>

<?php foreach ($memorials as $i => $m):
    $name    = htmlspecialchars($m['name'] ?? '');
    $title   = htmlspecialchars($m['title'] ?? '');
    $tagline = htmlspecialchars($m['tagline'] ?? '');
    $photo   = $m['photo'] ?? '';
    $content = $m['content'] ?? '';
    // Split first name for accent coloring
    $parts = explode(' ', $name, 2);
    $first = $parts[0];
    $last  = $parts[1] ?? '';
?>
<section class="section" <?= $i > 0 ? 'style="padding-top:0;"' : '' ?>>
    <div class="container" style="max-width:860px;">
        <div class="two-col reveal" style="margin-bottom:3rem;">
            <div>
                <?php if ($photo && file_exists(__DIR__ . '/assets/images/memorial/' . $photo)): ?>
                <div class="img-wrapper" style="height:380px;">
                    <img src="<?= asset('images/memorial/' . $photo) ?>" alt="<?= $name ?>" loading="lazy" style="object-fit:cover;width:100%;height:100%;">
                </div>
                <?php else: ?>
                <div class="img-placeholder" style="height:380px;">
                    <i class="fa fa-user"></i>
                    <span>Photo of <?= $name ?></span>
                </div>
                <?php endif; ?>
            </div>
            <div class="content-block">
                <?php if ($title): ?>
                <p class="section-label"><?= $title ?></p>
                <?php endif; ?>
                <h2 class="section-title" style="font-size:2.5rem;"><?= $first ?> <span><?= $last ?></span></h2>
                <?php if ($tagline): ?>
                <p style="font-style:italic;color:var(--rit-gray);margin-bottom:1rem;font-size:1.05rem;"><?= $tagline ?></p>
                <?php endif; ?>
                <div class="divider"></div>
                <?php
                // Render content: double newlines become paragraphs
                $paragraphs = preg_split('/\n\s*\n/', $content);
                foreach ($paragraphs as $p):
                    $p = trim($p);
                    if (!$p) continue;
                ?>
                <p><?= nl2br(htmlspecialchars($p)) ?></p>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endforeach; ?>

<?php require_once 'includes/footer.php'; ?>
