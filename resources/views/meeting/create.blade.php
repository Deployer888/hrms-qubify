<div class="card bg-none card-box">
    <form action="{{ url('meeting') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="branch_id" class="form-control-label">{{ __('Branch') }}</label>
                    <select class="form-control select2" name="branch_id" id="branch_id">
                        <option value="">{{ __('Select Branch') }}</option>
                        <option value="0">{{ __('All Branch') }}</option>
                        @foreach ($branch as $branchItem)
                            <option value="{{ $branchItem->id }}">{{ $branchItem->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="department_id" class="form-control-label">{{ __('Department') }}</label>
                    <select class="form-control select2" name="department_id[]" id="department_id" multiple>
                        <!-- Departments options will be loaded dynamically based on branch selection -->
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="employee_id" class="form-control-label">{{ __('Employee') }}</label>
                    <select class="form-control select2" name="employee_id[]" id="employee_id" multiple>
                        <!-- Employee options will be loaded dynamically based on department selection -->
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="title" class="form-control-label">{{ __('Meeting Title') }}</label>
                    <input type="text" name="title" id="title" class="form-control"
                        placeholder="{{ __('Enter Meeting Title') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="date" class="form-control-label">{{ __('Meeting Date') }}</label>
                    <input type="text" name="date" id="date" class="form-control datepicker">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="time" class="form-control-label">{{ __('Meeting Time') }}</label>
                    <input type="text" name="time" id="time" class="form-control timepicker">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="note" class="form-control-label">{{ __('Meeting Note') }}</label>
                    <textarea name="note" id="note" class="form-control" placeholder="{{ __('Enter Meeting Note') }}"></textarea>
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
