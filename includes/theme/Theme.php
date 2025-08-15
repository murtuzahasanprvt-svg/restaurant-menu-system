<?php
/**
 * Theme Management Class
 */

class Theme {
    private $currentTheme;
    private $themesPath;
    private $db;

    public function __construct() {
        $this->themesPath = THEMES_PATH;
        $this->db = Database::getInstance();
        $this->currentTheme = $this->getCurrentTheme();
    }

    public function getCurrentTheme() {
        // Get current theme from database
        $sql = "SELECT directory_name FROM themes WHERE is_active = 1 LIMIT 1";
        $this->db->query($sql);
        $result = $this->db->single();
        
        if ($result) {
            return $result['directory_name'];
        }
        
        // Fallback to default theme
        return 'default';
    }

    public function setCurrentTheme($themeName) {
        // Deactivate all themes
        $sql = "UPDATE themes SET is_active = 0";
        $this->db->query($sql);
        $this->db->execute();
        
        // Activate selected theme
        $sql = "UPDATE themes SET is_active = 1 WHERE directory_name = :theme_name";
        $this->db->query($sql);
        $this->db->bind(':theme_name', $themeName);
        
        if ($this->db->execute()) {
            $this->currentTheme = $themeName;
            return true;
        }
        
        return false;
    }

    public function getThemePath() {
        return $this->themesPath . '/' . $this->currentTheme;
    }

    public function getThemeUrl() {
        return APP_URL . '/themes/' . $this->currentTheme;
    }

    public function getViewPath($view) {
        $viewPath = $this->getThemePath() . '/views/' . $view . '.php';
        
        // Fallback to default theme if view doesn't exist
        if (!file_exists($viewPath) && $this->currentTheme !== 'default') {
            $viewPath = $this->themesPath . '/default/views/' . $view . '.php';
        }
        
        return $viewPath;
    }

    public function getLayoutPath($layout) {
        $layoutPath = $this->getThemePath() . '/layouts/' . $layout . '.php';
        
        // Fallback to default theme if layout doesn't exist
        if (!file_exists($layoutPath) && $this->currentTheme !== 'default') {
            $layoutPath = $this->themesPath . '/default/layouts/' . $layout . '.php';
        }
        
        return $layoutPath;
    }

    public function getPartialPath($partial) {
        $partialPath = $this->getThemePath() . '/partials/' . $partial . '.php';
        
        // Fallback to default theme if partial doesn't exist
        if (!file_exists($partialPath) && $this->currentTheme !== 'default') {
            $partialPath = $this->themesPath . '/default/partials/' . $partial . '.php';
        }
        
        return $partialPath;
    }

    public function getAssetUrl($asset) {
        return $this->getThemeUrl() . '/assets/' . $asset;
    }

    public function getAvailableThemes() {
        $themes = [];
        
        // Get themes from database
        $sql = "SELECT * FROM themes ORDER BY name";
        $this->db->query($sql);
        $dbThemes = $this->db->resultSet();
        
        // Get themes from filesystem
        $themeDirs = glob($this->themesPath . '/*', GLOB_ONLYDIR);
        
        foreach ($themeDirs as $themeDir) {
            $themeName = basename($themeDir);
            $themeConfig = $themeDir . '/config.php';
            
            $themeData = [
                'directory_name' => $themeName,
                'name' => $themeName,
                'description' => '',
                'version' => '1.0.0',
                'author' => 'Unknown',
                'is_active' => false,
                'is_installed' => false
            ];
            
            // Load theme config if exists
            if (file_exists($themeConfig)) {
                $config = include $themeConfig;
                $themeData = array_merge($themeData, $config);
            }
            
            // Check if theme is in database
            foreach ($dbThemes as $dbTheme) {
                if ($dbTheme['directory_name'] === $themeName) {
                    $themeData['is_installed'] = true;
                    $themeData['is_active'] = $dbTheme['is_active'];
                    $themeData['id'] = $dbTheme['id'];
                    break;
                }
            }
            
            $themes[] = $themeData;
        }
        
        return $themes;
    }

    public function installTheme($themeName) {
        $themePath = $this->themesPath . '/' . $themeName;
        
        if (!is_dir($themePath)) {
            return ['success' => false, 'message' => 'Theme directory not found.'];
        }
        
        $configFile = $themePath . '/config.php';
        if (!file_exists($configFile)) {
            return ['success' => false, 'message' => 'Theme configuration file not found.'];
        }
        
        $config = include $configFile;
        
        // Check if theme already exists
        $sql = "SELECT COUNT(*) as count FROM themes WHERE directory_name = :directory_name";
        $this->db->query($sql);
        $this->db->bind(':directory_name', $themeName);
        $result = $this->db->single();
        
        if ($result['count'] > 0) {
            return ['success' => false, 'message' => 'Theme is already installed.'];
        }
        
        // Insert theme into database
        $sql = "INSERT INTO themes (name, description, version, author, directory_name, is_active) 
                VALUES (:name, :description, :version, :author, :directory_name, :is_active)";
        
        $this->db->query($sql);
        $this->db->bind(':name', $config['name'] ?? $themeName);
        $this->db->bind(':description', $config['description'] ?? '');
        $this->db->bind(':version', $config['version'] ?? '1.0.0');
        $this->db->bind(':author', $config['author'] ?? 'Unknown');
        $this->db->bind(':directory_name', $themeName);
        $this->db->bind(':is_active', 0);
        
        if ($this->db->execute()) {
            return ['success' => true, 'message' => 'Theme installed successfully.'];
        }
        
        return ['success' => false, 'message' => 'Failed to install theme.'];
    }

    public function uninstallTheme($themeName) {
        // Don't allow uninstalling default theme
        if ($themeName === 'default') {
            return ['success' => false, 'message' => 'Cannot uninstall default theme.'];
        }
        
        // Don't allow uninstalling active theme
        if ($themeName === $this->currentTheme) {
            return ['success' => false, 'message' => 'Cannot uninstall active theme.'];
        }
        
        // Remove theme from database
        $sql = "DELETE FROM themes WHERE directory_name = :directory_name";
        $this->db->query($sql);
        $this->db->bind(':directory_name', $themeName);
        
        if ($this->db->execute()) {
            return ['success' => true, 'message' => 'Theme uninstalled successfully.'];
        }
        
        return ['success' => false, 'message' => 'Failed to uninstall theme.'];
    }

    public function renderPartial($partial, $data = []) {
        $partialPath = $this->getPartialPath($partial);
        
        if (file_exists($partialPath)) {
            extract($data);
            include $partialPath;
        }
    }

    public function getThemeConfig() {
        $configFile = $this->getThemePath() . '/config.php';
        
        if (file_exists($configFile)) {
            return include $configFile;
        }
        
        return [];
    }

    public function hasAsset($asset) {
        $assetPath = $this->getThemePath() . '/assets/' . $asset;
        return file_exists($assetPath);
    }
}
?>