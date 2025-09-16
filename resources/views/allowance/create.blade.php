<div class="card bg-none card-box">
    <form action="{{ url('allowance') }}" method="POST">
        @csrf
        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
        <div class="row">
            <div class="form-group col-md-12">
                <label for="allowance_option" class="form-control-label">{{ __('Allowance Options*') }}</label>
                <select name="allowance_option" id="allowance_option" class="form-control select2" required>
                    @foreach ($allowance_options as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="title" class="form-control-label">{{ __('Title') }}</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>
            <div class="form-group col-md-6">
                <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                <input type="number" name="amount" id="amount" class="form-control" required step="0.01">
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
                <button type="button" class="btn-create bg-gray" data-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </form>
</div>
