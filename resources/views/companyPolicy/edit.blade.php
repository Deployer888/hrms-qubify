<style>
/* Premium Modal Styling - Matching Create Modal */
.premium-form-container {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    max-width: 100%;
    margin: 0;
}

.page-header-premium {
    background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
    padding: 24px 30px;
    color: white;
    position: relative;
    overflow: hidden;
}

.page-header-premium::before {
    content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: conic-gradient(from 0deg at 50% 50%, transparent 0deg, rgba(255, 255, 255, 0.1) 60deg, transparent 120deg, rgba(255, 255, 255, 0.05) 180deg, transparent 240deg, rgba(255, 255, 255, 0.1) 300deg, transparent 360deg);
        animation: rotateBg 25s 
    linear infinite;
        pointer-events: none;;
}


    @keyframes rotateBg {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    } 50% { transform: translateY(-20px) rotate(180deg); }

/* Fade-in animation for the header */
.fade-in {
    animation: fadeInHeader 0.6s ease-out;
}

@keyframes fadeInHeader {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.form-title {
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 12px;
    position: relative;
    z-index: 3;
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
    z-index: 3;
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
.premium-input, .premium-select, .premium-textarea {
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

.premium-input:focus, .premium-select:focus, .premium-textarea:focus {
    border-color: #2563eb;
    background: white;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    transform: translateY(-1px);
}

.premium-textarea {
    resize: vertical;
    min-height: 100px;
}

/* File Upload Styling */
.premium-file-upload {
    position: relative;
    display: inline-block;
    width: 100%;
}

.premium-file-input {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.premium-file-label {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    border: 2px dashed #d1d5db;
    border-radius: 10px;
    background: #f9fafb;
    color: #6b7280;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    min-height: 50px;
}

.premium-file-label:hover {
    border-color: #2563eb;
    background: #eff6ff;
    color: #2563eb;
}

.premium-file-label i {
    font-size: 18px;
}

.file-name-display {
    margin-top: 8px;
    font-size: 12px;
    color: #6b7280;
    font-style: italic;
}

.current-file-info {
    margin-top: 8px;
    padding: 8px 12px;
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    border-radius: 6px;
    font-size: 12px;
    color: #0369a1;
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

/* Field Validation States */
.field-validation {
    position: relative;
}

.field-validation.focused .premium-input,
.field-validation.focused .premium-select,
.field-validation.focused .premium-textarea {
    border-color: #2563eb;
    background: white;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Responsive Design */
@media (max-width: 768px) {
    .premium-form-container {
        margin: 10px;
        border-radius: 12px;
    }
    
    .page-header-premium {
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
    .page-header-premium {
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
            <i class="fas fa-edit"></i>
            {{ __('Edit Company Policy') }}
        </h3>
        <p class="form-subtitle">{{ __('Update company policy information and attachments') }}</p>
    </div>

    <div class="form-body">
        <form action="{{ route('company-policy.update', $companyPolicy->id) }}" method="POST" enctype="multipart/form-data" id="policyEditForm">
            @csrf
            @method('PUT')
            <div class="row">
                <!-- Branch Field -->
                <div class="premium-form-group col-md-6">
                    <label for="branch" class="premium-label required">
                        <i class="fas fa-code-branch"></i>
                        {{ __('Branch') }}
                    </label>
                    <div class="field-validation">
                        <select name="branch" id="branch" class="premium-select select2" required>
                            <option value="0" {{ $companyPolicy->branch == 0 ? 'selected' : '' }}>{{ __('All') }}</option>
                            @foreach ($branches as $id => $name)
                                <option value="{{ $id }}" {{ $id == $companyPolicy->branch ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('branch')
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <!-- Title Field -->
                <div class="premium-form-group col-md-6">
                    <label for="title" class="premium-label required">
                        <i class="fas fa-heading"></i>
                        {{ __('Title') }}
                    </label>
                    <div class="field-validation">
                        <input type="text" 
                               name="title" 
                               id="title" 
                               class="premium-input" 
                               placeholder="{{ __('Enter policy title') }}"
                               value="{{ old('title', $companyPolicy->title) }}"
                               required>
                    </div>
                    @error('title')
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <!-- Description Field -->
                <div class="premium-form-group col-md-12">
                    <label for="description" class="premium-label">
                        <i class="fas fa-align-left"></i>
                        {{ __('Description') }}
                    </label>
                    <div class="field-validation">
                        <textarea name="description" 
                                  id="description" 
                                  class="premium-textarea" 
                                  placeholder="{{ __('Enter policy description (optional)') }}"
                                  rows="4">{{ old('description', $companyPolicy->description) }}</textarea>
                    </div>
                    @error('description')
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <!-- Attachment Field -->
                <div class="premium-form-group col-md-12">
                    <label for="attachment" class="premium-label">
                        <i class="fas fa-paperclip"></i>
                        {{ __('Attachment') }}
                    </label>
                    <div class="field-validation">
                        @if($companyPolicy->attachment)
                            <div class="current-file-info">
                                <i class="fas fa-file"></i>
                                {{ __('Current file') }}: {{ basename($companyPolicy->attachment) }}
                                <small class="d-block mt-1">{{ __('Upload a new file to replace the current one') }}</small>
                            </div>
                        @endif
                        <div class="premium-file-upload">
                            <input type="file" 
                                   name="attachment" 
                                   id="attachment" 
                                   class="premium-file-input"
                                   data-filename="attachment_edit"
                                   accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                            <label for="attachment" class="premium-file-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>{{ __('Choose new file here') }}</span>
                            </label>
                        </div>
                        <div class="file-name-display attachment_edit"></div>
                    </div>
                    @error('attachment')
                        <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="col-12">
                    <div class="form-actions">
                        <button type="button" class="premium-btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i>
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="premium-btn btn-primary" id="submitBtn">
                            <i class="fas fa-save"></i>
                            {{ __('Update Policy') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function () {
    // File upload handling
    $('#attachment').on('change', function() {
        const fileName = this.files[0] ? this.files[0].name : '';
        $('.attachment_edit').text(fileName || '');
        
        if (fileName) {
            $('.premium-file-label span').text(fileName);
            $('.premium-file-label').addClass('file-selected');
        } else {
            $('.premium-file-label span').text('{{ __("Choose new file here") }}');
            $('.premium-file-label').removeClass('file-selected');
        }
    });

    // Form validation and focus effects
    const form = document.getElementById('policyEditForm');
    const submitBtn = document.getElementById('submitBtn');
    const formFields = form.querySelectorAll('.premium-input, .premium-select, .premium-textarea');
    
    // Add event listeners to all form fields
    formFields.forEach(field => {
        // Add focus and blur effects
        field.addEventListener('focus', function() {
            this.closest('.field-validation').classList.add('focused');
        });
        
        field.addEventListener('blur', function() {
            this.closest('.field-validation').classList.remove('focused');
        });
    });

    // Form submission handling
    form.addEventListener('submit', function(e) {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
        submitBtn.disabled = true;
        
        // Let the form submit normally
        // The disabled state will prevent double submission
    });

    // Initialize select2 if available
    if (typeof $.fn.select2 !== 'undefined') {
        $('#branch').select2({
            placeholder: '{{ __("Select branch") }}',
            allowClear: false
        });
    }
});
</script>
