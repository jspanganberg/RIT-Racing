<?php
require_once __DIR__ . '/includes/auth.php';
auth_require();

$adminTitle     = 'In Memoriam';
$adminTitleSpan = 'Manager';
$msg = '';
$edit = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && empty($_FILES)) {
    $msg = ['type'=>'danger','text'=>'Upload too large! Server limit is ' . (ini_get('upload_max_filesize') ?: '2M') . '.'];
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    $entries = data_load('memorial.json');

    if (isset($_POST['delete_id'])) {
        $entries = array_values(array_filter($entries, fn($e) => $e['id'] !== $_POST['delete_id']));
        data_save('memorial.json', $entries);
        $msg = ['type'=>'success','text'=>'Entry removed.'];
    }
    elseif (isset($_POST['toggle_id'])) {
        foreach ($entries as &$e) {
            if ($e['id'] === $_POST['toggle_id']) { $e['active'] = !($e['active'] ?? true); break; }
        }
        data_save('memorial.json', $entries);
        header('Location: memorial.php'); exit;
    }
    elseif (isset($_POST['save_entry'])) {
        $id    = trim($_POST['entry_id'] ?? '');
        $photo = resolve_media_image(trim($_POST['photo_existing'] ?? ''), 'memorial');

        $entry = [
            'id'      => $id ?: uniqid(),
            'name'    => trim($_POST['name'] ?? ''),
            'title'   => trim($_POST['title'] ?? ''),
            'tagline' => trim($_POST['tagline'] ?? ''),
            'content' => trim($_POST['content'] ?? ''),
            'photo'   => $photo,
            'active'  => true,
            'order'   => (int)($_POST['order'] ?? 1),
        ];

        if (empty($entry['name'])) {
            $msg = ['type'=>'danger','text'=>'Name is required.'];
        } else {
            if ($id) {
                foreach ($entries as &$e) { if ($e['id'] === $id) { $e = array_merge($e, $entry); break; } }
            } else {
                $entries[] = $entry;
            }
            usort($entries, fn($a,$b) => ($a['order']??1) <=> ($b['order']??1));
            data_save('memorial.json', $entries);
            if (!isset($msg['type'])) $msg = ['type'=>'success','text'=> $id ? 'Entry updated.' : 'Entry added.'];
        }
    }
}

if (isset($_GET['edit'])) {
    $entries = data_load('memorial.json');
    foreach ($entries as $e) { if ($e['id'] === $_GET['edit']) { $edit = $e; break; } }
}

$entries = data_load('memorial.json');
usort($entries, fn($a,$b) => ($a['order']??1) <=> ($b['order']??1));

$topbarActions = '<a href="memorial.php?new=1" class="btn btn-primary"><i class="fa fa-plus"></i> Add Entry</a>';
include 'includes/admin-header.php';
?>

<?php if ($msg): ?>
<div class="alert alert-<?= $msg['type'] ?>"><i class="fa fa-<?= $msg['type']==='success'?'check-circle':'exclamation-circle' ?>"></i><?= htmlspecialchars($msg['text']) ?></div>
<?php endif; ?>

