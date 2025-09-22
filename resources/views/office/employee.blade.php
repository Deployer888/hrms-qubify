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
use Illuminate\Support\Facades\DB;
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

                            <p><a href="{{ (!empty($document->id)?asset('/document').'/'.$document->document_value:'') }}" target="_blank"><i class="fas fa-eye"></i></a></p>

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
    <div class="tab-content" id="location">[]
        <div class="section-title">Current Location</div>
        <div class="info-card">
            <p class="mb-3"><i class="fas fa-map-marker-alt text-danger mr-2"></i> <strong>Current Location:</strong> <span id="current-address">{{$latestLocation->location_name ?? ''}}</span></p>
            <p class="mb-3"><i class="fas fa-clock text-primary mr-2"></i> <strong>Last Updated:</strong> <span id="location-timestamp">{{ $latestLocation->time->format('d-m-Y h:i A') }}</span></p>
            <div class="location-map" id="employee-location-map"></div>
        </div>
        
        <div class="section-title">Location History</div>
        <div class="attendance-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody id="location-history-table">
                    @forelse($locationHistory as $location)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($location->created_at)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($location->created_at)->format('h:i A') }}</td>
                            <td>{{ $location->location_name ?? 'Unknown Location' }}</td>
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


<!-- jQuery first -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<!-- Chart.js (v2 or v4, pick one) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<!-- OR latest -->
<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script> -->

<!-- Then your custom script -->
<script src="{{asset('/js/chatify/code.js')}}"></script>


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
function getMonthlyAttendanceData($employeeId) {
    if (!$employeeId) {
        return [
            'present' => array_fill(0, 12, 0),
            'absent' => array_fill(0, 12, 0),
            'late' => array_fill(0, 12, 0)
        ];
    }
    
    $currentYear = date('Y');
    $monthlyData = [
        'present' => array_fill(0, 12, 0),
        'absent' => array_fill(0, 12, 0),
        'late' => array_fill(0, 12, 0)
    ];
    
    try {
        // Get attendance data for the current year
        $attendanceData = DB::table('attendance_employees')
            ->select(
                DB::raw('MONTH(date) as month'),
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->where('employee_id', $employeeId)
            ->whereYear('date', $currentYear)
            ->whereIn('status', ['present', 'absent', 'late'])
            ->groupBy(DB::raw('MONTH(date)'), 'status')
            ->get();
        
        // Populate the monthly data array
        foreach ($attendanceData as $record) {
            $monthIndex = $record->month - 1; // Convert to 0-based index
            $status = $record->status;
            
            if (isset($monthlyData[$status]) && $monthIndex >= 0 && $monthIndex < 12) {
                $monthlyData[$status][$monthIndex] = (int)$record->count;
            }
        }
        
        return $monthlyData;
    } catch (\Exception $e) {
        // Return sample data if query fails
        return [
            'present' => [21, 19, 22, 20, 21, 20, 22, 21, 19, 21, 20, 16],
            'absent' => [0, 1, 0, 1, 0, 1, 0, 1, 2, 0, 1, 0],
            'late' => [1, 1, 0, 1, 1, 0, 0, 0, 1, 1, 0, 2]
        ];
    }
}


@endphp
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initEmployeeMap" async defer></script>

<script>
  function initEmployeeMap() {
    // Example employee coordinates (New Delhi)
    const employeeLocation = { 
        lat: {{ $latestLocation->latitude ?? 0 }}, 
        lng: {{ $latestLocation->longitude ?? 0 }} 
    };

    // Create map
    const map = new google.maps.Map(document.getElementById("employee-location-map"), {
      zoom: 14,
      center: employeeLocation,
    });

    // Add marker
    new google.maps.Marker({
      position: employeeLocation,
      map: map,
      title: "Employee Location",
    });
  }
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const tabItems = document.querySelectorAll(".tab-item");
    const tabContents = document.querySelectorAll(".tab-content");

    tabItems.forEach(item => {
        item.addEventListener("click", function () {
            const target = this.getAttribute("data-tab");

            // Remove active class from all tabs
            tabItems.forEach(i => i.classList.remove("active"));
            tabContents.forEach(c => c.classList.remove("active"));

            // Add active class to clicked tab and target content
            this.classList.add("active");
            document.getElementById(target).classList.add("active");
            
            // Initialize charts when attendance tab is activated
            if (target === 'attendance') {
                setTimeout(() => {
                    if (window.monthlyAttendanceData && window.weeklyCheckinData) {
                        initEmployeeAttendanceCharts(window.monthlyAttendanceData, window.weeklyCheckinData);
                    } else {
                        initEmployeeAttendanceCharts();
                    }
                }, 100);
            }
        });
    });
    
    // Initialize charts if attendance tab is already active on page load
    if (document.querySelector('.tab-item[data-tab="attendance"]').classList.contains('active')) {
        setTimeout(() => {
            if (window.monthlyAttendanceData && window.weeklyCheckinData) {
                initEmployeeAttendanceCharts(window.monthlyAttendanceData, window.weeklyCheckinData);
            } else {
                initEmployeeAttendanceCharts();
            }
        }, 100);
    }
});
</script>


