<?php
$pageTitle       = 'Meet the Team';
$pageDescription = 'Meet the engineers, designers, and builders behind RIT Racing.';
require_once 'includes/header.php';

// ============================================================
//  TEAM DATA — Update this array each year
//  Add real photo filenames to 'photo' field when available
//  Leave 'photo' as '' to show the placeholder
// ============================================================

$admin = [
    ['Jason Mendola',   'Project Manager',  'jpm4675@rit.edu', ''],
    ['Logan McDonald',  'Chief Engineer',   'lmm6836@rit.edu', ''],
];

$design_leads = [
    ['Justin Surace',          'Aerodynamics Lead',        'jls8679@rit.edu', ''],
    ['Ethan Onslow',           'Brakes Lead',              'eeo9478@rit.edu', ''],
    ['Rose Anselm',            'Business Lead',            'rma3554@rit.edu', ''],
    ['Tyler Berry',            'Chassis Lead',             'trb9679@rit.edu', ''],
    ['Carson Tosta',           'Combustion Powertrain Lead','cjt3414@rit.edu', ''],
    ['Nick Vessa',             'Drivetrain Lead',          'nmv5049@rit.edu', ''],
    ['Chris Bencini',          'Electronics Manager',      'cmb3030@rit.edu', ''],
    ['Solomon Shulman',        'Firmware Lead',            'sls3445@rit.edu', ''],
    ['Dewel Gonzalez',         'Harness Lead',             'dg2863@rit.edu',  ''],
    ['Sean Whelton',           'PCB Lead',                 'spw5571@rit.edu', ''],
    ['Matthias Nigmann',       'Driverless Lead',          'mrn9018@rit.edu', ''],
    ['Andrew Etter',           'Electric Powertrain Mgr',  'ape4961@rit.edu', ''],
    ['Mehmet Sarp Ozengin',    'Cooling Lead',             'mso6857@rit.edu', ''],
    ['Zain Majid',             'Inverters Lead',           'zfm4970@rit.edu', ''],
    ['Noah Means',             'Suspension Lead',          'ndm3053@rit.edu', ''],
    ['Ryan Black',             'Vehicle Dynamics Lead',    'reb4668@rit.edu', ''],
];

$mfg_leads = [
    ['Victor Ortega Boisselier','Fabrication Lead',        'vmo6523@rit.edu', ''],
    ['Hayden Graff',            'CNC Lead',                'hmg1948@rit.edu', ''],
    ['Jair Texca Ramirez',      'Composites Lead',         'jt9728@rit.edu',  ''],
];

// Helper to render a team card
function teamCard(string $name, string $role, string $email, string $photo, bool $large = false): void {
    $cls = $large ? 'team-card lead' : 'team-card';
    $photoPath = $photo
        ? "assets/images/team/{$photo}"
        : '';
    echo "<div class=\"{$cls}\">";
    echo '<div class="team-card-photo">';
    if ($photoPath) {
        echo "<img src=\"" . asset($photoPath) . "\" alt=\"{$name}\" loading=\"lazy\">";
    } else {
        echo '<div class="img-placeholder" style="height:100%;border:none;">';
        echo '<i class="fa fa-user"></i>';
        echo "</div>";
    }
    echo '</div>';
    echo '<div class="team-card-info">';
    echo "<p class=\"team-card-role\">{$role}</p>";
    echo "<p class=\"team-card-name\">{$name}</p>";
    echo "<p class=\"team-card-email\">{$email}</p>";
    echo '</div>';
    echo '</div>';
}
?>

<section class="page-hero">
    <div class="container">
        <p class="page-hero-eyebrow">The People Behind the Car</p>
        <h1>Meet the <span style="color:var(--rit-orange);">Team</span></h1>
        <p>Every bolt turned and every line of code written by RIT students —
           driven by a shared passion for motorsport engineering.</p>
    </div>
</section>

<section class="section">
    <div class="container">

        <!-- Full team photo -->
        <div class="img-placeholder reveal" style="height:360px;margin-bottom:5rem;">
            <i class="fa fa-users" style="font-size:3rem;"></i>
            <span>Full Team Photo 2024–2025</span>
            <!--
            PHOTO: Replace with:
            <div class="img-wrapper reveal" style="height:360px;margin-bottom:5rem;">
                <img src="<?= asset('images/team/team-full-2025.jpg') ?>" alt="RIT Racing 2024-2025 team">
            </div>
            -->
        </div>

        <!-- ── Administration ── -->
        <div class="reveal">
            <p class="section-label">Leadership</p>
            <h2 class="section-title" style="margin-bottom:2rem;">Team <span>Administration</span></h2>
        </div>
        <div class="team-grid" style="grid-template-columns:repeat(auto-fill,minmax(260px,1fr));margin-bottom:4rem;" class="reveal">
            <?php foreach ($admin as [$name,$role,$email,$photo]): ?>
                <?php teamCard($name, $role, $email, $photo, true); ?>
            <?php endforeach; ?>
        </div>

        <!-- ── Design Leads ── -->
        <div class="reveal">
            <p class="section-label">Design</p>
            <h2 class="section-title" style="margin-bottom:2rem;">Design <span>Leads</span></h2>
        </div>
        <div class="team-grid reveal" style="margin-bottom:4rem;">
            <?php foreach ($design_leads as [$name,$role,$email,$photo]): ?>
                <?php teamCard($name, $role, $email, $photo); ?>
            <?php endforeach; ?>
        </div>

        <!-- ── Manufacturing Leads ── -->
        <div class="reveal">
            <p class="section-label">Manufacturing</p>
            <h2 class="section-title" style="margin-bottom:2rem;">Manufacturing <span>Leads</span></h2>
        </div>
        <div class="team-grid reveal" style="margin-bottom:4rem;">
            <?php foreach ($mfg_leads as [$name,$role,$email,$photo]): ?>
                <?php teamCard($name, $role, $email, $photo); ?>
            <?php endforeach; ?>
        </div>

        <!-- ── Associate Engineers ── -->
        <div class="reveal">
            <p class="section-label">Members</p>
            <h2 class="section-title" style="margin-bottom:1rem;">Associate <span>Engineers</span></h2>
            <p style="color:var(--rit-gray-light);margin-bottom:2rem;">
                <!-- UPDATE: Add associate engineers here in the same format as above -->
                Associate engineer listing coming soon — update the $associates array in team.php.
            </p>
        </div>

        <!--
        ADD ASSOCIATES: Uncomment and populate this array:

        $associates = [
            ['Name Here', 'Role Here', 'email@rit.edu', 'filename.jpg'],
            ...
        ];
        Then loop through with: foreach ($associates as [$name,$role,$email,$photo]) teamCard(...);
        -->

    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2>Want to See Your Name Here?</h2>
        <p>We recruit new members every semester.</p>
        <a href="<?= url('join.php') ?>" class="btn-dark">How to Join</a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
