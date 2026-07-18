<?php
require_once __DIR__ . '/includes/auth.php';
auth_require();

$adminTitle     = 'Homepage';
$adminTitleSpan = 'Editor';
$msg = '';

$s = data_load('settings.json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {

    // Save hero text
    if (isset($_POST['save_hero'])) {
        $hero_stats = [];
        foreach ($_POST['hero_stat_number'] ?? [] as $i => $num) {
            $num    = trim($num);
            $suffix = trim($_POST['hero_stat_suffix'][$i] ?? '');
            $label  = trim($_POST['hero_stat_label'][$i] ?? '');
            if ($num && $label) $hero_stats[] = ['number'=>$num, 'suffix'=>$suffix, 'label'=>$label];
        }
        $s['hero_label']      = trim($_POST['hero_label'] ?? '');
        $s['hero_headline_1'] = trim($_POST['hero_headline_1'] ?? '');
        $s['hero_headline_2'] = trim($_POST['hero_headline_2'] ?? '');
        $s['hero_subtext']    = trim($_POST['hero_subtext'] ?? '');
        $s['hero_stats']      = $hero_stats;
        $s['about_strip_label']   = trim($_POST['about_strip_label'] ?? '');
        $s['about_strip_title_1'] = trim($_POST['about_strip_title_1'] ?? '');
        $s['about_strip_title_2'] = trim($_POST['about_strip_title_2'] ?? '');
        $s['about_strip_text']    = trim($_POST['about_strip_text'] ?? '');
        $s['about_strip_quote']   = trim($_POST['about_strip_quote'] ?? '');
        data_save('settings.json', $s);
        $msg = ['type'=>'success','text'=>'Homepage saved.'];
    }

    // Save countdown events
    elseif (isset($_POST['save_countdown'])) {
        $events = [];
        foreach ($_POST['cd_title'] ?? [] as $i => $title) {
            $title = trim($title);
            if (!$title) continue;
            $events[] = [
                'id'       => preg_replace('/[^a-z0-9]+/', '-', strtolower($title)),
                'title'    => $title,
                'subtitle' => trim($_POST['cd_subtitle'][$i] ?? ''),
                'date'     => trim($_POST['cd_date'][$i] ?? ''),
                'location' => trim($_POST['cd_location'][$i] ?? ''),
                'active'   => isset($_POST['cd_active'][$i]),
            ];
        }
        $s['countdown_events'] = $events;
        data_save('settings.json', $s);
        $msg = ['type'=>'success','text'=>'Countdown events saved.'];
        $s = data_load('settings.json');
    }

    // Link hero image from media
    elseif (isset($_POST['link_site_image'])) {
        $slot = $_POST['image_slot'] ?? '';
        $filename = trim($_POST['linked_file'] ?? '');
        $folder = trim($_POST['linked_folder'] ?? 'site');
        $valid = ['hero_bg','team_group','shop_atmosphere'];
        if (in_array($slot, $valid) && $filename) {
            $base = dirname(dirname(__FILE__)) . '/assets/images/';
            $src = $base . $folder . '/' . $filename;
            $dest = $base . 'site/' . $filename;
            if (file_exists($src)) {
                if ($src !== $dest) { @copy($src, $dest); @chmod($dest, 0644); }
                $s['site_images'] = $s['site_images'] ?? [];
                $s['site_images'][$slot] = $filename;
                data_save('settings.json', $s);
                $msg = ['type'=>'success','text'=>'Image updated.'];
            }
        }
        $s = data_load('settings.json');
    }

    // Delete image
    elseif (isset($_POST['delete_site_image'])) {
        $slot = $_POST['image_slot'] ?? '';
        if (!empty($s['site_images'][$slot])) {
            $path = dirname(dirname(__FILE__)) . '/assets/images/site/' . $s['site_images'][$slot];
            if (file_exists($path)) @unlink($path);
            unset($s['site_images'][$slot]);
            data_save('settings.json', $s);
            $msg = ['type'=>'success','text'=>'Image removed.'];
        }
        $s = data_load('settings.json');
    }
}

