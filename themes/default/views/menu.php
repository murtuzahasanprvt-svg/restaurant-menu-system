<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($branch['name']); ?> | Culinary Excellence</title>
    
    <!-- Typography & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900&family=Poppins:wght@200;300;400;500;600;700;800&family=Tenor+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
</head>
<body>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title"><?php echo htmlspecialchars($branch['name']); ?></h1>
                <p class="hero-subtitle">An extraordinary culinary journey awaits</p>
                
                <div class="restaurant-info">
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo htmlspecialchars($branch['address']); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <span><?php echo htmlspecialchars($branch['phone']); ?></span>
                    </div>
                    <?php if (!empty($branch['email'])) : ?>
                        <div class="info-item">
                            <i class="fas fa-envelope"></i>
                            <span><?php echo htmlspecialchars($branch['email']); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Floating Navigation -->
    <nav class="floating-nav" id="floatingNav">
        <div class="nav-container">
            <a href="#" class="logo">RM</a>
            
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Discover our menu..." id="searchInput">
            </div>
            
            <div class="category-pills" id="categoryTabs">
                <button class="category-pill active" data-category="popular">
                    <i class="fas fa-fire"></i> Popular
                </button>
                <button class="category-pill" data-category="all">
                    <i class="fas fa-th"></i> All
                </button>
                <?php foreach ($categories as $category) : ?>
                    <button class="category-pill" data-category="<?php echo $category['id']; ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 60px;">
                <h2 class="section-title">Popular Selections</h2>
                <p class="subtitle">Curated by our chefs, loved by our guests</p>
            </div>

            <div class="menu-grid" id="menuGrid">
                <?php 
                // Get popular items (featured items) from database
                $popularItems = array_filter($menuItems, fn ($item) => $item['is_featured'] == 1);
                if (empty($popularItems)) {
                    // If no featured items, show first 6 items as popular
                    $popularItems = array_slice($menuItems, 0, 6);
                }
                
                foreach ($popularItems as $item) : 
                ?>
                    <div class="menu-card fade-in-up" data-category="popular" data-item-id="<?php echo $item['id']; ?>">
                        <?php if (!empty($item['image_url'])) : ?>
                            <img src="<?php echo asset('uploads/' . $item['image_url']); ?>" class="menu-image" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <?php else : ?>
                            <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" class="menu-image" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <?php endif; ?>
                        
                        <div class="menu-content">
                            <div class="menu-header">
                                <h3 class="menu-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <span class="menu-price">Tk <?php echo number_format($item['price'], 0); ?></span>
                            </div>
                            
                            <p class="menu-description"><?php echo htmlspecialchars($item['description']); ?></p>
                            
                            <div class="menu-meta">
                                <?php if (!empty($item['preparation_time'])) : ?>
                                    <div class="meta-item">
                                        <i class="fas fa-clock"></i>
                                        <span><?php echo $item['preparation_time']; ?> min</span>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($item['calories'])) : ?>
                                    <div class="meta-item">
                                        <i class="fas fa-fire"></i>
                                        <span><?php echo $item['calories']; ?> cal</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="menu-footer">
                                <div class="menu-tags">
                                    <?php if ($item['is_featured']) : ?>
                                        <span class="menu-tag">Chef's Choice</span>
                                    <?php endif; ?>
                                    <?php if ($item['is_available']) : ?>
                                        <span class="menu-tag">Available</span>
                                    <?php endif; ?>
                                </div>
                                
                                <button class="add-to-order" 
                                        data-item-id="<?php echo $item['id']; ?>" 
                                        data-item-name="<?php echo htmlspecialchars($item['name']); ?>" 
                                        data-item-price="<?php echo $item['price']; ?>" 
                                        data-item-branch-id="<?php echo $branch['id']; ?>">
                                    <i class="fas fa-plus"></i>
                                    Add
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- All Items (hidden by default) -->
                <?php foreach ($menuItems as $item) : ?>
                    <div class="menu-card" data-category="all" data-item-id="<?php echo $item['id']; ?>" style="display: none;">
                        <?php if (!empty($item['image_url'])) : ?>
                            <img src="<?php echo asset('uploads/' . $item['image_url']); ?>" class="menu-image" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <?php else : ?>
                            <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" class="menu-image" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <?php endif; ?>
                        
                        <div class="menu-content">
                            <div class="menu-header">
                                <h3 class="menu-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <span class="menu-price">Tk <?php echo number_format($item['price'], 0); ?></span>
                            </div>
                            
                            <p class="menu-description"><?php echo htmlspecialchars($item['description']); ?></p>
                            
                            <div class="menu-meta">
                                <?php if (!empty($item['preparation_time'])) : ?>
                                    <div class="meta-item">
                                        <i class="fas fa-clock"></i>
                                        <span><?php echo $item['preparation_time']; ?> min</span>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($item['calories'])) : ?>
                                    <div class="meta-item">
                                        <i class="fas fa-fire"></i>
                                        <span><?php echo $item['calories']; ?> cal</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="menu-footer">
                                <div class="menu-tags">
                                    <?php if ($item['is_featured']) : ?>
                                        <span class="menu-tag">Chef's Choice</span>
                                    <?php endif; ?>
                                    <?php if ($item['is_available']) : ?>
                                        <span class="menu-tag">Available</span>
                                    <?php endif; ?>
                                </div>
                                
                                <button class="add-to-order" 
                                        data-item-id="<?php echo $item['id']; ?>" 
                                        data-item-name="<?php echo htmlspecialchars($item['name']); ?>" 
                                        data-item-price="<?php echo $item['price']; ?>" 
                                        data-item-branch-id="<?php echo $branch['id']; ?>">
                                    <i class="fas fa-plus"></i>
                                    Add
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Category Items (hidden by default) -->
                <?php foreach ($categories as $category) : ?>
                    <?php $categoryItems = array_filter($menuItems, fn ($item) => $item['category_id'] == $category['id']); ?>
                    <?php foreach ($categoryItems as $item) : ?>
                        <div class="menu-card" data-category="<?php echo $category['id']; ?>" data-item-id="<?php echo $item['id']; ?>" style="display: none;">
                            <?php if (!empty($item['image_url'])) : ?>
                                <img src="<?php echo asset('uploads/' . $item['image_url']); ?>" class="menu-image" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <?php else : ?>
                                <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" class="menu-image" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <?php endif; ?>
                            
                            <div class="menu-content">
                                <div class="menu-header">
                                    <h3 class="menu-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <span class="menu-price">Tk <?php echo number_format($item['price'], 0); ?></span>
                                </div>
                                
                                <p class="menu-description"><?php echo htmlspecialchars($item['description']); ?></p>
                                
                                <div class="menu-meta">
                                    <?php if (!empty($item['preparation_time'])) : ?>
                                        <div class="meta-item">
                                            <i class="fas fa-clock"></i>
                                            <span><?php echo $item['preparation_time']; ?> min</span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($item['calories'])) : ?>
                                        <div class="meta-item">
                                            <i class="fas fa-fire"></i>
                                            <span><?php echo $item['calories']; ?> cal</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="menu-footer">
                                    <div class="menu-tags">
                                        <?php if ($item['is_featured']) : ?>
                                            <span class="menu-tag">Chef's Choice</span>
                                        <?php endif; ?>
                                        <?php if ($item['is_available']) : ?>
                                            <span class="menu-tag">Available</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <button class="add-to-order" 
                                            data-item-id="<?php echo $item['id']; ?>" 
                                            data-item-name="<?php echo htmlspecialchars($item['name']); ?>" 
                                            data-item-price="<?php echo $item['price']; ?>" 
                                            data-item-branch-id="<?php echo $branch['id']; ?>">
                                        <i class="fas fa-plus"></i>
                                        Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>

            <div class="empty-state" id="emptyState" style="display: none;">
                <i class="fas fa-search"></i>
                <h3>No items found</h3>
                <p>Try adjusting your search or browse another category</p>
            </div>
        </div>
    </main>

    <!-- Cart Sidebar -->
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <button class="cart-close" id="cartClose">
                <i class="fas fa-times"></i>
            </button>
            <div class="cart-icon">
                <i class="fas fa-shopping-basket"></i>
            </div>
            <h3 class="cart-title">Your Order</h3>
        </div>
        
        <div class="cart-body">
            <div class="order-type-selector">
                <button class="order-type-btn active" data-order-type="dine_in">
                    <i class="fas fa-utensils"></i>
                    Dine-in
                </button>
                <button class="order-type-btn" data-order-type="takeaway">
                    <i class="fas fa-shopping-bag"></i>
                    Takeaway
                </button>
                <button class="order-type-btn" data-order-type="delivery">
                    <i class="fas fa-truck"></i>
                    Delivery
                </button>
            </div>

            <div id="cartSummaryView">
                <div class="cart-items" id="cartItems">
                    <!-- Cart items will be dynamically inserted here -->
                </div>
                
                <div class="cart-summary">
                    <div class="summary-row">
                        <span class="summary-label">Subtotal</span>
                        <span class="summary-value" id="subtotalValue">Tk 0</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Tax & Fees (8.5%)</span>
                        <span class="summary-value" id="taxValue">Tk 0</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Total</span>
                        <span class="summary-value" id="totalValue">Tk 0</span>
                    </div>
                </div>
                
                <div class="cart-actions">
                    <button class="cart-btn cart-btn-primary" id="checkoutBtn" disabled>
                        <i class="fas fa-credit-card"></i> Proceed to Checkout
                    </button>
                    <button class="cart-btn cart-btn-secondary" id="clearCartBtn">
                        <i class="fas fa-trash"></i> Clear Cart
                    </button>
                </div>
            </div>

            <div id="tableSelectionView" class="table-selection">
                <div class="form-group">
                    <label class="form-label">Order Type</label>
                    <select class="form-select" id="orderTypeSelect">
                        <option value="dine_in">Dine In</option>
                        <option value="takeaway">Takeaway</option>
                        <option value="delivery">Delivery</option>
                    </select>
                </div>
                
                <div class="form-group" id="tableSelectionGroup">
                    <label class="form-label">Select Table</label>
                    <select class="form-select" id="tableSelect">
                        <option value="">Choose your table...</option>
                        <?php
                        // Fetch tables for the current branch from database
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
                
                <div class="button-group">
                    <button class="btn btn-primary" id="confirmOrderBtn">
                        <i class="fas fa-check"></i> Confirm Order
                    </button>
                    <button class="btn btn-secondary" id="cancelSelectionBtn">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Toggle Button -->
    <button class="cart-toggle" id="cartToggle">
        <i class="fas fa-shopping-basket"></i>
        <span class="cart-count" id="cartCount">0</span>
    </button>

    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Element References ---
        const menuGrid = document.getElementById('menuGrid');
        const emptyState = document.getElementById('emptyState');
        const searchInput = document.getElementById('searchInput');
        const categoryTabs = document.querySelectorAll('.category-pill');
        const menuCards = document.querySelectorAll('.menu-card');
        const floatingNav = document.getElementById('floatingNav');
        
        // Cart elements
        const cartSidebar = document.getElementById('cartSidebar');
        const cartToggle = document.getElementById('cartToggle');
        const cartClose = document.getElementById('cartClose');
        const overlay = document.getElementById('overlay');
        const cartCount = document.getElementById('cartCount');
        const cartItems = document.getElementById('cartItems');
        const subtotalValue = document.getElementById('subtotalValue');
        const taxValue = document.getElementById('taxValue');
        const totalValue = document.getElementById('totalValue');
        const checkoutBtn = document.getElementById('checkoutBtn');
        const clearCartBtn = document.getElementById('clearCartBtn');
        
        // Cart views
        const cartSummaryView = document.getElementById('cartSummaryView');
        const tableSelectionView = document.getElementById('tableSelectionView');
        
        // Order type elements
        const orderTypeBtns = document.querySelectorAll('.order-type-btn');
        const orderTypeSelect = document.getElementById('orderTypeSelect');
        const tableSelectionGroup = document.getElementById('tableSelectionGroup');
        const tableSelect = document.getElementById('tableSelect');
        const confirmOrderBtn = document.getElementById('confirmOrderBtn');
        const cancelSelectionBtn = document.getElementById('cancelSelectionBtn');

        // Constants
        const TAX_RATE = 0.085; // 8.5% tax

        // --- Scroll Effect for Navigation ---
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                floatingNav.classList.add('scrolled');
            } else {
                floatingNav.classList.remove('scrolled');
            }
        });

        // --- Cart Management ---
        function getCart() {
            const savedCart = localStorage.getItem('restaurant_cart');
            return savedCart ? JSON.parse(savedCart) : [];
        }

        function saveCart(cart) {
            localStorage.setItem('restaurant_cart', JSON.stringify(cart));
            updateCartDisplay();
            updateCartToggle();
            document.dispatchEvent(new Event('cartUpdated'));
        }

        function updateCartDisplay() {
            const cart = getCart();
            
            // Update cart count
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            cartCount.textContent = totalItems;
            
            // Update cart items display
            if (cart.length === 0) {
                cartItems.innerHTML = '<p style="text-align: center; color: var(--text-tertiary); padding: 20px;">Your cart is empty</p>';
                subtotalValue.textContent = 'Tk 0';
                taxValue.textContent = 'Tk 0';
                totalValue.textContent = 'Tk 0';
                checkoutBtn.disabled = true;
                return;
            }

            // Render cart items
            cartItems.innerHTML = cart.map(item => `
                <div class="cart-item">
                    <div class="cart-item-info">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">Tk ${item.price} × ${item.quantity}</div>
                    </div>
                    <div class="cart-item-quantity">
                        <button class="quantity-btn" onclick="updateQuantity(${item.id}, -1)">-</button>
                        <span>${item.quantity}</span>
                        <button class="quantity-btn" onclick="updateQuantity(${item.id}, 1)">+</button>
                    </div>
                </div>
            `).join('');

            // Calculate totals
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const tax = subtotal * TAX_RATE;
            const total = subtotal + tax;

            subtotalValue.textContent = `Tk ${subtotal.toFixed(0)}`;
            taxValue.textContent = `Tk ${tax.toFixed(0)}`;
            totalValue.textContent = `Tk ${total.toFixed(0)}`;
            
            checkoutBtn.disabled = false;
        }

        function updateCartToggle() {
            const cart = getCart();
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            
            cartCount.textContent = totalItems;
            cartCount.style.display = totalItems > 0 ? 'flex' : 'none';
            
            if (totalItems > 0) {
                cartToggle.classList.add('pulse');
                setTimeout(() => cartToggle.classList.remove('pulse'), 1000);
            }
        }

        function addToCart(itemId, itemName, itemPrice, branchId) {
            const cart = getCart();
            const existingItem = cart.find(item => item.id === itemId);
            
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id: itemId,
                    name: itemName,
                    price: parseFloat(itemPrice),
                    branch_id: branchId,
                    quantity: 1
                });
            }
            
            saveCart(cart);
            
            // Show visual feedback
            const button = document.querySelector(`[data-item-id="${itemId}"]`);
            if (button) {
                button.classList.add('added');
                button.innerHTML = '<i class="fas fa-check"></i> Added';
                setTimeout(() => {
                    button.classList.remove('added');
                    button.innerHTML = '<i class="fas fa-plus"></i> Add';
                }, 1500);
            }
        }

        // Global function for quantity buttons
        window.updateQuantity = function(itemId, change) {
            const cart = getCart();
            const item = cart.find(item => item.id === itemId);
            
            if (item) {
                item.quantity += change;
                if (item.quantity <= 0) {
                    cart.splice(cart.indexOf(item), 1);
                }
                saveCart(cart);
            }
        };

        // --- Category and Search Functions ---
        function filterItems() {
            const activeCategory = document.querySelector('.category-pill.active').dataset.category;
            const searchTerm = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;

            menuCards.forEach(card => {
                const matchesCategory = card.dataset.category === activeCategory;
                const title = card.querySelector('.menu-title').textContent.toLowerCase();
                const description = card.querySelector('.menu-description').textContent.toLowerCase();
                const matchesSearch = searchTerm === '' || title.includes(searchTerm) || description.includes(searchTerm);
                
                if (matchesCategory && matchesSearch) {
                    card.style.display = 'block';
                    card.classList.add('fade-in-up');
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Update section title
            updateSectionTitle(activeCategory);
            
            // Show/hide empty state
            emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
            menuGrid.style.display = visibleCount === 0 ? 'none' : 'grid';
        }

        function updateSectionTitle(category) {
            const sectionTitle = document.querySelector('.section-title');
            const sectionSubtitle = document.querySelector('.subtitle');
            
            switch(category) {
                case 'popular':
                    sectionTitle.textContent = 'Popular Selections';
                    sectionSubtitle.textContent = 'Curated by our chefs, loved by our guests';
                    break;
                case 'all':
                    sectionTitle.textContent = 'Complete Menu';
                    sectionSubtitle.textContent = 'Explore our entire collection of culinary masterpieces';
                    break;
                default:
                    const categoryTab = document.querySelector(`[data-category="${category}"]`);
                    const categoryName = categoryTab ? categoryTab.textContent.trim() : 'Menu Items';
                    sectionTitle.textContent = categoryName;
                    sectionSubtitle.textContent = `Discover our exquisite ${categoryName.toLowerCase()} selection`;
            }
        }

        function switchCategory(categoryId) {
            categoryTabs.forEach(tab => {
                tab.classList.remove('active');
                if (tab.dataset.category === categoryId) {
                    tab.classList.add('active');
                }
            });
            
            filterItems();
        }

        function switchOrderType(orderType) {
            orderTypeBtns.forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.orderType === orderType) {
                    btn.classList.add('active');
                }
            });
            
            if (orderTypeSelect) {
                orderTypeSelect.value = orderType;
                tableSelectionGroup.style.display = orderType === 'dine_in' ? 'block' : 'none';
            }
        }

        function showTableSelection() {
            cartSummaryView.style.display = 'none';
            tableSelectionView.style.display = 'block';
        }

        function showCartSummary() {
            tableSelectionView.style.display = 'none';
            cartSummaryView.style.display = 'block';
        }

        function openCart() {
            cartSidebar.classList.add('open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeCart() {
            cartSidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        // --- Event Listeners ---
        
        // Category tabs
        categoryTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                switchCategory(tab.dataset.category);
            });
        });

        // Search functionality
        searchInput.addEventListener('input', filterItems);

        // Add to cart buttons
        document.querySelectorAll('.add-to-order').forEach(btn => {
            btn.addEventListener('click', function() {
                const itemId = this.dataset.itemId;
                const itemName = this.dataset.itemName;
                const itemPrice = this.dataset.itemPrice;
                const branchId = this.dataset.itemBranchId;
                
                addToCart(itemId, itemName, itemPrice, branchId);
            });
        });

        // Cart toggle
        cartToggle.addEventListener('click', openCart);
        cartClose.addEventListener('click', closeCart);
        overlay.addEventListener('click', closeCart);

        // Order type buttons
        orderTypeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                switchOrderType(btn.dataset.orderType);
            });
        });

        // Checkout button
        checkoutBtn.addEventListener('click', showTableSelection);

        // Clear cart button
        clearCartBtn.addEventListener('click', () => {
            if (confirm('Are you sure you want to clear your cart?')) {
                localStorage.removeItem('restaurant_cart');
                updateCartDisplay();
                updateCartToggle();
            }
        });

        // Order type select change
        if (orderTypeSelect) {
            orderTypeSelect.addEventListener('change', function() {
                switchOrderType(this.value);
            });
        }

        // Cancel selection button
        cancelSelectionBtn.addEventListener('click', showCartSummary);

        // Confirm order button
        confirmOrderBtn.addEventListener('click', function() {
            const orderType = orderTypeSelect.value;
            const tableId = tableSelect.value;

            if (orderType === 'dine_in' && !tableId) {
                alert('Please select a table for your dine-in order.');
                return;
            }

            const cart = getCart();
            if (cart.length === 0) {
                alert('Your cart is empty. Please add items to your order.');
                return;
            }

            const checkoutData = {
                cart: cart,
                tableNumber: orderType === 'dine_in' ? tableId : null,
                branchId: '<?php echo $branch['id']; ?>',
                orderType: orderType,
                timestamp: new Date().toISOString()
            };

            localStorage.setItem('checkout_data', JSON.stringify(checkoutData));
            
            const baseUrl = window.baseUrl || '/restaurant-menu-system'; 
            window.location.href = baseUrl + '/checkout';
        });

        // --- Initialize ---
        updateCartDisplay();
        updateCartToggle();
        filterItems(); // Initial filter to show popular items

        // Listen for cart updates across tabs
        window.addEventListener('storage', function(e) {
            if (e.key === 'restaurant_cart') {
                updateCartDisplay();
                updateCartToggle();
            }
        });

        document.addEventListener('cartUpdated', () => {
            updateCartDisplay();
            updateCartToggle();
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Escape to close cart
            if (e.key === 'Escape' && cartSidebar.classList.contains('open')) {
                closeCart();
            }
            
            // Ctrl/Cmd + K to focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
            }
        });
    });
    </script>

</body>
</html>