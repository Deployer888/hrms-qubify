<div class="card bg-none card-box">
    <form action="{{ route('goaltracking.update', $goalTracking->id) }}" method="POST">
        @method('PUT')
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="branch" class="form-control-label">{{ __('Branch') }}</label>
                    <select name="branch" id="branch" class="form-control select2" required>
                        @foreach ($brances as $value => $label)
                            <option value="{{ $value }}" {{ $goalTracking->branch == $value ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="goal_type" class="form-control-label">{{ __('Goal Types') }}</label>
                    <select name="goal_type" id="goal_type" class="form-control select2" required>
                        @foreach ($goalTypes as $value => $label)
                            <option value="{{ $value }}"
                                {{ $goalTracking->goal_type == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="start_date" class="form-control-label">{{ __('Start Date') }}</label>
                    <input type="text" name="start_date" id="start_date" class="form-control datepicker"
                        value="{{ $goalTracking->start_date }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="end_date" class="form-control-label">{{ __('End Date') }}</label>
                    <input type="text" name="end_date" id="end_date" class="form-control datepicker"
                        value="{{ $goalTracking->end_date }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="subject" class="form-control-label">{{ __('Subject') }}</label>
                    <input type="text" name="subject" id="subject" class="form-control"
                        value="{{ $goalTracking->subject }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="target_achievement" class="form-control-label">{{ __('Target Achievement') }}</label>
                    <input type="text" name="target_achievement" id="target_achievement" class="form-control"
                        value="{{ $goalTracking->target_achievement }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="description" class="form-control-label">{{ __('Description') }}</label>
                    <textarea name="description" id="description" class="form-control">{{ $goalTracking->description }}</textarea>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="status" class="form-control-label">{{ __('Status') }}</label>
                    <select name="status" id="status" class="form-control select2">
                        @foreach ($status as $value => $label)
                            <option value="{{ $value }}"
                                {{ $goalTracking->status == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <fieldset id="demo1" class="rating">
                    @for ($i = 5; $i >= 1; $i--)
                        <input class="stars" type="radio" id="rating-{{ $i }}" name="rating"
                            value="{{ $i }}" {{ $goalTracking->rating == $i ? 'checked' : '' }}>
                        <label class="full" for="rating-{{ $i }}"
                            title="{{ [5 => 'Awesome', 4 => 'Pretty good', 3 => 'Meh', 2 => 'Kinda bad', 1 => 'Sucks big time'][$i] }} - {{ $i }} stars"></label>
                    @endfor
                </fieldset>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <input type="range" class="slider w-100 mb-0" name="progress" id="myRange"
                        value="{{ $goalTracking->progress }}" min="1" max="100"
                        oninput="ageOutputId.value = myRange.value">
                    <output name="ageOutputName" id="ageOutputId">{{ $goalTracking->progress }}</output>
                    %
                </div>
            </div>

            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
