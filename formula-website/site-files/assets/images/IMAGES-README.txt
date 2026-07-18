# RIT Racing — Image Asset Guide
# ============================================================
# This file documents every image placeholder in the site.
# When you have a real photo ready, drop it in the correct
# folder with the correct filename and remove the placeholder
# comment from the PHP file.
#
# Recommended formats: .jpg for photos, .png for logos/graphics
# ============================================================

## HERO IMAGES (assets/images/hero/)
# ─────────────────────────────────────────────────────────────
# f33-hero.jpg          — F33 at competition, wide/dramatic angle
#                          Used on: index.php (main hero background)
#                          Recommended size: 1920×1080px minimum
#
# shop-atmosphere.jpg   — Wide shop/build photo, full-width strip
#                          Used on: index.php (atmosphere strip section)
#                          Recommended size: 1920×500px
#
# og-image.jpg          — Open Graph / social share thumbnail
#                          Used on: all pages (meta og:image)
#                          Recommended size: 1200×630px
#
# f0-historic.jpg       — Early car photo (F0 or F1 era)
#                          Used on: about.php
#
# shop-build.jpg        — Team working in the shop
#                          Used on: about.php
#
# electric-action.jpg   — Electric car in motion or paddock
#                          Used on: about.php


## ELECTRIC CAR PHOTOS (assets/images/cars/electric/)
# ─────────────────────────────────────────────────────────────
# f33-main.jpg          — F33 hero shot (used on index + electric page)
# f33-detail-1.jpg      — F33 detail/angle 1
# f33-detail-2.jpg      — F33 detail/angle 2
# f33-detail-3.jpg      — F33 detail/angle 3
#
# f32-main.jpg          — F32 hero shot
# f32-detail-1.jpg      — F32 detail 1
# f32-detail-2.jpg      — F32 detail 2
# f32-detail-3.jpg      — F32 detail 3
#
# f30-main.jpg          — F30 hero shot
# f28-main.jpg          — F28 hero shot
# e2-main.jpg           — E2 hero shot
# e1-main.jpg           — E1 hero shot
#
# electric-action.jpg   — Any electric car in action (about page)


## COMBUSTION CAR PHOTOS (assets/images/cars/combustion/)
# ─────────────────────────────────────────────────────────────
# Naming convention: f[NUMBER]-main.jpg  (e.g., f27-main.jpg)
# Optional details:  f[NUMBER]-detail-1.jpg, etc.
#
# f27-main.jpg, f26-main.jpg, f25-main.jpg, f24-main.jpg
# f23-main.jpg, f22-main.jpg, f21-main.jpg, f20-main.jpg
# f19-main.jpg, f18-main.jpg, f17-main.jpg, f16-main.jpg
# f15-main.jpg, f14-main.jpg, f13-main.jpg, f12-main.jpg
# f11-main.jpg, f10-main.jpg, f9-main.jpg,  f8-main.jpg
# f7-main.jpg,  f6-main.jpg,  f5-main.jpg,  f4-main.jpg
# f3-main.jpg,  f2-main.jpg,  f1-main.jpg,  f0-main.jpg


## TEAM PHOTOS (assets/images/team/)
# ─────────────────────────────────────────────────────────────
# team-full-2025.jpg    — Full team photo 2024-2025 season
#                          Used on: team.php (hero strip)
#
# new-member-day.jpg    — New member day event photo
#                          Used on: join.php
#
# Individual headshots — name these by the member's last name:
# mendola.jpg, mcdonald.jpg, surace.jpg, onslow.jpg, etc.
# Then update the 'photo' field in the $admin / $design_leads
# arrays in team.php with the filename (just the filename, not path).


## SPONSOR LOGOS (assets/images/sponsors/)
# ─────────────────────────────────────────────────────────────
# These should be .png files with transparent backgrounds.
# The CSS will desaturate them on load and restore color on hover.
#
# ansys.png, siemens.png, moog.png, altair.png, l3harris.png
# calspan.png, mahle.png, skf.png, globalquest.png, hexcel.png
# rockwest.png, siglent.png, pmd.png, samuel-nelson.png
# altium.png, earthx.png, melasta.png, sae.png, davies-craig.png
# xrp.png, rbc.png, modern-coatings.png, lee.png, izze.png
# wny-energy.png, rochester-gear.png, aam.png, wirecare.png
# composite-envisions.png, textreme.png, mwi.png, prowire.png
# raptor.png, ssp.png, hms.png, rennscot.png, kern.png
# vi-grade.png, marren.png, first.png, kisssoft.png, dr-munson.png
#
# Recommended max display height: 50px
# Recommended file width: 300-600px at 2× for retina


## PROGRAMS PHOTOS (assets/images/programs/)
# ─────────────────────────────────────────────────────────────
# Used on programs.php for each subteam section.
#
# aero.jpg, business.jpg, brakes.jpg, composites.jpg
# chassis.jpg, cnc.jpg, electronics.jpg, electric-powertrain.jpg
# fabrication.jpg, suspension.jpg, vehicle-dynamics.jpg


## FILES (assets/files/)
# ─────────────────────────────────────────────────────────────
# rit-racing-sponsor-packet.pdf   — Sponsorship proposal PDF
#                                   Linked from sponsors.php and contribute.php


# ============================================================
# TIP: Use descriptive alt text on all <img> tags for SEO
# and accessibility. The template already has placeholder
# alt text — update it when you add real photos.
# ============================================================
