<?php
$pageTitle       = 'Home';
$pageDescription = 'RIT Racing — Formula SAE team at Rochester Institute of Technology. 2024 FSAE Michigan Champions. Designing, building, and racing since 1991.';
require_once 'includes/header.php';
?>

<!-- ============================================================
     HERO (simplified: headline + sentence + 2 CTAs + lazy 3D)
     ============================================================ -->
<section class="hero" aria-label="Hero">

    <!-- Background -->
    <?php $hero_bg = site_image('hero_bg'); ?>
    <?php if ($hero_bg): ?>
    <div class="hero-bg img-wrapper">
        <img src="<?= $hero_bg ?>" alt="" role="presentation" fetchpriority="high" decoding="async">
    </div>
    <?php else: ?>
    <div class="hero-bg hero-bg-placeholder"></div>
    <?php endif; ?>

    <div class="hero-content">
        <div class="hero-inner-grid">
            <div class="hero-text">
                <p class="hero-label"><?= htmlspecialchars(get_setting('hero_label', 'Kate Gleason College of Engineering · Since 1991')) ?></p>

                <h1 class="hero-title">
                    <?= htmlspecialchars(get_setting('hero_headline_1', 'Built to')) ?><br>
                    <span class="accent"><?= htmlspecialchars(get_setting('hero_headline_2', 'Win.')) ?></span>
                </h1>

                <p class="hero-sub">
                    <?= htmlspecialchars(get_setting('hero_subtext', "RIT Racing is Rochester Institute of Technology's Formula SAE team — designing, fabricating, and racing open-wheel electric race cars.")) ?>
                </p>

                <div class="hero-actions">
                    <a href="<?= url('join.php') ?>" class="btn btn-primary">
                        <i class="fa fa-users" aria-hidden="true"></i> Join the Team
                    </a>
                    <a href="<?= url('contribute.php') ?>" class="btn btn-outline">
                        <i class="fa fa-handshake" aria-hidden="true"></i> Sponsor Us
                    </a>
                </div>
            </div>

            <div class="hero-viewer-col" id="hero-viewer-col">
                <div id="hero-3d-viewer" class="hero-3d-viewer"></div>
            </div>
        </div>
    </div>

</section>


<!-- ============================================================
     STATS ROW (driven by Admin → Settings → Homepage Stats Bar)
     ============================================================ -->
<?php
$hero_stats = get_setting('hero_stats', []);
if (empty($hero_stats) || !is_array($hero_stats)) {
    // Fallback defaults when admin hasn't configured stats yet
    $hero_stats = [
        ['number' => '34', 'suffix' => '+', 'label' => 'Years Competing'],
        ['number' => '8',  'suffix' => '',  'label' => "Overall at Michigan '25"],
        ['number' => '1',  'suffix' => '',  'label' => "Michigan '24 Champions"],
        ['number' => '30', 'suffix' => '+', 'label' => 'Active Members'],
    ];
}
?>
<section class="section-sm" style="background: var(--rit-dark-2); border-bottom: 1px solid var(--rit-dark-3);">
    <div class="container">
        <div class="stats-row reveal">
            <?php foreach ($hero_stats as $hs):
                $num    = htmlspecialchars($hs['number'] ?? '');
                $suffix = htmlspecialchars($hs['suffix'] ?? '');
                $label  = htmlspecialchars($hs['label']  ?? '');
                $display = $num . $suffix;
                // Only animate numbers that make sense counting from zero
                $is_numeric = ctype_digit($num);
            ?>
            <div class="stat-block">
                <?php if ($is_numeric): ?>
                <h3 data-count="<?= $num ?>"<?= $suffix ? ' data-suffix="' . $suffix . '"' : '' ?>><?= $display ?></h3>
                <?php else: ?>
                <h3><?= $display ?></h3>
                <?php endif; ?>
                <p><?= $label ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ============================================================
     COUNTDOWN TIMER(S)
     Managed from Admin → Homepage → Countdown Events
     ============================================================ -->
