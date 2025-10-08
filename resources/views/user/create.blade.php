<style>
/* Premium Modal Styling - Matching Coupon Create */
.premium-form-container {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    max-width: 100%;
    margin: 0;
}

.modal-header-premium {
    background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
    padding: 24px 30px;
    color: white;
    position: relative;
    overflow: hidden;
}

.modal-header-premium::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

.form-title {
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 12px;
    position: relative;
    z-index: 2;
}

.form-title i {
    font-size: 28px;
    background: rgba(255, 255, 255, 0.2);
    padding: 8px;
    border-radius: 10px;
    backdrop-filter: blur(10px);
}

.form-subtitle {
    margin: 0;
    opacity: 0.9;
    font-size: 16px;
    font-weight: 400;
    position: relative;
    z-index: 2;
}

.form-body {
    padding: 30px;
}

/* Form Groups */
.premium-form-group {
    margin-bottom: 24px;
    position: relative;
}

.premium-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 14px;
}

.premium-label i {
    color: #2563eb;
    width: 16px;
    text-align: center;
}

.required::after {
    content: ' *';
    color: #ef4444;
    font-weight: 700;
}

/* Input Styling */
.premium-input, .premium-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    background: #f9fafb;
    transition: all 0.3s ease;
    outline: none;
}

.premium-input:focus, .premium-select:focus {
    border-color: #2563eb;
    background: white;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    transform: translateY(-1px);
}

.premium-input.error, .premium-select.error {
    border-color: #ef4444;
    background: #fef2f2;
    animation: shake 0.5s ease-in-out;
}

.premium-input.is-valid, .premium-select.is-valid {
    border-color: #10b981;
    background: #f0fdf4;
}

