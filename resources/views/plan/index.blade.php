@extends('layouts.admin')
@section('page-title')
    {{ __('Plans') }}
@endsection
@section('content')
<div class="container-fluid">
    {{-- Premium Header --}}
    <div class="page-header-premium fade-in">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="header-text">
                    <h1 class="page-title-compact">{{ __('Manage Plans') }}</h1>
                    <p class="page-subtitle-compact">{{ __('Choose the perfect plan for your organization') }}</p>
                </div>
            </div>
            <div class="header-stats">
                <div class="stat-item">
                    <p class="stat-number">{{ $plans->count() }}</p>
                    <p class="stat-label text-light">{{ __('Available Plans') }}</p>
                </div>
                @can('Create Plan')
                @if (
                    !empty($admin_payment_setting) &&
                        (($admin_payment_setting['is_stripe_enabled'] == 'on' &&
                            !empty($admin_payment_setting['stripe_key']) &&
                            !empty($admin_payment_setting['stripe_secret'])) ||
                            ($admin_payment_setting['is_paypal_enabled'] == 'on' &&
                                !empty($admin_payment_setting['paypal_client_id']) &&
                                !empty($admin_payment_setting['paypal_secret_key']))))
                <div class="stat-item">
                    <a href="#" data-url="{{ route('plans.create') }}" data-ajax-popup="true" 
                       data-title="{{ __('Create New Plan') }}"
                       class="premium-btn">
                        <i class="fa fa-plus"></i> {{ __('Create') }}
                    </a>
                </div>
                @endif
                @endcan
            </div>
        </div>
    </div>

    @php
        $activePlanId = \Auth::user()->plan;
        $active = $activePlanId ? true : false;
    @endphp

    @if($plans->count() > 0)
        <div class="row gx-4 gy-4 row-equal-height">
            @foreach ($plans as $plan)
            @php
                $isCompany = \Auth::user()->type == 'company';
                $isRecommended = false;
                $isActive = false;
                
                if(\Auth::user()->plan == $plan->id){
                    $isActive = true;
                }
                else if(!$active && $isCompany && \Auth::user()->employees_count == $plan->max_employees){
                    $isRecommended = true;
                }
            @endphp
            <div class="col-lg-3 col-md-6 col-sm-12 fade-in plan-card-container {{ $isRecommended || $isActive || \Auth::user()->type == 'super admin' ? '' : 'plan-blur' }}" 
                 style="animation-delay: {{ $loop->index * 0.1 }}s">
                <div class="premium-card plan-card {{ $isRecommended ? 'plan-recommended' : '' }} {{ $isActive ? 'plan-active' : '' }}">
                    {{-- Plan Status Badge --}}
                    @if($isRecommended && !$active)
                    <div class="plan-status-badge recommended-badge">
                        <i class="fas fa-star"></i>
                        {{ __('Recommended') }}
                    </div>
                    @endif
                    @if($isActive)
                    <div class="plan-status-badge active-badge">
                        <i class="fas fa-check-circle"></i>
                        {{ __('Active') }}
                    </div>
                    @endif

                    {{-- Actions Dropdown --}}
                    @if (Gate::check('Edit Plan') && \Auth::user()->type == 'super admin')
                    <div class="actions-dropdown">
                        <div class="dropdown">
                            <button class="actions-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" data-ajax-popup="true" 
                                       data-url="{{ route('plans.edit', $plan->id) }}" 
                                       data-title="{{ __('Edit Plan') }}"
                                       class="dropdown-item">
                                        <i class="fas fa-edit"></i>
                                        {{ __('Edit') }}
                                    </a>
                                </li>
                                @if($plan->id != 1)
                                <li>
                                    <a href="#" class="dropdown-item text-danger delete-plan" 
                                       data-plan-id="{{ $plan->id }}"
                                       data-plan-name="{{ $plan->name }}">
                                        <i class="fas fa-trash"></i>
                                        {{ __('Delete') }}
                                    </a>
                                    <form id="delete-form-{{ $plan->id }}" action="{{ route('plans.destroy', $plan->id) }}" method="POST" style="display: none;">
                                        @csrf 
                                        @method('DELETE')
                                    </form>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    @endif

                    <div class="premium-card-body" style="padding-top: {{ Gate::check('Edit Plan') && \Auth::user()->type == 'super admin' ? '70px' : '32px' }}">
                        <div>
                            {{-- Plan Icon --}}
                            <div class="plan-icon-wrapper">
                                <div class="plan-icon">
                                    @if($plan->price == 0)
                                        <i class="fas fa-gift"></i>
                                    @elseif($isRecommended)
                                        <i class="fas fa-crown"></i>
                                    @else
                                        <i class="fas fa-rocket"></i>
                                    @endif
                                </div>
                            </div>

                            {{-- Plan Info --}}
                            <h5 class="user-name">{{ $plan->name }}</h5>
                            <div class="plan-price">
                                <span class="price-currency">{{ (!empty(env('CURRENCY_SYMBOL')) ? env('CURRENCY_SYMBOL') : '$') }}</span>
                                <span class="price-amount">{{ $plan->price }}</span>
                                @if($plan->price > 0)
                                <span class="price-duration">/ {{ ucfirst($plan->duration) }}</span>
                                @elseif($plan->duration == '2_weeks')
                                <span class="price-duration">for 2 Weeks</span>
                                @else
                                <span class="price-duration">for a {{ ucfirst($plan->duration) }}</span>
                                @endif
                            </div>

                            {{-- Plan Features --}}
                            <div class="plan-features">
                                <div class="feature-item">
                                    <i class="fas fa-users feature-icon"></i>
                                    <span>{{ $plan->max_users == -1 ? __('Unlimited') : $plan->max_users }} {{ __('Users') }}</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-user-tie feature-icon"></i>
                                    <span>{{ $plan->max_employees == -1 ? __('Unlimited') : $plan->max_employees }} {{ __('Employees') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Plan Actions --}}
                        <div class="plan-actions">
                            @if(\Auth::user()->type != 'super admin')
                                @if($isActive)
                                    <div class="current-plan-badge">
                                        <i class="fas fa-check-circle"></i>
                                        {{ __('Current Plan') }}
                                    </div>
                                    @if(\Auth::user()->type == 'company' && \Auth::user()->plan_expire_date)
                                    <div class="plan-expires">
                                        <i class="fas fa-clock"></i>
                                        {{ __('Expires: ') }}{{ \Auth::user()->dateFormat(\Auth::user()->plan_expire_date) }}
                                    </div>
                                    @endif
                                @else
                                    {{-- Purchase/Request Actions --}}
                                    @if (
                                        (!empty($admin_payment_setting) &&
                                            ($admin_payment_setting['is_stripe_enabled'] == 'on' ||
                                                $admin_payment_setting['is_paypal_enabled'] == 'on' ||
                                                $admin_payment_setting['is_paystack_enabled'] == 'on' ||
                                                $admin_payment_setting['is_flutterwave_enabled'] == 'on' ||
                                                $admin_payment_setting['is_razorpay_enabled'] == 'on' ||
                                                $admin_payment_setting['is_mercado_enabled'] == 'on' ||
                                                $admin_payment_setting['is_paytm_enabled'] == 'on' ||
                                                $admin_payment_setting['is_mollie_enabled'] == 'on' ||
                                                $admin_payment_setting['is_paypal_enabled'] == 'on' ||
                                                $admin_payment_setting['is_skrill_enabled'] == 'on' ||
                                                $admin_payment_setting['is_coingate_enabled'] == 'on')) ||
                                            (isset($admin_payment_setting['is_paymentwall_enabled']) && $admin_payment_setting['is_paymentwall_enabled'] == 'on'))
                                        @can('Buy Plan')
                                            @if($plan->price > 0)
                                                <a href="{{ route('stripe', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)) }}"
                                                   class="premium-btn plan-action-btn">
                                                    <i class="fas fa-credit-card"></i>
                                                    {{ __('Buy Plan') }}
                                                </a>
                                            @else
                                                <a href="{{ route('stripe', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)) }}"
                                                   class="premium-btn plan-action-btn plan-free-btn">
                                                    <i class="fas fa-gift"></i>
                                                    {{ __('Get Free') }}
                                                </a>
                                            @endif
                                        @endcan
                                    @endif

                                    {{-- Plan Request --}}
                                    @if($plan->id != 1)
                                        @if(\Auth::user()->requested_plan != $plan->id)
                                            <a href="{{ route('plan_request', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)) }}"
                                               class="upgrade-link">
                                                <i class="fas fa-paper-plane"></i>
                                                {{ __('Request Plan') }}
                                            </a>
                                        @else
                                            <div class="requested-badge">
                                                <i class="fas fa-clock"></i>
                                                {{ __('Request Pending') }}
                                            </div>
                                        @endif
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="premium-card fade-in">
            <div class="premium-card-body">
                <div class="empty-state">
                    <i class="fas fa-credit-card"></i>
                    <h3>{{ __('No Plans Found') }}</h3>
                    <p>{{ __('Start by creating your first subscription plan.') }}</p>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Organizational Info Modal --}}
