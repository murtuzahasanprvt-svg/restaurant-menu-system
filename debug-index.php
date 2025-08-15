<?php
/**
 * Advanced Debug Entry Point for Restaurant Menu System
 * This file provides comprehensive debugging information before running the actual application
 */

// Define application path
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__);
}

// Start output buffering to prevent headers issues
ob_start();

// Initialize debug system
require_once ROOT_PATH . '/debug/AdvancedDebugger.php';
require_once ROOT_PATH . '/debug/ExecutionTracer.php';
require_once ROOT_PATH . '/debug/ClassVerifier.php';
require_once ROOT_PATH . '/debug/DependencyAnalyzer.php';
require_once ROOT_PATH . '/debug/DebugDashboard.php';

// Initialize all debug components
$debugger = AdvancedDebugger::getInstance();
$tracer = ExecutionTracer::getInstance();
$classVerifier = ClassVerifier::getInstance();
$dependencyAnalyzer = DependencyAnalyzer::getInstance();
$dashboard = DebugDashboard::getInstance();

// Start main execution trace
$tracer->startStep('DEBUG_INITIALIZATION', ['mode' => 'comprehensive']);

// Log system information
$debugger->log('SYSTEM_INFO', 'PHP Version: ' . phpversion());
$debugger->log('SYSTEM_INFO', 'Server OS: ' . php_uname('s'));
$debugger->log('SYSTEM_INFO', 'Memory Limit: ' . ini_get('memory_limit'));
$debugger->log('SYSTEM_INFO', 'Max Execution Time: ' . ini_get('max_execution_time') . 's');

// Check if we should run in debug mode or normal mode
$debugMode = isset($_GET['debug']) || isset($_GET['test']) || basename(__FILE__) === 'debug-index.php';

