<div class="hero-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Welcome to <?php echo $app_name; ?></h1>
                <p class="lead mb-4">Order your favorite meals with just a scan. Experience the future of dining with our QR code menu system.</p>
                <div class="d-flex gap-3">
                    <a href="<?php echo url('/branches'); ?>" class="btn btn-light btn-lg">Find Branches</a>
                    <a href="<?php echo url('/login'); ?>" class="btn btn-outline-light btn-lg">Login</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image text-center">
                    <div class="qr-demo">
                        <div class="qr-placeholder bg-white rounded p-4 d-inline-block">
                            <div class="qr-pattern">
                                <div class="qr-pattern-grid">
                                    <!-- QR Code Pattern Placeholder -->
                                    <div class="qr-grid"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="features-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5">How It Works</h2>
                <p class="lead">Simple, fast, and convenient ordering</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card text-center p-4">
                    <div class="feature-icon mb-3">
                        <div class="icon-circle bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-qrcode fa-2x"></i>
                        </div>
                    </div>
                    <h3>Scan QR Code</h3>
                    <p>Simply scan the QR code at your table using your smartphone camera.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card text-center p-4">
                    <div class="feature-icon mb-3">
                        <div class="icon-circle bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-utensils fa-2x"></i>
                        </div>
                    </div>
                    <h3>Browse Menu</h3>
                    <p>Explore our delicious menu items with detailed descriptions and images.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card text-center p-4">
                    <div class="feature-icon mb-3">
                        <div class="icon-circle bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                    <h3>Order & Pay</h3>
                    <p>Add items to your cart and place your order securely.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="branches-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5">Our Branches</h2>
                <p class="lead">Find a location near you</p>
            </div>
        </div>
        
        <div class="row g-4">
            <?php if (!empty($branches)): ?>
                <?php foreach ($branches as $branch): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="branch-card card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($branch['name']); ?></h5>
                                <p class="card-text">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($branch['address']); ?>
                                </p>
                                <p class="card-text">
                                    <i class="fas fa-phone"></i>
                                    <?php echo htmlspecialchars($branch['phone']); ?>
                                </p>
                                <?php if (!empty($branch['email'])): ?>
                                    <p class="card-text">
                                        <i class="fas fa-envelope"></i>
                                        <?php echo htmlspecialchars($branch['email']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-transparent">
                                <a href="<?php echo url('/branch/' . $branch['id']); ?>" class="btn btn-primary">View Menu</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p class="lead">No branches available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="row mt-4">
            <div class="col-12 text-center">
                <a href="<?php echo url('/branches'); ?>" class="btn btn-primary btn-lg">View All Branches</a>
            </div>
        </div>
    </div>
</div>

<div class="testimonials-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5">What Our Customers Say</h2>
                <p class="lead">Real experiences from our valued customers</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="testimonial-card card h-100">
                    <div class="card-body">
                        <div class="testimonial-rating mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text">"The QR code system is amazing! No more waiting for waiters to take orders. Just scan and order!"</p>
                        <div class="testimonial-author">
                            <strong>- John D.</strong>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="testimonial-card card h-100">
                    <div class="card-body">
                        <div class="testimonial-rating mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text">"Fast, convenient, and user-friendly. The menu is well-organized with great photos."</p>
                        <div class="testimonial-author">
                            <strong>- Sarah M.</strong>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="testimonial-card card h-100">
                    <div class="card-body">
                        <div class="testimonial-rating mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="far fa-star text-warning"></i>
                        </div>
                        <p class="card-text">"Great concept! Makes dining out so much more efficient. Highly recommended!"</p>
                        <div class="testimonial-author">
                            <strong>- Mike R.</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>