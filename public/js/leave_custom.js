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
    
    $('#leave_type_id').on('change', function() {
        var leaveTypeId = this.value;
        var employeeId = document.getElementById('employee_id').value;
    
        if (employeeId && leaveTypeId) {
            updateLeaveBalance(employeeId, leaveTypeId);
        }
    });
    
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
                // if (leaveBalanceElement) {
                    leaveBalanceElement.textContent = response.totalLeaveAvailed;
                    if(response.totalLeaveAvailed == 0){
                            optionElement.disabled = true;
                    }else{
                        optionElement.disabled = false;
                    }
                // }
            }
        });
    }
    

    var submitFlag = true;
    function checkExistingLeave(employeeId, startDate, endDate) {
        if (!employeeId || !startDate || !endDate) {
            return; 
        }

        $.ajax({
            url: '{{ url("leave/check-existing-leave") }}',
            method: 'GET',
            data: {
                employee_id: employeeId,
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                if (response.exists == true) {
                    $("#start_date, #end_date").closest('.form-group').addClass('has-error');
                    submitFlag = false;
                    toastr.error(response.message, 'Leave has already been applied for the same date');
                    // $("#existing_leave_error").text(response.message);
                } else {
                    submitFlag = true;
                    $("#start_date, #end_date").closest('.form-group').removeClass('has-error');
                    $("#existing_leave_error").text('');
                }
            }
        });
    }

    // Trigger the AJAX call on change of start or end date
    $('#start_date, #end_date').on('change', function() {
        var employeeId = $('#employee_id').val();
        if(employeeId == '' || employeeId == undefined){
            employeeId = "{{ \Auth::user()->employee->id }}";
        }
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();
        checkExistingLeave(employeeId, startDate, endDate);
    });
    
        
    $.validator.addMethod("greaterThan", function(value, element, param) {
        var startDate = $(param).val();
        var endDate = value;
        if (!startDate || !endDate) {
            // If either date is not filled, do not validate
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
                date: true
            },
            end_date: {
                required: true,
                date: true,
                greaterThan: "#start_date" // Uncomment and add custom validation for date comparison if needed
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
                error.insertAfter("#leave_type_error"); // Place the error message below the leave type dropdown
            } else {
                error.insertAfter(element); // Default placement for other elements
            }
        },
        submitHandler: function(form) {
            if(submitFlag == true){
                form.submit();
            }
            else{
                toastr.error('Leave has already been applied for the same date', 'Error');
                return false;
            }
        }
    });
});