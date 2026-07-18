<?php
require_once __DIR__ . '/includes/auth.php';
auth_require();

$adminTitle     = '3D Car';
$adminTitleSpan = 'Model';
$msg = '';

$models_dir = dirname(dirname(__FILE__)) . '/assets/models';
if (!is_dir($models_dir)) @mkdir($models_dir, 0755, true);

// Fix permissions on existing models (same as media.php image fix)
foreach (glob($models_dir . '/*.{glb,gltf}', GLOB_BRACE) as $f) {
    $perms = fileperms($f) & 0777;
    if ($perms < 0644) @chmod($f, 0644);
}

// Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && empty($_FILES)) {
    $msg = ['type'=>'danger','text'=>'Upload too large! Server limit is ' . (ini_get('upload_max_filesize') ?: '2M') . '.'];
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    if (isset($_POST['save_model'])) {
        // Save which model file to use
        $settings = data_load('settings.json');
        $settings = is_array($settings) ? $settings : [];
        $settings['car_model_file'] = trim($_POST['model_file'] ?? '');
        data_save('settings.json', $settings);
        $msg = ['type'=>'success','text'=>'Model settings saved.'];
    }

    if (isset($_POST['save_adjustments'])) {
        $settings = data_load('settings.json');
        $settings['model_rotate_x'] = (float)($_POST['model_rotate_x'] ?? -90);
        $settings['model_rotate_y'] = (float)($_POST['model_rotate_y'] ?? 0);
        $settings['model_rotate_z'] = (float)($_POST['model_rotate_z'] ?? 0);
        $settings['model_height']   = (float)($_POST['model_height'] ?? 0.65);
        $settings['model_scale']    = (float)($_POST['model_scale'] ?? 3.0);
        data_save('settings.json', $settings);
        $msg = ['type'=>'success','text'=>'Model position saved.'];
    }

    if (!empty($_FILES['model_upload']['name'])) {
        $file = $_FILES['model_upload'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['glb', 'gltf'];

        if (!in_array($ext, $allowed)) {
            $msg = ['type'=>'danger','text'=>'Only .glb and .gltf files are allowed.'];
        } elseif ($file['size'] > 50 * 1024 * 1024) {
            $msg = ['type'=>'danger','text'=>'File too large. Max 50MB.'];
        } elseif ($file['error'] !== UPLOAD_ERR_OK) {
            $msg = ['type'=>'danger','text'=>'Upload error: ' . $file['error']];
        } else {
            $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '-', $file['name']);
            $dest = $models_dir . '/' . $filename;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                // CRITICAL: Set readable permissions (same fix as image uploads)
                @chmod($dest, 0644);
                // Auto-set as active model
                $settings = data_load('settings.json');
                $settings = is_array($settings) ? $settings : [];
                $settings['car_model_file'] = $filename;
                data_save('settings.json', $settings);
                $msg = ['type'=>'success','text'=>"Model \"{$filename}\" uploaded and set as active."];
            } else {
                $msg = ['type'=>'danger','text'=>'Failed to move uploaded file.'];
            }
        }
    }

    if (isset($_POST['delete_model'])) {
        $fname = basename($_POST['delete_model']);
        $path = $models_dir . '/' . $fname;
        if (file_exists($path) && unlink($path)) {
            // Clear if it was the active model
            $settings = data_load('settings.json');
            if (($settings['car_model_file'] ?? '') === $fname) {
                $settings['car_model_file'] = '';
                data_save('settings.json', $settings);
            }
            $msg = ['type'=>'success','text'=>"Deleted \"{$fname}\"."];
        }
    }
}

// Get current settings and models list
$settings = data_load('settings.json');
$active_model = $settings['car_model_file'] ?? '';

$models = [];
if (is_dir($models_dir)) {
    foreach (scandir($models_dir) as $f) {
        if ($f === '.' || $f === '..') continue;
        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
        if (in_array($ext, ['glb', 'gltf'])) {
            $models[] = [
                'name' => $f,
                'size' => filesize($models_dir . '/' . $f),
                'date' => filemtime($models_dir . '/' . $f),
                'active' => ($f === $active_model),
            ];
        }
    }
}

$topbarActions = '';
include 'includes/admin-header.php';
?>

<?php if ($msg): ?>
<div class="alert alert-<?= $msg['type'] ?>"><i class="fa fa-<?= $msg['type']==='success'?'check-circle':'exclamation-circle' ?>"></i><?= htmlspecialchars($msg['text']) ?></div>
<?php endif; ?>

