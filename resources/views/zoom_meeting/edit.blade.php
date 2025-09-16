<div class="card bg-none card-box">
    <form method="POST" action="{{ route('zoom-meeting.update', $ZoomMeeting->id) }}">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label class="form-control-label">{{ __('Title') }}</label>
                    <input type="text" name="title" class="form-control" required="required"
                        value="{{ $ZoomMeeting->title }}">
                </div>
            </div>
            <div class="col-6">
                <div class="form-group select2_option">
                    <label class="form-control-label">{{ __('User') }}</label>
                    <select name="user_id" required="required" data-placeholder="Yours Placeholder"
                        class="form-control js-multiple-select">
                        @foreach ($employee_option as $optionId => $optionValue)
                            <option value="{{ $optionId }}">{{ $optionValue }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label class="form-control-label">{{ __('Start Date') }}</label>
                    <input type="text" name="start_date1"
                        class="form-control datetime_class datetime_class_start_date"
                        value="{{ $ZoomMeeting->start_date }}">
                    <input type="hidden" name="start_date" class="start_date" value="{{ $ZoomMeeting->start_date }}">
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label class="form-control-label">{{ __('Duration') }}</label>
                    <input type="number" name="duration" class="form-control" required="required" min="0"
                        value="{{ $ZoomMeeting->duration }}">
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label class="form-control-label">{{ __('Password') }}</label>
                    <input type="password" name="password" class="form-control" value="{{ $ZoomMeeting->password }}">
                </div>
            </div>
            <div class="col-12">
                <div class="form-group text-right">
                    <input type="submit" class="btn btn-sm btn-primary rounded-pill mr-auto"
                        value="{{ __('Save') }}" data-ajax-popup="true">
                </div>
            </div>
        </div>
    </form>
</div>
