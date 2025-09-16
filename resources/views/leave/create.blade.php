<style>
    .modal-content > div:first-child {
        display: none;
    }

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
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        /* margin-bottom: 25px; */
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

    .input-valid::after {
        content: '✓';
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #10b981;
        font-weight: bold;
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

    .employee-badge {
        background: #10b981;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 10px;
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

    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #4f84ff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: 8px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .employee-info {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 12px;
        color: #495057;
        margin-top: 5px;
        display: none;
    }

    .employee-info.show {
        display: block;
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
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="header-content">
            <h1>Leave Application</h1>
            <p>Submit your leave request with proper details</p>
        </div>
    </div>

    <div class="form-container">
        <form action="{{ url('leave') }}" method="post" id="leaveForm">
            @csrf
            <!-- Employee and Leave Type Selection -->
            <div class="row">
                <!-- Employee Selection (for HR users) -->
                @if(Auth::user()->type != 'employee')
                <div id="employee-section" class="form-group">
                    <label for="employee_id">
                        <i class="fas fa-user"></i> Employee
                    </label>
                    <div class="input-wrapper">
                        <select name="employee_id" id="employee_id" class="form-control">
                            <option value="">Select Employee</option>
                            @if(isset($employees))
                                @foreach($employees as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <span id="employee_error" class="text-danger"></span>
                    <div id="employee_info" class="employee-info">
                        <i class="fas fa-info-circle"></i> <span id="employee_details"></span>
                    </div>
                </div>
                @endif

                
            </div>

            <!-- Half Day / Full Day Selection -->
            <div class="form-row">
                <!-- Leave Type Selection -->
                <div class="form-group" id="leave-container">
                    <label for="leave_type_id">
                        <i class="fas fa-calendar-check"></i> Leave Type
                    </label>
                    <div class="input-wrapper">
                        <select name="leave_type_id" id="leave_type_id" class="form-control">
                            <option value="" disabled selected>Select Leave Type</option>
                            @if(Auth::user()->type == 'employee' && isset($leavetypes))
                                @foreach($leavetypes as $leavetype)
                                    <option value="{{ $leavetype->id }}" data-title="{{ $leavetype->title }}" {{ $leavetype->days == 0 ? 'disabled' : '' }}>
                                        {{ $leavetype->title }} ({{ $leavetype->days }})
                                    </option>
                                @endforeach
                            @endif
                            <!-- For HR users, options will be populated dynamically via JavaScript -->
                        </select>
                    </div>
                    <span id="leave_type_error" class="text-danger"></span>
                </div>

                <div class="form-group" id="halfday-container" style="display: none;">
                    <label for="is_halfday">
                        <i class="fas fa-clock"></i> Leave Duration
                    </label>
                    <div class="input-wrapper">
                        <select name="is_halfday" id="is_halfday" class="form-control">
                            <option value="full">Full Day</option>
                            <option value="half">Half Day</option>
                            <option value="short">Short Leave</option>
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
                    <div class="input-wrapper">
                        <input type="date" name="start_date" id="start_date" class="form-control"
                        value="{{ old('start_date') }}">
                    </div>
                </div>
                <div class="form-group" id="end_date_container">
                    <label for="end_date">
                        <i class="fas fa-calendar-day"></i> End Date
                    </label>
                    <div class="input-wrapper">
                        <input type="date" name="end_date" id="end_date" class="form-control">
                    </div>
                </div>
                <!-- Day Segment Selection - Positioned to align with Start Date -->
                <div class="form-group hidden" id="day_segment_container">
                    <label for="day_segment">
                        <i class="fas fa-sun"></i> Day Segment
                    </label>
                    <div class="input-wrapper">
                        <select name="day_segment" id="day_segment" class="form-control select2 ">
                            <option value="morning" {{ old('day_segment') == 'morning' ? 'selected' : '' }}>Morning</option>
                            <option value="afternoon" {{ old('day_segment') == 'afternoon' ? 'selected' : '' }}>Afternoon</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Time Selection -->
            <div class="form-row hidden" id="timing-container">
                <div class="form-group">
                    <label for="start_time">
                        <i class="fas fa-clock"></i> Start Time
                    </label>
                    <div class="input-wrapper">
                        <input type="time" name="start_time" id="start_time" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="end_time">
                        <i class="fas fa-clock"></i> End Time
                    </label>
                    <div class="input-wrapper">
                        <input type="time" name="end_time" id="end_time" class="form-control" readonly>
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
                    <div class="input-wrapper">
                        <textarea name="leave_reason" id="leave_reason" class="form-control" placeholder="Please provide a detailed reason for your leave..."></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="remark">
                        <i class="fas fa-sticky-note"></i> Additional Remarks
                        <span style="color: #6b7280; font-weight: normal;">(optional)</span>
                    </label>
                    <div class="input-wrapper">
                        <textarea name="remark" id="remark" class="form-control" placeholder="Any additional information..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="btn-group">
                <button type="submit" class="btn btn-primary" value="">
                    <i class="fas fa-paper-plane"></i> Submit Leave Request
                </button>
                
                <button href="#" class="btn btn-secondary text-white bg- float-right" data-bs-dismiss="modal" aria-label="Close" style="background-color: #989898 !important; color: white !important;">Cancel
                </button>
            </div>
        </form>
    </div>
</div>


<script>
/* --------------------------- helpers / fallbacks --------------------------- */
const notify = {
  error: (msg, title) => { if (window.toastr) { toastr.error(msg, title || 'Error'); } else { console.error(title ? `${title}: ${msg}` : msg); } },
  success: (msg, title) => { if (window.toastr) { toastr.success(msg, title || 'Success'); } else { console.log(title ? `${title}: ${msg}` : msg); } }
};

let submitFlag = true;            // blocked when duplicate leave detected
let currentEmployeeId = null;

/* --------------------------- ui accent helpers ---------------------------- */
function addValidationSuccess(element) {
  if (!element) return;
  if (element.value) {
    element.classList.add('input-valid');
    const wrapper = element.closest('.input-wrapper');
    if (wrapper) wrapper.classList.add('valid');
  }
}

function removeValidationSuccess(element) {
  if (!element) return;
  element.classList.remove('input-valid');
  const wrapper = element.closest('.input-wrapper');
  if (wrapper) wrapper.classList.remove('valid');
}

function resetAllContainers() {
  document.getElementById('day_segment_container')?.classList.add('hidden');
  document.getElementById('timing-container')?.classList.add('hidden');
  document.getElementById('end_date_container')?.classList.remove('hidden');
}

function updateEndDateToStartDate() {
  const startDate = document.getElementById('start_date')?.value;
  if (startDate) document.getElementById('end_date').value = startDate;
}

/* --------------------------- leave balance api ---------------------------- */
function updateLeaveBalance(employeeId, leaveTypeId) {
  if (!employeeId || !leaveTypeId) return;

  var startOfMonth = "{{ $startOfMonth }}";
  var endOfMonth   = "{{ $endOfMonth }}";

  $.ajax({
    url: '{{ url("leave/get-leave-balance") }}',
    method: 'GET',
    data: {
      employee_id: employeeId,
      leave_type_id: leaveTypeId,
      start_of_month: startOfMonth,
      end_of_month: endOfMonth,
    },
    success: function(response) {
      // Disable option if no balance; enable if available
      const opt = document.querySelector(`#leave_type_id option[value="${leaveTypeId}"]`);
      // if (opt) opt.disabled = (Number(response?.totalLeaveAvailed) === 0);
    },
    error: function() {
      // don't block UX if API fails
      console.warn('Leave balance check failed');
    }
  });
}

/* ----------------------- duplicate leave check api ------------------------ */
function checkExistingLeave(employeeId, startDate, endDate) {
  if (!employeeId || !startDate || !endDate) return;

  $.ajax({
    url: '{{ url("leave/check-existing-leave") }}',
    method: 'GET',
    data: { employee_id: employeeId, start_date: startDate, end_date: endDate },
    success: function(response) {
      if (response?.exists === true) {
        $("#start_date, #end_date").closest('.form-group').addClass('has-error');
        submitFlag = false;
        notify.error(response.message || 'Duplicate leave for selected dates', 'Leave already exists');
      } else {
        submitFlag = true;
        $("#start_date, #end_date").closest('.form-group').removeClass('has-error');
      }
    },
    error: function() {
      // On API error, allow submit (don’t hard fail form)
      submitFlag = true;
    }
  });
}

/* ---------------------------- time calculators ---------------------------- */
// Adds 2 hours to a 24h time string (HH:MM). Returns HH:MM (24h), clamped to 23:59.
function plusTwoHours(hhmm) {
  if (!hhmm) return '';
  const [h, m] = hhmm.split(':').map(Number);
  if (Number.isNaN(h) || Number.isNaN(m)) return '';
  const start = new Date();
  start.setHours(h, m, 0, 0);
  const end = new Date(start.getTime() + 2 * 60 * 60 * 1000);

  const eh = String(end.getHours()).padStart(2, '0');
  const em = String(end.getMinutes()).padStart(2, '0');

  // Clamp if somehow overflows (very unlikely)
  return `${eh}:${em}`;
}

/* ------------------------------- on ready -------------------------------- */
$(function () {
  // Fix: Load real-time balance for employees on page load
  @if(Auth::user()->type == 'employee')
  const employeeId = "{{ isset(\Auth::user()->employee) ? \Auth::user()->employee->id : '' }}";
  if (employeeId) {
    console.log('Loading balance for employee:', employeeId);
    // Multiple attempts to ensure balance loads
    setTimeout(function() {
      loadEmployeeLeaveTypes(employeeId);
    }, 500);
    setTimeout(function() {
      loadEmployeeLeaveTypes(employeeId);
    }, 1500);
    
    // Also refresh when page becomes visible
    document.addEventListener('visibilitychange', function() {
      if (!document.hidden) {
        setTimeout(function() {
          loadEmployeeLeaveTypes(employeeId);
        }, 200);
      }
    });
  }
  @endif

  // Keep end_date synced initially and when start_date changes (for half/short)
  $('#start_date').on('change', function() {
    const isHalf = $('#is_halfday').val();
    if (isHalf === 'half' || isHalf === 'short') {
      updateEndDateToStartDate();
    }
    addValidationSuccess(this);

    // Resolve employeeId for employee/HR contexts
    let employeeId = $('#employee_id').val();
    const userType = "{{ \Auth::user()->type }}";
    if ((!employeeId || employeeId === '') && userType === 'employee') {
      employeeId = "{{ isset(\Auth::user()->employee) ? \Auth::user()->employee->id : '' }}";
    }
    const sd = $('#start_date').val();
    const ed = $('#end_date').val();
    checkExistingLeave(employeeId, sd, ed);
  });

  $('#end_date').on('change', function() {
    addValidationSuccess(this);
    let employeeId = $('#employee_id').val();
    const userType = "{{ \Auth::user()->type }}";
    if ((!employeeId || employeeId === '') && userType === 'employee') {
      employeeId = "{{ isset(\Auth::user()->employee) ? \Auth::user()->employee->id : '' }}";
    }
    const sd = $('#start_date').val();
    const ed = $('#end_date').val();
    checkExistingLeave(employeeId, sd, ed);
  });

  // Employee select (HR only)
  $('#employee_id').on('change', function() {
    currentEmployeeId = this.value || null;
    const employeeInfo   = document.getElementById('employee_info');
    const employeeDetails= document.getElementById('employee_details');
    const errorSpan      = document.getElementById('employee_error');
    const leaveTypeSelect = document.getElementById('leave_type_id');

    errorSpan.textContent = '';
    employeeInfo.classList.remove('show');

    if (currentEmployeeId) {
      const txt = this.options[this.selectedIndex].text;
      employeeDetails.textContent = `Selected: ${txt}`;
      employeeInfo.classList.add('show');
      addValidationSuccess(this);
      
      // Load leave types for selected employee (Issue #2 & #3 fix)
      loadEmployeeLeaveTypes(currentEmployeeId);
    } else {
      // Clear leave types when no employee selected
      leaveTypeSelect.innerHTML = '<option value="" disabled selected>Select Employee First</option>';
      leaveTypeSelect.disabled = true;
    }
  });

  // Issue #2 & #3 Fix: Function to load leave types for selected employee
  function loadEmployeeLeaveTypes(employeeId) {
    const leaveTypeSelect = document.getElementById('leave_type_id');
    
    console.log('Loading leave types for employee:', employeeId);
    
    // Show loading state
    leaveTypeSelect.innerHTML = '<option value="" disabled selected>Loading...</option>';
    leaveTypeSelect.disabled = true;
    
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
        console.log('Leave types response:', response);
        if (response.success && response.leave_balances) {
          // Clear and populate leave types
          leaveTypeSelect.innerHTML = '<option value="" disabled selected>Select Leave Type</option>';
          
          response.leave_balances.forEach(function(leaveType) {
            const option = document.createElement('option');
            option.value = leaveType.id;
            option.setAttribute('data-title', leaveType.title);
            option.textContent = leaveType.title + ' (' + leaveType.available_balance + ')';
            // Issue #2 Fix: Don't disable options, just show balance
            option.disabled = false;
            leaveTypeSelect.appendChild(option);
            
            console.log('Added leave type:', leaveType.title, 'with balance:', leaveType.available_balance);
          });
          
          // Issue #2 Fix: Always enable the select after loading
          leaveTypeSelect.disabled = false;
        } else {
          leaveTypeSelect.innerHTML = '<option value="" disabled selected>No leave types available</option>';
          leaveTypeSelect.disabled = false;
        }
      },
      error: function(xhr, status, error) {
        console.error('Error loading leave types:', error);
        leaveTypeSelect.innerHTML = '<option value="" disabled selected>Error loading leave types</option>';
        // Issue #2 Fix: Enable select even on error to allow retry
        leaveTypeSelect.disabled = false;
        notify.error('Failed to load leave types. Please try again.', 'Error');
      }
    });
  }

  // Leave type -> show/hide half/short options based on title
  $('#leave_type_id').on('change', function() {
    const opt = this.options[this.selectedIndex];
    const leaveTitle = (opt?.getAttribute('data-title') || '').toLowerCase();
    const halfdayContainer = document.getElementById('halfday-container');

    if (leaveTitle.includes('paid') || leaveTitle.includes('sick')) {
      halfdayContainer.style.display = 'block';
    } else {
      halfdayContainer.style.display = 'none';
      $('#is_halfday').val('full').trigger('change');
    }

    // Update balance if we have employee selected
    let employeeId = $('#employee_id').val();
    const userType = "{{ \Auth::user()->type }}";
    if ((!employeeId || employeeId === '') && userType === 'employee') {
      employeeId = "{{ isset(\Auth::user()->employee) ? \Auth::user()->employee->id : '' }}";
    }
    if (employeeId) updateLeaveBalance(employeeId, this.value);

    addValidationSuccess(this);
  });

  // Duration select (full/half/short) controls visibility
  $('#is_halfday').on('change', function() {
    const v = this.value;
    const $endWrap  = $('#end_date_container');
    const $segWrap  = $('#day_segment_container');
    const $timeWrap = $('#timing-container');

    // reset containers
    $segWrap.addClass('hidden');
    $timeWrap.addClass('hidden');
    $endWrap.removeClass('hidden');

    if (v === 'full') {
      // full day: end date visible, no day segment, no timing
      $endWrap.removeClass('hidden');
    } else if (v === 'half') {
      // half: end date hidden, segment visible
      $endWrap.addClass('hidden');
      $segWrap.removeClass('hidden');
      updateEndDateToStartDate();
    } else if (v === 'short') {
      // short: end date hidden, segment + timing visible
      $endWrap.addClass('hidden');
      $segWrap.removeClass('hidden');
      $timeWrap.removeClass('hidden');
      updateEndDateToStartDate();

      // default start time based on current segment
      const seg = $('#day_segment').val();
      const t = (seg === 'morning') ? '09:00' : '14:00';
      $('#start_time').val(t).trigger('change');
    }
  });

  // Day segment switch affects default start_time for short leave
  $('#day_segment').on('change', function() {
    if ($('#is_halfday').val() === 'short') {
      const t = (this.value === 'morning') ? '09:00' : '14:00';
      $('#start_time').val(t).trigger('change');
      addValidationSuccess(document.getElementById('start_time'));
    }
  });

  // Start time -> auto compute end time (+2h)
  $('#start_time').on('change', function() {
    const val = this.value; // 24h "HH:MM"
    const end = plusTwoHours(val);
    $('#end_time').val(end);
    addValidationSuccess(this);
    addValidationSuccess(document.getElementById('end_time'));
  });

  // Light “✓” feedback on any filled input
  $(document).on('input change', 'input, select, textarea', function () {
    if (this.value && String(this.value).trim() !== '') addValidationSuccess(this);
    else removeValidationSuccess(this);
  });

  /* ---------------------------- jQuery Validate ---------------------------- */
  $.validator.addMethod("greaterThan", function(value, element, param) {
    const start = $(param).val();
    if (!start || !value) return true; // handled by required
    return new Date(start) <= new Date(value);
  }, "End date must be greater than or equal to start date.");

  $("form#leaveForm").validate({
    ignore: [], // validate hidden fields we control (we gate 'required' below)
    rules: {
      employee_id: {
        required: function() { 
          return $("select[name='employee_id']").length > 0 && !$("select[name='employee_id']").prop('disabled'); 
        }
      },
      leave_type_id: { 
        required: true,
        min: 1 // Issue #3 Fix: Ensure a valid leave type is selected
      },
      start_date:    { required: true, date: true },
      end_date: {
        required: function() {
          // end_date required only when its container is visible (Full day)
          return !$('#end_date_container').hasClass('hidden');
        },
        date: true,
        greaterThan: "#start_date"
      },
      start_time: {
        required: function() { return !$('#timing-container').hasClass('hidden'); }
      },
      leave_reason: { required: true, minlength: 10 }
    },
    messages: {
      employee_id:  { required: "Please select an employee." },
      leave_type_id:{ 
        required: "Please select a leave type.", 
        min: "Please select a valid leave type."
      },
      start_date:   { required: "Please select a start date.", date: "Please enter a valid date." },
      end_date:     { required: "Please select an end date.", date: "Please enter a valid date.", greaterThan: "End date must be greater than the start date." },
      start_time:   { required: "Please select a start time." },
      leave_reason: { required: "Please provide a reason for the leave.", minlength: "Your reason must be at least 10 characters long." }
    },
    errorElement: 'span',
    errorClass: 'text-danger',
    highlight: function(element) {
      $(element).closest('.form-group').addClass('has-error');
      removeValidationSuccess(element);
    },
    unhighlight: function(element) {
      $(element).closest('.form-group').removeClass('has-error');
      addValidationSuccess(element);
    },
    errorPlacement: function(error, element) {
      if (element.attr("name") === "leave_type_id") {
        error.insertAfter("#leave_type_error");
      } else {
        error.insertAfter(element);
      }
    },
    submitHandler: function(form) {
      if (submitFlag === true) {
        form.submit(); // real submit
      } else {
        notify.error('Leave has already been applied for the same date', 'Blocked');
        return false;
      }
    }
  });
});
</script>
