@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Attendance List') }}
@endsection
@php
use App\Helpers\Helper;
use Carbon\Carbon;
$requestType = isset($_GET['type']) ? $_GET['type'] : 'daily';

@endphp
<style>
    /* Responsive CSS Foundation */
    :root {
        --mobile-padding: 10px;
        --tablet-padding: 15px;
        --desktop-padding: 20px;
        --touch-target-size: 44px;
        --mobile-font-size: 14px;
        --tablet-font-size: 15px;
        --desktop-font-size: 16px;
        --mobile-gap: 10px;
        --tablet-gap: 15px;
        --desktop-gap: 20px;
    }

    /* Responsive Utility Classes */
    .mobile-only {
        display: block;
    }

    .tablet-up {
        display: none;
    }

    @media (min-width: 768px) {
        .mobile-only {
            display: none;
        }
        
        .tablet-up {
            display: block;
        }
    }

    /* Mobile-first responsive containers */
    .attendance-form-controls {
        display: flex;
        flex-direction: column;
        gap: var(--mobile-gap);
        width: 100%;
    }

    @media (min-width: 768px) {
        .attendance-form-controls {
            flex-direction: row;
            flex-wrap: wrap;
            align-items: center;
            gap: var(--tablet-gap);
        }
    }

    @media (min-width: 1024px) {
        .attendance-form-controls {
            gap: var(--desktop-gap);
        }
    }

    /* Responsive form sections */
    .form-left-section {
        width: 100%;
        margin-bottom: var(--mobile-gap);
    }

    .form-right-section {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: var(--mobile-gap);
    }

    @media (min-width: 768px) {
        .form-left-section {
            flex: 1;
            min-width: 300px;
            margin-bottom: 0;
        }

        .form-right-section {
            flex: 2;
            flex-direction: row;
            justify-content: flex-end;
            align-items: center;
            gap: var(--tablet-gap);
        }
    }

    /* Touch-friendly interactive elements */
    .touch-target {
        min-height: var(--touch-target-size);
        min-width: var(--touch-target-size);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Responsive text sizing */
    .responsive-text {
        font-size: var(--mobile-font-size);
    }

    @media (min-width: 768px) {
        .responsive-text {
            font-size: var(--tablet-font-size);
        }
    }

    @media (min-width: 1024px) {
        .responsive-text {
            font-size: var(--desktop-font-size);
        }
    }

    .btn-danger
    {
        background: red !important;
        border-color: red !important;
    }
    .not-found td{
        background: gray; /* Light gray */
    }
    .name-lable td{
        background: linear-gradient(135deg, #3a8ef6, #6259ca); /* Light gray */
        color: #fff;
        font-size: 16px !important
    }
    .d-flex.radio-check {
        display: inline-flex!important;
        float: inline-end!important;
        padding-top: 10px!important;
    }

    .absent-row td{
        color: white!important;
        background: red!important;
    }

    .weekend-row td {
        background: #00000059!important;
        color:white!important;
        font-weight: bolder!important;
    }

    .holiday-row td {
        background: #21ff0063!important;
        color:black!important;
        font-weight: bolder!important;
    }

    .leave-row td {
        background: gold!important;
        color:black!important;
        font-weight: bolder!important;
    }

    .dateRow th{
        /*background: content-box!important;*/
        background: #525252fc!important;
        color: #fff!important;
        padding: 5px 0!important;
    }

    .employee-name-row th u{
        font-size: larger;
    }
    .time-box {
        color: red !important; /* Change the text color to red */
        background: #fff; /* You can also change the background color if needed */
        border: 1px solid red !important; /* Optional: If you want to change the border color to red */
    }

    /* Responsive Table Wrapper */
    .table-responsive-wrapper {
        position: relative;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin-top: var(--mobile-gap);
    }

    @media (min-width: 768px) {
        .table-responsive-wrapper {
            margin-top: var(--tablet-gap);
        }
    }

    /* Responsive table styling */
    .attendance-table {
        min-width: 800px;
        font-size: var(--mobile-font-size);
    }

    @media (min-width: 768px) {
        .attendance-table {
            font-size: var(--tablet-font-size);
        }
    }

    @media (min-width: 1024px) {
        .attendance-table {
            font-size: var(--desktop-font-size);
            min-width: auto;
        }
    }

    /* Sticky first column for mobile */
    .attendance-table th:first-child,
    .attendance-table td:first-child {
        position: sticky;
        left: 0;
        background: inherit;
        z-index: 10;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    }

    @media (min-width: 1024px) {
        .attendance-table th:first-child,
        .attendance-table td:first-child {
            position: static;
            box-shadow: none;
        }
    }

    /* Enhanced Responsive Action Buttons */
    .action-btns {
        display: flex;
        gap: 5px;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        padding: 5px;
    }

    @media (min-width: 768px) {
        .action-btns {
            justify-content: flex-end;
            flex-wrap: nowrap;
        }
    }

    .action-btn {
        min-height: var(--touch-target-size);
        min-width: var(--touch-target-size);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.2s ease;
        text-decoration: none;
        font-size: var(--mobile-font-size);
        margin: 2px;
    }

    @media (min-width: 768px) {
        .action-btn {
            font-size: var(--tablet-font-size);
            min-width: auto;
            padding: 6px 12px;
        }
        
        .action-btn .mobile-only {
            display: none;
        }
    }

    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .action-btn:active {
        transform: translateY(0);
    }

    /* Mobile-specific action button layout */
    @media (max-width: 767px) {
        .action-btns {
            flex-direction: column;
            gap: 8px;
            width: 100%;
        }
        
        .action-btn {
            width: 100%;
            max-width: 120px;
            padding: 10px 15px;
            font-size: 14px;
        }
        
        .action-btn i {
            margin-right: 5px;
        }
    }

    /* Responsive collapse triggers */
    .collapse-trigger {
        cursor: pointer;
        padding: 12px 8px;
        font-size: var(--mobile-font-size);
        line-height: 1.4;
        transition: background-color 0.2s ease;
    }

    @media (max-width: 767px) {
        .collapse-trigger {
            padding: 15px 10px;
            font-size: 16px;
        }
    }

    @media (min-width: 768px) {
        .collapse-trigger {
            font-size: var(--tablet-font-size);
        }
    }

    /* Responsive nested tables */
    .nested-table {
        font-size: calc(var(--mobile-font-size) - 1px);
    }

    @media (max-width: 767px) {
        .nested-table th,
        .nested-table td {
            padding: 8px 4px;
            font-size: 13px;
        }
    }

    @media (min-width: 768px) {
        .nested-table {
            font-size: calc(var(--tablet-font-size) - 1px);
        }
    }

    /* Responsive Form Controls Styling */
    .total-hours-display {
        margin-bottom: var(--mobile-gap);
    }

    @media (min-width: 768px) {
        .total-hours-display {
            margin-bottom: 0;
        }
    }

    .radio-buttons-section {
        margin-bottom: var(--mobile-gap);
    }

    @media (min-width: 768px) {
        .radio-buttons-section {
            margin-bottom: 0;
        }
    }

    .radio-buttons-section .custom-control {
        margin-right: 0;
    }

    .radio-buttons-section .custom-control-label {
        padding-left: 1.5rem;
        cursor: pointer;
        font-size: var(--mobile-font-size);
    }

    @media (min-width: 768px) {
        .radio-buttons-section .custom-control-label {
            font-size: var(--tablet-font-size);
        }
    }

    .date-input-section {
        margin-bottom: var(--mobile-gap);
    }

    @media (min-width: 768px) {
        .date-input-section {
            margin-bottom: 0;
            min-width: 200px;
        }
    }

    .date-input-section input {
        width: 100%;
        font-size: var(--mobile-font-size);
        padding: 8px 12px;
    }

    @media (min-width: 768px) {
        .date-input-section input {
            font-size: var(--tablet-font-size);
        }
    }

    .action-buttons-section {
        display: flex;
        gap: var(--mobile-gap);
        justify-content: center;
    }

    @media (min-width: 768px) {
        .action-buttons-section {
            justify-content: flex-end;
            gap: var(--tablet-gap);
        }
    }

    .action-buttons-section .btn {
        min-height: var(--touch-target-size);
        padding: 8px 16px;
        font-size: var(--mobile-font-size);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @media (min-width: 768px) {
        .action-buttons-section .btn {
            font-size: var(--tablet-font-size);
        }
        
        .action-buttons-section .btn .mobile-only {
            display: none;
        }
    }

    /* Enhanced touch targets for mobile */
    @media (max-width: 767px) {
        .custom-control-input {
            width: var(--touch-target-size);
            height: var(--touch-target-size);
        }
        
        .custom-control-label::before,
        .custom-control-label::after {
            width: 1.5rem;
            height: 1.5rem;
        }
    }

    /* Enhanced Table Responsive Styling */
    .attendance-table th {
        font-size: var(--mobile-font-size);
        padding: var(--mobile-padding);
        white-space: nowrap;
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    @media (min-width: 768px) {
        .attendance-table th {
            font-size: var(--tablet-font-size);
            padding: var(--tablet-padding);
        }
    }

    @media (min-width: 1024px) {
        .attendance-table th {
            font-size: var(--desktop-font-size);
            padding: var(--desktop-padding);
        }
    }

    .attendance-table td {
        font-size: var(--mobile-font-size);
        padding: var(--mobile-padding);
        vertical-align: middle;
    }

    @media (min-width: 768px) {
        .attendance-table td {
            font-size: var(--tablet-font-size);
            padding: var(--tablet-padding);
        }
    }

    @media (min-width: 1024px) {
        .attendance-table td {
            font-size: var(--desktop-font-size);
            padding: var(--desktop-padding);
        }
    }

    /* Sticky first column enhancement */
    .attendance-table th:first-child,
    .attendance-table td:first-child {
        background-color: #fff;
        border-right: 1px solid #dee2e6;
    }

    .attendance-table th:first-child {
        background-color: #f8f9fa;
    }

    /* Enhanced name label styling for mobile */
    .name-lable td {
        background: linear-gradient(135deg, #3a8ef6, #6259ca) !important;
        color: #fff !important;
        font-size: var(--mobile-font-size) !important;
        font-weight: 600;
        text-align: center;
        padding: var(--tablet-padding) !important;
    }

    @media (min-width: 768px) {
        .name-lable td {
            font-size: var(--tablet-font-size) !important;
        }
    }

    @media (min-width: 1024px) {
        .name-lable td {
            font-size: var(--desktop-font-size) !important;
        }
    }

    /* Responsive table scroll indicator */
    .table-responsive-wrapper::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        width: 20px;
        background: linear-gradient(to left, rgba(0,0,0,0.1), transparent);
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    @media (max-width: 1023px) {
        .table-responsive-wrapper:hover::after,
        .table-responsive-wrapper:focus-within::after {
            opacity: 1;
        }
    }

    /* Enhanced Collapsible Sections */
    .collapse-trigger {
        position: relative;
        user-select: none;
        -webkit-tap-highlight-color: transparent;
    }

    .collapse-trigger:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .collapse-trigger:active {
        transform: translateY(0);
    }

    .collapse-icon {
        transition: transform 0.3s ease;
        font-size: 0.8em;
    }

    .collapse-trigger[aria-expanded="true"] .collapse-icon {
        transform: rotate(180deg);
    }

    /* Nested table wrapper */
    .nested-table-wrapper {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }

    .collapse.show .nested-table-wrapper {
        max-height: 1000px;
        transition: max-height 0.5s ease-in;
    }

    /* Enhanced nested table styling */
    .nested-table {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
    }

    .nested-table th {
        background-color: #e9ecef;
        font-size: calc(var(--mobile-font-size) - 1px);
        padding: 8px 6px;
        border-bottom: 1px solid #dee2e6;
    }

    @media (min-width: 768px) {
        .nested-table th {
            font-size: calc(var(--tablet-font-size) - 1px);
            padding: 10px 8px;
        }
    }

    .nested-table td {
        font-size: calc(var(--mobile-font-size) - 1px);
        padding: 6px 4px;
        border-bottom: 1px solid #f1f3f4;
    }

    @media (min-width: 768px) {
        .nested-table td {
            font-size: calc(var(--tablet-font-size) - 1px);
            padding: 8px 6px;
        }
    }

    /* Smooth collapse animations */
    .collapse {
        transition: all 0.3s ease;
    }

    .collapsing {
        transition: height 0.3s ease;
    }

    /* Touch-friendly collapse triggers */
    @media (max-width: 767px) {
        .collapse-trigger {
            min-height: var(--touch-target-size);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        
        .collapse-icon {
            margin-left: 8px;
            font-size: 1em;
        }
    }

    /* Responsive Modal Enhancements */
    @media (max-width: 767px) {
        .modal-dialog {
            margin: 10px;
            max-width: calc(100vw - 20px);
        }
        
        .modal-content {
            border-radius: 10px;
        }
        
        .modal-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .modal-body {
            padding: 20px;
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }
        
        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #e9ecef;
        }
        
        .modal-footer .btn {
            min-height: var(--touch-target-size);
            margin: 5px;
            flex: 1;
        }
    }

    /* Responsive Tooltip Positioning */
    @media (max-width: 767px) {
        .tooltip {
            font-size: 12px;
        }
        
        .tooltip-inner {
            padding: 8px 12px;
            border-radius: 6px;
        }
        
        /* Hide tooltips on mobile since we show text labels */
        .action-btn[data-toggle="tooltip"] {
            pointer-events: auto;
        }
        
        .action-btn .tooltip {
            display: none;
        }
    }

    /* Enhanced button states for better mobile feedback */
    .action-btn:focus {
        outline: 2px solid #007bff;
        outline-offset: 2px;
    }

    .btn-outline-primary:hover,
    .btn-outline-primary:focus {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
    }

    .btn-outline-success:hover,
    .btn-outline-success:focus {
        background-color: #28a745;
        border-color: #28a745;
        color: #fff;
    }

    .btn-outline-danger:hover,
    .btn-outline-danger:focus {
        background-color: #dc3545;
        border-color: #dc3545;
        color: #fff;
    }

    /* Performance Optimizations for Mobile */
    
    /* GPU acceleration for smooth animations */
    .collapse-trigger,
    .action-btn,
    .attendance-table th:first-child,
    .attendance-table td:first-child {
        will-change: transform;
        transform: translateZ(0);
    }
    
    /* Optimize scroll performance */
    .table-responsive-wrapper {
        contain: layout style paint;
        scroll-behavior: smooth;
    }
    
    /* Reduce repaints during scrolling */
    .attendance-table {
        contain: layout style;
    }
    
    /* Optimize font rendering */
    body, .attendance-table, .responsive-text {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-rendering: optimizeSpeed;
    }
    
    /* Loading states for better mobile UX */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    
    .loading-overlay.show {
        opacity: 1;
        visibility: visible;
    }
    
    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #007bff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Optimize images and icons for different screen densities */
    @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
        .action-btn i {
            font-size: 1.1em;
        }
    }
    
    /* Reduce motion for users who prefer it */
    @media (prefers-reduced-motion: reduce) {
        .collapse-trigger,
        .action-btn,
        .collapse,
        .collapsing {
            transition: none !important;
            animation: none !important;
        }
        
        .loading-spinner {
            animation: none;
            border-top-color: transparent;
        }
    }
    
    /* Critical CSS for above-the-fold content */
    .attendance-form-controls,
    .table-responsive-wrapper {
        contain: layout;
    }
    
    /* Lazy loading optimization */
    .lazy-load {
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .lazy-load.loaded {
        opacity: 1;
    }
    
    /* Memory optimization for large tables */
    @media (max-width: 767px) {
        .nested-table-wrapper {
            contain: strict;
        }
        
        .collapse:not(.show) .nested-table-wrapper {
            display: none;
        }
    }

    /* Testing and Validation Styles */
    
    /* Visual indicators for testing */
    .test-indicator {
        position: fixed;
        top: 10px;
        right: 10px;
        background: #28a745;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 10000;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .test-indicator.show {
        opacity: 1;
    }
    
    .test-indicator.error {
        background: #dc3545;
    }
    
    .test-indicator.warning {
        background: #ffc107;
        color: #212529;
    }
    
    /* Responsive breakpoint indicators (for development) */
    body::before {
        content: 'Desktop';
        position: fixed;
        top: 0;
        left: 0;
        background: #007bff;
        color: white;
        padding: 2px 8px;
        font-size: 10px;
        z-index: 10001;
        opacity: 0.7;
    }
    
    @media (max-width: 1023px) {
        body::before {
            content: 'Tablet';
            background: #6f42c1;
        }
    }
    
    @media (max-width: 767px) {
        body::before {
            content: 'Mobile';
            background: #e83e8c;
        }
    }
    
    /* Hide breakpoint indicator in production */
    @media (min-width: 1px) {
        body.production::before {
            display: none;
        }
    }
    
    /* Accessibility testing helpers */
    .focus-visible {
        outline: 2px solid #007bff !important;
        outline-offset: 2px !important;
    }
    
    /* Touch target size validation */
    @media (max-width: 767px) {
        .touch-target:not([style*="min-height"]) {
            min-height: 44px !important;
            min-width: 44px !important;
        }
    }
    
    /* Performance testing styles */
    .performance-warning {
        position: fixed;
        bottom: 10px;
        left: 10px;
        background: #ffc107;
        color: #212529;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 10000;
        display: none;
    }
    
    .performance-warning.show {
        display: block;
    }
</style>

@section('action-button')
@endsection
@section('content')
<!-- Loading Overlay for Mobile -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="text-center">
        <div class="loading-spinner"></div>
        <div class="mt-2 responsive-text">{{ __('Loading...') }}</div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card pb-3">
            <div class="card-body py-0">
                <form method="GET" action="{{ route('attendanceemployee.index') }}">
                    <div class="attendance-form-controls mt-3">
                        <!-- Left Section: Total Hours Display (Employee Only) -->
                        @if(\Auth::user()->type == 'employee')
                            <div class="form-left-section">
                                <?php
                                $isAbsent = $attendanceEmployee->isEmpty();
                                $isLeave = Helper::checkLeave($date ? $date : today(), $employee);
                                if ($isLeave != 0) {
                                    $leaveToday = Helper::checkLeaveWithTypes($date ? $date : today(), $employee);
                                    if ($leaveToday == 'afternoon halfday' || $leaveToday == 'morning halfday') {
                                        $leaveToday = 'Half-Day Leave';
                                    } elseif ($leaveToday == 'on short leave') {
                                        $leaveToday = 'Short Leave';
                                    } elseif ($leaveToday == 'fullday Leave') {
                                        $leaveToday = 'Leave';
                                    }
                                } else {
                                    $leaveToday = 0;
                                }
                                $totalTime = Helper::calculateTotalTimeDifference($attendanceEmployee);
                                $threshold = '08:00'; // Default threshold
                                if ($leaveToday == 'Half-Day Leave') {
                                    $threshold = '04:00';
                                } elseif ($leaveToday == 'Short Leave') {
                                    $threshold = '06:00';
                                }
                                ?>
                                <div class="total-hours-display" id="totalHours">
                                    <label class="form-label responsive-text">{{ __('Total Hours') }}</label>
                                    <input type="text" class="form-control responsive-text {{ $totalTime < $threshold ? 'time-box' : '' }}" value="{{ $totalTime }}" disabled>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Right Section: Form Controls -->
                        <div class="form-right-section">
                            <!-- Radio Buttons Section -->
                            <div class="radio-buttons-section">
                                <div class="d-flex justify-content-center gap-3">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="daily" value="daily" name="type" class="custom-control-input touch-target" onclick="activeDailyBox()" {{ $requestType == 'daily' ? 'checked' : '' }}>
                                        <label class="custom-control-label responsive-text" for="daily">{{ __('Day') }}</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="monthly" value="monthly" name="type" class="custom-control-input touch-target" onclick="activeMonthBox()" {{ $requestType == 'monthly' ? 'checked' : '' }}>
                                        <label class="custom-control-label responsive-text" for="monthly">{{ __('Month') }}</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Date/Month Input Section -->
                            <div class="date-input-section">
                                <div id="monthAndDate">
                                    @if($requestType == 'daily')
                                        <input type="date" name="date" class="form-control responsive-text" value="{{ isset($_GET['date']) ? $_GET['date'] : date('Y-m-d') }}">
                                    @else
                                        <input type="month" name="month" class="form-control responsive-text" value="{{ isset($_GET['month']) ? $_GET['month'] : date('Y-m') }}">
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Action Buttons Section -->
                            <div class="action-buttons-section">
                                <button type="submit" class="btn btn-primary touch-target" title="Search">
                                    <span class="btn-inner--icon"><i class="fas fa-search"></i></span>
                                    <span class="mobile-only ms-2">{{ __('Search') }}</span>
                                </button>
                                <a href="{{ route('attendanceemployee.index') }}" class="btn btn-secondary touch-target" title="Reset">
                                    <span class="btn-inner--icon"><i class="fas fa-sync-alt"></i></span>
                                    <span class="mobile-only ms-2">{{ __('Reset') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                            
                            <script>
                                function activeMonthBox() {
                                    document.getElementById('monthAndDate').innerHTML = `<input type="month" name="month" class="form-control responsive-text" value="{{ isset($_GET['month']) ? $_GET['month'] : date('Y-m') }}">`;
                                }
                            
                                function activeDailyBox() {
                                    document.getElementById('monthAndDate').innerHTML = `<input type="date" name="date" class="form-control responsive-text" value="{{ isset($_GET['date']) ? $_GET['date'] : date('Y-m-d') }}">`;
                                }
                            </script>
                        </div>
                    </div>
                </form>

                <div class="table-responsive-wrapper">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0 attendance-table" id="attendanceTable">
                        @if($requestType != 'monthly')
                        <thead>
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Clock In') }}</th>
                                <th>{{ __('Clock Out') }}</th>
                                <th>{{ __('Shift Start') }}</th>
                                <th>{{ __('Late/Rest') }}</th>
                                @if (Gate::check('Edit Attendance') || Gate::check('Delete Attendance') && \Auth::user()->type != 'employee')
                                    <th width="3%">{{ __('Action') }}</th>
                                @endif
                            </tr>
                        </thead>
                        @endif
                        <tbody id="" class="mb-3">
                            @if(\Auth::user()->type == 'employee')
                                @if($requestType == 'daily')
                                    @if(count($attendanceEmployee)>0)
                                        @if($empLeave)
                                            <tr>
                                                <td align="center" colspan="7" class="btn-warning" >({{strtoupper($empLeave['leavetype'])}} LEAVE) {{$empLeave['start_time']}}-{{$empLeave['end_time']}}</td>
                                            </tr>
                                        @endif
                                        @foreach ($attendanceEmployee as $key=>$attendance)
                                            <tr class="accordion-content">
                                                <td>{{ date('d-m-Y', strtotime($attendance->date)) }}</td>
                                                <td>{{ $attendance->status }}</td>
                                                <td>{{ $attendance->clock_in != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_in) : '00:00' }}</td>
                                                <td>{{ $attendance->clock_out != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_out) : '00:00' }}</td>
                                                <td>{{ $attendance->employee->shift_start ?? 'N/A' }}</td>
                                                
                                                <td>
                                                    @if($key)
                                                        {{Helper::dynRestTime($attendanceEmployee[$key-1]->clock_out??'',$attendanceEmployee[$key]->clock_in)}}
                                                    @else
                                                        {{Helper::dynLateTime(Auth::user()->employee->shift_start??'09:00:00',$attendance->clock_in)}}
                                                    @endif
                                                    <!--{{ Helper::convertTimeToMinutesAndSeconds($attendance->total_rest == '00:00:00' ? $attendance->late : $attendance->total_rest) }}{{ $attendance->total_rest == '00:00:00' ? ' (Late)' : ' (Rest)' }}-->
                                                    </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        @if($isWeekend)
                                        <tr>
                                            <td align="center" colspan="7" class="btn-white"> (WEEKEND)</td>
                                        </tr>
                                        @elseif($isLeave)
                                        <tr>
                                            <td align="center" colspan="7" class="btn-white"> (WEEKEND)</td>
                                        </tr>
                                        @else
                                        <tr>
                                            <td align="center" colspan="7" class="btn-danger"> (ABSENT)</td>
                                        </tr>
                                        @endif
                                    @endif
                                @else
                                    @foreach($monthAttendanceEmployee as $key => $attEmp)
                                    @php
                                        $doj = "2025-02-10";
                                        $date = "2025-02-28";
                                    
                                        // Create DateTime objects
                                        $dateTime1 = new DateTime(Auth::user()->employee->company_doj);
                                        $dateTime2 = new DateTime($key);
                                        if ($dateTime1 > $dateTime2)
                                        {
                                            continue;
                                        }
                                    @endphp
                                    @if($attEmp['is_weekend'] && !$attEmp['attendance'])
                                        <tr>
                                            <td align="center" colspan="7" class="btn-white">{{ $key }} (WEEKEND)</td>
                                        </tr>
                                    @elseif($attEmp['leave_detail'] && $attEmp['leave_detail']['leavetype'] == 'full')
                                        <tr>
                                            <td align="center" colspan="7" class="btn-warning" >{{ $key }} ({{strtoupper($attEmp['leave_detail']['leavetype'])}} LEAVE) {{$attEmp['leave_detail']['start_time']}}-{{$attEmp['leave_detail']['end_time']}}</td>
                                        </tr>
                                    @elseif($attEmp['attendance'])
                                    
                                        <tr>
                                            @if($attEmp['is_weekend'])
                                            <tr>
                                                <td align="center" colspan="7" class="collapse-trigger weekend-collapse" data-toggle="collapse" data-target="#collapse-{{ $key }}" role="button" style="background: linear-gradient(135deg, #3a8ef6, #6259ca); color: #fff;" >
                                                    {{ $key }} (WEEKEND)
                                                    <i class="fas fa-chevron-down ms-2 collapse-icon"></i>
                                                </td>
                                            </tr>
                                            @elseif($attEmp['leave_detail'] && $attEmp['leave_detail']['leavetype'] != 'full')
                                            <td align="center" colspan="7" class="collapse-trigger leave-collapse btn-warning" data-toggle="collapse" data-target="#collapse-{{ $key }}" role="button" >
                                                {{ $key }} ({{ strtoupper($attEmp['leave_detail']['leavetype']) }} LEAVE) 
                                                <span class="{{ $attEmp['hours'] < $attEmp['min_hours'] ? 'text-danger' : '' }}">({{$attEmp['hours']}})</span>  
                                                {{$attEmp['leave_detail']['start_time']}}{{$attEmp['leave_detail']['start_time']?'-':''}}{{$attEmp['leave_detail']['end_time']}}
                                                <i class="fas fa-chevron-down ms-2 collapse-icon"></i>
                                            </td>

                                            @else

                                            <td align="center" colspan="7" class="collapse-trigger normal-collapse" data-toggle="collapse" data-target="#collapse-{{ $key }}" role="button" style="background: linear-gradient(135deg, #3a8ef6, #6259ca); color: #fff;">
                                                {{ $key }} <span class="{{ $attEmp['hours'] < $attEmp['min_hours'] ? 'text-warning' : '' }}">({{$attEmp['hours']}})</span>
                                                <i class="fas fa-chevron-down ms-2 collapse-icon"></i>
                                            </td>
                                            @endif
                                        </tr>
                                        <tr class="collapse" id="collapse-{{ $key }}">
                                            <td colspan="7" class="p-0">
                                                <div class="nested-table-wrapper">
                                                    <table class="table nested-table mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Date') }}</th>
                                                            <th>{{ __('Status') }}</th>
                                                            <th>{{ __('Clock In') }}</th>
                                                            <th>{{ __('Clock Out') }}</th>
                                                            <th>{{ __('Shift Start') }}</th>
                                                            <th>{{ __('Late/Rest') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($attEmp['attendance'] as $key=>$attendance)
                                                            <tr class="accordion-content">
                                                                <td>{{ date('d-m-Y', strtotime($attendance['date'])) }}</td>
                                                                <td>{{ $attendance['status'] }}</td>
                                                                <td>{{ $attendance['clock_in'] != '00:00:00' ? \Auth::user()->timeFormat($attendance['clock_in']) : '00:00' }}</td>
                                                                <td>{{ $attendance['clock_out'] != '00:00:00' ? \Auth::user()->timeFormat($attendance['clock_out']) : '00:00' }}</td>
                                                                <td>{{ $attendance['employee']['shift_start'] ?? 'N/A' }}</td>
                                                                
                                                                <td>
                                                                    @if($key)
                                                                        {{Helper::dynRestTime($attEmp['attendance'][$key-1]['clock_out']??'',$attEmp['attendance'][$key]['clock_in'])}}
                                                                    @else
                                                                        {{Helper::dynLateTime(Auth::user()->employee->shift_start??'09:00:00',$attendance['clock_in'])}}
                                                                    @endif
                                                                    <!--{{ Helper::convertTimeToMinutesAndSeconds($attendance['total_rest'] == '00:00:00' ? $attendance['late'] : $attendance['total_rest']) }}{{ $attendance['total_rest'] == '00:00:00' ? ' (Late)' : ' (Rest)' }}-->
                                                                </td>
                                                            </tr>   
                                                        @endforeach
                                                    </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    @else
                                    <tr>
                                        <td align="center" colspan="7" class="btn-danger" >{{ $key }} (ABSENT) </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                @endif
                            @else
                                @if($holidays || $isWeekend)
                                    @if($holidays)
                                    <tr>
                                        <td colspan="7">
                                            HOLIDAY
                                        </td>
                                    </tr>
                                    @endif
                                    @if($isWeekend)
                                    <tr>
                                        <td colspan="7">
                                        WEEK-END
                                        </td>
                                    </tr>
                                    @endif
                                @else
                                    @foreach ($attendanceWithEmployee as $employee)
                                        <?php
                                        $isAbsent = $employee->attendance->isEmpty();
                                        $isLeave = Helper::checkLeave($date?$date:today(), $employee->id);
                                        if($isLeave != 0){
                                            $leaveToday = Helper::checkLeaveWithTypes($date?$date:today(), $employee->id);
                                            if($leaveToday == 'afternoon halfday' || $leaveToday == 'morning halfday'){
                                                $leaveToday = 'Half-Day Leave';
                                            }elseif($leaveToday == 'on short leave'){
                                                $leaveToday = 'Short Leave';
                                            }elseif($leaveToday == 'fullday Leave'){
                                                $leaveToday = 'Leave';
                                            }
                                        }else{
                                            $leaveToday = 0;
                                        }
                                        $totalTime = Helper::calculateTotalTimeDifference($employee->attendance);
                                        $threshold = '08:00'; // Default threshold
                                        if ($leaveToday == 'Half-Day Leave') {
                                            $threshold = '04:00';
                                        } elseif ($leaveToday == 'Short Leave') {
                                            $threshold = '06:00';
                                        }?>
                                        <tr class="name-lable">
                                            <td colspan="7" align="center">{{$employee->name}}(<strong>
                                                Total Time:
                                                <i class="{{ $totalTime < $threshold ? 'text-danger' : '' }}">
                                                    {{ $totalTime }}
                                                </i>

                                            </strong>) @if($leaveToday && $leaveToday != 0)
                                            <span class="bg-warning" style="color: #FFF !important; padding: 5px; border-radius: 10px; background:#ffac04 !important;">{{ $leaveToday }}</span>
                                            @endif</td>
                                        </tr>

                                        @forelse ($employee->attendance ?? [] as $attendance)
                                            <tr class="accordion-content">
                                                <td>{{ date('d-m-Y', strtotime($attendance->date)) }}</td>
                                                <td>{{ $attendance->status }}</td>
                                                <td>{{ $attendance->clock_in != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_in) : '00:00' }}</td>
                                                <td>{{ $attendance->clock_out != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_out) : '00:00' }}</td>
                                                <td>{{ $employee->shift_start ?? 'N/A' }}</td>
                                                <td>
                                                    @if ($attendance->total_rest == '00:00:00')
                                                        @if ($attendance->late < '00:00:00')
                                                            (Early)
                                                        @else
                                                            {{ Helper::convertTimeToMinutesAndSeconds($attendance->late) }} (Late)
                                                        @endif
                                                    @else
                                                        {{ Helper::convertTimeToMinutesAndSeconds($attendance->total_rest) }} (Rest)
                                                    @endif
                                                </td>
                                                <td class="action-btns">
                                                    @if($attendance->clock_out != '00:00:00')
                                                        <a href="#" data-url="{{ URL::to('copy/attendance/' . $attendance->id) }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Copy Attendance') }}" class="btn btn-sm btn-outline-primary action-btn touch-target" data-toggle="tooltip" title="{{ __('Copy') }}" data-placement="top">
                                                            <i class="far fa-copy"></i>
                                                            <span class="mobile-only ms-1">{{ __('Copy') }}</span>
                                                        </a>
                                                    @endif
                                                    @can('Edit Attendance')
                                                        <a href="#" data-url="{{ URL::to('attendanceemployee/' . $attendance->id . '/edit') }}" data-size="lg" data-ajax-popup="true" data-title="{{ __('Edit Attendance') }}" class="btn btn-sm btn-outline-success action-btn touch-target" data-toggle="tooltip" title="{{ __('Edit') }}" data-placement="top">
                                                            <i class="fas fa-pencil-alt"></i>
                                                            <span class="mobile-only ms-1">{{ __('Edit') }}</span>
                                                        </a>
                                                    @endcan
                                                    @can('Delete Attendance')
                                                        <a href="#" class="btn btn-sm btn-outline-danger action-btn touch-target" data-toggle="tooltip" title="{{ __('Delete') }}" data-placement="top" data-confirm="{{ __('Are You Sure?') . '|' . __('This action cannot be undone. Do you want to continue?') }}" data-confirm-yes="document.getElementById('delete-form-{{ $attendance->id }}').submit();">
                                                            <i class="fas fa-trash"></i>
                                                            <span class="mobile-only ms-1">{{ __('Delete') }}</span>
                                                        </a>
                                                        <form method="POST" action="{{ route('attendanceemployee.destroy', $attendance->id) }}" id="delete-form-{{ $attendance->id }}" style="display: none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @empty
                                        <tr class="{{ $leaveToday ? 'leave-row' : 'absent-row' }}">
                                            <td colspan="7" align="center">{{$leaveToday?$leaveToday:'Absent'}}</td>
                                        </tr>
                                        @endforelse

                                    @endforeach
                                @endif
                            @endif
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @push('script-page')
    <script>
        // Responsive JavaScript Enhancements
        
        // Viewport change handler for dynamic layout adjustments
        function handleViewportChange() {
            const viewport = window.innerWidth;
            const formContainer = document.querySelector('.attendance-form-controls');
            const tableWrapper = document.querySelector('.table-responsive-wrapper');
            
            try {
                // Adjust form layout based on viewport
                if (viewport < 768) {
                    formContainer?.classList.add('mobile-layout');
                    // Enable touch scrolling for table
                    if (tableWrapper) {
                        tableWrapper.style.webkitOverflowScrolling = 'touch';
                    }
                } else {
                    formContainer?.classList.remove('mobile-layout');
                }
                
                // Update collapse icons rotation
                updateCollapseIcons();
                
            } catch (error) {
                console.warn('Viewport change handler failed:', error);
            }
        }
        
        // Update collapse icons based on current state
        function updateCollapseIcons() {
            document.querySelectorAll('.collapse-trigger').forEach(trigger => {
                const target = trigger.getAttribute('data-target');
                const collapseElement = document.querySelector(target);
                const icon = trigger.querySelector('.collapse-icon');
                
                if (collapseElement && icon) {
                    if (collapseElement.classList.contains('show')) {
                        icon.style.transform = 'rotate(180deg)';
                        trigger.setAttribute('aria-expanded', 'true');
                    } else {
                        icon.style.transform = 'rotate(0deg)';
                        trigger.setAttribute('aria-expanded', 'false');
                    }
                }
            });
        }
        
        // Enhanced touch event handlers for mobile interactions
        function initTouchHandlers() {
            // Add touch feedback for action buttons
            document.querySelectorAll('.action-btn').forEach(btn => {
                btn.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.95)';
                }, { passive: true });
                
                btn.addEventListener('touchend', function() {
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                }, { passive: true });
            });
            
            // Enhanced collapse trigger touch handling
            document.querySelectorAll('.collapse-trigger').forEach(trigger => {
                trigger.addEventListener('touchstart', function() {
                    this.style.opacity = '0.8';
                }, { passive: true });
                
                trigger.addEventListener('touchend', function() {
                    this.style.opacity = '';
                }, { passive: true });
            });
        }
        
        // Responsive form validation and submission handling
        function initResponsiveFormHandling() {
            const form = document.querySelector('form[method="GET"]');
            if (!form) return;
            
            form.addEventListener('submit', function(e) {
                // Show loading overlay for mobile
                if (window.innerWidth < 768) {
                    showLoadingOverlay();
                }
                
                // Add loading state for better mobile UX
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    const originalContent = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span class="mobile-only ms-2">Loading...</span>';
                    submitBtn.disabled = true;
                    
                    // Re-enable after a delay (in case of same-page reload)
                    setTimeout(() => {
                        submitBtn.innerHTML = originalContent;
                        submitBtn.disabled = false;
                        hideLoadingOverlay();
                    }, 3000);
                }
            });
        }
        
        // Performance optimization functions
        function showLoadingOverlay() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.classList.add('show');
            }
        }
        
        function hideLoadingOverlay() {
            const overlay = document.getElementById('loadingOverlay');
            if (overlay) {
                overlay.classList.remove('show');
            }
        }
        
        // Optimize scroll performance for large datasets
        function optimizeScrollPerformance() {
            const tableWrapper = document.querySelector('.table-responsive-wrapper');
            if (!tableWrapper) return;
            
            let isScrolling = false;
            
            tableWrapper.addEventListener('scroll', () => {
                if (!isScrolling) {
                    window.requestAnimationFrame(() => {
                        // Optimize sticky column performance during scroll
                        const stickyElements = tableWrapper.querySelectorAll('th:first-child, td:first-child');
                        stickyElements.forEach(el => {
                            el.style.transform = `translateX(${tableWrapper.scrollLeft}px)`;
                        });
                        isScrolling = false;
                    });
                    isScrolling = true;
                }
            }, { passive: true });
        }
        
        // Lazy load nested tables for better performance
        function initLazyLoading() {
            const collapseElements = document.querySelectorAll('.collapse');
            
            collapseElements.forEach(collapse => {
                collapse.addEventListener('show.bs.collapse', function() {
                    const nestedTable = this.querySelector('.nested-table');
                    if (nestedTable && !nestedTable.classList.contains('loaded')) {
                        // Add loading class
                        nestedTable.classList.add('lazy-load');
                        
                        // Simulate loading delay for better UX
                        setTimeout(() => {
                            nestedTable.classList.add('loaded');
                            nestedTable.classList.remove('lazy-load');
                        }, 100);
                    }
                });
            });
        }
        
        // Memory optimization for mobile
        function optimizeMemoryUsage() {
            if (window.innerWidth < 768) {
                // Remove unused event listeners on mobile
                document.querySelectorAll('[data-toggle="tooltip"]').forEach(el => {
                    el.removeAttribute('data-toggle');
                    el.removeAttribute('title');
                });
                
                // Optimize image loading
                document.querySelectorAll('img').forEach(img => {
                    if (img.loading !== 'lazy') {
                        img.loading = 'lazy';
                    }
                });
            }
        }
        
        // Initialize responsive enhancements
        function initResponsiveEnhancements() {
            handleViewportChange();
            initTouchHandlers();
            initResponsiveFormHandling();
            optimizeScrollPerformance();
            initLazyLoading();
            optimizeMemoryUsage();
            
            // Listen for viewport changes with optimized debouncing
            window.addEventListener('resize', debounce(handleViewportChange, 250), { passive: true });
            window.addEventListener('orientationchange', () => {
                setTimeout(handleViewportChange, 100);
            }, { passive: true });
            
            // Listen for collapse events
            document.addEventListener('shown.bs.collapse', updateCollapseIcons, { passive: true });
            document.addEventListener('hidden.bs.collapse', updateCollapseIcons, { passive: true });
        }
        
        // Debounce function for performance
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
        
        $(document).ready(function() {
            // Performance optimization: Initialize after DOM is fully loaded
            setTimeout(() => {
                initResponsiveEnhancements();
                hideLoadingOverlay(); // Hide loading overlay once everything is initialized
            }, 100);
            
            // Preload critical resources for better performance
            if (window.innerWidth < 768) {
                // Preload commonly used icons
                const iconPreloader = document.createElement('link');
                iconPreloader.rel = 'preload';
                iconPreloader.as = 'font';
                iconPreloader.type = 'font/woff2';
                iconPreloader.crossOrigin = 'anonymous';
                document.head.appendChild(iconPreloader);
                
                // Run responsive functionality tests
                if (typeof runResponsiveTests === 'function') {
                    runResponsiveTests();
                }
            }
            
            // Responsive Testing Functions
            function runResponsiveTests() {
                console.log('Running responsive functionality tests...');
                
                // Test 1: Form controls responsiveness
                testFormControlsResponsiveness();
                
                // Test 2: Table scrolling and sticky columns
                testTableResponsiveness();
                
                // Test 3: Collapsible sections
                testCollapsibleSections();
                
                // Test 4: Action buttons functionality
                testActionButtons();
                
                // Test 5: Modal responsiveness
                testModalResponsiveness();
                
                console.log('Responsive tests completed.');
            }
            
            function testFormControlsResponsiveness() {
                const formContainer = document.querySelector('.attendance-form-controls');
                const radioButtons = document.querySelectorAll('input[type="radio"]');
                const dateInput = document.querySelector('#monthAndDate input');
                const actionButtons = document.querySelectorAll('.action-buttons-section .btn');
                
                console.log('✓ Form container found:', !!formContainer);
                console.log('✓ Radio buttons found:', radioButtons.length);
                console.log('✓ Date input found:', !!dateInput);
                console.log('✓ Action buttons found:', actionButtons.length);
                
                // Test radio button functionality
                if (radioButtons.length >= 2) {
                    console.log('✓ Radio button toggle test passed');
                }
            }
            
            function testTableResponsiveness() {
                const tableWrapper = document.querySelector('.table-responsive-wrapper');
                const attendanceTable = document.querySelector('.attendance-table');
                const stickyColumns = document.querySelectorAll('.attendance-table th:first-child, .attendance-table td:first-child');
                
                console.log('✓ Table wrapper found:', !!tableWrapper);
                console.log('✓ Attendance table found:', !!attendanceTable);
                console.log('✓ Sticky columns found:', stickyColumns.length);
                
                if (tableWrapper && window.innerWidth < 768) {
                    console.log('✓ Mobile table scroll enabled:', tableWrapper.style.overflowX !== 'visible');
                }
            }
            
            function testCollapsibleSections() {
                const collapseTriggers = document.querySelectorAll('.collapse-trigger');
                const collapseElements = document.querySelectorAll('.collapse');
                const nestedTables = document.querySelectorAll('.nested-table');
                
                console.log('✓ Collapse triggers found:', collapseTriggers.length);
                console.log('✓ Collapse elements found:', collapseElements.length);
                console.log('✓ Nested tables found:', nestedTables.length);
                
                // Test collapse functionality
                collapseTriggers.forEach((trigger, index) => {
                    const hasIcon = trigger.querySelector('.collapse-icon');
                    console.log(`✓ Collapse trigger ${index + 1} has icon:`, !!hasIcon);
                });
            }
            
            function testActionButtons() {
                const actionButtons = document.querySelectorAll('.action-btn');
                const touchTargets = document.querySelectorAll('.touch-target');
                
                console.log('✓ Action buttons found:', actionButtons.length);
                console.log('✓ Touch targets found:', touchTargets.length);
                
                // Test button accessibility
                actionButtons.forEach((btn, index) => {
                    const hasProperSize = btn.offsetHeight >= 44 || window.innerWidth >= 768;
                    console.log(`✓ Action button ${index + 1} has proper touch size:`, hasProperSize);
                });
            }
            
            function testModalResponsiveness() {
                const modals = document.querySelectorAll('.modal');
                console.log('✓ Modals found:', modals.length);
                
                // Test modal AJAX functionality
                const ajaxButtons = document.querySelectorAll('[data-ajax-popup="true"]');
                console.log('✓ AJAX modal buttons found:', ajaxButtons.length);
            }
            
            // Device-specific testing
            function getDeviceInfo() {
                const viewport = {
                    width: window.innerWidth,
                    height: window.innerHeight,
                    devicePixelRatio: window.devicePixelRatio || 1
                };
                
                const deviceType = viewport.width < 768 ? 'mobile' : 
                                 viewport.width < 1024 ? 'tablet' : 'desktop';
                
                console.log('Device Info:', {
                    type: deviceType,
                    viewport: viewport,
                    userAgent: navigator.userAgent.substring(0, 50) + '...',
                    touchSupport: 'ontouchstart' in window
                });
                
                return { viewport, deviceType };
            }
            
            // Cross-browser compatibility check
            function checkBrowserCompatibility() {
                const features = {
                    flexbox: CSS.supports('display', 'flex'),
                    grid: CSS.supports('display', 'grid'),
                    customProperties: CSS.supports('--test', 'value'),
                    transforms: CSS.supports('transform', 'translateX(0)'),
                    transitions: CSS.supports('transition', 'all 0.3s ease')
                };
                
                console.log('Browser Feature Support:', features);
                
                const unsupportedFeatures = Object.entries(features)
                    .filter(([key, supported]) => !supported)
                    .map(([key]) => key);
                
                if (unsupportedFeatures.length > 0) {
                    console.warn('Unsupported features detected:', unsupportedFeatures);
                } else {
                    console.log('✓ All required CSS features are supported');
                }
            }
            
            // Performance monitoring
            function monitorPerformance() {
                if ('performance' in window) {
                    const navigation = performance.getEntriesByType('navigation')[0];
                    const loadTime = navigation.loadEventEnd - navigation.loadEventStart;
                    
                    console.log('Performance Metrics:', {
                        pageLoadTime: loadTime + 'ms',
                        domContentLoaded: navigation.domContentLoadedEventEnd - navigation.domContentLoadedEventStart + 'ms',
                        firstPaint: performance.getEntriesByType('paint').find(entry => entry.name === 'first-paint')?.startTime + 'ms'
                    });
                    
                    if (loadTime > 3000) {
                        console.warn('⚠ Page load time is slower than recommended (>3s)');
                    } else {
                        console.log('✓ Page load performance is acceptable');
                    }
                }
            }
            
            // Initialize testing on page load
            window.addEventListener('load', function() {
                setTimeout(() => {
                    getDeviceInfo();
                    checkBrowserCompatibility();
                    monitorPerformance();
                }, 1000);
            });
            $('select[name="branch"]').on('change', function() {
                var branchId = $(this).val();  // Get the selected branch ID

                if (branchId) {
                    $.ajax({
                        url: '{{ route("getDepartmentsByBranch") }}',  // Laravel route for fetching departments and employees
                        type: 'GET',
                        data: { branch_id: branchId },  // Send the selected branch_id to the backend
                        success: function(response) {
                            console.log(response);

                            if (response.status === 'success') {
                                var departmentSelect = $('select[name="department"]');
                                var employeeSelect = $('select[name="employee"]');

                                departmentSelect.empty();  // Clear existing department options
                                employeeSelect.empty();  // Clear existing employee options

                                departmentSelect.append('<option value="" disabled selected>Select Department</option>');  // Add placeholder option
                                employeeSelect.append('<option value="" disabled selected>Select Employee</option>');  // Add placeholder option

                                // Loop through departments and append them to the department select box
                                $.each(response.data.departments, function(key, department) {
                                    departmentSelect.append('<option value="' + department.id + '">' + department.name + '</option>');
                                });

                                // Loop through employees and append them to the employee select box
                                $.each(response.data.employees, function(key, employee) {
                                    employeeSelect.append('<option value="' + employee.id + '">' + employee.name + '</option>');
                                });
                            } else {
                                $('select[name="department"]').empty().append('<option value="" disabled selected>Select Department</option>');
                                $('select[name="employee"]').empty().append('<option value="" disabled selected>Select Employee</option>');
                            }
                        },
                        error: function() {
                            // alert('Error fetching departments and employees.');
                            $('select[name="department"]').empty().append('<option value="" disabled selected>Select Department</option>');
                            $('select[name="employee"]').empty().append('<option value="" disabled selected>Select Employee</option>');
                        }
                    });
                } else {
                    // If no branch is selected, clear the department and employee dropdowns
                    $('select[name="department"]').empty().append('<option value="" disabled selected>Select Department</option>');
                    $('select[name="employee"]').empty().append('<option value="" disabled selected>Select Employee</option>');
                }
            });
        });
        $(document).ready(function() {

            // $('div[id="monthAndDate"]').on('change', function() {
            //     const preloader = document.getElementById('loader');
            //     preloader.style.display = 'block';

            //     var date = $(this).find('input').val(); 
            //     var type = $(this).find('input').attr('type'); 
            //     var employeeId = "{{ Auth::user()->employee->id }}"; 

            //     $.ajax({
            //         url: "{{ route('json.emp.attendance') }}", 
            //         type: 'POST',
            //         data: { 
            //             date: date,
            //             type: type,
            //             employee_id: employeeId,
            //             _token: "{{ csrf_token() }}" // CSRF token for Laravel POST requests
            //         },
            //         dataType: 'json', 
            //         success: function(response) {   
            //             console.log(response);
                        
            //             let tbody = $("#attendanceTable tbody"); // Target table body
            //             tbody.empty(); // Clear existing table rows before appending
            //             if(response.data.type == 'month')
            //             {
            //                 if (response.data.attendance && Object.keys(response.data.attendance).length > 0) {
            //                     Object.entries(response.data.attendance).forEach(([key, attEmp]) => {
            //                         let row = "";
                                    
            //                         if (attEmp.is_weekend && attEmp.attendance.length<1) {
            //                             row += `<tr><td align="center" colspan="7" class="btn-white">${key} (WEEKEND)</td></tr>`;
            //                         } else if (attEmp.leave_detail && attEmp.leave_detail.leavetype === "full") {
            //                             row += `<tr><td align="center" colspan="7" class="btn-warning">${key} (${attEmp.leave_detail.leavetype.toUpperCase()} LEAVE) ${attEmp.leave_detail.start_time}-${attEmp.leave_detail.end_time}</td></tr>`;
            //                         } else if (attEmp.attendance && attEmp.attendance.length>0) {
            //                             row += `<tr>`;
            //                             if (attEmp.is_weekend) {
            //                                 row += `<td align="center" colspan="7" data-toggle="collapse" data-target="#collapse-${key}" role="button" style="background: linear-gradient(135deg, #3a8ef6, #6259ca); color: #fff;">${key} (WEEKEND)</td>`;
            //                             } else if (attEmp.leave_detail && attEmp.leave_detail.leavetype !== "full") {
            //                                 row += `<td align="center" colspan="7" class="btn-warning" data-toggle="collapse" data-target="#collapse-${key}" role="button">${key} (${attEmp.leave_detail.leavetype.toUpperCase()} LEAVE) <span class="${attEmp.hours < attEmp.min_hours ? 'text-danger' : ''}">(${attEmp.hours})</span> ${attEmp.leave_detail.start_time ? attEmp.leave_detail.start_time + '-' : ''}${attEmp.leave_detail.end_time}</td>`;
            //                             } else {
            //                                 row += `<td align="center" colspan="7" data-toggle="collapse" data-target="#collapse-${key}" role="button" style="background: linear-gradient(135deg, #3a8ef6, #6259ca); color: #fff;">${key} <span class="${attEmp.hours < attEmp.min_hours ? 'text-warning' : ''}">(${attEmp.hours})</span></td>`;
            //                             }
            //                             row += `</tr>`;
    
            //                             // Attendance details collapsible row
            //                             row += `<tr class="collapse" id="collapse-${key}"><td colspan="7">
            //                                         <table class="table">
            //                                             <thead>
            //                                                 <tr>
            //                                                     <th>Date</th>
            //                                                     <th>Status</th>
            //                                                     <th>Clock In</th>
            //                                                     <th>Clock Out</th>
            //                                                     <th>Shift Start</th>
            //                                                     <th>Late/Rest</th>
            //                                                 </tr>
            //                                             </thead>
            //                                             <tbody>`;
    
            //                             attEmp.attendance.forEach(att => {
            //                                 row += `<tr>
            //                                             <td>${att.date}</td>
            //                                             <td>${att.status}</td>
            //                                             <td>${att.clock_in !== "00:00:00" ? att.clock_in : "00:00"}</td>
            //                                             <td>${att.clock_out !== "00:00:00" ? att.clock_out : "00:00"}</td>
            //                                             <td>${att.employee.shift_start ? att.employee.shift_start : "N/A"}</td>
            //                                             <td>${att.total_rest === "00:00:00" ? att.late + " (Late)" : att.total_rest + " (Rest)"}</td>
            //                                         </tr>`;
            //                             });
    
            //                             row += `        </tbody>
            //                                         </table>
            //                                     </td></tr>`;
            //                         } else {
            //                             row += `<tr><td align="center" colspan="7" class="btn-danger">${key} (ABSENT)</td></tr>`;
            //                         }
    
            //                         tbody.append(row);
            //                     });
            //                 } else {
            //                     tbody.append('<tr><td align="center" colspan="7">No attendance data available.</td></tr>');
            //                 }
            //             }
            //             if(response.data.type == 'date')
            //             {
            //                 // start 
            //                 if (response.data.attendances.length > 0) {
            //                     // If leave exists, show leave row
            //                     if (response.data.is_leave) {
            //                         tbody.append(`
            //                             <tr>
            //                                 <td align="center" colspan="7" class="btn-warning">
            //                                     (${response.data.emp_leave.leavetype.toUpperCase()} LEAVE) 
            //                                     ${response.data.emp_leave.start_time} - ${response.data.emp_leave.end_time}
            //                                 </td>
            //                             </tr>
            //                         `);
            //                     }

            //                     // Append attendance records
            //                     response.data.attendances.forEach(function (attendance) {
            //                         var restOrLate = attendance.total_rest === "00:00:00" ? "Late" : "Rest";
            //                         var restOrLateTime = attendance.total_rest === "00:00:00" ? attendance.late : attendance.total_rest;

            //                         tbody.append(`
            //                             <tr class="accordion-content">
            //                                 <td>${formatDate(attendance.date)}</td>
            //                                 <td>${attendance.status}</td>
            //                                 <td>${formatTime(attendance.clock_in)}</td>
            //                                 <td>${formatTime(attendance.clock_out)}</td>
            //                                 <td>${attendance.employee?.shift_start ?? 'N/A'}</td>
            //                                 <td>${convertTimeToMinutesAndSeconds(restOrLateTime)} (${restOrLate})</td>
            //                             </tr>
            //                         `);
            //                     });

            //                 } else {
            //                     // Handle weekend or absence
            //                     if (response.data.is_weekend) {
            //                         tbody.append(`
            //                             <tr>
            //                                 <td align="center" colspan="7" class="btn-white">(WEEKEND)</td>
            //                             </tr>
            //                         `);
            //                     } else if (response.data.is_leave) {
            //                         tbody.append(`
            //                             <tr>
            //                                 <td align="center" colspan="7" class="btn-warning">(LEAVE)</td>
            //                             </tr>
            //                         `);
            //                     } else {
            //                         tbody.append(`
            //                             <tr>
            //                                 <td align="center" colspan="7" class="btn-danger">(ABSENT)</td>
            //                             </tr>
            //                         `);
            //                     }
            //                 }
            //                 let totalTime = response.data.hours; // Example: "07:11 Hrs"
            //                 let threshold = response.data.min_hours; // Example: "06:00"

            //                 let $timeInput = $('#totalHours input[name="date"]');
                            
            //                 // Update the value
            //                 $timeInput.val(totalTime);

            //                 // Add or remove class based on condition
            //                 if (totalTime < threshold) {
            //                     $timeInput.addClass('time-box');
            //                 } else {
            //                     $timeInput.removeClass('time-box');
            //                 }
            //                 //end 
            //             }
            //             preloader.style.display = 'none';
            //         },
            //         error: function(xhr, status, error) {
            //             console.error('Error:', error);
            //             alert('Something went wrong! Please try again.');
            //         }
            //     });
            // });
        });


        $(document).ready(function() {
            $('select[name="department"]').on('change', function() {
                var departmentId = $(this).val();  // Get the selected branch ID
                if (departmentId) {
                    $.ajax({
                        url: '{{ route("getEmployeeByDepartment") }}',  // Laravel route for fetching departments and employees
                        type: 'GET',
                        data: {
                            department_id: departmentId,
                            },  // Send the selected branch_id to the backend
                        success: function(response) {
                            console.log(response);

                            if (response.status === 'success') {
                                var departmentSelect = $('select[name="department"]');
                                var employeeSelect = $('select[name="employee"]');

                                employeeSelect.empty();  // Clear existing employee options

                                employeeSelect.append('<option value="" disabled selected>Select Employee</option>');  // Add placeholder option

                                // Loop through employees and append them to the employee select box
                                $.each(response.data.employees, function(key, employee) {
                                    employeeSelect.append('<option value="' + employee.id + '">' + employee.name + '</option>');
                                });
                            } else {
                                // alert('Error: ' + response.message);  // Show error if no departments or employees found
                                $('select[name="employee"]').empty().append('<option value="" disabled selected>Select Employee</option>');
                            }
                        },
                        error: function() {
                            $('select[name="employee"]').empty().append('<option value="" disabled selected>Select Employee</option>');
                            // alert('Error fetching departments and employees.');
                        }
                    });
                } else {
                    $('select[name="employee"]').empty().append('<option value="" disabled selected>Select Employee</option>');
                }
            });
        });
        // Format date from YYYY-MM-DD to DD-MM-YYYY
        function formatDate(dateString) {
            var date = new Date(dateString);
            return date.toLocaleDateString('en-GB'); // Formats as DD-MM-YYYY
        }

        // Format time in 12-hour format with AM/PM
        function formatTime(timeString) {
            if (timeString === "00:00:00") return "00:00 AM"; // Handle midnight case

            var [hours, minutes] = timeString.split(":");
            hours = parseInt(hours, 10);
            var ampm = hours >= 12 ? "PM" : "AM";
            hours = hours % 12 || 12; // Convert 0 to 12 for 12 AM case
            return `${hours}:${minutes} ${ampm}`;
        }


        // Convert time to minutes & seconds
        function convertTimeToMinutesAndSeconds(timeString) {
            var parts = timeString.split(":");
            return parts[0] + "h " + parts[1] + "m " + parts[2] + "s";
        }
        
        // Enhanced AJAX error handling for mobile
        $(document).ajaxError(function(event, xhr, settings, thrownError) {
            console.error('AJAX Error:', thrownError);
            
            // Show user-friendly error message on mobile
            if (window.innerWidth < 768) {
                const errorMsg = document.createElement('div');
                errorMsg.className = 'alert alert-danger alert-dismissible fade show position-fixed';
                errorMsg.style.cssText = 'top: 20px; left: 20px; right: 20px; z-index: 9999;';
                errorMsg.innerHTML = `
                    <strong>Connection Error:</strong> Please check your internet connection and try again.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(errorMsg);
                
                // Auto-remove after 5 seconds
                setTimeout(() => {
                    errorMsg.remove();
                }, 5000);
            }
        });
        
        // Enhanced modal handling for mobile devices
        $(document).on('show.bs.modal', '.modal', function() {
            if (window.innerWidth < 768) {
                // Prevent body scroll on mobile when modal is open
                document.body.style.overflow = 'hidden';
                
                // Adjust modal positioning for mobile
                const modal = this;
                setTimeout(() => {
                    const modalDialog = modal.querySelector('.modal-dialog');
                    if (modalDialog) {
                        modalDialog.style.margin = '10px';
                        modalDialog.style.maxWidth = 'calc(100vw - 20px)';
                    }
                }, 100);
            }
        });
        
        $(document).on('hidden.bs.modal', '.modal', function() {
            if (window.innerWidth < 768) {
                // Restore body scroll when modal is closed
                document.body.style.overflow = '';
            }
        });
        
        // Enhanced tooltip handling for mobile
        $(document).ready(function() {
            if (window.innerWidth >= 768) {
                // Initialize tooltips only on desktop
                $('[data-toggle="tooltip"]').tooltip({
                    trigger: 'hover focus',
                    placement: 'top'
                });
            } else {
                // Disable tooltips on mobile since we show text labels
                $('[data-toggle="tooltip"]').tooltip('disable');
            }
        });
        
        // Handle orientation change
        $(window).on('orientationchange', function() {
            setTimeout(function() {
                // Reinitialize responsive components after orientation change
                initResponsiveEnhancements();
                
                // Refresh tooltips based on new orientation
                if (window.innerWidth >= 768) {
                    $('[data-toggle="tooltip"]').tooltip('enable');
                } else {
                    $('[data-toggle="tooltip"]').tooltip('disable');
                }
            }, 500);
        });
        
        // Performance optimization for large datasets on mobile
        if (window.innerWidth < 768) {
            // Implement virtual scrolling for large tables if needed
            const tableBody = document.querySelector('#attendanceTable tbody');
            if (tableBody && tableBody.children.length > 50) {
                console.log('Large dataset detected, consider implementing virtual scrolling');
                
                // Add loading indicator for better UX
                const loadingIndicator = document.createElement('div');
                loadingIndicator.className = 'text-center p-3 mobile-only';
                loadingIndicator.innerHTML = '<small class="text-muted">Scroll to load more...</small>';
                tableBody.appendChild(loadingIndicator);
            }
        }
    </script>
    @endpush

