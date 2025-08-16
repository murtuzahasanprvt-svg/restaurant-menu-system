<?php
// Get branches from database
$branchModel = new Branch();
$branches = $branchModel->getActiveBranches();
$selectedBranchId = isset($_GET['branch_id']) ? (int)$_GET['branch_id'] : null;

// Auto-select if only one branch exists
if (count($branches) === 1) {
    $selectedBranchId = $branches[0]['id'];
    // Redirect to menu.php if only one branch
    header('Location: menu.php?branch_id=' . $selectedBranchId);
    exit;
}

// Get selected branch data
$selectedBranch = null;
$menuItems = [];
$categories = [];

if ($selectedBranchId) {
    $selectedBranch = $branchModel->find($selectedBranchId);
    if ($selectedBranch) {
        // Get menu items for the selected branch
        $menuItems = $branchModel->getBranchMenuItems($selectedBranchId);
        
        // Get categories for the selected branch
        $categories = $branchModel->getBranchMenuCategories($selectedBranchId);
    }
}

// Get search and filter parameters
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$selectedCategory = isset($_GET['category']) ? trim($_GET['category']) : 'all';
$sortBy = isset($_GET['sort']) ? trim($_GET['sort']) : 'name';

// Filter menu items
$filteredMenuItems = array_filter($menuItems, function($item) use ($searchTerm, $selectedCategory) {
    $matchesSearch = empty($searchTerm) || 
                      stripos($item['name'], $searchTerm) !== false || 
                      stripos($item['description'], $searchTerm) !== false;
    $matchesCategory = $selectedCategory === 'all' || $item['category_id'] == $selectedCategory;
    return $matchesSearch && $matchesCategory;
});

// Sort menu items
usort($filteredMenuItems, function($a, $b) use ($sortBy) {
    switch ($sortBy) {
        case 'price':
            return $a['price'] - $b['price'];
        case 'price_desc':
            return $b['price'] - $a['price'];
        case 'name':
            return strcmp($a['name'], $b['name']);
        case 'popularity':
            return ($b['is_popular'] ? 1 : 0) - ($a['is_popular'] ? 1 : 0);
        default:
            return 0;
    }
});
?>

