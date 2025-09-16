<div class="card bg-none card-box">
    <form action="{{ url('event') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="branch_id" class="form-control-label">{{ __('Branch') }}</label>
                    <select class="form-control select2" name="branch_id" id="branch_id"
                        placeholder="{{ __('Select Branch') }}">
                        <option value="">{{ __('Select Branch') }}</option>
                        <option value="0">{{ __('All Branch') }}</option>
                        @foreach ($branch as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="department_id" class="form-control-label">{{ __('Department') }}</label>
                    <select class="form-control select2" name="department_id[]" id="department_id"
                        placeholder="{{ __('Select Department') }}" multiple>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="employee_id" class="form-control-label">{{ __('Employee') }}</label>
                    <select class="form-control select2" name="employee_id[]" id="employee_id"
                        placeholder="{{ __('Select Employee') }}" multiple>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="title" class="form-control-label">{{ __('Event Title') }}</label>
                    <input type="text" name="title" class="form-control"
                        placeholder="{{ __('Enter Event Title') }}">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="start_date" class="form-control-label">{{ __('Event Start Date') }}</label>
                    <input type="text" name="start_date" class="form-control datepicker">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="end_date" class="form-control-label">{{ __('Event End Date') }}</label>
                    <input type="text" name="end_date" class="form-control datepicker">
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
                            data-filename="upload_file" accept=".jpeg, .jpg, .png, .gif">
                    </label>
                    <p class="upload_file"></p>
                </div>
            </div>
            <div class="col-md-6">
                <label for="audio_file" class="form-control-label">{{ __('Select Audio File') }}</label>
                <div class="choose-file form-group">
                    <label for="audio_file" class="form-control-label">
                        <div>{{ __('Choose file here') }}</div>
                        <input type="file" class="form-control" name="audio_file" id="file"
                            data-filename="upload_audio_file" accept=".mp3, .wav">
                    </label>
                    <p class="upload_audio_file"></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="color"
                        class="form-control-label d-block mb-3">{{ __('Event Select Color') }}</label>
                    <div class="btn-group btn-group-toggle btn-group-colors event-tag" data-toggle="buttons">
                        <label class="btn bg-info active"><input type="radio" name="color" value="#00B8D9"
                                checked></label>
                        <label class="btn bg-warning"><input type="radio" name="color" value="#FFAB00"></label>
                        <label class="btn bg-danger"><input type="radio" name="color" value="#FF5630"></label>
                        <label class="btn bg-success"><input type="radio" name="color" value="#36B37E"></label>
                        <label class="btn bg-secondary"><input type="radio" name="color" value="#EFF2F7"></label>
                        <label class="btn bg-primary"><input type="radio" name="color" value="#051C4B"></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="description" class="form-control-label">{{ __('Event Description') }}</label>
                    <textarea name="description" class="form-control" placeholder="{{ __('Enter Event Description') }}"></textarea>
                </div>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
