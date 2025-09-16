<div class="card bg-none card-box">
    <form action="{{ url('goaltracking') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="branch" class="form-control-label">{{ __('Branch') }}</label>
                    <select name="branch" id="branch" class="form-control select2" required>
                        @foreach ($brances as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="goal_type" class="form-control-label">{{ __('Goal Types') }}</label>
                    <select name="goal_type" id="goal_type" class="form-control select2" required>
                        @foreach ($goalTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="start_date" class="form-control-label">{{ __('Start Date') }}</label>
                    <input type="text" name="start_date" id="start_date" class="form-control datepicker">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="end_date" class="form-control-label">{{ __('End Date') }}</label>
                    <input type="text" name="end_date" id="end_date" class="form-control datepicker">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="subject" class="form-control-label">{{ __('Subject') }}</label>
                    <input type="text" name="subject" id="subject" class="form-control">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="target_achievement" class="form-control-label">{{ __('Target Achievement') }}</label>
                    <input type="text" name="target_achievement" id="target_achievement" class="form-control">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="description" class="form-control-label">{{ __('Description') }}</label>
                    <textarea name="description" id="description" class="form-control"></textarea>
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
