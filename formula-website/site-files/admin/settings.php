<?php
require_once __DIR__ . '/includes/auth.php';
auth_require();

$adminTitle     = 'Site';
$adminTitleSpan = 'Settings';
$msg = '';

// Detect oversized POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && empty($_FILES)) {
    $msg = ['type'=>'danger','text'=>"Upload too large! Server limit is " . (ini_get('upload_max_filesize') ?: '2M') . "."];
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    $current = data_load('settings.json');

    // ── Link site image from media ────────────────────────────
    if (isset($_POST['link_site_image'])) {
        $slot = $_POST['image_slot'] ?? '';
        $filename = trim($_POST['linked_file'] ?? '');
        $folder = trim($_POST['linked_folder'] ?? 'site');
        $valid = ['join_hero','contact_building','og_image','favicon','nav_logo_dark','nav_logo_light'];
        if (in_array($slot, $valid) && $filename) {
            $base = dirname(dirname(__FILE__)) . '/assets/images/';
            $src = $base . $folder . '/' . $filename;
            $dest = $base . 'site/' . $filename;
            if (file_exists($src)) {
                if ($src !== $dest) { @copy($src, $dest); @chmod($dest, 0644); }
                $current['site_images'] = $current['site_images'] ?? [];
                $current['site_images'][$slot] = $filename;
                data_save('settings.json', $current);
                $msg = ['type'=>'success','text'=>'Image updated.'];
            }
        }
        $current = data_load('settings.json');
    }
    // ── Delete site image ─────────────────────────────────────
    elseif (isset($_POST['delete_site_image'])) {
        $slot = $_POST['image_slot'] ?? '';
        if (!empty($current['site_images'][$slot])) {
            $path = dirname(dirname(__FILE__)) . '/assets/images/site/' . $current['site_images'][$slot];
            if (file_exists($path)) @unlink($path);
            unset($current['site_images'][$slot]);
            data_save('settings.json', $current);
            $msg = ['type'=>'success','text'=>'Image removed.'];
        }
        $current = data_load('settings.json');
    }
    // ── Sponsor packet upload ─────────────────────────────────
    elseif (isset($_POST['upload_sponsor_packet'])) {
        if (!empty($_FILES['sponsor_packet']['name'])) {
            $ext = strtolower(pathinfo($_FILES['sponsor_packet']['name'], PATHINFO_EXTENSION));
            if ($ext !== 'pdf') {
                $msg = ['type'=>'danger','text'=>'Only PDF files are allowed.'];
            } else {
                $dest_dir = dirname(dirname(__FILE__)) . '/assets/files/';
                if (!is_dir($dest_dir)) @mkdir($dest_dir, 0755, true);
                $dest = $dest_dir . 'sponsor-packet.pdf';
                if (move_uploaded_file($_FILES['sponsor_packet']['tmp_name'], $dest)) {
                    @chmod($dest, 0644);
                    $current['sponsor_packet_file'] = 'sponsor-packet.pdf';
                    data_save('settings.json', $current);
                    $msg = ['type'=>'success','text'=>'Sponsor packet uploaded.'];
                }
            }
        }
    }
    elseif (isset($_POST['delete_sponsor_packet'])) {
        $pf = $current['sponsor_packet_file'] ?? '';
        if ($pf) {
            $path = dirname(dirname(__FILE__)) . '/assets/files/' . $pf;
            if (file_exists($path)) @unlink($path);
            unset($current['sponsor_packet_file']);
            data_save('settings.json', $current);
            $msg = ['type'=>'success','text'=>'Sponsor packet removed.'];
        }
    }
    // ── Save footer ───────────────────────────────────────────
    elseif (isset($_POST['save_footer'])) {
        $current['footer_tagline']   = trim($_POST['footer_tagline'] ?? '');
        $current['footer_subtitle']  = trim($_POST['footer_subtitle'] ?? '');
        $current['footer_copyright'] = trim($_POST['footer_copyright'] ?? '');
        $current['footer_links_1_title'] = trim($_POST['footer_links_1_title'] ?? 'Quick Links');
        $current['footer_links_2_title'] = trim($_POST['footer_links_2_title'] ?? 'Get Involved');
        $links1 = []; $links2 = [];
        foreach ($_POST['fl1_label'] ?? [] as $i => $lab) {
            $lab = trim($lab); $pg = trim($_POST['fl1_page'][$i] ?? '');
            if ($lab) $links1[] = ['label'=>$lab, 'page'=>$pg];
        }
        foreach ($_POST['fl2_label'] ?? [] as $i => $lab) {
            $lab = trim($lab); $pg = trim($_POST['fl2_page'][$i] ?? '');
            if ($lab) $links2[] = ['label'=>$lab, 'page'=>$pg];
        }
        $current['footer_links_1'] = $links1;
        $current['footer_links_2'] = $links2;
        data_save('settings.json', $current);
        $msg = ['type'=>'success','text'=>'Footer saved.'];
    }
    // ── Save all text settings (single form) ──────────────────
    elseif (isset($_POST['save_settings'])) {
        $times = [];
        foreach ($_POST['meeting_day'] ?? [] as $i => $d) {
            $d = trim($d); $t = trim($_POST['meeting_time'][$i] ?? '');
            if ($d && $t) $times[$d] = $t;
        }
        $current['site_name']        = trim($_POST['site_name'] ?? '');
        $current['contact_email']    = trim($_POST['contact_email'] ?? '');
        $current['latest_car']       = trim($_POST['latest_car'] ?? '');
        $current['latest_result']    = trim($_POST['latest_result'] ?? '');
        $current['biggest_win']      = trim($_POST['biggest_win'] ?? '');
        $current['meeting_location'] = trim($_POST['meeting_location'] ?? '');
        $current['meeting_times']    = $times;
        $current['social_instagram'] = trim($_POST['social_instagram'] ?? '');
        $current['social_facebook']  = trim($_POST['social_facebook'] ?? '');
        $current['social_twitter']   = trim($_POST['social_twitter'] ?? '');
        $current['social_youtube']   = trim($_POST['social_youtube'] ?? '');
        $current['social_linkedin']  = trim($_POST['social_linkedin'] ?? '');
        $current['google_analytics_id'] = trim($_POST['google_analytics_id'] ?? '');
        $current['achievement_banner_show']  = isset($_POST['achievement_banner_show']) ? 1 : 0;
        $current['achievement_banner_label'] = trim($_POST['achievement_banner_label'] ?? '');
        $current['achievement_banner_text']  = trim($_POST['achievement_banner_text'] ?? '');
        if (data_save('settings.json', $current)) {
            $msg = ['type'=>'success','text'=>'Settings saved.'];
        }
    }
}