<?php
$cd_events = get_setting('countdown_events', []);
$cd_active = array_values(array_filter($cd_events, fn($e) => !empty($e['active']) && strtotime($e['date'] ?? '') > time()));
if (!empty($cd_active)):
?>
<section class="countdown-section">
    <div class="container">
        <div class="countdown-carousel" id="countdown-carousel">
            <?php foreach ($cd_active as $ci => $ev): ?>
            <div class="countdown-slide <?= $ci === 0 ? 'active' : '' ?>"
                 data-date="<?= htmlspecialchars($ev['date'] ?? '') ?>"
                 data-title="<?= htmlspecialchars($ev['title'] ?? '') ?>"
                 data-subtitle="<?= htmlspecialchars($ev['subtitle'] ?? '') ?>"
                 data-location="<?= htmlspecialchars($ev['location'] ?? '') ?>">
                <div class="countdown-info">
                    <p class="countdown-label"><i class="fa fa-flag-checkered"></i> Upcoming Event</p>
                    <h2 class="countdown-title"><?= htmlspecialchars($ev['title'] ?? '') ?></h2>
                    <?php if (!empty($ev['subtitle'])): ?><p class="countdown-subtitle"><?= htmlspecialchars($ev['subtitle']) ?></p><?php endif; ?>
                    <?php if (!empty($ev['location'])): ?>
                    <p class="countdown-location">
                        <i class="fa fa-location-dot"></i>
                        <a href="https://www.google.com/maps/search/<?= urlencode($ev['location']) ?>"
                           target="_blank" rel="noopener"
                           style="color:inherit;text-decoration:none;border-bottom:1px dashed rgba(107,107,107,.4);transition:color .15s;"
                           onmouseover="this.style.color='var(--rit-orange)'"
                           onmouseout="this.style.color=''"><?= htmlspecialchars($ev['location']) ?></a>
                    </p>
                    <?php endif; ?>
                    <div class="countdown-actions">
                        <button class="countdown-cal-btn" onclick="addToCalendar(this)" aria-label="Add to calendar">
                            <i class="fa fa-calendar-plus"></i> Add to Calendar
                        </button>
                    </div>
                </div>
                <div class="countdown-timer">
                    <div class="countdown-unit"><span class="countdown-num" data-unit="days">--</span><span class="countdown-lbl">Days</span></div>
                    <div class="countdown-sep">:</div>
                    <div class="countdown-unit"><span class="countdown-num" data-unit="hours">--</span><span class="countdown-lbl">Hours</span></div>
                    <div class="countdown-sep">:</div>
                    <div class="countdown-unit"><span class="countdown-num" data-unit="minutes">--</span><span class="countdown-lbl">Min</span></div>
                    <div class="countdown-sep">:</div>
                    <div class="countdown-unit"><span class="countdown-num" data-unit="seconds">--</span><span class="countdown-lbl">Sec</span></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if (count($cd_active) > 1): ?>
        <div class="countdown-nav">
            <?php foreach ($cd_active as $ci => $ev): ?>
            <button class="countdown-dot <?= $ci === 0 ? 'active' : '' ?>" onclick="goToSlide(<?= $ci ?>)" aria-label="Event <?= $ci + 1 ?>"></button>
            <?php endforeach; ?>
            <button class="countdown-arrow" onclick="nextSlide()" aria-label="Next event"><i class="fa fa-chevron-right"></i></button>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>


<!-- ============================================================
     FEATURED CARS
     ============================================================ -->
