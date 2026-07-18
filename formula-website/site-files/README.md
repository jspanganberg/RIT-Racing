# RIT Racing — Website + Admin CMS

**Version 39** — PHP 8 / JSON flat-file storage / No database required

Created by **Jared Spanganberg** — jspanganberg@gmail.com | jcs7350@g.rit.edu | (585) 899-9309

---

## Quick Start

1. Upload the entire project folder to your web server
2. Set directory permissions: `chmod -R 755 data/ assets/images/ assets/models/ assets/files/`
3. Visit `/admin/` — you'll be redirected to create your first admin account
4. Log in and start managing content

### Server Requirements

- PHP 8.0+ (tested on 8.4)
- `fileinfo` extension (recommended; falls back to extension-based MIME detection)
- `gd` extension (optional; no longer used for auto-compression but retained if needed)
- Apache with `mod_rewrite` (optional, for clean URLs)
- No database needed — all data stored in JSON files
- No Composer dependencies

### Server Notes (people.rit.edu)

- Upload limit: 2MB per file (server-enforced, cannot be changed)
- PHP runs as CGI/FPM
- File permissions: all uploads automatically set to `0644` after every upload/copy
- Clean URLs: disabled by default (`CLEAN_URLS` in config.php). Enable when server supports `mod_rewrite`.
- Group name forms use `enctype="multipart/form-data"` to prevent space-to-underscore mangling on CGI hosts

---

## What Changed in v39

| Fix | Files Affected |
|-----|----------------|
| Password field unified — stored and read as `password_hash` (bcrypt) | `admin/users.php`, `admin/includes/auth.php` |
| Maintenance mode removed entirely | `admin/index.php`, `admin/includes/admin-header.php`, `includes/header.php` |
| Media Library: all image folders now editable (rename + delete) | `admin/media.php` |
| Media Library: Cropper.js crop tool on single-file upload | `admin/media.php` |
| Media Library: auto-compression removed (files saved as-is) | `admin/includes/auth.php`, `admin/media.php` |
| Sponsors mobile: forced 2-column grid on narrow screens | `assets/css/style.css` |
| Team mobile: forced 2-column card grid on all mobile widths | `assets/css/style.css` |
| 3D model viewer: swipe direction inverted for natural feel | `assets/js/car-viewer.js` |
| Admin team groups: spaces preserved correctly (no underscore mangling) | `admin/team.php` |
| Join page: contact form replaced with identical copy of contact.php form | `join.php` |

---

## Project Structure

