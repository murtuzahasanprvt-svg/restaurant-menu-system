<?php
/**
 * Class Loading Verifier - Verifies all classes can be loaded correctly
 */

class ClassVerifier {
    private static $instance = null;
    private $debugger;
    private $requiredClasses = [];
    private $classStatus = [];
    
    private function __construct() {
        $this->debugger = AdvancedDebugger::getInstance();
        $this->initializeRequiredClasses();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function initializeRequiredClasses() {
        $this->requiredClasses = [
            // Core Classes - Load in dependency order
            'Config' => ['file' => '/includes/config/Config.php', 'required' => true, 'dependencies' => []],
            'Database' => ['file' => '/includes/database/Database.php', 'required' => true, 'dependencies' => ['Config']],
            'Session' => ['file' => '/includes/session/Session.php', 'required' => true, 'dependencies' => ['Config']],
            'Auth' => ['file' => '/includes/auth/Auth.php', 'required' => true, 'dependencies' => ['Config', 'Database', 'Session']],
            'Theme' => ['file' => '/includes/theme/Theme.php', 'required' => true, 'dependencies' => ['Config']],
            'AddonManager' => ['file' => '/includes/addon/AddonManager.php', 'required' => false, 'dependencies' => ['Config', 'Database']],
            
            // Application Classes
            'Application' => ['file' => '/app/core/Application.php', 'required' => true, 'dependencies' => ['Config']],
            'Router' => ['file' => '/app/core/Router.php', 'required' => true, 'dependencies' => ['Config']],
            'Controller' => ['file' => '/app/core/Controller.php', 'required' => true, 'dependencies' => ['Config']],
            'Model' => ['file' => '/app/core/Model.php', 'required' => true, 'dependencies' => ['Config', 'Database']],
            
            // Model Classes
            'User' => ['file' => '/app/models/User.php', 'required' => true, 'dependencies' => ['Config', 'Database', 'Model']],
            'Branch' => ['file' => '/app/models/Branch.php', 'required' => true, 'dependencies' => ['Config', 'Database', 'Model']],
            'Table' => ['file' => '/app/models/Table.php', 'required' => true, 'dependencies' => ['Config', 'Database', 'Model']],
            'QRCode' => ['file' => '/app/models/QRCode.php', 'required' => true, 'dependencies' => ['Config', 'Database', 'Model']],
            'ActivityLog' => ['file' => '/app/models/ActivityLog.php', 'required' => true, 'dependencies' => ['Config', 'Database', 'Model']],
            
            // Controller Classes
            'HomeController' => ['file' => '/app/controllers/HomeController.php', 'required' => true, 'dependencies' => ['Config', 'Controller']],
            'AuthController' => ['file' => '/app/controllers/AuthController.php', 'required' => true, 'dependencies' => ['Config', 'Controller']],
            'DashboardController' => ['file' => '/app/controllers/DashboardController.php', 'required' => true, 'dependencies' => ['Config', 'Controller']],
            'BranchController' => ['file' => '/app/controllers/BranchController.php', 'required' => true, 'dependencies' => ['Config', 'Controller']],
            'MenuController' => ['file' => '/app/controllers/MenuController.php', 'required' => true, 'dependencies' => ['Config', 'Controller']],
            'OrderController' => ['file' => '/app/controllers/OrderController.php', 'required' => true, 'dependencies' => ['Config', 'Controller']],
            'QRController' => ['file' => '/app/controllers/QRController.php', 'required' => true, 'dependencies' => ['Config', 'Controller']],
            'ApiController' => ['file' => '/app/controllers/ApiController.php', 'required' => true, 'dependencies' => ['Config', 'Controller']],
        ];
    }
    
    public function verifyAllClasses() {
        $this->debugger->log('CLASS_VERIFICATION_START', 'Starting comprehensive class verification');
        
        $results = [
            'total_classes' => count($this->requiredClasses),
            'loaded_classes' => 0,
            'failed_classes' => 0,
            'missing_files' => 0,
            'class_details' => []
        ];
        
        // Sort classes by dependency order
        $sortedClasses = $this->sortByDependencies();
        
        foreach ($sortedClasses as $className => $classInfo) {
            $result = $this->verifyClass($className, $classInfo);
            $results['class_details'][$className] = $result;
            
            if ($result['status'] === 'loaded') {
                $results['loaded_classes']++;
            } elseif ($result['status'] === 'failed') {
                $results['failed_classes']++;
            } elseif ($result['status'] === 'missing_file') {
                $results['missing_files']++;
            }
        }
        
        $this->debugger->log('CLASS_VERIFICATION_COMPLETE', 'Class verification completed', $results);
        
        return $results;
    }
    
    private function sortByDependencies() {
        $sorted = [];
        $visited = [];
        $visiting = [];
        
        foreach ($this->requiredClasses as $className => $classInfo) {
            if (!isset($visited[$className])) {
                $this->visitClass($className, $visited, $visiting, $sorted);
            }
        }
        
        return $sorted;
    }
    
    private function visitClass($className, &$visited, &$visiting, &$sorted) {
        if (isset($visiting[$className])) {
            // Circular dependency detected
            return;
        }
        
        if (isset($visited[$className])) {
            return;
        }
        
        $visiting[$className] = true;
        
        // Visit dependencies first
        if (isset($this->requiredClasses[$className]['dependencies'])) {
            foreach ($this->requiredClasses[$className]['dependencies'] as $dependency) {
                if (isset($this->requiredClasses[$dependency])) {
                    $this->visitClass($dependency, $visited, $visiting, $sorted);
                }
            }
        }
        
        unset($visiting[$className]);
        $visited[$className] = true;
        $sorted[$className] = $this->requiredClasses[$className];
    }
    
    public function verifyClass($className, $classInfo) {
        $result = [
            'class_name' => $className,
            'file_path' => $classInfo['file'],
            'required' => $classInfo['required'],
            'status' => 'unknown',
            'error' => null,
            'file_exists' => false,
            'file_size' => 0,
            'file_readable' => false,
            'class_exists' => false,
            'dependencies' => []
        ];
        
        try {
            // Check if file exists
            $fullPath = ROOT_PATH . $classInfo['file'];
            $result['file_exists'] = file_exists($fullPath);
            
            if (!$result['file_exists']) {
                $result['status'] = 'missing_file';
                $result['error'] = 'File does not exist: ' . $fullPath;
                $this->debugger->log('CLASS_FILE_MISSING', "Class file missing: $className", ['file' => $fullPath]);
                return $result;
            }
            
            // Check file readability
            $result['file_readable'] = is_readable($fullPath);
            $result['file_size'] = filesize($fullPath);
            
            if (!$result['file_readable']) {
                $result['status'] = 'failed';
                $result['error'] = 'File is not readable: ' . $fullPath;
                $this->debugger->log('CLASS_FILE_NOT_READABLE', "Class file not readable: $className", ['file' => $fullPath]);
                return $result;
            }
            
            // Check if class already exists
            $result['class_exists'] = class_exists($className, false);
            
            if ($result['class_exists']) {
                $result['status'] = 'loaded';
                $this->debugger->log('CLASS_ALREADY_LOADED', "Class already loaded: $className");
                return $result;
            }
            
            // Preload dependencies if specified
            if (isset($classInfo['dependencies']) && !empty($classInfo['dependencies'])) {
                foreach ($classInfo['dependencies'] as $dependency) {
                    if (isset($this->requiredClasses[$dependency]) && !class_exists($dependency, false)) {
                        $this->debugger->log('CLASS_LOADING_DEPENDENCY', "Loading dependency $dependency for $className");
                        $depResult = $this->verifyClass($dependency, $this->requiredClasses[$dependency]);
                        if ($depResult['status'] !== 'loaded') {
                            $result['status'] = 'failed';
                            $result['error'] = "Dependency $dependency failed to load: " . ($depResult['error'] ?? 'Unknown error');
                            $this->debugger->log('CLASS_DEPENDENCY_FAILED', "Dependency $dependency failed for $className", ['error' => $result['error']]);
                            return $result;
                        }
                    }
                }
            }
            
            // Try to load the class
            $this->debugger->log('CLASS_LOADING', "Loading class: $className from: {$classInfo['file']}");
            
            // Include the file
            require_once $fullPath;
            
            // Check if class exists after loading
            $result['class_exists'] = class_exists($className, false);
            
            if ($result['class_exists']) {
                $result['status'] = 'loaded';
                $this->debugger->log('CLASS_LOADED', "Successfully loaded class: $className");
                
                // Check class dependencies
                $result['dependencies'] = $this->analyzeClassDependencies($className);
                
            } else {
                $result['status'] = 'failed';
                $result['error'] = 'Class not found after loading file: ' . $className;
                $this->debugger->log('CLASS_LOAD_FAILED', "Failed to load class: $className", ['file' => $fullPath]);
            }
            
        } catch (Exception $e) {
            $result['status'] = 'failed';
            $result['error'] = $e->getMessage();
            $this->debugger->log('CLASS_LOAD_EXCEPTION', "Exception loading class: $className", [
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
        
        return $result;
    }
    
    private function analyzeClassDependencies($className) {
        $dependencies = [];
        
        try {
            $reflection = new ReflectionClass($className);
            
            // Get parent class
            $parentClass = $reflection->getParentClass();
            if ($parentClass) {
                $dependencies['parent'] = $parentClass->getName();
            }
            
            // Get interfaces
            $interfaces = $reflection->getInterfaceNames();
            if (!empty($interfaces)) {
                $dependencies['interfaces'] = $interfaces;
            }
            
            // Check constructor dependencies
            $constructor = $reflection->getConstructor();
            if ($constructor) {
                $parameters = $constructor->getParameters();
                $constructorDependencies = [];
                foreach ($parameters as $parameter) {
                    if ($parameter->getClass()) {
                        $constructorDependencies[] = $parameter->getClass()->getName();
                    }
                }
                if (!empty($constructorDependencies)) {
                    $dependencies['constructor'] = $constructorDependencies;
                }
            }
            
        } catch (Exception $e) {
            $dependencies['error'] = $e->getMessage();
        }
        
        return $dependencies;
    }
    
    public function getRequiredClasses() {
        return $this->requiredClasses;
    }
    
    public function getClassStatus($className) {
        return $this->classStatus[$className] ?? null;
    }
    
    public function generateVerificationReport() {
        $results = $this->verifyAllClasses();
        
        $report = [
            'summary' => [
                'total_classes' => $results['total_classes'],
                'loaded_classes' => $results['loaded_classes'],
                'failed_classes' => $results['failed_classes'],
                'missing_files' => $results['missing_files'],
                'success_rate' => $results['total_classes'] > 0 ? 
                    round(($results['loaded_classes'] / $results['total_classes']) * 100, 2) : 0
            ],
            'classes' => $results['class_details'],
            'critical_issues' => [],
            'recommendations' => []
        ];
        
        // Identify critical issues
        foreach ($results['class_details'] as $className => $details) {
            if ($details['required'] && $details['status'] !== 'loaded') {
                $report['critical_issues'][] = [
                    'class' => $className,
                    'issue' => $details['error'] ?? 'Unknown error',
                    'status' => $details['status']
                ];
            }
        }
        
        // Generate recommendations
        if ($report['summary']['success_rate'] < 100) {
            $report['recommendations'][] = 'Some required classes failed to load. Check file permissions and paths.';
        }
        
        if ($report['summary']['missing_files'] > 0) {
            $report['recommendations'][] = 'Missing class files detected. Ensure all files are present.';
        }
        
        if (!empty($report['critical_issues'])) {
            $report['recommendations'][] = 'Critical issues found with required classes. System may not function properly.';
        }
        
        return $report;
    }
}
?>