<style>
    
    /* Beautiful Plans Table - Only essential CSS */
    
    .premium-plans-table {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 0;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: var(--shadow-md);
        transition: all 0.3s ease;
        margin-top: 2rem;
        position: relative;
        overflow: hidden;
    }

    .premium-plans-table::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
    }

    .premium-plans-table:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
        background: rgba(255, 255, 255, 1);
    }

    .plans-table {
        width: 100% !important;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
        background: transparent;
    }

    .plans-table thead {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border-radius: 20px 20px 0 0;
    }

    .plans-table thead th {
        padding: 24px 20px;
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--text-primary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
        text-align: left;
        white-space: nowrap;
    }

    .plans-table thead th:first-child {
        border-radius: 20px 0 0 0;
    }

    .plans-table thead th:last-child {
        border-radius: 0 20px 0 0;
        text-align: center;
    }

    .plans-table tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid rgba(241, 245, 249, 0.8);
        position: relative;
    }

    .plans-table tbody tr:hover {
        background: rgba(37, 99, 235, 0.02);
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.08);
    }

    .plans-table tbody tr:last-child {
        border-bottom: none;
    }

    .plans-table tbody tr.active-row {
        background: rgba(16, 185, 129, 0.03);
        border-left: 4px solid var(--success);
    }

    .plans-table tbody tr.recommended-row {
        background: rgba(245, 158, 11, 0.03);
        border-left: 4px solid var(--warning);
    }

    .plans-table tbody td {
        padding: 24px 20px;
        vertical-align: middle;
        border: none;
        font-weight: 500;
        color: var(--text-primary);
    }

    .plan-details {
        position: relative;
        min-width: 200px;
    }

    .plan-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .plan-name {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
    }

    .plan-subtitle {
        font-size: 0.85rem;
        color: var(--text-secondary);
        margin: 0;
    }

    .current-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: linear-gradient(135deg, var(--success), #34d399);
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .recommended-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: linear-gradient(135deg, var(--warning), #fbbf24);
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }

    .plan-pricing {
        text-align: center;
        min-width: 120px;
    }

    .price-display {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--primary);
        margin: 0 0 4px 0;
    }

    .price-display.free {
        color: var(--success);
    }

    .duration-display {
        font-size: 0.8rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .feature-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        min-width: 100px;
        justify-content: center;
        text-align: center;
    }

    .feature-badge.users {
        background: rgba(37, 99, 235, 0.1);
        border: 1px solid rgba(37, 99, 235, 0.2);
        color: var(--primary);
    }

    .feature-badge.employees {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.2);
        color: var(--success);
    }

    .feature-badge i {
        font-size: 0.75rem;
    }

    .plan-action {
        text-align: center;
        min-width: 140px;
    }

    .plan-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 16px;
        border: none;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.85rem;
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
        min-width: 120px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .plan-btn:hover {
        transform: translateY(-2px);
        text-decoration: none;
    }

    .plan-btn.active {
        background: linear-gradient(135deg, var(--success), #34d399);
        color: white;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .plan-btn.active:hover {
        color: white;
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    }

    .plan-btn.upgrade {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .plan-btn.upgrade:hover {
        color: white;
        box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .plans-table thead {
            display: none;
        }

        .plans-table tbody tr {
            display: block;
            margin-bottom: 20px;
            border-radius: 16px;
            border: 1px solid rgba(241, 245, 249, 0.8);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 0;
        }

        .plans-table tbody tr.active-row {
            border-left: 4px solid var(--success);
        }

        .plans-table tbody tr.recommended-row {
            border-left: 4px solid var(--warning);
        }

        .plans-table tbody td {
            display: block;
            padding: 16px 20px;
            border: none;
            position: relative;
            text-align: left;
        }

        .plans-table tbody td::before {
            content: attr(data-label);
            position: absolute;
            left: 20px;
            top: 6px;
            font-weight: 700;
            font-size: 0.75rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .plans-table tbody td:first-child::before {
            content: '';
        }

        .plans-table tbody td {
            padding-top: 28px;
        }

        .plans-table tbody td:first-child {
            padding-top: 20px;
        }

        .plan-action {
            text-align: center;
            padding: 20px;
        }

        .plan-btn {
            width: 100%;
            min-width: auto;
        }
    }

    @media (max-width: 768px) {
        .premium-plans-table {
            border-radius: 16px;
            margin-top: 1.5rem;
        }

        .plans-table tbody tr {
            margin-bottom: 16px;
            border-radius: 14px;
        }

        .plans-table tbody td {
            padding: 12px 16px;
            padding-top: 24px;
        }

        .plan-name {
            font-size: 1.1rem;
        }

        .price-display {
            font-size: 1.3rem;
        }

        .plan-btn {
            padding: 10px 14px;
            font-size: 0.8rem;
        }
    }

    @media (max-width: 576px) {
        .premium-plans-table {
            border-radius: 14px;
        }

        .plans-table tbody td {
            padding: 10px 14px;
            padding-top: 20px;
        }

        .plan-name {
            font-size: 1rem;
        }

        .price-display {
            font-size: 1.2rem;
        }

        .feature-badge {
            font-size: 0.8rem;
            padding: 6px 10px;
            min-width: 80px;
        }

        .plan-btn {
            padding: 8px 12px;
            font-size: 0.75rem;
        }
    }
</style>

<!-- Premium Header - uses styles from custom.css -->
<div class="page-header-premium fade-in">
    <div class="header-content">
        <div class="header-left">
            <div class="header-icon">
                <i class="fas fa-crown"></i>
            </div>
            <div class="header-text">
                <h1>{{ __('Subscription Plans') }}</h1>
                <p>{{ __('Choose the perfect plan for your organization') }}</p>
            </div>
        </div>
        <div class="header-stats">
            <div class="stat-item">
                <p class="stat-number">{{ $plans->count() }}</p>
                <p class="stat-label">{{ __('Available Plans') }}</p>
            </div>
            <div class="stat-item">
                <p class="stat-number">{{ $plans->where('price', '>', 0)->count() }}</p>
                <p class="stat-label">{{ __('Premium Plans') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="premium-plans-table fade-in">
    <table class="plans-table">
        <thead>
            <tr>
                <th>{{ __('Plan Details') }}</th>
                <th>{{ __('Pricing') }}</th>
                <th>{{ __('Users') }}</th>
                <th>{{ __('Employees') }}</th>
                <th>{{ __('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($plans as $index => $plan)
                <tr class="plan-row {{ $user->plan == $plan->id ? 'active-row' : '' }} {{ $index == 1 ? 'recommended-row' : '' }}">
                    <td class="plan-details">
                        @if($user->plan == $plan->id)
                            <div class="current-badge">{{ __('Current') }}</div>
                        @elseif($index == 1)
                            <div class="recommended-badge">{{ __('Recommended') }}</div>
                        @endif
                        
                        <div class="plan-info">
                            <div class="plan-name">{{ $plan->name }}</div>
                            <div class="plan-subtitle">
                                @if($plan->price == 0)
                                    {{ __('Perfect for testing') }}
                                @elseif($index == 1)
                                    {{ __('Most popular choice') }}
                                @elseif($index == 3)
                                    {{ __('Enterprise solution') }}
                                @else
                                    {{ __('Great for small teams') }}
                                @endif
                            </div>
                        </div>
                    </td>
                    
                    <td class="plan-pricing">
                        <div class="price-display {{ $plan->price == 0 ? 'free' : '' }}">
                            {{ (!empty(env('CURRENCY_SYMBOL')) ? env('CURRENCY_SYMBOL') : '$') . $plan->price }}
                        </div>
                        <div class="duration-display">
                            @if($plan->price > 0)
                                {{ '/ ' . $plan->duration }}
                            @elseif('2_weeks' == $plan->duration)
                                {{ 'for 2 Weeks' }}
                            @else
                                {{ 'for a ' . $plan->duration }}
                            @endif
                        </div>
                    </td>
                    
                    <td class="plan-users">
                        <div class="feature-badge users">
                            <i class="fas fa-users"></i>
                            <span>{{ $plan->max_users == -1 ? 'Unlimited' : $plan->max_users }}</span>
                        </div>
                    </td>
                    
                    <td class="plan-employees">
                        <div class="feature-badge employees">
                            <i class="fas fa-user-tie"></i>
                            <span>{{ $plan->max_employees == -1 ? 'Unlimited' : $plan->max_employees }}</span>
                        </div>
                    </td>
                    
                    <td class="plan-action">
                        @if($user->plan == $plan->id)
                            <span class="plan-btn active">
                                <i class="fas fa-check"></i>
                                {{ __('Current Plan') }}
                            </span>
                        @else
                            <a href="{{ route('plan.active', [$user->id, $plan->id]) }}" 
                               class="plan-btn upgrade">
                                <i class="fas fa-cart-plus"></i>
                                {{ __('Upgrade') }}
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>