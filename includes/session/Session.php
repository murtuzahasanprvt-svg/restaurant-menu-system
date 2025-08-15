<?php
/**
 * Session Management Class
 */

class Session {
    public function __construct() {
        // Only start session if headers haven't been sent
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public function has($key) {
        return isset($_SESSION[$key]);
    }

    public function remove($key) {
        if ($this->has($key)) {
            unset($_SESSION[$key]);
        }
    }

    public function clear() {
        session_unset();
        session_destroy();
    }

    public function regenerateId() {
        session_regenerate_id(true);
    }

    public function setFlash($type, $message) {
        $_SESSION['flash'][$type] = $message;
    }

    public function getFlash($type, $default = null) {
        $message = $_SESSION['flash'][$type] ?? $default;
        $this->removeFlash($type);
        return $message;
    }

    public function hasFlash($type) {
        return isset($_SESSION['flash'][$type]);
    }

    public function removeFlash($type) {
        if ($this->hasFlash($type)) {
            unset($_SESSION['flash'][$type]);
        }
    }

    public function getFlashMessages() {
        $messages = $_SESSION['flash'] ?? [];
        $_SESSION['flash'] = [];
        return $messages;
    }

    public function setUserData($userData) {
        $_SESSION['user'] = $userData;
    }

    public function getUserData() {
        return $_SESSION['user'] ?? null;
    }

    public function isLoggedIn() {
        return $this->has('user') && !empty($this->get('user'));
    }

    public function logout() {
        $this->clear();
    }

    public function setTimeout($timeout) {
        ini_set('session.gc_maxlifetime', $timeout);
        ini_set('session.cookie_lifetime', $timeout);
    }

    public function validateSession() {
        if ($this->has('last_activity')) {
            $inactiveTime = time() - $this->get('last_activity');
            $timeout = SESSION_LIFETIME;

            if ($inactiveTime >= $timeout) {
                $this->logout();
                return false;
            }
        }

        $this->set('last_activity', time());
        return true;
    }

    public function generateCsrfToken() {
        $token = bin2hex(random_bytes(32));
        $this->set('csrf_token', $token);
        return $token;
    }

    public function getCsrfToken() {
        return $this->get('csrf_token');
    }

    public function validateCsrfToken($token) {
        return $token === $this->getCsrfToken();
    }

    public function setLoginAttempts($username, $attempts) {
        $_SESSION['login_attempts'][$username] = $attempts;
        $_SESSION['login_attempts'][$username]['last_attempt'] = time();
    }

    public function getLoginAttempts($username) {
        return $_SESSION['login_attempts'][$username] ?? ['count' => 0, 'last_attempt' => 0];
    }

    public function isLockedOut($username) {
        $attempts = $this->getLoginAttempts($username);
        
        if ($attempts['count'] >= MAX_LOGIN_ATTEMPTS) {
            $lockoutTime = time() - $attempts['last_attempt'];
            return $lockoutTime < LOCKOUT_DURATION;
        }
        
        return false;
    }

    public function resetLoginAttempts($username) {
        if (isset($_SESSION['login_attempts'][$username])) {
            unset($_SESSION['login_attempts'][$username]);
        }
    }
}
?>