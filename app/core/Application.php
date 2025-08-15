<?php
/**
 * Core Application Class
 */

// Ensure dependencies are loaded before defining the class
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__, 2));
}

// Load configuration first to ensure constants are defined
if (!class_exists('Config')) {
    require_once ROOT_PATH . '/includes/config/Config.php';
    $config = Config::getInstance();
}

// Load Database class
if (!class_exists('Database')) {
    require_once ROOT_PATH . '/includes/database/Database.php';
}

// Load Session class
if (!class_exists('Session')) {
    require_once ROOT_PATH . '/includes/session/Session.php';
}

// Load Auth class
if (!class_exists('Auth')) {
    require_once ROOT_PATH . '/includes/auth/Auth.php';
}

// Load Theme class
if (!class_exists('Theme')) {
    require_once ROOT_PATH . '/includes/theme/Theme.php';
}

// Load Router class
if (!class_exists('Router')) {
    require_once ROOT_PATH . '/app/core/Router.php';
}

class Application {
    private static $instance = null;
    private $config;
    private $db;
    private $router;
    private $session;
    private $auth;
    private $theme;
    private $addons = [];

    private function __construct() {
        // Initialize database
        try {
            $this->db = Database::getInstance();
        } catch (Exception $e) {
            // Log database connection error but continue
            error_log("Database connection failed: " . $e->getMessage());
        }
        
        // Initialize session
        try {
            $this->session = new Session();
        } catch (Exception $e) {
            error_log("Session initialization failed: " . $e->getMessage());
        }
        
        // Initialize authentication
        try {
            $this->auth = new Auth();
        } catch (Exception $e) {
            error_log("Auth initialization failed: " . $e->getMessage());
        }
        
        // Initialize theme
        try {
            $this->theme = new Theme();
        } catch (Exception $e) {
            error_log("Theme initialization failed: " . $e->getMessage());
        }
        
        // Initialize addons
        try {
            $this->loadAddons();
        } catch (Exception $e) {
            error_log("Addons loading failed: " . $e->getMessage());
        }
        
        // Initialize router (but don't load routes yet)
        try {
            $this->router = new Router();
        } catch (Exception $e) {
            error_log("Router initialization failed: " . $e->getMessage());
        }
        
        // Set error handlers
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
    }

    public function initialize() {
        // Load routes after application is fully initialized
        $this->router->loadRoutes($this);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function run() {
        // Route the request
        $this->router->dispatch();
    }

    public function getDb() {
        return $this->db;
    }

    public function getSession() {
        return $this->session;
    }

    public function getAuth() {
        return $this->auth;
    }

    public function getTheme() {
        return $this->theme;
    }

    public function getRouter() {
        return $this->router;
    }

    public function getAddons() {
        return $this->addons;
    }

    private function loadAddons() {
        // Check if addons directory exists
        $addonsPath = defined('ADDONS_PATH') ? ADDONS_PATH : ROOT_PATH . '/addons';
        if (!file_exists($addonsPath)) {
            error_log("Addons directory not found: " . $addonsPath);
            return;
        }
        
        $addonDirs = glob($addonsPath . '/*', GLOB_ONLYDIR);
        
        foreach ($addonDirs as $addonDir) {
            $addonName = basename($addonDir);
            $addonFile = $addonDir . '/' . $addonName . '.php';
            
            if (file_exists($addonFile)) {
                // Ensure BaseAddon class is available
                if (!class_exists('BaseAddon')) {
                    try {
                        require_once ROOT_PATH . '/includes/addon/AddonManager.php';
                    } catch (Exception $e) {
                        error_log("Failed to load AddonManager: " . $e->getMessage());
                        continue;
                    }
                }
                
                try {
                    require_once $addonFile;
                    $addonClass = ucfirst($addonName) . 'Addon';
                    
                    if (class_exists($addonClass)) {
                        // Create addon data array for constructor
                        $addonData = [
                            'directory_name' => $addonName,
                            'name' => $addonName,
                            'version' => '1.0.0'
                        ];
                        
                        $this->addons[$addonName] = new $addonClass($addonData);
                    }
                } catch (Exception $e) {
                    error_log("Failed to load addon {$addonName}: " . $e->getMessage());
                }
            }
        }
    }

    public function handleError($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            return false;
        }

        $errorType = match($errno) {
            E_ERROR => 'Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated',
            default => 'Unknown'
        };

        $message = "[$errorType] $errstr in $errfile on line $errline";
        error_log($message);

        if (ini_get('display_errors')) {
            echo "<div style='color: red; font-family: monospace; padding: 10px; border: 1px solid #ccc; background: #f9f9f9;'>";
            echo "<strong>$errorType:</strong> $errstr<br>";
            echo "<em>in $errfile on line $errline</em>";
            echo "</div>";
        }

        return true;
    }

    public function handleException($exception) {
        $message = "Uncaught Exception: " . $exception->getMessage() . 
                   " in " . $exception->getFile() . " on line " . $exception->getLine();
        error_log($message);

        if (ini_get('display_errors')) {
            echo "<div style='color: red; font-family: monospace; padding: 10px; border: 1px solid #ccc; background: #f9f9f9;'>";
            echo "<strong>Uncaught Exception:</strong> " . $exception->getMessage() . "<br>";
            echo "<em>in " . $exception->getFile() . " on line " . $exception->getLine() . "</em>";
            echo "</div>";
        } else {
            echo "<h1>500 Internal Server Error</h1>";
            echo "<p>Something went wrong. Please try again later.</p>";
        }
    }

    // Prevent cloning of singleton instance
    private function __clone() {}

    // Prevent unserializing of singleton instance
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
?>