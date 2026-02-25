# RIT Racing Website
## ritformula.com — Complete Rebuild
## PHP + Vanilla CSS/JS — No frameworks required

---

## Folder Structure

```
ritracing/
│
├── index.php                   ← Homepage
├── about.php                   ← About the team
├── team.php                    ← Meet the team
├── programs.php                ← Subteam programs
├── sponsors.php                ← Sponsor listing
├── join.php                    ← Join the team + contact form
├── contribute.php              ← Donations & sponsorship info
├── contact.php                 ← Standalone contact page (create this)
├── in-memoriam.php             ← In memoriam page
├── contact-handler.php         ← Form POST handler (create this)
│
├── vehicles/
│   ├── electric.php            ← Electric car history (E1 → F33)
│   └── combustion.php         ← Combustion legacy (F0 → F27)
│
├── includes/
│   ├── config.php              ← ⭐ SITE-WIDE SETTINGS — edit this first
│   ├── header.php              ← HTML <head>, nav
│   └── footer.php             ← Footer, social links, scripts
│
└── assets/
    ├── css/
    │   └── style.css           ← Full design system
    ├── js/
    │   └── main.js             ← Nav, mobile menu, scroll reveal, counters
    ├── images/
    │   ├── IMAGES-README.txt   ← ⭐ Full guide to every image placeholder
    │   ├── hero/               ← Hero background + OG images
    │   ├── cars/
    │   │   ├── electric/       ← F33, F32, F30, F28, E2, E1 photos
    │   │   └── combustion/     ← F0–F27 photos
    │   ├── team/               ← Headshots + team group photos
    │   ├── sponsors/           ← Sponsor logo PNGs
    │   ├── programs/           ← Subteam action photos
    │   └── logos/              ← RIT Racing logos, favicon
    └── files/
        └── rit-racing-sponsor-packet.pdf  ← Sponsorship PDF
```

---

## Quick Start

### 1. Update Site Config
Open `includes/config.php` and update:
- `SITE_URL` — your live domain
- `BASE_PATH` — set to `''` if the site is at the domain root
- Meeting times, latest results, social media links

### 2. Add Your Photos
See `assets/images/IMAGES-README.txt` for a complete list of every
image placeholder and where each file should go.

Each placeholder in the PHP files looks like this:
```html
<div class="img-placeholder" style="height:400px;">
    <i class="fa fa-car"></i><span>F33 Hero Photo</span>
    <!--
    PHOTO: assets/images/cars/electric/f33-main.jpg
    Replace this block with:
    <div class="img-wrapper" style="height:400px;">
        <img src="..." alt="...">
    </div>
    -->
</div>
```
Just replace the `<div class="img-placeholder">` block with the
commented-out `<div class="img-wrapper">` block, and drop the image
file in the correct folder.

### 3. Update Team Data
In `team.php`, find the `$admin`, `$design_leads`, and `$mfg_leads`
arrays. Add/update names, roles, emails, and photo filenames each year.

### 4. Update Sponsor Data
In `sponsors.php`, find the `$sponsors` array. Add new sponsors, update
URLs, and add logo filenames when you have the logo files.

### 5. Build the Contact Handler
Create `contact-handler.php` to process the join form POST from `join.php`.
It should:
- Validate the input fields
- Send an email to `formula@rit.edu` via `mail()` or PHPMailer
- Redirect to `join.php?success=1` on success or `join.php?error=1` on failure

### 6. Add the Sponsor PDF
Drop your sponsorship packet PDF at:
`assets/files/rit-racing-sponsor-packet.pdf`

---

## Design System

### Colors (CSS Variables)
```
--rit-orange:       #F76902   ← Primary brand color
--rit-orange-light: #FF8A2B   ← Hover state
--rit-orange-dark:  #C45200   ← Pressed state
--rit-black:        #0A0A0A   ← Page background
--rit-dark:         #111111   ← Alt dark background
--rit-dark-2:       #1A1A1A   ← Card / section backgrounds
--rit-dark-3:       #222222   ← Borders, dividers
--rit-gray:         #888888   ← Secondary text
--rit-gray-light:   #CCCCCC   ← Body text on dark
--rit-white:        #F5F5F5   ← Headings, primary text
```

### Typography
```
--font-display:  'Bebas Neue'        ← Big headings, car names, numbers
--font-label:    'Barlow Condensed'  ← Nav, labels, buttons, tags
--font-body:     'Outfit'            ← All body copy
```

### Key CSS Classes
```css
.btn.btn-primary    ← Orange filled button
.btn.btn-outline    ← Outlined button
.btn.btn-ghost      ← Orange outline, fills orange on hover
.reveal             ← Add this + JS handles scroll-in animation
.reveal-delay-1/2/3/4  ← Stagger multiple reveals
.img-placeholder    ← Auto-renders a dashed placeholder box
.img-wrapper        ← Hover zoom wrapper for real images
.two-col            ← 50/50 responsive grid
.two-col.flip       ← Reverses column order
.section-label      ← Small uppercase orange label with line
.section-title      ← Large Bebas display heading
.car-card           ← Hover-lift card with orange top bar
.program-card       ← Grid card with icon
.team-card          ← Team member card with photo
.stats-row          ← Grid of stat blocks
.badge              ← Inline tag: .badge-gold, .badge-orange, .badge-silver
.result-banner      ← Left-bordered result/announcement block
```

---

## Pages to Still Create

These pages are linked in the nav but don't have PHP files yet:
- `contact.php` — Standalone contact page
- `vehicles/combustion.php` — Combustion car timeline (F0–F27)
- `programs.php` — Full programs breakdown
- `contribute.php` — Donations + sponsorship details
- `in-memoriam.php` — Tribute to Dave Hathaway
- `contact-handler.php` — Form processing backend

Use the existing pages as templates — they all follow the same
header.php / footer.php structure.

---

## Annual Update Checklist (each season)

- [ ] Update `LATEST_CAR`, `LATEST_RESULT`, `BIGGEST_WIN` in config.php
- [ ] Update meeting times in config.php
- [ ] Add new car entry to vehicles/electric.php
- [ ] Update team roster in team.php
- [ ] Add new photos to assets/images/
- [ ] Update sponsor list in sponsors.php
- [ ] Add season result to the News section on index.php
- [ ] Add milestone to about.php if applicable

---

## Deployment Notes

- Requires PHP 8.0+
- No database required — all data is in PHP arrays
- No composer dependencies
- Place on any shared PHP host (Bluehost, DigitalOcean, RIT servers, etc.)
- Set BASE_PATH to '' in config.php if deploying at domain root
- Enable HTTPS — sponsors and recruits will judge you on this
