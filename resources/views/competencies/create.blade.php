<div class="card bg-none card-box">
    <form action="{{ url('competencies') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Name input -->
            <div class="col-12">
                <div class="form-group">
                    <label for="name" class="form-control-label">{{ __('Name') }}</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}">
                </div>
            </div>
            <!-- Type select -->
            <div class="col-12">
                <div class="form-group">
                    <label for="type" class="form-control-label">{{ __('Type') }}</label>
                    <select name="type" id="type" class="form-control select2">
                        @foreach ($performance_types as $key => $value)
                            <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>
                                {{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- Submit and cancel buttons -->
            <div class="col-12">
                <button type="submit" class="btn-create badge-blue">{{ __('Create') }}</button>
                <button type="button" class="btn-create bg-gray" data-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </form>
</div>
