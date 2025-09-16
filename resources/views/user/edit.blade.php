<style>
    .modal-dialog {
        background: white;
        padding: 20px;
        border: 2px solid #fff;
        border-radius: 10px;
    }

    .modal-btn-cancel {
        color: #FFF;
        background-color: #FF5630;
        border-color: #FF5630;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15);
        border-radius: 7px;
        padding: 5px 15px;
    }

    .modal-btn-submit {
        color: #FFF;
        background-color: #5668d7;
        border-color: #5668d7;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15);
        border-radius: 7px;
        padding: 5px 15px;
    }

    #exampleModalLabel{
        display:none;
    }

    .modal-dialog-centered {
        min-height: calc(100% - 10.5rem);
    }
</style>


{{-- Modal Header with Same Animation as Create View --}}
<div class="page-header-premium fade-in modal-header-style">
    <div class="header-content header-content-modal">
        <div class="header-left">
            <div class="header-icon header-icon-modal">
                <i class="fas fa-user-edit"></i>
            </div>
            <div class="header-text">
                <h1 class="modal-header-title">{{ __('Edit User Account') }}</h1>
                <p class="modal-header-subtitle">{{ __('Update user information and permissions') }}</p>
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
                <i class="fas fa-user-check"></i>
                {{ strtoupper($user->type) }}
            </div>
            <div style="flex-shrink: 0; text-align: right; min-width: 140px;">
                <div class="info-label" style="margin-bottom: 2px; font-size: 0.7rem;">
                    <i class="fas fa-history" style="margin-right: 4px;"></i>{{ __('LAST UPDATED') }}
                </div>
                <div class="info-value" style="font-size: 0.8rem; color: var(--primary);">
                    {{ $user->updated_at ? $user->updated_at->format('M d, Y \a\t g:i A') : __('Never') }}
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('user.update', $user->id) }}" id="userEditForm">
        @csrf
        @method('PUT')
        <div class="row gx-4 gy-4">
            <!-- Full Name Field -->
            <div class="col-lg-6 col-md-6">
                <div class="mb-3">
                    <label class="modal-form-label required">{{ __('Full Name') }}</label>
                    <div class="field-validation position-relative valid">
                        <input type="text" 
                               name="name" 
                               id="name" 
                               class="form-control modal-form-input is-valid" 
                               value="{{ $user->name }}"
                               placeholder="{{ __('Enter full name') }}"
                               required>
                    </div>
                    @error('name')
                        <div class="modal-error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Email Address Field -->
            <div class="col-lg-6 col-md-6">
                <div class="mb-3">
                    <label class="modal-form-label required">{{ __('Email Address') }}</label>
                    <div class="field-validation position-relative valid">
                        <input type="email" 
                               name="email" 
                               id="email" 
                               class="form-control modal-form-input is-valid" 
                               value="{{ $user->email }}"
                               placeholder="{{ __('Enter email address') }}"
                               required>
                    </div>
                    @error('email')
                        <div class="modal-error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Company Name Field (if exists) -->
            @if($user->company_name || \Auth::user()->type == 'super admin')
            <div class="col-lg-6 col-md-6">
                <div class="mb-3">
                    <label class="modal-form-label {{ \Auth::user()->type == 'super admin' ? 'required' : '' }}">{{ __('Company Name') }}</label>
                    <div class="field-validation position-relative {{ $user->company_name ? 'valid' : '' }}">
                        <input type="text" 
                               name="company_name" 
                               id="company_name" 
                               class="form-control modal-form-input {{ $user->company_name ? 'is-valid' : '' }}" 
                               value="{{ $user->company_name }}"
                               placeholder="{{ __('Enter company name') }}"
                               {{ \Auth::user()->type == 'super admin' ? 'required' : '' }}>
                    </div>
                    @error('company_name')
                        <div class="modal-error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            @endif

            <!-- Role Field -->
            @if (\Auth::user()->type != 'super admin')
            <div class="col-lg-6 col-md-6">
                <div class="mb-3">
                    <label class="modal-form-label required">{{ __('User Role') }}</label>
                    <div class="field-validation position-relative valid">
                        <select name="role" 
                                id="role" 
                                class="form-control modal-form-select is-valid" 
                                required>
                            @foreach ($roles as $roleId => $roleName)
                                <option value="{{ $roleId }}" 
                                        @if ($user->roles->contains('name', $roleName)) selected @endif>
                                    {{ $roleName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('role')
                        <div class="modal-error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Password Reset Option -->
            <div class="col-lg-6 col-md-6">
                <div class="mb-3">
                    <label class="modal-form-label">{{ __('Password Management') }}</label>
                    <div class="plan-info" style="margin-bottom: 0;">
                        <div class="info-label" style="margin-bottom: 8px;">
                            <i class="fas fa-info-circle" style="color: var(--primary);"></i>
                            {{ __('Password changes require separate action for security') }}
                        </div>
                        <a href="#" 
                           data-ajax-popup="true" 
                           data-url="{{ route('user.reset', \Crypt::encrypt($user->id)) }}" 
                           data-title="{{ __('Reset User Password') }}"
                           class="upgrade-link">
                            <i class="fas fa-key"></i>
                            {{ __('Reset Password') }}
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="col-md-12">
                <div class="modal-button-container">
                    <button type="button" class="modal-btn modal-btn-cancel" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="modal-btn modal-btn-submit modal-btn-update" id="submitBtn">
                        <i class="fas fa-save"></i>
                        {{ __('Update User') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Additional CSS for Edit-specific styling -->
<style>
/* Edit form specific styles */
.modal-btn-update.has-changes {
    background: linear-gradient(135deg, #059669, #10b981) !important;
    animation: pulse-glow 2s infinite;
}

@keyframes pulse-glow {
    0%, 100% { 
        box-shadow: 0 8px 25px rgba(5, 150, 105, 0.15); 
        transform: translateY(-2px);
    }
    50% { 
        box-shadow: 0 12px 35px rgba(5, 150, 105, 0.25); 
        transform: translateY(-3px);
    }
}

.edit-user-badge {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.05), rgba(59, 130, 246, 0.05));
    border-radius: 12px;
    border: 1px solid rgba(37, 99, 235, 0.1);
    margin-bottom: 24px;
}

.change-indicator {
    position: absolute;
    top: -2px;
    right: -2px;
    width: 8px;
    height: 8px;
    background: var(--warning);
    border-radius: 50%;
    animation: pulse 2s infinite;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .plan-info > div {
        flex-direction: column !important;
        text-align: center;
        gap: 12px !important;
    }
    
    .plan-info > div > div:last-child {
        text-align: center !important;
        min-width: auto !important;
    }
    
    .role-badge {
        align-self: center;
    }
}
</style>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('userEditForm');
    const submitBtn = document.getElementById('submitBtn');
    const formFields = form.querySelectorAll('.modal-form-input, .modal-form-select');
    
    // Store original values for change detection
    const originalValues = {};
    formFields.forEach(field => {
        originalValues[field.name] = field.value;
    });

    // Form validation and change detection
    function updateFormValidation() {
        // Check for changes
        let hasChanges = false;
        formFields.forEach(field => {
            if (originalValues[field.name] !== field.value) {
                hasChanges = true;
                // Add change indicator
                if (!field.parentElement.querySelector('.change-indicator')) {
                    const indicator = document.createElement('div');
                    indicator.className = 'change-indicator';
                    field.parentElement.appendChild(indicator);
                }
            } else {
                // Remove change indicator
                const indicator = field.parentElement.querySelector('.change-indicator');
                if (indicator) {
                    indicator.remove();
                }
            }
        });
        
        // Update submit button state
        if (hasChanges) {
            submitBtn.classList.add('has-changes');
            submitBtn.innerHTML = '<i class="fas fa-save"></i> {{ __("Save Changes") }}';
        } else {
            submitBtn.classList.remove('has-changes');
            submitBtn.innerHTML = '<i class="fas fa-save"></i> {{ __("Update User") }}';
        }
        
        // Update field validation states
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

    // Form submission handling
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        
        const requiredFields = form.querySelectorAll('.modal-form-input[required], .modal-form-select[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (field.value.trim() === '') {
                isValid = false;
                const validationContainer = field.closest('.field-validation');
                validationContainer.classList.add('invalid');
                validationContainer.classList.remove('valid');
                field.classList.add('is-invalid');
                field.classList.remove('is-valid');
            }
        });
        
        if (isValid) {
            // Show success animation
            const successIndicator = document.createElement('div');
            successIndicator.className = 'success-message';
            successIndicator.innerHTML = '<i class="fas fa-check-circle"></i> {{ __("User information updated successfully!") }}';
            form.parentNode.insertBefore(successIndicator, form);
            
            setTimeout(() => {
                form.submit();
            }, 1000);
        } else {
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
            
            document.querySelectorAll('.is-invalid').forEach(field => {
                field.style.animation = 'modal-shake 0.5s';
                setTimeout(() => {
                    field.style.animation = '';
                }, 500);
            });
        }
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

    // Warn about unsaved changes
    window.addEventListener('beforeunload', function(e) {
        let hasChanges = false;
        formFields.forEach(field => {
            if (originalValues[field.name] !== field.value) {
                hasChanges = true;
            }
        });
        
        if (hasChanges) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Initialize validation
    updateFormValidation();
});
</script>