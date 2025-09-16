<div class="card bg-none card-box">
    <form method="POST" action="{{ url('warning') }}">
        @csrf
        <div class="row">
            @if (\Auth::user()->type != 'employee')
                <div class="form-group col-md-6 col-lg-6">
                    <label class="form-control-label">{{ __('Warning By') }}</label>
                    <select name="warning_by" class="form-control select2" required="required">
                        @foreach ($employees as $employeeId => $employeeName)
                            <option value="{{ $employeeId }}">{{ $employeeName }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="form-group col-md-6 col-lg-6">
                <label class="form-control-label">{{ __('Warning To') }}</label>
                <select name="warning_to" class="form-control select2">
                    @foreach ($employees as $employeeId => $employeeName)
                        <option value="{{ $employeeId }}">{{ $employeeName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6 col-lg-6">
                <label class="form-control-label">{{ __('Subject') }}</label>
                <input type="text" name="subject" class="form-control">
            </div>
            <div class="form-group col-md-6 col-lg-6">
                <label class="form-control-label">{{ __('Warning Date') }}</label>
                <input type="text" name="warning_date" class="form-control datepicker">
            </div>
            <div class="form-group col-md-12">
                <label class="form-control-label">{{ __('Description') }}</label>
                <textarea name="description" class="form-control" placeholder="{{ __('Enter Description') }}"></textarea>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-bs-dismiss="modal">
            </div>
        </div>
    </form>
</div>