<!-- Branch Selection Overlay -->
<?php if (empty($selectedBranchId) && count($branches) > 1): ?>
<div class="branch-selection-overlay" id="branchSelectionOverlay">
    <div class="branch-selection-modal">
        <div class="branch-selection-header">
            <h2 class="branch-selection-title">Choose Your Location</h2>
            <p class="branch-selection-subtitle">Select your preferred branch to explore our menu</p>
        </div>
        
        <div class="branch-selection-content">
            <div class="branches-list">
                <?php foreach ($branches as $branch): ?>
                    <div class="branch-option" data-branch-id="<?php echo $branch['id']; ?>" onclick="selectBranch(<?php echo $branch['id']; ?>)">
                        <div class="branch-option-info">
                            <h3 class="branch-option-name"><?php echo htmlspecialchars($branch['name']); ?></h3>
                            <p class="branch-option-description"><?php echo htmlspecialchars($branch['description']); ?></p>
                            <div class="branch-option-details">
                                <div class="branch-option-detail">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($branch['address']); ?></span>
                                </div>
                                <div class="branch-option-detail">
                                    <i class="fas fa-phone"></i>
                                    <span><?php echo htmlspecialchars($branch['phone']); ?></span>
                                </div>
                                <?php if (!empty($branch['email'])): ?>
                                    <div class="branch-option-detail">
                                        <i class="fas fa-envelope"></i>
                                        <span><?php echo htmlspecialchars($branch['email']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="branch-option-status">
                            <?php if ($branch['is_active']): ?>
                                <span class="status-badge status-open">Open Now</span>
                            <?php else: ?>
                                <span class="status-badge status-closed">Closed</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Blurry Background with Menu Preview -->
<div class="menu-preview-background" id="menuPreviewBackground">
    <?php
    // Show menu preview of first branch
    $firstBranch = $branches[0] ?? null;
    if ($firstBranch):
        $previewMenuItems = $branchModel->getBranchMenuItems($firstBranch['id']);
        $previewCategories = $branchModel->getBranchMenuCategories($firstBranch['id']);
    ?>
    <div class="menu-preview-container">
        <div class="menu-preview-header">
            <h1 class="menu-preview-title"><?php echo htmlspecialchars($firstBranch['name']); ?></h1>
            <p class="menu-preview-subtitle"><?php echo htmlspecialchars($firstBranch['description']); ?></p>
        </div>
        
        <div class="menu-preview-content">
            <div class="menu-preview-items">
                <?php if (!empty($previewMenuItems)): ?>
                    <?php foreach (array_slice($previewMenuItems, 0, 6) as $item): ?>
                        <div class="menu-preview-item">
                            <div class="menu-preview-item-content">
                                <h3 class="menu-preview-item-name"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="menu-preview-item-description"><?php echo htmlspecialchars($item['description']); ?></p>
                                <span class="menu-preview-item-price">$<?php echo number_format($item['price'], 2); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">
                    <?php echo !empty($selectedBranch) ? htmlspecialchars($selectedBranch['name']) : $app_name; ?>
                </h1>
                <p class="hero-subtitle">
                    <?php if (!empty($selectedBranch)): ?>
                        <?php echo htmlspecialchars($selectedBranch['description']); ?>
                    <?php else: ?>
                        Experience culinary excellence at our premium locations
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Branch Selection Section -->
<?php if (empty($selectedBranch) && count($branches) > 1): ?>
<section class="branch-selection-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Choose Your Location</h2>
            <p class="section-subtitle">Select your preferred branch to explore our menu</p>
        </div>
        
        <div class="branches-grid">
            <?php foreach ($branches as $branch): ?>
                <div class="branch-card" data-branch-id="<?php echo $branch['id']; ?>">
                    <div class="branch-card-content">
                        <div class="branch-info">
                            <h3 class="branch-name"><?php echo htmlspecialchars($branch['name']); ?></h3>
                            <p class="branch-description"><?php echo htmlspecialchars($branch['description']); ?></p>
                            <div class="branch-details">
                                <div class="branch-detail">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($branch['address']); ?></span>
                                </div>
                                <div class="branch-detail">
                                    <i class="fas fa-phone"></i>
                                    <span><?php echo htmlspecialchars($branch['phone']); ?></span>
                                </div>
                                <?php if (!empty($branch['email'])): ?>
                                    <div class="branch-detail">
                                        <i class="fas fa-envelope"></i>
                                        <span><?php echo htmlspecialchars($branch['email']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="branch-actions">
                            <div class="branch-status">
                                <?php if ($branch['is_active']): ?>
                                    <span class="status-badge status-open">Open Now</span>
                                <?php else: ?>
                                    <span class="status-badge status-closed">Closed</span>
                                <?php endif; ?>
                            </div>
                            <a href="?branch_id=<?php echo $branch['id']; ?>" class="btn btn-primary">
                                View Menu
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Menu Section -->
<?php if (!empty($selectedBranch)): ?>
<section class="menu-section">
    <div class="container">
        <!-- Branch Header -->
        <div class="branch-header">
            <div class="branch-header-content">
                <div class="branch-header-info">
                    <div class="branch-header-title">
                        <h2 class="branch-name"><?php echo htmlspecialchars($selectedBranch['name']); ?></h2>
                        <div class="branch-meta">
                            <span class="branch-meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($selectedBranch['address']); ?>
                            </span>
                            <span class="branch-meta-item">
                                <i class="fas fa-phone"></i>
                                <?php echo htmlspecialchars($selectedBranch['phone']); ?>
                            </span>
                            <span class="branch-meta-item">
                                <i class="fas fa-clock"></i>
                                <?php echo $selectedBranch['is_active'] ? 'Open Now' : 'Closed'; ?>
                            </span>
                        </div>
                    </div>
                    <?php if (count($branches) > 1): ?>
                        <div class="branch-selector">
                            <label for="branchSelect" class="branch-selector-label">Change Location</label>
                            <select id="branchSelect" class="branch-select" onchange="window.location.href='?branch_id=' + this.value">
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?php echo $branch['id']; ?>" <?php echo $branch['id'] == $selectedBranchId ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($branch['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="menu-controls">
            <div class="menu-search">
                <div class="search-input-group">
                    <input type="text" 
                           class="search-input" 
                           placeholder="Search menu items..." 
                           value="<?php echo htmlspecialchars($searchTerm); ?>"
                           id="menuSearch">
                    <button class="search-button" onclick="performSearch()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            
            <div class="menu-filters">
                <div class="filter-group">
                    <label for="categoryFilter" class="filter-label">Category</label>
                    <select id="categoryFilter" class="filter-select" onchange="applyFilters()">
                        <option value="all" <?php echo $selectedCategory === 'all' ? 'selected' : ''; ?>>All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo $selectedCategory == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="sortBy" class="filter-label">Sort By</label>
                    <select id="sortBy" class="filter-select" onchange="applyFilters()">
                        <option value="name" <?php echo $sortBy === 'name' ? 'selected' : ''; ?>>Name</option>
                        <option value="price" <?php echo $sortBy === 'price' ? 'selected' : ''; ?>>Price (Low to High)</option>
                        <option value="price_desc" <?php echo $sortBy === 'price_desc' ? 'selected' : ''; ?>>Price (High to Low)</option>
                        <option value="popularity" <?php echo $sortBy === 'popularity' ? 'selected' : ''; ?>>Popularity</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <button class="btn btn-outline-secondary" onclick="clearFilters()">
                        <i class="fas fa-times"></i> Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Category Tabs -->
        <div class="category-tabs">
            <div class="category-tabs-list">
                <button class="category-tab <?php echo $selectedCategory === 'all' ? 'active' : ''; ?>" 
                        onclick="selectCategory('all')">
                    All Items
                </button>
                <?php foreach ($categories as $category): ?>
                    <button class="category-tab <?php echo $selectedCategory == $category['id'] ? 'active' : ''; ?>" 
                            onclick="selectCategory('<?php echo $category['id']; ?>')">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Menu Items Grid -->
        <div class="menu-items-grid">
            <?php if (!empty($filteredMenuItems)): ?>
                <?php foreach ($filteredMenuItems as $item): ?>
                    <div class="menu-item-card" data-item-id="<?php echo $item['id']; ?>">
                        <div class="menu-item-image">
                            <?php if (!empty($item['image_url'])): ?>
                                <img src="<?php echo asset('uploads/' . $item['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                     class="menu-item-img">
                            <?php else: ?>
                                <div class="menu-item-placeholder">
                                    <i class="fas fa-utensils"></i>
                                </div>
                            <?php endif; ?>
                            <div class="menu-item-badges">
                                <?php if (!empty($item['is_popular'])): ?>
                                    <span class="badge badge-popular">Popular</span>
                                <?php endif; ?>
                                <?php if (!empty($item['is_vegetarian'])): ?>
                                    <span class="badge badge-vegetarian">Vegetarian</span>
                                <?php endif; ?>
                                <?php if (!empty($item['is_spicy'])): ?>
                                    <span class="badge badge-spicy">Spicy</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="menu-item-content">
                            <div class="menu-item-header">
                                <h3 class="menu-item-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <span class="menu-item-price">$<?php echo number_format($item['price'], 2); ?></span>
                            </div>
                            
                            <p class="menu-item-description">
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
                                <?php if (!empty($item['allergens'])): ?>
                                    <span class="meta-item allergen-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <?php echo htmlspecialchars($item['allergens']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="menu-item-actions">
                                <button class="btn btn-primary add-to-cart-btn" 
                                        data-item-id="<?php echo $item['id']; ?>"
                                        data-item-name="<?php echo htmlspecialchars($item['name']); ?>"
                                        data-item-price="<?php echo $item['price']; ?>"
                                        data-branch-id="<?php echo $selectedBranchId; ?>">
                                    <i class="fas fa-cart-plus"></i>
                                    Add to Cart
                                </button>
                                <button class="btn btn-outline-heart wishlist-btn" 
                                        onclick="toggleWishlist(<?php echo $item['id']; ?>)">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <div class="no-results-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="no-results-title">No items found</h3>
                    <p class="no-results-text">
                        <?php if (!empty($searchTerm) || $selectedCategory !== 'all'): ?>
                            Try adjusting your search or filter criteria
                        <?php else: ?>
                            No menu items available at this branch
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Features Section -->
<?php if (empty($selectedBranch)): ?>
<section class="features-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Why Choose Us</h2>
            <p class="section-subtitle">Experience the future of dining</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-qrcode"></i>
                </div>
                <h3 class="feature-title">QR Code Ordering</h3>
                <p class="feature-description">Scan QR codes at your table for instant access to our menu</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3 class="feature-title">Mobile Friendly</h3>
                <p class="feature-description">Order from any device with our responsive design</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <h3 class="feature-title">Secure Payment</h3>
                <p class="feature-description">Safe and secure payment processing</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <h3 class="feature-title">Fast Service</h3>
                <p class="feature-description">Quick order preparation and delivery</p>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
function performSearch() {
    const searchTerm = document.getElementById('menuSearch').value;
    const category = document.getElementById('categoryFilter').value;
    const sortBy = document.getElementById('sortBy').value;
    const branchId = <?php echo $selectedBranchId; ?>;
    
    const params = new URLSearchParams({
        branch_id: branchId,
        search: searchTerm,
        category: category,
        sort: sortBy
    });
    
    window.location.href = '?' + params.toString();
}

function applyFilters() {
    performSearch();
}

function clearFilters() {
    document.getElementById('menuSearch').value = '';
    document.getElementById('categoryFilter').value = 'all';
    document.getElementById('sortBy').value = 'name';
    performSearch();
}

function selectCategory(categoryId) {
    document.getElementById('categoryFilter').value = categoryId;
    performSearch();
}

function toggleWishlist(itemId) {
    // Wishlist functionality would be implemented here
    console.log('Toggle wishlist for item:', itemId);
}

function selectBranch(branchId) {
    // Redirect to menu.php with the selected branch ID
    window.location.href = 'menu.php?branch_id=' + branchId;
}

// Auto-search on typing
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('menuSearch');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch();
            }, 500);
        });
    }
});
</script>