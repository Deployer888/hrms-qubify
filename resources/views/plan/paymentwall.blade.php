@php
$logo=asset(Storage::url('uploads/logo'));
$company_favicon=Utility::getValByName('company_favicon');
@endphp
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>@if(Auth::user()->type == 'super admin')  {{config('app.name', 'Hrmgo Saas')}} @else {{(Utility::getValByName('title_text')) ? Utility::getValByName('title_text') : config('app.name', 'Hrmgo SaaS')}} @endif</title>
@if(Auth::user()->type == 'super admin')
  <link rel="icon" href="{{$logo.'/favicon.png'}}" type="image" sizes="16x16">
@else
  <link rel="icon" href="{{(isset($company_favicon) && !empty($company_favicon)?asset(Storage::url($company_favicon)):'favicon.png')}}" type="image" sizes="16x16">
@endif
<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
@php
$plan_id= \Illuminate\Support\Facades\Crypt::decrypt($data['plan_id']);
$plandata=App\Models\Plan::find($plan_id);
@endphp
<script src="https://api.paymentwall.com/brick/build/brick-default.1.5.0.min.js"> </script>
<div id="payment-form-container"> </div>
<script>

var brick = new Brick({
  public_key: '{{ $admin_payment_setting['paymentwall_public_key']  }}', // please update it to Brick live key before launch your project
  amount: '{{$plandata->price}}',
  currency: '{{ env("CURRENCY")  }}',
  container: 'payment-form-container',
  action: '{{route("plan.pay.with.paymentwall",[$data["plan_id"],$data["coupon"]])}}',
  success_url: '{{route("plans.index")}}',
  form: {
    merchant: 'Paymentwall',
    product:  '{{$plandata->name}}',
    pay_button: 'Pay',
    show_zip: true, // show zip code
    show_cardholder: true // show card holder name
  }
});


brick.showPaymentForm(function(data) {
    if(data.flag == 1){
        window.location.href ='{{route("callback.error",1)}}';
    }else{
        window.location.href ='{{route("callback.error",2)}}';
    }
    }, function(errors) {
    if(errors.flag == 1){
        window.location.href ='{{route("callback.error",1)}}';
    }else{
        window.location.href ='{{route("callback.error",2)}}';
    }
});

</script>


<!-- New design -->

