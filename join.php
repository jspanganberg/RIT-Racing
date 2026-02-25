<?php
$pageTitle       = 'Join the Team';
$pageDescription = 'Join RIT Racing — Formula SAE team at Rochester Institute of Technology. Open to all majors and skill levels.';
require_once 'includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <p class="page-hero-eyebrow">Get Involved</p>
        <h1>Join <span style="color:var(--rit-orange);">RIT Racing</span></h1>
        <p>We build race cars. We build engineers. All majors, all skill levels — if you're ready to work, there's a place for you here.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="two-col">
            <div class="content-block reveal">
                <p class="section-label">Meeting Times</p>
                <h2 class="section-title">Come See <span>Us</span></h2>
                <div class="divider"></div>
                <p>We hold open roundtable and introduction meetings weekly. No experience required — just show up.</p>

                <div style="margin-top:2rem;display:flex;flex-direction:column;gap:1px;background:var(--rit-dark-3);border:1px solid var(--rit-dark-3);">
                    <?php foreach (MEETING_TIMES as $day => $time): ?>
                    <div style="background:var(--rit-dark-2);padding:1.25rem 1.5rem;display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-family:var(--font-label);font-size:1rem;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;"><?= $day ?></span>
                        <span style="font-family:var(--font-display);font-size:1.5rem;color:var(--rit-orange);"><?= $time ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div style="margin-top:1.5rem;background:var(--rit-dark-2);border:1px solid var(--rit-dark-3);padding:1.25rem 1.5rem;">
                    <p style="font-family:var(--font-label);font-size:0.75rem;letter-spacing:0.15em;text-transform:uppercase;color:var(--rit-gray);margin-bottom:0.4rem;">Location</p>
                    <p style="font-weight:600;"><?= MEETING_LOCATION ?></p>
                </div>

                <div style="margin-top:2rem;">
                    <p class="section-label">For Fall 2025 Recruitment</p>
                    <p style="color:var(--rit-gray-light);">
                        Stay posted for our upcoming New Member events at the start of the semester.
                        Follow us on Instagram <a href="<?= SOCIAL_INSTAGRAM ?>" target="_blank" style="color:var(--rit-orange);">@rit_racing</a> for announcements.
                    </p>
                </div>
            </div>

            <div class="reveal reveal-delay-2">
                <!-- PHOTO: Team new member day or shop photo -->
                <div class="img-placeholder" style="height:480px;">
                    <i class="fa fa-users"></i>
                    <span>New Member Day Photo</span>
                    <!--
                    PHOTO: assets/images/team/new-member-day.jpg
                    <div class="img-wrapper" style="height:480px;">
                        <img src="<?= asset('images/team/new-member-day.jpg') ?>" alt="New member day">
                    </div>
                    -->
                </div>
            </div>
        </div>
    </div>
</section>

<!-- What we're looking for -->
<section class="section" style="background:var(--rit-dark-2);">
    <div class="container">
        <div class="reveal text-center" style="margin-bottom:3rem;">
            <p class="section-label" style="justify-content:center;">Who We Want</p>
            <h2 class="section-title">What We're <span>Looking For</span></h2>
        </div>
        <div class="program-grid reveal">
            <?php
            $roles = [
                ['fa-cogs',            'Mechanical Engineers', 'Suspension, chassis, powertrain, brakes — the core of the car.'],
                ['fa-microchip',       'Electrical Engineers', 'Harness design, PCB layout, motor controls, and embedded systems.'],
                ['fa-code',            'Computer Scientists',  'Firmware, vehicle dynamics software, data acquisition, and driverless systems.'],
                ['fa-layer-group',     'Composites / Fab',     'Carbon fiber layup, CNC machining, welding, and manual machining.'],
                ['fa-briefcase',       'Business / Marketing', 'Sponsorship outreach, social media, event planning, and business presentation.'],
                ['fa-question-circle', 'Any Major Welcome',    'If you\'re passionate and willing to work hard, there\'s a role for you.'],
            ];
            foreach ($roles as [$icon,$title,$desc]):
            ?>
            <div class="program-card">
                <div class="program-icon"><i class="fa <?= $icon ?>"></i></div>
                <h3><?= $title ?></h3>
                <p><?= $desc ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Contact form -->
