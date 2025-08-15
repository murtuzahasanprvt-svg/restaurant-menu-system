<div class="admin-branches">
    <div class="row">
        <div class="col-12">
            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <h1>Branches Management</h1>
                    <p class="text-muted">Manage restaurant branches and their settings</p>
                </div>
                <div>
                    <a href="<?php echo url('/admin/branches/create'); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Branch
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="row mb-4">
        <div class="col-md-6">
            <form method="GET" action="<?php echo url('/admin/branches'); ?>" class="d-flex">
                <input type="text" class="form-control me-2" name="search" 
                       placeholder="Search branches..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                <button type="submit" class="btn btn-outline-primary">Search</button>
            </form>
        </div>
        <div class="col-md-6">
            <div class="d-flex gap-2 justify-content-end">
                <select class="form-select" id="statusFilter" style="width: auto;">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                <button class="btn btn-outline-secondary" onclick="exportBranches()">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
        </div>
    </div>

    <!-- Branches Table -->
    <div class="card">
        <div class="card-body">
            <?php if (!empty($branches)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Users</th>
                                <th>Tables</th>
                                <th>Orders</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($branches as $branch): ?>
                                <tr>
                                    <td><?php echo $branch['id']; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($branch['logo_url'])): ?>
                                                <img src="<?php echo asset('uploads/' . $branch['logo_url']); ?>" 
                                                     class="rounded-circle me-2" style="width: 30px; height: 30px;" 
                                                     alt="<?php echo htmlspecialchars($branch['name']); ?>">
                                            <?php endif; ?>
                                            <strong><?php echo htmlspecialchars($branch['name']); ?></strong>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($branch['address']); ?></td>
                                    <td><?php echo htmlspecialchars($branch['phone']); ?></td>
                                    <td><?php echo !empty($branch['email']) ? htmlspecialchars($branch['email']) : '-'; ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo $branch['total_users'] ?? 0; ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning"><?php echo $branch['total_tables'] ?? 0; ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success"><?php echo $branch['total_orders'] ?? 0; ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $branch['is_active'] ? 'success' : 'danger'; ?>">
                                            <?php echo $branch['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo url('/admin/branches/' . $branch['id']); ?>" 
                                               class="btn btn-sm btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo url('/admin/branches/' . $branch['id'] . '/edit'); ?>" 
                                               class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?php echo url('/admin/branches/' . $branch['id'] . '/qr-codes'); ?>" 
                                               class="btn btn-sm btn-outline-info" title="QR Codes">
                                                <i class="fas fa-qrcode"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDelete(<?php echo $branch['id']; ?>, '<?php echo htmlspecialchars($branch['name']); ?>')" 
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <?php if ($branch['is_active']): ?>
                                                <button type="button" class="btn btn-sm btn-outline-warning" 
                                                        onclick="toggleStatus(<?php echo $branch['id']; ?>, 0)" 
                                                        title="Deactivate">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        onclick="toggleStatus(<?php echo $branch['id']; ?>, 1)" 
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
                    <nav aria-label="Branches pagination">
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
                    <i class="fas fa-store fa-3x text-muted mb-3"></i>
                    <h4>No Branches Found</h4>
                    <p class="text-muted">No branches match your search criteria.</p>
                    <a href="<?php echo url('/admin/branches/create'); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add First Branch
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary"><?php echo $stats['total_branches'] ?? 0; ?></h3>
                    <p class="text-muted">Total Branches</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success"><?php echo $stats['active_branches'] ?? 0; ?></h3>
                    <p class="text-muted">Active Branches</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning"><?php echo $stats['total_users'] ?? 0; ?></h3>
                    <p class="text-muted">Total Users</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-info"><?php echo $stats['total_orders'] ?? 0; ?></h3>
                    <p class="text-muted">Total Orders</p>
                </div>
            </div>
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
                <p>Are you sure you want to delete the branch "<span id="deleteBranchName"></span>"?</p>
                <p class="text-warning">This action cannot be undone and will delete all associated data.</p>
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
function confirmDelete(branchId, branchName) {
    document.getElementById('deleteBranchName').textContent = branchName;
    document.getElementById('deleteForm').action = '/admin/branches/' + branchId;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function toggleStatus(branchId, status) {
    if (confirm('Are you sure you want to ' + (status ? 'activate' : 'deactivate') + ' this branch?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/branches/' + branchId + '/toggle-status';
        form.innerHTML = `
            <input type="hidden" name="csrf_token" value="${csrfToken}">
            <input type="hidden" name="status" value="${status}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function exportBranches() {
    window.location.href = '/admin/branches/export';
}

// Filter functionality
document.getElementById('statusFilter').addEventListener('change', function() {
    const url = new URL(window.location);
    url.searchParams.set('status', this.value);
    window.location.href = url.toString();
});
</script>