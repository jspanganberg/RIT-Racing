<?php
$pageTitle       = 'Home';
$pageDescription = 'RIT Racing — Formula SAE team at Rochester Institute of Technology. 2024 FSAE Michigan Champions. Designing, building, and racing since 1991.';
require_once 'includes/header.php';
?>

<!-- ============================================================
     HERO
     ============================================================ -->
<section class="hero">

    <!-- Background: swap .hero-bg-placeholder for .img-wrapper when photo is ready -->
    <div class="hero-bg hero-bg-placeholder">
        <!--
        PHOTO: Replace this comment with:
        <img src="<?= asset('images/hero/f33-hero.jpg') ?>" alt="F33 at competition">
        Recommended: Wide landscape, car at speed or dramatic angle, ~1920x1080px
        -->
    </div>

    <div class="hero-speedlines"></div>

    <div class="hero-content">
        <p class="hero-label">Kate Gleason College of Engineering · Since 1991</p>

        <h1 class="hero-title">
            Built to<br>
            <span class="accent">Win.</span>
        </h1>

        <p class="hero-sub">
            RIT Racing is Rochester Institute of Technology's Formula SAE team —
            designing, fabricating, testing, and racing the finest open-wheel
            electric race cars in the world.
        </p>

        <div class="hero-stat-bar">
            <div class="hero-stat">
                <h3 data-count="34" data-suffix="+">34+</h3>
                <p>Years Competing</p>
            </div>
            <div class="hero-stat">
                <h3 data-count="8">8</h3>
                <p>Overall at Michigan '25</p>
            </div>
            <div class="hero-stat">
                <h3 data-count="1">1</h3>
                <p>Michigan '24 Champions</p>
            </div>
            <div class="hero-stat">
                <h3 data-count="30" data-suffix="+">30+</h3>
                <p>Active Members</p>
            </div>
        </div>

        <div class="hero-actions">
            <a href="<?= url('vehicles/electric.php') ?>" class="btn btn-primary">
                <i class="fa fa-bolt"></i> Our Cars
            </a>
            <a href="<?= url('join.php') ?>" class="btn btn-outline">
                <i class="fa fa-users"></i> Join the Team
            </a>
            <a href="<?= url('contribute.php') ?>" class="btn btn-ghost">
                <i class="fa fa-handshake"></i> Sponsor Us
            </a>
        </div>
    </div>

    <div class="hero-ticker">
        <span class="hero-ticker-label"><i class="fa fa-trophy"></i> Latest Result</span>
        <span class="hero-ticker-text"><?= LATEST_RESULT ?> &nbsp;|&nbsp; <?= BIGGEST_WIN ?></span>
    </div>

</section>


<!-- ============================================================
     STATS ROW
     ============================================================ -->
<section class="section-sm" style="background: var(--rit-dark-2); border-bottom: 1px solid var(--rit-dark-3);">
    <div class="container">
        <div class="stats-row reveal">
            <div class="stat-block">
                <h3 data-count="1991">1991</h3>
                <p>Founded</p>
            </div>
            <div class="stat-block">
                <h3 data-count="33">33</h3>
                <p>Cars Built</p>
            </div>
            <div class="stat-block">
                <h3 data-count="3">3</h3>
                <p>Continents Raced</p>
            </div>
            <div class="stat-block">
                <h3 data-count="10">10+</h3>
                <p>Subteams</p>
            </div>
            <div class="stat-block">
                <h3 data-count="72">72</h3>
                <p>Teams Beaten at Michigan '23</p>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     FEATURED CARS
     ============================================================ -->
