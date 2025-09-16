<div class="card bg-none card-box">
    <form action="{{ url('job-application') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="form-group col-md-12">
                <label for="job" class="form-control-label">{{ __('Job') }}</label>
                <select name="job" id="job" class="form-control select2">
                    @foreach ($jobs as $job_id => $job_name)
                        <option value="{{ $job_id }}">{{ $job_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="name" class="form-control-label">{{ __('Name') }}</label>
                <input type="text" name="name" id="name" class="form-control name">
            </div>
            <div class="form-group col-md-6">
                <label for="email" class="form-control-label">{{ __('Email') }}</label>
                <input type="text" name="email" id="email" class="form-control">
            </div>
            <div class="form-group col-md-6">
                <label for="phone" class="form-control-label">{{ __('Phone') }}</label>
                <input type="text" name="phone" id="phone" class="form-control">
            </div>
            <div class="form-group col-md-6 dob d-none">
                <label for="dob" class="form-control-label">{{ __('Date of Birth') }}</label>
                <input type="text" name="dob" id="dob" class="form-control datepicker"
                    value="{{ old('dob') }}">
            </div>
            <div class="form-group col-md-6 gender d-none">
                <label class="form-control-label">{{ __('Gender') }}</label>
                <div class="d-flex radio-check">
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="g_male" name="gender" value="Male" class="custom-control-input">
                        <label for="g_male" class="custom-control-label">{{ __('Male') }}</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="g_female" name="gender" value="Female" class="custom-control-input">
                        <label for="g_female" class="custom-control-label">{{ __('Female') }}</label>
                    </div>
                </div>
            </div>
            <div class="form-group col-md-6 country d-none">
                <label for="country" class="form-control-label">{{ __('Country') }}</label>
                <input type="text" name="country" id="country" class="form-control">
            </div>
            <div class="form-group col-md-6 country d-none">
                <label for="state" class="form-control-label">{{ __('State') }}</label>
                <input type="text" name="state" id="state" class="form-control">
            </div>
            <div class="form-group col-md-6 country d-none">
                <label for="city" class="form-control-label">{{ __('City') }}</label>
                <input type="text" name="city" id="city" class="form-control">
            </div>
            <div class="form-group col-md-6 profile d-none">
                <label for="profile" class="form-control-label">{{ __('Profile') }}</label>
                <div class="choose-file form-group">
                    <label for="profile" class="form-control-label">
                        <div>{{ __('Choose file here') }}</div>
                        <input type="file" name="profile" id="profile" class="form-control"
                            data-filename="profile_create">
                    </label>
                    <p class="profile_create"></p>
                </div>
            </div>
            <div class="form-group col-md-6 resume d-none">
                <label for="resume" class="form-control-label">{{ __('CV / Resume') }}</label>
                <div class="choose-file form-group">
                    <label for="resume" class="form-control-label">
                        <div>{{ __('Choose file here') }}</div>
                        <input type="file" name="resume" id="resume" class="form-control"
                            data-filename="resume_create">
                    </label>
                    <p class="resume_create"></p>
                </div>
            </div>
            <div class="form-group col-md-12 letter d-none">
                <label for="cover_letter" class="form-control-label">{{ __('Cover Letter') }}</label>
                <textarea name="cover_letter" id="cover_letter" class="form-control"></textarea>
            </div>
            @foreach ($questions as $question)
                <div class="form-group col-md-12  question question_{{ $question->id }} d-none">
                    <label for="question_{{ $question->id }}"
                        class="form-control-label">{{ $question->question }}</label>
                    <input type="text" name="question[{{ $question->question }}]"
                        id="question_{{ $question->id }}" class="form-control"
                        {{ $question->is_required == 'yes' ? 'required' : '' }}>
                </div>
            @endforeach
            <div class="col-12">
                <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
