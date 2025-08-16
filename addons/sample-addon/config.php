<?php
/**
 * Sample Addon Configuration
 */

return [
    'name' => 'Sample Addon',
    'description' => 'A sample addon to demonstrate the addon system functionality',
    'version' => '1.0.0',
    'author' => 'Restaurant Menu System',
    'website' => 'https://restaurant-menu-system.com',
    'priority' => 10,
    'requires' => [
        'php' => '>=7.4',
        'mysql' => '>=5.7'
    ],
    'permissions' => [
        'access_admin_panel',
        'manage_sample_data'
    ],
    'hooks' => [
        'before_menu_display',
        'after_order_create'
    ],
    'filters' => [
        'menu_item_price',
        'order_total'
    ]
];
?>