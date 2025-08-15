<?php
/**
 * Default Theme Configuration
 */

return [
    'name' => 'Default Theme',
    'description' => 'Default responsive theme for Restaurant Menu System',
    'version' => '1.0.0',
    'author' => 'Restaurant Menu System',
    'supports' => [
        'responsive',
        'mobile-friendly',
        'dark-mode'
    ],
    'colors' => [
        'primary' => '#e74c3c',
        'secondary' => '#34495e',
        'success' => '#27ae60',
        'warning' => '#f39c12',
        'danger' => '#e74c3c',
        'light' => '#ecf0f1',
        'dark' => '#2c3e50'
    ],
    'fonts' => [
        'primary' => 'Arial, sans-serif',
        'secondary' => 'Georgia, serif'
    ],
    'layout' => [
        'container_width' => '1200px',
        'sidebar_width' => '250px',
        'header_height' => '60px'
    ]
];
?>