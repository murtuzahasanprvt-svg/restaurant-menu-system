<?php
/**
 * Table Model
 */

class Table extends Model {
    protected $table = 'tables';
    protected $primaryKey = 'id';
    protected $fillable = [
        'branch_id', 'table_number', 'capacity', 'location', 'is_active'
    ];
    protected $timestamps = true;

    public function getBranchTables($branchId) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE branch_id = :branch_id AND is_active = 1 
                ORDER BY table_number";
        
        $this->db->query($sql);
        $this->db->bind(':branch_id', $branchId);
        return $this->db->resultSet();
    }

    public function getTableByNumber($branchId, $tableNumber) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE branch_id = :branch_id AND table_number = :table_number AND is_active = 1 
                LIMIT 1";
        
        $this->db->query($sql);
        $this->db->bind(':branch_id', $branchId);
        $this->db->bind(':table_number', $tableNumber);
        return $this->db->single();
    }

    public function getTableWithOrders($tableId) {
        $sql = "SELECT t.*, 
                       COUNT(o.id) as total_orders,
                       MAX(o.created_at) as last_order_date,
                       SUM(CASE WHEN o.status IN ('pending', 'confirmed', 'preparing') THEN 1 ELSE 0 END) as active_orders
                FROM {$this->table} t
                LEFT JOIN orders o ON t.id = o.table_id
                WHERE t.id = :table_id
                GROUP BY t.id";
        
        $this->db->query($sql);
        $this->db->bind(':table_id', $tableId);
        return $this->db->single();
    }

    public function getBranchTablesWithStatus($branchId) {
        $sql = "SELECT t.*, 
                       COUNT(o.id) as total_orders,
                       SUM(CASE WHEN o.status IN ('pending', 'confirmed', 'preparing') THEN 1 ELSE 0 END) as active_orders,
                       MAX(o.created_at) as last_order_date
                FROM {$this->table} t
                LEFT JOIN orders o ON t.id = o.table_id
                WHERE t.branch_id = :branch_id AND t.is_active = 1
                GROUP BY t.id
                ORDER BY t.table_number";
        
        $this->db->query($sql);
        $this->db->bind(':branch_id', $branchId);
        return $this->db->resultSet();
    }

    public function getAvailableTables($branchId) {
        $sql = "SELECT t.* 
                FROM {$this->table} t
                LEFT JOIN orders o ON t.id = o.table_id 
                    AND o.status IN ('pending', 'confirmed', 'preparing')
                WHERE t.branch_id = :branch_id 
                    AND t.is_active = 1
                    AND o.id IS NULL
                ORDER BY t.table_number";
        
        $this->db->query($sql);
        $this->db->bind(':branch_id', $branchId);
        return $this->db->resultSet();
    }

    public function getOccupiedTables($branchId) {
        $sql = "SELECT t.*, 
                       COUNT(o.id) as active_orders,
                       MAX(o.created_at) as last_order_date
                FROM {$this->table} t
                JOIN orders o ON t.id = o.table_id
                WHERE t.branch_id = :branch_id 
                    AND t.is_active = 1
                    AND o.status IN ('pending', 'confirmed', 'preparing')
                GROUP BY t.id
                ORDER BY t.table_number";
        
        $this->db->query($sql);
        $this->db->bind(':branch_id', $branchId);
        return $this->db->resultSet();
    }

    public function createTable($data) {
        // Validate table number uniqueness within branch
        if ($this->tableNumberExists($data['branch_id'], $data['table_number'])) {
            return ['success' => false, 'message' => 'Table number already exists in this branch.'];
        }

        return $this->create($data);
    }

    public function updateTable($id, $data) {
        // Get current table
        $current = $this->find($id);
        if (!$current) {
            return ['success' => false, 'message' => 'Table not found.'];
        }

        // Check if table number is being changed and if it conflicts
        if (isset($data['table_number']) && $data['table_number'] !== $current['table_number']) {
            if ($this->tableNumberExists($data['branch_id'], $data['table_number'], $id)) {
                return ['success' => false, 'message' => 'Table number already exists in this branch.'];
            }
        }

        return $this->update($id, $data);
    }

    public function getTableStats($branchId = null) {
        $sql = "SELECT 
                    COUNT(*) as total_tables,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_tables,
                    SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive_tables,
                    SUM(capacity) as total_capacity
                FROM {$this->table}";
        
        $params = [];
        
        if ($branchId) {
            $sql .= " WHERE branch_id = :branch_id";
            $params[':branch_id'] = $branchId;
        }
        
        $this->db->query($sql);
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        return $this->db->single();
    }

    public function searchTables($branchId, $query) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE branch_id = :branch_id 
                    AND is_active = 1
                    AND (table_number LIKE :query OR location LIKE :query)
                ORDER BY table_number";
        
        $this->db->query($sql);
        $this->db->bind(':branch_id', $branchId);
        $this->db->bind(':query', '%' . $query . '%');
        return $this->db->resultSet();
    }

    public function getTablesByCapacity($branchId, $minCapacity = null, $maxCapacity = null) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE branch_id = :branch_id AND is_active = 1";
        
        $params = [':branch_id' => $branchId];
        
        if ($minCapacity !== null) {
            $sql .= " AND capacity >= :min_capacity";
            $params[':min_capacity'] = $minCapacity;
        }
        
        if ($maxCapacity !== null) {
            $sql .= " AND capacity <= :max_capacity";
            $params[':max_capacity'] = $maxCapacity;
        }
        
        $sql .= " ORDER BY capacity, table_number";
        
        $this->db->query($sql);
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        return $this->db->resultSet();
    }

    public function validateTable($data) {
        $errors = [];

        // Table number validation
        if (empty($data['table_number'])) {
            $errors['table_number'] = 'Table number is required.';
        } elseif (!is_numeric($data['table_number']) || $data['table_number'] <= 0) {
            $errors['table_number'] = 'Table number must be a positive number.';
        }

        // Capacity validation
        if (empty($data['capacity'])) {
            $errors['capacity'] = 'Capacity is required.';
        } elseif (!is_numeric($data['capacity']) || $data['capacity'] <= 0) {
            $errors['capacity'] = 'Capacity must be a positive number.';
        } elseif ($data['capacity'] > 50) {
            $errors['capacity'] = 'Capacity cannot exceed 50 people.';
        }

        // Location validation
        if (!empty($data['location']) && strlen($data['location']) > 100) {
            $errors['location'] = 'Location cannot exceed 100 characters.';
        }

        return $errors;
    }

    private function tableNumberExists($branchId, $tableNumber, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE branch_id = :branch_id AND table_number = :table_number";
        
        $params = [
            ':branch_id' => $branchId,
            ':table_number' => $tableNumber
        ];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $this->db->query($sql);
        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        $result = $this->db->single();
        
        return $result['count'] > 0;
    }

    public function getTableRevenue($tableId, $startDate = null, $endDate = null) {
        $sql = "SELECT 
                    COUNT(*) as total_orders,
                    SUM(final_amount) as total_revenue,
                    AVG(final_amount) as average_order_value
                FROM orders 
                WHERE table_id = :table_id AND status IN ('delivered', 'ready')";
        
        $params = [':table_id' => $tableId];
        
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

    public function getMostPopularTables($branchId, $limit = 10) {
        $sql = "SELECT t.*, COUNT(o.id) as order_count
                FROM {$this->table} t
                JOIN orders o ON t.id = o.table_id
                WHERE t.branch_id = :branch_id 
                    AND t.is_active = 1
                    AND o.status IN ('delivered', 'ready')
                GROUP BY t.id
                ORDER BY order_count DESC
                LIMIT :limit";
        
        $this->db->query($sql);
        $this->db->bind(':branch_id', $branchId);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}
?>