<section class="section">
    <div class="container">
        <div class="reveal">
            <p class="section-label">Our Vehicles</p>
            <h2 class="section-title">The Latest <span>Machines</span></h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1px; background: var(--rit-dark-3); border: 1px solid var(--rit-dark-3); margin-top: 3rem;">

            <!-- F33 -->
            <div class="car-card reveal reveal-delay-1">
                <div class="car-card-img img-placeholder" style="height:240px;">
                    <i class="fa fa-car"></i>
                    <span>F33 Photo</span>
                    <!--
                    PHOTO: assets/images/cars/electric/f33-main.jpg
                    Replace div with:
                    <div class="car-card-img img-wrapper" style="height:240px;">
                        <img src="<?= asset('images/cars/electric/f33-main.jpg') ?>" alt="F33 — 2024-2025">
                    </div>
                    -->
                </div>
                <div class="car-card-body">
                    <p class="car-card-year">2024 – 2025</p>
                    <h3 class="car-card-name">F33 <span class="badge badge-orange">Current</span></h3>
                    <p class="car-card-desc">
                        RIT Racing's first hub motor car, marking a major leap in drivetrain
                        architecture. Improved weight distribution and torque vectoring capability.
                    </p>
                    <p class="car-card-result"><i class="fa fa-trophy"></i> 8th Overall — FSAE Michigan 2025</p>
                    <a href="<?= url('vehicles/electric.php') ?>#f33" class="btn btn-ghost" style="margin-top:1.25rem; font-size:0.8rem; padding: 0.6rem 1.25rem;">
                        Learn More <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- F32 -->
            <div class="car-card reveal reveal-delay-2">
                <div class="car-card-img img-placeholder" style="height:240px;">
                    <i class="fa fa-car"></i>
                    <span>F32 Photo</span>
                    <!--
                    PHOTO: assets/images/cars/electric/f32-main.jpg
                    -->
                </div>
                <div class="car-card-body">
                    <p class="car-card-year">2023 – 2024</p>
                    <h3 class="car-card-name">F32 <span class="badge badge-gold">🏆 Champions</span></h3>
                    <p class="car-card-desc">
                        Historic milestone — F32 became the first RIT Racing car to win
                        1st Place Overall at FSAE Michigan, cementing the team's place
                        at the top of American collegiate motorsport.
                    </p>
                    <p class="car-card-result"><i class="fa fa-trophy"></i> 1st Overall — FSAE Michigan 2024</p>
                    <a href="<?= url('vehicles/electric.php') ?>#f32" class="btn btn-ghost" style="margin-top:1.25rem; font-size:0.8rem; padding: 0.6rem 1.25rem;">
                        Learn More <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- F30 -->
            <div class="car-card reveal reveal-delay-3">
                <div class="car-card-img img-placeholder" style="height:240px;">
                    <i class="fa fa-car"></i>
                    <span>F30 Photo</span>
                    <!--
                    PHOTO: assets/images/cars/electric/f30-main.jpg
                    -->
                </div>
                <div class="car-card-body">
                    <p class="car-card-year">2021 – 2023</p>
                    <h3 class="car-card-name">F30</h3>
                    <p class="car-card-desc">
                        4th Overall at FSAE Michigan in a field of 72 teams — the fastest
                        single inboard motor FSAE car in North America at the time.
                    </p>
                    <p class="car-card-result"><i class="fa fa-trophy"></i> 4th Overall — FSAE Michigan</p>
                    <a href="<?= url('vehicles/electric.php') ?>#f30" class="btn btn-ghost" style="margin-top:1.25rem; font-size:0.8rem; padding: 0.6rem 1.25rem;">
                        Learn More <i class="fa fa-arrow-right"></i>
                    </a>
                </div>
            </div>

        </div>

        <div style="text-align:center; margin-top: 2rem;" class="reveal">
            <a href="<?= url('vehicles/electric.php') ?>" class="btn btn-outline">
                Full Electric History <i class="fa fa-arrow-right"></i>
            </a>
            <a href="<?= url('vehicles/combustion.php') ?>" class="btn btn-outline" style="margin-left:1rem;">
                Combustion Legacy <i class="fa fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>


<!-- ============================================================
     ABOUT STRIP
     ============================================================ -->
