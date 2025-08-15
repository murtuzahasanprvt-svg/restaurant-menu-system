<?php
/**
 * Addon Management System
 */

class AddonManager {
    private $addonsPath;
    private $db;
    private $loadedAddons = [];
    private $hooks = [];
    private $filters = [];

    public function __construct() {
        $this->addonsPath = ADDONS_PATH;
        $this->db = Database::getInstance();
        $this->loadInstalledAddons();
    }

    /**
     * Load all installed and active addons
     */
    public function loadInstalledAddons() {
        $sql = "SELECT * FROM addons WHERE is_installed = 1 AND is_active = 1 ORDER BY priority DESC";
        $this->db->query($sql);
        $addons = $this->db->resultSet();

        foreach ($addons as $addon) {
            $this->loadAddon($addon);
        }
    }

    /**
     * Load a specific addon
     */
    public function loadAddon($addonData) {
        $addonPath = $this->addonsPath . '/' . $addonData['directory_name'];
        $addonFile = $addonPath . '/' . $addonData['directory_name'] . '.php';

        if (!file_exists($addonFile)) {
            error_log("Addon file not found: {$addonFile}");
            return false;
        }

        // Include addon file
        require_once $addonFile;

        // Create addon class instance
        $className = ucfirst($addonData['directory_name']) . 'Addon';
        if (class_exists($className)) {
            $addonInstance = new $className($addonData);
            
            // Initialize addon
            if (method_exists($addonInstance, 'initialize')) {
                $addonInstance->initialize();
            }

            // Register addon hooks and filters
            if (method_exists($addonInstance, 'registerHooks')) {
                $addonInstance->registerHooks();
            }

            $this->loadedAddons[$addonData['directory_name']] = $addonInstance;
            return true;
        }

        return false;
    }

    /**
     * Get all available addons
     */
    public function getAvailableAddons() {
        $addons = [];

        // Get addons from database
        $sql = "SELECT * FROM addons ORDER BY name";
        $this->db->query($sql);
        $dbAddons = $this->db->resultSet();

        // Get addons from filesystem
        $addonDirs = glob($this->addonsPath . '/*', GLOB_ONLYDIR);

        foreach ($addonDirs as $addonDir) {
            $addonName = basename($addonDir);
            $addonConfig = $addonDir . '/config.php';
            $addonFile = $addonDir . '/' . $addonName . '.php';

            $addonData = [
                'directory_name' => $addonName,
                'name' => $addonName,
                'description' => '',
                'version' => '1.0.0',
                'author' => 'Unknown',
                'is_installed' => false,
                'is_active' => false,
                'priority' => 10
            ];

            // Load addon config if exists
            if (file_exists($addonConfig)) {
                $config = include $addonConfig;
                $addonData = array_merge($addonData, $config);
            }

            // Check if addon file exists
            $addonData['has_main_file'] = file_exists($addonFile);

            // Check if addon is in database
            foreach ($dbAddons as $dbAddon) {
                if ($dbAddon['directory_name'] === $addonName) {
                    $addonData['is_installed'] = true;
                    $addonData['is_active'] = $dbAddon['is_active'];
                    $addonData['id'] = $dbAddon['id'];
                    $addonData['priority'] = $dbAddon['priority'];
                    break;
                }
            }

            $addons[] = $addonData;
        }

        return $addons;
    }

