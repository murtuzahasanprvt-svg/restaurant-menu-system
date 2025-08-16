<?php
/**
 * Authentication Class
 */

class Auth {
    private $session;
    private $db;

    public function __construct() {
        $this->session = new Session();
        $this->db = Database::getInstance();
    }

    public function login($username, $password) {
        // Check if user is locked out
        if ($this->session->isLockedOut($username)) {
            return ['success' => false, 'message' => 'Account temporarily locked due to too many failed attempts.'];
        }

        // Get user from database
        $sql = "SELECT * FROM users WHERE username = :username AND is_active = 1 LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':username', $username);
        $user = $this->db->single();

        if (!$user) {
            $this->incrementLoginAttempts($username);
            return ['success' => false, 'message' => 'Invalid username or password.'];
        }

        // Verify password - check both password and password_hash columns for compatibility
        $passwordToVerify = $user['password'] ?? $user['password_hash'] ?? null;
        if (!$passwordToVerify || !password_verify($password, $passwordToVerify)) {
            $this->incrementLoginAttempts($username);
            return ['success' => false, 'message' => 'Invalid username or password.'];
        }

        // Check if password needs rehash
        if (password_needs_rehash($passwordToVerify, PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $this->updatePasswordHash($user['id'], $newHash);
        }

        // Reset login attempts
        $this->session->resetLoginAttempts($username);

        // Set session data
        $this->session->setUserData([
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'full_name' => $user['full_name'],
            'role' => $user['role'],
            'branch_id' => $user['branch_id']
        ]);

        // Update last login
        $this->updateLastLogin($user['id']);

        // Log activity
        $this->logActivity('login', 'User logged in: ' . $username);

        return ['success' => true, 'message' => 'Login successful.'];
    }

    public function logout() {
        $user = $this->getCurrentUser();
        if ($user) {
            $this->logActivity('logout', 'User logged out: ' . $user['username']);
        }
        
        $this->session->logout();
    }

    public function getCurrentUser() {
        return $this->session->getUserData();
    }

    public function isLoggedIn() {
        return $this->session->isLoggedIn();
    }

    public function hasRole($roles) {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $user = $this->getCurrentUser();
        $userRole = $user['role'];

        if (is_array($roles)) {
            return in_array($userRole, $roles);
        }

        return $userRole === $roles;
    }

    public function hasPermission($permission) {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $user = $this->getCurrentUser();
        $role = $user['role'];

        // Define role permissions
        $permissions = [
            'super_admin' => ['all'],
            'branch_manager' => ['manage_branch', 'manage_menu', 'manage_orders', 'manage_staff', 'view_reports'],
            'chef' => ['view_orders', 'update_order_status', 'view_menu'],
            'waiter' => ['create_orders', 'view_orders', 'update_order_status', 'view_menu'],
            'staff' => ['view_menu', 'view_orders']
        ];

        if (!isset($permissions[$role])) {
            return false;
        }

        return in_array('all', $permissions[$role]) || in_array($permission, $permissions[$role]);
    }

    public function canAccessBranch($branchId) {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $user = $this->getCurrentUser();
        
        // Super admin can access all branches
        if ($user['role'] === 'super_admin') {
            return true;
        }

        // Other users can only access their own branch
        return $user['branch_id'] == $branchId;
    }

    public function register($userData) {
        // Validate required fields
        $requiredFields = ['username', 'email', 'password', 'full_name', 'role'];
        foreach ($requiredFields as $field) {
            if (empty($userData[$field])) {
                return ['success' => false, 'message' => "Field {$field} is required."];
            }
        }

        // Check if username already exists
        if ($this->usernameExists($userData['username'])) {
            return ['success' => false, 'message' => 'Username already exists.'];
        }

        // Check if email already exists
        if ($this->emailExists($userData['email'])) {
            return ['success' => false, 'message' => 'Email already exists.'];
        }

        // Hash password
        $passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
        unset($userData['password']);

        // Set default values
        $userData['is_active'] = $userData['is_active'] ?? 1;

        // Insert user into database - use both password columns for compatibility
        $sql = "INSERT INTO users (username, email, password, password_hash, full_name, role, branch_id, is_active) 
                VALUES (:username, :email, :password, :password_hash, :full_name, :role, :branch_id, :is_active)";
        
        $this->db->query($sql);
        
        $this->db->bind(':username', $userData['username']);
        $this->db->bind(':email', $userData['email']);
        $this->db->bind(':password', $passwordHash);
        $this->db->bind(':password_hash', $passwordHash);
        $this->db->bind(':full_name', $userData['full_name']);
        $this->db->bind(':role', $userData['role']);
        $this->db->bind(':branch_id', $userData['branch_id'] ?? null);
        $this->db->bind(':is_active', $userData['is_active']);
        
        if ($this->db->execute()) {
            $userId = $this->db->lastInsertId();
            $this->logActivity('register', 'New user registered: ' . $userData['username']);
            return ['success' => true, 'message' => 'User registered successfully.', 'user_id' => $userId];
        }

        return ['success' => false, 'message' => 'Failed to register user.'];
    }

    public function updateProfile($userId, $userData) {
        $allowedFields = ['full_name', 'email', 'phone'];
        $updateData = array_intersect_key($userData, array_flip($allowedFields));

        if (empty($updateData)) {
            return ['success' => false, 'message' => 'No valid fields to update.'];
        }

        // Check if email is being changed and if it already exists
        if (isset($updateData['email']) && $this->emailExists($updateData['email'], $userId)) {
            return ['success' => false, 'message' => 'Email already exists.'];
        }

        $setParts = [];
        foreach ($updateData as $key => $value) {
            $setParts[] = "{$key} = :{$key}";
        }

        $sql = "UPDATE users SET " . implode(', ', $setParts) . " WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $userId);

        foreach ($updateData as $key => $value) {
            $this->db->bind(':' . $key, $value);
        }

        if ($this->db->execute()) {
            $this->logActivity('update_profile', 'Profile updated for user ID: ' . $userId);
            return ['success' => true, 'message' => 'Profile updated successfully.'];
        }

        return ['success' => false, 'message' => 'Failed to update profile.'];
    }

    public function changePassword($userId, $currentPassword, $newPassword) {
        // Get current user
        $sql = "SELECT password, password_hash FROM users WHERE id = :id LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':id', $userId);
        $user = $this->db->single();

        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        // Verify current password - check both columns for compatibility
        $passwordToVerify = $user['password'] ?? $user['password_hash'] ?? null;
        if (!$passwordToVerify || !password_verify($currentPassword, $passwordToVerify)) {
            return ['success' => false, 'message' => 'Current password is incorrect.'];
        }

        // Hash new password
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update password - update both columns for compatibility
        $sql = "UPDATE users SET password = :password, password_hash = :password_hash WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':password', $newPasswordHash);
        $this->db->bind(':password_hash', $newPasswordHash);
        $this->db->bind(':id', $userId);

        if ($this->db->execute()) {
            $this->logActivity('change_password', 'Password changed for user ID: ' . $userId);
            return ['success' => true, 'message' => 'Password changed successfully.'];
        }

        return ['success' => false, 'message' => 'Failed to change password.'];
    }

    private function incrementLoginAttempts($username) {
        $attempts = $this->session->getLoginAttempts($username);
        $attempts['count']++;
        $this->session->setLoginAttempts($username, $attempts);
    }

    private function updatePasswordHash($userId, $newHash) {
        $sql = "UPDATE users SET password = :password, password_hash = :password_hash WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':password', $newHash);
        $this->db->bind(':password_hash', $newHash);
        $this->db->bind(':id', $userId);
        $this->db->execute();
    }

    private function updateLastLogin($userId) {
        $sql = "UPDATE users SET last_login = NOW() WHERE id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $userId);
        $this->db->execute();
    }

    private function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE username = :username";
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
        
        return $result['count'] > 0;
    }

    private function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = :email";
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
        
        return $result['count'] > 0;
    }

    private function logActivity($action, $description) {
        $user = $this->getCurrentUser();
        $userId = $user ? $user['id'] : null;
        
        $logData = [
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ];
        
        $sql = "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
                VALUES (:user_id, :action, :description, :ip_address, :user_agent)";
        
        $this->db->query($sql);
        foreach ($logData as $key => $value) {
            $this->db->bind(':' . $key, $value);
        }
        $this->db->execute();
    }
}
?>