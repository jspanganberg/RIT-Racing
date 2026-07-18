# RIT Racing — Developer Guide

Created by Jared Spanganberg — jspanganberg@gmail.com | jcs7350@g.rit.edu

This guide covers everything that **cannot** be changed from the admin panel and requires editing PHP, CSS, or JS files directly.

---

## Table of Contents

1. Navigation Menu
2. Join Page
3. Contribute Page
4. Contact Form
5. Vehicle History Pages
6. Page SEO
7. CSS Theming and Colors
8. Adding a New Public Page
9. Adding a New Admin Editor
10. How the Image System Works
11. How the Footer Works
12. Clean URLs
13. Auto-Backups
14. Google Analytics
15. Cropper.js (Media Library Crop Tool)
16. 3D Car Viewer
17. Countdown Timers
18. Common Patterns and Gotchas

---

## 1. Navigation Menu

**File:** `includes/header.php` (around line 117)

```php
<!-- Simple link -->
<li><a href="<?= url('about.php') ?>" <?= active('about') ?>>About</a></li>

<!-- Dropdown -->
<li class="has-dropdown">
    <a href="#" class="dropdown-toggle">Vehicles <i class="fa fa-chevron-down"></i></a>
    <ul class="dropdown-menu">
        <li><a href="<?= url('vehicles/electric.php') ?>">Electric</a></li>
    </ul>
</li>
```

The mobile hamburger menu reads these same items automatically. No separate config needed.

---

## 2. Join Page

**File:** `join.php`

Meeting times and location are pulled from settings (editable from admin).

The **contact form** at the bottom of join.php is an exact copy of the form in contact.php — same fields, same CSRF setup, same success/error banners. `form_source` is set to `"join"` so the handler can distinguish submissions. Both forms post to `contact-handler.php`.

If you need to change subject options or add fields, update **both** `contact.php` and `join.php` to keep them in sync.

The page requires `session_start()` at the top (already present) because the CSRF token is stored in `$_SESSION['public_csrf']`.

The intro text and "What We're Looking For" role grid are hardcoded HTML — edit them directly in `join.php`.

---

## 3. Contribute Page

**File:** `contribute.php`

The "Where the Money Goes" section (Parts, Travel, Software, Testing) is hardcoded around line 71. Sponsor packet link and contact email are dynamic.

---

## 4. Contact Form

**Files:** `contact.php` (form), `join.php` (identical copy), `contact-handler.php` (processor)

Both forms use:
- `$_SESSION['public_csrf']` for CSRF — requires `session_start()` at the top of each page
- Honeypot fields (`website`, `phone_confirm`) hidden from real users
- `form_source` hidden field (`"contact"` or `"join"`) so the handler knows the origin
- `contact-handler.php` for POST processing via PHP `mail()`

To add a subject option: edit the `<select name="subject">` in **both** `contact.php` and `join.php`.

The handler validates fields and redirects back with `?success=1` or `?error=ratelimit` / `?error=1`.

---

## 5. Vehicle History Pages

**Files:** `vehicles/electric.php`, `vehicles/combustion.php`

Vehicle data is read from `vehicles.json` (editable via admin). Page layout, intro text, and the 3D model viewer section are hardcoded.

---

## 6. Page SEO

Every page sets two variables before including the header:

```php
$pageTitle       = 'Page Name';
$pageDescription = 'Description for search engines.';
require_once 'includes/header.php';
```

Used for `<title>`, `<meta description>`, OG tags, and Twitter cards.

---

## 7. CSS Theming and Colors

**File:** `assets/css/style.css`

### Color Variables
```css
--rit-orange: #F76902;      /* Brand orange */
--rit-black: #0A0A0A;       /* Page background (dark) */
--rit-dark-2: #161616;      /* Card backgrounds */
--rit-dark-3: #222222;      /* Borders */
--rit-gray: #6B6B6B;        /* Muted text */
--rit-white: #F0F0F0;       /* Primary text */
```

### Font Stack
```css
--font-display: 'Barlow Condensed', sans-serif;   /* Headlines */
--font-body: 'Outfit', sans-serif;                 /* Body text */
```

### Light Theme
All overrides are at the bottom of style.css under `[data-theme="light"]` selectors.

### Sponsor Cards
Use hardcoded `#e8e8e8` background in both themes (intentional — keeps logos visible against a neutral background regardless of theme).

