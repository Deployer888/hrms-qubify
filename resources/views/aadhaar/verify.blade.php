@extends('layouts.admin')
@section('page-title')
    {{-- __('Aadhaar Verification') --}}
@endsection
@php
use App\Helpers\Helper;
use Carbon\Carbon;
$requestType = isset($_GET['type']) ? $_GET['type'] : 'daily';
@endphp

@push('css-page')
<style>
    :root {
        --primary: #2563eb;
        --secondary: #3b82f6;
        --accent: #60a5fa;
        --info: #93c5fd;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        --shadow-md: 0 8px 25px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 15px 35px rgba(0, 0, 0, 0.1);
        --text-primary: #2d3748;
        --text-secondary: #6b7280;
    }

    body {
        background: linear-gradient(135deg, #eef2f6 0%, #d1d9e6 100%);
        min-height: 100vh;
    }

    .content-wrapper {
        background: transparent;
        padding: 0;
    }

    /* Premium Header */
    .page-header-premium {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 20px;
        padding: 24px 32px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-lg);
        position: relative;
        overflow: hidden;
    }
    .page-header-premium::before {
        content: '';
        position: absolute;
        top: -50%; left: -50%;
        width: 200%; height: 200%;
        background: radial-gradient(circle at center, rgba(255,255,255,0.15), transparent 70%);
        animation: rotateBg 20s linear infinite;
    }
    @keyframes rotateBg {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        z-index: 2;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .header-icon {
        width: 56px;
        height: 56px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        backdrop-filter: blur(10px);
    }

    .header-text h1 {
        font-size: 1.75rem;
        font-weight: 800;
        color: #fff;
        margin: 0;
        line-height: 1.2;
    }

    .header-text p {
        color: rgba(255, 255, 255, 0.85);
        margin: 4px 0 0 0;
        font-size: 0.95rem;
        font-weight: 500;
    }

    .header-timer {
        text-align: center;
        color: white;
    }

    .timer-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        line-height: 1;
    }

    .timer-label {
        font-size: 0.8rem;
        opacity: 0.9;
        margin: 4px 0 0 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Premium Cards */
    .premium-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: var(--shadow);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: none;
        margin-bottom: 16px;
        height: fit-content;
    }
    .premium-card::after {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 4px; height: 100%;
        background: linear-gradient(180deg, var(--primary), var(--secondary));
    }
    .premium-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .card-header-premium {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        padding: 16px 20px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .premium-card-body {
        padding: 20px;
    }

    /* Compact container for better space usage */
    .container-fluid {
        margin: 0 auto;
        padding: 0 16px;
    }

    /* Verification Steps */
    .verification-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 24px;
        position: relative;
        padding: 0 20px;
    }

    .verification-steps::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 40px;
        right: 40px;
        height: 2px;
        background: #e5e7eb;
        z-index: 1;
    }

    .verification-steps::after {
        content: '';
        position: absolute;
        top: 20px;
        left: 40px;
        width: 66.66%;
        height: 2px;
        background: linear-gradient(90deg, var(--success), var(--primary));
        z-index: 2;
        animation: progressFill 0.8s ease;
    }

    @keyframes progressFill {
        from { width: 33.33%; }
        to { width: 66.66%; }
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 3;
        background: white;
        padding: 0 12px;
    }

    .step-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e5e7eb;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        margin-bottom: 8px;
        transition: all 0.3s ease;
    }

    .step.completed .step-icon {
        background: linear-gradient(135deg, var(--success), #059669);
        color: white;
    }

    .step.active .step-icon {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        animation: pulse 2s infinite;
    }

    .step-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-secondary);
        text-align: center;
    }

    .step.completed .step-label,
    .step.active .step-label {
        color: var(--primary);
    }

    /* Main OTP Section - Better space utilization */
    .otp-main-section {
        background: linear-gradient(135deg, #f8fafc, #ffffff);
        border-radius: 16px;
        padding: 32px;
        text-align: center;
        margin-bottom: 20px;
        box-shadow: var(--shadow);
        min-height: 400px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .otp-header {
        margin-bottom: 32px;
    }

    .otp-header h2 {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 12px;
    }

    .otp-header p {
        font-size: 1.1rem;
        color: var(--text-secondary);
        margin: 0;
        line-height: 1.6;
    }

    /* Enhanced OTP Input */
    .otp-input-section {
        margin: 40px 0;
    }

    .otp-input {
        display: flex;
        justify-content: center;
        gap: 16px;
        margin: 32px 0;
    }

    .otp-digit {
        width: 70px;
        height: 70px;
        border: 3px solid #e5e7eb;
        border-radius: 16px;
        text-align: center;
        font-size: 1.8rem;
        font-weight: 700;
        transition: all 0.3s ease;
        background: #fff;
        box-shadow: var(--shadow);
    }

    .otp-digit:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        outline: none;
        transform: translateY(-4px) scale(1.05);
    }

    .otp-digit.filled {
        border-color: var(--success);
        background: rgba(16, 185, 129, 0.05);
        transform: scale(1.02);
    }

    /* Button Section */
    .button-section {
        margin-top: 32px;
    }

    .premium-btn {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border: none;
        color: white;
        padding: 18px 40px;
        border-radius: 16px;
        font-weight: 700;
        font-size: 1.1rem;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        transition: all 0.3s ease;
        text-decoration: none;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        margin: 0 12px 16px 12px;
        min-width: 180px;
        justify-content: center;
    }
    .premium-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s ease;
    }
    .premium-btn:hover::before {
        left: 100%;
    }
    .premium-btn:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg);
        color: white;
    }
    .premium-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .premium-btn-outline {
        background: transparent;
        border: 3px solid var(--text-secondary);
        color: var(--text-secondary);
        padding: 15px 36px;
        border-radius: 16px;
        font-weight: 700;
        font-size: 1.1rem;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        transition: all 0.3s ease;
        text-decoration: none;
        margin: 0 12px 16px 12px;
        min-width: 160px;
        justify-content: center;
    }
    .premium-btn-outline:hover {
        border-color: var(--primary);
        color: var(--primary);
        transform: translateY(-3px);
        box-shadow: var(--shadow);
    }

    /* Resend Section */
    .resend-section {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.05), rgba(59, 130, 246, 0.02));
        border-radius: 16px;
        padding: 24px;
        margin-top: 24px;
        border: 2px solid rgba(37, 99, 235, 0.1);
    }

    .resend-section h4 {
        color: var(--text-primary);
        margin-bottom: 12px;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .resend-btn {
        background: linear-gradient(135deg, var(--warning), #d97706);
        border: none;
        color: white;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1rem;
    }

    .resend-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }

    .resend-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Sidebar optimizations */
    .sidebar-card {
        margin-bottom: 16px;
        height: fit-content;
    }

    .sidebar-card .premium-card-body {
        padding: 16px;
    }

    /* Info Cards */
    .info-card {
        background: linear-gradient(135deg, #fff, #f8fafc);
        border-radius: 12px;
        padding: 16px;
        border-left: 4px solid var(--primary);
        transition: all 0.3s ease;
        margin-bottom: 12px;
    }

    .info-card:hover {
        transform: translateX(4px);
        box-shadow: var(--shadow);
    }

    .info-card-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        margin-bottom: 10px;
    }

    .info-card-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 4px 0;
    }

    .info-card-desc {
        font-size: 0.8rem;
        color: var(--text-secondary);
        line-height: 1.4;
        margin: 0;
    }

    /* Instructions list */
    .instruction-item {
        display: flex;
        align-items: start;
        margin-bottom: 16px;
        padding: 12px;
        background: rgba(37, 99, 235, 0.02);
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .instruction-item:hover {
        background: rgba(37, 99, 235, 0.05);
        transform: translateX(2px);
    }

    .instruction-number {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 700;
        margin-right: 12px;
        flex-shrink: 0;
    }

    .instruction-content h6 {
        margin: 0 0 4px 0;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .instruction-content small {
        color: var(--text-secondary);
        font-size: 0.8rem;
        line-height: 1.4;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            gap: 16px;
            text-align: center;
        }
        
        .otp-main-section {
            padding: 24px;
            margin-bottom: 16px;
        }
        
        .verification-steps {
            flex-direction: column;
            gap: 16px;
            padding: 0;
        }
        
        .verification-steps::before,
        .verification-steps::after {
            display: none;
        }
        
        .otp-digit {
            width: 50px;
            height: 50px;
            font-size: 1.4rem;
        }

        .otp-input {
            gap: 8px;
        }

        .premium-btn,
        .premium-btn-outline {
            display: block;
            margin: 8px 0;
            width: 100%;
        }
    }

    /* Animation */
    .fade-in {
        animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    /* Ensure equal heights */
    .row-equal-height {
        display: flex;
        flex-wrap: wrap;
    }

    .row-equal-height > [class*="col-"] {
        display: flex;
        flex-direction: column;
    }

    .sidebar-section {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
</style>
@endpush

@section('action-button')
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Premium Header --}}
<div class="page-header-premium">
    <div class="header-content">
        <div class="header-left">
            <div class="header-icon">
                <i class="fas fa-mobile-alt"></i>
            </div>
            <div class="header-text">
                <h1>{{ __('OTP Verification') }}</h1>
                <p>{{ __('Secure One-Time Password Authentication') }}</p>
            </div>
        </div>
        <div class="header-timer">
            <p class="timer-value" id="countdown">05:00</p>
            <p class="timer-label">{{ __('Time Remaining') }}</p>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row row-equal-height">
        <!-- Main OTP Section -->
        <div class="col-lg-8">
            {{-- Verification Steps --}}
            <div class="premium-card fade-in">
                <div class="premium-card-body" style="padding: 16px 20px;">
                    <div class="verification-steps">
                        <div class="step completed">
                            <div class="step-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <span class="step-label">{{ __('Details Entered') }}</span>
                        </div>
                        <div class="step completed">
                            <div class="step-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <span class="step-label">{{ __('OTP Sent') }}</span>
                        </div>
                        <div class="step active">
                            <div class="step-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <span class="step-label">{{ __('Enter OTP') }}</span>
                        </div>
                        <div class="step">
                            <div class="step-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <span class="step-label">{{ __('Verified') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main OTP Input Section --}}
            <div class="otp-main-section fade-in">
                <div class="otp-header">
                    <h2>{{ __('Enter Verification Code') }}</h2>
                    <p>{{ __('We have sent a 6-digit verification code to your registered mobile number. Please enter the code below to complete your identity verification.') }}</p>
                </div>

                <form action="{{ route('aadhaar.verify.otp.post') }}" method="POST" id="otpForm">
                    @csrf
                    
                    <div class="otp-input-section">
                        {{-- Modern OTP Input --}}
                        <div class="otp-input" id="otpInputs">
                            <input type="text" class="otp-digit" maxlength="1" data-index="0" placeholder="•">
                            <input type="text" class="otp-digit" maxlength="1" data-index="1" placeholder="•">
                            <input type="text" class="otp-digit" maxlength="1" data-index="2" placeholder="•">
                            <input type="text" class="otp-digit" maxlength="1" data-index="3" placeholder="•">
                            <input type="text" class="otp-digit" maxlength="1" data-index="4" placeholder="•">
                            <input type="text" class="otp-digit" maxlength="1" data-index="5" placeholder="•">
                        </div>
                        
                        {{-- Hidden traditional input for form submission --}}
                        <input type="hidden" id="otp" name="otp" required>
                    </div>
                    
                    {{-- Submit Buttons --}}
                    <div class="button-section">
                        <button type="submit" class="premium-btn" id="verifyBtn" disabled>
                            <i class="fas fa-check-circle"></i>
                            <span id="verifyOtpText">{{ __('Verify OTP') }}</span>
                            <span id="verifyOtpSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                        <a href="{{ route('aadhaar.index') }}" class="premium-btn-outline">
                            <i class="fas fa-arrow-left"></i>
                            {{ __('Go Back') }}
                        </a>
                    </div>
                </form>
                
                {{-- Resend Section --}}
                <div class="resend-section">
                    <h4>{{ __("Didn't receive the code?") }}</h4>
                    <p class="mb-3 text-muted">{{ __('If you haven\'t received the OTP within 2 minutes, you can request a new one.') }}</p>
                    <button class="resend-btn" id="resendBtn" disabled>
                        <i class="fas fa-redo"></i>
                        {{ __('Resend OTP') }} (<span id="resendTimer">60</span>s)
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Information -->
        <div class="col-lg-4 sidebar-section">
            {{-- Instructions --}}
            <div class="premium-card sidebar-card fade-in">
                <div class="card-header-premium">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle text-info"></i>
                        {{ __('How to Verify') }}
                    </h3>
                </div>
                <div class="premium-card-body">
                    <div class="instruction-item">
                        <div class="instruction-number">1</div>
                        <div class="instruction-content">
                            <h6>{{ __('Check Your Phone') }}</h6>
                            <small>{{ __('Look for SMS with 6-digit verification code') }}</small>
                        </div>
                    </div>
                    <div class="instruction-item">
                        <div class="instruction-number">2</div>
                        <div class="instruction-content">
                            <h6>{{ __('Enter the Code') }}</h6>
                            <small>{{ __('Type each digit in the boxes above') }}</small>
                        </div>
                    </div>
                    <div class="instruction-item">
                        <div class="instruction-number">3</div>
                        <div class="instruction-content">
                            <h6>{{ __('Verify Identity') }}</h6>
                            <small>{{ __('Complete the authentication process') }}</small>
                        </div>
                    </div>
                    <div class="instruction-item">
                        <div class="instruction-number">4</div>
                        <div class="instruction-content">
                            <h6>{{ __('Access Granted') }}</h6>
                            <small>{{ __('Proceed with Aadhaar verification') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Security Information --}}
            <div class="premium-card sidebar-card fade-in">
                <div class="card-header-premium">
                    <h3 class="card-title">
                        <i class="fas fa-shield-alt text-success"></i>
                        {{ __('Security Features') }}
                    </h3>
                </div>
                <div class="premium-card-body">
                    <div class="info-card">
                        <div class="info-card-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h4 class="info-card-title">{{ __('Time-Limited') }}</h4>
                        <p class="info-card-desc">{{ __('OTP expires in 5 minutes for enhanced security') }}</p>
                    </div>

                    <div class="info-card">
                        <div class="info-card-icon" style="background: rgba(37, 99, 235, 0.1); color: var(--primary);">
                            <i class="fas fa-random"></i>
                        </div>
                        <h4 class="info-card-title">{{ __('Unique Code') }}</h4>
                        <p class="info-card-desc">{{ __('Each OTP is generated uniquely and can only be used once') }}</p>
                    </div>

                    <div class="info-card">
                        <div class="info-card-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);">
                            <i class="fas fa-eye-slash"></i>
                        </div>
                        <h4 class="info-card-title">{{ __('Privacy Protected') }}</h4>
                        <p class="info-card-desc">{{ __('Your OTP is transmitted through secure encrypted channels') }}</p>
                    </div>
                </div>
            </div>

            {{-- Troubleshooting --}}
            <div class="premium-card sidebar-card fade-in">
                <div class="card-header-premium">
                    <h3 class="card-title">
                        <i class="fas fa-question-circle text-warning"></i>
                        {{ __('Need Help?') }}
                    </h3>
                </div>
                <div class="premium-card-body">
                    <div class="mb-3">
                        <h6 class="mb-2" style="color: var(--text-primary); font-size: 0.9rem;">{{ __('Code not received?') }}</h6>
                        <ul class="small text-muted list-unstyled" style="margin-left: 8px;">
                            <li style="margin-bottom: 4px;">• {{ __('Check your spam/junk folder') }}</li>
                            <li style="margin-bottom: 4px;">• {{ __('Ensure good network connectivity') }}</li>
                            <li style="margin-bottom: 4px;">• {{ __('Wait for the resend timer to expire') }}</li>
                        </ul>
                    </div>
                    <div class="mb-3">
                        <h6 class="mb-2" style="color: var(--text-primary); font-size: 0.9rem;">{{ __('Code expired?') }}</h6>
                        <ul class="small text-muted list-unstyled" style="margin-left: 8px;">
                            <li style="margin-bottom: 4px;">• {{ __('Request a new OTP') }}</li>
                            <li style="margin-bottom: 4px;">• {{ __('Complete verification within 5 minutes') }}</li>
                        </ul>
                    </div>
                    <div class="text-center" style="background: rgba(37, 99, 235, 0.05); padding: 12px; border-radius: 8px;">
                        <small class="text-muted">{{ __('Need help? Contact system administrator') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const otpInputs = document.querySelectorAll('.otp-digit');
    const hiddenOtpInput = document.getElementById('otp');
    const verifyBtn = document.getElementById('verifyBtn');
    const verifyOtpText = document.getElementById('verifyOtpText');
    const verifyOtpSpinner = document.getElementById('verifyOtpSpinner');
    const resendBtn = document.getElementById('resendBtn');
    const countdownElement = document.getElementById('countdown');
    const resendTimerElement = document.getElementById('resendTimer');
    
    let otpValues = ['', '', '', '', '', ''];
    let countdownTime = 300; // 5 minutes
    let resendTime = 60; // 60 seconds

    // Initialize timers
    startCountdown();
    startResendTimer();

    // OTP Input Handlers
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            const value = e.target.value;
            
            // Only allow numbers
            if (!/^\d*$/.test(value)) {
                e.target.value = '';
                return;
            }
            
            otpValues[index] = value;
            updateHiddenInput();
            updateInputStyles();
            
            // Auto-focus next input
            if (value && index < 5) {
                otpInputs[index + 1].focus();
            }
            
            // Auto-submit when all digits entered
            if (otpValues.every(val => val !== '') && otpValues.join('').length === 6) {
                setTimeout(() => {
                    document.getElementById('otpForm').submit();
                }, 200);
            }
        });

        input.addEventListener('keydown', (e) => {
            // Handle backspace
            if (e.key === 'Backspace' && !input.value && index > 0) {
                otpInputs[index - 1].focus();
                otpValues[index - 1] = '';
                otpInputs[index - 1].value = '';
                updateHiddenInput();
                updateInputStyles();
            }
            
            // Handle arrow keys
            if (e.key === 'ArrowLeft' && index > 0) {
                otpInputs[index - 1].focus();
            } else if (e.key === 'ArrowRight' && index < 5) {
                otpInputs[index + 1].focus();
            }
        });

        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pastedData = e.clipboardData.getData('text').replace(/\D/g, '');
            
            if (pastedData.length === 6) {
                for (let i = 0; i < 6; i++) {
                    otpInputs[i].value = pastedData[i] || '';
                    otpValues[i] = pastedData[i] || '';
                }
                updateHiddenInput();
                updateInputStyles();
                
                // Auto-submit on paste
                setTimeout(() => {
                    document.getElementById('otpForm').submit();
                }, 200);
            }
        });
    });

    function updateHiddenInput() {
        hiddenOtpInput.value = otpValues.join('');
        verifyBtn.disabled = hiddenOtpInput.value.length !== 6;
    }

    function updateInputStyles() {
        otpInputs.forEach((input, index) => {
            if (otpValues[index]) {
                input.classList.add('filled');
            } else {
                input.classList.remove('filled');
            }
        });
    }

    // Countdown Timer
    function startCountdown() {
        const interval = setInterval(() => {
            const minutes = Math.floor(countdownTime / 60);
            const seconds = countdownTime % 60;
            countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (countdownTime <= 0) {
                clearInterval(interval);
                // Handle expiry
                alert('{{ __("OTP has expired. Please request a new one.") }}');
                window.location.href = '{{ route("aadhaar.index") }}';
            }
            
            countdownTime--;
        }, 1000);
    }

    // Resend Timer
    function startResendTimer() {
        const interval = setInterval(() => {
            resendTimerElement.textContent = resendTime;
            
            if (resendTime <= 0) {
                clearInterval(interval);
                resendBtn.disabled = false;
                resendBtn.innerHTML = '<i class="fas fa-redo"></i> {{ __("Resend OTP") }}';
            }
            
            resendTime--;
        }, 1000);
    }

    // Form submission
    document.getElementById('otpForm').addEventListener('submit', function(e) {
        if (hiddenOtpInput.value.length !== 6) {
            e.preventDefault();
            alert('{{ __("Please enter the complete 6-digit OTP") }}');
            return;
        }
        
        // Show loading state
        verifyBtn.disabled = true;
        verifyOtpSpinner.classList.remove('d-none');
        verifyOtpText.textContent = '{{ __("Verifying...") }}';
    });

    // Resend OTP
    resendBtn.addEventListener('click', function() {
        if (!this.disabled) {
            // Reset form
            otpInputs.forEach((input, index) => {
                input.value = '';
                otpValues[index] = '';
            });
            updateHiddenInput();
            updateInputStyles();
            
            // Reset timers
            resendTime = 60;
            this.disabled = true;
            startResendTimer();
            
            // Show success message
            alert('{{ __("New OTP has been sent to your mobile number") }}');
            
            // Focus first input
            otpInputs[0].focus();
        }
    });

    // Auto-focus first input
    otpInputs[0].focus();
});
</script>
@endsection

@push('script-page')
@endpush