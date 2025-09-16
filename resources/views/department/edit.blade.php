<div class="card bg-none card-box">
    <form action="{{ route('department.update', $department->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Branch select -->
            <div class="col-12">
                <div class="form-group">
                    <label for="branch_id">{{ __('Branch') }}</label>
                    <select name="branch_id" id="branch_id" class="form-control select2" placeholder="{{ __('Select Branch') }}">
                        @foreach ($branch as $id => $name)
                            <option value="{{ $id }}" {{ $id == old('branch_id', $department->branch_id) ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- Name input -->
            <div class="col-12">
                <div class="form-group">
                    <label for="name">{{ __('Name') }}</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="{{ __('Enter Department Name') }}" value="{{ old('name', $department->name) }}">
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
