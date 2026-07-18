<?php
// ============================================================
//  PROGRAMS PAGE — Subteam Descriptions
//
//  All content is now editable from Admin → Programs.
//  Data stored in data/programs.json.
//  Photos are set from Admin → Settings → Images → Programs.
// ============================================================
$pageTitle       = 'Programs';
$pageDescription = 'Explore the engineering and manufacturing subteams that make up RIT Racing.';
require_once 'includes/header.php';

$programs = data_load('programs.json');
if (!is_array($programs)) $programs = [];
usort($programs, fn($a,$b) => ($a['order']??99) <=> ($b['order']??99));
?>
<section class="page-hero">
    <div class="container">
        <p class="page-hero-eyebrow">What You'll Work On</p>
        <h1>Our <span style="color:var(--rit-orange);">Programs</span></h1>
        <p>Eleven specialized subteams. One car. Every discipline of engineering represented.</p>
    </div>
</section>
<section class="section">
    <div class="container">
        <?php foreach ($programs as $i => $prog):
            $flip = ($i % 2 !== 0) ? 'flip' : '';
            $icon  = $prog['icon'] ?? 'fa-gear';
            $title = $prog['title'] ?? '';
            $desc  = $prog['description'] ?? '';
            $photo = $prog['photo'] ?? '';

            // Check for photo
            $photo_path = $photo ? (__DIR__ . '/assets/images/programs/' . $photo) : '';
            $has_photo = $photo && $photo_path && file_exists($photo_path);
        ?>
        <div id="prog-<?= htmlspecialchars($prog['id'] ?? $i) ?>" class="two-col <?= $flip ?> reveal" style="margin-bottom:5rem;padding-bottom:5rem;border-bottom:1px solid var(--rit-dark-3);scroll-margin-top:calc(var(--nav-height) + 2rem);">
            <div>
                <?php if ($has_photo): ?>
                <div class="img-wrapper" style="height:340px;">
                    <img src="<?= asset('images/programs/' . $photo) ?>" alt="<?= htmlspecialchars($title) ?>" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <?php else: ?>
                <div class="img-placeholder" style="height:340px;">
                    <i class="fa <?= htmlspecialchars($icon) ?>" style="font-size:2rem;"></i>
                    <span><?= htmlspecialchars($title) ?> Photo</span>
                </div>
                <?php endif; ?>
            </div>
            <div class="content-block">
                <div class="program-icon" style="margin-bottom:1.5rem;"><i class="fa <?= htmlspecialchars($icon) ?>"></i></div>
                <h2 class="section-title" style="font-size:clamp(1.8rem,4vw,3rem);"><?= htmlspecialchars($title) ?></h2>
                <div class="divider"></div>
                <p><?= htmlspecialchars($desc) ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<section class="cta-section">
    <div class="container">
        <h2>Find Your Team</h2>
        <p>No experience needed — just show up and start building.</p>
        <a href="<?= url('join.php') ?>" class="btn-dark">How to Join</a>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
