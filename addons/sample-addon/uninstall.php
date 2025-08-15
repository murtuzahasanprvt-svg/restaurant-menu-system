<?php
/**
 * Sample Addon Uninstallation Script
 */

// This script runs when the addon is uninstalled

$db = Database::getInstance();

// Drop sample addon tables
$tables = [
    'sample_addon_data',
    'sample_addon_settings'
];

foreach ($tables as $table) {
    $sql = "DROP TABLE IF EXISTS {$table}";
    $db->query($sql);
    $db->execute();
}

// Remove sample addon assets directory
$assetsDir = ADDONS_PATH . '/sample-addon/assets';
if (file_exists($assetsDir)) {
    // Remove all files in assets directory
    $files = glob($assetsDir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        } elseif (is_dir($file)) {
            // Remove subdirectories
            $subFiles = glob($file . '/*');
            foreach ($subFiles as $subFile) {
                if (is_file($subFile)) {
                    unlink($subFile);
                }
            }
            rmdir($file);
        }
    }
    // Remove the main assets directory
    if (is_dir($assetsDir)) {
        rmdir($assetsDir);
    }
}

// Clean up any sample addon data in main if needed
// For example, remove sample addon entries from user preferences or other related tables

// Log uninstallation
error_log("Sample addon uninstalled successfully at " . date('Y-m-d H:i:s'));

echo "Sample addon uninstallation completed successfully!\n";
?>