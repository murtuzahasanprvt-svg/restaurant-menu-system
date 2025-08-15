<?php
/**
 * Activity Log Model
 */

class ActivityLog extends Model {
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id', 'action', 'description', 'ip_address', 'user_agent'
    ];
    protected $timestamps = true;

    public function getUserActivities($userId, $limit = 50) {
        $sql = "SELECT al.*, u.username, u.full_name 
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.user_id = :user_id
                ORDER BY al.created_at DESC
                LIMIT :limit";
        
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getRecentActivities($limit = 20) {
        $sql = "SELECT al.*, u.username, u.full_name 
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                ORDER BY al.created_at DESC
                LIMIT :limit";
        
        $this->db->query($sql);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getActivitiesByAction($action, $limit = 50) {
        $sql = "SELECT al.*, u.username, u.full_name 
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.action = :action
                ORDER BY al.created_at DESC
                LIMIT :limit";
        
        $this->db->query($sql);
        $this->db->bind(':action', $action);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getActivitiesByDateRange($startDate, $endDate, $limit = 100) {
        $sql = "SELECT al.*, u.username, u.full_name 
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.created_at BETWEEN :start_date AND :end_date
                ORDER BY al.created_at DESC
                LIMIT :limit";
        
        $this->db->query($sql);
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getActivityStats() {
        $sql = "SELECT 
                    COUNT(*) as total_activities,
                    COUNT(DISTINCT user_id) as unique_users,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 END) as last_24h,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as last_7d,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as last_30d
                FROM {$this->table}";
        
        $this->db->query($sql);
        return $this->db->single();
    }

    public function getPopularActions($limit = 10) {
        $sql = "SELECT action, COUNT(*) as count
                FROM {$this->table}
                GROUP BY action
                ORDER BY count DESC
                LIMIT :limit";
        
        $this->db->query($sql);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getUserActivityStats($userId) {
        $sql = "SELECT 
                    COUNT(*) as total_activities,
                    MAX(created_at) as last_activity,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 END) as last_24h,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as last_7d,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as last_30d
                FROM {$this->table}
                WHERE user_id = :user_id";
        
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        return $this->db->single();
    }

    public function searchActivities($query, $limit = 50) {
        $sql = "SELECT al.*, u.username, u.full_name 
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.action LIKE :query 
                   OR al.description LIKE :query 
                   OR u.username LIKE :query 
                   OR u.full_name LIKE :query
                ORDER BY al.created_at DESC
                LIMIT :limit";
        
        $this->db->query($sql);
        $this->db->bind(':query', '%' . $query . '%');
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function logActivity($userId, $action, $description = null) {
        $data = [
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ];
        
        return $this->create($data);
    }

    public function cleanupOldActivities($days = 90) {
        $sql = "DELETE FROM {$this->table} WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)";
        $this->db->query($sql);
        $this->db->bind(':days', $days, PDO::PARAM_INT);
        return $this->db->execute();
    }

    public function getActivitiesByUserAgent($userAgent, $limit = 50) {
        $sql = "SELECT al.*, u.username, u.full_name 
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.user_agent LIKE :user_agent
                ORDER BY al.created_at DESC
                LIMIT :limit";
        
        $this->db->query($sql);
        $this->db->bind(':user_agent', '%' . $userAgent . '%');
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getActivitiesByIpAddress($ipAddress, $limit = 50) {
        $sql = "SELECT al.*, u.username, u.full_name 
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.ip_address = :ip_address
                ORDER BY al.created_at DESC
                LIMIT :limit";
        
        $this->db->query($sql);
        $this->db->bind(':ip_address', $ipAddress);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getFailedLoginAttempts($hours = 24) {
        $sql = "SELECT al.*, u.username, u.full_name 
                FROM {$this->table} al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.action = 'failed_login'
                AND al.created_at >= DATE_SUB(NOW(), INTERVAL :hours HOUR)
                ORDER BY al.created_at DESC";
        
        $this->db->query($sql);
        $this->db->bind(':hours', $hours, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}
?>