<div id="organizationalInfoModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="organizationalInfoModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- Modal Header with Premium Design --}}
            <div class="page-header-premium" style="margin-bottom: 0; border-radius: 24px 24px 0 0;">
                <div class="header-content" style="justify-content: space-between;">
                    <div class="header-left">
                        <div class="header-icon" style="width: 56px; height: 56px; font-size: 1.4rem;">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="header-text">
                            <h1 style="font-size: 1.6rem; margin: 0;">{{ __('Organization Info') }}</h1>
                            <p style="margin: 4px 0 0 0; font-size: 0.85rem;">{{ __('Complete your organization details') }}</p>
                        </div>
                    </div>
                    <div class="header-stats">
                        <a href="{{ route('logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                           class="actions-btn" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal Body --}}
            <div style="background: white; padding: 32px; border-radius: 0 0 24px 24px;">
                <form id="organizationalInfoForm">
                    <div class="row gx-4 gy-4">
                        <div class="col-lg-6">
                            <label class="modal-form-label">{{ __('Name') }}</label>
                            <div class="field-validation position-relative">
                                <input type="text" class="form-control modal-form-input" value="{{ Auth::user()->name }}" readonly disabled>
                                <i class="fas fa-user modal-input-icon"></i>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label class="modal-form-label">{{ __('Email') }}</label>
                            <div class="field-validation position-relative">
                                <input type="email" class="form-control modal-form-input" value="{{ Auth::user()->email }}" readonly disabled>
                                <i class="fas fa-envelope modal-input-icon"></i>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <label class="modal-form-label required">{{ __('Company Name') }}</label>
                            <div class="field-validation position-relative">
                                <input type="text" class="form-control modal-form-input" id="company_name" required>
                                <i class="fas fa-building modal-input-icon"></i>
                                <i class="fas fa-check-circle validation-icon"></i>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <label class="modal-form-label required">{{ __('Employees Count') }}</label>
                            <div class="field-validation position-relative">
                                <select class="form-control modal-form-select" id="employees_count" required>
                                    <option value="">{{ __('Select employee count') }}</option>
                                    <option value="10">{{ __('Up to 10') }}</option>
                                    <option value="50">{{ __('Up to 50') }}</option>
                                    <option value="100">{{ __('Up to 100') }}</option>
                                    <option value="150">{{ __('Up to 150') }}</option>
                                    <option value="200">{{ __('Up to 200') }}</option>
                                </select>
                                <i class="fas fa-check-circle validation-icon validation-icon-select"></i>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="modal-button-container">
                                <button type="button" id="saveButton" class="modal-btn modal-btn-submit">
                                    <i class="fas fa-save"></i>
                                    {{ __('Save Information') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    var authUserType = "{{ \Auth::user()->type }}";
    if(authUserType == 'super admin'){
        document.querySelectorAll('.plan-blur').forEach(function(el) {
            el.classList.remove('plan-blur');
        });
    }
    
    let userType = "{{ Auth::user()->type }}";
    let companyName = "{{ Auth::user()->company_name }}";
    let employeesCount = "{{ Auth::user()->employees_count }}";

    // Show organizational info modal for companies without complete info
    if (userType === 'company' && (!companyName || !employeesCount)) {
        $('#organizationalInfoModal').modal('show');
        $('body').addClass('modal-open');
    }

    // Save organizational info
    $('#saveButton').click(function () {
        let company_name = $('#company_name').val();
        let employees_count = $('#employees_count').val();

        if (company_name && employees_count) {
            $(this).addClass('loading');
            $(this).prop('disabled', true);
            
            $.ajax({
                url: '{{ route('update.organization.info') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    company_name: company_name,
                    employees_count: employees_count
                },
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error saving information. Please try again.');
                        $('#saveButton').removeClass('loading').prop('disabled', false);
                    }
                },
                error: function() {
                    alert('Error saving information. Please try again.');
                    $('#saveButton').removeClass('loading').prop('disabled', false);
                }
            });
        } else {
            alert('Please fill in all required fields.');
        }
    });

    // Plan card interactions for companies
    if (userType === 'company') {
        const planCards = document.querySelectorAll('.plan-card');

        planCards.forEach(card => {
            card.addEventListener('click', function() {
                // Remove blur from all cards and reset recommended state
                planCards.forEach(c => {
                    const container = c.closest('.plan-card-container');
                    if (c !== this) {
                        container.classList.add('plan-blur');
                        c.classList.remove('plan-recommended');
                    } else {
                        container.classList.remove('plan-blur');
                        if (!c.classList.contains('plan-active')) {
                            c.classList.add('plan-recommended');
                        }
                    }
                });
            });
        });
    }

    // Enhanced delete confirmation for plans
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-plan')) {
            e.preventDefault();
            const deleteBtn = e.target.closest('.delete-plan');
            const planId = deleteBtn.dataset.planId;
            const planName = deleteBtn.dataset.planName;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you want to delete the plan "${planName}"? This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete plan!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const card = deleteBtn.closest('.premium-card');
                        if (card) {
                            card.classList.add('loading-card');
                        }
                        document.getElementById(`delete-form-${planId}`).submit();
                    }
                });
            } else {
                const confirmMessage = `Are you sure you want to delete the plan "${planName}"? This action cannot be undone.`;
                if (confirm(confirmMessage)) {
                    const card = deleteBtn.closest('.premium-card');
                    if (card) {
                        card.classList.add('loading-card');
                    }
                    document.getElementById(`delete-form-${planId}`).submit();
                }
            }
        }
    });

    // Dropdown animations (same as users index)
    const dropdowns = document.querySelectorAll('.dropdown');
    dropdowns.forEach(dropdown => {
        const menu = dropdown.querySelector('.dropdown-menu');
        const button = dropdown.querySelector('[data-bs-toggle="dropdown"]');
        
        if (button && menu) {
            button.addEventListener('show.bs.dropdown', function() {
                menu.style.transform = 'translateY(-10px)';
                menu.style.opacity = '0';
            });
            
            button.addEventListener('shown.bs.dropdown', function() {
                menu.style.transform = 'translateY(0)';
                menu.style.opacity = '1';
            });
            
            button.addEventListener('hide.bs.dropdown', function() {
                menu.style.transform = 'translateY(-10px)';
                menu.style.opacity = '0';
            });
        }
    });

    // Form validation for modal
    const formFields = document.querySelectorAll('#organizationalInfoForm .modal-form-input, #organizationalInfoForm .modal-form-select');
    
    function updateFormValidation() {
        formFields.forEach(field => {
            const validationContainer = field.closest('.field-validation');
            if (field.hasAttribute('required') && field.value.trim() !== '') {
                validationContainer.classList.add('valid');
                validationContainer.classList.remove('invalid');
            } else if (field.hasAttribute('required')) {
                validationContainer.classList.remove('valid');
            }
        });
    }

    formFields.forEach(field => {
        field.addEventListener('input', updateFormValidation);
        field.addEventListener('change', updateFormValidation);
        
        field.addEventListener('focus', function() {
            this.closest('.field-validation').classList.add('focused');
        });
        
        field.addEventListener('blur', function() {
            this.closest('.field-validation').classList.remove('focused');
        });
    });

    updateFormValidation();
});
</script>

@endsection