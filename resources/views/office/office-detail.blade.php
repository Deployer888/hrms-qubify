@extends('layouts.admin')
@section('page-title')
    {{__('Office Management')}}
@endsection

@push('css-page')
<style>
    .office-card {
        transition: all 0.3s ease;
        border-radius: 12px;
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
        border-radius: 5px;
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
        border-radius: 4px;
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
    
    .office-metrics {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 30px;
    }
    
    .metric-card {
        flex: 1;
        min-width: 200px;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        margin-right: 15px;
        margin-bottom: 15px;
        text-align: center;
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
        border-radius: 5px 0 0 5px;
        padding: 10px 15px;
        font-size: 0.9rem;
    }
    
    .search-form button {
        background: #3a8ef6;
        color: white;
        border: none;
        border-radius: 0 5px 5px 0;
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
        border-bottom-left-radius: 10px;
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
                    
                    <div class="office-metrics">
                        <div class="metric-card">
                            <div class="metric-value">12</div>
                            <div class="metric-label">Total Offices</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">650</div>
                            <div class="metric-label">Total Employees</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">85%</div>
                            <div class="metric-label">Office Attendance</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">8</div>
                            <div class="metric-label">Countries</div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Office Card 1 -->
                        <div class="col-lg-4 col-md-6">
                            <div class="office-card">
                                <div class="office-status">Active</div>
                                <div class="office-header">
                                    <h3>Headquarters</h3>
                                    <div class="office-location">
                                        <i class="fas fa-map-marker-alt"></i> New York, USA
                                    </div>
                                    <div class="office-icon">
                                        <i class="fas fa-building"></i>
                                    </div>
                                </div>
                                <div class="office-body">
                                    <div class="office-stat">
                                        <span class="office-stat-label">Employees</span>
                                        <span class="office-stat-value">245</span>
                                    </div>
                                    <div class="office-stat">
                                        <span class="office-stat-label">Departments</span>
                                        <span class="office-stat-value">12</span>
                                    </div>
                                    <div class="office-stat">
                                        <span class="office-stat-label">Contact</span>
                                        <span class="office-stat-value">+1 (212) 555-1234</span>
                                    </div>
                                </div>
                                <div class="office-footer">
                                    <a href="{{ route('office.show', 1) }}" class="btn-view-details">View Details</a>
                                    <div class="btn-group-office">
                                        @can('Edit Office')
                                            <a href="#" class="btn btn-edit" data-url="{{ route('office.edit', 1) }}" data-ajax-popup="true" data-title="{{__('Edit Office')}}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('Delete Office')
                                            <a href="#" class="btn btn-delete" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-1').submit();">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <form method="POST" action="{{ route('office.destroy', 1) }}" id="delete-form-1" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Office Card 2 -->
                        <div class="col-lg-4 col-md-6">
                            <div class="office-card">
                                <div class="office-status">Active</div>
                                <div class="office-header">
                                    <h3>San Francisco Office</h3>
                                    <div class="office-location">
                                        <i class="fas fa-map-marker-alt"></i> San Francisco, USA
                                    </div>
                                    <div class="office-icon">
                                        <i class="fas fa-building"></i>
                                    </div>
                                </div>
                                <div class="office-body">
                                    <div class="office-stat">
                                        <span class="office-stat-label">Employees</span>
                                        <span class="office-stat-value">120</span>
                                    </div>
                                    <div class="office-stat">
                                        <span class="office-stat-label">Departments</span>
                                        <span class="office-stat-value">8</span>
                                    </div>
                                    <div class="office-stat">
                                        <span class="office-stat-label">Contact</span>
                                        <span class="office-stat-value">+1 (415) 555-6789</span>
                                    </div>
                                </div>
                                <div class="office-footer">
                                    <a href="{{ route('office.show', 2) }}" class="btn-view-details">View Details</a>
                                    <div class="btn-group-office">
                                        @can('Edit Office')
                                            <a href="#" class="btn btn-edit" data-url="{{ route('office.edit', 2) }}" data-ajax-popup="true" data-title="{{__('Edit Office')}}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('Delete Office')
                                            <a href="#" class="btn btn-delete" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-2').submit();">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <form method="POST" action="{{ route('office.destroy', 2) }}" id="delete-form-2" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Office Card 3 -->
                        <div class="col-lg-4 col-md-6">
                            <div class="office-card">
                                <div class="office-status">Active</div>
                                <div class="office-header">
                                    <h3>London Office</h3>
                                    <div class="office-location">
                                        <i class="fas fa-map-marker-alt"></i> London, UK
                                    </div>
                                    <div class="office-icon">
                                        <i class="fas fa-building"></i>
                                    </div>
                                </div>
                                <div class="office-body">
                                    <div class="office-stat">
                                        <span class="office-stat-label">Employees</span>
                                        <span class="office-stat-value">85</span>
                                    </div>
                                    <div class="office-stat">
                                        <span class="office-stat-label">Departments</span>
                                        <span class="office-stat-value">6</span>
                                    </div>
                                    <div class="office-stat">
                                        <span class="office-stat-label">Contact</span>
                                        <span class="office-stat-value">+44 20 1234 5678</span>
                                    </div>
                                </div>
                                <div class="office-footer">
                                    <a href="{{ route('office.show', 3) }}" class="btn-view-details">View Details</a>
                                    <div class="btn-group-office">
                                        @can('Edit Office')
                                            <a href="#" class="btn btn-edit" data-url="{{ route('office.edit', 3) }}" data-ajax-popup="true" data-title="{{__('Edit Office')}}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('Delete Office')
                                            <a href="#" class="btn btn-delete" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-3').submit();">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <form method="POST" action="{{ route('office.destroy', 3) }}" id="delete-form-3" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Office Card 4 -->
                        <div class="col-lg-4 col-md-6">
                            <div class="office-card">
                                <div class="office-status">Active</div>
                                <div class="office-header">
                                    <h3>Singapore Office</h3>
                                    <div class="office-location">
                                        <i class="fas fa-map-marker-alt"></i> Singapore
                                    </div>
                                    <div class="office-icon">
                                        <i class="fas fa-building"></i>
                                    </div>
                                </div>
                                <div class="office-body">
                                    <div class="office-stat">
                                        <span class="office-stat-label">Employees</span>
                                        <span class="office-stat-value">65</span>
                                    </div>
                                    <div class="office-stat">
                                        <span class="office-stat-label">Departments</span>
                                        <span class="office-stat-value">5</span>
                                    </div>
                                    <div class="office-stat">
                                        <span class="office-stat-label">Contact</span>
                                        <span class="office-stat-value">+65 6123 4567</span>
                                    </div>
                                </div>
                                <div class="office-footer">
                                    <a href="{{ route('office.show', 4) }}" class="btn-view-details">View Details</a>
                                    <div class="btn-group-office">
                                        @can('Edit Office')
                                            <a href="#" class="btn btn-edit" data-url="{{ route('office.edit', 4) }}" data-ajax-popup="true" data-title="{{__('Edit Office')}}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('Delete Office')
                                            <a href="#" class="btn btn-delete" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-4').submit();">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <form method="POST" action="{{ route('office.destroy', 4) }}" id="delete-form-4" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Office Card 5 -->
                        <div class="col-lg-4 col-md-6">
                            <div class="office-card">
                                <div class="office-status">Active</div>
                                <div class="office-header">
                                    <h3>Sydney Office</h3>
                                    <div class="office-location">
                                        <i class="fas fa-map-marker-alt"></i> Sydney, Australia
                                    </div>
                                    <div class="office-icon">
                                        <i class="fas fa-building"></i>
                                    </div>
                                </div>
                                <div class="office-body">
                                    <div class="office-stat">
                                        <span class="office-stat-label">Employees</span>
                                        <span class="office-stat-value">45</span>
                                    </div>
                                    <div class="office-stat">
                                        <span class="office-stat-label">Departments</span>
                                        <span class="office-stat-value">4</span>
                                    </div>
                                    <div class="office-stat">
                                        <span class="office-stat-label">Contact</span>
                                        <span class="office-stat-value">+61 2 9876 5432</span>
                                    </div>
                                </div>
                                <div class="office-footer">
                                    <a href="{{ route('office.show', 5) }}" class="btn-view-details">View Details</a>
                                    <div class="btn-group-office">
                                        @can('Edit Office')
                                            <a href="#" class="btn btn-edit" data-url="{{ route('office.edit', 5) }}" data-ajax-popup="true" data-title="{{__('Edit Office')}}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('Delete Office')
                                            <a href="#" class="btn btn-delete" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-5').submit();">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <form method="POST" action="{{ route('office.destroy', 5) }}" id="delete-form-5" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Office Card 6 -->
                        <div class="col-lg-4 col-md-6">
                            <div class="office-card">
                                <div class="office-status">Active</div>
                                <div class="office-header">
                                    <h3>Berlin Office</h3>
                                    <div class="office-location">
                                        <i class="fas fa-map-marker-alt"></i> Berlin, Germany
                                    </div>
                                    <div class="office-icon">
                                        <i class="fas fa-building"></i>
                                    </div>
                                </div>
                                <div class="office-body">
                                    <div class="office-stat">
                                        <span class="office-stat-label">Employees</span>
                                        <span class="office-stat-value">90</span>
                                    </div>
                                    <div class="office-stat">
                                        <span class="office-stat-label">Departments</span>
                                        <span class="office-stat-value">7</span>
                                    </div>
                                    <div class="office-stat">
                                        <span class="office-stat-label">Contact</span>
                                        <span class="office-stat-value">+49 30 1234 5678</span>
                                    </div>
                                </div>
                                <div class="office-footer">
                                    <a href="{{ route('office.show', 6) }}" class="btn-view-details">View Details</a>
                                    <div class="btn-group-office">
                                        @can('Edit Office')
                                            <a href="#" class="btn btn-edit" data-url="{{ route('office.edit', 6) }}" data-ajax-popup="true" data-title="{{__('Edit Office')}}">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan
                                        @can('Delete Office')
                                            <a href="#" class="btn btn-delete" data-toggle="tooltip" data-original-title="{{__('Delete')}}" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-6').submit();">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <form method="POST" action="{{ route('office.destroy', 6) }}" id="delete-form-6" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @can('Create Office')
    <a href="#" data-url="{{-- route('office.create') --}}" class="add-office-btn" data-ajax-popup="true" data-title="{{__('Create New Office')}}">
        <i class="fas fa-plus"></i>
    </a>
    @endcan
@endsection

@push('script-page')
<script>
    $(document).ready(function() {
        // Search functionality
        $('#search-office').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('.office-card').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
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
    });
</script>
@endpush