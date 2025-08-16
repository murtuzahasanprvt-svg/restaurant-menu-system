<div class="branches-page py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="page-header mb-5">
                    <h1 class="display-5">Our Branches</h1>
                    <p class="lead">Find a location near you and enjoy our delicious menu</p>
                </div>
            </div>
        </div>
        
        <!-- Search and Filter -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="search-box">
                    <form method="GET" action="<?php echo url('/branches'); ?>" class="d-flex">
                        <input type="text" class="form-control me-2" name="search" 
                               placeholder="Search branches..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="filter-box text-end">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary active">All Branches</button>
                        <button type="button" class="btn btn-outline-primary">Near Me</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Branches Grid -->
        <div class="row g-4">
            <?php if (!empty($branches)): ?>
                <?php foreach ($branches as $branch): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="branch-card card h-100">
                            <?php if (!empty($branch['logo_url'])): ?>
                                <div class="branch-logo">
                                    <img src="<?php echo asset('uploads/' . $branch['logo_url']); ?>" 
                                         class="card-img-top" alt="<?php echo htmlspecialchars($branch['name']); ?>">
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($branch['name']); ?></h5>
                                
                                <div class="branch-info">
                                    <div class="info-item mb-2">
                                        <i class="fas fa-map-marker-alt text-primary"></i>
                                        <span><?php echo htmlspecialchars($branch['address']); ?></span>
                                    </div>
                                    
                                    <div class="info-item mb-2">
                                        <i class="fas fa-phone text-primary"></i>
                                        <span><?php echo htmlspecialchars($branch['phone']); ?></span>
                                    </div>
                                    
                                    <?php if (!empty($branch['email'])): ?>
                                        <div class="info-item mb-2">
                                            <i class="fas fa-envelope text-primary"></i>
                                            <span><?php echo htmlspecialchars($branch['email']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($branch['description'])): ?>
                                        <div class="info-item mb-3">
                                            <p class="text-muted small"><?php echo htmlspecialchars($branch['description']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="card-footer bg-transparent">
                                <div class="d-flex gap-2">
                                    <a href="<?php echo url('/branch/' . $branch['id']); ?>" 
                                       class="btn btn-primary flex-fill">View Menu</a>
                                    <a href="tel:<?php echo htmlspecialchars($branch['phone']); ?>" 
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-phone"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <h4>No Branches Found</h4>
                        <p>There are no branches available at the moment. Please check back later.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if (!empty($pagination) && $pagination['last_page'] > 1): ?>
            <div class="row mt-5">
                <div class="col-12">
                    <nav aria-label="Branches pagination">
                        <ul class="pagination justify-content-center">
                            <?php if ($pagination['current_page'] > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?>">Previous</a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                                <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Map Section -->
<div class="map-section py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="display-5 text-center mb-4">Find Us on Map</h2>
                <div class="map-container">
                    <div class="map-placeholder bg-secondary text-white text-center p-5 rounded">
                        <i class="fas fa-map-marked-alt fa-3x mb-3"></i>
                        <h4>Interactive Map</h4>
                        <p>Map will be displayed here showing all branch locations</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>