$site_images = $s['site_images'] ?? [];
$images_dir = dirname(dirname(__FILE__)) . '/assets/images/site';

include 'includes/admin-header.php';

// Reuse the image card renderer
function render_hp_image(string $slot, string $label, string $help, array $site_images, string $images_dir): void {
    $current_file = $site_images[$slot] ?? '';
    $file_path = $images_dir . '/' . $current_file;
    $has_image = $current_file && file_exists($file_path);
    $card_id = 'img-' . str_replace('_','-',$slot);
    ?>
    <div class="img-slot-card">
        <div class="img-slot-card-label"><?= $label ?></div>
        <?php if ($has_image): ?>
        <div class="img-slot-card-preview">
            <img src="../assets/images/site/<?= htmlspecialchars($current_file) ?>" alt="" class="img-slot-card-img">
            <div class="img-slot-card-meta">
                <span class="img-slot-card-filename"><?= htmlspecialchars($current_file) ?></span>
                <form method="POST" class="form-inline" onsubmit="return confirm('Remove?')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="delete_site_image" value="1">
                    <input type="hidden" name="image_slot" value="<?= $slot ?>">
                    <button type="submit" class="btn btn-danger btn-sm btn-xs"><i class="fa fa-trash"></i></button>
                </form>
            </div>
        </div>
        <?php else: ?>
        <div class="img-slot-empty-sm">
            <i class="fa fa-image" style="margin-right:.4rem;"></i> No image set
        </div>
        <?php endif; ?>
        <button type="button" class="btn-browse btn-browse-full" id="<?= $card_id ?>-btn"><i class="fa fa-images"></i> Browse Media</button>
        <div class="img-slot-card-help"><?= $help ?></div>
        <form method="POST" id="<?= $card_id ?>-form" style="display:none;">
            <?= csrf_field() ?>
            <input type="hidden" name="link_site_image" value="1">
            <input type="hidden" name="image_slot" value="<?= $slot ?>">
            <input type="hidden" name="linked_file" id="<?= $card_id ?>-file" value="">
            <input type="hidden" name="linked_folder" id="<?= $card_id ?>-folder" value="">
        </form>
        <script>
        document.getElementById('<?= $card_id ?>-btn').addEventListener('click', function(e) {
            e.preventDefault();
            pickerOpen('all', function(file, folder, path) {
                document.getElementById('<?= $card_id ?>-file').value = file;
                document.getElementById('<?= $card_id ?>-folder').value = folder;
                document.getElementById('<?= $card_id ?>-form').submit();
            }, '<?= htmlspecialchars($current_file) ?>');
        });
        </script>
    </div>
    <?php
}
?>

<?php if ($msg): ?>
<div class="alert alert-<?= $msg['type'] ?>"><i class="fa fa-<?= $msg['type']==='success'?'check-circle':'exclamation-circle' ?>"></i> <?= htmlspecialchars($msg['text']) ?></div>
<?php endif; ?>

<!-- Hero Images -->
<div class="admin-card">
    <div class="admin-card-header"><h2><i class="fa fa-image" style="margin-right:.4rem;"></i> Homepage Images</h2></div>
    <div class="admin-card-body">
        <p class="section-note">Upload photos in <a href="media.php" style="color:var(--orange);">Media Library</a> first, then pick below.</p>
        <div class="img-grid">
            <?php
            render_hp_image('hero_bg', 'Hero Background', 'Wide landscape, car at speed. ~1920×1080px.', $site_images, $images_dir);
            render_hp_image('team_group', 'Team Group Photo', 'Full team photo. Shown on homepage and team page.', $site_images, $images_dir);
            render_hp_image('shop_atmosphere', 'Shop / Atmosphere', 'Wide shop or build photo. Homepage CTA section.', $site_images, $images_dir);
            ?>
        </div>
    </div>
</div>