<section class="section">
    <div class="container">
        <div class="reveal">
            <p class="section-label">Our Vehicles</p>
            <h2 class="section-title">The Latest <span>Vehicles</span></h2>
        </div>

        <div class="car-card-grid">
            <?php
            $featured_cars = array_slice(get_vehicles('electric'), 0, 3);
            foreach ($featured_cars as $ci => $fcar):
                $has_photo = !empty($fcar['photo']);
                $desc_short = mb_strimwidth(explode("\n", $fcar['description'])[0] ?? '', 0, 180, '...');
            ?>
            <div class="car-card reveal reveal-delay-<?= $ci + 1 ?>">
                <?php if ($has_photo): ?>
                <div class="car-card-img img-wrapper">
                    <img src="<?= asset('images/cars/electric/' . $fcar['photo']) ?>" alt="<?= htmlspecialchars($fcar['name']) ?> race car" loading="lazy">
                </div>
                <?php else: ?>
                <div class="car-card-img img-placeholder">
                    <i class="fa fa-car" aria-hidden="true"></i>
                    <span><?= htmlspecialchars($fcar['name']) ?> Photo</span>
                </div>
                <?php endif; ?>
                <div class="car-card-body">
                    <p class="car-card-year"><?= htmlspecialchars($fcar['years']) ?></p>
                    <h3 class="car-card-name"><?= htmlspecialchars($fcar['name']) ?>
                        <?php if (!empty($fcar['badge'])): ?>
                        <span class="badge badge-<?= $fcar['badge_color'] ?: 'orange' ?>"><?= htmlspecialchars($fcar['badge']) ?></span>
                        <?php endif; ?>
                    </h3>
                    <p class="car-card-desc"><?= htmlspecialchars($desc_short) ?></p>
                    <?php if (!empty($fcar['result'])): ?>
                    <p class="car-card-result"><i class="fa fa-trophy" aria-hidden="true"></i> <?= htmlspecialchars($fcar['result']) ?></p>
                    <?php endif; ?>
                    <a href="<?= url('vehicles/electric.php') ?>#<?= htmlspecialchars($fcar['id']) ?>" class="btn btn-ghost car-card-cta">
                        Learn More <i class="fa fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="reveal vehicles-cta-row">
            <a href="<?= url('vehicles/electric.php') ?>" class="btn btn-outline">
                Full Electric History <i class="fa fa-arrow-right" aria-hidden="true"></i>
            </a>
            <a href="<?= url('vehicles/combustion.php') ?>" class="btn btn-outline">
                Combustion Legacy <i class="fa fa-arrow-right" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</section>


<!-- ============================================================
     ABOUT STRIP
     ============================================================ -->
<section class="section" style="background: var(--rit-dark-2);">
    <div class="container">
        <div class="two-col">
            <div class="content-block reveal">
                <p class="section-label"><?= htmlspecialchars(get_setting('about_strip_label', 'Who We Are')) ?></p>
                <h2 class="section-title"><?= htmlspecialchars(get_setting('about_strip_title_1', 'More Than a')) ?> <span><?= htmlspecialchars(get_setting('about_strip_title_2', 'Racing Team')) ?></span></h2>
                <div class="divider"></div>
                <?php foreach (explode("\n\n", get_setting('about_strip_text', '')) as $para):
                    $para = trim($para); if ($para): ?>
                <p><?= htmlspecialchars($para) ?></p>
                <?php endif; endforeach; ?>
                <?php $quote = get_setting('about_strip_quote', ''); if ($quote): ?>
                <div class="pullquote">
                    "<?= htmlspecialchars($quote) ?>"
                </div>
                <?php endif; ?>
                <a href="<?= url('about.php') ?>" class="btn btn-primary" style="margin-top:0.5rem;">
                    Our Story <i class="fa fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>

            <div class="reveal reveal-delay-2">
                <?php $team_photo = site_image('team_group'); ?>
                <?php if ($team_photo): ?>
                <div class="img-wrapper about-strip-photo">
                    <img src="<?= $team_photo ?>" alt="RIT Racing team group photo" loading="lazy">
                </div>
                <?php else: ?>
                <div class="img-placeholder about-strip-photo">
                    <i class="fa fa-users"></i>
                    <span>Team Photo</span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>


<!-- ============================================================
     PROGRAMS PREVIEW
     ============================================================ -->
<section class="section">
    <div class="container">
        <div class="reveal" style="margin-bottom: 3rem;">
            <p class="section-label">What You'll Do</p>
            <h2 class="section-title">Our <span>Programs</span></h2>
        </div>

        <div class="program-grid">
            <?php
            $hp_programs = data_load('programs.json');
            if (!is_array($hp_programs)) $hp_programs = [];
            usort($hp_programs, fn($a,$b) => ($a['order']??99) <=> ($b['order']??99));
            foreach ($hp_programs as $i => $prog):
                $icon = $prog['icon'] ?? 'fa-gear';
                $title = $prog['title'] ?? '';
                $summary = $prog['summary'] ?? '';
                // Fall back to first sentence of description if no summary
                if (!$summary && !empty($prog['description'])) {
                    $summary = strtok($prog['description'], '.') . '.';
                    if (strlen($summary) > 100) $summary = substr($summary, 0, 97) . '...';
                }
            ?>
            <a href="<?= url('programs.php') ?>#prog-<?= htmlspecialchars($prog['id'] ?? $i) ?>" class="program-card reveal reveal-delay-<?= ($i % 4) + 1 ?>" style="text-decoration:none;color:inherit;">
                <div class="program-icon"><i class="fa <?= htmlspecialchars($icon) ?>"></i></div>
                <h3><?= htmlspecialchars($title) ?></h3>
                <p><?= htmlspecialchars($summary) ?></p>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ============================================================
     SHOP PHOTO STRIP (full-width atmosphere image)
     ============================================================ -->
