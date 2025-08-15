<?php
/**
 * Web Routes Configuration
 */

// Home routes
$router->get('/', 'HomeController@index');
$router->get('/home', 'HomeController@index');

// Authentication routes
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@register');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');
$router->get('/forgot-password', 'AuthController@forgotPassword');
$router->post('/forgot-password', 'AuthController@forgotPassword');
$router->get('/reset-password/{token}', 'AuthController@resetPassword');
$router->post('/reset-password', 'AuthController@resetPassword');

// Profile routes (require authentication)
$router->get('/profile', 'AuthController@profile');
$router->post('/profile', 'AuthController@profile');
$router->get('/change-password', 'AuthController@changePassword');
$router->post('/change-password', 'AuthController@changePassword');

// Dashboard routes
$router->get('/dashboard', 'DashboardController@index');

// Branch selection for customers
$router->get('/branches', 'BranchController@index');
$router->get('/branch/{id}', 'BranchController@show');

// Menu routes
$router->get('/menu/{branchId}', 'MenuController@index');
$router->get('/menu/{branchId}/category/{categoryId}', 'MenuController@category');
$router->get('/menu/{branchId}/item/{itemId}', 'MenuController@item');

// Order routes
$router->get('/order/{qrCode}', 'OrderController@create');
$router->post('/order', 'OrderController@store');
$router->get('/order/status/{orderNumber}', 'OrderController@status');
$router->get('/order/tracking/{orderNumber}', 'OrderController@tracking');

// QR code routes
$router->get('/qr/{code}', 'QRController@scan');

// API routes for AJAX requests
$router->post('/api/menu/items', 'ApiController@menuItems');
$router->post('/api/order/create', 'ApiController@createOrder');
$router->post('/api/order/status', 'ApiController@orderStatus');
$router->post('/api/qr/validate', 'ApiController@validateQR');

// Error handling
$router->notFound(function() {
    http_response_code(404);
    if (file_exists(APP_PATH . '/app/views/errors/404.php')) {
        include APP_PATH . '/app/views/errors/404.php';
    } else {
        echo '<h1>404 - Page Not Found</h1>';
        echo '<p>The page you are looking for does not exist.</p>';
    }
    exit;
});
?>