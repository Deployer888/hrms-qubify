@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Leave') }}
@endsection

@section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        @can('Create Leave')
            @if($selfLeaves == 'true' || \Auth::user()->type == 'hr')
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="{{ route('leave.create') }}" class="btn btn-xs btn-white btn-icon-only width-auto"
                    data-ajax-popup="true" data-title="{{ __('Create New Leave') }}">
                    <i class="fa fa-plus"></i> {{ __('Create') }}
                </a>
            </div>
            @endif
        @endcan
        <!--<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">-->
        <!--    <a href="{{ route('leave.export') }}" class="btn btn-xs btn-white btn-icon-only width-auto">-->
        <!--        <i class="fa fa-file-excel"></i> {{-- __('Export') --}}-->
        <!--    </a>-->
        <!--</div>-->
    </div>

@endsection
@if($data??0)
    <div class="modal fade show" id="commonModalCustom" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-modal="true" style="display: block; padding-left: 7px;backdrop-filter: blur(10px)">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div>
                    <h4 class="h4 font-weight-400 float-left modal-title" id="exampleModalLabel">Leave Action</h4>
                    <a href="#" class="more-text widget-text float-right close-icon" data-dismiss="modal" aria-label="Close" onclick="removeModel()">Close</a>
                </div>
                <div class="modal-body">
                    {!!$data!!}
                </div>
            </div>
        </div>
    </div>