<section class="section" style="background: var(--rit-dark-2);">
    <div class="container">
        <div class="two-col">
            <div class="content-block reveal">
                <p class="section-label">Who We Are</p>
                <h2 class="section-title">More Than a <span>Racing Team</span></h2>
                <div class="divider"></div>
                <p>
                    Founded in 1991 within the Kate Gleason College of Engineering at RIT,
                    we are one of the longest-running Formula SAE programs in North America.
                    Every car we build is a complete student-led engineering project —
                    from concept sketches to competition podiums.
                </p>
                <p>
                    We welcome 20–30 new members every year across disciplines including
                    mechanical engineering, electrical engineering, computer science,
                    business, and more. Our new member retention rate is among the highest
                    of any project team on campus.
                </p>
                <div class="pullquote">
                    "We don't just build cars — we build engineers."
                </div>
                <a href="<?= url('about.php') ?>" class="btn btn-primary" style="margin-top:0.5rem;">
                    Our Story <i class="fa fa-arrow-right"></i>
                </a>
            </div>

            <div class="reveal reveal-delay-2">
                <!-- PHOTO: Replace with real team photo -->
                <div class="img-placeholder" style="height:480px; border-radius:0;">
                    <i class="fa fa-users"></i>
                    <span>Team Photo</span>
                    <!--
                    Replace with:
                    <div class="img-wrapper" style="height:480px;">
                        <img src="<?= asset('images/team/team-2024.jpg') ?>" alt="RIT Racing 2024 Team">
                    </div>
                    -->
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     PROGRAMS PREVIEW
     ============================================================ -->
<section class="section">
    <div class="container">
        <div class="reveal" style="margin-bottom: 3rem;">
            <p class="section-label">What You'll Do</p>
            <h2 class="section-title">Our <span>Programs</span></h2>
        </div>

        <div class="program-grid">
            <?php
            $programs = [
                ['fa-wind',          'Aerodynamics',       'CFD simulation, wing design, and downforce validation.'],
                ['fa-microchip',     'Electronics',        'Harness design, PCB development, embedded systems, and DAQ.'],
                ['fa-bolt',          'Electric Powertrain','High-voltage battery, motor integration, and cooling systems.'],
                ['fa-cogs',          'Suspension',         'A-arms, uprights, bellcranks, and full chassis force analysis.'],
                ['fa-tachometer-alt','Vehicle Dynamics',   'Lap sim, data analysis, tire modeling, and driver-in-loop.'],
                ['fa-drafting-compass','Chassis',          'Carbon fiber monocoque design and structural safety analysis.'],
                ['fa-industry',      'CNC Manufacturing',  '3-, 4-, and 5-axis machining of complex geometry parts.'],
                ['fa-layer-group',   'Composites',         'Carbon fiber, Kevlar, and prepreg fabrication for aero and chassis.'],
                ['fa-wrench',        'Brakes & Controls',  'Brake bias, pedal box ergonomics, and steering geometry.'],
                ['fa-briefcase',     'Business',           'Sponsorship, marketing, event planning, and business presentation.'],
            ];
            foreach ($programs as $i => [$icon, $title, $desc]):
            ?>
            <div class="program-card reveal reveal-delay-<?= ($i % 4) + 1 ?>">
                <div class="program-icon"><i class="fa <?= $icon ?>"></i></div>
                <h3><?= $title ?></h3>
                <p><?= $desc ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align:center; margin-top:2rem;" class="reveal">
            <a href="<?= url('programs.php') ?>" class="btn btn-outline">
                View All Programs <i class="fa fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>


<!-- ============================================================
     SHOP PHOTO STRIP (full-width atmosphere image)
     ============================================================ -->
<div class="reveal" style="height: 360px; position:relative; overflow:hidden;">
    <!-- PHOTO: Replace with a wide shop/build photo for atmosphere -->
    <div class="img-placeholder" style="height:100%; border:none; border-radius:0;">
        <i class="fa fa-tools" style="font-size:3rem;"></i>
        <span>Shop / Build Photo — Full Width Atmosphere Shot</span>
        <!--
        Replace with:
        <img src="<?= asset('images/hero/shop-atmosphere.jpg') ?>"
             alt="RIT Racing machine shop"
             style="width:100%;height:100%;object-fit:cover;">
        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.4);"></div>
        -->
    </div>
