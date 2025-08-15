<?php
/**
 * Base Controller Class
 */

class Controller {
    protected $app;
    protected $db;
    protected $session;
    protected $auth;
    protected $theme;
    protected $data = [];

    public function __construct() {
        try {
            $this->app = Application::getInstance();
            $this->db = $this->app->getDb();
            $this->session = $this->app->getSession();
            $this->auth = $this->app->getAuth();
            $this->theme = $this->app->getTheme();
            
            // Set default data
            $this->data['app_name'] = APP_NAME;
            $this->data['app_url'] = APP_URL;
            $this->data['current_user'] = $this->auth->getCurrentUser();
            $this->data['flash_messages'] = $this->session->getFlashMessages();
        } catch (Exception $e) {
            error_log("Controller initialization failed: " . $e->getMessage());
            // Set minimal defaults
            $this->data['app_name'] = APP_NAME;
            $this->data['app_url'] = APP_URL;
        }
    }

    protected function render($view, $data = []) {
        // Merge controller data with view data
        $data = array_merge($this->data, $data);
        
        // Extract data to make variables available in view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewFile = $this->theme->getViewPath($view);
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new Exception("View file not found: {$viewFile}");
        }
        
        // Get the buffered content
        $content = ob_get_clean();
        
        // Include layout if it exists
        $layoutFile = $this->theme->getLayoutPath('default');
        if (file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            echo $content;
        }
    }

    protected function renderAdmin($view, $data = []) {
        // Merge controller data with view data
        $data = array_merge($this->data, $data);
        
        // Extract data to make variables available in view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewFile = APP_PATH . '/admin/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new Exception("Admin view file not found: {$viewFile}");
        }
        
        // Get the buffered content
        $content = ob_get_clean();
        
        // Include admin layout
        $layoutFile = APP_PATH . '/admin/views/layouts/admin.php';
        if (file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            echo $content;
        }
    }

    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect($url, $statusCode = 302) {
        header("Location: {$url}", true, $statusCode);
        exit;
    }

    protected function redirectBack() {
        $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL;
        $this->redirect($referer);
    }

    protected function requireAuth($roles = []) {
        if (!$this->auth->isLoggedIn()) {
            $this->session->setFlash('error', 'You must be logged in to access this page.');
            $this->redirect(APP_URL . '/login');
        }

        if (!empty($roles) && !$this->auth->hasRole($roles)) {
            $this->session->setFlash('error', 'You do not have permission to access this page.');
            $this->redirect(APP_URL . '/dashboard');
        }
    }

    protected function requireGuest() {
        if ($this->auth->isLoggedIn()) {
            $this->redirect(APP_URL . '/dashboard');
        }
    }

    protected function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            
            if (is_string($rule)) {
                $rule = explode('|', $rule);
            }
            
            foreach ($rule as $validation) {
                if (strpos($validation, ':') !== false) {
                    list($validationType, $parameter) = explode(':', $validation);
                } else {
                    $validationType = $validation;
                    $parameter = null;
                }
                
                switch ($validationType) {
                    case 'required':
                        if (empty($value)) {
                            $errors[$field][] = "The {$field} field is required.";
                        }
                        break;
                        
                    case 'email':
                        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = "The {$field} must be a valid email address.";
                        }
                        break;
                        
                    case 'min':
                        if (!empty($value) && strlen($value) < $parameter) {
                            $errors[$field][] = "The {$field} must be at least {$parameter} characters.";
                        }
                        break;
                        
                    case 'max':
                        if (!empty($value) && strlen($value) > $parameter) {
                            $errors[$field][] = "The {$field} must not exceed {$parameter} characters.";
                        }
                        break;
                        
                    case 'numeric':
                        if (!empty($value) && !is_numeric($value)) {
                            $errors[$field][] = "The {$field} must be a number.";
                        }
                        break;
                        
                    case 'alpha':
                        if (!empty($value) && !ctype_alpha($value)) {
                            $errors[$field][] = "The {$field} must contain only letters.";
                        }
                        break;
                        
                    case 'alphanum':
                        if (!empty($value) && !ctype_alnum($value)) {
                            $errors[$field][] = "The {$field} must contain only letters and numbers.";
                        }
                        break;
                }
            }
        }
        
        return empty($errors) ? true : $errors;
    }

    protected function sanitize($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->sanitize($value);
            }
        } else {
            $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
        
        return $data;
    }

    protected function setFlash($type, $message) {
        $this->session->setFlash($type, $message);
    }

    protected function logActivity($action, $description = null) {
        $user = $this->auth->getCurrentUser();
        $userId = $user ? $user['id'] : null;
        
        $logData = [
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ];
        
        $activityLog = new ActivityLog();
        $activityLog->create($logData);
    }

    protected function uploadFile($file, $destination) {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new Exception('Invalid file parameters.');
        }

        // Check file upload error
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new Exception('No file uploaded.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception('File size exceeds limit.');
            default:
                throw new Exception('Unknown file upload error.');
        }

        // Check file size
        if ($file['size'] > MAX_FILE_SIZE) {
            throw new Exception('File size exceeds limit.');
        }

        // Check file type
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $file['tmp_name']);
        finfo_close($fileInfo);

        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp'
        ];

        if (!in_array($mimeType, array_keys($allowedTypes))) {
            throw new Exception('File type not allowed.');
        }

        // Generate unique filename
        $extension = $allowedTypes[$mimeType];
        $filename = uniqid() . '.' . $extension;
        $filepath = $destination . '/' . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new Exception('Failed to move uploaded file.');
        }

        return $filename;
    }

    protected function generateQRCode($data, $filename = null) {
        if ($filename === null) {
            $filename = uniqid() . '.png';
        }

        $filepath = QR_CODE_PATH . '/' . $filename;

        // Use QR code library (you'll need to include a QR code library)
        // This is a placeholder - you'll need to implement actual QR code generation
        // For example, using endroid/qr-code or similar
        
        // For now, create a placeholder
        $image = imagecreatetruecolor(QR_CODE_SIZE, QR_CODE_SIZE);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        
        imagefill($image, 0, 0, $white);
        
        // Draw a simple pattern as placeholder
        for ($i = 0; $i < QR_CODE_SIZE; $i += 20) {
            for ($j = 0; $j < QR_CODE_SIZE; $j += 20) {
                if (($i + $j) % 40 == 0) {
                    imagefilledrectangle($image, $i, $j, $i + 10, $j + 10, $black);
                }
            }
        }
        
        imagepng($image, $filepath);
        imagedestroy($image);

        return $filename;
    }
}
?>