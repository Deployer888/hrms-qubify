<div class="card bg-none card-box">
    <form action="{{ url('otherpayment') }}" method="POST">
        @csrf
        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
        <div class="row">
            <div class="form-group col-md-12">
                <label for="title" class="form-control-label">{{ __('Title') }}</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>
            <div class="form-group col-md-12">
                <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                <input type="number" name="amount" id="amount" class="form-control" required step="0.01">
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
