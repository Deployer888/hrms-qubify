{{-- Modal Header with Same Animation --}}
<div class="page-header-premium fade-in modal-header-style">
    <div class="header-content header-content-modal">
        <div class="header-left">
            <div class="header-icon header-icon-modal">
                <i class="fas fa-gem"></i>
            </div>
            <div class="header-text">
                <h1 class="modal-header-title">{{ __('Create New Plan') }}</h1>
                <p class="modal-header-subtitle">{{ __('Design a subscription plan for your customers') }}</p>
            </div>
        </div>
        <div class="header-stats">
            <div class="stat-item">
                <div class="role-badge role-admin">
                    <i class="fas fa-plus"></i>
                    {{ __('NEW PLAN') }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Form Container --}}
<div class="modal-form-container">
    <form method="POST" action="{{ route('plans.store') }}" id="planCreateForm">
        @csrf
        <div class="row gx-4 gy-4">
            <!-- Plan Name -->
            <div class="col-lg-12">
                <div class="mb-3">
                    <label class="modal-form-label required">{{ __('Plan Name') }}</label>
                    <div class="field-validation position-relative">
                        <input type="text" 
                               name="name" 
                               id="name" 
                               class="form-control modal-form-input" 
                               placeholder="{{ __('Enter Plan Name') }}"
                               required>
                        <i class="fas fa-tag modal-input-icon"></i>
                        <i class="fas fa-check-circle validation-icon"></i>
                    </div>
                    <div class="plan-help-text">{{ __('Choose a descriptive name for your plan') }}</div>
                    @error('name')
                        <div class="modal-error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Price and Duration -->
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="modal-form-label">{{ __('Price') }}</label>
                    <div class="field-validation position-relative">
                        <input type="number" 
                               name="price" 
                               id="price" 
                               class="form-control modal-form-input price-input" 
                               placeholder="{{ __('Enter Plan Price') }}"
                               step="0.01" 
                               min="0">
                        <i class="fas fa-dollar-sign modal-input-icon"></i>
                        <i class="fas fa-check-circle validation-icon"></i>
                    </div>
                    <div class="price-help-text">{{ __('Set to 0 for free plan') }}</div>
                    <div id="pricePreview" class="price-preview-text"></div>
                    @error('price')
                        <div class="modal-error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="modal-form-label required">{{ __('Duration') }}</label>
                    <div class="field-validation position-relative">
                        <select name="duration" 
                                id="duration" 
                                class="form-control modal-form-select" 
                                required>
                            @foreach($arrDuration as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-check-circle validation-icon validation-icon-select"></i>
                    </div>
                    <div class="plan-help-text">{{ __('Select billing cycle for this plan') }}</div>
                    @error('duration')
                        <div class="modal-error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Max Users -->
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="modal-form-label required">{{ __('Maximum Users') }}</label>
                    <div class="field-validation position-relative">
                        <input type="number" 
                               name="max_users" 
                               id="max_users" 
                               class="form-control modal-form-input" 
                               placeholder="{{ __('Enter max users') }}"
                               required 
                               min="-1">
                        <i class="fas fa-users modal-input-icon"></i>
                        <i class="fas fa-check-circle validation-icon"></i>
                    </div>
                    <div class="unlimited-help-box">
                        <label class="unlimited-help-label">
                            <span class="unlimited-help-text">{{ __('-1 for Unlimited Users') }}</span>
                        </label>
                    </div>
                    @error('max_users')
                        <div class="modal-error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Max Employees -->
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="modal-form-label required">{{ __('Maximum Employees') }}</label>
                    <div class="field-validation position-relative">
                        <input type="number" 
                               name="max_employees" 
                               id="max_employees" 
                               class="form-control modal-form-input" 
                               placeholder="{{ __('Enter max employees') }}"
                               required 
                               min="-1">
                        <i class="fas fa-user-tie modal-input-icon"></i>
                        <i class="fas fa-check-circle validation-icon"></i>
                    </div>
                    <div class="unlimited-help-box">
                        <label class="unlimited-help-label">
                            <span class="unlimited-help-text">{{ __('-1 for Unlimited Employees') }}</span>
                        </label>
                    </div>
                    @error('max_employees')
                        <div class="modal-error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="col-lg-12">
                <div class="mb-3">
                    <label class="modal-form-label">{{ __('Description') }}</label>
                    <div class="field-validation position-relative">
                        <textarea name="description" 
                                  id="description" 
                                  class="form-control modal-form-input description-textarea" 
                                  rows="6" 
                                  placeholder="{{ __('Describe the features and benefits of this plan...') }}"></textarea>
                    </div>
                    <div id="charCount" class="description-char-count">
                        {{ __('Provide detailed plan description with rich formatting') }}
                    </div>
                    @error('description')
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
                    <button type="submit" class="modal-btn modal-btn-submit" id="submitBtn">
                        <i class="fas fa-plus"></i>
                        {{ __('Create Plan') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
$(document).ready(function() {
    const form = $('#planCreateForm');
    const submitBtn = $('#submitBtn');
    const formFields = $('.modal-form-input, .modal-form-select');
    const priceInput = $('#price');
    const pricePreview = $('#pricePreview');
    const durationSelect = $('#duration');
    const unlimitedUsers = $('#unlimited_users');
    const unlimitedEmployees = $('#unlimited_employees');
    const usersInput = $('#max_users');
    const employeesInput = $('#max_employees');
    const descriptionTextarea = $('#description');
    const charCount = $('#charCount');
    
    let ckEditor;
    
    // Initialize CKEditor with jQuery compatibility
    if (typeof ClassicEditor !== 'undefined') {
        ClassicEditor
            .create(descriptionTextarea[0], {
                toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'underline', '|',
                        'bulletedList', 'numberedList', '|',
                        'link', '|',
                        'blockQuote', 'insertTable', '|',
                        'undo', 'redo'
                    ]
                },
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                    ]
                },
                placeholder: 'Describe the features and benefits of this plan...',
                removePlugins: ['CKFinderUploadAdapter', 'CKFinder', 'EasyImage', 'Image', 'ImageCaption', 'ImageStyle', 'ImageToolbar', 'ImageUpload', 'MediaEmbed']
            })
            .then(editor => {
                ckEditor = editor;
                
                // Update character count
                updateCharCount();
                
                // Listen for content changes
                editor.model.document.on('change:data', () => {
                    updateCharCount();
                    updateFormValidation();
                });
                
                // Apply custom styles to CKEditor
                const editorElement = $(editor.ui.view.editable.element);
                editorElement.css({
                    'min-height': '150px',
                    'max-height': '300px',
                    'font-size': '0.9rem',
                    'font-family': 'var(--bs-body-font-family, inherit)',
                    'line-height': 'var(--bs-body-line-height, 1.5)'
                });
                
                // Style the editor container
                const editorContainer = $(editor.ui.element);
                editorContainer.css({
                    'border-radius': '12px',
                    'border': '2px solid #e5e7eb',
                    'overflow': 'hidden',
                    'transition': 'all 0.3s ease'
                }).addClass('form-control-like');
                
                // Focus/blur events for CKEditor
                editor.ui.focusTracker.on('change:isFocused', (evt, name, isFocused) => {
                    const validationContainer = descriptionTextarea.closest('.field-validation');
                    if (isFocused) {
                        validationContainer.addClass('focused');
                        editorContainer.css({
                            'border-color': 'var(--primary)',
                            'box-shadow': '0 0 0 3px rgba(37, 99, 235, 0.1)',
                            'transform': 'translateY(-1px)'
                        }).addClass('focus');
                    } else {
                        validationContainer.removeClass('focused');
                        editorContainer.css({
                            'border-color': '#e5e7eb',
                            'box-shadow': 'none',
                            'transform': 'translateY(0)'
                        }).removeClass('focus');
                    }
                });
            })
            .catch(error => {
                console.error('CKEditor initialization error:', error);
                console.log('Falling back to regular textarea');
                
                // Fallback to regular textarea
                descriptionTextarea.show().on('input', updateCharCount);
            });
    } else {
        console.warn('CKEditor not loaded, using regular textarea');
        descriptionTextarea.on('input', updateCharCount);
    }

    // Price preview functionality
    function updatePricePreview() {
        const price = parseFloat(priceInput.val()) || 0;
        const duration = durationSelect.find('option:selected').text();
        
        if (price === 0) {
            pricePreview.text('✨ Free Plan').css('color', 'var(--success)');
        } else {
            pricePreview.text(`💰 ${price.toFixed(2)} per ${duration.toLowerCase()}`).css('color', 'var(--primary)');
        }
    }
    
    priceInput.on('input', updatePricePreview);
    durationSelect.on('change', updatePricePreview);

    // Unlimited toggles functionality
    unlimitedUsers.on('change', function() {
        if ($(this).is(':checked')) {
            usersInput.val(-1).prop('disabled', true).css('opacity', '0.5').addClass('disabled');
            $('#usersHelp').html('<i class="fas fa-infinity"></i> Unlimited users enabled');
        } else {
            usersInput.prop('disabled', false).css('opacity', '1').removeClass('disabled').val('');
            $('#usersHelp').html('<i class="fas fa-infinity"></i> Use -1 for unlimited users');
        }
        updateFormValidation();
    });
    
    unlimitedEmployees.on('change', function() {
        if ($(this).is(':checked')) {
            employeesInput.val(-1).prop('disabled', true).css('opacity', '0.5').addClass('disabled');
            $('#employeesHelp').html('<i class="fas fa-infinity"></i> Unlimited employees enabled');
        } else {
            employeesInput.prop('disabled', false).css('opacity', '1').removeClass('disabled').val('');
            $('#employeesHelp').html('<i class="fas fa-infinity"></i> Use -1 for unlimited employees');
        }
        updateFormValidation();
    });

    // Character counter for description (works with both CKEditor and textarea)
    function updateCharCount() {
        let currentLength = 0;
        
        if (ckEditor) {
            const data = ckEditor.getData();
            const textOnly = data.replace(/<[^>]*>/g, ''); // Strip HTML tags for character count
            currentLength = textOnly.length;
        } else {
            currentLength = descriptionTextarea.val().length;
        }
        
        const maxLength = 1000;
        
        if (currentLength > maxLength * 0.8) {
            charCount.text(`${maxLength - currentLength} characters remaining`)
                    .css('color', currentLength > maxLength ? 'var(--danger)' : 'var(--warning)')
                    .addClass('text-warning');
        } else {
            charCount.text('Provide detailed plan description with rich formatting')
                    .css('color', '')
                    .removeClass('text-warning');
        }
    }

    // Form validation
    function updateFormValidation() {
        // Update field validation states
        formFields.each(function() {
            const field = $(this);
            if (field.prop('disabled')) return;
            
            const validationContainer = field.closest('.field-validation');
            if (field.prop('required') && field.val().trim() !== '') {
                validationContainer.addClass('valid').removeClass('invalid');
                field.addClass('is-valid').removeClass('is-invalid');
            } else if (!field.prop('required') && field.val().trim() !== '') {
                validationContainer.addClass('valid').removeClass('invalid');
                field.removeClass('is-invalid');
            } else if (field.prop('required')) {
                validationContainer.removeClass('valid');
                field.removeClass('is-valid');
            }
        });
    }

    // Add event listeners to all form fields
    formFields.on('input change', updateFormValidation);
    
    formFields.on('focus', function() {
        $(this).closest('.field-validation').addClass('focused');
    });
    
    formFields.on('blur', function() {
        $(this).closest('.field-validation').removeClass('focused');
    });

    // Form submission handling
    form.on('submit', function(e) {
        e.preventDefault();
        
        // Update hidden textarea with CKEditor content before submission
        if (ckEditor) {
            descriptionTextarea.val(ckEditor.getData());
        }
        
        submitBtn.addClass('loading').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');
        
        // Validate form
        const name = $('#name').val();
        const price = $('#price').val();
        const duration = $('#duration').val();
        const maxUsers = $('#max_users').val();
        const maxEmployees = $('#max_employees').val();
        
        let isValid = true;
        
        // Clear previous errors
        $('.modal-form-input, .modal-form-select').removeClass('is-invalid');
        $('.field-validation').removeClass('invalid');
        $('.modal-error-text').not('[data-error]').remove();
        
        // Validate name
        if (!name.trim()) {
            showError('name', 'Plan name is required');
            isValid = false;
        }
        
        // Validate price
        if (price !== '' && (isNaN(price) || price < 0)) {
            showError('price', 'Price must be a valid number (0 or greater)');
            isValid = false;
        }
        
        // Validate duration
        if (!duration) {
            showError('duration', 'Duration is required');
            isValid = false;
        }
        
        // Validate max users
        if (!maxUsers || (maxUsers != -1 && (isNaN(maxUsers) || maxUsers <= 0))) {
            showError('max_users', 'Max users must be a positive number or -1 for unlimited');
            isValid = false;
        }
        
        // Validate max employees
        if (!maxEmployees || (maxEmployees != -1 && (isNaN(maxEmployees) || maxEmployees <= 0))) {
            showError('max_employees', 'Max employees must be a positive number or -1 for unlimited');
            isValid = false;
        }
        
        if (isValid) {
            // Show success feedback
            submitBtn.html('<i class="fas fa-check"></i> Created!');
            
            setTimeout(() => {
                form[0].submit();
            }, 1000);
        } else {
            submitBtn.removeClass('loading').prop('disabled', false).html('<i class="fas fa-plus"></i> Create Plan');
            
            // jQuery shake animation
            $('.is-invalid').each(function() {
                const field = $(this);
                field.addClass('shake').css('animation', 'modal-shake 0.5s');
                setTimeout(() => {
                    field.removeClass('shake').css('animation', '');
                }, 500);
            });
        }
    });
    
    function showError(inputId, message) {
        const input = $('#' + inputId);
        const validationContainer = input.closest('.field-validation');
        
        input.addClass('is-invalid');
        validationContainer.addClass('invalid').removeClass('valid');
        
        const errorDiv = $('<div class="modal-error-text"><i class="fas fa-exclamation-circle"></i> ' + message + '</div>');
        input.closest('.mb-3').append(errorDiv);
    }

    // Ripple effect for buttons using jQuery
    $('.modal-btn').on('click', function(e) {
        const button = $(this);
        const ripple = $('<span class="modal-ripple"></span>');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.css({
            'left': x + 'px',
            'top': y + 'px'
        });
        
        button.append(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });

    // Initialize
    updatePricePreview();
    updateFormValidation();
    
    // Bootstrap 5.3.7 tooltips with jQuery
    if (typeof bootstrap !== 'undefined') {
        $('[data-bs-toggle="tooltip"]').each(function() {
            new bootstrap.Tooltip(this);
        });
    }
});
</script>