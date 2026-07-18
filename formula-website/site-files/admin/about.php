<?php
require_once __DIR__ . '/includes/auth.php';
auth_require();

$adminTitle     = 'About';
$adminTitleSpan = 'Page';
$msg = '';

$about = data_load('about.json');
if (!is_array($about)) $about = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {

    // Save sections text
    if (isset($_POST['save_sections'])) {
        $about['hero_eyebrow']    = trim($_POST['hero_eyebrow'] ?? '');
        $about['hero_title_1']    = trim($_POST['hero_title_1'] ?? '');
        $about['hero_title_2']    = trim($_POST['hero_title_2'] ?? '');
        $about['hero_subtitle']   = trim($_POST['hero_subtitle'] ?? '');
        $about['section1_label']  = trim($_POST['section1_label'] ?? '');
        $about['section1_title_1']= trim($_POST['section1_title_1'] ?? '');
        $about['section1_title_2']= trim($_POST['section1_title_2'] ?? '');
        $about['section1_text']   = trim($_POST['section1_text'] ?? '');
        $about['section2_label']  = trim($_POST['section2_label'] ?? '');
        $about['section2_title_1']= trim($_POST['section2_title_1'] ?? '');
        $about['section2_title_2']= trim($_POST['section2_title_2'] ?? '');
        $about['section2_text']   = trim($_POST['section2_text'] ?? '');
        $about['section2_quote']  = trim($_POST['section2_quote'] ?? '');
        $about['section3_label']  = trim($_POST['section3_label'] ?? '');
        $about['section3_title_1']= trim($_POST['section3_title_1'] ?? '');
        $about['section3_title_2']= trim($_POST['section3_title_2'] ?? '');
        $about['section3_text']   = trim($_POST['section3_text'] ?? '');
        data_save('about.json', $about);
        $msg = ['type'=>'success','text'=>'Sections saved.'];
    }

    // Save stats
    elseif (isset($_POST['save_stats'])) {
        $stats = [];
        foreach ($_POST['stat_number'] ?? [] as $i => $num) {
            $num   = trim($num);
            $label = trim($_POST['stat_label'][$i] ?? '');
            if ($num && $label) $stats[] = ['number'=>$num, 'label'=>$label];
        }
        $about['stats'] = $stats;
        data_save('about.json', $about);
        $msg = ['type'=>'success','text'=>'Stats saved.'];
    }

    // Save milestones
    elseif (isset($_POST['save_milestones'])) {
        $milestones = [];
        foreach ($_POST['ms_year'] ?? [] as $i => $year) {
            $year  = trim($year);
            $title = trim($_POST['ms_title'][$i] ?? '');
            $desc  = trim($_POST['ms_desc'][$i] ?? '');
            if ($year && $title) $milestones[] = ['year'=>$year,'title'=>$title,'description'=>$desc];
        }
        $about['milestones'] = $milestones;
        data_save('about.json', $about);
        $msg = ['type'=>'success','text'=>'Milestones saved.'];
    }

    // Link about image from media
    elseif (isset($_POST['link_about_image'])) {
        $slot = $_POST['image_slot'] ?? '';
        $filename = trim($_POST['linked_file'] ?? '');
        $folder = trim($_POST['linked_folder'] ?? 'site');
        $valid = ['about_heritage','about_shop','about_competition'];
        if (in_array($slot, $valid) && $filename) {
            $base = dirname(dirname(__FILE__)) . '/assets/images/';
            $src = $base . $folder . '/' . $filename;
            $dest = $base . 'site/' . $filename;
            if (file_exists($src)) {
                if ($src !== $dest) { @copy($src, $dest); @chmod($dest, 0644); }
                $ss = data_load('settings.json');
                $ss['site_images'] = $ss['site_images'] ?? [];
                $ss['site_images'][$slot] = $filename;
                data_save('settings.json', $ss);
                $msg = ['type'=>'success','text'=>'Image updated.'];
            }
        }
    }

    // Delete about image
    elseif (isset($_POST['delete_about_image'])) {
        $slot = $_POST['image_slot'] ?? '';
        $ss = data_load('settings.json');
        if (!empty($ss['site_images'][$slot])) {
            $path = dirname(dirname(__FILE__)) . '/assets/images/site/' . $ss['site_images'][$slot];
            if (file_exists($path)) @unlink($path);
            unset($ss['site_images'][$slot]);
            data_save('settings.json', $ss);
            $msg = ['type'=>'success','text'=>'Image removed.'];
        }
    }

    $about = data_load('about.json');
}