</div>


<!-- ============================================================
     LATEST NEWS / RESULTS
     ============================================================ -->
<section class="section" style="background: var(--rit-dark-2);">
    <div class="container">
        <div class="reveal" style="margin-bottom:3rem;">
            <p class="section-label">News & Results</p>
            <h2 class="section-title">What's <span>Happening</span></h2>
        </div>

        <div class="news-grid">
            <!--
                NEWS CARDS: Add / update these as results and news come in.
                Each card is a .news-card div with date, title, and description.
            -->
            <div class="news-card reveal reveal-delay-1">
                <p class="news-card-date">June 2025 — FSAE Michigan</p>
                <h3>F33 Finishes 8th Overall</h3>
                <p>Our first hub motor car, F33, completed its competition debut with
                   an 8th place overall finish at FSAE Michigan 2025 — a landmark result
                   for a brand-new drivetrain architecture.</p>
            </div>
            <div class="news-card reveal reveal-delay-2">
                <p class="news-card-date">June 2024 — FSAE Michigan</p>
                <h3>Historic 1st Place Win — F32</h3>
                <p>For the first time in team history, RIT Racing took 1st Place Overall
                   at FSAE Michigan with F32. A milestone 33 years in the making.</p>
            </div>
            <div class="news-card reveal reveal-delay-3">
                <p class="news-card-date">Fall 2025 — Recruitment</p>
                <h3>New Member Applications Open</h3>
                <p>Interested in joining for the F34 build season? We meet Tuesday,
                   Thursday at 8PM and Saturday at 10AM in KGCOE Bldg 9, Rm 2360.
                   All majors welcome.</p>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     SPONSORS STRIP
     ============================================================ -->
<section class="section-sm" style="background: var(--rit-dark-2); border-top: 1px solid var(--rit-dark-3);">
    <div class="container">
        <p class="section-label text-center" style="justify-content:center;" >Our Partners</p>
        <div style="display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:2.5rem; margin-top:2rem;" class="reveal">
            <!--
                SPONSOR LOGOS: Add real logos here as images.
                Each logo should be wrapped in an <a> tag.
                Example:
                <a href="https://www.ansys.com" target="_blank">
                    <img src="<?= asset('images/sponsors/ansys.png') ?>"
                         alt="ANSYS" style="height:36px; filter:brightness(0.6) saturate(0); transition:filter 0.3s;">
                </a>
                Hover effect is already handled by CSS .sponsor-logo
            -->
            <!-- Placeholder blocks for key sponsors -->
            <?php $sponsors = ['ANSYS','Siemens','Moog','Altair','L3 Harris','Calspan','MAHLE','SKF']; ?>
            <?php foreach ($sponsors as $s): ?>
            <div style="
                height:40px; min-width:100px; max-width:140px;
                background:var(--rit-dark-3);
                display:flex; align-items:center; justify-content:center;
                font-family:var(--font-label); font-size:0.7rem;
                letter-spacing:0.1em; color:var(--rit-gray);
                text-transform:uppercase; padding: 0 1rem;
            "><?= $s ?></div>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center; margin-top:2rem;" class="reveal">
            <a href="<?= url('sponsors.php') ?>" class="btn btn-outline" style="font-size:0.8rem; padding:0.6rem 1.5rem;">
                View All Sponsors <i class="fa fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>


<!-- ============================================================
     CTA
     ============================================================ -->
<section class="cta-section">
    <div class="container">
        <h2>Ready to Build Something?</h2>
        <p>All RIT students are welcome. All majors. All skill levels.</p>
        <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
            <a href="<?= url('join.php') ?>" class="btn-dark">Join the Team</a>
            <a href="<?= url('contribute.php') ?>" class="btn btn-outline" style="color:#000; border-color:rgba(0,0,0,0.3);">
                Become a Sponsor
            </a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