```
ritracing/
│
├── Public Pages
│   ├── index.php               Homepage
│   ├── about.php               About / history (reads from about.json)
│   ├── team.php                Team roster (reads from team.json)
│   ├── programs.php            Subteam descriptions (reads from programs.json)
│   ├── sponsors.php            Sponsor grid (reads from sponsors.json)
│   ├── join.php                Recruitment / meeting info + contact form (matches contact.php)
│   ├── contribute.php          Sponsorship / donation info
│   ├── contact.php             Contact form (CSRF, honeypot, rate-limited)
│   ├── contact-handler.php     Contact form processor (mail())
│   ├── in-memoriam.php         Memorial page (reads from memorial.json)
│   ├── 404.php                 Custom error page
│   ├── sitemap.php             Dynamic XML sitemap
│   └── vehicles/
│       ├── electric.php        Electric car history
│       └── combustion.php      Combustion car history
│
├── Includes
│   ├── includes/config.php     Path detection, helpers, settings accessors
│   ├── includes/header.php     HTML head, nav, theme toggle
│   └── includes/footer.php     Footer (reads links from settings.json)
│
├── Admin Panel (/admin/)
│   ├── index.php               Dashboard (stats, quick actions, diagnostics)
│   ├── homepage.php            Homepage editor (hero, stats, about strip, images)
│   ├── about.php               About page editor (content, stats, milestones, images)
│   ├── team.php                Team + subteam group management
│   ├── sponsors.php            Sponsor management
│   ├── vehicles.php            Vehicle data editor
│   ├── programs.php            Programs editor (with Browse Media for photos)
│   ├── memorial.php            In memoriam editor
│   ├── media.php               Media Library — upload, crop, rename, delete
│   ├── model.php               3D car model upload (.glb)
│   ├── settings.php            Site Settings (contact, social, branding, footer, files)
│   ├── users.php               Admin account management (bcrypt, password_hash key)
│   ├── api-media.php           JSON API for Browse Media picker
│   ├── login.php               Login page (with password eye toggle)
│   ├── setup.php               First-time account creation
│   ├── logout.php              Session destroy
│   └── includes/
│       ├── auth.php            Session, uploads, user auth, resolve_media_image
│       ├── admin-header.php    Admin shell (sidebar, CSS, topbar, flash messages)
│       └── admin-footer.php    Browse Media picker modal, closing HTML
│
├── Assets
│   ├── assets/css/style.css    All public styles (single file)
│   ├── assets/js/main.js       Navigation, scroll, animations
│   ├── assets/js/car-viewer.js Three.js 3D model viewer (natural swipe direction)
│   ├── assets/images/
│   │   ├── site/               Logos, hero, OG image, page photos
│   │   ├── team/               Team member headshots
│   │   ├── sponsors/           Sponsor logos
│   │   ├── cars/electric/      Electric vehicle photos
│   │   ├── cars/combustion/    Combustion vehicle photos
│   │   ├── memorial/           Memorial photos
│   │   ├── programs/           Program section photos
│   │   └── uploads/            General uploads (default Media Library target)
│   ├── assets/models/          3D model files (.glb)
│   └── assets/files/           Downloadable files (sponsor packet PDF)
│
├── Data (JSON flat-file storage)
│   ├── data/settings.json      All site configuration (30+ keys)
│   ├── data/team.json          Team roster
│   ├── data/sponsors.json      Sponsor list
│   ├── data/vehicles.json      Vehicle data (electric + combustion)
│   ├── data/programs.json      Program/subteam descriptions
│   ├── data/about.json         About page content, stats, milestones
│   ├── data/memorial.json      Memorial entries
│   ├── data/users.json         Admin accounts (bcrypt, stored as "password_hash" key)
│   └── data/backups/           Auto-backups (timestamped, last 20 per file)
│
├── Documentation
│   ├── README.md               This file
│   ├── DEPLOYMENT.md           Full server setup & domain switching guide
│   ├── DEVELOPER_GUIDE.md      Code-level change guide
│   ├── LICENSE.txt             Proprietary license
│   └── THIRD_PARTY_NOTICES.md  Third-party library credits
│
├── .htaccess                   Rewrite rules, upload limits, custom 404
├── .user.ini                   PHP settings (may not work on CGI/FPM)
└── robots.txt                  Search engine directives
```

---

## What's Editable From the Admin Panel

| Content | Admin Location |
|---------|---------------|
| Hero text, headline, eyebrow label | Homepage |
| Homepage stats bar (numbers + labels) | Homepage |
| Homepage "Who We Are" section | Homepage |
| Homepage images (hero bg, team photo, shop) | Homepage |
| Countdown events (title, date, location, on/off) | Homepage |
| About page — all 3 sections, quote | About Page → Content |
| About page — stats bar | About Page → Stats Bar |
| About page — milestones timeline | About Page → Milestones |
| About page — photos (heritage, shop, competition) | About Page → Images |
| Team members, roles, photos, emails | Team |
| Subteam group names (add/rename/delete) | Team → Manage Groups |
| Sponsor list, logos, featured sponsors | Sponsors |
| Vehicle data, specs, photos, badges | Vehicles |
| Program descriptions, icons, photos, summaries | Programs |
| Memorial entries and photos | Memorial |
| All images (upload, crop, rename, delete) | Media Library |
| 3D car model (.glb file) | 3D Model |
| 3D model position, rotation, scale | 3D Model |
| Site name, contact email | Site Settings |
| Competition results, latest car | Site Settings |
| Meeting times and location | Site Settings |
| Social media links (5 platforms) | Site Settings |
| Nav logos (dark + light mode) | Site Settings |
| Favicon, OG social share image | Site Settings |
| Footer text, link columns, copyright | Site Settings |
| Sponsor packet PDF | Site Settings |
| Google Analytics Measurement ID | Site Settings |
| Admin user accounts | Users |

---

## What Requires Code Changes

| Content | File | See Developer Guide |
|---------|------|-------------------|
| Navigation menu items | `includes/header.php` | Section 1 |
| Join page intro text / "What We're Looking For" section | `join.php` | Section 2 |
| Contribute page descriptions | `contribute.php` | Section 3 |
| Contact / Join form subject options | `contact.php`, `join.php` | Section 4 |
| Vehicle page layouts | `vehicles/*.php` | Section 5 |
| 404 page content | `404.php` | — |

