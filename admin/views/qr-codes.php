<div class="admin-qr-codes">
    <div class="row">
        <div class="col-12">
            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <h1>QR Codes Management</h1>
                    <p class="text-muted">Generate and manage QR codes for restaurant tables</p>
                </div>
                <div class="d-flex gap-2">
                    <?php if (!empty($branchId)): ?>
                        <button class="btn btn-success" onclick="batchGenerate(<?php echo $branchId; ?>)">
                            <i class="fas fa-qrcode"></i> Generate All
                        </button>
                        <button class="btn btn-outline-secondary" onclick="exportQRCodes(<?php echo $branchId; ?>)">
                            <i class="fas fa-download"></i> Export
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Branch Selection (if not specified) -->
    <?php if (empty($branchId)): ?>
        <div class="row mb-4">
            <div class="col-md-6">
                <label for="branchSelect" class="form-label">Select Branch</label>
                <select class="form-select" id="branchSelect" onchange="loadBranchQRCodes(this.value)">
                    <option value="">Choose a branch...</option>
                    <?php if (!empty($branches)): ?>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?php echo $branch['id']; ?>">
                                <?php echo htmlspecialchars($branch['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>
    <?php endif; ?>

    <!-- QR Codes Grid -->
    <?php if (!empty($branchId) && !empty($qrCodes)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            QR Codes for <?php echo htmlspecialchars($branch['name']); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <?php foreach ($qrCodes as $qrCode): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="qr-code-card card">
                                        <div class="card-body text-center">
                                            <div class="qr-code-image mb-3">
                                                <?php if (!empty($qrCode['qr_image_url']) && file_exists(PUBLIC_PATH . '/' . $qrCode['qr_image_url'])): ?>
                                                    <img src="<?php echo asset($qrCode['qr_image_url']); ?>" 
                                                         class="img-fluid" style="max-width: 200px;" 
                                                         alt="QR Code">
                                                <?php else: ?>
                                                    <div class="qr-placeholder bg-light p-4 rounded">
                                                        <i class="fas fa-qrcode fa-3x text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <h6 class="card-title">Table <?php echo htmlspecialchars($qrCode['table_number']); ?></h6>
                                            <?php if (!empty($qrCode['location'])): ?>
                                                <p class="text-muted small"><?php echo htmlspecialchars($qrCode['location']); ?></p>
                                            <?php endif; ?>
                                            
                                            <div class="qr-code-info mb-3">
                                                <small class="text-muted">
                                                    Capacity: <?php echo $qrCode['capacity']; ?> people<br>
                                                    Orders: <?php echo $qrCode['total_orders'] ?? 0; ?>
                                                </small>
                                            </div>
                                            
                                            <div class="qr-code-actions">
                                                <div class="btn-group w-100" role="group">
                                                    <a href="<?php echo asset($qrCode['qr_image_url']); ?>" 
                                                       class="btn btn-sm btn-outline-primary" target="_blank" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?php echo url('/admin/qr-codes/download/' . $qrCode['id']); ?>" 
                                                       class="btn btn-sm btn-outline-success" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-warning" 
                                                            onclick="regenerateQRCode(<?php echo $qrCode['id']; ?>)" title="Regenerate">
                                                        <i class="fas fa-sync"></i>
                                                    </button>
                                                    <?php if ($qrCode['is_active']): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="toggleStatus(<?php echo $qrCode['id']; ?>, 0)" title="Deactivate">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                                onclick="toggleStatus(<?php echo $qrCode['id']; ?>, 1)" title="Activate">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            
                                            <div class="qr-code-status mt-2">
                                                <span class="badge bg-<?php echo $qrCode['is_active'] ? 'success' : 'danger'; ?>">
                                                    <?php echo $qrCode['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif (!empty($branchId)): ?>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h4>No QR Codes Found</h4>
                    <p>No QR codes have been generated for this branch yet.</p>
                    <button class="btn btn-primary" onclick="batchGenerate(<?php echo $branchId; ?>)">
                        <i class="fas fa-qrcode"></i> Generate QR Codes
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Statistics -->
    <?php if (!empty($branchId) && !empty($stats)): ?>
        <div class="row">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-primary"><?php echo $stats['total_qr_codes'] ?? 0; ?></h3>
                        <p class="text-muted">Total QR Codes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-success"><?php echo $stats['active_qr_codes'] ?? 0; ?></h3>
                        <p class="text-muted">Active QR Codes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-warning"><?php echo $stats['inactive_qr_codes'] ?? 0; ?></h3>
                        <p class="text-muted">Inactive QR Codes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-info"><?php echo count($qrCodes) ?? 0; ?></h3>
                        <p class="text-muted">Tables Covered</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalTitle">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="confirmModalBody">
                Are you sure you want to perform this action?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmModalButton">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
function loadBranchQRCodes(branchId) {
    if (branchId) {
        window.location.href = '/admin/branches/' + branchId + '/qr-codes';
    }
}

function regenerateQRCode(qrCodeId) {
    if (confirm('Are you sure you want to regenerate this QR code? The old QR code will no longer work.')) {
        window.location.href = '/admin/qr-codes/regenerate/' + qrCodeId;
    }
}

function toggleStatus(qrCodeId, status) {
    const action = status ? 'activate' : 'deactivate';
    if (confirm(`Are you sure you want to ${action} this QR code?`)) {
        window.location.href = '/admin/qr-codes/toggle-status/' + qrCodeId;
    }
}

function batchGenerate(branchId) {
    if (confirm('Are you sure you want to generate QR codes for all tables in this branch?')) {
        window.location.href = '/admin/qr-codes/batch-generate/' + branchId;
    }
}

function exportQRCodes(branchId) {
    window.location.href = '/admin/qr-codes/export/' + branchId;
}

// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});
</script>