<!-- Pass PHP data to JS safely -->
<script>
    // Assign server data to window variables
    window.monthlyAttendanceData = @json($monthlyAttendanceData);
    window.weeklyCheckinData = @json($weeklyCheckinData);

    /**
     * Initialize Employee Attendance Charts
     * @param {Object} monthlyData - { present: [], absent: [], late: [] }
     * @param {Object|Array} weeklyData - Object with {data: [], labels: []} or legacy array format
     */
    function initEmployeeAttendanceCharts(
        monthlyData = { present: Array(12).fill(0), absent: Array(12).fill(0), late: Array(12).fill(0) },
        weeklyData = { data: Array(8).fill(9), labels: Array(8).fill('Week') }
    ) {
        console.log('Initializing Employee Attendance Charts...');
        console.log('Monthly Data:', monthlyData);
        console.log('Weekly Data:', weeklyData);
        console.log('Weekly Data Type:', typeof weeklyData);
        
        // Validate data structure
        if (!monthlyData || typeof monthlyData !== 'object') {
            console.warn('Invalid monthly data, using defaults');
            monthlyData = { present: Array(12).fill(0), absent: Array(12).fill(0), late: Array(12).fill(0) };
        }
        
        // Handle both legacy array format and new object format
        let weeklyDataValues, weeklyLabels;
        if (Array.isArray(weeklyData)) {
            // Legacy format - convert to new format
            console.warn('Using legacy weekly data format, generating default labels');
            weeklyDataValues = weeklyData;
            weeklyLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7', 'Week 8'];
        } else if (weeklyData && typeof weeklyData === 'object' && weeklyData.data && weeklyData.labels) {
            // New format with data and labels
            weeklyDataValues = weeklyData.data;
            weeklyLabels = weeklyData.labels;
        } else {
            console.warn('Invalid weekly data, using defaults');
            weeklyDataValues = Array(8).fill(9);
            weeklyLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7', 'Week 8'];
        }
        
        console.log('Processed Weekly Data Values:', weeklyDataValues);
        console.log('Processed Weekly Labels:', weeklyLabels);

        if (typeof Chart === 'undefined') {
            console.warn('Chart.js not loaded');
            document.querySelectorAll('.chart-container').forEach(c => {
                c.innerHTML = '<div class="text-center p-4"><p class="text-muted">Chart.js library not loaded. Please refresh the page.</p></div>';
            });
            return;
        }

        try {
            // --- Monthly Attendance Chart ---
            const monthlyChartEl = document.getElementById('monthly-attendance-chart');
            if (monthlyChartEl && !monthlyChartEl.chart) {
                console.log('Creating monthly attendance chart...');
                const ctx = monthlyChartEl.getContext('2d');
                monthlyChartEl.chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                        datasets: [
                            { label: 'Present', data: monthlyData.present, backgroundColor: '#28a745', barPercentage: 0.5, categoryPercentage: 0.8 },
                            { label: 'Absent',  data: monthlyData.absent, backgroundColor: '#dc3545', barPercentage: 0.5, categoryPercentage: 0.8 },
                            { label: 'Late',    data: monthlyData.late, backgroundColor: '#ffc107', barPercentage: 0.5, categoryPercentage: 0.8 }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: true, position: 'top', labels: { usePointStyle: true, padding: 20 } },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                callbacks: {
                                    label: function(tooltipItem) {
                                        const label = tooltipItem.dataset.label;
                                        const value = tooltipItem.raw;
                                        return `${label}: ${value} day${value !== 1 ? 's' : ''}`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: { stacked: true, grid: { display: false } },
                            y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } }
                        }
                    }
                });
                console.log('Monthly attendance chart created successfully');
            }

                // --- Weekly Check-in Time Chart ---

                function timeToDecimal(timeStr) {
                    // Convert "09:45 AM" → 9.75
                    const [time, modifier] = timeStr.split(' ');
                    let [hours, minutes] = time.split(':').map(Number);

                    if (modifier === 'PM' && hours !== 12) hours += 12;
                    if (modifier === 'AM' && hours === 12) hours = 0;

                    // ⏰ Round minutes to nearest 15
                    const roundedMinutes = Math.round(minutes / 15) * 15;
                    if (roundedMinutes === 60) {
                        hours += 1;
                        minutes = 0;
                    } else {
                        minutes = roundedMinutes;
                    }

                    return hours + (minutes / 60);
                }


                // Convert time data to numeric format if needed
                const numericWeeklyData = weeklyDataValues.map(t => {
                    if (typeof t === 'string') {
                        return timeToDecimal(t);
                    }
                    return t; // Already numeric
                });

                const checkinChartEl = document.getElementById('checkin-time-chart');
                if (checkinChartEl && !checkinChartEl.chart) {
                    const ctx = checkinChartEl.getContext('2d');
                    checkinChartEl.chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: weeklyLabels,
                            datasets: [{
                                label: 'Average Check-in Time',
                                data: numericWeeklyData,
                                borderColor: '#6259ca',
                                backgroundColor: 'rgba(98, 89, 202, 0.1)',
                                borderWidth: 2,
                                pointBackgroundColor: '#6259ca',
                                pointBorderColor: '#6259ca',
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: true, position: 'top', labels: { usePointStyle: true, padding: 20 } },
                                tooltip: {
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            const value = tooltipItem.raw;
                                            const hours = Math.floor(value);
                                            const minutes = Math.round((value - hours) * 60);
                                            return `Average Check-in: ${hours}:${minutes.toString().padStart(2,'0')}`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: { grid: { display: false } },
                                y: {
                                    min: 8,
                                    max: 14,
                                    ticks: {
                                        stepSize: 0.25,
                                        callback: function(value) {
                                            const hours = Math.floor(value);
                                            const minutes = Math.round((value - hours) * 60);
                                            return `${hours}:${minutes.toString().padStart(2,'0')}`;
                                        }
                                    },
                                    grid: { color: 'rgba(0,0,0,0.1)' }
                                }
                            }
                        }
                    });
                }

        } catch (error) {
            console.error('Error initializing charts:', error);
            document.querySelectorAll('.chart-container').forEach(c => {
                c.innerHTML = '<div class="text-center p-4"><p class="text-muted">Unable to load chart data. Please refresh the page.</p></div>';
            });
        }
    }

    // --- Initialize charts safely ---
    if (window.monthlyAttendanceData && window.monthlyAttendanceData.present) {
        initEmployeeAttendanceCharts(window.monthlyAttendanceData, window.weeklyCheckinData);
    } else {
        console.error('Monthly attendance data is missing:', window.monthlyAttendanceData);
        // Initialize with default data if server data is missing
        initEmployeeAttendanceCharts();
    }
</script>

