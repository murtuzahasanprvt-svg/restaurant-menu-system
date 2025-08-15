<?php
/**
 * Database Setup Script - Restaurant Menu System
 * Creates all required database tables
 */

// Define ROOT_PATH first
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__);
}

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "    <meta charset='UTF-8'>";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "    <title>Database Setup - Restaurant Menu System</title>";
echo "    <style>";
echo "        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }";
echo "        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }";
echo "        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }";
echo "        .success { color: #155724; background-color: #d4edda; padding: 15px; border-radius: 5px; margin-bottom: 20px; }";
echo "        .error { color: #dc3545; background-color: #f8d7da; padding: 15px; border-radius: 5px; margin-bottom: 20px; }";
echo "        .info { color: #004085; background-color: #cce5ff; padding: 15px; border-radius: 5px; margin-bottom: 20px; }";
echo "        pre { background-color: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }";
echo "    </style>";
echo "</head>";
echo "<body>";
echo "<div class='container'>";

echo "<h1>🗄️ Database Setup - Restaurant Menu System</h1>";

try {
    // Load configuration
    require_once 'includes/config/Config.php';
    $config = Config::getInstance();
    echo "<div class='success'>✓ Configuration loaded successfully!</div>";
    
    // Load Database class
    require_once 'includes/database/Database.php';
    echo "<div class='success'>✓ Database class loaded successfully!</div>";
    
    // Connect to database
    $db = Database::getInstance();
    echo "<div class='success'>✓ Database connection established!</div>";
    
    // Required tables with their creation SQL
    $requiredTables = [
        'users' => "CREATE TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            role ENUM('admin', 'staff', 'customer') NOT NULL DEFAULT 'customer',
            is_active BOOLEAN NOT NULL DEFAULT 1,
            last_login TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_username (username),
            INDEX idx_email (email),
            INDEX idx_role (role)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'branches' => "CREATE TABLE branches (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            address TEXT NOT NULL,
            phone VARCHAR(20) NOT NULL,
            email VARCHAR(100) NULL,
            description TEXT NULL,
            is_active BOOLEAN NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_name (name),
            INDEX idx_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'menu_items' => "CREATE TABLE menu_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            branch_id INT NOT NULL,
            category_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            description TEXT NULL,
            price DECIMAL(10,2) NOT NULL,
            image VARCHAR(255) NULL,
            is_available BOOLEAN NOT NULL DEFAULT 1,
            is_featured BOOLEAN NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
            INDEX idx_branch_id (branch_id),
            INDEX idx_category_id (category_id),
            INDEX idx_available (is_available),
            INDEX idx_featured (is_featured)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'qr_codes' => "CREATE TABLE qr_codes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            branch_id INT NOT NULL,
            table_id INT NOT NULL,
            code VARCHAR(50) NOT NULL UNIQUE,
            is_active BOOLEAN NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
            FOREIGN KEY (table_id) REFERENCES tables(id) ON DELETE CASCADE,
            INDEX idx_branch_id (branch_id),
            INDEX idx_table_id (table_id),
            INDEX idx_code (code),
            INDEX idx_active (is_active)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'tables' => "CREATE TABLE tables (
            id INT AUTO_INCREMENT PRIMARY KEY,
            branch_id INT NOT NULL,
            table_number VARCHAR(20) NOT NULL,
            capacity INT NOT NULL DEFAULT 4,
            location VARCHAR(100) NULL,
            is_available BOOLEAN NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
            UNIQUE KEY uk_branch_table (branch_id, table_number),
            INDEX idx_branch_id (branch_id),
            INDEX idx_available (is_available)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'activity_log' => "CREATE TABLE activity_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            action VARCHAR(255) NOT NULL,
            description TEXT NULL,
            ip_address VARCHAR(45) NULL,
            user_agent TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_user_id (user_id),
            INDEX idx_action (action),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'addons' => "CREATE TABLE addons (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT NULL,
            version VARCHAR(50) NOT NULL DEFAULT '1.0.0',
            author VARCHAR(255) NULL,
            directory_name VARCHAR(100) NOT NULL,
            is_installed BOOLEAN NOT NULL DEFAULT 0,
            is_active BOOLEAN NOT NULL DEFAULT 0,
            priority INT NOT NULL DEFAULT 10,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uk_directory_name (directory_name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'themes' => "CREATE TABLE themes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT NULL,
            version VARCHAR(20) NOT NULL DEFAULT '1.0.0',
            author VARCHAR(100) NULL,
            directory_name VARCHAR(50) NOT NULL UNIQUE,
            is_active BOOLEAN NOT NULL DEFAULT 0,
            is_installed BOOLEAN NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_active (is_active),
            INDEX idx_directory_name (directory_name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'menu_categories' => "CREATE TABLE menu_categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            branch_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            description TEXT NULL,
            display_order INT NOT NULL DEFAULT 0,
            is_active BOOLEAN NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
            INDEX idx_branch_id (branch_id),
            INDEX idx_active (is_active),
            INDEX idx_display_order (display_order)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'orders' => "CREATE TABLE orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_number VARCHAR(50) NOT NULL UNIQUE,
            branch_id INT NOT NULL,
            table_id INT NULL,
            user_id INT NULL,
            status ENUM('pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
            subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            tax_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            final_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            payment_method ENUM('cash', 'card', 'online') NULL,
            payment_status ENUM('pending', 'paid', 'refunded') NOT NULL DEFAULT 'pending',
            notes TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE,
            FOREIGN KEY (table_id) REFERENCES tables(id) ON DELETE SET NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_order_number (order_number),
            INDEX idx_branch_id (branch_id),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        'order_items' => "CREATE TABLE order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            menu_item_id INT NOT NULL,
            quantity INT NOT NULL DEFAULT 1,
            unit_price DECIMAL(10,2) NOT NULL,
            total_price DECIMAL(10,2) NOT NULL,
            special_instructions TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE,
            INDEX idx_order_id (order_id),
            INDEX idx_menu_item_id (menu_item_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];
    
    echo "<div class='info'>ℹ Creating required database tables...</div>";
    
    foreach ($requiredTables as $tableName => $sql) {
        $db->query("SHOW TABLES LIKE '{$tableName}'");
        $result = $db->single();
        
        if (!$result) {
            $db->query($sql);
            if ($db->execute()) {
                echo "<div class='success'>✓ Table '{$tableName}' created successfully!</div>";
            } else {
                echo "<div class='error'>✗ Failed to create table '{$tableName}'!</div>";
            }
        } else {
            echo "<div class='info'>ℹ Table '{$tableName}' already exists!</div>";
        }
    }
    
    // Insert sample data if tables are empty
    echo "<div class='info'>ℹ Inserting sample data...</div>";
    
    // Check if branches table is empty
    $db->query("SELECT COUNT(*) as count FROM branches");
    $result = $db->single();
    if ($result['count'] == 0) {
        $db->query("INSERT INTO branches (name, address, phone, email, description) VALUES 
            ('Main Restaurant', '123 Main Street, City, State 12345', '(555) 123-4567', 'main@restaurant.com', 'Our flagship location with full service dining.'),
            ('Downtown Branch', '456 Downtown Ave, City, State 12345', '(555) 987-6543', 'downtown@restaurant.com', 'Modern dining in the heart of downtown.'),
            ('Mall Location', '789 Shopping Center, City, State 12345', '(555) 456-7890', 'mall@restaurant.com', 'Convenient location in the shopping mall.')
        ");
        $db->execute();
        echo "<div class='success'>✓ Sample branches inserted!</div>";
    }
    
    // Check if users table is empty
    $db->query("SELECT COUNT(*) as count FROM users");
    $result = $db->single();
    if ($result['count'] == 0) {
        $db->query("INSERT INTO users (username, email, password, first_name, last_name, role) VALUES 
            ('admin', 'admin@restaurant.com', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', 'System', 'Administrator', 'admin'),
            ('staff1', 'staff1@restaurant.com', '" . password_hash('staff123', PASSWORD_DEFAULT) . "', 'John', 'Staff', 'staff'),
            ('customer1', 'customer1@example.com', '" . password_hash('customer123', PASSWORD_DEFAULT) . "', 'Jane', 'Customer', 'customer')
        ");
        $db->execute();
        echo "<div class='success'>✓ Sample users inserted!</div>";
    }
    
    // Check if themes table is empty
    $db->query("SELECT COUNT(*) as count FROM themes");
    $result = $db->single();
    if ($result['count'] == 0) {
        $db->query("INSERT INTO themes (name, description, version, author, directory_name, is_active) VALUES 
            ('Default Theme', 'The default theme for Restaurant Menu System', '1.0.0', 'System', 'default', 1)
        ");
        $db->execute();
        echo "<div class='success'>✓ Sample theme inserted!</div>";
    }
    
    // Add sample tables for each branch
    $db->query("SELECT id FROM branches");
    $branches = $db->resultSet();
    foreach ($branches as $branch) {
        $branchId = $branch['id'];
        
        // Check if tables exist for this branch
        $db->query("SELECT COUNT(*) as count FROM tables WHERE branch_id = :branch_id");
        $db->bind(':branch_id', $branchId);
        $result = $db->single();
        
        if ($result['count'] == 0) {
            $db->query("INSERT INTO tables (branch_id, table_number, capacity, location) VALUES 
                (:branch_id, 'T1', 4, 'Main Dining'),
                (:branch_id, 'T2', 4, 'Main Dining'),
                (:branch_id, 'T3', 6, 'Main Dining'),
                (:branch_id, 'T4', 2, 'Window Area'),
                (:branch_id, 'T5', 8, 'Private Room')
            ");
            $db->bind(':branch_id', $branchId);
            $db->execute();
            echo "<div class='success'>✓ Sample tables added for branch {$branchId}!</div>";
        }
        
        // Add sample menu categories for each branch
        $db->query("SELECT COUNT(*) as count FROM menu_categories WHERE branch_id = :branch_id");
        $db->bind(':branch_id', $branchId);
        $result = $db->single();
        
        if ($result['count'] == 0) {
            $db->query("INSERT INTO menu_categories (branch_id, name, description, display_order) VALUES 
                (:branch_id, 'Appetizers', 'Start your meal with our delicious appetizers', 1),
                (:branch_id, 'Main Courses', 'Hearty main courses to satisfy your hunger', 2),
                (:branch_id, 'Desserts', 'Sweet treats to end your meal', 3),
                (:branch_id, 'Beverages', 'Refreshing drinks and beverages', 4)
            ");
            $db->bind(':branch_id', $branchId);
            $db->execute();
            echo "<div class='success'>✓ Sample menu categories added for branch {$branchId}!</div>";
        }
    }
    
    echo "<div class='success'>";
    echo "<h3>🎉 Database Setup Complete!</h3>";
    echo "<p>All required tables have been created and sample data has been inserted.</p>";
    echo "<p><strong>Default Login:</strong></p>";
    echo "<ul>";
    echo "<li>Admin: admin / admin123</li>";
    echo "<li>Staff: staff1 / staff123</li>";
    echo "<li>Customer: customer1 / customer123</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='margin-top: 20px; text-align: center;'>";
    echo "<a href='advanced-fix.php' style='display: inline-block; padding: 15px 30px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; font-size: 16px; margin: 5px;'>🔧 Run Advanced Fix</a>";
    echo "<a href='index.php' style='display: inline-block; padding: 15px 30px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-size: 16px; margin: 5px;'>🚀 Launch Application</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "<h3>❌ Database Setup Failed</h3>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<h4>Stack Trace:</h4>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "</div>";
echo "</body>";
echo "</html>";
?>