$s = data_load('settings.json');
$site_images = $s['site_images'] ?? [];
$images_dir = dirname(dirname(__FILE__)) . '/assets/images/site';
$meeting_times = $s['meeting_times'] ?? ['Tuesday'=>'8:00 PM','Thursday'=>'8:00 PM','Saturday'=>'10:00 AM'];

// Image card renderer
function render_img(string $slot, string $label, string $help, array $si, string $dir, string $fit = 'cover'): void {
    $f = $si[$slot] ?? ''; $exists = $f && file_exists($dir.'/'.$f);
    $cid = 'img-'.str_replace('_','-',$slot);
    ?>
    <div class="img-slot-card">
        <div class="img-slot-card-label"><?= $label ?></div>
        <?php if ($exists): ?>
        <div class="img-slot-card-preview">
            <img src="../assets/images/site/<?= htmlspecialchars($f) ?>" alt="" class="img-slot-card-img<?= $fit==='contain'?' contain':'' ?>">
            <div class="img-slot-card-meta">
                <span class="img-slot-card-filename"><?= htmlspecialchars($f) ?></span>
                <form method="POST" class="form-inline" onsubmit="return confirm('Remove?')">
                    <?= csrf_field() ?><input type="hidden" name="delete_site_image" value="1"><input type="hidden" name="image_slot" value="<?= $slot ?>">
                    <button type="submit" class="btn btn-danger btn-sm btn-xs"><i class="fa fa-trash"></i></button>
                </form>
            </div>
        </div>
        <?php else: ?>
        <div class="img-slot-empty-sm"><i class="fa fa-image" style="margin-right:.4rem;"></i> No image set</div>
        <?php endif; ?>
        <button type="button" class="btn-browse btn-browse-full" id="<?= $cid ?>-btn"><i class="fa fa-images"></i> Browse Media</button>
        <div class="img-slot-card-help"><?= $help ?></div>
        <form method="POST" id="<?= $cid ?>-form" class="form-inline" style="display:none;">
            <?= csrf_field() ?><input type="hidden" name="link_site_image" value="1"><input type="hidden" name="image_slot" value="<?= $slot ?>">
            <input type="hidden" name="linked_file" id="<?= $cid ?>-file" value=""><input type="hidden" name="linked_folder" id="<?= $cid ?>-folder" value="">
        </form>
        <script>document.getElementById('<?= $cid ?>-btn').addEventListener('click',function(e){e.preventDefault();pickerOpen('all',function(file,folder){document.getElementById('<?= $cid ?>-file').value=file;document.getElementById('<?= $cid ?>-folder').value=folder;document.getElementById('<?= $cid ?>-form').submit();},'<?= htmlspecialchars($f) ?>');});</script>
    </div>
    <?php
}

