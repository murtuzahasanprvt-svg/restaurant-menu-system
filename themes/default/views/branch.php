<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Branch Header -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h2 class="card-title mb-0"><?php echo htmlspecialchars($branch['name']); ?></h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><i class="fas fa-map-marker-alt"></i> <strong>Address:</strong><br>
                            <?php echo htmlspecialchars($branch['address']); ?></p>
                            <p><i class="fas fa-phone"></i> <strong>Phone:</strong><br>
                            <?php echo htmlspecialchars($branch['phone']); ?></p>
                            <?php if (!empty($branch['email'])): ?>
                                <p><i class="fas fa-envelope"></i> <strong>Email:</strong><br>
                                <?php echo htmlspecialchars($branch['email']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <?php if (!empty($branch['description'])): ?>
                                <p><i class="fas fa-info-circle"></i> <strong>Description:</strong><br>
                                <?php echo nl2br(htmlspecialchars($branch['description'])); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($branch['logo_url'])): ?>
                                <div class="text-center">
                                    <img src="<?php echo htmlspecialchars($branch['logo_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($branch['name']); ?>" 
                                         class="img-fluid" style="max-height: 100px;">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- QR Codes Section -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-qrcode"></i> Scan to Order
                    </h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($qr_codes)): ?>
                        <div class="row">
                            <?php foreach ($qr_codes as $qr_code): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body text-center">
                                            <h5 class="card-title">Table <?php echo htmlspecialchars($qr_code['table_number']); ?></h5>
                                            <?php if (!empty($qr_code['location'])): ?>
                                                <p class="text-muted"><?php echo htmlspecialchars($qr_code['location']); ?></p>
                                            <?php endif; ?>
                                            <p class="text-muted">Capacity: <?php echo (int)$qr_code['capacity']; ?></p>
                                            
                                            <?php if (!empty($qr_code['qr_image_url'])): ?>
                                                <div class="mb-3">
                                                    <img src="<?php echo htmlspecialchars($qr_code['qr_image_url']); ?>" 
                                                         alt="QR Code for Table <?php echo htmlspecialchars($qr_code['table_number']); ?>" 
                                                         class="img-fluid" style="max-width: 200px;">
                                                </div>
                                            <?php else: ?>
                                                <div class="qr-placeholder bg-light p-3 mb-3">
                                                    <div class="text-center">
                                                        <i class="fas fa-qrcode fa-3x text-muted"></i>
                                                        <p class="text-muted mt-2">QR Code</p>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <p class="text-muted small">
                                                <i class="fas fa-info-circle"></i> 
                                                Scan this QR code to view the menu and place your order
                                            </p>
                                            
                                            <div class="mt-3">
                                                <a href="<?php echo url('/qr/' . $qr_code['qr_code']); ?>" 
                                                   class="btn btn-primary w-100">
                                                    <i class="fas fa-utensils"></i> Order from this Table
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            No QR codes available for this branch yet.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Back Button -->
            <div class="text-center mt-4">
                <a href="<?php echo url('/branches'); ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Branches
                </a>
            </div>
        </div>
    </div>
</div>