# RIT Racing Website — Server Deployment Guide

**Created by Jared Spanganberg** — jspanganberg@gmail.com | (585) 899-9309

This guide walks you through getting the website onto the RIT Racing server and switching ritformula.com to point at it.

---

## Before You Start — What You Need

Gather this info before starting. If you don't know the answers, ask whoever set up the server.

- [ ] **Server IP address** (e.g., `129.21.xxx.xxx`)
- [ ] **SSH login credentials** (username + password, or SSH key)
- [ ] **Root/sudo access** (needed to configure the web server)
- [ ] **What's installed** — run these commands after logging in:

```bash
# Check the operating system
cat /etc/os-release

# Check web server
apache2 -v 2>/dev/null || httpd -v 2>/dev/null || nginx -v 2>/dev/null

# Check PHP
php -v

# Check if mod_rewrite is enabled (Apache only)
apache2ctl -M 2>/dev/null | grep rewrite || httpd -M 2>/dev/null | grep rewrite
```

**Minimum requirements:** Linux, Apache or Nginx, PHP 8.0+

---

## Phase 1 — Install Server Software (if not already installed)

Skip this section if Apache and PHP are already running.

### Ubuntu / Debian

```bash
sudo apt update
sudo apt install -y apache2 php php-json php-mbstring php-fileinfo php-gd libapache2-mod-php
sudo a2enmod rewrite
sudo a2enmod headers
sudo systemctl restart apache2
```

### CentOS / RHEL / Fedora

```bash
sudo dnf install -y httpd php php-json php-mbstring php-fileinfo php-gd
sudo systemctl enable --now httpd
```

### Verify it's working

```bash
# Should show Apache version
apache2 -v || httpd -v

# Should show PHP 8.x
php -v

# Apache should be running
systemctl status apache2 || systemctl status httpd
```

Open a browser and visit `http://YOUR_SERVER_IP` — you should see the Apache default page.

---

## Phase 2 — Upload the Website

### Option A — SCP from your computer (recommended)

```bash
# From YOUR computer (not the server), run:
scp -r ritracing_v39/* username@YOUR_SERVER_IP:/var/www/ritracing/
```

### Option B — Upload zip and extract on server

```bash
# Upload the zip
scp deploy_final.zip username@YOUR_SERVER_IP:/tmp/

# SSH into server
ssh username@YOUR_SERVER_IP

# Extract
sudo mkdir -p /var/www/ritracing
sudo unzip /tmp/deploy_final.zip -d /tmp/
sudo cp -r /tmp/test/website_files/* /var/www/ritracing/
```

### Option C — Git (if you set up a repo)

```bash
cd /var/www
sudo git clone YOUR_REPO_URL ritracing
```

### Set file permissions

```bash
# Set ownership to the web server user
sudo chown -R www-data:www-data /var/www/ritracing

# Set directory permissions
sudo find /var/www/ritracing -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/ritracing -type f -exec chmod 644 {} \;

# Make data directories writable
sudo chmod -R 775 /var/www/ritracing/data
sudo chmod -R 775 /var/www/ritracing/assets/images
sudo chmod -R 775 /var/www/ritracing/assets/models
sudo chmod -R 775 /var/www/ritracing/assets/files
```

**Note:** On some systems the web user is `apache` instead of `www-data`. Check with:
```bash
ps aux | grep -E 'apache|httpd|nginx' | head -3
```

---

## Phase 3 — Configure Apache

### Create the virtual host config

```bash
sudo nano /etc/apache2/sites-available/ritformula.conf
```

Paste this (replace `YOUR_SERVER_IP` with your actual IP):

```apache
<VirtualHost *:80>
    ServerName ritformula.com
    ServerAlias www.ritformula.com
    DocumentRoot /var/www/ritracing

    <Directory /var/www/ritracing>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Logging
    ErrorLog ${APACHE_LOG_DIR}/ritracing-error.log
    CustomLog ${APACHE_LOG_DIR}/ritracing-access.log combined
</VirtualHost>
```

### Enable the site

```bash
# Ubuntu/Debian
sudo a2ensite ritformula.conf
sudo a2dissite 000-default.conf    # Optional: disable default site
sudo systemctl reload apache2

# CentOS/RHEL — put the config in:
# /etc/httpd/conf.d/ritformula.conf
# then:
sudo systemctl reload httpd
```

### Test it works locally

```bash
# On the server, test that Apache serves the site
curl -H "Host: ritformula.com" http://localhost/
```

You should see HTML output. If you get an error, check the log:
```bash
sudo tail -20 /var/log/apache2/ritracing-error.log
```

---

## Phase 4 — Enable Clean URLs

Now that you have a real server with mod_rewrite, turn on clean URLs:

### Edit config.php

```bash
sudo nano /var/www/ritracing/includes/config.php
```

Find this line (around line 114):
```php
define('CLEAN_URLS', false);
```

Change it to:
```php
define('CLEAN_URLS', true);
```

Now URLs will be `ritformula.com/about` instead of `ritformula.com/about.php`.

---

## Phase 5 — Set Up SSL (HTTPS)

Free SSL with Let's Encrypt — takes 2 minutes.

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-apache    # Ubuntu/Debian
# OR
sudo dnf install -y certbot python3-certbot-apache    # CentOS/RHEL

