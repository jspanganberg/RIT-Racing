<?php
require_once __DIR__ . '/includes/auth.php';
auth_require();

$adminTitle     = 'Team';
$adminTitleSpan = 'Roster';
$msg = '';
$edit_member = null;

// Load subteam list from settings (with defaults)
$default_subteams = ['Aerodynamics','Brakes & Driver Controls','Business','Chassis',
    'Drivetrain','Driverless','Electronics','Electric Powertrain',
    'Manufacturing','Suspension','Vehicle Dynamics'];
$settings = data_load('settings.json');
$raw_subteams = $settings['subteams'] ?? $default_subteams;

// Normalize — handle both flat-string format AND legacy object format
// (older versions stored subteams as [{"id":"aero","label":"Aerodynamics"}, ...])
$subteam_options = [];
foreach ((array)$raw_subteams as $item) {
    if (is_string($item)) {
        $subteam_options[] = $item;
    } elseif (is_array($item)) {
        // Pull a human-readable label out of the object
        $label = $item['label'] ?? $item['name'] ?? $item['title'] ?? $item['id'] ?? '';
        if ($label !== '') $subteam_options[] = (string)$label;
    }
}
// If everything was junk, fall back to defaults
if (empty($subteam_options)) $subteam_options = $default_subteams;
sort($subteam_options);

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && empty($_FILES)) {
    $msg = ['type'=>'danger','text'=>'Upload too large! Server limit is ' . (ini_get('upload_max_filesize') ?: '2M') . '. Resize the photo.'];
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {

    // ── Subteam management ──
    if (isset($_POST['add_subteam'])) {
        $new_name = trim($_POST['new_subteam'] ?? '');
        // Normalise whitespace — collapses multiple spaces, preserves single spaces.
        // The form uses enctype="multipart/form-data" to prevent CGI servers from
        // converting spaces to underscores via URL encoding (+ → _).
        $new_name = preg_replace('/\s+/', ' ', $new_name);
        if ($new_name && !in_array($new_name, $subteam_options)) {
            $subteam_options[] = $new_name;
            sort($subteam_options);
            $settings['subteams'] = $subteam_options;
            data_save('settings.json', $settings);
            $msg = ['type'=>'success','text'=>'Group "' . $new_name . '" added.'];
        } else {
            $msg = ['type'=>'danger','text'=>$new_name ? 'Group already exists.' : 'Name is required.'];
        }
    }
    elseif (isset($_POST['delete_subteam'])) {
        $del_name = trim($_POST['subteam_name'] ?? '');
        $subteam_options = array_values(array_filter($subteam_options, fn($s) => $s !== $del_name));
        $settings['subteams'] = $subteam_options;
        data_save('settings.json', $settings);
        $msg = ['type'=>'success','text'=>'Group "' . $del_name . '" removed.'];
    }
    elseif (isset($_POST['rename_subteam'])) {
        $old_name = trim($_POST['old_subteam'] ?? '');
        $new_name = trim($_POST['new_subteam'] ?? '');
        if ($old_name && $new_name && $old_name !== $new_name) {
            // Rename in options list
            $subteam_options = array_map(fn($s) => $s === $old_name ? $new_name : $s, $subteam_options);
            sort($subteam_options);
            $settings['subteams'] = $subteam_options;
            data_save('settings.json', $settings);
            // Rename in all team members
            $team = data_load('team.json');
            foreach ($team as &$m) {
                if (($m['subteam'] ?? '') === $old_name) $m['subteam'] = $new_name;
            }
            unset($m);
            data_save('team.json', $team);
            $msg = ['type'=>'success','text'=>'Renamed "' . $old_name . '" to "' . $new_name . '".'];
        }
    }

    // ── Member management ──
    $team = data_load('team.json');

    // Delete
    if (isset($_POST['delete_name'])) {
        $del = $_POST['delete_name'];
        $team = array_values(array_filter($team, fn($m) => $m['name'] !== $del));
        data_save('team.json', $team);
        $msg = ['type'=>'success','text'=>'Member removed.'];
    }
    // Save (new or edit)
    elseif (isset($_POST['save_member'])) {
        $edit_name = trim($_POST['edit_name'] ?? '');

        $member = [
            'name'    => trim($_POST['name']    ?? ''),
            'role'    => trim($_POST['role']    ?? ''),
            'group'   => trim($_POST['group']   ?? 'lead'),
            'subteam' => trim($_POST['subteam'] ?? ''),
            'email'   => trim($_POST['email']   ?? ''),
            'photo'   => resolve_media_image(trim($_POST['photo_existing'] ?? ''), 'team'),
            'order'   => (int)($_POST['order'] ?? 99),
        ];

        if (empty($member['name']) || empty($member['role'])) {
            $msg = ['type'=>'danger','text'=>'Name and role are required.'];
        } else {
            if ($edit_name) {
                // Update existing by name match
                foreach ($team as &$m) {
                    if ($m['name'] === $edit_name) {
                        $member['photo'] = $member['photo'] ?: ($m['photo'] ?? '');
                        $m = $member;
                        break;
                    }
                }
                unset($m);
            } else {
                $team[] = $member;
            }
            usort($team, fn($a,$b) => ($a['order']??99) <=> ($b['order']??99));
            data_save('team.json', $team);
            if (!is_array($msg) || !isset($msg['type'])) {
                $msg = ['type'=>'success','text'=> $edit_name ? 'Member updated.' : 'Member added.'];
            }
        }
    }
}

// Load for editing
if (isset($_GET['edit'])) {
    $team = data_load('team.json');
    foreach ($team as $m) { if ($m['name'] === $_GET['edit']) { $edit_member = $m; break; } }
}

$team = data_load('team.json');
usort($team, fn($a,$b) => ($a['order']??99) <=> ($b['order']??99));

$topbarActions = '<a href="team.php?new=1" class="btn btn-primary"><i class="fa fa-plus"></i> Add Member</a>';
include 'includes/admin-header.php';
?>

<?php if ($msg): ?>
<div class="alert alert-<?= $msg['type'] ?>"><i class="fa fa-<?= $msg['type']==='success'?'check-circle':'exclamation-circle' ?>"></i> <?= htmlspecialchars($msg['text']) ?></div>
<?php endif; ?>

<!-- Add / Edit Form -->
<?php if (isset($_GET['new']) || $edit_member): ?>
<div class="admin-card">
    <div class="admin-card-header">
        <h2><?= $edit_member ? 'Edit Member' : 'Add New Member' ?></h2>
        <a href="team.php" class="btn btn-secondary btn-sm">Cancel</a>
    </div>
    <div class="admin-card-body">
        <form method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="save_member" value="1">
            <input type="hidden" name="edit_name" value="<?= htmlspecialchars($edit_member['name'] ?? '') ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Full Name <span class="req">*</span></label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($edit_member['name']??'') ?>">
                </div>
                <div class="form-group">
                    <label>Role / Title <span class="req">*</span></label>
                    <input type="text" name="role" required value="<?= htmlspecialchars($edit_member['role']??'') ?>" placeholder="e.g. Aerodynamics Lead">
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select name="group">
                        <option value="admin" <?= ($edit_member['group']??'')==='admin'?'selected':'' ?>>Admin / Leadership</option>
                        <option value="lead" <?= ($edit_member['group']??'lead')==='lead'?'selected':'' ?>>Subteam Lead</option>
                        <option value="associate" <?= ($edit_member['group']??'')==='associate'?'selected':'' ?>>Associate / Member</option>
                        <option value="returning" <?= ($edit_member['group']??'')==='returning'?'selected':'' ?>>Returning Member</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Subteam</label>
                    <select name="subteam">
                        <option value="">— None (for admin/leadership) —</option>
                        <?php foreach ($subteam_options as $st): ?>
                        <option value="<?= htmlspecialchars($st) ?>" <?= ($edit_member['subteam']??'')===$st?'selected':'' ?>><?= htmlspecialchars($st) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="form-help">Which subteam this person belongs to</span>
                </div>
                <div class="form-group">
                    <label>RIT Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($edit_member['email']??'') ?>" placeholder="abc1234@rit.edu">
                </div>
                <div class="form-group">
                    <label>Display Order</label>
                    <input type="number" name="order" value="<?= $edit_member['order']??99 ?>" min="1">
                    <span class="form-help">Lower = appears first</span>
                </div>
                <div class="form-group form-full">
                    <label>Headshot Photo</label>
                    <input type="hidden" name="photo_existing" id="t-photo-val" value="<?= htmlspecialchars($edit_member['photo'] ?? '') ?>">
                    <button type="button" class="btn-browse" id="t-photo-browse"><i class="fa fa-images"></i> Browse Media</button>
                    <span class="form-help">Upload photos in <a href="media.php" target="_blank" style="color:var(--orange);">Media Library</a> first, then pick here.</span>
                    <div id="t-photo-preview">
                    <?php if (!empty($edit_member['photo'])):
                        $photo_path = dirname(dirname(__FILE__)) . '/assets/images/team/' . $edit_member['photo'];
                        if (file_exists($photo_path)):
                    ?>
                    <div class="photo-preview-box">
                        <img src="../assets/images/team/<?= htmlspecialchars($edit_member['photo']) ?>" alt="">
                        <div><div class="photo-name"><?= htmlspecialchars($edit_member['photo']) ?></div>
                        <button type="button" class="photo-clear" onclick="clearPicker('t-photo-val','t-photo-preview')">Remove</button></div>
                    </div>
                    <?php endif; endif; ?>
                    </div>
                </div>
                <script>document.addEventListener('DOMContentLoaded',function(){setupPicker('t-photo-browse','t-photo-val','t-photo-preview','team','../assets/images/team/');});</script>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> <?= $edit_member ? 'Save Changes' : 'Add Member' ?></button>
                <a href="team.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Leadership -->
<?php $admins = array_filter($team, fn($m) => ($m['group']??'') === 'admin'); ?>
<?php if (!empty($admins)): ?>
<div class="admin-card">
    <div class="admin-card-header"><h2>Leadership (<?= count($admins) ?>)</h2></div>
    <div class="admin-card-body" style="padding:0">
        <table class="admin-table">
            <thead><tr><th style="width:60px;">Photo</th><th>Name</th><th>Role</th><th>Order</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($admins as $m): ?>
            <tr>
                <td><?php if (!empty($m['photo'])): ?><img src="../assets/images/team/<?= htmlspecialchars($m['photo']) ?>" class="img-preview" alt=""><?php else: ?><div class="img-placeholder-sm"><i class="fa fa-user"></i></div><?php endif; ?></td>
                <td><strong><?= htmlspecialchars($m['name']) ?></strong></td>
                <td class="td-gray-light"><?= htmlspecialchars($m['role']) ?></td>
                <td class="td-gray"><?= $m['order']??'—' ?></td>
                <td>
                    <div class="actions">
                        <a href="?edit=<?= urlencode($m['name']) ?>" class="btn btn-secondary btn-sm btn-icon"><i class="fa fa-pen"></i></a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Remove <?= htmlspecialchars(addslashes($m['name'])) ?>?')">
                            <?= csrf_field() ?>
                            <input type="hidden" name="delete_name" value="<?= htmlspecialchars($m['name']) ?>">
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
<?php endif; ?>

<!-- Subteams -->
<?php
$by_subteam = [];
$returning_members = [];
foreach ($team as $m) {
    if (($m['group']??'') === 'admin') continue;
    if (($m['group']??'') === 'returning') { $returning_members[] = $m; continue; }
    $st = $m['subteam'] ?? 'Unassigned';
    $by_subteam[$st][] = $m;
}
ksort($by_subteam);
?>
<?php foreach ($by_subteam as $st_name => $members): ?>
<div class="admin-card">
    <div class="admin-card-header"><h2><?= htmlspecialchars($st_name) ?> (<?= count($members) ?>)</h2></div>
    <div class="admin-card-body" style="padding:0">
        <table class="admin-table">
            <thead><tr><th style="width:60px;">Photo</th><th>Name</th><th>Role</th><th>Type</th><th>Order</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($members as $m): ?>
            <tr>
                <td><?php if (!empty($m['photo'])): ?><img src="../assets/images/team/<?= htmlspecialchars($m['photo']) ?>" class="img-preview" alt=""><?php else: ?><div class="img-placeholder-sm"><i class="fa fa-user"></i></div><?php endif; ?></td>
                <td><strong><?= htmlspecialchars($m['name']) ?></strong></td>
                <td style="color:var(--gray-light);"><?= htmlspecialchars($m['role']) ?></td>
                <td><span class="badge <?= ($m['group']??'')==='lead'?'badge-orange':'badge-gray' ?>"><?= htmlspecialchars($m['group']??'') ?></span></td>
                <td style="color:var(--gray);"><?= $m['order']??'—' ?></td>
                <td>
                    <div class="actions">
                        <a href="?edit=<?= urlencode($m['name']) ?>" class="btn btn-secondary btn-sm btn-icon"><i class="fa fa-pen"></i></a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Remove <?= htmlspecialchars(addslashes($m['name'])) ?>?')">
                            <?= csrf_field() ?>
                            <input type="hidden" name="delete_name" value="<?= htmlspecialchars($m['name']) ?>">
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
<?php endforeach; ?>

<!-- Returning Members -->
<?php if (!empty($returning_members)): ?>
<div class="admin-card">
    <div class="admin-card-header"><h2>Returning Members (<?= count($returning_members) ?>)</h2></div>
    <div class="admin-card-body" style="padding:0">
        <table class="admin-table">
            <thead><tr><th style="width:60px;">Photo</th><th>Name</th><th>Role</th><th>Order</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($returning_members as $m): ?>
            <tr>
                <td><?php if (!empty($m['photo'])): ?><img src="../assets/images/team/<?= htmlspecialchars($m['photo']) ?>" class="img-preview" alt=""><?php else: ?><div class="img-placeholder-sm"><i class="fa fa-user"></i></div><?php endif; ?></td>
                <td><strong><?= htmlspecialchars($m['name']) ?></strong></td>
                <td class="td-gray-light"><?= htmlspecialchars($m['role']) ?></td>
                <td class="td-gray"><?= $m['order']??'—' ?></td>
                <td>
                    <div class="actions">
                        <a href="?edit=<?= urlencode($m['name']) ?>" class="btn btn-secondary btn-sm btn-icon"><i class="fa fa-pen"></i></a>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Remove <?= htmlspecialchars(addslashes($m['name'])) ?>?')">
                            <?= csrf_field() ?>
                            <input type="hidden" name="delete_name" value="<?= htmlspecialchars($m['name']) ?>">
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
<?php endif; ?>

<!-- ═══ Manage Groups ═══ -->
<div class="admin-card">
    <div class="admin-card-header">
        <h2><i class="fa fa-layer-group" style="margin-right:.4rem;"></i> Manage Groups</h2>
    </div>
    <div class="admin-card-body">
        <p class="section-note" style="margin-bottom:1rem;">Add, rename, or remove subteam group names. These appear in the dropdown when adding members and as section headers on the public team page.</p>

        <!-- Add new group -->
        <form method="POST" enctype="multipart/form-data" class="add-row">
            <?= csrf_field() ?>
            <div class="form-group" style="flex:1;">
                <label>New Group Name</label>
                <input type="text" name="new_subteam" placeholder="e.g. Driverless Software" required>
            </div>
            <button type="submit" name="add_subteam" value="1" class="btn btn-primary" style="margin-bottom:.05rem;"><i class="fa fa-plus"></i> Add</button>
        </form>

        <!-- List existing groups -->
        <div class="group-grid">
            <?php foreach ($subteam_options as $st):
                // Count members in this group
                $count = 0;
                foreach ($team as $m) { if (($m['subteam'] ?? '') === $st) $count++; }
            ?>
            <div class="group-item">
                <span style="flex:1;font-size:.85rem;font-weight:600;"><?= htmlspecialchars($st) ?></span>
                <span class="td-gray" style="font-size:.7rem;"><?= $count ?> members</span>

                <!-- Rename -->
                <button type="button" class="btn btn-secondary btn-sm btn-icon" onclick="renameGroup('<?= htmlspecialchars(addslashes($st)) ?>')" title="Rename"><i class="fa fa-pen"></i></button>

                <!-- Delete -->
                <form method="POST" style="display:inline;" onsubmit="return confirm('Remove group \'<?= htmlspecialchars(addslashes($st)) ?>\'? Members will keep their data but show as Unassigned.')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="delete_subteam" value="1">
                    <input type="hidden" name="subteam_name" value="<?= htmlspecialchars($st) ?>">
                    <button type="submit" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-trash"></i></button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Rename modal form (hidden, submitted via JS) -->
<form method="POST" id="rename-form" style="display:none;">
    <?= csrf_field() ?>
    <input type="hidden" name="rename_subteam" value="1">
    <input type="hidden" name="old_subteam" id="rename-old" value="">
    <input type="hidden" name="new_subteam" id="rename-new" value="">
</form>
<script>
function renameGroup(oldName) {
    var newName = prompt('Rename "' + oldName + '" to:', oldName);
    if (newName && newName.trim() && newName.trim() !== oldName) {
        document.getElementById('rename-old').value = oldName;
        document.getElementById('rename-new').value = newName.trim();
        document.getElementById('rename-form').submit();
    }
}
</script>

<?php include 'includes/admin-footer.php'; ?>
