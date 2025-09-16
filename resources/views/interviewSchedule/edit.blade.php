<div class="card bg-none card-box">
    <form action="{{ route('interview-schedule.update', $interviewSchedule->id) }}" method="POST">
        @method('PUT')
        @csrf
        <div class="row">
            <div class="form-group col-md-6">
                <label for="candidate" class="form-control-label">{{ __('Interviewer') }}</label>
                <select name="candidate" id="candidate" class="form-control select2" required>
                    @foreach ($candidates as $value => $label)
                        <option value="{{ $value }}"
                            {{ $interviewSchedule->candidate == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="employee" class="form-control-label">{{ __('Assign Employee') }}</label>
                <select name="employee" id="employee" class="form-control select2" required>
                    @foreach ($employees as $value => $label)
                        <option value="{{ $value }}"
                            {{ $interviewSchedule->employee == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="date" class="form-control-label">{{ __('Interview Date') }}</label>
                <input type="text" name="date" id="date" class="form-control datepicker"
                    value="{{ $interviewSchedule->date }}">
            </div>
            <div class="form-group col-md-6">
                <label for="time" class="form-control-label">{{ __('Interview Time') }}</label>
                <input type="text" name="time" id="time" class="form-control timepicker"
                    value="{{ $interviewSchedule->time }}">
            </div>
            <div class="form-group col-md-12">
                <label for="comment" class="form-control-label">{{ __('Comment') }}</label>
                <textarea name="comment" id="comment" class="form-control">{{ $interviewSchedule->comment }}</textarea>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