<!-- Hero Text -->
<form method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="save_hero" value="1">

    <div class="admin-card">
        <div class="admin-card-header"><h2><i class="fa fa-heading" style="margin-right:.4rem;"></i> Hero Text</h2></div>
        <div class="admin-card-body">
            <div class="form-grid">
                <div class="form-group form-full">
                    <label>Eyebrow Label</label>
                    <input type="text" name="hero_label" value="<?= htmlspecialchars($s['hero_label']??'Kate Gleason College of Engineering · Since 1991') ?>">
                    <span class="form-help">Small text above the headline</span>
                </div>
                <div class="form-group">
                    <label>Headline Line 1</label>
                    <input type="text" name="hero_headline_1" value="<?= htmlspecialchars($s['hero_headline_1']??'Built to') ?>">
                </div>
                <div class="form-group">
                    <label>Headline Line 2 <span style="color:var(--orange);">(orange)</span></label>
                    <input type="text" name="hero_headline_2" value="<?= htmlspecialchars($s['hero_headline_2']??'Win.') ?>">
                </div>
                <div class="form-group form-full">
                    <label>Subtext Paragraph</label>
                    <textarea name="hero_subtext" rows="3"><?= htmlspecialchars($s['hero_subtext']??'') ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header"><h2><i class="fa fa-chart-bar" style="margin-right:.4rem;"></i> Stats Bar</h2></div>
        <div class="admin-card-body">
            <p class="section-note">The big numbers below the hero (e.g. "34+ Years Competing").</p>
            <div id="hero-stats" class="list-col-md">
                <?php
                $hero_stats = $s['hero_stats'] ?? [];
                foreach ($hero_stats as $hs): ?>
                <div class="stat-edit-row">
                    <input type="text" name="hero_stat_number[]" value="<?= htmlspecialchars($hs['number']??'') ?>" placeholder="#" style="width:60px;">
                    <input type="text" name="hero_stat_suffix[]" value="<?= htmlspecialchars($hs['suffix']??'') ?>" placeholder="+" style="width:40px;">
                    <input type="text" name="hero_stat_label[]" value="<?= htmlspecialchars($hs['label']??'') ?>" placeholder="Label" style="flex:1;">
                    <button type="button" onclick="this.closest('.stat-edit-row').remove()" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-minus"></i></button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" onclick="addHeroStat()" class="btn btn-secondary btn-sm"><i class="fa fa-plus"></i> Add Stat</button>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header"><h2><i class="fa fa-users" style="margin-right:.4rem;"></i> About Strip — "Who We Are"</h2></div>
        <div class="admin-card-body">
            <p class="section-note">The section below the featured cars with team description and quote.</p>
            <div class="form-grid">
                <div class="form-group"><label>Section Label</label><input type="text" name="about_strip_label" value="<?= htmlspecialchars($s['about_strip_label']??'Who We Are') ?>"></div>
                <div class="form-group"><label>Title Part 1</label><input type="text" name="about_strip_title_1" value="<?= htmlspecialchars($s['about_strip_title_1']??'More Than a') ?>"></div>
                <div class="form-group"><label>Title Part 2 <span style="color:var(--orange);">(orange)</span></label><input type="text" name="about_strip_title_2" value="<?= htmlspecialchars($s['about_strip_title_2']??'Racing Team') ?>"></div>
                <div class="form-group form-full">
                    <label>Body Text</label>
                    <textarea name="about_strip_text" rows="5"><?= htmlspecialchars($s['about_strip_text']??'') ?></textarea>
                    <span class="form-help">Separate paragraphs with a blank line.</span>
                </div>
                <div class="form-group form-full">
                    <label>Pull Quote</label>
                    <input type="text" name="about_strip_quote" value="<?= htmlspecialchars($s['about_strip_quote']??'') ?>" placeholder="We don't just build cars — we build engineers.">
                    <span class="form-help">Leave empty to hide.</span>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Homepage</button>
    </div>
</form>

