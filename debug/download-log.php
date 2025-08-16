<?php
/**
 * Download debug logs
 */

$logFile = __DIR__ . '/debug.log';
$reportFile = __DIR__ . '/debug_report.json';

if (file_exists($logFile)) {
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="debug.log"');
    readfile($logFile);
} else {
    echo "Debug log file not found.";
}
?>