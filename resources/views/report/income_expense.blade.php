@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Income Vs Expense') }}
@endsection

@push('theme-script')
    <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
@endpush
{{-- {{ dd(json_encode($data)) }} --}}
@push('script-page')
    <script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
    <script>
        var options = {
            colors: ['#6777ef', '#fc544b'],
            series: {!! json_encode($data) !!},
            chart: {
                type: 'bar',
                height: 400,
                toolbar: {
                    show: false
                },
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: false,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: {!! json_encode($labels) !!},
            },
            yaxis: {
                title: {
                    text: "{{ \App\Models\Utility::getValByName('site_currency_symbol') }} "
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return "{{ \App\Models\Utility::getValByName('site_currency_symbol') }} " + val
                    }
                }
            },
            legend: {
                show: true,
                horizontalAlign: 'right',
            },

        };
        var chart = new ApexCharts(document.querySelector("#chart-finance"), options);
        setTimeout(function() {
            chart.render();
        }, 500);

        var filename = $('#filename').val();

        function saveAsPDF() {
            var element = document.getElementById('printableArea');
            var opt = {
                margin: 0.3,
                filename: filename,
                image: {
                    type: 'jpeg',
                    quality: 1
                },
                html2canvas: {
                    scale: 4,
                    dpi: 72,
                    letterRendering: true
                },
                jsPDF: {
                    unit: 'in',
                    format: 'A2'
                }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>
@endpush

@section('action-button')
    <div class="row d-flex justify-content-end">
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
            <form method="GET" action="{{ route('report.income-expense') }}" id="report_income_expense">
                <div class="all-select-box">
                    <div class="btn-box">
                        <label for="start_month" class="text-type">Start Month</label>
                        <input type="month" name="start_month" id="start_month" class="month-btn form-control"
                            value="">
                    </div>
                </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12">
            <div class="all-select-box">
                <div class="btn-box">
                    <label for="end_month" class="text-type">End Month</label>
                    <input type="month" name="end_month" id="end_month" class="month-btn form-control" value="">
                </div>
            </div>
        </div>

        <div class="col-auto my-custom">
            <a href="#" class="apply-btn"
                onclick="document.getElementById('report_income_expense').submit(); return false;" data-toggle="tooltip"
                data-original-title="{{ __('apply') }}">
                <span class="btn-inner--icon"><i class="fas fa-search"></i></span>
            </a>
            <a href="{{ route('report.income-expense') }}" class="reset-btn" data-toggle="tooltip"
                data-original-title="{{ __('Reset') }}">
                <span class="btn-inner--icon"><i class="fas fa-trash-restore-alt"></i></span>
            </a>
            <a href="#" class="action-btn" onclick="saveAsPDF()" data-toggle="tooltip"
                data-original-title="{{ __('Download') }}">
                <span class="btn-inner--icon"><i class="fas fa-download"></i></span>
            </a>
        </div>
    </div>
    </form>
@endsection

@section('content')
    <div id="printableArea">
        <div class="row mt-3">
            <div class="col-xl-3 col-md-6 col-lg-3">
                <div class="card p-4 mb-4">
                    <input type="hidden"
                        value="{{ __('Income vs Expense Report of') . ' ' }}{{ $filter['startDateRange'] . ' to ' . $filter['endDateRange'] }}"
                        id="filename">
                    <h5 class="report-text gray-text mb-0">{{ __('Report') }} :</h5>
                    <h5 class="report-text mb-0">{{ __('Income vs Expense Summary') }}</h5>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-lg-3">
                <div class="card p-4 mb-4">
                    <h5 class="report-text gray-text mb-0">{{ __('Duration') }} :</h5>
                    <h5 class="report-text mb-0">{{ $filter['startDateRange'] . ' to ' . $filter['endDateRange'] }}</h5>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-lg-3">
                <div class="card p-4 mb-4">
                    <h5 class="report-text gray-text mb-0">{{ __('Total Income') }} : <span
                            class="text-green">{{ \Auth::user()->priceFormat($incomeCount) }}</span></h5>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-lg-3">
                <div class="card p-4 mb-4">
                    <h5 class="report-text gray-text mb-0">{{ __('Total Expense') }} : <span
                            class="text-red">{{ \Auth::user()->priceFormat($expenseCount) }}</span></h5>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card bg-none">
                <div id="chart-finance" data-color="primary" class="p-3"></div>
            </div>
        </div>
    </div>
@endsection
