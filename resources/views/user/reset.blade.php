<!-- Reset Password Form - Using Custom CSS Classes -->

{{-- Modal Header with Same Animation as Create/Edit View --}}
<div class="page-header-premium fade-in modal-header-style">
    <div class="header-content header-content-modal">
        <div class="header-left">
            <div class="header-icon header-icon-modal">
                <i class="fas fa-key"></i>
            </div>
            <div class="header-text">
                <h1 class="modal-header-title">{{ __('Reset Password') }}</h1>
                <p class="modal-header-subtitle">{{ __('Create a new secure password for this account') }}</p>
            </div>
        </div>
        <div class="header-stats">
            <div class="stat-item">
                <img src="{{ $user->avatar ? asset(Storage::url('uploads/avatar/'.$user->avatar)) : asset(Storage::url('uploads/avatar/avatar.png')) }}" 
                     alt="User Avatar" 
                     class="user-avatar" style="width: 48px; height: 48px; margin: 0;">
            </div>
        </div>
    </div>
</div>

{{-- Form Container with White Background --}}
<div class="modal-form-container">
    {{-- User Info Badge - Single Row Layout --}}
    <div class="plan-info mb-4">
        <div style="display: flex; align-items: center; gap: 16px; width: 100%;">
            <img src="{{ $user->avatar ? asset(Storage::url('uploads/avatar/'.$user->avatar)) : asset(Storage::url('uploads/avatar/avatar.png')) }}" 
                 alt="User Avatar" 
                 class="user-avatar" style="width: 60px; height: 60px; margin: 0; flex-shrink: 0;">
            <div style="flex: 1; min-width: 0;">
                <div class="info-value" style="font-size: 1.1rem; margin-bottom: 4px; font-weight: 700; color: var(--primary);">{{ $user->name }}</div>
                <div class="info-label" style="margin-bottom: 0; font-size: 0.85rem;">{{ $user->email }}</div>
            </div>
            <div class="role-badge role-{{ $user->type }}" style="flex-shrink: 0;">
                <i class="fas fa-shield-alt"></i>
                {{ __('SECURE RESET') }}
            </div>
        </div>
    </div>

    {{-- Security Notice --}}
    <div class="plan-info mb-4" style="background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.1);">
        <div class="info-label" style="color: var(--danger); margin-bottom: 8px;">
            <i class="fas fa-shield-alt" style="margin-right: 8px;"></i>{{ __('Password Security Requirements') }}
        </div>
        <div class="info-label" style="margin-bottom: 8px; font-size: 0.8rem;">
            {{ __('Please ensure your new password meets the following criteria:') }}
        </div>
        <div class="info-grid" style="grid-template-columns: 1fr 1fr; gap: 8px; font-size: 0.75rem;">
            <div class="info-item" style="padding: 6px;">{{ __('At least 8 characters long') }}</div>
            <div class="info-item" style="padding: 6px;">{{ __('Contains uppercase and lowercase') }}</div>
            <div class="info-item" style="padding: 6px;">{{ __('Includes at least one number') }}</div>
            <div class="info-item" style="padding: 6px;">{{ __('Contains special character') }}</div>
        </div>
    </div>

    <form action="{{ route('user.password.update', $user->id) }}" method="POST" id="passwordResetForm">
        @csrf
        <div class="row gx-4 gy-4">
            <!-- New Password Field -->
            <div class="col-lg-6 col-md-6">
                <div class="mb-3">
                    <label class="modal-form-label required">{{ __('New Password') }}</label>
                    <div class="field-validation position-relative">
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="form-control modal-form-input" 
                               placeholder="{{ __('Enter new password') }}"
                               required 
                               autocomplete="new-password">
                        <i class="fas fa-lock modal-input-icon"></i>
                        <button type="button" class="password-toggle">
                            <i class="fas fa-eye"></i>
                        </button>
                        <i class="fas fa-check-circle validation-icon"></i>
                    </div>
                    
                    {{-- Password Strength Indicator --}}
                    <div class="plan-info mt-2" id="passwordStrengthContainer" style="display: none;">
                        <div style="height: 4px; background: #e5e7eb; border-radius: 2px; overflow: hidden; margin-bottom: 8px;">
                            <div id="passwordStrengthBar" style="height: 100%; width: 0%; transition: all 0.3s ease; border-radius: 2px;"></div>
                        </div>
                        <div class="info-label" id="passwordStrengthText" style="margin: 0; font-size: 0.75rem;"></div>
                    </div>
                    
                    @error('password')
                        <div class="modal-error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Confirm Password Field -->
            <div class="col-lg-6 col-md-6">
                <div class="mb-3">
                    <label class="modal-form-label required">{{ __('Confirm Password') }}</label>
                    <div class="field-validation position-relative">
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               class="form-control modal-form-input" 
                               placeholder="{{ __('Confirm new password') }}"
                               required 
                               autocomplete="new-password">
                        <i class="fas fa-lock modal-input-icon"></i>
                        <button type="button" class="password-toggle-confirm">
                            <i class="fas fa-eye"></i>
                        </button>
                        <i class="fas fa-check-circle validation-icon"></i>
                    </div>
                    @error('password_confirmation')
                        <div class="modal-error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="col-md-12">
                <div class="modal-button-container">
                    <button type="button" class="modal-btn modal-btn-cancel" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="modal-btn modal-btn-submit" id="submitBtn" disabled>
                        <i class="fas fa-key"></i>
                        {{ __('Update Password') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Additional CSS for Reset Password specific styling -->
<style>
/* Password toggle buttons */
.password-toggle,
.password-toggle-confirm {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    font-size: 1rem;
    z-index: 3;
    transition: all 0.3s ease;
    padding: 4px;
}

.password-toggle:hover,
.password-toggle-confirm:hover {
    color: var(--primary);
    transform: translateY(-50%) scale(1.1);
}

.field-validation:has(.password-toggle) .validation-icon,
.field-validation:has(.password-toggle-confirm) .validation-icon {
    right: 50px;
}

/* Password strength indicator colors */
.strength-weak {
    background: var(--danger) !important;
}

.strength-medium {
    background: var(--warning) !important;
}

.strength-strong {
    background: var(--success) !important;
}

/* Disabled submit button */
.modal-btn-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    pointer-events: none;
}

.modal-btn-submit.password-ready {
    background: linear-gradient(135deg, var(--success), #34d399) !important;
    animation: pulse-ready 2s infinite;
}

@keyframes pulse-ready {
    0%, 100% { 
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.15); 
        transform: translateY(-2px);
    }
    50% { 
        box-shadow: 0 12px 35px rgba(16, 185, 129, 0.25); 
        transform: translateY(-3px);
    }
}

/* Security requirements styling */
.security-requirements .info-item {
    font-size: 0.75rem;
    padding: 6px 8px;
    border-radius: 6px;
}

.security-requirements .info-item.met {
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.2);
    color: var(--success);
}

