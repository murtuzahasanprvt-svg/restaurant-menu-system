<?php
/**
 * Test script to verify constants are properly defined
 */

// Define application path
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__FILE__));
}

echo "Testing constant definitions...\n";

// Load configuration first to define constants
require_once ROOT_PATH . '/includes/config/Config.php';
$config = Config::getInstance();

echo "✓ Config loaded successfully\n";

// Test if ADDONS_PATH is defined
if (defined('ADDONS_PATH')) {
    echo "✓ ADDONS_PATH defined: " . ADDONS_PATH . "\n";
} else {
    echo "✗ ADDONS_PATH not defined\n";
}

// Test other constants
$required_constants = [
    'ROOT_PATH', 'APP_PATH', 'INCLUDES_PATH', 'THEMES_PATH', 'ADDONS_PATH',
    'PUBLIC_PATH', 'UPLOADS_PATH', 'QR_CODE_PATH', 'DB_HOST', 'DB_NAME',
    'DB_USER', 'DB_PASS', 'DB_CHARSET', 'APP_NAME', 'APP_URL', 'APP_VERSION'
];

foreach ($required_constants as $constant) {
    if (defined($constant)) {
        echo "✓ $constant: " . constant($constant) . "\n";
    } else {
        echo "✗ $constant: NOT DEFINED\n";
    }
}

echo "\nTesting AddonManager loading...\n";

try {
    require_once ROOT_PATH . '/includes/database/Database.php';
    echo "✓ Database class loaded\n";
    
    require_once ROOT_PATH . '/includes/addon/AddonManager.php';
    echo "✓ AddonManager class loaded\n";
    
    // Test creating AddonManager instance
    $addonManager = new AddonManager();
    echo "✓ AddonManager instance created successfully\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "✗ File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\nTest completed!\n";
?>