<!-- Upload -->
<div class="admin-card">
    <div class="admin-card-header"><h2>Upload 3D Model</h2></div>
    <div class="admin-card-body">
        <form method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
            <p class="section-note">
                Upload a <strong>.glb</strong> or <strong>.gltf</strong> file of the current car.
                This model is displayed in the interactive 3D viewer on the homepage hero section.
                You can export .glb files from CAD software like SolidWorks, Fusion 360, or Blender.
            </p>
            <div class="form-grid">
                <div class="form-group form-full">
                    <label>Model File (.glb or .gltf)</label>
                    <input type="file" name="model_upload" accept=".glb,.gltf">
                    <span class="form-help">Max 50MB. GLB (binary) recommended for best performance.</span>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> Upload Model</button>
            </div>
        </form>
    </div>
</div>

<!-- Existing Models -->
<div class="admin-card">
    <div class="admin-card-header"><h2>Uploaded Models (<?= count($models) ?>)</h2></div>
    <div class="admin-card-body" style="padding:0">
        <table class="admin-table">
            <thead><tr><th>File</th><th>Size</th><th>Uploaded</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php if (empty($models)): ?>
            <tr><td colspan="5" class="td-center" style="padding:2rem;color:var(--gray);">
                No 3D models uploaded yet. The homepage will show a wireframe placeholder until a model is uploaded.
            </td></tr>
            <?php endif; ?>
            <?php foreach ($models as $m): ?>
            <tr>
                <td><strong><i class="fa fa-cube" style="color:var(--orange);margin-right:.4rem;"></i><?= htmlspecialchars($m['name']) ?></strong></td>
                <td class="td-gray"><?= number_format($m['size'] / 1024 / 1024, 1) ?> MB</td>
                <td class="td-gray"><?= date('M j, Y', $m['date']) ?></td>
                <td>
                    <?php if ($m['active']): ?>
                    <span style="color:var(--orange);font-weight:600;font-size:.8rem;text-transform:uppercase;letter-spacing:.08em;">
                        <i class="fa fa-check-circle"></i> Active
                    </span>
                    <?php else: ?>
                    <form method="POST" class="form-inline">
                    <?= csrf_field() ?>
                        <input type="hidden" name="save_model" value="1">
                        <input type="hidden" name="model_file" value="<?= htmlspecialchars($m['name']) ?>">
                        <button type="submit" class="btn btn-secondary btn-sm">Set Active</button>
                    </form>
                    <?php endif; ?>
                </td>
                <td>
                    <form method="POST" class="form-inline" onsubmit="return confirm('Delete this model file?')">
                    <?= csrf_field() ?>
                        <input type="hidden" name="delete_model" value="<?= htmlspecialchars($m['name']) ?>">
                        <button type="submit" class="btn btn-danger btn-sm btn-icon"><i class="fa fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Info -->
<?php if ($active_model):
    $model_url = '../assets/models/' . $active_model;
    $rx = $settings['model_rotate_x'] ?? -90;
    $ry = $settings['model_rotate_y'] ?? 0;
    $rz = $settings['model_rotate_z'] ?? 0;
    $mh = $settings['model_height'] ?? 0.65;
    $ms = $settings['model_scale'] ?? 3.0;
