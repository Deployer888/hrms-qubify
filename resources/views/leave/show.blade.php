@extends('layouts.admin')

@section('page-title')
    {{ __('Leave Details') }}
@endsection

@push('css-page')
<style>
    .leave-details-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    .leave-header {
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

    .leave-content {
        padding: 40px;
    }

    .detail-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 25px;
    }

    .detail-row.single {
        grid-template-columns: 1fr;
    }

    .detail-item {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        border-left: 4px solid #4f84ff;
    }

    .detail-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .detail-value {
        font-size: 16px;
        color: #1f2937;
        font-weight: 500;
    }

    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        display: inline-block;
    }

    .status-pending {
        background: #fef3c7;
        color: #d97706;
    }

    .status-approved {
        background: #dcfce7;
        color: #059669;
    }

    .status-rejected {
        background: #fecaca;
        color: #dc2626;
    }

    .leave-type-badge {
        background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 15px;
        font-size: 14px;
        font-weight: 600;
        display: inline-block;
    }

    .time-display {
        background: #e0f2fe;
        color: #0277bd;
        padding: 6px 12px;
        border-radius: 8px;
        font-family: 'Courier New', monospace;
        font-weight: 600;
        display: inline-block;
        margin-top: 5px;
    }

    /* Dynamic styling based on leave type */
    .leave-header.short-leave {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .leave-header.half-day {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .leave-header.full-day {
        background: linear-gradient(135deg, #4f84ff 0%, #3b5fe6 100%);
    }

    .detail-item.short-leave {
        border-left-color: #f59e0b;
    }

    .detail-item.half-day {
        border-left-color: #10b981;
    }

    .detail-item.full-day {
        border-left-color: #4f84ff;
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
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4f84ff 0%, #3b5fe6 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(79, 132, 255, 0.3);
        color: white;
        text-decoration: none;
    }

    .btn-secondary {
        background: #f3f4f6;
        color: #6b7280;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
        color: #6b7280;
        text-decoration: none;
    }

    @media (max-width: 768px) {
        .detail-row {
            grid-template-columns: 1fr;
        }
        
        .leave-details-container {
            margin: 10px;
            border-radius: 15px;
        }
        
        .leave-header, .leave-content {
            padding: 20px;
        }
    }
</style>
@endpush

@section('content')
@php
    $leaveTypeClass = $leave->isShortLeave() ? 'short-leave' : ($leave->isHalfDay() ? 'half-day' : 'full-day');
@endphp
<div class="leave-details-container">
    <div class="leave-header {{ $leaveTypeClass }}">
        <div class="header-icon">
            @if($leave->isShortLeave())
                <i class="fas fa-clock"></i>
            @elseif($leave->isHalfDay())
                <i class="fas fa-adjust"></i>
            @else
                <i class="fas fa-calendar-day"></i>
            @endif
        </div>
        <div class="header-content">
            <h1>{{ $leave->leave_type_display }} Application</h1>
            <p>
                @if($leave->isShortLeave())
                    Review short leave request with time details
                @elseif($leave->isHalfDay())
                    Review half day leave request
                @else
                    Review full day leave request
                @endif
            </p>
        </div>
    </div>

    <div class="leave-content">
        <!-- Employee and Leave Type -->
        <div class="detail-row">
            <div class="detail-item {{ $leaveTypeClass }}">
                <div class="detail-label">
                    <i class="fas fa-user"></i> Employee
                </div>
                <div class="detail-value">
                    {{ $leave->employees ? $leave->employees->name : 'N/A' }}
                </div>
            </div>
            <div class="detail-item {{ $leaveTypeClass }}">
                <div class="detail-label">
                    <i class="fas fa-tag"></i> Leave Type
                </div>
                <div class="detail-value">
                    <span class="leave-type-badge">
                        {{ $leave->leaveType ? $leave->leaveType->title : 'N/A' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Applied Date and Status -->
        <div class="detail-row">
            <div class="detail-item {{ $leaveTypeClass }}">
                <div class="detail-label">
                    <i class="fas fa-calendar-plus"></i> Applied On
                </div>
                <div class="detail-value">
                    {{ \Auth::user()->dateFormat($leave->applied_on) }}
                </div>
            </div>
            <div class="detail-item {{ $leaveTypeClass }}">
                <div class="detail-label">
                    <i class="fas fa-info-circle"></i> Status
                </div>
                <div class="detail-value">
                    @if ($leave->status == 'Pending')
                        <span class="status-badge status-pending">
                            <i class="fas fa-clock"></i> Pending
                        </span>
                    @elseif($leave->status == 'Approve')
                        <span class="status-badge status-approved">
                            <i class="fas fa-check"></i> Approved
                        </span>
                    @else
                        <span class="status-badge status-rejected">
                            <i class="fas fa-times"></i> Rejected
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Dynamic Date/Time Display Based on Leave Type -->
        @if($leave->isShortLeave())
            <!-- Short Leave: Show time details prominently -->
            <div class="detail-row">
                <div class="detail-item {{ $leaveTypeClass }}">
                    <div class="detail-label">
                        <i class="fas fa-calendar-day"></i> Leave Date
                    </div>
                    <div class="detail-value">
                        {{ $leave->contextual_date_display }}
                    </div>
                </div>
                <div class="detail-item {{ $leaveTypeClass }}">
                    <div class="detail-label">
                        <i class="fas fa-clock"></i> Time Range
                    </div>
                    <div class="detail-value">
                        @if($leave->hasTimeComponent())
                            <div class="time-display">
                                {{ $leave->formatted_time_range }}
                            </div>
                        @else
                            <span class="text-muted">Time not specified</span>
                        @endif
                    </div>
                </div>
            </div>
        @elseif($leave->isHalfDay())
            <!-- Half Day: Show single date with half day indicator -->
            <div class="detail-row">
                <div class="detail-item {{ $leaveTypeClass }}">
                    <div class="detail-label">
                        <i class="fas fa-calendar-day"></i> Leave Date
                    </div>
                    <div class="detail-value">
                        {{ $leave->contextual_date_display }}
                    </div>
                </div>
                <div class="detail-item {{ $leaveTypeClass }}">
                    <div class="detail-label">
                        <i class="fas fa-adjust"></i> Day Segment
                    </div>
                    <div class="detail-value">
                        @if($leave->day_segment)
                            <span class="time-display">{{ ucwords($leave->day_segment) }}</span>
                        @else
                            <span class="time-display">Half Day</span>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <!-- Full Day: Show date range -->
            <div class="detail-row">
                <div class="detail-item {{ $leaveTypeClass }}">
                    <div class="detail-label">
                        <i class="fas fa-play-circle"></i> Start Date
                    </div>
                    <div class="detail-value">
                        {{ \Auth::user()->dateFormat($leave->start_date) }}
                    </div>
                </div>
                <div class="detail-item {{ $leaveTypeClass }}">
                    <div class="detail-label">
                        <i class="fas fa-stop-circle"></i> End Date
                    </div>
                    <div class="detail-value">
                        {{ \Auth::user()->dateFormat($leave->end_date) }}
                    </div>
                </div>
            </div>
        @endif

        <!-- Total Days and Leave Duration -->
        <div class="detail-row">
            <div class="detail-item {{ $leaveTypeClass }}">
                <div class="detail-label">
                    <i class="fas fa-clock"></i> Total Days
                </div>
                <div class="detail-value">
                    <strong>{{ $leave->duration_description }}</strong>
                </div>
            </div>
            <div class="detail-item {{ $leaveTypeClass }}">
                <div class="detail-label">
                    <i class="fas fa-tag"></i> Leave Category
                </div>
                <div class="detail-value">
                    <span class="leave-type-badge">{{ $leave->leave_type_display }}</span>
                    @if($leave->day_segment && $leave->isHalfDay())
                        <br><small style="color: #6b7280; margin-top: 5px; display: block;">({{ ucwords($leave->day_segment) }})</small>
                    @endif
                </div>
            </div>
        </div>

        <!-- Leave Reason -->
        <div class="detail-row single">
            <div class="detail-item {{ $leaveTypeClass }}">
                <div class="detail-label">
                    <i class="fas fa-comment"></i> Leave Reason
                </div>
                <div class="detail-value">
                    {{ $leave->leave_reason }}
                </div>
            </div>
        </div>

        <!-- Additional Remarks (if any) -->
        @if($leave->remark)
        <div class="detail-row single">
            <div class="detail-item {{ $leaveTypeClass }}">
                <div class="detail-label">
                    <i class="fas fa-sticky-note"></i> Additional Remarks
                </div>
                <div class="detail-value">
                    {{ $leave->remark }}
                </div>
            </div>
        </div>
        @endif

        <!-- Rejection Reason (if rejected) -->
        @if($leave->status == 'Reject' && $leave->reject_reason)
        <div class="detail-row single">
            <div class="detail-item" style="border-left-color: #dc2626;">
                <div class="detail-label">
                    <i class="fas fa-exclamation-triangle"></i> Rejection Reason
                </div>
                <div class="detail-value" style="color: #dc2626;">
                    {{ $leave->reject_reason }}
                </div>
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="btn-group">
            <a href="{{ route('leave.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Leave List
            </a>
            
            @if(\Auth::user()->can('Edit Leave') && $leave->status == 'Pending')
                <a href="{{ route('leave.edit', $leave->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i>
                    Edit Leave
                </a>
            @endif
        </div>
    </div>
</div>
@endsection