<div class="reveal shop-photo-strip">
    <?php $shop_img = site_image('shop_atmosphere'); ?>
    <?php if ($shop_img): ?>
    <img src="<?= $shop_img ?>" alt="RIT Racing machine shop" loading="lazy">
    <div class="shop-photo-strip-overlay"></div>
    <?php else: ?>
    <div class="img-placeholder shop-photo-strip-placeholder">
        <i class="fa fa-tools" aria-hidden="true"></i>
        <span>Shop Photo — upload via Admin → Settings → Site Images</span>
    </div>
    <?php endif; ?>
</div>


<!-- ============================================================
     CTA
     ============================================================ -->
<section class="cta-section">
    <div class="container">
        <h2>Ready to Build Something?</h2>
        <p>All RIT students are welcome. All majors. All skill levels.</p>
        <div class="cta-actions">
            <a href="<?= url('join.php') ?>" class="btn-dark">Join the Team</a>
            <a href="<?= url('contribute.php') ?>" class="btn-dark btn-dark-outline">
                Become a Sponsor
            </a>
        </div>
    </div>
</section>

<?php
$car_model = get_setting('car_model_file', '');
$model_url = $car_model ? asset('models/' . $car_model) : '';
?>
<!-- 3D Viewer: Auto-loads on page load -->
<script>
window.RIT_CAR_MODEL = <?= json_encode($model_url) ?>;
window.RIT_MODEL_SETTINGS = <?= json_encode([
    'rotate_x' => (float)(get_setting('model_rotate_x', -90)),
    'rotate_y' => (float)(get_setting('model_rotate_y', 0)),
    'rotate_z' => (float)(get_setting('model_rotate_z', 0)),
    'height'   => (float)(get_setting('model_height', 0.65)),
    'scale'    => (float)(get_setting('model_scale', 3.0)),
]) ?>;
(function(){
    var viewer = document.getElementById('hero-3d-viewer');
    if (!viewer) return;

    var loaded = false;
    function loadCarViewer(){
        if (loaded) return;
        loaded = true;

        // Load Three.js, then GLTFLoader (if a model is set), then car-viewer.js
        var s = document.createElement('script');
        s.src = 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js';
        s.onload = function(){
            var loadViewer = function(){
                var v = document.createElement('script');
                v.src = '<?= asset('js/car-viewer.js') ?>?v=<?= time() ?>';
                document.body.appendChild(v);
            };
            if (window.RIT_CAR_MODEL) {
                var g = document.createElement('script');
                g.src = 'https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js';
                g.onload = loadViewer;
                g.onerror = loadViewer;
                document.body.appendChild(g);
            } else {
                loadViewer();
            }
        };
        document.body.appendChild(s);
    }

    // Only pull in Three.js once the viewer is actually about to be seen —
    // it's a heavy payload that was previously loaded unconditionally on every visit.
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function(entries){
            entries.forEach(function(entry){
                if (entry.isIntersecting) { io.disconnect(); loadCarViewer(); }
            });
        }, { rootMargin: '200px' });
        io.observe(viewer);
    } else {
        loadCarViewer();
    }
})();
</script>

