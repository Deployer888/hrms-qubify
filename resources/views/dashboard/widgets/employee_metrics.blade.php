<div class="dashboard-widget widget-employee-metrics">
    <div class="widget-header">
        <h2>
            <i class="{{ $config['icon'] }}"></i>
            {{ $config['title'] }}
        </h2>
        <div class="widget-actions">
            <span class="small-text">{{ __('Real-time employee data') }}</span>
        </div>
    </div>
    <div class="widget-body">
        <div class="metrics-overview">
            <div class="metric-card primary">
                <div class="metric-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="metric-content">
                    <h3>{{ __('Total Employees') }}</h3>
                    <div class="metric-value">{{ $metrics['total'] }}</div>
                    <div class="metric-trend">
                        <i class="fas fa-arrow-up text-success"></i>
                        <span>{{ $metrics['growth_rate'] }}% {{ __('growth') }}</span>
                    </div>
                </div>
            </div>
            
            <div class="metric-card success">
                <div class="metric-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="metric-content">
                    <h3>{{ __('Active Employees') }}</h3>
                    <div class="metric-value">{{ $metrics['active'] }}</div>
                    <div class="metric-trend">
                        <span>{{ $metrics['active_percentage'] }}% {{ __('active') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="department-breakdown">
            <h4>{{ __('Department Breakdown') }}</h4>
            <div class="breakdown-list">
                @foreach($department_breakdown as $dept)
                <div class="breakdown-item">
                    <span class="dept-name">{{ $dept['name'] }}</span>
                    <span class="dept-count">{{ $dept['count'] }}</span>
                    <div class="progress">
                        <div class="progress-bar" style="width: {{ ($dept['count'] / $metrics['total']) * 100 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
.widget-employee-metrics .metrics-overview {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.widget-employee-metrics .metric-card {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-radius: 12px;
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.widget-employee-metrics .metric-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.widget-employee-metrics .metric-card.primary .metric-icon {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
}

.widget-employee-metrics .metric-card.success .metric-icon {
    background: linear-gradient(135deg, var(--success), #34d399);
}

.widget-employee-metrics .metric-content h3 {
    font-size: 0.8rem;
    color: var(--text-secondary);
    margin: 0 0 0.5rem 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.widget-employee-metrics .metric-value {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.widget-employee-metrics .metric-trend {
    font-size: 0.75rem;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.widget-employee-metrics .department-breakdown h4 {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.widget-employee-metrics .breakdown-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.75rem;
}

.widget-employee-metrics .dept-name {
    flex: 1;
    font-size: 0.9rem;
    color: var(--text-primary);
}

.widget-employee-metrics .dept-count {
    font-weight: 600;
    color: var(--primary);
    min-width: 30px;
    text-align: right;
}

.widget-employee-metrics .progress {
    flex: 1;
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
}

.widget-employee-metrics .progress-bar {
    height: 100%;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    border-radius: 3px;
    transition: width 0.6s ease;
}
</style>