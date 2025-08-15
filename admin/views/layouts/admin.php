<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Admin Panel - Restaurant Menu System'; ?></title>
    <meta name="description" content="<?php echo $description ?? 'Admin Panel for Restaurant Menu System'; ?>">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo asset('css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/admin.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo asset('favicon.ico'); ?>">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
</head>
<body class="admin-body">
    <!-- Admin Header -->
    <header class="admin-header">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="<?php echo url('/admin/dashboard'); ?>">
                    <i class="fas fa-utensils"></i>
                    <?php echo $app_name; ?> Admin
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="adminNavbar">
                    <ul class="navbar-nav me-auto">
                        <!-- Admin navigation items will be added based on user role -->
                    </ul>
                    
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i>
                                <?php echo htmlspecialchars($current_user['full_name']); ?>
                                <small class="text-muted">(<?php echo str_replace('_', ' ', $current_user['role']); ?>)</small>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo url('/profile'); ?>">
                                    <i class="fas fa-user"></i> Profile
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo url('/change-password'); ?>">
                                    <i class="fas fa-key"></i> Change Password
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo url('/logout'); ?>">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="admin-container">
        <!-- Admin Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-menu">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?php echo active_class('/admin/dashboard'); ?>" href="<?php echo url('/admin/dashboard'); ?>">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <!-- Super Admin Menu Items -->
                    <?php if ($auth->hasRole('super_admin')): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo active_class('/admin/users'); ?>" href="<?php echo url('/admin/users'); ?>">
                                <i class="fas fa-users"></i>
                                <span>Users</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo active_class('/admin/branches'); ?>" href="<?php echo url('/admin/branches'); ?>">
                                <i class="fas fa-store"></i>
                                <span>Branches</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo active_class('/admin/themes'); ?>" href="<?php echo url('/admin/themes'); ?>">
                                <i class="fas fa-palette"></i>
                                <span>Themes</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo active_class('/admin/addons'); ?>" href="<?php echo url('/admin/addons'); ?>">
                                <i class="fas fa-puzzle-piece"></i>
                                <span>Addons</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo active_class('/admin/settings'); ?>" href="<?php echo url('/admin/settings'); ?>">
                                <i class="fas fa-cog"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo active_class('/admin/logs'); ?>" href="<?php echo url('/admin/logs'); ?>">
                                <i class="fas fa-file-alt"></i>
                                <span>Activity Logs</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Branch Manager Menu Items -->
                    <?php if ($auth->hasRole('branch_manager')): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo active_class('/manager/branch'); ?>" href="<?php echo url('/manager/branch'); ?>">
                                <i class="fas fa-store"></i>
                                <span>My Branch</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo active_class('/manager/staff'); ?>" href="<?php echo url('/manager/staff'); ?>">
                                <i class="fas fa-users"></i>
                                <span>Staff Management</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo active_class('/manager/menu'); ?>" href="<?php echo url('/manager/menu'); ?>">
                                <i class="fas fa-utensils"></i>
                                <span>Menu Management</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo active_class('/manager/tables'); ?>" href="<?php echo url('/manager/tables'); ?>">
                                <i class="fas fa-table"></i>
                                <span>Tables</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo active_class('/manager/reports'); ?>" href="<?php echo url('/manager/reports'); ?>">
                                <i class="fas fa-chart-bar"></i>
                                <span>Reports</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Chef Menu Items -->
                    <?php if ($auth->hasRole('chef')): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo active_class('/chef/orders'); ?>" href="<?php echo url('/chef/orders'); ?>">
                                <i class="fas fa-clipboard-list"></i>
                                <span>Orders</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo active_class('/chef/menu'); ?>" href="<?php echo url('/chef/menu'); ?>">
                                <i class="fas fa-utensils"></i>
                                <span>Menu</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Waiter Menu Items -->
                    <?php if ($auth->hasRole('waiter')): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo active_class('/waiter/orders'); ?>" href="<?php echo url('/waiter/orders'); ?>">
                                <i class="fas fa-clipboard-list"></i>
                                <span>Orders</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo active_class('/waiter/tables'); ?>" href="<?php echo url('/waiter/tables'); ?>">
                                <i class="fas fa-table"></i>
                                <span>Tables</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo active_class('/waiter/menu'); ?>" href="<?php echo url('/waiter/menu'); ?>">
                                <i class="fas fa-utensils"></i>
                                <span>Menu</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Staff Menu Items -->
                    <?php if ($auth->hasRole('staff')): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo active_class('/dashboard'); ?>" href="<?php echo url('/dashboard'); ?>">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo active_class('/menu'); ?>" href="<?php echo url('/menu'); ?>">
                                <i class="fas fa-utensils"></i>
                                <span>Menu</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </aside>

        <!-- Admin Main Content -->
        <main class="admin-main">
            <!-- Flash Messages -->
            <?php if (!empty($flash_messages)): ?>
                <div class="flash-messages mb-4">
                    <?php foreach ($flash_messages as $type => $message): ?>
                        <div class="alert alert-<?php echo $type; ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Page Content -->
            <div class="admin-content">
                <?php echo $content; ?>
            </div>
        </main>
    </div>

    <!-- JavaScript -->
    <script src="<?php echo asset('js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?php echo asset('js/admin.js'); ?>"></script>
    
    <!-- CSRF Token for AJAX -->
    <script>
        const csrfToken = '<?php echo csrf_token(); ?>';
    </script>
</body>
</html>