<?php
/**
 * Router Class
 */

class Router {
    private $routes = [];
    private $params = [];
    private $notFoundHandler = null;

    public function __construct() {
        // Don't load routes in constructor to avoid circular dependency
    }

    public function loadRoutes($app = null) {
        // Load web routes
        $webRoutesFile = APP_PATH . '/app/config/routes.php';
        if (file_exists($webRoutesFile)) {
            // Pass router instance to routes file
            $router = $this;
            require_once $webRoutesFile;
        }

        // Load admin routes
        $adminRoutesFile = APP_PATH . '/admin/config/routes.php';
        if (file_exists($adminRoutesFile)) {
            require_once $adminRoutesFile;
        }

        // Load API routes
        $apiRoutesFile = APP_PATH . '/api/config/routes.php';
        if (file_exists($apiRoutesFile)) {
            require_once $apiRoutesFile;
        }
    }

    public function add($method, $route, $handler) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'route' => $route,
            'handler' => $handler,
            'params' => []
        ];
    }

    public function get($route, $handler) {
        $this->add('GET', $route, $handler);
    }

    public function post($route, $handler) {
        $this->add('POST', $route, $handler);
    }

    public function put($route, $handler) {
        $this->add('PUT', $route, $handler);
    }

    public function delete($route, $handler) {
        $this->add('DELETE', $route, $handler);
    }

    public function any($route, $handler) {
        $this->add('GET', $route, $handler);
        $this->add('POST', $route, $handler);
        $this->add('PUT', $route, $handler);
        $this->add('DELETE', $route, $handler);
    }

    public function group($prefix, $callback) {
        $previousPrefix = $this->prefix ?? '';
        $this->prefix = $previousPrefix . $prefix;

        if (is_callable($callback)) {
            $callback($this);
        }

        $this->prefix = $previousPrefix;
    }

    public function notFound($handler) {
        $this->notFoundHandler = $handler;
    }

    public function dispatch() {
        $uri = $this->getUri();
        $method = $_SERVER['REQUEST_METHOD'];

        // Handle POST method override for PUT and DELETE
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        $route = $this->findRoute($uri, $method);

        if ($route) {
            return $this->executeHandler($route['handler'], $route['params']);
        }

        // Try to handle dynamic routes
        foreach ($this->routes as $route) {
            if ($route['method'] === $method) {
                $pattern = $this->convertPatternToRegex($route['route']);
                
                if (preg_match($pattern, $uri, $matches)) {
                    array_shift($matches); // Remove the full match
                    $params = $this->extractParams($route['route'], $matches);
                    
                    return $this->executeHandler($route['handler'], $params);
                }
            }
        }

        // No route found, handle 404
        $this->handleNotFound();
    }

    private function getUri() {
        $uri = $_SERVER['REQUEST_URI'];
        
        // Remove query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        // Remove base path if exists
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if ($basePath !== '/') {
            $uri = substr($uri, strlen($basePath));
        }
        
        return '/' . trim($uri, '/');
    }

    private function findRoute($uri, $method) {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['route'] === $uri) {
                return $route;
            }
        }
        return null;
    }

    private function convertPatternToRegex($pattern) {
        // Convert route pattern to regex
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+):([^}]+)\}/', '(?P<$1>$2)', $pattern);
        return '#^' . $pattern . '$#';
    }

    private function extractParams($route, $matches) {
        $params = [];
        $routeParts = explode('/', trim($route, '/'));
        $uriParts = explode('/', trim($this->getUri(), '/'));

        foreach ($routeParts as $index => $part) {
            if (preg_match('/\{([a-zA-Z0-9_]+)\}/', $part, $matches2)) {
                $paramName = $matches2[1];
                if (isset($uriParts[$index])) {
                    $params[$paramName] = $uriParts[$index];
                }
            }
        }

        return $params;
    }

    private function executeHandler($handler, $params = []) {
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }

        if (is_string($handler)) {
            $parts = explode('@', $handler);
            if (count($parts) === 2) {
                list($controller, $method) = $parts;
                
                $controllerFile = $this->getControllerFile($controller);
                
                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                    
                    $controllerClass = $this->getControllerClass($controller);
                    
                    if (class_exists($controllerClass)) {
                        $controllerInstance = new $controllerClass();
                        
                        if (method_exists($controllerInstance, $method)) {
                            return call_user_func_array([$controllerInstance, $method], $params);
                        } else {
                            throw new Exception("Method '{$method}' not found in class '{$controllerClass}'");
                        }
                    } else {
                        throw new Exception("Class '{$controllerClass}' not found");
                    }
                } else {
                    throw new Exception("Controller file not found: {$controllerFile}");
                }
            } else {
                throw new Exception("Invalid handler format. Expected 'Controller@method'");
            }
        }

        throw new Exception("Handler not found: " . print_r($handler, true));
    }

    private function getControllerFile($controller) {
        // Check if it's an admin controller
        if (strpos($controller, 'Admin\\') === 0) {
            $controller = substr($controller, 6);
            return APP_PATH . '/admin/controllers/' . $controller . 'Controller.php';
        }
        
        // Check if it's an API controller
        if (strpos($controller, 'Api\\') === 0) {
            $controller = substr($controller, 4);
            return APP_PATH . '/api/controllers/' . $controller . 'Controller.php';
        }
        
        // Default web controller
        return APP_PATH . '/app/controllers/' . $controller . 'Controller.php';
    }

    private function getControllerClass($controller) {
        // Check if it's an admin controller
        if (strpos($controller, 'Admin\\') === 0) {
            return $controller;
        }
        
        // Check if it's an API controller
        if (strpos($controller, 'Api\\') === 0) {
            return $controller;
        }
        
        // Default web controller
        return $controller;
    }

    private function handleNotFound() {
        http_response_code(404);
        
        if ($this->notFoundHandler) {
            return call_user_func($this->notFoundHandler);
        }
        
        // Default 404 handler
        if (file_exists(APP_PATH . '/app/views/errors/404.php')) {
            include APP_PATH . '/app/views/errors/404.php';
        } else {
            echo '<h1>404 - Page Not Found</h1>';
            echo '<p>The page you are looking for does not exist.</p>';
        }
        
        exit;
    }

    public function url($route, $params = []) {
        $url = $route;
        
        foreach ($params as $key => $value) {
            $url = str_replace('{' . $key . '}', $value, $url);
        }
        
        return APP_URL . $url;
    }

    public function getRoutesCount() {
        return count($this->routes);
    }

    public function getRoutes() {
        return $this->routes;
    }

    public function testGetUri() {
        return $this->getUri();
    }

    public function testFindRoute($uri, $method) {
        return $this->findRoute($uri, $method);
    }
}
?>