<!-- Countdown Events -->
<?php $cd_events = $s['countdown_events'] ?? []; ?>
<form method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="save_countdown" value="1">
    <div class="admin-card">
        <div class="admin-card-header">
            <h2><i class="fa fa-clock" style="margin-right:.4rem;"></i> Countdown Events</h2>
        </div>
        <div class="admin-card-body">
            <p class="section-note">Add upcoming events with countdown timers. Active events with future dates appear on the homepage. Past events are automatically hidden. Multiple events cycle every 8 seconds.</p>
            <div id="cd-events" class="list-col-lg">
                <?php foreach ($cd_events as $i => $ev): ?>
                <div class="cd-row">
                    <div class="flex-row" style="margin-bottom:.5rem;">
                        <label class="flex-row" style="cursor:pointer;margin:0;white-space:nowrap;">
                            <input type="checkbox" name="cd_active[<?= $i ?>]" <?= !empty($ev['active']) ? 'checked' : '' ?> style="accent-color:var(--orange);">
                            <span style="font-size:.7rem;color:var(--gray);text-transform:uppercase;letter-spacing:.08em;">Active</span>
                        </label>
                        <input type="text" name="cd_title[]" value="<?= htmlspecialchars($ev['title']??'') ?>" placeholder="Event Title" style="flex:1;font-weight:600;">
                        <button type="button" onclick="this.closest('.cd-row').remove()" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-trash"></i></button>
                    </div>
                    <div class="flex-row" style="flex-wrap:wrap;">
                        <input type="text" name="cd_subtitle[]" value="<?= htmlspecialchars($ev['subtitle']??'') ?>" placeholder="Subtitle (optional)" style="flex:1;min-width:140px;">
                        <input type="datetime-local" name="cd_date[]" value="<?= htmlspecialchars(str_replace(' ','T',substr($ev['date']??'',0,16))) ?>" style="width:210px;" required>
                        <input type="text" name="cd_location[]" value="<?= htmlspecialchars($ev['location']??'') ?>" placeholder="Location (optional)" style="flex:1;min-width:140px;">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" onclick="addCountdownEvent()" class="btn btn-secondary btn-sm"><i class="fa fa-plus"></i> Add Event</button>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Events</button>
    </div>
</form>

<script>
function addHeroStat() {
    var wrap = document.getElementById('hero-stats');
    var row = document.createElement('div');
    row.className = 'stat-edit-row';
    row.innerHTML = '<input type="text" name="hero_stat_number[]" placeholder="#" style="width:60px;">' +
                    '<input type="text" name="hero_stat_suffix[]" placeholder="+" style="width:40px;">' +
                    '<input type="text" name="hero_stat_label[]" placeholder="Label" style="flex:1;">' +
                    '<button type="button" onclick="this.closest(\'.stat-edit-row\').remove()" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-minus"></i></button>';
    wrap.appendChild(row);
}

var cdIndex = <?= count($cd_events) ?>;
function addCountdownEvent() {
    var wrap = document.getElementById('cd-events');
    var row = document.createElement('div');
    row.className = 'cd-row';
    row.innerHTML = '<div class="flex-row" style="margin-bottom:.5rem;">' +
        '<label class="flex-row" style="cursor:pointer;margin:0;white-space:nowrap;"><input type="checkbox" name="cd_active[' + cdIndex + ']" checked style="accent-color:var(--orange);"><span style="font-size:.7rem;color:var(--gray);text-transform:uppercase;letter-spacing:.08em;">Active</span></label>' +
        '<input type="text" name="cd_title[]" placeholder="Event Title" style="flex:1;font-weight:600;">' +
        '<button type="button" onclick="this.closest(\'.cd-row\').remove()" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-trash"></i></button>' +
        '</div><div class="flex-row" style="flex-wrap:wrap;">' +
        '<input type="text" name="cd_subtitle[]" placeholder="Subtitle (optional)" style="flex:1;min-width:140px;">' +
        '<input type="datetime-local" name="cd_date[]" style="width:210px;" required>' +
        '<input type="text" name="cd_location[]" placeholder="Location (optional)" style="flex:1;min-width:140px;">' +
        '</div>';
    wrap.appendChild(row);
    cdIndex++;
}
</script>

<?php include 'includes/admin-footer.php'; ?>
