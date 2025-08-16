<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($branch['name']); ?> - Menu</title>
    <style>
        /* This makes the sidebar 'stick' to the top as the user scrolls */
        .sidebar-sticky {
            position: -webkit-sticky;
            position: sticky;
            top: 1.5rem; /* Adjust this value based on your navbar's height */
        }

        /* Minor styling improvements for the menu item cards */
        .menu-item-card .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .price-tag {
            font-size: 1rem;
        }
    </style>
</head>
<body>

<div class="menu-page py-5 bg-light">
    <div class="container">
        <div class="row g-5">

            <div class="col-lg-8">
                <div class="branch-header mb-5">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($branch['name']); ?></h1>
                            <p class="lead text-muted mb-2"><?php echo htmlspecialchars($branch['address']); ?></p>
                            <div class="branch-meta">
                                <span class="badge bg-primary me-2">
                                    <i class="fas fa-phone me-1"></i> <?php echo htmlspecialchars($branch['phone']); ?>
                                </span>
                                <?php if (!empty($branch['email'])) : ?>
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-envelope me-1"></i> <?php echo htmlspecialchars($branch['email']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <a href="<?php echo url('/branches'); ?>" class="btn btn-outline-secondary flex-shrink-0 ms-3">
                            <i class="fas fa-arrow-left me-1"></i> Back to Branches
                        </a>
                    </div>
                </div>

                <div class="menu-categories mb-4">
                    <h3 class="mb-3">Menu Categories</h3>
                    <ul class="nav nav-pills" id="categoryTabs" role="tablist">
                        <li class="nav-item me-2 mb-2" role="presentation">
                            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-items" type="button" role="tab">All Items</button>
                        </li>
                        <?php foreach ($categories as $category) : ?>
                            <li class="nav-item me-2 mb-2" role="presentation">
                                <button class="nav-link" id="category-<?php echo $category['id']; ?>-tab" data-bs-toggle="tab" data-bs-target="#category-pane-<?php echo $category['id']; ?>" type="button" role="tab"><?php echo htmlspecialchars($category['name']); ?></button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="tab-content" id="categoryTabContent">
                    <div class="tab-pane fade show active" id="all-items" role="tabpanel">
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-4">
                            <?php if (!empty($menuItems)) : ?>
                                <?php foreach ($menuItems as $item) : ?>
                                    <div class="col">
                                        <div class="card h-100 menu-item-card shadow-sm border-0">
                                            <?php if (!empty($item['image_url'])) : ?>
                                                <img src="<?php echo asset('uploads/' . $item['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                            <?php endif; ?>
                                            <div class="card-body d-flex flex-column">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($item['name']); ?></h5>
                                                    <span class="price-tag badge bg-success ms-2">$<?php echo number_format($item['price'], 2); ?></span>
                                                </div>
                                                <p class="card-text text-muted small flex-grow-1"><?php echo htmlspecialchars($item['description']); ?></p>
                                                <div class="menu-item-meta d-flex justify-content-start gap-3 text-muted small mb-2">
                                                    <?php if (!empty($item['preparation_time'])) : ?><span><i class="fas fa-clock me-1"></i> <?php echo $item['preparation_time']; ?> min</span><?php endif; ?>
                                                    <?php if (!empty($item['calories'])) : ?><span><i class="fas fa-fire me-1"></i> <?php echo $item['calories']; ?> cal</span><?php endif; ?>
                                                </div>
                                                <?php if (!empty($item['allergens'])): ?>
                                                    <div class="allergens-badge mt-2"><small class="text-muted"><strong>Allergens:</strong> <?php echo htmlspecialchars($item['allergens']); ?></small></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-footer bg-white border-0 pt-0 pb-3">
                                                <div class="d-grid">
                                                    <button class="btn btn-primary add-to-cart-btn" data-item-id="<?php echo $item['id']; ?>" data-item-name="<?php echo htmlspecialchars($item['name']); ?>" data-item-price="<?php echo $item['price']; ?>" data-item-branch-id="<?php echo $branch['id']; ?>">
                                                        <i class="fas fa-cart-plus me-2"></i> Add to Cart
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <div class="col-12">
                                    <div class="alert alert-info">No menu items found for this branch.</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php foreach ($categories as $category) : ?>
                        <div class="tab-pane fade" id="category-pane-<?php echo $category['id']; ?>" role="tabpanel">
                            <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-4">
                                <?php $categoryItems = array_filter($menuItems, fn ($item) => $item['category_id'] == $category['id']); ?>
                                <?php if (!empty($categoryItems)) : ?>
                                    <?php foreach ($categoryItems as $item) : ?>
                                        <div class="col">
                                            <div class="card h-100 menu-item-card shadow-sm border-0">
                                                <?php if (!empty($item['image_url'])) : ?>
                                                    <img src="<?php echo asset('uploads/' . $item['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                                <?php endif; ?>
                                                <div class="card-body d-flex flex-column">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h5 class="card-title mb-0"><?php echo htmlspecialchars($item['name']); ?></h5>
                                                        <span class="price-tag badge bg-success ms-2">$<?php echo number_format($item['price'], 2); ?></span>
                                                    </div>
                                                    <p class="card-text text-muted small flex-grow-1"><?php echo htmlspecialchars($item['description']); ?></p>
                                                    <div class="menu-item-meta d-flex justify-content-start gap-3 text-muted small mb-2">
                                                        <?php if (!empty($item['preparation_time'])) : ?><span><i class="fas fa-clock me-1"></i> <?php echo $item['preparation_time']; ?> min</span><?php endif; ?>
                                                        <?php if (!empty($item['calories'])) : ?><span><i class="fas fa-fire me-1"></i> <?php echo $item['calories']; ?> cal</span><?php endif; ?>
                                                    </div>
                                                    <?php if (!empty($item['allergens'])): ?>
                                                        <div class="allergens-badge mt-2"><small class="text-muted"><strong>Allergens:</strong> <?php echo htmlspecialchars($item['allergens']); ?></small></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="card-footer bg-white border-0 pt-0 pb-3">
                                                    <div class="d-grid">
                                                        <button class="btn btn-primary add-to-cart-btn" data-item-id="<?php echo $item['id']; ?>" data-item-name="<?php echo htmlspecialchars($item['name']); ?>" data-item-price="<?php echo $item['price']; ?>" data-item-branch-id="<?php echo $branch['id']; ?>">
                                                            <i class="fas fa-cart-plus me-2"></i> Add to Cart
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <div class="col-12">
                                        <div class="alert alert-secondary">No items found in this category.</div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sidebar-sticky">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3">
                            <h4 class="mb-0"><i class="fas fa-shopping-basket me-2 text-primary"></i>Your Order</h4>
                        </div>
                        <div class="card-body p-4">

                            <div id="cartSummaryView">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0 text-muted">Total:</h5>
                                    <span class="fs-4 fw-bold text-success" id="menuCartTotal">$0.00</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-4 text-muted">
                                    <h6 class="mb-0">Items:</h6>
                                    <span class="fw-bold" id="menuCartCount">0</span>
                                </div>
                                <div class="d-grid">
                                    <button class="btn btn-success btn-lg" id="menuCheckoutBtn" disabled>
                                        <i class="fas fa-credit-card me-2"></i> Proceed to Checkout
                                    </button>
                                </div>
                            </div>

                            <div id="tableSelectionView" style="display: none;">
                                <h5 class="mb-3">Confirm Order Details</h5>
                                <div class="mb-3">
                                    <label for="orderTypeSelect" class="form-label">Order Type</label>
                                    <select class="form-select" id="orderTypeSelect">
                                        <option value="dine_in">Dine In</option>
                                        <option value="takeaway">Takeaway</option>
                                        <option value="delivery">Delivery</option>
                                    </select>
                                </div>
                                <div class="mb-3" id="tableSelectionGroup">
                                    <label for="tableSelect" class="form-label">Select Table</label>
                                    <select class="form-select" id="tableSelect">
                                        <option value="">Select a table...</option>
                                        <?php
                                        // Fetch tables for the current branch
                                        $db = Database::getInstance();
                                        $db->query("SELECT * FROM tables WHERE branch_id = :branch_id AND is_active = 1 ORDER BY table_number");
                                        $db->bind(':branch_id', $branch['id']);
                                        $tables = $db->resultSet();
                                        ?>
                                        <?php foreach ($tables as $table) : ?>
                                            <option value="<?php echo $table['id']; ?>">
                                                Table <?php echo htmlspecialchars($table['table_number']); ?>
                                                <?php if (!empty($table['description'])) : ?> (<?php echo htmlspecialchars($table['description']); ?>)<?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary" id="confirmOrderBtn"><i class="fas fa-check me-2"></i> Confirm & Checkout</button>
                                    <button type="button" class="btn btn-outline-secondary" id="cancelSelectionBtn"><i class="fas fa-times me-2"></i> Cancel</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Element References ---
    const cartCountEl = document.getElementById('menuCartCount');
    const cartTotalEl = document.getElementById('menuCartTotal');
    const checkoutBtn = document.getElementById('menuCheckoutBtn');
    
    const cartSummaryView = document.getElementById('cartSummaryView');
    const tableSelectionView = document.getElementById('tableSelectionView');

    const orderTypeSelect = document.getElementById('orderTypeSelect');
    const tableSelectionGroup = document.getElementById('tableSelectionGroup');
    const tableSelect = document.getElementById('tableSelect');
    
    const confirmOrderBtn = document.getElementById('confirmOrderBtn');
    const cancelSelectionBtn = document.getElementById('cancelSelectionBtn');

    // --- Cart Update Function ---
    // This function reads from localStorage and updates the sidebar display.
    // It is called on page load and whenever the cart is modified.
    function updateCartDisplay() {
        const savedCart = localStorage.getItem('restaurant_cart');
        const cart = savedCart ? JSON.parse(savedCart) : [];

        if (cart.length > 0) {
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const totalAmount = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

            cartCountEl.textContent = totalItems;
            cartTotalEl.textContent = '$' + totalAmount.toFixed(2);
            checkoutBtn.disabled = false;
        } else {
            cartCountEl.textContent = '0';
            cartTotalEl.textContent = '$0.00';
            checkoutBtn.disabled = true;
        }
    }

    // --- UI State Transitions ---
    function showTableSelection() {
        cartSummaryView.style.display = 'none';
        tableSelectionView.style.display = 'block';
    }

    function showCartSummary() {
        tableSelectionView.style.display = 'none';
        cartSummaryView.style.display = 'block';
    }

    // --- Event Listeners ---
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', showTableSelection);
    }

    if (cancelSelectionBtn) {
        cancelSelectionBtn.addEventListener('click', showCartSummary);
    }
    
    if (orderTypeSelect) {
        orderTypeSelect.addEventListener('change', function() {
            tableSelectionGroup.style.display = (this.value === 'dine_in') ? 'block' : 'none';
        });
    }

    if (confirmOrderBtn) {
        confirmOrderBtn.addEventListener('click', function() {
            const orderType = orderTypeSelect.value;
            const tableId = tableSelect.value;

            if (orderType === 'dine_in' && !tableId) {
                alert('Please select a table for your dine-in order.');
                return;
            }

            const savedCart = localStorage.getItem('restaurant_cart');
            const cart = savedCart ? JSON.parse(savedCart) : [];

            if (cart.length > 0) {
                const urlParams = new URLSearchParams(window.location.search);
                const branchId = urlParams.get('branch_id') || urlParams.get('branch') || '1'; // Fallback
                
                const checkoutData = {
                    cart: cart,
                    tableNumber: orderType === 'dine_in' ? tableId : null,
                    branchId: branchId,
                    orderType: orderType,
                    timestamp: new Date().toISOString()
                };

                localStorage.setItem('checkout_data', JSON.stringify(checkoutData));
                localStorage.setItem('checkout_cart', JSON.stringify(cart));
                
                const baseUrl = window.baseUrl || '/restaurant-menu-system'; 
                window.location.href = baseUrl + '/checkout';
            }
        });
    }

    // --- Initial Load & Global Listener ---
    updateCartDisplay();

    // Listen for storage events to keep the cart updated across tabs
    window.addEventListener('storage', function(e) {
        if (e.key === 'restaurant_cart') {
            updateCartDisplay();
        }
    });
    
    // NOTE: Make sure your "Add to Cart" button's click handler dispatches this custom event
    // after it modifies localStorage, e.g., document.dispatchEvent(new Event('cartUpdated'));
    // This ensures the display updates instantly without a page reload.
    document.addEventListener('cartUpdated', updateCartDisplay);
});
</script>

</body>
</html>