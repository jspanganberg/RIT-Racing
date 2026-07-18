<?php
/**
 * admin/media.php — Media Library
 *
 * Features:
 *   - Upload with optional Cropper.js (single file) or direct multi-file
 *   - Crop existing images in-place (replaces the file on disk)
 *   - Move images between folders
 *   - Rename images
 *   - Delete images
 *   - Filter tabs by folder
 */
require_once __DIR__ . '/includes/auth.php';
auth_require();

$adminTitle     = 'Media';
$adminTitleSpan = 'Library';
$msg = '';

$base_upload = dirname(dirname(__FILE__)) . '/assets/images/';
$subfolders  = ['uploads','team','sponsors','programs','hero','cars/electric','cars/combustion','site','memorial'];

// Fix permissions on all images
foreach ($subfolders as $sf) {
    $dir = $base_upload . $sf;
    if (is_dir($dir)) {
        foreach (glob($dir . '/*.{jpg,jpeg,png,gif,webp,svg}', GLOB_BRACE) ?: [] as $f) {
            if ((fileperms($f) & 0777) < 0644) @chmod($f, 0644);
        }
    }
}

$server_max = ini_get('upload_max_filesize') ?: '2M';

// Convert PHP shorthand (2M, 8M, etc.) to bytes for JS comparison
function return_bytes(string $val): int {
    $val = trim($val);
    $last = strtolower($val[-1] ?? '');
    $num  = (int) $val;
    return match($last) { 'g' => $num * 1073741824, 'm' => $num * 1048576, 'k' => $num * 1024, default => $num };
}

