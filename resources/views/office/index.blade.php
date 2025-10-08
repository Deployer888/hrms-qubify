@extends('layouts.admin')
@section('page-title')
    {{__('Office Management')}}
@endsection

@push('css-page')
<style>
    .office-card {
        transition: all 0.3s ease;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        margin-bottom: 25px;
        position: relative;
    }
    
    .office-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    
    .office-header {
        background: linear-gradient(135deg, #3a8ef6, #6259ca);
        color: white;
        padding: 20px;
        position: relative;
        overflow: hidden;
    }
    
    .office-header h3 {
        margin: 0;
        font-weight: 600;
        font-size: 1.3rem;
        position: relative;
        z-index: 2;
        color: #fff;
    }
    
    .office-header .office-location {
        font-size: 0.9rem;
        opacity: 0.9;
        margin-top: 5px;
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
    }
    
    .office-header .office-location i {
        margin-right: 5px;
    }
    
    .office-header .office-icon {
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 2rem;
        opacity: 0.2;
        z-index: 1;
    }
    
    .office-body {
        padding: 20px;
        background: white;
    }
    
    .office-stat {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }
    
    .office-stat-label {
        color: #6c757d;
        font-size: 0.85rem;
    }
    
    .office-stat-value {
        font-weight: 600;
        color: #343a40;
    }
    
    .office-footer {
        padding: 12px 20px;
        background: #f8f9fa;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid rgba(0,0,0,0.05);
    }
    
    .btn-view-details {
        background: linear-gradient(135deg, #3a8ef6, #6259ca);
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 8px;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }
    
    .btn-view-details:hover {
        background: linear-gradient(135deg, #1c7ae0, #5349b5);
        color: white;
    }
    
    .btn-group-office .btn {
        padding: 5px 10px;
        font-size: 0.8rem;
        border-radius: 8px;
    }
    
    .btn-edit {
        background-color: #ffc107;
        color: #212529;
        margin-right: 5px;
    }
    
    .btn-delete {
        background-color: #dc3545;
        color: white;
    }
    
    .section-title {
        position: relative;
        margin-bottom: 30px;
        padding-bottom: 15px;
    }
    
    .section-title:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: linear-gradient(135deg, #3a8ef6, #6259ca);
    }
    
    .metric-card:last-child {
        margin-right: 0;
    }
    
    .metric-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 5px;
        color: #3a8ef6;
    }
    
    .metric-label {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .search-form {
        display: flex;
        margin-bottom: 30px;
    }
    
    .search-form input {
        flex: 1;
        border: 1px solid #ced4da;
        border-radius: 8px 0 0 8px;
        padding: 10px 15px;
        font-size: 0.9rem;
    }
    
    .search-form button {
        background: #3a8ef6;
        color: white;
        border: none;
        border-radius: 0 8px 8px 0;
        padding: 10px 20px;
    }
    
    .office-status {
        position: absolute;
        top: 0;
        right: 0;
        background: #28a745;
        color: white;
        font-size: 0.7rem;
        padding: 5px 10px;
        border-bottom-left-radius: 15px;
    }
    
    .add-office-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3a8ef6, #6259ca);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        z-index: 100;
    }
    
    .add-office-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .office-metrics {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin: 20px 0;
    }

    .metric-card {
        background: white;
        border-radius: 12px;
        padding: 24px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #f0f0f0;
        position: relative;
        transition: all 0.2s ease;
        overflow: hidden;
    }

    .metric-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        border-radius: 2px 0 0 2px;
    }

    .offices-card::before {
        background: #7C4DFF;
    }

    .employees-card::before {
        background: #00BCD4;
    }

    .attendance-card::before {
        background: #FF5722;
    }

    .cities-card::before {
        background: #4CAF50;
    }

    .metric-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .offices-card .metric-icon {
        background: #7C4DFF;
    }

    .employees-card .metric-icon {
        background: #00BCD4;
    }

    .attendance-card .metric-icon {
        background: #FF5722;
    }

    .cities-card .metric-icon {
        background: #4CAF50;
    }

    .metric-icon svg {
        width: 24px;
        height: 24px;
        color: white;
    }

    .metric-content {
        flex: 1;
    }

    .metric-value {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
        line-height: 1.2;
        margin-bottom: 4px;
    }

    .metric-label {
        font-size: 0.875rem;
        color: #7f8c8d;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    @media (max-width: 768px) {
        .office-metrics {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        
        .metric-card {
            padding: 20px 16px;
        }
        
        .metric-value {
            font-size: 1.75rem;
        }
        
        .metric-icon {
            width: 40px;
            height: 40px;
        }
        
        .metric-icon svg {
            width: 20px;
            height: 20px;
        }
    }
</style>
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
                    
                    <!-- <div class="office-metrics">
                        <div class="metric-card">
                            <div class="metric-value">{{ count($offices) }}</div>
                            <div class="metric-label">Total Offices</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">{{ $totalEmployees }}</div>
                            <div class="metric-label">Total Employees</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">{{ $attendancePercentage }}%</div>
                            <div class="metric-label">Office Attendance</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">{{ $totalCities }}</div>
                            <div class="metric-label">Cities</div>
                        </div>
                    </div> -->

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
                       {{--  <!-- @foreach($offices as $office)
                        <div class="col-lg-4 col-md-6">
                            <div class="office-card">
                                <div class="office-status">Active</div>
                                <div class="office-header">
                                    <h3>{{ $office->name }}</h3>
                                    <div class="office-location">
                                        <i class="fas fa-map-marker-alt"></i> {{ $office->city }}, {{ $office->country }}
                                    </div>
                                    <div class="office-icon">
                                        <i class="fas fa-building"></i>
                                    </div>
                                </div>
                                <div class="office-body">
                                    <div class="office-stat">
                                        <span class="office-stat-label">Employees</span>
                                        <span class="office-stat-value">{{ $office->employees()->count() }}</span>
                                    </div>
                                    <div class="office-stat">
                                        <span class="office-stat-label">Departments</span>
                                        <span class="office-stat-value">{{ \App\Models\Department::count() }}</span>
                                    </div>
                                    <div class="office-stat">
                                        <span class="office-stat-label">Contact</span>
                                        <span class="office-stat-value">{{ $office->phone }}</span>
                                    </div>
                                </div>
                                <div class="office-footer">
                                    <a href="{{ route('office.one.index', $office->id) }}" class="btn-view-details">View Details</a>
                                    <div class="btn-group-office">
                                        @can('Edit Office')
                                        <a href="#" class="btn btn-edit" data-url="{{ route('office.edit', $office->id) }}" data-ajax-popup="true" data-title="{{__('Edit Office')}}">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        @endcan
                                        @can('Delete Office')
                                        <a href="#" class="btn btn-delete" data-confirm="{{__('Are you sure?') | __('This action cannot be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{ $office->id }}').submit();">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <form id="delete-form-{{ $office->id }}" action="{{ route('office.destroy', $office->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach  --> --}}
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

                    if (response.html.trim() === '') {
                        $('#no-result').show();
                        $('.office-row').html(''); // clear all office cards
                    } else {
                        $('#no-result').hide();
                        $('.office-row').html(response.html); // **replace** content, not append
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