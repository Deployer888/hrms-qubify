<div class="card bg-none card-box">
    <div class="row px-3">
        <div class="col-md-4 mb-3">
            <h5 class="emp-title mb-0">{{ __('Employee') }}</h5>
            <h5 class="emp-title black-text">
                {{ !empty($payslip->employees) ? \Auth::user()->employeeIdFormat($payslip->employees->employee_id) : '' }}
            </h5>
        </div>
        <div class="col-md-4 mb-3">
            <h5 class="emp-title mb-0">{{ __('Basic Salary') }}</h5>
            <h5 class="emp-title black-text">{{ \Auth::user()->priceFormat($payslip->basic_salary) }}</h5>
        </div>
        <div class="col-md-4 mb-3">
            <h5 class="emp-title mb-0">{{ __('Payroll Month') }}</h5>
            <h5 class="emp-title black-text">{{ \Auth::user()->dateFormat($payslip->salary_month) }}</h5>
        </div>

        <div class="col-lg-12 our-system">
            <form action="{{ route('payslip.updateemployee', $payslip->employee_id) }}" method="post">
                @csrf
                <input type="hidden" name="payslip_id" value="{{ $payslip->id }}" class="form-control">
                <div class="row">
                    <ul class="nav nav-tabs my-4">
                        <li>
                            <a data-toggle="tab" href="#allowance" class="active">{{ __('Allowance') }}</a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#commission">{{ __('Commission') }}</a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#loan">{{ __('Loan') }}</a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#deduction">{{ __('Saturation Deduction') }}</a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#payment">{{ __('Other Payment') }}</a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#overtime">{{ __('Overtime') }}</a>
                        </li>
                    </ul>
                    <div class="tab-content pt-4">
                        <div id="allowance" class="tab-pane in active">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card bg-none mb-0">
                                        <div class="row px-3">
                                            @php
                                                $allowances = json_decode($payslip->allowance);
                                            @endphp
                                            @foreach ($allowances as $allowance)
                                                <div class="col-md-12 form-group">
                                                    <label class="form-control-label">{{ $allowance->title }}</label>
                                                    <input type="text" name="allowance[]"
                                                        value="{{ $allowance->amount }}" class="form-control">
                                                    <input type="hidden" name="allowance_id[]"
                                                        value="{{ $allowance->id }}" class="form-control">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="commission" class="tab-pane">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card bg-none mb-0">
                                        <div class="row px-3">
                                            @php
                                                $commissions = json_decode($payslip->commission);
                                            @endphp
                                            @foreach ($commissions as $commission)
                                                <div class="col-md-12 form-group">
                                                    <label class="form-control-label">{{ $commission->title }}</label>
                                                    <input type="text" name="commission[]"
                                                        value="{{ $commission->amount }}" class="form-control">
                                                    <input type="hidden" name="commission_id[]"
                                                        value="{{ $commission->id }}" class="form-control">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="loan" class="tab-pane">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card bg-none mb-0">
                                        <div class="row px-3">
                                            @php
                                                $loans = json_decode($payslip->loan);
                                            @endphp
                                            @foreach ($loans as $loan)
                                                <div class="col-md-12 form-group">
                                                    <label class="form-control-label">{{ $loan->title }}</label>
                                                    <input type="text" name="loan[]" value="{{ $loan->amount }}"
                                                        class="form-control">
                                                    <input type="hidden" name="loan_id[]" value="{{ $loan->id }}"
                                                        class="form-control">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="deduction" class="tab-pane">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card bg-none mb-0">
                                        <div class="row px-3">
                                            @php
                                                $saturation_deductions = json_decode($payslip->saturation_deduction);
                                            @endphp
                                            @foreach ($saturation_deductions as $deduction)
                                                <div class="col-md-12 form-group">
                                                    <label class="form-control-label">{{ $deduction->title }}</label>
                                                    <input type="text" name="saturation_deductions[]"
                                                        value="{{ $deduction->amount }}" class="form-control">
                                                    <input type="hidden" name="saturation_deductions_id[]"
                                                        value="{{ $deduction->id }}" class="form-control">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="payment" class="tab-pane">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card bg-none mb-0">
                                        <div class="row px-3">
                                            @php
                                                $other_payments = json_decode($payslip->other_payment);
                                            @endphp
                                            @foreach ($other_payments as $payment)
                                                <div class="col-md-12 form-group">
                                                    <label class="form-control-label">{{ $payment->title }}</label>
                                                    <input type="text" name="other_payment[]"
                                                        value="{{ $payment->amount }}" class="form-control">
                                                    <input type="hidden" name="other_payment_id[]"
                                                        value="{{ $payment->id }}" class="form-control">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="overtime" class="tab-pane">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card bg-none mb-0">
                                        <div class="row px-3">
                                            @php
                                                $overtimes = json_decode($payslip->overtime);
                                            @endphp
                                            @foreach ($overtimes as $overtime)
                                                <div class="col-md-6 form-group">
                                                    <label
                                                        class="form-control-label">{{ $overtime->title . ' ' . __('Rate') }}</label>
                                                    <input type="text" name="rate[]"
                                                        value="{{ $overtime->rate }}" class="form-control">
                                                    <input type="hidden" name="rate_id[]"
                                                        value="{{ $overtime->id }}" class="form-control">
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label
                                                        class="form-control-label">{{ $overtime->title . ' ' . __('Hours') }}</label>
                                                    <input type="text" name="hours[]"
                                                        value="{{ $overtime->rate }}" class="form-control">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-4 text-right">
                    <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                    <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray"
                        data-dismiss="modal">
                </div>
            </form>
        </div>
    </div>
</div>
