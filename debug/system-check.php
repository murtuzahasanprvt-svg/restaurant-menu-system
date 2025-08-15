<?php
/**
 * Comprehensive system check
 */

// Define application path
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

// Load configuration first to define constants
require_once ROOT_PATH . '/includes/config/Config.php';
$config = Config::getInstance();

require_once ROOT_PATH . '/debug/AdvancedDebugger.php';
require_once ROOT_PATH . '/debug/ClassVerifier.php';
require_once ROOT_PATH . '/debug/DependencyAnalyzer.php';

$debugger = AdvancedDebugger::getInstance();
$classVerifier = ClassVerifier::getInstance();
$dependencyAnalyzer = DependencyAnalyzer::getInstance();

// Perform comprehensive system check
$systemCheck = [
    'php_version' => [
        'current' => phpversion(),
        'required' => '7.4.0',
        'status' => version_compare(phpversion(), '7.4.0', '>=') ? 'PASS' : 'FAIL'
    ],
    'extensions' => [
        'pdo' => extension_loaded('pdo'),
        'pdo_mysql' => extension_loaded('pdo_mysql'),
        'mysqli' => extension_loaded('mysqli'),
        'json' => extension_loaded('json'),
        'session' => extension_loaded('session'),
        'fileinfo' => extension_loaded('fileinfo'),
        'gd' => extension_loaded('gd'),
    ],
    'php_settings' => [
        'memory_limit' => ini_get('memory_limit'),
        'max_execution_time' => ini_get('max_execution_time'),
        'display_errors' => ini_get('display_errors'),
        'error_reporting' => ini_get('error_reporting'),
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
    ],
    'file_permissions' => [],
    'class_verification' => $classVerifier->generateVerificationReport(),
    'dependency_analysis' => $dependencyAnalyzer->generateDependencyReport(),
];

// Check file permissions
$importantPaths = [
    ROOT_PATH => 'Main directory',
    ROOT_PATH . '/includes' => 'Includes directory',
    ROOT_PATH . '/app' => 'Application directory',
    ROOT_PATH . '/debug' => 'Debug directory',
    ROOT_PATH . '/themes' => 'Themes directory',
    ROOT_PATH . '/public' => 'Public directory',
];

foreach ($importantPaths as $path => $description) {
    $systemCheck['file_permissions'][$description] = [
        'exists' => file_exists($path),
        'readable' => is_readable($path),
        'writable' => is_writable($path),
        'path' => $path
    ];
}

// Generate report
$report = json_encode($systemCheck, JSON_PRETTY_PRINT);

