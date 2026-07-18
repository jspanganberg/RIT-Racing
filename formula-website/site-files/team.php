<?php
$pageTitle       = 'Meet the Team';
$pageDescription = 'Meet the engineers, designers, and builders behind RIT Racing.';
require_once 'includes/header.php';

$team = data_load('team.json');
if (!is_array($team)) $team = [];
usort($team, fn($a,$b) => ($a['order']??99) <=> ($b['order']??99));

// Split into groups
$admins    = array_filter($team, fn($m) => ($m['group'] ?? '') === 'admin');
$returning = array_filter($team, fn($m) => ($m['group'] ?? '') === 'returning');
$active    = array_filter($team, fn($m) => !in_array($m['group'] ?? '', ['admin', 'returning']));

// Build subteam groups from active members
$subteams = [];
foreach ($active as $m) {
    $st = $m['subteam'] ?? 'Other';
    if (!isset($subteams[$st])) $subteams[$st] = [];
    $subteams[$st][] = $m;
}
ksort($subteams);

$total_count = count($admins) + count($active) + count($returning);
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

        <!-- Team photo -->
        <?php $team_img = site_image('team_group'); ?>
        <?php if ($team_img): ?>
        <div class="img-wrapper reveal" style="height:380px;margin-bottom:4rem;">
            <img src="<?= $team_img ?>" alt="RIT Racing Team" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <?php else: ?>
        <div class="img-placeholder reveal" style="height:380px;margin-bottom:4rem;">
            <i class="fa fa-users" style="font-size:3rem;"></i>
            <span>Full Team Photo — upload via Admin → Settings → Site Images</span>
        </div>
        <?php endif; ?>

        <!-- ═══════════ LEADERSHIP ═══════════ -->
        <?php if (!empty($admins)): ?>
        <div class="reveal" style="text-align:center;">
            <p class="section-label" style="justify-content:center;">Leadership</p>
            <h2 class="section-title" style="margin-bottom:2rem;">Team <span>Administration</span></h2>
        </div>
        <div class="org-admin-row reveal" style="margin-bottom:4rem;">
            <?php foreach ($admins as $m): ?>
            <div class="org-admin-card">
                <div class="org-admin-photo">
                    <?php if (!empty($m['photo'])): ?>
                    <img src="<?= asset('images/team/' . $m['photo']) ?>" alt="<?= htmlspecialchars($m['name']) ?>" loading="lazy">
                    <?php else: ?>
                    <div class="org-photo-placeholder"><i class="fa fa-user"></i></div>
                    <?php endif; ?>
                </div>
                <div class="org-admin-info">
                    <p class="org-role"><?= htmlspecialchars($m['role']) ?></p>
                    <p class="org-name"><?= htmlspecialchars($m['name']) ?></p>
                    <?php if (!empty($m['email'])): ?>
                    <p class="org-email"><?= htmlspecialchars($m['email']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- ═══════════ SUBTEAMS ═══════════ -->
        <?php foreach ($subteams as $subteam_name => $members): ?>
        <div class="reveal" style="margin-bottom:3rem;">
            <div class="org-subteam-header">
                <h3><?= htmlspecialchars($subteam_name) ?></h3>
                <span class="org-subteam-count"><?= count($members) ?></span>
            </div>
            <div class="org-member-grid">
                <?php foreach ($members as $m): ?>
                <div class="org-member-card">
                    <div class="org-member-photo">
                        <?php if (!empty($m['photo'])): ?>
                        <img src="<?= asset('images/team/' . $m['photo']) ?>" alt="<?= htmlspecialchars($m['name']) ?>" loading="lazy">
                        <?php else: ?>
                        <div class="org-photo-placeholder"><i class="fa fa-user"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="org-member-card-info">
                        <p class="org-role"><?= htmlspecialchars($m['role']) ?></p>
                        <p class="org-name-member"><?= htmlspecialchars($m['name']) ?></p>
                        <?php if (!empty($m['email'])): ?>
                        <p class="org-email-member"><?= htmlspecialchars($m['email']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
</section>

<!-- ═══════════ RETURNING MEMBERS ═══════════ -->
<?php if (!empty($returning)): ?>
<section class="section" style="background:var(--rit-dark-2);">
    <div class="container">
        <div class="reveal" style="text-align:center;">
            <p class="section-label" style="justify-content:center;">Welcome Back</p>
            <h2 class="section-title" style="margin-bottom:2rem;">Returning <span>Members</span></h2>
            <p style="color:var(--rit-gray);margin-bottom:2rem;max-width:600px;margin-left:auto;margin-right:auto;">
                Experienced members returning to the team this season — bringing knowledge and skills from previous builds.
            </p>
        </div>
        <div class="org-member-grid reveal">
            <?php foreach ($returning as $m): ?>
            <div class="org-member-card">
                <div class="org-member-photo">
                    <?php if (!empty($m['photo'])): ?>
                    <img src="<?= asset('images/team/' . $m['photo']) ?>" alt="<?= htmlspecialchars($m['name']) ?>" loading="lazy">
                    <?php else: ?>
                    <div class="org-photo-placeholder"><i class="fa fa-user"></i></div>
                    <?php endif; ?>
                </div>
                <div class="org-member-card-info">
                    <p class="org-role"><?= htmlspecialchars($m['role']) ?></p>
                    <p class="org-name-member"><?= htmlspecialchars($m['name']) ?></p>
                    <?php if (!empty($m['email'])): ?>
                    <p class="org-email-member"><?= htmlspecialchars($m['email']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="cta-section">
    <div class="container">
        <h2>Want to See Your Name Here?</h2>
        <p>We recruit new members every semester.</p>
        <a href="<?= url('join.php') ?>" class="btn-dark">How to Join</a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
