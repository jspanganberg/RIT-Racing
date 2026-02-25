<?php
$pageTitle       = 'Programs';
$pageDescription = 'Explore the engineering and manufacturing subteams that make up RIT Racing.';
require_once 'includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <p class="page-hero-eyebrow">What You'll Work On</p>
        <h1>Our <span style="color:var(--rit-orange);">Programs</span></h1>
        <p>Ten specialized subteams. One car. Every discipline of engineering represented.</p>
    </div>
</section>
<section class="section">
    <div class="container">
        <?php
        $progs = [
            ['fa-wind',           'Aerodynamics',
             'Our aero team designs the front wing, rear wing, side pods, and underbody of the car using CFD simulations and wind tunnel validation courtesy of our sponsors. We gather quantitative downforce and drag data to iterate toward the optimal aerodynamic balance for FSAE courses.',
             'aero.jpg'],
            ['fa-microchip',      'Electronics',
             'The Electronics group owns everything electrical on the car — from the 12V low-voltage control system to the high-voltage accumulator wiring. This includes harness manufacturing, PCB design, embedded systems programming, and data acquisition.',
             'electronics.jpg'],
            ['fa-bolt',           'Electric Powertrain',
             'The Electric Powertrain team designs, assembles, and tests the heart of our car — the high-voltage accumulator, motor controllers, cooling system, and drivetrain integration. Safety and performance are equally critical in everything this team does.',
             'electric-powertrain.jpg'],
            ['fa-tachometer-alt', 'Vehicle Dynamics',
             'Vehicle Dynamics runs lap simulations, analyzes tire data, and develops the setup philosophy of the car. This team bridges simulation and the physical car, using data from track testing to validate and refine models.',
             'vehicle-dynamics.jpg'],
            ['fa-cogs',           'Suspension',
             'The Suspension team designs A-arms, uprights, bellcranks, wheel centers, and shock mounts. Every component is structurally analyzed based on force inputs from the Vehicle Dynamics team, with weight and stiffness at the forefront of every design decision.',
             'suspension.jpg'],
            ['fa-drafting-compass','Chassis',
             'The Chassis group is responsible for the carbon fiber monocoque — the structural core of the car. They design to maximize rigidity and safety while minimizing weight, conducting thorough finite element analysis on all load paths.',
             'chassis.jpg'],
            ['fa-industry',       'CNC Manufacturing',
             'The CNC team machines every non-composite part that can\'t be done by hand — uprights, hubs, gear carriers, brackets, and more. They operate 3-, 4-, and 5-axis CNC mills, programming and running complex geometries from raw billet.',
             'cnc.jpg'],
            ['fa-layer-group',    'Composites Manufacturing',
             'The Composites team fabricates all carbon fiber, Kevlar, and prepreg parts — body panels, wings, monocoque skins, and aerodynamic components. They manage mold design, layup schedules, and autoclave curing.',
             'composites.jpg'],
            ['fa-car',            'Brakes & Driver Controls',
             'The Brakes & Driver Controls team designs the brake system (calipers, rotors, bias bar, pedal box), steering geometry, and driver interface. Ergonomics and pedal feel are as important as stopping power.',
             'brakes.jpg'],
            ['fa-briefcase',      'Business Operations',
             'Business Operations handles everything that keeps the team funded and organized — sponsorship outreach, social media management, apparel design, event planning, and the business plan presentation event at competition.',
             'business.jpg'],
        ];
        foreach ($progs as $i => [$icon, $title, $desc, $photo]):
        $flip = ($i % 2 !== 0) ? 'flip' : '';
        ?>
        <div class="two-col <?= $flip ?> reveal" style="margin-bottom:5rem;padding-bottom:5rem;border-bottom:1px solid var(--rit-dark-3);">
            <div>
                <div class="img-placeholder" style="height:340px;">
                    <i class="fa <?= $icon ?>" style="font-size:2.5rem;"></i>
                    <span><?= $title ?> Photo</span>
                    <!--
                    PHOTO: assets/images/programs/<?= $photo ?>
                    <div class="img-wrapper" style="height:340px;">
                        <img src="<?= asset("images/programs/{$photo}") ?>" alt="<?= $title ?>">
                    </div>
                    -->
                </div>
            </div>
            <div class="content-block">
                <div class="program-icon" style="margin-bottom:1.5rem;"><i class="fa <?= $icon ?>"></i></div>
                <h2 class="section-title" style="font-size:clamp(1.8rem,4vw,3rem);"><?= $title ?></h2>
                <div class="divider"></div>
                <p><?= $desc ?></p>
                <a href="<?= url('join.php') ?>" class="btn btn-ghost" style="margin-top:1.5rem;">
                    Join This Team <i class="fa fa-arrow-right"></i>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<section class="cta-section">
    <div class="container">
        <h2>Find Your Team</h2>
        <p>Not sure where you fit? Come to a meeting and we'll figure it out together.</p>
        <a href="<?= url('join.php') ?>" class="btn-dark">Join Us</a>
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
