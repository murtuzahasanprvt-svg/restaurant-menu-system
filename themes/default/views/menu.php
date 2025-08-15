<div class="menu-page py-5">
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
                        <?php if (!empty($menuItems)): ?>
                            <?php foreach ($menuItems as $item): ?>
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
                                                    <?php echo format_price($item['price']); ?>
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
                                                        data-item-price="<?php echo $item['price']; ?>">
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
                            <?php 
                            $categoryItems = array_filter($menuItems, function($item) use ($category) {
                                return $item['category_id'] == $category['id'];
                            });
                            ?>
                            
                            <?php if (!empty($categoryItems)): ?>
                                <?php foreach ($categoryItems as $item): ?>
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
                                                        <?php echo format_price($item['price']); ?>
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
                                                            data-item-price="<?php echo $item['price']; ?>">
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
</div>

<!-- Shopping Cart Sidebar -->
<div class="cart-sidebar" id="cartSidebar">
    <div class="cart-header">
        <h5>Shopping Cart</h5>
        <button type="button" class="btn-close" id="closeCartSidebar"></button>
    </div>
    
    <div class="cart-body">
        <div class="cart-items" id="cartItems">
            <p class="text-muted text-center">Your cart is empty</p>
        </div>
    </div>
    
    <div class="cart-footer">
        <div class="cart-total">
            <strong>Total: <span id="cartTotal">$0.00</span></strong>
        </div>
        <button class="btn btn-primary w-100" id="checkoutBtn" disabled>
            Proceed to Checkout
        </button>
    </div>
</div>

<!-- Cart Toggle Button -->
<button class="cart-toggle-btn" id="cartToggleBtn">
    <i class="fas fa-shopping-cart"></i>
    <span class="cart-count" id="cartCount">0</span>
</button>