<!-- Countdown Timer JS -->
<script>
(function(){
    var slides = document.querySelectorAll('.countdown-slide');
    if (!slides.length) return;
    var current = 0, total = slides.length, autoTimer;

    function updateCountdowns() {
        slides.forEach(function(slide) {
            var target = new Date(slide.dataset.date).getTime();
            var now = Date.now();
            var diff = target - now;
            if (diff <= 0) {
                slide.querySelectorAll('.countdown-num').forEach(function(n){ n.textContent = '0'; });
                return;
            }
            var d = Math.floor(diff / 86400000);
            var h = Math.floor((diff % 86400000) / 3600000);
            var m = Math.floor((diff % 3600000) / 60000);
            var s = Math.floor((diff % 60000) / 1000);
            slide.querySelector('[data-unit="days"]').textContent = d;
            slide.querySelector('[data-unit="hours"]').textContent = String(h).padStart(2,'0');
            slide.querySelector('[data-unit="minutes"]').textContent = String(m).padStart(2,'0');
            slide.querySelector('[data-unit="seconds"]').textContent = String(s).padStart(2,'0');
        });
    }
    updateCountdowns();
    setInterval(updateCountdowns, 1000);

    window.goToSlide = function(i) {
        slides[current].classList.remove('active');
        var dots = document.querySelectorAll('.countdown-dot');
        if (dots[current]) dots[current].classList.remove('active');
        current = i % total;
        slides[current].classList.add('active');
        if (dots[current]) dots[current].classList.add('active');
        resetAuto();
    };
    window.nextSlide = function() { goToSlide(current + 1); };
    function resetAuto() { clearInterval(autoTimer); if (total > 1) autoTimer = setInterval(function(){ goToSlide(current+1); }, 10000); }
    resetAuto();

    // Add to Calendar — generates a .ics file client-side (no server needed)
    // Works with Apple Calendar, Google Calendar, Outlook, and any ICS-compatible app
    window.addToCalendar = function(btn) {
        var slide = btn.closest('.countdown-slide');
        var title    = slide.dataset.title    || 'RIT Racing Event';
        var location = slide.dataset.location || '';
        var subtitle = slide.dataset.subtitle || '';
        var dateStr  = slide.dataset.date;
        if (!dateStr) return;

        var start = new Date(dateStr);
        // Default event duration: 3 days (typical FSAE competition length)
        var end = new Date(start.getTime() + 3 * 24 * 60 * 60 * 1000);

        function icsDate(d) {
            // Format: YYYYMMDDTHHMMSSZ
            return d.getUTCFullYear() +
                String(d.getUTCMonth()+1).padStart(2,'0') +
                String(d.getUTCDate()).padStart(2,'0') + 'T' +
                String(d.getUTCHours()).padStart(2,'0') +
                String(d.getUTCMinutes()).padStart(2,'0') +
                String(d.getUTCSeconds()).padStart(2,'0') + 'Z';
        }

        function icsEscape(s) {
            return s.replace(/\\/g,'\\\\').replace(/;/g,'\\;').replace(/,/g,'\\,').replace(/\n/g,'\\n');
        }

        var uid = 'ritracing-' + Date.now() + '@ritformula.com';
        var desc = subtitle ? icsEscape(subtitle) : 'RIT Racing — Formula SAE';
        var ics = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//RIT Racing//Event//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:' + uid,
            'DTSTAMP:' + icsDate(new Date()),
            'DTSTART:' + icsDate(start),
            'DTEND:'   + icsDate(end),
            'SUMMARY:' + icsEscape(title),
            'DESCRIPTION:' + desc,
            location ? 'LOCATION:' + icsEscape(location) : '',
            location ? 'GEO;X-APPLE-MAPKIT-HANDLE=' + encodeURIComponent(location) : '',
            'END:VEVENT',
            'END:VCALENDAR'
        ].filter(Boolean).join('\r\n');

        // Trigger download
        var blob = new Blob([ics], { type: 'text/calendar;charset=utf-8' });
        var url  = URL.createObjectURL(blob);
        var a    = document.createElement('a');
        a.href     = url;
        a.download = title.replace(/[^a-zA-Z0-9 ]/g,'').replace(/ +/g,'-').toLowerCase() + '.ics';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        // Brief visual feedback on the button
        var orig = btn.innerHTML;
        btn.innerHTML = '<i class="fa fa-check"></i> Added!';
        btn.style.borderColor = 'var(--rit-orange)';
        btn.style.color = 'var(--rit-orange)';
        setTimeout(function(){ btn.innerHTML = orig; btn.style.borderColor = ''; btn.style.color = ''; }, 2000);
    };
})();
</script>

<?php
$pageScripts = [];
require_once 'includes/footer.php';
?>
