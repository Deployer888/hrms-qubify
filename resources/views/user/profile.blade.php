@extends('layouts.admin')

@section('page-title')
    {{ __('Account Setting') }}
@endsection

@push('css-page')
<style>
    :root {
        --primary: #2563eb;
        --secondary: #3b82f6;
        --accent: #60a5fa;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        --shadow-md: 0 8px 25px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 15px 35px rgba(0, 0, 0, 0.1);
        --shadow-xl: 0 25px 50px rgba(0, 0, 0, 0.15);
        --text-primary: #2d3748;
        --text-secondary: #6b7280;
        --border-radius: 16px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        background-attachment: fixed;
        min-height: 100vh;
        position: relative;
    }

    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: 
            radial-gradient(circle at 20% 50%, rgba(120, 119, 198, 0.3), transparent 50.2%),
            radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1), transparent 50.2%),
            radial-gradient(circle at 40% 80%, rgba(120, 119, 198, 0.2), transparent 50.2%);
        pointer-events: none;
        z-index: -1;
    }

    .container-fluid {
        padding: 0 20px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    /* Premium Header */
    .page-header-premium {
        background: linear-gradient(135deg, 
            rgba(37, 99, 235, 0.95) 0%, 
            rgba(59, 130, 246, 0.95) 50%, 
            rgba(96, 165, 250, 0.95) 100%);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 24px;
        padding: 32px 40px;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
        transition: var(--transition);
    }

    .page-header-premium::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: conic-gradient(from 0deg at 50% 50%, 
            transparent 0deg, 
            rgba(255, 255, 255, 0.1) 60deg, 
            transparent 120deg, 
            rgba(255, 255, 255, 0.05) 180deg, 
            transparent 240deg, 
            rgba(255, 255, 255, 0.1) 300deg, 
            transparent 360deg);
        animation: rotateBg 25s linear infinite;
        pointer-events: none;
    }

    @keyframes rotateBg {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 20px;
        position: relative;
        z-index: 2;
    }

    .header-icon {
        width: 72px;
        height: 72px;
        background: rgba(255, 255, 255, 0.15);
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: white;
        backdrop-filter: blur(20px);
        box-shadow: 0 8px 32px rgba(255, 255, 255, 0.1);
        transition: var(--transition);
    }

    .header-text h1 {
        font-size: 2rem;
        font-weight: 800;
        color: #fff;
        margin: 0;
        line-height: 1.2;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .header-text p {
        color: rgba(255, 255, 255, 0.9);
        margin: 6px 0 0 0;
        font-size: 1rem;
        font-weight: 500;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    /* Premium Cards */
    .premium-card {
        background: rgba(255, 255, 255, 0.98);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 24px;
        box-shadow: var(--shadow-xl);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .premium-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, var(--primary) 0%, var(--secondary) 50%, var(--accent) 100%);
        box-shadow: 0 0 20px rgba(37, 99, 235, 0.3);
    }

    .premium-card:hover {
        transform: translateY(-4px) scale(1.002);
        box-shadow: 0 48px 80px rgba(37, 99, 235, 0.15);
    }

    /* Avatar Section */
    .avatar-container {
        position: relative;
        display: inline-block;
    }

    .avatar {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        object-fit: cover;
        border: 6px solid rgba(255, 255, 255, 0.8);
        box-shadow: 
            0 20px 40px rgba(0, 0, 0, 0.1),
            0 8px 16px rgba(37, 99, 235, 0.2);
        transition: var(--transition);
        cursor: pointer;
    }

    .avatar:hover {
        transform: scale(1.05);
        box-shadow: 
            0 25px 50px rgba(0, 0, 0, 0.15),
            0 12px 24px rgba(37, 99, 235, 0.3);
    }

    .avatar-upload-btn {
        position: absolute;
        bottom: 8px;
        right: 8px;
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        border: 4px solid white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.1rem;
        cursor: pointer;
        transition: var(--transition);
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.3);
    }

    .avatar-upload-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 12px 32px rgba(37, 99, 235, 0.4);
    }

    .user-name {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-primary);
        margin: 16px 0 8px 0;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .user-role {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        padding: 8px 20px;
        border-radius: 24px;
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        box-shadow: 0 4px 16px rgba(37, 99, 235, 0.3);
        border: none;
        display: inline-block;
    }

    .user-email {
        color: var(--text-secondary);
        font-size: 1rem;
        font-weight: 500;
    }

    /* Card Headers */
    .premium-card-header {
        background: linear-gradient(135deg, 
            rgba(248, 250, 252, 0.95) 0%, 
            rgba(241, 245, 249, 0.95) 100%);
        padding: 24px 32px;
        border-bottom: 1px solid rgba(229, 231, 235, 0.5);
        display: flex;
        align-items: center;
        gap: 12px;
        border-radius: 24px 24px 0 0;
        position: relative;
    }

    .premium-card-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 5%;
        right: 5%;
        height: 1px;
        background: linear-gradient(90deg, 
            transparent 0%, 
            rgba(37, 99, 235, 0.3) 20%, 
            rgba(37, 99, 235, 0.6) 50%, 
            rgba(37, 99, 235, 0.3) 80%, 
            transparent 100%);
    }

    .premium-card-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .premium-card-title i {
        color: var(--primary);
        font-size: 1.1rem;
    }

    /* Card Body */
    .premium-card-body {
        padding: 32px;
    }

    /* Form Elements */
    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        font-weight: 700;
        color: var(--text-primary);
        font-size: 0.95rem;
        margin-bottom: 8px;
        display: block;
        letter-spacing: -0.025em;
    }

    .form-control {
        width: 100%;
        padding: 16px 20px;
        border: 2px solid rgba(229, 231, 235, 0.6);
        border-radius: 16px;
        font-size: 1rem;
        font-weight: 500;
        color: var(--text-primary);
        background: rgba(255, 255, 255, 0.8);
        transition: var(--transition);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 
            0 0 0 4px rgba(37, 99, 235, 0.1),
            0 8px 24px rgba(37, 99, 235, 0.15);
        transform: translateY(-2px);
    }

    .form-control::placeholder {
        color: var(--text-secondary);
        opacity: 0.7;
    }

    /* Premium Button */
    .premium-btn {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        border: none;
        color: white;
        padding: 16px 32px;
        border-radius: 16px;
        font-weight: 700;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: var(--transition);
        text-decoration: none;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(37, 99, 235, 0.3);
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
        background: linear-gradient(90deg, 
            transparent 0%, 
            rgba(255, 255, 255, 0.2) 50%, 
            transparent 100%);
        transition: left 0.6s ease;
    }

    .premium-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.4);
        color: white;
    }

    .premium-btn:hover::before {
        left: 100%;
    }

    /* Error Messages */
    .text-danger {
        color: var(--danger) !important;
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .text-danger::before {
        content: '⚠';
        font-size: 0.9rem;
    }

    /* Info Items for Personal Details */
    .info-item {
        margin-bottom: 20px;
    }

    .info-label {
        font-weight: 700;
        color: var(--text-primary);
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        color: var(--text-secondary);
        font-size: 1rem;
        font-weight: 500;
        padding: 12px 16px;
        background: rgba(248, 250, 252, 0.8);
        border-radius: 12px;
        border: 1px solid rgba(229, 231, 235, 0.5);
    }

    /* Animations */
    .fade-in {
        animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes fadeIn {
        from { 
            opacity: 0; 
            transform: translateY(30px) scale(0.95); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0) scale(1); 
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 0 16px;
        }

        .page-header-premium {
            padding: 24px 28px;
            border-radius: 20px;
        }

        .header-content {
            flex-direction: column;
            text-align: center;
            gap: 16px;
        }

        .header-icon {
            width: 64px;
            height: 64px;
            font-size: 1.6rem;
        }

        .header-text h1 {
            font-size: 1.7rem;
        }

        .premium-card-body {
            padding: 24px;
        }

        .avatar {
            width: 120px;
            height: 120px;
        }

        .avatar-upload-btn {
            width: 42px;
            height: 42px;
            font-size: 1rem;
        }

        .form-control {
            padding: 14px 18px;
        }

        .premium-btn {
            width: 100%;
            justify-content: center;
            padding: 14px 24px;
        }
    }

    /* Password Strength Indicator */
    .password-strength {
        margin-top: 8px;
        height: 4px;
        border-radius: 2px;
        background: rgba(229, 231, 235, 0.5);
        overflow: hidden;
        transition: var(--transition);
    }

    .password-strength-fill {
        height: 100%;
        border-radius: 2px;
        transition: var(--transition);
        width: 0%;
    }

    .password-strength-weak .password-strength-fill {
        background: var(--danger);
        width: 33%;
    }

    .password-strength-medium .password-strength-fill {
        background: var(--warning);
        width: 66%;
    }

    .password-strength-strong .password-strength-fill {
        background: var(--success);
        width: 100%;
    }
</style>
@endpush

@section('content')
    {{-- Premium Header --}}
    <div class="page-header-premium fade-in">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-cogs"></i>
            </div>
            <div class="header-text">
                <h1>{{ __('Account Settings') }}</h1>
                <p>{{ __('Manage your personal details and password securely') }}</p>
            </div>
        </div>
    </div>

    <div class="row gx-4 gy-4">
        {{-- Profile Card --}}
        <div class="col-lg-4 col-md-5">
            <div class="premium-card text-center">
                <div class="premium-card-body">
                    <form method="POST" action="{{ route('update.account') }}" enctype="multipart/form-data" id="profileForm">
                        @csrf
                        <div class="avatar-container">
                            <label for="avatar" class="d-inline-block position-relative">
                                <img src="{{ !empty($userDetail->avatar) ? asset(Storage::url('uploads/avatar/'.$userDetail->avatar)) : asset(Storage::url('uploads/avatar/avatar.png')) }}" 
                                     alt="Avatar" 
                                     class="avatar">
                                <div class="avatar-upload-btn">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <input type="file" 
                                       id="avatar" 
                                       name="profile" 
                                       class="d-none" 
                                       accept="image/*"
                                       onchange="handleAvatarChange(this)">
                            </label>
                        </div>
                        
                        <h4 class="user-name">{{ $userDetail->name }}</h4>
                        <span class="user-role">{{ ucfirst($userDetail->type) }}</span>
                        <p class="user-email">{{ $userDetail->email }}</p>
                        
                        <div class="mt-4">
                            <div class="info-item">
                                <div class="info-label">{{ __('Member Since') }}</div>
                                <div class="info-value">{{ $userDetail->created_at->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Settings Panel --}}
        <div class="col-lg-8 col-md-7">
            {{-- Personal Information --}}
            {{-- <div class="premium-card">
                <div class="premium-card-header">
                    <h6 class="premium-card-title">
                        <i class="fas fa-user"></i>
                        {{ __('Personal Information') }}
                    </h6>
                </div>
                <div class="premium-card-body">
                    <form method="POST" action="{{ route('update.profile') }}" id="profileInfoForm">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Full Name') }}</label>
                                    <input type="text" 
                                           name="name" 
                                           class="form-control" 
                                           value="{{ $userDetail->name }}"
                                           placeholder="{{ __('Enter your full name') }}"
                                           required>
                                    @error('name')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Email Address') }}</label>
                                    <input type="email" 
                                           name="email" 
                                           class="form-control" 
                                           value="{{ $userDetail->email }}"
                                           placeholder="{{ __('Enter your email address') }}"
                                           required>
                                    @error('email')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Phone Number') }}</label>
                                    <input type="tel" 
                                           name="phone" 
                                           class="form-control" 
                                           value="{{ $userDetail->phone ?? '' }}"
                                           placeholder="{{ __('Enter your phone number') }}">
                                    @error('phone')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Department') }}</label>
                                    <input type="text" 
                                           name="department" 
                                           class="form-control" 
                                           value="{{ $userDetail->department ?? '' }}"
                                           placeholder="{{ __('Enter your department') }}">
                                    @error('department')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button type="submit" class="premium-btn">
                                <i class="fas fa-save"></i> 
                                {{ __('Update Profile') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div> --}}

            {{-- Change Password --}}
            <div class="premium-card">
                <div class="premium-card-header">
                    <h6 class="premium-card-title">
                        <i class="fas fa-lock"></i>
                        {{ __('Change Password') }}
                    </h6>
                </div>
                <div class="premium-card-body">
                    <form method="POST" action="{{ route('update.password') }}" id="passwordForm">
                        @csrf
                        <div class="row g-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Current Password') }}</label>
                                    <input type="password" 
                                           name="current_password" 
                                           class="form-control" 
                                           placeholder="{{ __('Enter your current password') }}"
                                           required>
                                    @error('current_password')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('New Password') }}</label>
                                    <input type="password" 
                                           name="new_password" 
                                           id="new_password"
                                           class="form-control" 
                                           placeholder="{{ __('Enter new password') }}"
                                           required>
                                    <div class="password-strength" id="passwordStrength">
                                        <div class="password-strength-fill"></div>
                                    </div>
                                    @error('new_password')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Confirm New Password') }}</label>
                                    <input type="password" 
                                           name="confirm_password" 
                                           class="form-control" 
                                           placeholder="{{ __('Confirm your new password') }}"
                                           required>
                                    @error('confirm_password')<div class="text-danger">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button type="submit" class="premium-btn">
                                <i class="fas fa-key"></i> 
                                {{ __('Change Password') }}
                            </button>
                        </div>
                    </form>
                </div>    
            </div>
        </div>
    </div>

    <!-- Success/Error Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 1050; min-width: 300px;">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 1050; min-width: 300px;">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password strength indicator
            const passwordField = document.getElementById('new_password');
            const strengthIndicator = document.getElementById('passwordStrength');
            
            if (passwordField && strengthIndicator) {
                passwordField.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;
                    let strengthClass = '';
                    
                    // Check password criteria
                    if (password.length >= 8) strength++;
                    if (/[A-Z]/.test(password)) strength++;
                    if (/[a-z]/.test(password)) strength++;
                    if (/[0-9]/.test(password)) strength++;
                    if (/[^A-Za-z0-9]/.test(password)) strength++;
                    
                    // Determine strength level
                    if (strength >= 4) {
                        strengthClass = 'strong';
                    } else if (strength >= 3) {
                        strengthClass = 'medium';
                    } else if (password.length > 0) {
                        strengthClass = 'weak';
                    }
                    
                    // Update strength indicator
                    strengthIndicator.className = `password-strength password-strength-${strengthClass}`;
                });
            }
            
            // Avatar change handler
            window.handleAvatarChange = function(input) {
                if (input.files && input.files[0]) {
                    const file = input.files[0];
                    
                    // Validate file type
                    if (!file.type.match('image.*')) {
                        alert('Please select a valid image file.');
                        return;
                    }
                    
                    // Validate file size (max 2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Please select an image smaller than 2MB.');
                        return;
                    }
                    
                    // Preview image
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const avatar = input.closest('.avatar-container').querySelector('.avatar');
                        avatar.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                    
                    // Submit form
                    document.getElementById('profileForm').submit();
                }
            };
            
            // Form validation
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('.premium-btn');
                    if (submitBtn) {
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                        submitBtn.disabled = true;
                    }
                });
            });
            
            // Auto-hide alerts
            setTimeout(() => {
                document.querySelectorAll('.alert').forEach(alert => {
                    if (alert.querySelector('.btn-close')) {
                        alert.querySelector('.btn-close').click();
                    }
                });
            }, 5000);
        });
    </script>
@endsection