include 'includes/admin-header.php';
?>

<?php if ($msg): ?>
<div class="alert alert-<?= $msg['type'] ?>"><i class="fa fa-<?= $msg['type']==='success'?'check-circle':'exclamation-circle' ?>"></i> <?= htmlspecialchars($msg['text']) ?></div>
<?php endif; ?>

<!-- Single form for all text settings — no more multi-form sync issues -->
<form method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="save_settings" value="1">

    <!-- General -->
    <div class="admin-card">
        <div class="admin-card-header"><h2><i class="fa fa-building" style="margin-right:.4rem;"></i> Site Info</h2></div>
        <div class="admin-card-body">
            <div class="form-grid">
                <div class="form-group">
                    <label>Site Name</label>
                    <input type="text" name="site_name" value="<?= htmlspecialchars($s['site_name']??'RIT Racing') ?>">
                    <span class="form-help">Browser tab and SEO</span>
                </div>
                <div class="form-group">
                    <label>Contact Email</label>
                    <input type="email" name="contact_email" value="<?= htmlspecialchars($s['contact_email']??'') ?>" placeholder="formula@rit.edu">
                    <span class="form-help">Footer and contact page</span>
                </div>
                <div class="form-group form-full">
                    <label><i class="fa fa-chart-line" style="color:var(--orange);margin-right:.3rem;"></i> Google Analytics Measurement ID</label>
                    <input type="text" name="google_analytics_id" value="<?= htmlspecialchars($s['google_analytics_id']??'') ?>" placeholder="G-XXXXXXXXXX">
                    <span class="form-help">Get this from <a href="https://analytics.google.com" target="_blank" style="color:var(--orange);">Google Analytics</a> → Admin → Data Streams → Measurement ID. Leave empty to disable tracking.</span>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header"><h2><i class="fa fa-trophy" style="margin-right:.4rem;"></i> Competition</h2></div>
        <div class="admin-card-body">
            <div class="form-grid">
                <div class="form-group">
                    <label>Current / Latest Car</label>
                    <input type="text" name="latest_car" value="<?= htmlspecialchars($s['latest_car']??'') ?>" placeholder="e.g. F34">
                </div>
                <div class="form-group">
                    <label>Latest Result</label>
                    <input type="text" name="latest_result" value="<?= htmlspecialchars($s['latest_result']??'') ?>" placeholder="e.g. 8th Place Overall — FSAE Michigan 2025">
                </div>
                <div class="form-group form-full">
                    <label>Biggest / Featured Win</label>
                    <input type="text" name="biggest_win" value="<?= htmlspecialchars($s['biggest_win']??'') ?>" placeholder="e.g. 1st Place Overall — FSAE Michigan 2024 (F32)">
                </div>
            </div>

            <!-- Historic Achievement Banner (Electric Cars Page) -->
            <div class="subsection-divider">
                <label class="flex-row" style="margin-bottom:.75rem;cursor:pointer;">
                    <input type="checkbox" name="achievement_banner_show" <?= !empty($s['achievement_banner_show']) ? 'checked' : '' ?> style="accent-color:var(--orange);">
                    <strong style="color:var(--white);font-size:.85rem;">Show Historic Achievement Banner on Electric Cars page</strong>
                </label>
                <p class="section-note" style="margin-bottom:.75rem;">Orange trophy banner displayed at the top of the Electric Cars page. Uncheck to hide it entirely.</p>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Banner Label</label>
                        <input type="text" name="achievement_banner_label" value="<?= htmlspecialchars($s['achievement_banner_label'] ?? 'Historic Achievement') ?>" placeholder="e.g. Historic Achievement">
                        <span class="form-help">Small orange text above the main banner text.</span>
                    </div>
                    <div class="form-group">
                        <label>Banner Text</label>
                        <input type="text" name="achievement_banner_text" value="<?= htmlspecialchars($s['achievement_banner_text'] ?? 'F32 (2023–2024) claimed 1st Place Overall at FSAE Michigan 2024 — the first Michigan win in RIT Racing\'s 33-year history.') ?>" placeholder="e.g. F32 claimed 1st Place at FSAE Michigan 2024...">
                        <span class="form-help">The full achievement sentence shown on the banner.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Meeting Times -->
    <div class="admin-card">
        <div class="admin-card-header"><h2><i class="fa fa-calendar" style="margin-right:.4rem;"></i> Meeting Times</h2></div>
        <div class="admin-card-body">
            <div id="meeting-times" class="list-col-sm">
                <?php foreach ($meeting_times as $day => $time): ?>
                <div class="meeting-row">
                    <input type="text" name="meeting_day[]" value="<?= htmlspecialchars($day) ?>" placeholder="Day" style="width:130px;">
                    <input type="text" name="meeting_time[]" value="<?= htmlspecialchars($time) ?>" placeholder="8:00 PM" style="flex:1;">
                    <button type="button" onclick="this.closest('.meeting-row').remove()" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-minus"></i></button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" onclick="addMeetingRow()" class="btn btn-secondary btn-sm"><i class="fa fa-plus"></i> Add Time</button>
            <div class="form-group" style="margin-top:1rem;">
                <label>Meeting Location</label>
                <input type="text" name="meeting_location" value="<?= htmlspecialchars($s['meeting_location']??'') ?>" placeholder="Kate Gleason College of Engineering — Bldg 9, Rm 2360">
            </div>
        </div>
    </div>

    <!-- Social -->
    <div class="admin-card">
        <div class="admin-card-header"><h2><i class="fa fa-share-nodes" style="margin-right:.4rem;"></i> Social Media</h2></div>
        <div class="admin-card-body">
            <div class="form-grid">
                <div class="form-group"><label><i class="fab fa-instagram" style="color:#E4405F;margin-right:.3rem"></i> Instagram</label><input type="url" name="social_instagram" value="<?= htmlspecialchars($s['social_instagram']??'') ?>" placeholder="https://instagram.com/..."></div>
                <div class="form-group"><label><i class="fab fa-facebook" style="color:#1877F2;margin-right:.3rem"></i> Facebook</label><input type="url" name="social_facebook" value="<?= htmlspecialchars($s['social_facebook']??'') ?>" placeholder="https://facebook.com/..."></div>
                <div class="form-group"><label><i class="fab fa-x-twitter" style="margin-right:.3rem"></i> Twitter / X</label><input type="url" name="social_twitter" value="<?= htmlspecialchars($s['social_twitter']??'') ?>" placeholder="https://twitter.com/..."></div>
                <div class="form-group"><label><i class="fab fa-youtube" style="color:#FF0000;margin-right:.3rem"></i> YouTube</label><input type="url" name="social_youtube" value="<?= htmlspecialchars($s['social_youtube']??'') ?>" placeholder="https://youtube.com/..."></div>
                <div class="form-group"><label><i class="fab fa-linkedin" style="color:#0A66C2;margin-right:.3rem"></i> LinkedIn</label><input type="url" name="social_linkedin" value="<?= htmlspecialchars($s['social_linkedin']??'') ?>" placeholder="https://linkedin.com/..."></div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Settings</button>
    </div>
