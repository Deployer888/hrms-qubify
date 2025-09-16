<div class="card bg-none card-box">
    <form action="{{ url('overtime') }}" method="POST">
        @csrf
        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
        <div class="row">
            <div class="form-group col-md-6">
                <label for="title" class="form-control-label">{{ __('Overtime Title*') }}</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
                <label for="number_of_days" class="form-control-label">{{ __('Number of days') }}</label>
                <input type="number" name="number_of_days" id="number_of_days" class="form-control" required
                    step="0.01">
            </div>
            <div class="form-group col-md-6">
                <label for="hours" class="form-control-label">{{ __('Hours') }}</label>
                <input type="number" name="hours" id="hours" class="form-control" required step="0.01">
            </div>
            <div class="form-group col-md-6">
                <label for="rate" class="form-control-label">{{ __('Rate') }}</label>
                <input type="number" name="rate" id="rate" class="form-control" required step="0.01">
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
