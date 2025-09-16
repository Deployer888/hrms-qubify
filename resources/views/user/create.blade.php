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

<!-- HTML Structure -->
{{-- Modal Header with Same Animation as Index --}}
<div class="page-header-premium fade-in modal-header-style">
    <div class="header-content header-content-modal">
        <div class="header-left">
            <div class="header-icon header-icon-modal">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="header-text">
                <h1 class="modal-header-title">{{ __('Create New User') }}</h1>
                <p class="modal-header-subtitle">{{ __('Add a new user to your system with the required information') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Form Container with White Background --}}
<div class="modal-form-container">
    <form method="POST" action="{{ route('user.store') }}" id="userForm">
        @csrf
        <div class="row gx-4 gy-4">
            <!-- Full Name Field -->
            <div class="col-lg-6 col-md-6">
                <div class="mb-3">
                    <label class="modal-form-label required">{{ __('Full Name') }}</label>
                    <div class="field-validation position-relative">
                        <input type="text" 
                               name="name" 
                               id="name" 
                               class="form-control modal-form-input" 
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
                    <div class="field-validation position-relative">
                        <input type="email" 
                               name="email" 
                               id="email" 
                               class="form-control modal-form-input" 
                               placeholder="{{ __('Enter email address') }}"
                               required>
                    </div>
                    @error('email')
                        <div class="modal-error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Password Field -->
            <div class="col-lg-6 col-md-6">
                <div class="mb-3">
                    <label class="modal-form-label required">{{ __('Password') }}</label>
                    <div class="field-validation position-relative">
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="form-control modal-form-input" 
                               placeholder="{{ __('Enter secure password') }}"
                               required>
                    </div>
                    @error('password')
                        <div class="modal-error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Company Name Field -->
            <div class="col-lg-6 col-md-6">
                <div class="mb-3">
                    <label class="modal-form-label">{{ __('Company Name') }}</label>
                    <div class="field-validation position-relative">
                        <input type="text" 
                               name="company_name" 
                               id="company_name" 
                               class="form-control modal-form-input" 
                               placeholder="{{ __('Enter company name (optional)') }}">
                    </div>
                    @error('company_name')
                        <div class="modal-error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Role Field -->
            @if (\Auth::user()->type != 'super admin')
            <div class="col-lg-12">
                <div class="mb-3">
                    <label class="modal-form-label required">{{ __('User Role') }}</label>
                    <div class="field-validation position-relative">
                        <select name="role" 
                                id="role" 
                                class="form-control modal-form-select" 
                                required>
                            <option value="">{{ __('Select user role') }}</option>
                            @foreach ($roles as $roleId => $roleName)
                                <option value="{{ $roleId }}">{{ $roleName }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('role')
                        <div class="modal-error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="col-md-12">
                <div class="modal-button-container">
                    <button type="button" class="modal-btn modal-btn-cancel btn-danger" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="modal-btn modal-btn-submit btn-primary" id="submitBtn">
                        <i class="fas fa-user-plus"></i>
                        {{ __('Create User') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('userForm');
    const submitBtn = document.getElementById('submitBtn');
    const formFields = form.querySelectorAll('.modal-form-input, .modal-form-select');
    
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
            setTimeout(() => {
                form.submit();
            }, 800);
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

    updateFormValidation();
});
</script>