<?php
/**
 * System Configuration - Legacy Compatibility
 * This file now uses the new Config class for backward compatibility
 */

// Load the new configuration manager
require_once __DIR__ . '/config/Config.php';
$config = Config::getInstance();

// All constants are now defined by the Config class
// This file remains for backward compatibility only
?>