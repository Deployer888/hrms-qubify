<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaySlipRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \Auth::user()->can('Create Pay Slip') || \Auth::user()->can('Edit Pay Slip');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [];

        // Rules for payslip generation
        if ($this->isMethod('post') && $this->routeIs('payslip.store')) {
            $rules = [
                'month' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:12'
                ],
                'year' => [
                    'required',
                    'integer',
                    'min:' . (date('Y') - 5),
                    'max:' . (date('Y') + 1)
                ]
            ];
        }

        // Rules for payslip update
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules = [
                'basic_salary' => [
                    'required',
                    'numeric',
                    'min:0',
                    'max:999999.99'
                ],
                'net_payble' => [
                    'required',
                    'numeric',
                    'min:0',
                    'max:999999.99'
                ],
                'allowance' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:999999.99'
                ],
                'commission' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:999999.99'
                ],
                'loan' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:999999.99'
                ],
                'saturation_deduction' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:999999.99'
                ],
                'other_payment' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:999999.99'
                ],
                'overtime' => [
                    'nullable',
                    'numeric',
                    'min:0',
                    'max:999999.99'
                ]
            ];
        }

        // Rules for bulk payment
        if ($this->isMethod('post') && $this->routeIs('payslip.bulkpayment')) {
            $rules = [
                'employee' => [
                    'required',
                    'array',
                    'min:1'
                ],
                'employee.*' => [
                    'required',
                    'integer',
                    'exists:employees,id'
                ]
            ];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'month.required' => 'Please select a month.',
            'month.integer' => 'Month must be a valid number.',
            'month.min' => 'Month must be between 1 and 12.',
            'month.max' => 'Month must be between 1 and 12.',
            
            'year.required' => 'Please select a year.',
            'year.integer' => 'Year must be a valid number.',
            'year.min' => 'Year cannot be more than 5 years in the past.',
            'year.max' => 'Year cannot be more than 1 year in the future.',
            
            'basic_salary.required' => 'Basic salary is required.',
            'basic_salary.numeric' => 'Basic salary must be a valid number.',
            'basic_salary.min' => 'Basic salary cannot be negative.',
            'basic_salary.max' => 'Basic salary cannot exceed 999,999.99.',
            
            'net_payble.required' => 'Net payable amount is required.',
            'net_payble.numeric' => 'Net payable amount must be a valid number.',
            'net_payble.min' => 'Net payable amount cannot be negative.',
            'net_payble.max' => 'Net payable amount cannot exceed 999,999.99.',
            
            'allowance.numeric' => 'Allowance must be a valid number.',
            'allowance.min' => 'Allowance cannot be negative.',
            'allowance.max' => 'Allowance cannot exceed 999,999.99.',
            
            'commission.numeric' => 'Commission must be a valid number.',
            'commission.min' => 'Commission cannot be negative.',
            'commission.max' => 'Commission cannot exceed 999,999.99.',
            
            'loan.numeric' => 'Loan amount must be a valid number.',
            'loan.min' => 'Loan amount cannot be negative.',
            'loan.max' => 'Loan amount cannot exceed 999,999.99.',
            
            'saturation_deduction.numeric' => 'Deduction amount must be a valid number.',
            'saturation_deduction.min' => 'Deduction amount cannot be negative.',
            'saturation_deduction.max' => 'Deduction amount cannot exceed 999,999.99.',
            
            'other_payment.numeric' => 'Other payment must be a valid number.',
            'other_payment.min' => 'Other payment cannot be negative.',
            'other_payment.max' => 'Other payment cannot exceed 999,999.99.',
            
            'overtime.numeric' => 'Overtime amount must be a valid number.',
            'overtime.min' => 'Overtime amount cannot be negative.',
            'overtime.max' => 'Overtime amount cannot exceed 999,999.99.',
            
            'employee.required' => 'Please select at least one employee.',
            'employee.array' => 'Invalid employee selection.',
            'employee.min' => 'Please select at least one employee.',
            'employee.*.required' => 'Employee selection is required.',
            'employee.*.integer' => 'Invalid employee ID.',
            'employee.*.exists' => 'Selected employee does not exist.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'month' => 'month',
            'year' => 'year',
            'basic_salary' => 'basic salary',
            'net_payble' => 'net payable amount',
            'allowance' => 'allowance',
            'commission' => 'commission',
            'loan' => 'loan',
            'saturation_deduction' => 'deduction',
            'other_payment' => 'other payment',
            'overtime' => 'overtime',
            'employee' => 'employees',
            'employee.*' => 'employee'
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Custom validation for payslip generation
            if ($this->isMethod('post') && $this->routeIs('payslip.store')) {
                $this->validatePayslipGeneration($validator);
            }

            // Custom validation for payslip update
            if ($this->isMethod('put') || $this->isMethod('patch')) {
                $this->validatePayslipUpdate($validator);
            }
        });
    }

    /**
     * Custom validation for payslip generation.
     */
    private function validatePayslipGeneration($validator)
    {
        $month = $this->input('month');
        $year = $this->input('year');

        if ($month && $year) {
            // Check if the selected month is not in the future
            $selectedDate = \Carbon\Carbon::createFromDate($year, $month, 1);
            $currentDate = \Carbon\Carbon::now()->startOfMonth();

            if ($selectedDate->greaterThan($currentDate)) {
                $validator->errors()->add('month', 'Cannot generate payslips for future months.');
            }

            // Check if payslips already exist for this month
            $salaryMonth = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);
            $existingPayslips = \App\Models\PaySlip::where('salary_month', $salaryMonth)
                ->whereHas('employee', function($q) {
                    $q->where('created_by', \Auth::user()->creatorId());
                })
                ->exists();

            if ($existingPayslips) {
                $validator->errors()->add('month', 'Payslips for this month already exist.');
            }
        }
    }

    /**
     * Custom validation for payslip update.
     */
    private function validatePayslipUpdate($validator)
    {
        $basicSalary = $this->input('basic_salary', 0);
        $allowance = $this->input('allowance', 0);
        $commission = $this->input('commission', 0);
        $overtime = $this->input('overtime', 0);
        $otherPayment = $this->input('other_payment', 0);
        
        $loan = $this->input('loan', 0);
        $saturationDeduction = $this->input('saturation_deduction', 0);
        
        $netPayable = $this->input('net_payble', 0);

        // Calculate expected net payable
        $totalEarnings = $basicSalary + $allowance + $commission + $overtime + $otherPayment;
        $totalDeductions = $loan + $saturationDeduction;
        $expectedNetPayable = $totalEarnings - $totalDeductions;

        // Allow some tolerance for manual adjustments (within 10% difference)
        $tolerance = abs($expectedNetPayable * 0.1);
        $difference = abs($netPayable - $expectedNetPayable);

        if ($difference > $tolerance && $expectedNetPayable > 0) {
            $validator->errors()->add('net_payble', 
                'Net payable amount seems incorrect. Expected approximately ' . 
                number_format($expectedNetPayable, 2) . ' based on earnings and deductions.'
            );
        }

        // Ensure basic salary is not zero if net payable is positive
        if ($netPayable > 0 && $basicSalary <= 0) {
            $validator->errors()->add('basic_salary', 'Basic salary must be greater than zero when net payable is positive.');
        }
    }
}