### Mobile Grids
- **Sponsors:** `@media (max-width: 600px)` forces `.sponsor-logo` to exactly 2 columns (`calc(50% - 1px)`)
- **Team members:** Both `@media (max-width: 768px)` and `@media (max-width: 480px)` force `.org-member-card` to 2 columns (`calc(50% - 2px)`)

### If Changing Brand Color
Search for: `#F76902`, `rgba(247,105,2`, and `RIT_ORANGE = 0xF76902` in `car-viewer.js`.

---

## 8. Adding a New Public Page

```php
<?php
$pageTitle       = 'New Page';
$pageDescription = 'SEO description.';
require_once 'includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <p class="page-hero-eyebrow">Eyebrow</p>
        <h1>Page <span style="color:var(--rit-orange);">Title</span></h1>
    </div>
</section>
<section class="section">
    <div class="container">
        <!-- Content -->
    </div>
</section>
<?php require_once 'includes/footer.php'; ?>
```

Then add a nav link in `includes/header.php` and add to `sitemap.php`.

If the page has a contact form, copy the form block from `contact.php` and add `session_start()` + CSRF setup at the top (see join.php for the exact pattern).

---

## 9. Adding a New Admin Editor

1. Create `data/newdata.json` (empty array `[]`)
2. Create `admin/neweditor.php` following the pattern of `admin/sponsors.php`
3. Add sidebar link in `admin/includes/admin-header.php`
4. For image support, use the Browse Media pattern:

```php
<input type="hidden" name="photo_existing" id="x-photo-val" value="">
<button type="button" class="btn-browse" id="x-photo-browse">
    <i class="fa fa-images"></i> Browse Media
</button>
<div id="x-photo-preview"></div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    setupPicker('x-photo-browse', 'x-photo-val', 'x-photo-preview',
                'uploads', '../assets/images/uploads/');
});
</script>
```

In the save handler: `$photo = resolve_media_image(trim($_POST['photo_existing'] ?? ''), 'uploads');`

---

## 10. How the Image System Works

```
Upload in Media Library
    → optionally crop in-browser with Cropper.js
    → upload_image() validates MIME, saves file to assets/images/{folder}/
    → chmod(0644) makes it web-readable

User clicks "Browse Media" in any editor
    → api-media.php returns JSON of all images across all subfolders
    → User clicks thumbnail → filename saved to hidden input

Form submitted
    → resolve_media_image(filename, target_folder) runs
    → If file not in target folder, searches all folders and copies it there
    → chmod(0644) on the copy
    → Filename saved in JSON data
```

**9 admin pages** use Browse Media: Homepage, About, Team, Sponsors, Vehicles, Programs, Memorial, Settings (branding), Settings (page images).

**Note:** Auto-compression was removed in v39. Images are saved exactly as uploaded. Use squoosh.app or tinypng.com to compress before uploading, or rely on the Cropper.js resize (cropped canvas is sent at max 2560px, 92% JPEG quality).

---

## 11. How the Footer Works

The footer reads all content from `settings.json`. Link columns are stored as arrays of `{label, page}` objects, editable from Admin → Site Settings → Footer section. Social links are pulled from the social media settings above.

---

## 12. Clean URLs

Controlled by `CLEAN_URLS` constant in `includes/config.php`. When `true`, the `url()` helper strips `.php` from all links. The `.htaccess` rewrite rules map clean URLs back to PHP files. Default is `false` for compatibility with people.rit.edu.

---

## 13. Auto-Backups

Every call to `data_save()` in `includes/config.php` automatically:
1. Copies the existing JSON file to `data/backups/` with a timestamp (e.g., `settings_2026-04-02_143022.json`)
2. Prunes old backups, keeping only the last 20 per file

To restore from backup: copy the backup file from `data/backups/` back to `data/`, renaming it to the original name (e.g., `team_2026-04-02_120000.json` → `team.json`).

---

## 14. Google Analytics

Set the Measurement ID (starts with `G-`) in Admin → Site Settings → Google Analytics. The tracking script is injected in `includes/header.php` before the closing `</head>` tag on all public pages. Leave the field empty to disable tracking entirely.

The Dashboard diagnostics panel shows whether analytics is configured and displays the active Measurement ID.

---

## 15. Cropper.js (Media Library Crop Tool)

**File:** `admin/media.php`  
**CDN:** `cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/`

When a single file is selected in the Media Library upload zone (click or drag), the upload is intercepted before it reaches the server and Cropper.js opens in a full-screen modal.

### Controls
- **Aspect ratio buttons:** Free, 1:1, 16:9, 4:3, 9:16
- **Crop & Upload** — sends the cropped canvas blob to the server (JPEG at 92% quality, PNG preserved)
- **Upload Original** — bypasses the crop and sends the raw file
- **Cancel** — discards the selection

