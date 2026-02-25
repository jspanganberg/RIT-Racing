<?php
$pageTitle       = 'Our Sponsors';
$pageDescription = 'The companies and organizations that make RIT Racing possible.';
require_once 'includes/header.php';

// ============================================================
//  SPONSOR DATA
//  Add logo filenames to 'logo' when files are placed in:
//  assets/images/sponsors/
//  Leave 'logo' as '' to show a text placeholder.
// ============================================================
$sponsors = [
    'Presenting' => [
        ['Dr. David Munson',  'https://www.rit.edu/directory/dcmpro-david-munson', 'dr-munson.png'],
    ],
    'Gold' => [
        ['ANSYS',    'https://www.ansys.com',    'ansys.png'],
        ['Siemens',  'https://www.siemens.com',  'siemens.png'],
        ['Moog',     'https://www.moog.com',     'moog.png'],
        ['Altair',   'https://altair.com',       'altair.png'],
        ['L3 Harris','https://www.l3harris.com', 'l3harris.png'],
    ],
    'Silver' => [
        ['Calspan',        'https://calspan.com',              'calspan.png'],
        ['MAHLE',          'https://www.mahle.com',            'mahle.png'],
        ['SKF',            'https://www.skf.com/us',           'skf.png'],
        ['Global Quest',   'https://globalquestinc.com',       'globalquest.png'],
        ['Hexcel',         'https://www.hexcel.com',           'hexcel.png'],
        ['Rockwest Composites','https://www.rockwestcomposites.com','rockwest.png'],
        ['Siglent',        'https://siglentna.com',            'siglent.png'],
        ['PMD Automation', 'https://www.pmdautomation.com',    'pmd.png'],
        ['Samuel Nelson',  'https://www.samuel.com',           'samuel-nelson.png'],
        ['Altium',         'https://www.altium.com',           'altium.png'],
        ['EarthX',         'https://earthxbatteries.com',      'earthx.png'],
        ['Melasta',        'https://www.melasta.com',          'melasta.png'],
    ],
    'Bronze / In-Kind' => [
        ['SAE International',  'https://www.sae.org',         'sae.png'],
        ['Davies Craig',       'https://daviescraig.com',     'davies-craig.png'],
        ['XRP',                'https://www.xrp.com',         'xrp.png'],
        ['RBC Bearings',       'https://www.rbcbearings.com', 'rbc.png'],
        ['Modern Coatings',    'https://moderncoatings.com',  'modern-coatings.png'],
        ['The Lee Company',    'https://www.theleeco.com',    'lee.png'],
        ['Izze Racing',        'http://www.izzeracing.com',   'izze.png'],
        ['WNY Energy',         'https://www.wnyenergy.com',   'wny-energy.png'],
        ['Rochester Gear',     'https://rochestergear.com',   'rochester-gear.png'],
        ['AAM',                'https://www.aam.com',         'aam.png'],
        ['WireCare',           'https://www.wirecare.com',    'wirecare.png'],
        ['Composite Envisions','https://compositeenvisions.com','composite-envisions.png'],
        ['TeXtreme',           'https://www.textreme.com',    'textreme.png'],
        ['MWI',                'https://mwi-inc.com',         'mwi.png'],
        ['Prowire USA',        'https://www.prowireusa.com',  'prowire.png'],
        ['Raptor Workholding', 'https://raptorworkholding.com','raptor.png'],
        ['Schroth Racing (SSP)','https://www.schrothracing.com','ssp.png'],
        ['HMS Motorsport',     'https://www.hmsmotorsport.com','hms.png'],
        ['Rennscot MFG',       'https://www.rennscotmfg.com', 'rennscot.png'],
        ['KERN Microtechnik',  'https://en.kern-microtechnik.com','kern.png'],
        ['VI-grade',           'https://www.vi-grade.com',   'vi-grade.png'],
        ['Marren Fuel Injection','https://www.injector.com',  'marren.png'],
        ['FIRST',              'https://www.firstinspires.org','first.png'],
        ['KISSsoft',           '#',                           'kisssoft.png'],
    ],
];
?>

<section class="page-hero">
    <div class="container">
        <p class="page-hero-eyebrow">Our Partners</p>
        <h1>Our <span style="color:var(--rit-orange);">Sponsors</span></h1>
        <p>RIT Racing is made possible by the generous support of industry partners
           who share our passion for engineering excellence.</p>
        <div style="margin-top:2rem;display:flex;gap:1rem;flex-wrap:wrap;">
            <a href="<?= url('contribute.php') ?>" class="btn btn-primary">Become a Sponsor</a>
            <a href="<?= asset('files/rit-racing-sponsor-packet.pdf') ?>" target="_blank" class="btn btn-outline">
                <i class="fa fa-download"></i> Sponsor Packet (PDF)
            </a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">

        <?php foreach ($sponsors as $tier => $list): ?>
        <div class="sponsor-tier reveal">
            <p class="sponsor-tier-label"><?= $tier ?> Sponsors</p>
            <div class="sponsor-grid">
                <?php foreach ($list as [$name, $url, $logo]): ?>
                <a href="<?= $url ?>" target="_blank" rel="noopener" class="sponsor-logo" title="<?= htmlspecialchars($name) ?>">
                    <?php if ($logo && file_exists(__DIR__ . "/assets/images/sponsors/{$logo}")): ?>
                        <img src="<?= asset("images/sponsors/{$logo}") ?>" alt="<?= htmlspecialchars($name) ?>" loading="lazy">
                    <?php else: ?>
                        <!-- Logo placeholder: add <?= $logo ?> to assets/images/sponsors/ -->
                        <span style="font-family:var(--font-label);font-size:0.8rem;letter-spacing:0.08em;text-transform:uppercase;color:var(--rit-gray);text-align:center;">
                            <?= htmlspecialchars($name) ?>
                        </span>
                    <?php endif; ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2>Partner With Champions</h2>
        <p>Support the team that won FSAE Michigan 2024. We offer tax credit as a 501(c)3 organization.</p>
        <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
            <a href="<?= url('contribute.php') ?>" class="btn-dark">Become a Sponsor</a>
            <a href="mailto:<?= CONTACT_EMAIL ?>" class="btn btn-outline" style="color:#000;border-color:rgba(0,0,0,0.3);">
                <i class="fa fa-envelope"></i> Contact Us
            </a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
