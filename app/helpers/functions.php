<?php
/**
 * Helper Functions
 */

if (!function_exists('asset')) {
    function asset($path) {
        return APP_URL . '/public/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url($path = '') {
        return APP_URL . '/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    function redirect($url) {
        if (!headers_sent()) {
            header("Location: {$url}");
            exit;
        } else {
            echo "<script>window.location.href = '" . htmlspecialchars($url) . "';</script>";
            exit;
        }
    }
}

if (!function_exists('back')) {
    function back() {
        $referer = $_SERVER['HTTP_REFERER'] ?? url();
        if ($referer) {
            redirect($referer);
        } else {
            redirect(url());
        }
    }
}

if (!function_exists('old')) {
    function old($field, $default = '') {
        return $_SESSION['old'][$field] ?? $default;
    }
}

if (!function_exists('error')) {
    function error($field) {
        return $_SESSION['errors'][$field] ?? '';
    }
}

if (!function_exists('has_error')) {
    function has_error($field) {
        return isset($_SESSION['errors'][$field]);
    }
}

if (!function_exists('flash')) {
    function flash($type) {
        return $_SESSION['flash'][$type] ?? '';
    }
}

if (!function_exists('has_flash')) {
    function has_flash($type) {
        return isset($_SESSION['flash'][$type]);
    }
}

if (!function_exists('format_price')) {
    function format_price($price, $currency = 'USD') {
        return number_format($price, 2) . ' ' . $currency;
    }
}

if (!function_exists('format_date')) {
    function format_date($date, $format = 'Y-m-d H:i:s') {
        return date($format, strtotime($date));
    }
}

if (!function_exists('format_time_ago')) {
    function format_time_ago($date) {
        $timestamp = strtotime($date);
        $now = time();
        $diff = $now - $timestamp;

        if ($diff < 60) {
            return 'just now';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . ' minutes ago';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . ' hours ago';
        } elseif ($diff < 604800) {
            return floor($diff / 86400) . ' days ago';
        } else {
            return format_date($date, 'M j, Y');
        }
    }
}

if (!function_exists('sanitize')) {
    function sanitize($input) {
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = sanitize($value);
            }
        } else {
            $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        }
        return $input;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token() {
        $session = new Session();
        $token = $session->getCsrfToken();
        if (!$token) {
            $token = $session->generateCsrfToken();
        }
        return $token;
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field() {
        return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('method_field')) {
    function method_field($method) {
        return '<input type="hidden" name="_method" value="' . strtoupper($method) . '">';
    }
}

if (!function_exists('is_active_route')) {
    function is_active_route($route) {
        $currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return $currentUri === $route;
    }
}

if (!function_exists('active_class')) {
    function active_class($route) {
        return is_active_route($route) ? 'active' : '';
    }
}

if (!function_exists('str_limit')) {
    function str_limit($string, $limit = 100, $end = '...') {
        if (!is_string($string)) {
            return '';
        }
        if (strlen($string) <= $limit) {
            return $string;
        }
        return substr($string, 0, $limit) . $end;
    }
}

if (!function_exists('str_slug')) {
    function str_slug($string) {
        $string = strtolower($string);
        $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
        $string = preg_replace('/[\s-]+/', ' ', $string);
        $string = preg_replace('/\s/', '-', $string);
        return $string;
    }
}

if (!function_exists('generate_order_number')) {
    function generate_order_number() {
        $prefix = 'ORD';
        $timestamp = date('YmdHis');
        $random = mt_rand(1000, 9999);
        return $prefix . $timestamp . $random;
    }
}

if (!function_exists('is_mobile')) {
    function is_mobile() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $mobileAgents = [
            'Android', 'iPhone', 'iPad', 'iPod', 'BlackBerry', 
            'Windows Phone', 'Palm', 'Symbian', 'Mobile'
        ];
        
        foreach ($mobileAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                return true;
            }
        }
        
        return false;
    }
}

if (!function_exists('get_client_ip')) {
    function get_client_ip() {
        $ipAddress = '';
        
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipAddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }
        
        return $ipAddress;
    }
}

if (!function_exists('get_gravatar')) {
    function get_gravatar($email, $size = 80) {
        $hash = md5(strtolower(trim($email)));
        return "https://www.gravatar.com/avatar/{$hash}?s={$size}&d=mp&r=g";
    }
}

if (!function_exists('is_valid_email')) {
    function is_valid_email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('is_valid_phone')) {
    function is_valid_phone($phone) {
        return preg_match('/^[\d\s\-\+\(\)]+$/', $phone);
    }
}

if (!function_exists('format_phone')) {
    function format_phone($phone) {
        $phone = preg_replace('/[^\d]/', '', $phone);
        
        if (strlen($phone) === 10) {
            return '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6);
        } elseif (strlen($phone) === 11) {
            return '(' . substr($phone, 1, 3) . ') ' . substr($phone, 4, 3) . '-' . substr($phone, 7);
        }
        
        return $phone;
    }
}

if (!function_exists('get_file_extension')) {
    function get_file_extension($filename) {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }
}

if (!function_exists('is_allowed_file_type')) {
    function is_allowed_file_type($filename, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp']) {
        $extension = strtolower(get_file_extension($filename));
        return in_array($extension, $allowedTypes);
    }
}

if (!function_exists('format_file_size')) {
    function format_file_size($bytes) {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}

if (!function_exists('debug')) {
    function debug($data) {
        echo '<pre>';
        if (is_array($data) || is_object($data)) {
            print_r($data);
        } else {
            echo htmlspecialchars(var_export($data, true));
        }
        echo '</pre>';
    }
}

if (!function_exists('dd')) {
    function dd($data) {
        debug($data);
        die();
    }
}
?>