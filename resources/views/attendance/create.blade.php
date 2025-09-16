<style>
    /* Clean Modal Styling */
    .attendance-form-container {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        max-width: 600px;
        margin: 0 auto;
    }

    .attendance-form-header {
        background: #fff;
        padding: 2rem 2rem 1rem 2rem;
        text-align: left;
    }

    .attendance-form-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 0.5rem 0;
        letter-spacing: -0.025em;
    }

    .attendance-form-header p {
        font-size: 1rem;
        color: #6b7280;
        margin: 0;
        font-weight: 400;
    }

    .attendance-form-body {
        padding: 1rem 2rem 2rem 2rem;
        background: #fff;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .form-group-clean {
        display: flex;
        flex-direction: column;
    }

    .form-label-clean {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-label-clean i {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .form-control-clean {
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.875rem 1rem;
        font-size: 0.95rem;
        font-weight: 500;
        background: #fff;
        color: #374151;
        transition: all 0.3s ease;
        width: 100%;
        box-sizing: border-box;
    }

    .form-control-clean:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .form-control-clean:hover:not(:focus) {
        border-color: #9ca3af;
    }

    .form-control-clean::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }

    /* Select Styling */
    .select-clean {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.75rem center;
        background-repeat: no-repeat;
        background-size: 1.25rem;
        padding-right: 3rem;
        cursor: pointer;
    }

    .select-clean:focus {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%233b82f6' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    }

    /* Time Display Styling */
    .time-display-clean {
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.875rem 1rem;
        font-size: 0.95rem;
        font-weight: 500;
        background: #fff;
        color: #374151;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
    }

    .time-display-clean:hover {
        border-color: #9ca3af;
    }

    .time-display-clean:focus,
    .time-display-clean.active {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .time-display-clean i {
        color: #6b7280;
        font-size: 1rem;
    }

    .time-display-clean:focus i,
    .time-display-clean.active i {
        color: #3b82f6;
    }

    /* Time Picker Modal */
    .time-picker-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        backdrop-filter: blur(4px);
    }

    .time-picker-content {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        min-width: 300px;
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .time-picker-header {
        text-align: center;
        margin-bottom: 1.5rem;
        font-weight: 600;
        color: #374151;
        font-size: 1.1rem;
    }

    .time-picker-controls {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .time-input {
        width: 70px;
        text-align: center;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.75rem;
        font-weight: 600;
        font-size: 1.2rem;
        color: #374151;
    }

    .time-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .time-separator {
        font-size: 1.5rem;
        font-weight: bold;
        color: #6b7280;
    }

    .time-picker-actions {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }

    .time-picker-btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .time-picker-btn.cancel {
        background: #f3f4f6;
        color: #374151;
    }

    .time-picker-btn.cancel:hover {
        background: #e5e7eb;
        transform: translateY(-1px);
    }

    .time-picker-btn.confirm {
        background: #3b82f6;
        color: white;
    }

    .time-picker-btn.confirm:hover {
        background: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    /* Form Actions */
    .form-actions-clean {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        padding-top: 1.5rem;
        border-top: 1px solid #f3f4f6;
    }

    .btn-clean {
        padding: 0.875rem 1.75rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        min-width: 140px;
        justify-content: center;
    }

    .btn-cancel-clean {
        background: #6b7280;
        color: white;
    }

    .btn-cancel-clean:hover {
        background: #4b5563;
        transform: translateY(-1px);
        color: white;
        text-decoration: none;
    }

    .btn-primary-clean {
        background: #3b82f6;
        color: white;
    }

    .btn-primary-clean:hover {
        background: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        color: white;
        text-decoration: none;
    }

    .loading-btn {
        opacity: 0.7;
        pointer-events: none;
        cursor: not-allowed;
    }

    .loading-btn .fa-spinner {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Error States */
    .form-control-clean.error,
    .time-display-clean.error {
        border-color: #ef4444;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
    }

    .error-message {
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 0.25rem;
        font-weight: 500;
        display: none;
    }

    .error-message.show {
        display: block;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .attendance-form-container {
            margin: 1rem;
            border-radius: 8px;
        }
        
        .attendance-form-header {
            padding: 1.5rem 1.5rem 1rem 1.5rem;
        }
        
        .attendance-form-header h1 {
            font-size: 1.5rem;
        }
        
        .attendance-form-body {
            padding: 1rem 1.5rem 1.5rem 1.5rem;
        }
        
        .form-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .form-actions-clean {
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .btn-clean {
            width: 100%;
        }
        
        .time-picker-content {
            margin: 1rem;
            min-width: auto;
            width: calc(100% - 2rem);
        }
    }

    /* Hide default modal elements */
    #exampleModalLabel {
        display: none;
    }

    .close-icon {
        display: none;
    }

    .modal-body, .modal-content{
        background: #fff;
    }
</style>

<div class="attendance-form-container">
    <!-- Clean Header -->
    <div class="attendance-form-header">
        <h1>Create New Attendance</h1>
        <p>Add a new attendance to your system with the required information</p>
    </div>

    <!-- Form -->
    <form action="{{ url('attendanceemployee') }}" method="POST" id="attendanceForm">
        @csrf
        
        <div class="attendance-form-body">
            <!-- Form Grid -->
            <div class="form-grid">
                <!-- Employee Selection -->
                <div class="form-group-clean">
                    <label class="form-label-clean" for="employee_id">
                        <i class="fas fa-user"></i>
                        Employee
                    </label>
                    <select name="employee_id" id="employee_id" class="form-control-clean select-clean" required>
                        <option value="">Select Employee</option>
                        @foreach ($employees as $id => $name)
                        <option value="{{ $id }}" {{ ($id == ($employee_id ?? 0)) ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <div class="error-message" id="employee-error"></div>
                </div>

                <!-- Date Selection -->
                <div class="form-group-clean">
                    <label class="form-label-clean" for="date">
                        <i class="fas fa-calendar"></i>
                        Date
                    </label>
                    <input type="text" name="date" id="date" class="form-control-clean datepicker" value="{{$date}}" readonly required>
                    <div class="error-message" id="date-error"></div>
                </div>

                <!-- Clock In Time -->
                <div class="form-group-clean">
                    <label class="form-label-clean" for="clock_in">
                        <i class="fas fa-sign-in-alt"></i>
                        Clock In Time
                    </label>
                    <div class="time-display-clean" data-target="clock_in" id="clock_in_display" tabindex="0">
                        <span id="clock_in_text">00:00</span>
                        <i class="fas fa-clock"></i>
                    </div>
                    <input type="hidden" name="clock_in" id="clock_in" value="00:00" required>
                    <div class="error-message" id="clock_in-error"></div>
                </div>

                <!-- Clock Out Time -->
                <div class="form-group-clean">
                    <label class="form-label-clean" for="clock_out">
                        <i class="fas fa-sign-out-alt"></i>
                        Clock Out Time
                    </label>
                    <div class="time-display-clean" data-target="clock_out" id="clock_out_display" tabindex="0">
                        <span id="clock_out_text">00:00</span>
                        <i class="fas fa-clock"></i>
                    </div>
                    <input type="hidden" name="clock_out" id="clock_out" value="">
                    <div class="error-message" id="clock_out-error"></div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions-clean">
                <button type="button" class="btn-clean btn-cancel-clean" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
                <button type="submit" class="btn-clean btn-primary-clean" id="submitBtn">
                    <i class="fas fa-plus"></i>
                    Create Attendance
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Time Picker Modal -->
<div class="time-picker-modal" id="timePickerModal">
    <div class="time-picker-content">
        <div class="time-picker-header">Select Time</div>
        <div class="time-picker-controls">
            <input type="number" class="time-input" id="hourInput" min="0" max="23" value="0" placeholder="00">
            <span class="time-separator">:</span>
            <input type="number" class="time-input" id="minuteInput" min="0" max="59" value="0" placeholder="00">
        </div>
        <div class="time-picker-actions">
            <button type="button" class="time-picker-btn cancel" onclick="closeTimePicker()">Cancel</button>
            <button type="button" class="time-picker-btn confirm" onclick="confirmTime()">OK</button>
        </div>
    </div>
</div>

<script>
// Ensure jQuery is available or provide fallback
(function() {
    'use strict';
    
    // Check if jQuery is available
    function waitForJQuery(callback) {
        if (typeof $ !== 'undefined' && typeof jQuery !== 'undefined') {
            callback();
        } else {
            setTimeout(function() {
                waitForJQuery(callback);
            }, 100);
        }
    }

    // Time Picker Functionality
    let currentTimeTarget = null;

    window.openTimePicker = function(target) {
        currentTimeTarget = target;
        const currentValue = document.getElementById(target).value;
        
        // Add active state to clicked element
        document.querySelectorAll('.time-display-clean').forEach(el => el.classList.remove('active'));
        document.getElementById(target + '_display').classList.add('active');
        
        if (currentValue && currentValue !== 'HH:MM' && currentValue !== '00:00') {
            const [hour, minute] = currentValue.split(':');
            document.getElementById('hourInput').value = parseInt(hour);
            document.getElementById('minuteInput').value = parseInt(minute);
        } else {
            const now = new Date();
            document.getElementById('hourInput').value = target === 'clock_in' ? 9 : now.getHours();
            document.getElementById('minuteInput').value = 0;
        }
        
        document.getElementById('timePickerModal').style.display = 'flex';
        
        // Focus on hour input
        setTimeout(() => {
            document.getElementById('hourInput').focus();
            document.getElementById('hourInput').select();
        }, 100);
    }

    window.closeTimePicker = function() {
        document.getElementById('timePickerModal').style.display = 'none';
        // Remove active state from all time displays
        document.querySelectorAll('.time-display-clean').forEach(el => el.classList.remove('active'));
        currentTimeTarget = null;
    }

    window.confirmTime = function() {
        if (!currentTimeTarget) return;
        
        const hour = String(document.getElementById('hourInput').value).padStart(2, '0');
        const minute = String(document.getElementById('minuteInput').value).padStart(2, '0');
        const timeValue = `${hour}:${minute}`;
        
        document.getElementById(currentTimeTarget).value = timeValue;
        document.getElementById(currentTimeTarget + '_text').textContent = timeValue;
        
        window.closeTimePicker();
        clearError(currentTimeTarget);
        
        // Visual feedback
        const display = document.getElementById(currentTimeTarget + '_display');
        display.style.backgroundColor = '#f0fdf4';
        display.style.borderColor = '#10b981';
        setTimeout(() => {
            display.style.backgroundColor = '#fff';
            display.style.borderColor = '#e5e7eb';
        }, 1000);
    }

    // Form validation functions
    function timeToMinutes(timeStr) {
        const [hours, minutes] = timeStr.split(':').map(Number);
        return hours * 60 + minutes;
    }

    function showError(field, message) {
        const errorElement = document.getElementById(field + '-error');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.add('show');
        }
        
        // Add error styling to field
        const fieldElement = document.getElementById(field);
        const displayElement = document.getElementById(field + '_display');
        
        if (fieldElement) {
            fieldElement.classList.add('error');
        }
        if (displayElement) {
            displayElement.classList.add('error');
        }
    }

    function clearError(field) {
        const errorElement = document.getElementById(field + '-error');
        if (errorElement) {
            errorElement.classList.remove('show');
        }
        
        // Remove error styling from field
        const fieldElement = document.getElementById(field);
        const displayElement = document.getElementById(field + '_display');
        
        if (fieldElement) {
            fieldElement.classList.remove('error');
        }
        if (displayElement) {
            displayElement.classList.remove('error');
        }
    }

    function clearAllErrors() {
        document.querySelectorAll('.error-message').forEach(error => {
            error.classList.remove('show');
        });
        
        document.querySelectorAll('.form-control-clean, .time-display-clean').forEach(field => {
            field.classList.remove('error');
        });
    }

    // Form validation
    function validateForm() {
        let isValid = true;
        
        // Clear previous errors
        clearAllErrors();
        
        // Validate employee
        const employee = document.getElementById('employee_id').value;
        if (!employee) {
            showError('employee', 'Please select an employee');
            isValid = false;
        }
        
        // Validate date
        const date = document.getElementById('date').value;
        if (!date) {
            showError('date', 'Please select a date');
            isValid = false;
        }
        
        // Validate clock in
        const clockIn = document.getElementById('clock_in').value;
        if (!clockIn || clockIn === '00:00') {
            showError('clock_in', 'Please enter a valid clock in time');
            isValid = false;
        }
        
        // Validate clock out (if provided)
        const clockOut = document.getElementById('clock_out').value;
        if (clockOut && clockOut !== '' && clockIn) {
            // Convert times to minutes for comparison
            const clockInMinutes = timeToMinutes(clockIn);
            const clockOutMinutes = timeToMinutes(clockOut);
            
            if (clockOutMinutes <= clockInMinutes) {
                showError('clock_out', 'Clock out time must be after clock in time');
                isValid = false;
            }
        }
        
        return isValid;
    }

    // Initialize everything when DOM is ready
    function initializeForm() {
        // Time display click handlers
        document.querySelectorAll('.time-display-clean').forEach(display => {
            display.addEventListener('click', function() {
                const target = this.getAttribute('data-target');
                window.openTimePicker(target);
            });
            
            // Keyboard support
            display.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    const target = this.getAttribute('data-target');
                    window.openTimePicker(target);
                }
            });
        });

        // Input validation and formatting
        const hourInput = document.getElementById('hourInput');
        const minuteInput = document.getElementById('minuteInput');
        
        if (hourInput) {
            hourInput.addEventListener('input', function() {
                let value = parseInt(this.value) || 0;
                if (value < 0) value = 0;
                if (value > 23) value = 23;
                this.value = value;
            });

            hourInput.addEventListener('keydown', function(e) {
                if (e.key === 'Tab' && !e.shiftKey) {
                    e.preventDefault();
                    document.getElementById('minuteInput').focus();
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    window.confirmTime();
                } else if (e.key === 'Escape') {
                    window.closeTimePicker();
                }
            });
        }

        if (minuteInput) {
            minuteInput.addEventListener('input', function() {
                let value = parseInt(this.value) || 0;
                if (value < 0) value = 0;
                if (value > 59) value = 59;
                this.value = value;
            });

            minuteInput.addEventListener('keydown', function(e) {
                if (e.key === 'Tab' && e.shiftKey) {
                    e.preventDefault();
                    document.getElementById('hourInput').focus();
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    window.confirmTime();
                } else if (e.key === 'Escape') {
                    window.closeTimePicker();
                }
            });
        }

        // Close time picker when clicking outside
        const timePickerModal = document.getElementById('timePickerModal');
        if (timePickerModal) {
            timePickerModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    window.closeTimePicker();
                }
            });
        }

        // Form submission handling
        const attendanceForm = document.getElementById('attendanceForm');
        if (attendanceForm) {
            attendanceForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (validateForm()) {
                    const submitBtn = document.getElementById('submitBtn');
                    if (submitBtn) {
                        submitBtn.classList.add('loading-btn');
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
                    }
                    
                    // Submit form after brief delay
                    setTimeout(() => {
                        this.submit();
                    }, 500);
                }
            });
        }

        // Real-time validation on field changes
        const employeeSelect = document.getElementById('employee_id');
        const dateInput = document.getElementById('date');
        
        if (employeeSelect) {
            employeeSelect.addEventListener('change', function() {
                if (this.value) clearError('employee');
            });
        }

        if (dateInput) {
            dateInput.addEventListener('change', function() {
                if (this.value) clearError('date');
            });
        }

        console.log('Attendance form initialized successfully');
    }

    // Global keyboard handler
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const timePicker = document.getElementById('timePickerModal');
            if (timePicker && timePicker.style.display === 'flex') {
                window.closeTimePicker();
            }
        }
    });

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeForm);
    } else {
        initializeForm();
    }

    // Wait for jQuery and initialize components that depend on it
    waitForJQuery(function() {
        console.log('jQuery is available, initializing jQuery components');
        
        // Initialize datepicker if available
        if (typeof $.fn.daterangepicker !== 'undefined') {
            $('.datepicker').each(function() {
                if (!$(this).hasClass('hasDatepicker')) {
                    $(this).daterangepicker({
                        singleDatePicker: true,
                        minDate: '0000-01-01',
                        format: 'YYYY-MM-DD',
                        locale: {
                            format: 'YYYY-MM-DD'
                        },
                        autoUpdateInput: false
                    });

                    $(this).on('apply.daterangepicker', function(ev, picker) {
                        $(this).val(picker.startDate.format('YYYY-MM-DD'));
                        $(this).trigger('change');
                        clearError('date');
                    });

                    $(this).on('cancel.daterangepicker', function(ev, picker) {
                        $(this).val('');
                    });
                }
            });
        }

        // Initialize Select2 if available
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').each(function() {
                if (!$(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2({
                        disableOnMobile: false,
                        nativeOnMobile: false,
                        dropdownParent: $(this).closest('.modal').length ? $(this).closest('.modal') : $('body'),
                        placeholder: 'Select an option',
                        allowClear: false
                    });

                    $(this).on('select2:select', function() {
                        const fieldId = $(this).attr('id');
                        clearError(fieldId);
                    });
                }
            });
        }
    });

})();
</script>