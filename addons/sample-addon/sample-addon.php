<?php
/**
 * Sample Addon
 */

class SampleAddon extends BaseAddon {
    
    public function initialize() {
        // Initialize addon functionality
        $this->createSampleTables();
        $this->registerSampleRoutes();
        $this->addSampleAssets();
        
        // Register hooks
        $this->addHook('before_menu_display', [$this, 'beforeMenuDisplay']);
        $this->addHook('after_order_create', [$this, 'afterOrderCreate']);
        
        // Register filters
        $this->addFilter('menu_item_price', [$this, 'filterMenuItemPrice']);
        $this->addFilter('order_total', [$this, 'filterOrderTotal']);
        
        // Add admin menu item
        $this->addAdminMenuItem();
    }
    
    public function registerHooks() {
        // Additional hook registration
        $this->addHook('user_registered', [$this, 'onUserRegistered']);
        $this->addHook('branch_created', [$this, 'onBranchCreated']);
    }

    // Hook implementations
    public function beforeMenuDisplay($params) {
        // Add sample data to menu display
        $params['sample_data'] = 'Sample addon data';
        return $params;
    }
    
    public function afterOrderCreate($params) {
        // Log order creation
        $this->logOrderCreation($params['order_id']);
        return $params;
    }
    
    public function onUserRegistered($params) {
        // Send welcome email or perform other actions
        $this->sendWelcomeEmail($params['user_id']);
        return $params;
    }
    
    public function onBranchCreated($params) {
        // Initialize branch-specific sample data
        $this->initializeBranchData($params['branch_id']);
        return $params;
    }
    
    // Filter implementations
    public function filterMenuItemPrice($price, $params) {
        // Apply sample discount or markup
        $discount = 0.1; // 10% discount
        return $price * (1 - $discount);
    }
    
    public function filterOrderTotal($total, $params) {
        // Apply sample tax or fee
        $tax = 0.08; // 8% tax
        return $total * (1 + $tax);
    }
    
    // Helper methods
    private function createSampleTables() {
        $db = Database::getInstance();
        
        // Create sample data table
        $sql = "CREATE TABLE IF NOT EXISTS sample_addon_data (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            data_type VARCHAR(50) NOT NULL,
            data_value TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )";
        
        $db->query($sql);
        $db->execute();
    }
    
    private function registerSampleRoutes() {
        // Add sample routes to the system
        $router = Router::getInstance();
        
        // Sample page route
        $router->addRoute('GET', '/sample', function() {
            $this->showSamplePage();
        });
        
        // Sample API route
        $router->addRoute('POST', '/api/sample', function() {
            $this->handleSampleApi();
        });
    }
    
    private function addSampleAssets() {
        // Add sample CSS and JS
        add_hook('before_head_close', function($params) {
            echo '<link rel="stylesheet" href="' . $this->getAddonUrl() . '/assets/css/sample.css">';
            return $params;
        });
        
        add_hook('before_body_close', function($params) {
            echo '<script src="' . $this->getAddonUrl() . '/assets/js/sample.js"></script>';
            return $params;
        });
    }
    
    private function addAdminMenuItem() {
        // Add admin menu item for sample addon
        add_hook('admin_menu_items', function($params) {
            $params[] = [
                'title' => 'Sample Addon',
                'url' => '/admin/sample-addon',
                'icon' => 'fas fa-puzzle-piece',
                'permission' => 'manage_sample_data'
            ];
            return $params;
        });
    }
    
    private function logOrderCreation($orderId) {
        $db = Database::getInstance();
        
        $sql = "INSERT INTO sample_addon_data (user_id, data_type, data_value) 
                VALUES (:user_id, 'order_log', :data_value)";
        
        $db->query($sql);
        $db->bind(':user_id', $_SESSION['user_id'] ?? 0);
        $db->bind(':data_value', json_encode([
            'order_id' => $orderId,
            'action' => 'created',
            'timestamp' => date('Y-m-d H:i:s')
        ]));
        $db->execute();
    }
    
    private function sendWelcomeEmail($userId) {
        // Sample email sending logic
        $userModel = new User();
        $user = $userModel->find($userId);
        
        if ($user && isset($user['email'])) {
            // In a real implementation, you would send an email
            error_log("Sample addon: Welcome email would be sent to {$user['email']}");
        }
    }
    
    private function initializeBranchData($branchId) {
        $db = Database::getInstance();
        
        // Create sample settings for the branch
        $sql = "INSERT INTO sample_addon_data (user_id, data_type, data_value) 
                VALUES (:user_id, 'branch_settings', :data_value)";
        
        $db->query($sql);
        $db->bind(':user_id', 0); // System user
        $db->bind(':data_value', json_encode([
            'branch_id' => $branchId,
            'sample_setting_1' => 'default_value',
            'sample_setting_2' => true,
            'created_at' => date('Y-m-d H:i:s')
        ]));
        $db->execute();
    }
    
    public function showSamplePage() {
        // Display sample page
        $theme = new Theme();
        $data = [
            'title' => 'Sample Addon Page',
            'content' => $this->getSamplePageContent()
        ];
        
        $viewPath = $theme->getViewPath('sample-page');
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo '<h1>Sample Addon</h1><p>This is a sample addon page.</p>';
        }
    }
    
    public function handleSampleApi() {
        // Handle sample API requests
        $input = json_decode(file_get_contents('php://input'), true);
        
        $response = [
            'success' => true,
            'message' => 'Sample addon API response',
            'data' => $input ?? []
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    private function getSamplePageContent() {
        return '
            <div class="container">
                <h1>Sample Addon</h1>
                <p>This is a sample addon that demonstrates the addon system functionality.</p>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Features</h5>
                            </div>
                            <div class="card-body">
                                <ul>
                                    <li>Hook system integration</li>
                                    <li>Filter system integration</li>
                                    <li>Custom routes</li>
                                    <li>Database tables</li>
                                    <li>Admin panel integration</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Sample Data</h5>
                            </div>
                            <div class="card-body">
                                <p>This addon creates sample data and demonstrates various features.</p>
                                <button class="btn btn-primary" onclick="sampleAddonTest()">Test Addon</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ';
    }
    
    public function deactivate() {
        // Clean up when addon is deactivated
        // Note: We don't drop tables on deactivation, only on uninstall
    }
}
?>