@endif
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0 dataTable" id="datatable">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>{{ __('Leave Type') }}</th>
                                    <th>{{ __('Applied On') }}</th>
                                    <th>{{ __('Start Date') }}</th>
                                    <th>{{ __('End Date') }}</th>
                                    <th>{{ __('Total Days') }}</th>
                                    <th>{{ __('Half/Full Day') }}</th>
                                    <th>{{ __('Leave Reason') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th width="3%">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0; ?>
                                @if(\Auth::user()->type == 'employee' && $selfLeaves == 'true')
                                
                                    @foreach ($leaves as $key => $leave)
                                        <tr>
                                            <td id="sno">{{ ++$i }}</td>
                                            @if (\Auth::user()->type != 'employee' || \Auth::user()->is_team_leader == '0')
                                                <td>{{ !empty(\Auth::user()->getEmployee($leave->employee_id)) ? \Auth::user()->getEmployee($leave->employee_id)->name : '' }}
                                                </td>
                                            @endif
                                            <td>{{ !empty(\Auth::user()->getLeaveType($leave->leave_type_id)) ? \Auth::user()->getLeaveType($leave->leave_type_id)->title : '' }}
                                            </td>
                                            <td>{{ \Auth::user()->dateFormat($leave->applied_on) }}</td>
                                            <td>{{ \Auth::user()->dateFormat($leave->start_date) }} @if($leave->leavetype == 'short') {{ $leave->start_time }} @endif</td>
                                            <td>{{ \Auth::user()->dateFormat($leave->end_date) }} @if($leave->leavetype == 'short') {{ $leave->end_time }} @endif</td>
                                            @php
                                                    $startDate = new \DateTime($leave->start_date);
                                                    $endDate = new \DateTime($leave->end_date);
    
                                                    // Include end date in the calculation
                                                    $endDate->modify('+1 day');
    
                                                    $interval = new \DateInterval('P1D');
                                                    $daterange = new \DatePeriod($startDate, $interval, $endDate);
    
                                                    $total_leave_days = 0;
                                                    if($leave->leavetype == 'half' || $leave->leavetype == 'short'){
                                                        $total_leave_days = $leave->total_leave_days;
                                                    }else{
                                                        foreach ($daterange as $date) {
                                                            if ($date->format('N') < 6) { // 'N' gives day of the week, 1 (for Monday) through 7 (for Sunday)
                                                                $total_leave_days++;
                                                            }
                                                        }
                                                    }
                                            @endphp
                                            <td class="text-center">{{-- $total_leave_days --}} {{ $leave->total_leave_days }}</td>
                                            <td>{{ ucwords($leave->leavetype) }} @if($leave->day_segment) ({{ ucwords($leave->day_segment) }}) @endif</td>
                                            <td>{{ \Illuminate\Support\Str::limit($leave->leave_reason, 20) }} {{-- $leave->leave_reason --}}</td>
                                            <td>
                                                @if ($leave->status == 'Pending')
                                                    <div class="badge badge-pill badge-warning">{{ $leave->status }}</div>
                                                @elseif($leave->status == 'Approve')
                                                    <div class="badge badge-pill badge-success">{{ $leave->status }}</div>
                                                @else($leave->status == 'Reject')
                                                    <a href="#"
                                                        data-url="{{ URL::to('leave/' . $leave->id . '/reason') }}"
                                                        data-size="lg" data-ajax-popup="true"
                                                        data-title="{{ __('See Reason') }}"
                                                        data-toggle="tooltip" data-original-title="{{ __('See Reason') }}">
                                                        <div class="badge btn badge-pill badge-danger" id="rejectStatus">{{ $leave->status }}</div>
                                                    </a>
                                                @endif
                                            </td>
                                            <td class="text-right action-btns">
                                                @if (\Auth::user()->type == 'employee' && (\Auth::user()->employee->is_team_leader == 0 ||  \Auth::user()->employee->id == $leave->employee_id))
                                                    @if ($leave->status == 'Pending')
                                                        @can('Edit Leave')
                                                            <a href="#"
                                                                data-url="{{ URL::to('leave/' . $leave->id . '/edit') }}"
                                                                data-size="lg" data-ajax-popup="true"
                                                                data-title="{{ __('Edit Leave') }}" class="edit-icon"
                                                                data-toggle="tooltip" data-original-title="{{ __('Edit') }}"><i
                                                                    class="fas fa-pencil-alt"></i></a>
                                                        @endcan
                                                    @endif
                                                @else
                                                    {{-- @if (\Carbon\Carbon::parse($leave->end_date)->greaterThan(\Carbon\Carbon::now()))--}}
                                                        <a href="#"
                                                            data-url="{{ URL::to('leave/' . $leave->id . '/action') }}"
                                                            data-size="lg" data-ajax-popup="true"
                                                            data-title="{{ __('Leave Action') }}" class="edit-icon bg-success"
                                                            data-toggle="tooltip" data-original-title="{{ __('Leave Action') }}"><i
                                                                class="fas fa-caret-right"></i> </a>
                                                        @can('Edit Leave')
                                                            @if ($leave->status == 'Pending' && (\Auth::user()->type == 'employee' && \Auth::user()->employee->is_team_leader == 0) || \Auth::user()->type == 'hr' || \Auth::user()->type == 'company')
                                                                <a href="#" data-url="{{ URL::to('leave/' . $leave->id . '/edit') }}"
                                                                    data-size="lg" data-ajax-popup="true"
                                                                    data-title="{{ __('Edit Leave') }}" class="edit-icon"
                                                                    data-toggle="tooltip" data-original-title="{{ __('Edit') }}"><i
                                                                        class="fas fa-pencil-alt"></i></a>
                                                            @endif
                                                        @endcan
                                                    {{-- @endif --}}
                                                @endif
                                                @if ($leave->status == 'Pending' && (\Auth::user()->type == 'employee' && \Auth::user()->employee->id == $leave->employee_id || \Auth::user()->type == 'employee' && \Auth::user()->employee->is_team_leader == 0) || \Auth::user()->type == 'hr' || \Auth::user()->type == 'company')
                                                    @can('Edit Leave')
                                                        @can('Delete Leave')
                                                            <a href="#" class="delete-icon" data-toggle="tooltip"
                                                                data-original-title="{{ __('Delete') }}"
                                                                data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="document.getElementById('delete-form-{{ $leave->id }}').submit();"><i
                                                                    class="fas fa-trash"></i></a>
                                                            <form method="POST" action="{{ route('leave.destroy', $leave->id) }}"
                                                                id="delete-form-{{ $leave->id }}">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        @endif
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    @foreach ($leaves as $employee)
                                        @php
                                            // Get the leaves for each employee directly from the relationship
                                            $employeeLeaves = $employee->employeeLeaves;
                                        @endphp
                                        
                                        @if(!count($employeeLeaves)) @continue @endif
                                    
                                        {{-- Employee Name Row --}}
                                        <tr class="employee-name-row text-center" >
                                            <th colspan="11" class="pt-3">
                                                <u style="font-size: large;">{{ $employee->name }} <strong>( Total Leaves: {{!empty($employeeLeaves) ? count($employeeLeaves) : 'Note Taken Yet'}} )</strong></u>
                                            </th>
                                        </tr>
                                    
                                        {{-- Loop through the leaves for this employee --}}
                                        
                                        @foreach ($employeeLeaves as $leave)
                                            <tr>
                                                <td id="sno">{{ ++$i }}</td>
                                                <td>{{ !empty(\Auth::user()->getLeaveType($leave->leave_type_id)) ? \Auth::user()->getLeaveType($leave->leave_type_id)->title : '' }}</td>
                                                <td>{{ \Auth::user()->dateFormat($leave->applied_on) }}</td>
                                                <td>{{ \Auth::user()->dateFormat($leave->start_date) }} @if($leave->leavetype == 'short') {{ $leave->start_time }} @endif</td>
                                                <td>{{ \Auth::user()->dateFormat($leave->end_date) }} @if($leave->leavetype == 'short') {{ $leave->end_time }} @endif</td>
                                    
                                                @php
                                                    $startDate = new \DateTime($leave->start_date);
                                                    $endDate = new \DateTime($leave->end_date);
                                                    $endDate->modify('+1 day');
                                                    $interval = new \DateInterval('P1D');
                                                    $daterange = new \DatePeriod($startDate, $interval, $endDate);
                                    
                                                    $total_leave_days = 0;
                                                    if ($leave->leavetype == 'half' || $leave->leavetype == 'short') {
                                                        $total_leave_days = $leave->total_leave_days;
                                                    } else {
                                                        foreach ($daterange as $date) {
                                                            if ($date->format('N') < 6) { // Weekdays only
                                                                $total_leave_days++;
                                                            }
                                                        }
                                                    }
                                                @endphp
                                                <td class="text-center">{{ $leave->total_leave_days }}</td>
                                                <td>{{ ucwords($leave->leavetype) }} @if($leave->day_segment) ({{ ucwords($leave->day_segment) }}) @endif</td>
                                                <td>{{ \Illuminate\Support\Str::limit($leave->leave_reason, 20) }}</td>
                                    
                                                <td>
                                                    @if ($leave->status == 'Pending')
                                                        <div class="badge badge-pill badge-warning">{{ $leave->status }}</div>
                                                    @elseif($leave->status == 'Approve')
                                                        <div class="badge badge-pill badge-success">{{ $leave->status }}</div>
                                                    @else
                                                        <a href="#"
                                                           data-url="{{ URL::to('leave/' . $leave->id . '/reason') }}"
                                                           data-size="lg" data-ajax-popup="true"
                                                           data-title="{{ __('See Reason') }}"
                                                           data-toggle="tooltip" data-original-title="{{ __('See Reason') }}">
                                                            <div class="badge btn badge-pill badge-danger" id="rejectStatus">{{ $leave->status }}</div>
                                                        </a>
                                                    @endif
                                                </td>
                                    
                                                <td class="text-right action-btns">
                                                    @if (\Auth::user()->type == 'employee' && (\Auth::user()->employee->is_team_leader == 0 || \Auth::user()->employee->id == $leave->employee_id))
                                                        @if ($leave->status == 'Pending')
                                                            @can('Edit Leave')
                                                                <a href="#"
                                                                   data-url="{{ URL::to('leave/' . $leave->id . '/edit') }}"
                                                                   data-size="lg" data-ajax-popup="true"
                                                                   data-title="{{ __('Edit Leave') }}" class="edit-icon"
                                                                   data-toggle="tooltip" data-original-title="{{ __('Edit') }}">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                </a>
                                                            @endcan
                                                        @endif
                                                    @else
                                                        <a href="#"
                                                           data-url="{{ URL::to('leave/' . $leave->id . '/action') }}"
                                                           data-size="lg" data-ajax-popup="true"
                                                           data-title="{{ __('Leave Action') }}" class="edit-icon bg-success"
                                                           data-toggle="tooltip" data-original-title="{{ __('Leave Action') }}">
                                                            <i class="fas fa-caret-right"></i>
                                                        </a>
                                                        @can('Edit Leave')
                                                            @if ($leave->status == 'Pending' && (\Auth::user()->type == 'employee' && \Auth::user()->employee->is_team_leader == 0) || \Auth::user()->type == 'hr' || \Auth::user()->type == 'company')
                                                                <a href="#" data-url="{{ URL::to('leave/' . $leave->id . '/edit') }}"
                                                                   data-size="lg" data-ajax-popup="true"
                                                                   data-title="{{ __('Edit Leave') }}" class="edit-icon"
                                                                   data-toggle="tooltip" data-original-title="{{ __('Edit') }}">
                                                                    <i class="fas fa-pencil-alt"></i>
                                                                </a>
                                                            @endif
                                                        @endcan
                                                    @endif
                                    
                                                    @if ($leave->status == 'Pending' && (\Auth::user()->type == 'employee' && \Auth::user()->employee->id == $leave->employee_id || \Auth::user()->type == 'employee' && \Auth::user()->employee->is_team_leader == 0) || \Auth::user()->type == 'hr' || \Auth::user()->type == 'company')
                                                        @can('Delete Leave')
                                                            <a href="#" class="delete-icon" data-toggle="tooltip"
                                                               data-original-title="{{ __('Delete') }}"
                                                               data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                               data-confirm-yes="document.getElementById('delete-form-{{ $leave->id }}').submit();">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                            <form method="POST" action="{{ route('leave.destroy', $leave->id) }}"
                                                                  id="delete-form-{{ $leave->id }}">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        @endcan
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                       
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('script-page')
        <script>
            function removeModel()
            {
                document.getElementById('commonModalCustom').remove();
            }
            
            $(document).on('change', '#employee_id', function() {
                    var employeeId = $(this).val();
                    if (employeeId) {
                        $.ajax({
                            url: "{{ url('/leave/get-paid-leave-balance') }}" + "/" + employeeId,
                            type: 'GET',
                            success: function(response) {
                                /*if (response.leavetypes) {
                                    $('#leave_type_id').html('');
                                    $.each(response.leavetypes, function(index, leave) {
                                        var optionText = leave.title;
                                        if (leave.title === "Paid Leave") {
                                            optionText += ' (' + leave.days + ')';
                                        } else {
                                            optionText += ' (' + leave.days + ')';
                                        }
                                        // Check if leave.days is 0, then add disabled attribute
                                        var option = $('<option>', {
                                            value: leave.id,
                                            text: optionText,
                                            'data-title': leave.title,
                                            disabled: leave.days === 0
                                        });
                                        $('#leave_type_id').append(option);
                                    });
                                    halfDayLeave();
                                }*/
                                
                                if (response.leavetypes) {
                                    $('#leave_type_id').html('');
                                    $.each(response.leavetypes, function(index, leave) {
                                        if (leave.title == "Paternity Leaves" && response.employee.gender == 'Female') return true;
                                        if (leave.title == "Maternity Leaves" && response.employee.gender == 'Male') return true;
                                        var optionText = leave.title;
                                        if (leave.title === "Paid Leave") {
                                            optionText += ' (' + leave.days + ')';
                                        } else {
                                            optionText += ' (' + leave.days + ')';
                                        }
                                
                                        // Check if the leave type is "Birthday Leave" or leave ID 8
                                        var isBirthdayLeave = (leave.title === "Birthday Leave" || leave.id === 8);
                                        var isSameMonthAsDOB = false;
                                
                                        // Logic to check if the user's DOB month matches the current month
                                        if (isBirthdayLeave) {
                                            var dob = new Date(response.employee.dob); // Parse DOB from response
                                            var dobMonth = dob.getMonth() + 1; // JavaScript months are 0-based, so add 1
                                            var currentMonth = new Date().getMonth() + 1;
                                            isSameMonthAsDOB = (dobMonth === currentMonth);
                                        }
            
                                        // Add the 'disabled' attribute based on the conditions
                                        var option = $('<option>', {
                                            value: leave.id,
                                            text: optionText,
                                            'data-title': leave.title,
                                            disabled: leave.days === 0 || (isBirthdayLeave && !isSameMonthAsDOB)
                                        });
                                        $('#leave_type_id').append(option);
                                    });
                                    halfDayLeave();
                                }

                            },
                            error: function() {
                                console.log('Error fetching paid leave balance');
                            }
                        });
                    }
                });

            $(document).ready(function() {
                if ($.fn.DataTable.isDataTable('#datatable')) {
                    $('#datatable').DataTable().destroy();
                }
                var table = $('#datatable').DataTable({
                    "columnDefs": [
                        {
                            "targets": [3],
                            "type": "date" 
                        }
                    ],
                    "order": [[3, 'desc']], // Default sorting order
                    "rowCallback": function(row, data, index) {
                        // Update row number starting from 1
                        var info = table.page.info();
                        var page = info.page;
                        var length = info.length;
                        var rowNumber = page * length + (index + 1);
                        $('td:eq(0)', row).html(rowNumber);
                    }
                });
            
                table.order([3, 'desc']).draw();
            });
            
            document.addEventListener('DOMContentLoaded', function() {
                    const dateCells = document.querySelectorAll('.accordion-content-empName');  // Selecting the date cells.
            
                    dateCells.forEach(cell => {
                        cell.addEventListener('click', function() {
                            let currentRow = this.closest('tr');  
                            
                            let nextRow = currentRow.nextElementSibling;
                            while (nextRow && nextRow.classList.contains('accordion-content')) {
                                // Toggle the display for each .accordion-content row
                                if (nextRow.style.display === 'none' || nextRow.style.display === '') {
                                    nextRow.style.display = 'table-row';
                                } else {
                                    nextRow.style.display = 'none';
                                }
                                
                                // Move to the next row
                                nextRow = nextRow.nextElementSibling;
                            }
                        });
                    });
                });
        </script>
    @endpush
