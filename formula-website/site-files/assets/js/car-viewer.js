/**
 * RIT Racing — 3D Car Model Viewer
 * Interactive orbit viewer for the hero section.
 * Requires Three.js r128 loaded before this script.
 *
 * Orbit controls:
 *   - Drag / touch: orbits camera. Dragging RIGHT rotates car RIGHT (theta +=).
 *     This matches natural swipe expectation — the car follows your finger.
 *   - Scroll wheel: zoom in/out
 *   - Auto-rotate: resumes 3 seconds after last interaction
 *
 * Model settings are read from window.RIT_MODEL_SETTINGS (set by index.php):
 *   rotate_x, rotate_y, rotate_z (degrees), height (float), scale (float)
 *
 * If no GLB is uploaded, a wireframe placeholder car is rendered instead.
 */
(function () {
    'use strict';

    var container = document.getElementById('hero-3d-viewer');
    if (!container) {
        return;
    }

    if (typeof THREE === 'undefined') {
        return;
    }

    // Force container to have dimensions if CSS isn't giving them
    var w = container.clientWidth;
    var h = container.clientHeight;

    if (w < 10 || h < 10) {
        // CSS layout hasn't given us dimensions — force them
        container.style.width = '100%';
        container.style.height = '500px';
        w = container.clientWidth;
        h = container.clientHeight;
    }

    try {
        initViewer(container);
    } catch(e) {
    }
})();