// ── POST handler ───────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && empty($_FILES)) {
    $msg = ['type'=>'danger','text'=>"Upload too large! Server limit is {$server_max}. Resize and try again."];
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    $action = $_POST['action'] ?? 'upload';

    // ── Upload new file ────────────────────────────────────────────────────
    if ($action === 'upload' && !empty($_FILES['images'])) {
        $folder      = preg_replace('/[^a-zA-Z0-9\/\-_]/', '', $_POST['folder'] ?? 'uploads');
        $custom_name = trim($_POST['custom_name'] ?? '');
        $files       = $_FILES['images'];
        $count = 0; $errors = []; $uploaded_names = [];

        $file_count = is_array($files['name']) ? count($files['name']) : 1;
        for ($i = 0; $i < $file_count; $i++) {
            $single = [
                'name'     => is_array($files['name'])     ? $files['name'][$i]     : $files['name'],
                'type'     => is_array($files['type'])     ? $files['type'][$i]     : $files['type'],
                'tmp_name' => is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'],
                'error'    => is_array($files['error'])    ? $files['error'][$i]    : $files['error'],
                'size'     => is_array($files['size'])     ? $files['size'][$i]     : $files['size'],
            ];
            $name_for_file = ($i === 0 && !empty($custom_name)) ? $custom_name : '';
            $result = upload_image($single, $folder, $name_for_file);
            if ($result['ok']) { $count++; $uploaded_names[] = $result['filename']; }
            else $errors[] = $result['error'];
        }

        if ($count > 0) $msg = ['type'=>'success','text'=>"$count image(s) uploaded: " . implode(', ', $uploaded_names)];
        if ($errors)    $msg = ['type'=>'danger', 'text'=>"$count uploaded. Errors: " . implode(', ', $errors)];
    }

    // ── Crop existing image (replaces file in-place) ───────────────────────
    // Receives a base64 data-URL of the cropped canvas from JS
    elseif ($action === 'crop_existing') {
        $sub      = preg_replace('/[^a-zA-Z0-9\/\-_]/', '', $_POST['sub'] ?? 'uploads');
        $filename = basename($_POST['filename'] ?? '');
        $dataurl  = $_POST['cropped_data'] ?? '';
        $dest     = $base_upload . $sub . '/' . $filename;

        $safe_base = realpath($base_upload);
        $safe_dest = realpath(dirname($dest)) ?: '';
        if (!$filename || !file_exists($dest)) {
            $msg = ['type'=>'danger','text'=>'File not found.'];
        } elseif (!$safe_base || strpos($safe_dest, $safe_base) !== 0) {
            $msg = ['type'=>'danger','text'=>'Invalid destination path.'];
        } elseif (!$dataurl || strpos($dataurl, 'data:image/') !== 0) {
            $msg = ['type'=>'danger','text'=>'Invalid crop data.'];
        } else {
            // Strip the data-URL header and decode
            $comma   = strpos($dataurl, ',');
            $b64     = substr($dataurl, $comma + 1);
            $decoded = base64_decode($b64);

            if ($decoded === false || strlen($decoded) < 100) {
                $msg = ['type'=>'danger','text'=>'Crop data could not be decoded.'];
            } elseif (@getimagesizefromstring($decoded) === false) {
                $msg = ['type'=>'danger','text'=>'Crop result is not a valid image.'];
            } else {
                file_put_contents($dest, $decoded);
                @chmod($dest, 0644);
                $msg = ['type'=>'success','text'=>"Cropped and saved: {$filename}"];
            }
        }
    }

    // ── Rename ─────────────────────────────────────────────────────────────
    elseif ($action === 'rename') {
        $sub      = preg_replace('/[^a-zA-Z0-9\/\-_]/', '', $_POST['sub'] ?? 'uploads');
        $old_name = basename($_POST['old_name'] ?? '');
        $new_name = trim($_POST['new_name'] ?? '');
        $dir      = $base_upload . $sub . '/';
        $old_path = $dir . $old_name;

        if (!$old_name || !$new_name) {
            $msg = ['type'=>'danger','text'=>'Name cannot be empty.'];
        } elseif (!file_exists($old_path)) {
            $msg = ['type'=>'danger','text'=>'File not found.'];
        } else {
            $ext  = strtolower(pathinfo($old_name, PATHINFO_EXTENSION));
            $base = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $new_name);
            $base = trim($base,'_') ?: 'image';
            $new_file = $base . '.' . $ext;
            $n = 1;
            while (file_exists($dir . $new_file)) { $new_file = $base . '-' . $n++ . '.' . $ext; }
            rename($old_path, $dir . $new_file);
            @chmod($dir . $new_file, 0644);
            $msg = ['type'=>'success','text'=>"Renamed to: {$new_file}"];
        }
    }

    // ── Move between folders ───────────────────────────────────────────────
    elseif ($action === 'move') {
        $from_sub = preg_replace('/[^a-zA-Z0-9\/\-_]/', '', $_POST['from_sub'] ?? 'uploads');
        $to_sub   = preg_replace('/[^a-zA-Z0-9\/\-_]/', '', $_POST['to_sub']   ?? 'uploads');
        $filename = basename($_POST['filename'] ?? '');
        $from_path = $base_upload . $from_sub . '/' . $filename;
        $to_dir    = $base_upload . $to_sub . '/';
        $to_path   = $to_dir . $filename;

        if (!$filename || !file_exists($from_path)) {
            $msg = ['type'=>'danger','text'=>'File not found.'];
        } elseif ($from_sub === $to_sub) {
            $msg = ['type'=>'danger','text'=>'Already in that folder.'];
        } else {
            if (!is_dir($to_dir)) @mkdir($to_dir, 0755, true);
            // If a file with same name exists in target, add a suffix
            if (file_exists($to_path)) {
                $ext  = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $base = pathinfo($filename, PATHINFO_FILENAME);
                $n = 1;
                while (file_exists($to_dir . $base . '-' . $n . '.' . $ext)) $n++;
                $to_path = $to_dir . $base . '-' . $n . '.' . $ext;
            }
            rename($from_path, $to_path);
            @chmod($to_path, 0644);
            $moved_name = basename($to_path);
            $msg = ['type'=>'success','text'=>"Moved '{$filename}' to '{$to_sub}'." . ($moved_name !== $filename ? " Saved as '{$moved_name}'." : '')];
        }
    }
}

// ── Delete (GET) ───────────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $safe = basename($_GET['delete']);
    $sub  = preg_replace('/[^a-zA-Z0-9\/\-_]/', '', $_GET['sub'] ?? 'uploads');
    $path = $base_upload . $sub . '/' . $safe;
    if (file_exists($path) && strpos(realpath($path), realpath($base_upload)) === 0) {
        unlink($path);
        $msg = ['type'=>'success','text'=>'Image deleted.'];
    }
}

// ── Scan all images ────────────────────────────────────────────────────────
$all_images = [];
$img_extensions = ['jpg','jpeg','png','webp','gif','svg'];
foreach ($subfolders as $sub) {
    $dir = $base_upload . $sub . '/';
    if (!is_dir($dir)) continue;
    foreach ($img_extensions as $ext) {
        foreach (glob($dir . '*.' . $ext) ?: [] as $file) {
            $all_images[] = ['sub'=>$sub,'file'=>basename($file),'size'=>filesize($file),'mtime'=>filemtime($file)];
        }
    }
}
usort($all_images, fn($a,$b) => $b['mtime'] <=> $a['mtime']);

