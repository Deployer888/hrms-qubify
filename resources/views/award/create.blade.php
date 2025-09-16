<div class="card bg-none card-box">
    <form action="{{ url('award') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Employee select -->
            <div class="form-group col-md-6 col-lg-6">
                <label for="employee_id" class="form-control-label">{{ __('Employee') }}</label>
                <select name="employee_id" id="employee_id" class="form-control select2" required>
                    @foreach ($employees as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Award Type select -->
            <div class="form-group col-md-6 col-lg-6">
                <label for="award_type" class="form-control-label">{{ __('Award Type') }}</label>
                <select name="award_type" id="award_type" class="form-control select2" required>
                    @foreach ($awardtypes as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Date input -->
            <div class="form-group col-md-6 col-lg-6">
                <label for="date" class="form-control-label">{{ __('Date') }}</label>
                <input type="text" name="date" id="date" class="form-control datepicker">
            </div>
            <!-- Gift input -->
            <div class="form-group col-md-6 col-lg-6">
                <label for="gift" class="form-control-label">{{ __('Gift') }}</label>
                <input type="text" name="gift" id="gift" class="form-control" placeholder="{{ __('Enter Gift') }}">
            </div>
            <!-- Description input -->
            <div class="form-group col-md-12">
                <label for="description" class="form-control-label">{{ __('Description') }}</label>
                <textarea name="description" id="description" class="form-control" placeholder="{{ __('Enter Description') }}"></textarea>
            </div>
            <!-- Submit and cancel buttons -->
            <div class="col-12">
                <button type="submit" class="btn-create badge-blue">{{ __('Create') }}</button>
                <button type="button" class="btn-create bg-gray" data-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </form>
</div>
