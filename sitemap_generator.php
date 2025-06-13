<?php
/**
 * Sitemap Generator for Ted Rubin Project Management Site
 * Run this file to generate sitemap.xml
 * Usage: php generate-sitemap.php or visit in browser
 */

require_once 'config/database.php';

// Site configuration
$siteUrl = 'https://yoursite.com'; // Change this to your actual domain
$currentDate = date('c'); // Current date in ISO 8601 format

// Static pages with their priorities and change frequencies
$staticPages = [
    [
        'url' => '/',
        'lastmod' => $currentDate,
        'changefreq' => 'weekly',
        'priority' => '1.0'
    ],
    [
        'url' => '/portfolio.php',
        'lastmod' => $currentDate,
        'changefreq' => 'weekly',
        'priority' => '0.9'
    ]
];

// Get dynamic portfolio pages
$portfolioManager = new PortfolioManager();
$portfolioItems = $portfolioManager->getAllItems('active');

$dynamicPages = [];
foreach ($portfolioItems as $item) {
    $dynamicPages[] = [
        'url' => '/portfolio.php?category=' . urlencode($item['category']),
        'lastmod' => date('c', strtotime($item['updated_at'])),
        'changefreq' => 'monthly',
        'priority' => '0.7'
    ];
}

// Remove duplicate category URLs
$seenCategories = [];
$dynamicPages = array_filter($dynamicPages, function($page) use (&$seenCategories) {
    if (in_array($page['url'], $seenCategories)) {
        return false;
    }
    $seenCategories[] = $page['url'];
    return true;
});

// Combine all pages
$allPages = array_merge($staticPages, $dynamicPages);

// Generate XML
$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

foreach ($allPages as $page) {
    $xml .= '  <url>' . "\n";
    $xml .= '    <loc>' . $siteUrl . $page['url'] . '</loc>' . "\n";
    $xml .= '    <lastmod>' . $page['lastmod'] . '</lastmod>' . "\n";
    $xml .= '    <changefreq>' . $page['changefreq'] . '</changefreq>' . "\n";
    $xml .= '    <priority>' . $page['priority'] . '</priority>' . "\n";
    $xml .= '  </url>' . "\n";
}

$xml .= '</urlset>';

// Save to file
$sitemapFile = 'sitemap.xml';
if (file_put_contents($sitemapFile, $xml)) {
    echo "✅ Sitemap generated successfully: $sitemapFile\n";
    echo "📊 Total URLs: " . count($allPages) . "\n";
    echo "📁 Static pages: " . count($staticPages) . "\n";
    echo "🎯 Dynamic pages: " . count($dynamicPages) . "\n";
    echo "\n";
    echo "Next steps:\n";
    echo "1. Submit sitemap to Google Search Console\n";
    echo "2. Add sitemap URL to robots.txt\n";
    echo "3. Set up automatic regeneration (monthly)\n";
} else {
    echo "❌ Error: Could not write sitemap file. Check file permissions.\n";
}

// If accessed via browser, show formatted output
if (isset($_SERVER['HTTP_HOST'])) {
    echo "<pre>";
    echo htmlspecialchars($xml);
    echo "</pre>";
    echo "<p><strong>Sitemap saved to:</strong> <a href='sitemap.xml'>sitemap.xml</a></p>";
}
?>