$active_sub = $_GET['folder'] ?? 'all';
$filtered   = $active_sub === 'all' ? $all_images : array_values(array_filter($all_images, fn($i) => $i['sub'] === $active_sub));

include 'includes/admin-header.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js" defer></script>

<?php if ($msg): ?>
<div class="alert alert-<?= $msg['type'] ?>"><i class="fa fa-<?= $msg['type']==='success'?'check-circle':'exclamation-triangle' ?>"></i> <?= htmlspecialchars($msg['text']) ?></div>
<?php endif; ?>

<!-- ── Upload card ──────────────────────────────────────────────────────── -->
<div class="admin-card">
    <div class="admin-card-header">
        <h2>Upload Images</h2>
        <span class="td-gray">Max <?= $server_max ?> · Single file opens crop tool first</span>
    </div>
    <div class="admin-card-body">
        <form method="POST" enctype="multipart/form-data" id="upload-form">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="upload">
            <div class="form-grid">
                <div class="form-group">
                    <label>Save to Folder</label>
                    <select name="folder" id="sel-folder">
                        <?php foreach ($subfolders as $sf): ?>
                        <option value="<?= $sf ?>"><?= $sf ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Custom Filename <span class="td-gray">(optional — leave blank to keep original name)</span></label>
                    <input type="text" name="custom_name" placeholder="e.g. e1-main (no extension)">
                    <span class="form-help">Letters, numbers, hyphens, underscores. Extension added automatically.</span>
                </div>
                <div class="form-group form-full">
                    <label>Files</label>
                    <div class="upload-zone" id="drop-zone" onclick="document.getElementById('file-input').click()">
                        <i class="fa fa-cloud-arrow-up"></i>
                        <p><strong>Click to browse</strong> or drag &amp; drop</p>
                        <p class="form-help">JPG, PNG, WebP, GIF &nbsp;·&nbsp; Single file → crop tool &nbsp;·&nbsp; Multiple files → upload directly</p>
                    </div>
                    <input type="file" id="file-input" name="images[]" multiple accept="image/*" style="display:none">
                    <div id="file-preview" class="file-preview-grid"></div>
                    <div id="size-warning" class="size-warn">
                        <i class="fa fa-triangle-exclamation"></i> <span id="size-warning-text"></span>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary" id="upload-btn" disabled><i class="fa fa-upload"></i> Upload</button>
                <span id="file-count" class="td-gray"></span>
            </div>
        </form>
    </div>
</div>

<!-- ── Shared crop modal (upload new + crop existing) ───────────────────── -->
<div id="crop-modal">
    <p id="crop-modal-label" class="crop-modal-label"></p>
    <div class="crop-ratio-row" id="ratio-btns">
        <button class="ratio-btn active" data-ratio="">Free</button>
        <button class="ratio-btn" data-ratio="1">1:1</button>
        <button class="ratio-btn" data-ratio="1.7778">16:9</button>
        <button class="ratio-btn" data-ratio="1.3333">4:3</button>
        <button class="ratio-btn" data-ratio="0.5625">9:16</button>
    </div>
    <div id="crop-wrap"><img id="crop-img" src="" alt=""></div>
    <div class="crop-btn-row" id="crop-btns">
        <button class="crop-btn crop-btn-secondary" id="skip-crop-btn"><i class="fa fa-upload"></i> Upload Original</button>
        <button class="crop-btn crop-btn-primary"   id="do-crop-btn"><i class="fa fa-crop"></i> Crop &amp; Save</button>
        <button class="crop-btn crop-btn-secondary" id="cancel-crop-btn"><i class="fa fa-xmark"></i> Cancel</button>
    </div>
</div>

<!-- ── Filter tabs ──────────────────────────────────────────────────────── -->
<div class="media-filter">
    <a href="?folder=all" class="media-tab <?= $active_sub==='all'?'active':'' ?>">All (<?= count($all_images) ?>)</a>
    <?php foreach ($subfolders as $sub):
        $cnt = count(array_filter($all_images, fn($i) => $i['sub'] === $sub));
        if (!$cnt) continue;
    ?>
    <a href="?folder=<?= urlencode($sub) ?>" class="media-tab <?= $active_sub===$sub?'active':'' ?>"><?= $sub ?> (<?= $cnt ?>)</a>
    <?php endforeach; ?>
</div>

