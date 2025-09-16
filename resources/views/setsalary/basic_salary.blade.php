<div class="card bg-none card-box">
    <form action="{{ route('employee.salary.update', $employee->id) }}" method="post">
        @csrf
        <div class="row">
            <div class="form-group col-md-12">
                <label for="salary_type" class="form-control-label">{{ __('Payslip Type*') }}</label>
                <select name="salary_type" id="salary_type" class="form-control select2" required>
                    @foreach ($payslip_type as $key => $type)
                        <option value="{{ $key }}" {{ $employee->salary_type == $key ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-12">
                <label for="salary" class="form-control-label">{{ __('Salary') }}</label>
                <input type="number" name="salary" id="salary" class="form-control" required
                    value="{{ $employee->salary }}">
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Save Change') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-bs-dismiss="modal">
            </div>
        </div>
    </form>
</div>
