<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h2 class="card-title mb-0">
                        <i class="fas fa-check-circle"></i> Order Status
                    </h2>
                </div>
                <div class="card-body">
                    <?php 
                    // Helper function for safe htmlspecialchars handling
                    if (!function_exists('safe_htmlspecialchars')) {
                        function safe_htmlspecialchars($string, $default = '') {
                            return htmlspecialchars($string ?? $default);
                        }
                    }
                    
                    // Helper function to get status badge class
                    if (!function_exists('getStatusBadgeClass')) {
                        function getStatusBadgeClass($status) {
                            switch ($status) {
                                case 'pending':
                                    return 'warning';
                                case 'confirmed':
                                    return 'info';
                                case 'preparing':
                                    return 'primary';
                                case 'ready':
                                    return 'success';
                                case 'delivered':
                                    return 'secondary';
                                case 'cancelled':
                                    return 'danger';
                                default:
                                    return 'secondary';
                            }
                        }
                    }
                    ?>
                    
                    <?php if (!empty($flash_messages['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo safe_htmlspecialchars($flash_messages['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($flash_messages['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo safe_htmlspecialchars($flash_messages['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Order Information -->
                    <div class="order-info mb-4">
                        <h4>Order Information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Order Number:</strong> <?php echo safe_htmlspecialchars($order['order_number']); ?></p>
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-<?php echo getStatusBadgeClass($order['status']); ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                                    </span>
                                </p>
                                <p><strong>Order Type:</strong> <?php echo ucfirst(str_replace('_', ' ', $order['order_type'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Branch:</strong> <?php echo safe_htmlspecialchars($order['branch_name']); ?></p>
                                <?php if (!empty($order['table_number'])): ?>
                                    <p><strong>Table:</strong> <?php echo safe_htmlspecialchars($order['table_number']); ?></p>
                                <?php endif; ?>
                                <p><strong>Placed:</strong> <?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="customer-info mb-4">
                        <h4>Customer Information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> <?php echo safe_htmlspecialchars($order['customer_name']); ?></p>
                                <p><strong>Phone:</strong> <?php echo safe_htmlspecialchars($order['customer_phone']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($order['customer_email'])): ?>
                                    <p><strong>Email:</strong> <?php echo safe_htmlspecialchars($order['customer_email']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (!empty($order['special_instructions'])): ?>
                            <div class="mt-3">
                                <p><strong>Special Instructions:</strong></p>
                                <p class="text-muted"><?php echo safe_htmlspecialchars($order['special_instructions']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Order Items -->
                    <div class="order-items mb-4">
                        <h4>Order Items</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td><?php echo safe_htmlspecialchars($item['item_name']); ?></td>
                                            <td><?php echo (int)$item['quantity']; ?></td>
                                            <td>$<?php echo number_format($item['unit_price'], 2); ?></td>
                                            <td>$<?php echo number_format($item['total_price'], 2); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Tax (8.5%):</strong></td>
                                        <td>$<?php echo number_format($order['tax_amount'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                        <td><strong>$<?php echo number_format($order['final_amount'], 2); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Order Status Timeline -->
                    <div class="order-timeline mb-4">
                        <h4>Order Timeline</h4>
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker">
                                    <i class="fas fa-check text-success"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Order Placed</h6>
                                    <p class="text-muted"><?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker">
                                    <i class="fas fa-clock text-warning <?php echo in_array($order['status'], ['confirmed', 'preparing', 'ready', 'delivered']) ? '' : 'opacity-50'; ?>"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Order Confirmed</h6>
                                    <p class="text-muted">Waiting for confirmation</p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker">
                                    <i class="fas fa-utensils text-info <?php echo in_array($order['status'], ['preparing', 'ready', 'delivered']) ? '' : 'opacity-50'; ?>"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Preparing</h6>
                                    <p class="text-muted">Kitchen preparing your order</p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker">
                                    <i class="fas fa-check-circle text-success <?php echo in_array($order['status'], ['ready', 'delivered']) ? '' : 'opacity-50'; ?>"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Ready for Pickup/Delivery</h6>
                                    <p class="text-muted">Your order is ready</p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-marker">
                                    <i class="fas fa-check-double text-success <?php echo $order['status'] === 'delivered' ? '' : 'opacity-50'; ?>"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>Delivered/Completed</h6>
                                    <p class="text-muted">Order completed</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo url('/menu/' . ($order['branch_id'] ?? '1')); ?>" class="btn btn-outline-primary">
                            <i class="fas fa-shopping-cart"></i> Order Again
                        </a>
                        <a href="<?php echo url('/branches'); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-store"></i> View Branches
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 40px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    padding-bottom: 30px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -25px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: white;
    border: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #e9ecef;
}

.timeline-content h6 {
    margin: 0 0 5px 0;
    color: #495057;
}

.timeline-content p {
    margin: 0;
    font-size: 0.875rem;
    color: #6c757d;
}

.opacity-50 {
    opacity: 0.5;
}
</style>