<!-- ── Image grid ───────────────────────────────────────────────────────── -->
<?php if (empty($filtered)): ?>
<div class="admin-card"><div class="admin-card-body td-center td-gray" style="padding:3rem;">No images yet. Upload some above.</div></div>
<?php else: ?>
<div class="media-grid">
    <?php foreach ($filtered as $idx => $img):
        $web_path = '../assets/images/' . $img['sub'] . '/' . $img['file'];
    ?>
    <div class="media-card" id="card-<?= $idx ?>">
        <div class="media-thumb">
            <img src="<?= htmlspecialchars($web_path) ?>" alt="" loading="lazy">
        </div>
        <div class="media-card-body">
            <p class="media-card-filename"><?= htmlspecialchars($img['file']) ?></p>
            <p class="media-card-meta"><?= htmlspecialchars($img['sub']) ?> · <?= round($img['size']/1024) ?>KB</p>
            <div class="media-card-actions">
                <button class="btn btn-secondary btn-sm btn-copy-name"
                        onclick="copyPath('<?= htmlspecialchars($img['file'],ENT_QUOTES) ?>')" title="Copy filename">
                    <i class="fa fa-copy"></i> Copy
                </button>
                <button class="btn btn-secondary btn-sm btn-icon" title="Crop / Rename / Move"
                        onclick="toggleEdit('ep-<?= $idx ?>')">
                    <i class="fa fa-pen"></i>
                </button>
                <a href="?delete=<?= urlencode($img['file']) ?>&sub=<?= urlencode($img['sub']) ?>&folder=<?= urlencode($active_sub) ?>"
                   class="btn btn-danger btn-sm btn-icon" onclick="return confirm('Delete this image?')" title="Delete">
                    <i class="fa fa-trash"></i>
                </a>
            </div>
        </div>

        <!-- Edit panel: crop existing + rename + move ─────────────────── -->
        <div class="edit-panel" id="ep-<?= $idx ?>">

            <!-- Crop existing -->
            <label>Crop Image</label>
            <button class="btn btn-secondary btn-sm btn-full"
                    onclick="openCropExisting('<?= htmlspecialchars($web_path,ENT_QUOTES) ?>','<?= htmlspecialchars($img['file'],ENT_QUOTES) ?>','<?= htmlspecialchars($img['sub'],ENT_QUOTES) ?>')">
                <i class="fa fa-crop"></i> Open Crop Tool
            </button>

            <!-- Rename -->
            <label>Rename (no extension)</label>
            <div class="edit-row">
                <input type="text" id="ri-<?= $idx ?>" value="<?= htmlspecialchars(pathinfo($img['file'],PATHINFO_FILENAME)) ?>">
                <button class="btn btn-primary btn-sm"
                        onclick="doRename(<?= $idx ?>,'<?= htmlspecialchars($img['file'],ENT_QUOTES) ?>','<?= htmlspecialchars($img['sub'],ENT_QUOTES) ?>')">
                    <i class="fa fa-check"></i>
                </button>
            </div>

            <!-- Move -->
            <label>Move to Folder</label>
            <div class="edit-row">
                <select id="mv-<?= $idx ?>">
                    <?php foreach ($subfolders as $sf): ?>
                    <option value="<?= $sf ?>" <?= $sf===$img['sub']?'selected':'' ?>><?= $sf ?></option>
                    <?php endforeach; ?>
                </select>
                <button class="btn btn-secondary btn-sm"
                        onclick="doMove(<?= $idx ?>,'<?= htmlspecialchars($img['file'],ENT_QUOTES) ?>','<?= htmlspecialchars($img['sub'],ENT_QUOTES) ?>')">
                    <i class="fa fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div id="copy-toast" class="copy-toast">
    <i class="fa fa-check"></i> Filename copied!
</div>

<!-- Hidden forms for rename, move, crop-existing ─────────────────────── -->
<form method="POST" id="rename-form" style="display:none;">
    <?= csrf_field() ?>
    <input type="hidden" name="action"   value="rename">
    <input type="hidden" name="sub"      id="rf-sub">
    <input type="hidden" name="old_name" id="rf-old">
    <input type="hidden" name="new_name" id="rf-new">
</form>
<form method="POST" id="move-form" style="display:none;">
    <?= csrf_field() ?>
    <input type="hidden" name="action"   value="move">
    <input type="hidden" name="from_sub" id="mf-from">
    <input type="hidden" name="to_sub"   id="mf-to">
    <input type="hidden" name="filename" id="mf-file">
