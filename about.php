<?php
$pageTitle       = 'About';
$pageDescription = 'The story of RIT Racing — 34 years of Formula SAE engineering at Rochester Institute of Technology.';
require_once 'includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <p class="page-hero-eyebrow">Est. 1991</p>
        <h1>Our <span style="color:var(--rit-orange);">Story</span></h1>
        <p>Over three decades of building race cars, building engineers, and chasing podiums on three continents.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="two-col">
            <div class="content-block reveal">
                <p class="section-label">The Beginning</p>
                <h2 class="section-title">Born in the <span>Shop</span></h2>
                <div class="divider"></div>
                <p>RIT Racing has its roots in the SAE Collegiate Design Series dating back to
                   1986. In 1991, the team made the switch to the newly emerging Formula SAE
                   program — building F0, a steel spaceframe car powered by a Honda CBR600F
                   engine, and proving that RIT students could build a real race car from scratch.</p>
                <p>From that first prototype, we've never stopped. Every year, a new generation
                   of engineers inherits the knowledge of the last, pushes the design further,
                   and puts it to the test on track at competitions across North America, Europe,
                   and Australia.</p>
            </div>
            <div class="reveal reveal-delay-2">
                <div class="img-placeholder" style="height:400px;">
                    <i class="fa fa-car"></i>
                    <span>Early Car Photo (F0/F1 era)</span>
                    <!--
                    PHOTO: assets/images/hero/f0-historic.jpg
                    -->
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:var(--rit-dark-2);">
    <div class="container">
        <div class="stats-row reveal">
            <div class="stat-block"><h3>1991</h3><p>Year Founded</p></div>
            <div class="stat-block"><h3>33+</h3><p>Cars Built</p></div>
            <div class="stat-block"><h3>3</h3><p>Continents Competed</p></div>
            <div class="stat-block"><h3>30+</h3><p>Active Members</p></div>
            <div class="stat-block"><h3>1st</h3><p>Michigan 2024</p></div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="two-col flip">
            <div class="reveal reveal-delay-2">
                <div class="img-placeholder" style="height:400px;">
                    <i class="fa fa-tools"></i><span>Shop / Build Photo</span>
                    <!-- PHOTO: assets/images/hero/shop-build.jpg -->
                </div>
            </div>
            <div class="content-block reveal">
                <p class="section-label">What We Do</p>
                <h2 class="section-title">Design. Build. <span>Race.</span></h2>
                <div class="divider"></div>
                <p>Every year, the team takes on the full engineering challenge from the ground up —
                   designing suspension geometry, building carbon fiber body panels, writing
                   embedded firmware, fabricating titanium hardware, and tuning the car right up
                   until it rolls into the competition paddock.</p>
                <p>We compete in the SAE Collegiate Design Series Formula SAE program, which
                   challenges university teams to design, build, test, and race a small formula
                   car. Judging covers static events (design, cost, business presentation) and
                   dynamic events (acceleration, skidpad, autocross, and endurance).</p>
                <div class="pullquote">
                    "The knowledge you gain here doesn't come from a textbook."
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:var(--rit-dark-2);">
    <div class="container">
        <div class="reveal text-center" style="margin-bottom:3rem;">
            <p class="section-label" style="justify-content:center;">Milestones</p>
            <h2 class="section-title">A History of <span>Excellence</span></h2>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1px;background:var(--rit-dark-3);border:1px solid var(--rit-dark-3);" class="reveal">
            <?php
            $milestones = [
                ['1991','First FSAE Car','F0 — the steel spaceframe prototype that started everything. Awarded Rookie of the Year in 1993.'],
                ['1996','Top 2 at Michigan','F4 secured 1st in Acceleration, 1st in Autocross, and 2nd Overall at Detroit.'],
                ['1999','FSUK Champions','F7 won 1st Overall at the inaugural Formula Student UK event — and 1st in Design and Acceleration.'],
                ['2001','Australian Victory','F9 climbed to 1st Overall at FSAE Australasia — a dominant performance on three continents.'],
                ['2009','Back on Top','F17 took 1st at FSAE California and 2nd at Michigan — one of the most successful cars in team history.'],
                ['2019','Electric Transition','F27 closes the combustion era; the full focus shifts to electric Formula SAE.'],
                ['2024','Michigan Champions','F32 wins 1st Place Overall at FSAE Michigan — the first Michigan win in team history.'],
                ['2025','Hub Motor Era','F33 debuts hub motors, finishing 8th Overall and opening a new chapter in drivetrain architecture.'],
            ];
            foreach ($milestones as [$year,$title,$desc]):
            ?>
            <div style="background:var(--rit-dark-2);padding:2rem;">
                <p style="font-family:var(--font-display);font-size:3rem;color:var(--rit-orange);line-height:1;margin-bottom:0.5rem;"><?= $year ?></p>
                <h3 style="font-family:var(--font-label);font-size:1rem;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;margin-bottom:0.75rem;"><?= $title ?></h3>
                <p style="font-size:0.875rem;color:var(--rit-gray-light);line-height:1.7;"><?= $desc ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="two-col">
            <div class="content-block reveal">
                <p class="section-label">The Electric Era</p>
                <h2 class="section-title">All In on <span>Electric</span></h2>
                <div class="divider"></div>
                <p>In 2016, RIT Racing began building electric cars alongside its combustion
                   program. By 2019, with F27's final combustion season complete, the team made
                   a full commitment to electric Formula SAE — a decision that would culminate
                   in a national championship just five years later.</p>
                <p>Today, RIT Racing operates exclusively in Formula SAE Electric, pushing the
                   boundaries of battery technology, motor control, and vehicle dynamics
                   in pursuit of the fastest lap time possible.</p>
                <a href="<?= url('vehicles/electric.php') ?>" class="btn btn-primary" style="margin-top:1rem;">
                    See Our Electric Cars <i class="fa fa-bolt"></i>
                </a>
            </div>
            <div class="reveal reveal-delay-2">
                <div class="img-placeholder" style="height:360px;">
                    <i class="fa fa-bolt"></i><span>Electric Car Action Photo</span>
                    <!-- PHOTO: assets/images/cars/electric/electric-action.jpg -->
                </div>
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