### Multi-file behaviour
When multiple files are selected or dropped at once, the cropper is bypassed entirely and all files upload directly. The cropper only fires for single-file selections.

### Canvas size cap
The cropped canvas is capped at 2560×2560px via `getCroppedCanvas({ maxWidth: 2560, maxHeight: 2560 })`.

### Modifying the tool
To add more aspect ratios, add buttons inside `<div id="crop-modal">` with `data-ratio="W/H"` (e.g., `data-ratio="2.3333"` for 7:3). The JavaScript handles them automatically via the `.ratio-btn` listener.

---

## 16. 3D Car Viewer

**File:** `assets/js/car-viewer.js`  
**Library:** Three.js r128 (loaded via CDN in `index.php`)

The viewer auto-initialises on the homepage whenever the `#hero-3d-viewer` container exists. No click required.

### Orbit controls
- **Drag / swipe** — orbits the camera around the model. Dragging right rotates the car right (`theta +=`), dragging up tilts down.
- **Scroll wheel** — zooms in/out
- **Auto-rotate** — resumes 3 seconds after the user stops interacting

### Admin-configurable settings
Stored in `settings.json` and passed to the viewer via `window.RIT_MODEL_SETTINGS`:

| Key | Default | Range | Description |
|-----|---------|-------|-------------|
| `model_rotate_x` | -90 | -180 to 180 | X-axis rotation in degrees (fixes Z-up CAD export) |
| `model_rotate_y` | 0 | -180 to 180 | Y-axis rotation |
| `model_rotate_z` | 0 | -180 to 180 | Z-axis rotation |
| `model_height` | 0.65 | -1.0 to 3.0 | Vertical position above the platform disc |
| `model_scale` | 3.0 | 0.5 to 8.0 | Size relative to the platform disc |

### Placeholder car
If no GLB model is uploaded, a wireframe formula car built from Three.js primitives is shown automatically.

---

## 17. Countdown Timers

Managed from Admin → Homepage → Countdown Events. Stored in `settings.json` under `countdown_events`:

```json
{ "id": "michigan2026", "title": "FSAE Michigan", "subtitle": "Presented by GM",
  "date": "2026-06-17T08:00:00", "location": "MIS — Brooklyn, MI", "active": true }
```

Each event has an active toggle. Events whose date has passed are automatically hidden. Multiple active future events auto-cycle every 10 seconds. Users can navigate manually via the dots or arrow.

---

## 18. Common Patterns and Gotchas

### File Permissions (CRITICAL)
Every file write must be followed by `@chmod($path, 0644)`. The RIT server creates files with `0600` (owner-only), which the web server can't read.

### Password Storage
All passwords stored as `password_hash` key (bcrypt). Never store plaintext. The `login_attempt()` function in `auth.php` reads `$user['password_hash']` exclusively — records without this key will be rejected and shown as "(reset required)" in the Users panel.

### Group Names with Spaces
The add-group form in `admin/team.php` uses `enctype="multipart/form-data"` to prevent shared-hosting CGI servers from mangling spaces into underscores. The PHP handler also runs `preg_replace('/\s+/', ' ', $new_name)` to normalise whitespace before saving.

### CSRF Protection
All admin forms: `<?= csrf_field() ?>` in the form, `csrf_verify()` in the handler.  
Public forms (contact, join): use `$_SESSION['public_csrf']` generated at the top of each page.

### Data Save Pattern
```php
$data = data_load('filename.json');
// modify $data
data_save('filename.json', $data);
```

### Alert Messages
```php
$msg = ['type'=>'success', 'text'=>'Saved.'];
// or
$msg = ['type'=>'danger', 'text'=>'Error message.'];
```

### Scroll Reveal
Add `class="reveal"` to any element. Add `reveal-delay-2` or `reveal-delay-3` for stagger.

### Admin Header (no maintenance banner)
`admin/includes/admin-header.php` no longer checks or renders any maintenance mode banner. The maintenance feature was removed in v39 — the `maintenance_mode` key in settings.json is ignored.

### Homepage Program Cards
The program grid on the homepage reads from `programs.json` and renders each program as a clickable card linking to `programs.php#prog-{id}`. The programs page has matching anchor IDs with `scroll-margin-top` to account for the fixed nav.

---

## Contact

**Creator:** Jared Spanganberg
- jspanganberg@gmail.com
- jcs7350@g.rit.edu
- (585) 899-9309
