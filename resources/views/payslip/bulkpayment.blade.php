@extends('layouts.admin')
@section('page-title')
    {{ __('Bulk Payment') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('payslip.index') }}">{{ __('Pay Slip') }}</a></li>
    <li class="breadcrumb-item">{{ __('Bulk Payment') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Bulk Payment for') }} {{ date('F Y', strtotime($date . '-01')) }}</h5>
                </div>
                <div class="card-body">
                    @if($unpaidPayslips->count() > 0)
                        <form action="{{ route('payslip.bulkpayment', $date) }}" method="post">
                            @csrf
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="select-all" class="form-check-input">
                                        </th>
                                        <th>{{ __('Employee Name') }}</th>
                                        <th>{{ __('Employee ID') }}</th>
                                        <th>{{ __('Department') }}</th>
                                        <th>{{ __('Basic Salary') }}</th>
                                        <th>{{ __('Net Salary') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($unpaidPayslips as $payslip)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="employee[]" value="{{ $payslip->employee_id }}" class="form-check-input employee-checkbox">
                                            </td>
                                            <td>{{ $payslip->employee->name ?? 'N/A' }}</td>
                                            <td>{{ $payslip->employee->employee_id ?? 'N/A' }}</td>
                                            <td>{{ $payslip->employee->department->name ?? 'N/A' }}</td>
                                            <td>{{ Utility::getValByName('site_currency_symbol') }}{{ number_format($payslip->basic_salary, 2) }}</td>
                                            <td>{{ Utility::getValByName('site_currency_symbol') }}{{ number_format($payslip->net_payble, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5">{{ __('Total Selected Amount:') }}</th>
                                        <th id="total-amount">{{ Utility::getValByName('site_currency_symbol') }}0.00</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <i class="ti ti-alert-triangle"></i>
                                    <strong>{{ __('Warning:') }}</strong> {{ __('This action will mark the selected payslips as paid. This action cannot be undone.') }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <a href="{{ route('payslip.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                                <button type="submit" class="btn btn-success" id="bulk-pay-submit" disabled>
                                    <i class="ti ti-credit-card"></i> {{ __('Process Payment') }}
                                </button>
                            </div>
                        </div>
                        </form>
                    @else
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle"></i>
                            {{ __('No unpaid payslips found for the selected month.') }}
                        </div>
                        <a href="{{ route('payslip.index') }}" class="btn btn-primary">{{ __('Back to Payslips') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    <script>
        $(document).ready(function() {
            // Select all functionality
            $('#select-all').on('change', function() {
                $('.employee-checkbox').prop('checked', this.checked);
                updateTotalAmount();
                updateSubmitButton();
            });

            // Individual checkbox change
            $('.employee-checkbox').on('change', function() {
                updateSelectAllState();
                updateTotalAmount();
                updateSubmitButton();
            });

            // Update select all state
            function updateSelectAllState() {
                let totalCheckboxes = $('.employee-checkbox').length;
                let checkedCheckboxes = $('.employee-checkbox:checked').length;
                
                $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
                $('#select-all').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes);
            }

            // Update total amount
            function updateTotalAmount() {
                let total = 0;
                $('.employee-checkbox:checked').each(function() {
                    let row = $(this).closest('tr');
                    let netSalary = row.find('td:last').text();
                    // Extract numeric value
                    let amount = parseFloat(netSalary.replace(/[^0-9.-]+/g, '')) || 0;
                    total += amount;
                });
                
                $('#total-amount').text('{{ Utility::getValByName('site_currency_symbol') }}' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            }

            // Update submit button state
            function updateSubmitButton() {
                let checkedCount = $('.employee-checkbox:checked').length;
                $('#bulk-pay-submit').prop('disabled', checkedCount === 0);
            }

            // Form submission confirmation
            $('form').on('submit', function(e) {
                let checkedCount = $('.employee-checkbox:checked').length;
                if (checkedCount === 0) {
                    e.preventDefault();
                    alert('Please select at least one employee.');
                    return false;
                }
                
                if (!confirm('Are you sure you want to process payment for ' + checkedCount + ' employee(s)?')) {
                    e.preventDefault();
                    return false;
                }
                
                // Show loading state
                $('#bulk-pay-submit').prop('disabled', true).html('<i class="ti ti-loader"></i> Processing...');
            });
        });
    </script>
@endpush

@push('style-page')
    <style>
        .table th {
            border-top: none;
            font-weight: 600;
        }
        
        .employee-checkbox, #select-all {
            transform: scale(1.2);
        }
        
        .alert {
            border-left: 4px solid;
        }
        
        .alert-warning {
            border-left-color: #ffc107;
        }
        
        .alert-info {
            border-left-color: #17a2b8;
        }
        
        #total-amount {
            font-weight: bold;
            color: #28a745;
        }
        
        .btn {
            margin-right: 10px;
        }
        
        .card-header h5 {
            margin: 0;
            font-weight: 600;
        }
    </style>
@endpush