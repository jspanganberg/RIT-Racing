<?php
require_once __DIR__ . '/includes/auth.php';
auth_require();

$adminTitle     = 'Programs';
$adminTitleSpan = 'Editor';
$msg = '';
$edit = null;

$programs = data_load('programs.json');
if (!is_array($programs)) $programs = [];

// Common FA icons for programs — grouped by category
$icon_options = [
    // Motion / Engineering
    'fa-wind','fa-bolt','fa-fire','fa-gauge','fa-tachometer-alt','fa-gears','fa-cogs',
    'fa-gear','fa-screwdriver-wrench','fa-wrench','fa-hammer','fa-tools',
    // Vehicles / Mechanical
    'fa-car','fa-car-side','fa-truck-monster','fa-motorcycle','fa-road',
    // Electronics / Tech
    'fa-microchip','fa-plug','fa-laptop-code','fa-code','fa-robot','fa-cpu',
    'fa-satellite','fa-wifi','fa-wave-square','fa-battery-full',
    // Design / Build
    'fa-drafting-compass','fa-ruler-combined','fa-ruler','fa-compass',
    'fa-layer-group','fa-cubes','fa-cube','fa-palette','fa-paint-brush',
    // Structure / Materials
    'fa-industry','fa-shield-halved','fa-shield','fa-hard-hat',
    'fa-shapes','fa-draw-polygon',
    // Business / People
    'fa-briefcase','fa-chart-line','fa-chart-bar','fa-users-gear','fa-users',
    'fa-handshake','fa-medal','fa-trophy','fa-flag-checkered','fa-star',
    // Data / Analysis
    'fa-magnifying-glass-chart','fa-sliders','fa-table','fa-database',
    'fa-calculator','fa-square-root-variable',
    // Misc useful
    'fa-leaf','fa-recycle','fa-stopwatch','fa-circle-nodes','fa-network-wired',
    'fa-arrow-trend-up','fa-bullseye','fa-crosshairs',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {

    // Delete
    if (isset($_POST['delete_id'])) {
        $del = trim($_POST['delete_id']);
        $programs = array_values(array_filter($programs, fn($p) => ($p['id']??'') !== $del));
        data_save('programs.json', $programs);
        $msg = ['type'=>'success','text'=>'Program removed.'];
    }
    // Save (add or edit)
    elseif (isset($_POST['save_program'])) {
        $id = trim($_POST['program_id'] ?? '') ?: strtolower(preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($_POST['title'] ?? 'program'))));
        $edit_id = trim($_POST['edit_id'] ?? '');

        $entry = [
            'id'          => $id,
            'icon'        => trim($_POST['icon'] ?? 'fa-gear'),
            'title'       => trim($_POST['title'] ?? ''),
            'summary'     => trim($_POST['summary'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'photo'       => resolve_media_image(trim($_POST['photo_existing'] ?? ''), 'programs'),
            'order'       => (int)($_POST['order'] ?? 99),
        ];

        if (empty($entry['title'])) {
            $msg = ['type'=>'danger','text'=>'Title is required.'];
        } else {
            if ($edit_id) {
                foreach ($programs as &$p) {
                    if (($p['id']??'') === $edit_id) { $p = array_merge($p, $entry); break; }
                }
                unset($p);
            } else {
                $programs[] = $entry;
            }
            usort($programs, fn($a,$b) => ($a['order']??99) <=> ($b['order']??99));
            data_save('programs.json', $programs);
            $msg = ['type'=>'success','text'=>$edit_id ? 'Program updated.' : 'Program added.'];
            header('Location: programs.php'); exit;
        }
    }
    // Reorder
    elseif (isset($_POST['move_id']) && isset($_POST['direction'])) {
        $move_id = trim($_POST['move_id']);
        $dir = $_POST['direction'];
        usort($programs, fn($a,$b) => ($a['order']??99) <=> ($b['order']??99));
        foreach ($programs as $i => $p) {
            if (($p['id']??'') === $move_id) {
                $swap = ($dir === 'up') ? $i - 1 : $i + 1;
                if (isset($programs[$swap])) {
                    $tmp = $programs[$i]['order'] ?? $i;
                    $programs[$i]['order'] = $programs[$swap]['order'] ?? $swap;
                    $programs[$swap]['order'] = $tmp;
                }
                break;
            }
        }
        usort($programs, fn($a,$b) => ($a['order']??99) <=> ($b['order']??99));
        data_save('programs.json', $programs);
        header('Location: programs.php'); exit;
    }
}

// Load for edit
if (isset($_GET['edit'])) {
    $eid = $_GET['edit'];
    foreach ($programs as $p) {
        if (($p['id']??'') === $eid) { $edit = $p; break; }
    }
}

usort($programs, fn($a,$b) => ($a['order']??99) <=> ($b['order']??99));

$topbarActions = '<a href="?new=1" class="btn btn-primary"><i class="fa fa-plus"></i> Add Program</a>';
include 'includes/admin-header.php';
?>

<?php if ($msg): ?>
<div class="alert alert-<?= $msg['type'] ?>"><i class="fa fa-<?= $msg['type']==='success'?'check-circle':'exclamation-circle' ?>"></i> <?= htmlspecialchars($msg['text']) ?></div>
<?php endif; ?>

<?php if (isset($_GET['new']) || $edit): ?>
<div class="admin-card">
    <div class="admin-card-header">
        <h2><?= $edit ? 'Edit Program' : 'Add Program' ?></h2>
        <a href="programs.php" class="btn btn-secondary btn-sm">Cancel</a>
    </div>
    <div class="admin-card-body">
        <form method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="save_program" value="1">
            <input type="hidden" name="edit_id" value="<?= htmlspecialchars($edit['id']??'') ?>">
            <input type="hidden" name="program_id" value="<?= htmlspecialchars($edit['id']??'') ?>">
            <div class="form-grid">
                <div class="form-group">
                    <label>Title <span class="req">*</span></label>
                    <input type="text" name="title" required value="<?= htmlspecialchars($edit['title']??'') ?>" placeholder="e.g. Aerodynamics">
                </div>
                <div class="form-group">
                    <label>Icon</label>
                    <?php $current_icon = $edit['icon'] ?? 'fa-gear'; ?>
                    <input type="hidden" name="icon" id="icon-value" value="<?= htmlspecialchars($current_icon) ?>">

                    <!-- Search + custom entry -->
                    <div style="display:flex;gap:.5rem;margin-bottom:.6rem;">
                        <input type="text" id="icon-search" placeholder="Search icons... (e.g. gear, car, bolt)"
                               style="flex:1;padding:.45rem .65rem;background:var(--dark3);border:1px solid var(--border);color:var(--white);font-size:.82rem;"
                               oninput="filterIcons(this.value)">
                        <div style="display:flex;align-items:center;gap:.4rem;padding:.45rem .65rem;background:var(--dark3);border:1px solid var(--orange);min-width:110px;">
                            <i class="fa <?= htmlspecialchars($current_icon) ?>" id="icon-preview" style="color:var(--orange);font-size:1rem;width:16px;text-align:center;"></i>
                            <span id="icon-preview-name" style="font-size:.72rem;color:var(--gray-light);font-family:monospace;"><?= htmlspecialchars($current_icon) ?></span>
                        </div>
                    </div>

                    <!-- Visual icon grid -->
                    <div id="icon-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(46px,1fr));gap:4px;max-height:220px;overflow-y:auto;padding:4px;background:var(--dark3);border:1px solid var(--border);">
                        <?php foreach ($icon_options as $ico): ?>
                        <button type="button"
                                class="icon-tile <?= $ico === $current_icon ? 'selected' : '' ?>"
                                data-icon="<?= $ico ?>"
                                title="<?= $ico ?>"
                                onclick="selectIcon('<?= $ico ?>')"
                                style="aspect-ratio:1;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:2px;background:var(--dark2);border:1px solid <?= $ico === $current_icon ? 'var(--orange)' : 'var(--border)' ?>;cursor:pointer;padding:.3rem .15rem;transition:border-color .1s;">
                            <i class="fa <?= $ico ?>" style="font-size:1rem;color:<?= $ico === $current_icon ? 'var(--orange)' : 'var(--gray-light)' ?>;"></i>
                        </button>
                        <?php endforeach; ?>
                    </div>

                    <!-- Custom icon fallback -->
                    <div style="margin-top:.5rem;display:flex;align-items:center;gap:.5rem;">
                        <span style="font-size:.7rem;color:var(--gray);white-space:nowrap;">Custom FA class:</span>
                        <input type="text" id="icon-custom" placeholder="e.g. fa-car-burst"
                               style="flex:1;padding:.35rem .55rem;background:var(--dark3);border:1px solid var(--border);color:var(--white);font-size:.8rem;font-family:monospace;"
                               oninput="if(this.value.trim()) selectIcon(this.value.trim())">
                    </div>
                    <span class="form-help"><a href="https://fontawesome.com/search?o=r&m=free&s=solid" target="_blank" style="color:var(--orange);">Browse all free icons</a> — paste any FA class name in the custom field above</span>
                </div>
                <div class="form-group">
                    <label>Display Order</label>
                    <input type="number" name="order" value="<?= $edit['order']??count($programs)+1 ?>" min="1">
                </div>
                <div class="form-group form-full">
                    <label>Photo</label>
                    <input type="hidden" name="photo_existing" id="p-photo-val" value="<?= htmlspecialchars($edit['photo']??'') ?>">
                    <button type="button" class="btn-browse" id="p-photo-browse"><i class="fa fa-images"></i> Browse Media</button>
                    <span class="form-help">Upload photos in <a href="media.php" target="_blank" style="color:var(--orange);">Media Library</a> first, then pick here.</span>
                    <div id="p-photo-preview">
                    <?php if (!empty($edit['photo'])):
                        $pp = dirname(dirname(__FILE__)) . '/assets/images/programs/' . $edit['photo'];
                        if (file_exists($pp)):
                    ?>
                    <div class="photo-preview-box">
                        <img src="../assets/images/programs/<?= htmlspecialchars($edit['photo']) ?>" alt="">
                        <div><div class="photo-name"><?= htmlspecialchars($edit['photo']) ?></div>
                        <button type="button" class="photo-clear" onclick="clearPicker('p-photo-val','p-photo-preview')">Remove</button></div>
                    </div>
                    <?php endif; endif; ?>
                    </div>
                </div>
                <script>document.addEventListener('DOMContentLoaded',function(){setupPicker('p-photo-browse','p-photo-val','p-photo-preview','programs','../assets/images/programs/');});</script>
                <div class="form-group form-full">
                    <label>Homepage Summary</label>
                    <input type="text" name="summary" value="<?= htmlspecialchars($edit['summary']??'') ?>" placeholder="e.g. CFD simulation, wing design, and downforce validation." maxlength="120">
                    <span class="form-help">Short one-liner for the homepage grid. If empty, the first sentence of the description is used.</span>
                </div>
                <div class="form-group form-full">
                    <label>Full Description</label>
                    <textarea name="description" rows="5" placeholder="Describe what this subteam does..."><?= htmlspecialchars($edit['description']??'') ?></textarea>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> <?= $edit ? 'Save Changes' : 'Add Program' ?></button>
                <a href="programs.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2>Programs (<?= count($programs) ?>)</h2>
        <a href="?new=1" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add</a>
    </div>
    <div class="admin-card-body" style="padding:0;">
        <table class="admin-table">
            <thead><tr><th style="width:40px;">Icon</th><th>Title</th><th>Image</th><th>Order</th><th>Actions</th></tr></thead>
            <tbody>
            <?php if (empty($programs)): ?>
            <tr><td colspan="5" class="td-center" style="padding:2rem;color:var(--gray);">No programs yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($programs as $i => $p):
                $has_img = !empty($p['photo']);
            ?>
            <tr>
                <td class="td-center" style="font-size:1.1rem;color:var(--orange);"><i class="fa <?= htmlspecialchars($p['icon']??'fa-gear') ?>"></i></td>
                <td>
                    <strong><?= htmlspecialchars($p['title']??'') ?></strong>
                    <br><span class="td-gray" style="font-size:.75rem;"><?= htmlspecialchars($p['summary'] ?? mb_strimwidth($p['description']??'', 0, 80, '...')) ?></span>
                </td>
                <td><?= $has_img ? '<span class="badge badge-green">Set</span>' : '<span class="badge badge-gray">None</span>' ?></td>
                <td class="td-gray"><?= $p['order']??'—' ?></td>
                <td>
                    <div class="actions">
                        <!-- Reorder -->
                        <?php if ($i > 0): ?>
                        <form method="POST" style="display:inline;"><input type="hidden" name="move_id" value="<?= $p['id'] ?>"><input type="hidden" name="direction" value="up"><?= csrf_field() ?><button type="submit" class="btn btn-secondary btn-sm btn-icon" title="Move up"><i class="fa fa-arrow-up"></i></button></form>
                        <?php endif; ?>
                        <?php if ($i < count($programs)-1): ?>
                        <form method="POST" style="display:inline;"><input type="hidden" name="move_id" value="<?= $p['id'] ?>"><input type="hidden" name="direction" value="down"><?= csrf_field() ?><button type="submit" class="btn btn-secondary btn-sm btn-icon" title="Move down"><i class="fa fa-arrow-down"></i></button></form>
                        <?php endif; ?>
                        <!-- Edit -->
                        <a href="?edit=<?= urlencode($p['id']) ?>" class="btn btn-secondary btn-sm btn-icon"><i class="fa fa-pen"></i></a>
                        <!-- Delete -->
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Remove <?= htmlspecialchars(addslashes($p['title']??'')) ?>?')">
                            <?= csrf_field() ?>
                            <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header"><h2><i class="fa fa-circle-info" style="margin-right:.4rem;"></i> How It Works</h2></div>
    <div class="admin-card-body" style="font-size:.85rem;color:var(--gray-light);line-height:1.6;">
        <p>Each program appears as a section on the public Programs page with an icon, title, description, and optional photo.</p>
        <p style="margin-top:.5rem;"><strong style="color:var(--orange);">Photos:</strong> Go to <a href="settings.php#images" style="color:var(--orange);">Settings → Images → Programs</a> to upload photos for each program. The image slot name (e.g. <code>prog_aero</code>) links the photo to the program.</p>
        <p style="margin-top:.5rem;"><strong style="color:var(--orange);">Order:</strong> Use the arrow buttons or set the order number to control the display order. Odd-numbered positions show the photo on the left, even on the right.</p>
    </div>
</div>

<script>
// ── Icon picker ────────────────────────────────────────────────────────────
function selectIcon(icon) {
    // Update hidden value
    document.getElementById('icon-value').value = icon;
    // Update preview
    var prev = document.getElementById('icon-preview');
    prev.className = 'fa ' + icon;
    document.getElementById('icon-preview-name').textContent = icon;
    // Highlight selected tile
    document.querySelectorAll('.icon-tile').forEach(function(t) {
        var selected = t.dataset.icon === icon;
        t.style.borderColor = selected ? 'var(--orange)' : 'var(--border)';
        t.querySelector('i').style.color = selected ? 'var(--orange)' : 'var(--gray-light)';
    });
    // Sync custom field
    var grid_icons = Array.from(document.querySelectorAll('.icon-tile')).map(function(t){ return t.dataset.icon; });
    document.getElementById('icon-custom').value = grid_icons.includes(icon) ? '' : icon;
}

function filterIcons(q) {
    q = q.toLowerCase().trim();
    document.querySelectorAll('.icon-tile').forEach(function(t) {
        t.style.display = (!q || t.dataset.icon.includes(q)) ? '' : 'none';
    });
}
</script>

<?php include 'includes/admin-footer.php'; ?>
