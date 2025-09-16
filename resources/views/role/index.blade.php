@extends('layouts.admin')

@section('page-title')
{{ __('Manage Roles') }}
@endsection

@push('css-page')
<style>
/* Enhanced Roles Management Styles */
:root {
    --primary-gradient: #2563eb;
    --card-shadow: 0 10px 30px rgba(0,0,0,0.1);
    --card-shadow-hover: 0 20px 40px rgba(0,0,0,0.15);
    --border-radius: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

body {
    background: #2563eb;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    position: relative;
    min-height: 100vh;
}

body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background:
        radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 60%, rgba(102, 126, 234, 0.1) 0%, transparent 50%);
    pointer-events: none;
    z-index: -1;
}

.container-fluid {
    position: relative;
    z-index: 1;
}

/* Premium Page Header */
.page-header-compact {
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 20px;
    padding: 30px 40px;
    margin-bottom: 30px;
    box-shadow: var(--card-shadow);
    border: 1px solid rgba(255,255,255,0.8);
    backdrop-filter: blur(10px);
    position: relative;
    overflow: hidden;
}

.page-header-compact::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--primary-gradient);
}

.header-content {
    display: flex;
    align-items: center;
}

.header-icon {
    width: 60px;
    height: 60px;
    background: var(--primary-gradient);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    transition: var(--transition);
}

.header-icon i {
    font-size: 24px;
    color: white;
}

.page-title-compact {
    font-size: 28px;
    font-weight: 800;
    color: #1f2937;
    margin: 0;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.all-button-box {
    margin: 0;
}

.premium-btn {
    background: var(--primary-gradient);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 12px 24px;
    font-weight: 600;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: var(--transition);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.premium-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: var(--transition);
}

.premium-btn:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    color: white;
    text-decoration: none;
}

.premium-btn:hover::before {
    left: 100%;
    transition: left 0.6s ease-in-out;
}

/* Premium Card */
.premium-card {
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    border: 1px solid rgba(255,255,255,0.8);
    backdrop-filter: blur(10px);
    overflow: hidden;
    transition: var(--transition);
    position: relative;
}

.premium-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--primary-gradient);
}

.premium-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--card-shadow-hover);
}

.premium-card-body {
    padding: 0;
}

/* Enhanced Table */
.table-responsive {
    border-radius: var(--border-radius);
    overflow: hidden;
}

.premium-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
    background: transparent;
}

.premium-table thead {
    background: var(--primary-gradient);
}

.premium-table thead th {
    color: white;
    font-weight: 700;
    padding: 20px 25px;
    text-align: center;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
    position: relative;
}

.premium-table thead th:first-child {
    text-align: left;
}

.premium-table thead th i {
    margin-right: 8px;
    opacity: 0.9;
}

.premium-table tbody tr {
    transition: var(--transition);
    border-bottom: 1px solid rgba(102, 126, 234, 0.1);
}

.premium-table tbody tr:hover {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    transform: scale(1.01);
}

.premium-table tbody tr:last-child {
    border-bottom: none;
}

.premium-table tbody td {
    padding: 20px 25px;
    vertical-align: middle;
    font-size: 14px;
    border: none;
}

/* Role Display */
.role-name {
    display: flex;
    align-items: center;
    gap: 15px;
}

