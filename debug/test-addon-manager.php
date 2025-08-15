<?php
/**
 * Quick test for AddonManager constant fix
 */

// Define application path
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

echo "<h1>Testing AddonManager Constant Fix</h1>";

// Test 1: Load Config first
echo "<h2>Test 1: Loading Config class</h2>";
try {
    require_once ROOT_PATH . '/includes/config/Config.php';
    $config = Config::getInstance();
    echo "<p style='color: green;'>✅ Config class loaded successfully</p>";
    echo "<p>ADDONS_PATH defined: " . (defined('ADDONS_PATH') ? 'YES' : 'NO') . "</p>";
    if (defined('ADDONS_PATH')) {
        echo "<p>ADDONS_PATH value: " . ADDONS_PATH . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Config class failed: " . $e->getMessage() . "</p>";
}

// Test 2: Load Database class (dependency of AddonManager)
echo "<h2>Test 2: Loading Database class</h2>";
try {
    require_once ROOT_PATH . '/includes/database/Database.php';
    echo "<p style='color: green;'>✅ Database class loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database class failed: " . $e->getMessage() . "</p>";
}

// Test 3: Load AddonManager
echo "<h2>Test 3: Loading AddonManager class</h2>";
try {
    require_once ROOT_PATH . '/includes/addon/AddonManager.php';
    echo "<p style='color: green;'>✅ AddonManager class loaded successfully</p>";
    
    // Test instantiation
    echo "<h2>Test 4: Creating AddonManager instance</h2>";
    $addonManager = new AddonManager();
    echo "<p style='color: green;'>✅ AddonManager instance created successfully</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ AddonManager failed: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}

// Test 5: Check if addon directory exists
echo "<h2>Test 5: Checking addon directory</h2>";
$addonsPath = ROOT_PATH . '/addons';
if (file_exists($addonsPath)) {
    echo "<p style='color: green;'>✅ Addons directory exists: " . $addonsPath . "</p>";
} else {
    echo "<p style='color: orange;'>⚠️ Addons directory does not exist: " . $addonsPath . "</p>";
}

echo "<h2>Test Summary</h2>";
echo "<p>If all tests passed, the ADDONS_PATH constant issue has been resolved.</p>";
echo "<p><a href='system-check.php'>Run Full System Check</a></p>";
?>