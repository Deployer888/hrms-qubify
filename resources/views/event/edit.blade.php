<div class="card bg-none card-box">
    <form action="{{ route('event.update', $event->id) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="title" class="form-control-label">{{ __('Event Title') }}</label>
                    <input type="text" name="title" id="title" class="form-control"
                        placeholder="{{ __('Enter Event Title') }}" value="{{ $event->title }}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="start_date" class="form-control-label">{{ __('Event Start Date') }}</label>
                    <input type="text" name="start_date" id="start_date" class="form-control datepicker"
                        value="{{ $event->start_date }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="end_date" class="form-control-label">{{ __('Event End Date') }}</label>
                    <input type="text" name="end_date" id="end_date" class="form-control datepicker"
                        value="{{ $event->end_date }}">
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-md-6">
                <label for="image" class="form-control-label">{{ __('Select Image File') }}</label>
                <div class="choose-file form-group">
                    <label for="image" class="form-control-label">
                        <div>{{ __('Choose file here') }}</div>
                        <input type="file" class="form-control" name="image" id="file"
                            data-filename="upload_file"  accept=".jpeg, .jpg, .png, .gif">
                    </label>
                    <p class="upload_file"></p>
                    <br> 
                    <span class="text-xs"><a
                        href="{{ !empty($event->image) ? asset(Storage::url('uploads/event/images')) . '/' . $event->image : '' }}"
                        target="_blank">{{ !empty($event->image) ? $event->image : '' }}</a>
                    </span>
                </div>
            </div>
            <div class="col-md-6">
                <label for="audio_file" class="form-control-label">{{ __('Select Audio File') }}</label>
                <div class="choose-file form-group">
                    <label for="audio_file" class="form-control-label">
                        <div>{{ __('Choose file here') }}</div>
                        <input type="file" class="form-control" name="audio_file" id="file"
                            data-filename="upload_audio_file"  accept=".mp3, .wav">
                    </label>
                    <p class="upload_audio_file"></p>
                    <br> 
                    <span class="text-xs"><a
                        href="{{ !empty($event->audio_file) ? asset(Storage::url('uploads/event/audio')) . '/' . $event->audio_file : '' }}"
                        target="_blank">{{ !empty($event->audio_file) ? $event->audio_file : '' }}</a>
                    </span>
                    
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="color" class="form-control-label d-block mb-3">{{ __('Event Select Color') }}</label>
                    <div class="btn-group btn-group-toggle btn-group-colors event-tag" data-toggle="buttons">
                        @foreach (['#00B8D9' => 'bg-info', '#FFAB00' => 'bg-warning', '#FF5630' => 'bg-danger', '#36B37E' => 'bg-success', '#EFF2F7' => 'bg-secondary', '#051C4B' => 'bg-primary'] as $value => $class)
                            <label class="btn {{ $class }} {{ $event->color == $value ? 'active' : '' }}">
                                <input type="radio" name="color" value="{{ $value }}"
                                    {{ $event->color == $value ? 'checked' : '' }}>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="description" class="form-control-label">{{ __('Event Description') }}</label>
                    <textarea name="description" id="description" class="form-control" placeholder="{{ __('Enter Event Description') }}">{{ $event->description }}</textarea>
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>

<script>
    if ($(".datepicker").length) {
        $('.datepicker').daterangepicker({
            singleDatePicker: true,
            format: 'yyyy-mm-dd',
            locale: date_picker_locale,
        });
    }
</script>
