@extends('layouts.admin')

@push('script-page')
@endpush

{{-- @section('page-title')
    {{__('Manage Coupon Detail')}}
@endsection --}}

@section('content')
<div class="container-fluid">
    {{-- Premium Header - uses styles from custom.css --}}
    <div class="page-header-premium fade-in">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="header-text">
                    <h1>{{ __('Coupon Usage Details') }}</h1>
                    <p>{{ __('Track and monitor coupon usage by customers') }}</p>
                </div>
            </div>
            <div class="header-stats">
                <div class="stat-item">
                    <p class="stat-number">{{ $userCoupons->count() }}</p>
                    <p class="stat-label">{{ __('Total Uses') }}</p>
                </div>
                <div class="stat-item">
                    <p class="stat-number">{{ $userCoupons->unique('user_id')->count() }}</p>
                    <p class="stat-label">{{ __('Unique Users') }}</p>
                </div>
                <div class="stat-item">
                    <a href="{{ url()->previous() }}" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        {{ __('Back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Coupon Information --}}
    @if(isset($coupon))
    <div class="coupon-info fade-in">
        <div class="coupon-icon">
            <i class="fas fa-ticket-alt"></i>
        </div>
        <div class="coupon-details">
            <h4>{{ $coupon->name ?? 'Coupon Details' }}</h4>
            <p>{{ __('Code: ') }}{{ $coupon->code ?? 'N/A' }} | {{ __('Discount: ') }}{{ $coupon->discount ?? 'N/A' }}% | {{ __('Limit: ') }}{{ $coupon->limit ?? 'N/A' }}</p>
        </div>
    </div>
    @endif

    {{-- Premium Table Card - uses styles from custom.css --}}
    <div class="premium-table-container fade-in">
        <div class="table-header">
            <div>
                <h2 class="table-title">{{ __('Usage History') }}</h2>
                <p class="table-subtitle">{{ __('Complete list of coupon usage by customers') }}</p>
            </div>
        </div>
        
        @if($userCoupons->count() > 0)
            <div class="table-responsive">
                <table class="premium-table table">
                    <thead>
                        <tr>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Usage Date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($userCoupons as $userCoupon)
                            <tr class="font-style">
                                <td data-label="{{ __('Customer') }}">
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            {{ !empty($userCoupon->userDetail) ? strtoupper(substr($userCoupon->userDetail->name, 0, 1)) : 'U' }}
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name">
                                                {{ !empty($userCoupon->userDetail) ? $userCoupon->userDetail->name : __('Unknown User') }}
                                            </div>
                                            <div class="user-email">
                                                {{ !empty($userCoupon->userDetail) ? $userCoupon->userDetail->email : __('No email available') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="{{ __('Usage Date') }}">
                                    <div class="date-time" data-full-date="{{ $userCoupon->created_at->format('M d, Y h:i A') }}">
                                        <div class="date-main">
                                            {{ $userCoupon->created_at->format('M d, Y') }}
                                        </div>
                                        <div class="date-sub">
                                            {{ $userCoupon->created_at->format('h:i A') }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-ticket-alt"></i>
                <h3>{{ __('No Usage Records Found') }}</h3>
                <p>{{ __('This coupon has not been used by any customers yet.') }}</p>
                <a href="{{ url()->previous() }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back to Coupons') }}
                </a>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced hover effects for table rows
    const tableRows = document.querySelectorAll('.premium-table tbody tr');
    
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(8px)';
            this.style.boxShadow = '0 4px 12px rgba(37, 99, 235, 0.1)';
        });

        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
            this.style.boxShadow = 'none';
        });
    });

    // Button ripple effect
    const buttons = document.querySelectorAll('.back-btn');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
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

    document.querySelectorAll('.premium-table-container, .coupon-info').forEach(el => {
        observer.observe(el);
    });

    // Enhanced date/time interaction with copy functionality
    const dateTimeElements = document.querySelectorAll('.date-time');
    dateTimeElements.forEach(element => {
        const dateMain = element.querySelector('.date-main');
        const dateSub = element.querySelector('.date-sub');
        const fullDate = element.getAttribute('data-full-date');
        
        if (dateMain && dateSub && fullDate) {
            const originalTime = dateSub.textContent;
            
            element.addEventListener('mouseenter', function() {
                dateSub.textContent = 'Click to copy';
                dateSub.classList.add('copy-hint');
                element.style.cursor = 'pointer';
            });
            
            element.addEventListener('mouseleave', function() {
                if (!dateSub.classList.contains('copied-feedback')) {
                    dateSub.textContent = originalTime;
                    dateSub.classList.remove('copy-hint');
                }
                element.style.cursor = 'default';
            });
            
            element.addEventListener('click', function() {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(fullDate).then(() => {
                        dateSub.textContent = 'Copied!';
                        dateSub.classList.remove('copy-hint');
                        dateSub.classList.add('copied-feedback');
                        
                        setTimeout(() => {
                            dateSub.textContent = originalTime;
                            dateSub.classList.remove('copied-feedback');
                        }, 2000);
                    }).catch(() => {
                        // Fallback for older browsers
                        const textArea = document.createElement('textarea');
                        textArea.value = fullDate;
                        document.body.appendChild(textArea);
                        textArea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textArea);
                        
                        dateSub.textContent = 'Copied!';
                        dateSub.classList.remove('copy-hint');
                        dateSub.classList.add('copied-feedback');
                        
                        setTimeout(() => {
                            dateSub.textContent = originalTime;
                            dateSub.classList.remove('copied-feedback');
                        }, 2000);
                    });
                }
            });
        }
    });

    // Enhanced coupon info animation
    const couponInfo = document.querySelector('.coupon-info');
    if (couponInfo) {
        couponInfo.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 8px 25px rgba(37, 99, 235, 0.15)';
        });

        couponInfo.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    }

    // Add loading state for back button
    const backButtons = document.querySelectorAll('.back-btn');
    backButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (this.href) {
                const icon = this.querySelector('i');
                if (icon) {
                    icon.className = 'fas fa-spinner fa-spin';
                }
                this.style.opacity = '0.7';
                this.style.pointerEvents = 'none';
            }
        });
    });

    // Enhanced table row selection feedback
    tableRows.forEach((row, index) => {
        row.addEventListener('click', function() {
            // Remove previous selection
            tableRows.forEach(r => r.classList.remove('selected'));
            
            // Add selection to current row
            this.classList.add('selected');
            
            // Add subtle pulse effect
            this.style.animation = 'pulse 0.3s ease';
            setTimeout(() => {
                this.style.animation = '';
            }, 300);
        });
    });
});
</script>
@endsection