<?php
$pageTitle       = 'Electric Vehicles';
$pageDescription = 'RIT Racing electric car history — from E1 to the championship-winning F32 and hub motor F33.';
require_once '../includes/header.php';
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

        <!-- Championship banner -->
        <div class="result-banner reveal" style="margin-bottom:4rem;">
            <i class="fa fa-trophy"></i>
            <div>
                <strong>Historic Achievement</strong>
                F32 (2023–2024) claimed 1st Place Overall at FSAE Michigan 2024 —
                the first Michigan win in RIT Racing's 33-year history.
            </div>
        </div>

        <!-- ── F33 ── -->
        <div id="f33" class="two-col reveal" style="margin-bottom:5rem;">
            <div>
                <!-- PHOTO: assets/images/cars/electric/f33-main.jpg -->
                <div class="img-placeholder" style="height:400px;">
                    <i class="fa fa-car"></i><span>F33 Hero Photo</span>
                    <!--
                    <div class="img-wrapper" style="height:400px;">
                        <img src="<?= asset('../images/cars/electric/f33-main.jpg') ?>" alt="F33">
                    </div>
                    -->
                </div>
                <div style="display:flex;gap:1px;background:var(--rit-dark-3);margin-top:1px;">
                    <?php for($i=1;$i<=3;$i++): ?>
                    <div class="img-placeholder" style="height:120px;flex:1;">
                        <i class="fa fa-image"></i><span style="font-size:0.6rem;">F33 Detail <?=$i?></span>
                        <!--
                        PHOTO: assets/images/cars/electric/f33-detail-<?=$i?>.jpg
                        -->
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="content-block">
                <span class="badge badge-orange" style="margin-bottom:1rem;display:inline-block;">Current Car</span>
                <p class="car-card-year">2024 – 2025</p>
                <h2 class="section-title">F33</h2>
                <div class="divider"></div>
                <p>F33 marks RIT Racing's transition to hub motors — placing the drive
                   motors directly in each wheel hub. This architecture dramatically improves
                   weight distribution, enables torque vectoring, and reduces unsprung
                   mass complexity.</p>
                <p>At FSAE Michigan 2025, F33 achieved 8th Place Overall on its competition
                   debut — an exceptional result for a fundamentally redesigned powertrain
                   platform.</p>

                <!-- SPECS TABLE — fill in when known -->
                <table style="width:100%;border-collapse:collapse;margin-top:1.5rem;font-size:0.875rem;">
                    <tr style="border-bottom:1px solid var(--rit-dark-3);">
                        <td style="padding:0.6rem 0;color:var(--rit-gray);font-family:var(--font-label);letter-spacing:0.08em;text-transform:uppercase;font-size:0.75rem;">Motor Configuration</td>
                        <td style="padding:0.6rem 0;font-weight:600;">Hub Motors (×4) <span style="color:var(--rit-gray);font-size:0.8rem;">— PLACEHOLDER</span></td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--rit-dark-3);">
                        <td style="padding:0.6rem 0;color:var(--rit-gray);font-family:var(--font-label);letter-spacing:0.08em;text-transform:uppercase;font-size:0.75rem;">Battery Voltage</td>
                        <td style="padding:0.6rem 0;font-weight:600;">— V <span style="color:var(--rit-gray);font-size:0.8rem;">— PLACEHOLDER</span></td>
                    </tr>
                    <tr style="border-bottom:1px solid var(--rit-dark-3);">
                        <td style="padding:0.6rem 0;color:var(--rit-gray);font-family:var(--font-label);letter-spacing:0.08em;text-transform:uppercase;font-size:0.75rem;">Peak Power</td>
                        <td style="padding:0.6rem 0;font-weight:600;">— kW <span style="color:var(--rit-gray);font-size:0.8rem;">— PLACEHOLDER</span></td>
                    </tr>
                    <tr>
                        <td style="padding:0.6rem 0;color:var(--rit-gray);font-family:var(--font-label);letter-spacing:0.08em;text-transform:uppercase;font-size:0.75rem;">Best Result</td>
                        <td style="padding:0.6rem 0;font-weight:600;color:var(--rit-orange);">8th Overall — FSAE Michigan 2025</td>
                    </tr>
                </table>
            </div>
        </div>

        <hr style="border:none;border-top:1px solid var(--rit-dark-3);margin-bottom:5rem;">

        <!-- ── F32 ── -->
        <div id="f32" class="two-col flip reveal" style="margin-bottom:5rem;">
            <div>
                <div class="img-placeholder" style="height:400px;">
                    <i class="fa fa-car"></i><span>F32 Hero Photo</span>
                    <!--
                    PHOTO: assets/images/cars/electric/f32-main.jpg
                    -->
                </div>
                <div style="display:flex;gap:1px;background:var(--rit-dark-3);margin-top:1px;">
                    <?php for($i=1;$i<=3;$i++): ?>
                    <div class="img-placeholder" style="height:120px;flex:1;">
                        <i class="fa fa-image"></i><span style="font-size:0.6rem;">F32 Detail <?=$i?></span>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="content-block">
                <span class="badge badge-gold" style="margin-bottom:1rem;display:inline-block;">🏆 Michigan Champions</span>
                <p class="car-card-year">2023 – 2024</p>
                <h2 class="section-title">F32</h2>
                <div class="divider"></div>
                <p>The pinnacle of RIT Racing's single inboard motor rear-wheel-drive
                   architecture. F32 represented years of refinement in electronics,
                   aerodynamics, and chassis design converging into one car.</p>
                <p>At FSAE Michigan 2024, F32 claimed 1st Place Overall — the first time
                   in the team's history that RIT Racing won at Michigan. A result that
                   defined a generation of the program.</p>
                <table style="width:100%;border-collapse:collapse;margin-top:1.5rem;font-size:0.875rem;">
                    <tr style="border-bottom:1px solid var(--rit-dark-3);">
                        <td style="padding:0.6rem 0;color:var(--rit-gray);font-family:var(--font-label);letter-spacing:0.08em;text-transform:uppercase;font-size:0.75rem;">Motor Configuration</td>
                        <td style="padding:0.6rem 0;font-weight:600;">Single Inboard, RWD <span style="color:var(--rit-gray);font-size:0.8rem;">— Add Details</span></td>
                    </tr>
                    <tr>
                        <td style="padding:0.6rem 0;color:var(--rit-gray);font-family:var(--font-label);letter-spacing:0.08em;text-transform:uppercase;font-size:0.75rem;">Best Result</td>
                        <td style="padding:0.6rem 0;font-weight:600;color:var(--rit-orange);">1st Overall — FSAE Michigan 2024</td>
                    </tr>
                </table>
            </div>
        </div>

        <hr style="border:none;border-top:1px solid var(--rit-dark-3);margin-bottom:5rem;">

        <!-- ── F30 ── -->
        <div id="f30" class="two-col reveal" style="margin-bottom:5rem;">
            <div>
                <div class="img-placeholder" style="height:340px;">
                    <i class="fa fa-car"></i><span>F30 Photo</span>
                    <!-- PHOTO: assets/images/cars/electric/f30-main.jpg -->
                </div>
            </div>
            <div class="content-block">
                <p class="car-card-year">2021 – 2023</p>
                <h2 class="section-title">F30</h2>
                <div class="divider"></div>
                <p>4th Overall at FSAE Michigan in a field of 72 teams. F30 was also
                   the fastest single inboard motor FSAE car in North America. A car
                   defined by long nights and relentless development across three years.</p>
            </div>
        </div>

        <hr style="border:none;border-top:1px solid var(--rit-dark-3);margin-bottom:5rem;">

        <!-- ── F28 ── -->
        <div id="f28" class="two-col flip reveal" style="margin-bottom:5rem;">
            <div>
                <div class="img-placeholder" style="height:300px;">
                    <i class="fa fa-car"></i><span>F28 Photo</span>
                    <!-- PHOTO: assets/images/cars/electric/f28-main.jpg -->
                </div>
            </div>
            <div class="content-block">
                <p class="car-card-year">2019 – 2021</p>
                <h2 class="section-title">F28</h2>
                <div class="divider"></div>
                <p>Originally designed as the 2020 competition car, F28 became a symbol
                   of resilience through the pandemic years. Despite the disruption,
                   F28 earned 2nd in Design at both Las Vegas and Michigan competitions.</p>
            </div>
        </div>

        <hr style="border:none;border-top:1px solid var(--rit-dark-3);margin-bottom:5rem;">

        <!-- ── E2 & E1 ── -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1px;background:var(--rit-dark-3);border:1px solid var(--rit-dark-3);" class="reveal">
            <div style="background:var(--rit-dark-2);padding:2rem;">
                <div class="img-placeholder" style="height:220px;margin-bottom:1.5rem;">
                    <i class="fa fa-car"></i><span>E2 Photo</span>
                    <!-- PHOTO: assets/images/cars/electric/e2-main.jpg -->
                </div>
                <p class="car-card-year">2017 – 2018</p>
                <h3 style="font-family:var(--font-display);font-size:2rem;margin-bottom:0.75rem;">E2</h3>
                <p style="color:var(--rit-gray-light);font-size:0.9rem;line-height:1.7;">
                    RIT Racing's most competitive electric outing to that point. After months
                    of testing, E2 won its Endurance event and finished 2nd at Formula North.
                </p>
            </div>
            <div style="background:var(--rit-dark-2);padding:2rem;">
                <div class="img-placeholder" style="height:220px;margin-bottom:1.5rem;">
                    <i class="fa fa-car"></i><span>E1 Photo</span>
                    <!-- PHOTO: assets/images/cars/electric/e1-main.jpg -->
                </div>
                <p class="car-card-year">2016 – 2017</p>
                <h3 style="font-family:var(--font-display);font-size:2rem;margin-bottom:0.75rem;">E1</h3>
                <p style="color:var(--rit-gray-light);font-size:0.9rem;line-height:1.7;">
                    The car that started it all. E1 marked RIT Racing's first foray into
                    electric Formula SAE — laying the foundation for everything that followed.
                </p>
            </div>
        </div>

    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2>Want to Build the Next One?</h2>
        <p>Join the team working on F34.</p>
        <a href="<?= url('../join.php') ?>" class="btn-dark">Join RIT Racing</a>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
