@extends('layouts.admin')
@section('page-title')
    {{__('Office Management')}}
@endsection

@push('css-page')
<link rel="stylesheet" href="{{ asset('css/company/constant-office.css') }}">
<link rel="stylesheet" href="{{ asset('css/model.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-8">
                            <h2 class="section-title">{{__('Office Locations')}}</h2>
                            <p class="text-muted">Manage all your company office locations and their details</p>
                        </div>
                        <div class="col-md-4">
                            <div class="search-form">
                                <input type="text" placeholder="Search offices..." id="search-office">
                                <button type="button"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                    

                   <div class="office-metrics">
                        <div class="metric-card offices-card">
                            <div class="metric-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                                </svg>
                            </div>
                            <div class="metric-content">
                                <div class="metric-value">{{ count($offices) }}</div>
                                <div class="metric-label">Total Offices</div>
                            </div>
                        </div>
                        <div class="metric-card employees-card">
                            <div class="metric-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                            <div class="metric-content">
                                <div class="metric-value">{{ $totalEmployees }}</div>
                                <div class="metric-label">Total Employees</div>
                            </div>
                        </div>
                        <div class="metric-card attendance-card">
                            <div class="metric-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="metric-content">
                                <div class="metric-value">{{ $attendancePercentage }}%</div>
                                <div class="metric-label">Office Attendance</div>
                            </div>
                        </div>
                        <div class="metric-card cities-card">
                            <div class="metric-icon">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                            </div>
                            <div class="metric-content">
                                <div class="metric-value">{{ $totalCities }}</div>
                                <div class="metric-label">Cities</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                      <div class="col-12 text-center" id="no-result" style="display: none;">
                            <p>No offices found.</p>
                       </div>
                    </div>
                    <div class="row office-row">
                        <div class="col-12 text-center" id="search-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin fa-2x"></i> Searching...
                        </div>
                      

                        @foreach($offices as $office)
                            @include('office.office_cards', ['office' => $office])
                        @endforeach
   
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @can('Create Office')
    <a href="#" data-url="{{ route('office.create') }}" class="add-office-btn" data-ajax-popup="true" data-title="{{__('Create New Office')}}">
        <i class="fas fa-plus"></i>
    </a>
    @endcan
@endsection

@push('theme-script')
<script>
    $(document).ready(function() {
        // Search functionality
      $('#search-office').on('keyup', function() {
            let query = $(this).val();

            // Show loading spinner
            $('#search-loading').show();  
            $('#no-result').hide();

            $.ajax({
                url: "{{ route('office.index') }}",
                type: "GET",
                data: { query: query },
                success: function(response) {
                    $('#search-loading').hide(); // hide spinner

                    if (!response.html || response.html.trim() === '') {
                            $('#no-result').show();
                            $('.office-row').html(''); // clear all office cards
                        } else {
                            $('#no-result').hide();
                            $('.office-row').html(response.html); // replace content
                        }
                },
                error: function() {
                    $('#search-loading').hide();
                    alert('Something went wrong. Please try again.');
                }
            });
        });


        
        // Animation on scroll
        $(window).scroll(function() {
            $('.office-card').each(function() {
                var position = $(this).offset().top;
                var scrollPosition = $(window).scrollTop() + $(window).height();
                
                if (position < scrollPosition) {
                    $(this).addClass('animated fadeInUp');
                }
            });
        });
        
        // Delete confirmation
        $(document).on('click', '.btn-delete', function() {
            var text = $(this).attr('data-confirm');
            var confirmYes = $(this).attr('data-confirm-yes');
            
            if (confirm(text)) {
                eval(confirmYes);
            }
            return false;
        });
    });
</script>
@endpush