.role-avatar {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: var(--primary-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 800;
    font-size: 16px;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.role-avatar::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
    animation: float 3s ease-in-out infinite;
}

.premium-table tbody tr:hover .role-avatar {
    transform: scale(1.1) rotate(5deg);
}

.role-details h6 {
    font-size: 16px;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 4px 0;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.role-details small {
    color: #6b7280;
    font-size: 12px;
    font-weight: 500;
}

/* Permission Count */
.permission-count {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    padding: 10px 16px;
    border-radius: 20px;
    border: 1px solid rgba(102, 126, 234, 0.2);
    font-weight: 600;
    color: #667eea;
    font-size: 13px;
    transition: var(--transition);
}

.permission-count i {
    color: #667eea;
    font-size: 14px;
}

.premium-table tbody tr:hover .permission-count {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
    border-color: rgba(102, 126, 234, 0.3);
    transform: scale(1.05);
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 8px;
    justify-content: center;
}

.action-btn {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
    text-decoration: none;
    font-size: 14px;
    position: relative;
    overflow: hidden;
}

.action-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: var(--transition);
}

.action-btn:hover::before {
    left: 100%;
    transition: left 0.6s ease-in-out;
}

.btn-show {
    background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.btn-show:hover {
    transform: translateY(-3px) scale(1.1);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
    color: white;
    text-decoration: none;
}

.btn-edit {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
}

.btn-edit:hover {
    transform: translateY(-3px) scale(1.1);
    box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
    color: white;
    text-decoration: none;
}

.btn-delete {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
}

.btn-delete:hover {
    transform: translateY(-3px) scale(1.1);
    box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
    color: white;
}

/* Loading States */
.loading-card {
    position: relative;
    pointer-events: none;
}

.loading-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(248,250,252,0.95) 100%);
    backdrop-filter: blur(10px);
    border-radius: var(--border-radius);
    z-index: 100;
    display: flex;
    align-items: center;
    justify-content: center;
}

.loading-card::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 40px;
    height: 40px;
    margin: -20px 0 0 -20px;
    border: 3px solid transparent;
    border-top-color: #667eea;
    border-right-color: #667eea;
    border-radius: 50%;
    animation: spin 1s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
    z-index: 101;
}

@keyframes spin {
    0% { transform: rotate(0deg) scale(1); }
    50% { transform: rotate(180deg) scale(1.1); }
    100% { transform: rotate(360deg) scale(1); }
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-10px) rotate(180deg); }
}

/* Enhanced SweetAlert2 Styling */
.swal2-popup {
    border-radius: 20px !important;
    box-shadow: 0 25px 50px rgba(0,0,0,0.15) !important;
    border: 1px solid rgba(255,255,255,0.2) !important;
}

.swal2-title {
    color: #1f2937 !important;
    font-weight: 700 !important;
}

.swal2-confirm {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
    border-radius: 10px !important;
    padding: 10px 20px !important;
    font-weight: 600 !important;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3) !important;
}

.swal2-cancel {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%) !important;
    border-radius: 10px !important;
    padding: 10px 20px !important;
    font-weight: 600 !important;
    box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3) !important;
}

/* Responsive Design */
@media (max-width: 768px) {
    .page-header-compact {
        padding: 20px;
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }

    .header-content {
        justify-content: center;
    }

    .page-title-compact {
        font-size: 24px;
    }

    .premium-table tbody td {
        padding: 15px 20px;
    }

    .role-name {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }

    .action-buttons {
        flex-wrap: wrap;
        gap: 6px;
    }

    .action-btn {
        width: 36px;
        height: 36px;
    }
}

