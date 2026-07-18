<?php
$pageTitle       = 'Electric Vehicles';
$pageDescription = 'RIT Racing electric car history — from E1 to the championship-winning F32 and hub motor F33.';
require_once __DIR__ . '/../includes/header.php';

$cars = get_vehicles('electric');
$first_car = $cars[0] ?? null;
?>

<section class="page-hero">
    <div class="container">
        <p class="page-hero-eyebrow">Our Vehicles</p>
        <h1>Electric <span style="color:var(--rit-orange);">Program</span></h1>
        <p>From our first electric car in 2016 to the 2024 FSAE Michigan Champions —
           the story of RIT Racing's electric era is one of relentless progression.</p>
    </div>
</section>

<section class="section">
    <div class="container">

        <!-- Championship banner (editable in Admin → Site Settings → Competition) -->
        <?php
        $banner_show  = !empty(get_setting('achievement_banner_show', 0));
        $banner_label = get_setting('achievement_banner_label', 'Historic Achievement');
        $banner_text  = get_setting('achievement_banner_text', '');
        if ($banner_show && $banner_text):
        ?>
        <div class="result-banner reveal" style="margin-bottom:4rem;">
            <i class="fa fa-trophy"></i>
            <div>
                <strong><?= htmlspecialchars($banner_label) ?></strong>
                <?= htmlspecialchars($banner_text) ?>
            </div>
        </div>
        <?php endif; ?>

        <?php foreach ($cars as $i => $car):
            $flip = ($i % 2 !== 0) ? 'flip' : '';
            $photo_file = $car['photo'] ?? '';
            $photo_fs   = $photo_file ? (dirname(__DIR__) . '/assets/images/cars/electric/' . $photo_file) : '';
            $has_photo  = $photo_file && $photo_fs && file_exists($photo_fs);
        ?>
        <div id="<?= htmlspecialchars($car['id']) ?>" class="two-col <?= $flip ?> reveal" style="margin-bottom:5rem;">
            <div>
                <?php if ($has_photo): ?>
                <div class="img-wrapper" style="height:400px;">
                    <img src="<?= asset('images/cars/electric/' . $photo_file) ?>" alt="<?= htmlspecialchars($car['name']) ?>" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <?php else: ?>
                <div class="img-placeholder" style="height:400px;">
                    <i class="fa fa-car"></i><span><?= htmlspecialchars($car['name']) ?> Photo</span>
                </div>
                <?php endif; ?>
            </div>
            <div class="content-block">
                <?php if (!empty($car['badge'])): ?>
                <span class="badge badge-<?= $car['badge_color'] ?: 'orange' ?>" style="margin-bottom:1rem;display:inline-block;"><?= htmlspecialchars($car['badge']) ?></span>
                <?php endif; ?>
                <p class="car-card-year"><?= htmlspecialchars($car['years']) ?></p>
                <h2 class="section-title"><?= htmlspecialchars($car['name']) ?></h2>
                <?php if (!empty($car['subtitle'])): ?>
                <p style="font-family:var(--font-label);font-size:0.85rem;font-weight:600;letter-spacing:0.08em;color:var(--rit-gray-light);margin-bottom:0.75rem;text-transform:uppercase;">
                    <?= htmlspecialchars($car['subtitle']) ?>
                </p>
                <?php endif; ?>
                <div class="divider"></div>

                <?php if (!empty($car['description'])):
                    foreach (explode("\n\n", $car['description']) as $para):
                        $para = trim($para);
                        if ($para): ?>
                <p><?= htmlspecialchars($para) ?></p>
                <?php   endif;
                    endforeach;
                else: ?>
                <p style="color:var(--rit-gray);font-style:italic;">Description coming soon.</p>
                <?php endif; ?>

                <?php if (!empty($car['specs'])): ?>
                <table style="width:100%;border-collapse:collapse;margin-top:1.5rem;font-size:0.875rem;">
                    <?php foreach ($car['specs'] as $si => $spec): ?>
                    <tr<?= $si < count($car['specs'])-1 ? ' style="border-bottom:1px solid var(--rit-dark-3);"' : '' ?>>
                        <td style="padding:0.6rem 0;color:var(--rit-gray);font-family:var(--font-label);letter-spacing:0.08em;text-transform:uppercase;font-size:0.75rem;"><?= htmlspecialchars($spec['label']) ?></td>
                        <td style="padding:0.6rem 0;font-weight:600;"><?= htmlspecialchars($spec['value']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($i < count($cars) - 1): ?>
        <hr style="border:none;border-top:1px solid var(--rit-dark-3);margin-bottom:5rem;">
        <?php endif; ?>

        <?php endforeach; ?>

    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2>Want to Build the Next One?</h2>
        <?php
        $cur_car  = get_latest_car();               // e.g. "F33" or "F34"
        $cur_num  = (int) preg_replace('/[^0-9]/', '', $cur_car);  // 33
        $prefix   = preg_replace('/[0-9]/', '', $cur_car);         // "F"
        $next_car = $prefix . ($cur_num + 1);        // "F34"
        ?>
        <p>Join the team working on <?= htmlspecialchars($next_car) ?>.</p>
        <a href="<?= url('join.php') ?>" class="btn-dark">Join RIT Racing</a>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
