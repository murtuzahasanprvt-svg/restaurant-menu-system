/**
 * Restaurant Menu System - Main JavaScript
 */

// Global variables
let cart = [];
let cartSidebar = null;
let cartToggleBtn = null;
let closeCartSidebar = null;
let cartItems = null;
let cartCount = null;
let cartTotal = null;
let checkoutBtn = null;

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    initializeCart();
    initializeTooltips();
    initializeModals();
    initializeForms();
    initializeSearch();
    initializeRealTimeUpdates();
    
    // Initialize theme-specific functionality
    if (typeof initializeTheme === 'function') {
        initializeTheme();
    }
});

// Cart functionality
function initializeCart() {
    cartSidebar = document.getElementById('cartSidebar');
    cartToggleBtn = document.getElementById('cartToggleBtn');
    closeCartSidebar = document.getElementById('closeCartSidebar');
    cartItems = document.getElementById('cartItems');
    cartCount = document.getElementById('cartCount');
    cartTotal = document.getElementById('cartTotal');
    checkoutBtn = document.getElementById('checkoutBtn');

    if (cartToggleBtn) {
        cartToggleBtn.addEventListener('click', toggleCart);
    }

    if (closeCartSidebar) {
        closeCartSidebar.addEventListener('click', closeCart);
    }

    // Close cart when clicking outside
    document.addEventListener('click', function(event) {
        if (cartSidebar && cartSidebar.classList.contains('show') &&
            !cartSidebar.contains(event.target) &&
            !cartToggleBtn.contains(event.target)) {
            closeCart();
        }
    });

    // Add to cart buttons
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('add-to-cart-btn') || 
            event.target.closest('.add-to-cart-btn')) {
            const btn = event.target.classList.contains('add-to-cart-btn') ? 
                       event.target : event.target.closest('.add-to-cart-btn');
            
            const itemId = btn.dataset.itemId;
            const itemName = btn.dataset.itemName;
            const itemPrice = parseFloat(btn.dataset.itemPrice);
            
            addToCart(itemId, itemName, itemPrice);
        }
    });

    // Remove from cart buttons
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-from-cart') || 
            event.target.closest('.remove-from-cart')) {
            const btn = event.target.classList.contains('remove-from-cart') ? 
                       event.target : event.target.closest('.remove-from-cart');
            
            const itemId = btn.dataset.itemId;
            removeFromCart(itemId);
        }
    });

    // Update quantity buttons
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('quantity-btn')) {
            const btn = event.target;
            const itemId = btn.dataset.itemId;
            const action = btn.dataset.action;
            
            updateQuantity(itemId, action);
        }
    });

    // Checkout button
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', proceedToCheckout);
    }

    loadCartFromStorage();
}

function toggleCart() {
    if (cartSidebar) {
        cartSidebar.classList.toggle('show');
    }
}

function closeCart() {
    if (cartSidebar) {
        cartSidebar.classList.remove('show');
    }
}

function addToCart(itemId, itemName, itemPrice) {
    const existingItem = cart.find(item => item.id === itemId);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: itemId,
            name: itemName,
            price: itemPrice,
            quantity: 1
        });
    }
    
    updateCartDisplay();
    saveCartToStorage();
    showNotification('Item added to cart!', 'success');
}

function removeFromCart(itemId) {
    cart = cart.filter(item => item.id !== itemId);
    updateCartDisplay();
    saveCartToStorage();
    showNotification('Item removed from cart!', 'info');
}

function updateQuantity(itemId, action) {
    const item = cart.find(item => item.id === itemId);
    
    if (item) {
        if (action === 'increase') {
            item.quantity += 1;
        } else if (action === 'decrease' && item.quantity > 1) {
            item.quantity -= 1;
        }
        
        updateCartDisplay();
        saveCartToStorage();
    }
}

