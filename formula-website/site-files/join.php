<?php
session_start();
if (empty($_SESSION['public_csrf'])) {
    $_SESSION['public_csrf'] = bin2hex(random_bytes(32));
}
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
                    <?php foreach (get_setting("meeting_times",["Tuesday"=>"8:00 PM","Thursday"=>"8:00 PM","Saturday"=>"10:00 AM"]) as $day => $time): ?>
                    <div style="background:var(--rit-dark-2);padding:1.25rem 1.5rem;display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-family:var(--font-label);font-size:1rem;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;"><?= $day ?></span>
                        <span style="font-family:var(--font-display);font-size:1.5rem;color:var(--rit-orange);"><?= $time ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div style="margin-top:1.5rem;background:var(--rit-dark-2);border:1px solid var(--rit-dark-3);padding:1.25rem 1.5rem;">
                    <p style="font-family:var(--font-label);font-size:0.75rem;letter-spacing:0.15em;text-transform:uppercase;color:var(--rit-gray);margin-bottom:0.4rem;">Location</p>
                    <p style="font-weight:600;"><?= get_setting("meeting_location","Kate Gleason College of Engineering — Bldg 9, Rm 2360") ?></p>
                </div>

                <div style="margin-top:2rem;">
                    <p class="section-label">For Fall 2025 Recruitment</p>
                    <p style="color:var(--rit-gray-light);">
                        Stay posted for our upcoming New Member events at the start of the semester.
                        Follow us on Instagram <a href="<?= htmlspecialchars(get_setting("social_instagram","https://www.instagram.com/rit_racing/")) ?>" target="_blank" style="color:var(--rit-orange);">@rit_racing</a> for announcements.
                    </p>
                </div>
            </div>

            <div class="reveal reveal-delay-2">
                <?php $join_img = site_image('join_hero'); ?>
                <?php if ($join_img): ?>
                <div class="img-wrapper" style="height:480px;">
                    <img src="<?= $join_img ?>" alt="Join RIT Racing" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <?php else: ?>
                <div class="img-placeholder" style="height:480px;">
                    <i class="fa fa-users"></i>
                    <span>Recruitment Photo — upload via Admin → Settings</span>
                </div>
                <?php endif; ?>
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

        <?php if (isset($_GET['success'])): ?>
        <div class="result-banner" style="margin-bottom:2rem;">
            <i class="fa fa-check-circle"></i>
            <div><strong>Message Sent!</strong> We'll get back to you soon.</div>
        </div>
        <?php elseif (isset($_GET['error'])): ?>
        <div class="result-banner" style="margin-bottom:2rem;border-color:#c0392b;">
            <i class="fa fa-exclamation-circle" style="color:#c0392b;"></i>
            <div>
                <strong>Something went wrong.</strong>
                <?php if ($_GET['error'] === 'ratelimit'): ?>
                    Too many submissions. Please wait an hour and try again.
                <?php else: ?>
                    Please try again or email us directly at <a href="mailto:<?= htmlspecialchars(get_contact_email()) ?>" style="color:var(--rit-orange);"><?= htmlspecialchars(get_contact_email()) ?></a>.
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <form action="<?= url('contact-handler.php') ?>" method="POST" class="reveal">
            <input type="hidden" name="form_source" value="join">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['public_csrf'], ENT_QUOTES, 'UTF-8') ?>">
            <div style="display:none;" aria-hidden="true">
                <input type="text" name="website" tabindex="-1" autocomplete="off">
                <input type="text" name="phone_confirm" tabindex="-1" autocomplete="off">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1px;background:var(--rit-dark-3);">
                <div style="background:var(--rit-dark-2);padding:1rem;">
                    <label for="first_name" style="font-family:var(--font-label);font-size:0.75rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--rit-gray);display:block;margin-bottom:0.5rem;">First Name <span style="color:var(--rit-orange);">*</span></label>
                    <input type="text" id="first_name" name="first_name" required style="width:100%;background:transparent;border:none;border-bottom:1px solid var(--rit-dark-3);color:var(--rit-white);font-family:var(--font-body);font-size:1rem;padding:0.5rem 0;outline:none;">
                </div>
                <div style="background:var(--rit-dark-2);padding:1rem;">
                    <label for="last_name" style="font-family:var(--font-label);font-size:0.75rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--rit-gray);display:block;margin-bottom:0.5rem;">Last Name <span style="color:var(--rit-orange);">*</span></label>
                    <input type="text" id="last_name" name="last_name" required style="width:100%;background:transparent;border:none;border-bottom:1px solid var(--rit-dark-3);color:var(--rit-white);font-family:var(--font-body);font-size:1rem;padding:0.5rem 0;outline:none;">
                </div>
            </div>

            <div style="background:var(--rit-dark-2);border:1px solid var(--rit-dark-3);border-top:none;padding:1rem;">
                <label for="email" style="font-family:var(--font-label);font-size:0.75rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--rit-gray);display:block;margin-bottom:0.5rem;">Email Address <span style="color:var(--rit-orange);">*</span></label>
                <input type="email" id="email" name="email" required style="width:100%;background:transparent;border:none;border-bottom:1px solid var(--rit-dark-3);color:var(--rit-white);font-family:var(--font-body);font-size:1rem;padding:0.5rem 0;outline:none;">
            </div>

            <div style="background:var(--rit-dark-2);border:1px solid var(--rit-dark-3);border-top:none;padding:1rem;">
                <label for="subject" style="font-family:var(--font-label);font-size:0.75rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--rit-gray);display:block;margin-bottom:0.5rem;">Subject</label>
                <select id="subject" name="subject" style="width:100%;background:var(--rit-dark-2);border:none;border-bottom:1px solid var(--rit-dark-3);color:var(--rit-white);font-family:var(--font-body);font-size:1rem;padding:0.5rem 0;outline:none;">
                    <option value="General Inquiry">General Inquiry</option>
                    <option value="Sponsorship">Sponsorship / Partnership</option>
                    <option value="Press / Media">Press / Media</option>
                    <option value="Alumni">Alumni Inquiry</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div style="background:var(--rit-dark-2);border:1px solid var(--rit-dark-3);border-top:none;padding:1rem;">
                <label for="message" style="font-family:var(--font-label);font-size:0.75rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--rit-gray);display:block;margin-bottom:0.5rem;">Message <span style="color:var(--rit-orange);">*</span></label>
                <textarea id="message" name="message" required rows="5" style="width:100%;background:transparent;border:none;border-bottom:1px solid var(--rit-dark-3);color:var(--rit-white);font-family:var(--font-body);font-size:1rem;padding:0.5rem 0;outline:none;resize:vertical;"></textarea>
            </div>

            <div style="margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;font-size:1rem;">
                    Send Message <i class="fa fa-paper-plane"></i>
                </button>
            </div>
        </form>

        <p style="text-align:center;margin-top:1.5rem;font-size:0.875rem;color:var(--rit-gray);">
            Or email us directly at
            <a href="mailto:<?= htmlspecialchars(get_contact_email()) ?>" style="color:var(--rit-orange);"><?= htmlspecialchars(get_contact_email()) ?></a>
        </p>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
