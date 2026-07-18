<?php
require_once __DIR__ . '/includes/auth.php';
auth_require();

$adminTitle     = 'Sponsors';
$adminTitleSpan = 'Manager';
$msg = '';
$edit = null;

$files_dir = dirname(dirname(__FILE__)) . '/assets/files';
if (!is_dir($files_dir)) @mkdir($files_dir, 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && empty($_FILES)) {
    $msg = ['type'=>'danger','text'=>'Upload too large! Server limit is ' . (ini_get('upload_max_filesize') ?: '2M') . '.'];
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    $sponsors = data_load('sponsors.json');

    // ── Sponsor Packet Upload ────────────────────────────────
    if (isset($_POST['upload_packet'])) {
        if (!empty($_FILES['packet_file']['name'])) {
            $file = $_FILES['packet_file'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($ext !== 'pdf') {
                $msg = ['type'=>'danger','text'=>'Sponsor packet must be a PDF file.'];
            } elseif ($file['size'] > 25 * 1024 * 1024) {
                $msg = ['type'=>'danger','text'=>'File too large. Max 25MB.'];
            } elseif ($file['error'] !== UPLOAD_ERR_OK) {
                $msg = ['type'=>'danger','text'=>'Upload error code: ' . $file['error']];
            } else {
                $filename = 'rit-racing-sponsor-packet.pdf';
                $dest = $files_dir . '/' . $filename;
                if (file_exists($dest)) unlink($dest);
                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $settings = data_load('settings.json');
                    $settings['sponsor_packet_file'] = $filename;
                    $settings['sponsor_packet_updated'] = date('Y-m-d H:i:s');
                    $settings['sponsor_packet_size'] = $file['size'];
                    data_save('settings.json', $settings);
                    $msg = ['type'=>'success','text'=>'Sponsor packet uploaded successfully.'];
                } else {
                    $msg = ['type'=>'danger','text'=>'Failed to save file. Check directory permissions.'];
                }
            }
        } else {
            $msg = ['type'=>'danger','text'=>'No file selected.'];
        }
    }
    elseif (isset($_POST['delete_packet'])) {
        $pp = $files_dir . '/rit-racing-sponsor-packet.pdf';
        if (file_exists($pp)) unlink($pp);
        $settings = data_load('settings.json');
        unset($settings['sponsor_packet_file'], $settings['sponsor_packet_updated'], $settings['sponsor_packet_size']);
        data_save('settings.json', $settings);
        $msg = ['type'=>'success','text'=>'Sponsor packet removed.'];
    }
    elseif (isset($_POST['delete_id'])) {
        $sponsors = array_values(array_filter($sponsors, fn($s) => $s['id'] !== $_POST['delete_id']));
        data_save('sponsors.json', $sponsors);
        $msg = ['type'=>'success','text'=>'Sponsor removed.'];
    }
    elseif (isset($_POST['toggle_id'])) {
        foreach ($sponsors as &$s) {
            if ($s['id'] === $_POST['toggle_id']) { $s['active'] = !($s['active'] ?? true); break; }
        }
        data_save('sponsors.json', $sponsors);
        header('Location: sponsors.php'); exit;
    }
    elseif (isset($_POST['save_sponsor'])) {
        $id   = trim($_POST['sponsor_id'] ?? '');
        $logo = resolve_media_image(trim($_POST['logo_existing'] ?? ''), 'sponsors');
        $sponsor = [
            'id'          => $id ?: uniqid(),
            'name'        => trim($_POST['name']  ?? ''),
            'url'         => trim($_POST['url']   ?? ''),
            'logo'        => $logo,
            'active'      => true,
            'order'       => (int)($_POST['order'] ?? 99),
            'featured'    => !empty($_POST['featured']),
            'title'       => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
        ];
        if (empty($sponsor['name'])) {
            $msg = ['type'=>'danger','text'=>'Name is required.'];
        } else {
            if ($id) {
                foreach ($sponsors as &$s) { if ($s['id'] === $id) { $s = array_merge($s, $sponsor); break; } }
            } else {
                $sponsors[] = $sponsor;
            }
            usort($sponsors, fn($a,$b) => ($a['order']??99) <=> ($b['order']??99));
            data_save('sponsors.json', $sponsors);
            if (!isset($msg['type'])) $msg = ['type'=>'success','text'=> $id ? 'Sponsor updated.' : 'Sponsor added.'];
        }
    }
}

