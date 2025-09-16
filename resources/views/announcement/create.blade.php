<div class="card bg-none card-box">
    <form action="{{ url('announcement') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="title" class="form-control-label">{{ __('Announcement Title') }}</label>
                    <input type="text" name="title" id="title" class="form-control"
                        placeholder="{{ __('Enter Announcement Title') }}" value="{{ old('title') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="branch_id" class="form-control-label">{{ __('Branch') }}</label>
                    <select name="branch_id" id="branch_id" class="form-control select2"
                        placeholder="{{ __('Select Branch') }}">
                        <option value="">{{ __('Select Branch') }}</option>
                        <option value="0">{{ __('All Branch') }}</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="department_id" class="form-control-label">{{ __('Department') }}</label>
                    <select name="department_id[]" id="department_id" class="form-control select2" multiple
                        placeholder="{{ __('Select Department') }}">
                        <!-- Options for departments go here -->
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="employee_id" class="form-control-label">{{ __('Employee') }}</label>
                    <select name="employee_id[]" id="employee_id" class="form-control select2" multiple
                        placeholder="{{ __('Select Employee') }}">
                        <!-- Options for employees go here -->
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="start_date" class="form-control-label">{{ __('Announcement Start Date') }}</label>
                    <input type="text" name="start_date" id="start_date" class="form-control datepicker"
                        value="{{ old('start_date') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="end_date" class="form-control-label">{{ __('Announcement End Date') }}</label>
                    <input type="text" name="end_date" id="end_date" class="form-control datepicker"
                        value="{{ old('end_date') }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="description" class="form-control-label">{{ __('Announcement Description') }}</label>
                    <textarea name="description" id="description" class="form-control" placeholder="{{ __('Enter Announcement Title') }}">{{ old('description') }}</textarea>
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
                <button type="button" class="btn-create bg-gray" data-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </form>
</div>
