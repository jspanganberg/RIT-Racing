<?php
session_start();
if (empty($_SESSION['public_csrf'])) {
    $_SESSION['public_csrf'] = bin2hex(random_bytes(32));
}
$pageTitle       = 'Contact';
$pageDescription = 'Get in touch with RIT Racing — formula@rit.edu — Kate Gleason College of Engineering, RIT.';
require_once 'includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <p class="page-hero-eyebrow">Reach Out</p>
        <h1>Contact <span style="color:var(--rit-orange);">Us</span></h1>
        <p>Questions, press inquiries, sponsorship discussions, or just want to say hi — we'd love to hear from you.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="two-col" style="align-items:start;">

            <!-- Contact Info -->
            <div class="reveal">
                <p class="section-label">Find Us</p>
                <h2 class="section-title" style="font-size:2.5rem;margin-bottom:2rem;">Get in <span>Touch</span></h2>

                <div style="display:flex;flex-direction:column;gap:1px;background:var(--rit-dark-3);border:1px solid var(--rit-dark-3);">

                    <div style="background:var(--rit-dark-2);padding:1.5rem;display:flex;gap:1.25rem;align-items:flex-start;">
                        <div class="program-icon" style="flex-shrink:0;width:2.5rem;height:2.5rem;font-size:1rem;">
                            <i class="fa fa-envelope"></i>
                        </div>
                        <div>
                            <p style="font-family:var(--font-label);font-size:0.75rem;letter-spacing:0.15em;text-transform:uppercase;color:var(--rit-gray);margin-bottom:0.3rem;">Email</p>
                            <a href="mailto:<?= htmlspecialchars(get_contact_email()) ?>" style="color:var(--rit-orange);font-weight:600;">
                                <?= htmlspecialchars(get_contact_email()) ?>
                            </a>
                        </div>
                    </div>

                    <div style="background:var(--rit-dark-2);padding:1.5rem;display:flex;gap:1.25rem;align-items:flex-start;">
                        <div class="program-icon" style="flex-shrink:0;width:2.5rem;height:2.5rem;font-size:1rem;">
                            <i class="fa fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <p style="font-family:var(--font-label);font-size:0.75rem;letter-spacing:0.15em;text-transform:uppercase;color:var(--rit-gray);margin-bottom:0.3rem;">Location</p>
                            <p style="font-weight:600;line-height:1.6;"><?= get_setting("meeting_location","Kate Gleason College of Engineering — Bldg 9, Rm 2360") ?></p>
                        </div>
                    </div>

                    <div style="background:var(--rit-dark-2);padding:1.5rem;display:flex;gap:1.25rem;align-items:flex-start;">
                        <div class="program-icon" style="flex-shrink:0;width:2.5rem;height:2.5rem;font-size:1rem;">
                            <i class="fa fa-clock"></i>
                        </div>
                        <div>
                            <p style="font-family:var(--font-label);font-size:0.75rem;letter-spacing:0.15em;text-transform:uppercase;color:var(--rit-gray);margin-bottom:0.5rem;">Meeting Times</p>
                            <?php foreach (get_setting("meeting_times",["Tuesday"=>"8:00 PM","Thursday"=>"8:00 PM","Saturday"=>"10:00 AM"]) as $day => $time): ?>
                            <p style="font-weight:600;margin-bottom:0.2rem;">
                                <?= htmlspecialchars($day) ?> <span style="color:var(--rit-orange);"><?= htmlspecialchars($time) ?></span>
                            </p>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div style="background:var(--rit-dark-2);padding:1.5rem;display:flex;gap:1.25rem;align-items:flex-start;">
                        <div class="program-icon" style="flex-shrink:0;width:2.5rem;height:2.5rem;font-size:1rem;">
                            <i class="fab fa-instagram"></i>
                        </div>
                        <div>
                            <p style="font-family:var(--font-label);font-size:0.75rem;letter-spacing:0.15em;text-transform:uppercase;color:var(--rit-gray);margin-bottom:0.3rem;">Social Media</p>
                            <a href="<?= htmlspecialchars(get_setting("social_instagram","https://www.instagram.com/rit_racing/")) ?>" target="_blank" style="color:var(--rit-orange);font-weight:600;">@rit_racing</a>
                        </div>
                    </div>

                </div>

                <?php $bldg_img = site_image('contact_building'); ?>
                <?php if ($bldg_img): ?>
                <div class="img-wrapper reveal" style="height:220px;margin-top:1px;">
                    <img src="<?= $bldg_img ?>" alt="KGCOE Building" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <?php else: ?>
                <div class="img-placeholder reveal" style="height:220px;margin-top:1px;">
                    <i class="fa fa-map"></i>
                    <span>Building Photo — upload via Admin → Settings</span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Form -->
            <div class="reveal reveal-delay-2">

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

                <form action="<?= url('contact-handler.php') ?>" method="POST">
                    <!-- Hidden fields -->
                    <input type="hidden" name="form_source" value="contact">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['public_csrf'], ENT_QUOTES, 'UTF-8') ?>">
                    <!-- Honeypot fields — hidden from real users, bots fill these -->
                    <div style="display:none;" aria-hidden="true">
                        <input type="text" name="website" tabindex="-1" autocomplete="off">
                        <input type="text" name="phone_confirm" tabindex="-1" autocomplete="off">
                    </div>

                    <?php
                    // Reusable field renderer
                    function form_field(string $label, string $name, string $type = 'text', bool $required = true, string $placeholder = ''): void {
                        echo '<div style="background:var(--rit-dark-2);border:1px solid var(--rit-dark-3);border-top:none;padding:1rem;">';
                        echo "<label for=\"{$name}\" style=\"font-family:var(--font-label);font-size:0.75rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--rit-gray);display:block;margin-bottom:0.5rem;\">{$label}" . ($required ? ' <span style="color:var(--rit-orange);">*</span>' : '') . "</label>";
                        $req = $required ? 'required' : '';
                        $ph  = $placeholder ? "placeholder=\"{$placeholder}\"" : '';
                        echo "<input type=\"{$type}\" id=\"{$name}\" name=\"{$name}\" {$req} {$ph} style=\"width:100%;background:transparent;border:none;border-bottom:1px solid var(--rit-dark-3);color:var(--rit-white);font-family:var(--font-body);font-size:1rem;padding:0.5rem 0;outline:none;\" value=\"" . htmlspecialchars($_POST[$name] ?? '') . "\">";
                        echo '</div>';
                    }
                    ?>

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
            </div>

        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>