{{--

@extends('layouts.admin')
@section('page-title')
    {{ __('Dashboard') }}
@endsection
@push('css-page')
    <link href="{{ asset('css/dash.css') }}" rel="stylesheet">
    <style>
        :root {
            --primary: #059669;
            --secondary: #10b981;
            --accent: #34d399;
            --info: #6ee7b7;
            --success: #047857;
            --warning: #f59e0b;
            --danger: #dc2626;
            --shadow: 0 4px 6px rgba(5, 150, 105, 0.07);
            --shadow-md: 0 8px 25px rgba(5, 150, 105, 0.1);
            --shadow-lg: 0 15px 35px rgba(5, 150, 105, 0.12);
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --gradient-primary: linear-gradient(135deg, #059669, #10b981);
            --gradient-secondary: linear-gradient(135deg, #10b981, #34d399);
            --gradient-accent: linear-gradient(135deg, #34d399, #6ee7b7);
        }
        
        .page-title {
            display: none;
        }

        .office-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(5, 150, 105, 0.08);
            margin-bottom: 25px;
            position: relative;
            background: white;
            border: 1px solid rgba(5, 150, 105, 0.1);
        }

        .office-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(5, 150, 105, 0.15);
        }

        .office-card .card-header {
            background: var(--gradient-primary);
            color: white;
            padding: 24px;
            position: relative;
            overflow: hidden;
            border-bottom: none;
        }

        .office-card .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at center, rgba(255,255,255,0.1), transparent 70%);
            animation: shimmer 3s linear infinite;
        }

        @keyframes shimmer {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .office-card .card-header h3 {
            margin: 0;
            font-weight: 700;
            font-size: 1.4rem;
            position: relative;
            z-index: 2;
            color: #fff !important;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .office-card-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .office-card-link:hover {
            text-decoration: none;
            color: inherit;
        }

        .office-stats {
            padding: 20px 0;
        }

        /* Enhanced Premium Header */
        .page-header-compact {
            background: linear-gradient(135deg, 
                rgba(5, 150, 105, 0.95) 0%, 
                rgba(16, 185, 129, 0.95) 50%, 
                rgba(52, 211, 153, 0.95) 100%);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            padding: 32px 40px;
            margin-bottom: 32px;
            box-shadow: 
                0 32px 64px rgba(5, 150, 105, 0.3),
                0 8px 32px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
            transform-style: preserve-3d;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .page-header-compact::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                conic-gradient(from 0deg at 50% 50%, 
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

        .page-header-compact .header-content {
            position: relative;
            z-index: 2;
            flex: 1;
            min-width: 0;
        }

        .page-title-compact {
            font-size: 2.2rem;
            font-weight: 800;
            color: #fff;
            margin: 0;
            line-height: 1.2;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            letter-spacing: -0.025em;
        }

        .header-icon {
            width: 52px;
            height: 52px;
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            backdrop-filter: blur(20px);
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .header-icon:hover {
            transform: rotate(5deg) scale(1.05);
            background: rgba(255, 255, 255, 0.2);
        }

        .all-button-box {
            position: relative;
            z-index: 2;
            min-width: 200px;
        }

        .form-select {
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: 14px 18px;
            color: white;
            font-weight: 600;
            backdrop-filter: blur(20px);
            width: 100%;
            transition: all 0.3s ease;
        }

        .form-select:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.4);
            background: rgba(255, 255, 255, 0.2);
        }

        .form-select option {
            background: var(--primary);
            color: white;
            padding: 10px;
        }

        /* Enhanced Summary Cards */
        .summary-card {
            background: white;
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 8px 32px rgba(5, 150, 105, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(5, 150, 105, 0.1);
        }

        .summary-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 48px rgba(5, 150, 105, 0.15);
        }

        .summary-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .present-card::before {
            background: linear-gradient(135deg, #059669, #10b981);
        }

        .absent-card::before {
            background: linear-gradient(135deg, #dc2626, #ef4444);
        }

        .late-card::before {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
        }

        .leave-card::before {
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
        }

        .summary-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .present-card .summary-icon {
            background: var(--gradient-primary);
        }

        .absent-card .summary-icon {
            background: linear-gradient(135deg, #dc2626, #ef4444);
        }

        .late-card .summary-icon {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
        }

        .leave-card .summary-icon {
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
        }

        .summary-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 12px;
        }

        .summary-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary);
            margin: 0;
            line-height: 1;
        }

        /* Enhanced Dashboard Cards */
        .dashboard-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(5, 150, 105, 0.1);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(5, 150, 105, 0.1);
            margin-bottom: 24px;
        }

        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 48px rgba(5, 150, 105, 0.15);
        }

        .dashboard-card .card-header {
            background: var(--gradient-primary);
            color: white;
            padding: 24px;
            border-bottom: none;
            position: relative;
            overflow: hidden;
        }

        .dashboard-card .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at center, rgba(255,255,255,0.1), transparent 70%);
            animation: shimmer 3s linear infinite;
        }

        .dashboard-card .card-header h3 {
            margin: 0;
            font-weight: 700;
            font-size: 1.3rem;
            position: relative;
            z-index: 2;
            color: #fff !important;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .dashboard-card .card-body {
            padding: 28px;
        }

        /* Enhanced Gauge Styles */
        .gauge-container {
            text-align: center;
            position: relative;
            margin: 20px auto;
            max-width: 400px;
        }

        .gauge-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
            max-width: 300px;
        }

        .gauge-image {
            width: 100%;
            height: auto;
            filter: hue-rotate(240deg) saturate(1.2);
        }

        .needle-container {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
        }

        .gauge-info {
            margin-top: 20px;
        }

        .gauge-percentage {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(5, 150, 105, 0.1);
        }

        .gauge-details {
            color: var(--text-secondary);
            font-size: 1rem;
            font-weight: 500;
        }

        /* Enhanced Stat Items */
        .stat-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
            padding: 12px 0;
            border-bottom: 1px solid rgba(5, 150, 105, 0.1);
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            background: rgba(5, 150, 105, 0.02);
            padding-left: 12px;
            padding-right: 12px;
            border-radius: 8px;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .stat-value {
            font-weight: 700;
            color: var(--primary);
            font-size: 1rem;
        }

        .attendance-percentage {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--primary);
            text-shadow: 0 2px 4px rgba(5, 150, 105, 0.1);
        }

        .attendance-label {
            color: var(--text-secondary);
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* Enhanced Section Headers */
        .section-header {
            position: relative;
            margin-bottom: 32px;
            padding-bottom: 16px;
        }

        .section-header h2 {
            position: relative;
            margin-bottom: 16px;
            padding-bottom: 16px;
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .section-header h2:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 4px;
            background: var(--gradient-primary);
            border-radius: 2px;
        }

        /* Enhanced Button Styles */
        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(5, 150, 105, 0.4);
            background: linear-gradient(135deg, #047857, #059669);
        }

        /* Enhanced Table Styles */
        .table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table th {
            background: var(--gradient-primary);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
            padding: 16px 12px;
            border: none;
        }

        .table td {
            padding: 12px;
            border-bottom: 1px solid rgba(5, 150, 105, 0.1);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: rgba(5, 150, 105, 0.02);
        }

        .attendance-list {
            min-height: 280px;
            max-height: 320px;
            overflow: auto;
            margin: 0;
            border-radius: 12px;
            border: 1px solid rgba(5, 150, 105, 0.1);
        }

        .scrollable-tbody {
            display: block;
            max-height: 260px;
            overflow-y: auto;
        }

        thead, tbody tr {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        /* Enhanced Badge Styles */
        .badge {
            border-radius: 20px;
            padding: 6px 12px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success {
            background: var(--gradient-primary);
            color: white;
        }

        .badge-danger {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: white;
        }

        .badge-warning {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            color: white;
        }

        .badge-info {
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            color: white;
        }

        /* Chart Container Enhancements */
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
            min-height: 300px;
            padding: 10px;
        }

        .office-chart-container {
            position: relative;
            height: 200px;
            width: 100%;
            min-height: 200px;
            padding: 10px;
        }

        .office-chart-summary {
            position: relative;
            height: 140px;
            width: 100%;
            min-height: 140px;
            padding: 10px;
        }

        /* Enhanced Needle Styles */
        .needle {
            position: absolute;
            width: 6px;
            height: calc(100% - 10px);
            background: linear-gradient(to top, #dc2626, #ef4444);
            bottom: 5px;
            left: 48.7%;
            transform: translateX(-50%) rotate(-90deg);
            transform-origin: bottom center;
            transition: transform 2s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 10;
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
        }

        .needle:before {
            content: '';
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 16px;
            height: 16px;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
        }

        .needle-center {
            position: absolute;
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #374151, #4b5563);
            border-radius: 50%;
            bottom: -6px;
            left: 49%;
            transform: translateX(-50%);
            z-index: 11;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border: 2px solid white;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-header-compact {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 24px;
                padding: 28px 24px;
                border-radius: 20px;
            }
            
            .header-content {
                width: 100%;
                margin-bottom: 0 !important;
            }
            
            .page-title-compact {
                font-size: 1.8rem;
            }
            
            .header-icon {
                width: 44px;
                height: 44px;
                font-size: 1.2rem;
            }
            
            .all-button-box {
                width: 100%;
                min-width: auto;
            }
            
            .summary-card {
                padding: 24px;
                margin-bottom: 20px;
            }
            
            .summary-icon {
                width: 52px;
                height: 52px;
                font-size: 1.6rem;
            }
            
            .summary-value {
                font-size: 2.2rem;
            }
            
            .dashboard-card .card-header {
                padding: 20px;
            }
            
            .dashboard-card .card-body {
                padding: 24px;
            }
            
            .office-card .card-header {
                padding: 20px;
            }
            
            .office-card .card-body {
                padding: 20px;
            }
            
            .gauge-percentage {
                font-size: 2rem;
            }
            
            .attendance-percentage {
                font-size: 1.8rem;
            }
            
            .chart-container {
                height: 250px;
                min-height: 250px;
            }
            
            .office-chart-container {
                height: 180px;
                min-height: 180px;
            }
            
            .office-chart-summary {
                height: 120px;
                min-height: 120px;
            }
        }

        @media (max-width: 480px) {
            .page-header-compact {
                padding: 24px 20px;
                border-radius: 16px;
            }
            
            .page-title-compact {
                font-size: 1.5rem;
            }
            
            .header-icon {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
            
            .form-select {
                padding: 12px 16px;
                font-size: 0.9rem;
            }
            
            .summary-card {
                padding: 20px;
            }
            
            .summary-icon {
                width: 48px;
                height: 48px;
                font-size: 1.4rem;
            }
            
            .summary-value {
                font-size: 2rem;
            }
            
            .dashboard-card .card-header {
                padding: 18px;
            }
            
            .dashboard-card .card-body {
                padding: 20px;
            }
            
            .gauge-percentage {
                font-size: 1.8rem;
            }
            
            .needle {
                width: 4px;
                height: calc(100% - 6px);
            }
            
            .needle:before {
                width: 12px;
                height: 12px;
                top: -6px;
            }
            
            .needle-center {
                width: 20px;
                height: 20px;
            }
        }

        /* Enhanced Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .dashboard-card,
        .summary-card,
        .office-card {
            animation: fadeInUp 0.6s ease-out;
        }

        .summary-icon:hover {
            animation: pulse 1s ease-in-out;
        }

        /* Enhanced Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(5, 150, 105, 0.1);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gradient-primary);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #047857, #059669);
        }

        /* Loading State */
        .loading {
            position: relative;
            overflow: hidden;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(5, 150, 105, 0.1), transparent);
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        /* Enhanced Focus States */
        .form-select:focus,
        .btn:focus {
            outline: 2px solid var(--accent);
            outline-offset: 2px;
        }

        /* Print Styles */
        @media print {
            .page-header-compact,
            .btn,
            .form-select {
                display: none !important;
            }
            
            .dashboard-card,
            .summary-card,
            .office-card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
        }
    </style>
@endpush
@section('content')
    <div class="attendance-dashboard">
        <!-- Header Section with Title -->
        <div class="page-header-compact d-flex align-items-center justify-content-between">
            <div class="header-content d-flex align-items-center">
                <div class="header-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h1 class="page-title-compact ms-3">{{ __('Performance Insights Dashboard') }}</h1>
            </div>
            <div class="all-button-box">
                <select class="form-select custom-select" id="office-filter" name="office">
                    <option value="all" selected>🏢 All Offices</option>
                    @foreach ($offices as $office)
                        <option value="{{ $office->id }}">🏢 {{ $office->name }} Office</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-container d-none">
            <div class="row g-3">
                <div class="col-md-3 d-none">
                    <div class="filter-card">
                        <label for="date-range">Date Range</label>
                        <div class="input-group">
                            <input type="text" class="form-control custom-date" id="date-range" name="date_range"
                                readonly>
                            <button class="btn btn-primary date-btn">
                                <i class="fas fa-calendar"></i>
                            </button>
                        </div>
                        <input type="hidden" id="start-date" name="start_date" value="{{ $startDate ?? '' }}">
                        <input type="hidden" id="end-date" name="end_date" value="{{ $endDate ?? '' }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards Section -->
        <div class="summary-section">
            <div class="row g-3">
                <div class="col-md-3 col-6 mb-md-0 mb-3">
                    <div class="summary-card present-card">
                        <div class="summary-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="summary-content">
                            <h3 class="summary-title">Present</h3>
                            <p class="summary-value" id="present-count">{{ $officeData['present'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-md-0 mb-3">
                    <div class="summary-card absent-card">
                        <div class="summary-icon">
                            <i class="fas fa-user-times"></i>
                        </div>
                        <div class="summary-content">
                            <h3 class="summary-title">Absent</h3>
                            <p class="summary-value" id="absent-count">{{ $officeData['absent'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-md-0 mb-3">
                    <div class="summary-card late-card">
                        <div class="summary-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="summary-content">
                            <h3 class="summary-title">Late</h3>
                            <p class="summary-value" id="late-count">{{ $officeData['late'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-md-0 mb-3">
                    <div class="summary-card leave-card">
                        <div class="summary-icon">
                            <i class="fas fa-calendar-minus"></i>
                        </div>
                        <div class="summary-content">
                            <h3 class="summary-title">On Leave</h3>
                            <p class="summary-value" id="leave-count">{{ $officeData['on_leave'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Content -->
        <div class="main-dashboard-content">
            <div class="row">
                <!-- Employee Attendance Status -->
                <div class="col-md-4">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fas fa-users me-2"></i>Employee Attendance Status</h3>
                        </div>
                        <div class="card-body">
                            <div class="card bg-none attendance-list">
                                <div class="table-responsive">
                                    <table class="table align-items-center">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list scrollable-tbody">
                                            @foreach ($notClockIns as $notClockIn)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar avatar-sm rounded-circle bg-primary me-2">
                                                                {{ substr($notClockIn->name, 0, 1) }}
                                                            </div>
                                                            {{ $notClockIn->name }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class='badge {{ $notClockIn->class }}'>
                                                            {{ $notClockIn->status }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <a href="{{ route('attendanceemployee.index') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-eye me-2"></i>{{ __('View All Attendance') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Overall Attendance Gauge -->
                <div class="col-md-4">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fas fa-tachometer-alt me-2"></i>Overall Attendance</h3>
                        </div>
                        <div class="card-body">
                            <div class="gauge-container">
                                <div class="gauge-wrapper">
                                    <img src="{{ asset('landing/images/gauge.png') }}" class="gauge-image" width="100%" alt="Gauge">
                                    <div class="needle-container">
                                        <div class="needle" id="attendance-needle"></div>
                                        <div class="needle-center"></div>
                                    </div>
                                </div>
                                <div class="gauge-info">
                                    <div class="gauge-percentage" id="overall-percentage">{{ $officeData['attendance_rate'] }}%</div>
                                    <div class="gauge-details">
                                        <i class="fas fa-users text-primary mr-1"></i>
                                        <span id="present-employees">{{ $officeData['present'] }}</span> /
                                        <span id="total-expected">{{ $totalEmployees }}</span> Present
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Attendance Breakdown Chart -->
                <div class="col-md-4">
                    <div class="dashboard-card" id="attendance-breakdown-card">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-pie me-2"></i>Attendance Breakdown</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" id="attendance-breakdown-chart-container">
                                <canvas id="attendance-breakdown-chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <!-- Weekly Trend Chart -->
                <div class="col-md-6">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-line me-2"></i>Weekly Attendance Trend</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="position: relative; height: 300px;">
                                <canvas id="weekly-trend-chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Status Chart -->
                <div class="col-md-6">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-bar me-2"></i>Employee Status Comparison</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="employee-status-chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Office-wise Analytics Section -->
        <div class="office-analytics-section mt-4">
            <div class="section-header">
                <h2><i class="fas fa-building me-2"></i>Office-wise Analytics</h2>
            </div>

            <div class="office-cards" id="office-cards">
                @foreach($offices as $office)
                    @php
                        $office_data = $officesData[$office->id] ?? [
                            'total' => 0,
                            'present' => 0,
                            'absent' => 0,
                            'late' => 0,
                            'on_leave' => 0,
                            'attendance_rate' => 0,
                            'departments' => [],
                            'branches' => []
                        ];
                        
                        $office_departments = implode(',', $office_data['departments'] ?? []);
                        $office_branches = implode(',', $office_data['branches'] ?? []);
                    @endphp
                    <div class="office-card" 
                        data-office-id="{{ $office->id }}" 
                        data-office-departments="{{ $office_departments }}"
                        data-office-branches="{{ $office_branches }}">
                        <a href="{{ route('office.one.index', $office->id) }}" class="office-card-link">
                            <div class="card-header">
                                <h3><i class="fas fa-building me-2"></i>{{ $office->name }} Office</h3>
                            </div>
                        </a>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="office-chart-summary">
                                        <canvas id="office-bar-{{ $office->id }}"></canvas>
                                    </div>
                                    <div class="attendance-info text-center mt-3">
                                        <div class="attendance-percentage">{{ $office_data['attendance_rate'] }}%</div>
                                        <div class="attendance-label">Overall Attendance Rate</div>
                                        <div class="gauge-details mt-2">
                                            <i class="fas fa-users text-primary mr-1"></i>
                                            <span>{{ $office_data['present'] }}</span> / <span>{{ $office_data['total'] }}</span> Present
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="office-stats">
                                        <div class="stat-item">
                                            <span class="stat-label"><i class="fas fa-users mr-1"></i>Total Employees:</span>
                                            <span class="stat-value">{{ $office_data['total'] }}</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-label"><i class="fas fa-user-check mr-1"></i>Present:</span>
                                            <span class="stat-value">{{ $office_data['present'] }}</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-label"><i class="fas fa-user-times mr-1"></i>Absent:</span>
                                            <span class="stat-value">{{ $office_data['absent'] }}</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-label"><i class="fas fa-clock mr-1"></i>Late:</span>
                                            <span class="stat-value">{{ $office_data['late'] }}</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-label"><i class="fas fa-calendar-minus mr-1"></i>On Leave:</span>
                                            <span class="stat-value">{{ $office_data['on_leave'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="office-chart-container mt-3">
                                <canvas id="office-trend-{{ $office->id }}"></canvas>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Data Container for JavaScript -->
    <div id="dashboard-data" class="d-none" 
        data-overall-percentage="{{ $officeData['attendance_rate'] }}"
        data-present-count="{{ $officeData['present'] }}" 
        data-absent-count="{{ $officeData['absent'] }}"
        data-late-count="{{ $officeData['late'] }}" 
        data-leave-count="{{ $officeData['on_leave'] }}"
        data-total-employees="{{ $officeData['total'] }}" 
        data-working-days="22"
        data-weekly-trend='@json($weeklyTrendData)' 
        data-office-data='@json($officesData)'>
    </div>
@endsection

@push('script-page')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-gauge@0.3.0/dist/chartjs-gauge.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{ asset('js/dash.js') }}"></script>
    <script>
        // Set Chart.js global defaults for green theme
        Chart.defaults.global.defaultFontColor = '#1f2937';
        Chart.defaults.global.defaultFontFamily = 'Inter, system-ui, sans-serif';
        Chart.defaults.global.defaultFontSize = 12;

        // Green color palette for charts
        const chartColors = {
            primary: '#059669',
            secondary: '#10b981',
            accent: '#34d399',
            info: '#6ee7b7',
            success: '#047857',
            warning: '#f59e0b',
            danger: '#dc2626',
            gradients: {
                primary: 'rgba(5, 150, 105, 0.8)',
                secondary: 'rgba(16, 185, 129, 0.8)',
                accent: 'rgba(52, 211, 153, 0.8)',
                info: 'rgba(110, 231, 183, 0.8)',
                warning: 'rgba(245, 158, 11, 0.8)',
                danger: 'rgba(220, 38, 38, 0.8)'
            }
        };

        $(document).ready(function() {
            // Initialize dashboard functionality
            initializeDashboard();
            
            // Set up office filter
            setupOfficeFilter();
            
            // Initialize charts
            initializeCharts();
            
            // Set up gauge
            initializeGauge();
            
            // Add loading animations
            addLoadingAnimations();
        });

        function initializeDashboard() {
            // Add enhanced animations to cards
            $('.dashboard-card, .summary-card, .office-card').each(function(index) {
                $(this).css({
                    'opacity': '0',
                    'transform': 'translateY(30px)',
                    'animation-delay': (index * 0.1) + 's'
                });
                
                setTimeout(() => {
                    $(this).css({
                        'opacity': '1',
                        'transform': 'translateY(0)',
                        'transition': 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)'
                    });
                }, index * 100);
            });
        }

        function setupOfficeFilter() {
            const officeFilter = $('#office-filter');
            
            officeFilter.on('change', function() {
                const selectedOffice = $(this).val();
                
                // Add loading effect
                $(this).addClass('loading');
                
                setTimeout(() => {
                    // Filter logic here
                    filterOffices(selectedOffice);
                    $(this).removeClass('loading');
                }, 300);
            });
        }

        function filterOffices(selectedOffice) {
            if (selectedOffice === 'all') {
                $('.office-card').show();
                updateSummaryTotals();
            } else {
                $('.office-card').hide();
                $(`.office-card[data-office-id="${selectedOffice}"]`).show();
                updateOfficeSummary(selectedOffice);
            }
        }

        function initializeCharts() {
            // Initialize all charts with green theme
            initializeAttendanceBreakdownChart();
            initializeWeeklyTrendChart();
            initializeEmployeeStatusChart();
            initializeOfficeCharts();
        }

        function initializeAttendanceBreakdownChart() {
            const ctx = document.getElementById('attendance-breakdown-chart');
            if (!ctx) return;

            const data = {
                labels: ['Present', 'Absent', 'Late', 'On Leave'],
                datasets: [{
                    data: [
                        parseInt($('#dashboard-data').attr('data-present-count')) || 0,
                        parseInt($('#dashboard-data').attr('data-absent-count')) || 0,
                        parseInt($('#dashboard-data').attr('data-late-count')) || 0,
                        parseInt($('#dashboard-data').attr('data-leave-count')) || 0
                    ],
                    backgroundColor: [
                        chartColors.gradients.primary,
                        chartColors.gradients.danger,
                        chartColors.gradients.warning,
                        chartColors.gradients.accent
                    ],
                    borderColor: [
                        chartColors.primary,
                        chartColors.danger,
                        chartColors.warning,
                        chartColors.accent
                    ],
                    borderWidth: 2
                }]
            };

            new Chart(ctx, {
                type: 'doughnut',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            fontColor: '#1f2937'
                        }
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem, data) {
                                const dataset = data.datasets[tooltipItem.datasetIndex];
                                const total = dataset.data.reduce((sum, value) => sum + value, 0);
                                const value = dataset.data[tooltipItem.index];
                                const percentage = Math.round((value / total) * 100);
                                return `${data.labels[tooltipItem.index]}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            });
        }

        function initializeWeeklyTrendChart() {
            const ctx = document.getElementById('weekly-trend-chart');
            if (!ctx) return;

            const weeklyTrendData = JSON.parse($('#dashboard-data').attr('data-weekly-trend') || '[]');
            
            const data = {
                labels: weeklyTrendData.map(item => item.day),
                datasets: [{
                    label: 'Present',
                    data: weeklyTrendData.map(item => item.present),
                    borderColor: chartColors.primary,
                    backgroundColor: chartColors.gradients.primary,
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Absent',
                    data: weeklyTrendData.map(item => item.absent),
                    borderColor: chartColors.danger,
                    backgroundColor: chartColors.gradients.danger,
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Late',
                    data: weeklyTrendData.map(item => item.late),
                    borderColor: chartColors.warning,
                    backgroundColor: chartColors.gradients.warning,
                    tension: 0.4,
                    fill: true
                }]
            };

            new Chart(ctx, {
                type: 'line',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                fontColor: '#6b7280'
                            },
                            gridLines: {
                                color: 'rgba(5, 150, 105, 0.1)'
                            }
                        }],
                        xAxes: [{
                            ticks: {
                                fontColor: '#6b7280'
                            },
                            gridLines: {
                                display: false
                            }
                        }]
                    },
                    legend: {
                        labels: {
                            fontColor: '#1f2937'
                        }
                    }
                }
            });
        }

        function initializeEmployeeStatusChart() {
            const ctx = document.getElementById('employee-status-chart');
            if (!ctx) return;

            const presentCount = parseInt($('#dashboard-data').attr('data-present-count')) || 0;
            const lateCount = parseInt($('#dashboard-data').attr('data-late-count')) || 0;
            const onTimeCount = presentCount - lateCount;

            const data = {
                labels: ['On Time', 'Late', 'Absent', 'On Leave'],
                datasets: [{
                    data: [
                        onTimeCount,
                        lateCount,
                        parseInt($('#dashboard-data').attr('data-absent-count')) || 0,
                        parseInt($('#dashboard-data').attr('data-leave-count')) || 0
                    ],
                    backgroundColor: [
                        chartColors.gradients.primary,
                        chartColors.gradients.warning,
                        chartColors.gradients.danger,
                        chartColors.gradients.accent
                    ],
                    borderColor: [
                        chartColors.primary,
                        chartColors.warning,
                        chartColors.danger,
                        chartColors.accent
                    ],
                    borderWidth: 2
                }]
            };

            new Chart(ctx, {
                type: 'horizontalBar',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            ticks: {
                                beginAtZero: true,
                                fontColor: '#6b7280'
                            },
                            gridLines: {
                                color: 'rgba(5, 150, 105, 0.1)'
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                fontColor: '#6b7280'
                            },
                            gridLines: {
                                display: false
                            }
                        }]
                    },
                    legend: {
                        display: false
                    }
                }
            });
        }

        function initializeOfficeCharts() {
            const officeData = JSON.parse($('#dashboard-data').attr('data-office-data') || '{}');
            
            Object.keys(officeData).forEach(officeId => {
                const office = officeData[officeId];
                createOfficeBarChart(officeId, office);
                createOfficeTrendChart(officeId, office.weeklyData || []);
            });
        }

        function createOfficeBarChart(officeId, data) {
            const ctx = document.getElementById(`office-bar-${officeId}`);
            if (!ctx) return;

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Present', 'Absent', 'Late'],
                    datasets: [{
                        label: 'Employee Count',
                        data: [data.present, data.absent, data.late],
                        backgroundColor: [
                            chartColors.gradients.primary,
                            chartColors.gradients.danger,
                            chartColors.gradients.warning
                        ],
                        borderColor: [
                            chartColors.primary,
                            chartColors.danger,
                            chartColors.warning
                        ],
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                fontColor: '#6b7280'
                            },
                            gridLines: {
                                color: 'rgba(5, 150, 105, 0.1)'
                            }
                        }],
                        xAxes: [{
                            ticks: {
                                fontColor: '#6b7280'
                            },
                            gridLines: {
                                display: false
                            }
                        }]
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            fontColor: '#1f2937',
                            fontSize: 10
                        }
                    }
                }
            });
        }

        function createOfficeTrendChart(officeId, weeklyData) {
            const ctx = document.getElementById(`office-trend-${officeId}`);
            if (!ctx || !weeklyData.length) return;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: weeklyData.map(item => item.day),
                    datasets: [{
                        label: 'Present',
                        data: weeklyData.map(item => item.present),
                        borderColor: chartColors.primary,
                        backgroundColor: chartColors.gradients.primary,
                        tension: 0.4,
                        pointRadius: 3
                    }, {
                        label: 'Absent',
                        data: weeklyData.map(item => item.absent),
                        borderColor: chartColors.danger,
                        backgroundColor: chartColors.gradients.danger,
                        tension: 0.4,
                        pointRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                fontSize: 10,
                                fontColor: '#6b7280'
                            },
                            gridLines: {
                                color: 'rgba(5, 150, 105, 0.1)'
                            }
                        }],
                        xAxes: [{
                            ticks: {
                                fontSize: 10,
                                fontColor: '#6b7280'
                            },
                            gridLines: {
                                display: false
                            }
                        }]
                    },
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            fontSize: 9,
                            fontColor: '#1f2937'
                        }
                    }
                }
            });
        }

        function initializeGauge() {
            const percentage = parseInt($('#dashboard-data').attr('data-overall-percentage')) || 0;
            updateGaugeNeedle(percentage);
        }

        function updateGaugeNeedle(percentage) {
            const rotation = -90 + (percentage * 1.8);
            $('#attendance-needle').css({
                'transform': `translateX(-50%) rotate(${rotation}deg)`,
                'transition': 'transform 2s cubic-bezier(0.4, 0, 0.2, 1)'
            });
        }

        function addLoadingAnimations() {
            // Add subtle loading animations
            $('.summary-card').hover(
                function() {
                    $(this).find('.summary-icon').addClass('animate-pulse');
                },
                function() {
                    $(this).find('.summary-icon').removeClass('animate-pulse');
                }
            );
        }

        function updateSummaryTotals() {
            const data = $('#dashboard-data');
            $('#present-count').text(data.attr('data-present-count'));
            $('#absent-count').text(data.attr('data-absent-count'));
            $('#late-count').text(data.attr('data-late-count'));
            $('#leave-count').text(data.attr('data-leave-count'));
            $('#overall-percentage').text(data.attr('data-overall-percentage') + '%');
        }

        function updateOfficeSummary(officeId) {
            const officeData = JSON.parse($('#dashboard-data').attr('data-office-data'));
            const office = officeData[officeId];
            
            if (office) {
                $('#present-count').text(office.present);
                $('#absent-count').text(office.absent);
                $('#late-count').text(office.late);
                $('#leave-count').text(office.on_leave || 0);
                $('#overall-percentage').text(office.attendance_rate + '%');
                updateGaugeNeedle(office.attendance_rate);
            }
        }

        // Add smooth scrolling for office filter
        $('#office-filter').on('change', function() {
            const selectedOffice = $(this).val();
            if (selectedOffice !== 'all') {
                $('html, body').animate({
                    scrollTop: $(`.office-card[data-office-id="${selectedOffice}"]`).offset().top - 100
                }, 500);
            }
        });

        // Add window resize handler for responsive charts
        $(window).on('resize', function() {
            Chart.helpers.each(Chart.instances, function(instance) {
                instance.resize();
            });
        });
    </script>
@endpush

--}}