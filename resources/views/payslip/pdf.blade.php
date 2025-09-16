@php
    $logo = asset(Storage::url('uploads/logo/'));
    $company_logo = Utility::getValByName('company_logo');
@endphp
<style>
    td {
        padding: 5px !important;
    }

    /* Printable area */
    #printableArea {
        font-family: Arial, sans-serif;
        padding: 10px;
        border: 1px solid #ccc;
        width: 100%;
        box-sizing: border-box;
    }

    table {
        width: 100%;
        margin-top: 20px;
        border-collapse: collapse;
    }


    table th, table td {
        padding: 8px 10px;
        border: 1px solid #ddd;
        text-align: left;
    }

    /* Adjust the width of second column */
    .column-second {
        width: 25%;
    }

    .column-first {
        width: 75%;
    }

    .font-size-larger {
        font-size: larger;
    }

    .font-weight-bold {
        font-weight: bold;
    }

    .note {
        padding-top: 15px;
        font-size: 14px;
    }
    
    .modal-content{
        width: fit-content!important;
    }
    
    .modal-dialog.modal-dialog-centered .card{
        padding-bottom: 40px!important;
    }
</style>

<div class="card bg-none card-box">
    <div class="text-md-right mb-2">
        <a href="#" class="btn btn-xs rounded-pill btn-warning" onclick="saveAsPDF()"><span class="fa fa-download"></span></a>
        <a title="Mail Send" href="{{route('payslip.send',[$employee->id,$payslip->salary_month])}}" class="btn btn-xs rounded-pill btn-primary"><span class="fa fa-paper-plane"></span></a>
    </div>

    <div class="invoice" id="printableArea">
        <div class="invoice-print">
            <div class="row">
                <div class="col-lg-12">
                    <div class="invoice-title row">
                        <div class="invoice-number col-6">
                            <img src="{{$logo.'/'.(isset($company_logo) && !empty($company_logo)?$company_logo:'logo.png')}}" width="170px;">
                        </div>
                        <div class="text-right col-6">
                            <p style="font-size:14px">QUBIFY TECHNOLOGIES PVT LTD<br>
                            SECOND FLOOR, 242, TRICITY PLAZA,<br> PEERMUCHELLA, SECTOR 20 PANCHKULA</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="payslip-title font-size-larger">PAYSLIP FOR {{ strtoupper(date('F, Y', strtotime($payslip->salary_month))) }}</div>
            <div class="row">
                <div class="">
                    <table class="table table-bordered">
                        <tbody>
                            <tr class="font-weight-bold">
                                <th class="text-center">{{ $employee->name }}</th>
                            </tr>

                            <tr>
                                <td><strong>Employee ID</strong></td>
                                <td><strong>Date Joined</strong></td>
                                <td><strong>Department</strong></td>
                            </tr>
                            <tr>
                                <td>{{ $employeeID }}</td>
                                <td>{{ $employee->dateFormat($employee->company_doj) }}</td>
                                <td>{{!empty($employee->department) ? $employee->department->name : ''}}</td>
                            </tr>

                            <tr>
                                <td><strong>Designation</strong></td>
                                <td><strong>Payment Mode</strong></td>
                                <td><strong>Bank</strong></td>
                            </tr>
                            <tr>
                                <td>{{!empty($employee->designation) ? $employee->designation->name : ''}}</td>
                                <td>Bank Transfer</td>
                                <td>{{$employee->bank_name}}</td>
                            </tr>

                            <tr>
                                <td><strong>Bank IFSC</strong></td>
                                <td><strong>Bank Account</strong></td>
                                <td><strong>PAN</strong></td>
                            </tr>
                            <tr>
                                <td>{{ $employee->bank_identifier_code }}</td>
                                <td>{{ $employee->account_number }}</td>
                                <td>{{ $employee->tax_payer_id }}</td>
                            </tr>

                            <tr class="font-size-larger">
                                <td><strong>SALARY DETAILS</strong></td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td><strong>Actual Payable Days</strong></td>
                                <td><strong>Total Working Days</strong></td>
                                <td><strong>Loss of Pay Days</strong></td>
                                <td><strong>Days Payable</strong></td>
                            </tr>
                            <tr>
                                <td>{{ $payslip->actual_payable_days }}</td>
                                <td>{{ $payslip->total_working_days }}</td>
                                <td>{{ $payslip->loss_of_pay_days }}</td>
                                <td>{{ $payslip->actual_payable_days }}</td>
                            </tr>

                            <tr class="font-size-larger">
                                <td><strong>EARNINGS</strong></td>
                                <td></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td><strong>Basic</strong></td>
                                <td>₹ {{ $payslip->basic_salary }}</td>
                                <td><strong>DEDUCTIONS</strong></td>
                                <td>₹ {{ $payslip->total_deduction }}</td>
                            </tr>
                            <tr>
                                <td><strong>HRA</strong></td>
                                <td>₹ {{ $payslip->hra }}</td>
                                <td><strong>TDS</strong></td>
                                <td>₹ {{ $payslip->tds }}</td>
                            </tr>
                            <tr>
                                <td><strong>Special Allowance</strong></td>
                                <td>₹ {{ $payslip->special_allowance }}</td>
                            </tr>

                            <tr class="font-size-larger">
                                <td><strong>Total Earnings </strong></td>
                                <td><strong>₹ {{ $payslip->total_earnings }}</strong></td>
                                <td><strong>Total Deduction</strong></td>
                                <td><strong>₹ {{ $payslip->total_deduction }}</strong></td>
                            </tr>

                            <tr>
                                <td><strong>Net Salary Payable</strong></td>
                                <td>₹ {{ $payslip->net_payble }}</td>
                            </tr>
                            <?php
                                function numberToWords($number)
                                {
                                    // Ensure you have the php-intl extension installed and enabled.
                                    $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
                                
                                    return ucfirst($formatter->format($number));
                                }
                            ?>
                            <tr>
                                <td><strong>Net Salary in words</strong> </td>
                                <td style="white-space: normal; word-wrap: break-word; line-height: 1.5;">
                                    {{ numberToWords($payslip->net_payble) }} only.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="note">
                **Note: All amounts displayed in this pay slip are in INR
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
<script>
    function saveAsPDF() {
        var element = document.getElementById('printableArea');
        var opt = {
            margin: [0.5, 0.2, 0.5, 0.2], 
            padding: [0.0, 0.2, 0.0, 0.2],  
            filename: '{{$employee->name}}.pdf',
            image: {type: 'jpeg', quality: 1},
            html2canvas: {scale: 3, dpi: 72, letterRendering: true},
            jsPDF: {unit: 'in', format: 'A4'}
        };
        html2pdf().set(opt).from(element).save();
    }
</script>