if ($debugMode) {
    // Debug mode - show comprehensive debugging information
    $tracer->startStep('DEBUG_MODE_ACTIVATION');
    
    // Verify all classes
    $tracer->startStep('CLASS_VERIFICATION');
    $classVerificationResults = $classVerifier->verifyAllClasses();
    $tracer->endStep('CLASS_VERIFICATION', $classVerificationResults);
    
    // Analyze dependencies
    $tracer->startStep('DEPENDENCY_ANALYSIS');
    $dependencyAnalysisResults = $dependencyAnalyzer->analyzeDependencies();
    $tracer->endStep('DEPENDENCY_ANALYSIS', $dependencyAnalysisResults);
    
    // Generate comprehensive report
    $tracer->startStep('REPORT_GENERATION');
    $debuggerReport = $debugger->generateReport();
    $traceReport = $tracer->generateTraceReport();
    $classReport = $classVerifier->generateVerificationReport();
    $dependencyReport = $dependencyAnalyzer->generateDependencyReport();
    $tracer->endStep('REPORT_GENERATION', [
        'debugger_report' => $debuggerReport,
        'trace_report' => $traceReport,
        'class_report' => $classReport,
        'dependency_report' => $dependencyReport
    ]);
    
    // Save reports
    $debugger->saveReport();
    
    $tracer->endStep('DEBUG_MODE_ACTIVATION');
    
    // Show debug dashboard
    $dashboard->renderDashboard();
    
} else {
    // Normal mode - try to run the actual application with debugging
    $tracer->startStep('NORMAL_MODE_EXECUTION');
    
    try {
        // Load configuration manager with tracing
        $tracer->startStep('CONFIG_LOADING');
        $debugger->log('CONFIG_LOADING', 'Loading configuration manager');
        require_once ROOT_PATH . '/includes/config/Config.php';
        $config = Config::getInstance();
        $debugger->logConstant('ROOT_PATH', ROOT_PATH);
        $debugger->logConstant('APP_PATH', APP_PATH);
        $debugger->logConstant('APP_NAME', APP_NAME);
        $debugger->logConstant('APP_URL', APP_URL);
        $tracer->endStep('CONFIG_LOADING', ['config_loaded' => true]);
        
        // Load core classes with tracing
        $tracer->startStep('CORE_CLASSES_LOADING');
        $coreClasses = [
            'Database' => '/includes/database/Database.php',
            'Session' => '/includes/session/Session.php',
            'Auth' => '/includes/auth/Auth.php',
            'Theme' => '/includes/theme/Theme.php',
            'AddonManager' => '/includes/addon/AddonManager.php',
            'Router' => '/app/core/Router.php',
            'Application' => '/app/core/Application.php',
            'Model' => '/app/core/Model.php',
            'Controller' => '/app/core/Controller.php'
        ];
        
        foreach ($coreClasses as $className => $file) {
            $tracer->startStep("LOADING_$className");
            $debugger->logClassLoad($className, $file);
            
            if (file_exists(ROOT_PATH . $file)) {
                require_once ROOT_PATH . $file;
                $loaded = class_exists($className);
                $debugger->log('CLASS_LOAD_RESULT', "$className loaded: " . ($loaded ? 'SUCCESS' : 'FAILED'));
            } else {
                $debugger->logError("Class file not found: $file");
            }
            
            $tracer->endStep("LOADING_$className", ['loaded' => class_exists($className)]);
        }
        $tracer->endStep('CORE_CLASSES_LOADING');
        
        // Load models with tracing
        $tracer->startStep('MODELS_LOADING');
        $modelClasses = [
            'User' => '/app/models/User.php',
            'Branch' => '/app/models/Branch.php',
            'Table' => '/app/models/Table.php',
            'QRCode' => '/app/models/QRCode.php',
            'ActivityLog' => '/app/models/ActivityLog.php'
        ];
        
        foreach ($modelClasses as $className => $file) {
            $tracer->startStep("LOADING_MODEL_$className");
            $debugger->logClassLoad($className, $file);
            
            if (file_exists(ROOT_PATH . $file)) {
                require_once ROOT_PATH . $file;
                $loaded = class_exists($className);
                $debugger->log('MODEL_LOAD_RESULT', "$className loaded: " . ($loaded ? 'SUCCESS' : 'FAILED'));
            } else {
                $debugger->logError("Model file not found: $file");
            }
            
            $tracer->endStep("LOADING_MODEL_$className", ['loaded' => class_exists($className)]);
        }
        $tracer->endStep('MODELS_LOADING');
        
        // Load controllers with tracing
        $tracer->startStep('CONTROLLERS_LOADING');
        $controllerClasses = [
            'AuthController' => '/app/controllers/AuthController.php',
            'QRController' => '/app/controllers/QRController.php',
            'HomeController' => '/app/controllers/HomeController.php',
            'DashboardController' => '/app/controllers/DashboardController.php',
            'BranchController' => '/app/controllers/BranchController.php',
            'MenuController' => '/app/controllers/MenuController.php',
            'OrderController' => '/app/controllers/OrderController.php',
            'ApiController' => '/app/controllers/ApiController.php'
        ];
        
        foreach ($controllerClasses as $className => $file) {
            $tracer->startStep("LOADING_CONTROLLER_$className");
            $debugger->logClassLoad($className, $file);
            
            if (file_exists(ROOT_PATH . $file)) {
                require_once ROOT_PATH . $file;
                $loaded = class_exists($className);
                $debugger->log('CONTROLLER_LOAD_RESULT', "$className loaded: " . ($loaded ? 'SUCCESS' : 'FAILED'));
            } else {
                $debugger->logError("Controller file not found: $file");
            }
            
            $tracer->endStep("LOADING_CONTROLLER_$className", ['loaded' => class_exists($className)]);
        }
        $tracer->endStep('CONTROLLERS_LOADING');
        
        // Load helper functions
        $tracer->startStep('HELPERS_LOADING');
        $debugger->log('HELPERS_LOADING', 'Loading helper functions');
        require_once ROOT_PATH . '/app/helpers/functions.php';
        $tracer->endStep('HELPERS_LOADING', ['helpers_loaded' => true]);
        
        // Start the application with detailed tracing
        $tracer->startStep('APPLICATION_START');
        $debugger->logApplicationState('APPLICATION_START', 'Creating Application instance');
        
        $app = Application::getInstance();
        $debugger->logApplicationState('APPLICATION_CREATED', 'Application singleton created');
        
        // Initialize the application
        $tracer->startStep('APPLICATION_INITIALIZATION');
        $debugger->logApplicationState('APPLICATION_INITIALIZING', 'Initializing application');
        $app->initialize();
        $debugger->logApplicationState('APPLICATION_INITIALIZED', 'Application initialized successfully');
        $tracer->endStep('APPLICATION_INITIALIZATION', ['initialized' => true]);
        
        // Get router and check routes
        $tracer->startStep('ROUTER_VERIFICATION');
        $router = $app->getRouter();
        $routesCount = $router->getRoutesCount();
        $debugger->logApplicationState('ROUTER_VERIFIED', "Router loaded with $routesCount routes");
        $tracer->endStep('ROUTER_VERIFICATION', ['routes_count' => $routesCount]);
        
        $tracer->endStep('APPLICATION_START', ['application_ready' => true]);
        
        // Run the application with error handling
        $tracer->startStep('APPLICATION_EXECUTION');
        $debugger->logApplicationState('APPLICATION_EXECUTION_START', 'Starting application execution');
        
        try {
            $app->run();
            $debugger->logApplicationState('APPLICATION_EXECUTION_SUCCESS', 'Application executed successfully');
            $tracer->endStep('APPLICATION_EXECUTION', ['success' => true]);
            
        } catch (Exception $e) {
            $debugger->logError('APPLICATION_EXECUTION_FAILED', $e->getFile(), $e->getLine());
            $debugger->log('APPLICATION_ERROR', $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $tracer->endStep('APPLICATION_EXECUTION', ['success' => false, 'error' => $e->getMessage()]);
            
            // Show error page
            showErrorPage($e);
        }
        
        $tracer->endStep('NORMAL_MODE_EXECUTION');
        
    } catch (Exception $e) {
        $debugger->logError('FATAL_ERROR', $e->getFile(), $e->getLine());
        $debugger->log('FATAL_EXCEPTION', $e->getMessage(), [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        $tracer->endStep('NORMAL_MODE_EXECUTION', ['success' => false, 'fatal_error' => $e->getMessage()]);
        
        // Show fatal error page
        showFatalErrorPage($e);
    }
}

$tracer->endStep('DEBUG_INITIALIZATION');

// Save final report
$debugger->saveReport();

// End output buffering
ob_end_flush();

// Helper functions for error pages
function showErrorPage($exception) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Application Error - Restaurant Menu System</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
            .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
            h1 { color: #dc3545; border-bottom: 2px solid #dc3545; padding-bottom: 10px; }
            .error { color: #dc3545; background-color: #f8d7da; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
            .debug { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 20px; font-family: monospace; }
            .actions { margin-top: 20px; }
            .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>❌ Application Error</h1>
            <div class="error">
                <strong>Error:</strong> <?php echo htmlspecialchars($exception->getMessage()); ?>
            </div>
            <div class="debug">
                <strong>File:</strong> <?php echo htmlspecialchars($exception->getFile()); ?>:<?php echo $exception->getLine(); ?><br>
                <strong>Type:</strong> <?php echo get_class($exception); ?><br>
                <strong>Trace:</strong><br>
                <pre><?php echo htmlspecialchars($exception->getTraceAsString()); ?></pre>
            </div>
            <div class="actions">
                <a href="debug-index.php?debug=1" class="btn">🔍 Run Debug Analysis</a>
                <a href="index.php" class="btn">🔄 Try Again</a>
            </div>
        </div>
    </body>
    </html>
    <?php
}

function showFatalErrorPage($exception) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Fatal Error - Restaurant Menu System</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
            .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
            h1 { color: #721c24; border-bottom: 2px solid #721c24; padding-bottom: 10px; }
            .error { color: #721c24; background-color: #f8d7da; padding: 20px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb; }
            .debug { background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-top: 20px; font-family: monospace; border: 1px solid #dee2e6; }
            .actions { margin-top: 30px; text-align: center; }
            .btn { display: inline-block; padding: 15px 30px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px; font-size: 16px; }
            .btn-primary { background: #007bff; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>💀 Fatal Application Error</h1>
            <div class="error">
                <h3>🚨 Critical System Failure</h3>
                <p><strong>Error:</strong> <?php echo htmlspecialchars($exception->getMessage()); ?></p>
                <p><strong>File:</strong> <?php echo htmlspecialchars($exception->getFile()); ?>:<?php echo $exception->getLine(); ?></p>
                <p><strong>Type:</strong> <?php echo get_class($exception); ?></p>
            </div>
            <div class="debug">
                <h4>🔍 Debug Information</h4>
                <p><strong>Stack Trace:</strong></p>
                <pre><?php echo htmlspecialchars($exception->getTraceAsString()); ?></pre>
            </div>
            <div class="actions">
                <a href="debug-index.php?debug=1" class="btn">🔍 Run Comprehensive Debug Analysis</a>
                <a href="debug-index.php" class="btn btn-primary">🔄 Restart Application</a>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>