<div class="auth-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="auth-card card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="auth-title">Register</h2>
                            <p class="text-muted">Create your account</p>
                        </div>
                        
                        <form method="POST" action="<?php echo url('/register'); ?>" class="auth-form">
                            <?php echo csrf_field(); ?>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control <?php echo has_error('username') ? 'is-invalid' : ''; ?>" 
                                               id="username" name="username" value="<?php echo old('username'); ?>" required>
                                        <?php if (has_error('username')): ?>
                                            <div class="invalid-feedback"><?php echo error('username'); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control <?php echo has_error('email') ? 'is-invalid' : ''; ?>" 
                                               id="email" name="email" value="<?php echo old('email'); ?>" required>
                                        <?php if (has_error('email')): ?>
                                            <div class="invalid-feedback"><?php echo error('email'); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control <?php echo has_error('full_name') ? 'is-invalid' : ''; ?>" 
                                       id="full_name" name="full_name" value="<?php echo old('full_name'); ?>" required>
                                <?php if (has_error('full_name')): ?>
                                    <div class="invalid-feedback"><?php echo error('full_name'); ?></div>
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
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control <?php echo has_error('confirm_password') ? 'is-invalid' : ''; ?>" 
                                       id="confirm_password" name="confirm_password" required>
                                <?php if (has_error('confirm_password')): ?>
                                    <div class="invalid-feedback"><?php echo error('confirm_password'); ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select <?php echo has_error('role') ? 'is-invalid' : ''; ?>" 
                                        id="role" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="staff" <?php echo old('role') == 'staff' ? 'selected' : ''; ?>>Staff</option>
                                    <option value="waiter" <?php echo old('role') == 'waiter' ? 'selected' : ''; ?>>Waiter</option>
                                    <option value="chef" <?php echo old('role') == 'chef' ? 'selected' : ''; ?>>Chef</option>
                                    <option value="branch_manager" <?php echo old('role') == 'branch_manager' ? 'selected' : ''; ?>>Branch Manager</option>
                                </select>
                                <?php if (has_error('role')): ?>
                                    <div class="invalid-feedback"><?php echo error('role'); ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <label for="branch_id" class="form-label">Branch (Optional)</label>
                                <select class="form-select" id="branch_id" name="branch_id">
                                    <option value="">Select Branch</option>
                                    <?php if (!empty($branches)): ?>
                                        <?php foreach ($branches as $branch): ?>
                                            <option value="<?php echo $branch['id']; ?>" 
                                                    <?php echo old('branch_id') == $branch['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($branch['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Register</button>
                            </div>
                        </form>
                        
                        <div class="auth-links mt-4 text-center">
                            <p class="mb-0">
                                Already have an account? <a href="<?php echo url('/login'); ?>" class="text-decoration-none">Login here</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>