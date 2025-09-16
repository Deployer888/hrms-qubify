<div class="premium-form-container fade-in">
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
