<div class="card bg-none card-box">
    <form action="{{ route('leave.update', $leave->id) }}" method="post" id="leaveForm">
        @csrf
        @method('PUT')
        @if (\Auth::user()->type != 'employee')
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="employee_id">{{ __('Employee') }}</label>
                        <select name="employee_id" id="employee_id" class="form-control select2" placeholder="{{ __('Select Employee') }}">
                            @foreach ($employees as $id => $name)
                                <option value="{{ $id }}" @if ($id == $leave->employee_id) selected @endif>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @else
            <input type="hidden" value="{{ $leave->employee_id }}" name="employee_id" id="employee_id">
        @endif

        <div class="row">
            <div class="col-md-12" id="leave-container">
                <div class="form-group">
                    <label for="leave_type_id">{{ __('Leave Type') }}</label>
                    <select name="leave_type_id" id="leave_type_id" class="form-control select2" onchange="halfDayLeave()">
                        <option value="" disabled>Select Leave Type</option>
                        @if(\Auth::user()->type != 'employee')
                            @foreach ($leavetypes as $id => $leaveData)
                                <option value="{{ $leaveData->id }}" data-title="{{ $leaveData }}" {{ $leaveData->id == $leave->leave_type_id ? 'selected' : '' }}>
                                    {{ $leaveData->title }}
                                    <span id="leave-balance-{{ $leaveData->id }}"></span>
                                </option>
                            @endforeach
                        @else
                            @foreach ($leavetypes as $key => $leavetype)
                                <option value="{{ $leavetype->id }}" data-title="{{ $leavetype->title }}" {{ $leavetype->id == $leave->leave_type_id ? 'selected' : '' }} {{ ($leavetype->days == 0 && $leave->status != 'Pending') ? 'disabled' : '' }} {{ ($leavetype->title == "Paid Leave" && \Auth::user()->employee->paid_leave_balance - $totalLeaveAvailed == 0) ? 'disable' : '' }}>
                                    {{ $leavetype->title }}
                                    @if ($leavetype->title == "Paid Leave")
                                        ({{ \Auth::user()->employee->paid_leave_balance - $totalLeaveAvailed }})
                                    @else
                                        ({{ $leavetype->days }})
                                    @endif
                                </option>
                            @endforeach
                        @endif
                    </select>
                    <span id="leave_type_error" class="text-danger"></span>
                </div>
            </div>

            <div class="col-md-6" id="halfday-container" style="display: none;">
                <div class="form-group">
                    <label for="is_halfday">{{ __('Half Day / Full Day') }}</label>
                    <select name="is_halfday" id="is_halfday" class="form-control select2">
                        <option value="full" {{ old('is_halfday', $leave->is_halfday) == 'full' ? 'selected' : '' }}>Full Day</option>
                        <option value="half" {{ old('is_halfday', $leave->is_halfday) == 'half' ? 'selected' : '' }}>Half Day</option>
                        <option value="short" {{ old('is_halfday', $leave->is_halfday) == 'short' ? 'selected' : '' }}>Short Leave</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="start_date">{{ __('Start Date') }}</label>
                    <input type="text" name="start_date" id="start_date" class="form-control datepicker" value="{{ date('d-m-Y', strtotime($leave->start_date)) }}">
                </div>
            </div>
            <div class="col-md-6" id="end_date_container">
                <div class="form-group">
                    <label for="end_date">{{ __('End Date') }}</label>
                    <input type="text" name="end_date" id="end_date" class="form-control datepicker" value="{{ date('d-m-Y', strtotime($leave->end_date)) }}">
                </div>
            </div>

            <div class="col-md-6" id="day_segment_option">
                <div class="form-group">
                    <label for="day_segment">{{ __('Day Segment') }}</label>
                    <select name="day_segment" id="day_segment" class="form-control select2">
                        <option value="morning" {{ old('day_segment', $leave->day_segment) == 'morning' ? 'selected' : '' }}>Morning</option>
                        <option value="afternoon" {{ old('day_segment', $leave->day_segment) == 'afternoon' ? 'selected' : '' }}>Afternoon</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row timing">
            <div class="col-md-6" id="start_time_container">
                <div class="form-group">
                    <label for="start_time">{{ __('Start Time') }}</label>
                    <input type="text" name="start_time" id="start_time" class="form-control timepicker" value="{{ $leave->start_time }}">
                </div>
            </div>
            <div class="col-md-6" id="end_time_container">
                <div class="form-group">
                    <label for="end_time">{{ __('End Time') }}</label>
                    <input type="text" name="end_time" id="end_time" class="form-control timepicker" value="{{ $leave->end_time }}">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="leave_reason">{{ __('Leave Reason') }}</label>
                    <textarea name="leave_reason" id="leave_reason" class="form-control" placeholder="{{ __('Leave Reason') }}">{{ $leave->leave_reason }}</textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="remark">{{ __('Remark') }}</label>
                    <textarea name="remark" id="remark" class="form-control" placeholder="{{ __('Leave Remark') }}">{{ $leave->remark }}</textarea>
                </div>
            </div>
        </div>

        @role('Company')
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="status">{{ __('Status') }}</label>
                        <select name="status" id="status" class="form-control select2">
                            <option value="">{{ __('Select Status') }}</option>
                            <option value="pending" @if ($leave->status == 'Pending') selected @endif>{{ __('Pending') }}</option>
                            <option value="approval" @if ($leave->status == 'Approval') selected @endif>{{ __('Approval') }}</option>
                            <option value="reject" @if ($leave->status == 'Reject') selected @endif>{{ __('Reject') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        @endrole

        <div class="row">
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('.timepicker').timepicker({
            timeFormat: 'h:mm p',
            interval: 60,
            minTime: '10',
            maxTime: '10:00pm',
            defaultTime: '11',
            startTime: '09:00',
            dynamic: false,
            dropdown: true,
            scrollbar: true
        });

        $('.timing').hide();
        $('#day_segment_option').hide();
        $('#is_halfday').change(function() {
            var selectedValue = $(this).val();
            if (selectedValue == 'short') {
                $('.timing').show();
                var start_date = $('#start_date').val();
                $('#end_date').val(start_date);
                $('#end_date_container').hide();
                $('#day_segment_option').show();
            } else {
                $('.timing').hide();
                $('#end_date').show();
            }
            
            if (selectedValue == 'half') {
                $('#end_date_container').hide();
                $('#day_segment_option').show();
                var start_date = $('#start_date').val();
                $('#end_date').val(start_date);
                $('#end_date').hide();
            } else if (selectedValue == 'full') {
                $('#end_date_container').show();
                $('#day_segment_option').hide();
                $('#end_date').show();
            }
        });

        function halfDayLeave() {
            var selectElement = document.getElementById('leave_type_id');
            var daySegment = document.getElementById('day_segment');
            var endDate = document.getElementById('end_date');
            var leaveDiv = document.getElementById('leave-container');
            var selectedOption = selectElement.options[selectElement.selectedIndex];
            var leaveTitle = selectedOption.getAttribute('data-title');
            const halfdayContainer = document.getElementById('halfday-container');
            if (leaveTitle === 'Paid Leave') {
                halfdayContainer.style.display = 'block';
                leaveDiv.classList.remove('col-md-12');
                leaveDiv.classList.add('col-md-6');
            } else {
                daySegment.style.display = 'none';
                endDate.style.display = 'block';
                halfdayContainer.style.display = 'none';
                leaveDiv.classList.add('col-md-12');
            }
        }

        $('#employee_id').on('change', function() {
            var employeeId = this.value;
            var leaveTypeId = document.getElementById('leave_type_id').value;
            
            if (employeeId && leaveTypeId) {
                updateLeaveBalance(employeeId, leaveTypeId);
            }
        });

        document.getElementById('leave_type_id').addEventListener('change', function() {
            var leaveTypeId = this.value;
            var employeeId = document.getElementById('employee_id').value;

            if (employeeId && leaveTypeId) {
                updateLeaveBalance(employeeId, leaveTypeId);
            }
        });

        var userType = "{{ \Auth::user()->type }}";
        if( userType == 'hr') {
            var employeeId = $('#employee_id').val();
            var leaveId = "{{ $leave->leave_type_id }}";
            if (employeeId) {
                $.ajax({
                    url: "{{ url('/leave/get-paid-leave-balance') }}" + "/" + employeeId,
                    type: 'GET',
                    success: function(response) {
                        if (response.leavetypes) {
                            $('#leave_type_id').html('');
                            $.each(response.leavetypes, function(index, leave) {
                                var optionText = leave.title;
                                if (leave.title === "Paid Leave") {
                                    optionText += ' (' + leave.days + ')';
                                } else {
                                    optionText += ' (' + leave.days + ')';
                                }
                                // Check if leave.days is 0, then add disabled attribute
                                var optionAttributes = {
                                    value: leave.id,
                                    text: optionText,
                                    {{--  disabled: leave.days === 0  --}}
                                };
                                // Check if the leaveId matches the current leave.id
                                if (leaveId == leave.id) {
                                    optionAttributes.selected = 'selected';
                                }
            
                                var option = $('<option>', optionAttributes);
                                $('#leave_type_id').append(option);
                            });
                        }
                    },
                    error: function() {
                        console.log('Error fetching paid leave balance');
                    }
                });
            }
        }


        function updateLeaveBalance(employeeId, leaveTypeId) {
            var startOfMonth = "{{ $startOfMonth }}"; 
            var endOfMonth = "{{ $endOfMonth }}";
            
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
                    var optionElement = document.querySelector('option[value="' + leaveTypeId + '"]');
                    var leaveBalanceElement = document.getElementById('leave-balance-' + leaveTypeId);
                    leaveBalanceElement.textContent = response.totalLeaveAvailed;
                    if(response.totalLeaveAvailed == 0){
                        optionElement.disabled = true;
                    } else {
                        optionElement.disabled = false;
                    }
                }
            });
        }

    
        var submitFlag = true;
        function checkExistingLeave(employeeId, startDate, endDate) {
            if (!employeeId || !startDate || !endDate) {
                return; 
            }
            
            var leave_id = "{{ $leave->id }}";

            $.ajax({
                url: '{{ url("leave/check-existing-leave") }}',
                method: 'GET',
                data: {
                    employee_id: employeeId,
                    start_date: startDate,
                    end_date: endDate,
                    leave_id: leave_id,
                },
                success: function(response) {
                    if (response.exists == true) {
                        $("#start_date, #end_date").closest('.form-group').addClass('has-error');
                        submitFlag = false;
                        toastr.error(response.message, 'Leave has already been applied for the same start/end date');
                    } else {
                        submitFlag = true;
                        $("#start_date, #end_date").closest('.form-group').removeClass('has-error');
                        $("#existing_leave_error").text('');
                    }
                }
            });
        }

        $('#start_date, #end_date').on('change', function() {
            var employeeId = $('#employee_id').val();
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();
            checkExistingLeave(employeeId, startDate, endDate);
        });

        $.validator.addMethod("greaterThan", function(value, element, param) {
            var startDate = $(param).val();
            var endDate = value;
            if (!startDate || !endDate) {
                return true;
            }
            return new Date(startDate) <= new Date(endDate);
        }, "End date must be greater than or equal to start date.");
            
        $("form#leaveForm").validate({
            rules: {
                employee_id: {
                    required: function() {
                        return $("select[name='employee_id']").length > 0;
                    }
                },
                leave_type_id: {
                    required: true
                },
                start_date: {
                    required: true,
                    dateFormat: true
                },
                end_date: {
                    required: true,
                    dateFormat: true,
                    greaterThan: "#start_date"
                },
                start_time: {
                    required: function() {
                        return $("#start_time_container").is(':visible');
                    },
                    time: true
                },
                end_time: {
                    required: function() {
                        return $("#end_time_container").is(':visible');
                    },
                    time: true
                },
                leave_reason: {
                    required: true,
                    minlength: 10
                }
            },
            messages: {
                employee_id: {
                    required: "Please select an employee."
                },
                leave_type_id: {
                    required: "Please select a leave type."
                },
                start_date: {
                    required: "Please select a start date.",
                    date: "Please enter a valid date."
                },
                end_date: {
                    required: "Please select an end date.",
                    date: "Please enter a valid date.",
                    greaterThan: "End date must be greater than the start date."
                },
                start_time: {
                    required: "Please select a start time.",
                    time: "Please enter a valid time."
                },
                end_time: {
                    required: "Please select an end time.",
                    time: "Please enter a valid time."
                },
                leave_reason: {
                    required: "Please provide a reason for the leave.",
                    minlength: "Your reason must be at least 10 characters long."
                }
            },
            errorElement: 'span',
            errorClass: 'text-danger',
            highlight: function(element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function(element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "leave_type_id") {
                    error.insertAfter("#leave_type_error");
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                if(submitFlag == true){
                    form.submit();
                }
                else{
                    toastr.error('Leave has already been applied for the same start/end date', 'Error');
                    return false;
                }
            }
        });
    });
</script>
