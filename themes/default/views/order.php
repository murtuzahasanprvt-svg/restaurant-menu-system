<div class="order-page py-5">
    <div class="container">
        <!-- Branch Header -->
        <div class="branch-header mb-5">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5"><?php echo htmlspecialchars($branch['name']); ?></h1>
                    <p class="lead"><?php echo htmlspecialchars($branch['address']); ?></p>
                    <div class="branch-meta">
                        <span class="badge bg-primary me-2">
                            <i class="fas fa-phone"></i> <?php echo htmlspecialchars($branch['phone']); ?>
                        </span>
                        <?php if (!empty($branch['email'])): ?>
                            <span class="badge bg-secondary">
                                <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($branch['email']); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <a href="<?php echo url('/branches'); ?>" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Back to Branches
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Table Information -->
        <div class="table-info-card card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chair"></i> Ordering from Table <?php echo htmlspecialchars($qr_data['table_number']); ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="mb-0">
                            <i class="fas fa-info-circle"></i> 
                            You are placing an order for Table <?php echo htmlspecialchars($qr_data['table_number']); ?>. 
                            Your order will be delivered to this table.
                        </p>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <span class="badge bg-success fs-6">
                                Table <?php echo htmlspecialchars($qr_data['table_number']); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="selectedTable" value="<?php echo htmlspecialchars($qr_data['table_number']); ?>">
                <input type="hidden" id="selectedBranch" value="<?php echo htmlspecialchars($branch['id']); ?>">
                <input type="hidden" id="qrCode" value="<?php echo htmlspecialchars($qr_data['qr_code']); ?>">
            </div>
        </div>
        
        <!-- Menu Categories -->
        <div class="menu-categories mb-4">
            <div class="row">
                <div class="col-12">
                    <h3 class="mb-4">Menu Categories</h3>
                    <div class="category-tabs">
                        <ul class="nav nav-pills" id="categoryTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" 
                                        data-bs-target="#all" type="button" role="tab">
                                    All Items
                                </button>
                            </li>
                            <?php foreach ($categories as $category): ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="category-<?php echo $category['id']; ?>-tab" 
                                            data-bs-toggle="tab" data-bs-target="#category-<?php echo $category['id']; ?>" 
                                            type="button" role="tab">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Menu Items -->
        <div class="menu-content">
            <div class="tab-content" id="categoryTabContent">
                <!-- All Items Tab -->
                <div class="tab-pane fade show active" id="all" role="tabpanel">
                    <div class="row g-4">
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <?php if (!empty($category['items'])): ?>
                                    <?php foreach ($category['items'] as $item): ?>
                                        <div class="col-md-6 col-lg-4">
                                            <div class="menu-item-card card h-100">
                                                <?php if (!empty($item['image_url'])): ?>
                                                    <div class="menu-item-image">
                                                        <img src="<?php echo asset('uploads/' . $item['image_url']); ?>" 
                                                             class="card-img-top" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="card-body">
                                                    <div class="menu-item-header">
                                                        <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                                                        <span class="price-tag badge bg-success">
                                                            $<?php echo number_format($item['price'], 2); ?>
                                                        </span>
                                                    </div>
                                                    
                                                    <p class="card-text text-muted small">
                                                        <?php echo htmlspecialchars($item['description']); ?>
                                                    </p>
                                                    
                                                    <div class="menu-item-meta">
                                                        <?php if (!empty($item['preparation_time'])): ?>
                                                            <span class="meta-item">
                                                                <i class="fas fa-clock"></i>
                                                                <?php echo $item['preparation_time']; ?> min
                                                            </span>
                                                        <?php endif; ?>
                                                        
                                                        <?php if (!empty($item['calories'])): ?>
                                                            <span class="meta-item">
                                                                <i class="fas fa-fire"></i>
                                                                <?php echo $item['calories']; ?> cal
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                    
                                                    <?php if (!empty($item['allergens'])): ?>
                                                        <div class="allergens-badge mt-2">
                                                            <small class="text-muted">
                                                                <strong>Allergens:</strong> <?php echo htmlspecialchars($item['allergens']); ?>
                                                            </small>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <div class="card-footer bg-transparent">
                                                    <div class="d-grid gap-2">
                                                        <button class="btn btn-primary add-to-cart-btn" 
                                                                data-item-id="<?php echo $item['id']; ?>"
                                                                data-item-name="<?php echo htmlspecialchars($item['name']); ?>"
                                                                data-item-price="<?php echo $item['price']; ?>"
                                                                data-item-branch-id="<?php echo $branch['id']; ?>">
                                                            <i class="fas fa-cart-plus"></i> Add to Cart
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="alert alert-info text-center">
                                    <h4>No Menu Items Available</h4>
                                    <p>There are no menu items available at this branch at the moment.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Category Tabs -->
                <?php foreach ($categories as $category): ?>
                    <div class="tab-pane fade" id="category-<?php echo $category['id']; ?>" role="tabpanel">
                        <div class="row g-4">
                            <?php if (!empty($category['items'])): ?>
                                <?php foreach ($category['items'] as $item): ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="menu-item-card card h-100">
                                            <?php if (!empty($item['image_url'])): ?>
                                                <div class="menu-item-image">
                                                    <img src="<?php echo asset('uploads/' . $item['image_url']); ?>" 
                                                         class="card-img-top" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="card-body">
                                                <div class="menu-item-header">
                                                    <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                                                    <span class="price-tag badge bg-success">
                                                        $<?php echo number_format($item['price'], 2); ?>
                                                    </span>
                                                </div>
                                                
                                                <p class="card-text text-muted small">
                                                    <?php echo htmlspecialchars($item['description']); ?>
                                                </p>
                                                
                                                <div class="menu-item-meta">
                                                    <?php if (!empty($item['preparation_time'])): ?>
                                                        <span class="meta-item">
                                                            <i class="fas fa-clock"></i>
                                                            <?php echo $item['preparation_time']; ?> min
                                                        </span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($item['calories'])): ?>
                                                        <span class="meta-item">
                                                            <i class="fas fa-fire"></i>
                                                            <?php echo $item['calories']; ?> cal
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <?php if (!empty($item['allergens'])): ?>
                                                    <div class="allergens-badge mt-2">
                                                        <small class="text-muted">
                                                            <strong>Allergens:</strong> <?php echo htmlspecialchars($item['allergens']); ?>
                                                        </small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="card-footer bg-transparent">
                                                <div class="d-grid gap-2">
                                                    <button class="btn btn-primary add-to-cart-btn" 
                                                            data-item-id="<?php echo $item['id']; ?>"
                                                            data-item-name="<?php echo htmlspecialchars($item['name']); ?>"
                                                            data-item-price="<?php echo $item['price']; ?>"
                                                            data-item-branch-id="<?php echo $branch['id']; ?>">
                                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <div class="alert alert-info text-center">
                                        <h4>No Items in This Category</h4>
                                        <p>There are no menu items available in this category at the moment.</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Checkout Section -->
    <div class="checkout-section py-4">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="cart-summary">
                            <span class="cart-count badge bg-primary" id="menuCartCount">0</span>
                            <span class="cart-total ms-2">Total: $<span id="menuCartTotal">0.00</span></span>
                        </div>
                        <button class="btn btn-success btn-lg" id="menuCheckoutBtn" disabled>
                            <i class="fas fa-credit-card"></i> Proceed to Checkout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Table Selection Section -->
    <div class="table-selection-section py-3" id="tableSelectionSection" style="display: none;">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chair"></i> Select Order Type
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Order Type</label>
                                <div class="order-type-options">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="orderType" id="dineInRadio" value="dine_in" checked>
                                        <label class="form-check-label" for="dineInRadio">
                                            <i class="fas fa-utensils"></i> Dine In
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="orderType" id="takeawayRadio" value="takeaway">
                                        <label class="form-check-label" for="takeawayRadio">
                                            <i class="fas fa-shopping-bag"></i> Takeaway
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3" id="tableSelection">
                                <label for="tableSelect" class="form-label">Table</label>
                                <select class="form-select" id="tableSelect">
                                    <option value="<?php echo htmlspecialchars($qr_data['table_number']); ?>" selected>
                                        Table <?php echo htmlspecialchars($qr_data['table_number']); ?>
                                    </option>
                                </select>
                                <small class="form-text text-muted">Your order will be served at this table.</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary flex-fill" id="confirmTableSelection">
                                    <i class="fas fa-check"></i> Confirm Order
                                </button>
                                <button class="btn btn-secondary flex-fill" id="cancelTableSelection">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table-info-card {
    border-left: 4px solid #28a745;
}