include 'includes/admin-header.php';
?>

<?php if ($msg): ?>
<div class="alert alert-<?= $msg['type'] ?>"><i class="fa fa-<?= $msg['type']==='success'?'check-circle':'exclamation-circle' ?>"></i> <?= htmlspecialchars($msg['text']) ?></div>
<?php endif; ?>

<!-- Tabs -->
<div class="page-tabs">
    <button class="page-tab active" onclick="showTab('sections')"><i class="fa fa-align-left"></i> Content</button>
    <button class="page-tab" onclick="showTab('stats')"><i class="fa fa-chart-bar"></i> Stats Bar</button>
    <button class="page-tab" onclick="showTab('milestones')"><i class="fa fa-timeline"></i> Milestones</button>
    <button class="page-tab" onclick="showTab('images')"><i class="fa fa-image"></i> Images</button>
</div>

<!-- ═══ CONTENT ═══ -->
<div class="tab-panel active" id="panel-sections">
<p class="section-intro"><i class="fa fa-align-left"></i> Edit the text content for each section of the About page. Photos are managed from <a href="settings.php#images" style="color:var(--orange);">Settings → Images</a>.</p>
<form method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="save_sections" value="1">

    <div class="admin-card">
        <div class="admin-card-header"><h2>Hero Banner</h2></div>
        <div class="admin-card-body">
            <div class="form-grid">
                <div class="form-group">
                    <label>Eyebrow</label>
                    <input type="text" name="hero_eyebrow" value="<?= htmlspecialchars($about['hero_eyebrow']??'Est. 1991') ?>">
                </div>
                <div class="form-group">
                    <label>Title Line 1</label>
                    <input type="text" name="hero_title_1" value="<?= htmlspecialchars($about['hero_title_1']??'Our') ?>">
                </div>
                <div class="form-group">
                    <label>Title Line 2 <span style="color:var(--orange);">(orange)</span></label>
                    <input type="text" name="hero_title_2" value="<?= htmlspecialchars($about['hero_title_2']??'Story') ?>">
                </div>
                <div class="form-group form-full">
                    <label>Subtitle</label>
                    <input type="text" name="hero_subtitle" value="<?= htmlspecialchars($about['hero_subtitle']??'') ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header"><h2>Section 1 — History (Left text, Right photo)</h2></div>
        <div class="admin-card-body">
            <div class="form-grid">
                <div class="form-group"><label>Label</label><input type="text" name="section1_label" value="<?= htmlspecialchars($about['section1_label']??'The Beginning') ?>"></div>
                <div class="form-group"><label>Title Part 1</label><input type="text" name="section1_title_1" value="<?= htmlspecialchars($about['section1_title_1']??'Born in the') ?>"></div>
                <div class="form-group"><label>Title Part 2 <span style="color:var(--orange);">(orange)</span></label><input type="text" name="section1_title_2" value="<?= htmlspecialchars($about['section1_title_2']??'Shop') ?>"></div>
                <div class="form-group form-full">
                    <label>Body Text</label>
                    <textarea name="section1_text" rows="6"><?= htmlspecialchars($about['section1_text']??'') ?></textarea>
                    <span class="form-help">Separate paragraphs with a blank line.</span>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header"><h2>Section 2 — What We Do (Left photo, Right text)</h2></div>
        <div class="admin-card-body">
            <div class="form-grid">
                <div class="form-group"><label>Label</label><input type="text" name="section2_label" value="<?= htmlspecialchars($about['section2_label']??'What We Do') ?>"></div>
                <div class="form-group"><label>Title Part 1</label><input type="text" name="section2_title_1" value="<?= htmlspecialchars($about['section2_title_1']??'Design. Build.') ?>"></div>
                <div class="form-group"><label>Title Part 2 <span style="color:var(--orange);">(orange)</span></label><input type="text" name="section2_title_2" value="<?= htmlspecialchars($about['section2_title_2']??'Race.') ?>"></div>
                <div class="form-group form-full">
                    <label>Body Text</label>
                    <textarea name="section2_text" rows="6"><?= htmlspecialchars($about['section2_text']??'') ?></textarea>
                    <span class="form-help">Separate paragraphs with a blank line.</span>
                </div>
                <div class="form-group form-full">
                    <label>Quote</label>
                    <input type="text" name="section2_quote" value="<?= htmlspecialchars($about['section2_quote']??'') ?>" placeholder="The knowledge you gain here doesn't come from a textbook.">
                    <span class="form-help">Displayed as a pull-quote below the text. Leave empty to hide.</span>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header"><h2>Section 3 — Electric Era (Left text, Right photo)</h2></div>
        <div class="admin-card-body">
            <div class="form-grid">
                <div class="form-group"><label>Label</label><input type="text" name="section3_label" value="<?= htmlspecialchars($about['section3_label']??'The Electric Era') ?>"></div>
                <div class="form-group"><label>Title Part 1</label><input type="text" name="section3_title_1" value="<?= htmlspecialchars($about['section3_title_1']??'All In on') ?>"></div>
                <div class="form-group"><label>Title Part 2 <span style="color:var(--orange);">(orange)</span></label><input type="text" name="section3_title_2" value="<?= htmlspecialchars($about['section3_title_2']??'Electric') ?>"></div>
                <div class="form-group form-full">
                    <label>Body Text</label>
                    <textarea name="section3_text" rows="6"><?= htmlspecialchars($about['section3_text']??'') ?></textarea>
                    <span class="form-help">Separate paragraphs with a blank line.</span>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Content</button>
    </div>
