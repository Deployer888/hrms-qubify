<div class="premium-form-container fade-in">
    <div class="form-header">
        <h3 class="form-title">
            <i class="fas fa-edit"></i>
            {{ __('Edit Coupon') }}
        </h3>
        <p class="form-subtitle">{{ __('Update coupon details and settings') }}</p>
    </div>

    <div class="form-body">
        <form action="{{ route('coupons.update', $coupon->id) }}" method="POST" id="editCouponForm">
            @csrf
            @method('PUT')
            <div class="row">
                <!-- Name input -->
                <div class="premium-form-group col-md-12">
                    <label for="name" class="premium-label">
                        <i class="fas fa-tag"></i>
                        {{ __('Coupon Name') }}
                        <span class="required">*</span>
                    </label>
                    <input type="text" name="name" id="name" class="premium-input" required 
                           value="{{ old('name', $coupon->name) }}" placeholder="Enter coupon name">
                    <div class="help-text">
                        <i class="fas fa-info-circle"></i>
                        {{ __('Choose a memorable name for your coupon') }}
                    </div>
                </div>

                <!-- Discount and Limit inputs -->
                <div class="premium-form-group col-md-6">
                    <label for="discount" class="premium-label">
                        <i class="fas fa-percent"></i>
                        {{ __('Discount') }}
                        <span class="required">*</span>
                    </label>
                    <input type="number" name="discount" id="discount" class="premium-input" required step="0.01" 
                           value="{{ old('discount', $coupon->discount) }}" placeholder="0.00">
                    <div class="help-text">
                        <i class="fas fa-info-circle"></i>
                        {{ __('Discount percentage (e.g., 20 for 20%)') }}
                    </div>
                </div>

                <div class="premium-form-group col-md-6">
                    <label for="limit" class="premium-label">
                        <i class="fas fa-limit"></i>
                        {{ __('Usage Limit') }}
                        <span class="required">*</span>
                    </label>
                    <input type="number" name="limit" id="limit" class="premium-input" required 
                           value="{{ old('limit', $coupon->limit) }}" placeholder="100">
                    <div class="help-text">
                        <i class="fas fa-info-circle"></i>
                        {{ __('Maximum number of times this coupon can be used') }}
                    </div>
                </div>

                <!-- Code input -->
                <div class="premium-form-group col-md-12">
                    <label for="code" class="premium-label">
                        <i class="fas fa-code"></i>
                        {{ __('Coupon Code') }}
                    </label>
                    <div class="premium-label">
                        <span class="coupon-code">
                            {{ __($coupon->code) }}
                        </span>
                    </div>
                    <div class="help-text">
                        <i class="fas fa-info-circle"></i>
                        {{ __('Unique coupon code (letters and numbers only)') }}
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="col-12">
                    <div class="form-actions">
                        <button type="button" class="premium-btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i>
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="premium-btn btn-warning" id="updateBtn">
                            <i class="fas fa-save"></i>
                            {{ __('Update Coupon') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form validation
        const form = document.getElementById('editCouponForm');
        const updateBtn = document.getElementById('updateBtn');
        
        // Store original values to detect changes
        const originalValues = {
            name: document.getElementById('name').value,
            discount: document.getElementById('discount').value,
            limit: document.getElementById('limit').value,
            code: document.getElementById('code').value
        };
        
        // Detect changes and highlight updated fields
        function detectChanges() {
            const inputs = ['name', 'discount', 'limit', 'code'];
            let hasChanges = false;
            
            inputs.forEach(inputId => {
                const input = document.getElementById(inputId);
                const label = input.parentNode.querySelector('.premium-label');
                
                if (input.value !== originalValues[inputId]) {
                    label.classList.add('updated-label');
                    hasChanges = true;
                } else {
                    label.classList.remove('updated-label');
                }
            });
            
            // Enable/disable update button based on changes
            if (hasChanges) {
                updateBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
                updateBtn.style.background = 'linear-gradient(135deg, var(--warning), #fbbf24)';
            } else {
                updateBtn.innerHTML = '<i class="fas fa-save"></i> Update Coupon';
                updateBtn.style.background = 'linear-gradient(135deg, var(--text-secondary), #9ca3af)';
            }
        }
        
        // Add change listeners
        ['name', 'discount', 'limit', 'code'].forEach(inputId => {
            document.getElementById(inputId).addEventListener('input', detectChanges);
        });
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Add loading state
            updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            updateBtn.classList.add('loading-btn');
            
            // Validate form
            const name = document.getElementById('name').value;
            const discount = document.getElementById('discount').value;
            const limit = document.getElementById('limit').value;
            const code = document.getElementById('code').value;
            
            let isValid = true;
            
            // Clear previous errors
            document.querySelectorAll('.premium-input').forEach(input => {
                input.classList.remove('error');
            });
            document.querySelectorAll('.error-message').forEach(msg => msg.remove());
            
            // Validate name
            if (!name.trim()) {
                showError('name', 'Coupon name is required');
                isValid = false;
            }
            
            // Validate discount
            if (!discount || discount <= 0 || discount > 100) {
                showError('discount', 'Discount must be between 0 and 100');
                isValid = false;
            }
            
            // Validate limit
            if (!limit || limit <= 0) {
                showError('limit', 'Usage limit must be greater than 0');
                isValid = false;
            }
            
            // Validate code
            if (!code.trim()) {
                showError('code', 'Coupon code is required');
                isValid = false;
            } else if (!/^[A-Z0-9\-]+$/.test(code.trim())) {
                showError('code', 'Coupon code can only contain letters, numbers, and hyphens');
                isValid = false;
            }
            
            if (isValid) {
                // Simulate form submission
                setTimeout(() => {
                    // Reset button
                    updateBtn.innerHTML = '<i class="fas fa-check"></i> Updated!';
                    updateBtn.classList.remove('loading-btn');
                    
                    // Actually submit the form
                    form.submit();
                }, 1000);
            } else {
                // Reset button
                updateBtn.innerHTML = '<i class="fas fa-save"></i> Update Coupon';
                updateBtn.classList.remove('loading-btn');
            }
        });
        
        function showError(inputId, message) {
            const input = document.getElementById(inputId);
            input.classList.add('error');
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
            
            input.parentNode.appendChild(errorDiv);
        }

        // Input focus animations
        document.querySelectorAll('.premium-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentNode.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentNode.classList.remove('focused');
                this.classList.remove('error');
            });
            
            input.addEventListener('input', function() {
                this.classList.remove('error');
                const errorMsg = this.parentNode.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            });
        });

        // Auto-format code input
        document.getElementById('code').addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9\-]/g, '');
        });

        // Button ripple effect
        document.querySelectorAll('.premium-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple');
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // Initial change detection
        detectChanges();
    });

    // Add CSS for ripple effect
    const style = document.createElement('style');
    style.textContent = `
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
</script>