See `DEVELOPER_GUIDE.md` for step-by-step instructions on code changes.

---

## Image System

**Workflow:** Upload to Media Library (optionally crop first) → Browse Media in any editor → image auto-copies to correct folder.

| Folder | Used By | Set From |
|--------|---------|----------|
| `site/` | Homepage, about, join, contact, logos | Homepage / About Page / Site Settings |
| `team/` | Team page | Team editor |
| `sponsors/` | Sponsor logos | Sponsors editor |
| `cars/electric/` | Electric vehicle photos | Vehicles editor |
| `cars/combustion/` | Combustion vehicle photos | Vehicles editor |
| `memorial/` | Memorial page | Memorial editor |
| `programs/` | Programs page | Programs editor |
| `uploads/` | General purpose (default upload target) | Media Library |

### Media Library Features

- **Upload** — drag & drop or file browser; folder selector; optional custom filename (blank = keep original name)
- **Crop** — single-file uploads open Cropper.js automatically; aspect ratio buttons (Free, 1:1, 16:9, 4:3, 9:16); "Upload Original" skips crop; multi-file uploads skip the cropper entirely
- **Rename** — pen icon on any image opens an inline rename panel; type new name (no extension), click ✓
- **Delete** — trash icon with confirmation on all images in all folders
- **Filter tabs** — click a folder tab to filter the grid

**Key function:** `resolve_media_image($filename, $target_folder)` — when an image is picked from a different folder, this function copies it to the correct location with `chmod 0644`.

---

## Password / Authentication Notes

Admin passwords are stored in `data/users.json` under the key `password_hash` (bcrypt via `PASSWORD_BCRYPT`). Both `auth.php` (login) and `users.php` (create/edit) use this key exclusively.

If you have an older `users.json` with a `password` key, the Users page will show a "(reset required)" badge — set a new password through the admin UI to migrate that account.

To manually create a hash for direct JSON editing, use the Password Hash Generator at the bottom of Admin → Users.

---

## Data File Schemas

### settings.json — 30+ keys covering all site configuration

Key groups: site info, hero text, hero stats, about strip, countdown events, meeting times, social links, footer text, footer links, site images, sponsor packet, subteams, Google Analytics.

**Note:** `maintenance_mode` key is no longer read or written — it can be safely left in existing settings.json files or removed.

### team.json — Array of member objects

```json
{ "name": "...", "role": "...", "group": "admin|lead|associate|returning",
  "subteam": "...", "email": "...", "photo": "...", "order": 1 }
```

### sponsors.json — Array of sponsor objects

```json
{ "id": "...", "name": "...", "url": "...", "logo": "...",
  "active": true, "order": 1, "featured": false, "title": "...", "description": "..." }
```

### vehicles.json — Object with `electric` and `combustion` arrays

```json
{ "id": "f33", "name": "F33", "years": "2024–2025", "subtitle": "...",
  "description": "...", "result": "...", "photo": "...", "badge": "Current Car",
  "badge_color": "", "specs": [{"label":"...","value":"..."}] }
```

### programs.json — Array of program objects

```json
{ "id": "aero", "icon": "fa-wind", "title": "Aerodynamics",
  "summary": "Short one-liner for homepage grid",
  "description": "Full description for programs page",
  "photo": "aero.jpg", "order": 1 }
```

### users.json — Array of admin account objects

```json
{ "username": "admin", "name": "Display Name", "role": "admin|editor",
  "password_hash": "$2y$10$...", "created_at": "2026-04-16 00:00:00" }
```

---

## Key Functions

### includes/config.php

| Function | Purpose |
|----------|---------|
| `asset($path)` | Full URL to `assets/` file |
| `url($page)` | Full URL to page (strips .php if `CLEAN_URLS` is true) |
| `data_load($file)` | Load + decode JSON from `data/` |
| `data_save($file, $data)` | Save data as JSON to `data/` (with auto-backup) |
| `get_setting($key, $default)` | Read from `settings.json` (static cached) |
| `site_image($slot)` | URL for admin-uploaded site image |
| `get_contact_email()` | Shorthand for contact email setting |
| `get_meeting_location()` | Shorthand for meeting location setting |
| `get_vehicles($program)` | Vehicle array for electric or combustion |

### admin/includes/auth.php

