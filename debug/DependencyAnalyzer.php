<?php
/**
 * Dependency Analyzer - Analyzes system dependencies and loading order
 */

class DependencyAnalyzer {
    private static $instance = null;
    private $debugger;
    private $dependencyMap = [];
    private $loadingOrder = [];
    private $circularDependencies = [];
    
    private function __construct() {
        $this->debugger = AdvancedDebugger::getInstance();
        $this->initializeDependencyMap();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function initializeDependencyMap() {
        $this->dependencyMap = [
            'Config' => [], // No dependencies - should be loaded first
            'Database' => ['Config'],
            'Session' => ['Config'],
            'Auth' => ['Config', 'Database', 'Session'],
            'Theme' => ['Config'],
            'AddonManager' => ['Config'],
            'Router' => ['Config'],
            'Application' => ['Config', 'Database', 'Session', 'Auth', 'Theme', 'AddonManager', 'Router'],
            'Model' => ['Config', 'Database', 'Application'],
            'Controller' => ['Config', 'Application', 'Model'],
            'User' => ['Config', 'Database', 'Model'],
            'Branch' => ['Config', 'Database', 'Model'],
            'Table' => ['Config', 'Database', 'Model'],
            'QRCode' => ['Config', 'Database', 'Model'],
            'ActivityLog' => ['Config', 'Database', 'Model'],
            'HomeController' => ['Config', 'Application', 'Controller', 'Branch'],
            'AuthController' => ['Config', 'Application', 'Controller', 'User'],
            'DashboardController' => ['Config', 'Application', 'Controller'],
            'BranchController' => ['Config', 'Application', 'Controller', 'Branch'],
            'MenuController' => ['Config', 'Application', 'Controller'],
            'OrderController' => ['Config', 'Application', 'Controller'],
            'QRController' => ['Config', 'Application', 'Controller', 'QRCode'],
            'ApiController' => ['Config', 'Application', 'Controller'],
        ];
    }
    
    public function analyzeDependencies() {
        $this->debugger->log('DEPENDENCY_ANALYSIS_START', 'Starting comprehensive dependency analysis');
        
        $analysis = [
            'total_classes' => count($this->dependencyMap),
            'loading_order_valid' => true,
            'circular_dependencies' => [],
            'missing_dependencies' => [],
            'class_status' => [],
            'recommendations' => []
        ];
        
        // Check each class's dependencies
        foreach ($this->dependencyMap as $className => $dependencies) {
            $classAnalysis = $this->analyzeClassDependencies($className, $dependencies);
            $analysis['class_status'][$className] = $classAnalysis;
            
            if (!$classAnalysis['dependencies_satisfied']) {
                $analysis['loading_order_valid'] = false;
                $analysis['missing_dependencies'] = array_merge(
                    $analysis['missing_dependencies'],
                    $classAnalysis['missing_dependencies']
                );
            }
        }
        
        // Check for circular dependencies
        $analysis['circular_dependencies'] = $this->detectCircularDependencies();
        
        // Generate recommendations
        $analysis['recommendations'] = $this->generateRecommendations($analysis);
        
        $this->debugger->log('DEPENDENCY_ANALYSIS_COMPLETE', 'Dependency analysis completed', $analysis);
        
        return $analysis;
    }
    
    private function analyzeClassDependencies($className, $dependencies) {
        $analysis = [
            'class_name' => $className,
            'dependencies' => $dependencies,
            'dependencies_satisfied' => true,
            'missing_dependencies' => [],
            'loaded' => false,
            'loadable' => true
        ];
        
        // Check if class is currently loaded
        $analysis['loaded'] = class_exists($className, false);
        
        // Check each dependency
        foreach ($dependencies as $dependency) {
            if (!class_exists($dependency, false)) {
                $analysis['dependencies_satisfied'] = false;
                $analysis['missing_dependencies'][] = $dependency;
                $analysis['loadable'] = false;
            }
        }
        
        return $analysis;
    }
    
    private function detectCircularDependencies() {
        $circular = [];
        $visited = [];
        $recursionStack = [];
        
        foreach ($this->dependencyMap as $className => $dependencies) {
            if (!isset($visited[$className])) {
                $this->visitNode($className, $visited, $recursionStack, $circular);
            }
        }
        
        return $circular;
    }
    
    private function visitNode($className, &$visited, &$recursionStack, &$circular) {
        $visited[$className] = true;
        $recursionStack[$className] = true;
        
        if (isset($this->dependencyMap[$className])) {
            foreach ($this->dependencyMap[$className] as $dependency) {
                if (!isset($visited[$dependency])) {
                    $this->visitNode($dependency, $visited, $recursionStack, $circular);
                } elseif (isset($recursionStack[$dependency])) {
                    $circular[] = [
                        'cycle' => array_keys($recursionStack),
                        'detected_at' => $dependency
                    ];
                }
            }
        }
        
        unset($recursionStack[$className]);
    }
    
    private function generateRecommendations($analysis) {
        $recommendations = [];
        
        if (!$analysis['loading_order_valid']) {
            $recommendations[] = 'Loading order issues detected. Some classes depend on others that are not yet loaded.';
        }
        
        if (!empty($analysis['circular_dependencies'])) {
            $recommendations[] = 'Circular dependencies detected. This may cause infinite recursion during initialization.';
        }
        
        if (!empty($analysis['missing_dependencies'])) {
            $uniqueMissing = array_unique($analysis['missing_dependencies']);
            $recommendations[] = 'Missing dependencies: ' . implode(', ', $uniqueMissing) . 
                               '. Ensure these classes are loaded before their dependents.';
        }
        
        // Check for common issues
        $loadableClasses = array_filter($analysis['class_status'], function($status) {
            return $status['loadable'];
        });
        
        if (count($loadableClasses) < count($analysis['class_status'])) {
            $recommendations[] = 'Some classes cannot be loaded due to missing dependencies. Check the class status details.';
        }
        
        return $recommendations;
    }
    
    public function getOptimalLoadingOrder() {
        $this->debugger->log('LOADING_ORDER_CALCULATION', 'Calculating optimal loading order');
        
        $order = [];
        $remaining = array_keys($this->dependencyMap);
        
        while (!empty($remaining)) {
            $found = false;
            
            foreach ($remaining as $index => $className) {
                $dependencies = $this->dependencyMap[$className];
                $dependenciesSatisfied = true;
                
                // Check if all dependencies are already in order
                foreach ($dependencies as $dependency) {
                    if (!in_array($dependency, $order)) {
                        $dependenciesSatisfied = false;
                        break;
                    }
                }
                
                if ($dependenciesSatisfied) {
                    $order[] = $className;
                    unset($remaining[$index]);
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                // Circular dependency detected, break the loop
                $this->debugger->log('LOADING_ORDER_ERROR', 'Circular dependency detected, cannot determine optimal order', ['remaining' => $remaining]);
                break;
            }
        }
        
        return $order;
    }
    
    public function validateCurrentLoadingOrder($currentOrder) {
        $this->debugger->log('LOADING_ORDER_VALIDATION', 'Validating current loading order');
        
        $validation = [
            'is_valid' => true,
            'issues' => [],
            'optimized_order' => $this->getOptimalLoadingOrder()
        ];
        
        foreach ($currentOrder as $index => $className) {
            if (!isset($this->dependencyMap[$className])) {
                $validation['issues'][] = "Class $className is not in dependency map";
                continue;
            }
            
            $dependencies = $this->dependencyMap[$className];
            foreach ($dependencies as $dependency) {
                $dependencyIndex = array_search($dependency, $currentOrder);
                if ($dependencyIndex === false || $dependencyIndex > $index) {
                    $validation['is_valid'] = false;
                    $validation['issues'][] = "Class $className depends on $dependency but it's loaded after or not at all";
                }
            }
        }
        
        return $validation;
    }
    
    public function generateDependencyReport() {
        $analysis = $this->analyzeDependencies();
        $optimalOrder = $this->getOptimalLoadingOrder();
        
        $report = [
            'analysis' => $analysis,
            'optimal_loading_order' => $optimalOrder,
            'current_loading_order' => $this->getCurrentLoadingOrder(),
            'order_validation' => $this->validateCurrentLoadingOrder($this->getCurrentLoadingOrder()),
            'system_health' => $this->calculateSystemHealth($analysis)
        ];
        
        return $report;
    }
    
    private function getCurrentLoadingOrder() {
        // This would typically be extracted from the actual loading sequence
        // For now, we'll return the order from index.php
        return [
            'Config', 'Database', 'Session', 'Auth', 'Theme', 'AddonManager',
            'Router', 'Application', 'Model', 'Controller', 'User', 'Branch',
            'Table', 'QRCode', 'ActivityLog', 'AuthController', 'QRController',
            'HomeController', 'DashboardController', 'BranchController',
            'MenuController', 'OrderController', 'ApiController'
        ];
    }
    
    private function calculateSystemHealth($analysis) {
        $health = [
            'score' => 100,
            'status' => 'excellent',
            'issues' => []
        ];
        
        // Deduct points for loading order issues
        if (!$analysis['loading_order_valid']) {
            $health['score'] -= 30;
            $health['issues'][] = 'Loading order issues';
        }
        
        // Deduct points for circular dependencies
        if (!empty($analysis['circular_dependencies'])) {
            $health['score'] -= 40;
            $health['issues'][] = 'Circular dependencies';
        }
        
        // Deduct points for missing dependencies
        if (!empty($analysis['missing_dependencies'])) {
            $missingCount = count(array_unique($analysis['missing_dependencies']));
            $health['score'] -= min(30, $missingCount * 10);
            $health['issues'][] = 'Missing dependencies';
        }
        
        // Determine status
        if ($health['score'] >= 90) {
            $health['status'] = 'excellent';
        } elseif ($health['score'] >= 70) {
            $health['status'] = 'good';
        } elseif ($health['score'] >= 50) {
            $health['status'] = 'fair';
        } else {
            $health['status'] = 'poor';
        }
        
        return $health;
    }
}
?>