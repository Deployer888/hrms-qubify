<div class="card bg-none card-box">
    <form action="{{ url('loan') }}" method="POST">
        @csrf
        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
        <div class="row">
            <div class="form-group col-md-12">
                <label for="title" class="form-control-label">{{ __('Title') }}</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}"
                    required>
            </div>
            <div class="form-group col-md-6">
                <label for="loan_option" class="form-control-label">{{ __('Loan Options*') }}</label>
                <select name="loan_option" id="loan_option" class="form-control select2" required>
                    @foreach ($loan_options as $key => $option)
                        <option value="{{ $key }}" {{ old('loan_option') == $key ? 'selected' : '' }}>
                            {{ $option }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="amount" class="form-control-label">{{ __('Loan Amount') }}</label>
                <input type="number" name="amount" id="amount" class="form-control" value="{{ old('amount') }}"
                    required step="0.01">
            </div>
            <div class="form-group col-md-6">
                <label for="start_date" class="form-control-label">{{ __('Start Date') }}</label>
                <input type="text" name="start_date" id="start_date" class="form-control datepicker"
                    value="{{ old('start_date') }}" required>
            </div>
            <div class="form-group col-md-6">
                <label for="end_date" class="form-control-label">{{ __('End Date') }}</label>
                <input type="text" name="end_date" id="end_date" class="form-control datepicker"
                    value="{{ old('end_date') }}" required>
            </div>
            <div class="form-group col-md-12">
                <label for="reason" class="form-control-label">{{ __('Reason') }}</label>
                <textarea name="reason" id="reason" class="form-control" rows="1" required>{{ old('reason') }}</textarea>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
