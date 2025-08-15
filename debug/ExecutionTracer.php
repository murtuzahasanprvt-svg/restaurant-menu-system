<?php
/**
 * Execution Tracer - Tracks every step of application execution
 */

class ExecutionTracer {
    private static $instance = null;
    private $debugger;
    private $traceStack = [];
    private $currentStep = 0;
    
    private function __construct() {
        $this->debugger = AdvancedDebugger::getInstance();
        $this->traceStack = [];
        $this->currentStep = 0;
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function startStep($name, $data = []) {
        $this->currentStep++;
        $step = [
            'id' => $this->currentStep,
            'name' => $name,
            'start_time' => microtime(true),
            'start_memory' => memory_get_usage(),
            'data' => $data,
            'parent' => end($this->traceStack)['id'] ?? null
        ];
        
        $this->traceStack[] = $step;
        
        $this->debugger->log('STEP_START', "Step $this->currentStep: $name", [
            'step_id' => $this->currentStep,
            'data' => $data
        ]);
        
        return $this->currentStep;
    }
    
    public function endStep($stepId = null, $result = null) {
        if ($stepId === null) {
            $stepId = $this->currentStep;
        }
        
        // Find the step in stack
        $stepIndex = null;
        foreach ($this->traceStack as $index => $step) {
            if ($step['id'] === $stepId) {
                $stepIndex = $index;
                break;
            }
        }
        
        if ($stepIndex === null) {
            $this->debugger->log('TRACE_ERROR', "Cannot end step $stepId - step not found");
            return false;
        }
        
        $step = &$this->traceStack[$stepIndex];
        $step['end_time'] = microtime(true);
        $step['end_memory'] = memory_get_usage();
        $step['duration'] = $step['end_time'] - $step['start_time'];
        $step['memory_used'] = $step['end_memory'] - $step['start_memory'];
        $step['result'] = $result;
        
        $this->debugger->log('STEP_END', "Step $stepId: {$step['name']} completed", [
            'duration' => $step['duration'],
            'memory_used' => $step['memory_used'],
            'result' => $result
        ]);
        
        return true;
    }
    
    public function traceFunction($className, $methodName, $params = []) {
        $this->debugger->logMethodCall($className, $methodName, $params);
    }
    
    public function traceError($error, $file = null, $line = null) {
        $this->debugger->logError($error, $file, $line);
    }
    
    public function traceConstant($name, $value, $defined = true) {
        $this->debugger->logConstant($name, $value, $defined);
    }
    
    public function getTraceStack() {
        return $this->traceStack;
    }
    
    public function getCurrentStep() {
        return $this->currentStep;
    }
    
    public function generateTraceReport() {
        $totalSteps = count($this->traceStack);
        $totalDuration = 0;
        $totalMemory = 0;
        
        foreach ($this->traceStack as $step) {
            if (isset($step['duration'])) {
                $totalDuration += $step['duration'];
                $totalMemory += $step['memory_used'];
            }
        }
        
        return [
            'total_steps' => $totalSteps,
            'total_duration' => $totalDuration,
            'total_memory' => $totalMemory,
            'average_step_duration' => $totalSteps > 0 ? $totalDuration / $totalSteps : 0,
            'steps' => $this->traceStack
        ];
    }
}
?>