<div class="card bg-none card-box">
    <form action="{{ route('job.on.board.store', $id) }}" method="POST">
        @csrf
        <div class="row">
            @if ($id == 0)
                <div class="form-group col-md-12">
                    <label for="application" class="form-control-label">{{ __('Interviewer') }}</label>
                    <select name="application" id="application" class="form-control select2" required>
                        @foreach ($applications as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="form-group col-md-12">
                <label for="joining_date" class="form-control-label">{{ __('Joining Date') }}</label>
                <input type="text" name="joining_date" id="joining_date" class="form-control datepicker">
            </div>
            <div class="form-group col-md-12">
                <label for="status" class="form-control-label">{{ __('Status') }}</label>
                <select name="status" id="status" class="form-control select2">
                    @foreach ($status as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
                <button type="button" class="btn-create bg-gray" data-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </form>
</div>
