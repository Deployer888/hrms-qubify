<div class="card bg-none card-box">
    <form action="{{ route('timesheet.update', $timeSheet->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="row">
            @if (\Auth::user()->type != 'employee')
                <div class="form-group col-md-12">
                    <label for="employee_id" class="form-control-label">{{ __('Employee') }}</label>
                    <select name="employee_id" id="employee_id" class="form-control font-style select2" required>
                        @foreach ($employees as $id => $name)
                            <option value="{{ $id }}" @if ($timeSheet->employee_id == $id) selected @endif>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="form-group col-md-6">
                <label for="date" class="form-control-label">{{ __('Date') }}</label>
                <input type="text" name="date" id="date" class="form-control datepicker"
                    value="{{ $timeSheet->date }}" required>
            </div>
            <div class="form-group col-md-6">
                <label for="hours" class="form-control-label">{{ __('Hours') }}</label>
                <input type="number" name="hours" id="hours" class="form-control" value="{{ $timeSheet->hours }}"
                    required step="0.01">
            </div>
            <div class="form-group col-md-12">
                <label for="remark" class="form-control-label">{{ __('Remark') }}</label>
                <textarea name="remark" id="remark" class="form-control" rows="2">{{ $timeSheet->remark }}</textarea>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-bs-dismiss="modal">
            </div>
        </div>
    </form>
</div>
