<div class="auth-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="auth-card card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="auth-title">Login</h2>
                            <p class="text-muted">Sign in to your account</p>
                        </div>
                        
                        <form method="POST" action="<?php echo url('/login'); ?>" class="auth-form">
                            <?php echo csrf_field(); ?>
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control <?php echo has_error('username') ? 'is-invalid' : ''; ?>" 
                                       id="username" name="username" value="<?php echo old('username'); ?>" required>
                                <?php if (has_error('username')): ?>
                                    <div class="invalid-feedback"><?php echo error('username'); ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control <?php echo has_error('password') ? 'is-invalid' : ''; ?>" 
                                       id="password" name="password" required>
                                <?php if (has_error('password')): ?>
                                    <div class="invalid-feedback"><?php echo error('password'); ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                        
                        <div class="auth-links mt-4 text-center">
                            <p class="mb-2">
                                <a href="<?php echo url('/forgot-password'); ?>" class="text-decoration-none">Forgot Password?</a>
                            </p>
                            <p class="mb-0">
                                Don't have an account? <a href="<?php echo url('/register'); ?>" class="text-decoration-none">Register here</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>