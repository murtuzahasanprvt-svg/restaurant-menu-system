<?php
/**
 * Branch Model
 */

class Branch extends Model {
    protected $table = 'branches';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'address', 'phone', 'email', 'description', 'logo_url', 'is_active'
    ];
    protected $timestamps = true;

    public function getActiveBranches() {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY name";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function getBranchWithStats($id) {
        $sql = "SELECT b.*, 
                       COUNT(DISTINCT u.id) as total_users,
                       COUNT(DISTINCT t.id) as total_tables,
                       COUNT(DISTINCT mc.id) as total_categories,
                       COUNT(DISTINCT mi.id) as total_menu_items,
                       COUNT(DISTINCT o.id) as total_orders
                FROM {$this->table} b
                LEFT JOIN users u ON b.id = u.branch_id AND u.is_active = 1
                LEFT JOIN tables t ON b.id = t.branch_id AND t.is_active = 1
                LEFT JOIN menu_categories mc ON b.id = mc.branch_id AND mc.is_active = 1
                LEFT JOIN menu_items mi ON mc.id = mi.category_id AND mi.is_available = 1
                LEFT JOIN orders o ON b.id = o.branch_id
                WHERE b.id = :id
                GROUP BY b.id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function searchBranches($query) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (name LIKE :query OR address LIKE :query OR phone LIKE :query) 
                AND is_active = 1 
                ORDER BY name";
        $this->db->query($sql);
        $this->db->bind(':query', '%' . $query . '%');
        return $this->db->resultSet();
    }

    public function getBranchStats() {
        $sql = "SELECT 
                    COUNT(*) as total_branches,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_branches
                FROM {$this->table}";
        $this->db->query($sql);
        return $this->db->single();
    }

    public function deactivate($id) {
        return $this->update($id, ['is_active' => false]);
    }

    public function activate($id) {
        return $this->update($id, ['is_active' => true]);
    }

    public function updateLogo($id, $logoUrl) {
        return $this->update($id, ['logo_url' => $logoUrl]);
    }

    public function validateBranch($data) {
        $errors = [];

        // Name validation
        if (empty($data['name'])) {
            $errors['name'] = 'Branch name is required.';
        } elseif (strlen($data['name']) < 2) {
            $errors['name'] = 'Branch name must be at least 2 characters.';
        }

        // Address validation
        if (empty($data['address'])) {
            $errors['address'] = 'Address is required.';
        }

        // Phone validation
        if (empty($data['phone'])) {
            $errors['phone'] = 'Phone number is required.';
        } elseif (!preg_match('/^[\d\s\-\+\(\)]+$/', $data['phone'])) {
            $errors['phone'] = 'Please enter a valid phone number.';
        }

        // Email validation
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        return $errors;
    }

    public function getBranchUsers($branchId) {
        $sql = "SELECT * FROM users WHERE branch_id = :branch_id AND is_active = 1 ORDER BY full_name";
        $this->db->query($sql);
        $this->db->bind(':branch_id', $branchId);
        return $this->db->resultSet();
    }

    public function getBranchTables($branchId) {
        $sql = "SELECT * FROM tables WHERE branch_id = :branch_id AND is_active = 1 ORDER BY table_number";
        $this->db->query($sql);
        $this->db->bind(':branch_id', $branchId);
        return $this->db->resultSet();
    }

    public function getBranchMenuCategories($branchId) {
        $sql = "SELECT * FROM menu_categories WHERE branch_id = :branch_id AND is_active = 1 ORDER BY display_order, name";
        $this->db->query($sql);
        $this->db->bind(':branch_id', $branchId);
        return $this->db->resultSet();
    }

    public function getBranchMenuItems($branchId) {
        $sql = "SELECT mi.*, mc.name as category_name 
                FROM menu_items mi
                JOIN menu_categories mc ON mi.category_id = mc.id
                WHERE mc.branch_id = :branch_id AND mi.is_available = 1
                ORDER BY mc.display_order, mi.display_order, mi.name";
        $this->db->query($sql);
        $this->db->bind(':branch_id', $branchId);
        return $this->db->resultSet();
    }

    public function getBranchOrders($branchId, $limit = 50) {
        $sql = "SELECT * FROM orders WHERE branch_id = :branch_id ORDER BY created_at DESC LIMIT :limit";
        $this->db->query($sql);
        $this->db->bind(':branch_id', $branchId);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getBranchOrdersByStatus($branchId, $status) {
        $sql = "SELECT * FROM orders WHERE branch_id = :branch_id AND status = :status ORDER BY created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':branch_id', $branchId);
        $this->db->bind(':status', $status);
        return $this->db->resultSet();
    }

    public function getBranchRevenue($branchId, $startDate = null, $endDate = null) {
        $sql = "SELECT 
                    COUNT(*) as total_orders,
                    SUM(final_amount) as total_revenue,
                    AVG(final_amount) as average_order_value
                FROM orders 
                WHERE branch_id = :branch_id AND status IN ('delivered', 'ready')";
        
        $params = [':branch_id' => $branchId];
        
        if ($startDate) {
            $sql .= " AND created_at >= :start_date";
            $params[':start_date'] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND created_at <= :end_date";
            $params[':end_date'] = $endDate;
        }
        
        $this->db->query($sql);
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        return $this->db->single();
    }

    public function getBranchPopularItems($branchId, $limit = 10) {
        $sql = "SELECT mi.*, COUNT(oi.id) as order_count
                FROM menu_items mi
                JOIN order_items oi ON mi.id = oi.menu_item_id
                JOIN orders o ON oi.order_id = o.id
                WHERE mi.category_id IN (
                    SELECT id FROM menu_categories WHERE branch_id = :branch_id
                )
                AND o.status IN ('delivered', 'ready')
                GROUP BY mi.id
                ORDER BY order_count DESC
                LIMIT :limit";
        
        $this->db->query($sql);
        $this->db->bind(':branch_id', $branchId);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function isNameAvailable($name, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE name = :name";
        $params = [':name' => $name];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $this->db->query($sql);
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        $result = $this->db->single();
        
        return $result['count'] == 0;
    }
}
?>