    /**
     * Install an addon
     */
    public function installAddon($addonName) {
        $addonPath = $this->addonsPath . '/' . $addonName;

        if (!is_dir($addonPath)) {
            return ['success' => false, 'message' => 'Addon directory not found.'];
        }

        $configFile = $addonPath . '/config.php';
        $addonFile = $addonPath . '/' . $addonName . '.php';

        if (!file_exists($configFile)) {
            return ['success' => false, 'message' => 'Addon configuration file not found.'];
        }

        if (!file_exists($addonFile)) {
            return ['success' => false, 'message' => 'Addon main file not found.'];
        }

        $config = include $configFile;

        // Check if addon already exists
        $sql = "SELECT COUNT(*) as count FROM addons WHERE directory_name = :directory_name";
        $this->db->query($sql);
        $this->db->bind(':directory_name', $addonName);
        $result = $this->db->single();

        if ($result['count'] > 0) {
            return ['success' => false, 'message' => 'Addon is already installed.'];
        }

        // Run installation script if exists
        $installFile = $addonPath . '/install.php';
        if (file_exists($installFile)) {
            include $installFile;
        }

        // Insert addon into database
        $sql = "INSERT INTO addons (name, description, version, author, directory_name, is_installed, is_active, priority) 
                VALUES (:name, :description, :version, :author, :directory_name, :is_installed, :is_active, :priority)";

        $this->db->query($sql);
        $this->db->bind(':name', $config['name'] ?? $addonName);
        $this->db->bind(':description', $config['description'] ?? '');
        $this->db->bind(':version', $config['version'] ?? '1.0.0');
        $this->db->bind(':author', $config['author'] ?? 'Unknown');
        $this->db->bind(':directory_name', $addonName);
        $this->db->bind(':is_installed', 1);
        $this->db->bind(':is_active', 0);
        $this->db->bind(':priority', $config['priority'] ?? 10);

        if ($this->db->execute()) {
            return ['success' => true, 'message' => 'Addon installed successfully.'];
        }

        return ['success' => false, 'message' => 'Failed to install addon.'];
    }

    /**
     * Uninstall an addon
     */
    public function uninstallAddon($addonName) {
        // Check if addon exists and is installed
        $sql = "SELECT * FROM addons WHERE directory_name = :directory_name AND is_installed = 1";
        $this->db->query($sql);
        $this->db->bind(':directory_name', $addonName);
        $addon = $this->db->single();

        if (!$addon) {
            return ['success' => false, 'message' => 'Addon not found or not installed.'];
        }

        // Deactivate addon first
        $this->deactivateAddon($addonName);

        // Run uninstallation script if exists
        $addonPath = $this->addonsPath . '/' . $addonName;
        $uninstallFile = $addonPath . '/uninstall.php';
        if (file_exists($uninstallFile)) {
            include $uninstallFile;
        }

        // Remove addon from database
        $sql = "DELETE FROM addons WHERE directory_name = :directory_name";
        $this->db->query($sql);
        $this->db->bind(':directory_name', $addonName);

        if ($this->db->execute()) {
            // Remove from loaded addons
            unset($this->loadedAddons[$addonName]);
            return ['success' => true, 'message' => 'Addon uninstalled successfully.'];
        }

        return ['success' => false, 'message' => 'Failed to uninstall addon.'];
    }

    /**
     * Activate an addon
     */
    public function activateAddon($addonName) {
        $sql = "UPDATE addons SET is_active = 1 WHERE directory_name = :directory_name";
        $this->db->query($sql);
        $this->db->bind(':directory_name', $addonName);

        if ($this->db->execute()) {
            // Reload addon
            $addonData = $this->getAddonData($addonName);
            if ($addonData) {
                $this->loadAddon($addonData);
            }
            return ['success' => true, 'message' => 'Addon activated successfully.'];
        }

        return ['success' => false, 'message' => 'Failed to activate addon.'];
    }

    /**
     * Deactivate an addon
     */
    public function deactivateAddon($addonName) {
        $sql = "UPDATE addons SET is_active = 0 WHERE directory_name = :directory_name";
        $this->db->query($sql);
        $this->db->bind(':directory_name', $addonName);

        if ($this->db->execute()) {
            // Remove from loaded addons
            if (isset($this->loadedAddons[$addonName])) {
                $addon = $this->loadedAddons[$addonName];
                if (method_exists($addon, 'deactivate')) {
                    $addon->deactivate();
                }
                unset($this->loadedAddons[$addonName]);
            }
            return ['success' => true, 'message' => 'Addon deactivated successfully.'];
        }

        return ['success' => false, 'message' => 'Failed to deactivate addon.'];
    }

    /**
     * Get addon data
     */
    private function getAddonData($addonName) {
        $sql = "SELECT * FROM addons WHERE directory_name = :directory_name";
        $this->db->query($sql);
        $this->db->bind(':directory_name', $addonName);
        return $this->db->single();
    }

