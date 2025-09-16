<style>
    .container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    .header {
        background: linear-gradient(135deg, #4f84ff 0%, #3b5fe6 100%);
        color: white;
        padding: 30px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .header-icon {
        width: 50px;
        height: 50px;
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .header-content h1 {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .header-content p {
        opacity: 0.9;
        font-size: 16px;
    }

    .form-container {
        padding: 40px;
    }

    .form-group {
        margin-bottom: 25px;
        position: relative;
    }

    .form-row {
        display: grid!important;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 25px;
    }

    .form-row.single {
        grid-template-columns: 1fr;
    }

    label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .input-wrapper {
        position: relative;
    }

    input[type="text"], 
    input[type="date"],
    input[type="time"],
    input[type="email"], 
    select, 
    textarea {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: #fafafa;
    }

    input:focus, 
    select:focus, 
    textarea:focus {
        outline: none;
        border-color: #4f84ff;
        background: white;
        box-shadow: 0 0 0 3px rgba(79, 132, 255, 0.1);
    }

    .input-valid {
        border-color: #10b981 !important;
        background: white;
    }

    .input-wrapper.valid::after {
        content: '✓';
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #10b981;
        font-weight: bold;
        z-index: 10;
    }

    textarea {
        resize: vertical;
        min-height: 80px;
    }

    .btn-group {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px solid #f3f4f6;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4f84ff 0%, #3b5fe6 100%);
        color: white;
        flex: 1;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(79, 132, 255, 0.3);
    }

    .btn-secondary {
        background: #f3f4f6;
        color: #6b7280;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
    }

    .text-danger {
        color: #ef4444;
        font-size: 12px;
        margin-top: 5px;
    }

    .has-error input,
    .has-error select,
    .has-error textarea {
        border-color: #ef4444;
        background: #fef2f2;
    }

    .hidden {
        display: none !important;
    }

    .status-badge {
        background: #10b981;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 10px;
    }

    .status-badge.pending {
        background: #f59e0b;
    }

    .status-badge.reject {
        background: #ef4444;
    }

    select option:disabled {
        color: #9ca3af;
        background: #f9fafb;
    }

    .info-text {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
    }

    .balance-info {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 12px;
        color: #0369a1;
        margin-top: 5px;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
        
        .container {
            margin: 10px;
            border-radius: 15px;
        }
        
        .header, .form-container {
            padding: 20px;
        }
    }
</style>
<div class="container">
    <div class="header">
        <div class="header-icon">
            <i class="fas fa-edit"></i>
        </div>
        <div class="header-content">
            <h1>Edit Leave Application</h1>
            <p>Update your leave request details</p>
        </div>
    </div>

    <div class="form-container">
        <form action="{{ route('leave.update', $leave->id) }}" method="POST" id="leaveForm">
            @csrf
            @method('PUT')
            
            <!-- Employee Selection (for non-employees) -->
            @if(\Auth::user()->type != 'employee')
            <div id="employee-section" class="form-group">
                <label for="employee_id">
                    <i class="fas fa-user"></i> Employee
                </label>
                <div class="input-wrapper">
                    <!-- <select name="employee_id" id="employee_id" class="form-control" onchange="getEmployeeLeaveBalances(this.value)"> -->
                    <select name="employee_id" id="employee_id" class="form-control" disabled>
                        @foreach($employees as $id => $name)
                            <option value="{{ $id }}" {{ $leave->employee_id == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endif

            <!-- Leave Type Selection -->
            <div class="form-row">
                <div class="form-group" id="leave-container">
                    <label for="leave_type_id">
                        <i class="fas fa-calendar-check"></i> Leave Type
                    </label>
                    <div class="input-wrapper {{ $leave->leave_type_id ? 'valid' : '' }}">
                        <select name="leave_type_id" id="leave_type_id" class="form-control {{ $leave->leave_type_id ? 'input-valid' : '' }}" onchange="handleLeaveTypeChange()">
                            <option value="" disabled>Select Leave Type</option>
                            @foreach($leavetypes as $leavetype)
                                <option value="{{ $leavetype->id }}" 
                                        data-title="{{ $leavetype->title }}" 
                                        {{ $leave->leave_type_id == $leavetype->id ? 'selected' : '' }}  {{ $leavetype->days > 0 ? '' : 'disabled' }}>
                                    {{ $leavetype->title }} ({{ $leavetype->days }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <span id="leave_type_error" class="text-danger"></span>
                </div>

                <!-- Half Day / Full Day Selection -->
                <div class="form-group" id="halfday-container">
                    <label for="is_halfday">
                        <i class="fas fa-clock"></i> Leave Duration
                    </label>
                    <div class="input-wrapper {{ $leave->leavetype ? 'valid' : '' }}">
                        <select name="is_halfday" id="is_halfday" class="form-control {{ $leave->leavetype ? 'input-valid' : '' }}" onchange="handleDurationChange()">
                            <option value="full" {{ $leave->leavetype == 'full' ? 'selected' : '' }}>Full Day</option>
                            <option value="half" {{ $leave->leavetype == 'half' ? 'selected' : '' }}>Half Day</option>
                            <option value="short" {{ $leave->leavetype == 'short' ? 'selected' : '' }}>Short Leave</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Date Selection -->
            <div class="form-row">
                <div class="form-group">
                    <label for="start_date">
                        <i class="fas fa-calendar-day"></i> Start Date
                    </label>
                    <div class="input-wrapper {{ $leave->start_date ? 'valid' : '' }}">
                        <input type="date" name="start_date" id="start_date" class="form-control {{ $leave->start_date ? 'input-valid' : '' }}" value="{{ $leave->start_date }}" onchange="updateEndDate()">
                    </div>
                </div>
                <div class="form-group {{ $leave->leavetype == 'full' ? '' : 'hidden' }}" id="end_date_container">
                    <label for="end_date">
                        <i class="fas fa-calendar-day"></i> End Date
                    </label>
                    <div class="input-wrapper {{ $leave->end_date ? 'valid' : '' }}">
                        <input type="date" name="end_date" id="end_date" class="form-control {{ $leave->end_date ? 'input-valid' : '' }}" value="{{ $leave->end_date }}">
                    </div>
                </div>
                <!-- Day Segment Selection - Positioned to align with Start Date -->
                <div class="form-group {{ $leave->leavetype == 'half' || $leave->leavetype == 'short' ? '' : 'hidden' }}" id="day_segment_container">
                    <label for="day_segment">
                        <i class="fas fa-sun"></i> Day Segment
                    </label>
                    <div class="input-wrapper {{ $leave->day_segment ? 'valid' : '' }}">
                        <select name="day_segment" id="day_segment" class="form-control {{ $leave->day_segment ? 'input-valid' : '' }}" onchange="handleDaySegmentChange()">
                            <option value="morning" {{ $leave->day_segment == 'morning' ? 'selected' : '' }}>Morning</option>
                            <option value="afternoon" {{ $leave->day_segment == 'afternoon' ? 'selected' : '' }}>Afternoon</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Time Selection -->
            <div class="form-row {{ $leave->leavetype == 'short' ? '' : 'hidden' }}" id="timing-container">
                <div class="form-group">
                    <label for="start_time">
                        <i class="fas fa-clock"></i> Start Time
                    </label>
                    <div class="input-wrapper {{ $leave->start_time ? 'valid' : '' }}">
                        <input type="time" name="start_time" id="start_time" class="form-control {{ $leave->start_time ? 'input-valid' : '' }}" value="{{ $leave->start_time }}" onchange="calculateEndTime()">
                    </div>
                </div>
                <div class="form-group">
                    <label for="end_time">
                        <i class="fas fa-clock"></i> End Time
                    </label>
                    <div class="input-wrapper {{ $leave->end_time ? 'valid' : '' }}">
                        <input type="time" name="end_time" id="end_time" class="form-control {{ $leave->end_time ? 'input-valid' : '' }}" value="{{ $leave->end_time }}" readonly>
                    </div>
                    <div class="info-text">Automatically calculated (Start time + 2 hours)</div>
                </div>
            </div>

            <!-- Reason and Remarks -->
            <div class="form-row">
                <div class="form-group">
                    <label for="leave_reason">
                        <i class="fas fa-comment"></i> Leave Reason
                    </label>
                    <div class="input-wrapper {{ $leave->leave_reason ? 'valid' : '' }}">
                        <textarea name="leave_reason" id="leave_reason" class="form-control {{ $leave->leave_reason ? 'input-valid' : '' }}" placeholder="Please provide a detailed reason for your leave...">{{ $leave->leave_reason }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="remark">
                        <i class="fas fa-sticky-note"></i> Additional Remarks
                        <span style="color: #6b7280; font-weight: normal;">(optional)</span>
                    </label>
                    <div class="input-wrapper {{ $leave->remark ? 'valid' : '' }}">
                        <textarea name="remark" id="remark" class="form-control {{ $leave->remark ? 'input-valid' : '' }}" placeholder="Any additional information...">{{ $leave->remark }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Status Section (for Company/HR role) -->
            @if(\Auth::user()->type == 'company' || \Auth::user()->type == 'hr')
            <div class="form-row single" id="status-section">
                <div class="form-group">
                    <label for="status">
                        <i class="fas fa-check-circle"></i> Status
                    </label>
                    <div class="input-wrapper {{ $leave->status ? 'valid' : '' }}">
                        <select name="status" id="status" class="form-control {{ $leave->status ? 'input-valid' : '' }}">
                            <option value="">Select Status</option>
                            <option value="Pending" {{ $leave->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Approve" {{ $leave->status == 'Approve' ? 'selected' : '' }}>Approve</option>
                            <option value="Reject" {{ $leave->status == 'Reject' ? 'selected' : '' }}>Reject</option>
                        </select>
                    </div>
                </div>
            </div>
            @endif

            <!-- Submit Buttons -->
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Update Leave Request
                </button>
                <a href="{{ route('leave.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Global variables
    let submitFlag = true;

    // Initialize form
    document.addEventListener('DOMContentLoaded', function() {
        initializeForm();
        initializeValidation();
    });
    
    // Fix modal conflicts - comprehensive modal state reset
    $(document).on('hidden.bs.modal', '.modal', function() {
        // Reset any global variables or states
        submitFlag = true;
        // Clear any validation errors
        $('.has-error').removeClass('has-error');
        $('.text-danger').text('');
        // Remove any event listeners that might conflict
        $(this).removeData('bs.modal');
        // Clear form data
        $(this).find('form')[0]?.reset();
        console.log('Modal closed - state reset');
    });
    
    // Additional fix for modal backdrop issues
    $(document).on('show.bs.modal', '.modal', function() {
        // Ensure no other modals are open
        $('.modal').not(this).modal('hide');
        // Remove any lingering backdrops
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
    });

    // Issue #4 Fix: Initialize form based on existing data with proper prepopulation
    function initializeForm() {
        const currentLeaveType = document.getElementById('leave_type_id').selectedOptions[0]?.getAttribute('data-title');
        const currentDuration = document.getElementById('is_halfday').value;
        
        console.log('Current Leave Type:', currentLeaveType);
        console.log('Current Duration:', currentDuration);
        
        // Check if leave type supports half-day options
        if (currentLeaveType === 'Paid Leave' || currentLeaveType === 'Sick Leave') {
            document.getElementById('halfday-container').classList.remove('hidden');
            document.getElementById('halfday-container').style.display = 'block';
            setupFormByDurationType(currentDuration);
        } else {
            document.getElementById('halfday-container').classList.add('hidden');
            document.getElementById('halfday-container').style.display = 'none';
            resetToFullDay();
        }
        
        // Issue #4 Fix: Add validation success indicators for prepopulated fields
        const fieldsToValidate = ['leave_type_id', 'start_date', 'end_date', 'leave_reason', 'start_time', 'day_segment'];
        fieldsToValidate.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field && field.value && field.value.trim() !== '') {
                addValidationSuccess(field);
            }
        });
        
        // Fix: Load real-time balance for current employee
        setTimeout(function() {
            refreshLeaveBalance();
        }, 500); // Small delay to ensure DOM is ready
        
        // Also refresh when page becomes visible (in case user switched tabs)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                setTimeout(refreshLeaveBalance, 200);
            }
        });
    }
    
    // Fix: Function to refresh leave balance display
    function refreshLeaveBalance() {
        const employeeId = {{ $leave->employee_id }};
        console.log('Refreshing balance for employee:', employeeId);
        
        if (employeeId) {
            $.ajax({
                url: '{{ url("leave/get-employee-leave-balances") }}',
                method: 'GET',
                data: { 
                    employee_id: employeeId,
                    _t: Date.now(), // Cache busting
                    force_refresh: true
                },
                cache: false,
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                },
                success: function(response) {
                    console.log('Balance refresh response:', response);
                    if (response.success && response.leave_balances) {
                        // Update leave type options with current balances
                        const leaveTypeSelect = document.getElementById('leave_type_id');
                        const currentValue = leaveTypeSelect.value;
                        
                        console.log('Current leave type value:', currentValue);
                        
                        // Clear and repopulate options
                        leaveTypeSelect.innerHTML = '<option value="" disabled>Select Leave Type</option>';
                        
                        response.leave_balances.forEach(function(leaveType) {
                            const option = document.createElement('option');
                            option.value = leaveType.id;
                            option.setAttribute('data-title', leaveType.title);
                            option.textContent = leaveType.title + ' (' + leaveType.available_balance + ')';
                            option.selected = (leaveType.id == currentValue);
                            leaveTypeSelect.appendChild(option);
                            
                            if (leaveType.id == currentValue) {
                                console.log('Updated balance for', leaveType.title, ':', leaveType.available_balance);
                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to refresh leave balance:', error);
                    console.error('Response:', xhr.responseText);
                }
            });
        }
    }

    // Handle leave type change
    function handleLeaveTypeChange() {
        const selectElement = document.getElementById('leave_type_id');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const leaveTitle = selectedOption.getAttribute('data-title');
        const halfdayContainer = document.getElementById('halfday-container');
        
        // Reset all containers to default state
        resetAllContainers();
        
        // Show half day container only for Paid Leave or Sick Leave
        if (leaveTitle === 'Paid Leave' || leaveTitle === 'Sick Leave') {
            halfdayContainer.classList.remove('hidden');
        } else {
            halfdayContainer.classList.add('hidden');
            // Reset to full day for other leave types
            document.getElementById('is_halfday').value = 'full';
            handleDurationChange();
        }
        
        // Add validation success indicator
        addValidationSuccess(selectElement);
    }

    // Handle duration change (Full Day / Half Day / Short Leave)
    function handleDurationChange() {
        const selectedValue = document.getElementById('is_halfday').value;
        console.log('Duration changed to:', selectedValue);
        setupFormByDurationType(selectedValue);
    }

    // Setup form based on duration type - FIXED VERSION
    function setupFormByDurationType(durationType) {
        const daySegmentContainer = document.getElementById('day_segment_container');
        const timingContainer = document.getElementById('timing-container');
        const endDateContainer = document.getElementById('end_date_container');
        
        console.log('Setting up form for duration:', durationType);
        console.log('Timing container found:', !!timingContainer);
        
        // First, hide all conditional containers
        hideElement(daySegmentContainer);
        hideElement(timingContainer);
        showElement(endDateContainer);
        
        switch(durationType) {
            case 'full':
                // Full day: Show end date only
                showElement(endDateContainer);
                break;
                
            case 'half':
                // Half day: Hide end date, show day segment
                hideElement(endDateContainer);
                showElement(daySegmentContainer);
                updateEndDateToStartDate();
                break;
                
            case 'short':
                // Short leave: Hide end date, show day segment AND timing
                console.log('Setting up short leave - showing timing container');
                hideElement(endDateContainer);
                showElement(daySegmentContainer);
                showElement(timingContainer);
                updateEndDateToStartDate();
                setDefaultStartTime();
                break;
        }
    }

    // Helper function to show element reliably
    function showElement(element) {
        if (element) {
            // Remove all possible hidden classes and set display
            element.classList.remove('hidden', 'd-none');
            element.style.display = 'block';
            element.style.visibility = 'visible';
            element.style.opacity = '1';
            
            // Also ensure child inputs are enabled
            const inputs = element.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.disabled = false;
                input.style.display = 'block';
            });
            
            console.log('Element shown:', element.id);
        }
    }

    // Helper function to hide element reliably
    function hideElement(element) {
        if (element) {
            element.classList.add('hidden');
            element.style.display = 'none';
            console.log('Element hidden:', element.id);
        }
    }

    // Handle day segment change
    function handleDaySegmentChange() {
        const selectedDuration = document.getElementById('is_halfday').value;
        
        // For short leave, update start time based on day segment
        if (selectedDuration === 'short') {
            setDefaultStartTime();
        }
    }

    // Set default start time based on day segment
    function setDefaultStartTime() {
        const daySegment = document.getElementById('day_segment').value;
        const startTimeInput = document.getElementById('start_time');
        
        // Only set default if no existing value
        if (!startTimeInput.value) {
            // Set default times in 24-hour format (HTML5 time input format)
            if (daySegment === 'morning') {
                startTimeInput.value = '09:00';
            } else {
                startTimeInput.value = '14:00';
            }
        }
        
        // Calculate end time and add validation
        calculateEndTime();
        addValidationSuccess(startTimeInput);
    }

    // Issue #4 Fix: Calculate end time (start time + 2 hours) with proper format handling
    function calculateEndTime() {
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        
        if (startTimeInput.value) {
            try {
                // Parse the time input (HTML5 time input provides 24-hour format)
                const [hours, minutes] = startTimeInput.value.split(':').map(Number);
                
                // Create date object for calculation
                const startTime = new Date();
                startTime.setHours(hours, minutes, 0, 0);
                
                // Add 2 hours
                const endTime = new Date(startTime.getTime() + (2 * 60 * 60 * 1000));
                
                // Format back to 24-hour format for the input field
                const endHours = endTime.getHours().toString().padStart(2, '0');
                const endMinutes = endTime.getMinutes().toString().padStart(2, '0');
                
                endTimeInput.value = endHours + ':' + endMinutes;
                
                // Add validation success indicator
                addValidationSuccess(startTimeInput);
                addValidationSuccess(endTimeInput);
                
            } catch (error) {
                console.error('Error calculating end time:', error);
                endTimeInput.value = '';
            }
        } else {
            endTimeInput.value = '';
        }
    }

    // Update end date to match start date
    function updateEndDate() {
        const startDate = document.getElementById('start_date').value;
        const isDurationNotFull = ['half', 'short'].includes(document.getElementById('is_halfday').value);
        
        if (isDurationNotFull) {
            document.getElementById('end_date').value = startDate;
        }
    }

    // Update end date to start date for half/short leaves
    function updateEndDateToStartDate() {
        const startDate = document.getElementById('start_date').value;
        if (startDate) {
            document.getElementById('end_date').value = startDate;
        }
    }

    // Reset to full day layout
    function resetToFullDay() {
        showElement(document.getElementById('end_date_container'));
        hideElement(document.getElementById('day_segment_container'));
        hideElement(document.getElementById('timing-container'));
    }

    // Reset all containers to default state
    function resetAllContainers() {
        hideElement(document.getElementById('day_segment_container'));
        hideElement(document.getElementById('timing-container'));
        showElement(document.getElementById('end_date_container'));
    }

    // Add validation success indicator
    function addValidationSuccess(element) {
        if (element.value) {
            element.classList.add('input-valid');
            const wrapper = element.closest('.input-wrapper');
            if (wrapper) {
                wrapper.classList.add('valid');
            }
        }
    }

    // Remove validation success indicator
    function removeValidationSuccess(element) {
        element.classList.remove('input-valid');
        const wrapper = element.closest('.input-wrapper');
        if (wrapper) {
            wrapper.classList.remove('valid');
        }
    }

    // Initialize validation
    function initializeValidation() {
        // Issue #4 Fix: Add validation on input change with date validation
        const inputs = document.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.value.trim()) {
                    addValidationSuccess(this);
                } else {
                    removeValidationSuccess(this);
                }
                
                // Check for existing leave when dates change
                if (this.id === 'start_date' || this.id === 'end_date') {
                    const employeeId = document.getElementById('employee_id')?.value || {{ $leave->employee_id }};
                    const startDate = document.getElementById('start_date').value;
                    const endDate = document.getElementById('end_date').value;
                    if (startDate && endDate) {
                        checkExistingLeave(employeeId, startDate, endDate);
                    }
                }
            });
            
            input.addEventListener('input', function() {
                if (this.value.trim()) {
                    addValidationSuccess(this);
                } else {
                    removeValidationSuccess(this);
                }
            });
        });

        // Issue #4 Fix: Form submission validation with actual form submission
        document.getElementById('leaveForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (validateForm()) {
                if (submitFlag) {
                    // Actually submit the form
                    this.submit();
                } else {
                    alert('Leave has already been applied for the same date');
                }
            }
        });
    }

    // Basic form validation
    function validateForm() {
        let isValid = true;
        const requiredFields = ['leave_type_id', 'start_date', 'leave_reason'];
        
        // Check if timing is visible and add start_time to required fields
        const timingContainer = document.getElementById('timing-container');
        if (!timingContainer.classList.contains('hidden')) {
            requiredFields.push('start_time');
        }
        
        // Check if end date is visible and add to required fields
        const endDateContainer = document.getElementById('end_date_container');
        if (!endDateContainer.classList.contains('hidden')) {
            requiredFields.push('end_date');
        }
        
        requiredFields.forEach(fieldName => {
            const field = document.querySelector(`[name="${fieldName}"]`);
            if (field && !field.value.trim()) {
                isValid = false;
                field.closest('.form-group').classList.add('has-error');
                removeValidationSuccess(field);
            } else if (field) {
                field.closest('.form-group').classList.remove('has-error');
                addValidationSuccess(field);
            }
        });
        
        // Validate leave reason length
        const leaveReason = document.getElementById('leave_reason');
        if (leaveReason.value.trim().length < 10) {
            isValid = false;
            leaveReason.closest('.form-group').classList.add('has-error');
            removeValidationSuccess(leaveReason);
        }
        
        // Validate time format for short leave
        const timingContainer = document.getElementById('timing-container');
        if (!timingContainer.classList.contains('hidden')) {
            const startTimeInput = document.getElementById('start_time');
            if (startTimeInput.value) {
                // Validate time format (HH:MM)
                const timePattern = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/;
                if (!timePattern.test(startTimeInput.value)) {
                    isValid = false;
                    startTimeInput.closest('.form-group').classList.add('has-error');
                    removeValidationSuccess(startTimeInput);
                }
            }
        }
        
        return isValid;
    }

    // Get employee leave balances via AJAX
    function getEmployeeLeaveBalances(employeeId) {
        if (!employeeId) return;
        
        $.ajax({
            url: '{{ url("leave/get-paid-leave-balance") }}/' + employeeId,
            type: 'GET',
            success: function(response) {
                if (response.leavetypes) {
                    // Update leave type dropdown with new balances
                    const leaveTypeSelect = document.getElementById('leave_type_id');
                    leaveTypeSelect.innerHTML = '<option value="" disabled>Select Leave Type</option>';
                    
                    response.leavetypes.forEach(function(leaveType) {
                        const option = document.createElement('option');
                        option.value = leaveType.id;
                        option.setAttribute('data-title', leaveType.title);
                        option.textContent = leaveType.title + ' (' + leaveType.days + ')';
                        leaveTypeSelect.appendChild(option);
                    });
                    
                    // Reset form state
                    resetFormState();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching employee leave balances:', error);
                alert('Error loading employee leave balances. Please try again.');
            }
        });
    }
    
    // Reset form state when employee changes
    function resetFormState() {
        document.getElementById('leave_type_id').value = '';
        document.getElementById('is_halfday').value = 'full';
        handleDurationChange();
        removeValidationSuccess(document.getElementById('leave_type_id'));
    }

    // Issue #4 Fix: Check for existing leave with proper AJAX implementation
    function checkExistingLeave(employeeId, startDate, endDate) {
        if (!employeeId || !startDate || !endDate) {
            submitFlag = true;
            return;
        }

        $.ajax({
            url: '{{ url("leave/check-existing-leave") }}',
            method: 'GET',
            data: {
                employee_id: employeeId,
                start_date: startDate,
                end_date: endDate,
                leave_id: {{ $leave->id }} // Exclude current leave from check
            },
            success: function(response) {
                if (response.exists === true) {
                    $("#start_date, #end_date").closest('.form-group').addClass('has-error');
                    submitFlag = false;
                    alert('Leave has already been applied for the same date');
                } else {
                    submitFlag = true;
                    $("#start_date, #end_date").closest('.form-group').removeClass('has-error');
                }
            },
            error: function() {
                // On API error, allow submit (don't hard fail form)
                submitFlag = true;
            }
        });
    }

    // Update leave balance (placeholder function)
    function updateLeaveBalance(employeeId, leaveTypeId) {
        // This would typically make an AJAX call to update leave balance
        console.log('Updating leave balance for employee:', employeeId, 'leave type:', leaveTypeId);
    }

    // Update day segment from time
    function updateDaySegmentFromTime(startTime) {
        if (!startTime) return;
        
        const hour = parseInt(startTime.split(':')[0]);
        const daySegment = document.getElementById('day_segment');
        
        if (hour < 13) {
            daySegment.value = 'morning';
        } else {
            daySegment.value = 'afternoon';
        }
    }

    // Update end time from start time
    function updateEndTimeFromStartTime(startTime) {
        if (!startTime) return;
        
        const startTimeInput = document.getElementById('start_time');
        startTimeInput.value = startTime;
        calculateEndTime();
    }
</script>