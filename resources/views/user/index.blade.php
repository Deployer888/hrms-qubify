@extends('layouts.admin')

@section('page-title')
@if (Auth::user()->type == 'super admin')
    {{ __('Companies') }}
@else
    {{ __('Users') }}
@endif
@endsection

@push('css-page')
  <link rel="stylesheet" href="{{ asset('css/superAdmin/company.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    {{-- Premium Header --}}
    <div class="page-header-premium fade-in">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    @if(Auth::user()->type == 'super admin')
                        <i class="fas fa-building"></i>
                    @else
                        <i class="fas fa-users"></i>
                    @endif
                </div>
                <div class="header-text">
                    @if (Auth::user()->type == 'super admin')
                        <h1 class="page-title-compact">{{ __('Manage Companies') }}</h1>
                        <p class="page-subtitle-compact">{{ __('Manage and monitor company accounts and permissions') }}</p>
                    @else
                        <h1 class="page-title-compact">{{ __('Manage Users') }} </h1>
                        <p class="page-subtitle-compact">{{ __('Manage and monitor user accounts and permissions') }}</p>
                    @endif
                </div>
            </div>
            <div class="header-stats">
                <div class="stat-item">
                    @if (Auth::user()->type == 'super admin')
                        <p class="stat-number">{{ $users->count() }}</p>
                        <p class="stat-label"><b>{{ __('Total Companies') }}</b></p>
                    @else
                        <p class="stat-number">{{ $users->count() }}</p>
                        <p class="stat-label"><b>{{ __('Total Users') }}</b></p>
                    @endif
                </div>
                @can('Create User')
                <div class="stat-item">
                    <a href="#" data-url="{{ route('user.create') }}" data-ajax-popup="true" data-size="xl"
                       data-title="{{ Auth::user()->type == 'super admin' ? __('Create New Company') : __('Create New User') }}"
                       class="premium-btn">
                        <i class="fa fa-plus"></i> {{ __('Create') }}
                    </a>
                </div>
                @endcan
            </div>
        </div>
    </div>

    @if($users->count() > 0)
        <div class="row-equal-height">
            @foreach($users as $user)
            <div class="fade-in" style="animation-delay: {{ $loop->index * 0.1 }}s">
                <div class="premium-card">
                    {{-- Actions Dropdown --}}
                    @if (Gate::check('Edit User') || Gate::check('Delete User'))
                    <div class="actions-dropdown">
                        <div class="dropdown">
                            <button class="actions-btn" type="button" id="dropdownMenuButton{{ $user->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $user->id }}">
                                @can('Edit User')
                                <li>
                                    <a href="#" data-ajax-popup="true"
                                       data-url="{{ route('user.edit',$user->id) }}"
                                       data-title="{{ Auth::user()->type == 'super admin' ? __('Edit Company') : __('Edit User') }}"
                                       class="dropdown-item">
                                        <i class="fas fa-edit"></i>
                                        {{ __('Edit') }}
                                    </a>
                                </li>
                                @endcan
                                @can('Delete User')
                                <li>
                                    <a href="#" class="dropdown-item text-danger delete-user"
                                       data-user-id="{{ $user->id }}"
                                       data-user-name="{{ $user->name }}"
                                       data-user-type="{{ Auth::user()->type == 'super admin' ? 'company' : 'user' }}">
                                        <i class="fas fa-trash"></i>
                                        {{ __('Delete') }}
                                    </a>
                                    <form id="delete-form-{{ $user->id }}" action="{{ route('user.destroy',$user->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </li>
                                @endcan
                                <li>
                                    <a href="#" data-ajax-popup="true"
                                       data-url="{{ route('user.reset',\Crypt::encrypt($user->id)) }}"
                                       data-title="{{ Auth::user()->type == 'super admin' ? __('Reset Company Password') : __('Reset User Password') }}"
                                       class="dropdown-item">
                                        <i class="fas fa-key"></i>
                                        {{ __('Reset Password') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    @endif

                    <div class="premium-card-body">
                        <div class="card-main-content">
                            {{-- Avatar --}}
                            <div class="avatar-wrapper {{ $user->last_login && \Carbon\Carbon::parse($user->last_login)->diffInHours(now()) < 24 ? '' : 'offline-avatar' }}">
                                <img src="{{ $user->avatar ? asset(Storage::url('uploads/avatar/'.$user->avatar)) : asset(Storage::url('uploads/avatar/avatar.png')) }}"
                                     alt="avatar"
                                     class="user-avatar">
                            </div>

                            {{-- User Info --}}
                            <h5 class="user-name">{{ $user->name }}</h5>
                            <p class="user-company">{{ $user->company_name ?? 'Company Name' }}</p>
                            <p class="user-email">{{ $user->email }}</p>

                            <div class="role-badge role-{{ str_replace(' ', '-', strtolower($user->type)) }}">
                                @if($user->type == 'admin')
                                    <i class="fas fa-user-shield"></i>
                                @elseif($user->type == 'employee')
                                    <i class="fas fa-user"></i>
                                @elseif($user->type == 'manager')
                                    <i class="fas fa-user-tie"></i>
                                @elseif($user->type == 'director')
                                    <i class="fas fa-user-graduate"></i>
                                @elseif($user->type == 'project manager')
                                    <i class="fas fa-tasks"></i>
                                @elseif($user->type == 'hr')
                                    <i class="fas fa-heart"></i>
                                @else
                                    <i class="fas fa-crown"></i>
                                @endif
                                {{ Auth::user()->type == 'super admin' ? 'Company' : strtoupper(str_replace(' ', ' ', $user->type)) }}
                            </div>
                        </div>

                        @if(Auth::user()->type == 'super admin')
                        <div class="plan-info">
                            <div class="info-grid">
                                <div class="info-item">
                                    <div class="info-label">{{ __('Plan') }}</div>
                                    <div class="info-value">{{ $user->currentPlan->name ?? 'No Plan Selected' }}</div>
                                </div>
                                <div class="info-item">
                                    <a href="#" data-ajax-popup="true"
                                       data-url="{{ route('plan.upgrade',$user->id) }}"
                                       data-title="{{ __('Upgrade Company Plan') }}"
                                       class="upgrade-link">
                                        <i class="fas fa-arrow-up"></i>
                                        {{ __('Upgrade') }}
                                    </a>
                                </div>
                            </div>

                            <div class="plan-expires">
                                <i class="fas fa-clock"></i>
                                @if($user->plan_expire_date == null)
                                {{ __('No Limit') }}
                                @else
                                {{ __('Expires: ') }}{{ Auth::user()->dateFormat($user->plan_expire_date) }}
                                @endif
                            </div>

                            <div class="user-stats">
                                <div class="info-item">
                                    <div class="info-label">{{ __('Users') }}</div>
                                    <div class="info-value">{{ Auth::user()->countUsers() }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">{{ __('Employees') }}</div>
                                    <div class="info-value">{{ Auth::user()->countEmployees() }}</div>
                                </div>
                            </div>
                        </div>
                        @else
                        {{-- For non-super admin users, show basic stats --}}
                        
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="premium-card fade-in">
            <div class="premium-card-body">
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h3>{{ Auth::user()->type == 'super admin' ? __('No Companies Found') : __('No Users Found') }}</h3>
                    <p>{{ Auth::user()->type == 'super admin' ? __('Start by creating your first company account.') : __('Start by creating your first user account.') }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('script-page')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced hover effects for cards
    const cards = document.querySelectorAll('.premium-card');

    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Enhanced delete confirmation with proper styling
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-user')) {
            e.preventDefault();
            const deleteBtn = e.target.closest('.delete-user');
            const userId = deleteBtn.dataset.userId;
            const userName = deleteBtn.dataset.userName;
            const userType = deleteBtn.dataset.userType;

            // Show confirmation dialog with SweetAlert2 or native confirm
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: `Are you sure?`,
                    text: `Do you want to delete this ${userType} "${userName}"? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: `Yes, delete ${userType}!`,
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
                        document.getElementById(`delete-form-${userId}`).submit();
                    }
                });
            } else {
                // Fallback to native confirm
                const confirmMessage = `Are you sure you want to delete this ${userType} "${userName}"? This action cannot be undone.`;
                if (confirm(confirmMessage)) {
                    // Add loading state to the card
                    const card = deleteBtn.closest('.premium-card');
                    if (card) {
                        card.classList.add('loading-card');
                    }

                    // Submit the form
                    document.getElementById(`delete-form-${userId}`).submit();
                }
            }
        }
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

    // Smooth scroll to top when creating new user
    const createButtons = document.querySelectorAll('[data-ajax-popup]');
    createButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.dataset.title && this.dataset.title.includes('Create')) {
                setTimeout(() => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }, 100);
            }
        });
    });

    // Add ripple effect to buttons
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

    // Enhanced dropdown animations
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        const menu = dropdown.querySelector('.dropdown-menu');

        dropdown.addEventListener('show.bs.dropdown', function() {
            if (menu) {
                menu.style.transform = 'translateY(-10px)';
                menu.style.opacity = '0';
                setTimeout(() => {
                    menu.style.transform = 'translateY(0)';
                    menu.style.opacity = '1';
                }, 10);
            }
        });
    });

    // Remove loading state when modal is closed
    document.addEventListener('hidden.bs.modal', function() {
        document.querySelectorAll('.loading-card').forEach(card => {
            card.classList.remove('loading-card');
        });
    });

    // Handle window resize for better responsive behavior
    function handleResize() {
        const windowWidth = window.innerWidth;

        // Adjust card heights based on screen size
        const cards = document.querySelectorAll('.premium-card');
        cards.forEach(card => {
            if (windowWidth <= 576) {
                card.style.minHeight = '380px';
            } else if (windowWidth <= 768) {
                card.style.minHeight = '400px';
            } else if (windowWidth <= 992) {
                card.style.minHeight = '420px';
            } else {
                card.style.minHeight = '420px';
            }
        });
    }

    // Initial call and resize listener
    handleResize();
    window.addEventListener('resize', handleResize);

    // Intersection Observer for fade-in animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe all fade-in elements
    document.querySelectorAll('.fade-in').forEach(el => {
        observer.observe(el);
    });
});
</script>
@endpush