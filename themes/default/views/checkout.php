<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2 class="card-title mb-0">
                        <i class="fas fa-shopping-cart"></i> Checkout
                    </h2>
                </div>
                <div class="card-body">
                    <!-- Order Summary -->
                    <div class="order-summary mb-4">
                        <h4>Order Summary</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody id="cartItemsBody">
                                    <tr>
                                        <td colspan="4" class="text-center">Loading cart...</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                        <td id="subtotalAmount">$0.00</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Tax (8.5%):</strong></td>
                                        <td id="taxAmount">$0.00</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                        <td id="totalAmount"><strong>$0.00</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Checkout Form -->
                    <form method="POST" action="<?php echo url('/checkout'); ?>" id="checkoutForm">
                        <input type="hidden" name="cart" id="cartData" value="">
                        <input type="hidden" name="branch_id" id="branchId" value="1"> <!-- This should be dynamic -->
                        <input type="hidden" name="table_number" id="tableNumber" value=""> <!-- Table number from selection -->
                        <input type="hidden" name="order_type" id="orderType" value="dine_in"> <!-- Order type from selection -->
                        
                        <h4 class="mb-3">Customer Information</h4>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="customer_name" class="form-label">Full Name <span class="required-field text-danger" id="nameRequired">*</span></label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name">
                            </div>
                            <div class="col-md-6">
                                <label for="customer_phone" class="form-label">Phone Number <span class="required-field text-danger" id="phoneRequired">*</span></label>
                                <input type="tel" class="form-control" id="customer_phone" name="customer_phone">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="customer_email" class="form-label">Email Address <span class="required-field text-danger" id="emailRequired">*</span></label>
                                <input type="email" class="form-control" id="customer_email" name="customer_email">
                            </div>
                            <div class="col-md-6">
                                <label for="order_type_display" class="form-label">Order Type</label>
                                <input type="text" class="form-control" id="order_type_display" value="Dine In" readonly>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="special_instructions" class="form-label">Special Instructions</label>
                            <textarea class="form-control" id="special_instructions" name="special_instructions" rows="3" 
                                      placeholder="Any special requests or dietary requirements..."></textarea>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="placeOrderBtn">
                                <i class="fas fa-credit-card"></i> Place Order
                            </button>
                            <a href="<?php echo url('/menu/1'); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Menu
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load cart data from localStorage and display it
document.addEventListener('DOMContentLoaded', function() {
    const checkoutData = localStorage.getItem('checkout_data');
    const cartData = localStorage.getItem('checkout_cart');
    const restaurantCart = localStorage.getItem('restaurant_cart');
    
    let cart = [];
    let tableNumber = null;
    let branchId = null;
    let qrCode = null;
    let orderType = 'dine_in';
    
    // Try to get complete checkout data first
    if (checkoutData) {
        const data = JSON.parse(checkoutData);
        cart = data.cart || [];
        tableNumber = data.tableNumber || null;
        branchId = data.branchId || null;
        qrCode = data.qrCode || null;
        orderType = data.orderType || 'dine_in';
    } else if (cartData) {
        // Fallback to old cart data format
        cart = JSON.parse(cartData);
    } else if (restaurantCart) {
        // Fallback to restaurant cart data
        cart = JSON.parse(restaurantCart);
    }
    
    console.log('Checkout page loaded cart:', cart);
    console.log('Order type:', orderType);
    console.log('Table number:', tableNumber);
    
    // Extract branch ID from URL if not already set
    if (!branchId) {
        const urlParams = new URLSearchParams(window.location.search);
        branchId = urlParams.get('branch_id') || urlParams.get('branch') || '1';
    }
    
    // Set form values
    if (cart.length > 0) {
        document.getElementById('cartData').value = JSON.stringify(cart);
        
        // Set table number if available
        if (tableNumber) {
            document.getElementById('tableNumber').value = tableNumber;
        }
        
        // Set branch ID
        document.getElementById('branchId').value = branchId;
        
        // Set order type
        document.getElementById('orderType').value = orderType;
        
        // Update order type display
        updateOrderTypeDisplay(orderType);
        
        // Update field requirements based on order type
        updateFieldRequirements(orderType);
        
        displayCartItems(cart);
    } else {
        displayEmptyCart();
        updateFieldRequirements('dine_in'); // Default requirements
    }
    
    // Handle form submission to clear cart
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            // Validate form based on order type
            if (!validateForm(orderType)) {
                e.preventDefault();
                return;
            }
            
            const placeOrderBtn = document.getElementById('placeOrderBtn');
            placeOrderBtn.disabled = true;
            placeOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Placing Order...';
        });
    }
});