?>
<div class="admin-card">
    <div class="admin-card-header"><h2><i class="fa fa-sliders" style="margin-right:.4rem;"></i> Position & Rotation</h2></div>
    <div class="admin-card-body">
        <p class="section-note">Adjust the model so it sits on the disc correctly. Changes update the preview in real time. Click Save when it looks right.</p>

        <div class="model-adj-grid">
            <!-- Preview -->
            <div>
                <div id="model-preview" class="model-preview"></div>
                <div class="model-orbit-hint">Drag to orbit · Scroll to zoom</div>
            </div>

            <!-- Controls -->
            <div>
                <form method="POST" id="adj-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="save_adjustments" value="1">

                    <div class="slider-group">
                        <label class="slider-label">
                            <span>Rotate X</span>
                            <div class="flex-row">
                                <input type="number" id="rx-num" value="<?= $rx ?>" min="-180" max="180" step="1" class="num-input" oninput="document.getElementById('rx').value=this.value;updatePreview()">
                                <span>°</span>
                            </div>
                        </label>
                        <input type="range" name="model_rotate_x" id="rx" min="-180" max="180" step="1" value="<?= $rx ?>" style="width:100%;" oninput="document.getElementById('rx-num').value=this.value;updatePreview()">
                    </div>

                    <div class="slider-group">
                        <label class="slider-label">
                            <span>Rotate Y</span>
                            <div class="flex-row">
                                <input type="number" id="ry-num" value="<?= $ry ?>" min="-180" max="180" step="1" class="num-input" oninput="document.getElementById('ry').value=this.value;updatePreview()">
                                <span>°</span>
                            </div>
                        </label>
                        <input type="range" name="model_rotate_y" id="ry" min="-180" max="180" step="1" value="<?= $ry ?>" style="width:100%;" oninput="document.getElementById('ry-num').value=this.value;updatePreview()">
                    </div>

                    <div class="slider-group">
                        <label class="slider-label">
                            <span>Rotate Z</span>
                            <div class="flex-row">
                                <input type="number" id="rz-num" value="<?= $rz ?>" min="-180" max="180" step="1" class="num-input" oninput="document.getElementById('rz').value=this.value;updatePreview()">
                                <span>°</span>
                            </div>
                        </label>
                        <input type="range" name="model_rotate_z" id="rz" min="-180" max="180" step="1" value="<?= $rz ?>" style="width:100%;" oninput="document.getElementById('rz-num').value=this.value;updatePreview()">
                    </div>

                    <div class="slider-group">
                        <label class="slider-label">
                            <span>Height</span>
                            <input type="number" id="mh-num" value="<?= $mh ?>" min="-1" max="3" step="0.05" class="num-input" oninput="document.getElementById('mh').value=this.value;updatePreview()">
                        </label>
                        <input type="range" name="model_height" id="mh" min="-1" max="3" step="0.05" value="<?= $mh ?>" style="width:100%;" oninput="document.getElementById('mh-num').value=this.value;updatePreview()">
                    </div>

                    <div class="slider-group">
                        <label class="slider-label">
                            <span>Scale</span>
                            <input type="number" id="ms-num" value="<?= $ms ?>" min="0.5" max="8" step="0.1" class="num-input" oninput="document.getElementById('ms').value=this.value;updatePreview()">
                        </label>
                        <input type="range" name="model_scale" id="ms" min="0.5" max="8" step="0.1" value="<?= $ms ?>" style="width:100%;" oninput="document.getElementById('ms-num').value=this.value;updatePreview()">
                    </div>

                    <div class="slider-btn-row">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Position</button>
                        <button type="button" class="btn btn-secondary" onclick="resetDefaults()"><i class="fa fa-undo"></i> Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 3D Preview Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