.table-info-card .card-header {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
}

.table-info-card .card-body {
    background-color: #f8f9fa;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Order type selection functionality
    const orderTypeRadios = document.querySelectorAll('input[name="orderType"]');
    const tableSelection = document.getElementById('tableSelection');
    const tableSelect = document.getElementById('tableSelect');
    
    // Get selected order type
    function getSelectedOrderType() {
        const selected = document.querySelector('input[name="orderType"]:checked');
        return selected ? selected.value : 'dine_in';
    }
    
    // Handle order type change
    orderTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const orderType = this.value;
            if (orderType === 'dine_in') {
                tableSelection.style.display = 'block';
            } else {
                tableSelection.style.display = 'none';
            }
        });
    });
    
    // Update menu page cart display
    function updateMenuCartDisplay() {
        const cartCount = document.getElementById('menuCartCount');
        const cartTotal = document.getElementById('menuCartTotal');
        const checkoutBtn = document.getElementById('menuCheckoutBtn');
        
        if (!cartCount || !cartTotal || !checkoutBtn) return;
        
        // Get cart from localStorage
        const savedCart = localStorage.getItem('restaurant_cart');
        const cart = savedCart ? JSON.parse(savedCart) : [];
        
        if (cart.length > 0) {
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const totalAmount = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            cartCount.textContent = totalItems;
            cartTotal.textContent = totalAmount.toFixed(2);
            checkoutBtn.disabled = false;
        } else {
            cartCount.textContent = '0';
            cartTotal.textContent = '0.00';
            checkoutBtn.disabled = true;
        }
    }
    
    // Handle checkout button click
    const checkoutBtn = document.getElementById('menuCheckoutBtn');
    const tableSelectionSection = document.getElementById('tableSelectionSection');
    
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
            // Show table selection section
            tableSelectionSection.style.display = 'block';
            tableSelectionSection.scrollIntoView({ behavior: 'smooth' });
        });
    }
    
    // Handle confirm table selection
    const confirmTableSelection = document.getElementById('confirmTableSelection');
    if (confirmTableSelection) {
        confirmTableSelection.addEventListener('click', function() {
            const orderType = getSelectedOrderType();
            const tableId = tableSelect.value;
            
            // Validate selection
            if (orderType === 'dine_in' && !tableId) {
                alert('Please select a table for dine-in orders.');
                return;
            }
            
            // Get cart data and save checkout data
            const savedCart = localStorage.getItem('restaurant_cart');
            const cart = savedCart ? JSON.parse(savedCart) : [];
            
            if (cart.length > 0) {
                // Get QR code and table information from hidden inputs
                const qrCode = document.getElementById('qrCode');
                const qrCodeValue = qrCode ? qrCode.value : null;
                const branchId = document.getElementById('selectedBranch');
                const branchIdValue = branchId ? branchId.value : '1';
                
                // Prepare checkout data
                const checkoutData = {
                    cart: cart,
                    tableNumber: orderType === 'dine_in' ? tableId : null,
                    branchId: branchIdValue,
                    qrCode: qrCodeValue,
                    orderType: orderType,
                    timestamp: new Date().toISOString()
                };
                
                // Save checkout data to localStorage
                localStorage.setItem('checkout_data', JSON.stringify(checkoutData));
                localStorage.setItem('checkout_cart', JSON.stringify(cart));
                
                // Redirect to checkout page
                const baseUrl = window.baseUrl || '/restaurant-menu-system';
                window.location.href = baseUrl + '/checkout';
            }
        });
    }
    
    // Handle cancel table selection
    const cancelTableSelection = document.getElementById('cancelTableSelection');
    if (cancelTableSelection) {
        cancelTableSelection.addEventListener('click', function() {
            tableSelectionSection.style.display = 'none';
            document.getElementById('dineInRadio').checked = true;
            tableSelection.style.display = 'block';
            tableSelect.value = '';
        });
    }
    
    // Update cart display when cart changes
    function updateCartDisplay() {
        updateMenuCartDisplay();
    }
    
    // Initial update
    updateMenuCartDisplay();
    
    // Listen for storage changes to update cart display
    window.addEventListener('storage', function(e) {
        if (e.key === 'restaurant_cart') {
            updateMenuCartDisplay();
        }
    });
});
</script>