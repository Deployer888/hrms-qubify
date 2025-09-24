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