| Function | Purpose |
|----------|---------|
| `upload_image($file, $subfolder, $custom_name)` | Validated upload with MIME check and chmod (no auto-compression) |
| `resolve_media_image($filename, $target)` | Copy image to correct folder if needed |
| `auth_require()` | Redirect to login if not authenticated |
| `csrf_field()` / `csrf_verify()` | CSRF protection for all admin forms |
| `login_attempt($user, $pass)` | Authenticate against `password_hash` key with bcrypt |
| `users_load()` / `users_save()` | Load/save the users.json array |

---

## Features

- **Dark/Light theme** — toggle in nav, saved to localStorage
- **Diagnostics** — Dashboard checks JSON integrity, folder permissions, broken images, PHP config, analytics status
- **Countdown timers** — multiple event countdowns on homepage, auto-cycle every 10 seconds, managed from admin
- **Auto-backups** — every JSON save creates a timestamped backup in `data/backups/`, keeps last 20 per file
- **Google Analytics** — paste a Measurement ID in Site Settings, auto-injected on all public pages
- **Cropper.js** — single-file uploads open an in-browser crop tool; supports Free, 1:1, 16:9, 4:3, 9:16 ratios; multi-file uploads bypass the cropper
- **Browse Media picker** — unified image selection across all 9 editors
- **Full Media Library management** — upload, crop, rename, and delete images from all subfolders
- **Auto file permissions** — `chmod 0644` on every upload and copy operation
- **Clean URL support** — optional toggle in config.php (requires mod_rewrite)
- **Custom 404 page** — via .htaccess rewrite fallback
- **Mobile responsive** — hamburger menu, 2-column sponsor grid on mobile, 2-column team member cards on mobile
- **3D car viewer** — Three.js orbit viewer with natural swipe direction, auto-rotate, admin-adjustable rotation/height/scale
- **Clickable program cards** — homepage grid links to each program's section on the programs page
- **Footer editor** — fully editable footer text, two link columns, copyright line (Site Settings)
- **Password visibility toggle** — eye icon on login, setup, and user management pages
- **Unified contact/join forms** — identical forms on both pages; CSRF protected, honeypot anti-spam, rate limiting
- **SEO** — dynamic sitemap, OG tags, structured data, meta descriptions

---

## Deployment

For full server setup, DNS switching, and SSL instructions, see **`DEPLOYMENT.md`**.

### Quick Checklist (after server is configured)

1. Upload all files to the server
2. Set permissions: `chmod -R 755 data/ assets/images/ assets/models/ assets/files/`
3. Visit `/admin/` to create your account
4. Upload images via Media Library
5. Configure all sections: Homepage, About, Team, Sponsors, Vehicles, Programs
6. Set up Site Settings (contact, social, branding, footer)
7. Test both dark and light themes on mobile and desktop
8. Test contact form and join form
9. Test 3D model viewer swipe on mobile

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Images don't display | Visit Admin → Media (auto-fixes permissions). Check folder is writable. |
| "Upload too large" | Server limit is 2MB on people.rit.edu. Resize images before uploading. |
| White/blank page (500 error) | Check Dashboard → Diagnostics. Check PHP error log. |
| Login not working | Delete `data/users.json` and visit `/admin/setup.php` to recreate account. |
| User account shows "reset required" | Old account has legacy `password` key — set a new password to migrate to `password_hash`. |
| Browse Media empty | Upload images to Media Library first. |
| 3D model not loading | Visit Admin → 3D Model page (auto-fixes permissions). Re-upload if needed. |
| Clean URLs give 404 | Set `CLEAN_URLS` to `false` in `includes/config.php`. |
| Accidentally deleted data | Check `data/backups/` for timestamped copies. Rename back to original filename. |
| Analytics not tracking | Verify Measurement ID in Site Settings starts with `G-`. |
| Slow page loads | Compress images before uploading (squoosh.app, tinypng.com). Keep under 200KB each. |
| Group names saving with underscores | Fixed in v39. If still occurring, ensure the add-group form has `enctype="multipart/form-data"`. |
| Sponsors showing as single column on mobile | Hard-refresh (Ctrl+Shift+R) — CSS cache may be stale. |

---

## Contact

**Creator:** Jared Spanganberg
- Email: jspanganberg@gmail.com
- RIT Email: jcs7350@g.rit.edu
- Phone: (585) 899-9309

**Team:** RIT Racing — formula@rit.edu

## License

Proprietary — see `LICENSE.txt`.
