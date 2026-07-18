<?php
require_once __DIR__ . '/includes/auth.php';
auth_require();

$adminTitle     = 'Vehicles';
$adminTitleSpan = 'Manager';
$msg = '';

// Load vehicles
$vehicles = data_load('vehicles.json');
if (empty($vehicles['electric']))    $vehicles['electric']    = [];
if (empty($vehicles['combustion']))  $vehicles['combustion']  = [];

// Which program tab?
$program = ($_GET['program'] ?? 'electric');
if (!in_array($program, ['electric','combustion'])) $program = 'electric';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && empty($_FILES)) {
    $msg = ['type'=>'danger','text'=>'Upload too large! Server limit is ' . (ini_get('upload_max_filesize') ?: '2M') . '. Resize the image.'];
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {

    // Delete
    if (isset($_POST['delete_id'])) {
        $del = $_POST['delete_id'];
        $vehicles[$program] = array_values(array_filter($vehicles[$program], fn($v) => $v['id'] !== $del));
        data_save('vehicles.json', $vehicles);
        $msg = ['type'=>'success','text'=>'Vehicle deleted.'];
    }

    // Reorder
    elseif (isset($_POST['reorder'])) {
        $order = json_decode($_POST['reorder'], true);
        if (is_array($order)) {
            $indexed = [];
            foreach ($vehicles[$program] as $v) $indexed[$v['id']] = $v;
            $reordered = [];
            $i = 1;
            foreach ($order as $id) {
                if (isset($indexed[$id])) {
                    $indexed[$id]['order'] = $i++;
                    $reordered[] = $indexed[$id];
                    unset($indexed[$id]);
                }
            }
            foreach ($indexed as $v) { $v['order'] = $i++; $reordered[] = $v; }
            $vehicles[$program] = $reordered;
            data_save('vehicles.json', $vehicles);
            $msg = ['type'=>'success','text'=>'Order updated.'];
        }
    }

    // Save vehicle
    elseif (isset($_POST['save_vehicle'])) {
        $id       = strtolower(trim(preg_replace('/[^a-z0-9\-]/', '', strtolower($_POST['id'] ?? ''))));
        $edit_id  = $_POST['edit_id'] ?? '';

        $photo_filename = trim($_POST['photo_existing'] ?? '');
        // Ensure the image exists in the correct cars folder
        $photo_filename = resolve_media_image($photo_filename, 'cars/' . $program);

        $entry = [
            'id'          => $id,
            'name'        => trim($_POST['name'] ?? ''),
            'years'       => trim($_POST['years'] ?? ''),
            'subtitle'    => trim($_POST['subtitle'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'result'      => trim($_POST['result'] ?? ''),
            'badge'       => trim($_POST['badge'] ?? ''),
            'badge_color' => trim($_POST['badge_color'] ?? ''),
            'photo'       => $photo_filename,
        ];

        // Parse specs (electric only)
        if ($program === 'electric') {
            $specs = [];
            foreach ($_POST['spec_label'] ?? [] as $si => $sl) {
                $sl = trim($sl);
                $sv = trim($_POST['spec_value'][$si] ?? '');
                if ($sl && $sv) $specs[] = ['label' => $sl, 'value' => $sv];
            }
            $entry['specs'] = $specs;
        }

        if (empty($id) || empty($entry['name'])) {
            $msg = ['type'=>'danger','text'=>'ID and Name are required.'];
        } else {
            $found = false;
            foreach ($vehicles[$program] as $i => $v) {
                if ($v['id'] === $edit_id) {
                    $entry['order'] = $v['order'] ?? ($i + 1);
                    $vehicles[$program][$i] = $entry;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                // New vehicle: give it order 0 and bump every existing entry up by 1
                // so it always sorts to the top after usort
                foreach ($vehicles[$program] as &$v) {
                    $v['order'] = ($v['order'] ?? 1) + 1;
                }
                unset($v);
                $entry['order'] = 1;
                array_unshift($vehicles[$program], $entry);
            }
            data_save('vehicles.json', $vehicles);
            $msg = ['type'=>'success','text'=> $found ? 'Vehicle updated.' : 'Vehicle added.'];
        }
    }

    // Reload after save
    $vehicles = data_load('vehicles.json');
}

// Editing?
$edit = null;
if (isset($_GET['edit'])) {
    foreach ($vehicles[$program] as $v) {
        if ($v['id'] === $_GET['edit']) { $edit = $v; break; }
    }
}

$list = $vehicles[$program] ?? [];
usort($list, fn($a,$b) => ($a['order']??99) <=> ($b['order']??99));

$topbarActions  = '<a href="?program='.$program.'&new=1" class="btn btn-primary"><i class="fa fa-plus"></i> Add Vehicle</a>';
include 'includes/admin-header.php';
?>

<?php if ($msg): ?>
<div class="alert alert-<?= $msg['type'] ?>"><i class="fa fa-<?= $msg['type']==='success'?'check-circle':'exclamation-circle' ?>"></i> <?= htmlspecialchars($msg['text']) ?></div>
<?php endif; ?>

<!-- Program tabs -->
<div style="display:flex;gap:0;margin-bottom:1.5rem;border-bottom:1px solid var(--border);">
    <a href="?program=electric" style="padding:.75rem 1.5rem;font-family:'Barlow Condensed',sans-serif;font-size:.85rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;text-decoration:none;border-bottom:2px solid <?= $program==='electric'?'var(--orange)':'transparent' ?>;color:<?= $program==='electric'?'var(--orange)':'var(--gray)' ?>;"><i class="fa fa-bolt"></i> Electric</a>
    <a href="?program=combustion" style="padding:.75rem 1.5rem;font-family:'Barlow Condensed',sans-serif;font-size:.85rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;text-decoration:none;border-bottom:2px solid <?= $program==='combustion'?'var(--orange)':'transparent' ?>;color:<?= $program==='combustion'?'var(--orange)':'var(--gray)' ?>;"><i class="fa fa-gas-pump"></i> Combustion</a>
</div>

<!-- Add/Edit form -->
<?php if (isset($_GET['new']) || $edit): ?>
<div class="admin-card">
    <div class="admin-card-header">
        <h2><?= $edit ? 'Edit '.$edit['name'] : 'New '.ucfirst($program).' Vehicle' ?></h2>
        <a href="?program=<?= $program ?>" class="btn btn-secondary btn-sm">Cancel</a>
    </div>
    <div class="admin-card-body">
        <form method="POST" action="?program=<?= $program ?>">
                    <?= csrf_field() ?>
            <input type="hidden" name="save_vehicle" value="1">
            <input type="hidden" name="edit_id" value="<?= htmlspecialchars($edit['id']??'') ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Vehicle ID <span class="req">*</span></label>
                    <input type="text" name="id" required pattern="[a-z0-9\-]+"
                           value="<?= htmlspecialchars($edit['id']??'') ?>"
                           placeholder="e.g. f34" <?= $edit ? 'readonly style="opacity:.5"' : '' ?>>
                    <span class="form-help">Lowercase, used in URLs (e.g. f34, e1)</span>
                </div>
                <div class="form-group">
                    <label>Display Name <span class="req">*</span></label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($edit['name']??'') ?>" placeholder="e.g. F34">
                </div>
                <div class="form-group">
                    <label>Years</label>
                    <input type="text" name="years" value="<?= htmlspecialchars($edit['years']??'') ?>" placeholder="e.g. 2025 – 2026">
                </div>
                <div class="form-group">
                    <label>Subtitle</label>
                    <input type="text" name="subtitle" value="<?= htmlspecialchars($edit['subtitle']??'') ?>" placeholder="e.g. Next Generation Hub Motors">
                </div>
                <div class="form-group form-full">
                    <label>Description</label>
                    <textarea name="description" rows="5" placeholder="Tell the story of this car..."><?= htmlspecialchars($edit['description']??'') ?></textarea>
                    <span class="form-help">Use blank lines to separate paragraphs</span>
                </div>
                <div class="form-group">
                    <label>Best Result</label>
                    <input type="text" name="result" value="<?= htmlspecialchars($edit['result']??'') ?>" placeholder="e.g. 1st Overall — FSAE Michigan 2024">
                </div>
                <div class="form-group form-full">
                    <label>Car Photo</label>
                    <input type="hidden" name="photo_existing" id="v-photo-val" value="<?= htmlspecialchars($edit['photo'] ?? '') ?>">
                    <button type="button" class="btn-browse" id="v-photo-browse"><i class="fa fa-images"></i> Browse Media</button>
                    <span class="form-help">Upload photos in <a href="media.php" target="_blank" style="color:var(--orange);">Media Library</a> first, then pick here.</span>
                    <div id="v-photo-preview">
                    <?php
                    $cur_photo = $edit['photo'] ?? '';
                    $cur_photo_path = dirname(dirname(__FILE__)) . '/assets/images/cars/' . $program . '/' . $cur_photo;
                    if ($cur_photo && file_exists($cur_photo_path)):
                    ?>
                    <div class="photo-preview-box">
                        <img src="../assets/images/cars/<?= $program ?>/<?= htmlspecialchars($cur_photo) ?>" alt="">
                        <div><div class="photo-name"><?= htmlspecialchars($cur_photo) ?></div>
                        <button type="button" class="photo-clear" onclick="clearPicker('v-photo-val','v-photo-preview')">Remove</button></div>
                    </div>
                    <?php endif; ?>
                    </div>
                </div>
                <script>document.addEventListener('DOMContentLoaded',function(){setupPicker('v-photo-browse','v-photo-val','v-photo-preview','cars/<?= $program ?>','../assets/images/cars/<?= $program ?>/');});</script>
                <?php if ($program === 'electric'): ?>
                <div class="form-group">
                    <label>Badge Text</label>
                    <input type="text" name="badge" value="<?= htmlspecialchars($edit['badge']??'') ?>" placeholder="e.g. Current Car, 🏆 Champions">
                </div>
                <div class="form-group">
                    <label>Badge Color</label>
                    <select name="badge_color">
                        <option value="">None</option>
                        <option value="orange" <?= ($edit['badge_color']??'')==='orange'?'selected':'' ?>>Orange (Current)</option>
                        <option value="gold" <?= ($edit['badge_color']??'')==='gold'?'selected':'' ?>>Gold (Champion)</option>
                        <option value="green" <?= ($edit['badge_color']??'')==='green'?'selected':'' ?>>Green</option>
                        <option value="gray" <?= ($edit['badge_color']??'')==='gray'?'selected':'' ?>>Gray</option>
                    </select>
                </div>
                <?php else: ?>
                <input type="hidden" name="badge" value="">
                <input type="hidden" name="badge_color" value="">
                <?php endif; ?>
            </div>

            <?php if ($program === 'electric'): ?>
            <!-- Specs editor -->
            <div class="specs-section">
                <label class="specs-label">Spec Sheet</label>
                <div id="specs-list" class="list-col-sm">
                    <?php foreach ($edit['specs'] ?? [] as $spec): ?>
                    <div class="spec-row">
                        <input type="text" name="spec_label[]" value="<?= htmlspecialchars($spec['label']) ?>" placeholder="Label (e.g. Motor)" style="width:200px;">
                        <input type="text" name="spec_value[]" value="<?= htmlspecialchars($spec['value']) ?>" placeholder="Value (e.g. Hub Motors ×4)" style="flex:1;">
                        <button type="button" onclick="this.closest('.spec-row').remove()" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-minus"></i></button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" onclick="addSpec()"><i class="fa fa-plus"></i> Add Spec</button>
            </div>
            <?php endif; ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> <?= $edit ? 'Save Changes' : 'Add Vehicle' ?></button>
                <a href="?program=<?= $program ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Vehicle list -->
<div class="admin-card">
    <div class="admin-card-header">
        <h2><?= ucfirst($program) ?> Vehicles (<?= count($list) ?>)</h2>
    </div>
    <div class="admin-card-body" style="padding:0">
        <table class="admin-table">
            <thead><tr><th style="width:40px">#</th><th style="width:60px">Photo</th><th>Name</th><th>Years</th><th>Result</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($list as $i => $v):
                $v_photo = $v['photo'] ?? '';
                $v_photo_path = dirname(dirname(__FILE__)) . '/assets/images/cars/' . $program . '/' . $v_photo;
                $v_has_photo = $v_photo && file_exists($v_photo_path);
            ?>
            <tr>
                <td class="td-gray" style="font-size:.75rem;"><?= $i + 1 ?></td>
                <td>
                    <?php if ($v_has_photo): ?>
                    <img src="../assets/images/cars/<?= $program ?>/<?= htmlspecialchars($v_photo) ?>" class="img-preview" alt="">
                    <?php else: ?>
                    <div class="img-placeholder-sm"><i class="fa fa-image"></i></div>
                    <?php endif; ?>
                </td>
                <td>
                    <strong style="color:var(--orange);font-family:'Barlow Condensed',sans-serif;font-size:1.1rem;"><?= htmlspecialchars($v['name']) ?></strong>
                    <?php if (!empty($v['badge'])): ?>
                    <span class="badge badge-<?= $v['badge_color'] ?: 'gray' ?>" style="margin-left:.5rem;"><?= htmlspecialchars($v['badge']) ?></span>
                    <?php endif; ?>
                </td>
                <td class="td-gray-light"><?= htmlspecialchars($v['years']) ?></td>
                <td style="font-size:.8rem;"><?= htmlspecialchars($v['result'] ?: '—') ?></td>
                <td>
                    <div class="actions">
                        <a href="?program=<?= $program ?>&edit=<?= urlencode($v['id']) ?>" class="btn btn-secondary btn-sm btn-icon"><i class="fa fa-pen"></i></a>
                        <form method="POST" action="?program=<?= $program ?>" style="display:inline" onsubmit="return confirm('Delete <?= htmlspecialchars(addslashes($v['name'])) ?>?')">
                    <?= csrf_field() ?>
                            <input type="hidden" name="delete_id" value="<?= htmlspecialchars($v['id']) ?>">
                            <button type="submit" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($list)): ?>
            <tr><td colspan="6" class="td-center" style="color:var(--gray);padding:2rem;">No vehicles yet. Click "Add Vehicle" to get started.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function addSpec() {
    const list = document.getElementById('specs-list');
    const row = document.createElement('div');
    row.className = 'spec-row';
    row.innerHTML = '<input type="text" name="spec_label[]" placeholder="Label" style="width:200px;">' +
                    '<input type="text" name="spec_value[]" placeholder="Value" style="flex:1;">' +
                    '<button type="button" onclick="this.closest(\'.spec-row\').remove()" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-minus"></i></button>';
    list.appendChild(row);
}
function previewPhoto(sel) {
    var preview = document.getElementById('photo-preview');
    if (!preview) return;
    if (!sel.value) { preview.innerHTML = ''; return; }
    var program = '<?= $program ?>';
    var src = '../assets/images/cars/' + program + '/' + sel.value;
    preview.innerHTML = '<img src="' + src + '" alt="" style="max-width:240px;max-height:140px;object-fit:cover;border:1px solid var(--border);">';
}
</script>

<?php include 'includes/admin-footer.php'; ?>
