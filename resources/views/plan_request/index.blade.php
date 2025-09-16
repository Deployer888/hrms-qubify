@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Plan Request') }}
@endsection

@section('content')
<div class="container-fluid">
    {{-- Premium Header --}}
    <div class="page-header-premium fade-in">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="header-text">
                    <h1>{{ __('Manage Plan Requests') }}</h1>
                    <p>{{ __('Review and approve company plan upgrade requests') }}</p>
                </div>
            </div>
            <div class="header-stats">
                <div class="stat-item">
                    <p class="stat-number">{{ $plan_requests->count() }}</p>
                    <p class="stat-label text-light">{{ __('Total Requests') }}</p>
                </div>
                <div class="stat-item">
                    <p class="stat-number">{{ $plan_requests->where('status', 'pending')->count() }}</p>
                    <p class="stat-label text-light">{{ __('Pending') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Premium Table Card --}}
    <div class="premium-table-container fade-in">
        <div class="table-header">
            <div>
                <h2 class="table-title">{{ __('Plan Requests') }}</h2>
                <p class="table-subtitle">{{ __('Review and manage all plan upgrade requests') }}</p>
            </div>
        </div>
        
        @if($plan_requests->count() > 0)
            <div class="table-responsive">
                <table class="premium-table table">
                    <thead>
                        <tr>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('Plan Details') }}</th>
                            <th>{{ __('Capacity') }}</th>
                            <th>{{ __('Duration') }}</th>
                            <th>{{ __('Request Date') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($plan_requests as $prequest)
                            <tr class="font-style">
                                <td data-label="{{ __('User') }}">
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($prequest->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name">{{ $prequest->user->name ?? 'Unknown User' }}</div>
                                            <div class="user-email">{{ $prequest->user->email ?? 'No email' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="{{ __('Plan Details') }}">
                                    <div class="plan-badge">
                                        <i class="fas fa-crown"></i>
                                        {{ $prequest->plan->name }}
                                    </div>
                                </td>
                                <td data-label="{{ __('Capacity') }}">
                                    <div class="metric-value">{{ $prequest->plan->max_employees }}</div>
                                    <div class="metric-label">{{ __('Employees') }}</div>
                                    <div class="metric-value">{{ $prequest->plan->max_users }}</div>
                                    <div class="metric-label">{{ __('Users') }}</div>
                                </td>
                                <td data-label="{{ __('Duration') }}">
                                    <div class="duration-badge">
                                        <i class="fas fa-calendar-alt"></i>
                                        {{ $prequest->duration }}
                                    </div>
                                </td>
                                <td data-label="{{ __('Request Date') }}">
                                    <div class="metric-value">{{ $prequest->created_at->format('M d, Y') }}</div>
                                    <div class="metric-label">{{ $prequest->created_at->format('h:i A') }}</div>
                                </td>
                                <td data-label="{{ __('Status') }}">
                                    <span class="status-badge status-pending">
                                        <i class="fas fa-clock"></i>
                                        {{ __('Pending') }}
                                    </span>
                                </td>
                                <td data-label="{{ __('Actions') }}" class="action">
                                    <a href="{{ route('plan_request.update', $prequest->id) }}"
                                        class="premium-btn btn-approve">
                                        <i class="fas fa-check"></i>
                                        {{ __('Approve') }}
                                    </a>
                                    <a href="#" class="premium-btn btn-reject" 
                                       data-toggle="tooltip"
                                       data-original-title="{{ __('Reject Request') }}"
                                       data-confirm="{{ __('Are You Sure?') . '|' . __('This action cannot be undone. Do you want to reject this request?') }}"
                                       data-confirm-yes="document.getElementById('delete-form-{{ $prequest->id }}').submit();">
                                        <i class="fas fa-times"></i>
                                        {{ __('Reject') }}
                                    </a>
                                    <form method="POST"
                                          action="{{ route('plan_requests.destroy', $prequest->id) }}"
                                          id="delete-form-{{ $prequest->id }}"
                                          style="display: none;">
                                        @method('DELETE')
                                        @csrf
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <h3>{{ __('No Plan Requests Found') }}</h3>
                <p>{{ __('There are currently no plan upgrade requests to review.') }}</p>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced hover effects for table rows

    // Enhanced confirmation dialog
    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-confirm]')) {
            e.preventDefault();
            const confirmBtn = e.target.closest('[data-confirm]');
            const confirmData = confirmBtn.dataset.confirm.split('|');
            const title = confirmData[0];
            const message = confirmData[1];
            const confirmYes = confirmBtn.dataset.confirmYes;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, reject it!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'swal2-popup',
                        title: 'swal2-title',
                        confirmButton: 'swal2-confirm',
                        cancelButton: 'swal2-cancel'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Add loading state to row
                        const row = confirmBtn.closest('tr');
                        if (row) {
                            row.classList.add('loading-row');
                        }
                        
                        // Execute the action
                        eval(confirmYes);
                    }
                });
            } else {
                // Fallback to native confirm
                if (confirm(title + '\n' + message)) {
                    // Add loading state to row
                    const row = confirmBtn.closest('tr');
                    if (row) {
                        row.classList.add('loading-row');
                    }
                    
                    // Execute the action
                    eval(confirmYes);
                }
            }
        }
    });

    // Loading state for approve buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-approve')) {
            const row = e.target.closest('tr');
            if (row) {
                row.classList.add('loading-row');
            }
        }
    });

    // Button ripple effect
    const buttons = document.querySelectorAll('.premium-btn');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Smooth animations on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeIn 0.6s ease';
            }
        });
    });

    document.querySelectorAll('.premium-table-container').forEach(el => {
        observer.observe(el);
    });
});

// Add CSS for ripple effect
const style = document.createElement('style');
style.textContent = `
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
        pointer-events: none;
    }
    
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>
@endsection 