<?php
/**
 * SEO Helper Functions
 */

// Generate page meta tags
function generateMetaTags($title, $description = '', $keywords = '', $image = '') {
    $siteName = APP_NAME;
    $fullTitle = $title . ' | ' . $siteName;
    
    // Truncate if needed
    if (strlen($fullTitle) > META_TITLE_LENGTH) {
        $fullTitle = substr($fullTitle, 0, META_TITLE_LENGTH - 3) . '...';
    }
    
    if (strlen($description) > META_DESCRIPTION_LENGTH) {
        $description = substr($description, 0, META_DESCRIPTION_LENGTH - 3) . '...';
    }
    
    $meta = [
        'title' => htmlspecialchars($fullTitle, ENT_QUOTES),
        'description' => htmlspecialchars($description, ENT_QUOTES),
        'keywords' => htmlspecialchars($keywords, ENT_QUOTES),
        'image' => $image ? htmlspecialchars($image, ENT_QUOTES) : asset('images/logo.png'),
        'url' => htmlspecialchars(getCurrentUrl(), ENT_QUOTES)
    ];
    
    return $meta;
}

// Get current URL
function getCurrentUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    return $protocol . '://' . $host . $uri;
}

// Generate job Schema.org JSON-LD
function generateJobSchema($job) {
    $schema = [
        '@context' => 'https://schema.org/',
        '@type' => 'JobPosting',
        'title' => $job['title'],
        'description' => strip_tags($job['description']),
        'datePosted' => date('Y-m-d', strtotime($job['created_at'])),
        'employmentType' => strtoupper(str_replace('-', '_', $job['type'])),
        'hiringOrganization' => [
            '@type' => 'Organization',
            'name' => APP_NAME
        ],
        'jobLocation' => [
            '@type' => 'Place',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $job['location'],
                'addressCountry' => 'IN'
            ]
        ]
    ];
    
    if (!empty($job['salary'])) {
        $salaryParts = explode('-', $job['salary']);
        if (count($salaryParts) === 2) {
            $schema['baseSalary'] = [
                '@type' => 'MonetaryAmount',
                'currency' => 'INR',
                'value' => [
                    '@type' => 'QuantitativeValue',
                    'minValue' => trim($salaryParts[0]),
                    'maxValue' => trim($salaryParts[1]),
                    'unitText' => 'MONTH'
                ]
            ];
        }
    }
    
    return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}

// Generate breadcrumb Schema.org JSON-LD
function generateBreadcrumbSchema($items) {
    $listItems = [];
    foreach ($items as $index => $item) {
        $listItems[] = [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $item['name'],
            'item' => $item['url'] ?? getCurrentUrl()
        ];
    }
    
    $schema = [
        '@context' => 'https://schema.org/',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $listItems
    ];
    
    return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}

// Render meta tags in HTML
function renderMetaTags($meta) {
    echo '<title>' . $meta['title'] . '</title>' . "\n";
    echo '<meta name="description" content="' . $meta['description'] . '">' . "\n";
    if (!empty($meta['keywords'])) {
        echo '<meta name="keywords" content="' . $meta['keywords'] . '">' . "\n";
    }
    
    // Open Graph tags
    echo '<meta property="og:title" content="' . $meta['title'] . '">' . "\n";
    echo '<meta property="og:description" content="' . $meta['description'] . '">' . "\n";
    echo '<meta property="og:image" content="' . $meta['image'] . '">' . "\n";
    echo '<meta property="og:url" content="' . $meta['url'] . '">' . "\n";
    echo '<meta property="og:type" content="website">' . "\n";
    
    // Twitter Card tags
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . $meta['title'] . '">' . "\n";
    echo '<meta name="twitter:description" content="' . $meta['description'] . '">' . "\n";
    echo '<meta name="twitter:image" content="' . $meta['image'] . '">' . "\n";
}