function initViewer(container) {
    var RIT_ORANGE = 0xF76902;

    // ── Scene ────────────────────────────────────────────────
    var scene    = new THREE.Scene();
    var camera   = new THREE.PerspectiveCamera(40, 1, 0.1, 100);
    var renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });

    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setClearColor(0x000000, 0);
    container.appendChild(renderer.domElement);

    camera.position.set(4, 2.2, 5);
    var lookAtY = (window.RIT_MODEL_SETTINGS && window.RIT_MODEL_SETTINGS.height !== undefined) ? window.RIT_MODEL_SETTINGS.height : 0.65;
    camera.lookAt(0, lookAtY, 0);

    // ── Lights ───────────────────────────────────────────────
    scene.add(new THREE.AmbientLight(0xffffff, 0.4));

    var key = new THREE.DirectionalLight(0xffffff, 0.9);
    key.position.set(5, 8, 5);
    scene.add(key);

    var fill = new THREE.DirectionalLight(RIT_ORANGE, 0.35);
    fill.position.set(-4, 3, -2);
    scene.add(fill);

    var rim = new THREE.PointLight(RIT_ORANGE, 0.6, 15);
    rim.position.set(-3, 1, -4);
    scene.add(rim);

    // ── Ground grid ──────────────────────────────────────────
    var gridPts = [];
    for (var i = -8; i <= 8; i++) {
        gridPts.push(-8, 0, i, 8, 0, i);
        gridPts.push(i, 0, -8, i, 0, 8);
    }
    var gridGeo = new THREE.BufferGeometry();
    gridGeo.setAttribute('position', new THREE.Float32BufferAttribute(gridPts, 3));
    var grid = new THREE.LineSegments(gridGeo,
        new THREE.LineBasicMaterial({ color: RIT_ORANGE, transparent: true, opacity: 0.08 }));
    grid.position.y = -0.01;
    scene.add(grid);

    // Circular platform
    var platformGeo = new THREE.CylinderGeometry(2.2, 2.2, 0.04, 64);
    var platform = new THREE.Mesh(platformGeo,
        new THREE.MeshStandardMaterial({ color: 0x111111, metalness: 0.8, roughness: 0.3 }));
    platform.position.y = -0.02;
    scene.add(platform);

    // Platform edge ring
    var ringGeo = new THREE.TorusGeometry(2.2, 0.015, 8, 128);
    var ringMat = new THREE.MeshStandardMaterial({
        color: RIT_ORANGE, emissive: RIT_ORANGE, emissiveIntensity: 0.5
    });
    var ring = new THREE.Mesh(ringGeo, ringMat);
    ring.rotation.x = Math.PI / 2;
    scene.add(ring);

    // ── Placeholder Car (shown when no GLB model is uploaded) ──
    var carGroup = new THREE.Group();
    scene.add(carGroup);

    function buildPlaceholderCar() {
        var wireMat = new THREE.MeshStandardMaterial({
            color: RIT_ORANGE, wireframe: true, transparent: true, opacity: 0.7
        });
        var solidMat = new THREE.MeshStandardMaterial({
            color: 0x1a1a1a, metalness: 0.6, roughness: 0.4
        });
        var orangeSolid = new THREE.MeshStandardMaterial({
            color: RIT_ORANGE, metalness: 0.5, roughness: 0.3
        });

        // Main body
        var bodyGeo = new THREE.BoxGeometry(3.2, 0.35, 1.3);
        var body = new THREE.Mesh(bodyGeo, solidMat);
        body.position.set(0, 0.35, 0);
        carGroup.add(body);
        carGroup.add(new THREE.Mesh(bodyGeo, wireMat).translateY(0.35));

        // Cockpit
        var cockpitGeo = new THREE.BoxGeometry(1.0, 0.45, 0.7);
        var cockpit = new THREE.Mesh(cockpitGeo, solidMat);
        cockpit.position.set(-0.2, 0.6, 0);
        carGroup.add(cockpit);
        var cw = new THREE.Mesh(cockpitGeo, wireMat);
        cw.position.set(-0.2, 0.6, 0);
        carGroup.add(cw);

        // Nose cone
        var noseGeo = new THREE.ConeGeometry(0.3, 1.0, 4);
        var nose = new THREE.Mesh(noseGeo, orangeSolid);
        nose.rotation.z = -Math.PI / 2;
        nose.position.set(2.1, 0.35, 0);
        carGroup.add(nose);

        // Front wing
        var fw = new THREE.Mesh(new THREE.BoxGeometry(0.4, 0.06, 1.8), orangeSolid);
        fw.position.set(1.8, 0.12, 0);
        carGroup.add(fw);
        [-0.9, 0.9].forEach(function(z) {
            var ep = new THREE.Mesh(new THREE.BoxGeometry(0.5, 0.2, 0.03), orangeSolid);
            ep.position.set(1.8, 0.12, z);
            carGroup.add(ep);
        });

        // Rear wing
        var rw = new THREE.Mesh(new THREE.BoxGeometry(0.3, 0.06, 1.5), orangeSolid);
        rw.position.set(-1.5, 0.9, 0);
        carGroup.add(rw);
        [-0.75, 0.75].forEach(function(z) {
            var ep = new THREE.Mesh(new THREE.BoxGeometry(0.4, 0.5, 0.03), orangeSolid);
            ep.position.set(-1.5, 0.7, z);
            carGroup.add(ep);
        });

        // Wheels
        var wheelGeo = new THREE.CylinderGeometry(0.22, 0.22, 0.18, 16);
        var wheelMat = new THREE.MeshStandardMaterial({ color: 0x333333, metalness: 0.7, roughness: 0.2 });
        [[1.1,0.22,0.75],[1.1,0.22,-0.75],[-1.1,0.22,0.75],[-1.1,0.22,-0.75]].forEach(function(pos) {
            var wheel = new THREE.Mesh(wheelGeo, wheelMat);
            wheel.rotation.x = Math.PI / 2;
            wheel.position.set(pos[0], pos[1], pos[2]);
            carGroup.add(wheel);
        });

        // Sidepods
        [-0.6, 0.6].forEach(function(z) {
            var sp = new THREE.Mesh(new THREE.BoxGeometry(1.2, 0.25, 0.25), solidMat);
            sp.position.set(-0.1, 0.38, z);
            carGroup.add(sp);
        });

        // Roll hoop
        var rh = new THREE.Mesh(new THREE.CylinderGeometry(0.08, 0.08, 0.4, 8), solidMat);
        rh.position.set(-0.6, 0.85, 0);
        carGroup.add(rh);
    }

    // ── Load GLB model or fall back to placeholder ────────────
    var modelUrl = window.RIT_CAR_MODEL || '';
    var modelSettings = window.RIT_MODEL_SETTINGS || {};
    var rotX = (modelSettings.rotate_x !== undefined ? modelSettings.rotate_x : -90) * Math.PI / 180;
    var rotY = (modelSettings.rotate_y !== undefined ? modelSettings.rotate_y : 0) * Math.PI / 180;
    var rotZ = (modelSettings.rotate_z !== undefined ? modelSettings.rotate_z : 0) * Math.PI / 180;
    var modelHeight = modelSettings.height !== undefined ? modelSettings.height : 0.65;
    var modelScale  = modelSettings.scale  !== undefined ? modelSettings.scale  : 3.0;

    if (modelUrl && typeof THREE.GLTFLoader !== 'undefined') {
        var loader = new THREE.GLTFLoader();
        loader.load(modelUrl, function(gltf) {
            var model = gltf.scene;
            
            // Apply admin-configured rotation
            model.rotation.set(rotX, rotY, rotZ);
            
            // Auto-scale using admin-configured scale factor
            var box = new THREE.Box3().setFromObject(model);
            var size = box.getSize(new THREE.Vector3());
            var maxDim = Math.max(size.x, size.y, size.z);
            var scale = modelScale / maxDim;
            model.scale.setScalar(scale);
            // Re-center after scaling
            box.setFromObject(model);
            var center = box.getCenter(new THREE.Vector3());
            model.position.sub(center);
            model.position.y -= box.min.y;
            model.position.y += modelHeight;
            carGroup.add(model);
        }, undefined, function(err) {
            buildPlaceholderCar();
        });
    } else {
        buildPlaceholderCar();
    }

    // ── Orbit Controls ───────────────────────────────────────
    var isDragging = false;
    var prevX = 0, prevY = 0;
    var spherical = {
        theta: Math.atan2(camera.position.x, camera.position.z),
        phi: Math.acos(camera.position.y / camera.position.length()),
        radius: camera.position.length()
    };
    var autoRotate = true;
    var autoTimer = null;

    function updateCamera() {
        var sinPhi = Math.sin(spherical.phi);
        camera.position.set(
            spherical.radius * sinPhi * Math.sin(spherical.theta),
            spherical.radius * Math.cos(spherical.phi),
            spherical.radius * sinPhi * Math.cos(spherical.theta)
        );
        camera.lookAt(0, lookAtY, 0);
    }

    function onDown(e) {
        isDragging = true;
        autoRotate = false;
        clearTimeout(autoTimer);
        var pt = e.touches ? e.touches[0] : e;
        prevX = pt.clientX; prevY = pt.clientY;
        container.style.cursor = 'grabbing';
    }
    function onMove(e) {
        if (!isDragging) return;
        e.preventDefault();
        var pt = e.touches ? e.touches[0] : e;
        var dx = pt.clientX - prevX, dy = pt.clientY - prevY;
        prevX = pt.clientX; prevY = pt.clientY;
        spherical.theta += dx * 0.008;
        spherical.phi = Math.max(0.4, Math.min(Math.PI * 0.48, spherical.phi + dy * 0.008));
        updateCamera();
    }
    function onUp() {
        isDragging = false;
        container.style.cursor = 'grab';
        autoTimer = setTimeout(function() { autoRotate = true; }, 3000);
    }
    function onWheel(e) {
        e.preventDefault();
        spherical.radius = Math.max(3.5, Math.min(12, spherical.radius + e.deltaY * 0.005));
        updateCamera();
    }

    container.addEventListener('mousedown', onDown);
    container.addEventListener('mousemove', onMove);
    container.addEventListener('mouseup', onUp);
    container.addEventListener('mouseleave', onUp);
    container.addEventListener('touchstart', onDown, { passive: true });
    container.addEventListener('touchmove', onMove, { passive: false });
    container.addEventListener('touchend', onUp);
    container.addEventListener('wheel', onWheel, { passive: false });
    container.style.cursor = 'grab';

    // ── Resize ───────────────────────────────────────────────
    function resize() {
        var w = container.clientWidth || container.offsetWidth;
        var h = container.clientHeight || container.offsetHeight;
        if (w < 10 || h < 10) {
            // Fallback: use parent dimensions
            var parent = container.parentElement;
            if (parent) {
                w = parent.clientWidth || 400;
                h = parent.clientHeight || 400;
            }
        }
        if (w < 10) w = 400;
        if (h < 10) h = 400;
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
        renderer.setSize(w, h);
    }
    window.addEventListener('resize', resize);
    resize();

    // ── Animate ──────────────────────────────────────────────
    var clock = new THREE.Clock();

    function animate() {
        requestAnimationFrame(animate);
        var dt = clock.getDelta();
        if (autoRotate) {
            spherical.theta += dt * 0.25;
            updateCamera();
        }
        ring.material.emissiveIntensity = 0.3 + Math.sin(clock.elapsedTime * 1.5) * 0.2;
        renderer.render(scene, camera);
    }
    animate();
}
