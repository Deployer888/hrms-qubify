<div class="card bg-none card-box">
    <form action="{{ route('leavetype.update', $leavetype->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="title" class="form-control-label">{{ __('Leave Type') }}</label>
                    <input type="text" name="title" id="title" class="form-control"
                        placeholder="{{ __('Enter Leave Type Name') }}" value="{{ old('title', $leavetype->title) }}">
                    @error('title')
                        <span class="invalid-name" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="days" class="form-control-label">{{ __('Days Per Year') }}</label>
                    <input type="number" name="days" id="days" class="form-control"
                        placeholder="{{ __('Enter Days / Year') }}" value="{{ old('days', $leavetype->days) }}">
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