</form>

<!-- Images (separate from the form above) -->
<div class="admin-card">
    <div class="admin-card-header"><h2><i class="fa fa-image" style="margin-right:.4rem;"></i> Site Images</h2></div>
    <div class="admin-card-body">
        <p class="section-note">Site-wide images. Homepage and About page images are managed from their own editors.</p>
        <div class="img-grid">
            <?php
            render_img('join_hero', 'Join / Recruitment Photo', 'New member day or hands-on activity.', $site_images, $images_dir);
            render_img('contact_building', 'Contact Page Building', 'KGCOE building exterior.', $site_images, $images_dir);
            ?>
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header"><h2><i class="fa fa-palette" style="margin-right:.4rem;"></i> Branding & Logo</h2></div>
    <div class="admin-card-body">
        <div class="img-grid">
            <?php
            render_img('nav_logo_dark', 'Nav Logo — Dark Mode', 'Light/white logo for dark backgrounds.', $site_images, $images_dir, 'contain');
            render_img('nav_logo_light', 'Nav Logo — Light Mode', 'Dark logo for light backgrounds.', $site_images, $images_dir, 'contain');
            render_img('og_image', 'Social Share Image (OG)', '~1200×630px. Shown on social media.', $site_images, $images_dir);
            render_img('favicon', 'Favicon', 'Browser tab icon. SVG or PNG ~32×32px.', $site_images, $images_dir, 'contain');
            ?>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="admin-card">
    <div class="admin-card-header"><h2><i class="fa fa-shoe-prints" style="margin-right:.4rem;"></i> Footer</h2></div>
    <div class="admin-card-body">
        <p class="section-note">Edit the footer text, link columns, and copyright line. Social links and contact info are pulled from the fields above.</p>
        <form method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="save_footer" value="1">
            <div class="form-grid">
                <div class="form-group"><label>Tagline</label><input type="text" name="footer_tagline" value="<?= htmlspecialchars($s['footer_tagline']??'Kate Gleason College of Engineering') ?>"><span class="form-help">Shown below the logo</span></div>
                <div class="form-group"><label>Subtitle</label><input type="text" name="footer_subtitle" value="<?= htmlspecialchars($s['footer_subtitle']??'Rochester Institute of Technology') ?>"></div>
                <div class="form-group form-full"><label>Copyright Text</label><input type="text" name="footer_copyright" value="<?= htmlspecialchars($s['footer_copyright']??'RIT Racing. All rights reserved.') ?>"><span class="form-help">Year is added automatically</span></div>
            </div>

            <div class="footer-cols">
                <!-- Column 1 -->
                <div>
                    <label style="font-weight:600;margin-bottom:.5rem;display:block;">Column 1 Title</label>
                    <input type="text" name="footer_links_1_title" value="<?= htmlspecialchars($s['footer_links_1_title']??'Quick Links') ?>" class="footer-col-title-input">
                    <div id="footer-col1" class="footer-col-links">
                        <?php foreach ($s['footer_links_1'] ?? [] as $lk): ?>
                        <div class="flink-row">
                            <input type="text" name="fl1_label[]" value="<?= htmlspecialchars($lk['label']??'') ?>" placeholder="Label" style="flex:1;">
                            <input type="text" name="fl1_page[]" value="<?= htmlspecialchars($lk['page']??'') ?>" placeholder="page.php" style="width:140px;font-size:.8rem;">
                            <button type="button" onclick="this.closest('.flink-row').remove()" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-minus"></i></button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" onclick="addFooterLink('footer-col1','fl1')" class="btn btn-secondary btn-sm" style="margin-top:.4rem;"><i class="fa fa-plus"></i></button>
                </div>
                <!-- Column 2 -->
                <div>
                    <label style="font-weight:600;margin-bottom:.5rem;display:block;">Column 2 Title</label>
                    <input type="text" name="footer_links_2_title" value="<?= htmlspecialchars($s['footer_links_2_title']??'Get Involved') ?>" class="footer-col-title-input">
                    <div id="footer-col2" class="footer-col-links">
                        <?php foreach ($s['footer_links_2'] ?? [] as $lk): ?>
                        <div class="flink-row">
                            <input type="text" name="fl2_label[]" value="<?= htmlspecialchars($lk['label']??'') ?>" placeholder="Label" style="flex:1;">
                            <input type="text" name="fl2_page[]" value="<?= htmlspecialchars($lk['page']??'') ?>" placeholder="page.php" style="width:140px;font-size:.8rem;">
                            <button type="button" onclick="this.closest('.flink-row').remove()" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-minus"></i></button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" onclick="addFooterLink('footer-col2','fl2')" class="btn btn-secondary btn-sm" style="margin-top:.4rem;"><i class="fa fa-plus"></i></button>
                </div>
            </div>
            <div class="form-actions" style="margin-top:1rem;">
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Footer</button>
            </div>
        </form>
    </div>
