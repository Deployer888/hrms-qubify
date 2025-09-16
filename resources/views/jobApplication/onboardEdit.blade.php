<div class="card bg-none card-box">
    <form action="{{ route('job.on.board.update', $jobOnBoard->id) }}" method="POST">
        @csrf
        @method('POST')
        <div class="row">
            <div class="form-group col-md-12">
                <label for="joining_date" class="form-control-label">{{ __('Joining Date') }}</label>
                <input type="text" name="joining_date" id="joining_date"
                    value="{{ old('joining_date', $jobOnBoard->joining_date) }}" class="form-control datepicker">
            </div>
            <div class="form-group col-md-12">
                <label for="status" class="form-control-label">{{ __('Status') }}</label>
                <select name="status" id="status" class="form-control select2">
                    @foreach ($status as $key => $value)
                        <option value="{{ $key }}" {{ $jobOnBoard->status == $key ? 'selected' : '' }}>
                            {{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <button type="button" class="btn-create bg-gray" data-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </form>
</div>
