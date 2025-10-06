<style>
    /* Modal Container */
    .modal-dialog {
        max-width: 800px;
        margin: 1.5rem auto;
        background: transparent;
        padding: 0;
        border: none;
        border-radius: 0;
    }

    .modal-content {
        border: none;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        backdrop-filter: blur(20px);
    }

    .modal-body {
        padding: 0;
    }

    /* Premium Form Container */
    .premium-form-container {
        background: white;
        border-radius: 24px;
        overflow: hidden;
        animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Form Header */
    .form-header {
        background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        padding: 32px 40px;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .form-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -30%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-10px) rotate(180deg); }
    }

    .form-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 8px;
        color: white;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .form-title i {
        width: 48px;
        height: 48px;
        background: rgba(255,255,255,0.15);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .form-subtitle {
        font-size: 14px;
        opacity: 0.9;
        color: white;
        font-weight: 400;
        margin: 0;
        position: relative;
        z-index: 2;
    }

    /* Form Body */
    .form-body {
        padding: 40px;
        background: white;
    }

    /* Premium Form Groups */
    .premium-form-group {
        margin-bottom: 24px;
        position: relative;
    }

    .premium-form-group.focused .premium-label {
        color: #2563eb;
        transform: scale(1.02);
    }

    /* Premium Labels */
    .premium-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }

    .premium-label i {
        color: #2563eb;
        font-size: 14px;
    }

    .required {
        color: #ef4444;
        font-weight: 700;
    }

    /* Premium Inputs */
    .premium-input {
        width: 100%;
        padding: 14px 18px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: white;
        color: #374151;
    }

    .premium-input:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        transform: translateY(-1px);
    }

    .premium-input::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }

    .premium-input.error {
        border-color: #ef4444;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
    }

    .font-uppercase {
        text-transform: uppercase;
        letter-spacing: 1px;
        font-family: 'Monaco', 'Menlo', monospace;
        font-weight: 600;
    }

    /* Input Group Premium */
    .input-group-premium {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-group-premium .premium-input {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-right: none;
    }

    .generate-btn {
        padding: 14px 18px;
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        color: white;
        border: 2px solid #2563eb;
        border-left: none;
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 600;
        position: relative;
        overflow: hidden;
    }

    .generate-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    }

    .generate-btn:active {
        transform: translateY(0);
    }

    /* Radio Group */
    .radio-group {
        display: flex;
        gap: 24px;
        margin-top: 8px;
    }

    .radio-item {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        padding: 12px 16px;
        border-radius: 12px;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .radio-item:hover {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.05) 0%, rgba(59, 130, 246, 0.05) 100%);
        border-color: rgba(37, 99, 235, 0.1);
    }

    .radio-item.checked {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.1) 0%, rgba(59, 130, 246, 0.1) 100%);
        border-color: rgba(37, 99, 235, 0.2);
    }

    .radio-input {
        width: 20px;
        height: 20px;
        border: 2px solid #d1d5db;
        border-radius: 50%;
        position: relative;
        transition: all 0.3s ease;
        background: white;
    }

    .radio-input.checked {
        border-color: #2563eb;
        background: #2563eb;
    }

    .radio-input.checked::after {
        content: '';
        width: 8px;
        height: 8px;
        background: white;
        border-radius: 50%;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .radio-label {
        font-weight: 600;
        color: #374151;
        font-size: 14px;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .radio-item.checked .radio-label {
        color: #2563eb;
    }

    /* Help Text */
    .help-text {
        font-size: 12px;
        color: #6b7280;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
    }

    .help-text i {
        color: #2563eb;
        font-size: 10px;
    }

    /* Error Messages */
    .error-message {
        color: #ef4444;
        font-size: 12px;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .error-message i {
        font-size: 12px;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        gap: 16px;
        justify-content: center;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid #f3f4f6;
    }

    .premium-btn {
        padding: 14px 28px;
        border-radius: 25px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
        overflow: hidden;
        min-width: 140px;
        justify-content: center;
    }

    .premium-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.6s ease-in-out;
    }

    .premium-btn:hover::before {
        left: 100%;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        color: white;
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #6b7280, #9ca3af);
        color: white;
        box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
    }

    .btn-secondary:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 8px 25px rgba(107, 114, 128, 0.4);
    }

    .loading-btn {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Success Animations */
    .premium-input.success {
        border-color: #10b981;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
    }

    .success-animation {
        animation: successPulse 0.6s ease;
    }

    @keyframes successPulse {
        0%, 100% { 
            background: white; 
        }
        50% { 
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(52, 211, 153, 0.05)); 
        }
    }

    /* Modal Dialog Centered */
    .modal-dialog-centered {
        min-height: calc(100% - 3rem);
        display: flex;
        align-items: center;
    }

    /* Hide default modal label */
    #exampleModalLabel {
        display: none;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .form-header {
            padding: 24px 20px;
        }
        
        .form-title {
            font-size: 24px;
            flex-direction: column;
            text-align: center;
            gap: 8px;
        }
        
        .form-title i {
            width: 44px;
            height: 44px;
            font-size: 18px;
        }
        
        .form-body {
            padding: 24px 20px;
        }
        
        .radio-group {
            flex-direction: column;
            gap: 12px;
        }
        
        .form-actions {
            flex-direction: column;
            gap: 12px;
        }
        
        .premium-btn {
            width: 100%;
        }
        
        .modal-dialog {
            margin: 0.5rem;
            max-width: none;
        }
    }

    @media (max-width: 576px) {
        .form-header {
            padding: 20px 16px;
        }
        
        .form-title {
            font-size: 20px;
        }
        
        .form-body {
            padding: 20px 16px;
        }
        
        .premium-input {
            padding: 12px 16px;
            font-size: 13px;
        }
        
        .generate-btn {
            padding: 12px 16px;
        }
    }

    /* Old Modal Button Overrides */
    .modal-btn-cancel,
    .modal-btn-submit {
        display: none !important;
    }

    /* Custom Scrollbar */
    .form-body::-webkit-scrollbar {
        width: 4px;
    }

    .form-body::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .form-body::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 2px;
    }

    .form-body::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
</style>

<div class="premium-form-container ">
    <div class="form-header">
        <h3 class="form-title">
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
                        <i class="fas fa-limit"></i>
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

        let isValid = true;
        let name = $('#name').val().trim();
        let discount = $('#discount').val();
        let limit = $('#limit').val();
        let isManual = $('.radio-item.checked').data('value') === 'manual';

        $('#submitBtn').html('<i class="fas fa-spinner fa-spin"></i> Creating...').addClass('loading-btn');
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
            setTimeout(() => {
                $('#submitBtn').html('<i class="fas fa-check"></i> Created!').removeClass('loading-btn');
                $('#couponForm')[0].submit();
            }, 1000);
        } else {
            $('#submitBtn').html('<i class="fas fa-plus"></i> Create Coupon').removeClass('loading-btn');
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
});
</script>
