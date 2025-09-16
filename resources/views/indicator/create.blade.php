<div class="card bg-none card-box">
    <form action="{{ url('indicator') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="branch" class="form-control-label">{{ __('Branch') }}</label>
                    <select name="branch" id="branch" class="form-control select2" required>
                        @foreach ($brances as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="department" class="form-control-label">{{ __('Department') }}</label>
                    <select name="department" id="department" class="form-control select2" required>
                        @foreach ($departments as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="designation" class="form-control-label">{{ __('Designation') }}</label>
                    <select name="designation" id="designation" class="form-control select2" required>
                        @foreach ($degisnation as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            @foreach ($performance_types as $performance_type)
                <div class="col-md-12 mt-3">
                    <h6>{{ $performance_type->name }}</h6>
                    <hr class="mt-0">
                </div>
                @foreach ($performance_type->types as $types)
                    <div class="col-6">
                        {{ $types->name }}
                    </div>
                    <div class="col-6">
                        <fieldset id='demo1' class="rating">
                            @for ($i = 5; $i >= 1; $i--)
                                <input class="stars" type="radio"
                                    id="technical-{{ $i }}-{{ $types->id }}"
                                    name="rating[{{ $types->id }}]" value="{{ $i }}" />
                                <label class="full" for="technical-{{ $i }}-{{ $types->id }}"
                                    title="{{ ['Awesome', 'Pretty good', 'Meh', 'Kinda bad', 'Sucks big time'][$i - 1] }} - {{ $i }} stars"></label>
                            @endfor
                        </fieldset>
                    </div>
                @endforeach
            @endforeach
        </div>
        <div class="row">
            <div class="col-12">
                <input type="submit" value="{{ __('Create') }}" class="btn-create badge-blue">
                <input type="button" value="{{ __('Cancel') }}" class="btn-create bg-gray" data-dismiss="modal">
            </div>
        </div>
    </form>
</div>