if (isset($_GET['edit'])) {
    $sponsors = data_load('sponsors.json');
    foreach ($sponsors as $s) { if ($s['id'] === $_GET['edit']) { $edit = $s; break; } }
}

$sponsors = data_load('sponsors.json');
usort($sponsors, fn($a,$b) => ($a['order']??99) <=> ($b['order']??99));

$settings = data_load('settings.json');
$packet_file = $settings['sponsor_packet_file'] ?? '';
$packet_path = $files_dir . '/' . $packet_file;
$packet_exists = $packet_file && file_exists($packet_path);

$topbarActions = '<a href="sponsors.php?new=1" class="btn btn-primary"><i class="fa fa-plus"></i> Add Sponsor</a>';
include 'includes/admin-header.php';
?>

<?php if ($msg): ?>
<div class="alert alert-<?= $msg['type'] ?>"><i class="fa fa-<?= $msg['type']==='success'?'check-circle':'exclamation-circle' ?>"></i><?= htmlspecialchars($msg['text']) ?></div>
<?php endif; ?>

<!-- ── Sponsor Packet ─────────────────────────────────────── -->
<div class="admin-card">
    <div class="admin-card-header">
        <h2><i class="fa fa-file-pdf" style="margin-right:.4rem;"></i> Sponsor Packet (PDF)</h2>
        <?php if ($packet_exists): ?>
        <a href="../assets/files/<?= htmlspecialchars($packet_file) ?>" target="_blank" class="btn btn-secondary btn-sm"><i class="fa fa-external-link"></i> View Current</a>
        <?php endif; ?>
    </div>
    <div class="admin-card-body">
        <?php if ($packet_exists): ?>
        <div class="file-info-box">
            <div class="file-info-icon"><i class="fa fa-file-pdf"></i></div>
            <div class="file-info-body">
                <div style="font-weight:600;"><?= htmlspecialchars($packet_file) ?></div>
                <div class="file-info-meta">
                    <?= number_format(($settings['sponsor_packet_size'] ?? filesize($packet_path)) / 1024 / 1024, 1) ?> MB
                    <?php if (!empty($settings['sponsor_packet_updated'])): ?>
                    &middot; Updated <?= date('M j, Y', strtotime($settings['sponsor_packet_updated'])) ?>
                    <?php endif; ?>
                </div>
            </div>
            <form method="POST" onsubmit="return confirm('Remove the sponsor packet?')">
                    <?= csrf_field() ?>
                <input type="hidden" name="delete_packet" value="1">
                <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Remove</button>
            </form>
        </div>
        <?php else: ?>
        <div style="padding:1rem;background:rgba(247,105,2,.06);border:1px solid rgba(247,105,2,.15);margin-bottom:1.25rem;font-size:.875rem;color:var(--gray-light);">
            <i class="fa fa-info-circle" style="color:var(--orange);margin-right:.5rem;"></i>
            No sponsor packet uploaded yet. Upload a PDF below.
        </div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
            <input type="hidden" name="upload_packet" value="1">
            <div class="upload-row">
                <div class="form-group" style="flex:1;min-width:200px;">
                    <label><?= $packet_exists ? 'Replace Sponsor Packet' : 'Upload Sponsor Packet' ?></label>
                    <input type="file" name="packet_file" accept=".pdf" required>
                    <span class="form-help">PDF only. Max 25MB.</span>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-bottom:.4rem;"><i class="fa fa-upload"></i> <?= $packet_exists ? 'Replace' : 'Upload' ?></button>
            </div>
        </form>
    </div>
</div>