</form>
<form method="POST" id="crop-existing-form" style="display:none;">
    <?= csrf_field() ?>
    <input type="hidden" name="action"       value="crop_existing">
    <input type="hidden" name="sub"          id="ce-sub">
    <input type="hidden" name="filename"     id="ce-filename">
    <input type="hidden" name="cropped_data" id="ce-data">
</form>

<script>
// ── Upload crop flow ───────────────────────────────────────────────────────
const fileInput  = document.getElementById('file-input');
const uploadForm = document.getElementById('upload-form');
const dropZone   = document.getElementById('drop-zone');
const cropModal  = document.getElementById('crop-modal');
const cropImg    = document.getElementById('crop-img');

let cropper       = null;
let pendingFile   = null;   // for new uploads
let cropMode      = 'upload'; // 'upload' or 'existing'
let existingSub   = '';
let existingFile  = '';

// ── Ratio buttons (shared by both modes) ────────────────────────────────
document.querySelectorAll('.ratio-btn').forEach(b => b.addEventListener('click', function () {
    document.querySelectorAll('.ratio-btn').forEach(x => x.classList.remove('active'));
    this.classList.add('active');
    if (cropper) cropper.setAspectRatio(this.dataset.ratio ? parseFloat(this.dataset.ratio) : NaN);
}));

// ── Upload: single-file intercept ────────────────────────────────────────
fileInput.addEventListener('change', function () {
    if (!this.files.length) return;
    if (this.files.length === 1) {
        pendingFile = this.files[0];
        cropMode = 'upload';
        openCropperWithSrc(URL.createObjectURL(pendingFile), 'Crop before uploading: ' + pendingFile.name);
        document.getElementById('skip-crop-btn').style.display = '';
        this.value = '';
    } else {
        updatePreview([...this.files]);
        document.getElementById('upload-btn').disabled = false;
        document.getElementById('file-count').textContent = this.files.length + ' file(s) selected';
    }
});

// Drag & drop
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('dragover'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
    e.preventDefault(); dropZone.classList.remove('dragover');
    const files = [...e.dataTransfer.files].filter(f => f.type.startsWith('image/'));
    if (!files.length) return;
    if (files.length === 1) {
        pendingFile = files[0];
        cropMode = 'upload';
        openCropperWithSrc(URL.createObjectURL(pendingFile), 'Crop before uploading: ' + pendingFile.name);
        document.getElementById('skip-crop-btn').style.display = '';
    } else {
        const dt = new DataTransfer();
        files.forEach(f => dt.items.add(f));
        fileInput.files = dt.files;
        updatePreview(files);
        document.getElementById('upload-btn').disabled = false;
        document.getElementById('file-count').textContent = files.length + ' file(s) selected';
    }
});

// ── Crop existing image ──────────────────────────────────────────────────
function openCropExisting(webPath, filename, sub) {
    cropMode     = 'existing';
    existingSub  = sub;
    existingFile = filename;
    // Add cache-busting so we see the current version if it was cropped before
    openCropperWithSrc(webPath + '?t=' + Date.now(), 'Editing: ' + filename);
    document.getElementById('skip-crop-btn').style.display = 'none'; // hide "Upload Original" for existing
}

function openCropperWithSrc(src, label) {
    document.getElementById('crop-modal-label').textContent = label || '';
    cropImg.src = src;
    cropModal.classList.add('open');
    if (cropper) { cropper.destroy(); cropper = null; }
    cropImg.onload = function() {
        cropper = new Cropper(cropImg, { viewMode:1, autoCropArea:1, movable:true, zoomable:true, background:false });
    };
}

// Skip crop (upload only)
document.getElementById('skip-crop-btn').addEventListener('click', () => {
    closeCropper();
    setFileInputTo(pendingFile);
});

// Crop & save / Crop & upload
document.getElementById('do-crop-btn').addEventListener('click', () => {
    if (!cropper) return;

    if (cropMode === 'upload') {
        // New file upload — put cropped blob into the file input
        const mime = pendingFile.type === 'image/png' ? 'image/png' : 'image/jpeg';
        cropper.getCroppedCanvas({maxWidth:2560,maxHeight:2560}).toBlob(blob => {
            closeCropper();
            setFileInputTo(new File([blob], pendingFile.name, {type: mime}));
        }, mime, 0.92);

    } else {
        // Existing file — send as base64 data-URL via hidden form
        const ext  = existingFile.split('.').pop().toLowerCase();
        const mime = ext === 'png' ? 'image/png' : 'image/jpeg';
        const dataUrl = cropper.getCroppedCanvas({maxWidth:2560,maxHeight:2560}).toDataURL(mime, 0.92);

        // Warn if the base64 payload might exceed PHP's post_max_size
        const estBytes = Math.round(dataUrl.length * 0.75);
        const postMax  = <?= (int)(return_bytes(ini_get('post_max_size')) * 0.9) ?>;
        if (estBytes > postMax) {
            alert('The cropped image (' + (estBytes/1024/1024).toFixed(1) + ' MB) may exceed the server upload limit. Try cropping a smaller area or use a lower-resolution source image.');
            return;
        }

        closeCropper();
        document.getElementById('ce-sub').value      = existingSub;
        document.getElementById('ce-filename').value = existingFile;
        document.getElementById('ce-data').value     = dataUrl;
        document.getElementById('crop-existing-form').submit();
    }
});

