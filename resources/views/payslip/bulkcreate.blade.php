<div class="card bg-none card-box">
    <form action="{{ url('payslip/bulkpayment/' . $date) }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {{ __('Total Unpaid Employee') }} <b>{{ count($unpaidEmployees) }}</b> {{ _('out of') }}
                    <b>{{ count($Employees) }}</b>
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Bulk Payment') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
