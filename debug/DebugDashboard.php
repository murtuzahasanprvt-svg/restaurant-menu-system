<?php
/**
 * Debug Dashboard - Visual interface for debugging information
 */

class DebugDashboard {
    private static $instance = null;
    private $debugger;
    private $tracer;
    
    private function __construct() {
        $this->debugger = AdvancedDebugger::getInstance();
        $this->tracer = ExecutionTracer::getInstance();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function renderDashboard() {
        $report = $this->debugger->generateReport();
        $traceReport = $this->tracer->generateTraceReport();
        
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Advanced Debug Dashboard - Restaurant Menu System</title>
            <style>
                body { 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                    margin: 0; 
                    padding: 20px; 
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: #333;
                }
                .dashboard { 
                    max-width: 1400px; 
                    margin: 0 auto; 
                    background: white; 
                    border-radius: 12px; 
                    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                    overflow: hidden;
                }
                .header { 
                    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
                    color: white; 
                    padding: 30px; 
                    text-align: center;
                }
                .header h1 { 
                    margin: 0; 
                    font-size: 2.5em;
                    font-weight: 300;
                }
                .header p { 
                    margin: 10px 0 0 0; 
                    opacity: 0.8;
                    font-size: 1.1em;
                }
                .content { padding: 30px; }
                .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px; }
                .card { 
                    background: #f8f9fa; 
                    border: 1px solid #e9ecef; 
                    border-radius: 8px; 
                    padding: 20px;
                    transition: transform 0.2s, box-shadow 0.2s;
                }
                .card:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
                .card h3 { 
                    margin: 0 0 15px 0; 
                    color: #2c3e50; 
                    border-bottom: 2px solid #3498db;
                    padding-bottom: 10px;
                }
                .metric { 
                    display: flex; 
                    justify-content: space-between; 
                    margin-bottom: 10px; 
                    padding: 8px 0;
                    border-bottom: 1px solid #e9ecef;
                }
                .metric:last-child { border-bottom: none; }
                .metric-label { font-weight: 600; color: #555; }
                .metric-value { font-weight: 700; color: #2c3e50; }
                .success { color: #27ae60; }
                .error { color: #e74c3c; }
                .warning { color: #f39c12; }
                .info { color: #3498db; }
                .log-section { margin-top: 30px; }
                .log-container { 
                    background: #2c3e50; 
                    color: #ecf0f1; 
                    border-radius: 8px; 
                    padding: 20px; 
                    font-family: 'Courier New', monospace;
                    max-height: 400px;
                    overflow-y: auto;
                }
                .log-entry { 
                    margin-bottom: 8px; 
                    padding: 5px 0;
                    border-bottom: 1px solid #34495e;
                }
                .log-entry:last-child { border-bottom: none; }
                .log-time { color: #95a5a6; font-size: 0.9em; }
                .log-type { 
                    display: inline-block; 
                    padding: 2px 8px; 
                    border-radius: 4px; 
                    font-size: 0.8em; 
                    font-weight: bold;
                    margin-right: 10px;
                }
                .log-type.ERROR { background: #e74c3c; color: white; }
                .log-type.SUCCESS { background: #27ae60; color: white; }
                .log-type.INFO { background: #3498db; color: white; }
                .log-type.WARNING { background: #f39c12; color: white; }
                .actions { text-align: center; margin-top: 30px; }
                .btn { 
                    display: inline-block; 
                    padding: 12px 24px; 
                    background: #3498db; 
                    color: white; 
                    text-decoration: none; 
                    border-radius: 6px; 
                    margin: 0 10px;
                    transition: background 0.2s;
                }
                .btn:hover { background: #2980b9; }
                .btn-success { background: #27ae60; }
                .btn-success:hover { background: #229954; }
                .btn-danger { background: #e74c3c; }
                .btn-danger:hover { background: #c0392b; }
                .progress-bar { 
                    width: 100%; 
                    height: 8px; 
                    background: #ecf0f1; 
                    border-radius: 4px; 
                    overflow: hidden; 
                    margin: 10px 0;
                }
                .progress-fill { 
                    height: 100%; 
                    background: linear-gradient(90deg, #3498db, #2ecc71); 
                    transition: width 0.3s ease; 
                }
                .trace-step { 
                    background: white; 
                    border: 1px solid #ddd; 
                    border-radius: 6px; 
                    padding: 15px; 
                    margin-bottom: 10px;
                }
                .step-header { 
                    display: flex; 
                    justify-content: space-between; 
                    align-items: center; 
                    margin-bottom: 10px; 
                }
                .step-name { font-weight: bold; color: #2c3e50; }
                .step-duration { color: #7f8c8d; font-size: 0.9em; }
                .step-details { color: #555; font-size: 0.9em; }
            </style>
        </head>
        <body>
            <div class="dashboard">
                <div class="header">
                    <h1>🔍 Advanced Debug Dashboard</h1>
                    <p>Restaurant Menu System - Comprehensive Debugging Information</p>
                </div>
                
                <div class="content">
                    <!-- Performance Metrics -->
                    <div class="grid">
                        <div class="card">
                            <h3>⚡ Performance Metrics</h3>
                            <div class="metric">
                                <span class="metric-label">Execution Time:</span>
                                <span class="metric-value"><?php echo number_format($report['execution_time'] * 1000, 2); ?> ms</span>
                            </div>
                            <div class="metric">
                                <span class="metric-label">Memory Usage:</span>
                                <span class="metric-value"><?php echo $this->formatBytes($report['memory_usage']); ?></span>
                            </div>
                            <div class="metric">
                                <span class="metric-label">Peak Memory:</span>
                                <span class="metric-value"><?php echo $this->formatBytes(memory_get_peak_usage()); ?></span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo min(100, $report['execution_time'] * 1000); ?>%"></div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <h3>📊 Execution Statistics</h3>
                            <div class="metric">
                                <span class="metric-label">Log Entries:</span>
                                <span class="metric-value"><?php echo $report['log_entries']; ?></span>
                            </div>
                            <div class="metric">
                                <span class="metric-label">Errors:</span>
                                <span class="metric-value error"><?php echo count($report['errors']); ?></span>
                            </div>
                            <div class="metric">
                                <span class="metric-label">Classes Loaded:</span>
                                <span class="metric-value success"><?php echo count($report['classes_loaded']); ?></span>
                            </div>
                            <div class="metric">
                                <span class="metric-label">Method Calls:</span>
                                <span class="metric-value info"><?php echo count($report['methods_called']); ?></span>
                            </div>
                        </div>
                        
                        <div class="card">
                            <h3>🎯 System Status</h3>
                            <div class="metric">
                                <span class="metric-label">PHP Version:</span>
                                <span class="metric-value"><?php echo phpversion(); ?></span>
                            </div>
                            <div class="metric">
                                <span class="metric-label">Server OS:</span>
                                <span class="metric-value"><?php echo php_uname('s'); ?></span>
                            </div>
                            <div class="metric">
                                <span class="metric-label">Memory Limit:</span>
                                <span class="metric-value"><?php echo ini_get('memory_limit'); ?></span>
                            </div>
                            <div class="metric">
                                <span class="metric-label">Max Execution:</span>
                                <span class="metric-value"><?php echo ini_get('max_execution_time'); ?>s</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Trace Information -->
                    <div class="card">
                        <h3>🔍 Execution Trace</h3>
                        <?php if (!empty($traceReport['steps'])): ?>
                            <?php foreach ($traceReport['steps'] as $step): ?>
                                <div class="trace-step">
                                    <div class="step-header">
                                        <span class="step-name">Step <?php echo $step['id']; ?>: <?php echo $step['name']; ?></span>
                                        <span class="step-duration">
                                            <?php echo isset($step['duration']) ? number_format($step['duration'] * 1000, 2) . ' ms' : 'Running...'; ?>
                                        </span>
                                    </div>
                                    <div class="step-details">
                                        Memory: <?php echo isset($step['memory_used']) ? $this->formatBytes($step['memory_used']) : 'N/A'; ?>
                                        <?php if (!empty($step['data'])): ?>
                                            | Data: <?php echo json_encode($step['data']); ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No trace steps recorded.</p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Error Log -->
                    <?php if (!empty($report['errors'])): ?>
                        <div class="card">
                            <h3>❌ Error Log</h3>
                            <div class="log-container">
                                <?php foreach ($report['errors'] as $error): ?>
                                    <div class="log-entry">
                                        <span class="log-time"><?php echo number_format($error['timestamp'] * 1000, 2); ?> ms</span>
                                        <span class="log-type ERROR">ERROR</span>
                                        <?php echo htmlspecialchars($error['message']); ?>
                                        <?php if (!empty($error['data'])): ?>
                                            <br><small><?php echo json_encode($error['data']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Recent Log Entries -->
                    <div class="card">
                        <h3>📝 Recent Log Entries</h3>
                        <div class="log-container">
                            <?php 
                            $recentLogs = array_slice($this->debugger->getExecutionLog(), -20);
                            foreach ($recentLogs as $log): 
                            ?>
                                <div class="log-entry">
                                    <span class="log-time"><?php echo number_format($log['timestamp'] * 1000, 2); ?> ms</span>
                                    <span class="log-type <?php echo $log['type']; ?>"><?php echo $log['type']; ?></span>
                                    <?php echo htmlspecialchars($log['message']); ?>
                                    <?php if (!empty($log['data'])): ?>
                                        <br><small>Data: <?php echo json_encode($log['data']); ?></small>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="actions">
                        <a href="index.php" class="btn btn-success">🚀 Launch Application</a>
                        <a href="debug/download-log.php" class="btn">📥 Download Logs</a>
                        <a href="debug/clear-logs.php" class="btn btn-danger">🗑️ Clear Logs</a>
                        <a href="debug/system-check.php" class="btn">🔧 System Check</a>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
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
}
?>