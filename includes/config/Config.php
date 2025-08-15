<?php
/**
 * Configuration Manager Class
 * Handles all system configuration and prevents constant redefinition
 */

class Config {
    private static $instance = null;
    private $config = [];
    private $constantsDefined = false;

    private function __construct() {
        $this->loadConfiguration();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadConfiguration() {
        // Database Configuration - Local Laragon Settings
        $this->config['db'] = [
            'host' => 'localhost',
            'name' => 'restaurant_menu_system',
            'user' => 'root',
            'pass' => '',
            'charset' => 'utf8mb4'
        ];

        // Application Configuration - Local Development Settings
        $this->config['app'] = [
            'name' => 'Restaurant Menu System',
            'url' => 'http://localhost/restaurant-menu-system',
            'version' => '1.0.0'
        ];

        // Define constants only once
        if (!$this->constantsDefined) {
            $this->defineConstants();
            $this->constantsDefined = true;
        }
    }

    private function defineConstants() {
        // Define ROOT_PATH if not already defined
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', dirname(__DIR__));
        }

        // Database Constants
        if (!defined('DB_HOST')) {
            define('DB_HOST', $this->config['db']['host']);
        }
        if (!defined('DB_NAME')) {
            define('DB_NAME', $this->config['db']['name']);
        }
        if (!defined('DB_USER')) {
            define('DB_USER', $this->config['db']['user']);
        }
        if (!defined('DB_PASS')) {
            define('DB_PASS', $this->config['db']['pass']);
        }
        if (!defined('DB_CHARSET')) {
            define('DB_CHARSET', $this->config['db']['charset']);
        }

        // Application Constants
        if (!defined('APP_NAME')) {
            define('APP_NAME', $this->config['app']['name']);
        }
        if (!defined('APP_URL')) {
            define('APP_URL', $this->config['app']['url']);
        }
        if (!defined('APP_VERSION')) {
            define('APP_VERSION', $this->config['app']['version']);
        }

        // Path Constants
        if (!defined('APP_PATH')) {
            define('APP_PATH', ROOT_PATH);
        }
        if (!defined('INCLUDES_PATH')) {
            define('INCLUDES_PATH', ROOT_PATH . '/includes');
        }
        if (!defined('THEMES_PATH')) {
            define('THEMES_PATH', ROOT_PATH . '/themes');
        }
        if (!defined('ADDONS_PATH')) {
            define('ADDONS_PATH', ROOT_PATH . '/addons');
        }
        if (!defined('PUBLIC_PATH')) {
            define('PUBLIC_PATH', ROOT_PATH . '/public');
        }
        if (!defined('UPLOADS_PATH')) {
            define('UPLOADS_PATH', ROOT_PATH . '/uploads');
        }
        if (!defined('QR_CODE_PATH')) {
            define('QR_CODE_PATH', ROOT_PATH . '/uploads/qrcodes');
        }

        // Session Configuration
        if (!defined('SESSION_LIFETIME')) {
            define('SESSION_LIFETIME', 3600);
        }
        if (!defined('SESSION_NAME')) {
            define('SESSION_NAME', 'restaurant_menu_session');
        }

        // Security Configuration
        if (!defined('CSRF_TOKEN_NAME')) {
            define('CSRF_TOKEN_NAME', 'csrf_token');
        }
        if (!defined('HASH_COST')) {
            define('HASH_COST', 12);
        }
        if (!defined('MAX_LOGIN_ATTEMPTS')) {
            define('MAX_LOGIN_ATTEMPTS', 5);
        }
        if (!defined('LOCKOUT_DURATION')) {
            define('LOCKOUT_DURATION', 900);
        }

        // QR Code Configuration
        if (!defined('QR_CODE_SIZE')) {
            define('QR_CODE_SIZE', 300);
        }
        if (!defined('QR_CODE_PREFIX')) {
            define('QR_CODE_PREFIX', 'QR-');
        }

        // Error Reporting
        if (!defined('DEBUG_MODE')) {
            define('DEBUG_MODE', true);
        }

        // Set error reporting based on DEBUG_MODE
        if (DEBUG_MODE) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else {
            error_reporting(0);
            ini_set('display_errors', 0);
        }

        // Set timezone
        date_default_timezone_set('UTC');

        // Ensure upload directories exist
        if (!file_exists(UPLOADS_PATH)) {
            mkdir(UPLOADS_PATH, 0755, true);
        }
        if (!file_exists(QR_CODE_PATH)) {
            mkdir(QR_CODE_PATH, 0755, true);
        }
    }

    public function get($key, $default = null) {
        return $this->config[$key] ?? $default;
    }

    public function set($key, $value) {
        $this->config[$key] = $value;
    }

    // Prevent cloning of singleton instance
    private function __clone() {}

    // Prevent unserializing of singleton instance
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
?>