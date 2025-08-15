<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Restaurant Menu System'; ?></title>
    <meta name="description" content="<?php echo $description ?? 'QR Code Restaurant Menu System'; ?>">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo asset('css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo $theme->getAssetUrl('css/style.css'); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo asset('favicon.ico'); ?>">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="<?php echo url(); ?>">
                    <?php echo $app_name; ?>
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo active_class('/'); ?>" href="<?php echo url(); ?>">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo active_class('/branches'); ?>" href="<?php echo url('/branches'); ?>">Branches</a>
                        </li>
                    </ul>
                    
                    <ul class="navbar-nav">
                        <?php if ($auth->isLoggedIn()): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <?php echo htmlspecialchars($current_user['full_name']); ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?php echo url('/profile'); ?>">Profile</a></li>
                                    <li><a class="dropdown-item" href="<?php echo url('/change-password'); ?>">Change Password</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo url('/logout'); ?>">Logout</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo url('/login'); ?>">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo url('/register'); ?>">Register</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Flash Messages -->
    <?php if (!empty($flash_messages)): ?>
        <div class="flash-messages">
            <?php foreach ($flash_messages as $type => $message): ?>
                <div class="alert alert-<?php echo $type; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">
        <?php echo $content; ?>
    </main>

    <!-- Footer -->
    <footer class="main-footer bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><?php echo $app_name; ?></h5>
                    <p>QR Code Restaurant Menu System</p>
                </div>
                <div class="col-md-6">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo url('/branches'); ?>" class="text-white-50">Branches</a></li>
                        <li><a href="<?php echo url('/login'); ?>" class="text-white-50">Login</a></li>
                        <li><a href="<?php echo url('/register'); ?>" class="text-white-50">Register</a></li>
                    </ul>
                </div>
            </div>
            <hr class="bg-white">
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> <?php echo $app_name; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="<?php echo asset('js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?php echo $theme->getAssetUrl('js/script.js'); ?>"></script>
    
    <!-- CSRF Token for AJAX -->
    <script>
        const csrfToken = '<?php echo csrf_token(); ?>';
    </script>
</body>
</html>