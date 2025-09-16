<style>
    /* Clean Modal Styling */
    .attendance-edit-container {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        max-width: 600px;
        margin: 0 auto;
    }

    .attendance-edit-header {
        background: #fff;
        padding: 2rem 2rem 1rem 2rem;
        text-align: left;
    }

    .attendance-edit-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 0.5rem 0;
        letter-spacing: -0.025em;
    }

    .attendance-edit-header p {
        font-size: 1rem;
        color: #6b7280;
        margin: 0;
        font-weight: 400;
    }

    .attendance-edit-body {
        padding: 1rem 2rem 2rem 2rem;
        background: #fff;
    }

    .form-grid-edit {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .form-group-edit {
        display: flex;
        flex-direction: column;
    }

    .form-label-edit {
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

    .form-label-edit i {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .form-control-edit {
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

    .form-control-edit:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .form-control-edit:hover:not(:focus) {
        border-color: #9ca3af;
    }

    .form-control-edit::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }

    /* Select Styling */
    .select-edit {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.75rem center;
        background-repeat: no-repeat;
        background-size: 1.25rem;
        padding-right: 3rem;
        cursor: pointer;
    }

    .select-edit:focus {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%233b82f6' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    }

    /* Time Display Styling */
    .time-display-edit {
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

    .time-display-edit:hover {
        border-color: #9ca3af;
    }

    .time-display-edit:focus,
    .time-display-edit.active {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .time-display-edit i {
        color: #6b7280;
        font-size: 1rem;
    }

    .time-display-edit:focus i,
    .time-display-edit.active i {
        color: #3b82f6;
    }

    /* Time Picker Modal */
    .time-picker-modal-edit {
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

    .time-picker-content-edit {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        min-width: 300px;
        animation: slideUpEdit 0.3s ease-out;
    }

    @keyframes slideUpEdit {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .time-picker-header-edit {
        text-align: center;
        margin-bottom: 1.5rem;
        font-weight: 600;
        color: #374151;
        font-size: 1.1rem;
    }

    .time-picker-controls-edit {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .time-input-edit {
        width: 70px;
        text-align: center;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.75rem;
        font-weight: 600;
        font-size: 1.2rem;
        color: #374151;
    }

    .time-input-edit:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .time-separator-edit {
        font-size: 1.5rem;
        font-weight: bold;
        color: #6b7280;
    }

    .time-picker-actions-edit {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }

    .time-picker-btn-edit {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .time-picker-btn-edit.cancel {
        background: #f3f4f6;
        color: #374151;
    }

    .time-picker-btn-edit.cancel:hover {
        background: #e5e7eb;
        transform: translateY(-1px);
    }

    .time-picker-btn-edit.confirm {
        background: #3b82f6;
        color: white;
    }

    .time-picker-btn-edit.confirm:hover {
        background: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    /* Form Actions */
    .form-actions-edit {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        padding-top: 1.5rem;
        border-top: 1px solid #f3f4f6;
    }

    .btn-edit {
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
        justify-content: center;
    }

    .btn-cancel-edit {
        background: #6b7280;
        color: white;
    }

    .btn-cancel-edit:hover {
        background: #4b5563;
        transform: translateY(-1px);
        color: white;
        text-decoration: none;
    }

    .btn-update-edit {
        background: #10b981;
        color: white;
    }

    .btn-update-edit:hover {
        background: #059669;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        color: white;
        text-decoration: none;
    }

    .loading-btn-edit {
        opacity: 0.7;
        pointer-events: none;
        cursor: not-allowed;
    }

    .loading-btn-edit .fa-spinner {
        animation: spinEdit 1s linear infinite;
    }

    @keyframes spinEdit {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Error States */
    .form-control-edit.error,
    .time-display-edit.error {
        border-color: #ef4444;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
    }

    .error-message-edit {
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 0.25rem;
        font-weight: 500;
        display: none;
    }

    .error-message-edit.show {
        display: block;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .attendance-edit-container {
            margin: 1rem;
            border-radius: 8px;
        }
        
        .attendance-edit-header {
            padding: 1.5rem 1.5rem 1rem 1.5rem;
        }
        
        .attendance-edit-header h1 {
            font-size: 1.5rem;
        }
        
        .attendance-edit-body {
            padding: 1rem 1.5rem 1.5rem 1.5rem;
        }
        
        .form-grid-edit {
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .form-actions-edit {
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .btn-edit {
            width: 100%;
        }
        
        .time-picker-content-edit {
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

<div class="attendance-edit-container">
    <!-- Clean Header -->
    <div class="attendance-edit-header">
        <h1>Edit Attendance</h1>
        <p>Update attendance information with the required details</p>
    </div>

    <!-- Form -->
    <form action="{{ route('attendanceemployee.update', $attendanceEmployee->id) }}" method="POST" id="editAttendanceForm">
        @csrf
        @method('PUT')
        
        <div class="attendance-edit-body">
            <!-- Form Grid -->
            <div class="form-grid-edit">
                <!-- Employee Selection -->
                <div class="form-group-edit">
                    <label class="form-label-edit" for="employee_id">
                        <i class="fas fa-user"></i>
                        Employee
                    </label>
                    <select name="employee_id" id="employee_id" class="form-control-edit select-edit" required>
                        @foreach ($employees as $id => $name)
                            <option value="{{ $id }}" {{ $id == $attendanceEmployee->employee_id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <div class="error-message-edit" id="employee-error"></div>
                </div>

                <!-- Date Selection -->
                <div class="form-group-edit">
                    <label class="form-label-edit" for="date">
                        <i class="fas fa-calendar"></i>
                        Date
                    </label>
                    <input type="text" name="date" id="date" class="form-control-edit datepicker" value="{{ $attendanceEmployee->date }}" readonly required>
                    <div class="error-message-edit" id="date-error"></div>
                </div>

                <!-- Clock In Time -->
                <div class="form-group-edit">
                    <label class="form-label-edit" for="clock_in">
                        <i class="fas fa-sign-in-alt"></i>
                        Clock In Time
                    </label>
                    <div class="time-display-edit" data-target="clock_in" id="clock_in_display" tabindex="0">
                        <span id="clock_in_text">{{ date('H:i', strtotime($attendanceEmployee->clock_in)) }}</span>
                        <i class="fas fa-clock"></i>
                    </div>
                    <input type="hidden" name="clock_in" id="clock_in" value="{{ date('H:i', strtotime($attendanceEmployee->clock_in)) }}" required>
                    <div class="error-message-edit" id="clock_in-error"></div>
                </div>

                <!-- Clock Out Time -->
                <div class="form-group-edit">
                    <label class="form-label-edit" for="clock_out">
                        <i class="fas fa-sign-out-alt"></i>
                        Clock Out Time
                    </label>
                    <div class="time-display-edit" data-target="clock_out" id="clock_out_display" tabindex="0">
                        <span id="clock_out_text">{{ date('H:i', strtotime($attendanceEmployee->clock_out)) ?? '' }}</span>
                        <i class="fas fa-clock"></i>
                    </div>
                    <input type="hidden" name="clock_out" id="clock_out" value="{{ date('H:i', strtotime($attendanceEmployee->clock_out)) }}">
                    <div class="error-message-edit" id="clock_out-error"></div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions-edit">
                <button type="button" class="btn-edit btn-cancel-edit" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
                <button type="submit" class="btn-edit btn-update-edit" id="updateBtn">
                    <i class="fas fa-save"></i>
                    Update Attendance
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Time Picker Modal -->
<div class="time-picker-modal-edit" id="timePickerModalEdit">
    <div class="time-picker-content-edit">
        <div class="time-picker-header-edit">Select Time</div>
        <div class="time-picker-controls-edit">
            <input type="number" class="time-input-edit" id="hourInputEdit" min="0" max="23" value="0" placeholder="00">
            <span class="time-separator-edit">:</span>
            <input type="number" class="time-input-edit" id="minuteInputEdit" min="0" max="59" value="0" placeholder="00">
        </div>
        <div class="time-picker-actions-edit">
            <button type="button" class="time-picker-btn-edit cancel" onclick="closeTimePickerEdit()">Cancel</button>
            <button type="button" class="time-picker-btn-edit confirm" onclick="confirmTimeEdit()">OK</button>
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

    // Time Picker Functionality for Edit Form
    let currentTimeTargetEdit = null;

    window.openTimePickerEdit = function(target) {
        currentTimeTargetEdit = target;
        const currentValue = document.getElementById(target).value;
        
        // Add active state to clicked element
        document.querySelectorAll('.time-display-edit').forEach(el => el.classList.remove('active'));
        document.getElementById(target + '_display').classList.add('active');
        
        if (currentValue && currentValue !== 'HH:MM' && currentValue !== '00:00:00') {
            // Handle both HH:MM and HH:MM:SS formats
            const timeParts = currentValue.split(':');
            document.getElementById('hourInputEdit').value = parseInt(timeParts[0]);
            document.getElementById('minuteInputEdit').value = parseInt(timeParts[1]);
        } else {
            const now = new Date();
            document.getElementById('hourInputEdit').value = target === 'clock_in' ? 9 : now.getHours();
            document.getElementById('minuteInputEdit').value = 0;
        }
        
        document.getElementById('timePickerModalEdit').style.display = 'flex';
        
        // Focus on hour input
        setTimeout(() => {
            document.getElementById('hourInputEdit').focus();
            document.getElementById('hourInputEdit').select();
        }, 100);
    }

    window.closeTimePickerEdit = function() {
        document.getElementById('timePickerModalEdit').style.display = 'none';
        // Remove active state from all time displays
        document.querySelectorAll('.time-display-edit').forEach(el => el.classList.remove('active'));
        currentTimeTargetEdit = null;
    }

    window.confirmTimeEdit = function() {
        if (!currentTimeTargetEdit) return;
        
        const hour = String(document.getElementById('hourInputEdit').value).padStart(2, '0');
        const minute = String(document.getElementById('minuteInputEdit').value).padStart(2, '0');
        const timeValue = `${hour}:${minute}`;
        
        document.getElementById(currentTimeTargetEdit).value = timeValue;
        document.getElementById(currentTimeTargetEdit + '_text').textContent = timeValue;
        
        window.closeTimePickerEdit();
        clearErrorEdit(currentTimeTargetEdit);
        
        // Visual feedback
        const display = document.getElementById(currentTimeTargetEdit + '_display');
        display.style.backgroundColor = '#f0fdf4';
        display.style.borderColor = '#10b981';
        setTimeout(() => {
            display.style.backgroundColor = '#fff';
            display.style.borderColor = '#e5e7eb';
        }, 1000);
    }

    // Form validation functions
    function timeToMinutesEdit(timeStr) {
        const timeParts = timeStr.split(':');
        const hours = parseInt(timeParts[0]) || 0;
        const minutes = parseInt(timeParts[1]) || 0;
        return hours * 60 + minutes;
    }

    function showErrorEdit(field, message) {
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

    function clearErrorEdit(field) {
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

    function clearAllErrorsEdit() {
        document.querySelectorAll('.error-message-edit').forEach(error => {
            error.classList.remove('show');
        });
        
        document.querySelectorAll('.form-control-edit, .time-display-edit').forEach(field => {
            field.classList.remove('error');
        });
    }

    // Form validation
    function validateEditForm() {
        let isValid = true;
        
        // Clear previous errors
        clearAllErrorsEdit();
        
        // Validate employee
        const employee = document.getElementById('employee_id').value;
        if (!employee) {
            showErrorEdit('employee', 'Please select an employee');
            isValid = false;
        }
        
        // Validate date
        const date = document.getElementById('date').value;
        if (!date) {
            showErrorEdit('date', 'Please select a date');
            isValid = false;
        }
        
        // Validate clock in
        const clockIn = document.getElementById('clock_in').value;
        if (!clockIn || clockIn === '00:00:00') {
            showErrorEdit('clock_in', 'Please enter a valid clock in time');
            isValid = false;
        }
        
        // Validate clock out (if provided)
        const clockOut = document.getElementById('clock_out').value;
        if (clockOut && clockOut !== '' && clockOut !== '00:00' && clockOut !== '00:00:00' && clockIn) {
            // Convert times to minutes for comparison
            const clockInMinutes = timeToMinutesEdit(clockIn);
            const clockOutMinutes = timeToMinutesEdit(clockOut);
            
            if (clockOutMinutes <= clockInMinutes) {
                showErrorEdit('clock_out', 'Clock out time must be after clock in time');
                isValid = false;
            }
        }
        
        return isValid;
    }

    // Initialize everything when DOM is ready
    function initializeEditForm() {
        // Time display click handlers
        document.querySelectorAll('.time-display-edit').forEach(display => {
            display.addEventListener('click', function() {
                const target = this.getAttribute('data-target');
                window.openTimePickerEdit(target);
            });
            
            // Keyboard support
            display.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    const target = this.getAttribute('data-target');
                    window.openTimePickerEdit(target);
                }
            });
        });

        // Input validation and formatting
        const hourInputEdit = document.getElementById('hourInputEdit');
        const minuteInputEdit = document.getElementById('minuteInputEdit');
        
        if (hourInputEdit) {
            hourInputEdit.addEventListener('input', function() {
                let value = parseInt(this.value) || 0;
                if (value < 0) value = 0;
                if (value > 23) value = 23;
                this.value = value;
            });

            hourInputEdit.addEventListener('keydown', function(e) {
                if (e.key === 'Tab' && !e.shiftKey) {
                    e.preventDefault();
                    document.getElementById('minuteInputEdit').focus();
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    window.confirmTimeEdit();
                } else if (e.key === 'Escape') {
                    window.closeTimePickerEdit();
                }
            });
        }

        if (minuteInputEdit) {
            minuteInputEdit.addEventListener('input', function() {
                let value = parseInt(this.value) || 0;
                if (value < 0) value = 0;
                if (value > 59) value = 59;
                this.value = value;
            });

            minuteInputEdit.addEventListener('keydown', function(e) {
                if (e.key === 'Tab' && e.shiftKey) {
                    e.preventDefault();
                    document.getElementById('hourInputEdit').focus();
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    window.confirmTimeEdit();
                } else if (e.key === 'Escape') {
                    window.closeTimePickerEdit();
                }
            });
        }

        // Close time picker when clicking outside
        const timePickerModalEdit = document.getElementById('timePickerModalEdit');
        if (timePickerModalEdit) {
            timePickerModalEdit.addEventListener('click', function(e) {
                if (e.target === this) {
                    window.closeTimePickerEdit();
                }
            });
        }

        // Form submission handling
        const editForm = document.getElementById('editAttendanceForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (validateEditForm()) {
                    const updateBtn = document.getElementById('updateBtn');
                    if (updateBtn) {
                        updateBtn.classList.add('loading-btn-edit');
                        updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
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
                if (this.value) clearErrorEdit('employee');
            });
        }

        if (dateInput) {
            dateInput.addEventListener('change', function() {
                if (this.value) clearErrorEdit('date');
            });
        }

        console.log('Edit attendance form initialized successfully');
    }

    // Global keyboard handler
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const timePicker = document.getElementById('timePickerModalEdit');
            if (timePicker && timePicker.style.display === 'flex') {
                window.closeTimePickerEdit();
            }
        }
    });

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeEditForm);
    } else {
        initializeEditForm();
    }

    // Wait for jQuery and initialize components that depend on it
    waitForJQuery(function() {
        console.log('jQuery is available, initializing jQuery components for edit form');
        
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
                        clearErrorEdit('date');
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
                        clearErrorEdit(fieldId);
                    });
                }
            });
        }
    });

})();
</script>