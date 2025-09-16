@extends('layouts.admin')
@section('page-title')
    {{ __('Coupons') }}
@endsection
@push('script-page')
    <script>
        $(document).on('click', '.code', function() {
            var type = $(this).val();
            if (type == 'manual') {
                $('#manual').removeClass('d-none');
                $('#manual').addClass('d-block');
                $('#auto').removeClass('d-block');
                $('#auto').addClass('d-none');
            } else {
                $('#auto').removeClass('d-none');
                $('#auto').addClass('d-block');
                $('#manual').removeClass('d-block');
                $('#manual').addClass('d-none');
            }
        });

        $(document).on('click', '#code-generate', function() {
            var length = 10;
            var result = '';
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            var charactersLength = characters.length;
            for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            $('#auto-code').val(result);
        });
    </script>
@endpush

@push('css-page')
    <style>
        #commonModal{
            padding: 25px!important;
        }

        /* Coupon Code Badge */
        .coupon-code {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(59, 130, 246, 0.1));
            color: var(--primary);
            border: 1px solid rgba(37, 99, 235, 0.2);
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            font-family: 'Monaco', 'Menlo', monospace;
            letter-spacing: 0.5px;
        }

        .coupon-code i {
            font-size: 0.75rem;
        }

        /* Discount Badge */
        .discount-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(52, 211, 153, 0.1));
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: 16px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        /* Usage Stats */
        .usage-stats {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .usage-bar {
            width: 100%;
            height: 6px;
            background: #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
        }

        .usage-fill {
            height: 100%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .usage-text {
            font-size: 0.75rem;
            color: var(--text-secondary);
            text-align: center;
        }

        /* Coupon Name */
        .coupon-name {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.9rem;
            margin: 0;
        }

        .coupon-desc {
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin: 2px 0 0 0;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 24px;
            opacity: 0.3;
            color: var(--text-secondary);
        }

        .empty-state h3 {
            margin-bottom: 12px;
            color: var(--text-primary);
            font-weight: 600;
        }

        .empty-state p {
            margin-bottom: 24px;
            font-size: 0.9rem;
        }

        /* Loading State */
        .loading-row {
            opacity: 0.6;
            pointer-events: none;
            position: relative;
        }

        .loading-row::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            z-index: 999;
        }

        .loading-row::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 1000;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Premium Header --}}
    <div class="page-header-premium fade-in">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="header-text">
                    <h1>{{ __('Manage Coupons') }}</h1>
                    <p>{{ __('Create and manage discount coupons for your customers') }}</p>
                </div>
            </div>
            <div class="header-stats">
                <div class="stat-item">
                    <p class="stat-number">{{ $coupons->count() }}</p>
                    <p class="stat-label">{{ __('Total Coupons') }}</p>
                </div>
                <div class="stat-item">
                    <p class="stat-number">{{ $coupons->where('is_active', 1)->count() }}</p>
                    <p class="stat-label">{{ __('Active') }}</p>
                </div>
                @can('create coupon')
                <div class="stat-item">
                    <a href="#" data-url="{{ route('coupons.create') }}" 
                       data-ajax-popup="true"
                       data-title="{{ __('Create New Coupon') }}" 
                       class="premium-btn">
                        <i class="fa fa-plus"></i> {{ __('Create Coupon') }}
                    </a>
                </div>
                @endcan
            </div>
        </div>
    </div>

    {{-- Premium Table Card --}}
    <div class="premium-table-container fade-in">
        <div class="table-header">
            <div>
                <h2 class="table-title">{{ __('Coupon List') }}</h2>
                <p class="table-subtitle">{{ __('Manage all your discount coupons') }}</p>
            </div>
        </div>
        
        @if($coupons->count() > 0)
            <div class="table-responsive">
                <table class="premium-table table">
                    <thead>
                        <tr>
                            <th>{{ __('Coupon Details') }}</th>
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Discount') }}</th>
                            <th>{{ __('Usage') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coupons as $coupon)
                            <tr class="font-style">
                                <td data-label="{{ __('Coupon Details') }}">
                                    <div class="coupon-name">{{ $coupon->name }}</div>
                                    <div class="coupon-desc">{{ __('Discount Coupon') }}</div>
                                </td>
                                <td data-label="{{ __('Code') }}">
                                    <div class="coupon-code">
                                        <i class="fas fa-tag"></i>
                                        {{ $coupon->code }}
                                    </div>
                                </td>
                                <td data-label="{{ __('Discount') }}">
                                    <div class="discount-badge">
                                        {{ $coupon->discount }}
                                        <i class="fas fa-percent"></i>
                                    </div>
                                </td>
                                <td data-label="{{ __('Usage') }}">
                                    <div class="usage-stats">
                                        @php
                                            $usedCount = $coupon->used_coupon();
                                            $totalLimit = $coupon->limit;
                                            $usagePercent = $totalLimit > 0 ? ($usedCount / $totalLimit) * 100 : 0;
                                        @endphp
                                        <div class="usage-bar">
                                            <div class="usage-fill" style="width: {{ $usagePercent }}%"></div>
                                        </div>
                                        <div class="usage-text">
                                            {{ $usedCount }} / {{ $totalLimit }} {{ __('used') }}
                                        </div>
                                    </div>
                                </td>
                                <td data-label="{{ __('Actions') }}" class="action-btns">
                                    <a href="{{ route('coupons.show', $coupon->id) }}" 
                                       class="action-btn view-btn"
                                       data-toggle="tooltip" 
                                       data-original-title="{{ __('View Details') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('edit coupon')
                                        <a href="#" 
                                           class="action-btn edit-btn"
                                           data-url="{{ route('coupons.edit', $coupon->id) }}" 
                                           data-ajax-popup="true"
                                           data-title="{{ __('Edit Coupon') }}" 
                                           data-toggle="tooltip"
                                           data-original-title="{{ __('Edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('delete coupon')
                                        <a href="#" 
                                           class="action-btn delete-btn" 
                                           data-toggle="tooltip"
                                           data-original-title="{{ __('Delete') }}"
                                           data-confirm="{{ __('Are You Sure?') . '|' . __('This action cannot be undone. Do you want to continue?') }}"
                                           data-confirm-yes="document.getElementById('delete-form-{{ $coupon->id }}').submit();">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <form action="{{ route('coupons.destroy', $coupon->id) }}" 
                                              method="POST"
                                              id="delete-form-{{ $coupon->id }}"
                                              style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-ticket-alt"></i>
                <h3>{{ __('No Coupons Found') }}</h3>
                <p>{{ __('Start by creating your first discount coupon to attract customers.') }}</p>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

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
                        
                        // Execute the action
                        eval(confirmYes);
                    }
                });
            } else {
                // Fallback to native confirm
                if (confirm(title + '\n' + message)) {
                    // Add loading state to row
                    // Execute the action
                    eval(confirmYes);
                }
            }
        }
    });

    // Enhanced usage bar animations
    const usageBars = document.querySelectorAll('.usage-fill');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const bar = entry.target;
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 100);
            }
        });
    });

    usageBars.forEach(bar => {
        observer.observe(bar);
    });

    // Copy coupon code functionality
    document.querySelectorAll('.coupon-code').forEach(codeElement => {
        codeElement.addEventListener('click', function() {
            const code = this.textContent.trim();
            if (navigator.clipboard) {
                navigator.clipboard.writeText(code).then(() => {
                    // Show success message
                    const originalContent = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-check"></i> Copied!';
                    this.style.background = 'linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(52, 211, 153, 0.1))';
                    this.style.color = 'var(--success)';
                    
                    setTimeout(() => {
                        this.innerHTML = originalContent;
                        this.style.background = '';
                        this.style.color = '';
                    }, 2000);
                });
            }
        });
        
        // Add cursor pointer to indicate clickable
        codeElement.style.cursor = 'pointer';
        codeElement.setAttribute('title', 'Click to copy');
    });
});
</script>
@endsection