// Save report
$reportFile = __DIR__ . '/system_check.json';
file_put_contents($reportFile, $report);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Check - Restaurant Menu System</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); overflow: hidden; }
        .header { background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 2.5em; font-weight: 300; }
        .content { padding: 30px; }
        .section { margin-bottom: 30px; background: #f8f9fa; border-radius: 8px; padding: 20px; }
        .section h2 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-top: 0; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .card { background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; }
        .card h3 { margin: 0 0 15px 0; color: #2c3e50; }
        .status-pass { color: #28a745; font-weight: bold; }
        .status-fail { color: #dc3545; font-weight: bold; }
        .status-warning { color: #ffc107; font-weight: bold; }
        .metric { display: flex; justify-content: space-between; margin-bottom: 10px; padding: 8px 0; border-bottom: 1px solid #e9ecef; }
        .metric:last-child { border-bottom: none; }
        .actions { text-align: center; margin-top: 30px; }
        .btn { display: inline-block; padding: 12px 24px; background: #3498db; color: white; text-decoration: none; border-radius: 6px; margin: 0 10px; transition: background 0.2s; }
        .btn:hover { background: #2980b9; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #229954; }
        .btn-danger { background: #e74c3c; }
        .btn-danger:hover { background: #c0392b; }
        .progress-bar { width: 100%; height: 8px; background: #e9ecef; border-radius: 4px; overflow: hidden; margin: 10px 0; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, #3498db, #2ecc71); transition: width 0.3s ease; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 System Check Report</h1>
            <p>Comprehensive system analysis for Restaurant Menu System</p>
        </div>
        
        <div class="content">
            <!-- PHP Version Check -->
            <div class="section">
                <h2>🐘 PHP Version</h2>
                <div class="card">
                    <div class="metric">
                        <span>Current Version:</span>
                        <span><?php echo $systemCheck['php_version']['current']; ?></span>
                    </div>
                    <div class="metric">
                        <span>Required Version:</span>
                        <span><?php echo $systemCheck['php_version']['required']; ?></span>
                    </div>
                    <div class="metric">
                        <span>Status:</span>
                        <span class="status-<?php echo strtolower($systemCheck['php_version']['status']); ?>">
                            <?php echo $systemCheck['php_version']['status']; ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- PHP Extensions -->
            <div class="section">
                <h2>📦 PHP Extensions</h2>
                <div class="grid">
                    <?php foreach ($systemCheck['extensions'] as $ext => $loaded): ?>
                        <div class="card">
                            <h3><?php echo $ext; ?></h3>
                            <div class="metric">
                                <span>Status:</span>
                                <span class="status-<?php echo $loaded ? 'pass' : 'fail'; ?>">
                                    <?php echo $loaded ? 'LOADED' : 'NOT LOADED'; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- PHP Settings -->
            <div class="section">
                <h2>⚙️ PHP Configuration</h2>
                <div class="grid">
                    <?php foreach ($systemCheck['php_settings'] as $setting => $value): ?>
                        <div class="card">
                            <h3><?php echo $setting; ?></h3>
                            <div class="metric">
                                <span>Value:</span>
                                <span><?php echo htmlspecialchars($value); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- File Permissions -->
            <div class="section">
                <h2>📁 File Permissions</h2>
                <div class="grid">
                    <?php foreach ($systemCheck['file_permissions'] as $desc => $perms): ?>
                        <div class="card">
                            <h3><?php echo $desc; ?></h3>
                            <div class="metric">
                                <span>Exists:</span>
                                <span class="status-<?php echo $perms['exists'] ? 'pass' : 'fail'; ?>">
                                    <?php echo $perms['exists'] ? 'YES' : 'NO'; ?>
                                </span>
                            </div>
                            <div class="metric">
                                <span>Readable:</span>
                                <span class="status-<?php echo $perms['readable'] ? 'pass' : 'fail'; ?>">
                                    <?php echo $perms['readable'] ? 'YES' : 'NO'; ?>
                                </span>
                            </div>
                            <div class="metric">
                                <span>Writable:</span>
                                <span class="status-<?php echo $perms['writable'] ? 'pass' : 'warning'; ?>">
                                    <?php echo $perms['writable'] ? 'YES' : 'NO'; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Class Verification -->
            <div class="section">
                <h2>📚 Class Verification</h2>
                <div class="card">
                    <div class="metric">
                        <span>Total Classes:</span>
                        <span><?php echo $systemCheck['class_verification']['summary']['total_classes']; ?></span>
                    </div>
                    <div class="metric">
                        <span>Loaded Classes:</span>
                        <span class="status-pass"><?php echo $systemCheck['class_verification']['summary']['loaded_classes']; ?></span>
                    </div>
                    <div class="metric">
                        <span>Failed Classes:</span>
                        <span class="status-<?php echo $systemCheck['class_verification']['summary']['failed_classes'] > 0 ? 'fail' : 'pass'; ?>">
                            <?php echo $systemCheck['class_verification']['summary']['failed_classes']; ?>
                        </span>
                    </div>
                    <div class="metric">
                        <span>Missing Files:</span>
                        <span class="status-<?php echo $systemCheck['class_verification']['summary']['missing_files'] > 0 ? 'fail' : 'pass'; ?>">
                            <?php echo $systemCheck['class_verification']['summary']['missing_files']; ?>
                        </span>
                    </div>
                    <div class="metric">
                        <span>Success Rate:</span>
                        <span><?php echo $systemCheck['class_verification']['summary']['success_rate']; ?>%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $systemCheck['class_verification']['summary']['success_rate']; ?>%"></div>
                    </div>
                </div>
            </div>
            
            <!-- Dependency Analysis -->
            <div class="section">
                <h2>🔗 Dependency Analysis</h2>
                <div class="card">
                    <div class="metric">
                        <span>System Health:</span>
                        <span class="status-<?php echo $systemCheck['dependency_analysis']['system_health']['score'] >= 70 ? 'pass' : 'fail'; ?>">
                            <?php echo ucfirst($systemCheck['dependency_analysis']['system_health']['status']); ?> 
                            (<?php echo $systemCheck['dependency_analysis']['system_health']['score']; ?>%)
                        </span>
                    </div>
                    <div class="metric">
                        <span>Loading Order:</span>
                        <span class="status-<?php echo $systemCheck['dependency_analysis']['analysis']['loading_order_valid'] ? 'pass' : 'fail'; ?>">
                            <?php echo $systemCheck['dependency_analysis']['analysis']['loading_order_valid'] ? 'VALID' : 'INVALID'; ?>
                        </span>
                    </div>
                    <div class="metric">
                        <span>Circular Dependencies:</span>
                        <span class="status-<?php echo empty($systemCheck['dependency_analysis']['analysis']['circular_dependencies']) ? 'pass' : 'fail'; ?>">
                            <?php echo count($systemCheck['dependency_analysis']['analysis']['circular_dependencies']); ?> found
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="actions">
                <a href="../debug-index.php?debug=1" class="btn btn-success">🔍 View Debug Dashboard</a>
                <a href="../index.php" class="btn">🚀 Launch Application</a>
                <a href="download-report.php" class="btn">📥 Download Report</a>
            </div>
        </div>
    </div>
</body>
</html>
<?php
?>