document.getElementById('cancel-crop-btn').addEventListener('click', () => {
    if (cropMode === 'upload') {
        pendingFile = null;
        updatePreview([]);
        document.getElementById('upload-btn').disabled = true;
        document.getElementById('file-count').textContent = '';
    }
    closeCropper();
});

function closeCropper() {
    cropModal.classList.remove('open');
    if (cropper) { cropper.destroy(); cropper = null; }
}

function setFileInputTo(file) {
    const dt = new DataTransfer();
    dt.items.add(file);
    fileInput.files = dt.files;
    updatePreview([file]);
    document.getElementById('upload-btn').disabled = false;
    document.getElementById('file-count').textContent = '1 file ready';
}

// ── Thumbnails ────────────────────────────────────────────────────────────
function updatePreview(files) {
    const preview = document.getElementById('file-preview');
    const warn    = document.getElementById('size-warning');
    const warnTxt = document.getElementById('size-warning-text');
    preview.innerHTML = ''; warn.style.display = 'none';
    if (!files.length) return;
    let large = [];
    files.forEach(f => {
        if (f.size > 500*1024) large.push(f.name + ' (' + (f.size/1024/1024).toFixed(1) + 'MB)');
        const r = new FileReader();
        r.onload = e => {
            const wrap = document.createElement('div');
            wrap.className = 'file-thumb-wrap';
            const img = document.createElement('img');
            img.src = e.target.result;
            wrap.appendChild(img);
            if (f.size > 500*1024) {
                const b = document.createElement('span');
                b.className = 'file-thumb-badge';
                b.textContent = (f.size/1024/1024).toFixed(1)+'MB';
                wrap.appendChild(b);
            }
            preview.appendChild(wrap);
        };
        r.readAsDataURL(f);
    });
    if (large.length) { warnTxt.textContent = large.length + ' file(s) over 500KB: ' + large.join(', '); warn.style.display = 'block'; }
}

// ── Edit panel toggle ────────────────────────────────────────────────────
function toggleEdit(id) {
    document.querySelectorAll('.edit-panel.open').forEach(p => { if (p.id !== id) p.classList.remove('open'); });
    document.getElementById(id).classList.toggle('open');
}
document.addEventListener('click', e => {
    if (!e.target.closest('[id^="card-"]'))
        document.querySelectorAll('.edit-panel.open').forEach(p => p.classList.remove('open'));
});

// ── Rename ────────────────────────────────────────────────────────────────
function doRename(idx, oldName, sub) {
    const n = document.getElementById('ri-'+idx).value.trim();
    if (!n) { alert('Name cannot be empty.'); return; }
    document.getElementById('rf-sub').value = sub;
    document.getElementById('rf-old').value = oldName;
    document.getElementById('rf-new').value = n;
    document.getElementById('rename-form').submit();
}

// ── Move ──────────────────────────────────────────────────────────────────
function doMove(idx, filename, fromSub) {
    const toSub = document.getElementById('mv-'+idx).value;
    if (toSub === fromSub) { alert('Already in that folder.'); return; }
    if (!confirm('Move "' + filename + '" from "' + fromSub + '" to "' + toSub + '"?')) return;
    document.getElementById('mf-from').value = fromSub;
    document.getElementById('mf-to').value   = toSub;
    document.getElementById('mf-file').value = filename;
    document.getElementById('move-form').submit();
}

// ── Copy filename ─────────────────────────────────────────────────────────
function copyPath(filename) {
    navigator.clipboard.writeText(filename).then(() => {
        const t = document.getElementById('copy-toast');
        t.style.display = 'block'; setTimeout(() => t.style.display = 'none', 2000);
    });
}
</script>

<?php include 'includes/admin-footer.php'; ?>
