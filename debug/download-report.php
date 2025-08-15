<?php
/**
 * Download system check report
 */

$reportFile = __DIR__ . '/system_check.json';

if (file_exists($reportFile)) {
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="system_check.json"');
    readfile($reportFile);
} else {
    echo "System check report file not found. Please run system check first.";
}
?>