function updateOrderTypeDisplay(orderType) {
    const orderTypeDisplay = document.getElementById('order_type_display');
    if (orderTypeDisplay) {
        const orderTypeText = {
            'dine_in': 'Dine In',
            'takeaway': 'Takeaway',
            'delivery': 'Delivery'
        };
        orderTypeDisplay.value = orderTypeText[orderType] || 'Dine In';
    }
}

function updateFieldRequirements(orderType) {
    const nameRequired = document.getElementById('nameRequired');
    const phoneRequired = document.getElementById('phoneRequired');
    const emailRequired = document.getElementById('emailRequired');
    const customerName = document.getElementById('customer_name');
    const customerPhone = document.getElementById('customer_phone');
    const customerEmail = document.getElementById('customer_email');
    
    // For delivery, all fields are required
    if (orderType === 'delivery') {
        nameRequired.style.display = 'inline';
        phoneRequired.style.display = 'inline';
        emailRequired.style.display = 'inline';
        customerName.required = true;
        customerPhone.required = true;
        customerEmail.required = true;
    } 
    // For dine-in and takeaway, all fields are optional
    else {
        nameRequired.style.display = 'none';
        phoneRequired.style.display = 'none';
        emailRequired.style.display = 'none';
        customerName.required = false;
        customerPhone.required = false;
        customerEmail.required = false;
    }
}

function validateForm(orderType) {
    const customerName = document.getElementById('customer_name').value.trim();
    const customerPhone = document.getElementById('customer_phone').value.trim();
    const customerEmail = document.getElementById('customer_email').value.trim();
    
    // For delivery, validate all fields
    if (orderType === 'delivery') {
        if (!customerName) {
            alert('Full name is required for delivery orders.');
            return false;
        }
        if (!customerPhone) {
            alert('Phone number is required for delivery orders.');
            return false;
        }
        if (!customerEmail) {
            alert('Email address is required for delivery orders.');
            return false;
        }
        // Simple email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(customerEmail)) {
            alert('Please enter a valid email address.');
            return false;
        }
    }
    
    return true;
}

function displayCartItems(cart) {
    const cartItemsBody = document.getElementById('cartItemsBody');
    
    if (cart.length === 0) {
        displayEmptyCart();
        return;
    }
    
    let html = '';
    let subtotal = 0;
    
    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;
        
        html += `
            <tr>
                <td>${item.name}</td>
                <td>$${item.price.toFixed(2)}</td>
                <td>${item.quantity}</td>
                <td>$${itemTotal.toFixed(2)}</td>
            </tr>
        `;
    });
    
    cartItemsBody.innerHTML = html;
    
    // Calculate totals
    const taxRate = 0.085;
    const taxAmount = subtotal * taxRate;
    const totalAmount = subtotal + taxAmount;
    
    document.getElementById('subtotalAmount').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('taxAmount').textContent = `$${taxAmount.toFixed(2)}`;
    document.getElementById('totalAmount').textContent = `$${totalAmount.toFixed(2)}`;
}

function displayEmptyCart() {
    const cartItemsBody = document.getElementById('cartItemsBody');
    cartItemsBody.innerHTML = '<tr><td colspan="4" class="text-center">Your cart is empty</td></tr>';
    
    document.getElementById('subtotalAmount').textContent = '$0.00';
    document.getElementById('taxAmount').textContent = '$0.00';
    document.getElementById('totalAmount').textContent = '$0.00';
}

// Function to clear cart data from localStorage
function clearCartData() {
    localStorage.removeItem('restaurant_cart');
    localStorage.removeItem('checkout_data');
    localStorage.removeItem('checkout_cart');
    console.log('Cart data cleared from localStorage');
}
</script>