<script>
(function(){
    var container = document.getElementById('model-preview');
    if (!container) return;

    var ORANGE = 0xF76902;
    var scene = new THREE.Scene();
    var camera = new THREE.PerspectiveCamera(40, container.clientWidth / container.clientHeight, 0.1, 100);
    var renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setClearColor(0x0a0a0a, 1);
    renderer.setSize(container.clientWidth, container.clientHeight);
    container.appendChild(renderer.domElement);

    camera.position.set(4, 2.2, 5);
    camera.lookAt(0, 0.65, 0);

    scene.add(new THREE.AmbientLight(0xffffff, 0.4));
    var key = new THREE.DirectionalLight(0xffffff, 0.9); key.position.set(5, 8, 5); scene.add(key);
    var fill = new THREE.DirectionalLight(ORANGE, 0.35); fill.position.set(-4, 3, -2); scene.add(fill);

    // Platform
    var plat = new THREE.Mesh(new THREE.CylinderGeometry(2.2, 2.2, 0.04, 64),
        new THREE.MeshStandardMaterial({ color: 0x111111, metalness: 0.8, roughness: 0.3 }));
    plat.position.y = -0.02; scene.add(plat);
    var ring = new THREE.Mesh(new THREE.TorusGeometry(2.2, 0.015, 8, 128),
        new THREE.MeshStandardMaterial({ color: ORANGE, emissive: ORANGE, emissiveIntensity: 0.5 }));
    ring.rotation.x = Math.PI / 2; scene.add(ring);

    // Load model
    var carGroup = new THREE.Group();
    scene.add(carGroup);
    var loadedModel = null;
    var origBox = null;

    var loader = new THREE.GLTFLoader();
    loader.load('<?= $model_url ?>', function(gltf) {
        loadedModel = gltf.scene;
        carGroup.add(loadedModel);
        applyTransform();
    });

    window.applyTransform = function() {
        if (!loadedModel) return;
        var rx = parseFloat(document.getElementById('rx').value) * Math.PI / 180;
        var ry = parseFloat(document.getElementById('ry').value) * Math.PI / 180;
        var rz = parseFloat(document.getElementById('rz').value) * Math.PI / 180;
        var h  = parseFloat(document.getElementById('mh').value);
        var s  = parseFloat(document.getElementById('ms').value);

        loadedModel.rotation.set(rx, ry, rz);
        loadedModel.scale.setScalar(1); // reset scale to get true bounds
        loadedModel.position.set(0, 0, 0);

        // Auto-scale
        var box = new THREE.Box3().setFromObject(loadedModel);
        var size = box.getSize(new THREE.Vector3());
        var maxDim = Math.max(size.x, size.y, size.z);
        var scale = s / maxDim;
        loadedModel.scale.setScalar(scale);

        // Re-center
        box.setFromObject(loadedModel);
        var center = box.getCenter(new THREE.Vector3());
        loadedModel.position.sub(center);
        loadedModel.position.y -= box.min.y;
        loadedModel.position.y += h;
    };

    window.updatePreview = function() {
        applyTransform();
    };

    window.resetDefaults = function() {
        document.getElementById('rx').value = -90; document.getElementById('rx-num').value = -90;
        document.getElementById('ry').value = 0;   document.getElementById('ry-num').value = 0;
        document.getElementById('rz').value = 0;   document.getElementById('rz-num').value = 0;
        document.getElementById('mh').value = 0.65; document.getElementById('mh-num').value = 0.65;
        document.getElementById('ms').value = 3.0;  document.getElementById('ms-num').value = 3.0;
        updatePreview();
    };

    // Orbit controls
    var isDrag = false, prevX = 0, prevY = 0, autoRot = true, autoT = null;
    var sph = { theta: Math.atan2(4, 5), phi: Math.acos(2.2 / Math.sqrt(4*4+2.2*2.2+5*5)), radius: Math.sqrt(4*4+2.2*2.2+5*5) };

    function updCam() {
        var sp = Math.sin(sph.phi);
        camera.position.set(sph.radius*sp*Math.sin(sph.theta), sph.radius*Math.cos(sph.phi), sph.radius*sp*Math.cos(sph.theta));
        camera.lookAt(0, 0.65, 0);
    }
    container.addEventListener('mousedown', function(e){ isDrag=true; autoRot=false; clearTimeout(autoT); prevX=e.clientX; prevY=e.clientY; container.style.cursor='grabbing'; });
    container.addEventListener('mousemove', function(e){ if(!isDrag)return; sph.theta-=(e.clientX-prevX)*0.008; sph.phi=Math.max(0.4,Math.min(Math.PI*0.48,sph.phi+(e.clientY-prevY)*0.008)); prevX=e.clientX; prevY=e.clientY; updCam(); });
    container.addEventListener('mouseup', function(){ isDrag=false; container.style.cursor='grab'; autoT=setTimeout(function(){autoRot=true;},3000); });
    container.addEventListener('mouseleave', function(){ isDrag=false; container.style.cursor='grab'; });
    container.addEventListener('wheel', function(e){ e.preventDefault(); sph.radius=Math.max(3.5,Math.min(12,sph.radius+e.deltaY*0.005)); updCam(); }, {passive:false});

    // Animate
    var clock = new THREE.Clock();
    function anim() {
        requestAnimationFrame(anim);
        if (autoRot) { sph.theta += clock.getDelta()*0.25; updCam(); } else { clock.getDelta(); }
        renderer.render(scene, camera);
    }
    anim();

    window.addEventListener('resize', function() {
        var w = container.clientWidth, h = container.clientHeight;
        camera.aspect = w/h; camera.updateProjectionMatrix(); renderer.setSize(w, h);
    });
})();
</script>
<?php endif; ?>

<!-- How It Works -->
<div class="admin-card">
    <div class="admin-card-header"><h2>How It Works</h2></div>
    <div class="admin-card-body">
        <div style="color:var(--gray);font-size:.85rem;line-height:1.7;">
            <p><strong>The homepage features an interactive 3D viewer</strong> where visitors can drag to orbit around the car and scroll to zoom.</p>
            <p style="margin-top:.75rem;"><strong>To add your car model:</strong></p>
            <ol style="margin-top:.5rem;padding-left:1.5rem;">
                <li>Export your CAD model as a <code>.glb</code> file (SolidWorks → Blender → Export GLB, or Fusion 360 → Export → GLB)</li>
                <li>Keep the file under 50MB — simplify meshes if needed for web performance</li>
                <li>Upload it using the form above</li>
                <li>It will automatically become the active model on the homepage</li>
            </ol>
            <p style="margin-top:.75rem;"><strong>Tips for best results:</strong></p>
            <ul style="margin-top:.5rem;padding-left:1.5rem;">
                <li>Reduce polygon count for web (aim for under 500k triangles)</li>
                <li>Use GLB (binary) format rather than GLTF for smaller file size</li>
                <li>Bake textures at reasonable resolution (1024×1024 or 2048×2048)</li>
                <li>Center the model at origin in your 3D software before exporting</li>
            </ul>
            <p style="margin-top:.75rem;">If no model is uploaded, a wireframe placeholder car will be shown instead.</p>
        </div>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>