.security-requirements .info-item.unmet {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.2);
    color: var(--danger);
}

/* Password match indicator */
.password-match-indicator {
    position: absolute;
    right: 50px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.8rem;
    font-weight: 600;
    z-index: 2;
}

.password-match-indicator.match {
    color: var(--success);
}

.password-match-indicator.no-match {
    color: var(--danger);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .modal-form-container {
        padding: 24px;
    }
    
    .password-toggle,
    .password-toggle-confirm {
        right: 12px;
        font-size: 0.9rem;
    }
    
    .field-validation:has(.password-toggle) .validation-icon,
    .field-validation:has(.password-toggle-confirm) .validation-icon {
        right: 44px;
    }
}
</style>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('passwordResetForm');
    const submitBtn = document.getElementById('submitBtn');
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('password_confirmation');
    const passwordStrengthContainer = document.getElementById('passwordStrengthContainer');
    const passwordStrengthBar = document.getElementById('passwordStrengthBar');
    const passwordStrengthText = document.getElementById('passwordStrengthText');
    
    // Password strength checker
    function checkPasswordStrength(password) {
        let strength = 0;
        let feedback = [];
        
        if (password.length >= 8) {
            strength++;
        } else {
            feedback.push('8+ characters');
        }
        
        if (/[a-z]/.test(password)) {
            strength++;
        } else {
            feedback.push('lowercase');
        }
        
        if (/[A-Z]/.test(password)) {
            strength++;
        } else {
            feedback.push('uppercase');
        }
        
        if (/[0-9]/.test(password)) {
            strength++;
        } else {
            feedback.push('number');
        }
        
        if (/[^A-Za-z0-9]/.test(password)) {
            strength++;
        } else {
            feedback.push('special char');
        }
        
        return { strength, feedback };
    }
    
    // Update password strength indicator
    function updatePasswordStrength() {
        const password = passwordField.value;
        const { strength, feedback } = checkPasswordStrength(password);
        
        if (password.length === 0) {
            passwordStrengthContainer.style.display = 'none';
            return;
        }
        
        passwordStrengthContainer.style.display = 'block';
        
        // Update strength bar
        const percentage = (strength / 5) * 100;
        passwordStrengthBar.style.width = percentage + '%';
        
        // Update bar color and text
        passwordStrengthBar.className = '';
        if (strength <= 2) {
            passwordStrengthBar.classList.add('strength-weak');
            passwordStrengthText.textContent = 'Weak - Missing: ' + feedback.join(', ');
            passwordStrengthText.style.color = 'var(--danger)';
        } else if (strength <= 4) {
            passwordStrengthBar.classList.add('strength-medium');
            passwordStrengthText.textContent = 'Good - Missing: ' + feedback.join(', ');
            passwordStrengthText.style.color = 'var(--warning)';
        } else {
            passwordStrengthBar.classList.add('strength-strong');
            passwordStrengthText.textContent = 'Strong password';
            passwordStrengthText.style.color = 'var(--success)';
        }
        
        return strength;
    }
    
    // Form validation
    function updateFormValidation() {
        const password = passwordField.value;
        const confirmPassword = confirmPasswordField.value;
        
        let isValid = true;
        
        // Check password field
        const passwordValidation = passwordField.closest('.field-validation');
        if (password.length > 0) {
            const strength = updatePasswordStrength();
            
            if (strength >= 3) {
                passwordValidation.classList.add('valid');
                passwordValidation.classList.remove('invalid');
                passwordField.classList.add('is-valid');
                passwordField.classList.remove('is-invalid');
            } else {
                passwordValidation.classList.add('invalid');
                passwordValidation.classList.remove('valid');
                passwordField.classList.add('is-invalid');
                passwordField.classList.remove('is-valid');
                isValid = false;
            }
        } else {
            passwordValidation.classList.remove('valid', 'invalid');
            passwordField.classList.remove('is-valid', 'is-invalid');
            isValid = false;
        }
        
        // Check confirm password field
        const confirmValidation = confirmPasswordField.closest('.field-validation');
        if (confirmPassword.length > 0) {
            if (password === confirmPassword && password.length > 0) {
                confirmValidation.classList.add('valid');
                confirmValidation.classList.remove('invalid');
                confirmPasswordField.classList.add('is-valid');
                confirmPasswordField.classList.remove('is-invalid');
            } else {
                confirmValidation.classList.add('invalid');
                confirmValidation.classList.remove('valid');
                confirmPasswordField.classList.add('is-invalid');
                confirmPasswordField.classList.remove('is-valid');
                isValid = false;
            }
        } else {
            confirmValidation.classList.remove('valid', 'invalid');
            confirmPasswordField.classList.remove('is-valid', 'is-invalid');
            isValid = false;
        }
        
        // Update submit button state
        if (isValid) {
            submitBtn.disabled = false;
            submitBtn.classList.add('password-ready');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.remove('password-ready');
        }
    }
    
    // Add event listeners
    passwordField.addEventListener('input', updateFormValidation);
    confirmPasswordField.addEventListener('input', updateFormValidation);
        
    // Focus effects
    [passwordField, confirmPasswordField].forEach(field => {
        field.addEventListener('focus', function() {
            this.closest('.field-validation').classList.add('focused');
        });
        
        field.addEventListener('blur', function() {
            this.closest('.field-validation').classList.remove('focused');
        });
    });
    
    // Form submission handling
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        
        // Validate passwords match
        if (passwordField.value !== confirmPasswordField.value) {
            const confirmValidation = confirmPasswordField.closest('.field-validation');
            confirmValidation.classList.add('invalid');
            confirmValidation.classList.remove('valid');
            confirmPasswordField.classList.add('is-invalid');
            confirmPasswordField.classList.remove('is-valid');
            
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
            
            confirmPasswordField.style.animation = 'modal-shake 0.5s';
            setTimeout(() => {
                confirmPasswordField.style.animation = '';
            }, 500);
            
            return;
        }
        
        // Check password strength
        const { strength } = checkPasswordStrength(passwordField.value);
        if (strength < 3) {
            const passwordValidation = passwordField.closest('.field-validation');
            passwordValidation.classList.add('invalid');
            passwordValidation.classList.remove('valid');
            passwordField.classList.add('is-invalid');
            passwordField.classList.remove('is-valid');
            
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
            
            passwordField.style.animation = 'modal-shake 0.5s';
            setTimeout(() => {
                passwordField.style.animation = '';
            }, 500);
            
            return;
        }
        
        // Show success message
        const successMessage = document.createElement('div');
        successMessage.className = 'success-message';
        successMessage.innerHTML = '<i class="fas fa-check-circle"></i> {{ __("Password updated successfully!") }}';
        form.parentNode.insertBefore(successMessage, form);
        
        setTimeout(() => {
            form.submit();
        }, 1000);
    });
    
    // Ripple effect for buttons
    document.querySelectorAll('.modal-btn').forEach(button => {
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
    
    // Initialize validation
    updateFormValidation();
});
</script>