# Get the certificate (run AFTER DNS is pointing to your server)
sudo certbot --apache -d ritformula.com -d www.ritformula.com
```

Certbot will ask for your email and automatically configure Apache for HTTPS. It also sets up auto-renewal.

**Important:** This step must be done AFTER Phase 6 (DNS), because Let's Encrypt needs to verify you own the domain by connecting to your server.

---

## Phase 6 — Point the Domain to Your Server

This is the step that makes ritformula.com load your new site.

### In Wix (where the domain is currently managed)

1. Log into your **Wix account**
2. Go to **Domains** → click **ritformula.com**
3. Go to **DNS Settings** or **Manage DNS Records**
4. Find the existing **A record** (it currently points to Wix's servers)
5. **Change the A record** to point to your server's IP address:

```
Type: A
Host: @
Value: YOUR_SERVER_IP
TTL: 3600
```

6. Also update or add a **CNAME for www**:

```
Type: CNAME
Host: www
Value: ritformula.com
TTL: 3600
```

7. **Delete any other A records** that point to Wix's IP addresses (they usually look like `23.236.x.x` or `185.230.x.x`)

### Wait for DNS propagation

DNS changes take 15 minutes to 48 hours to spread worldwide. You can check progress:

```bash
# From your computer
nslookup ritformula.com

# Should show YOUR_SERVER_IP, not Wix's IP
```

Or use https://dnschecker.org and enter `ritformula.com`.

### Then set up SSL

Once DNS is pointing to your server and you can load the site via `http://ritformula.com`, run the Certbot command from Phase 5.

---

## Phase 7 — First-Time Setup

1. Visit `https://ritformula.com/admin/setup.php`
2. Create your admin account (min 10 character password)
3. You'll be logged in automatically
4. Go through each section:
   - **Homepage** — set hero text, stats, countdown events, images
   - **About Page** — verify content, stats, milestones, images
   - **Team** — add/verify team members
   - **Sponsors** — verify sponsor list
   - **Vehicles** — verify car data
   - **Programs** — verify programs
   - **Site Settings** — set contact email, social links, Google Analytics ID
   - **Media** — upload all photos
---

## Phase 8 — Verify Everything

Run through this checklist:

- [ ] Homepage loads at `https://ritformula.com`
- [ ] HTTPS padlock shows in browser
- [ ] All nav links work
- [ ] Dark/light theme toggle works
- [ ] 3D car viewer loads and swipe works naturally
- [ ] Team page shows members in 2-column grid on mobile
- [ ] Sponsor logos display in 2-column grid on mobile
- [ ] Contact form sends email
- [ ] Join form sends email
- [ ] Admin panel works at `/admin/`
- [ ] Clean URLs work (no .php in address bar)
- [ ] Mobile hamburger menu works
- [ ] Countdown timer ticks
- [ ] Dashboard diagnostics show all green

---

## Troubleshooting

### "403 Forbidden"
```bash
# Fix permissions
sudo chown -R www-data:www-data /var/www/ritracing
sudo chmod -R 755 /var/www/ritracing
```

### "500 Internal Server Error"
```bash
# Check the error log
sudo tail -30 /var/log/apache2/ritracing-error.log

# Most common cause: .htaccess issue. Make sure AllowOverride All is set.
```

### "404 Not Found" on clean URLs
```bash
# Make sure mod_rewrite is enabled
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Images don't display
```bash
# Fix image directory permissions
sudo chmod -R 775 /var/www/ritracing/assets/images
sudo chown -R www-data:www-data /var/www/ritracing/assets/images
```

### PHP errors / white screen
```bash
# Enable error display temporarily
sudo nano /var/www/ritracing/.user.ini
# Add: display_errors = On

# Check PHP error log
sudo tail -30 /var/log/php_errors.log
```

### Contact form not sending
```bash
# Check if mail is installed
php -m | grep mail

# Install if missing
sudo apt install -y sendmail
sudo systemctl start sendmail
```

### DNS not propagating
- Double-check you deleted ALL old Wix A records
- Wait up to 48 hours (usually much faster)
- Use https://dnschecker.org to check global propagation
- Try clearing your browser cache and flushing DNS:
  ```bash
  # Windows
  ipconfig /flushdns
  # Mac
  sudo dscacheutil -flushcache
  ```

---

## Ongoing Maintenance

### Updating the site
Upload new files via SCP and set permissions:
```bash
scp -r updated_files/* username@SERVER_IP:/var/www/ritracing/
sudo chown -R www-data:www-data /var/www/ritracing
```

### Backups
The site auto-backs up JSON data to `data/backups/`. For full server backups:
```bash
# Add to crontab for weekly backups
sudo crontab -e
# Add this line:
0 3 * * 0 tar -czf /backups/ritracing-$(date +\%Y\%m\%d).tar.gz /var/www/ritracing/data /var/www/ritracing/assets
```

### SSL renewal
Certbot auto-renews. Verify with:
```bash
sudo certbot renew --dry-run
```

### Monitoring
Check the site is up:
```bash
curl -s -o /dev/null -w "%{http_code}" https://ritformula.com
# Should output: 200
```

---

## Quick Reference

| What | Where |
|------|-------|
| Website files | `/var/www/ritracing/` |
| Apache config | `/etc/apache2/sites-available/ritformula.conf` |
| Error log | `/var/log/apache2/ritracing-error.log` |
| Access log | `/var/log/apache2/ritracing-access.log` |
| PHP config | `/etc/php/8.x/apache2/php.ini` |
| SSL certificates | `/etc/letsencrypt/live/ritformula.com/` |
| Data files | `/var/www/ritracing/data/` |
| Data backups | `/var/www/ritracing/data/backups/` |
| Admin panel | `https://ritformula.com/admin/` |

---

## Contact

**Creator:** Jared Spanganberg
- jspanganberg@gmail.com
- jcs7350@g.rit.edu
- (585) 899-9309
