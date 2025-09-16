<div class="card bg-none card-box">
    <form action="{{ url('resignation') }}" method="POST">
        @csrf
        <div class="row">
            @if (\Auth::user()->type != 'employee')
                <div class="form-group col-lg-12">
                    <label for="employee_id" class="form-control-label">{{ __('Employee') }}</label>
                    <select name="employee_id" id="employee_id" class="form-control select2" required>
                        @foreach ($employees as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="form-group col-lg-6 col-md-6">
                <label for="notice_date" class="form-control-label">{{ __('Notice Date') }}</label>
                <input type="text" name="notice_date" id="notice_date" class="form-control datepicker"
                    value="{{ old('notice_date') }}">
            </div>
            <div class="form-group col-lg-6 col-md-6">
                <label for="resignation_date" class="form-control-label">{{ __('Resignation Date') }}</label>
                <input type="text" name="resignation_date" id="resignation_date" class="form-control datepicker"
                    value="{{ old('resignation_date') }}">
            </div>
            <div class="form-group col-lg-12">
                <label for="description" class="form-control-label">{{ __('Description') }}</label>
                <textarea name="description" id="description" class="form-control" placeholder="{{ __('Enter Description') }}">{{ old('description') }}</textarea>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