<?php if (isset($_GET['new']) || $edit): ?>
<div class="admin-card">
    <div class="admin-card-header">
        <h2><?= $edit ? 'Edit Sponsor' : 'Add Sponsor' ?></h2>
        <a href="sponsors.php" class="btn btn-secondary btn-sm">Cancel</a>
    </div>
    <div class="admin-card-body">
        <form method="POST">
                    <?= csrf_field() ?>
            <input type="hidden" name="save_sponsor" value="1">
            <input type="hidden" name="sponsor_id" value="<?= htmlspecialchars($edit['id']??'') ?>">
            <div class="form-grid">
                <div class="form-group">
                    <label>Sponsor Name <span class="req">*</span></label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($edit['name']??'') ?>">
                </div>
                <div class="form-group">
                    <label>Website URL</label>
                    <input type="url" name="url" value="<?= htmlspecialchars($edit['url']??'') ?>" placeholder="https://...">
                </div>
                <div class="form-group">
                    <label>Display Order</label>
                    <input type="number" name="order" value="<?= $edit['order']??99 ?>" min="1">
                </div>
                <div class="form-group">
                    <label>Title / Role</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($edit['title']??'') ?>" placeholder="e.g. President, RIT">
                    <span class="form-help">Shown below name on featured sponsors.</span>
                </div>
                <div class="form-group form-full">
                    <label style="display:flex;align-items:center;gap:.5rem;">
                        <input type="checkbox" name="featured" value="1" <?= !empty($edit['featured']) ? 'checked' : '' ?> style="width:auto;">
                        <span style="font-size:.85rem;color:var(--orange);">⭐ Featured Sponsor</span>
                    </label>
                    <span class="form-help">Featured sponsors get a large, highlighted section at the top of the Sponsors page with photo, title, and description.</span>
                </div>
                <div class="form-group form-full">
                    <label>Description</label>
                    <textarea name="description" rows="3" placeholder="A sentence or two about this sponsor's support..."><?= htmlspecialchars($edit['description']??'') ?></textarea>
                    <span class="form-help">Only shown for featured sponsors.</span>
                </div>
                <div class="form-group form-full">
                    <label>Logo / Photo</label>
                    <input type="hidden" name="logo_existing" id="s-logo-val" value="<?= htmlspecialchars($edit['logo'] ?? '') ?>">
                    <button type="button" class="btn-browse" id="s-logo-browse"><i class="fa fa-images"></i> Browse Media</button>
                    <span class="form-help">Upload logos in <a href="media.php" target="_blank" style="color:var(--orange);">Media Library</a> first, then pick here.</span>
                    <div id="s-logo-preview">
                    <?php if (!empty($edit['logo'])):
                        $logo_path = $root . '/html/assets/images/sponsors/' . basename($edit['logo']);
                        if (file_exists($logo_path)):
                    ?>
                    <div class="photo-preview-box">
                        <img src="../assets/images/sponsors/<?= htmlspecialchars($edit['logo']) ?>" alt="" class="logo-preview">
                        <div><div class="photo-name"><?= htmlspecialchars($edit['logo']) ?></div>
                        <button type="button" class="photo-clear" onclick="clearPicker('s-logo-val','s-logo-preview')">Remove</button></div>
                    </div>
                    <?php endif; endif; ?>
                    </div>
                </div>
                <script>document.addEventListener('DOMContentLoaded',function(){setupPicker('s-logo-browse','s-logo-val','s-logo-preview','sponsors','/assets/images/sponsors/');});</script>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> <?= $edit ? 'Save Changes' : 'Add Sponsor' ?></button>
                <a href="sponsors.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<div class="admin-card">
    <div class="admin-card-header"><h2>All Sponsors (<?= count($sponsors) ?>)</h2></div>
    <div class="admin-card-body" style="padding:0">
        <table class="admin-table">
            <thead><tr><th style="width:70px">Logo</th><th>Name</th><th>Order</th><th>Featured</th><th>Actions</th></tr></thead>
            <tbody>
            <?php if (empty($sponsors)): ?>
            <tr><td colspan="5" class="td-center" style="padding:2rem;color:var(--gray);">No sponsors yet.</td></tr>
            <?php endif; ?>
            <?php foreach ($sponsors as $s): ?>
            <tr>
                <td>
                    <?php if (!empty($s['logo'])): ?>
                    <img src="../assets/images/sponsors/<?= htmlspecialchars($s['logo']) ?>" class="img-preview logo-preview" alt="">
                    <?php else: ?>
                    <div class="img-placeholder-sm" style="font-size:.6rem;">No Logo</div>
                    <?php endif; ?>
                </td>
                <td><strong><?= htmlspecialchars($s['name']) ?></strong><?php if(!empty($s['title'])): ?><br><span class="td-gray" style="font-size:.75rem;"><?= htmlspecialchars($s['title']) ?></span><?php endif; ?></td>
                <td class="td-gray"><?= $s['order']??'—' ?></td>
                <td><?= !empty($s['featured']) ? '<span class="badge badge-orange">⭐</span>' : '—' ?></td>
                <td>
                    <div class="actions">
                        <a href="?edit=<?= urlencode($s['id']) ?>" class="btn btn-secondary btn-sm btn-icon"><i class="fa fa-pen"></i></a>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Remove sponsor?')">
                    <?= csrf_field() ?>
                            <input type="hidden" name="delete_id" value="<?= $s['id'] ?>">
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
