<div class="card bg-none card-box">
    <form action="{{ url('designation') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Department select -->
            <div class="col-12">
                <div class="form-group">
                    <label for="department_id" class="form-control-label">{{ __('Department') }}</label>
                    <select name="department_id" id="department_id" class="form-control select2" required>
                        @foreach ($departments as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Name input -->
                <div class="form-group">
                    <label for="name" class="form-control-label">{{ __('Name') }}</label>
                    <input type="text" name="name" id="name" class="form-control"
                        placeholder="{{ __('Enter Designation Name') }}" value="{{ old('name') }}">
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <!-- Submit and cancel buttons -->
            <div class="col-12">
                <button type="submit" class="btn-create badge-blue">{{ __('Create') }}</button>
                <button type="button" class="btn-create bg-gray" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </form>
</div>
