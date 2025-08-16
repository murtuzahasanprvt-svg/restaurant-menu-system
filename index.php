<?php
/**
 * Restaurant Menu System - Main Entry Point (FINAL DEBUGGING VERSION)
 */

// Force the server to display all errors at the very beginning
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define application path
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__);
}

// Start output buffering
ob_start();

try {
    // Load configuration manager
    require_once ROOT_PATH . '/includes/config/Config.php';
    $config = Config::getInstance();

    // Load all core classes
    require_once ROOT_PATH . '/includes/database/Database.php';
    require_once ROOT_PATH . '/includes/session/Session.php';
    require_once ROOT_PATH . '/includes/auth/Auth.php';
    require_once ROOT_PATH . '/includes/theme/Theme.php';
    require_once ROOT_PATH . '/includes/addon/AddonManager.php';
    require_once ROOT_PATH . '/app/core/Router.php';
    require_once ROOT_PATH . '/app/core/Application.php';
    require_once ROOT_PATH . '/app/core/Model.php';
    require_once ROOT_PATH . '/app/core/Controller.php';

    // Load all models
    require_once ROOT_PATH . '/app/models/User.php';
    require_once ROOT_PATH . '/app/models/Branch.php';
    require_once ROOT_PATH . '/app/models/Table.php';
    require_once ROOT_PATH . '/app/models/QRCode.php';
    require_once ROOT_PATH . '/app/models/ActivityLog.php';

    // Load all controllers
    require_once ROOT_PATH . '/app/controllers/HomeController.php';
    require_once ROOT_PATH . '/app/controllers/AuthController.php';
    require_once ROOT_PATH . '/app/controllers/DashboardController.php';
    require_once ROOT_PATH . '/app/controllers/BranchController.php';
    require_once ROOT_PATH . '/app/controllers/MenuController.php';
    require_once ROOT_PATH . '/app/controllers/OrderController.php';
    require_once ROOT_PATH . '/app/controllers/QRController.php';
    require_once ROOT_PATH . '/app/controllers/ApiController.php';

    // Load helper functions
    require_once ROOT_PATH . '/app/helpers/functions.php';

    // Start the application
    $app = Application::getInstance();
    $app->initialize();
    $app->run();

} catch (Throwable $e) {
    // This will catch ANY error, including fatal ones, and display it.
    http_response_code(500);
    ob_clean(); // Clear any previous output
    echo "<h1>❌ A Fatal Application Error Occurred</h1>";
    echo "<p>This is the hidden error that was causing the blank page.</p>";
    echo "<pre style='background:#fff0f0; border:1px solid #ffcccc; padding:10px; border-radius:5px;'>";
    echo "<strong>Error Type:</strong> " . get_class($e) . "\n";
    echo "<strong>Message:</strong> " . $e->getMessage() . "\n\n";
    echo "<strong>File:</strong> " . $e->getFile() . " on line " . $e->getLine() . "\n\n";
    echo "<strong>Stack Trace:</strong>\n" . $e->getTraceAsString();
    echo "</pre>";
    exit;
}

// End output buffering
ob_end_flush();
?>