    /**
     * Hook system
     */
    public function addHook($hookName, $callback, $priority = 10) {
        if (!isset($this->hooks[$hookName])) {
            $this->hooks[$hookName] = [];
        }
        $this->hooks[$hookName][] = ['callback' => $callback, 'priority' => $priority];
        $this->sortHooks($hookName);
    }

    public function executeHook($hookName, $params = []) {
        if (!isset($this->hooks[$hookName])) {
            return $params;
        }

        foreach ($this->hooks[$hookName] as $hook) {
            if (is_callable($hook['callback'])) {
                $params = call_user_func($hook['callback'], $params);
            }
        }

        return $params;
    }

    private function sortHooks($hookName) {
        if (isset($this->hooks[$hookName])) {
            usort($this->hooks[$hookName], function($a, $b) {
                return $a['priority'] - $b['priority'];
            });
        }
    }

    /**
     * Filter system
     */
    public function addFilter($filterName, $callback, $priority = 10) {
        if (!isset($this->filters[$filterName])) {
            $this->filters[$filterName] = [];
        }
        $this->filters[$filterName][] = ['callback' => $callback, 'priority' => $priority];
        $this->sortFilters($filterName);
    }

    public function applyFilter($filterName, $value, $params = []) {
        if (!isset($this->filters[$filterName])) {
            return $value;
        }

        foreach ($this->filters[$filterName] as $filter) {
            if (is_callable($filter['callback'])) {
                $value = call_user_func($filter['callback'], $value, $params);
            }
        }

        return $value;
    }

    private function sortFilters($filterName) {
        if (isset($this->filters[$filterName])) {
            usort($this->filters[$filterName], function($a, $b) {
                return $a['priority'] - $b['priority'];
            });
        }
    }

    /**
     * Get loaded addon
     */
    public function getLoadedAddon($addonName) {
        return $this->loadedAddons[$addonName] ?? null;
    }

    /**
     * Get all loaded addons
     */
    public function getLoadedAddons() {
        return $this->loadedAddons;
    }

    /**
     * Check if addon is loaded
     */
    public function isAddonLoaded($addonName) {
        return isset($this->loadedAddons[$addonName]);
    }
}

/**
 * Base Addon Class
 */
abstract class BaseAddon {
    protected $addonData;
    protected $addonManager;

    public function __construct($addonData) {
        $this->addonData = $addonData;
        $this->addonManager = new AddonManager();
    }

    abstract public function initialize();

    public function registerHooks() {
        // Override in child classes
    }

    public function deactivate() {
        // Override in child classes
    }

    protected function addHook($hookName, $callback, $priority = 10) {
        $this->addonManager->addHook($hookName, $callback, $priority);
    }

    protected function addFilter($filterName, $callback, $priority = 10) {
        $this->addonManager->addFilter($filterName, $callback, $priority);
    }

    protected function executeHook($hookName, $params = []) {
        return $this->addonManager->executeHook($hookName, $params);
    }

    protected function applyFilter($filterName, $value, $params = []) {
        return $this->addonManager->applyFilter($filterName, $value, $params);
    }

    public function getAddonData() {
        return $this->addonData;
    }

    public function getAddonPath() {
        return ADDONS_PATH . '/' . $this->addonData['directory_name'];
    }

    public function getAddonUrl() {
        return APP_URL . '/addons/' . $this->addonData['directory_name'];
    }
}

// Global addon manager instance
$addonManager = new AddonManager();

// Global helper functions
function add_hook($hookName, $callback, $priority = 10) {
    global $addonManager;
    $addonManager->addHook($hookName, $callback, $priority);
}

function execute_hook($hookName, $params = []) {
    global $addonManager;
    return $addonManager->executeHook($hookName, $params);
}

function add_filter($filterName, $callback, $priority = 10) {
    global $addonManager;
    $addonManager->addFilter($filterName, $callback, $priority);
}

function apply_filter($filterName, $value, $params = []) {
    global $addonManager;
    return $addonManager->applyFilter($filterName, $value, $params);
}
?>