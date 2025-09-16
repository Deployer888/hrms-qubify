<div class="card bg-none card-box">
    <form method="POST" action="{{ url('termination') }}">
        @csrf
        <div class="row">
            <div class="form-group col-lg-6 col-md-6">
                <label for="employee_id" class="form-control-label">{{ __('Employee') }}</label>
                <select name="employee_id" id="employee_id" class="form-control select2" required>
                    @foreach ($employees as $employeeId => $employeeName)
                        <option value="{{ $employeeId }}">{{ $employeeName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-6 col-md-6">
                <label for="termination_type" class="form-control-label">{{ __('Termination Type') }}</label>
                <select name="termination_type" id="termination_type" class="form-control select2" required>
                    @foreach ($terminationtypes as $typeId => $typeName)
                        <option value="{{ $typeId }}">{{ $typeName }}</option>
                    @endforeach
                </select>
            </div>
            {{-- <div class="form-group col-lg-6 col-md-6">
                <label for="notice_date" class="form-control-label">{{ __('Notice Date') }}</label>
                <input type="text" name="notice_date" id="notice_date" class="form-control datepicker">
            </div> --}}
            <div class="form-group col-lg-6 col-md-6">
                <label for="termination_date" class="form-control-label">{{ __('Termination Date') }}</label>
                <input type="text" name="termination_date" id="termination_date" class="form-control datepicker">
            </div>
            <div class="form-group col-lg-12">
                <label for="description" class="form-control-label">{{ __('Description') }}</label>
                <textarea name="description" id="description" class="form-control" placeholder="{{ __('Enter Description') }}"></textarea>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-bs-dismiss="modal">
            </div>
        </div>
    </form>
</div>
