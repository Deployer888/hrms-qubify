<style>
    :root {
        --primary: #2563eb;
        --primary-dark: #1d4ed8;
        --secondary: #3b82f6;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #60a5fa;
        --dark: #1f2937;
        --light: #f8fafc;
        --border: #e5e7eb;
        --text-primary: #111827;
        --text-secondary: #6b7280;
        --text-muted: #9ca3af;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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

    /* Dynamic header colors based on leave type */
    /* .header.short-leave {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .header.half-day {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .header.full-day {
        background: linear-gradient(135deg, #4f84ff 0%, #3b5fe6 100%);
    } */

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

    .details-table {
        width: 100%;
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        margin-bottom: 30px;
        border: none;
    }

    .details-table tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background-color 0.3s ease;
    }

    .details-table tr:hover {
        background-color: #f8fafc;
    }

    .details-table tr:last-child {
        border-bottom: none;
    }

    .details-table th {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        color: #374151;
        font-weight: 600;
        padding: 16px 20px;
        text-align: left;
        font-size: 14px;
        width: 180px;
        vertical-align: top;
        border: none;
    }

    .details-table td {
        padding: 16px 20px;
        color: #6b7280;
        font-size: 14px;
        line-height: 1.6;
        border: none;
        vertical-align: top;
    }

    .breakword {
        white-space: normal !important;
        max-width: 100% !important;
        overflow-wrap: anywhere !important;
        word-break: break-word;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge.pending {
        background: #fef3c7;
        color: #d97706;
    }

    .status-badge.approval {
        background: #d1fae5;
        color: #059669;
    }

    .status-badge.reject {
        background: #fee2e2;
        color: #dc2626;
    }

    .form-group {
        margin-bottom: 25px;
    }

    label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        font-size: 14px;
    }

    textarea {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: #fafafa;
        resize: vertical;
        min-height: 100px;
        font-family: inherit;
    }

    textarea:focus {
        outline: none;
        border-color: #4f84ff;
        background: white;
        box-shadow: 0 0 0 3px rgba(79, 132, 255, 0.1);
    }

    .btn-group {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px solid #f3f4f6;
    }

    .btn {
        padding: 14px 28px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1;
        justify-content: center;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
    }

    .rejection-section {
        background: #fef2f2;
        border: 2px solid #fecaca;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .rejection-section.hidden {
        display: none;
    }

    .rejection-section label {
        color: #dc2626;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .rejection-section textarea {
        border-color: #f87171;
        background: white;
    }

    .rejection-section textarea:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }

    .info-section {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 1px solid #bae6fd;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 30px;
    }

    /* Dynamic info section colors */
    .short-leave .info-section {
        background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        border: 1px solid #fde68a;
    }

    .short-leave .info-section h3,
    .short-leave .info-section p {
        color: #d97706 !important;
    }

    .half-day .info-section {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border: 1px solid #bbf7d0;
    }

    .half-day .info-section h3,
    .half-day .info-section p {
        color: #059669 !important;
    }

    .info-section h3 {
        color: #0369a1;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .leave-dates {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .date-chip {
        background: #dbeafe;
        color: #1e40af;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 500;
    }

    /* Dynamic date chip colors based on leave type */
    .short-leave .date-chip {
        background: #fef3c7;
        color: #d97706;
    }

    .half-day .date-chip {
        background: #dcfce7;
        color: #059669;
    }

    .full-day .date-chip {
        background: #dbeafe;
        color: #1e40af;
    }

    @media (max-width: 768px) {
        .container {
            margin: 10px;
            border-radius: 15px;
        }
        
        .header, .form-container {
            padding: 20px;
        }

        .btn-group {
            flex-direction: column;
        }

        .details-table th {
            width: auto;
            padding: 12px 15px;
        }

        .details-table td {
            padding: 12px 15px;
        }
    }

    .fade-in {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

@php
    $leaveTypeClass = $leave->isShortLeave() ? 'short-leave' : ($leave->isHalfDay() ? 'half-day' : 'full-day');
@endphp

<div class="container {{ $leaveTypeClass }}">
    <div class="header {{ $leaveTypeClass }}">
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
            <h1>{{ $leave->leave_type_display }} Application Review</h1>
            <p>
                @if($leave->isShortLeave())
                    Review and take action on short leave request
                @elseif($leave->isHalfDay())
                    Review and take action on half day request
                @else
                    Review and take action on leave request
                @endif
            </p>
        </div>
    </div>

    <div class="form-container">
        <form action="{{ route('leave.changeaction') }}" method="post" id="leaveForm">
            @csrf
            
            <!-- Leave Details Section -->
            <div class="info-section">
                <h3>
                    @if($leave->isShortLeave())
                        <i class="fas fa-clock"></i>
                        Short Leave Request Details
                    @elseif($leave->isHalfDay())
                        <i class="fas fa-adjust"></i>
                        Half Day Request Details
                    @else
                        <i class="fas fa-info-circle"></i>
                        Leave Request Details
                    @endif
                </h3>
                <p style="color: #0369a1; margin: 0; font-size: 14px;">
                    @if($leave->isShortLeave())
                        Review this short leave request with time details before making a decision.
                    @elseif($leave->isHalfDay())
                        Review this half day leave request before making a decision.
                    @else
                        Review the following information carefully before making a decision.
                    @endif
                </p>
            </div>

            <table class="details-table">
                <tr>
                    <th>
                        <i class="fas fa-user" style="margin-right: 8px; color: #6b7280;"></i>
                        Employee
                    </th>
                    <td><strong>{{ $leave->employees ? $leave->employees->name : 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <th>
                        <i class="fas fa-calendar-check" style="margin-right: 8px; color: #6b7280;"></i>
                        Leave Type
                    </th>
                    <td>{{ $leave->leaveType ? $leave->leaveType->title : 'N/A' }}</td>
                </tr>
                <tr>
                    <th>
                        <i class="fas fa-clock" style="margin-right: 8px; color: #6b7280;"></i>
                        Applied On
                    </th>
                    <td>{{ \Auth::user()->dateFormat($leave->applied_on) }}</td>
                </tr>
                
                @if($leave->isShortLeave())
                    <!-- Short Leave: Show single date with time range -->
                    <tr>
                        <th>
                            <i class="fas fa-calendar-day" style="margin-right: 8px; color: #6b7280;"></i>
                            Start Date
                        </th>
                        <td>
                            <div class="leave-dates">
                                <span class="date-chip">{{ \Auth::user()->dateFormat($leave->start_date) }}</span>
                                @if($leave->start_time)
                                    <span style="color: #6b7280; font-size: 12px;">{{ $leave->formatted_start_time }}</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <i class="fas fa-calendar-day" style="margin-right: 8px; color: #6b7280;"></i>
                            End Date
                        </th>
                        <td>
                            <div class="leave-dates">
                                <span class="date-chip">{{ \Auth::user()->dateFormat($leave->end_date) }}</span>
                                @if($leave->end_time)
                                    <span style="color: #6b7280; font-size: 12px;">{{ $leave->formatted_end_time }}</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @elseif($leave->isHalfDay())
                    <!-- Half Day: Show single date with day segment -->
                    <tr>
                        <th>
                            <i class="fas fa-calendar-day" style="margin-right: 8px; color: #6b7280;"></i>
                            Leave Date
                        </th>
                        <td>
                            <div class="leave-dates">
                                <span class="date-chip">{{ \Auth::user()->dateFormat($leave->start_date) }}</span>
                                @if($leave->day_segment)
                                    <span style="color: #6b7280; font-size: 12px;">({{ ucwords($leave->day_segment) }})</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @else
                    <!-- Full Day: Show date range -->
                    <tr>
                        <th>
                            <i class="fas fa-play-circle" style="margin-right: 8px; color: #6b7280;"></i>
                            Start Date
                        </th>
                        <td>
                            <div class="leave-dates">
                                <span class="date-chip">{{ \Auth::user()->dateFormat($leave->start_date) }}</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <i class="fas fa-stop-circle" style="margin-right: 8px; color: #6b7280;"></i>
                            End Date
                        </th>
                        <td>
                            <div class="leave-dates">
                                <span class="date-chip">{{ \Auth::user()->dateFormat($leave->end_date) }}</span>
                            </div>
                        </td>
                    </tr>
                @endif

                <!-- Total Days -->
                <tr>
                    <th>
                        <i class="fas fa-clock" style="margin-right: 8px; color: #6b7280;"></i>
                        Total Days
                    </th>
                    <td><strong>{{ $leave->duration_description }}</strong></td>
                </tr>

                <tr>
                    <th>
                        <i class="fas fa-comment" style="margin-right: 8px; color: #6b7280;"></i>
                        Leave Reason
                    </th>
                    <td class="breakword">{{ $leave->leave_reason }}</td>
                </tr>
                <tr>
                    <th>
                        <i class="fas fa-info-circle" style="margin-right: 8px; color: #6b7280;"></i>
                        Current Status
                    </th>
                    <td>
                        @if ($leave->status == 'Pending')
                            <span class="status-badge pending">Pending</span>
                        @elseif($leave->status == 'Approve')
                            <span class="status-badge approval">Approved</span>
                        @else
                            <span class="status-badge reject">Rejected</span>
                        @endif
                    </td>
                </tr>
            </table>

            <!-- Rejection Reason Section -->
            <div class="rejection-section hidden" id="rejectReasonDiv">
                <div class="form-group">
                    <label for="reject_reason">
                        <i class="fas fa-exclamation-triangle"></i>
                        Reason for Rejection
                    </label>
                    <textarea 
                        name="reject_reason" 
                        id="reject_reason" 
                        placeholder="Please provide a clear reason for rejecting this leave application..."
                    ></textarea>
                </div>
            </div>

            <!-- Hidden Inputs -->
            <input type="hidden" value="{{ $leave->id }}" name="leave_id">
            <input type="hidden" name="status" id="statusInput">

            <!-- Action Buttons -->
            <div class="btn-group">
                <button type="button" class="btn btn-success" id="approvalBtn">
                    <i class="fas fa-check"></i>
                    Approve Leave
                </button>
                <button type="button" class="btn btn-danger" id="rejectBtn">
                    <i class="fas fa-times"></i>
                    Reject Leave
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const approvalBtn = document.getElementById('approvalBtn');
        const rejectBtn = document.getElementById('rejectBtn');
        const rejectReasonDiv = document.getElementById('rejectReasonDiv');
        const rejectReasonTextarea = document.getElementById('reject_reason');

        // Approval button click handler
        approvalBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to approve this leave application?')) {
                // Hide rejection section if visible
                rejectReasonDiv.classList.add('hidden');
                rejectReasonTextarea.value = '';
                
                // Submit the form with approval action
                submitAction('approval');
            }
        });

        // Reject button click handler
        rejectBtn.addEventListener('click', function() {
            // Show rejection reason section
            rejectReasonDiv.classList.remove('hidden');
            rejectReasonDiv.classList.add('fade-in');
            
            // Focus on textarea
            setTimeout(() => {
                rejectReasonTextarea.focus();
            }, 100);
            
            // Add submit handler for rejection
            setupRejectionSubmit();
        });

        function setupRejectionSubmit() {
            // Update the reject button text to indicate it will submit
            rejectBtn.innerHTML = '<i class="fas fa-times"></i> Submit Rejection';
            
            // Remove the old event listener and add new one
            const newRejectBtn = rejectBtn.cloneNode(true);
            rejectBtn.parentNode.replaceChild(newRejectBtn, rejectBtn);
            
            newRejectBtn.addEventListener('click', function() {
                const rejectReason = rejectReasonTextarea.value.trim();
                
                if (!rejectReason) {
                    alert('Please provide a reason for rejection.');
                    rejectReasonTextarea.focus();
                    return;
                }

                if (rejectReason.length < 10) {
                    alert('Rejection reason must be at least 10 characters long.');
                    rejectReasonTextarea.focus();
                    return;
                }
                
                // Debug: Log the rejection reason
                console.log('Rejection reason:', rejectReason);
                
                if (confirm('Are you sure you want to reject this leave application?')) {
                    // Submit the form with reject action
                    submitAction('reject', rejectReason);
                }
            });
        }

        function updateStatusDisplay(status) {
            const statusElement = document.querySelector('.status-badge');
            statusElement.classList.remove('pending', 'approval', 'reject');
            statusElement.classList.add(status);
            
            switch(status) {
                case 'approval':
                    statusElement.textContent = 'Approved';
                    break;
                case 'reject':
                    statusElement.textContent = 'Rejected';
                    break;
                default:
                    statusElement.textContent = 'Pending';
            }
        }

        function submitAction(action, reason = '') {
            const form = document.getElementById('leaveForm');
            const statusInput = document.getElementById('statusInput');
            
            // Prevent multiple submissions
            if (form.dataset.submitting === 'true') {
                return;
            }
            form.dataset.submitting = 'true';
            
            // Set the status value
            statusInput.value = action === 'approval' ? 'Approval' : 'Reject';
            
            // For rejection, ensure the textarea has the reason
            if (action === 'reject' && reason) {
                rejectReasonTextarea.value = reason;
                console.log('Setting textarea value to:', reason);
                console.log('Textarea value after setting:', rejectReasonTextarea.value);
            }

            // Debug: Log form data before submission
            const formData = new FormData(form);
            console.log('Form data being submitted:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ':', value);
            }

            // Show loading state
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(btn => {
                btn.disabled = true;
                btn.style.opacity = '0.6';
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            });

            // Submit the form
            console.log('Submitting form with action:', action);
            console.log('Form action URL:', form.action);
            console.log('Form method:', form.method);
            form.submit();
        }

        // Add validation for textarea
        rejectReasonTextarea.addEventListener('input', function() {
            const length = this.value.length;
            const minLength = 10;
            
            if (length > 0 && length < minLength) {
                this.style.borderColor = '#f87171';
            } else if (length >= minLength) {
                this.style.borderColor = '#10b981';
            } else {
                this.style.borderColor = '#f87171';
            }
        });
    });
</script>