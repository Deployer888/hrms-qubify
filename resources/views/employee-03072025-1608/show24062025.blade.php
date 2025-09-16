@extends('layouts.admin')

@section('page-title')
    {{__('Employee')}}
@endsection
@if($employee->team_leader_id != $employee->currentUEmpID || \Auth::user()->type == 'hr' || \Auth::user()->type == 'company')
@section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        @can('Edit Employee')
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="{{route('employee.edit',\Illuminate\Support\Facades\Crypt::encrypt($employee->id))}}" class="btn btn-xs btn-white btn-icon-only width-auto">
                    <i class="fa fa-edit"></i> {{ __('Edit') }}
                </a>
            </div>
        @endcan
    </div>
@endsection
@endif
@section('content')
    <div class="row">
        <div class="col-md-6 ">
            <div class="employee-detail-wrap">
                <div class="card" style="height: 410px;">
                    <div class="card-header">
                        <h6 class="mb-0">{{__('Personal Detail')}}</h6>
                    </div>
                    <div class="card-body employee-detail-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info text-sm">
                                    <strong>{{__('EmployeeId')}}</strong>
                                    <p>{{$employeesId}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info text-sm font-style">
                                    <strong>{{__('Name')}}</strong>
                                    <p>{{$employee->name}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info text-sm font-style">
                                    <strong>{{__('Official Email')}}</strong>
                                    <p>{{$employee->email}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info text-sm font-style">
                                    <strong>{{__('Personal Email')}}</strong>
                                    <p>{{$employee->user->personal_email ?? ''}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info text-sm">
                                    <strong>{{__('Date of Birth')}}</strong>
                                    <p>{{$employee->dob ? \Auth::user()->dateFormat($employee->dob) : ''}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info text-sm">
                                    <strong>{{__('Phone')}}</strong>
                                    <p>{{$employee->phone}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info text-sm">
                                    <strong>{{__('Address')}}</strong>
                                    <p>{{$employee->address}}</p>
                                </div>
                            </div>
                            @if($employee->team_leader_id != $employee->currentUEmpID || \Auth::user()->type == 'hr' || \Auth::user()->type == 'company')
                            <!--<div class="col-md-6">
                                <div class="info text-sm">
                                    <strong>{{-- __('Salary Type') --}}</strong>
                                    <p>{{-- !empty($employee->salaryType)?$employee->salaryType->name:'' --}}</p>
                                </div>
                            </div>-->
                            <div class="col-md-6">
                                <div class="info text-sm">
                                    <strong>{{__('Basic Salary')}}</strong>
                                    <div id="salary-display" style="display: flex; align-items: center;">
                                        <span id="hidden-salary" class="salary-text">*****</span>
                                        <span id="actual-salary" class="salary-text" style="display: none;">₹ {{$employee->salary}}</span>
                                        <button type="button" id="toggle-salary" class="btn-eye" onclick="toggleSalary()">
                                            <i id="eye-icon" class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 ">
            <div class="employee-detail-wrap">
                <div class="card" style="height: 410px;">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-0">{{__('Company Detail')}}</h6>
                            @if($employee->is_probation == 1)
                            <h6 class="mb-0">On-Probation</h6>
                            @endif
                        </div>
                    </div>
                    <div class="card-body employee-detail-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info text-sm">
                                    <strong>{{__('Branch')}}</strong>
                                    <p>{{!empty($employee->branch)?$employee->branch->name:''}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info text-sm font-style">
                                    <strong>{{__('Department')}}</strong>
                                    <p>{{!empty($employee->department)?$employee->department->name:''}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info text-sm font-style">
                                    <strong>{{__('Designation')}}</strong>
                                    <p>{{!empty($employee->designation)?$employee->designation->name:''}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info text-sm">
                                    <strong>{{__('Date Of Joining')}}</strong>
                                    <p>{{ !empty($employee->company_doj) ? \Auth::user()->dateFormat($employee->company_doj) : ''}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info text-sm font-style">
                                    <strong>{{__('Shift Start')}}</strong>
                                    <p>{{!empty($employee->shift_start)?$employee->shift_start:''}}</p>
                                </div>
                            </div>
                            @if($employee->is_team_leader == 0)
                            <div class="col-md-6">
                                <div class="info text-sm font-style">
                                    <strong>{{__('Team Leader')}}</strong>
                                    <p>{{ !empty($teamLeaderDetails) ? $teamLeaderDetails->name : '' }}</p>
                                </div>
                            </div>
                            @endif
                            @if($employee->is_active == 0)
                            <div class="col-md-6">
                                <div class="info text-sm">
                                    <strong>{{__('Date Of Exit')}}</strong>
                                    <p>{{\Auth::user()->dateFormat($employee->date_of_exit)}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info text-sm">
                                    <strong>{{__(!empty($employee->termination) ? 'Termination Reason' : 'Resignation Reason')}}</strong>
                                    <p>
                                        @php
                                            $description = !empty($employee->termination)
                                                ? $employee->termination->description??''
                                                : $employee->resignation->description??'';
                                        @endphp

                                        {{ Str::limit($description, 80) }}

                                        @if (strlen($description) > 80)
                                            <a href="javascript:void(0)" class="read-more-btn text-info text-underline" data-description="{{ $description }}"><b>Read More</b></a>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php use Carbon\Carbon; ?>
    @if($employee->team_leader_id != $employee->currentUEmpID || \Auth::user()->type == 'hr' || \Auth::user()->type == 'company')
    <div class="row">
        <div class="col-md-6 ">
            <div class="employee-detail-wrap">
                <div class="card"  style="height: 330px;">
                    <div class="card-header">
                        <h6 class="mb-0">{{__('Annual Leave Details')}}</h6>
                    </div>
                    <div class="card-body employee-detail-body">
                        <div class="row mb-1">
                            @php
                                $formattedDate = Carbon::today()->format('Y-m-d');
                                $employeedoc = $employee->documents()->pluck('document_value','document_id');
                                $companyDoj = Carbon::parse($employee->company_doj);
                                $currentYear = Carbon::now();
                                $totalLeaves = 0;
                            @endphp
                            <div class="col-md-3 mb-1 text-center">
                                <u><strong>{{__('Leave Types')}}</strong></u>
                            </div>
                            <div class="col-md-3 mb-1 text-center">
                                <u><strong>{{__('Total Leaves')}}</strong></u>
                            </div>
                            <div class="col-md-3 mb-1 text-center">
                                <u><strong>{{__('Leaves Available')}}</strong></u>
                            </div>
                            <div class="col-md-3 mb-1 text-center">
                                <u><strong>{{__('Leaves Availed')}}</strong></u>
                            </div>
                            @foreach($leaves as $key=>$leave)
                                @if($employee->gender == "Male" && $leave->title == "Maternity Leaves") @continue; @endif
                                @if($employee->gender == "Female" && $leave->title == "Paternity Leaves") @continue; @endif
                                @if($employee->is_probation == 0 || $leave->title == 'Sick Leave')
                                <div class="col-md-3 mb-2 text-center">
                                    <i>{{$leave->title }}</i>
                                </div>
                                <div class="col-md-3 mb-2 text-center">
                                        @php $totalLeaves = $leave->days; @endphp
                                    <span>{{ $totalLeaves }}</span>
                                </div>
                                @if($companyDoj->diffInYears($currentYear) < 1 && $leave->id == 3)
                                    @php $totalLeaves = $employee->is_probation == 1 ? 0 : $employee->paid_leave_balance @endphp
                                @endif
                                @php
                                $leavesAvailed = \App\Helpers\Helper::totalLeaveAvailed($employee->id, $employee->company_doj, $formattedDate, $leave->id);
                                @endphp
                                <div class="col-md-3 mb-2 text-center">
                                    @if($leave->title != 'Paid Leave')
                                    <span>{{ $totalLeaves - $leavesAvailed }}</span>
                                    @else
                                    <span>{{ $employee->paid_leave_balance }}</span>
                                    @endif
                                </div>
                                <div class="col-md-3 mb-2 text-center">
                                    <span>{{ $leavesAvailed }}</span>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 ">
            <div class="employee-detail-wrap">
                <div class="card" style="height: 330px;">
                    <div class="card-header">
                        <h6 class="mb-0">{{__('Bank Account Detail')}}</h6>
                    </div>
                    <div class="card-body employee-detail-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info text-sm">
                                    <strong>{{__('Account Holder Name')}}</strong>
                                    <p>{{$employee->account_holder_name}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info text-sm font-style">
                                    <strong>{{__('Account Number')}}</strong>
                                    <p>{{$employee->account_number}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info text-sm font-style">
                                    <strong>{{__('Bank Name')}}</strong>
                                    <p>{{$employee->bank_name}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info text-sm">
                                    <strong>{{__('Bank IFSC Code')}}</strong>
                                    <p>{{$employee->bank_identifier_code}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info text-sm">
                                    <strong>{{__('Branch Location')}}</strong>
                                    <p>{{$employee->branch_location}}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info text-sm">
                                    <strong>{{__('PAN Number')}}</strong>
                                    <p>{{$employee->tax_payer_id}}</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 ">
            <div class="employee-detail-wrap">
                <div class="card" style="height: 330px;">
                    <div class="card-header">
                        <h6 class="mb-0">{{__('Document Detail')}}</h6>
                    </div>
                    <div class="card-body employee-detail-body">
                        <div class="row">
                            @php
                               $employeedoc = $employee->documents()->pluck('document_value','document_id');
                            @endphp
                            @foreach($documents as $key=>$document)
                                <div class="col-md-6">
                                    <div class="info text-sm">
                                        <strong>{{$document->name }}</strong>
                                        @if(!empty($employeedoc[$document->id]))
                                            @php
                                                $filename = $employeedoc[$document->id];
                                                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                                $supportedExtensions = ['jpeg', 'png', 'jpg', 'svg', 'pdf', 'doc'];
                                                $fileUrl = asset(Storage::url('uploads/document')).'/'.$filename;
                                            @endphp
                                            
                                            <p>
                                                <a href="{{ $fileUrl }}" target="_blank">
                                                    @if(in_array($extension, $supportedExtensions))
                                                        @switch($extension)
                                                            @case('jpeg')
                                                            @case('jpg')
                                                            @case('png')
                                                            @case('svg')
                                                                {{-- Image Preview Thumbnail --}}
                                                                <div class="preview-container">
                                                                    <a href="{{ $fileUrl }}" target="_blank">
                                                                        <img src="{{ $fileUrl }}" 
                                                                             alt="Image Preview" 
                                                                             class="w-16 h-16 object-cover rounded border hover:shadow-lg transition-shadow cursor-pointer"
                                                                             title="Click to view full size: {{ $filename }}">
                                                                    </a>
                                                                </div>
                                                                @break
                                                            
                                                            @case('pdf')
                                                            <div class="preview-container">
                                                                <div class="w-16 h-16 border rounded overflow-hidden cursor-pointer" 
                                                                     onclick="window.open('https://docs.google.com/viewer?url={{ urlencode($fileUrl) }}', '_blank')" 
                                                                     title="Click to preview PDF">
                                                                    <iframe src="https://docs.google.com/viewer?url={{ urlencode($fileUrl) }}&embedded=true" 
                                                                            class="w-full h-full scale-50 origin-top-left transform pointer-events-none"
                                                                            style="width: 200%; height: 200%;"
                                                                            loading="lazy">
                                                                    </iframe>
                                                                </div>
                                                            </div>
                                                            @break
                                                            
                                                            @case('doc')
                                                            @case('docx')
                                                                {{-- Word Document Preview using Google Docs Viewer --}}
                                                                <div class="preview-container">
                                                                    <a href="{{ $fileUrl }}" target="_blank" class="text-xs text-blue-500 hover:underline">
                                                                        <div class="w-16 h-16 border rounded overflow-hidden bg-blue-50">
                                                                            <iframe src="https://docs.google.com/viewer?url={{ urlencode($fileUrl) }}&embedded=true" 
                                                                                    class="w-full h-full scale-50 origin-top-left transform"
                                                                                    style="width: 200%; height: 200%;"
                                                                                    title="Document Preview">
                                                                            </iframe>
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                                @break
                                                            
                                                            @default
                                                                {{-- Generic File --}}
                                                                <div class="preview-container">
                                                                    <a href="{{ $fileUrl }}" target="_blank" class="text-xs text-blue-500 hover:underline">
                                                                        <div class="w-16 h-16 border rounded bg-gray-50 flex items-center justify-center">
                                                                            <i class="fas fa-file text-gray-500 text-xl"></i>
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                        @endswitch
                                                    @else
                                                        {{-- Unsupported file type --}}
                                                        <div class="preview-container">
                                                            <div class="w-16 h-16 border rounded bg-gray-50 flex items-center justify-center">
                                                                <i class="fas fa-file text-gray-500 text-xl"></i>
                                                            </div>
                                                            <p class="text-xs mt-1 truncate w-16">{{ $document->name }}</p>
                                                            <a href="{{ $fileUrl }}" target="_blank" class="text-xs text-blue-500 hover:underline">
                                                                Download
                                                            </a>
                                                        </div>
                                                    @endif
                                                </a>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal to show complete description -->
    <div class="modal fade" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="descriptionModalLabel">Description</h5>
                </div>
                <div class="modal-body">
                    <p id="modal-description"></p>
                </div>
            </div>
        </div>
    </div>
@endsection

