<?php
/**
 * Restaurant Menu System - Main Entry Point
 */

// Define application path
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__);
}

// Start output buffering to prevent headers issues
ob_start();

// Load configuration manager (this handles all constants safely)
require_once ROOT_PATH . '/includes/config/Config.php';
$config = Config::getInstance();

// Load core classes in correct order
require_once ROOT_PATH . '/includes/database/Database.php';
require_once ROOT_PATH . '/includes/session/Session.php';
require_once ROOT_PATH . '/includes/auth/Auth.php';
require_once ROOT_PATH . '/includes/theme/Theme.php';
require_once ROOT_PATH . '/includes/addon/AddonManager.php';
require_once ROOT_PATH . '/app/core/Router.php';
require_once ROOT_PATH . '/app/core/Application.php';
require_once ROOT_PATH . '/app/core/Model.php';
require_once ROOT_PATH . '/app/core/Controller.php';

// Load models
require_once ROOT_PATH . '/app/models/User.php';
require_once ROOT_PATH . '/app/models/Branch.php';
require_once ROOT_PATH . '/app/models/Table.php';
require_once ROOT_PATH . '/app/models/QRCode.php';
require_once ROOT_PATH . '/app/models/ActivityLog.php';

// Load controllers
require_once ROOT_PATH . '/app/controllers/AuthController.php';
require_once ROOT_PATH . '/app/controllers/QRController.php';
require_once ROOT_PATH . '/app/controllers/HomeController.php';
require_once ROOT_PATH . '/app/controllers/DashboardController.php';
require_once ROOT_PATH . '/app/controllers/BranchController.php';
require_once ROOT_PATH . '/app/controllers/MenuController.php';
require_once ROOT_PATH . '/app/controllers/OrderController.php';
require_once ROOT_PATH . '/app/controllers/ApiController.php';

// Load helper functions
require_once ROOT_PATH . '/app/helpers/functions.php';

// Start the application
$app = Application::getInstance();

// Initialize the application (loads routes, etc.)
$app->initialize();

// Run the application
$app->run();

// End output buffering
ob_end_flush();
?>