.premium-input.is-invalid, .premium-select.is-invalid {
    border-color: #ef4444;
    background: #fef2f2;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

/* Form Actions */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.premium-btn {
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    position: relative;
    overflow: hidden;
    min-width: 120px;
    justify-content: center;
}

.btn-secondary {
    background: #f3f4f6;
    color: #6b7280;
    border-color: #d1d5db;
}

.btn-secondary:hover {
    background: #e5e7eb;
    color: #374151;
    transform: translateY(-1px);
}

.btn-primary {
    background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
    color: white;
    border-color: #2563eb;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.loading-btn {
    opacity: 0.8;
    cursor: not-allowed;
}

.premium-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/* Error Messages */
.error-message {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #ef4444;
    font-size: 12px;
    margin-top: 6px;
    font-weight: 500;
}

.error-message i {
    font-size: 11px;
}

/* Field Validation States */
.field-validation {
    position: relative;
}

.field-validation.focused .premium-input,
.field-validation.focused .premium-select {
    border-color: #2563eb;
    background: white;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.field-validation.valid .premium-input,
.field-validation.valid .premium-select {
    border-color: #10b981;
    background: #f0fdf4;
}

.field-validation.invalid .premium-input,
.field-validation.invalid .premium-select {
    border-color: #ef4444;
    background: #fef2f2;
}

/* Ripple Effect */
.modal-ripple {
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

@keyframes modal-shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .premium-form-container {
        margin: 10px;
        border-radius: 12px;
    }
    
    .modal-header-premium {
        padding: 20px;
    }
    
    .form-title {
        font-size: 20px;
    }
    
    .form-body {
        padding: 20px;
    }
    
    .form-actions {
        flex-direction: column-reverse;
    }
    
    .premium-btn {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .modal-header-premium {
        padding: 16px;
    }
    
    .form-title {
        font-size: 18px;
    }
    
    .form-body {
        padding: 16px;
    }
    
    .premium-form-group {
        margin-bottom: 20px;
    }
}
</style>

<div class="premium-form-container">
    <div class="page-header-premium fade-in">
        <h3 class="form-title text-white">
            <i class="fas fa-user-plus"></i>
            {{ __('Create New User') }}
        </h3>
        <p class="form-subtitle">{{ __('Add a new user to your system with the required information') }}</p>
    </div>

    <div class="form-body">
        <form method="POST" action="{{ route('user.store') }}" id="userForm">
            @csrf
            <div class="row">
                <!-- Full Name Field -->
                <div class="premium-form-group col-md-6">
                    <label for="name" class="premium-label required">
                        <i class="fas fa-user"></i>
                        {{ __('Full Name') }}
                    </label>
                    <div class="field-validation">
                        <input type="text" 
                               name="name" 
                               id="name" 
                               class="premium-input" 
                               placeholder="{{ __('Enter full name') }}"
                               required>
                    </div>
                    @error('name')
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <!-- Email Address Field -->
                <div class="premium-form-group col-md-6">
                    <label for="email" class="premium-label required">
                        <i class="fas fa-envelope"></i>
                        {{ __('Email Address') }}
                    </label>
                    <div class="field-validation">
                        <input type="email" 
                               name="email" 
                               id="email" 
                               class="premium-input" 
                               placeholder="{{ __('Enter email address') }}"
                               required autocomplete="off">
                    </div>
                    @error('email')
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="premium-form-group col-md-6">
                    <label for="password" class="premium-label required">
                        <i class="fas fa-lock"></i>
                        {{ __('Password') }}
                    </label>
                    <div class="field-validation">
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="premium-input" 
                               placeholder="{{ __('Enter secure password') }}"
                               required autocomplete="off">
                    </div>
                    @error('password')
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <!-- Company Name Field -->
                <div class="premium-form-group col-md-6">
                    <label for="company_name" class="premium-label">
                        <i class="fas fa-building"></i>
                        {{ __('Company Name') }}
                    </label>
                    <div class="field-validation">
                        <input type="text" 
                               name="company_name" 
                               id="company_name" 
                               class="premium-input" 
                               placeholder="{{ __('Enter company name (optional)') }}">
                    </div>
                    @error('company_name')
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <!-- Role Field -->
                @if (\Auth::user()->type != 'super admin')
                <div class="premium-form-group col-md-12">
                    <label for="role" class="premium-label required">
                        <i class="fas fa-user-tag"></i>
                        {{ __('User Role') }}
                    </label>
                    <div class="field-validation">
                        <select name="role" 
                                id="role" 
                                class="premium-select" 
                                required>
                            <option value="">{{ __('Select user role') }}</option>
                            @foreach ($roles as $roleId => $roleName)
                                <option value="{{ $roleId }}">{{ $roleName }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('role')
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="col-12">
                    <div class="form-actions">
                        <button type="button" class="premium-btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i>
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="premium-btn btn-primary" id="submitBtn">
                            <i class="fas fa-user-plus"></i>
                            {{ __('Create User') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function () {
    // Small delay to ensure modal is fully loaded
    setTimeout(function() {
        const form = document.getElementById('userForm');
        const submitBtn = document.getElementById('submitBtn');
        const formFields = form.querySelectorAll('.premium-input, .premium-select');
        
        // Form validation and progress tracking
        function updateFormValidation() {
            formFields.forEach(field => {
                const validationContainer = field.closest('.field-validation');
                if (field.hasAttribute('required') && field.value.trim() !== '') {
                    validationContainer.classList.add('valid');
                    validationContainer.classList.remove('invalid');
                    field.classList.add('is-valid');
                    field.classList.remove('is-invalid');
                } else if (!field.hasAttribute('required') && field.value.trim() !== '') {
                    validationContainer.classList.add('valid');
                    validationContainer.classList.remove('invalid');
                } else if (field.hasAttribute('required')) {
                    validationContainer.classList.remove('valid');
                    field.classList.remove('is-valid');
                }
            });
        }

        // Add event listeners to all form fields
        formFields.forEach(field => {
            field.addEventListener('input', updateFormValidation);
            field.addEventListener('change', updateFormValidation);
            
            // Add focus and blur effects
            field.addEventListener('focus', function() {
                this.closest('.field-validation').classList.add('focused');
            });
            
            field.addEventListener('blur', function() {
                this.closest('.field-validation').classList.remove('focused');
                
                // Email validation
                if (this.type === 'email' && this.value) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    const validationContainer = this.closest('.field-validation');
                    
                    if (emailRegex.test(this.value)) {
                        validationContainer.classList.add('valid');
                        validationContainer.classList.remove('invalid');
                        this.classList.add('is-valid');
                        this.classList.remove('is-invalid');
                    } else {
                        validationContainer.classList.add('invalid');
                        validationContainer.classList.remove('valid');
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                    }
                }
            });
        });

        // Password strength indicator
        const passwordField = document.getElementById('password');
        if (passwordField) {
            passwordField.addEventListener('input', function() {
                const password = this.value;
                const validationContainer = this.closest('.field-validation');
                
                let strength = 0;
                if (password.length >= 8) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/[a-z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;
                
                if (strength >= 3) {
                    validationContainer.classList.add('valid');
                    validationContainer.classList.remove('invalid');
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                } else if (password.length > 0) {
                    validationContainer.classList.add('invalid');
                    validationContainer.classList.remove('valid');
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                }
            });
        }

        // Form submission handling
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
            submitBtn.classList.add('loading-btn');
            submitBtn.disabled = true;
            
            const requiredFields = form.querySelectorAll('.premium-input[required], .premium-select[required]');
            let isValid = true;
            
            // Clear previous errors
            document.querySelectorAll('.error-message').forEach(error => error.remove());
            
            requiredFields.forEach(field => {
                if (field.value.trim() === '') {
                    isValid = false;
                    const validationContainer = field.closest('.field-validation');
                    validationContainer.classList.add('invalid');
                    validationContainer.classList.remove('valid');
                    field.classList.add('is-invalid');
                    field.classList.remove('is-valid');
                    
                    // Add error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message';
                    errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> This field is required';
                    field.closest('.premium-form-group').appendChild(errorDiv);
                }
            });
            
            if (isValid) {
                // Submit the form properly
                this.submit();
            } else {
                submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Create User';
                submitBtn.classList.remove('loading-btn');
                submitBtn.disabled = false;
                
                // Shake animation for invalid fields
                document.querySelectorAll('.is-invalid').forEach(field => {
                    field.style.animation = 'shake 0.5s';
                    setTimeout(() => {
                        field.style.animation = '';
                    }, 500);
                });
            }
        });

        // Ripple effect for buttons
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
                ripple.classList.add('modal-ripple');
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // Input focus animations
        $('.premium-input, .premium-select').on('focus', function () {
            $(this).closest('.premium-form-group').addClass('focused');
        }).on('blur', function () {
            $(this).closest('.premium-form-group').removeClass('focused');
            $(this).removeClass('error');
        }).on('input', function () {
            $(this).removeClass('error');
            $(this).siblings('.error-message').remove();
        });

        updateFormValidation();
    }, 100); // Small delay to ensure modal is ready
});
</script>