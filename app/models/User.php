<?php
/**
 * User Model
 */

class User extends Model {
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'username', 'email', 'password_hash', 'full_name', 'role', 
        'branch_id', 'is_active', 'last_login'
    ];
    protected $hidden = ['password_hash'];
    protected $timestamps = true;

    public function findByUsername($username) {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':username', $username);
        return $this->db->single();
    }

    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    public function findByRole($role) {
        $sql = "SELECT * FROM {$this->table} WHERE role = :role AND is_active = 1 ORDER BY full_name";
        $this->db->query($sql);
        $this->db->bind(':role', $role);
        return $this->db->resultSet();
    }

    public function findByBranch($branchId) {
        $sql = "SELECT * FROM {$this->table} WHERE branch_id = :branch_id AND is_active = 1 ORDER BY full_name";
        $this->db->query($sql);
        $this->db->bind(':branch_id', $branchId);
        return $this->db->resultSet();
    }

    public function findByBranchAndRole($branchId, $role) {
        $sql = "SELECT * FROM {$this->table} WHERE branch_id = :branch_id AND role = :role AND is_active = 1 ORDER BY full_name";
        $this->db->query($sql);
        $this->db->bind(':branch_id', $branchId);
        $this->db->bind(':role', $role);
        return $this->db->resultSet();
    }

    public function getActiveUsers() {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY full_name";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    public function searchUsers($query) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (username LIKE :query OR email LIKE :query OR full_name LIKE :query) 
                AND is_active = 1 
                ORDER BY full_name";
        $this->db->query($sql);
        $this->db->bind(':query', '%' . $query . '%');
        return $this->db->resultSet();
    }

    public function updateLastLogin($id) {
        $sql = "UPDATE {$this->table} SET last_login = NOW() WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function deactivate($id) {
        return $this->update($id, ['is_active' => false]);
    }

    public function activate($id) {
        return $this->update($id, ['is_active' => true]);
    }

    public function changePassword($id, $newPassword) {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($id, ['password_hash' => $passwordHash]);
    }

    public function getUserStats() {
        $sql = "SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_users,
                    SUM(CASE WHEN role = 'super_admin' THEN 1 ELSE 0 END) as super_admins,
                    SUM(CASE WHEN role = 'branch_manager' THEN 1 ELSE 0 END) as branch_managers,
                    SUM(CASE WHEN role = 'chef' THEN 1 ELSE 0 END) as chefs,
                    SUM(CASE WHEN role = 'waiter' THEN 1 ELSE 0 END) as waiters,
                    SUM(CASE WHEN role = 'staff' THEN 1 ELSE 0 END) as staff
                FROM {$this->table}";
        $this->db->query($sql);
        return $this->db->single();
    }

    public function getRecentUsers($limit = 10) {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT :limit";
        $this->db->query($sql);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function validateUser($data) {
        $errors = [];

        // Username validation
        if (empty($data['username'])) {
            $errors['username'] = 'Username is required.';
        } elseif (strlen($data['username']) < 3) {
            $errors['username'] = 'Username must be at least 3 characters.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $errors['username'] = 'Username can only contain letters, numbers, and underscores.';
        }

        // Email validation
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        // Full name validation
        if (empty($data['full_name'])) {
            $errors['full_name'] = 'Full name is required.';
        } elseif (strlen($data['full_name']) < 2) {
            $errors['full_name'] = 'Full name must be at least 2 characters.';
        }

        // Role validation
        if (empty($data['role'])) {
            $errors['role'] = 'Role is required.';
        } elseif (!in_array($data['role'], ['super_admin', 'branch_manager', 'chef', 'waiter', 'staff'])) {
            $errors['role'] = 'Invalid role selected.';
        }

        // Password validation (for new users)
        if (isset($data['password']) && !empty($data['password'])) {
            if (strlen($data['password']) < 6) {
                $errors['password'] = 'Password must be at least 6 characters.';
            }
        }

        return $errors;
    }

    public function isUsernameAvailable($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE username = :username";
        $params = [':username' => $username];
        
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

    public function isEmailAvailable($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = :email";
        $params = [':email' => $email];
        
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

    public function getBranchUsers($branchId) {
        $sql = "SELECT * FROM {$this->table} WHERE branch_id = :branch_id AND is_active = 1 ORDER BY full_name";
        $this->db->query($sql);
        $this->db->bind(':branch_id', $branchId);
        return $this->db->resultSet();
    }

    public function getRolePermissions($role) {
        $permissions = [
            'super_admin' => [
                'all',
                'manage_users',
                'manage_branches',
                'manage_menu',
                'manage_orders',
                'manage_themes',
                'manage_addons',
                'view_reports',
                'system_settings'
            ],
            'branch_manager' => [
                'manage_branch',
                'manage_menu',
                'manage_orders',
                'manage_staff',
                'view_reports'
            ],
            'chef' => [
                'view_orders',
                'update_order_status',
                'view_menu'
            ],
            'waiter' => [
                'create_orders',
                'view_orders',
                'update_order_status',
                'view_menu'
            ],
            'staff' => [
                'view_menu',
                'view_orders'
            ]
        ];

        return $permissions[$role] ?? [];
    }

    public function hasPermission($userId, $permission) {
        $user = $this->find($userId);
        if (!$user) {
            return false;
        }

        $rolePermissions = $this->getRolePermissions($user['role']);
        return in_array('all', $rolePermissions) || in_array($permission, $rolePermissions);
    }
}
?>