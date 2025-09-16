@extends('layouts.admin')
@section('page-title')
    {{__('Employee Details')}}
@endsection

@push('css-page')
<link rel="stylesheet" href="{{ asset('assets/css/office_employee.css') }}">
@endpush

@php
use App\Helpers\Helper;
use Carbon\Carbon;
@endphp

@section('content')
<div class="employee-profile">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-bg"></div>
        <div class="profile-content">
            @php $profile=asset(Storage::url('uploads/avatar/')); @endphp
            <img src="{{(!empty($employee->user->avatar) ? $profile.'/'.$employee->user->avatar : $profile.'/avatar.png')}}" alt="{{ $employee->name }}" class="profile-img">
            <div class="profile-info">
                <h1 class="text-light">{{ $employee->name }}</h1>
                <div class="designation">{{ isset($employee->designation) ? $employee->designation->name : '' }}</div>
                <div class="meta-info">
                    <span class="status-indicator status-{{ $employee->status == 'active' ? 'active' : 'inactive' }}"></span> 
                    {{ ucfirst($employee->status) }} Employee
                </div>
                <div class="profile-meta">
                    <div class="meta-item">
                        <i class="fas fa-envelope"></i> {{ $employee->email }}
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-phone"></i> {{ $employee->phone }}
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-id-card"></i> {{ isset($employee->user) ? $employee->user->employeeIdFormat($employee->employee_id) : $employee->employee_id }}
                    </div>
                </div>
            </div>
        </div>
        <div class="profile-actions">
            <a href="javascript:history.back()" class="back-button">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            {{-- @can('Edit Employee')
                <a href="#" class="btn btn-light btn-sm" data-url="{{ route('employee.edit', $employee->id) }}" data-ajax-popup="true" data-title="{{ __('Edit Employee') }}">
                    <i class="fas fa-pencil-alt"></i> Edit
                </a>
            @endcan --}}
        </div>
    </div>
    
    <!-- Tab Navigation -->
    <div class="tab-navigation">
        <div class="tab-item active" data-tab="overview">Overview</div>
        <div class="tab-item" data-tab="attendance">Attendance</div>
        <div class="tab-item" data-tab="documents">Documents</div>
        {{-- <div class="tab-item" data-tab="activity">Activity</div> --}}
        <div class="tab-item" data-tab="location">Location</div>
    </div>
    
    <!-- Tab Content -->
    <div class="tab-content active" id="overview">
        <div class="row">
            <div class="col-md-8">
                <div class="section-title">Personal Information</div>
                <div class="info-card">
                    <ul class="info-list">
                        <li>
                            <span class="info-label">Full Name</span>
                            <span class="info-value">{{ $employee->name }}</span>
                        </li>
                        <li>
                            <span class="info-label">Employee ID</span>
                            <span class="info-value">{{ isset($employee->user) ? $employee->user->employeeIdFormat($employee->employee_id) : $employee->employee_id }}</span>
                        </li>
                        <li>
                            <span class="info-label">Date of Birth</span>
                            <span class="info-value">{{ date('d M Y', strtotime($employee->dob)) }}</span>
                        </li>
                        <li>
                            <span class="info-label">Gender</span>
                            <span class="info-value">{{ ucfirst($employee->gender) }}</span>
                        </li>
                        <li>
                            <span class="info-label">Email</span>
                            <span class="info-value">{{ $employee->email }}</span>
                        </li>
                        <li>
                            <span class="info-label">Phone</span>
                            <span class="info-value">{{ $employee->phone }}</span>
                        </li>
                        <li>
                            <span class="info-label">Address</span>
                            <span class="info-value">{{ $employee->address }}</span>
                        </li>
                    </ul>
                </div>
                
                <div class="section-title">Employment Information</div>
                <div class="info-card">
                    <ul class="info-list">
                        <li>
                            <span class="info-label">Department</span>
                            <span class="info-value">{{ $department ? $department->name : '--' }}</span>
                        </li>
                        <li>
                            <span class="info-label">Designation</span>
                            <span class="info-value">{{ isset($employee->designation) ? $employee->designation->name : '--' }}</span>
                        </li>
                        <li>
                            <span class="info-label">Office</span>
                            <span class="info-value">{{ $office ? $office->name : '--' }}</span>
                        </li>
                        <li>
                            <span class="info-label">Date of Joining</span>
                            <span class="info-value">{{ \Carbon\Carbon::parse($employee->company_doj ?? $employee->joining_date)->format('d M Y') }}</span>
                        </li>
                        <li>
                            <span class="info-label">Team Leader / Department Head</span>
                            <span class="info-value">
                                @if(isset($employee->is_team_leader) && $employee->is_team_leader)
                                    Team Leader
                                @elseif(isset($employee->teamLeader))
                                    {{ $employee->teamLeader->name }}
                                @elseif(isset($departmentHead))
                                    {{ $departmentHead->name }}
                                @else
                                    --
                                @endif
                            </span>
                        </li>
                        <li>
                            <span class="info-label">Employment Status</span>
                            <span class="info-value">
                                @if(isset($employee->is_active))
                                    {{ ucfirst($employee->is_active ? "Active" : "In-Active") }}
                                @else
                                    {{ ucfirst($employee->status) }}
                                @endif
                            </span>
                        </li>
                        <li>
                            <span class="info-label">Work Shift</span>
                            <span class="info-value">
                                @if(isset($employee->shift_start))
                                    {{ date('h:i A', strtotime($employee->shift_start)) }} - 
                                    {{ isset($employee->shift_end) ? date('h:i A', strtotime($employee->shift_end)) : '6:00 PM' }}
                                @elseif(isset($employee->shift))
                                    {{ $employee->shift }}
                                @else 
                                    9:00 AM - 6:00 PM
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>
                
                <div class="section-title">Bank Information</div>
                <div class="info-card">
                    <ul class="info-list">
                        <li>
                            <span class="info-label">Account Holder</span>
                            <span class="info-value">{{ $employee->bank_holder_name ?: $employee->name }}</span>
                        </li>
                        <li>
                            <span class="info-label">Account Number</span>
                            <span class="info-value">{{ $employee->account_number ? '**** **** **** ' . substr($employee->account_number, -4) : '--' }}</span>
                        </li>
                        <li>
                            <span class="info-label">Bank Name</span>
                            <span class="info-value">{{ $employee->bank_name ?: '--' }}</span>
                        </li>
                        <li>
                            <span class="info-label">IFSC Code</span>
                            <span class="info-value">{{ $employee->bank_identifier_code ?: '--' }}</span>
                        </li>
                        <li>
                            <span class="info-label">Branch Location</span>
                            <span class="info-value">{{ $employee->branch_location ?: '--' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="section-title">Status</div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-value">{{ $attendanceRate }}%</div>
                            <div class="stat-label">Attendance Rate (This Month)</div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-value">{{ $avgCheckinTime }}</div>
                            <div class="stat-label">Average Check-in Time</div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <div class="stat-value">{{ $experience }}</div>
                            <div class="stat-label">Work Experience</div>
                        </div>
                    </div>
                </div>
                
                <div class="section-title">Leave Balance</div>
                <div class="info-card">
                    <ul class="info-list">
                        @foreach($leaveData as $leaveKey => $leaveInfo)
                            @php
                                $isMaternityLeave = str_contains(strtolower($leaveKey), 'maternity');
                                $isPaternityLeave = str_contains(strtolower($leaveKey), 'paternity');
                                $shouldDisplay = true;
                                
                                // Hide maternity leave for males
                                if ($employee->gender == 'Male' && $isMaternityLeave) {
                                    $shouldDisplay = false;
                                }
                                
                                // Hide paternity leave for females
                                if ($employee->gender == 'Female' && $isPaternityLeave) {
                                    $shouldDisplay = false;
                                }
                            @endphp
                            
                            @if($shouldDisplay)
                                <li>
                                    <span class="info-label">{{ ucwords(str_replace('_', ' ', $leaveKey)) }} Leave</span>
                                    <span class="info-value">{{ $leaveInfo['used'] }} / {{ $leaveInfo['total'] }} days</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="tab-content" id="attendance">
        <div class="section-title">Attendance Analytics</div>
        <div class="row">
            <div class="col-lg-6">
                <div class="attendance-chart">
                    <h4>Monthly Attendance</h4>
                    <div class="chart-container">
                        <canvas id="monthly-attendance-chart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="attendance-chart">
                    <h4>Check-in Time Analysis</h4>
                    <div class="chart-container">
                        <canvas id="checkin-time-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="section-title">Attendance Statistics</div>
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-value">{{ $attendanceRate }}%</div>
                    <div class="stat-label">Present Rate</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value">{{ $avgCheckinTime }}</div>
                    <div class="stat-label">Avg Check-in</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div class="stat-value">{{ $absentDays > 0 && isset($totalWorkingDays) ? round(($absentDays / $totalWorkingDays) * 100) : 0 }}%</div>
                    <div class="stat-label">Absent Rate</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stat-value">{{ $lateDays > 0 && isset($totalWorkingDays) ? round(($lateDays / $totalWorkingDays) * 100) : 0 }}%</div>
                    <div class="stat-label">Late Rate</div>
                </div>
            </div>
        </div>
        
        <div class="section-title">Recent Attendance Log</div>
        <div class="attendance-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Working Hours</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $previousDate = null;
                    @endphp
                    @forelse($recentAttendances as $attendance)
                        @php
                            $currentDate = \Carbon\Carbon::parse($attendance->date)->format('d M Y');
                            $showDivider = $previousDate && $currentDate !== $previousDate;
                        @endphp
                        
                        @if($showDivider)
                            <tr class="date-divider">
                                <td colspan="6" class="border-top border-secondary">
                                    <!-- You can leave this empty or add a light spacer row -->
                                </td>
                            </tr>
                        @endif
                        
                        <tr>
                            <td>{{ $currentDate }}</td>
                            <td>
                                <span class="badge badge-status badge-{{ $attendance->status == 'present' ? 'present' : ($attendance->status == 'absent' ? 'absent' : ($attendance->status == 'late' ? 'late' : 'leave')) }}">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </td>
                            <td>{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('h:i A') : '--' }}</td>
                            <td>{{ ($attendance->clock_out && $attendance->clock_out != '00:00:00') ? \Carbon\Carbon::parse($attendance->clock_out)->format('h:i A') : '--' }}</td>
                            <td>
                                @if($attendance->clock_in && $attendance->clock_out)
                                    {{ Helper::convertTimeToMinutesAndSeconds($attendance->total_rest == '00:00:00' ? $attendance->late : $attendance->total_rest) }}{{ $attendance->total_rest == '00:00:00' ? ' (Late)' : ' (Rest)' }}
                                @else
                                    --
                                @endif
                            </td>
                            <td>{{ $attendance->location ?? ($office ? $office->name : '--') }}</td>
                        </tr>
                        
                        @php
                            $previousDate = $currentDate;
                        @endphp
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No attendance records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="tab-content" id="documents">
        <div class="section-title">Employee Documents</div>
        <div class="document-list">
            @forelse($documents as $document)
                <div class="document-item">
                    <div class="document-icon">
                        <i class="fas fa-file-{{ getFileIconByType($document->document_value) }}"></i>
                    </div>
                    <div class="document-info">
                        <div class="document-name">{{ $document->name }}</div>
                        <div class="document-meta">
                            Uploaded on {{ \Carbon\Carbon::parse($document->created_at)->format('d M Y') }} • 
                            {{ strtoupper(pathinfo($document->document_value, PATHINFO_EXTENSION)) }} • 
                        </div>
                    </div>

                    <div class="document-actions">
                        @if(asset(Storage::url('uploads/document')).'/'.$document->document_value)

                            <p><a href="{{ (!empty($document->id)?asset(Storage::url('uploads/document')).'/'.$document->document_value:'') }}" target="_blank"><i class="fas fa-eye"></i></a></p>

                        @endif
                        @can('Delete Employee Document')
                            <a href="#" data-confirm="{{__('Are you sure?')|__('This action cannot be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-document-{{ $document->id }}').submit();" title="Delete">
                                <i class="fas fa-trash text-danger"></i>
                            </a>
                            <form id="delete-document-{{ $document->id }}" action="{{ route('employee.document.destroy', $document->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endcan
                    </div>
                </div>
            @empty
                <div class="text-center py-3">
                    <p>No documents found</p>
                </div>
            @endforelse
        </div>
        
        @can('Upload Document')
            <div class="mt-4">
                <a href="#" class="btn btn-primary" data-url="{{ route('employee.document.create', $employee->id) }}" data-ajax-popup="true" data-title="{{ __('Upload New Document') }}">
                    <i class="fas fa-upload"></i> Upload New Document
                </a>
            </div>
        @endcan
    </div>
    
    {{-- <div class="tab-content" id="activity">
        <div class="section-title">Recent Activity</div>
        <div class="activity-timeline">
            @forelse($activities as $activity)
                <div class="timeline-item">
                    <div class="timeline-date">{{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}</div>
                    <div class="timeline-content">
                        <div class="timeline-title">{{ $activity->title }}</div>
                        <div class="timeline-text">{{ $activity->description }}</div>
                    </div>
                </div>
            @empty
                <div class="text-center py-3">
                    <p>No recent activities found</p>
                </div>
            @endforelse
        </div>
    </div> --}}
    
    <!-- Location Tab Content -->
    <div class="tab-content" id="location">
        <div class="section-title">Current Location</div>
        <div class="info-card">
            <p class="mb-3"><i class="fas fa-map-marker-alt text-danger mr-2"></i> <strong>Current Location:</strong> <span id="current-address">Loading address...</span></p>
            <p class="mb-3"><i class="fas fa-clock text-primary mr-2"></i> <strong>Last Updated:</strong> <span id="location-timestamp">Today, {{ now()->format('h:i A') }}</span></p>
            <div class="location-map" id="employee-location-map"></div>
        </div>
        
        <div class="section-title">Location History</div>
        <div class="attendance-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Location</th>
                        <th>Activity</th>
                    </tr>
                </thead>
                <tbody id="location-history-table">
                    @forelse($locationHistory as $location)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($location->created_at)->format('d M Y, h:i A') }}</td>
                            <td>{{ $location->location_name ?? 'Unknown Location' }}</td>
                            <td>{{ $location->activity_type ?? 'Location Update' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No location history found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('script-page')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<!-- Google Maps JavaScript API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBUI4YwyEVg-TcI_R-sRdwuCuA22pY9VXg&callback=initMap" async defer></script>
<script src="{{ asset('assets/js/office_employee.js') }}"></script>
@endpush

@php
/**
 * Helper function to get file icon based on document type
 */
function getFileIconByType($type) {
    $iconMap = [
        'pdf' => 'pdf',
        'doc' => 'word',
        'docx' => 'word',
        'xls' => 'excel',
        'xlsx' => 'excel',
        'jpg' => 'image',
        'jpeg' => 'image',
        'png' => 'image',
        'txt' => 'alt',
        'zip' => 'archive',
        'rar' => 'archive'
    ];
    
    $extension = strtolower(pathinfo($type, PATHINFO_EXTENSION));
    return $iconMap[$extension] ?? 'document';
}

/**
 * Helper function to format file size
 */
function formatFileSize($size) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    while ($size >= 1024 && $i < count($units) - 1) {
        $size /= 1024;
        $i++;
    }
    return round($size, 1) . ' ' . $units[$i];
}

/**
 * Helper function to get monthly attendance data for the chart
 */
function getMonthlyAttendanceData($employeeId, $status) {
    // This would be replaced with actual database query
    // For now, return sample data
    $sampleData = [
        'present' => [21, 19, 22, 20, 21, 20, 22, 21, 19, 21, 20, 16],
        'absent' => [0, 1, 0, 1, 0, 1, 0, 1, 2, 0, 1, 0],
        'late' => [1, 1, 0, 1, 1, 0, 0, 0, 1, 1, 0, 2]
    ];
    
    return $sampleData[$status] ?? array_fill(0, 12, 0);
}

/**
 * Helper function to get weekly check-in time data for the chart
 */
function getWeeklyCheckinData($employeeId) {
    // This would be replaced with actual database query
    // Sample data - checkin times in decimal format (e.g., 9.75 = 9:45 AM)
    return [9.92, 9.75, 9.83, 10.25, 9.67, 9.75, 9.83, 9.75];
}
@endphp