<style>
    /* Enhanced Role Form Styles */
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --card-shadow: 0 10px 30px rgba(0,0,0,0.1);
        --card-shadow-hover: 0 20px 40px rgba(0,0,0,0.15);
        --border-radius: 16px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #3b82f6;
        --light: #f8fafc;
        --dark: #1f2937;
    }

    .modal-dialog {
        max-width: 95vw !important;
        margin: 30px auto;
    }

    .modal-content {
        border: none !important;
        border-radius: 20px !important;
        box-shadow: 0 25px 50px rgba(0,0,0,0.15) !important;
        backdrop-filter: blur(10px);
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        overflow: hidden;
    }

    /* Premium Form Container */
    .premium-form-container {
        position: relative;
        background: transparent;
        min-height: 70vh;
        max-height: 85vh;
        overflow: hidden;
    }

    .premium-form-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary-gradient);
        z-index: 1;
    }

    /* Form Header */
    .form-header {
        background: var(--primary-gradient);
        padding: 25px 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }

    .form-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: float 6s ease-in-out infinite;
        pointer-events: none;
    }

    .form-header h4 {
        color: white;
        font-weight: 800;
        font-size: 20px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .form-header h4 i {
        font-size: 22px;
        opacity: 0.9;
    }

    .btn-close {
        background: rgba(255, 255, 255, 0.2) !important;
        border: 1px solid rgba(255,255,255,0.3) !important;
        border-radius: 8px !important;
        padding: 8px 12px !important;
        color: white !important;
        font-size: 1.2rem !important;
        cursor: pointer !important;
        transition: var(--transition) !important;
        backdrop-filter: blur(10px);
    }

    .btn-close:hover {
        background: rgba(255, 255, 255, 0.3) !important;
        transform: scale(1.1) rotate(90deg);
    }

    /* Form Body */
    .form-body {
        padding: 0;
        max-height: calc(85vh - 180px);
        overflow-y: auto;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    }

    /* Role Information Card */
    .role-info-card {
        padding: 30px;
        border-bottom: 1px solid rgba(102, 126, 234, 0.1);
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.02) 0%, rgba(118, 75, 162, 0.02) 100%);
    }

    /* Premium Form Group */
    .premium-form-group {
        margin-bottom: 25px;
    }

    .premium-form-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 12px;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .premium-form-label i {
        color: #667eea;
        font-size: 16px;
    }

    .premium-form-control {
        width: 100%;
        padding: 15px 20px;
        border: 2px solid rgba(102, 126, 234, 0.1);
        border-radius: 12px;
        font-size: 14px;
        font-weight: 500;
        background: white;
        transition: var(--transition);
        color: var(--dark);
        position: relative;
    }

    .premium-form-control:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        transform: translateY(-2px);
    }

    .premium-form-control::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }

    /* Permissions Section */
    .permissions-section {
        padding: 30px;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    }

    .permissions-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 2px solid rgba(102, 126, 234, 0.1);
    }

    .permissions-header h6 {
        font-size: 18px;
        font-weight: 800;
        color: var(--dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .permissions-header h6 i {
        color: #667eea;
        font-size: 20px;
    }

    .select-all-section {
        display: flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        padding: 12px 16px;
        border-radius: 10px;
        border: 1px solid rgba(102, 126, 234, 0.2);
        transition: var(--transition);
        cursor: pointer;
    }

    .select-all-section:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
        transform: scale(1.05);
    }

    .select-all-section span {
        font-weight: 600;
        color: #667eea;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Modules Grid */
    .modules-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 20px;
    }

    /* Module Card */
    .module-card {
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        border: 2px solid rgba(102, 126, 234, 0.1);
        border-radius: 15px;
        overflow: hidden;
        transition: var(--transition);
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        position: relative;
    }

    .module-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--primary-gradient);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s ease;
    }

    .module-card:hover {
        border-color: rgba(102, 126, 234, 0.3);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
    }

    .module-card:hover::before {
        transform: scaleX(1);
    }

    /* Module Header */
    .module-header {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid rgba(102, 126, 234, 0.1);
    }

    .module-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
        color: var(--dark);
        font-size: 15px;
    }

    .module-icon {
        width: 36px;
        height: 36px;
        background: var(--primary-gradient);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        transition: var(--transition);
    }

    .module-card:hover .module-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .module-select-all {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 600;
        color: #667eea;
        cursor: pointer;
        transition: var(--transition);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .module-select-all:hover {
        color: #5a67d8;
        transform: scale(1.05);
    }

    /* Permissions List */
    .permissions-list {
        padding: 20px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 12px;
    }

    /* Permission Item */
    .permission-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 15px;
        border-radius: 10px;
        border: 2px solid rgba(102, 126, 234, 0.1);
        transition: var(--transition);
        cursor: pointer;
        background: white;
        position: relative;
        overflow: hidden;
    }

    .permission-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
        transition: var(--transition);
    }

    .permission-item:hover {
        border-color: rgba(102, 126, 234, 0.3);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.15);
    }

    .permission-item:hover::before {
        left: 100%;
        transition: left 0.6s ease-in-out;
    }

    .permission-item.checked {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border-color: #667eea;
        transform: translateY(-2px);
    }

    .permission-item.checked::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 0;
        height: 0;
        border-left: 15px solid transparent;
        border-top: 15px solid #10b981;
    }

    .permission-item.checked .permission-label {
        color: #667eea;
        font-weight: 700;
    }

    /* Permission Types Colors */
    .permission-manage {
        border-left: 4px solid #667eea;
    }

    .permission-create {
        border-left: 4px solid #10b981;
    }

    .permission-edit {
        border-left: 4px solid #f59e0b;
    }

    .permission-delete {
        border-left: 4px solid #ef4444;
    }

    .permission-show {
        border-left: 4px solid #3b82f6;
    }

    .permission-move,
    .permission-add {
        border-left: 4px solid #8b5cf6;
    }

    /* Premium Checkbox */
    .premium-checkbox {
        position: relative;
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .premium-checkbox input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    .checkmark {
        height: 20px;
        width: 20px;
        background: white;
        border: 2px solid rgba(102, 126, 234, 0.3);
        border-radius: 6px;
        position: relative;
        transition: var(--transition);
        flex-shrink: 0;
    }

    .premium-checkbox:hover .checkmark {
        border-color: #667eea;
        transform: scale(1.1);
    }

    .premium-checkbox input:checked ~ .checkmark {
        background: var(--primary-gradient);
        border-color: #667eea;
        transform: scale(1.1);
    }

    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
        left: 6px;
        top: 2px;
        width: 6px;
        height: 10px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    .premium-checkbox input:checked ~ .checkmark:after {
        display: block;
    }

    .permission-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--dark);
        transition: var(--transition);
        text-transform: capitalize;
    }

    /* Form Actions */
    .form-actions {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        padding: 25px 30px;
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        border-top: 1px solid rgba(102, 126, 234, 0.1);
    }

    .premium-btn {
        padding: 12px 24px;
        border-radius: 10px;
        border: none;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .premium-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: var(--transition);
    }

    .premium-btn:hover::before {
        left: 100%;
        transition: left 0.6s ease-in-out;
    }

    .btn-cancel {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
    }

    .btn-cancel:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 8px 25px rgba(107, 114, 128, 0.4);
        color: white;
        text-decoration: none;
    }

    .btn-create {
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-create:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        color: white;
        text-decoration: none;
    }

    /* Error Styling */
    .mt-2 {
        margin-top: 8px !important;
    }

    /* Animations */
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-10px) rotate(180deg); }
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    /* Custom Scrollbar */
    .form-body::-webkit-scrollbar {
        width: 6px;
    }

    .form-body::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .form-body::-webkit-scrollbar-thumb {
        background: var(--primary-gradient);
        border-radius: 3px;
    }

    .form-body::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .modules-grid {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 15px;
        }

        .permissions-list {
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
        }
    }

    @media (max-width: 768px) {
        .modal-dialog {
            margin: 15px;
        }

        .form-header {
            padding: 20px;
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }

        .form-header h4 {
            font-size: 18px;
        }

        .role-info-card,
        .permissions-section {
            padding: 20px;
        }

        .permissions-header {
            flex-direction: column;
            gap: 15px;
        }

        .modules-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .permissions-list {
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        }

        .form-actions {
            padding: 20px;
            flex-direction: column;
        }

        .premium-btn {
            justify-content: center;
        }
    }

    @media (max-width: 576px) {
        .permission-item {
            padding: 10px 12px;
        }

        .permission-label {
            font-size: 12px;
        }

        .module-header {
            padding: 15px;
            flex-direction: column;
            gap: 10px;
            text-align: center;
        }
    }
    </style>

    <div class="premium-form-container">
        <!-- Form Header -->
        <div class="form-header">
            <h4>
                <i class="fas fa-plus-circle"></i>
                {{ __('Create New Role') }}
            </h4>
            <button type="button" class="btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('roles.store') }}" method="POST">
            @csrf

            <!-- Form Body -->
            <div class="form-body">
                <!-- Role Information Card -->
                <div class="role-info-card">
                    <div class="premium-form-group">
                        <label for="name" class="premium-form-label">
                            <i class="fas fa-tag"></i>
                            {{ __('Role Name') }} <span style="color: var(--danger);">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               class="premium-form-control"
                               placeholder="{{ __('Enter Role Name (e.g., Manager, Supervisor, etc.)') }}"
                               value="{{ old('name') }}"
                               required>
                        @error('name')
                            <div class="mt-2" style="color: var(--danger); font-size: 0.875rem; font-weight: 500;">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Permissions Section -->
                @if (!empty($permissions))
                    <div class="permissions-section">
                        <div class="permissions-header">
                            <h6>
                                <i class="fas fa-shield-alt"></i>
                                {{ __('Assign Permissions to Role') }}
                            </h6>
                            <div class="select-all-section">
                                <label class="premium-checkbox">
                                    <input type="checkbox" id="checkall">
                                    <span class="checkmark"></span>
                                </label>
                                <span style="font-size: 0.9rem; font-weight: 500;">{{ __('Select All') }}</span>
                            </div>
                        </div>

                        <div class="modules-grid">
                            @php
                                $modules = [
                                    'User' => 'fas fa-users',
                                    'Role' => 'fas fa-user-shield',
                                    'Award' => 'fas fa-trophy',
                                    'Transfer' => 'fas fa-exchange-alt',
                                    'Resignation' => 'fas fa-sign-out-alt',
                                    'Travel' => 'fas fa-plane',
                                    'Promotion' => 'fas fa-arrow-up',
                                    'Complaint' => 'fas fa-exclamation-triangle',
                                    'Warning' => 'fas fa-exclamation',
                                    'Termination' => 'fas fa-times-circle',
                                    'Department' => 'fas fa-building',
                                    'Designation' => 'fas fa-id-badge',
                                    'Document Type' => 'fas fa-file-alt',
                                    'Branch' => 'fas fa-code-branch',
                                    'Award Type' => 'fas fa-medal',
                                    'Termination Type' => 'fas fa-ban',
                                    'Employee' => 'fas fa-user',
                                    'Payslip Type' => 'fas fa-receipt',
                                    'Allowance Option' => 'fas fa-plus-circle',
                                    'Loan Option' => 'fas fa-hand-holding-usd',
                                    'Deduction Option' => 'fas fa-minus-circle',
                                    'Set Salary' => 'fas fa-dollar-sign',
                                    'Allowance' => 'fas fa-gift',
                                    'Commission' => 'fas fa-percentage',
                                    'Loan' => 'fas fa-credit-card',
                                    'Saturation Deduction' => 'fas fa-calculator',
                                    'Other Payment' => 'fas fa-money-bill',
                                    'Overtime' => 'fas fa-clock',
                                    'Pay Slip' => 'fas fa-file-invoice-dollar',
                                    'Account List' => 'fas fa-list',
                                    'Payee' => 'fas fa-user-plus',
                                    'Payer' => 'fas fa-user-minus',
                                    'Income Type' => 'fas fa-chart-line',
                                    'Expense Type' => 'fas fa-chart-bar',
                                    'Payment Type' => 'fas fa-credit-card',
                                    'Deposit' => 'fas fa-piggy-bank',
                                    'Expense' => 'fas fa-shopping-cart',
                                    'Transfer Balance' => 'fas fa-balance-scale',
                                    'Event' => 'fas fa-calendar-check',
                                    'Announcement' => 'fas fa-bullhorn',
                                    'Leave Type' => 'fas fa-calendar-times',
                                    'Leave' => 'fas fa-calendar-minus',
                                    'Meeting' => 'fas fa-handshake',
                                    'Ticket' => 'fas fa-ticket-alt',
                                    'Attendance' => 'fas fa-clock',
                                    'TimeSheet' => 'fas fa-table',
                                    'Holiday' => 'fas fa-calendar-day',
                                    'Plan' => 'fas fa-clipboard-list',
                                    'Assets' => 'fas fa-briefcase',
                                    'Document' => 'fas fa-folder',
                                    'Employee Profile' => 'fas fa-user-circle',
                                    'Employee Last Login' => 'fas fa-sign-in-alt',
                                    'Indicator' => 'fas fa-chart-pie',
                                    'Appraisal' => 'fas fa-star',
                                    'Goal Tracking' => 'fas fa-bullseye',
                                    'Goal Type' => 'fas fa-target',
                                    'Competencies' => 'fas fa-brain',
                                    'Company Policy' => 'fas fa-gavel',
                                    'Trainer' => 'fas fa-chalkboard-teacher',
                                    'Training' => 'fas fa-graduation-cap',
                                    'Training Type' => 'fas fa-book',
                                    'Job Category' => 'fas fa-layer-group',
                                    'Job Stage' => 'fas fa-stairs',
                                    'Job' => 'fas fa-briefcase',
                                    'Job Application' => 'fas fa-file-signature',
                                    'Job OnBoard' => 'fas fa-user-check',
                                    'Job Application Note' => 'fas fa-sticky-note',
                                    'Job Application Skill' => 'fas fa-cogs',
                                    'Custom Question' => 'fas fa-question-circle',
                                    'Interview Schedule' => 'fas fa-calendar-alt',
                                    'Office' => 'fas fa-building',
                                    'Career' => 'fas fa-rocket',
                                    'Report' => 'fas fa-chart-area',
                                    'Performance Type' => 'fas fa-tachometer-alt'
                                ];

                                if (Auth::user()->type == 'super admin') {
                                    $modules['Language'] = 'fas fa-language';
                                }
                            @endphp

                            @foreach ($modules as $module => $icon)
                                @php
                                    $moduleId = str_replace(' ', '', $module);
                                    $modulePermissions = [];
                                    $permissionTypes = ['Manage', 'Create', 'Edit', 'Delete', 'Show', 'Move', 'client permission', 'invite user', 'buy', 'Add'];

                                    foreach ($permissionTypes as $type) {
                                        $permName = $type . ' ' . $module;
                                        if (in_array($permName, (array) $permissions)) {
                                            $key = array_search($permName, $permissions);
                                            if ($key !== false) {
                                                $modulePermissions[] = [
                                                    'key' => $key,
                                                    'name' => $permName,
                                                    'type' => $type,
                                                    'checked' => false // Always false for create form
                                                ];
                                            }
                                        }
                                    }
                                @endphp

                                @if (!empty($modulePermissions))
                                    <div class="module-card">
                                        <div class="module-header">
                                            <div class="module-title">
                                                <div class="module-icon">
                                                    <i class="{{ $icon }}"></i>
                                                </div>
                                                {{ $module }}
                                            </div>
                                            <label class="module-select-all">
                                                <input type="checkbox" class="ischeck" data-id="{{ $moduleId }}">
                                                {{ __('All') }}
                                            </label>
                                        </div>

                                        <div class="permissions-list">
                                            @foreach ($modulePermissions as $permission)
                                                <div class="permission-item permission-{{ strtolower($permission['type']) }}">
                                                    <label class="premium-checkbox">
                                                        <input type="checkbox"
                                                               class="isscheck isscheck_{{ $moduleId }}"
                                                               id="permission{{ $permission['key'] }}"
                                                               name="permissions[]"
                                                               value="{{ $permission['key'] }}">
                                                        <span class="checkmark"></span>
                                                    </label>
                                                    <span class="permission-label">
                                                        {{ ucfirst(strtolower($permission['type'])) }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="button" class="premium-btn btn-cancel" data-dismiss="modal" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="premium-btn btn-create">
                    <i class="fas fa-plus"></i>
                    {{ __('Create Role') }}
                </button>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // Select all functionality
            $("#checkall").click(function() {
                $('input:checkbox').not(this).prop('checked', this.checked);
                updatePermissionItemStyles();
            });

            // Module select all functionality
            $(".ischeck").click(function() {
                var moduleId = $(this).data('id');
                var isChecked = this.checked;
                $('.isscheck_' + moduleId).prop('checked', isChecked);
                updatePermissionItemStyles();
            });

            // Individual permission checkbox functionality
            $(document).on('change', '.isscheck', function() {
                updatePermissionItemStyles();
            });

            // Update visual styles for permission items
            function updatePermissionItemStyles() {
                $('.permission-item').each(function() {
                    var checkbox = $(this).find('input[type="checkbox"]');
                    if (checkbox.prop('checked')) {
                        $(this).addClass('checked');
                    } else {
                        $(this).removeClass('checked');
                    }
                });
            }

            // Initialize styles
            updatePermissionItemStyles();

            // Click on permission item to toggle checkbox
            $(document).on('click', '.permission-item', function(e) {
                if (e.target.type !== 'checkbox') {
                    var checkbox = $(this).find('input[type="checkbox"]');
                    checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
                }
            });

            // Form validation
            $('form').on('submit', function(e) {
                var roleName = $('#name').val().trim();
                if (roleName === '') {
                    e.preventDefault();
                    alert('{{ __("Please enter a role name") }}');
                    $('#name').focus();
                    return false;
                }
            });

            // Enhanced interactions
            $('.module-card').hover(
                function() {
                    $(this).find('.module-icon').addClass('animate__animated animate__pulse');
                },
                function() {
                    $(this).find('.module-icon').removeClass('animate__animated animate__pulse');
                }
            );

            // Ripple effect for buttons
            $('.premium-btn').on('click', function(e) {
                const button = this;
                const ripple = document.createElement('span');
                const rect = button.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.style.position = 'absolute';
                ripple.style.borderRadius = '50%';
                ripple.style.background = 'rgba(255, 255, 255, 0.5)';
                ripple.style.transform = 'scale(0)';
                ripple.style.animation = 'ripple 0.6s linear';
                ripple.style.pointerEvents = 'none';

                button.appendChild(ripple);

                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });

            // Select all section click handler
            $('.select-all-section').on('click', function() {
                const checkbox = $(this).find('#checkall');
                checkbox.prop('checked', !checkbox.prop('checked')).trigger('click');
            });

            // Module select all click handler
            $('.module-select-all').on('click', function(e) {
                if (e.target.type !== 'checkbox') {
                    const checkbox = $(this).find('input[type="checkbox"]');
                    checkbox.prop('checked', !checkbox.prop('checked')).trigger('click');
                }
            });
        });

        // Add CSS for ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>