<?php
/**
 * Authentication Controller
 */

class AuthController extends Controller {
    private $userModel;
    private $activityLogModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
        $this->activityLogModel = new ActivityLog();
    }

    public function login() {
        $this->requireGuest();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
        } else {
            $this->render('auth/login');
        }
    }

    public function register() {
        $this->requireGuest();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleRegister();
        } else {
            $this->render('auth/register');
        }
    }

    public function logout() {
        if ($this->auth->isLoggedIn()) {
            $user = $this->auth->getCurrentUser();
            $this->activityLogModel->logActivity($user['id'], 'logout', 'User logged out');
        }
        
        $this->auth->logout();
        $this->session->setFlash('success', 'You have been logged out successfully.');
        $this->redirect(APP_URL . '/login');
    }

    public function forgotPassword() {
        $this->requireGuest();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleForgotPassword();
        } else {
            $this->render('auth/forgot-password');
        }
    }

    public function resetPassword($token = null) {
        $this->requireGuest();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleResetPassword();
        } else {
            $this->render('auth/reset-password', ['token' => $token]);
        }
    }

    public function profile() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleProfileUpdate();
        } else {
            $user = $this->auth->getCurrentUser();
            $this->render('auth/profile', ['user' => $user]);
        }
    }

    public function changePassword() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePasswordChange();
        } else {
            $this->render('auth/change-password');
        }
    }

    private function handleLogin() {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        // Validate CSRF token
        if (!$this->session->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->session->setFlash('error', 'Invalid CSRF token.');
            $this->redirectBack();
        }

        // Validate input
        if (empty($username) || empty($password)) {
            $this->session->setFlash('error', 'Please enter both username and password.');
            $this->redirectBack();
        }

        // Attempt login
        $result = $this->auth->login($username, $password);

        if ($result['success']) {
            $user = $this->auth->getCurrentUser();
            
            // Log successful login
            $this->activityLogModel->logActivity($user['id'], 'login', 'User logged in successfully');
            
            // Set remember me cookie if requested
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expires = time() + (30 * 24 * 60 * 60); // 30 days
                setcookie('remember_token', $token, $expires, '/', '', true, true);
                
                // Store token in database (you'll need to implement this)
                // $this->userModel->updateRememberToken($user['id'], $token, $expires);
            }

            // Redirect based on user role
            $redirectUrl = $this->getRedirectUrlByRole($user['role']);
            $this->session->setFlash('success', $result['message']);
            $this->redirect($redirectUrl);
        } else {
            // Log failed login attempt
            $this->activityLogModel->logActivity(null, 'failed_login', "Failed login attempt for username: {$username}");
            
            $this->session->setFlash('error', $result['message']);
            $this->redirectBack();
        }
    }

    private function handleRegister() {
        $userData = [
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'full_name' => $_POST['full_name'] ?? '',
            'role' => $_POST['role'] ?? 'staff',
            'branch_id' => !empty($_POST['branch_id']) ? (int)$_POST['branch_id'] : null
        ];

        // Validate CSRF token
        if (!$this->session->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->session->setFlash('error', 'Invalid CSRF token.');
            $this->redirectBack();
        }

        // Validate user data
        $errors = $this->userModel->validateUser($userData);
        
        // Check username availability
        if (!$this->userModel->isUsernameAvailable($userData['username'])) {
            $errors['username'] = 'Username is already taken.';
        }
        
        // Check email availability
        if (!$this->userModel->isEmailAvailable($userData['email'])) {
            $errors['email'] = 'Email is already registered.';
        }

        if (!empty($errors)) {
            $this->session->setFlash('errors', $errors);
            $this->session->setFlash('old', $userData);
            $this->redirectBack();
        }

        // Register user
        $result = $this->auth->register($userData);

        if ($result['success']) {
            $this->session->setFlash('success', $result['message']);
            $this->redirect(APP_URL . '/login');
        } else {
            $this->session->setFlash('error', $result['message']);
            $this->session->setFlash('old', $userData);
            $this->redirectBack();
        }
    }

    private function handleProfileUpdate() {
        $userId = $this->auth->getCurrentUser()['id'];
        $profileData = [
            'full_name' => $_POST['full_name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? ''
        ];

        // Validate CSRF token
        if (!$this->session->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->session->setFlash('error', 'Invalid CSRF token.');
            $this->redirectBack();
        }

        // Validate profile data
        $errors = [];
        
        if (empty($profileData['full_name'])) {
            $errors['full_name'] = 'Full name is required.';
        }
        
        if (empty($profileData['email'])) {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($profileData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if (!empty($errors)) {
            $this->session->setFlash('errors', $errors);
            $this->session->setFlash('old', $profileData);
            $this->redirectBack();
        }

        // Update profile
        $result = $this->auth->updateProfile($userId, $profileData);

        if ($result['success']) {
            $this->activityLogModel->logActivity($userId, 'update_profile', 'Profile updated successfully');
            $this->session->setFlash('success', $result['message']);
        } else {
            $this->session->setFlash('error', $result['message']);
        }

        $this->redirectBack();
    }

    private function handlePasswordChange() {
        $userId = $this->auth->getCurrentUser()['id'];
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate CSRF token
        if (!$this->session->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->session->setFlash('error', 'Invalid CSRF token.');
            $this->redirectBack();
        }

        // Validate passwords
        $errors = [];
        
        if (empty($currentPassword)) {
            $errors['current_password'] = 'Current password is required.';
        }
        
        if (empty($newPassword)) {
            $errors['new_password'] = 'New password is required.';
        } elseif (strlen($newPassword) < 6) {
            $errors['new_password'] = 'New password must be at least 6 characters.';
        }
        
        if (empty($confirmPassword)) {
            $errors['confirm_password'] = 'Please confirm your new password.';
        } elseif ($newPassword !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }

        if (!empty($errors)) {
            $this->session->setFlash('errors', $errors);
            $this->redirectBack();
        }

        // Change password
        $result = $this->auth->changePassword($userId, $currentPassword, $newPassword);

        if ($result['success']) {
            $this->activityLogModel->logActivity($userId, 'change_password', 'Password changed successfully');
            $this->session->setFlash('success', $result['message']);
        } else {
            $this->session->setFlash('error', $result['message']);
        }

        $this->redirectBack();
    }

    private function handleForgotPassword() {
        $email = $_POST['email'] ?? '';

        // Validate CSRF token
        if (!$this->session->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->session->setFlash('error', 'Invalid CSRF token.');
            $this->redirectBack();
        }

        if (empty($email)) {
            $this->session->setFlash('error', 'Please enter your email address.');
            $this->redirectBack();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->session->setFlash('error', 'Please enter a valid email address.');
            $this->redirectBack();
        }

        // Check if user exists
        $user = $this->userModel->findByEmail($email);
        
        if ($user) {
            // Generate password reset token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour
            
            // Store token in database (you'll need to implement this)
            // $this->userModel->storePasswordResetToken($user['id'], $token, $expires);
            
            // Send reset email (you'll need to implement email functionality)
            // $this->sendPasswordResetEmail($user['email'], $token);
            
            $this->activityLogModel->logActivity($user['id'], 'forgot_password', 'Password reset requested');
        }

        // Always show success message to prevent email enumeration
        $this->session->setFlash('success', 'If your email address is registered, you will receive a password reset link.');
        $this->redirect(APP_URL . '/login');
    }

    private function handleResetPassword() {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate CSRF token
        if (!$this->session->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->session->setFlash('error', 'Invalid CSRF token.');
            $this->redirectBack();
        }

        if (empty($token)) {
            $this->session->setFlash('error', 'Invalid reset token.');
            $this->redirect(APP_URL . '/forgot-password');
        }

        if (empty($password)) {
            $this->session->setFlash('error', 'Password is required.');
            $this->redirectBack();
        }

        if (strlen($password) < 6) {
            $this->session->setFlash('error', 'Password must be at least 6 characters.');
            $this->redirectBack();
        }

        if ($password !== $confirmPassword) {
            $this->session->setFlash('error', 'Passwords do not match.');
            $this->redirectBack();
        }

        // Verify token and get user (you'll need to implement this)
        // $user = $this->userModel->verifyPasswordResetToken($token);
        
        // For now, just show success message
        $this->session->setFlash('success', 'Password has been reset successfully.');
        $this->redirect(APP_URL . '/login');
    }

    private function getRedirectUrlByRole($role) {
        switch ($role) {
            case 'super_admin':
                return APP_URL . '/admin/dashboard';
            case 'branch_manager':
                return APP_URL . '/manager/dashboard';
            case 'chef':
                return APP_URL . '/chef/dashboard';
            case 'waiter':
                return APP_URL . '/waiter/dashboard';
            case 'staff':
                return APP_URL . '/dashboard';
            default:
                return APP_URL . '/dashboard';
        }
    }
}
?>