</div>

<script>
function addFooterLink(containerId, prefix) {
    var wrap = document.getElementById(containerId);
    var row = document.createElement('div');
    row.className = 'flink-row';
    row.innerHTML = '<input type="text" name="'+prefix+'_label[]" placeholder="Label" style="flex:1;">' +
        '<input type="text" name="'+prefix+'_page[]" placeholder="page.php" style="width:140px;font-size:.8rem;">' +
        '<button type="button" onclick="this.closest(\'.flink-row\').remove()" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-minus"></i></button>';
    wrap.appendChild(row);
}
</script>

<!-- Sponsor Packet -->
<div class="admin-card">
    <div class="admin-card-header"><h2><i class="fa fa-file-pdf" style="margin-right:.4rem;"></i> Sponsor Packet PDF</h2></div>
    <div class="admin-card-body">
        <?php
        $packetFile = $s['sponsor_packet_file'] ?? '';
        $packetPath = dirname(dirname(__FILE__)) . '/assets/files/' . $packetFile;
        if ($packetFile && file_exists($packetPath)):
        ?>
        <div class="file-info-box">
            <i class="fa fa-file-pdf file-info-icon"></i>
            <div class="file-info-body">
                <strong><?= htmlspecialchars($packetFile) ?></strong>
                <div class="file-info-meta"><?= round(filesize($packetPath)/1024) ?> KB</div>
            </div>
            <a href="../assets/files/<?= htmlspecialchars($packetFile) ?>" target="_blank" class="btn btn-secondary btn-sm"><i class="fa fa-download"></i> View</a>
            <form method="POST" class="form-inline" onsubmit="return confirm('Remove packet?')">
                <?= csrf_field() ?><input type="hidden" name="delete_sponsor_packet" value="1">
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Remove</button>
            </form>
        </div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?><input type="hidden" name="upload_sponsor_packet" value="1">
            <div class="upload-row">
                <div class="form-group" style="flex:1;min-width:200px;">
                    <label><?= ($packetFile && file_exists($packetPath)) ? 'Replace' : 'Upload' ?> Sponsor Packet</label>
                    <input type="file" name="sponsor_packet" accept=".pdf,application/pdf" required>
                    <span class="form-help">PDF only.</span>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-bottom:.4rem;"><i class="fa fa-upload"></i> Upload</button>
            </div>
        </form>
    </div>
</div>

<script>
function addMeetingRow() {
    var wrap = document.getElementById('meeting-times');
    var row = document.createElement('div');
    row.className = 'meeting-row';
    row.innerHTML = '<input type="text" name="meeting_day[]" placeholder="Day" style="width:130px;">' +
                    '<input type="text" name="meeting_time[]" placeholder="8:00 PM" style="flex:1;">' +
                    '<button type="button" onclick="this.closest(\'.meeting-row\').remove()" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-minus"></i></button>';
    wrap.appendChild(row);
}
</script>

<?php include 'includes/admin-footer.php'; ?>