@media (max-width: 576px) {
    .premium-table thead th,
    .premium-table tbody td {
        padding: 12px 15px;
        font-size: 13px;
    }

    .role-avatar {
        width: 40px;
        height: 40px;
        font-size: 14px;
    }

    .role-details h6 {
        font-size: 14px;
    }

    .permission-count {
        padding: 8px 12px;
        font-size: 12px;
    }
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #2563eb;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Enhanced Header --}}
    <div class="page-header-compact d-flex align-items-center justify-content-between">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-user-shield"></i>
            </div>
            <h1 class="page-title-compact">{{ __('Manage Roles') }}</h1>
        </div>
        @can('Create Role')
            <div class="all-button-box">
                <a href="#" data-ajax-popup="true" data-url="{{ route('roles.create') }}" data-size="xl"
                    data-title="{{ __('Create New Role') }}" class="premium-btn">
                    <i class="fa fa-plus"></i> {{ __('Create') }}
                </a>
            </div>
        @endcan
    </div>

    {{-- Enhanced Roles Table --}}
    <div class="premium-card">
        <div class="premium-card-body">
            <div class="table-responsive">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user-tag"></i>{{ __('Role') }}</th>
                            <th><i class="fas fa-key"></i>{{ __('Permissions Count') }}</th>
                            <th><i class="fas fa-cogs"></i>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>
                                    <div class="role-name">
                                        <div class="role-avatar">
                                            {{ strtoupper(substr($role->name, 0, 2)) }}
                                        </div>
                                        <div class="role-details">
                                            <h6>{{ strtoupper($role->name) }}</h6>
                                            <small>{{ __('Role Management') }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="permission-count">
                                        <i class="fas fa-shield-alt"></i>
                                        {{ $role->permissions->count() }} {{ __('Permissions') }}
                                    </span>
                                </td>

                                <td>
                                    <div class="action-buttons">
                                        {{-- Show Button --}}
                                        @can('Show Role')
                                            <a href="{{ route('roles.show', $role->id) }}"
                                                data-title="{{ __('View Role Details') }}: {{ $role->name }}"
                                                class="action-btn btn-show" title="{{ __('View Details') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endcan

                                        {{-- Edit Button --}}
                                        @can('Edit Role')
                                            <a href="#" data-ajax-popup="true"
                                                data-url="{{ route('roles.edit', $role->id) }}" data-size="xl"
                                                data-title="{{ __('Edit Role') }} : {{ $role->name }}"
                                                class="action-btn btn-edit" title="{{ __('Edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan

                                        {{-- Delete Button --}}
                                        @if ($role->name !== 'employee')
                                            @can('Delete Role')
                                                <button type="button" class="action-btn btn-delete delete-role"
                                                    data-role-id="{{ $role->id }}" data-role-name="{{ $role->name }}"
                                                    title="{{ __('Delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                {{-- Hidden Form for Delete --}}
                                                <form id="delete-form-{{ $role->id }}"
                                                    action="{{ route('roles.destroy', $role->id) }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            @endcan
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-page')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced delete confirmation with proper styling
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-role')) {
            e.preventDefault();
            const deleteBtn = e.target.closest('.delete-role');
            const roleId = deleteBtn.getAttribute('data-role-id');
            const roleName = deleteBtn.getAttribute('data-role-name');

            // Check if SweetAlert2 is available
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you want to delete the role "${roleName}"? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'swal2-popup',
                        title: 'swal2-title',
                        confirmButton: 'swal2-confirm',
                        cancelButton: 'swal2-cancel'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Add loading state to the card
                        const card = deleteBtn.closest('.premium-card');
                        if (card) {
                            card.classList.add('loading-card');
                        }
                        // Submit the form
                        const form = document.getElementById(`delete-form-${roleId}`);
                        if (form) {
                            form.submit();
                        }
                    }
                });
            } else {
                // Fallback to native confirm if SweetAlert2 is not available
                const confirmMessage = `Are you sure you want to delete the role "${roleName}"? This action cannot be undone.`;
                if (confirm(confirmMessage)) {
                    // Add loading state to the card
                    const card = deleteBtn.closest('.premium-card');
                    if (card) {
                        card.classList.add('loading-card');
                    }
                    // Submit the form
                    const form = document.getElementById(`delete-form-${roleId}`);
                    if (form) {
                        form.submit();
                    }
                }
            }
        }
    });

    // Enhanced hover effects for table rows
    const tableRows = document.querySelectorAll('.premium-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01) translateY(-2px)';
        });

        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) translateY(0)';
        });
    });

    // Loading state for AJAX actions
    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-ajax-popup]')) {
            const card = e.target.closest('.premium-card');
            if (card) {
                card.classList.add('loading-card');
                setTimeout(() => {
                    card.classList.remove('loading-card');
                }, 3000); // Remove loading state after 3 seconds
            }
        }
    });

    // Remove loading state when modal is closed
    document.addEventListener('hidden.bs.modal', function() {
        document.querySelectorAll('.loading-card').forEach(card => {
            card.classList.remove('loading-card');
        });
    });

    // Enhanced animations for action buttons
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.1) rotate(5deg)';
        });

        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1) rotate(0deg)';
        });
    });

    // Ripple effect for buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.premium-btn, .action-btn')) {
            const button = e.target.closest('.premium-btn, .action-btn');
            const ripple = document.createElement('span');
            const rect = button.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(255, 255, 255, 0.5)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s linear';
            ripple.style.pointerEvents = 'none';

            button.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        }
    });
});

// Add CSS for ripple animation
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>
@endpush