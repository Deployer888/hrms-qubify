<style>
/* Premium Modal Styling */
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

.required {
    color: #ef4444;
    font-weight: 700;
}

/* Input Styling */
.premium-input {
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

.premium-input:focus {
    border-color: #2563eb;
    background: white;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    transform: translateY(-1px);
}

.premium-input.error {
    border-color: #ef4444;
    background: #fef2f2;
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.premium-input.font-uppercase {
    text-transform: uppercase;
    letter-spacing: 1px;
    font-family: 'Courier New', monospace;
    font-weight: 600;
}

.premium-input.success {
    border-color: #10b981;
    background: #f0fdf4;
}

.success-animation {
    animation: successPulse 0.6s ease-in-out;
}

@keyframes successPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

/* Help Text */
.help-text {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #6b7280;
    margin-top: 6px;
    font-style: italic;
}

.help-text i {
    color: #9ca3af;
    font-size: 11px;
}

/* Radio Group */
.radio-group {
    display: flex;
    gap: 20px;
    margin-top: 8px;
}

.radio-item {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    background: #f9fafb;
    transition: all 0.3s ease;
    flex: 1;
}

.radio-item:hover {
    border-color: #2563eb;
    background: #eff6ff;
    transform: translateY(-1px);
}

.radio-item.checked {
    border-color: #2563eb;
    background: #eff6ff;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
}

.radio-input {
    width: 20px;
    height: 20px;
    border: 2px solid #d1d5db;
    border-radius: 50%;
    position: relative;
    transition: all 0.3s ease;
}

.radio-input.checked {
    border-color: #2563eb;
    background: #2563eb;
}

.radio-input.checked::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 8px;
    height: 8px;
    background: white;
    border-radius: 50%;
    transform: translate(-50%, -50%);
}

.radio-label {
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    margin: 0;
}

/* Input Group Premium */
.input-group-premium {
    display: flex;
    align-items: stretch;
    position: relative;
}

.input-group-premium .premium-input {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-right: none;
    flex: 1;
}

.generate-btn {
    background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
    border: 2px solid #2563eb;
    border-left: none;
    border-top-right-radius: 10px;
    border-bottom-right-radius: 10px;
    color: white;
    padding: 12px 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.generate-btn:hover {
    background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.generate-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
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
    
    .radio-group {
        flex-direction: column;
        gap: 12px;
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
            <i class="fas fa-ticket-alt"></i>
            {{ __('Create New Coupon') }}
        </h3>
        <p class="form-subtitle">{{ __('Create discount coupons to attract more customers') }}</p>
    </div>

    <div class="form-body">
        <form action="{{ url('coupons') }}" method="POST" id="couponForm">
            @csrf
            <div class="row">
                <!-- Name input -->
                <div class="premium-form-group col-md-12">
                    <label for="name" class="premium-label">
                        <i class="fas fa-tag"></i>
                        {{ __('Coupon Name') }}
                        <span class="required">*</span>
                    </label>
                    <input type="text" name="name" id="name" class="premium-input" required placeholder="Enter coupon name">
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
                    <input type="number" name="discount" id="discount" class="premium-input" required step="0.01" placeholder="0.00">
                    <div class="help-text">
                        <i class="fas fa-info-circle"></i>
                        {{ __('Discount percentage (e.g., 20 for 20%)') }}
                    </div>
                </div>

                <div class="premium-form-group col-md-6">
                    <label for="limit" class="premium-label">
                        <i class="fas fa-users"></i>
                        {{ __('Usage Limit') }}
                        <span class="required">*</span>
                    </label>
                    <input type="number" name="limit" id="limit" class="premium-input" required placeholder="100">
                    <div class="help-text">
                        <i class="fas fa-info-circle"></i>
                        {{ __('Maximum number of times this coupon can be used') }}
                    </div>
                </div>

                <!-- Code generation option -->
                <div class="premium-form-group col-md-12">
                    <label class="premium-label">
                        <i class="fas fa-code"></i>
                        {{ __('Code Generation') }}
                        <span class="required">*</span>
                    </label>
                    <div class="radio-group">
                        <div class="radio-item" data-value="manual">
                            <div class="radio-input checked" id="manual-radio"></div>
                            <label class="radio-label" for="manual-radio">{{ __('Manual Entry') }}</label>
                        </div>
                        <div class="radio-item" data-value="auto">
                            <div class="radio-input" id="auto-radio"></div>
                            <label class="radio-label" for="auto-radio">{{ __('Auto Generate') }}</label>
                        </div>
                    </div>
                </div>

                <!-- Manual code input -->
                <div class="premium-form-group col-md-12 d-block" id="manual">
                    <label for="manualCode" class="premium-label">
                        <i class="fas fa-keyboard"></i>
                        {{ __('Enter Code') }}
                    </label>
                    <input class="premium-input font-uppercase" name="manualCode" type="text" id="manualCode" placeholder="ENTER-CODE-HERE">
                    <div class="help-text">
                        <i class="fas fa-info-circle"></i>
                        {{ __('Enter a unique coupon code (letters and numbers only)') }}
                    </div>
                </div>

                <!-- Auto-generated code input -->
                <div class="premium-form-group col-md-12 d-none" id="auto">
                    <label for="autoCode" class="premium-label">
                        <i class="fas fa-magic"></i>
                        {{ __('Generated Code') }}
                    </label>
                    <div class="input-group-premium">
                        <input class="premium-input font-uppercase" name="autoCode" type="text" id="auto-code" readonly placeholder="CODE-WILL-BE-GENERATED">
                        <button type="button" class="generate-btn" id="code-generate" title="Generate New Code">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <div class="help-text">
                        <i class="fas fa-info-circle"></i>
                        {{ __('Click the generate button to create a random code') }}
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="col-12">
                    <div class="form-actions">
                        <button type="button" class="premium-btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i>
                            Cancel
                        </button>
                        <button type="submit" class="premium-btn btn-primary" id="submitBtn">
                            <i class="fas fa-plus"></i>
                            {{ __('Create Coupon') }}
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
    // Radio button functionality
    $('.radio-item').on('click', function () {
        let value = $(this).data('value');

        $('.radio-item .radio-input').removeClass('checked');
        $('.radio-item').removeClass('checked');

        $(this).find('.radio-input').addClass('checked');
        $(this).addClass('checked');

        if (value === 'manual') {
            $('#manual').removeClass('d-none').addClass('d-block');
            $('#auto').removeClass('d-block').addClass('d-none');
        } else {
            $('#auto').removeClass('d-none').addClass('d-block');
            $('#manual').removeClass('d-block').addClass('d-none');
        }
    });

    // Code generation functionality
    $('#code-generate').on('click', function () {
        let $btn = $(this);
        $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

        setTimeout(function () {
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let result = '';
            for (let i = 0; i < 10; i++) {
                result += characters.charAt(Math.floor(Math.random() * characters.length));
            }

            $('#auto-code').val(result).addClass('success');
            $btn.html('<i class="fas fa-sync-alt"></i>').prop('disabled', false);
            $('#auto-code').addClass('success-animation');

            setTimeout(() => {
                $('#auto-code').removeClass('success-animation');
            }, 600);
        }, 500);
    });

    // Form validation
    $('#couponForm').on('submit', function (e) {
        e.preventDefault();
        e.stopPropagation();

        let isValid = true;
        let name = $('#name').val().trim();
        let discount = $('#discount').val();
        let limit = $('#limit').val();
        let isManual = $('.radio-item.checked').data('value') === 'manual';

        $('#submitBtn').html('<i class="fas fa-spinner fa-spin"></i> Creating...').addClass('loading-btn').prop('disabled', true);
        $('.premium-input').removeClass('error');
        $('.error-message').remove();

        if (!name) {
            showError('name', 'Coupon name is required');
            isValid = false;
        }

        if (!discount || discount <= 0 || discount > 100) {
            showError('discount', 'Discount must be between 0 and 100');
            isValid = false;
        }

        if (!limit || limit <= 0) {
            showError('limit', 'Usage limit must be greater than 0');
            isValid = false;
        }

        if (isManual) {
            let manualCode = $('#manualCode').val().trim();
            if (!manualCode) {
                showError('manualCode', 'Coupon code is required');
                isValid = false;
            }
        } else {
            let autoCode = $('#auto-code').val().trim();
            if (!autoCode) {
                showError('auto-code', 'Please generate a code first');
                isValid = false;
            }
        }

        if (isValid) {
            // Submit the form properly
            this.submit();
        } else {
            $('#submitBtn').html('<i class="fas fa-plus"></i> Create Coupon').removeClass('loading-btn').prop('disabled', false);
        }
    });

    function showError(inputId, message) {
        let $input = $('#' + inputId);
        $input.addClass('error');
        let errorDiv = $('<div class="error-message"><i class="fas fa-exclamation-circle"></i> ' + message + '</div>');
        $input.parent().append(errorDiv);
    }

    // Input focus animations
    $('.premium-input').on('focus', function () {
        $(this).closest('.premium-form-group').addClass('focused');
    }).on('blur', function () {
        $(this).closest('.premium-form-group').removeClass('focused');
        $(this).removeClass('error');
    }).on('input', function () {
        $(this).removeClass('error');
        $(this).siblings('.error-message').remove();
    });

    // Ripple effect
    $('.premium-btn, .generate-btn').on('click', function (e) {
        let $button = $(this);
        let offset = $button.offset();
        let size = Math.max($button.outerWidth(), $button.outerHeight());
        let x = e.pageX - offset.left - size / 2;
        let y = e.pageY - offset.top - size / 2;

        let $ripple = $('<span class="ripple"></span>').css({
            width: size + 'px',
            height: size + 'px',
            left: x + 'px',
            top: y + 'px'
        });

        $button.append($ripple);
        setTimeout(() => $ripple.remove(), 600);
    });

    // Inject ripple CSS
    $('<style>')
        .prop('type', 'text/css')
        .html(`
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
        }`)
        .appendTo('head');
    }, 100); // Small delay to ensure modal is ready
});
</script>
