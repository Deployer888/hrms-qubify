@extends('layouts.admin')

@section('page-title')
    {{ __('Role Details') }}
@endsection

@push('css-page')
<style>
/* Enhanced Role Show Styles */
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --card-shadow: 0 10px 30px rgba(0,0,0,0.1);
    --card-shadow-hover: 0 20px 40px rgba(0,0,0,0.15);
    --border-radius: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

.btn-edit {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
}

.btn-edit:hover {
    box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
}

/* Premium Show Container */
.premium-show-container {
    max-width: 1200px;
    margin: 0 auto;
}

/* Show Body */
.show-body {
    display: grid;
    gap: 30px;
}

/* Top Section */
.top-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

/* Info Cards */
.info-card {
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    border: 1px solid rgba(255,255,255,0.8);
    backdrop-filter: blur(10px);
    overflow: hidden;
    transition: var(--transition);
    position: relative;
}

.info-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--primary-gradient);
}

.info-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--card-shadow-hover);
}

.info-card-header {
    padding: 25px 30px 20px;
    border-bottom: 1px solid rgba(102, 126, 234, 0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.info-card-title {
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.info-card-title i {
    color: #667eea;
    font-size: 20px;
}

.count-badge {
    background: var(--primary-gradient);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

/* Role Info Content */
.role-info-content {
    padding: 30px;
}

.role-info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid rgba(102, 126, 234, 0.1);
}

.role-info-item:last-child {
    border-bottom: none;
}

.role-info-label {
    font-weight: 600;
    color: #6b7280;
    font-size: 14px;
}

.role-info-value {
    font-weight: 700;
    color: #1f2937;
    font-size: 16px;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.status-active {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

/* Users Content */
.users-content {
    padding: 30px;
    max-height: 300px;
    overflow-y: auto;
}

.user-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    border-radius: 12px;
    margin-bottom: 15px;
    border: 1px solid rgba(102, 126, 234, 0.1);
    transition: var(--transition);
}

.user-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
}

.user-item:last-child {
    margin-bottom: 0;
}

.user-info .user-name {
    font-weight: 600;
    color: #1f2937;
    font-size: 14px;
    margin-bottom: 4px;
}

.user-info .user-email {
    color: #6b7280;
    font-size: 12px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #6b7280;
}

.empty-state i {
    font-size: 48px;
    color: #9ca3af;
    margin-bottom: 15px;
}

.empty-state p {
    font-size: 16px;
    font-weight: 500;
    margin: 0;
}

/* Permissions Section */
.permissions-section {
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
    border-radius: var(--border-radius);
    box-shadow: var(--card-shadow);
    border: 1px solid rgba(255,255,255,0.8);
    backdrop-filter: blur(10px);
    overflow: hidden;
    position: relative;
}

.permissions-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--primary-gradient);
}

.permissions-header {
    padding: 25px 30px 20px;
    border-bottom: 1px solid rgba(102, 126, 234, 0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
}

.permissions-title {
    font-size: 20px;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.permissions-title i {
    color: #667eea;
    font-size: 22px;
}

/* Permissions Grid */
.permissions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 25px;
    padding: 30px;
}

.module-permission-card {
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 12px;
    border: 1px solid rgba(102, 126, 234, 0.1);
    overflow: hidden;
    transition: var(--transition);
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.module-permission-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
}

.module-permission-header {
    background: var(--primary-gradient);
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.module-permission-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}

.module-permission-name {
    color: white;
    font-weight: 700;
    font-size: 16px;
    margin: 0;
}

.module-permission-list {
    padding: 20px;
}

.permission-detail {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid rgba(102, 126, 234, 0.1);
}

.permission-detail:last-child {
    border-bottom: none;
}

.permission-icon {
    width: 24px;
    height: 24px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    font-weight: bold;
    flex-shrink: 0;
}

.permission-name {
    font-weight: 600;
    color: #1f2937;
    font-size: 14px;
    margin-bottom: 2px;
}

.permission-description {
    color: #6b7280;
    font-size: 12px;
    font-style: italic;
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .top-section {
        grid-template-columns: 1fr;
    }

    .permissions-grid {
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    }
}

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

    .permissions-grid {
        grid-template-columns: 1fr;
        gap: 20px;
        padding: 20px;
    }

    .info-card-header,
    .permissions-header {
        padding: 20px;
    }

    .role-info-content,
    .users-content {
        padding: 20px;
    }
}

@media (max-width: 576px) {
    .role-info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }

    .user-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }

    .permissions-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="premium-show-container">
        <!-- Header -->
        <div class="page-header-compact d-flex align-items-center justify-content-between">
            <div class="header-content d-flex align-items-center">
                <div class="header-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h1 class="page-title-compact ml-3">{{ __('Role Details') }}: {{ strtoupper($role->name) }}</h1>
            </div>
            @can('Edit Role')
                <a href="#"
                    data-ajax-popup="true"
                    data-url="{{ route('roles.edit', $role->id) }}"
                    data-size="xl"
                    data-title="{{ __('Edit Role') }}: {{ $role->name }}"
                    class="premium-btn btn-edit">
                    <i class="fas fa-edit"></i>
                    {{ __('Edit Role') }}
                </a>
            @endcan
        </div>

        <!-- Show Body -->
        <div class="show-body">
            <!-- Top Section: Role Information & Users -->
            <div class="top-section">
                <!-- Role Information Card -->
                <div class="info-card">
                    <div class="info-card-header">
                        <h5 class="info-card-title">
                            <i class="fas fa-info-circle"></i>
                            {{ __('Role Information') }}
                        </h5>
                    </div>

                    <div class="role-info-content">
                        <div class="role-info-item">
                            <span class="role-info-label">{{ __('Name') }}:</span>
                            <span class="role-info-value">{{ strtoupper($role->name) }}</span>
                        </div>
                        <div class="role-info-item">
                            <span class="role-info-label">{{ __('Description') }}:</span>
                            <span class="role-info-value">{{ __('Full access to all modules') }}</span>
                        </div>
                        <div class="role-info-item">
                            <span class="role-info-label">{{ __('Status') }}:</span>
                            <span class="status-active">{{ __('Active') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Users with this Role Card -->
                <div class="info-card">
                    <div class="info-card-header">
                        <h5 class="info-card-title">
                            <i class="fas fa-users"></i>
                            {{ __('Users with this Role') }}
                        </h5>
                        @php
                            $usersWithRole = \App\Models\User::role($role->name)->where('is_active', 1)->get();
                        @endphp
                        <span class="count-badge">{{ $usersWithRole->count() }}</span>
                    </div>

                    <div class="users-content">
                        @forelse($usersWithRole as $user)
                            <div class="user-item">
                                <div class="user-info">
                                    <div class="user-name">{{ $user->name }}</div>
                                    <div class="user-email">{{ $user->email }}</div>
                                </div>
                                <span class="status-active">{{ __('Active') }}</span>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="fas fa-user-slash"></i>
                                <p>{{ __('No users assigned to this role') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Permissions Section -->
            <div class="permissions-section">
                <div class="permissions-header">
                    <h5 class="permissions-title">
                        <i class="fas fa-shield-alt"></i>
                        {{ __('Permissions') }}
                    </h5>
                    <span class="count-badge">{{ $role->permissions->count() }}</span>
                </div>

                @if($role->permissions->count() > 0)
                    <div class="permissions-grid">
                        @php
                            // Group permissions by module
                            $permissionsByModule = [];
                            $moduleIcons = [
                                'User' => 'fas fa-users',
                                'Role' => 'fas fa-user-shield',
                                'Dashboard' => 'fas fa-tachometer-alt',
                                'Buy' => 'fas fa-shopping-cart',
                                'Booking' => 'fas fa-calendar-check',
                                'Staff' => 'fas fa-user-tie',
                                'Billing' => 'fas fa-file-invoice-dollar',
                                'Garage' => 'fas fa-warehouse',
                                'Customer' => 'fas fa-user-friends',
                                'Retail Customer' => 'fas fa-store',
                                'Permissions' => 'fas fa-key',
                                'Award' => 'fas fa-trophy',
                                'Transfer' => 'fas fa-exchange-alt',
                                'Resignation' => 'fas fa-sign-out-alt',
                                'Travel' => 'fas fa-plane',
                                'Promotion' => 'fas fa-arrow-up',
                                'Complaint' => 'fas fa-exclamation-triangle',
                                'Warning' => 'fas fa-exclamation',
                                'Termination' => 'fas fa-times-circle',
                                'Department' => 'fas fa-building',
                                'Designation' => 'fas fa-id-badge',
                                'Document Type' => 'fas fa-file-alt',
                                'Branch' => 'fas fa-code-branch',
                                'Employee' => 'fas fa-user',
                                'Attendance' => 'fas fa-clock',
                                'Leave' => 'fas fa-calendar-minus',
                                'Office' => 'fas fa-building',
                                'Report' => 'fas fa-chart-area'
                            ];

                            foreach ($role->permissions as $permission) {
                                $parts = explode(' ', $permission->name, 2);
                                $action = $parts[0] ?? '';
                                $module = $parts[1] ?? 'Other';

                                $permissionsByModule[$module][] = [
                                    'action' => $action,
                                    'full_name' => $permission->name,
                                    'description' => strtolower(str_replace(' ', '.', $permission->name))
                                ];
                            }
                        @endphp

                        @foreach($permissionsByModule as $module => $permissions)
                            <div class="module-permission-card">
                                <div class="module-permission-header">
                                    <div class="module-permission-icon">
                                        <i class="{{ $moduleIcons[$module] ?? 'fas fa-cog' }}"></i>
                                    </div>
                                    <h6 class="module-permission-name">{{ $module }}</h6>
                                </div>

                                <div class="module-permission-list">
                                    @foreach($permissions as $permission)
                                        <div class="permission-detail">
                                            <div class="permission-icon">✓</div>
                                            <div>
                                                <div class="permission-name">{{ $permission['full_name'] }}</div>
                                                <div class="permission-description">{{ $permission['description'] }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-shield-alt"></i>
                        <p>{{ __('No permissions assigned to this role') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('script-page')
<script>
    $(document).ready(function() {
        // Enhanced button interactions
        $('.premium-btn').hover(
            function() {
                $(this).addClass('animate__animated animate__pulse');
            },
            function() {
                $(this).removeClass('animate__animated animate__pulse');
            }
        );

        // Smooth scroll animation for long content
        $('.permissions-grid').css({
            'max-height': '600px',
            'overflow-y': 'auto'
        });

        // Custom scrollbar for permissions grid
        if ($('.permissions-grid')[0]) {
            $('.permissions-grid').css({
                'scrollbar-width': 'thin',
                'scrollbar-color': 'var(--primary) var(--light)'
            });
        }

        // Add loading state for edit button
        $('[data-ajax-popup="true"]').on('click', function() {
            var $btn = $(this);
            var originalText = $btn.html();
            $btn.html('<i class="fas fa-spinner fa-spin"></i> Loading...');

            setTimeout(function() {
                $btn.html(originalText);
            }, 2000);
        });

        // Ripple effect for buttons
        $('.premium-btn').on('click', function(e) {
            const button = this;
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
        });

        // Enhanced card animations
        $('.info-card, .module-permission-card').each(function() {
            $(this).on('mouseenter', function() {
                $(this).find('.info-card-title, .module-permission-name').addClass('animate__animated animate__pulse');
            }).on('mouseleave', function() {
                $(this).find('.info-card-title, .module-permission-name').removeClass('animate__animated animate__pulse');
            });
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