<?php if (isset($_GET['new']) || $edit): ?>
<div class="admin-card">
    <div class="admin-card-header">
        <h2><?= $edit ? 'Edit Memorial Entry' : 'Add Memorial Entry' ?></h2>
        <a href="memorial.php" class="btn btn-secondary btn-sm">Cancel</a>
    </div>
    <div class="admin-card-body">
        <form method="POST">
                    <?= csrf_field() ?>
            <input type="hidden" name="save_entry" value="1">
            <input type="hidden" name="entry_id" value="<?= htmlspecialchars($edit['id']??'') ?>">
            <div class="form-grid">
                <div class="form-group">
                    <label>Full Name <span class="req">*</span></label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($edit['name']??'') ?>">
                </div>
                <div class="form-group">
                    <label>Title / Role</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($edit['title']??'') ?>" placeholder="e.g. Faculty Advisor, Team Member">
                </div>
                <div class="form-group">
                    <label>Tagline / Quote</label>
                    <input type="text" name="tagline" value="<?= htmlspecialchars($edit['tagline']??'') ?>" placeholder="A brief remembrance line">
                </div>
                <div class="form-group">
                    <label>Display Order</label>
                    <input type="number" name="order" value="<?= $edit['order']??1 ?>" min="1">
                </div>
                <div class="form-group form-full">
                    <label>Memorial Text</label>
                    <textarea name="content" rows="8" placeholder="Write the memorial tribute. Use blank lines between paragraphs."><?= htmlspecialchars($edit['content']??'') ?></textarea>
                    <span class="form-help">Plain text. Separate paragraphs with a blank line.</span>
                </div>
                <div class="form-group form-full">
                    <label>Photo</label>
                    <input type="hidden" name="photo_existing" id="m-photo-val" value="<?= htmlspecialchars($edit['photo'] ?? '') ?>">
                    <button type="button" class="btn-browse" id="m-photo-browse"><i class="fa fa-images"></i> Browse Media</button>
                    <span class="form-help">Upload photos in <a href="media.php" target="_blank" style="color:var(--orange);">Media Library</a> first, then pick here.</span>
                    <div id="m-photo-preview">
                    <?php if (!empty($edit['photo'])):
                        $photo_path = dirname(dirname(__FILE__)) . '/assets/images/memorial/' . $edit['photo'];
                        if (file_exists($photo_path)):
                    ?>
                    <div class="photo-preview-box">
                        <img src="../assets/images/memorial/<?= htmlspecialchars($edit['photo']) ?>" alt="">
                        <div><div class="photo-name"><?= htmlspecialchars($edit['photo']) ?></div>
                        <button type="button" class="photo-clear" onclick="clearPicker('m-photo-val','m-photo-preview')">Remove</button></div>
                    </div>
                    <?php endif; endif; ?>
                    </div>
                </div>
                <script>document.addEventListener('DOMContentLoaded',function(){setupPicker('m-photo-browse','m-photo-val','m-photo-preview','memorial','../assets/images/memorial/');});</script>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> <?= $edit ? 'Save Changes' : 'Add Entry' ?></button>
                <a href="memorial.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header"><h2>Memorial Entries (<?= count($entries) ?>)</h2></div>
    <div class="admin-card-body" style="padding:0">
        <table class="admin-table">
            <thead><tr><th style="width:70px">Photo</th><th>Name</th><th>Title</th><th>Order</th><th>Active</th><th>Actions</th></tr></thead>
            <tbody>
            <?php if (empty($entries)): ?>
            <tr><td colspan="6" class="td-center" style="padding:2rem;color:var(--gray);">No memorial entries yet. Click "Add Entry" to create one.</td></tr>
            <?php endif; ?>
            <?php foreach ($entries as $e): ?>
            <tr style="<?= !($e['active']??true) ? 'opacity:0.5' : '' ?>">
                <td>
                    <?php if (!empty($e['photo'])): ?>
                    <img src="../assets/images/memorial/<?= htmlspecialchars($e['photo']) ?>" class="img-preview" style="object-fit:cover;" alt="">
                    <?php else: ?>
                    <div class="img-placeholder-sm"><i class="fa fa-user" style="font-size:.9rem;color:var(--gray);"></i></div>
                    <?php endif; ?>
                </td>
                <td><strong><?= htmlspecialchars($e['name']) ?></strong></td>
                <td class="td-gray" style="font-size:.8rem;"><?= htmlspecialchars($e['title']??'') ?: '—' ?></td>
                <td class="td-gray"><?= $e['order']??'—' ?></td>
                <td>
                    <form method="POST" style="display:inline">
                    <?= csrf_field() ?>
                        <input type="hidden" name="toggle_id" value="<?= $e['id'] ?>">
                        <label class="toggle-switch"><input type="checkbox" onchange="this.form.submit()" <?= ($e['active']??true)?'checked':'' ?>><span class="toggle-slider"></span></label>
                    </form>
                </td>
                <td>
                    <div class="actions">
                        <a href="?edit=<?= urlencode($e['id']) ?>" class="btn btn-secondary btn-sm btn-icon"><i class="fa fa-pen"></i></a>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Remove this memorial entry?')">
                    <?= csrf_field() ?>
                            <input type="hidden" name="delete_id" value="<?= $e['id'] ?>">
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

<?php include 'includes/admin-footer.php'; ?>
