<?php
// Generate sitemap dynamically based on current URL
require_once __DIR__ . '/includes/config.php';
header('Content-Type: application/xml; charset=UTF-8');
header('Cache-Control: public, max-age=86400'); // cache for 24 hours

$pages = [
    [''           , '1.0', 'monthly'],
    ['about'      , '0.8', 'yearly'],
    ['electric', '0.9', 'yearly'],
    ['combustion', '0.7', 'yearly'],
    ['programs'   , '0.8', 'yearly'],
    ['team'       , '0.8', 'yearly'],
    ['sponsors'   , '0.7', 'monthly'],
    ['join'       , '0.9', 'monthly'],
    ['contribute' , '0.8', 'yearly'],
    ['contact'    , '0.6', 'yearly'],
    ['in-memoriam', '0.5', 'yearly'],
];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($pages as [$path, $priority, $freq]) {
    $loc = SITE_URL . '/' . $path;
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($loc) . "</loc>\n";
    echo "    <priority>$priority</priority>\n";
    echo "    <changefreq>$freq</changefreq>\n";
    echo "  </url>\n";
}
echo '</urlset>';
