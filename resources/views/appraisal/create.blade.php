<div class="card bg-none card-box">
    <form action="{{ url('appraisal') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="branch" class="form-control-label">{{ __('Branch') }}</label>
                    <select name="branch" id="branch" class="form-control select2" required>
                        @foreach ($branches as $key => $name)
                            <option value="{{ $key }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="employee" class="form-control-label">{{ __('Employee') }}</label>
                    <select name="employee" id="employee" class="form-control select2" required>
                        @foreach ($employees as $key => $name)
                            <option value="{{ $key }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="appraisal_date" class="form-control-label">{{ __('Select Month') }}</label>
                    <input type="text" name="appraisal_date" id="appraisal_date"
                        class="form-control custom-datepicker" value="{{ old('appraisal_date') }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="remark" class="form-control-label">{{ __('Remarks') }}</label>
                    <textarea name="remark" id="remark" class="form-control">{{ old('remark') }}</textarea>
                </div>
            </div>
        </div>
        <div class="row">
            @foreach ($performance_types as $performance_type)
                <div class="col-md-12 mt-3">
                    <h6>{{ $performance_type->name }}</h6>
                    <hr class="mt-0">
                </div>
                @foreach ($performance_type->types as $type)
                    <div class="col-6">{{ $type->name }}</div>
                    <div class="col-6">
                        <fieldset class="rating">
                            @for ($i = 5; $i >= 1; $i--)
                                <input class="stars" type="radio"
                                    id="rating-{{ $i }}-{{ $type->id }}"
                                    name="rating[{{ $type->id }}]" value="{{ $i }}">
                                <label class="full" for="rating-{{ $i }}-{{ $type->id }}"
                                    title="{{ $i }} stars"></label>
                            @endfor
                        </fieldset>
                    </div>
                @endforeach
            @endforeach
        </div>

        <div class="row">
            <div class="col-12">
                <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
                <button type="button" class="btn-create bg-gray" data-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </form>
</div>
