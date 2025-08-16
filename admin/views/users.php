<div class="admin-users">
    <div class="row">
        <div class="col-12">
            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <h1>Users Management</h1>
                    <p class="text-muted">Manage system users and their permissions</p>
                </div>
                <div>
                    <a href="<?php echo url('/admin/users/create'); ?>" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Add New User
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="row mb-4">
        <div class="col-md-6">
            <form method="GET" action="<?php echo url('/admin/users'); ?>" class="d-flex">
                <input type="text" class="form-control me-2" name="search" 
                       placeholder="Search users..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <button type="submit" class="btn btn-outline-primary">Search</button>
            </form>
        </div>
        <div class="col-md-6">
            <div class="d-flex gap-2 justify-content-end">
                <select class="form-select" id="roleFilter" style="width: auto;">
                    <option value="">All Roles</option>
                    <option value="super_admin">Super Admin</option>
                    <option value="branch_manager">Branch Manager</option>
                    <option value="chef">Chef</option>
                    <option value="waiter">Waiter</option>
                    <option value="staff">Staff</option>
                </select>
                <select class="form-select" id="statusFilter" style="width: auto;">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-body">
            <?php if (!empty($users)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Branch</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $this->getRoleBadgeClass($user['role']); ?>">
                                            <?php echo str_replace('_', ' ', ucwords($user['role'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo !empty($user['branch_name']) ? htmlspecialchars($user['branch_name']) : '-'; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $user['is_active'] ? 'success' : 'danger'; ?>">
                                            <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo !empty($user['last_login']) ? format_time_ago($user['last_login']) : 'Never'; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo url('/admin/users/' . $user['id'] . '/edit'); ?>" 
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDelete(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')" 
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <?php if ($user['is_active']): ?>
                                                <button type="button" class="btn btn-sm btn-outline-warning" 
                                                        onclick="toggleStatus(<?php echo $user['id']; ?>, 0)" 
                                                        title="Deactivate">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        onclick="toggleStatus(<?php echo $user['id']; ?>, 1)" 
                                                        title="Activate">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if (!empty($pagination) && $pagination['last_page'] > 1): ?>
                    <nav aria-label="Users pagination">
                        <ul class="pagination justify-content-center">
                            <?php if ($pagination['current_page'] > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?>">Previous</a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                                <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h4>No Users Found</h4>
                    <p class="text-muted">No users match your search criteria.</p>
                    <a href="<?php echo url('/admin/users/create'); ?>" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Add First User
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the user "<span id="deleteUsername"></span>"?</p>
                <p class="text-warning">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(userId, username) {
    document.getElementById('deleteUsername').textContent = username;
    document.getElementById('deleteForm').action = '/admin/users/' + userId;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function toggleStatus(userId, status) {
    if (confirm('Are you sure you want to ' + (status ? 'activate' : 'deactivate') + ' this user?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/users/' + userId + '/toggle-status';
        form.innerHTML = `
            <input type="hidden" name="csrf_token" value="${csrfToken}">
            <input type="hidden" name="status" value="${status}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Filter functionality
document.getElementById('roleFilter').addEventListener('change', function() {
    const url = new URL(window.location);
    url.searchParams.set('role', this.value);
    window.location.href = url.toString();
});

document.getElementById('statusFilter').addEventListener('change', function() {
    const url = new URL(window.location);
    url.searchParams.set('status', this.value);
    window.location.href = url.toString();
});
</script>