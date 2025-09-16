<div class="card bg-none card-box">
    <form action="{{ route('meeting.update', ['meeting' => $meeting->id]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="title" class="form-control-label">{{ __('Meeting Title') }}</label>
                    <input type="text" name="title" id="title" value="{{ $meeting->title }}" class="form-control"
                        placeholder="{{ __('Enter Meeting Title') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="date" class="form-control-label">{{ __('Meeting Date') }}</label>
                    <input type="text" name="date" id="date" value="{{ $meeting->date }}"
                        class="form-control datepicker">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="time" class="form-control-label">{{ __('Meeting Time') }}</label>
                    <input type="text" name="time" id="time" value="{{ $meeting->time }}"
                        class="form-control timepicker">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="note" class="form-control-label">{{ __('Meeting Note') }}</label>
                    <textarea name="note" id="note" class="form-control" placeholder="{{ __('Enter Meeting Note') }}">{{ $meeting->note }}</textarea>
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
