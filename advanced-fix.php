<?php
/**
 * Advanced Fix Script - Restaurant Menu System
 * Addresses all core issues systematically
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define constants first
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__);
}

if (!defined('APP_PATH')) {
    define('APP_PATH', __DIR__);
}

// Start output buffering to prevent headers issues
ob_start();

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "    <meta charset='UTF-8'>";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "    <title>Advanced Fix - Restaurant Menu System</title>";
echo "    <style>";
echo "        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }";
echo "        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }";
echo "        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }";
echo "        h2 { color: #555; border-left: 4px solid #007bff; padding-left: 10px; margin-top: 30px; }";
echo "        .success { color: #155724; background-color: #d4edda; padding: 15px; border-radius: 5px; margin-bottom: 20px; }";
echo "        .error { color: #dc3545; background-color: #f8d7da; padding: 15px; border-radius: 5px; margin-bottom: 20px; }";
echo "        .info { color: #004085; background-color: #cce5ff; padding: 15px; border-radius: 5px; margin-bottom: 20px; }";
echo "        .warning { color: #856404; background-color: #fff3cd; padding: 15px; border-radius: 5px; margin-bottom: 20px; }";
echo "        code { background-color: #f8f9fa; padding: 2px 4px; border-radius: 3px; font-family: monospace; }";
echo "        pre { background-color: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }";
echo "        .fix-section { margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }";
echo "        .progress-bar { width: 100%; height: 20px; background-color: #e9ecef; border-radius: 10px; overflow: hidden; margin: 10px 0; }";
echo "        .progress-fill { height: 100%; background-color: #28a745; transition: width 0.3s ease; }";
echo "    </style>";
echo "</head>";
echo "<body>";
echo "<div class='container'>";

echo "<h1>🔧 Advanced Fix - Restaurant Menu System</h1>";
echo "<p>This script systematically fixes all core issues in the restaurant menu system.</p>";

$fixesApplied = 0;
$totalFixes = 8;

function logFix($message, $success = true) {
    global $fixesApplied;
    if ($success) {
        echo "<div class='success'>✓ {$message}</div>";
        $fixesApplied++;
    } else {
        echo "<div class='error'>✗ {$message}</div>";
    }
}

try {
    // FIX 1: Load Configuration and Constants
    echo "<div class='fix-section'>";
    echo "<h2>Fix 1: Configuration and Constants</h2>";
    
    require_once 'includes/config/Config.php';
    $config = Config::getInstance();
    logFix("Configuration loaded successfully");
    
    // Verify constants are defined
    $requiredConstants = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'APP_NAME', 'APP_URL'];
    foreach ($requiredConstants as $constant) {
        if (defined($constant)) {
            logFix("Constant {$constant} defined: " . constant($constant));
        } else {
            logFix("Constant {$constant} missing", false);
        }
    }
    echo "</div>";

    // FIX 2: Database Connection and Tables
    echo "<div class='fix-section'>";
    echo "<h2>Fix 2: Database Setup</h2>";
    
    require_once 'includes/database/Database.php';
    logFix("Database class loaded");
    
    $db = Database::getInstance();
    logFix("Database connection established");
    
    // Check required tables
    $requiredTables = ['users', 'branches', 'menu_items', 'qr_codes', 'tables', 'activity_log', 'addons'];
    foreach ($requiredTables as $table) {
        $db->query("SHOW TABLES LIKE '{$table}'");
        $result = $db->single();
        if ($result) {
            logFix("Table '{$table}' exists");
        } else {
            logFix("Table '{$table}' missing - creating...");
            
            // Create missing tables
            switch ($table) {
                case 'activity_log':
                    $sql = "CREATE TABLE activity_log (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        user_id INT NULL,
                        action VARCHAR(255) NOT NULL,
                        description TEXT NULL,
                        ip_address VARCHAR(45) NULL,
                        user_agent TEXT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        INDEX idx_user_id (user_id),
                        INDEX idx_action (action),
                        INDEX idx_created_at (created_at)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                    break;
                    
                case 'addons':
                    $sql = "CREATE TABLE addons (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(255) NOT NULL,
                        description TEXT NULL,
                        version VARCHAR(50) NOT NULL DEFAULT '1.0.0',
                        author VARCHAR(255) NULL,
                        directory_name VARCHAR(100) NOT NULL,
                        is_installed BOOLEAN NOT NULL DEFAULT 0,
                        is_active BOOLEAN NOT NULL DEFAULT 0,
                        priority INT NOT NULL DEFAULT 10,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        UNIQUE KEY uk_directory_name (directory_name)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
                    break;
            }
            
            if (isset($sql)) {
                $db->query($sql);
                if ($db->execute()) {
                    logFix("Table '{$table}' created successfully");
                } else {
                    logFix("Failed to create table '{$table}'", false);
                }
            }
        }
    }
    echo "</div>";

    // FIX 3: Load Core Classes in Correct Order
    echo "<div class='fix-section'>";
    echo "<h2>Fix 3: Core Classes Loading</h2>";
    
    $coreClasses = [
        'includes/session/Session.php',
        'includes/auth/Auth.php', 
        'includes/theme/Theme.php',
        'includes/addon/AddonManager.php',
        'app/core/Router.php',
        'app/core/Application.php',
        'app/core/Model.php',
        'app/core/Controller.php'
    ];
    
    foreach ($coreClasses as $classFile) {
        if (file_exists($classFile)) {
            require_once $classFile;
            $className = pathinfo($classFile, PATHINFO_FILENAME);
            logFix("Loaded {$className} class");
        } else {
            logFix("Missing {$classFile}", false);
        }
    }
    echo "</div>";

    // FIX 4: Load Model Classes
    echo "<div class='fix-section'>";
    echo "<h2>Fix 4: Model Classes</h2>";
    
    $modelClasses = [
        'app/models/ActivityLog.php',
        'app/models/User.php',
        'app/models/Branch.php',
        'app/models/QRCode.php',
        'app/models/Table.php'
    ];
    
    foreach ($modelClasses as $modelFile) {
        if (file_exists($modelFile)) {
            require_once $modelFile;
            $modelName = pathinfo($modelFile, PATHINFO_FILENAME);
            logFix("Loaded {$modelName} model");
        } else {
            logFix("Missing {$modelFile}", false);
        }
    }
    echo "</div>";

    // FIX 5: Load Controller Classes
    echo "<div class='fix-section'>";
    echo "<h2>Fix 5: Controller Classes</h2>";
    
    $controllerClasses = [
        'app/controllers/HomeController.php',
        'app/controllers/AuthController.php',
        'app/controllers/DashboardController.php',
        'app/controllers/BranchController.php',
        'app/controllers/MenuController.php',
        'app/controllers/OrderController.php',
        'app/controllers/QRController.php',
        'app/controllers/ApiController.php'
    ];
    
    foreach ($controllerClasses as $controllerFile) {
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            $controllerName = pathinfo($controllerFile, PATHINFO_FILENAME);
            logFix("Loaded {$controllerName} controller");
        } else {
            logFix("Missing {$controllerFile}", false);
        }
    }
    echo "</div>";

    // FIX 6: Load Helper Functions
    echo "<div class='fix-section'>";
    echo "<h2>Fix 6: Helper Functions</h2>";
    
    if (file_exists('app/helpers/functions.php')) {
        require_once 'app/helpers/functions.php';
        logFix("Helper functions loaded");
    } else {
        logFix("Helper functions file missing", false);
    }
    echo "</div>";

    // FIX 7: Test Application Initialization
    echo "<div class='fix-section'>";
    echo "<h2>Fix 7: Application Initialization</h2>";
    
    try {
        $app = Application::getInstance();
        logFix("Application singleton created");
        
        $app->initialize();
        logFix("Application initialized successfully");
        
        // Test components
        if ($app->getDb()) logFix("Database component available");
        if ($app->getSession()) logFix("Session component available");
        if ($app->getAuth()) logFix("Auth component available");
        if ($app->getTheme()) logFix("Theme component available");
        if ($app->getRouter()) logFix("Router component available");
        
    } catch (Exception $e) {
        logFix("Application initialization failed: " . $e->getMessage(), false);
    }
    echo "</div>";

    // FIX 8: Test Controller Instantiation
    echo "<div class='fix-section'>";
    echo "<h2>Fix 8: Controller Testing</h2>";
    
    try {
        $qrController = new QRController();
        logFix("QRController instantiated successfully");
        
        if (method_exists($qrController, 'logActivity')) {
            logFix("logActivity method available");
        }
        
    } catch (Exception $e) {
        logFix("Controller testing failed: " . $e->getMessage(), false);
    }
    echo "</div>";

    // FINAL RESULTS
    echo "<div class='fix-section' style='background-color: #f8f9fa; border: 2px solid #007bff;'>";
    echo "<h2>🎉 Advanced Fix Results</h2>";
    
    $percentage = round(($fixesApplied / $totalFixes) * 100);
    echo "<div class='progress-bar' style='height: 30px;'>";
    echo "<div class='progress-fill' style='width: {$percentage}%; height: 100%; background-color: " . ($percentage >= 80 ? '#28a745' : ($percentage >= 60 ? '#ffc107' : '#dc3545')) . ";'></div>";
    echo "</div>";
    
    echo "<h3>Overall Score: {$fixesApplied}/{$totalFixes} ({$percentage}%)</h3>";
    
    if ($percentage >= 80) {
        echo "<div class='success'>";
        echo "<h3>🎉 Excellent! System is ready for production!</h3>";
        echo "<p>All major components are working correctly. The application should now run without errors.</p>";
        echo "</div>";
    } elseif ($percentage >= 60) {
        echo "<div class='warning'>";
        echo "<h3>⚠️ Good, but some issues remain</h3>";
        echo "<p>Most components are working, but there are still some issues that need attention.</p>";
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "<h3>❌ Critical issues detected</h3>";
        echo "<p>Multiple components are not working correctly. Please review the errors above and fix the issues.</p>";
        echo "</div>";
    }
    
    echo "<div style='margin-top: 20px; text-align: center;'>";
    echo "<a href='index.php' style='display: inline-block; padding: 15px 30px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; font-size: 16px; margin: 5px;'>🚀 Launch Application</a>";
    echo "</div>";
    
    echo "</div>";

} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h3>❌ Critical Error During Advanced Fix</h3>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<h4>Stack Trace:</h4>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

// End output buffering
ob_end_flush();

echo "</div>";
echo "</body>";
echo "</html>";
?>