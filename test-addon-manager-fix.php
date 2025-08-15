<?php
/**
 * Test script to verify AddonManager constant and dependency fixes
 */

// Define application path
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__);
}

echo "<h1>Testing AddonManager Fixes</h1>";

// Test 1: Load AddonManager directly (should handle dependencies internally)
echo "<h2>Test 1: Loading AddonManager class directly</h2>";
try {
    require_once ROOT_PATH . '/includes/addon/AddonManager.php';
    echo "<p style='color: green;'>✅ AddonManager class loaded successfully</p>";
    
    // Check if constants are defined
    echo "<p>ADDONS_PATH defined: " . (defined('ADDONS_PATH') ? 'YES' : 'NO') . "</p>";
    if (defined('ADDONS_PATH')) {
        echo "<p>ADDONS_PATH value: " . ADDONS_PATH . "</p>";
    }
    
    echo "<p>APP_URL defined: " . (defined('APP_URL') ? 'YES' : 'NO') . "</p>";
    if (defined('APP_URL')) {
        echo "<p>APP_URL value: " . APP_URL . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ AddonManager failed: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}

// Test 2: Check if AddonManager instance was created
echo "<h2>Test 2: Checking AddonManager instance</h2>";
if (isset($addonManager)) {
    if ($addonManager === null) {
        echo "<p style='color: orange;'>⚠️ AddonManager instance is null (dependencies missing)</p>";
    } else {
        echo "<p style='color: green;'>✅ AddonManager instance created successfully</p>";
        echo "<p>Instance type: " . get_class($addonManager) . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ AddonManager instance not found</p>";
}

// Test 3: Test global helper functions
echo "<h2>Test 3: Testing global helper functions</h2>";
try {
    // Test execute_hook function
    $result = execute_hook('test_hook', ['param1' => 'value1']);
    echo "<p style='color: green;'>✅ execute_hook function works: " . json_encode($result) . "</p>";
    
    // Test apply_filter function
    $result = apply_filter('test_filter', 'original_value');
    echo "<p style='color: green;'>✅ apply_filter function works: " . $result . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Helper functions failed: " . $e->getMessage() . "</p>";
}

// Test 4: Test BaseAddon class
echo "<h2>Test 4: Testing BaseAddon class</h2>";
try {
    // Create a test addon class
    class TestAddon extends BaseAddon {
        public function initialize() {
            return "Test addon initialized";
        }
    }
    
    $testAddonData = [
        'directory_name' => 'test',
        'name' => 'Test Addon',
        'version' => '1.0.0'
    ];
    
    $testAddon = new TestAddon($testAddonData);
    echo "<p style='color: green;'>✅ BaseAddon class works</p>";
    echo "<p>Test addon created successfully</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ BaseAddon class failed: " . $e->getMessage() . "</p>";
}

// Test 5: Check if addon directory exists
echo "<h2>Test 5: Checking addon directory</h2>";
$addonsPath = defined('ADDONS_PATH') ? ADDONS_PATH : ROOT_PATH . '/addons';
if (file_exists($addonsPath)) {
    echo "<p style='color: green;'>✅ Addons directory exists: " . $addonsPath . "</p>";
    
    // List addon directories
    $addonDirs = glob($addonsPath . '/*', GLOB_ONLYDIR);
    echo "<p>Found " . count($addonDirs) . " addon directories:</p>";
    echo "<ul>";
    foreach ($addonDirs as $dir) {
        echo "<li>" . basename($dir) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: orange;'>⚠️ Addons directory does not exist: " . $addonsPath . "</p>";
}

echo "<h2>Test Summary</h2>";
echo "<p>If all tests passed, the AddonManager constant and dependency issues have been resolved.</p>";
echo "<p><a href='debug/test-addon-manager.php'>Run Original Test</a></p>";
echo "<p><a href='debug/system-check.php'>Run Full System Check</a></p>";
?>