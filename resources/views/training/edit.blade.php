<div class="card bg-none card-box">
    <form action="{{ route('training.update', $training->id) }}" method="post">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="branch" class="form-control-label">{{ __('Branch') }}</label>
                    <select name="branch" id="branch" class="form-control select2" required>
                        @foreach ($branches as $id => $name)
                            <option value="{{ $id }}" @if ($training->branch == $id) selected @endif>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="trainer_option" class="form-control-label">{{ __('Trainer Option') }}</label>
                    <select name="trainer_option" id="trainer_option" class="form-control select2" required>
                        @foreach ($options as $id => $name)
                            <option value="{{ $id }}" @if ($training->trainer_option == $id) selected @endif>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="training_type" class="form-control-label">{{ __('Training Type') }}</label>
                    <select name="training_type" id="training_type" class="form-control select2" required>
                        @foreach ($trainingTypes as $id => $name)
                            <option value="{{ $id }}" @if ($training->training_type == $id) selected @endif>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="trainer" class="form-control-label">{{ __('Trainer') }}</label>
                    <select name="trainer" id="trainer" class="form-control select2" required>
                        @foreach ($trainers as $id => $name)
                            <option value="{{ $id }}" @if ($training->trainer == $id) selected @endif>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="training_cost" class="form-control-label">{{ __('Training Cost') }}</label>
                    <input type="number" name="training_cost" id="training_cost" class="form-control" step="0.01"
                        value="{{ $training->training_cost }}" required>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="employee" class="form-control-label">{{ __('Employee') }}</label>
                    <select name="employee" id="employee" class="form-control select2" required>
                        @foreach ($employees as $id => $name)
                            <option value="{{ $id }}" @if ($training->employee == $id) selected @endif>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="start_date" class="form-control-label">{{ __('Start Date') }}</label>
                    <input type="text" name="start_date" id="start_date" class="form-control datepicker"
                        value="{{ $training->start_date }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="end_date" class="form-control-label">{{ __('End Date') }}</label>
                    <input type="text" name="end_date" id="end_date" class="form-control datepicker"
                        value="{{ $training->end_date }}">
                </div>
            </div>
            <div class="form-group col-lg-12">
                <label for="description" class="form-control-label">{{ __('Description') }}</label>
                <textarea name="description" id="description" class="form-control" placeholder="{{ __('Description') }}">{{ $training->description }}</textarea>
            </div>
            <div class="col-12">
                <input type="submit" value="{{ __('Update') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-bs-dismiss="modal">
            </div>
        </div>
    </form>
</div>
