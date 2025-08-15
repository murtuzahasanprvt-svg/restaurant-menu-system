<div class="admin-dashboard">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1>Dashboard</h1>
                <p class="text-muted">Welcome back, <?php echo htmlspecialchars($current_user['full_name']); ?>!</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <?php if ($auth->hasRole('super_admin')): ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card card bg-primary text-white">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-store fa-2x"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['total_branches'] ?? 0; ?></h3>
                            <p>Total Branches</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card card bg-success text-white">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['total_users'] ?? 0; ?></h3>
                            <p>Total Users</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card card bg-warning text-white">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-clipboard-list fa-2x"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['total_orders'] ?? 0; ?></h3>
                            <p>Total Orders</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card card bg-info text-white">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo format_price($stats['total_revenue'] ?? 0); ?></h3>
                            <p>Total Revenue</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($auth->hasRole('branch_manager')): ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card card bg-primary text-white">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['branch_staff'] ?? 0; ?></h3>
                            <p>Staff Members</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card card bg-success text-white">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-utensils fa-2x"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['branch_menu_items'] ?? 0; ?></h3>
                            <p>Menu Items</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card card bg-warning text-white">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-table fa-2x"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['branch_tables'] ?? 0; ?></h3>
                            <p>Tables</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card card bg-info text-white">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo format_price($stats['branch_revenue'] ?? 0); ?></h3>
                            <p>Branch Revenue</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($auth->hasRole('chef')): ?>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="stat-card card bg-warning text-white">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['pending_orders'] ?? 0; ?></h3>
                            <p>Pending Orders</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="stat-card card bg-primary text-white">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-fire fa-2x"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['preparing_orders'] ?? 0; ?></h3>
                            <p>Preparing Orders</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="stat-card card bg-success text-white">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-check fa-2x"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['completed_orders'] ?? 0; ?></h3>
                            <p>Completed Today</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($auth->hasRole('waiter')): ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card card bg-primary text-white">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-clipboard-list fa-2x"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['active_orders'] ?? 0; ?></h3>
                            <p>Active Orders</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card card bg-success text-white">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-check fa-2x"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['completed_orders'] ?? 0; ?></h3>
                            <p>Completed Today</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card card bg-warning text-white">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-table fa-2x"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $stats['occupied_tables'] ?? 0; ?></h3>
                            <p>Occupied Tables</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card card bg-info text-white">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo format_price($stats['today_revenue'] ?? 0); ?></h3>
                            <p>Today's Revenue</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Activity</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentActivity)): ?>
                        <div class="activity-list">
                            <?php foreach ($recentActivity as $activity): ?>
                                <div class="activity-item">
                                    <div class="activity-content">
                                        <div class="activity-user">
                                            <strong><?php echo htmlspecialchars($activity['username'] ?? 'System'); ?></strong>
                                            <span class="text-muted"><?php echo htmlspecialchars($activity['full_name'] ?? ''); ?></span>
                                        </div>
                                        <div class="activity-action">
                                            <?php echo htmlspecialchars($activity['action']); ?>
                                            <?php if (!empty($activity['description'])): ?>
                                                - <?php echo htmlspecialchars($activity['description']); ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="activity-time">
                                            <small class="text-muted">
                                                <?php echo format_time_ago($activity['created_at']); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No recent activity found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <?php if ($auth->hasRole('super_admin')): ?>
                            <a href="<?php echo url('/admin/users'); ?>" class="list-group-item list-group-item-action">
                                <i class="fas fa-user-plus"></i> Add New User
                            </a>
                            <a href="<?php echo url('/admin/branches'); ?>" class="list-group-item list-group-item-action">
                                <i class="fas fa-store"></i> Manage Branches
                            </a>
                            <a href="<?php echo url('/admin/settings'); ?>" class="list-group-item list-group-item-action">
                                <i class="fas fa-cog"></i> System Settings
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($auth->hasRole('branch_manager')): ?>
                            <a href="<?php echo url('/manager/menu'); ?>" class="list-group-item list-group-item-action">
                                <i class="fas fa-plus"></i> Add Menu Item
                            </a>
                            <a href="<?php echo url('/manager/staff'); ?>" class="list-group-item list-group-item-action">
                                <i class="fas fa-user-plus"></i> Add Staff Member
                            </a>
                            <a href="<?php echo url('/manager/tables'); ?>" class="list-group-item list-group-item-action">
                                <i class="fas fa-plus"></i> Add Table
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($auth->hasRole('chef')): ?>
                            <a href="<?php echo url('/chef/orders'); ?>" class="list-group-item list-group-item-action">
                                <i class="fas fa-clipboard-list"></i> View Orders
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($auth->hasRole('waiter')): ?>
                            <a href="<?php echo url('/waiter/orders'); ?>" class="list-group-item list-group-item-action">
                                <i class="fas fa-plus"></i> Create Order
                            </a>
                            <a href="<?php echo url('/waiter/tables'); ?>" class="list-group-item list-group-item-action">
                                <i class="fas fa-table"></i> Manage Tables
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>