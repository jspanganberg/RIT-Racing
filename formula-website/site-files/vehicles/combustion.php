<?php
$pageTitle       = 'Combustion Legacy';
$pageDescription = 'The complete history of RIT Racing combustion cars — F0 (1991) through F27 (2019).';
require_once __DIR__ . '/../includes/header.php';

$cars = get_vehicles('combustion');
$total = count($cars);
?>

<section class="page-hero">
    <div class="container">
        <p class="page-hero-eyebrow">1991 – 2019</p>
        <h1>Combustion <span style="color:var(--rit-orange);">Legacy</span></h1>
        <p><?= $total ?> cars. 28 years. Three continents. The complete history of RIT Racing's combustion era — from F0 to F27.</p>
    </div>
</section>

<!-- Stats strip -->
<div style="background:var(--rit-dark-2);border-bottom:1px solid var(--rit-dark-3);">
    <div class="container">
        <div class="stats-row">
            <div class="stat-block"><h3><?= $total ?></h3><p>Combustion Cars Built</p></div>
            <div class="stat-block"><h3>1991</h3><p>First Car (F0)</p></div>
            <div class="stat-block"><h3>2019</h3><p>Last Car (F27)</p></div>
            <div class="stat-block"><h3>3</h3><p>Continents Competed</p></div>
            <div class="stat-block"><h3>1st</h3><p>FSUK 1999, Australia 2001</p></div>
        </div>
    </div>
</div>

<section class="section">
    <div class="container">

        <div class="result-banner reveal" style="margin-bottom:4rem;">
            <i class="fa fa-info-circle"></i>
            <div>
                <strong>End of an Era</strong>
                In 2019, with F27's final season complete, RIT Racing committed fully to electric Formula SAE.
                The combustion program ran for 28 years and competed on three continents.
                <a href="<?= url('vehicles/electric.php') ?>" style="color:var(--rit-orange);margin-left:0.5rem;">See the Electric Era →</a>
            </div>
        </div>

        <?php foreach ($cars as $i => $car):
            $flip = ($i % 2 !== 0) ? 'flip' : '';
            $photo_file = $car['photo'] ?? '';
            $photo_fs   = $photo_file ? (dirname(__DIR__) . '/assets/images/cars/combustion/' . $photo_file) : '';
            $has_photo  = $photo_file && $photo_fs && file_exists($photo_fs);
        ?>
        <div id="<?= htmlspecialchars($car['id']) ?>" class="two-col <?= $flip ?> reveal"
             style="margin-bottom:4rem;padding-bottom:4rem;border-bottom:1px solid var(--rit-dark-3);">

            <div>
                <?php if ($has_photo): ?>
                <div class="img-wrapper" style="height:380px;">
                    <img src="<?= asset('images/cars/combustion/' . $photo_file) ?>" alt="<?= htmlspecialchars($car['name']) ?> — <?= htmlspecialchars($car['years']) ?>" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <?php else: ?>
                <div class="img-placeholder" style="height:380px;">
                    <i class="fa fa-car"></i>
                    <span><?= htmlspecialchars($car['name']) ?> Photo</span>
                </div>
                <?php endif; ?>
            </div>

            <div class="content-block">
                <p class="car-card-year"><?= htmlspecialchars($car['years']) ?></p>
                <h2 class="section-title" style="font-size:clamp(2rem,5vw,3.5rem);">
                    <?= htmlspecialchars($car['name']) ?>
                </h2>
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
                <p style="color:var(--rit-gray);font-style:italic;">
                    <p style="color:var(--rit-gray);font-style:italic;">More details about this vehicle coming soon.</p>
                </p>
                <?php endif; ?>

                <?php if (!empty($car['result'])): ?>
                <p class="car-card-result" style="margin-top:1rem;">
                    <i class="fa fa-trophy"></i> <?= htmlspecialchars($car['result']) ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2>The Electric Era Continues</h2>
        <p>From F0 to <?= htmlspecialchars(get_latest_car()) ?> — the pursuit of excellence never stops.</p>
        <a href="<?= url('vehicles/electric.php') ?>" class="btn-dark">
            See Our Electric Cars <i class="fa fa-bolt"></i>
        </a>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
