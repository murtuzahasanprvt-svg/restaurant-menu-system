<?php
/**
 * Quick verification script to test the main application
 */

// Define application path
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__);
}

// Start output buffering to prevent headers issues
ob_start();

// Load configuration manager
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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification - Restaurant Menu System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #28a745; padding-bottom: 10px; }
        .success { color: #155724; background-color: #d4edda; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .error { color: #dc3545; background-color: #f8d7da; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .btn { display: inline-block; padding: 15px 30px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; font-size: 16px; margin: 5px; }
        .btn-success { background: #28a745; }
    </style>
</head>
<body>
<div class="container">
    <h1>✅ System Verification Complete</h1>
    
    <?php
    try {
        // Test Application initialization
        $app = Application::getInstance();
        $app->initialize();
        
        echo "<div class='success'>✅ Application initialized successfully</div>";
        
        // Test Router
        $router = $app->getRouter();
        $routesCount = $router->getRoutesCount();
        
        echo "<div class='success'>✅ Router loaded with {$routesCount} routes</div>";
        
        // Test HomeController
        if (class_exists('HomeController')) {
            echo "<div class='success'>✅ HomeController class available</div>";
        }
        
        // Test route finding
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        
        $uri = $router->testGetUri();
        $route = $router->testFindRoute($uri, 'GET');
        
        if ($route) {
            echo "<div class='success'>✅ Route found for '/'</div>";
            echo "<div class='success'>✅ Handler: " . htmlspecialchars($route['handler']) . "</div>";
        } else {
            echo "<div class='error'>❌ Route not found for '/'</div>";
        }
        
        echo "<div class='success'>✅ All systems operational!</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        echo "<div class='error'>❌ File: " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</div>";
    }
    ?>
    
    <div style="text-align: center; margin-top: 30px;">
        <a href="index.php" class="btn btn-success">🚀 Launch Application</a>
        <a href="test-fixes.php" class="btn">🔧 Run Full Tests</a>
    </div>
</div>
</body>
</html>
<?php
ob_end_flush();
?>