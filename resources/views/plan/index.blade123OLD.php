@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Plan') }}
@endsection

@section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        @can('Create Plan')
            @if (
                !empty($admin_payment_setting) &&
                    (($admin_payment_setting['is_stripe_enabled'] == 'on' &&
                        !empty($admin_payment_setting['stripe_key']) &&
                        !empty($admin_payment_setting['stripe_secret'])) ||
                        ($admin_payment_setting['is_paypal_enabled'] == 'on' &&
                            !empty($admin_payment_setting['paypal_client_id']) &&
                            !empty($admin_payment_setting['paypal_secret_key']))))
                <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                    <a href="#" data-url="{{ route('plans.create') }}" class="btn btn-xs btn-white btn-icon-only width-auto"
                        data-ajax-popup="true" data-toggle="tooltip" data-title="{{ __('Create New Plan') }}"
                        data-original-title="{{ __('Create Plan') }}">
                        <i class="fa fa-plus"></i> {{ __('Create') }}
                    </a>
                </div>
            @endif
        @endcan
    </div>
@endsection

@section('content')

    <style>
        .modal-open .content {
            filter: blur(5px);
        }
        
        .modal-content {
            background: white;
            box-shadow: 0 1rem 1rem rgb(0 0 0);
            padding: 0 20px;
        }
        
        .modal-open .blur-content {
            filter: blur(5px);
            transition: filter 0.3s ease;
            pointer-events: none; /* Prevent interaction with the blurred content */
        }
    
        .modal-open .modal-backdrop, .modal-backdrop {
            z-index: 1130 !important;
        }
    
        .modal-content {
            background: white;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
            padding: 20px;
            z-index: 1131; 
        }
        
        .modal{
            z-index: 1131;
        }
        
        .modal-backdrop.show {
            opacity: 0.98;
        }
        
        .blurred {
            filter: blur(1px);
            opacity: 1;
            transition: filter 0.3s ease, opacity 0.3s ease;
        }
    
        .recommended {
            transform: scale(1.09);
            position: relative;
            transition: transform 0.3s ease;
        }
    
        .star-badge {
            position: absolute;
            top: 10px;
            left: 65px;
            background: gold;
            color: black;
            font-size: 14px;
            padding: 5px;
            border-radius: 25%;
            font-weight: 900;
        }
    </style>

    <div class="row mt-3">
        @foreach ($plans as $plan)
            @php
                $isCompany = \Auth::user()->type == 'company';
                $isRecommended = $isCompany && \Auth::user()->employees_count == $plan->max_employees;
            @endphp
            <div class="col-lg-4 col-xl-3 col-md-6 col-sm-6 mb-4 {{ $isRecommended ? '' : ($isCompany ? 'blurred' : '') }}">
                <div class="plan-3 {{ $isRecommended ? 'recommended' : '' }}">
                    @if($isRecommended)
                        <div class="star-badge">{{ __('Recommended') }}</div>
                    @endif
                    <h6>{{ $plan->name }}</h6>
                    <p class="price">
                        <sup>{{ (!empty(env('CURRENCY_SYMBOL')) ? env('CURRENCY_SYMBOL') : '$') . $plan->price }}</sup>
                        <sub>{{ __('Duration : ') . ucfirst($plan->duration) }}</sub>
                    </p>
                    <p class="price-text"></p>
                    <ul class="plan-detail">
                        <li>{{ $plan->max_users == -1 ? __('Unlimited') : $plan->max_users }} {{ __('Users') }}</li>
                        <li>{{ $plan->max_employees == -1 ? __('Unlimited') : $plan->max_employees }}
                            {{ __('Employees') }}
                        </li>
                    </ul>
                    @can('Edit Plan')
                        <a title="{{ __('Edit Plan') }}" href="#" class="button text-xs"
                            data-url="{{ route('plans.edit', $plan->id) }}" data-ajax-popup="true"
                            data-title="{{ __('Edit Plan') }}" data-toggle="tooltip"
                            data-original-title="{{ __('Edit') }}">
                            <i class="far fa-edit"></i>
                        </a>
                    @endcan
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
                            @if ($plan->id != \Auth::user()->plan && \Auth::user()->type != 'super admin')
                                @if ($plan->price > 0)
                                    <a href="{{ route('stripe', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)) }}"
                                        class="button text-xs">{{ __('Buy Plan') }}</a>
                                @else
                                    <a href="#" class="button text-xs">{{ __('Free') }}</a>
                                @endif
                            @endif
                        @endcan
                    @endif

                    @if ($plan->id != 1 && \Auth::user()->type != 'super admin')
                        @if (\Auth::user()->requested_plan != $plan->id)
                            @if (\Auth::user()->plan == $plan->id)
                                <a href="#" class="badge badge-pill badge-success">
                                    <span class="btn-inner--icon">active</span>
                                </a>
                            @else
                                <a href="{{ route('plan_request', \Illuminate\Support\Facades\Crypt::encrypt($plan->id)) }}"
                                    class="badge badge-pill badge-success">
                                    <span class="btn-inner--icon"><i class="fas fa-share"></i></span>
                                </a>
                            @endif
                        @else
                            @if (\Auth::user()->plan == $plan->id)
                                <a href="#" class="badge badge-pill badge-success">
                                    <span class="btn-inner--icon">active</span>
                                </a>
                            @else
                                <a href="#" class="badge badge-pill badge-danger" data-toggle="tooltip"
                                    data-original-title="{{ __('Delete') }}"
                                    data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                    data-confirm-yes="document.getElementById('delete-form-{{ $plan->id }}').submit();">
                                    <span class="btn-inner--icon"><i class="fas fa-times"></i></span>
                                </a>
                                <form method="POST" action="{{ route('plans.destroy', $plan->id) }}"
                                    id="delete-form-{{ $plan->id }}">
                                    @method('DELETE')
                                    @csrf
                                </form>
                            @endif
                        @endif
                    @endif

                    @php
                        $plan_expire_date = \Auth::user()->plan_expire_date;
                        // dd(\Auth::user()->plan);
                    @endphp
                    @if (\Auth::user()->type == 'company' && \Auth::user()->plan == $plan->id)
                        <p class="server-plan text-white">
                            {{ __('Plan Expired : ') }}
                            {{ !empty($plan_expire_date) ? \Auth::user()->dateFormat($plan_expire_date) : 'Unlimited' }}
                        </p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Modal HTML -->
    <div id="organizationalInfoModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="organizationalInfoModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="organizationalInfoModalLabel">{{ __('Organizational Info') }}</h5>
                    <div class="" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt text-danger"></i>
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="organizationalInfoForm">
                        <div class="form-group">
                            <label for="name">{{ __('Name') }}</label>
                            <input type="text" class="form-control" id="name" value="{{ Auth::user()->name }}" readonly disabled>
                        </div>
                        <div class="form-group">
                            <label for="email">{{ __('Email') }}</label>
                            <input type="email" class="form-control" id="email" value="{{ Auth::user()->email }}" readonly disabled>
                        </div>
                        <div class="form-group">
                            <label for="company_name">{{ __('Company Name') }}</label>
                            <input type="text" class="form-control" id="company_name" required>
                        </div>
                        <div class="form-group">
                            <label for="employees_count">{{ __('Employees Count') }}</label>
                            <select class="form-control" id="employees_count" required>
                                <option value="">Select</option>
                                <option value="10">Upto 10</option>
                                <option value="50">Upto 50</option>
                                <option value="100">Upto 100</option>
                                <option value="150">Upto 150</option>
                                <option value="200">Upto 200</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="saveButton" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let userType = "{{ Auth::user()->type }}";
            let companyName = "{{ Auth::user()->company_name }}";
            let employeesCount = "{{ Auth::user()->employees_count }}";
    
            if (userType === 'company' && (!companyName || !employeesCount)) {
                $('#organizationalInfoModal').modal('show');
                $('body').addClass('modal-open');
            }
    
            $('#saveButton').click(function () {
                let company_name = $('#company_name').val();
                let employees_count = $('#employees_count').val();
    
                if (company_name && employees_count) {
                    // Send the data to the server to save it (use AJAX or a form submit)
                    // For example, using AJAX:
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
                                // $('#organizationalInfoModal').modal('hide');
                                // $('body').removeClass('modal-open');
                                location.reload();
                            } else {
                                // Handle validation errors
                            }
                        }
                    });
                } else {
                    alert('Please fill in all required fields.');
                }
            });
            
            
            if (userType === 'company') {
                const planCards = document.querySelectorAll('.plan-3');
            
                planCards.forEach(card => {
                    card.addEventListener('click', function() {
                        planCards.forEach(c => {
                            if (c !== this) {
                                c.closest('.col-lg-4').classList.add('blurred');
                                c.classList.remove('recommended');
                            } else {
                                c.closest('.col-lg-4').classList.remove('blurred');
                                c.classList.add('recommended');
                            }
                        });
                    });
                });
            }

            
            
        });
    </script>


@endsection
