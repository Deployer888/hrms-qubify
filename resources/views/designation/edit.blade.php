<div class="card bg-none card-box">
    <form action="{{ route('designation.update', $designation->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Department select -->
            <div class="col-12">
                <div class="form-group">
                    <label for="department_id" class="form-control-label">{{ __('Department') }}</label>
                    <select name="department_id" id="department_id" class="form-control select2" required>
                        @foreach ($departments as $id => $name)
                            <option value="{{ $id }}"
                                {{ $id == old('department_id', $designation->department_id) ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Name input -->
                <div class="form-group">
                    <label for="name" class="form-control-label">{{ __('Name') }}</label>
                    <input type="text" name="name" id="name" class="form-control"
                        placeholder="{{ __('Enter Department Name') }}" value="{{ old('name', $designation->name) }}">
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <!-- Submit and cancel buttons -->
            <div class="col-12">
                <button type="submit" class="btn-create badge-blue">{{ __('Update') }}</button>
                <button type="button" class="btn-create bg-gray" data-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </form>
</div>