function updateCartDisplay() {
    if (!cartItems || !cartCount || !cartTotal || !checkoutBtn) return;
    
    if (cart.length === 0) {
        cartItems.innerHTML = '<p class="text-muted text-center">Your cart is empty</p>';
        checkoutBtn.disabled = true;
    } else {
        let html = '';
        let total = 0;
        
        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            
            html += `
                <div class="cart-item">
                    <div class="cart-item-info">
                        <h6>${item.name}</h6>
                        <small class="text-muted">$${item.price.toFixed(2)} each</small>
                    </div>
                    <div class="cart-item-controls">
                        <div class="quantity-controls">
                            <button class="quantity-btn btn btn-sm btn-outline-secondary" 
                                    data-item-id="${item.id}" data-action="decrease">-</button>
                            <span class="quantity">${item.quantity}</span>
                            <button class="quantity-btn btn btn-sm btn-outline-secondary" 
                                    data-item-id="${item.id}" data-action="increase">+</button>
                        </div>
                        <div class="cart-item-total">
                            $${itemTotal.toFixed(2)}
                        </div>
                        <button class="remove-from-cart btn btn-sm btn-outline-danger" 
                                data-item-id="${item.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });
        
        cartItems.innerHTML = html;
        checkoutBtn.disabled = false;
    }
    
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = totalItems;
    cartTotal.textContent = `$${total.toFixed(2)}`;
}

function saveCartToStorage() {
    localStorage.setItem('restaurant_cart', JSON.stringify(cart));
}

function loadCartFromStorage() {
    const savedCart = localStorage.getItem('restaurant_cart');
    if (savedCart) {
        cart = JSON.parse(savedCart);
        updateCartDisplay();
    }
}

function proceedToCheckout() {
    if (cart.length === 0) return;
    
    // Save cart to session storage for checkout page
    sessionStorage.setItem('checkout_cart', JSON.stringify(cart));
    
    // Redirect to checkout page
    window.location.href = '/checkout';
}

// Tooltip initialization
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Modal initialization
function initializeModals() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('show.bs.modal', function() {
            // Add any modal initialization logic here
        });
    });
}

// Form handling
function initializeForms() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        });
    });
    
    // Custom form validations
    initializeFormValidations();
}

function initializeFormValidations() {
    // Username validation
    const usernameInputs = document.querySelectorAll('input[name="username"]');
    usernameInputs.forEach(input => {
        input.addEventListener('input', function() {
            const value = this.value;
            const regex = /^[a-zA-Z0-9_]+$/;
            
            if (value && !regex.test(value)) {
                this.setCustomValidity('Username can only contain letters, numbers, and underscores');
            } else {
                this.setCustomValidity('');
            }
        });
    });
    
    // Email validation
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        input.addEventListener('input', function() {
            const value = this.value;
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (value && !regex.test(value)) {
                this.setCustomValidity('Please enter a valid email address');
            } else {
                this.setCustomValidity('');
            }
        });
    });
    
    // Phone validation
    const phoneInputs = document.querySelectorAll('input[name="phone"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function() {
            const value = this.value;
            const regex = /^[\d\s\-\+\(\)]+$/;
            
            if (value && !regex.test(value)) {
                this.setCustomValidity('Please enter a valid phone number');
            } else {
                this.setCustomValidity('');
            }
        });
    });
}

// Search functionality
function initializeSearch() {
    const searchInputs = document.querySelectorAll('.search-input');
    searchInputs.forEach(input => {
        let searchTimeout;
        
        input.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            searchTimeout = setTimeout(() => {
                performSearch(query);
            }, 300);
        });
    });
}

function performSearch(query) {
    if (query.length < 2) {
        clearSearchResults();
        return;
    }
    
    // Perform AJAX search
    fetch('/api/search', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify({ query: query })
    })
    .then(response => response.json())
    .then(data => {
        displaySearchResults(data);
    })
    .catch(error => {
        console.error('Search error:', error);
    });
}

function displaySearchResults(results) {
    const resultsContainer = document.getElementById('searchResults');
    if (!resultsContainer) return;
    
    if (results.length === 0) {
        resultsContainer.innerHTML = '<div class="search-no-results">No results found</div>';
        return;
    }
    
    let html = '';
    results.forEach(result => {
        html += `
            <div class="search-result-item" onclick="selectSearchResult('${result.id}', '${result.type}')">
                <div class="search-result-title">${result.title}</div>
                <div class="search-result-description">${result.description}</div>
            </div>
        `;
    });
    
    resultsContainer.innerHTML = html;
}

function clearSearchResults() {
    const resultsContainer = document.getElementById('searchResults');
    if (resultsContainer) {
        resultsContainer.innerHTML = '';
    }
}

function selectSearchResult(id, type) {
    // Handle search result selection
    console.log('Selected:', id, type);
    clearSearchResults();
}

// Real-time updates
function initializeRealTimeUpdates() {
    // Initialize WebSocket connection if available
    if (typeof WebSocket !== 'undefined') {
        initializeWebSocket();
    }
    
    // Fallback to periodic updates
    setInterval(checkForUpdates, 30000); // Check every 30 seconds
}

function initializeWebSocket() {
    const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
    const wsUrl = `${protocol}//${window.location.host}/ws`;
    
    try {
        const ws = new WebSocket(wsUrl);
        
        ws.onopen = function() {
            console.log('WebSocket connected');
            // Subscribe to relevant channels
            if (typeof currentUser !== 'undefined' && currentUser.branch_id) {
                ws.send(JSON.stringify({
                    type: 'subscribe',
                    channel: `branch_${currentUser.branch_id}`
                }));
            }
        };
        
        ws.onmessage = function(event) {
            const data = JSON.parse(event.data);
            handleRealTimeUpdate(data);
        };
        
        ws.onclose = function() {
            console.log('WebSocket disconnected');
            // Attempt to reconnect after 5 seconds
            setTimeout(initializeWebSocket, 5000);
        };
        
        ws.onerror = function(error) {
            console.error('WebSocket error:', error);
        };
        
        window.ws = ws;
    } catch (error) {
        console.error('WebSocket initialization failed:', error);
    }
}

function handleRealTimeUpdate(data) {
    switch (data.type) {
        case 'order_update':
            handleOrderUpdate(data.data);
            break;
        case 'new_order':
            handleNewOrder(data.data);
            break;
        case 'menu_update':
            handleMenuUpdate(data.data);
            break;
        case 'system_notification':
            handleSystemNotification(data.data);
            break;
    }
}

function handleOrderUpdate(orderData) {
    // Update order status in the UI
    const orderElements = document.querySelectorAll(`[data-order-id="${orderData.id}"]`);
    orderElements.forEach(element => {
        const statusElement = element.querySelector('.order-status');
        if (statusElement) {
            statusElement.textContent = orderData.status;
            statusElement.className = `order-status badge bg-${getStatusBadgeClass(orderData.status)}`;
        }
    });
    
    // Show notification if relevant
    if (typeof currentUser !== 'undefined' && orderData.user_id === currentUser.id) {
        showNotification(`Order #${orderData.order_number} status updated to ${orderData.status}`, 'info');
    }
}

function handleNewOrder(orderData) {
    // Show notification for new orders
    if (typeof currentUser !== 'undefined' && 
        (currentUser.role === 'chef' || currentUser.role === 'waiter' || currentUser.role === 'branch_manager')) {
        showNotification(`New order #${orderData.order_number} received!`, 'success');
        
        // Play notification sound if available
        playNotificationSound();
    }
}

function handleMenuUpdate(menuData) {
    // Update menu items in the UI
    if (menuData.action === 'update' || menuData.action === 'delete') {
        const menuElement = document.querySelector(`[data-menu-item-id="${menuData.item_id}"]`);
        if (menuElement) {
            if (menuData.action === 'delete') {
                menuElement.remove();
            } else {
                // Update menu item details
                updateMenuItem(menuElement, menuData);
            }
        }
    }
    
    showNotification('Menu updated', 'info');
}

function handleSystemNotification(notification) {
    showNotification(notification.message, notification.type || 'info');
}

function checkForUpdates() {
    // Check for order updates if user is logged in
    if (typeof currentUser !== 'undefined' && currentUser.id) {
        fetch('/api/check-updates', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify({ 
                user_id: currentUser.id,
                last_check: localStorage.getItem('last_update_check') || Date.now()
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.updates) {
                data.updates.forEach(update => {
                    handleRealTimeUpdate(update);
                });
            }
            localStorage.setItem('last_update_check', Date.now());
        })
        .catch(error => {
            console.error('Update check error:', error);
        });
    }
}

// Utility functions
function getStatusBadgeClass(status) {
    const statusClasses = {
        'pending': 'warning',
        'confirmed': 'info',
        'preparing': 'primary',
        'ready': 'success',
        'delivered': 'success',
        'cancelled': 'danger'
    };
    return statusClasses[status] || 'secondary';
}

function updateMenuItem(element, data) {
    // Update menu item with new data
    if (data.name) {
        const nameElement = element.querySelector('.menu-item-name');
        if (nameElement) nameElement.textContent = data.name;
    }
    
    if (data.price) {
        const priceElement = element.querySelector('.menu-item-price');
        if (priceElement) priceElement.textContent = `$${data.price.toFixed(2)}`;
    }
    
    if (data.description) {
        const descElement = element.querySelector('.menu-item-description');
        if (descElement) descElement.textContent = data.description;
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show notification`;
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.notifications-container') || document.body;
    container.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

function playNotificationSound() {
    // Play a notification sound if available
    const audio = new Audio('/assets/sounds/notification.mp3');
    audio.play().catch(error => {
        console.error('Failed to play notification sound:', error);
    });
}

// Export functions for global use
window.toggleCart = toggleCart;
window.closeCart = closeCart;
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.updateQuantity = updateQuantity;
window.showNotification = showNotification;