</form>
</div>

<!-- ═══ STATS ═══ -->
<div class="tab-panel" id="panel-stats">
<p class="section-intro"><i class="fa fa-chart-bar"></i> The big numbers shown in the dark bar between the first two sections.</p>
<form method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="save_stats" value="1">
    <div class="admin-card">
        <div class="admin-card-header"><h2>Stats Bar</h2></div>
        <div class="admin-card-body">
            <div id="about-stats" class="list-col-md">
                <?php foreach ($about['stats'] ?? [] as $st): ?>
                <div class="stat-edit-row">
                    <input type="text" name="stat_number[]" value="<?= htmlspecialchars($st['number']??'') ?>" placeholder="Number" style="width:100px;">
                    <input type="text" name="stat_label[]" value="<?= htmlspecialchars($st['label']??'') ?>" placeholder="Label" style="flex:1;">
                    <button type="button" onclick="this.closest('.stat-edit-row').remove()" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-minus"></i></button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" onclick="addAboutStat()" class="btn btn-secondary btn-sm"><i class="fa fa-plus"></i> Add Stat</button>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Stats</button>
    </div>
</form>
</div>

<!-- ═══ MILESTONES ═══ -->
<div class="tab-panel" id="panel-milestones">
<p class="section-intro"><i class="fa fa-timeline"></i> Timeline entries shown in the milestones grid. Each has a year, title, and description.</p>
<form method="POST">
    <?= csrf_field() ?>
    <input type="hidden" name="save_milestones" value="1">
    <div class="admin-card">
        <div class="admin-card-header"><h2>Milestones (<?= count($about['milestones'] ?? []) ?>)</h2></div>
        <div class="admin-card-body">
            <div id="milestones-list" class="list-col-lg">
                <?php foreach ($about['milestones'] ?? [] as $ms): ?>
                <div class="milestone-row">
                    <input type="text" name="ms_year[]" value="<?= htmlspecialchars($ms['year']??'') ?>" placeholder="Year" style="width:80px;">
                    <input type="text" name="ms_title[]" value="<?= htmlspecialchars($ms['title']??'') ?>" placeholder="Title" style="width:180px;">
                    <textarea name="ms_desc[]" rows="2" placeholder="Description" style="flex:1;"><?= htmlspecialchars($ms['description']??'') ?></textarea>
                    <button type="button" onclick="this.closest('.milestone-row').remove()" class="btn btn-danger btn-sm btn-icon" style="margin-top:.25rem;"><i class="fa fa-trash"></i></button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" onclick="addMilestone()" class="btn btn-secondary btn-sm"><i class="fa fa-plus"></i> Add Milestone</button>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Milestones</button>
    </div>
</form>
</div>

<!-- ═══ IMAGES ═══ -->
<div class="tab-panel" id="panel-images">
<p class="section-intro"><i class="fa fa-image"></i> Photos for the About page sections. Upload in <a href="media.php" style="color:var(--orange);">Media Library</a> first, then pick below.</p>
<?php
$ss = data_load('settings.json');
$si = $ss['site_images'] ?? [];
$idir = dirname(dirname(__FILE__)) . '/assets/images/site';

