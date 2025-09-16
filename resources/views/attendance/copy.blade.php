<style>
    /* Clean Modal Styling */
    .attendance-copy-container {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        max-width: 600px;
        margin: 0 auto;
    }

    .attendance-copy-header {
        background: #fff;
        padding: 2rem 2rem 1rem 2rem;
        text-align: left;
    }

    .attendance-copy-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0 0 0.5rem 0;
        letter-spacing: -0.025em;
    }

    .attendance-copy-header p {
        font-size: 1rem;
        color: #6b7280;
        margin: 0;
        font-weight: 400;
    }

    .attendance-copy-body {
        padding: 1rem 2rem 2rem 2rem;
        background: #fff;
    }

    .form-grid-copy {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .form-group-copy {
        display: flex;
        flex-direction: column;
    }

    .form-label-copy {
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

    .form-label-copy i {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .form-control-copy {
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

    .form-control-copy:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .form-control-copy:hover:not(:focus) {
        border-color: #9ca3af;
    }

    .form-control-copy::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }

    .form-control-copy:disabled {
        background: #f9fafb;
        color: #6b7280;
        cursor: not-allowed;
        border-color: #d1d5db;
    }

    /* Select Styling */
    .select-copy {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.75rem center;
        background-repeat: no-repeat;
        background-size: 1.25rem;
        padding-right: 3rem;
        cursor: pointer;
    }

    .select-copy:focus {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%233b82f6' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    }

    .select-copy:disabled {
        cursor: not-allowed;
    }

    /* Time Display Styling */
    .time-display-copy {
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

    .time-display-copy:hover {
        border-color: #9ca3af;
    }

    .time-display-copy:focus,
    .time-display-copy.active {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .time-display-copy i {
        color: #6b7280;
        font-size: 1rem;
    }

    .time-display-copy:focus i,
    .time-display-copy.active i {
        color: #3b82f6;
    }

    /* Time Picker Modal */
    .time-picker-modal-copy {
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

    .time-picker-content-copy {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        min-width: 300px;
        animation: slideUpCopy 0.3s ease-out;
    }

    @keyframes slideUpCopy {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .time-picker-header-copy {
        text-align: center;
        margin-bottom: 1.5rem;
        font-weight: 600;
        color: #374151;
        font-size: 1.1rem;
    }

    .time-picker-controls-copy {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .time-input-copy {
        width: 70px;
        text-align: center;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.75rem;
        font-weight: 600;
        font-size: 1.2rem;
        color: #374151;
    }

    .time-input-copy:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .time-separator-copy {
        font-size: 1.5rem;
        font-weight: bold;
        color: #6b7280;
    }

    .time-picker-actions-copy {
        display: flex;
        gap: 0.75rem;
        justify-content: flex-end;
    }

    .time-picker-btn-copy {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .time-picker-btn-copy.cancel {
        background: #f3f4f6;
        color: #374151;
    }

    .time-picker-btn-copy.cancel:hover {
        background: #e5e7eb;
        transform: translateY(-1px);
    }

    .time-picker-btn-copy.confirm {
        background: #3b82f6;
        color: white;
    }

    .time-picker-btn-copy.confirm:hover {
        background: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    /* Form Actions */
    .form-actions-copy {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        padding-top: 1.5rem;
        border-top: 1px solid #f3f4f6;
    }

    .btn-copy {
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

    .btn-cancel-copy {
        background: #6b7280;
        color: white;
    }

    .btn-cancel-copy:hover {
        background: #4b5563;
        transform: translateY(-1px);
        color: white;
        text-decoration: none;
    }

    .btn-create-copy {
        background: #8b5cf6;
        color: white;
    }

    .btn-create-copy:hover {
        background: #7c3aed;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        color: white;
        text-decoration: none;
    }

    .loading-btn-copy {
        opacity: 0.7;
        pointer-events: none;
        cursor: not-allowed;
    }

    .loading-btn-copy .fa-spinner {
        animation: spinCopy 1s linear infinite;
    }

    .modal-body, .modal-content{
        background: #fff;
    }

    @keyframes spinCopy {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Error States */
    .form-control-copy.error,
    .time-display-copy.error {
        border-color: #ef4444;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
    }

    .error-message-copy {
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 0.25rem;
        font-weight: 500;
        display: none;
    }

    .error-message-copy.show {
        display: block;
    }

    /* Disabled Field Styling */
    .form-group-copy.disabled {
        opacity: 0.7;
    }

    .form-group-copy.disabled .form-label-copy {
        color: #9ca3af;
    }

    .form-group-copy.disabled .form-label-copy::after {
        content: " (Original Employee)";
        font-size: 0.75rem;
        color: #6b7280;
        font-weight: 400;
        text-transform: none;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .attendance-copy-container {
            margin: 1rem;
            border-radius: 8px;
        }
        
        .attendance-copy-header {
            padding: 1.5rem 1.5rem 1rem 1.5rem;
        }
        
        .attendance-copy-header h1 {
            font-size: 1.5rem;
        }
        
        .attendance-copy-body {
            padding: 1rem 1.5rem 1.5rem 1.5rem;
        }
        
        .form-grid-copy {
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .form-actions-copy {
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .btn-copy {
            width: 100%;
        }
        
        .time-picker-content-copy {
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
</style>

<div class="attendance-copy-container">
    <!-- Clean Header -->
    <div class="attendance-copy-header">
        <h1>Copy Attendance</h1>
        <p>Create a new attendance record based on the selected attendance</p>
    </div>

    <!-- Form -->
    <form action="{{ url('attendanceemployee') }}" method="POST" id="copyAttendanceForm">
        @csrf
        
        <div class="attendance-copy-body">
            <!-- Form Grid -->
            <div class="form-grid-copy">
                <!-- Employee Selection (Disabled) -->
                <div class="form-group-copy disabled">
                    <label class="form-label-copy" for="employee_id">
                        <i class="fas fa-user"></i>
                        Employee
                    </label>
                    <select name="employee_id_display" id="employee_id_display" class="form-control-copy select-copy" disabled>
                        @foreach ($employees as $id => $name)
                            <option value="{{ $id }}" {{ $id == $attendanceEmployee->employee_id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="employee_id" value="{{ $attendanceEmployee->employee_id }}">
                    <div class="error-message-copy" id="employee-error"></div>
                </div>

                <!-- Date Selection -->
                <div class="form-group-copy">
                    <label class="form-label-copy" for="date">
                        <i class="fas fa-calendar"></i>
                        Date
                    </label>
                    <input type="text" name="date" id="date" class="form-control-copy datepicker" value="{{ $attendanceEmployee->date }}" readonly required>
                    <div class="error-message-copy" id="date-error"></div>
                </div>

                <!-- Clock In Time -->
                <div class="form-group-copy">
                    <label class="form-label-copy" for="clock_in">
                        <i class="fas fa-sign-in-alt"></i>
                        Clock In Time
                    </label>
                    <div class="time-display-copy" data-target="clock_in" id="clock_in_display" tabindex="0">
                        <span id="clock_in_text">{{ date('H:i', strtotime($attendanceEmployee->clock_in)) }}</span>
                        <i class="fas fa-clock"></i>
                    </div>
                    <input type="hidden" name="clock_in" id="clock_in" value="{{ date('H:i', strtotime($attendanceEmployee->clock_in)) }}" required>
                    <div class="error-message-copy" id="clock_in-error"></div>
                </div>

                <!-- Clock Out Time -->
                <div class="form-group-copy">
                    <label class="form-label-copy" for="clock_out">
                        <i class="fas fa-sign-out-alt"></i>
                        Clock Out Time
                    </label>
                    <div class="time-display-copy" data-target="clock_out" id="clock_out_display" tabindex="0">
                        <span id="clock_out_text">{{ date('H:i', strtotime($attendanceEmployee->clock_out)) ?: 'HH:MM' }}</span>
                        <i class="fas fa-clock"></i>
                    </div>
                    <input type="hidden" name="clock_out" id="clock_out" value="{{ date('H:i', strtotime($attendanceEmployee->clock_out)) }}">
                    <div class="error-message-copy" id="clock_out-error"></div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions-copy">
                <button type="button" class="btn-copy btn-cancel-copy" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
                <button type="submit" class="btn-copy btn-create-copy" id="createBtn">
                    <i class="fas fa-copy"></i>
                    Create Copy
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Time Picker Modal -->
<div class="time-picker-modal-copy" id="timePickerModalCopy">
    <div class="time-picker-content-copy">
        <div class="time-picker-header-copy">Select Time</div>
        <div class="time-picker-controls-copy">
            <input type="number" class="time-input-copy" id="hourInputCopy" min="0" max="23" value="0" placeholder="00">
            <span class="time-separator-copy">:</span>
            <input type="number" class="time-input-copy" id="minuteInputCopy" min="0" max="59" value="0" placeholder="00">
        </div>
        <div class="time-picker-actions-copy">
            <button type="button" class="time-picker-btn-copy cancel" onclick="closeTimePickerCopy()">Cancel</button>
            <button type="button" class="time-picker-btn-copy confirm" onclick="confirmTimeCopy()">OK</button>
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

        // Time Picker Functionality for Copy Form
        let currentTimeTargetCopy = null;

        window.openTimePickerCopy = function(target) {
            currentTimeTargetCopy = target;
            const currentValue = document.getElementById(target).value;
            
            // Add active state to clicked element
            document.querySelectorAll('.time-display-copy').forEach(el => el.classList.remove('active'));
            document.getElementById(target + '_display').classList.add('active');
            
            if (currentValue && currentValue !== 'HH:MM' && currentValue !== '00:00') {
                // Handle both HH:MM and HH:MM:SS formats
                const timeParts = currentValue.split(':');
                document.getElementById('hourInputCopy').value = parseInt(timeParts[0]);
                document.getElementById('minuteInputCopy').value = parseInt(timeParts[1]);
            } else {
                const now = new Date();
                document.getElementById('hourInputCopy').value = target === 'clock_in' ? 9 : now.getHours();
                document.getElementById('minuteInputCopy').value = 0;
            }
            
            document.getElementById('timePickerModalCopy').style.display = 'flex';
            
            // Focus on hour input
            setTimeout(() => {
                document.getElementById('hourInputCopy').focus();
                document.getElementById('hourInputCopy').select();
            }, 100);
        }

        window.closeTimePickerCopy = function() {
            document.getElementById('timePickerModalCopy').style.display = 'none';
            // Remove active state from all time displays
            document.querySelectorAll('.time-display-copy').forEach(el => el.classList.remove('active'));
            currentTimeTargetCopy = null;
        }

        window.confirmTimeCopy = function() {
            if (!currentTimeTargetCopy) return;
            
            const hour = String(document.getElementById('hourInputCopy').value).padStart(2, '0');
            const minute = String(document.getElementById('minuteInputCopy').value).padStart(2, '0');
            const timeValue = `${hour}:${minute}`;
            
            document.getElementById(currentTimeTargetCopy).value = timeValue;
            document.getElementById(currentTimeTargetCopy + '_text').textContent = timeValue;
            
            window.closeTimePickerCopy();
            clearErrorCopy(currentTimeTargetCopy);
            
            // Visual feedback
            const display = document.getElementById(currentTimeTargetCopy + '_display');
            display.style.backgroundColor = '#f0fdf4';
            display.style.borderColor = '#10b981';
            setTimeout(() => {
                display.style.backgroundColor = '#fff';
                display.style.borderColor = '#e5e7eb';
            }, 1000);
        }

        // Form validation functions
        function timeToMinutesCopy(timeStr) {
            const timeParts = timeStr.split(':');
            const hours = parseInt(timeParts[0]) || 0;
            const minutes = parseInt(timeParts[1]) || 0;
            return hours * 60 + minutes;
        }

        function showErrorCopy(field, message) {
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

        function clearErrorCopy(field) {
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

        function clearAllErrorsCopy() {
            document.querySelectorAll('.error-message-copy').forEach(error => {
                error.classList.remove('show');
            });
            
            document.querySelectorAll('.form-control-copy, .time-display-copy').forEach(field => {
                field.classList.remove('error');
            });
        }

        // Form validation
        function validateCopyForm() {
            let isValid = true;
            
            // Clear previous errors
            clearAllErrorsCopy();
            
            // Validate date
            const date = document.getElementById('date').value;
            if (!date) {
                showErrorCopy('date', 'Please select a date');
                isValid = false;
            }
            
            // Validate clock in
            const clockIn = document.getElementById('clock_in').value;
            if (!clockIn || clockIn === '00:00') {
                showErrorCopy('clock_in', 'Please enter a valid clock in time');
                isValid = false;
            }
            
            // Validate clock out (if provided and not 00:00)
            const clockOut = document.getElementById('clock_out').value;
            if (clockOut && clockOut !== '' && clockOut !== '00:00' && clockOut !== '00:00' && clockIn) {
                // Convert times to minutes for comparison
                const clockInMinutes = timeToMinutesCopy(clockIn);
                const clockOutMinutes = timeToMinutesCopy(clockOut);
                
                if (clockOutMinutes <= clockInMinutes) {
                    showErrorCopy('clock_out', 'Clock out time must be after clock in time');
                    isValid = false;
                }
            }
            
            return isValid;
        }

        // Initialize everything when DOM is ready
        function initializeCopyForm() {
            // Time display click handlers
            document.querySelectorAll('.time-display-copy').forEach(display => {
                display.addEventListener('click', function() {
                    const target = this.getAttribute('data-target');
                    window.openTimePickerCopy(target);
                });
                
                // Keyboard support
                display.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        const target = this.getAttribute('data-target');
                        window.openTimePickerCopy(target);
                    }
                });
            });

            // Input validation and formatting
            const hourInputCopy = document.getElementById('hourInputCopy');
            const minuteInputCopy = document.getElementById('minuteInputCopy');
            
            if (hourInputCopy) {
                hourInputCopy.addEventListener('input', function() {
                    let value = parseInt(this.value) || 0;
                    if (value < 0) value = 0;
                    if (value > 23) value = 23;
                    this.value = value;
                });

                hourInputCopy.addEventListener('keydown', function(e) {
                    if (e.key === 'Tab' && !e.shiftKey) {
                        e.preventDefault();
                        document.getElementById('minuteInputCopy').focus();
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        window.confirmTimeCopy();
                    } else if (e.key === 'Escape') {
                        window.closeTimePickerCopy();
                    }
                });
            }

            if (minuteInputCopy) {
                minuteInputCopy.addEventListener('input', function() {
                    let value = parseInt(this.value) || 0;
                    if (value < 0) value = 0;
                    if (value > 59) value = 59;
                    this.value = value;
                });

                minuteInputCopy.addEventListener('keydown', function(e) {
                    if (e.key === 'Tab' && e.shiftKey) {
                        e.preventDefault();
                        document.getElementById('hourInputCopy').focus();
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        window.confirmTimeCopy();
                    } else if (e.key === 'Escape') {
                        window.closeTimePickerCopy();
                    }
                });
            }

            // Close time picker when clicking outside
            const timePickerModalCopy = document.getElementById('timePickerModalCopy');
            if (timePickerModalCopy) {
                timePickerModalCopy.addEventListener('click', function(e) {
                    if (e.target === this) {
                        window.closeTimePickerCopy();
                    }
                });
            }

            // Form submission handling
            const copyForm = document.getElementById('copyAttendanceForm');
            if (copyForm) {
                copyForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    if (validateCopyForm()) {
                        const createBtn = document.getElementById('createBtn');
                        if (createBtn) {
                            createBtn.classList.add('loading-btn-copy');
                            createBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
                        }
                        
                        // Submit form after brief delay
                        setTimeout(() => {
                            this.submit();
                        }, 500);
                    }
                });
            }

            // Real-time validation on field changes
            const dateInput = document.getElementById('date');
            
            if (dateInput) {
                dateInput.addEventListener('change', function() {
                    if (this.value) clearErrorCopy('date');
                });
            }

            console.log('Copy attendance form initialized successfully');
        }

        // Global keyboard handler
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const timePicker = document.getElementById('timePickerModalCopy');
                if (timePicker && timePicker.style.display === 'flex') {
                    window.closeTimePickerCopy();
                }
            }
        });

        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeCopyForm);
        } else {
            initializeCopyForm();
        }

        // Wait for jQuery and initialize components that depend on it
        waitForJQuery(function() {
            console.log('jQuery is available, initializing jQuery components for copy form');
            
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
                            clearErrorCopy('date');
                        });

                        $(this).on('cancel.daterangepicker', function(ev, picker) {
                            $(this).val('');
                        });
                    }
                });
            }

            // Note: Employee select is disabled, so no Select2 initialization needed
        });

    })();
</script>