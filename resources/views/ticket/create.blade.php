<div class="card bg-none card-box">
    <form action="ticket" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="title" class="form-control-label">{{ __('Subject') }}</label>
                    <input type="text" name="title" class="form-control"
                        placeholder="{{ __('Enter Ticket Subject') }}">
                </div>
            </div>
        </div>
        @if (\Auth::user()->type != 'employee')
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="employee_id" class="form-control-label">{{ __('Ticket for Employee') }}</label>
                        <select name="employee_id" class="form-control select2"
                            placeholder="{{ __('Select Employee') }}">
                            @foreach ($employees as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="priority" class="form-control-label">{{ __('Priority') }}</label>
                    <select name="priority" class="form-control select2">
                        <option value="low">{{ __('Low') }}</option>
                        <option value="medium">{{ __('Medium') }}</option>
                        <option value="high">{{ __('High') }}</option>
                        <option value="critical">{{ __('Critical') }}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="end_date" class="form-control-label">{{ __('End Date') }}</label>
                    <input type="text" name="end_date" class="form-control datepicker">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="description" class="form-control-label">{{ __('Description') }}</label>
                    <textarea name="description" class="form-control" placeholder="{{ __('Ticket Description') }}"></textarea>
                </div>
            </div>
        </div>
        <div class="col-12">
            <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
            <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-bs-dismiss="modal">
        </div>
    </form>
</div>