function render_about_img(string $slot, string $label, string $help, array $si, string $idir): void {
    $f = $si[$slot] ?? '';
    $exists = $f && file_exists($idir.'/'.$f);
    $cid = 'aimg-'.str_replace('_','-',$slot);
    ?>
    <div class="img-slot-card">
        <div class="img-slot-card-label"><?= $label ?></div>
        <?php if ($exists): ?>
        <div class="img-slot-card-preview">
            <img src="../assets/images/site/<?= htmlspecialchars($f) ?>" alt="" class="img-slot-card-img" style="height:120px;">
            <div class="img-slot-card-meta">
                <span class="img-slot-card-filename"><?= htmlspecialchars($f) ?></span>
                <form method="POST" class="form-inline" onsubmit="return confirm('Remove?')">
                    <?= csrf_field() ?><input type="hidden" name="delete_about_image" value="1"><input type="hidden" name="image_slot" value="<?= $slot ?>">
                    <button type="submit" class="btn btn-danger btn-sm btn-xs"><i class="fa fa-trash"></i></button>
                </form>
            </div>
        </div>
        <?php else: ?>
        <div class="img-slot-empty-sm" style="height:120px;"><i class="fa fa-image" style="margin-right:.4rem;"></i> No image set</div>
        <?php endif; ?>
        <button type="button" class="btn-browse btn-browse-full" id="<?= $cid ?>-btn"><i class="fa fa-images"></i> Browse Media</button>
        <div class="img-slot-card-help"><?= $help ?></div>
        <form method="POST" id="<?= $cid ?>-form" style="display:none;">
            <?= csrf_field() ?><input type="hidden" name="link_about_image" value="1"><input type="hidden" name="image_slot" value="<?= $slot ?>">
            <input type="hidden" name="linked_file" id="<?= $cid ?>-file" value=""><input type="hidden" name="linked_folder" id="<?= $cid ?>-folder" value="">
        </form>
        <script>document.getElementById('<?= $cid ?>-btn').addEventListener('click',function(e){e.preventDefault();pickerOpen('all',function(file,folder){document.getElementById('<?= $cid ?>-file').value=file;document.getElementById('<?= $cid ?>-folder').value=folder;document.getElementById('<?= $cid ?>-form').submit();},'<?= htmlspecialchars($f) ?>');});</script>
    </div>
    <?php
}
?>
<div class="img-grid-lg">
    <?php
    render_about_img('about_heritage', 'Section 1 — Heritage Photo', 'Historic or early car photo. Shown next to the history text.', $si, $idir);
    render_about_img('about_shop', 'Section 2 — Shop Photo', 'Team working in the shop. Shown next to the "What We Do" text.', $si, $idir);
    render_about_img('about_competition', 'Section 3 — Competition Photo', 'Car on track or at competition. Shown next to the electric era text.', $si, $idir);
    ?>
</div>
</div>

<script>
function showTab(name) {
    document.querySelectorAll('.page-tab').forEach(function(t){ t.classList.remove('active'); });
    document.querySelectorAll('.tab-panel').forEach(function(p){ p.classList.remove('active'); });
    document.getElementById('panel-' + name).classList.add('active');
    document.querySelectorAll('.page-tab').forEach(function(t){
        if (t.onclick && t.onclick.toString().indexOf("'" + name + "'") !== -1) t.classList.add('active');
    });
    history.replaceState(null, '', '#' + name);
}
document.addEventListener('DOMContentLoaded', function() {
    var hash = window.location.hash.replace('#','');
    if (hash && document.getElementById('panel-' + hash)) showTab(hash);
});

function addAboutStat() {
    var wrap = document.getElementById('about-stats');
    var row = document.createElement('div');
    row.className = 'stat-edit-row';
    row.innerHTML = '<input type="text" name="stat_number[]" placeholder="Number" style="width:100px;">' +
                    '<input type="text" name="stat_label[]" placeholder="Label" style="flex:1;">' +
                    '<button type="button" onclick="this.closest(\'.stat-edit-row\').remove()" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-minus"></i></button>';
    wrap.appendChild(row);
}

function addMilestone() {
    var wrap = document.getElementById('milestones-list');
    var row = document.createElement('div');
    row.className = 'milestone-row';
    row.innerHTML = '<input type="text" name="ms_year[]" placeholder="Year" style="width:80px;">' +
                    '<input type="text" name="ms_title[]" placeholder="Title" style="width:180px;">' +
                    '<textarea name="ms_desc[]" rows="2" placeholder="Description" style="flex:1;"></textarea>' +
                    '<button type="button" onclick="this.closest(\'.milestone-row\').remove()" class="btn btn-danger btn-sm btn-icon" style="margin-top:.25rem;"><i class="fa fa-trash"></i></button>';
    wrap.appendChild(row);
}
</script>

<?php include 'includes/admin-footer.php'; ?>
