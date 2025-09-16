{{-- Modal Header with Same Animation --}}
<div class="page-header-premium fade-in modal-header-style">
    <div class="header-content header-content-modal">
        <div class="header-left">
            <div class="header-icon header-icon-modal">
                <i class="fas fa-edit"></i>
            </div>
            <div class="header-text">
                <h1 class="modal-header-title">{{ __('Edit Plan') }}</h1>
                <p class="modal-header-subtitle">{{ __('Update subscription plan details') }}</p>
            </div>
        </div>
        <div class="header-stats">
            <div class="stat-item">
                <div class="role-badge role-admin">
                    <i class="fas fa-gem"></i>
                    {{ strtoupper($plan->name) }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Form Container --}}
<div class="modal-form-container">
    {{-- Plan Info Badge --}}
    <div class="plan-info mb-4">
        <div class="plan-info-content">
            <div class="plan-icon-container">
                @if($plan->price == 0)
                    <i class="fas fa-gift"></i>
                @else
                    <i class="fas fa-crown"></i>
                @endif
            </div>
            <div class="plan-details">
                <div class="plan-name">{{ $plan->name }}</div>
                <div class="plan-pricing">
                    {{ (!empty(env('CURRENCY_SYMBOL')) ? env('CURRENCY_SYMBOL') : '$') }}{{ $plan->price }} / {{ ucfirst($plan->duration) }}
                </div>
            </div>
            <div class="plan-limits">
                <div class="limits-label">
                    <i class="fas fa-users"></i>{{ __('LIMITS') }}
                </div>
                <div class="limits-value">
                    {{ $plan->max_users == -1 ? '∞' : $plan->max_users }} Users, {{ $plan->max_employees == -1 ? '∞' : $plan->max_employees }} Employees
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('plans.update', $plan->id) }}" id="planEditForm">
        @csrf
        @method('PUT')
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
                               value="{{ $plan->name }}"
                               placeholder="{{ __('Enter Plan Name') }}"
                               required>
                        <i class="fas fa-tag modal-input-icon"></i>
                        <i class="fas fa-check-circle validation-icon"></i>
                    </div>
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
                               value="{{ $plan->price }}"
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
                                <option value="{{ $key }}" {{ $plan->duration == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-check-circle validation-icon validation-icon-select"></i>
                    </div>
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
                               value="{{ $plan->max_users }}"
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
                               value="{{ $plan->max_employees }}"
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
                                  placeholder="{{ __('Describe the features and benefits...') }}">{!! $plan->description !!}</textarea>
                    </div>
                    <div id="charCount" class="description-char-count">
                        {{ __('Provide detailed plan description') }}
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
                    <button type="submit" class="modal-btn modal-btn-submit modal-btn-update" id="submitBtn">
                        <i class="fas fa-save"></i>
                        {{ __('Update Plan') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    const form = $('#planEditForm');
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
                    'font-family': 'inherit'
                });
                
                // Style the editor container
                const editorContainer = $(editor.ui.element);
                editorContainer.css({
                    'border-radius': '12px',
                    'border': '2px solid #e5e7eb',
                    'overflow': 'hidden',
                    'transition': 'all 0.3s ease'
                });
                
                // Focus/blur events for CKEditor
                editor.ui.focusTracker.on('change:isFocused', (evt, name, isFocused) => {
                    const validationContainer = descriptionTextarea.closest('.field-validation');
                    if (isFocused) {
                        validationContainer.addClass('focused');
                        editorContainer.css({
                            'border-color': 'var(--primary)',
                            'box-shadow': '0 0 0 3px rgba(37, 99, 235, 0.1)',
                            'transform': 'translateY(-1px)'
                        });
                    } else {
                        validationContainer.removeClass('focused');
                        editorContainer.css({
                            'border-color': '#e5e7eb',
                            'box-shadow': 'none',
                            'transform': 'translateY(0)'
                        });
                    }
                });
            })
            .catch(error => {
                console.error('CKEditor initialization error:', error);
                // Fallback to regular textarea
                descriptionTextarea.on('input', updateCharCount);
            });
    } else {
        console.warn('CKEditor not loaded, using regular textarea');
        descriptionTextarea.on('input', updateCharCount);
    }
    
    // Store original values for change detection
    const originalValues = {};
    formFields.each(function() {
        originalValues[$(this).attr('name')] = $(this).val();
    });
    
    // Store original description value
    originalValues['description'] = descriptionTextarea.val();

    // Price preview functionality
    function updatePricePreview() {
        const price = parseFloat(priceInput.val()) || 0;
        const duration = durationSelect.find('option:selected').text();
        
        if (price === 0) {
            pricePreview.text('✨ Free Plan').css('color', 'var(--success)');
        } else {
            pricePreview.text(`💰 $${price.toFixed(2)} per ${duration.toLowerCase()}`).css('color', 'var(--primary)');
        }
    }
    
    priceInput.on('input', updatePricePreview);
    durationSelect.on('change', updatePricePreview);

    // Unlimited toggles functionality
    unlimitedUsers.on('change', function() {
        if ($(this).is(':checked')) {
            usersInput.val(-1).prop('disabled', true).css('opacity', '0.5');
        } else {
            usersInput.prop('disabled', false).css('opacity', '1');
            const originalValue = originalValues['max_users'];
            usersInput.val(originalValue != -1 ? originalValue : '');
        }
        updateFormValidation();
    });
    
    unlimitedEmployees.on('change', function() {
        if ($(this).is(':checked')) {
            employeesInput.val(-1).prop('disabled', true).css('opacity', '0.5');
        } else {
            employeesInput.prop('disabled', false).css('opacity', '1');
            const originalValue = originalValues['max_employees'];
            employeesInput.val(originalValue != -1 ? originalValue : '');
        }
        updateFormValidation();
    });

    // Character counter for description
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
                    .css('color', currentLength > maxLength ? 'var(--danger)' : 'var(--warning)');
        } else {
            charCount.text('Provide detailed plan description with rich formatting')
                    .css('color', '');
        }
    }

    // Form validation and change detection
    function updateFormValidation() {
        // Check for changes
        let hasChanges = false;
        
        // Check regular form fields
        formFields.each(function() {
            const field = $(this);
            if (field.prop('disabled')) return; // Skip disabled fields
            
            const fieldName = field.attr('name');
            if (originalValues[fieldName] !== field.val()) {
                hasChanges = true;
                // Add change indicator
                const parentElement = field.parent();
                if (!parentElement.find('.change-indicator').length) {
                    const indicator = $('<div class="change-indicator"></div>');
                    parentElement.append(indicator);
                }
            } else {
                // Remove change indicator
                field.parent().find('.change-indicator').remove();
            }
        });
        
        // Check CKEditor content
        if (ckEditor) {
            const currentDescription = ckEditor.getData();
            if (originalValues['description'] !== currentDescription) {
                hasChanges = true;
                const validationContainer = descriptionTextarea.closest('.field-validation');
                if (!validationContainer.find('.change-indicator').length) {
                    const indicator = $('<div class="change-indicator"></div>');
                    validationContainer.append(indicator);
                }
            } else {
                descriptionTextarea.closest('.field-validation').find('.change-indicator').remove();
            }
        }
        
        // Update submit button state
        if (hasChanges) {
            submitBtn.addClass('has-changes').html('<i class="fas fa-save"></i> {{ __("Save Changes") }}');
        } else {
            submitBtn.removeClass('has-changes').html('<i class="fas fa-save"></i> {{ __("Update Plan") }}');
        }
        
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

    // Warn about unsaved changes
    $(window).on('beforeunload', function(e) {
        let hasChanges = false;
        
        // Check regular form fields
        formFields.each(function() {
            const field = $(this);
            const fieldName = field.attr('name');
            if (!field.prop('disabled') && originalValues[fieldName] !== field.val()) {
                hasChanges = true;
                return false; // Break out of each loop
            }
        });
        
        // Check CKEditor content
        if (ckEditor && originalValues['description'] !== ckEditor.getData()) {
            hasChanges = true;
        }
        
        if (hasChanges) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Initialize
    updatePricePreview();
    updateFormValidation();
    
    // Bootstrap tooltips with jQuery
    if (typeof bootstrap !== 'undefined') {
        $('[data-bs-toggle="tooltip"]').each(function() {
            new bootstrap.Tooltip(this);
        });
    }
});
</script>