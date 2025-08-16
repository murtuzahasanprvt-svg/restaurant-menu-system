<?php
/**
 * Advanced Debugger for Restaurant Menu System
 * Provides comprehensive debugging and error tracking
 */

class AdvancedDebugger {
    private static $instance = null;
    private $logFile;
    private $executionLog = [];
    private $startTime;
    private $memoryStart;
    private $enabled = true;
    
    private function __construct() {
        $this->startTime = microtime(true);
        $this->memoryStart = memory_get_usage();
        $this->logFile = __DIR__ . '/debug.log';
        
        // Clear previous log
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
        
        $this->log('DEBUGGER_START', 'Advanced Debugger initialized');
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function log($type, $message, $data = null) {
        if (!$this->enabled) return;
        
        $timestamp = microtime(true) - $this->startTime;
        $memory = memory_get_usage() - $this->memoryStart;
        
        $logEntry = [
            'timestamp' => $timestamp,
            'memory' => $memory,
            'type' => $type,
            'message' => $message,
            'data' => $data,
            'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)
        ];
        
        $this->executionLog[] = $logEntry;
        
        // Write to file
        $fileLog = sprintf(
            "[%.4f] [%s] [%s] %s\n",
            $timestamp,
            $this->formatBytes($memory),
            $type,
            $message
        );
        
        if ($data !== null) {
            $fileLog .= "Data: " . print_r($data, true) . "\n";
        }
        
        file_put_contents($this->logFile, $fileLog, FILE_APPEND);
    }
    
    public function logClassLoad($className, $file, $success = true) {
        $this->log('CLASS_LOAD', "Loading class: $className from: $file", [
            'success' => $success,
            'file_exists' => file_exists($file),
            'file_size' => file_exists($file) ? filesize($file) : 0
        ]);
    }
    
    public function logMethodCall($className, $method, $params = []) {
        $this->log('METHOD_CALL', "Calling: $className::$method", $params);
    }
    
    public function logError($error, $file = null, $line = null) {
        $this->log('ERROR', $error, [
            'file' => $file,
            'line' => $line,
            'php_error' => error_get_last()
        ]);
    }
    
    public function logConstant($name, $value, $defined = true) {
        $this->log('CONSTANT', "Constant: $name = " . print_r($value, true), [
            'defined' => $defined
        ]);
    }
    
    public function logRoute($method, $route, $handler) {
        $this->log('ROUTE', "$method $route -> $handler");
    }
    
    public function logApplicationState($state, $data = []) {
        $this->log('APP_STATE', $state, $data);
    }
    
    public function getExecutionLog() {
        return $this->executionLog;
    }
    
    public function generateReport() {
        $endTime = microtime(true);
        $totalTime = $endTime - $this->startTime;
        $totalMemory = memory_get_usage() - $this->memoryStart;
        
        $report = [
            'execution_time' => $totalTime,
            'memory_usage' => $totalMemory,
            'log_entries' => count($this->executionLog),
            'errors' => array_filter($this->executionLog, function($entry) {
                return $entry['type'] === 'ERROR';
            }),
            'classes_loaded' => array_filter($this->executionLog, function($entry) {
                return $entry['type'] === 'CLASS_LOAD';
            }),
            'methods_called' => array_filter($this->executionLog, function($entry) {
                return $entry['type'] === 'METHOD_CALL';
            })
        ];
        
        return $report;
    }
    
    public function saveReport() {
        $report = $this->generateReport();
        $reportFile = __DIR__ . '/debug_report.json';
        file_put_contents($reportFile, json_encode($report, JSON_PRETTY_PRINT));
        return $reportFile;
    }
    
    private function formatBytes($bytes) {
        if ($bytes === 0) return '0B';
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . $units[$pow];
    }
    
    public function enable() {
        $this->enabled = true;
        $this->log('DEBUGGER_CONTROL', 'Debugger enabled');
    }
    
    public function disable() {
        $this->log('DEBUGGER_CONTROL', 'Debugger disabled');
        $this->enabled = false;
    }
}
?>