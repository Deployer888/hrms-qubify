<div class="card bg-none card-box">
    <form action="travel" method="post">
        @csrf
        <div class="row">
            <div class="form-group col-md-12">
                <label for="employee_id" class="form-control-label">{{ __('Employee') }}</label>
                <select name="employee_id" id="employee_id" class="form-control select2" required>
                    @foreach ($employees as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-lg-6 col-md-6">
                <label for="start_date" class="form-control-label">{{ __('Start Date') }}</label>
                <input type="text" name="start_date" id="start_date" class="form-control datepicker">
            </div>
            <div class="form-group col-lg-6 col-md-6">
                <label for="end_date" class="form-control-label">{{ __('End Date') }}</label>
                <input type="text" name="end_date" id="end_date" class="form-control datepicker">
            </div>
            <div class="form-group col-lg-6 col-md-6">
                <label for="purpose_of_visit" class="form-control-label">{{ __('Purpose of Visit') }}</label>
                <input type="text" name="purpose_of_visit" id="purpose_of_visit" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="place_of_visit" class="form-control-label">{{ __('Place Of Visit') }}</label>
                <input type="text" name="place_of_visit" id="place_of_visit" class="form-control">
            </div>
            <div class="form-group col-md-12">
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