<section class="section">
    <div class="container" style="max-width:720px;">
        <div class="reveal text-center" style="margin-bottom:3rem;">
            <p class="section-label" style="justify-content:center;">Reach Out</p>
            <h2 class="section-title">Get in <span>Touch</span></h2>
        </div>

        <!--
            FORM HANDLING:
            This form posts to contact-handler.php.
            Create that file to handle the POST request and send an email via mail() or PHPMailer.
            The handler should validate fields and redirect back with ?success=1 or ?error=1.
        -->
        <?php if (isset($_GET['success'])): ?>
        <div class="result-banner" style="margin-bottom:2rem;">
            <i class="fa fa-check-circle" style="color:var(--rit-orange);"></i>
            <div><strong>Message Sent!</strong> We'll be in touch soon.</div>
        </div>
        <?php endif; ?>

        <form action="contact-handler.php" method="POST" class="reveal">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1px;background:var(--rit-dark-3);">
                <div style="background:var(--rit-dark-2);padding:1rem;">
                    <label style="font-family:var(--font-label);font-size:0.75rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--rit-gray);display:block;margin-bottom:0.5rem;">First Name *</label>
                    <input type="text" name="first_name" required style="width:100%;background:transparent;border:none;border-bottom:1px solid var(--rit-dark-3);color:var(--rit-white);font-family:var(--font-body);font-size:1rem;padding:0.5rem 0;outline:none;">
                </div>
                <div style="background:var(--rit-dark-2);padding:1rem;">
                    <label style="font-family:var(--font-label);font-size:0.75rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--rit-gray);display:block;margin-bottom:0.5rem;">Last Name *</label>
                    <input type="text" name="last_name" required style="width:100%;background:transparent;border:none;border-bottom:1px solid var(--rit-dark-3);color:var(--rit-white);font-family:var(--font-body);font-size:1rem;padding:0.5rem 0;outline:none;">
                </div>
            </div>
            <div style="background:var(--rit-dark-2);border:1px solid var(--rit-dark-3);border-top:none;padding:1rem;">
                <label style="font-family:var(--font-label);font-size:0.75rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--rit-gray);display:block;margin-bottom:0.5rem;">RIT Email *</label>
                <input type="email" name="email" required style="width:100%;background:transparent;border:none;border-bottom:1px solid var(--rit-dark-3);color:var(--rit-white);font-family:var(--font-body);font-size:1rem;padding:0.5rem 0;outline:none;">
            </div>
            <div style="background:var(--rit-dark-2);border:1px solid var(--rit-dark-3);border-top:none;padding:1rem;">
                <label style="font-family:var(--font-label);font-size:0.75rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--rit-gray);display:block;margin-bottom:0.5rem;">Area of Interest</label>
                <select name="interest" style="width:100%;background:var(--rit-dark-2);border:none;border-bottom:1px solid var(--rit-dark-3);color:var(--rit-white);font-family:var(--font-body);font-size:1rem;padding:0.5rem 0;outline:none;">
                    <option value="">Select one...</option>
                    <option>Aerodynamics</option>
                    <option>Brakes &amp; Driver Controls</option>
                    <option>Business</option>
                    <option>Chassis</option>
                    <option>CNC Manufacturing</option>
                    <option>Composites</option>
                    <option>Driverless</option>
                    <option>Electric Powertrain</option>
                    <option>Electronics / Firmware</option>
                    <option>Fabrication</option>
                    <option>Suspension</option>
                    <option>Vehicle Dynamics</option>
                    <option>Not Sure Yet</option>
                </select>
            </div>
            <div style="background:var(--rit-dark-2);border:1px solid var(--rit-dark-3);border-top:none;padding:1rem;">
                <label style="font-family:var(--font-label);font-size:0.75rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--rit-gray);display:block;margin-bottom:0.5rem;">Message (optional)</label>
                <textarea name="message" rows="4" style="width:100%;background:transparent;border:none;border-bottom:1px solid var(--rit-dark-3);color:var(--rit-white);font-family:var(--font-body);font-size:1rem;padding:0.5rem 0;outline:none;resize:vertical;"></textarea>
            </div>
            <div style="margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;font-size:1rem;">
                    Send Message <i class="fa fa-arrow-right"></i>
                </button>
            </div>
        </form>

        <p style="text-align:center;margin-top:1.5rem;font-size:0.875rem;color:var(--rit-gray);">
            Or email us directly at
            <a href="mailto:<?= CONTACT_EMAIL ?>" style="color:var(--rit-orange);"><?= CONTACT_EMAIL ?></a>
        </p>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
